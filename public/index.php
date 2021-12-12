<?php
    session_start();

    // controller
    $page = isset($_GET['page']) ? $_GET['page'] : "";

    switch($page) {
        case "add_new":
            require_once "../includes/add_new.php";
            break;
        case "view_applicants":
            require_once "../includes/view_applicants.php";
            break;
        default:
            require_once "../includes/home.php";
            break;
    };
