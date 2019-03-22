DROP TABLE IF EXISTS EventVoucher;
DROP TABLE IF EXISTS Tag;
DROP TABLE IF EXISTS EventNotification;
DROP TABLE IF EXISTS Event;
DROP TABLE IF EXISTS EventCategory;
DROP TABLE IF EXISTS IssueNotification;
DROP TABLE IF EXISTS Notification;
DROP TABLE IF EXISTS Issue;
DROP TABLE IF EXISTS Person;
DROP TYPE IF EXISTS TICKET_PAYMENT_TYPE;
DROP TYPE IF EXISTS EVENT_NOTIFICATION_TYPE;
DROP TYPE IF EXISTS EVENT_STATUS;


-- Types

CREATE TYPE EVENT_STATUS AS ENUM ('Active', 'Disabled', 'Cancelled');

CREATE TYPE EVENT_NOTIFICATION_TYPE AS ENUM ('EventInvitation', 'EventDisabling', 'EventCancellation', 'EventRemoval', 'EventModerator');

CREATE TYPE TICKET_PAYMENT_TYPE AS ENUM ('Voucher', 'Paypal');


-- Tables

-- R01
CREATE TABLE Person (
	person_id SERIAL PRIMARY KEY,
	name TEXT NOT NULL,
	email TEXT NOT NULL CONSTRAINT person_email_unique UNIQUE,
	password TEXT NOT NULL,
	oauth_token TEXT,
	is_disabled BOOLEAN NOT NULL DEFAULT false,
	is_admin BOOLEAN NOT NULL DEFAULT false
);

-- R02
CREATE TABLE Issue (
	issue_id SERIAL PRIMARY KEY,
	title TEXT NOT NULL,
	content TEXT NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	is_solved BOOLEAN NOT NULL DEFAULT false,
    creator_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE,
    solver_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE,
	referenced_person INTEGER REFERENCES Person(person_id) ON UPDATE CASCADE
	-- referenced_event INTEGER REFERENCES Event(event_id) ON UPDATE CASCADE,
	-- referenced_announcement INTEGER REFERENCES Announcement(announcement_id) ON UPDATE CASCADE,
	-- referenced_post INTEGER REFERENCES Post(post_id) ON UPDATE CASCADE,
	-- referenced_comment INTEGER REFERENCES Comment(comment_id) ON UPDATE CASCADE
);

-- R03
CREATE TABLE Notification (
    notification_id SERIAL PRIMARY KEY,
	is_dismissed BOOLEAN NOT NULL DEFAULT false,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE
);

-- R04
CREATE TABLE IssueNotification(
    notification_id INTEGER PRIMARY KEY REFERENCES Notification(notification_id) ON UPDATE CASCADE,
	content TEXT NOT NULL,
    issue_id INTEGER NOT NULL REFERENCES Issue(issue_id) ON UPDATE CASCADE
);

-- R07
CREATE TABLE EventCategory (
	event_category_id SERIAL PRIMARY KEY,
	name TEXT NOT NULL
);

-- R06
CREATE TABLE Event (
	event_id SERIAL PRIMARY KEY,
	title TEXT NOT NULL,
	description TEXT NOT NULL,
	price real,
	latitude real,
	longitude real,
	start_timestamp TIMESTAMP WITH TIME zone NOT NULL,
	end_timestamp TIMESTAMP WITH TIME zone,
    event_category_id INTEGER NOT NULL REFERENCES EventCategory(event_category_id) ON UPDATE CASCADE,
	TYPE EVENT_STATUS NOT NULL,
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE,

    CONSTRAINT price_check CHECK (price >= 0),
    CONSTRAINT latitude_check CHECK ((latitude IS NULL) OR (latitude >= -90 AND latitude <= 90)),
    CONSTRAINT longitude_check CHECK ((longitude IS NULL) OR (longitude >= -180 AND longitude <= 180)),
    CONSTRAINT start_timestamp_check CHECK (start_timestamp > now()),
    CONSTRAINT end_timestamp_check CHECK ((end_timestamp is NULL) OR (end_timestamp > start_timestamp))
);

-- R05
CREATE TABLE EventNotification(
    notification_id INTEGER PRIMARY KEY REFERENCES Notification(notification_id) ON UPDATE CASCADE,
	TYPE EVENT_NOTIFICATION_TYPE NOT NULL,
    event_id INTEGER NOT NULL REFERENCES Event(event_id) ON UPDATE CASCADE
);

-- R08
CREATE TABLE Tag (
	tag_id SERIAL PRIMARY KEY,
	name TEXT NOT NULL
);

-- R09
CREATE TABLE EventVoucher (
	event_voucher_id SERIAL PRIMARY KEY,
	code TEXT NOT NULL,
	is_dismissed BOOLEAN NOT NULL DEFAULT false,
    event_id INTEGER NOT NULL REFERENCES Event(event_id) ON UPDATE CASCADE,
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE
);













