<?php
    include '../db/pdo.php';

    if (!isset($_GET['id'])) {
        include '404.html';
        die;
    }

    // collect info from database
    // match form data
    $sql = "SELECT * FROM match_form WHERE match_form_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':id' => $_GET['id']
    ));
    $match_form_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // get applicant data
    $sql = "SELECT * FROM application WHERE match_form_id = :id AND m_type = 'mentor'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':id' => $_GET['id']
    ));
    $mentor_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM application WHERE match_form_id = :id AND m_type = 'mentee'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':id' => $_GET['id']
    ));
    $mentee_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM application WHERE match_form_id = :id ORDER BY 'date_created' DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':id' => $_GET['id']
    ));
    $last_app = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // get matches
    $sql = "SELECT mp.match_id, mp.date_created, mp.confidence_rate, a.application_id AS 'mentee_application_id',
            a.first_name AS 'mentee_first_name', a.last_name AS 'mentee_last_name', a.email AS 'mentee_email',
            a2.application_id AS 'mentor_application_id', a2.first_name AS 'mentor_first_name', a2.last_name AS 'mentor_last_name', a2.email AS 'mentor_email'
            FROM match_pair mp
            JOIN application a ON mp.mentee_application_id = a.application_id
            JOIN application a2 ON mp.mentor_application_id = a2.application_id
            WHERE mp.match_form_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':id' => $_GET['id']
    ));
    $matches_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // construct base url for distribution links
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
    $base_url = $protocol.$_SERVER['SERVER_NAME']."/application.php/?id=".$_GET['id']."&m_type=";
?>


