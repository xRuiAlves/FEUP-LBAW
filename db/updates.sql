-- Update event
UPDATE events
SET description = 'This is an updated event description',
    latitude = 51,
    longitude = 52,
    status = 'Disabled'
WHERE id = 2;

-- Delete event
DELETE FROM events
WHERE id = 5;

-- Update post
UPDATE posts
SET content = 'This is an updated post!'
WHERE id = 4;

-- Update comment
UPDATE comments
SET content = 'This is an updated comment!'
WHERE id = 3;

-- Update issue
UPDATE issues
SET is_solved = true,
    solver_id = 5
WHERE id = 2;

-- Update rating
UPDATE ratings
SET value = -1
WHERE user_id = 1 AND post_id = 1;

-- Delete rating
DELETE FROM ratings
WHERE user_id = 2 AND post_id = 2;