<?php
    include_once "../db/pdo.php";

    $stmt = $pdo->query("SELECT * FROM match_form");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <title>Mentor Match</title>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Mentor Match</span>
    </div>
</nav>
<div class="container">
    <div class="row py-3">
        <div class="d-flex py-2 justify-content-between">
            <h5>My Matches</h5>
            <a href="?page=add_new" class="btn btn-success">
                Create New
            </a>
        </div>
    </div>



        <?php
            foreach($rows as $row) {
                echo '<div class="row py-3 mb-1" style="border-radius: 4px; border: 1px solid rgba(0, 0, 0, 0.125)">';
                echo '<div class="col-6 my-auto">';
                echo ('<h5>'.$row['title'].'</h5>');
                echo ('<small>'.$row['date_created'].'</small>');
                echo '</div>';
                echo '<div class="col-2 text-center my-auto">';
                echo '<h5>#</h5>';
                echo '<small>Mentors</small>';
                echo '</div>';
                echo '<div class="col-2 text-center my-auto">';
                echo '<h5>#</h5>';
                echo '<small>Mentees</small>';
                echo '</div>';
                echo '<div class="col-2 text-end my-auto">';
                echo '<div class="dropdown">';
                echo '<button class="btn btn-lg btn-outline-secondary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">';
                echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16">';
                echo '<path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>';
                echo '</svg>';
                echo '</button>';
                echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
                echo '<li><a class="dropdown-item" href="#">Action</a></li>';
                echo '<li><a class="dropdown-item" href="#">Another action</a></li>';
                echo '<li><a class="dropdown-item" href="#">Something else here</a></li>';
                echo '</ul>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        ?>
</div>
</body>
</html>