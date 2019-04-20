-----------------------------------------------
-- Clear Previously existing table and types --
-----------------------------------------------

DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS issues CASCADE;
DROP TABLE IF EXISTS event_tags CASCADE;
DROP TABLE IF EXISTS favorites CASCADE;
DROP TABLE IF EXISTS organizers CASCADE;
DROP TABLE IF EXISTS tickets CASCADE;
DROP TABLE IF EXISTS ratings CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS posts CASCADE;
DROP TABLE IF EXISTS event_vouchers CASCADE;
DROP TABLE IF EXISTS tags CASCADE;
DROP TABLE IF EXISTS events CASCADE;
DROP TABLE IF EXISTS event_categories CASCADE;
DROP TABLE IF EXISTS users CASCADE;

DROP TYPE IF EXISTS TICKET_PAYMENT_TYPE CASCADE;
DROP TYPE IF EXISTS NOTIFICATION_TYPE CASCADE;
DROP TYPE IF EXISTS EVENT_STATUS CASCADE;

-----------
-- Types --
-----------

CREATE TYPE EVENT_STATUS AS ENUM ('Active', 'Disabled', 'Cancelled');

CREATE TYPE NOTIFICATION_TYPE AS ENUM ('IssueNotification', 'EventInvitation', 'EventDisabling', 'EventActivation',
    'EventCancellation', 'EventRemoval', 'EventOrganizer', 'EventUpdate', 'EventAnnouncement');

CREATE TYPE TICKET_PAYMENT_TYPE AS ENUM ('Voucher', 'Paypal');

------------
-- Tables --
------------

-- R01
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(64) NOT NULL,
	email VARCHAR(128) NOT NULL CONSTRAINT user_email_unique UNIQUE,
	password VARCHAR(128) NOT NULL,
    is_disabled BOOLEAN NOT NULL DEFAULT false,
    is_admin BOOLEAN NOT NULL DEFAULT false,
    remember_token VARCHAR, -- Necessary for Laravel session remembering
    search TSVECTOR
);

-- R05
CREATE TABLE event_categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(20) NOT NULL
);

-- R04
CREATE TABLE events (
	id SERIAL PRIMARY KEY,
	title VARCHAR(30) NOT NULL,
	description TEXT NOT NULL,
	price REAL NOT NULL,
	location VARCHAR(60),
	latitude REAL,
	longitude REAL,
	start_timestamp TIMESTAMP WITH TIME zone NOT NULL,
	end_timestamp TIMESTAMP WITH TIME zone,
    event_category_id INTEGER REFERENCES event_categories(id) ON DELETE CASCADE,
	status EVENT_STATUS NOT NULL DEFAULT 'Active',
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	search TSVECTOR,

    CONSTRAINT price_check CHECK (price >= 0),
    CONSTRAINT latitude_check CHECK ((latitude IS NULL) OR (latitude >= -90 AND latitude <= 90)),
    CONSTRAINT longitude_check CHECK ((longitude IS NULL) OR (longitude >= -180 AND longitude <= 180)),
    CONSTRAINT start_timestamp_check CHECK (start_timestamp > now()),
    CONSTRAINT end_timestamp_check CHECK ((end_timestamp is NULL) OR (end_timestamp > start_timestamp))
);

-- R06
CREATE TABLE tags (
	id SERIAL PRIMARY KEY,
	name VARCHAR(30) NOT NULL
);

-- R07
CREATE TABLE event_vouchers (
	id SERIAL PRIMARY KEY,
	code VARCHAR(128) NOT NULL,
    is_used BOOLEAN NOT NULL DEFAULT false,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,

    CONSTRAINT unique_voucher_in_event UNIQUE(code, event_id)
);

-- R08
CREATE TABLE posts (
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    "timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    rating INTEGER DEFAULT 0,
    num_comments INTEGER DEFAULT 0,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    is_announcement BOOLEAN NOT NULL,

    CONSTRAINT announcement_post CHECK (
        (is_announcement = false AND rating IS NOT NULL AND num_comments IS NOT NULL) OR
        (is_announcement = true)
    ) 
);

-- R09
CREATE TABLE comments (
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    "timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE
);

-- R10
CREATE TABLE ratings (
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    value INTEGER NOT NULL,

    PRIMARY KEY (user_id, post_id),
    CONSTRAINT rating_value CHECK (
        value = 1 OR value = -1
    )
);

-- R11
CREATE TABLE tickets (
	id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	is_checked_in BOOLEAN NOT NULL DEFAULT false,
	check_in_organizer_id INTEGER REFERENCES users(id) ON UPDATE CASCADE,
	nif INTEGER DEFAULT 999999999,
	billing_name VARCHAR(64),
	address VARCHAR(128),
    event_voucher_id INTEGER REFERENCES event_vouchers(id) ON UPDATE CASCADE,
	type TICKET_PAYMENT_TYPE NOT NULL,
	paypal_order_id VARCHAR(256),

    CONSTRAINT ticket_payment CHECK (
        (type = 'Voucher' AND event_voucher_id IS NOT NULL) OR
        (type = 'Paypal' AND paypal_order_id IS NOT NULL)
    )
);

-- R12
CREATE TABLE organizers (
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,

    PRIMARY KEY (user_id, event_id)
);

-- R13
CREATE TABLE favorites (
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,

    PRIMARY KEY (user_id, event_id)
);

-- R14
CREATE TABLE event_tags (
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    tag_id INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE,

    PRIMARY KEY (event_id, tag_id)
);

