<?php
    try {
        $sql = "DELETE FROM match_form
                WHERE match_form_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':id' => $_GET['id']
        ));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);

    } catch (Exception $e) {
        echo json_encode($e);
    }