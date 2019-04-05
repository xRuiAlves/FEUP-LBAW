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
INSERT INTO users(name, email, password, is_admin) VALUES 
    ('Mark', 'user1@email.net', 'XXXXX', true),
    ('Lucy', 'user2@email.net', 'XXXXX', true),
    ('Susan', 'user3@email.net', 'XXXXX', true),
    ('Peter', 'user4@email.net', 'XXXXX', true),
    ('John', 'user5@email.net', 'XXXXX', true);
INSERT INTO users(name, email, password) VALUES 
    ('Jonathan', 'user6@email.net', 'XXXXX'),
    ('Kevin', 'user23@email.net', 'XXXXX'),
    ('Richard', 'user7@email.net', 'XXXXX'),
    ('Martha', 'user8@email.net', 'XXXXX'),
    ('Louis', 'user9@email.net', 'XXXXX'),
    ('Mia', 'user10@email.net', 'XXXXX'),
    ('Anna', 'user11@email.net', 'XXXXX'),
    ('Bruno', 'user12@email.net', 'XXXXX'),
    ('Carlos', 'user13@email.net', 'XXXXX'),
    ('Paul', 'user14@email.net', 'XXXXX'),
    ('Charles', 'user15@email.net', 'XXXXX'),
    ('Paula', 'user16@email.net', 'XXXXX'),
    ('Francis', 'user17@email.net', 'XXXXX'),
    ('Gil', 'user18@email.net', 'XXXXX'),
    ('Jennifer', 'user19@email.net', 'XXXXX'),
    ('Dominic', 'user20@email.net', 'XXXXX'),
    ('Nathan', 'user21@email.net', 'XXXXX'),
    ('Ethan', 'user22@email.net', 'XXXXX');

-- Event Categories
INSERT INTO event_categories(id, name) VALUES 
    (1, 'Sports'),
    (2, 'Science'),
    (3, 'Art'),
    (4, 'Learning'),
    (5, 'Technology'),
    (6, 'Health Care'),
    (7, 'Music');

