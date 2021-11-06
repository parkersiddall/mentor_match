CREATE DATABASE mentor_match DEFAULT CHARACTER	SET utf8;
USE mentor_match;

-- table for match forms
CREATE TABLE match_form (
    match_form_id INT NOT NULL AUTO_INCREMENT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    title TEXT NOT NULL,
    mentor_desc TEXT NOT NULL,
    mentee_desc TEXT NOT NULL,
    mentor_app_open BOOL NOT NULL DEFAULT FALSE,
    mentee_app_open BOOL NOT NULL DEFAULT FALSE,
    collect_first_name BOOL NOT NULL DEFAULT FALSE,
    collect_last_name BOOL NOT NULL DEFAULT FALSE,
    collect_email BOOL NOT NULL DEFAULT FALSE,
    collect_phone BOOL NOT NULL DEFAULT FALSE,
    collect_stud_id BOOL NOT NULL DEFAULT FALSE,
    PRIMARY KEY(match_form_id)
);

