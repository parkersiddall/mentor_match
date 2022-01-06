<?php
    include_once "../db/pdo.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // validate responses
        $response = Array();
        try {
            // TODO: validate that email includes @
            foreach ($_POST as $key => $value) {
                if ($key === 'questions') {
                    foreach($_POST['questions'] as $question) {
                        if ($question['questionId'] === '' || $question['optionId'] === '') {
                            throw new Exception("Responses are blank.", 400);
                        }
                    }
                } else if ($value === ''){
                    throw new Exception("$key cannot be blank.", 400);
                }
            }

            // add to DB
            $sql = "INSERT INTO application (match_form_id, m_type, first_name, last_name, email, phone,
                        stud_id) VALUES (:match_form_id, :m_type, :first_name, :last_name,
                        :email, :phone, :stud_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':match_form_id' => htmlentities($_POST['matchFormId']),
                ':m_type' => htmlentities($_POST['mType']),
                ':first_name' => isset($_POST['firstName']) ? htmlentities($_POST['firstName']) : null,
                ':last_name' => isset($_POST['lastName']) ? htmlentities($_POST['lastName']) : null,
                ':email' => isset($_POST['email']) ? htmlentities($_POST['email']) : null,
                ':phone' => isset($_POST['phone']) ? htmlentities($_POST['phone']) : null,
                ':stud_id' => isset($_POST['studentId']) ? htmlentities($_POST['studentId']) : null
            ));
            $application_id = $pdo->lastInsertId();

            foreach($_POST['questions'] as $question) {
                $sql = "INSERT INTO question_response (application_id, question_id, option_id) 
                        VALUES ( :application_id, :question_id, :option_id);";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ':application_id' => $application_id,
                    ':question_id' => htmlentities($question['questionId']),
                    ':option_id' => htmlentities($question['optionId'])
                ));
            }

            http_response_code(200);
            $response['message'] = "Application saved successfully!";

        } catch (Exception $e) {
            http_response_code($e->getCode());
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        die;
    }

    // else if GET method
    if (!isset($_GET['id']) || !isset($_GET['m_type'])) {
        include "404.html";
        die;
    }

    $m_types = array('mentor', 'mentee');
    if (!in_array($_GET['m_type'], $m_types)) {
        include "404.html";
        die;
    }

    // get form data
    $sql = "SELECT * FROM match_form WHERE match_form_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':id' => $_GET['id']
    ));
    $form_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$form_data) {
        include "404.html";
        die;

    } else {
        $m_type_string = $_GET['m_type'] == 'mentor' ? 'mentor_app_open' : 'mentee_app_open';
        if($form_data[0][$m_type_string] == 0) {
            include "application_closed.html";
            die;
        }

        // get questions and options
        $sql = "SELECT * FROM question WHERE match_form_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':id' => $_GET['id']
        ));
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $form_questions = array();
        foreach($questions as $question) {
            $question['options'] = array();

            $sql = "SELECT * FROM question_option WHERE question_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':id' => $question['question_id']
            ));
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($options as $option) {
                array_push($question['options'], $option);
            }
            array_push($form_questions, $question);
        }
    }

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
    <script src="/assets/application.js"></script>
    <title>Mentor Match Application</title>
</head>
<body>
    <?php include "../includes/components/navbar.php" ?>
    <div id="form" class="container mt-3 text center">
        <h2>
            <?php echo ($form_data[0]['title']);?>
        </h2>
        <p>
            <?php
                echo $_GET['m_type'] == 'mentor' ? ($form_data[0]['mentor_desc']) : ($form_data[0]['mentee_desc']);
            ?>
        </p>
        <br>
        <form id="app-form">
            <input id="match-form-id" value="<?php echo $_GET['id']?>" style="display: none"></input>
            <input id="m-type" value="<?php echo $_GET['m_type']?>" style="display: none"></input>
            <?php
                if($form_data[0]['collect_first_name']) {
                    echo '<div class="mb-3">';
                    echo '<label for="first-name" class="form-label">First Name</label>';
                    echo '<input type="text" class="form-control" id="first-name">';
                    echo '</div>';
                }
                if($form_data[0]['collect_last_name']) {
                    echo '<div class="mb-3">';
                    echo '<label for="last-name" class="form-label">Last Name</label>';
                    echo '<input type="text" class="form-control" id="last-name">';
                    echo '</div>';
                }
                if($form_data[0]['collect_email']) {
                    echo '<div class="mb-3">';
                    echo '<label for="email" class="form-label">Email Address</label>';
                    echo '<input type="email" class="form-control" id="email">';
                    echo '</div>';
                }
                if($form_data[0]['collect_phone']) {
                    echo '<div class="mb-3">';
                    echo '<label for="phone" class="form-label">Phone number</label>';
                    echo '<input type="text" class="form-control" id="phone">';
                    echo '</div>';
                }
                if($form_data[0]['collect_stud_id']) {
                    echo '<div class="mb-3">';
                    echo '<label for="student-id" class="form-label">Student Id</label>';
                    echo '<input type="text" class="form-control" id="student-id">';
                    echo '</div>';
                }

                foreach($form_questions as $question) {
                    echo '<div class="mb-3">';
                    echo "<label for='{$question['question_id']}' class='form-label'>{$question['question_text']}</label>";
                    echo "<select type='text' class='form-select question' id='{$question['question_id']}'>";
                    foreach($question['options'] as $option) {
                        echo "<option class='option' value='{$option['option_id']}'>{$option['option_text']}</option>";
                    }
                    echo '</select>';
                    echo '</div>';
                }
            ?>
            <br>
            <button class="btn btn-primary" type="submit">Submit</button>
        </form>
    </div>
    <div id="success-message" class="container h-100" style="display:none;">
        <h3>Success!</h3>
        <p>You've successfully submitted your application. Once the matching has been completed, you will receive information about your mentor/mentee from your university.</p>
    </div>

</body>
</html>






