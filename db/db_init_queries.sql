CREATE DATABASE mentor_match DEFAULT CHARACTER	SET utf8;
USE mentor_match;

-- table for match forms
-- TODO create a uuid of 24 random ASCI chars. Set as index. To be used for public form urls
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

-- table for questions in match forms
CREATE TABLE question (
    question_id INT NOT NULL AUTO_INCREMENT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    match_form_id INT NOT NULL,
    priority INT NOT NULL,
    question_text TEXT NOT NULL,
    PRIMARY KEY(question_id),
    FOREIGN KEY(match_form_id)
      REFERENCES match_form(match_form_id)
      ON DELETE CASCADE
);

-- table for question options
CREATE TABLE question_option (
    option_id INT NOT NULL AUTO_INCREMENT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL,
    PRIMARY KEY(option_id),
    FOREIGN KEY(question_id)
        REFERENCES question(question_id)
        ON DELETE CASCADE
);

-- table for applications
CREATE TABLE application (
     application_id INT NOT NULL AUTO_INCREMENT,
     date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     match_form_id INT NOT NULL,
     m_type TEXT NOT NULL,
     first_name TEXT NOT NULL,
     last_name TEXT NOT NULL,
     email TEXT NOT NULL,
     phone TEXT NOT NULL,
     stud_id TEXT NOT NULL,
     PRIMARY KEY(application_id),
     FOREIGN KEY(match_form_id)
         REFERENCES match_form(match_form_id)
         ON DELETE CASCADE
);

-- table for application questions and response
CREATE TABLE question_response (
    question_response_id INT NOT NULL AUTO_INCREMENT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    application_id INT NOT NULL,
    question_id INT NOT NULL,
    option_id INT NOT NULL,
    PRIMARY KEY(question_response_id),
    FOREIGN KEY(application_id)
       REFERENCES application(application_id)
       ON DELETE CASCADE,
    FOREIGN KEY(question_id)
       REFERENCES question(question_id)
       ON DELETE CASCADE,
    FOREIGN KEY(option_id)
       REFERENCES question_option(option_id)
       ON DELETE CASCADE
);