<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script
            src="https://code.jquery.com/jquery-3.6.0.js"
            integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
            crossorigin="anonymous"></script>
    <title>Match Form Overview</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Mentor Match</span>
        </div>
    </nav>
    <div id="form" class="container mt-3 text center">
        <div class="row">
            <div class="col-6">
                <h4><?php echo $match_form_data[0]['title']?></h4>
                <small><?php echo $match_form_data[0]['date_created']?></small>
            </div>
            <div class="col-6 text-end">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown button
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="<?php echo '?page=make_matches&id='.$_GET['id']?>">Make Matches</a></li>
                        <li><a class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#distributionModal">Distribution</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row justify-content-center py-3">
            <div class="col-12 text-center m-3">
                <h5>Quick Stats</h5>
            </div>
            <div class="col-2 text-center my-auto">
                <h5><?php echo count($mentor_data)?></h5>
                <small>Mentors</small>
            </div>
            <div class="col-2 text-center my-auto">
                <h5><?php echo count($mentee_data)?></h5>
                <small>Mentees</small>
            </div>
            <div class="col-2 text-center my-auto">
                <h5>
                <?php echo(count($mentee_data) && count($mentor_data)? count($mentee_data) / count($mentor_data).'/1' : 'N/A')?>
                </h5>
                <small>Mentee/Mentor Ratio</small>
            </div>
            <div class="col-2 text-center my-auto">
                <h5><?php echo($last_app? $last_app[0]['date_created'] : 'N/A')?></h5>
                <small>Last application</small>
            </div>
        </div>
        <br>
        <div class="row">
            <ul class="nav nav-tabs mb-3 nav-fill" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="mentors-tab-button" data-bs-toggle="pill" data-bs-target="#mentors-tab-content" type="button" role="tab" aria-controls="mentors-tab" aria-selected="true">Mentors</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mentee-tab-button" data-bs-toggle="pill" data-bs-target="#mentee-tab-content" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Mentees</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="matches-tab-button" data-bs-toggle="pill" data-bs-target="#matches-tab-content" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Matches</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="mentors-tab-content" role="tabpanel" aria-labelledby="mentors-tab-button">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                            <tr>
                                <th scope="col">Application ID</th>
                                <th scope="col">Date</th>
                                <?php
                                    if ($match_form_data[0]['collect_first_name']){
                                        echo '<th scope="col">First Name</th>';
                                    }
                                    if ($match_form_data[0]['collect_last_name']){
                                        echo '<th scope="col">Last Name</th>';
                                    }
                                    if ($match_form_data[0]['collect_email']){
                                        echo '<th scope="col">Email</th>';
                                    }
                                    if ($match_form_data[0]['collect_phone']){
                                        echo '<th scope="col">Phone</th>';
                                    }
                                    if ($match_form_data[0]['collect_stud_id']){
                                        echo '<th scope="col">Student ID</th>';
                                    }
                                ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach($mentor_data as $mentor) {
                                    echo '<tr>';
                                    echo "<td>{$mentor['application_id']}</td>";
                                    echo "<td>{$mentor['date_created']}</td>";

                                    if ($match_form_data[0]['collect_first_name']){
                                        echo "<td>{$mentor['first_name']}</td>";
                                    }
                                    if ($match_form_data[0]['collect_last_name']){
                                        echo "<td>{$mentor['last_name']}</td>";
                                    }
                                    if ($match_form_data[0]['collect_email']){
                                        echo "<td>{$mentor['email']}</td>";
                                    }
                                    if ($match_form_data[0]['collect_phone']){
                                        echo "<td>{$mentor['phone']}</td>";
                                    }
                                    if ($match_form_data[0]['collect_stud_id']){
                                        echo "<td>{$mentor['stud_id']}</td>";
                                    }

                                    echo '</tr>';
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="mentee-tab-content" role="tabpanel" aria-labelledby="mentee-tab-button">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Application ID</th>
                                <th scope="col">Date</th>
                                <?php
                                if ($match_form_data[0]['collect_first_name']){
                                    echo '<th scope="col">First Name</th>';
                                }
                                if ($match_form_data[0]['collect_last_name']){
                                    echo '<th scope="col">Last Name</th>';
                                }
                                if ($match_form_data[0]['collect_email']){
                                    echo '<th scope="col">Email</th>';
                                }
                                if ($match_form_data[0]['collect_phone']){
                                    echo '<th scope="col">Phone</th>';
                                }
                                if ($match_form_data[0]['collect_stud_id']){
                                    echo '<th scope="col">Student ID</th>';
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($mentee_data as $mentee) {
                                echo '<tr>';
                                echo "<td>{$mentee['application_id']}</td>";
                                echo "<td>{$mentee['date_created']}</td>";

                                if ($match_form_data[0]['collect_first_name']){
                                    echo "<td>{$mentee['first_name']}</td>";
                                }
                                if ($match_form_data[0]['collect_last_name']){
                                    echo "<td>{$mentee['last_name']}</td>";
                                }
                                if ($match_form_data[0]['collect_email']){
                                    echo "<td>{$mentee['email']}</td>";
                                }
                                if ($match_form_data[0]['collect_phone']){
                                    echo "<td>{$mentee['phone']}</td>";
                                }
                                if ($match_form_data[0]['collect_stud_id']){
                                    echo "<td>{$mentee['stud_id']}</td>";
                                }

                                echo '</tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade show" id="matches-tab-content" role="tabpanel" aria-labelledby="matches-tab-button">
                    <div class="table-responsive-xxl">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Match ID</th>
                                <th scope="col">Date Matched</th>
                                <th scope="col">Confidence Rate</th>
                                <th scope="col">Mentee Application ID</th>
                                <th scope="col">Mentee First Name</th>
                                <th scope="col">Mentee Last Name</th>
                                <th scope="col">Mentee Email</th>
                                <th scope="col">Mentor Application ID</th>
                                <th scope="col">Mentor First Name</th>
                                <th scope="col">Mentor Last Name</th>
                                <th scope="col">Mentor Email</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($matches_data as $match) {
                                echo '<tr>';
                                echo "<td>{$match['match_id']}</td>";
                                echo "<td>{$match['date_created']}</td>";
                                echo "<td>".round($match['confidence_rate'] * 100 ).'%'."</td>";
                                echo "<td>{$match['mentee_application_id']}</td>";
                                echo "<td>{$match['mentee_first_name']}</td>";
                                echo "<td>{$match['mentee_last_name']}</td>";
                                echo "<td>{$match['mentee_email']}</td>";
                                echo "<td>{$match['mentor_application_id']}</td>";
                                echo "<td>{$match['mentor_first_name']}</td>";
                                echo "<td>{$match['mentor_last_name']}</td>";
                                echo "<td>{$match['mentor_email']}</td>";
                                echo '</tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="distributionModal" tabindex="-1" aria-labelledby="distributionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Distribution</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>Application Links</h5>
                        <p>Distribute these links to those who need to apply.</p>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Mentor Application</label>
                            <input type="email" class="form-control" id="exampleFormControlInput1" value="<?php echo $base_url."mentor"?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Mentee Application</label>
                            <input type="email" class="form-control" id="exampleFormControlInput1" value="<?php echo $base_url."mentee"?>" disabled>
                        </div>
                        <br>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>