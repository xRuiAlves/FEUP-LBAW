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