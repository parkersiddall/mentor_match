<?php
    include_once __DIR__.'/../db/pdo.php';
    session_start();

    // if user not authenticated, return landing page

    // router
    $request_uri = parse_url($_SERVER['REQUEST_URI']);
    switch($request_uri['path']) {

        // views
        case '/':
            require_once __DIR__.'/../includes/views/home.php';
            break;
        case '/create':
            require_once __DIR__ . '/../includes/views/add_new.php';
            break;
        case '/application':
            require_once __DIR__.'/./application.php';
            break;
        case '/project':
            require_once __DIR__.'/../includes/view_applicants.php';
            break;

        // api
        case '/api/projects':
            require_once __DIR__.'/../includes/api/projects.php';
            break;
        case '/api/applicants':
            require_once __DIR__.'/../includes/api/applicants.php';
            break;
        case '/api/responses':
            require_once __DIR__.'/../includes/api/responses.php';
            break;

        default:
            require_once __DIR__.'/./404.html';
            break;
    };
