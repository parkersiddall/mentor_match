<?php
    include_once 'utilities/Matcher.php';

    try {
        // make new matchs
        $matcher = new matcher($_GET['id'], $pdo);
        $matcher->match_open_mentees();

        // get matches, return them as JSON
        $sql = "SELECT mp.match_id, mp.date_created, mp.confidence_rate, a.application_id AS 'mentee_application_id',
            a.first_name AS 'mentee_first_name', a.last_name AS 'mentee_last_name', a.email AS 'mentee_email',
            a2.application_id AS 'mentor_application_id', a2.first_name AS 'mentor_first_name', a2.last_name AS 'mentor_last_name', a2.email AS 'mentor_email'
            FROM match_pair mp
            JOIN application a ON mp.mentee_application_id = a.application_id
            JOIN application a2 ON mp.mentor_application_id = a2.application_id
            WHERE mp.match_form_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':id' => $_GET['id']
        ));
        $matches_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($matches_data);

    } catch (Exception $e) {
        http_response_code($e->getCode());
        echo json_encode($e->getMessage());
    }






