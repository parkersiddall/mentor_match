<?php
    include_once "../db/pdo.php";

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
        if($form_data[0]['mentor_app_open']) {
            // TODO create view for closed apps. Return it here.
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
    <title>Document</title>
</head>
<body>
    <div class="container mt-3 text center">
        <h2>
            <?php echo ($form_data[0]['title']);?>
        </h2>
        <p>
            <?php
                echo $_GET['m_type'] == 'mentor' ? ($form_data[0]['mentor_desc']) : ($form_data[0]['mentee_desc']);
            ?>
        </p>
        <br>
        <form action="">
            <?php
                if($form_data[0]['collect_first_name']) {
                    echo '<div class="mb-3">';
                    echo '<label for="firstName" class="form-label">First Name</label>';
                    echo '<input type="text" class="form-control" id="firstName">';
                    echo '</div>';
                }
                if($form_data[0]['collect_last_name']) {
                    echo '<div class="mb-3">';
                    echo '<label for="lastName" class="form-label">Last Name</label>';
                    echo '<input type="text" class="form-control" id="lastName">';
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
                    echo '<label for="studentId" class="form-label">Student Id</label>';
                    echo '<input type="text" class="form-control" id="studentId">';
                    echo '</div>';
                }

                foreach($form_questions as $question) {
                    echo '<div class="mb-3">';
                    echo "<label for='studentId' class='form-label'>{$question['question_text']}</label>";
                    echo "<select type='text' class='form-select question' id='{$question['question_id']}'>";
                    foreach($question['options'] as $option) {
                        echo "<option class='option' value='{$option['option_id']}'>{$option['option_text']}</option>";
                    }
                    echo '</select>';
                    echo '</div>';
                }
            ?>
        </form>
    </div>

</body>
</html>






