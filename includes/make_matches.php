<?php
    include '../db/pdo.php';
    include_once 'utilities/Matcher.php';

    if (!isset($_GET['id'])) {
        include '404.html';
        die;
    }

    try {
        $matcher = new matcher($_GET['id'], $pdo);
        $matcher->match_open_mentees();
        header("Location: localhost?page=view_applicants&id=".$_GET['id']);
        die;
    } catch (Exception $e) {
        // TODO: return error page
        include "404.html";
    }






