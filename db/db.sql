-----------------------------------------------
-- Clear Previously existing table and types --
-----------------------------------------------

DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS issues CASCADE;
DROP TABLE IF EXISTS event_tags CASCADE;
DROP TABLE IF EXISTS favorites CASCADE;
DROP TABLE IF EXISTS organizers CASCADE;
DROP TABLE IF EXISTS tickets CASCADE;
DROP TABLE IF EXISTS ratings CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS posts CASCADE;
DROP TABLE IF EXISTS announcements CASCADE;
DROP TABLE IF EXISTS event_vouchers CASCADE;
DROP TABLE IF EXISTS tags CASCADE;
DROP TABLE IF EXISTS events CASCADE;
DROP TABLE IF EXISTS event_categories CASCADE;
DROP TABLE IF EXISTS users CASCADE;

DROP TYPE IF EXISTS TICKET_PAYMENT_TYPE CASCADE;
DROP TYPE IF EXISTS NOTIFICATION_TYPE CASCADE;
DROP TYPE IF EXISTS EVENT_STATUS CASCADE;

-----------
-- Types --
-----------

CREATE TYPE EVENT_STATUS AS ENUM ('Active', 'Disabled', 'Cancelled');

CREATE TYPE NOTIFICATION_TYPE AS ENUM ('IssueNotification', 'EventInvitation', 'EventDisabling', 'EventCancellation', 'EventRemoval', 'EventModerator');

CREATE TYPE TICKET_PAYMENT_TYPE AS ENUM ('Voucher', 'Paypal');

------------
-- Tables --
------------

-- R01
CREATE TABLE users (
	id SERIAL PRIMARY KEY,
	name TEXT NOT NULL,
	email TEXT NOT NULL CONSTRAINT user_email_unique UNIQUE,
	password TEXT NOT NULL,
	oauth_token TEXT,
	is_disabled BOOLEAN NOT NULL DEFAULT false,
	is_admin BOOLEAN NOT NULL DEFAULT false
);

-- R07
CREATE TABLE event_categories (
	id SERIAL PRIMARY KEY,
	name TEXT NOT NULL
);

-- R06
CREATE TABLE events (
	id SERIAL PRIMARY KEY,
	title TEXT NOT NULL,
	description TEXT NOT NULL,
	price real NOT NULL,
	latitude real,
	longitude real,
	start_timestamp TIMESTAMP WITH TIME zone NOT NULL,
	end_timestamp TIMESTAMP WITH TIME zone,
    event_category_id INTEGER NOT NULL REFERENCES event_categories(event_category_id) ON UPDATE CASCADE,
	TYPE EVENT_STATUS NOT NULL,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE,

    CONSTRAINT price_check CHECK (price >= 0),
    CONSTRAINT latitude_check CHECK ((latitude IS NULL) OR (latitude >= -90 AND latitude <= 90)),
    CONSTRAINT longitude_check CHECK ((longitude IS NULL) OR (longitude >= -180 AND longitude <= 180)),
    CONSTRAINT start_timestamp_check CHECK (start_timestamp > now()),
    CONSTRAINT end_timestamp_check CHECK ((end_timestamp is NULL) OR (end_timestamp > start_timestamp))
);

-- R08
CREATE TABLE tags (
	id SERIAL PRIMARY KEY,
	name TEXT NOT NULL
);

-- R09
CREATE TABLE event_vouchers (
	id SERIAL PRIMARY KEY,
	code TEXT NOT NULL,
	is_dismissed BOOLEAN NOT NULL DEFAULT false,
    event_id INTEGER NOT NULL REFERENCES events(event_id) ON UPDATE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE
);

-- R10
CREATE TABLE announcements (
 	id SERIAL PRIMARY KEY,
	content TEXT NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    event_id INTEGER NOT NULL REFERENCES events(event_id) ON UPDATE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE
);

-- R11
CREATE TABLE posts (
 	post_id SERIAL PRIMARY KEY,
	content TEXT NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	rating INTEGER NOT NULL DEFAULT 0,
	num_comments INTEGER NOT NULL DEFAULT 0,
    event_id INTEGER NOT NULL REFERENCES events(event_id) ON UPDATE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE
);

-- R12
CREATE TABLE comments (
 	id SERIAL PRIMARY KEY,
	content TEXT NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    post_id INTEGER NOT NULL REFERENCES posts(post_id) ON UPDATE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE
);

-- R13
CREATE TABLE ratings (
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE,
    post_id INTEGER NOT NULL REFERENCES posts(post_id) ON UPDATE CASCADE,

    PRIMARY KEY (user_id, post_id)
);

-- R14
CREATE TABLE tickets (
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(event_id) ON UPDATE CASCADE,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	is_checked_in BOOLEAN NOT NULL DEFAULT false,
	check_in_organizer_id INTEGER REFERENCES users(user_id) ON UPDATE CASCADE,
	nif INTEGER DEFAULT 999999999,
	billing_name TEXT,
	address TEXT,
    event_voucher_id INTEGER REFERENCES event_vouchers(event_voucher_id) ON UPDATE CASCADE,
	TYPE TICKET_PAYMENT_TYPE NOT NULL,

    PRIMARY KEY (user_id, event_id)
);

-- R15
CREATE TABLE organizers (
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(event_id) ON UPDATE CASCADE,

    PRIMARY KEY (user_id, event_id)
);

-- R16
CREATE TABLE favorites (
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(event_id) ON UPDATE CASCADE,

    PRIMARY KEY (user_id, event_id)
);

-- R17
CREATE TABLE event_tags (
    event_id INTEGER NOT NULL REFERENCES events(event_id) ON UPDATE CASCADE,
    tag_id INTEGER NOT NULL REFERENCES tags(tag_id) ON UPDATE CASCADE,

    PRIMARY KEY (event_id, tag_id)
);

-- R02
CREATE TABLE issues (
	id SERIAL PRIMARY KEY,
	title TEXT NOT NULL,
	content TEXT NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	is_solved BOOLEAN NOT NULL DEFAULT false,
    creator_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE,
    solver_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE,
	referenced_user INTEGER REFERENCES users(user_id) ON UPDATE CASCADE,
	referenced_event INTEGER REFERENCES events(event_id) ON UPDATE CASCADE,
	referenced_announcement INTEGER REFERENCES announcements(announcement_id) ON UPDATE CASCADE,
	referenced_post INTEGER REFERENCES posts(post_id) ON UPDATE CASCADE,
	referenced_comment INTEGER REFERENCES comments(comment_id) ON UPDATE CASCADE
);

-- R03
CREATE TABLE notifications (
    id SERIAL PRIMARY KEY,
	TYPE NOTIFICATION_TYPE NOT NULL,
	is_dismissed BOOLEAN NOT NULL DEFAULT false,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON UPDATE CASCADE,
    event_id INTEGER REFERENCES events(event_id) ON UPDATE CASCADE,
	content TEXT,
    issue_id INTEGER REFERENCES issues(issue_id) ON UPDATE CASCADE
);
