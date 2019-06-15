<?php

	// Midiel: You meed the config/config.php file for this narvbar to function
    require_once('config/config.php');

	require_once('includes/connect.inc.php');
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="<?php echo ROOT_URL; ?>gridview.php">GeekText</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarColor01" bis_skin_checked="1">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="<?php echo ROOT_URL; ?>gridview.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="text" placeholder="Search">
                <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
            </form>
            
        </ul>

        <ul class="navbar-nav ml-auto">
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo ROOT_URL; ?>register.php">Register</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo ROOT_URL; ?>login.php">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo ROOT_URL; ?>cart.php">Cart</a>
            </li>

        </ul>
        
    </div>
</nav>