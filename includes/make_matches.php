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

        // construct base url for redirect
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
        $base_url = $protocol.$_SERVER['SERVER_NAME']."/?page=view_applicants&id=";

        header("Location:".$base_url.$_GET['id']);
        die;
    } catch (Exception $e) {
        // TODO: return error page
        include "404.html";
    }






