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
SELECT e.id, to_tsvector(coalesce(e.title,'')) as title, to_tsvector(coalesce(e.description, '')) as description, to_tsvector(coalesce(e.location, '')) as location, to_tsvector(coalesce(event_categories.name ,'')) AS category, to_tsvector(coalesce(string_agg(tags.name, ' '),'')) AS tags

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
            (SELECT setweight(title, 'A') || setweight(description, 'C') || setweight(location, 'B') || setweight(category, 'B') || setweight(tags, 'B')
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
        SELECT id as event_id, setweight(title, 'A') || setweight(description, 'C') || setweight(location, 'B') || setweight(category, 'B') || setweight(tags, 'B')
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
        SELECT id as event_id, setweight(title, 'A') || setweight(description, 'C') || setweight(location, 'B') || setweight(category, 'B') || setweight(tags, 'B')
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
        SELECT id as event_id, setweight(title, 'A') || setweight(description, 'C') || setweight(location, 'B') || setweight(category, 'B') || setweight(tags, 'B')
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
        SELECT event_search_fields.id as event_id, setweight(event_search_fields.title, 'A') || setweight(event_search_fields.description, 'C') || setweight(event_search_fields.location, 'B') || setweight(category, 'B') || setweight(tags, 'B')
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
SELECT i.id, i.creator_id AS creator_id, to_tsvector(coalesce(i.title,'')) as title, to_tsvector(coalesce(i.content, '')) as content, to_tsvector(coalesce(users.name ,'')) AS creator

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
            (SELECT setweight(title, 'A') || setweight(content, 'C') || setweight(creator, 'B')
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
        SELECT issue_search_fields.id as issue_id, setweight(title, 'A') || setweight(content, 'C') || setweight(creator, 'B')
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