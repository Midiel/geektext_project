<?php

	// Midiel: You meed the config/config.php file for this narvbar to function
  require_once('config/config.php');

	require_once('includes/connect.inc.php');

  /* check if session has already started */
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  //checking if there is a user logged in
  $email = $_SESSION['email'];
  $token = $_SESSION['token'];
  $user_id = $_SESSION['user_id'];


if (isset($token) && !empty($token))
{
    $query = "SELECT token FROM user WHERE email = '$email' LIMIT 1";
    $run = mysqli_query($con, $query);
    while($row = mysqli_fetch_assoc($run)){
      if ($row['token'] == $token)
          $logged_in = true;
      }
  }

  //displaying login info in html
  if($logged_in == true)
  {
    $glyphicon_log_in = '<li class="nav-item"><a class="nav-link" href="login.php"  ><span class="fa fa-sign-out"></span> Log out </a></li>';
  }
  else
  {
    $email = '';
    $password = '';
    $token = '';
    session_destroy();
    $glyphicon_log_in =
      '<li class="nav-item"><a class="nav-link" href="#"  onclick="event.preventDefault();"><span class="fa fa-new-window"></span> Sign Up </a></li>
      <li class="nav-item"><a class="nav-link" href="login.php"><span class="fa fa-plus"></span> Log in </a></li>';
  }




?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="index.php">GeekText</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarColor01" bis_skin_checked="1">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="text" placeholder="Search">
                <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
            </form>

        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link active" href="?new_arrivals=true" >New Arrivals</a> </li>
            <li class="nav-item"><a class="nav-link" href="?top_sellers=true">Top Sellers</a> </li>
            <li class="nav-item">
              <div class="dropdown">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                  Sorth by
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="?sorth_by=tittle">Tittle</a>
                  <a class="dropdown-item" href="?sorth_by=price">Price</a>
                  <a class="dropdown-item" href="?sorth_by=release_date">Release Date</a>
                </div>
              </div>
            </li>

        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="#"  onclick="event.preventDefault();"><span class="fa fa-user"></span> <?php echo $email;?> </a> </li>
            <?php echo $glyphicon_log_in;?>
            <li class="nav-item"><a class="nav-link" href="#" onclick="event.preventDefault();"><span class="fa fa-shopping-cart"> <?php echo $items_in_cart;?> </span></a> </li>

        </ul>

    </div>
</nav>
