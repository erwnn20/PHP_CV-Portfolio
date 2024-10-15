-- Create database
CREATE
DATABASE cv_db;

-- Use the database
USE
cv_db;

-- Create table for users
CREATE TABLE user
(
    id         UUID PRIMARY KEY,
    email      VARCHAR(255) UNIQUE NOT NULL,
    first_name VARCHAR(100)        NOT NULL,
    last_name  VARCHAR(100)        NOT NULL,
    password   VARCHAR(255)        NOT NULL,
    admin      BOOLEAN             NOT NULL DEFAULT false
);

INSERT INTO user (id, email, first_name, last_name, password, admin)
VALUES ('f15fab2b-e769-4bdf-8f17-7f58ee4cfa5d', 'admin@exemple.com', 'admin', '_', 'admin', true);

-- Create table for users
CREATE TABLE cv
(
    id           UUID PRIMARY KEY,
    creator_id   UUID UNIQUE  NOT NULL,
    title        VARCHAR(255),
    description  TEXT,
    skills       JSON COMMENT 'Type: string[]',
    experiences  JSON COMMENT 'Structure: role (string), company (string), start_date (year-month), end_date (year-month)',
    certificates JSON COMMENT 'Structure: degree (string), school (string), date (year)'
);

-- Create table for project for portfolio
CREATE TABLE project
(
    id          UUID PRIMARY KEY,
    creator_id  UUID         NOT NULL,
    title       VARCHAR(255) NOT NULL,
    description TEXT         NOT NULL,
    theme       VARCHAR(100),
    link        VARCHAR(255),
    images      JSON COMMENT 'Type: string[]'
);

-- Create foreign keys
ALTER TABLE cv
    ADD FOREIGN KEY (creator_id) REFERENCES user (id);
ALTER TABLE project
    ADD FOREIGN KEY (creator_id) REFERENCES user (id);