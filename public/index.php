<?php
    include_once __DIR__.'/../db/pdo.php';

    session_start();

    // router
    $request_uri = parse_url($_SERVER['REQUEST_URI']);
    switch($request_uri['path']) {

        // registration and authentication
        case '/register':
            require_once __DIR__ . '/../includes/utilities/middleware/is_not_authenticated.php';
            require_once __DIR__ . '/../includes/views/authentication/register.php';
            break;
        case '/login':
            require_once __DIR__ . '/../includes/utilities/middleware/is_not_authenticated.php';
            require_once __DIR__ . '/../includes/views/authentication/login.php';
            break;

        // views
        case '/':
            if (isset($_SESSION['USER'])){
                require_once __DIR__.'/../includes/views/home.php';
            } else {
                require_once __DIR__.'/./landing_page.html';
            }
            break;
        case '/create':
            require_once __DIR__ . '/../includes/utilities/middleware/is_authenticated.php';
            require_once __DIR__ . '/../includes/views/add_new.php';
            break;
        case '/application':
            require_once __DIR__.'/./application.php';
            break;
        case '/project':
            require_once __DIR__ . '/../includes/utilities/middleware/is_authenticated.php';
            require_once __DIR__ . '/../includes/views/view_applicants.php';
            break;

        // api
        case '/api/projects':
            require_once __DIR__ . '/../includes/utilities/middleware/is_authenticated.php';
            require_once __DIR__.'/../includes/api/projects/projects.php';
            break;
        case '/api/projects/app_status':
            require_once __DIR__ . '/../includes/utilities/middleware/is_authenticated.php';
            require_once __DIR__.'/../includes/api/projects/app_status.php';
            break;
        case '/api/applicants':
            require_once __DIR__ . '/../includes/utilities/middleware/is_authenticated.php';
            require_once __DIR__.'/../includes/api/applicants.php';
            break;
        case '/api/responses':
            require_once __DIR__ . '/../includes/utilities/middleware/is_authenticated.php';
            require_once __DIR__.'/../includes/api/responses.php';
            break;
        case '/api/matches':
            require_once __DIR__ . '/../includes/utilities/middleware/is_authenticated.php';
            require_once __DIR__.'/../includes/api/matches.php';
            break;


        default:
            require_once __DIR__.'/./404.html';
            break;
    };
