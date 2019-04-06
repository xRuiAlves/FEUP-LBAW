
--Quando um tuplo de rating é criado, dar update ao atributo “rating” da classe post

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

--FTS
DROP TRIGGER IF EXISTS insert_event_search ON events;
DROP TRIGGER IF EXISTS update_event_search ON events;
DROP TRIGGER IF EXISTS update_event_search_on_tags_update ON tags;
DROP TRIGGER IF EXISTS update_event_search_on_tags_insert ON event_tags;
DROP TRIGGER IF EXISTS update_event_search_on_tags_delete ON tags;
DROP TRIGGER IF EXISTS update_event_search_on_tags_delete ON event_tags;
DROP TRIGGER IF EXISTS update_event_search_on_category_update ON event_categories;
DROP TRIGGER IF EXISTS update_event_search_on_category_insert ON event_categories;
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
