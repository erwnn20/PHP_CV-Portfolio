﻿-- Create database
CREATE
DATABASE cv_db;

-- Use the database
USE
cv_db;

-- Create table for users
CREATE TABLE user
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    email      VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name  VARCHAR(100) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    admin      BOOLEAN      NOT NULL
);

INSERT INTO user (email, first_name, last_name, password, admin)
VALUES ('admin@exemple.com', 'admin', '_', 'admin', true)

-- Create table for users
CREATE TABLE cv
(
    id           INT PRIMARY KEY AUTO_INCREMENT,
    creator_id   INT          NOT NULL,
    title        VARCHAR(255) NOT NULL,
    description  TEXT,
    skills       JSON,
    certificates JSON,
    experiences  JSON
);

-- Create table for project for portfolio
CREATE TABLE project
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    creator_id  INT          NOT NULL,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    images      JSON
);
