
DROP INDEX IF EXISTS posts_index;
DROP INDEX IF EXISTS voucher_code_index;
DROP INDEX IF EXISTS posts_timestamp_index;
DROP INDEX IF EXISTS posts_rating_index;
DROP INDEX IF EXISTS comments_index;
DROP INDEX IF EXISTS comments_timestamp_index;
DROP INDEX IF EXISTS tickets_index;
DROP INDEX IF EXISTS issues_timestamp_index;
DROP INDEX IF EXISTS issues_solved_index;
DROP INDEX IF EXISTS notifications_index;
DROP INDEX IF EXISTS notifications_timestamp_index;
DROP INDEX IF EXISTS event_invite_notification_spam;


CREATE INDEX voucher_code_index ON event_vouchers USING btree (code, event_id);

CREATE INDEX posts_index ON posts USING btree (event_id, is_announcement);
CREATE INDEX posts_timestamp_index ON posts USING btree (timestamp);
CREATE INDEX posts_rating_index ON posts USING btree (rating) WHERE is_announcement = false;

CREATE INDEX comments_index ON comments USING hash (post_id);
CREATE INDEX comments_timestamp_index ON comments USING btree (timestamp);

CREATE INDEX tickets_index ON tickets USING btree (user_id, event_id);

CREATE INDEX issues_timestamp_index ON issues USING btree(timestamp);
CREATE INDEX issues_solved_index ON issues USING btree(is_solved);

CREATE INDEX notifications_index ON notifications USING btree(user_id, is_dismissed);
CREATE INDEX notifications_timestamp_index ON notifications USING btree(timestamp);

--CREATE INDEX events_search_index ON events USING GIN (search);
--CREATE INDEX users_search_index ON users USING GIN (search);
--CREATE INDEX issues_search_index ON issues USING GIN (search);

CREATE UNIQUE INDEX event_invite_notification_spam ON notifications (user_id, event_id, type) WHERE type = 'EventInvitation';


--template
--CREATE INDEX name ON table USING hash (column);