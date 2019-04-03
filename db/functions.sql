CREATE OR REPLACE FUNCTION
    insert_rating_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET rating = rating + NEW.value
        WHERE posts.id = NEW.post_id;
        
        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION
    update_rating_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET rating = rating + NEW.value - OLD.value
        WHERE posts.id = NEW.post_id;
        
        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION
    delete_rating_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET rating = rating - OLD.value
        WHERE posts.id = OLD.post_id;
        
        RETURN OLD;
    END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION
    insert_comment_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET num_comments = num_comments + 1
        WHERE posts.id = NEW.post_id;
        
        RETURN NEW;
    END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION
    delete_comment_function() RETURNS TRIGGER AS $$
    BEGIN
        UPDATE posts
        SET num_comments = num_comments - 1
        WHERE posts.id = OLD.post_id;
        
        RETURN OLD;
    END;
$$ LANGUAGE 'plpgsql';

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

CREATE OR REPLACE FUNCTION
    remove_attendee_function() RETURNS TRIGGER AS $$
    BEGIN
        INSERT INTO notifications(type, user_id, event_id)
        VALUES ('EventRemoval', OLD.user_id, OLD.event_id);
        
        RETURN OLD;
    END;
$$ LANGUAGE 'plpgsql';

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