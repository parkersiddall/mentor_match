<?php

if (!isset($_GET['id'])) {
    http_response_code( "400");
    echo json_encode("Missing ID parameter.");
    die;
}
try {
    // GET
    if($_SERVER["REQUEST_METHOD"] === "GET") {

        $data = null;

        $sql = "SELECT * FROM application a where a.match_form_id = :match_form_id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':match_form_id' => htmlentities($_GET['id'])
        ));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //TODO: add in if statements for m_type or app id and filter data

        // send json back
        echo json_encode($data);
        die;
    }

    // POST
    if($_SERVER["REQUEST_METHOD"] == "POST") {

    }
} catch (Exception $e) {
    //TODO figure out how to handle PDO exceptions...
    http_response_code($e->getCode() ? $e->getCode() : "500");
    echo json_encode($e->getMessage() ? $e->getMessage() : "Internal Server Error");
    die;
}
?>