<?php
    include '../db/pdo.php';
    include_once 'Matcher.php';

    if (!isset($_GET['id'])) {
        include '404.html';
        die;
    }

    try {
        $matcher = new matcher(24, $pdo);
        $matcher->match_open_mentees();
        header("Location: localhost?page=view_applicants&id=24");
        die;
    } catch (Exception $e) {
        // TODO: return error page
        include "404.html";
    }






