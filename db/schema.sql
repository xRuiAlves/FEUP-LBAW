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
    name VARCHAR(64) NOT NULL,
	email VARCHAR(128) NOT NULL CONSTRAINT user_email_unique UNIQUE,
	password VARCHAR(128) NOT NULL,  -- other value for varchar?
    is_disabled BOOLEAN NOT NULL DEFAULT false,
    is_admin BOOLEAN NOT NULL DEFAULT false
);

-- R05
CREATE TABLE event_categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(20) NOT NULL
);

-- R04
CREATE TABLE events (
	id SERIAL PRIMARY KEY,
	title VARCHAR(30) NOT NULL,
	description TEXT NOT NULL,
	price REAL NOT NULL,
	location VARCHAR(60),
	latitude REAL,
	longitude REAL,
	start_timestamp TIMESTAMP WITH TIME zone NOT NULL,
	end_timestamp TIMESTAMP WITH TIME zone,
    event_category_id INTEGER REFERENCES event_categories(id) ON DELETE CASCADE,
	status EVENT_STATUS NOT NULL DEFAULT 'Active',
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,

    CONSTRAINT price_check CHECK (price >= 0),
    CONSTRAINT latitude_check CHECK ((latitude IS NULL) OR (latitude >= -90 AND latitude <= 90)),
    CONSTRAINT longitude_check CHECK ((longitude IS NULL) OR (longitude >= -180 AND longitude <= 180)),
    CONSTRAINT start_timestamp_check CHECK (start_timestamp > now()),
    CONSTRAINT end_timestamp_check CHECK ((end_timestamp is NULL) OR (end_timestamp > start_timestamp))
);

-- R06
CREATE TABLE tags (
	id SERIAL PRIMARY KEY,
	name VARCHAR(30) NOT NULL
);

-- R07
CREATE TABLE event_vouchers (
	id SERIAL PRIMARY KEY,
	code VARCHAR(128) NOT NULL,
    is_used BOOLEAN NOT NULL DEFAULT false,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE
);

-- R08
CREATE TABLE posts (
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    "timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    rating INTEGER DF 0,
    num_comments INTEGER DF 0,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    is_announcement BOOLEAN NOT NULL,

    CONSTRAINT announcement_post CHECK (
        (is_announcement = true AND rating IS NULL AND num_comments IS NULL) OR
        (is_announcement != true)
    ) 
);

-- R09
CREATE TABLE comments (
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    "timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE
);

-- R10
CREATE TABLE ratings (
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    value INTEGER NOT NULL,

    PRIMARY KEY (user_id, post_id),
    CONSTRAINT rating_value CHECK (
        value = 1 OR value = -1
    )
);

-- R11
CREATE TABLE tickets (
	id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	is_checked_in BOOLEAN NOT NULL DEFAULT false,
	check_in_organizer_id INTEGER REFERENCES users(id) ON UPDATE CASCADE,
	nif INTEGER DEFAULT 999999999,
	billing_name VARCHAR(64),
	address VARCHAR(128),
    event_voucher_id INTEGER REFERENCES event_vouchers(id) ON UPDATE CASCADE,
	type TICKET_PAYMENT_TYPE NOT NULL,
	paypal_order_id VARCHAR(256),

    CONSTRAINT ticket_payment CHECK (
        (type = 'Voucher' AND event_voucher_id IS NOT NULL) OR
        (type = 'Paypal' AND paypal_order_id IS NOT NULL)
    )
);

-- R12
CREATE TABLE organizers (
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,

    PRIMARY KEY (user_id, event_id)
);

-- R13
CREATE TABLE favorites (
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,

    PRIMARY KEY (user_id, event_id)
);

-- R14
CREATE TABLE event_tags (
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    tag_id INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE,

    PRIMARY KEY (event_id, tag_id)
);

-- R02
CREATE TABLE issues (
	id SERIAL PRIMARY KEY,
	title VARCHAR(64) NOT NULL,
	content VARCHAR(800) NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	is_solved BOOLEAN NOT NULL DEFAULT false,
    creator_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    solver_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
	referenced_user INTEGER REFERENCES users(id) ON DELETE CASCADE,
	referenced_event INTEGER REFERENCES events(id) ON DELETE CASCADE,
	referenced_post INTEGER REFERENCES posts(id) ON DELETE CASCADE,
	referenced_comment INTEGER REFERENCES comments(id) ON DELETE CASCADE,

    CONSTRAINT self_reporting CHECK (creator_id != referenced_user)
);

-- R03
CREATE TABLE notifications (
    id SERIAL PRIMARY KEY,
	type NOTIFICATION_TYPE NOT NULL,
	is_dismissed BOOLEAN NOT NULL DEFAULT false,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
	content TEXT,
    issue_id INTEGER REFERENCES issues(id) ON DELETE CASCADE,

    CONSTRAINT issue_notification CHECK (
		(type = 'IssueNotification' AND issue_id IS NOT NULL AND content IS NOT NULL) OR
		(type != 'IssueNotification' AND event_id IS NOT NULL)
	),

	CONSTRAINT notification_spam UNIQUE(user_id, event_id, type)
);
