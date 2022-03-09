<?php
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../../..");
    $dotenv->load();

    $form_error_msg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // check that user exists
        $sql = "SELECT * FROM app_user u where u.email = :email;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':email' => htmlentities($_POST['email'])
        ));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($data) == 0) {
            // user does not exist
            $form_error_msg = 'The inserted email and/or password is incorrect.';
        }

        if (count($data) == 1) {
            // check that password is correct
            $saved_hashed_password = $data[0]['password_hash'];
            echo $saved_hashed_password;
            $hashed_user_input = hash('md5', $_POST['password'].$_ENV['SALT']);

            if ($hashed_user_input === $saved_hashed_password) {
                // log user in and redirect home
                $_SESSION['USER'] = $data[0]['user_id'];
                header("Location: /");
            } else {
                $form_error_msg = 'The inserted email and/or password is incorrect.';
            }
        }
    }
?>

<html>
<head>
    <?php include __DIR__."/../../components/head_content.php" ?>
    <!-- <script src="/assets/js/register.js"></script> -->
    <title>Mentor Match Application</title>
</head>
<body>
<?php include __DIR__."/../../components/navbar.php" ?>
<div id="form" class="container mt-3 text center">
    <h2>
        Login
    </h2>
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
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <br>
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required
        </div>
        <hr>
        <button class="btn btn-primary" type="submit">Submit</button>
        <br>
        <br>
        <div class="text-center">
            <p>Don't have an account? <a href="/register">Sign up</a> for free.</p>
        </div>

    </form>
</div>
</body>
</html>