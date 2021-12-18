<?php
    try {
        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=mentor_match', 'user', 'password');
        // See the "errors" folder for details...
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode("Internal server error.");
        die;
    }