-- R02
CREATE TABLE issues (
	id SERIAL PRIMARY KEY,
	title VARCHAR(64) NOT NULL,
	content VARCHAR(800) NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	is_solved BOOLEAN NOT NULL DEFAULT false,
    creator_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    solver_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
	referenced_user INTEGER REFERENCES users(id) ON DELETE CASCADE,
	referenced_event INTEGER REFERENCES events(id) ON DELETE CASCADE,
	referenced_post INTEGER REFERENCES posts(id) ON DELETE CASCADE,
	referenced_comment INTEGER REFERENCES comments(id) ON DELETE CASCADE,
    search TSVECTOR,

    CONSTRAINT self_reporting CHECK (creator_id != referenced_user)
);

-- R03
CREATE TABLE notifications (
    id SERIAL PRIMARY KEY,
	type NOTIFICATION_TYPE NOT NULL,
	is_dismissed BOOLEAN NOT NULL DEFAULT false,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
	content TEXT,
    issue_id INTEGER REFERENCES issues(id) ON DELETE CASCADE,

    CONSTRAINT issue_notification CHECK (
		(type = 'IssueNotification' AND issue_id IS NOT NULL AND content IS NOT NULL) OR
		(type != 'IssueNotification' AND event_id IS NOT NULL)
	)
);


---------------
-- Functions --
---------------

-- FUNCTION01
CREATE OR REPLACE FUNCTION
    insert_rating_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET rating = rating + NEW.value
        WHERE posts.id = NEW.post_id;
        
        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION02
CREATE OR REPLACE FUNCTION
    update_rating_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET rating = rating + NEW.value - OLD.value
        WHERE posts.id = NEW.post_id;
        
        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION03
CREATE OR REPLACE FUNCTION
    delete_rating_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET rating = rating - OLD.value
        WHERE posts.id = OLD.post_id;
        
        RETURN OLD;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION04
CREATE OR REPLACE FUNCTION
    insert_comment_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET num_comments = num_comments + 1
        WHERE posts.id = NEW.post_id;
        
        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION05
CREATE OR REPLACE FUNCTION
    delete_comment_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET num_comments = num_comments - 1
        WHERE posts.id = OLD.post_id;
        
        RETURN OLD;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION06
CREATE OR REPLACE FUNCTION
    disable_event_function() RETURNS TRIGGER AS $$
    DECLARE
        u record;
    BEGIN
        FOR 
            u 
        IN
            SELECT DISTINCT user_id
            FROM (
                SELECT user_id
                FROM tickets
                WHERE event_id = NEW.id
                UNION
                SELECT user_id
                FROM organizers
                WHERE event_id = NEW.id
                UNION
                SELECT user_id
                FROM favorites
                WHERE event_id = NEW.id
            ) AS users
        LOOP
            INSERT INTO notifications(type, user_id, event_id)
            VALUES ('EventDisabling', u.user_id, NEW.id);
        END LOOP;

        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION07
CREATE OR REPLACE FUNCTION
    cancel_event_function() RETURNS TRIGGER AS $$
    DECLARE
        u record;
    BEGIN
        FOR 
            u 
        IN
            SELECT DISTINCT user_id
            FROM (
                SELECT user_id
                FROM tickets
                WHERE event_id = NEW.id
                UNION
                SELECT user_id
                FROM organizers
                WHERE event_id = NEW.id
                UNION
                SELECT user_id
                FROM favorites
                WHERE event_id = NEW.id
            ) AS users
        LOOP
            INSERT INTO notifications(type, user_id, event_id)
            VALUES ('EventCancellation', u.user_id, NEW.id);
        END LOOP;

        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION08
CREATE OR REPLACE FUNCTION
    activate_event_function() RETURNS TRIGGER AS $$
    DECLARE
        u record;
    BEGIN
        FOR 
            u 
        IN
            SELECT DISTINCT user_id
            FROM (
                SELECT user_id
                FROM tickets
                WHERE event_id = NEW.id
                UNION
                SELECT user_id
                FROM organizers
                WHERE event_id = NEW.id
                UNION
                SELECT user_id
                FROM favorites
                WHERE event_id = NEW.id
            ) AS users
        LOOP
            INSERT INTO notifications(type, user_id, event_id)
            VALUES ('EventActivation', u.user_id, NEW.id);
        END LOOP;

        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION09
CREATE OR REPLACE FUNCTION
    remove_attendee_function() RETURNS TRIGGER AS $$
    BEGIN
        INSERT INTO notifications(type, user_id, event_id)
        VALUES ('EventRemoval', OLD.user_id, OLD.event_id);
        
        RETURN OLD;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION10
CREATE OR REPLACE FUNCTION
    organizer_promotion_function() RETURNS TRIGGER AS $$
    BEGIN
        INSERT INTO notifications(type, user_id, event_id)
        VALUES ('EventOrganizer', NEW.user_id, NEW.event_id);
        
        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION11
CREATE OR REPLACE FUNCTION
    event_data_updated_function() RETURNS TRIGGER AS $$
    DECLARE
        u record;
    BEGIN
        FOR 
            u 
        IN
            SELECT DISTINCT user_id
            FROM (
                SELECT user_id
                FROM tickets
                WHERE event_id = NEW.id
                UNION
                SELECT user_id
                FROM favorites
                WHERE event_id = NEW.id
            ) AS users
        LOOP
            INSERT INTO notifications(type, user_id, event_id)
            VALUES ('EventUpdate', u.user_id, NEW.id);
        END LOOP;

        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION12
