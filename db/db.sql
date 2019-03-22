-----------------------------------------------
-- Clear Previously existing table and types --
-----------------------------------------------

DROP TABLE IF EXISTS IssueNotification;
DROP TABLE IF EXISTS Issue;
DROP TABLE IF EXISTS EventTag;
DROP TABLE IF EXISTS Favorite;
DROP TABLE IF EXISTS Organizing;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS Rating;
DROP TABLE IF EXISTS Comment;
DROP TABLE IF EXISTS Post;
DROP TABLE IF EXISTS Announcement;
DROP TABLE IF EXISTS EventVoucher;
DROP TABLE IF EXISTS Tag;
DROP TABLE IF EXISTS EventNotification;
DROP TABLE IF EXISTS Event;
DROP TABLE IF EXISTS EventCategory;
DROP TABLE IF EXISTS Notification;
DROP TABLE IF EXISTS Person;

DROP TYPE IF EXISTS TICKET_PAYMENT_TYPE;
DROP TYPE IF EXISTS EVENT_NOTIFICATION_TYPE;
DROP TYPE IF EXISTS EVENT_STATUS;

-----------
-- Types --
-----------

CREATE TYPE EVENT_STATUS AS ENUM ('Active', 'Disabled', 'Cancelled');

CREATE TYPE EVENT_NOTIFICATION_TYPE AS ENUM ('EventInvitation', 'EventDisabling', 'EventCancellation', 'EventRemoval', 'EventModerator');

CREATE TYPE TICKET_PAYMENT_TYPE AS ENUM ('Voucher', 'Paypal');

------------
-- Tables --
------------

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

-- R03
CREATE TABLE Notification (
    notification_id SERIAL PRIMARY KEY,
	is_dismissed BOOLEAN NOT NULL DEFAULT false,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE
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
	price real NOT NULL,
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

-- R10
CREATE TABLE Announcement (
 	announcement_id SERIAL PRIMARY KEY,
	content TEXT NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    event_id INTEGER NOT NULL REFERENCES Event(event_id) ON UPDATE CASCADE,
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE
);

-- R11
CREATE TABLE Post (
 	post_id SERIAL PRIMARY KEY,
	content TEXT NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	rating INTEGER NOT NULL DEFAULT 0,
	num_comments INTEGER NOT NULL DEFAULT 0,
    event_id INTEGER NOT NULL REFERENCES Event(event_id) ON UPDATE CASCADE,
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE
);

-- R12
CREATE TABLE Comment (
 	comment_id SERIAL PRIMARY KEY,
	content TEXT NOT NULL,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    post_id INTEGER NOT NULL REFERENCES Post(post_id) ON UPDATE CASCADE,
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE
);

-- R13
CREATE TABLE Rating (
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE,
    post_id INTEGER NOT NULL REFERENCES Post(post_id) ON UPDATE CASCADE,

    PRIMARY KEY (person_id, post_id)
);

-- R14
CREATE TABLE Ticket (
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE,
    event_id INTEGER NOT NULL REFERENCES Event(event_id) ON UPDATE CASCADE,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
	is_checked_in BOOLEAN NOT NULL DEFAULT false,
	check_in_organizer_id INTEGER REFERENCES Person(person_id) ON UPDATE CASCADE,
	nif INTEGER DEFAULT 999999999,
	billing_name TEXT,
	address TEXT,
    event_voucher_id INTEGER REFERENCES EventVoucher(event_voucher_id) ON UPDATE CASCADE,
	TYPE TICKET_PAYMENT_TYPE NOT NULL,

    PRIMARY KEY (person_id, event_id)
);

-- R15
CREATE TABLE Organizing (
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE,
    event_id INTEGER NOT NULL REFERENCES Event(event_id) ON UPDATE CASCADE,

    PRIMARY KEY (person_id, event_id)
);

-- R16
CREATE TABLE Favorite (
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE,
    event_id INTEGER NOT NULL REFERENCES Event(event_id) ON UPDATE CASCADE,

    PRIMARY KEY (person_id, event_id)
);

-- R17
CREATE TABLE EventTag (
    event_id INTEGER NOT NULL REFERENCES Event(event_id) ON UPDATE CASCADE,
    tag_id INTEGER NOT NULL REFERENCES Tag(tag_id) ON UPDATE CASCADE,

    PRIMARY KEY (event_id, tag_id)
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
	referenced_person INTEGER REFERENCES Person(person_id) ON UPDATE CASCADE,
	referenced_event INTEGER REFERENCES Event(event_id) ON UPDATE CASCADE,
	referenced_announcement INTEGER REFERENCES Announcement(announcement_id) ON UPDATE CASCADE,
	referenced_post INTEGER REFERENCES Post(post_id) ON UPDATE CASCADE,
	referenced_comment INTEGER REFERENCES Comment(comment_id) ON UPDATE CASCADE
);

-- R04
CREATE TABLE IssueNotification(
    notification_id INTEGER PRIMARY KEY REFERENCES Notification(notification_id) ON UPDATE CASCADE,
	content TEXT NOT NULL,
    issue_id INTEGER NOT NULL REFERENCES Issue(issue_id) ON UPDATE CASCADE
);
