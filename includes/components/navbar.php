<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1"><a href="/" style="text-decoration: none; color: white">Mentor Match</a></span>
        <?php
            if (isset($_SESSION['USER'])){
                echo '<div class="d-flex">';
                echo '<a href="/logout">';
                echo '<button type="submit" class="btn btn-sm btn-outline-light">Logout</button>';
                echo '</a>';
                echo '</div>';
            }
        ?>



    </div>

</nav>