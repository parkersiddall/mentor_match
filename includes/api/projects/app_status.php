<?php

if (!isset($_GET['id'])) {
    http_response_code( "400");
    echo json_encode("Missing ID parameter.");
    die;
}

try {
    // PUT to modify project app statuses
    if($_SERVER["REQUEST_METHOD"] === "PUT") {
        // get json from request
        $json = file_get_contents('php://input');
        $request_data = json_decode($json, true);

        // validation
        $app_status_options = array("open", "closed");
        if (!is_bool($request_data['mentorApplicationStatus'])) {
            throw new Exception("Application status must true or false.", 400);
        }
        if (!is_bool($request_data['menteeApplicationStatus'])) {
            throw new Exception("Application status must be true or false.", 400);
        }

        // update DB
        $sql = "UPDATE match_form SET mentor_app_open=:mentor_app_open, mentee_app_open=:mentee_app_open where match_form_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':mentor_app_open' => $request_data['mentorApplicationStatus'] === true,
            ':mentee_app_open' => $request_data['menteeApplicationStatus'] === true,
            ':id' => htmlentities($_GET['id'])
        ));

        echo $json;
        die;
    }
} catch (Exception $e) {
    //TODO figure out how to handle PDO exceptions...
    http_response_code($e->getCode() ? $e->getCode() : "500");
    echo json_encode($e->getMessage() ? $e->getMessage() : "Internal Server Error");
    die;
}
?>