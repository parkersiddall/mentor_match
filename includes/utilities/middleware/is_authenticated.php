<?php
    if (!isset($_SESSION['USER'])) {
        header("Location: /login");
    }

    // TODO: check response type header, send back 403 with json
?>