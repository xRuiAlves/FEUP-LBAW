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

-----------
-- Types --
-----------

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
    name VARCHAR(20) NOT NULL CONSTRAINT event_category_name_unique UNIQUE
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
	is_disabled BOOLEAN NOT NULL DEFAULT false,
	is_cancelled BOOLEAN NOT NULL DEFAULT false,
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
    AFTER UPDATE OF is_disabled ON events
    FOR EACH ROW 
    WHEN (NEW.is_disabled = true)
    EXECUTE PROCEDURE disable_event_function();

-- TRIGGER07
CREATE TRIGGER cancel_event_trigger 
    AFTER UPDATE OF is_cancelled ON events
    FOR EACH ROW 
    WHEN (NEW.is_cancelled = true)
    EXECUTE PROCEDURE cancel_event_function();

-- TRIGGER08
CREATE TRIGGER activate_event_trigger 
    AFTER UPDATE OF is_disabled ON events
    FOR EACH ROW 
    WHEN (NEW.is_disabled = false)
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
    ('Mark', 'mark.williams@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W', true),
    ('Lucy', 'lucy1997@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W', true),
    ('Susan', 'susana123@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W', true),
    ('Peter', 'peter_football@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W', true),
    ('Mr. Admin', 'admin@lbaw.pt', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W', true),
    ('John', 'john_the_man@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W', true);
INSERT INTO users(name, email, password) VALUES 
    ('Jonathan', 'jonny.mp4@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Regular User', 'user@lbaw.pt', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Kevin', 'kps88@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Richard', 'ricky_dodo@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Martha', 'martha_stevenson@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Louis', 'louis@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Mia', 'mia.ll94@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Anna', 'anna_bannanna@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Bruno', 'bruno_lopes@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Carlos', 'charlesey@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Paul', 'paulkps@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Charles', 'charleyyy@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Paula', 'paula_abdul_jabbar@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Francis', 'frakyy1337@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Gil', 'gilson@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Jennifer', 'jenny_ford@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Dominic', 'dom_html@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Nathan', 'naynay1972@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W'),
    ('Ethan', 'et_than@email.net', '$2y$10$AUu.UDFET4ym3OCW0OthOeR0wdpSFIxCtIWA2M1bOsmVRKkihCP3W');

-- Event Categories
INSERT INTO event_categories(name) VALUES 
    ('Sports'),
    ('Science'),
    ('Art'),
    ('Learning'),
    ('Technology'),
    ('Health Care'),
    ('Music'),
    ('Travelling'),
    ('Nature'),
    ('Entertainment');

-- Events
INSERT INTO events(title, description, price, location, latitude, longitude, start_timestamp, end_timestamp, event_category_id, user_id) VALUES 
    ('SINF 2020', 'Semana de Informática is a conference that aims to bring together students of informatics, computer science and engineering to learn, socialize and connect with each other and the business world. The event takes place every year in Faculdade de Engenharia da Universidade do Porto, in Portugal.', 1.00, 'FEUP, Porto, Portugal', 41.1780, -8.5980, to_timestamp('16-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), to_timestamp('17-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 4, 2);
INSERT INTO events(title, location, latitude, longitude, description, price, start_timestamp, event_category_id, is_cancelled, is_disabled, user_id) VALUES 
    ('Hash Code 2020', 'Delhi, India', 28.644800, 77.216721, 'Hash Code is a team programming competition, organized by Google, for students and professionals around the world. You pick your team and programming language and we pick an engineering problem for you to solve. This year’s contest kicks off with an Online Qualification Round, where your team can compete from wherever you’d like, including from one of our Hash Code hubs. Top teams will then be invited to a Google office for the Final Round.', 0.00, to_timestamp('20-05-2021 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 5, false, false, 2),
    ('Visiting Albania', 'Tirana, Albania', 41.327953, 19.819025, 'A part of Illyria in ancient times and later of the Roman Empire, Albania was ruled by the Byzantine Empire from 535 to 1204. An alliance (1444–1466) of Albanian chiefs failed to halt the advance of the Ottoman Turks, and the country remained under at least nominal Turkish rule for more than four centuries, until it proclaimed its independence on Nov. 28, 1912.', 1.00, to_timestamp('22-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 8, false, false, 3),
    ('Enjoying Berlin', 'Berlin, Germany', 52.520008, 13.404954, 'Located in central Europe, Germany is made up of the North German Plain, the Central German Uplands (Mittelgebirge), and the Southern German Highlands. The Bavarian plateau in the southwest averages 1,600 ft (488 m) above sea level, but it reaches 9,721 ft (2,962 m) in the Zugspitze Mountains, the highest point in the country. Germanys major rivers are the Danube, the Elbe, the Oder, the Weser, and the Rhine. Germany is about the size of Montana.', 1.00, to_timestamp('31-06-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 8, false, false, 1),
    ('Tech Meeting - UK Edition', 'London, United Kingdom', 51.509865, -0.118092, 'The United Kingdom is a constitutional monarchy and parliamentary democracy, with a queen and a parliament that has two houses: the House of Lords, with 574 life peers, 92 hereditary peers, and 26 bishops; and the House of Commons, which has 651 popularly elected members. Supreme legislative power is vested in parliament, which sits for five years unless dissolved sooner. The House of Lords was stripped of most of its power in 1911, and now its main function is to revise legislation. In Nov. 1999, hundreds of hereditary peers were expelled in an effort to make the body more democratic. The executive power of the Crown is exercised by the cabinet, headed by the prime minister.', 1, to_timestamp('31-06-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 5, false, false, 1),
    ('Bird Watching', 'São Tomé e Príncipe', 0.33654, 6.72732, 'São Tomé and Príncipe, believed to have been originally uninhabited, were explored by Portuguese navigators in 1471 and settled by the end of the century. Intensive cultivation by slave labor made the islands a major producer of sugar during the 17th century, but output declined until the introduction of coffee and cocoa in the 19th century brought new prosperity. The island of São Tomé was the worlds largest producer of cocoa in 1908, and the crop is still its most important. Working conditions for laborers, however, were horrendous, and in 1909 British and German chocolate manufacturers boycotted São Tomé cocoa in protest. An exile liberation movement was formed in 1953 after Portuguese landowners quelled labor riots by killing several hundred African workers.', 2.00, to_timestamp('22-07-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 9, false, false, 7),
    ('Lets Talk about Economy', 'Warsow, Poland', 52.237049, 21.017532, 'Great (north) Poland was founded in 966 by Mieszko I, who belonged to the Piast dynasty. The tribes of southern Poland then formed Little Poland. In 1047, both Great Poland and Little Poland united under the rule of Casimir I the Restorer. Poland merged with Lithuania by royal marriage in 1386. The Polish-Lithuanian state reached the peak of its power between the 14th and 16th centuries, scoring military successes against the (Germanic) Knights of the Teutonic Order, the Russians, and the Ottoman Turks.', 3.50, to_timestamp('22-08-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, false, false, 6),
    ('The Story of Malta', 'Valleta, Malta', 35.89972, 14.51472, 'The strategic importance of Malta was recognized by the Phoenicians, who occupied it, as did, in turn, the Greeks, Carthaginians, and Romans. The apostle Paul was shipwrecked there in A.D. 60. With the division of the Roman Empire in A.D. 395, Malta was assigned to the eastern portion dominated by Constantinople. Between 870 and 1090, it came under Arab rule. In 1091, the Norman noble Roger I, then ruler of Sicily, came to Malta with a small retinue and defeated the Arabs. The Knights of St. John (Malta), who obtained the three habitable Maltese islands of Malta, Gozo, and Comino from Charles V in 1530, reached their highest fame when they withstood an attack by superior Turkish forces in 1565. Napoléon seized Malta in 1798, but the French forces were ousted by British troops the next year, and British rule was confirmed by the Treaty of Paris in 1814.', 0.00, to_timestamp('22-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 4, true, false, 17),
    ('Dancing the Salsa', 'Lisbon, Portugal', 38.71667, -9.13333, 'Salsa is a popular form of social dance originating from Cuban folk dances. The movements of Salsa are a combination of the Afro-Cuban dances Son, cha-cha-cha, Mambo, Rumba, and the Danzón. The dance, along with salsa music, saw major development in the mid-1970s in New York. Different regions of Latin America and the United States have distinct salsa styles of their own, such as Cuban, Puerto Rican, Cali Colombia, L.A. and New York styles. Salsa dance socials are commonly held in night clubs, bars, ballrooms, restaurants, and outside, especially when part of an outdoor festival.', 1.20, to_timestamp('31-05-2020 17:36:38', 'dd-mm-yyyy hh24:mi:ss'), 3, false, false, 12),
    ('Spanish Lecture: Avión', 'Madrid, Spain', 38.71667, -9.13333, 'Un avión (del francés avion, y este como forma aumentativa del latín avis, ave), también denominado aeroplano, es un aerodino de ala fija, o aeronave con mayor densidad que el aire, dotado de alas y un espacio de carga, y capaz de volar impulsado por ninguno, uno o más motores. Los aeroplanos incluyen a los monoplanos, los biplanos y los triplanos. Los aeroplanos sin motor se denominan planeadores o veleros, y han sido usados desde los inicios de la aviación, para la llamada aviación deportiva, e incluso para el transporte de tropas durante la Segunda Guerra Mundial.', 15.75, to_timestamp('22-07-2022 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 4, false, false, 14),
    ('Food meeting: Tortilla', 'Madrid, Spain', 38.71667, -9.13333, 'La tortilla de patata, tortilla de patatas o tortilla española —también llamada tortilla de papas en Hispanoamérica, Canarias o Andalucía — es una tortilla (es decir, huevo batido, cuajado con aceite en la sartén) con patatas aunque también se le puede añadir más ingredientes como cebolla. Se trata de uno de los platos más conocidos y emblemáticos de la cocina española, siendo un producto muy popular que se puede encontrar en casi cualquier bar o restaurante de España.​', 22.50, to_timestamp('26-06-2020 20:36:38', 'dd-mm-yyyy hh24:mi:ss'), 6, false, true, 20),
    ('Russia and Chess', 'Moscow, Russia', 55.751244, 37.618423, 'Шахматът се играе върху квадратна дъска, която е разделена на 64 (8×8) квадратчета. Условно цветът на 32 от тези квадратчета е бял, а на другите 32 – черен. Цветовете на полетата се редуват, като се изисква първото поле отляво на първия ред от страната на всеки играч да е черно. Всеки играч разполага в началото с 8 пешки, 2 топа (или тура), 2 коня, 2 офицера, 1 дама (или популярно царица) и 1 цар. Целта на играта е да се създаде такава ситуация, при която е пленен царят на противника. Това се нарича мат или матиране. За да се постигне това, фигурите извършват движения по дъската, наречени ходове, съблюдавайки правилата за движение на фигурите. Първи на ход са винаги белите, а след тях черните. Когато фигура от един цвят може да се премести на дадено поле, но то е заето от противникова фигура, се извършва „вземане“. При това действие фигурата, която е била първоначално на полето, се премахва от дъската, а другата се поставя на нейно място. Вземат се само фигури на противника. За документиране на партия се използва шахматната нотация.', 0.00, to_timestamp('29-05-2021 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 1, false, false, 21),
    ('Wine tasting', 'London, England', 51.509865, -0.118092, 'Wine is an alcoholic drink made from fermented grapes. Yeast consumes the sugar in the grapes and converts it to ethanol, carbon dioxide, and heat. Different varieties of grapes and strains of yeasts produce different styles of wine. These variations result from the complex interactions between the biochemical development of the grape, the reactions involved in fermentation, the terroir, and the production process. Many countries enact legal appellations intended to define styles and qualities of wine. These typically restrict the geographical origin and permitted varieties of grapes, as well as other aspects of wine production. Wines not made from grapes include rice wine and fruit wines such as plum, cherry, pomegranate, currant and elderberry.', 0.00, to_timestamp('30-12-2020 22:36:38', 'dd-mm-yyyy hh24:mi:ss'), 10, false, false, 13),
    ('Aristídes de Sousa Mendes', 'Freixo de Espada a Cinta, Portugal', 41.083, -6.817, 'Aristides de Sousa Mendes, também conhecido por Aristides de Sousa Mendes do Amaral e Abranches, nome para qual o alterou, (Cabanas de Viriato, 19 de julho de 1885 — Lisboa, 3 de abril de 1954) foi um cônsul Português. Enquanto Cônsul de Portugal em Bordéus no ano da invasão de França pela Alemanha Nazi na Segunda Guerra Mundial, desafiou ordens expressas do presidente António de Oliveira Salazar, que acumulava a função de ministro dos Negócios Estrangeiros, e durante três dias e três noites concedeu milhares de vistos de entrada em Portugal a refugiados, principalmente de origem judia que fugiam da Alemanha, mas também outros indivíduos que simplesmente procuravam simples asilo, pois desejavam fugir de França em 1940.', 9.99, to_timestamp('23-05-2020 12:02:38', 'dd-mm-yyyy hh24:mi:ss'), 4, true, false, 4),
    ('Lets talk about Smarthphones', 'Braga, Portugal', 41.55032, -8.42005, 'Smartphones are a class of mobile phones and of multi-purpose mobile computing devices. They are distinguished from feature phones by their stronger hardware capabilities and extensive mobile operating systems, which facilitate wider software, internet (including web browsing over mobile broadband), and multimedia functionality (including music, video, cameras, and gaming), alongside core phone functions such as voice calls and text messaging. Smartphones typically include various sensors that can be leveraged by their software, such as a magnetometer, proximity sensors, barometer, gyroscope and accelerometer, and support wireless communications protocols such as Bluetooth, Wi-Fi, and satellite navigation.', 9.50, to_timestamp('25-12-2020 15:05:55', 'dd-mm-yyyy hh24:mi:ss'), 5, false, false, 6);

-- Tags
INSERT INTO tags(id, name) VALUES 
    (1, 'Fun'),
    (2, 'Roadtrip'),
    (3, 'Semana Informática'),
    (4, 'Engeneering'),
    (5, 'Computers'),
    (6, 'Internships'),
    (7, 'Goodies'),
    (8, 'Google'),
    (9, 'HashCode'),
    (10, 'Programming Competition'),
    (11, 'Optimization'),
    (12, 'Tirana'),
    (13, 'Future of Technology'),
    (14, 'United Kingdom'),
    (15, 'Innovation'),
    (16, 'Birds'),
    (17, 'Bird Watching'),
    (18, 'Nature'),
    (19, 'Economy'),
    (20, 'Finance'),
    (21, 'Infra-structure'),
    (22, 'History'),
    (23, 'Dancing'),
    (24, 'Physical Exercise'),
    (25, 'Airplane'),
    (26, 'Flying'),
    (27, 'Food Tasting'),
    (28, 'Spanish Food'),
    (29, 'Mediterranean'),
    (30, 'Chess'),
    (31, 'Chaturanga'),
    (32, 'Alcohol'),
    (33, 'Wine Tasting'),
    (34, 'Vintage Wines'),
    (35, 'Philanthropist'),
    (36, 'World War II'),
    (37, 'WW2'),
    (38, 'Hero'),
    (39, 'Smartphones'),
    (40, 'Phones');

    

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
INSERT INTO posts(content, event_id, user_id, is_announcement, timestamp) VALUES 
    ('Bring your coats guys, it is gonna be sunny', 1, 1, true, to_timestamp('01-03-2020 15:21:44', 'dd-mm-yyyy hh24:mi:ss')),
    ('We are happy to inform you that there will be giveways in the event!', 1, 2, true, to_timestamp('02-03-2020 16:22:45', 'dd-mm-yyyy hh24:mi:ss')),
    ('I hope you are excited for the event! Our speaker sure are!', 1, 1, true, to_timestamp('03-03-2020 17:23:46', 'dd-mm-yyyy hh24:mi:ss')),
    ('To those who are wondering: Yes, the food is completely free.', 1, 11, true, to_timestamp('04-03-2020 18:24:47', 'dd-mm-yyyy hh24:mi:ss')),
    ('The workshop that was going to take place in room B301 was moved to B303 due to logistics reasons. Thanks for the understanding.', 1, 1, true, to_timestamp('05-03-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss')),
    ('Dont forget to follow us on the social networks!', 1, 11, true, to_timestamp('06-03-2020 19:25:34', 'dd-mm-yyyy hh24:mi:ss')),
    ('Bring your umbrella guys, it is gonna be rainy!', 2, 8, true, to_timestamp('07-03-2020 11:31:35', 'dd-mm-yyyy hh24:mi:ss')),
    ('The event might start 5 minutes later than expected due to logistics reasons. Thank you for your understanding.', 3, 9, true, to_timestamp('08-03-2020 12:32:36', 'dd-mm-yyyy hh24:mi:ss')),
    ('Dont forget to bring your cameras, there will be good opportunities for good photos!', 4, 4, true, to_timestamp('09-03-2020 13:33:51', 'dd-mm-yyyy hh24:mi:ss')),
    ('I advise you bring notepads to take your notes.', 5, 12, true, to_timestamp('10-03-2020 14:33:52', 'dd-mm-yyyy hh24:mi:ss')),
    ('For those who want to bring food from home, there will be microwaves to heat up your food!', 6, 13, true, to_timestamp('11-03-2020 15:34:53', 'dd-mm-yyyy hh24:mi:ss')),
    ('We announce that we will be offering lunch to the first 15 people to arrive! COME FAST!', 7, 16, true, to_timestamp('12-03-2020 16:35:54', 'dd-mm-yyyy hh24:mi:ss')),
    ('I hope you guys are ready for the event of your lives!', 8, 17, true, to_timestamp('13-03-2020 17:36:55', 'dd-mm-yyyy hh24:mi:ss')),
    ('Unfortunately, we are sad to inform that our guide Sarah is sick. However, Howard will cover her shift and we asure you he is just as competent. Thank you.', 9, 15, true, to_timestamp('14-03-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss')),
    ('Bring your good mood, cuz so will we! ;)', 10, 14, true, to_timestamp('15-03-2020 18:41:56', 'dd-mm-yyyy hh24:mi:ss')),
    ('Bring your coats guys, it is gonna be sunny', 11, 20, true, to_timestamp('16-03-2020 19:42:57', 'dd-mm-yyyy hh24:mi:ss')),
    ('To those wondering if there will be food: yes. there will.', 12, 15, true, to_timestamp('17-03-2020 11:43:58', 'dd-mm-yyyy hh24:mi:ss')),
    ('The event might start 15 minutes later than expected due to logistics reasons. Thank you for your understanding.', 13, 13, true, to_timestamp('18-03-2020 12:4:14', 'dd-mm-yyyy hh24:mi:ss')),
    ('It is official: Drinks are on us!!', 14, 23, true, to_timestamp('19-03-2020 13:45:11', 'dd-mm-yyyy hh24:mi:ss')),
    ('Bring your coats guys, it is gonna be sunny', 15, 5, true, to_timestamp('20-03-2020 14:46:12', 'dd-mm-yyyy hh24:mi:ss')),
    ('We inform that reception will start 30 minutes before starting time. See you there.', 11, 20, true, to_timestamp('21-03-2020 15:46:13', 'dd-mm-yyyy hh24:mi:ss')),
    ('Bring your coats guys, it is gonna be sunny', 7, 13, true, to_timestamp('22-03-2020 16:51:15', 'dd-mm-yyyy hh24:mi:ss')),
    ('If you get lost, use your gps! Coordinates available in the event page', 2, 2, true, to_timestamp('23-03-2020 17:52:16', 'dd-mm-yyyy hh24:mi:ss')),
    ('We will be having a goodies giveway, stay tuned for more information ;)', 9, 12, true, to_timestamp('24-03-2020 18:53:17', 'dd-mm-yyyy hh24:mi:ss')),
    ('The event might start 10 minutes later than expected due to logistics reasons. Thank you for your understanding.', 13, 3, true, to_timestamp('25-03-2020 19:54:21', 'dd-mm-yyyy hh24:mi:ss')),
    ('Amazing!!!!', 1, 2, false, to_timestamp('26-03-2020 11:55:29', 'dd-mm-yyyy hh24:mi:ss')),
    ('Oh my god! I am so excited, last years edition was awesome.', 1, 3, false, to_timestamp('27-03-2020 12:56:29', 'dd-mm-yyyy hh24:mi:ss')),
    ('They did a great job last year. Im having high hopes this year.', 1, 5, false, to_timestamp('28-03-2020 13:57:31', 'dd-mm-yyyy hh24:mi:ss')),
    ('Last year I got my first summer internship thanks to this event!', 1, 7, false, to_timestamp('29-03-2020 14:58:32', 'dd-mm-yyyy hh24:mi:ss')),
    ('So glad NIAEFEUP keeps organizing events for FEUPs students.', 1, 12, false, to_timestamp('30-03-2020 15:59:33', 'dd-mm-yyyy hh24:mi:ss')),
    ('Another year, another Semana de Informática! Yey!', 1, 8, false, to_timestamp('01-04-2020 16:11:38', 'dd-mm-yyyy hh24:mi:ss')),
    ('Eheheh, awesome you guys are organizing this event again. Go NIAEFEUP!', 1, 9, false, to_timestamp('02-04-2020 17:12:30', 'dd-mm-yyyy hh24:mi:ss')),
    ('Nucleo de Informática you say? Interesting...', 1, 15, false, to_timestamp('03-04-2020 18:13:40', 'dd-mm-yyyy hh24:mi:ss')),
    ('Legen - wait for it - dary! LEGENDARY!', 1, 14, false, to_timestamp('04-04-2020 19:14:50', 'dd-mm-yyyy hh24:mi:ss')),
    ('Thank you! This is what I was needing!', 1, 13, false, to_timestamp('05-04-2020 11:15:02', 'dd-mm-yyyy hh24:mi:ss')),
    ('Yey goodies!!!', 1, 4, false, to_timestamp('06-04-2020 12:16:03', 'dd-mm-yyyy hh24:mi:ss')),
    ('I have been waiting for this basically all summer.', 2, 3, false, to_timestamp('07-04-2020 13:17:05', 'dd-mm-yyyy hh24:mi:ss')),
    ('Well, looks like we are in for a good time.', 3, 4, false, to_timestamp('08-04-2020 14:21:05', 'dd-mm-yyyy hh24:mi:ss')),
    ('Meh, kinda tired of these kind of events tbh.', 4, 5, false, to_timestamp('09-04-2020 15:22:05', 'dd-mm-yyyy hh24:mi:ss')),
    ('Wooohooo, partyyyy!', 5, 6, false, to_timestamp('10-04-2020 16:23:11', 'dd-mm-yyyy hh24:mi:ss')),
    ('Interesting initiative. Cheer from Chicago.', 6, 7, false, to_timestamp('11-04-2020 20:24:55', 'dd-mm-yyyy hh24:mi:ss')),
    ('Amazing. This brings me back to my young day when I went travelling with my friends.', 7, 8, false, to_timestamp('12-04-2020 04:25:55', 'dd-mm-yyyy hh24:mi:ss')),
    ('Yey! Big hug from Norway! Love your energy.', 8, 9, false, to_timestamp('13-04-2020 11:26:33', 'dd-mm-yyyy hh24:mi:ss')),
    ('Cheers from Brazil.', 9, 19, false, to_timestamp('14-04-2020 12:27:44', 'dd-mm-yyyy hh24:mi:ss')),
    ('Do we need to bring our umbrellas?', 10, 20, false, to_timestamp('15-04-2020 13:29:59', 'dd-mm-yyyy hh24:mi:ss')),
    ('Is any preparation advised for the event? Thank you in advance.', 11, 21, false, to_timestamp('16-04-2020 14:11:38', 'dd-mm-yyyy hh24:mi:ss')),
    ('I cant believe this... Really?', 12, 22, false, to_timestamp('17-04-2020 15:23:01', 'dd-mm-yyyy hh24:mi:ss')),
    ('Cant wait. Thank you, organizers team! Good job!', 11, 23, false, to_timestamp('18-04-2020 16:41:02', 'dd-mm-yyyy hh24:mi:ss')),
    ('Great idea! Regards from HOng Kong.', 12, 9, false, to_timestamp('19-04-2020 17:42:50', 'dd-mm-yyyy hh24:mi:ss')),
    ('Well, would you recommend that we bring food from home?.', 5, 19, false, to_timestamp('20-04-2020 18:43:10', 'dd-mm-yyyy hh24:mi:ss')),
    ('Is there any restaurants nearby?', 3, 20, false, to_timestamp('21-04-2020 21:44:13', 'dd-mm-yyyy hh24:mi:ss')),
    ('Is there parking space near the event lounge?', 7, 21, false, to_timestamp('22-04-2020 22:45:14', 'dd-mm-yyyy hh24:mi:ss'));

-- Comments
INSERT INTO comments(content, post_id, user_id, timestamp) VALUES
    ('What an amazing event!', 27, 1, to_timestamp('20-03-2020 11:23:51', 'dd-mm-yyyy hh24:mi:ss')),
    ('Really incredible!', 27, 2, to_timestamp('21-03-2020 12:24:52', 'dd-mm-yyyy hh24:mi:ss')),
    ('The fact that people still take the time to organize events like this warms my heart.', 27, 3, to_timestamp('22-03-2020 13:25:53', 'dd-mm-yyyy hh24:mi:ss')),
    ('This is indeed a nice way to spend the day, imo.', 28, 4, to_timestamp('23-03-2020 14:26:54', 'dd-mm-yyyy hh24:mi:ss')),
    ('This brings me back to my younger days.', 29, 14, to_timestamp('24-03-2020 15:27:55', 'dd-mm-yyyy hh24:mi:ss')),
    ('I do not enjoy this. Just saying...', 30, 15, to_timestamp('25-03-2020 16:31:56', 'dd-mm-yyyy hh24:mi:ss')),
    ('Wooooooowww', 31, 16, to_timestamp('26-03-2020 17:32:57', 'dd-mm-yyyy hh24:mi:ss')),
    ('Very, VERY nice! :) ', 32, 16, to_timestamp('27-03-2020 18:33:58', 'dd-mm-yyyy hh24:mi:ss')),
    ('A good day to you!', 33, 22, to_timestamp('28-03-2020 19:34:59', 'dd-mm-yyyy hh24:mi:ss')),
    ('Well, you do ceartainly have a point. However, some people may say you are wrong. Other people may say you are right.', 34, 21, to_timestamp('29-03-2020 20:51:01', 'dd-mm-yyyy hh24:mi:ss')),
    ('Eheheh, true that!', 35, 20, to_timestamp('30-03-2020 21:41:02', 'dd-mm-yyyy hh24:mi:ss')),
    ('Feels good man, someone who shares my way of thinking.', 36, 16, to_timestamp('02-04-2020 23:42:31', 'dd-mm-yyyy hh24:mi:ss')),
    ('Lets make the Internets great again!', 37, 9, to_timestamp('03-04-2020 22:43:32', 'dd-mm-yyyy hh24:mi:ss')),
    ('Yeap. I shared this on Reddit cuz it is so awesome', 38, 4, to_timestamp('04-04-2020 08:44:33', 'dd-mm-yyyy hh24:mi:ss')),
    ('So. Very. Good.', 39, 4, to_timestamp('05-04-2020 09:45:34', 'dd-mm-yyyy hh24:mi:ss')),
    ('Yes very good me agrrees sorry my englush not verry guud', 40, 23, to_timestamp('06-04-2020 10:46:35', 'dd-mm-yyyy hh24:mi:ss')),
    ('A great way to spend your Sunday afternoon', 41, 17, to_timestamp('07-04-2020 11:01:36', 'dd-mm-yyyy hh24:mi:ss')),
    ('Love it', 42, 18, to_timestamp('08-04-2020 12:02:37', 'dd-mm-yyyy hh24:mi:ss')),
    ('Amen to that!', 43, 4, to_timestamp('09-04-2020 13:55:41', 'dd-mm-yyyy hh24:mi:ss')),
    ('Lol yeap. You say what everyone is thinking.', 44, 5, to_timestamp('10-04-2020 14:56:42', 'dd-mm-yyyy hh24:mi:ss')),
    ('Boy oh boy!', 45, 6, to_timestamp('12-04-2020 15:57:43', 'dd-mm-yyyy hh24:mi:ss')),
    ('Yes. A hug to you from western colombia ;) ', 46, 7, to_timestamp('11-04-2020 16:58:44', 'dd-mm-yyyy hh24:mi:ss')),
    ('This takes me back to the time I visited Zimbabue with my older cousin!!! What a great time I had.', 47, 8, to_timestamp('13-04-2020 17:59:45', 'dd-mm-yyyy hh24:mi:ss'));

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
    (12, 24, -1),
    (12, 20, 1),
    (11, 21, -1),
    (13, 22, 1),
    (13, 41, -1),
    (7, 15, 1),
    (8, 15, -1),
    (9, 16, 1),
    (10, 20, -1),
    (11, 31, 1),
    (12, 22, -1),
    (13, 23, 1),
    (14, 24, -1),
    (15, 15, 1),
    (16, 16, -1), 
    (17, 17, -1),
    (18, 28, 1),
    (1, 29, -1),
    (2, 20, 1),
    (2, 21, -1),
    (3, 12, 1),
    (4, 13, -1),
    (5, 14, -1),
    (6, 25, 1),
    (7, 26, -1),
    (8, 27, 1),
    (9, 28, -1),
    (10, 29, 1),
    (11, 30, -1),
    (1, 20, 1),
    (2, 29, 1),
    (3, 40, 1),
    (4, 40, 1),
    (5, 41, 1),
    (6, 41, 1),
    (7, 42, 1),
    (8, 42, 1),
    (8, 43, 1),
    (9, 43, 1),
    (11, 44, 1),
    (12, 44, 1),
    (13, 45, 1),
    (14, 46, 1),
    (15, 47, 1),
    (16, 47, 1),
    (17, 47, 1),
    (18, 48, 1),
    (19, 49, 1),
    (20, 49, 1),
    (21, 33, 1),
    (22, 33, 1),
    (23, 34, 1),
    (22, 34, 1),
    (21, 35, 1),
    (20, 36, 1),
    (19, 36, 1),
    (18, 36, 1),
    (17, 37, 1),
    (16, 37, 1),
    (15, 37, 1);

-- Tickets
INSERT INTO tickets(user_id, event_id, type, paypal_order_id, timestamp)
    VALUES (1, 3, 'Paypal', 'PAYPAL-CONFIRMATION-6424', to_timestamp('02-03-2020 23:42:31', 'dd-mm-yyyy hh24:mi:ss'));
INSERT INTO tickets(user_id, event_id, type, paypal_order_id, nif, billing_name, timestamp) VALUES 
    (5, 2, 'Paypal', 'PAYPAL-CONFIRMATION-4002', 245024952, 'Peter', to_timestamp('12-03-2020 13:13:06', 'dd-mm-yyyy hh24:mi:ss')),
    (5, 11, 'Paypal', 'PAYPAL-CONFIRMATION-7601', 239682352, 'Mark Anthony', to_timestamp('13-03-2020 14:13:43', 'dd-mm-yyyy hh24:mi:ss')),
    (6, 12, 'Paypal', 'PAYPAL-CONFIRMATION-8277', 249486937, 'Sarah', to_timestamp('13-03-2020 15:13:12', 'dd-mm-yyyy hh24:mi:ss')),
    (7, 12, 'Paypal', 'PAYPAL-CONFIRMATION-9648', 228574453, 'António Lopes', to_timestamp('02-03-2020 16:21:52', 'dd-mm-yyyy hh24:mi:ss')),
    (13, 3, 'Paypal', 'PAYPAL-CONFIRMATION-2326', 282397537, 'Cassandra Mel', to_timestamp('03-03-2020 21:37:27', 'dd-mm-yyyy hh24:mi:ss')),
    (15, 4, 'Paypal', 'PAYPAL-CONFIRMATION-2266', 229385479, 'Varys Humgburt', to_timestamp('07-03-2020 22:22:36', 'dd-mm-yyyy hh24:mi:ss')),
    (14, 12, 'Paypal', 'PAYPAL-CONFIRMATION-4713', 205791573, 'John Malcovich', to_timestamp('05-03-2020 23:41:31', 'dd-mm-yyyy hh24:mi:ss'));
INSERT INTO tickets(user_id, event_id, type, event_voucher_id, timestamp) VALUES 
    (1, 2, 'Voucher', 1, to_timestamp('02-03-2020 11:31:11', 'dd-mm-yyyy hh24:mi:ss')),
    (1, 2, 'Voucher', 2, to_timestamp('03-03-2020 12:32:12', 'dd-mm-yyyy hh24:mi:ss')),
    (3, 4, 'Voucher', 3, to_timestamp('04-03-2020 13:34:13', 'dd-mm-yyyy hh24:mi:ss')),
    (4, 4, 'Voucher', 4, to_timestamp('05-03-2020 14:41:14', 'dd-mm-yyyy hh24:mi:ss')),
    (5, 7, 'Voucher', 5, to_timestamp('06-03-2020 15:42:14', 'dd-mm-yyyy hh24:mi:ss')),
    (13, 7, 'Voucher', 6, to_timestamp('07-03-2020 16:43:15', 'dd-mm-yyyy hh24:mi:ss')),
    (7, 7, 'Voucher', 7, to_timestamp('08-03-2020 17:44:16', 'dd-mm-yyyy hh24:mi:ss')),
    (8, 7, 'Voucher', 8, to_timestamp('09-03-2020 18:45:17', 'dd-mm-yyyy hh24:mi:ss')),
    (9, 7, 'Voucher', 9, to_timestamp('31-03-2020 19:51:18', 'dd-mm-yyyy hh24:mi:ss')),
    (10, 13, 'Voucher', 10, to_timestamp('11-03-2020 20:59:19', 'dd-mm-yyyy hh24:mi:ss')),
    (11, 13, 'Voucher', 11, to_timestamp('12-03-2020 06:58:20', 'dd-mm-yyyy hh24:mi:ss')),
    (12, 12, 'Voucher', 12, to_timestamp('23-03-2020 07:57:21', 'dd-mm-yyyy hh24:mi:ss')),
    (13, 11, 'Voucher', 13, to_timestamp('14-03-2020 08:56:22', 'dd-mm-yyyy hh24:mi:ss')),
    (15, 10, 'Voucher', 14, to_timestamp('15-04-2020 11:55:23', 'dd-mm-yyyy hh24:mi:ss')),
    (15, 5, 'Voucher', 15, to_timestamp('02-04-2020 12:54:23', 'dd-mm-yyyy hh24:mi:ss')),
    (16, 6, 'Voucher', 16, to_timestamp('17-04-2020 13:01:24', 'dd-mm-yyyy hh24:mi:ss')),
    (17, 7, 'Voucher', 17, to_timestamp('18-04-2020 14:02:25', 'dd-mm-yyyy hh24:mi:ss')),
    (18, 14, 'Voucher', 18, to_timestamp('19-04-2020 15:31:26', 'dd-mm-yyyy hh24:mi:ss')),
    (19, 5, 'Voucher', 19, to_timestamp('03-04-2020 16:32:26', 'dd-mm-yyyy hh24:mi:ss')),
    (10, 4, 'Voucher', 20, to_timestamp('21-04-2020 17:33:27', 'dd-mm-yyyy hh24:mi:ss')),
    (21, 10, 'Voucher', 21, to_timestamp('22-04-2020 18:34:28', 'dd-mm-yyyy hh24:mi:ss')),
    (22, 15, 'Voucher', 22, to_timestamp('23-04-2020 19:35:31', 'dd-mm-yyyy hh24:mi:ss')),
    (23, 15, 'Voucher', 23, to_timestamp('24-04-2020 15:36:32', 'dd-mm-yyyy hh24:mi:ss')),
    (7, 3, 'Voucher', 24, to_timestamp('23-04-2020 13:36:33', 'dd-mm-yyyy hh24:mi:ss'));

-- Organizers
INSERT INTO organizers(user_id, event_id) VALUES 
    (2, 1),
    (2, 2),
    (3, 3),
    (1, 4),
    (1, 5),
    (7, 6),
    (6, 7),
    (17, 8),
    (12, 9),
    (14, 10),
    (20, 11),
    (21, 12),
    (13, 13),
    (4, 14),
    (6, 15),
    (1, 1),
    (4, 4),
    (14, 5),
    (15, 6),
    (16, 7),
    (18, 9),
    (19, 10),
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
    (23, 14),
    (1, 6),
    (2, 7),
    (3, 8),
    (4, 9),
    (5, 10),
    (6, 11),
    (7, 12),
    (8, 13),
    (9, 14),
    (10, 15),
    (11, 1),
    (12, 2),
    (13, 3),
    (14, 4),
    (15, 5),
    (16, 6),
    (17, 7),
    (18, 8),
    (19, 9),
    (20, 10),
    (21, 11),
    (22, 2),
    (23, 13),
    (14, 14),
    (15, 15),
    (16, 1),
    (17, 2),
    (18, 3),
    (19, 4),
    (20, 11),
    (21, 6),
    (22, 7),
    (23, 8);

-- Event Tags
INSERT INTO event_tags(event_id, tag_id) VALUES 
    (1, 1),
    (1, 3),
    (1, 4),
    (1, 5),
    (1, 6),
    (1, 7),
    (2, 1),
    (2, 8),
    (2, 9),
    (2, 10),
    (2, 11),
    (3, 1),
    (3, 2),
    (3, 12),
    (4, 1),
    (4, 2),
    (5, 13),
    (5, 14),
    (5, 15),
    (6, 1),
    (6, 16),
    (6, 17),
    (6, 18),
    (7, 19),
    (7, 20),
    (7, 21),
    (8, 22),
    (9, 23),
    (9, 24),
    (10, 25),
    (10, 26),
    (11, 27),
    (11, 28),
    (11, 29),
    (12, 30),
    (12, 31),
    (13, 1),
    (13, 32),
    (13, 33),
    (13, 34),
    (14, 35),
    (14, 36),
    (14, 37),
    (14, 38),
    (15, 39),
    (15, 40);

-- Issues
INSERT INTO issues(title, content, creator_id, referenced_user, timestamp)
    VALUES ('User harrasment', 'This user was harassing me!', 1, 4, to_timestamp('12-03-2020 11:11:41', 'dd-mm-yyyy hh24:mi:ss'));
INSERT INTO issues(title, content, creator_id, referenced_event, timestamp)
    VALUES ('Fake Event', 'This event has fake news... ', 1, 3, to_timestamp('13-03-2020 12:11:42', 'dd-mm-yyyy hh24:mi:ss'));
INSERT INTO issues(title, content, creator_id, referenced_post, timestamp)
    VALUES ('Offensive Post', 'This post is offensive to me.', 2, 3, to_timestamp('14-03-2020 13:33:43', 'dd-mm-yyyy hh24:mi:ss'));
INSERT INTO issues(title, content, creator_id, referenced_comment, timestamp)
    VALUES ('Unpolite comment', 'This comment is unpolite!!', 2, 5, to_timestamp('15-03-2020 14:34:44', 'dd-mm-yyyy hh24:mi:ss'));
INSERT INTO issues(title, content, creator_id, timestamp) VALUES 
    ('FAQ', 'I cant find the FAQ', 5, to_timestamp('16-03-2020 16:12:45', 'dd-mm-yyyy hh24:mi:ss')),
    ('Contacts missing', 'I am not able to find your contacts', 6, to_timestamp('17-03-2020 17:13:46', 'dd-mm-yyyy hh24:mi:ss')),
    ('Issue Submission', 'I am unable to find the form to submit an issue!', 7, to_timestamp('19-03-2020 18:14:47', 'dd-mm-yyyy hh24:mi:ss')),
    ('Eventbrite Connection', 'Do you guys support connection to Eventbrite?', 8, to_timestamp('20-03-2020 19:15:48', 'dd-mm-yyyy hh24:mi:ss')),
    ('Eventbrite Application', 'Is this eventbrite?', 9, to_timestamp('21-03-2020 08:16:11', 'dd-mm-yyyy hh24:mi:ss')),
    ('MeetUp Application', 'Is this MeetUp?', 10, to_timestamp('22-03-2020 09:21:12', 'dd-mm-yyyy hh24:mi:ss')),
    ('Great Job', 'Just passing by to say you are doing a great job!', 11, to_timestamp('23-03-2020 20:31:13', 'dd-mm-yyyy hh24:mi:ss')),
    ('Administration Team', 'How can I become an admin?', 12, to_timestamp('24-03-2020 21:32:14', 'dd-mm-yyyy hh24:mi:ss')),
    ('Thank You', 'Thank you for all your support!', 13, to_timestamp('25-03-2020 22:33:15', 'dd-mm-yyyy hh24:mi:ss')),
    ('Google Callendar', 'Is this Google Callendar?', 14, to_timestamp('26-03-2020 23:51:16', 'dd-mm-yyyy hh24:mi:ss')),
    ('Веб приложение', 'Это приложение Eventbrite webb?', 15, to_timestamp('27-03-2020 11:52:17', 'dd-mm-yyyy hh24:mi:ss')),
    ('Aдминистрация', 'Как я могу стать администратором?', 16, to_timestamp('28-03-2020 12:53:18', 'dd-mm-yyyy hh24:mi:ss')),
    ('Häufig gestellte Fragen', 'Wo finde ich den Website-Abschnitt mit häufig gestellten Fragen?', 17, to_timestamp('01-04-2020 13:54:19', 'dd-mm-yyyy hh24:mi:ss')),
    ('Jaju mudneun jilmun', 'Jaju mudneun jilmun web saiteu segsyeon-eun eodieseo chaj-eul su issseubnikka?', 18, to_timestamp('02-04-2020 14:55:21', 'dd-mm-yyyy hh24:mi:ss')),
    ('Events Attendance', 'I cancelled a ticket for an event. Meanwhile, it got full but I would like my ticket back! Can I get it back?', 19, to_timestamp('11-04-2020 13:36:33', 'dd-mm-yyyy hh24:mi:ss')),
    ('Event Creator to Administrator', 'I have hosted a lot of Events in your application. May I become an admin?', 20, to_timestamp('12-04-2020 16:56:22', 'dd-mm-yyyy hh24:mi:ss'));

-- Notifications
INSERT INTO notifications(type, content, user_id, issue_id, timestamp)
    VALUES ('IssueNotification', 'We, the admins, solved your issue', 1, 2, to_timestamp('01-03-2020 11:31:51', 'dd-mm-yyyy hh24:mi:ss'));
INSERT INTO notifications(type, user_id, event_id, is_dismissed, timestamp)
    VALUES ('EventOrganizer', 2, 2, true, to_timestamp('02-03-2020 12:33:52', 'dd-mm-yyyy hh24:mi:ss'));
INSERT INTO notifications(type, user_id, event_id, timestamp) VALUES 
    ('EventInvitation', 4, 1, to_timestamp('03-03-2020 13:32:53', 'dd-mm-yyyy hh24:mi:ss')), 
    ('EventInvitation', 5, 2, to_timestamp('04-03-2020 14:34:54', 'dd-mm-yyyy hh24:mi:ss')), 
    ('EventInvitation', 6, 3, to_timestamp('05-03-2020 15:35:55', 'dd-mm-yyyy hh24:mi:ss')), 
    ('EventDisabling', 7, 4, to_timestamp('06-03-2020 16:36:56', 'dd-mm-yyyy hh24:mi:ss')), 
    ('EventDisabling', 8, 5, to_timestamp('07-03-2020 17:37:57', 'dd-mm-yyyy hh24:mi:ss')), 
    ('EventDisabling', 9, 6, to_timestamp('08-03-2020 18:33:58', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventActivation', 10, 7, to_timestamp('09-03-2020 19:34:54', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventActivation', 11, 8, to_timestamp('12-03-2020 20:35:56', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventActivation', 12, 9, to_timestamp('13-03-2020 21:36:59', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventCancellation', 13, 10, to_timestamp('14-03-2020 08:37:50', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventCancellation', 14, 11, to_timestamp('15-03-2020 09:38:21', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventCancellation', 15, 12, to_timestamp('16-03-2020 10:39:22', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventRemoval', 16, 13, to_timestamp('17-03-2020 11:41:23', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventRemoval', 17, 14, to_timestamp('18-03-2020 12:42:24', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventRemoval', 18, 15, to_timestamp('19-03-2020 13:43:25', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventOrganizer', 19, 1, to_timestamp('01-04-2020 14:40:26', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventOrganizer', 20, 2, to_timestamp('04-04-2020 15:44:27', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventOrganizer', 21, 3, to_timestamp('09-04-2020 16:45:28', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventUpdate', 22, 4, to_timestamp('11-04-2020 17:46:29', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventUpdate', 23, 5, to_timestamp('12-04-2020 18:47:30', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventUpdate', 1, 6, to_timestamp('13-04-2020 19:48:31', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventAnnouncement', 2, 7, to_timestamp('14-04-2020 20:49:32', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventAnnouncement', 3, 8, to_timestamp('15-04-2020 21:50:33', 'dd-mm-yyyy hh24:mi:ss')),
    ('EventAnnouncement', 4, 9, to_timestamp('16-04-2020 22:51:34', 'dd-mm-yyyy hh24:mi:ss'));