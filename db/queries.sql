-- Authenticate user (log in process) - TODO: MANTER ESTA QUERY?
SELECT id, is_disabled
FROM users
WHERE email = 'XXXXXX'
AND password = 'XXXXXX';

-- Getting user information after log in
SELECT name, email, is_admin
FROM users
WHERE id = 1;

-- User checks their notifications
SELECT type, timestamp, event_id, content, issue_id
FROM notifications
WHERE is_dismissed = false AND
	  user_id = 4
ORDER BY timestamp DESC;

-- Querying a user's dashboard (participating + organizing + created events)
SELECT DISTINCT title, price, latitude, longitude, start_timestamp, end_timestamp, event_categories.name AS category, status
FROM tickets 
	INNER JOIN events ON (tickets.event_id = events.id)
	INNER JOIN event_categories ON (events.event_category_id = event_categories.id)
WHERE tickets.user_id = 1
UNION
SELECT title, price, latitude, longitude, start_timestamp, end_timestamp, event_categories.name AS category, status
FROM organizers 
	INNER JOIN events ON (organizers.event_id = events.id)
	INNER JOIN event_categories ON (events.event_category_id = event_categories.id)
WHERE organizers.user_id = 1;

-- Getting full-text-search results on events - implies the pre-computation of search field on events
SELECT events.id, title, price, latitude, longitude, start_timestamp, end_timestamp, event_categories.name AS category
FROM events
INNER JOIN event_categories ON (events.event_category_id = event_categories.id)
WHERE search @@ plainto_tsquery('english', 'SinF')
ORDER BY ts_rank(search, plainto_tsquery('english', 'SinF')) DESC;


-- Getting an event's information for the event page
SELECT title, description, price, latitude, longitude, start_timestamp, end_timestamp, event_categories.name AS category, users.name AS creator, status
FROM events 
	INNER JOIN users ON (events.user_id = users.id)
	INNER JOIN event_categories ON (events.event_category_id = event_categories.id)
WHERE events.id = 1;

-- Getting all of the event's announcements
SELECT content, timestamp
FROM posts
WHERE event_id = 1 AND
	  is_announcement = true
ORDER BY timestamp DESC;

-- Getting an event's discussion forum
SELECT content, timestamp, rating, num_comments, users.name AS creator
FROM posts INNER JOIN users ON (posts.user_id = users.id)
WHERE posts.event_id = 4 AND
	  is_announcement = false
ORDER BY rating DESC, timestamp DESC; -- most well rated come first

-- Getting the comments for a given post
SELECT content, timestamp, users.name AS creator
FROM comments INNER JOIN users ON (comments.user_id = users.id)
WHERE post_id = 3
OFFSET 0
LIMIT 5;

-- Event organizer seeing all the event's tickets (to chech-in users)
SELECT users.name AS attendee, is_checked_in, event_voucher_id, paypal_order_id
FROM tickets INNER JOIN users ON (tickets.user_id = users.id)
WHERE event_id = 3;

-- Application administrator views all users (with pagination)
SELECT name, email, is_disabled, is_admin
FROM users
OFFSET 0
LIMIT 15;

-- Application administrator views user issues (with pagination)
SELECT title, content, timestamp, is_solved, users.name AS creator, referenced_user, referenced_event, referenced_post, referenced_comment
FROM issues INNER JOIN users ON (issues.creator_id = users.id)
OFFSET 0
LIMIT 15;

-- Application administrator views all events (with pagination)
SELECT title, description, price, start_timestamp, end_timestamp, status, event_categories.name as category, users.name as creator
FROM events 
	INNER JOIN users ON (events.user_id = users.id)
	INNER JOIN event_categories ON (events.event_category_id = event_categories.id)
OFFSET 0
LIMIT 15;

-- Getting full-text-search results on issues - implies the pre-computation of search field on issues
SELECT issues.id, title, content, timestamp, is_solved, users.name as creator
FROM issues
INNER JOIN users ON (issues.creator_id = users.id)
WHERE issues.search @@ plainto_tsquery('english', 'issue search')
ORDER BY ts_rank(issues.search, plainto_tsquery('english', 'issue search')) DESC;

-- Getting full-text-search results on users - implies the pre-computation of search field on users
SELECT id, name, email
FROM users
WHERE search @@ plainto_tsquery('english', 'mark')
ORDER BY ts_rank(search, plainto_tsquery('english', 'mark')) DESC;







