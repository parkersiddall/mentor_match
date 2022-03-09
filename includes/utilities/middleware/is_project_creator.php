<?php
    // pull project with id from db
    if (isset($_GET['id'])) {
        $sql = "SELECT * FROM match_form mf where mf.match_form_id = :match_form_id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':match_form_id' => htmlentities($_GET['id'])
        ));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // check owner
        if ($data[0]['creator'] !== $_SESSION['USER']) {
            header("Location: /login");
        }
    }


    // TODO: check response type header, send back 403 with json
?>




