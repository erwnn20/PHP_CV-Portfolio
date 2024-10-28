-- Create database
CREATE
DATABASE cv_db;

-- Use the database
USE
cv_db;

-- Create table for users
CREATE TABLE user
(
    id              UUID PRIMARY KEY,
    email           VARCHAR(255) UNIQUE NOT NULL,
    first_name      VARCHAR(100)        NOT NULL,
    last_name       VARCHAR(100)        NOT NULL,
    profile_picture BOOLEAN                      DEFAULT false,
    password        VARCHAR(255)        NOT NULL,
    admin           BOOLEAN             NOT NULL DEFAULT false,
    ban_id          UUID UNIQUE
);

INSERT INTO user (id, email, first_name, last_name, password, admin)
VALUES ('f15fab2b-e769-4bdf-8f17-7f58ee4cfa5d', 'admin@exemple.com', 'admin', '_', 'admin', true);

-- Create table for users
CREATE TABLE cv
(
    id           UUID PRIMARY KEY,
    creator_id   UUID UNIQUE NOT NULL,
    image        BOOLEAN DEFAULT false,
    title        VARCHAR(255),
    description  TEXT,
    email        VARCHAR(255),
    phone_number VARCHAR(20),
    address      TEXT,
    skills       JSON COMMENT 'Structure: skill (string), year_exp (int)',
    languages    JSON COMMENT 'Structure: lang (string), level (string)',
    interests    JSON COMMENT 'Type: string[]',
    experiences  JSON COMMENT 'Structure: role (string), company (string), tasks (string[]), start_date (string: year-month), end_date (string: year-month)',
    certificates JSON COMMENT 'Structure: degree (string), school (string), date (string: year)'
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
    images      JSON COMMENT 'Type: string[]',
    ban_id      UUID UNIQUE
);

-- Create table for banned user and projects
CREATE TABLE ban
(
    id       UUID PRIMARY KEY,
    admin_id UUID         NOT NULL,
    cause    VARCHAR(255) NOT NULL,
    message  TEXT
);

-- Create foreign keys
ALTER TABLE user
    ADD FOREIGN KEY (ban_id) REFERENCES ban (id) ON DELETE SET NULL;
ALTER TABLE cv
    ADD FOREIGN KEY (creator_id) REFERENCES user (id);
ALTER TABLE project
    ADD FOREIGN KEY (creator_id) REFERENCES user (id);
ALTER TABLE project
    ADD FOREIGN KEY (ban_id) REFERENCES ban (id) ON DELETE SET NULL;
ALTER TABLE ban
    ADD FOREIGN KEY (admin_id) REFERENCES user (id);
