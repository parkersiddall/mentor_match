<?php
    session_start();

    // controller
    $request_uri = parse_url($_SERVER['REQUEST_URI']);
    switch($request_uri["path"]) {
        case '/':
            require_once '../includes/home.php';
            break;
        case '/create':
            require_once '../includes/add_new.php';
            break;
        case '/application':
            require_once './application.php';
            break;
        case '/project':
            require_once '../includes/view_applicants.php';
            break;
        default:
            require_once './404.html';
            break;
    };
