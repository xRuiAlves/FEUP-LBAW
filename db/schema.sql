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