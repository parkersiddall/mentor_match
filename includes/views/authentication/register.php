<?php
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../../..");
    $dotenv->load();

    $form_error_msg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // check that email is valid
        if (!str_contains($_POST['email'], '@')) {
            $form_error_msg = 'Invalid email';
        };

        // check that passwords match
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $form_error_msg = 'Passwords do not match.';
        }

        // check that email does not exist in DB
        $sql = "SELECT * FROM user u where u.email = :email;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':email' => htmlentities($_POST['email'])
        ));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($data) == 0) {
            // create account
            $sql = "INSERT INTO user (
                      email,
                      password_hash,
                      school_name,
                      school_city,
                      school_state,
                      school_country,
                      accept_terms,
                      approved) 
                      VALUES (
                        :email,
                        :password_hash,
                        :school_name,
                        :school_city,
                        :school_state,
                        :school_country,
                        :accept_terms,
                        :approved
                        );";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':email' => htmlentities($_POST['email']),
                ':password_hash' => hash('md5', $_POST['password'].$_ENV['SALT']),
                ':school_name' => htmlentities($_POST['school_name']),
                ':school_city' => htmlentities($_POST['school_city']),
                ':school_state' => htmlentities($_POST['school_state']),
                ':school_country' => htmlentities($_POST['school_country']),
                ':accept_terms' => True,
                ':approved' => True
            ));
            $user_id = $pdo->lastInsertId();

            // log user in
            $_SESSION['USER'] = $user_id;

            // redirect to home
            header("Location: /");
        } else {
            $form_error_msg = 'Unable to create user. Try using a different email address.';
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
    <script src="/assets/js/register.js"></script>
    <title>Mentor Match Application</title>
</head>
<body>
    <?php include __DIR__."/../../components/navbar.php" ?>
    <div id="form" class="container mt-3 text center">
        <h2>
            Getting started with Mentor Match
        </h2>
        <p>
            To create an account with Mentor Match, begin by filling out the form below.
        </p>
        <p>
            Our team will then review your registration and activate your account. You can expect to receive an email notification once your account has been activated. After that, you will be able to log in using the email and password provided during registration.
        </p>
        <hr>
        <?php
            if ($form_error_msg) {

                echo '<div class="alert alert-danger" role="alert">';
                echo $form_error_msg;
                echo '</div>';
            }
        ?>
        <form id="registration-form" method="POST">
            <div class="mb-3">
                <h4>User credentials</h4>
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <br>
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <br>
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <br>
            </div>
            <hr>
            <div class="mb-3">
                <h4>School information</h4>
                <label for="school_name" class="form-label">School Name</label>
                <input type="text" class="form-control" id="school_name" name="school_name" required>
                <br>
                <label for="school_city" class="form-label">School City</label>
                <input type="text" class="form-control" id="school_city" name="school_city" required>
                <br>
                <label for="school_state" class="form-label">School State</label>
                <select id="school_state" class="form-select" aria-label="School state" name="school_state" required>
                    <option value="" selected disabled>Select</option>
                    <?php
                    $states = json_decode(file_get_contents(__DIR__ . "/../../utilities/form_lists/states.json"), true);
                    foreach ($states as $state) {
                        echo '<option value='.$state.'>'.$state.'</option>';
                    }
                    ?>
                </select>
                <br>
                <label for="school_country" class="form-label">School Country</label>
                <select id="school_country" class="form-select" aria-label="School country" name="school_country" required>
                    <option value="" selected disabled>Select</option>
                    <?php
                    $countries = json_decode(file_get_contents(__DIR__ . "/../../utilities/form_lists/countries.json"), true);
                    foreach ($countries as $country) {
                        echo '<option value='.$country.'>'.$country.'</option>';
                    }
                    ?>
                </select>
                <br>
            </div>
            <hr>
            <div>
                <h4>Terms and conditions</h4>
                <p>By checking the box below, you declare that you have read and agree with the terms and conditions.</p>
                <input class="form-check-input" type="checkbox" value="" id="terms" name="terms" required>
                <label class="form-check-label" for="terms">
                    I accept the terms and conditions.
                </label>
            </div>
            <hr>
            <button class="btn btn-primary" type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
