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

        if(isset($_GET['application_id'])) {
            $sql = "SELECT * FROM application a where a.match_form_id = :match_form_id
                    AND a.application_id = :application_id;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':match_form_id' => htmlentities($_GET['id']),
                ':application_id' => htmlentities($_GET['application_id'])
            ));
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif (isset($_GET['m_type'])) {
            // TODO validate m_type
            $sql = "SELECT * FROM application a where a.match_form_id = :match_form_id
                    AND a.m_type = :m_type;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':match_form_id' => htmlentities($_GET['id']),
                ':m_type' => htmlentities($_GET['m_type'])
            ));
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT * FROM application a where a.match_form_id = :match_form_id;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':match_form_id' => htmlentities($_GET['id'])
            ));
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

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