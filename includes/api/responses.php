<?php

if ((!isset($_GET['id'])) || (!isset($_GET['application_id']))) {
    http_response_code( "400");
    echo json_encode("Missing id and/or application_id parameters.");
    die;
}
try {
    // GET
    if($_SERVER["REQUEST_METHOD"] === "GET") {

        $data = null;

        $sql = "SELECT a.m_type, a.first_name, a.last_name, q.question_text, qo.option_text
            FROM application a
                     JOIN question_response qr ON a.application_id = qr.application_id
                     JOIN question q ON q.question_id = qr.question_id
                     JOIN question_option qo ON qo.option_id = qr.option_id
            WHERE a.application_id = :application_id
            AND a.match_form_id = :match_form_id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':match_form_id' => $_GET['id'],
            ':application_id' => $_GET['application_id']
        ));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // send json back
        echo json_encode($data);
        die;
    }

    // POST
    if($_SERVER["REQUEST_METHOD"] == "POST") {

    }
} catch (Exception $e) {
    echo $e;
    //TODO figure out how to handle PDO exceptions...
    http_response_code($e->getCode() ? $e->getCode() : 500);
    echo json_encode($e->getMessage() ? $e->getMessage() : "Internal Server Error");
    die;
}
?>