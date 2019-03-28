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

-- Update comment content
UPDATE comments
SET content = 'This is an updated comment!'
WHERE id = 3;

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