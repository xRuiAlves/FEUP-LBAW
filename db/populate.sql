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
INSERT INTO users(name, email, password, is_admin)
    VALUES ('User1', 'user1@email.net', 'XXXXX', true);
INSERT INTO users(name, email, password)
    VALUES ('User2', 'User2@email.net', 'XXXXX');
INSERT INTO users(name, email, password)
    VALUES ('User3', 'User3@email.net', 'XXXXX');
INSERT INTO users(name, email, password)
    VALUES ('User4', 'User4@email.net', 'XXXXX');
INSERT INTO users(name, email, password, is_admin)
    VALUES ('User5', 'User5@email.net', 'XXXXX', true);

-- Event Categories
INSERT INTO event_categories(id, name) VALUES (1, 'Sports');
INSERT INTO event_categories(id, name) VALUES (2, 'Science');
INSERT INTO event_categories(id, name) VALUES (3, 'Art');
INSERT INTO event_categories(id, name) VALUES (4, 'Learning');
INSERT INTO event_categories(id, name) VALUES (5, 'Music');

-- Events
INSERT INTO events(title, description, price, latitude, longitude, start_timestamp, end_timestamp, event_category_id, status, user_id)
    VALUES ('SINF 2020', 'Very good event', 1, 30.1, -14.2, to_timestamp('16-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), to_timestamp('17-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 4, 'Active', 2);
INSERT INTO events(title, description, price, start_timestamp, event_category_id, status, user_id)
    VALUES ('Event2', 'Very good event', 0, to_timestamp('20-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 4, 'Active', 2);
INSERT INTO events(title, description, price, start_timestamp, event_category_id, status, user_id)
    VALUES ('Event3', 'Event.', 1.50, to_timestamp('21-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 1, 'Active', 1);
INSERT INTO events(title, description, price, start_timestamp, event_category_id, status, user_id)
    VALUES ('Event4', '...', 1, to_timestamp('22-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 1);
INSERT INTO events(title, description, price, start_timestamp, event_category_id, status, user_id)
    VALUES ('Event5', 'A B C', 1, to_timestamp('23-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 3, 'Active', 4);

-- Tags
INSERT INTO tags(name) VALUES ('Engineering');
INSERT INTO tags(name) VALUES ('Fun');
INSERT INTO tags(name) VALUES ('GreatMovie');
INSERT INTO tags(name) VALUES ('NIAEFEUP');
INSERT INTO tags(name) VALUES ('Respect');

-- Event Vouchers
INSERT INTO event_vouchers(code, event_id, user_id)
    VALUES ('XPTO1001', 2, 1);
INSERT INTO event_vouchers(code, event_id, user_id)
    VALUES ('XPTO1002', 2, 1);
INSERT INTO event_vouchers(code, event_id, user_id)
    VALUES ('XPTO1500', 4, 1);
INSERT INTO event_vouchers(code, event_id, user_id)
    VALUES ('XPTO1192', 4, 1);
INSERT INTO event_vouchers(code, event_id, user_id)
    VALUES ('XPTO1562', 4, 5);
    
-- Posts
INSERT INTO posts(content, event_id, user_id, is_announcement)
    VALUES ('Announcement Number 1', 1, 1, true);
INSERT INTO posts(content, event_id, user_id, is_announcement)
    VALUES ('Announcement Number 2', 3, 1, true);
INSERT INTO posts(content, event_id, user_id, is_announcement)
    VALUES ('Post Number 1', 5, 2, false);
INSERT INTO posts(content, event_id, user_id, is_announcement)
    VALUES ('Post Number 2', 5, 3, false);
INSERT INTO posts(content, event_id, user_id, is_announcement)
    VALUES ('Post Number 3', 4, 4, false);

-- Comments
INSERT INTO comments(content, post_id, user_id)
    VALUES ('Comment number 1!', 3, 1);
INSERT INTO comments(content, post_id, user_id)
    VALUES ('Comment number 2!', 3, 2);
INSERT INTO comments(content, post_id, user_id)
    VALUES ('Comment number 3!', 4, 3);
INSERT INTO comments(content, post_id, user_id)
    VALUES ('Comment number 4!', 5, 4);
INSERT INTO comments(content, post_id, user_id)
    VALUES ('Comment number 5!', 4, 5);

-- Ratings
INSERT INTO ratings(user_id, post_id, value)
    VALUES (1, 1, 1);
INSERT INTO ratings(user_id, post_id, value)
    VALUES (2, 1, -1);
INSERT INTO ratings(user_id, post_id, value)
    VALUES (2, 2, 1);
INSERT INTO ratings(user_id, post_id, value)
    VALUES (4, 4, -1);
INSERT INTO ratings(user_id, post_id, value)
    VALUES (4, 5, -1);

-- Tickets
INSERT INTO tickets(user_id, event_id, type, event_voucher_id)
    VALUES (1, 3, 'Voucher', 1);
INSERT INTO tickets(user_id, event_id, type, event_voucher_id)
    VALUES (1, 3, 'Voucher', 2);
INSERT INTO tickets(user_id, event_id, type, paypal_order_id)
    VALUES (1, 3, 'Paypal', 'PAYPAL-CONFIRMATION-6424');
INSERT INTO tickets(user_id, event_id, type, event_voucher_id)
    VALUES (4, 5, 'Voucher', 3);
INSERT INTO tickets(user_id, event_id, type, paypal_order_id, nif, billing_name)
    VALUES (5, 2, 'Paypal', 'PAYPAL-CONFIRMATION-7512', 245024952, 'Peter');

-- Organizers
INSERT INTO organizers(user_id, event_id) VALUES (1, 2);
INSERT INTO organizers(user_id, event_id) VALUES (2, 3);
INSERT INTO organizers(user_id, event_id) VALUES (3, 4);
INSERT INTO organizers(user_id, event_id) VALUES (4, 5);
INSERT INTO organizers(user_id, event_id) VALUES (5, 1);

-- Favorites
INSERT INTO favorites(user_id, event_id) VALUES (5, 5);
INSERT INTO favorites(user_id, event_id) VALUES (4, 5);
INSERT INTO favorites(user_id, event_id) VALUES (3, 5);
INSERT INTO favorites(user_id, event_id) VALUES (2, 2);
INSERT INTO favorites(user_id, event_id) VALUES (1, 2);

-- Event Tags
INSERT INTO event_tags(event_id, tag_id) VALUES (5, 3);
INSERT INTO event_tags(event_id, tag_id) VALUES (5, 4);
INSERT INTO event_tags(event_id, tag_id) VALUES (4, 2);
INSERT INTO event_tags(event_id, tag_id) VALUES (2, 2);
INSERT INTO event_tags(event_id, tag_id) VALUES (1, 2);

-- Issues
INSERT INTO issues(title, content, creator_id, referenced_user)
    VALUES ('Issue #1', 'I have an issue', 1, 4);
INSERT INTO issues(title, content, creator_id, referenced_event)
    VALUES ('Issue #2', 'I have an issue', 1, 3);
INSERT INTO issues(title, content, creator_id, referenced_post)
    VALUES ('Issue #3', 'I have an issue', 2, 3);
INSERT INTO issues(title, content, creator_id, referenced_comment)
    VALUES ('Issue #4', 'I have an issue', 2, 5);
INSERT INTO issues(title, content, creator_id)
    VALUES ('Issue #5', 'I have an issue', 5);

-- Notifications
INSERT INTO notifications(type, content, user_id, issue_id)
    VALUES ('IssueNotification', 'We, the admins, solved your issue', 1, 2);
INSERT INTO notifications(type, user_id, event_id)
    VALUES ('EventInvitation', 4, 1);
INSERT INTO notifications(type, user_id, event_id)
    VALUES ('EventRemoval', 4, 2);
INSERT INTO notifications(type, user_id, event_id, is_dismissed)
    VALUES ('EventModerator', 2, 2, true);
INSERT INTO notifications(type, user_id, event_id)
    VALUES ('EventCancellation', 5, 2);