CREATE OR REPLACE FUNCTION
    event_announcement_creation_function() RETURNS TRIGGER AS $$
    DECLARE
        u record;
    BEGIN
        FOR 
            u 
        IN
            SELECT DISTINCT user_id
            FROM (
                SELECT user_id
                FROM tickets
                WHERE event_id = NEW.event_id
                UNION
                SELECT user_id
                FROM favorites
                WHERE event_id = NEW.event_id
            ) AS users
        LOOP
            INSERT INTO notifications(type, user_id, event_id)
            VALUES ('EventAnnouncement', u.user_id, NEW.event_id);
        END LOOP;

        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION13
CREATE OR REPLACE FUNCTION
    ticket_with_voucher_function() RETURNS TRIGGER AS $$
    BEGIN
        IF 
            (SELECT event_vouchers.event_id
            FROM event_vouchers
            WHERE event_vouchers.id = NEW.event_voucher_id) != NEW.event_id
        THEN
            RAISE EXCEPTION 'Invalid voucher for the given event';
        ELSE
            IF 
                (SELECT is_used
                FROM event_vouchers
                WHERE event_vouchers.id = NEW.event_voucher_id) = true
            THEN
                RAISE EXCEPTION 'Voucher already redeemed';
            ELSE
                UPDATE event_vouchers
                SET is_used = true
                WHERE event_vouchers.id = NEW.event_voucher_id;
            END IF;
        END IF;

        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

-- FUNCTION14
CREATE OR REPLACE FUNCTION
    issue_solving_function() RETURNS TRIGGER AS $$
    BEGIN
        IF 
            (SELECT is_admin
            FROM users
            WHERE users.id = NEW.solver_id) != true
        THEN
            RAISE EXCEPTION 'Issue can only be solved by an administrator';
        END IF;

        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';


DROP VIEW IF EXISTS event_search_fields;
CREATE VIEW event_search_fields
AS
SELECT e.id, setweight(to_tsvector(coalesce(e.title,'')), 'A') as title, 
setweight(to_tsvector(coalesce(e.description, '')), 'C') as description, 
setweight(to_tsvector(coalesce(e.location, '')), 'B') as location, 
setweight(to_tsvector(coalesce(event_categories.name ,'')), 'B') AS category, 
setweight(to_tsvector(coalesce(string_agg(tags.name, ' '),'')), 'B') AS tags

FROM events AS e
INNER JOIN event_categories ON (e.event_category_id = event_categories.id)
LEFT OUTER JOIN event_tags ON e.id = event_tags.event_id
LEFT OUTER JOIN tags ON event_tags.tag_id = tags.id
GROUP BY e.id, event_categories.name
;

-- FUNCTION15
CREATE OR REPLACE FUNCTION event_search_update() RETURNS TRIGGER AS $$
BEGIN

    IF TG_OP = 'INSERT' OR (TG_OP = 'UPDATE' AND (OLD.title <> NEW.title OR OLD.description <> NEW.description OR OLD.location <> NEW.location OR OLD.event_category_id <> NEW.event_category_id)) THEN
        UPDATE events 
        SET search = (SELECT result_search FROM 
            (SELECT title || description || location || category || tags
            AS result_search 
            FROM event_search_fields WHERE id = NEW.id)
        AS subquery)
        WHERE id = NEW.id;
    END IF;

  RETURN NEW;
END
$$ LANGUAGE 'plpgsql';

-- FUNCTION16
CREATE OR REPLACE FUNCTION event_search_update_tags() RETURNS TRIGGER AS $$
DECLARE
	temprow RECORD;
BEGIN
	
    FOR temprow IN
        SELECT id as event_id, title || description || location || category || tags
        AS result_search 
        FROM event_search_fields 
        INNER JOIN event_tags 
        ON id = event_tags.event_id
        WHERE tag_id = NEW.id
    LOOP
        UPDATE events 
        SET search = temprow.result_search
        WHERE id = temprow.event_id;
    END LOOP;

  RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

--FUNCTION17
CREATE OR REPLACE FUNCTION event_search_update_new_tags() RETURNS TRIGGER AS $$
DECLARE
	temprow RECORD;
BEGIN
	
    FOR temprow IN
        SELECT id as event_id, title || description || location || category || tags
        AS result_search 
        FROM event_search_fields 
        INNER JOIN event_tags 
        ON id = event_tags.event_id
        WHERE tag_id = NEW.tag_id
    LOOP
        UPDATE events 
        SET search = temprow.result_search
        WHERE id = temprow.event_id;
    END LOOP;

  RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

--FUNCTION18
CREATE OR REPLACE FUNCTION event_search_update_deleted_tags() RETURNS TRIGGER AS $$
DECLARE
	temprow RECORD;
BEGIN
	
    FOR temprow IN
        SELECT id as event_id, title || description || location || category || tags
        AS result_search 
        FROM event_search_fields 
        INNER JOIN event_tags 
        ON id = event_tags.event_id
        WHERE tag_id = OLD.tag_id
    LOOP
        UPDATE events 
        SET search = temprow.result_search
        WHERE id = temprow.event_id;
    END LOOP;

  RETURN OLD;
END;
$$ LANGUAGE 'plpgsql';

--FUNCTION19
CREATE OR REPLACE FUNCTION event_search_update_category() RETURNS TRIGGER AS $$
DECLARE
	temprow RECORD;
BEGIN
	
    FOR temprow IN
        SELECT event_search_fields.id as event_id, event_search_fields.title || 
        event_search_fields.description || event_search_fields.location || category || tags
        AS result_search 
        FROM event_search_fields 
        INNER JOIN events 
		ON event_search_fields.id = events.id
    
        WHERE event_category_id = NEW.id
        
    LOOP
        UPDATE events 
        SET search = temprow.result_search
        WHERE id = temprow.event_id;
    END LOOP;

  RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';


