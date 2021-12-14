<?php
try {

    // TODO: insert new query below
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
        ':application_id' => $_POST['application_id']
    ));
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode($e);
}