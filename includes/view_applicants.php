<?php
    include '../db/pdo.php';
    if (!isset($_GET['id'])) {
        include '404.html';
        die;
    }

    // if method is POST
        // check for id
        // check for action
        // switch for action type

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : "";

        switch($action) {
            case "make_matches":
                require_once __DIR__ . "/make_matches.php";  //TODO add __DIR__ to other relative includes
                die;
            case "delete_match_form":
                require_once "../includes/delete_match_form.php";
                die;
            case "get_applicant_info":
                require_once "../includes/get_applicant_data.php";
                die;
            default:
                echo json_encode("Bad Request");
                die;
                // send back error json
        };
    }

    // else if method is GET, do what is below

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
    $base_url = $protocol.$_SERVER['SERVER_NAME']."/application?id=".$_GET['id']."&m_type=";
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
    <script src="./assets/view_applicants.js"></script>
    <title>Match Form Overview</title>
</head>
<body>
    <!--hidden elements to access data from JavaScript-->
    <input id="match-form-id" type="text" hidden value="<?php echo $_GET['id']?>">
    <?php include "components/navbar.php" ?>
    <div id="form" class="container mt-3 text center">
        <div class="row">
            <div class="col-6">
                <h4><?php echo $match_form_data[0]['title']?></h4>
                <small><?php echo $match_form_data[0]['date_created']?></small>
            </div>
            <div class="col-6 text-end">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="/" role="button" class="btn btn-outline-primary py-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-return-left pt-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
                        </svg>
                    </a>
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li id="make-matches" ><a class="dropdown-item" href="#">Make Matches</a></li>
                            <li><a class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#distributionModal">Distribution</a></li>
                            <li id="delete-match-form" ><a class="dropdown-item" href="#">Delete Match Form</a></li>
                        </ul>
                    </div>
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
                <?php echo(count($mentee_data) && count($mentor_data)? ceil(count($mentee_data)/ count($mentor_data)).'/1' : 'N/A')?>
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
                            <table class="table table-hover">
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
                                <th scope="col"></th>
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
                                    echo '
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                                    <button type="button" class="btn btn-outline-secondary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                          <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                          <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        ';
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
                            <tbody id="match-data">
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