<?php

    require_once('../vendor/autoload.php');

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/..");
    $dotenv->load();

    $connection_string = $_ENV['CONNECTION_STRING'];
    $user = $_ENV['MYSQL_USER'];
    $password = $_ENV['MYSQL_PASSWORD'];

    try {
        $pdo = new PDO($connection_string, $user, $password);
        // See the "errors" folder for details...
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "DB connected!";
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode($e);
        die;
    }

