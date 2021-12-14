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
    <?php include "components/navbar.php" ?>
<div id="form" class="container mt-3 text center">
    <div class="row">
        <div class="col-6">
            <h4>My Projects</h4>
        </div>
        <div class="col-6 text-end">
                <a href="/create" class="btn btn-success">
                    Create New
                </a>
        </div>
    </div>
</div>
<div class="container py-3">

        <?php
            foreach($rows as $row) {
                echo "<a href='/project?id={$row['match_form_id']}' style='text-decoration: none; color: black'>";
                echo '<div class="row py-3 mb-2" style="border-radius: 4px; border: 1px solid rgba(0, 0, 0, 0.125)">';
                echo '<div class="col-8 my-auto">';
                echo ('<h5>'.$row['title'].'</h5>');
                echo ('<small>'.$row['date_created'].'</small>');
                echo '</div>';
                echo '<div class="col-2 text-center my-auto">';
                echo '<h6>';
                echo $row['mentor_app_open'] == 0?'CLOSED':'OPEN';
                echo '</h6>';
                echo '<small>Mentor Application</small>';
                echo '</div>';
                echo '<div class="col-2 text-center my-auto">';
                echo '<h6>';
                echo $row['mentee_app_open'] == 0?'CLOSED':'OPEN';
                echo '</h6>';
                echo '<small>Mentee Application</small>';
                echo '</div>';
                echo '</div>';
                echo "</a>";
            }
        ?>
</div>
</body>
</html>