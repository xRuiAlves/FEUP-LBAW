DROP INDEX IF EXISTS voucher_code_index;
DROP INDEX IF EXISTS announcements_index;
DROP INDEX IF EXISTS discussion_posts_index;
DROP INDEX IF EXISTS comments_index;
DROP INDEX IF EXISTS tickets_index;
DROP INDEX IF EXISTS issues_timestamp_index;
DROP INDEX IF EXISTS notifications_index;
DROP INDEX IF EXISTS event_invite_notification_spam;

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

--CREATE INDEX events_search_index ON events USING GIN (search);
--CREATE INDEX users_search_index ON users USING GIN (search);
--CREATE INDEX issues_search_index ON issues USING GIN (search);

CREATE UNIQUE INDEX event_invite_notification_spam ON notifications (user_id, event_id, type) WHERE type = 'EventInvitation';


--template
--CREATE INDEX name ON table USING hash (column);