--FUNCTION20
CREATE OR REPLACE FUNCTION user_search_update() RETURNS TRIGGER AS $$
BEGIN

    IF TG_OP = 'INSERT' OR (TG_OP = 'UPDATE' AND (OLD.name <> NEW.name OR OLD.email <> NEW.email)) THEN
        UPDATE users 
        SET search = (SELECT setweight(to_tsvector(name), 'A') || setweight(to_tsvector(email), 'B')
        FROM users WHERE id = NEW.id)
        WHERE id = NEW.id;
    END IF;

  RETURN NEW;
END
$$ LANGUAGE 'plpgsql';


DROP VIEW IF EXISTS issue_search_fields;
CREATE VIEW issue_search_fields
AS
SELECT i.id, i.creator_id AS creator_id, setweight(to_tsvector(coalesce(i.title,'')), 'A') as title, 
setweight(to_tsvector(coalesce(i.content, '')), 'C') as content, 
setweight(to_tsvector(coalesce(users.name ,'')), 'B') AS creator

FROM issues AS i
INNER JOIN users ON (i.creator_id = users.id)
GROUP BY i.id, users.name
;

--FUNCTION21
CREATE OR REPLACE FUNCTION issue_search_update() RETURNS TRIGGER AS $$
BEGIN

    IF TG_OP = 'INSERT' OR (TG_OP = 'UPDATE' AND (OLD.title <> NEW.title OR OLD.content <> NEW.content )) THEN
        UPDATE issues 
        SET search = (SELECT result_search FROM 
            (SELECT title || content || creator
            AS result_search 
            FROM issue_search_fields WHERE id = NEW.id)
        AS subquery)
        WHERE id = NEW.id;
    END IF;

  RETURN NEW;
END
$$ LANGUAGE 'plpgsql';

--FUNCTION22
CREATE OR REPLACE FUNCTION issue_search_update_creator_name() RETURNS TRIGGER AS $$
DECLARE
	temprow RECORD;
BEGIN
	
    FOR temprow IN
        SELECT issue_search_fields.id as issue_id, title || content || creator
        AS result_search 
        FROM issue_search_fields 
        INNER JOIN users 
		ON issue_search_fields.creator_id = users.id
    
        WHERE creator_id = NEW.id
        
    LOOP
        UPDATE issues 
        SET search = temprow.result_search
        WHERE id = temprow.issue_id;
    END LOOP;

  RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';


--------------
-- Triggers --
--------------

DROP TRIGGER IF EXISTS insert_rating_trigger ON ratings;
DROP TRIGGER IF EXISTS update_rating_trigger ON ratings;
DROP TRIGGER IF EXISTS delete_rating_trigger ON ratings;
DROP TRIGGER IF EXISTS insert_comment_trigger ON comments;
DROP TRIGGER IF EXISTS delete_comment_trigger ON comments;
DROP TRIGGER IF EXISTS disable_event_trigger ON events;
DROP TRIGGER IF EXISTS cancel_event_trigger ON events;
DROP TRIGGER IF EXISTS activate_event_trigger ON events;
DROP TRIGGER IF EXISTS remove_attendee_trigger ON tickets;
DROP TRIGGER IF EXISTS organizer_promotion_trigger ON organizers;
DROP TRIGGER IF EXISTS event_data_updated_trigger ON events;
DROP TRIGGER IF EXISTS event_announcement_creation_trigger ON posts;
DROP TRIGGER IF EXISTS ticket_with_voucher_trigger ON tickets;
DROP TRIGGER IF EXISTS issue_solving_trigger ON issues;
DROP TRIGGER IF EXISTS insert_event_search ON events;
DROP TRIGGER IF EXISTS update_event_search ON events;
DROP TRIGGER IF EXISTS update_event_search_on_tags_update ON tags;
DROP TRIGGER IF EXISTS update_event_search_on_tags_insert ON event_tags;
DROP TRIGGER IF EXISTS update_event_search_on_tags_delete ON event_tags;
DROP TRIGGER IF EXISTS update_event_search_on_category_update ON event_categories;
DROP TRIGGER IF EXISTS insert_user_search ON users;
DROP TRIGGER IF EXISTS update_user_search ON users;
DROP TRIGGER IF EXISTS insert_issue_search ON issues;
DROP TRIGGER IF EXISTS update_issue_search ON issues;
DROP TRIGGER IF EXISTS update_issue_search_on_creator_name_update ON users;

-- TRIGGER01
CREATE TRIGGER insert_rating_trigger 
    AFTER INSERT ON ratings
    FOR EACH ROW 
    EXECUTE PROCEDURE insert_rating_function();

-- TRIGGER02
CREATE TRIGGER update_rating_trigger 
    AFTER UPDATE OF value ON ratings
    FOR EACH ROW 
    EXECUTE PROCEDURE update_rating_function();

-- TRIGGER03
CREATE TRIGGER delete_rating_trigger 
    BEFORE DELETE ON ratings
    FOR EACH ROW 
    EXECUTE PROCEDURE delete_rating_function();

-- TRIGGER04
CREATE TRIGGER insert_comment_trigger 
    AFTER INSERT ON comments
    FOR EACH ROW 
    EXECUTE PROCEDURE insert_comment_function();

-- TRIGGER05
CREATE TRIGGER delete_comment_trigger 
    BEFORE DELETE ON comments
    FOR EACH ROW 
    EXECUTE PROCEDURE delete_comment_function();

-- TRIGGER06
CREATE TRIGGER disable_event_trigger 
    AFTER UPDATE OF status ON events
    FOR EACH ROW 
    WHEN (NEW.status = 'Disabled')
    EXECUTE PROCEDURE disable_event_function();

