<?php
    include '../db/pdo.php';
    include_once 'Matcher.php';

    if (!isset($_GET['id'])) {
        include '404.html';
        die;
    }

    $matcher = new matcher(24, $pdo);
    $matcher->set_ratio();
    $matcher->set_matching_params();
    $matcher->set_open_applications();

    foreach($matcher->open_mentees as $param) {
        foreach($param as $foo => $item){
            print_r($foo);
            print_r($item);
        }
    }
    echo "<br>";
    foreach($matcher->open_mentors as $param) {
        foreach($param as $foo => $item){
            print_r($foo);
            print_r($item);
        }
    }