-- Events
INSERT INTO events(title, description, price, latitude, longitude, start_timestamp, end_timestamp, event_category_id, status, user_id) VALUES 
    ('SINF 2020', 'Very good event', 1, 30.1, -14.2, to_timestamp('16-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), to_timestamp('17-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 4, 'Active', 2);
INSERT INTO events(title, description, price, start_timestamp, event_category_id, status, user_id) VALUES 
    ('SINF 2021', 'Also a very good event', 0, to_timestamp('20-05-2021 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 4, 'Active', 2),
    ('SINF 2035', 'Event.', 1.50, to_timestamp('21-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 1, 'Active', 1),
    ('ENEI', '...', 1, to_timestamp('22-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 3),
    ('Meeting', '...', 1, to_timestamp('30-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Disabled', 3),
    ('Great Meeting', '...', 1, to_timestamp('31-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 1),
    ('Tour', '......', 1, to_timestamp('22-07-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 7),
    ('Party', '......', 1, to_timestamp('22-08-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 6),
    ('A great party', '...', 1, to_timestamp('22-05-2020 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Cancelled', 17),
    ('Birthday', '...', 1, to_timestamp('31-05-2020 17:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 12),
    ('Flight party', '......', 1, to_timestamp('22-07-2022 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 14),
    ('Food meeting', '...', 1, to_timestamp('26-06-2020 20:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Disabled', 20),
    ('Food competition', '...', 1, to_timestamp('29-05-2021 15:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 21),
    ('Wine tasting', '...', 1, to_timestamp('30-12-2020 22:36:38', 'dd-mm-yyyy hh24:mi:ss'), 2, 'Active', 13),
    ('Boat trip', 'A B C', 1, to_timestamp('23-05-2020 12:02:38', 'dd-mm-yyyy hh24:mi:ss'), 3, 'Cancelled', 4);

-- Tags
INSERT INTO tags(name) VALUES 
    ('Engineering'),
    ('Fun'),
    ('GreatMovie'),
    ('NIAEFEUP'),
    ('Football'),
    ('Basketball'),
    ('Competition'),
    ('Teamwork'),
    ('Bonding'),
    ('Helping'),
    ('Nature'),
    ('Yoga'),
    ('Food'),
    ('Boats'),
    ('Handball'),
    ('Programming'),
    ('C++'),
    ('Databases'),
    ('Web Engineer'),
    ('Cars'),
    ('Bycicles'),
    ('Running'),
    ('Party'),
    ('Jogging'),
    ('Respect');

    -- Event Vouchers
INSERT INTO event_vouchers(code, event_id, user_id) VALUES 
    ('XPTO1001', 2, 1),
    ('XPTO1002', 2, 1),
    ('XPTO1500', 4, 3),
    ('XPTO1192', 4, 7),
    ('XPTO1193', 7, 13),
    ('XPTO1194', 7, 22),
    ('XPTO1195', 7, 21),
    ('XPTO1196', 7, 21),
    ('XPTO1197', 7, 16),
    ('XPTO1198', 13, 12),
    ('XPTO1199', 13, 19),
    ('XPTO1312', 12, 12),
    ('XPTO1314', 11, 19),
    ('XPTO1316', 10, 6),
    ('XPTO1318', 5, 6),
    ('XPTO2193', 6, 7),
    ('XPTO2195', 7, 8),
    ('XPTO2196', 14, 3),
    ('XPTO2352', 5, 4),
    ('XPTO2353', 4, 5),
    ('XPTO2354', 10, 6),
    ('XPTO2277', 15, 7),
    ('XPTO2823', 15, 21),
    ('XPTO1562', 3, 18),
    ('XPTO8134', 10, 6),
    ('XPTO8135', 5, 6),
    ('XPTO8136', 6, 7),
    ('XPTO8137', 7, 8),
    ('XPTO8138', 14, 3),
    ('XPTO8139', 5, 4),
    ('XPTO8140', 4, 5),
    ('XPTO8141', 10, 6),
    ('XPTO8142', 15, 7),
    ('XPTO8143', 15, 21);
    
-- Posts
INSERT INTO posts(content, event_id, user_id, is_announcement) VALUES 
    ('Announcement Number 1', 1, 1, true),
    ('Announcement Number 2', 3, 6, true),
    ('Announcement Number 3', 4, 7, true),
    ('Announcement Number 4', 5, 8, true),
    ('Announcement Number 5', 3, 9, true),
    ('Announcement Number 6', 7, 10, true),
    ('Announcement Number 7', 7, 11, true),
    ('Announcement Number 8', 13, 12, true),
    ('Announcement Number 9', 12, 13, true),
    ('Announcement Number 10', 11, 14, true),
    ('Announcement Number 11', 10, 15, true),
    ('Announcement Number 12', 9, 16, true),
    ('Post Number 1', 5, 2, false),
    ('Post Number 2', 6, 3, false),
    ('Post Number 3', 1, 4, false),
    ('Post Number 4', 1, 5, false),
    ('Post Number 5', 2, 6, false),
    ('Post Number 6', 2, 7, false),
    ('Post Number 7', 3, 8, false),
    ('Post Number 8', 3, 9, false),
    ('Post Number 9', 8, 19, false),
    ('Post Number 10', 8, 20, false),
    ('Post Number 11', 9, 21, false),
    ('Post Number 12', 10, 22, false),
    ('Post Number 13', 11, 23, false);

-- Comments
INSERT INTO comments(content, post_id, user_id) VALUES
    ('Comment number 1!', 14, 1),
    ('Comment number 2!', 14, 2),
    ('Comment number 3!', 15, 3),
    ('Comment number 4!', 15, 4),
    ('Comment number 5!', 16, 14),
    ('Comment number 6!', 17, 15),
    ('Comment number 7!', 18, 16),
    ('Comment number 8!', 19, 16),
    ('Comment number 9!', 19, 22),
    ('Comment number 10!', 18, 21),
    ('Comment number 11!', 19, 20),
    ('Comment number 12!', 20, 16),
    ('Comment number 13!', 20, 9),
    ('Comment number 14!', 20, 4),
    ('Comment number 15!', 19, 4),
    ('Comment number 16!', 21, 23),
    ('Comment number 17!', 21, 17),
    ('Comment number 18!', 21, 18),
    ('Comment number 19!', 21, 4),
    ('Comment number 20!', 21, 5),
    ('Comment number 21!', 21, 6),
    ('Comment number 22!', 21, 7),
    ('Comment number 23!', 14, 8);

-- Ratings
INSERT INTO ratings(user_id, post_id, value) VALUES
    (1, 13, 1),
    (2, 13, -1),
    (2, 14, 1),
    (4, 16, 1),
    (14, 15, 1),
    (14, 16, -1),
    (17, 20, -1),
    (18, 14, 1),
    (19, 14, -1),
    (20, 14, -1),
    (21, 14, 1),
    (21, 15, 1),
    (21, 16, -1),
    (22, 17, -1),
    (23, 18, 1),
    (10, 19, 1),
    (11, 19, -1),
    (12, 19, -1),
    (12, 20, 1),
    (11, 21, -1),
    (13, 22, 1),
    (13, 23, -1),
    (7, 15, 1),
    (8, 15, -1),
    (9, 16, 1);

-- Tickets
INSERT INTO tickets(user_id, event_id, type, paypal_order_id)
    VALUES (1, 3, 'Paypal', 'PAYPAL-CONFIRMATION-6424');
INSERT INTO tickets(user_id, event_id, type, paypal_order_id, nif, billing_name)
    VALUES (5, 2, 'Paypal', 'PAYPAL-CONFIRMATION-7512', 245024952, 'Peter');
INSERT INTO tickets(user_id, event_id, type, event_voucher_id) VALUES 
    (1, 2, 'Voucher', 1),
    (1, 2, 'Voucher', 2),
    (3, 4, 'Voucher', 3),
    (4, 4, 'Voucher', 4),
    (5, 7, 'Voucher', 5),
    (6, 7, 'Voucher', 6),
    (7, 7, 'Voucher', 7),
    (8, 7, 'Voucher', 8),
    (9, 7, 'Voucher', 9),
    (10, 13, 'Voucher', 10),
    (11, 13, 'Voucher', 11),
    (12, 12, 'Voucher', 12),
    (13, 11, 'Voucher', 13),
    (14, 10, 'Voucher', 14),
    (15, 5, 'Voucher', 15),
    (16, 6, 'Voucher', 16),
    (17, 7, 'Voucher', 17),
    (18, 14, 'Voucher', 18),
    (19, 5, 'Voucher', 19),
    (10, 4, 'Voucher', 20),
    (21, 10, 'Voucher', 21),
    (22, 15, 'Voucher', 22),
    (23, 15, 'Voucher', 23),
    (7, 3, 'Voucher', 24);

-- Organizers
INSERT INTO organizers(user_id, event_id) VALUES 
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (14, 5),
    (15, 6),
    (16, 7),
    (17, 8),
    (18, 9),
    (19, 10),
    (20, 11),
    (21, 12),
    (22, 13),
    (23, 14),
    (23, 15),
    (11, 1),
    (8, 2),
    (9, 3),
    (10, 4),
    (12, 5),
    (13, 6),
    (13, 7),
    (14, 8),
    (15, 9),
    (15, 10),
    (15, 12),
    (3, 13),
    (4, 14),
    (5, 15);

-- Favorites
INSERT INTO favorites(user_id, event_id) VALUES 
    (5, 5),
    (4, 5),
    (3, 5),
    (2, 2),
    (18, 7),
    (12, 9),
    (13, 11),
    (17, 13),
    (15, 6),
    (14, 8),
    (13, 10),
    (9, 4),
    (8, 3),
    (7, 2),
    (6, 1),
    (20, 5),
    (20, 6),
    (20, 7),
    (21, 1),
    (21, 2),
    (21, 3),
    (22, 11),
    (22, 12),
    (22, 13),
    (23, 14);

-- Event Tags
INSERT INTO event_tags(event_id, tag_id) VALUES 
    (1, 1),
    (1, 2),
    (1, 3),
    (1, 4),
    (2, 5),
    (2, 6),
    (2, 7),
    (2, 8),
    (3, 9),
    (3, 10),
    (3, 11),
    (3, 12),
    (4, 13),
    (4, 14),
    (4, 15),
    (4, 16),
    (5, 17),
    (5, 18),
    (5, 19),
    (5, 20),
    (6, 21),
    (6, 22),
    (6, 23),
    (6, 24),
    (7, 25),
    (7, 1),
    (7, 2),
    (7, 3),
    (8, 4),
    (8, 5),
    (8, 6),
    (8, 7),
    (9, 8),
    (9, 9),
    (9, 10),
    (9, 11),
    (10, 12),
    (10, 13),
    (10, 14),
    (10, 15),
    (11, 16),
    (11, 17),
    (11, 18),
    (11, 19),
    (12, 20),
    (12, 21),
    (12, 22),
    (12, 23),
    (13, 24),
    (13, 25),
    (13, 1),
    (13, 2),
    (14, 3),
    (14, 4),
    (14, 5),
    (14, 6),
    (15, 7),
    (15, 8),
    (15, 9),
    (15, 10);

-- Issues
INSERT INTO issues(title, content, creator_id, referenced_user)
    VALUES ('Issue #1', 'I have an issue', 1, 4);
INSERT INTO issues(title, content, creator_id, referenced_event)
    VALUES ('Issue #2', 'I have an issue', 1, 3);
INSERT INTO issues(title, content, creator_id, referenced_post)
    VALUES ('Issue #3', 'I have an issue', 2, 3);
INSERT INTO issues(title, content, creator_id, referenced_comment)
    VALUES ('Issue #4', 'I have an issue', 2, 5);
INSERT INTO issues(title, content, creator_id) VALUES 
    ('Issue #5', 'I have an issue', 5),
    ('Issue #6', 'I have an issue', 6),
    ('Issue #7', 'I have an issue', 7),
    ('Issue #8', 'I have an issue', 8),
    ('Issue #9', 'I have an issue', 9),
    ('Issue #10', 'I have an issue', 10),
    ('Issue #11', 'I have an issue', 11),
    ('Issue #12', 'I have an issue', 12),
    ('Issue #13', 'I have an issue', 13),
    ('Issue #14', 'I have an issue', 14),
    ('Issue #15', 'I have an issue', 15),
    ('Issue #16', 'I have an issue', 16),
    ('Issue #17', 'I have an issue', 17),
    ('Issue #18', 'I have an issue', 18),
    ('Issue #19', 'I have an issue', 19),
    ('Issue #20', 'I have an issue', 20);

-- Notifications
INSERT INTO notifications(type, content, user_id, issue_id)
    VALUES ('IssueNotification', 'We, the admins, solved your issue', 1, 2);
INSERT INTO notifications(type, user_id, event_id, is_dismissed)
    VALUES ('EventOrganizer', 2, 2, true);
INSERT INTO notifications(type, user_id, event_id) VALUES 
    ('EventInvitation', 4, 1), 
    ('EventInvitation', 5, 2), 
    ('EventInvitation', 6, 3), 
    ('EventDisabling', 7, 4), 
    ('EventDisabling', 8, 5), 
    ('EventDisabling', 9, 6),
    ('EventActivation', 10, 7),
    ('EventActivation', 11, 8),
    ('EventActivation', 12, 9),
    ('EventCancellation', 13, 10),
    ('EventCancellation', 14, 11),
    ('EventCancellation', 15, 12),
    ('EventRemoval', 16, 13),
    ('EventRemoval', 17, 14),
    ('EventRemoval', 18, 15),
    ('EventOrganizer', 19, 1),
    ('EventOrganizer', 20, 2),
    ('EventOrganizer', 21, 3),
    ('EventUpdate', 22, 4),
    ('EventUpdate', 23, 5),
    ('EventUpdate', 1, 6),
    ('EventAnnouncement', 2, 7),
    ('EventAnnouncement', 3, 8),
    ('EventAnnouncement', 4, 9);
