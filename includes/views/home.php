<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/spinner.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script
            src="https://code.jquery.com/jquery-3.6.0.js"
            integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
            crossorigin="anonymous"></script>
    <script src="/assets/home.js"></script>
    <title>Mentor Match</title>
</head>
<body>
<?php include __DIR__."/../components/navbar.php" ?>
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
<div id="project-container" class="container py-3">
    <!--projects inserted dynamically via JS-->
    <div id="loading-icon" class="row justify-content-center p-5 m-5">
        <div class="spinner-border text-secondary p-5" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>
</body>
</html>