-- TRIGGER07
CREATE TRIGGER cancel_event_trigger 
    AFTER UPDATE OF status ON events
    FOR EACH ROW 
    WHEN (NEW.status = 'Cancelled')
    EXECUTE PROCEDURE cancel_event_function();

-- TRIGGER08
CREATE TRIGGER activate_event_trigger 
    AFTER UPDATE OF status ON events
    FOR EACH ROW 
    WHEN (NEW.status = 'Active')
    EXECUTE PROCEDURE activate_event_function();

-- TRIGGER09
CREATE TRIGGER remove_attendee_trigger
    AFTER DELETE ON tickets
    FOR EACH ROW
    EXECUTE PROCEDURE remove_attendee_function();

-- TRIGGER10
CREATE TRIGGER organizer_promotion_trigger
    AFTER INSERT ON organizers
    FOR EACH ROW
    EXECUTE PROCEDURE organizer_promotion_function();

-- TRIGGER11
CREATE TRIGGER event_data_updated_trigger
    AFTER UPDATE OF title, description, price, location, 
        latitude, longitude, start_timestamp, end_timestamp 
    ON events
    FOR EACH ROW
    EXECUTE PROCEDURE event_data_updated_function();

-- TRIGGER12
CREATE TRIGGER event_announcement_creation_trigger
    AFTER INSERT ON posts
    FOR EACH ROW
    WHEN (NEW.is_announcement)
    EXECUTE PROCEDURE event_announcement_creation_function();

-- TRIGGER13
CREATE TRIGGER ticket_with_voucher_trigger
    BEFORE INSERT ON tickets
    FOR EACH ROW
    WHEN (NEW.event_voucher_id IS NOT NULL)
    EXECUTE PROCEDURE ticket_with_voucher_function();

-- TRIGGER14
CREATE TRIGGER issue_solving_trigger
    BEFORE UPDATE OF solver_id ON issues
    FOR EACH ROW
    EXECUTE PROCEDURE issue_solving_function();

--TRIGGER15
CREATE TRIGGER insert_event_search 
    AFTER INSERT ON events
    FOR EACH ROW 
    EXECUTE PROCEDURE event_search_update();

--TRIGGER16
CREATE TRIGGER update_event_search 
    AFTER UPDATE ON events
    FOR EACH ROW 
    EXECUTE PROCEDURE event_search_update();

--TRIGGER17
CREATE TRIGGER update_event_search_on_tags_update
    AFTER UPDATE ON tags
    FOR EACH ROW 
    EXECUTE PROCEDURE event_search_update_tags();

--TRIGGER18
CREATE TRIGGER update_event_search_on_tags_insert
    AFTER INSERT ON event_tags
    FOR EACH ROW 
    EXECUTE PROCEDURE event_search_update_new_tags();

--TRIGGER19
CREATE TRIGGER update_event_search_on_tags_delete
    BEFORE DELETE ON event_tags
    FOR EACH ROW 
    EXECUTE PROCEDURE event_search_update_deleted_tags();

--TRIGGER20
CREATE TRIGGER update_event_search_on_category_update
    AFTER UPDATE ON event_categories
    FOR EACH ROW 
    EXECUTE PROCEDURE event_search_update_category();

--TRIGGER21
CREATE TRIGGER insert_user_search 
    AFTER INSERT ON users
    FOR EACH ROW 
    EXECUTE PROCEDURE user_search_update();

--TRIGGER22
CREATE TRIGGER update_user_search 
    AFTER UPDATE ON users
    FOR EACH ROW 
    EXECUTE PROCEDURE user_search_update();

--TRIGGER23
CREATE TRIGGER insert_issue_search 
    AFTER INSERT ON issues
    FOR EACH ROW 
    EXECUTE PROCEDURE issue_search_update();

--TRIGGER24
CREATE TRIGGER update_issue_search 
    AFTER UPDATE ON issues
    FOR EACH ROW 
    EXECUTE PROCEDURE issue_search_update();

--TRIGGER25
CREATE TRIGGER update_issue_search_on_creator_name_update
    AFTER UPDATE ON users
    FOR EACH ROW 
    EXECUTE PROCEDURE issue_search_update_creator_name();


-------------
-- Indexes --
-------------

DROP INDEX IF EXISTS voucher_code_index;
DROP INDEX IF EXISTS announcements_index;
DROP INDEX IF EXISTS discussion_posts_index;
DROP INDEX IF EXISTS comments_index;
DROP INDEX IF EXISTS tickets_index;
DROP INDEX IF EXISTS issues_timestamp_index;
DROP INDEX IF EXISTS notifications_index;
DROP INDEX IF EXISTS event_invite_notification_spam;
DROP INDEX IF EXISTS events_search_index;
DROP INDEX IF EXISTS users_search_index;
DROP INDEX IF EXISTS issues_search_index;

--IDX01
CREATE INDEX voucher_code_index ON event_vouchers USING btree (code, event_id);

--IDX02
CREATE INDEX announcements_index ON posts USING btree (event_id, is_announcement, timestamp) WHERE is_announcement = true;

--IDX03
CREATE INDEX discussion_posts_index ON posts USING btree (event_id, is_announcement, rating, timestamp) WHERE is_announcement = false;

--IDX04
CREATE INDEX comments_index ON comments USING btree (post_id, timestamp);

--IDX05
CREATE INDEX tickets_index ON tickets USING btree (user_id, event_id);

--IDX06
CREATE INDEX issues_timestamp_index ON issues USING btree(is_solved, timestamp);

--IDX07
CREATE INDEX notifications_index ON notifications USING btree(user_id, is_dismissed, timestamp);

--IDX08
CREATE INDEX events_search_index ON events USING gist(search);

