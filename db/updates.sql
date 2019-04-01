-- Event organizer updating the event's data
UPDATE events
SET description = 'This is an updated event description',
    latitude = 51,
    longitude = 52,
WHERE id = 2;

-- Event creator cancelling an event
UPDATE events
SET status = 'Cancelled'
WHERE id = 3;

-- Platform administrator disabling an event
UPDATE events
SET status = 'Disabled'
WHERE id = 4;

-- Update post content
UPDATE posts
SET content = 'This is an updated post!'
WHERE id = 4;

-- Administrator solves an issue
UPDATE issues
SET is_solved = true,
    solver_id = 5
WHERE id = 2;

-- Updating a rating on a post
UPDATE ratings
SET value = -1
WHERE user_id = 1 AND post_id = 1;

-- Removing rating from a post
DELETE FROM ratings
WHERE user_id = 2 AND post_id = 2;

-- Admin updating user (set user as admin)
UPDATE users
SET is_admin = true
WHERE id = 3;

-- Admin updating user (disable user account)
UPDATE users
SET is_disabled = true
WHERE id = 4;

-- User registering
INSERT INTO users(name, email, password)
VALUES ('Paul Peterson', 'paul@email.net', 'XXXXX');

-- Event creation
INSERT INTO events(title, description, price, latitude, longitude, start_timestamp, end_timestamp, event_category_id, user_id)
VALUES (
    'South-West Yearly Rally', 
    'Take a walk on the wild side...!', 
    7.50, 
    60, 
    120, 
    to_timestamp('30-12-2022 15:00:00', 'dd-mm-yyyy hh24:mi:ss'), 
    to_timestamp('31-12-2022 09:00:00', 'dd-mm-yyyy hh24:mi:ss'), 
    4, 
    2
);

-- Creating a tag and adding it to the event
INSERT INTO tags(name) VALUES ('Fun');
INSERT INTO event_tags(event_id, tag_id) VALUES (6, 6);

-- Organizer emits a voucher 
INSERT INTO event_vouchers(code, event_id, user_id)
VALUES ('VOUCHER-85205', 4, 5);

-- Organizer creates an announcement about the event
INSERT INTO posts(content, event_id, user_id, is_announcement)
VALUES ('Bring your coats, it is going to be cold.', 4, 5, true); 

-- User creates a post in the event's discussion section
INSERT INTO posts(content, event_id, user_id, is_announcement)
VALUES ('How cold is it going to be?', 4, 2, false); 

-- Organizer answer a user's question in the discussion section
INSERT INTO comments(content, post_id, user_id)
VALUES ('Very cold!', 6, 5);

-- User submits an issue report an event
INSERT INTO issues(title, content, creator_id, referenced_event)
VALUES (
    'Events organizers lying about the weather', 
    'The event organizers said it is going to be cold. However, I checked, and it is going to be very hot.', 
    2, 
    4
);

-- Admin answer a user's issue and the user is notified
INSERT INTO notifications(type, content, user_id, issue_id)
VALUES ('IssueNotification', 'The event organizers are going to get banned. Thank you.', 2, 6);

-- User gets invited to attend an event
INSERT INTO notifications(type, user_id, event_id)
VALUES ('EventInvitation', 3, 2);

-- User favorites an event
INSERT INTO favorites(user_id, event_id)
VALUES (3, 1);

-- User rates a post
INSERT INTO ratings(user_id, post_id, value)
VALUES (3, 6, -1);
