<?php
try {
    // GET
    if($_SERVER["REQUEST_METHOD"] === "GET") {

        $data = null;

        if (isset($_GET['id'])) {
            $sql = "SELECT * FROM match_form mf where mf.match_form_id = :match_form_id;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':match_form_id' => htmlentities($_GET['id'])
            ));
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->query("SELECT * FROM match_form");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // send json back
        echo json_encode($data);
        die;
    }

    // POST
    if($_SERVER["REQUEST_METHOD"] == "POST") {

        // get json from request
        $json = file_get_contents('php://input');
        $request_data = json_decode($json, true);

        // validation
        foreach ($request_data as $key => $value) {
            if ($key === 'questions') {
                continue;
            } else if ($value === "") {
                throw new Exception("Please provide a valid $key", 400);
            }
        }
        if (!isset($request_data['title'])
            || !isset($request_data['mentorDescription'])
            || !isset($request_data['menteeDescription'])
            || !isset($request_data['questions'])
        ) {
            throw new Exception("Missing title, descriptions, and/or questions", 400);
        }

        foreach($request_data['questions'] as $question) {
            if ($question['question'] === '') {
                throw new Exception("Questions cannot be blank.", 400);
            }
            foreach($question['options'] as $option) {
                if ($option === '') {
                    throw new Exception("Options cannot be blank.", 400);
                }
            }
        }

        $app_status_options = array("open", "closed");
        if (!in_array(strtolower($request_data['mentorApplicationStatus']), $app_status_options)) {
            throw new Exception("Application status must be open or closed.", 400);
        }
        if (!in_array(strtolower($request_data['menteeApplicationStatus']), $app_status_options)) {
            throw new Exception("Application status must be open or closed.", 400);
        }

        if(!is_bool($request_data['collectFirstName']) || !is_bool($request_data['collectLastName'])
            || !is_bool($request_data['collectEmail']) || !is_bool($request_data['collectPhone'])
            || !is_bool($request_data['collectStudentID'])
            ) {
            throw new Exception("Collect parameters must be true or false", 400);
        }

        // persist data to DB
        $sql = "INSERT INTO match_form (title, mentor_desc, mentee_desc, mentor_app_open, mentee_app_open,
                    collect_first_name, collect_last_name, collect_email, collect_phone, collect_stud_id
                    ) VALUES (:title, :mentor_desc, :mentee_desc, :mentor_app_open, :mentee_app_open,
                      :collect_first_name, :collect_last_name, :collect_email, :collect_phone, :collect_stud_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':title' => htmlentities($request_data['title']),
            ':mentor_desc' => htmlentities($request_data['mentorDescription']),
            ':mentee_desc' => htmlentities($request_data['menteeDescription']),
            ':mentor_app_open' => $request_data['mentorApplicationStatus'] === true,
            ':mentee_app_open' => $request_data['menteeApplicationStatus'] === true,
            ':collect_first_name' => $request_data['collectFirstName'] === true,
            ':collect_last_name' => $request_data['collectLastName'] === true,
            ':collect_email' => $request_data['collectEmail'] === true,
            ':collect_phone' => $request_data['collectPhone'] === true,
            ':collect_stud_id' => $request_data['collectStudentID'] === true
        ));
        $match_form_id = $pdo->lastInsertId();

        // loop through questions and options save to db
        foreach($request_data['questions'] as $question) {
            $sql = "INSERT INTO question (match_form_id, priority, question_text) VALUES ( :match_form_id, :priority, :question_text);";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':match_form_id' => htmlentities($match_form_id),
                ':priority' => htmlentities($question['priority']),
                ':question_text' => htmlentities($question['question'])
            ));
            $question_id = $pdo->lastInsertId();

            foreach($question['options'] as $option) {
                $sql = "INSERT INTO question_option (question_id, option_text) VALUES ( :question_id, :option_text);";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ':question_id' => htmlentities($question_id),
                    ':option_text' => htmlentities($option)
                ));
            }
        }

        // get match form and send it back
        $sql = "SELECT * FROM match_form mf where mf.match_form_id = :match_form_id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':match_form_id' => $match_form_id)
        );
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(201);
        echo json_encode($data);
        die;
    }

    // DELETE
    if($_SERVER["REQUEST_METHOD"] === "DELETE") {

        if (!isset($_GET['id'])) {
            http_response_code( "400");
            echo json_encode("Missing ID parameter.");
            die;
        }

        $data = null;

        $sql = "DELETE FROM match_form
                WHERE match_form_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':id' => $_GET['id']
        ));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // send json back
        http_response_code( "204");
        die;
    }
} catch (Exception $e) {
    //TODO figure out how to handle PDO exceptions...
    http_response_code($e->getCode() ? $e->getCode() : "500");
    echo json_encode($e->getMessage() ? $e->getMessage() : "Internal Server Error");
    die;
}
?>