--IDX09
CREATE INDEX users_search_index ON users USING gin(search);

--IDX10
CREATE INDEX issues_search_index ON issues USING gin(search);

--IDX11
CREATE UNIQUE INDEX event_invite_notification_spam ON notifications (user_id, event_id, type) WHERE type = 'EventInvitation';














DELETE FROM users;
DELETE FROM event_categories;
DELETE FROM events;
DELETE FROM tags;
DELETE FROM event_vouchers;
DELETE FROM posts;
DELETE FROM comments;
DELETE FROM ratings;
DELETE FROM tickets;
DELETE FROM organizers;
DELETE FROM favorites;
DELETE FROM event_tags;
DELETE FROM issues;
DELETE FROM notifications;

-- Users
INSERT INTO users(name, email, password, is_admin) VALUES 
    ('Mark', 'user1@email.net', 'XXXXX', true),
    ('Lucy', 'user2@email.net', 'XXXXX', true),
    ('Susan', 'user3@email.net', 'XXXXX', true),
    ('Peter', 'user4@email.net', 'XXXXX', true),
    ('John', 'user5@email.net', 'XXXXX', true);
INSERT INTO users(name, email, password) VALUES 
    ('Jonathan', 'user6@email.net', 'XXXXX'),
    ('Kevin', 'user23@email.net', 'XXXXX'),
    ('Richard', 'user7@email.net', 'XXXXX'),
    ('Martha', 'user8@email.net', 'XXXXX'),
    ('Louis', 'user9@email.net', 'XXXXX'),
    ('Mia', 'user10@email.net', 'XXXXX'),
    ('Anna', 'user11@email.net', 'XXXXX'),
    ('Bruno', 'user12@email.net', 'XXXXX'),
    ('Carlos', 'user13@email.net', 'XXXXX'),
    ('Paul', 'user14@email.net', 'XXXXX'),
    ('Charles', 'user15@email.net', 'XXXXX'),
    ('Paula', 'user16@email.net', 'XXXXX'),
    ('Francis', 'user17@email.net', 'XXXXX'),
    ('Gil', 'user18@email.net', 'XXXXX'),
    ('Jennifer', 'user19@email.net', 'XXXXX'),
    ('Dominic', 'user20@email.net', 'XXXXX'),
    ('Nathan', 'user21@email.net', 'XXXXX'),
    ('Ethan', 'user22@email.net', 'XXXXX');

-- Event Categories
INSERT INTO event_categories(id, name) VALUES 
    (1, 'Sports'),
    (2, 'Science'),
    (3, 'Art'),
    (4, 'Learning'),
    (5, 'Technology'),
    (6, 'Health Care'),
    (7, 'Music');

