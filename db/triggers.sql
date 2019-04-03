
--Quando um tuplo de rating é criado, dar update ao atributo “rating” da classe post

DROP TRIGGER IF EXISTS insert_rating_trigger ON ratings;
DROP TRIGGER IF EXISTS update_rating_trigger ON ratings;
DROP TRIGGER IF EXISTS delete_rating_trigger ON ratings;
DROP TRIGGER IF EXISTS insert_comment_trigger ON comments;
DROP TRIGGER IF EXISTS delete_comment_trigger ON comments;
DROP TRIGGER IF EXISTS disable_event_trigger ON events;

CREATE TRIGGER insert_rating_trigger 
    AFTER INSERT ON ratings
    FOR EACH ROW 
    EXECUTE PROCEDURE insert_rating_function();

CREATE TRIGGER update_rating_trigger 
    AFTER UPDATE OF value ON ratings
    FOR EACH ROW 
    EXECUTE PROCEDURE update_rating_function();

CREATE TRIGGER delete_rating_trigger 
    BEFORE DELETE ON ratings
    FOR EACH ROW 
    EXECUTE PROCEDURE delete_rating_function();

CREATE TRIGGER insert_comment_trigger 
    AFTER INSERT ON comments
    FOR EACH ROW 
    EXECUTE PROCEDURE insert_comment_function();

CREATE TRIGGER delete_comment_trigger 
    BEFORE DELETE ON comments
    FOR EACH ROW 
    EXECUTE PROCEDURE delete_comment_function();

CREATE TRIGGER disable_event_trigger 
    AFTER UPDATE OF status ON events
    FOR EACH ROW 
    WHEN (NEW.status = 'Disabled')
    EXECUTE PROCEDURE disable_event_function();

CREATE TRIGGER cancel_event_trigger 
    AFTER UPDATE OF status ON events
    FOR EACH ROW 
    WHEN (NEW.status = 'Cancelled')
    EXECUTE PROCEDURE cancel_event_function();

CREATE TRIGGER remove_attendee_trigger
    AFTER DELETE ON tickets
    FOR EACH ROW
    EXECUTE PROCEDURE remove_attendee_function();