<?php
    include __DIR__ . '/../../db/pdo.php';
    if (!isset($_GET['id'])) {
        include '404.html';
        die;
    }

    // construct base url for distribution links
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
    $base_url = $protocol.$_SERVER['SERVER_NAME']."/application?id=".$_GET['id']."&m_type=";
?>

<html>
<head>
    <?php include __DIR__."/../components/head_content.php" ?>
    <link rel="stylesheet" href="/assets/css/view_applicants.css">
    <script src="./assets/js/view_applicants.js"></script>
    <title>Match Form Overview</title>
</head>
<body>
    <!--hidden elements to access data from JavaScript-->
    <input id="match-form-id" type="text" hidden value="<?php echo $_GET['id']?>">
    <?php include __DIR__.'/../components/navbar.php' ?>
    <div id="form" class="container mt-3 text center">
        <div class="row">
            <div class="col-6">
                <h4 id="project-title"></h4>
                <small id="project-date-created"></small>
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
        <div class="row justify-content-center quick-stats-row">
            <div class="col-12 text-center m-3">
                <h5 class="quick-stats-title">Quick Stats</h5>
            </div>
            <div class="col-3 text-center my-auto">
                <h5 id="project-mentor-count"></h5>
                <small>Mentors</small>
            </div>
            <div class="col-3 text-center my-auto">
                <h5 id="project-mentee-count"></h5>
                <small>Mentees</small>
            </div>
            <div class="col-3 text-center my-auto">
                <h5  id="project-ratio"></h5>
                <small>Mentee/Mentor Ratio</small>
            </div>
            <div class="col-3 text-center my-auto">
                <h5 id="project-num-matches"></h5>
                <small>Matches</small>
            </div>
        </div>
        <br>
        <div class="row table-row">
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
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Student ID</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody id="mentor-container">
                                <!-- data inserted via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="mentee-tab-content" role="tabpanel" aria-labelledby="mentee-tab-button">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">Application ID</th>
                                <th scope="col">Date</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Student ID</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody id="mentee-container">
                                <!-- data inserted via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade show" id="matches-tab-content" role="tabpanel" aria-labelledby="matches-tab-button">
                    <div class="table-responsive-xxl">
                        <table class="table table-hover">
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
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody id="matches-container">
                            <!-- data inserted via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- distribution modal -->
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
                            <label for="exampleFormControlInput1" class="form-label"><b>Mentor Application</b></label>
                            <input type="email" class="form-control" id="exampleFormControlInput1" value="<?php echo $base_url."mentor"?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label"><b>Mentee Application</b></label>
                            <input type="email" class="form-control" id="exampleFormControlInput1" value="<?php echo $base_url."mentee"?>" disabled>
                        </div>
                        <br>
                        <h5>Application Status</h5>
                        <p>If enabled, applicants will be able to view and submit the application form. If disabled, the application will be closed and applicants will receive a closed message.</p>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="mentor-app-status">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Accepting Mentor applications</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="mentee-app-status">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Accepting Mentee applications</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="close-app-status"type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button id="save-app-status" type="button" class="btn btn-success">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- applicant response modal -->
        <div class="modal fade" id="applicantResponseModal" tabindex="-1" aria-labelledby="applicantResponseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Question Responses</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="question-responses-container">
                            <!-- questions and responses inserted via JS -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>