-- Events
INSERT INTO events(title, description, price, location, latitude, longitude, start_timestamp, end_timestamp, event_category_id, status, user_id) VALUES 
    ('SINF 2020', 'Very good event', 1, 'FEUP, Porto, Portugal', 41.1772571, -8.5955435, to_timestamp('16-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), to_timestamp('17-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 4, 'Active', 2);
INSERT INTO events(title, description, price, start_timestamp, event_category_id, status, user_id) VALUES 
    ('SINF 2021', 'Also a very good event', 0, to_timestamp('20-05-2021 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 4, 'Active', 2),
    ('SINF 2035', 'Event.', 1.50, to_timestamp('21-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 1, 'Active', 1),
    ('ENEI', '...', 1, to_timestamp('22-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 3),
    ('Meeting', '...', 1, to_timestamp('30-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Disabled', 3),
    ('Great Meeting', '...', 1, to_timestamp('31-06-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 1),
    ('Another Great Meeting', '...', 1, to_timestamp('31-06-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 1),
    ('Tour', '......', 1, to_timestamp('22-07-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 7),
    ('Party', '......', 1, to_timestamp('22-08-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 6),
    ('A great party', '...', 1, to_timestamp('22-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Cancelled', 17),
    ('Birthday', '...', 1, to_timestamp('31-05-2020 17:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 12),
    ('Flight party', '......', 1, to_timestamp('22-07-2022 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 14),
    ('Food meeting', '...', 1, to_timestamp('26-06-2020 20:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Disabled', 20),
    ('Food competition', '...', 1, to_timestamp('29-05-2021 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 21),
    ('Wine tasting', '...', 1, to_timestamp('30-12-2020 22:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 13),
    ('Boat trip', 'A B C', 1, to_timestamp('23-05-2020 12:02:38', 'dd-mm-yyyy hh24:mi:ss'), 3, 'Cancelled', 4);

-- Tags
INSERT INTO tags(name) VALUES 
    ('Engineering'),
    ('Fun'),
    ('GreatMovie'),
    ('NIAEFEUP'),
    ('Football'),
    ('Basketball'),
    ('Competition'),
    ('Teamwork'),
    ('Bonding'),
    ('Helping'),
    ('Nature'),
    ('Yoga'),
    ('Food'),
    ('Boats'),
    ('Handball'),
    ('Programming'),
    ('C++'),
    ('Databases'),
    ('Web Engineer'),
    ('Cars'),
    ('Bycicles'),
    ('Running'),
    ('Party'),
    ('Jogging'),
    ('Respect');

    -- Event Vouchers
INSERT INTO event_vouchers(code, event_id, user_id) VALUES 
    ('XPTO1001', 2, 1),
    ('XPTO1002', 2, 1),
    ('XPTO1500', 4, 3),
    ('XPTO1192', 4, 7),
    ('XPTO1193', 7, 13),
    ('XPTO1194', 7, 22),
    ('XPTO1195', 7, 21),
    ('XPTO1196', 7, 21),
    ('XPTO1197', 7, 16),
    ('XPTO1198', 13, 12),
    ('XPTO1199', 13, 19),
    ('XPTO1312', 12, 12),
    ('XPTO1314', 11, 19),
    ('XPTO1316', 10, 6),
    ('XPTO1318', 5, 6),
    ('XPTO2193', 6, 7),
    ('XPTO2195', 7, 8),
    ('XPTO2196', 14, 3),
    ('XPTO2352', 5, 4),
    ('XPTO2353', 4, 5),
    ('XPTO2354', 10, 6),
    ('XPTO2277', 15, 7),
    ('XPTO2823', 15, 21),
    ('XPTO1562', 3, 18),
    ('XPTO8134', 10, 6),
    ('XPTO8135', 5, 6),
    ('XPTO8136', 6, 7),
    ('XPTO8137', 7, 8),
    ('XPTO8138', 14, 3),
    ('XPTO8139', 5, 4),
    ('XPTO8140', 4, 5),
    ('XPTO8141', 10, 6),
    ('XPTO8142', 15, 7),
    ('XPTO8143', 15, 21);
    
-- Posts
INSERT INTO posts(content, event_id, user_id, is_announcement) VALUES 
    ('Announcement Number 1', 1, 1, true),
    ('Announcement Number 2', 3, 6, true),
    ('Announcement Number 3', 4, 7, true),
    ('Announcement Number 4', 5, 8, true),
    ('Announcement Number 5', 3, 9, true),
    ('Announcement Number 6', 7, 10, true),
    ('Announcement Number 7', 7, 11, true),
    ('Announcement Number 8', 13, 12, true),
    ('Announcement Number 9', 12, 13, true),
    ('Announcement Number 10', 11, 14, true),
    ('Announcement Number 11', 10, 15, true),
    ('Announcement Number 12', 9, 16, true),
    ('Post Number 1', 5, 2, false),
    ('Post Number 2', 6, 3, false),
    ('Post Number 3', 1, 4, false),
    ('Post Number 4', 1, 5, false),
    ('Post Number 5', 2, 6, false),
    ('Post Number 6', 2, 7, false),
    ('Post Number 7', 3, 8, false),
    ('Post Number 8', 3, 9, false),
    ('Post Number 9', 8, 19, false),
    ('Post Number 10', 8, 20, false),
    ('Post Number 11', 9, 21, false),
    ('Post Number 12', 10, 22, false),
    ('Post Number 13', 11, 23, false);

-- Comments
INSERT INTO comments(content, post_id, user_id) VALUES
    ('Comment number 1!', 14, 1),
    ('Comment number 2!', 14, 2),
    ('Comment number 3!', 15, 3),
    ('Comment number 4!', 15, 4),
    ('Comment number 5!', 16, 14),
    ('Comment number 6!', 17, 15),
    ('Comment number 7!', 18, 16),
    ('Comment number 8!', 19, 16),
    ('Comment number 9!', 19, 22),
    ('Comment number 10!', 18, 21),
    ('Comment number 11!', 19, 20),
    ('Comment number 12!', 20, 16),
    ('Comment number 13!', 20, 9),
    ('Comment number 14!', 20, 4),
    ('Comment number 15!', 19, 4),
    ('Comment number 16!', 21, 23),
    ('Comment number 17!', 21, 17),
    ('Comment number 18!', 21, 18),
    ('Comment number 19!', 21, 4),
    ('Comment number 20!', 21, 5),
    ('Comment number 21!', 21, 6),
    ('Comment number 22!', 21, 7),
    ('Comment number 23!', 14, 8);

-- Ratings
INSERT INTO ratings(user_id, post_id, value) VALUES
    (1, 13, 1),
    (2, 13, -1),
    (2, 14, 1),
    (4, 16, 1),
    (14, 15, 1),
    (14, 16, -1),
    (17, 20, -1),
    (18, 14, 1),
    (19, 14, -1),
    (20, 14, -1),
    (21, 14, 1),
    (21, 15, 1),
    (21, 16, -1),
    (22, 17, -1),
    (23, 18, 1),
    (10, 19, 1),
    (11, 19, -1),
    (12, 19, -1),
    (12, 20, 1),
    (11, 21, -1),
    (13, 22, 1),
    (13, 23, -1),
    (7, 15, 1),
    (8, 15, -1),
    (9, 16, 1);

-- Tickets
INSERT INTO tickets(user_id, event_id, type, paypal_order_id)
    VALUES (1, 3, 'Paypal', 'PAYPAL-CONFIRMATION-6424');
INSERT INTO tickets(user_id, event_id, type, paypal_order_id, nif, billing_name)
    VALUES (5, 2, 'Paypal', 'PAYPAL-CONFIRMATION-7512', 245024952, 'Peter');
INSERT INTO tickets(user_id, event_id, type, event_voucher_id) VALUES 
    (1, 2, 'Voucher', 1),
    (1, 2, 'Voucher', 2),
    (3, 4, 'Voucher', 3),
    (4, 4, 'Voucher', 4),
    (5, 7, 'Voucher', 5),
    (6, 7, 'Voucher', 6),
    (7, 7, 'Voucher', 7),
    (8, 7, 'Voucher', 8),
    (9, 7, 'Voucher', 9),
    (10, 13, 'Voucher', 10),
    (11, 13, 'Voucher', 11),
    (12, 12, 'Voucher', 12),
    (13, 11, 'Voucher', 13),
    (14, 10, 'Voucher', 14),
    (15, 5, 'Voucher', 15),
    (16, 6, 'Voucher', 16),
    (17, 7, 'Voucher', 17),
    (18, 14, 'Voucher', 18),
    (19, 5, 'Voucher', 19),
    (10, 4, 'Voucher', 20),
    (21, 10, 'Voucher', 21),
    (22, 15, 'Voucher', 22),
    (23, 15, 'Voucher', 23),
    (7, 3, 'Voucher', 24);

-- Organizers
INSERT INTO organizers(user_id, event_id) VALUES 
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (14, 5),
    (15, 6),
    (16, 7),
    (17, 8),
    (18, 9),
    (19, 10),
    (20, 11),
    (21, 12),
    (22, 13),
    (23, 14),
    (23, 15),
    (11, 1),
    (8, 2),
    (9, 3),
    (10, 4),
    (12, 5),
    (13, 6),
    (13, 7),
    (14, 8),
    (15, 9),
    (15, 10),
    (15, 12),
    (3, 13),
    (4, 14),
    (5, 15);

-- Favorites
INSERT INTO favorites(user_id, event_id) VALUES 
    (5, 5),
    (4, 5),
    (3, 5),
    (2, 2),
    (18, 7),
    (12, 9),
    (13, 11),
    (17, 13),
    (15, 6),
    (14, 8),
    (13, 10),
    (9, 4),
    (8, 3),
    (7, 2),
    (6, 1),
    (20, 5),
    (20, 6),
    (20, 7),
    (21, 1),
    (21, 2),
    (21, 3),
    (22, 11),
    (22, 12),
    (22, 13),
    (23, 14);

-- Event Tags
INSERT INTO event_tags(event_id, tag_id) VALUES 
    (1, 1),
    (1, 2),
    (1, 3),
    (1, 4),
    (2, 5),
    (2, 6),
    (2, 7),
    (2, 8),
    (3, 9),
    (3, 10),
    (3, 11),
    (3, 12),
    (4, 13),
    (4, 14),
    (4, 15),
    (4, 16),
    (5, 17),
    (5, 18),
    (5, 19),
    (5, 20),
    (6, 21),
    (6, 22),
    (6, 23),
    (6, 24),
    (7, 25),
    (7, 1),
    (7, 2),
    (7, 3),
    (8, 4),
    (8, 5),
    (8, 6),
    (8, 7),
    (9, 8),
    (9, 9),
    (9, 10),
    (9, 11),
    (10, 12),
    (10, 13),
    (10, 14),
    (10, 15),
    (11, 16),
    (11, 17),
    (11, 18),
    (11, 19),
    (12, 20),
    (12, 21),
    (12, 22),
    (12, 23),
    (13, 24),
    (13, 25),
    (13, 1),
    (13, 2),
    (14, 3),
    (14, 4),
    (14, 5),
    (14, 6),
    (15, 7),
    (15, 8),
    (15, 9),
    (15, 10);

-- Issues
INSERT INTO issues(title, content, creator_id, referenced_user)
    VALUES ('Issue #1', 'I have an issue', 1, 4);
INSERT INTO issues(title, content, creator_id, referenced_event)
    VALUES ('Issue #2', 'I have an issue', 1, 3);
INSERT INTO issues(title, content, creator_id, referenced_post)
    VALUES ('Issue #3', 'I have an issue', 2, 3);
INSERT INTO issues(title, content, creator_id, referenced_comment)
    VALUES ('Issue #4', 'I have an issue', 2, 5);
INSERT INTO issues(title, content, creator_id) VALUES 
    ('Issue #5', 'I have an issue', 5),
    ('Issue #6', 'I have an issue', 6),
    ('Issue #7', 'I have an issue', 7),
    ('Issue #8', 'I have an issue', 8),
    ('Issue #9', 'I have an issue', 9),
    ('Issue #10', 'I have an issue', 10),
    ('Issue #11', 'I have an issue', 11),
    ('Issue #12', 'I have an issue', 12),
    ('Issue #13', 'I have an issue', 13),
    ('Issue #14', 'I have an issue', 14),
    ('Issue #15', 'I have an issue', 15),
    ('Issue #16', 'I have an issue', 16),
    ('Issue #17', 'I have an issue', 17),
    ('Issue #18', 'I have an issue', 18),
    ('Issue #19', 'I have an issue', 19),
    ('Issue #20', 'I have an issue', 20);

-- Notifications
INSERT INTO notifications(type, content, user_id, issue_id)
    VALUES ('IssueNotification', 'We, the admins, solved your issue', 1, 2);
INSERT INTO notifications(type, user_id, event_id, is_dismissed)
    VALUES ('EventOrganizer', 2, 2, true);
INSERT INTO notifications(type, user_id, event_id) VALUES 
    ('EventInvitation', 4, 1), 
    ('EventInvitation', 5, 2), 
    ('EventInvitation', 6, 3), 
    ('EventDisabling', 7, 4), 
    ('EventDisabling', 8, 5), 
    ('EventDisabling', 9, 6),
    ('EventActivation', 10, 7),
    ('EventActivation', 11, 8),
    ('EventActivation', 12, 9),
    ('EventCancellation', 13, 10),
    ('EventCancellation', 14, 11),
    ('EventCancellation', 15, 12),
    ('EventRemoval', 16, 13),
    ('EventRemoval', 17, 14),
    ('EventRemoval', 18, 15),
    ('EventOrganizer', 19, 1),
    ('EventOrganizer', 20, 2),
    ('EventOrganizer', 21, 3),
    ('EventUpdate', 22, 4),
    ('EventUpdate', 23, 5),
    ('EventUpdate', 1, 6),
    ('EventAnnouncement', 2, 7),
    ('EventAnnouncement', 3, 8),
    ('EventAnnouncement', 4, 9);