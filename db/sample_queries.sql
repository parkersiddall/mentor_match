-- create new match_form
INSERT INTO match_form (
    title,
    mentor_desc,
    mentee_desc,
    mentor_app_open,
    mentee_app_open,
    collect_first_name,
    collect_last_name,
    collect_email,
    collect_phone,
    collect_stud_id
)
VALUES (
   "sample title 2",
   "sample mentor desc 2",
   "sample mentee_desc 2",
   FALSE,
   FALSE,
   TRUE,
   TRUE,
   TRUE,
   TRUE,
   FALSE
);

-- create new question for match form
INSERT INTO question (
    match_form_id,
    priority,
    question_text
)
VALUES (
   1,
   1,
   "Sample question 1"
);

-- create new option for question
INSERT INTO option (
    question_id,
    option_text
)
VALUES (
   1,
   "Sample option 1"
);

-- select all questions for given match_form
SELECT mf.match_form_id, q.question_id, q.question_text, q.priority
FROM match_form mf
         JOIN question q ON mf.match_form_id = q.match_form_id
WHERE mf.match_form_id = 1
ORDER BY q.priority;

-- select all options for a given match_form
SELECT mf.match_form_id, q.question_id, q.question_text, q.priority, o.option_text
FROM match_form mf
         JOIN question q ON mf.match_form_id = q.match_form_id
         JOIN `option` o ON q.question_id = o.question_id
WHERE mf.match_form_id = 1
ORDER BY q.priority;

-- get questions and responses based on application id
SELECT a.m_type, a.first_name, a.last_name, q.question_text, qo.option_text
FROM application a
         JOIN question_response qr ON a.application_id = qr.application_id
         JOIN question q ON q.question_id = qr.question_id
         JOIN question_option qo ON qo.option_id = qr.option_id
WHERE a.application_id = 24
  AND a.match_form_id = 24;


