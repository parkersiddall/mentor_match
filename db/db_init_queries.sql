CREATE DATABASE mentor_match DEFAULT CHARACTER	SET utf8;
USE mentor_match;

-- table for users
CREATE TABLE app_user (
    user_id INT NOT NULL AUTO_INCREMENT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email TEXT NOT NULL,
    password_hash TEXT NOT NULL,
    school_name TEXT NOT NULL,
    school_city TEXT NOT NULL,
    school_state TEXT NOT NULL,
    school_country TEXT NOT NULL,
    accept_terms BOOL NOT NULL DEFAULT FALSE,
    approved BOOL NOT NULL DEFAULT FALSE,
    PRIMARY KEY(user_id)
);

-- table for match forms
-- TODO create a uuid of 24 random ASCI chars. Set as index. To be used for public form urls
-- TODO add user column to this query (and write create to update existing table in DB)
CREATE TABLE match_form (
    match_form_id INT NOT NULL AUTO_INCREMENT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creator TEXT NOT NULL,
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
    PRIMARY KEY(match_form_id),
    FOREIGN KEY(creator)
        REFERENCES app_user(user_id)
        ON DELETE CASCADE
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
     first_name TEXT,
     last_name TEXT,
     email TEXT,
     phone TEXT,
     stud_id TEXT,
     num_matches INT NOT NULL DEFAULT 0,
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

-- table for matches
CREATE TABLE match_pair (
   match_id INT NOT NULL AUTO_INCREMENT,
   date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   match_form_id INT NOT NULL,
   mentor_application_id INT NOT NULL,
   mentee_application_id INT NOT NULL,
   confidence_rate FLOAT NOT NULL,
   PRIMARY KEY(match_id),
   FOREIGN KEY(match_form_id)
       REFERENCES match_form(match_form_id)
       ON DELETE CASCADE,
   FOREIGN KEY(mentor_application_id)
       REFERENCES application(application_id)
       ON DELETE CASCADE,
   FOREIGN KEY(mentee_application_id)
       REFERENCES application(application_id)
       ON DELETE CASCADE
);
