<?php
try {
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
    }
} catch (Exception $e) {
    echo $e;
}
?>