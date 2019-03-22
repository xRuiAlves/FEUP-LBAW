-- Domains

DROP TYPE IF EXISTS EVENT_STATUS;
CREATE TYPE EVENT_STATUS AS ENUM ('Active', 'Disabled', 'Cancelled');

DROP TYPE IF EXISTS EVENT_NOTIFICATION_TYPE;
CREATE TYPE EVENT_NOTIFICATION_TYPE AS ENUM ('EventInvitation', 'EventDisabling', 'EventCancellation', 'EventRemoval', 'EventModerator');

DROP TYPE IF EXISTS TICKET_PAYMENT_TYPE;
CREATE TYPE TICKET_PAYMENT_TYPE AS ENUM ('Voucher', 'Paypal');


-- Tables

DROP TABLE IF EXISTS Person;
CREATE TABLE Person (
	person_id SERIAL PRIMARY KEY,
	name varchar(64) NOT NULL,
	email text NOT NULL CONSTRAINT person_email_unique UNIQUE,
	password text NOT NULL,
	oauth_token text,
	is_disabled BOOLEAN NOT NULL DEFAULT false,
	is_admin BOOLEAN NOT NULL DEFAULT false
);

DROP TABLE IF EXISTS Notification;
CREATE TABLE Notification (
    notification_id SERIAL PRIMARY KEY,
	is_dismissed BOOLEAN NOT NULL DEFAULT false,
	"timestamp" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    person_id INTEGER NOT NULL REFERENCES Person(person_id) ON UPDATE CASCADE
);


