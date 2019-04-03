CREATE INDEX voucher_code_index ON table USING hash (code, event_id);



--CREATE INDEX events_search_index ON events USING GIN (search);
--CREATE INDEX users_search_index ON users USING GIN (search);

CREATE UNIQUE INDEX event_invite_notification_spam ON notifications (user_id, event_id, type) WHERE type = 'EventInvitation';


--template
--CREATE INDEX name ON table USING hash (column);