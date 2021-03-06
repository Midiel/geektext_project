<?php
  /* NOTE: Include "navbar_libs.php" in <head> for styles to properly apply. */

  require_once('connect.inc.php');

  /* check if session has already started */
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $logged_in = false;
  }

  $one_star = set_star(1);
  $two_stars = set_star(2);
  $three_stars = set_star(3);
  $four_stars = set_star(4);
  $five_stars = set_star(5);
  $logged_in = false;

  if(isset($_SESSION['token'])) {
    $email = $_SESSION['email'];
    $token = $_SESSION['token'];
    $user_id = $_SESSION['user_id'];
    $nav_nickname = $_SESSION['nickname'];
    $logged_in = true;
  }

//Keeping sorting values visible after reloead
$sort_display = 'Sort by';
if (isset($_GET['sort_by']) && !empty($_GET['sort_by']))
{
  $sort_display = $_GET['sort_by'];

  if ($sort_display == 'title'){
    $sort_display =  'Sorted by Title';
  } else if ($sort_display == 'authors'){
    $sort_display =  'Sorted by Author';
  } else if ($sort_display == 'price'){
    $sort_display =  'Sorted by Price';
  } else if ($sort_display == 'published_date'){
    $sort_display =  'Sorted by Release Date';
  } else if ($sort_display == 'average_rating'){
    $sort_display =  'Sorted by Rating in Ascending Order';
  } else if ($sort_display == 'average_rating_des'){
    $sort_display =  'Sorted by Rating in Descending Order';
  }

}



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
      '<li class="nav-item"><a class="nav-link" href="register.php"><span class="fa fa-new-window"></span> Sign Up </a></li>
      <li class="nav-item"><a class="nav-link" href="login.php"><span class="fa fa-plus"></span> Log in </a></li>';
  }

  $on_homepage = !empty($path); // if '$path' exists, we are on homepage (index.php)
  if ($on_homepage)
  {
    //appending or replacing path
    if(strpos($path,"?") > 0)
    {
      $path .= "&";
    }
    else
    {
      $path .= "?";
    }

    //setting as active top sellers or New arrivals
    if(strpos($path,"top_sellers=true") > 0)
    {
      $top_seller_link = '<li class="nav-item"><a class="nav-link active" href="?new_arrivals=true" >See our new arrivals</a> </li>';
    }
    else
    {
      $top_seller_link = '<li class="nav-item"><a class="nav-link active" href="?top_sellers=true">See our top sellers</a> </li>';
    }
  }
  else // do not add sorting to navbar
  {
    // done to prevent errors
    $path = "";
    $top_seller_link = "";
  }

  if (empty($items_in_cart)) { $items_in_cart = ""; }

  $browse_by_rating_display = "Browse by Rating";
  if (isset($_GET['browse_by_rating']) && !empty($_GET['browse_by_rating']))
  {
    $browse_by_rating_display = $_GET['browse_by_rating'];
    $direction_display = $_GET['direction'];

    if ($browse_by_rating_display == 1 && $direction_display == 'up' ){
      $browse_by_rating_display =  set_star(1).' or up';
    } else if ($browse_by_rating_display == 2 && $direction_display == 'up' ){
      $browse_by_rating_display =  set_star(2).' or up';
    } else if ($browse_by_rating_display == 3 && $direction_display == 'up' ){
      $browse_by_rating_display =  set_star(3).' or up';
    } else if ($browse_by_rating_display == 4 && $direction_display == 'up' ){
      $browse_by_rating_display =  set_star(4).' or up';
    } else if ($browse_by_rating_display == 5 && $direction_display == 'up' ){
      $browse_by_rating_display =  set_star(5);
    } else if ($browse_by_rating_display == 4 && $direction_display == 'down' ){
      $browse_by_rating_display =  set_star(4).' or less';
    }else if ($browse_by_rating_display == 3 && $direction_display == 'down' ){
      $browse_by_rating_display =  set_star(3).' or less';
    }else if ($browse_by_rating_display == 2 && $direction_display == 'down' ){
      $browse_by_rating_display =  set_star(2).' or less';
    }else if ($browse_by_rating_display == 1 && $direction_display == 'down' ){
      $browse_by_rating_display =  set_star(1);
    }

  }

  function set_star($start_double)
  {
    $result = '';
    for($i = 0; $i < 5; $i++)
    {
      if($i <= ($start_double - 1))
      {
        $result .= '<i class="fa fa-star" style="color:black;"></i>';
      }
      else if($start_double > $i)
      {
        $result .= '<i class="fa fa-star-half-full"></i>';
      } else
      {
        $result .= '<i class="fa fa-star-o" style="color:black;"></i>';
      }

    }

    return $result;
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
      <form class="form-inline my-2 my-lg-0" method="GET" >
        <input list="search_list" class="form-control mr-sm-2" id="search" name="search" placeholder="Search">
        <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
        <datalist id="search_list" name="search2">
          <option id="op1">
          <option id="op2">
          <option id="op3">
          <option id="op4">
          <option id="op5">
          <option id="op6">
        </datalist>
      </form>

    </ul>
    <ul class="navbar-nav ml-auto" style="display: <?php echo $on_homepage ? "flex" : "none";?>">
      <li class="nav-item">
        <div class="dropdown">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
            <?php echo $browse_by_rating_display; ?>
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="<?php echo $path.'browse_by_rating=1&direction=up';?>"><?php echo $one_star;?> or up</a>
            <a class="dropdown-item" href="<?php echo $path.'browse_by_rating=2&direction=up';?>"><?php echo $two_stars;?> or up</a>
            <a class="dropdown-item" href="<?php echo $path.'browse_by_rating=3&direction=up';?>"><?php echo $three_stars;?> or up</a>
            <a class="dropdown-item" href="<?php echo $path.'browse_by_rating=4&direction=up';?>"><?php echo $four_stars;?> or up</a>
            <a class="dropdown-item" href="<?php echo $path.'browse_by_rating=5&direction=up';?>"><?php echo $five_stars;?></a>
            <a class="dropdown-item" href="<?php echo $path.'browse_by_rating=4&direction=down';?>"><?php echo $four_stars;?> or less</a>
            <a class="dropdown-item" href="<?php echo $path.'browse_by_rating=3&direction=down';?>"><?php echo $three_stars;?> or less</a>
            <a class="dropdown-item" href="<?php echo $path.'browse_by_rating=2&direction=down';?>"><?php echo $two_stars;?> or less</a>
            <a class="dropdown-item" href="<?php echo $path.'browse_by_rating=1&direction=down';?>"><?php echo $one_star;?></a>
          </div>
        </div>
      </li>

    </ul>
    <ul class="navbar-nav ml-auto" style="display: <?php echo $on_homepage ? "flex" : "none";?>">
      <?php echo $top_seller_link;?>
    </ul>
    <ul class="navbar-nav ml-auto" style="display: <?php echo $on_homepage ? "flex" : "none";?>">
      <li class="nav-item">
        <div class="dropdown">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
            <?php echo $sort_display; ?>
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="<?php echo $path.'sort_by=title';?>">Title</a>
            <a class="dropdown-item" href="<?php echo $path.'sort_by=authors';?>">Author</a>
            <a class="dropdown-item" href="<?php echo $path.'sort_by=price';?>">Price</a>
            <a class="dropdown-item" href="<?php echo $path.'sort_by=published_date';?>">Release Date</a>
            <a class="dropdown-item" href="<?php echo $path.'sort_by=average_rating';?>">Rating in Ascending Order</a>
            <a class="dropdown-item" href="<?php echo $path.'sort_by=average_rating_des';?>">Rating in Descending Order</a>
          </div>
        </div>
      </li>

    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <span class="nav-link active" style="padding-right: 24px;">
          <?php echo (empty($nav_nickname) ? "" : "Hi, " . $nav_nickname . "!"); ?>
        </span>
      </li>
      <li class="nav-item">
      <?php
      if ($logged_in)
      {
        echo '<a class="nav-link" href="account.php"><span class="fa fa-user"></span> My Account</a>';
      }
      ?>
      </li>
      <?php echo $glyphicon_log_in;?>
      <li class="nav-item" id="nav-counter">
        <a class="nav-link disabled" href="cart.php">
          <span class="fa fa-shopping-cart"></span>
        </a>
      </li>
    </ul>

  </div>
</nav>

<script>
  //ajax code for service Autocomplete$(document).ready(function(){
  $(document).ready(function() {
    $("#search").keyup(function() {
      var to_server = document.getElementById('search').value;
      $.ajax({
        url: "http://yasmanisubirat.com/cen4010/includes/search_ajax.php?val=" + to_server,
        success: function(result) {
          var serv_arr = result.split(",");
          $("#op1").val(serv_arr[0]);
          $("#op2").val(serv_arr[1]);
          $("#op3").val(serv_arr[2]);
          $("#op4").val(serv_arr[3]);
          $("#op5").val(serv_arr[4]);
          $("#op6").val(serv_arr[5]);
        }
      });
    });


    // update the cart counter
    <?php if(isset($_SESSION['token'])) { ?>
    updateCartCounter();
    <?php } ?>

  });

  function updateCartCounter() {

    $.post("includes/cart_ajax.php", {
        update_nav: true

      })
      .done(function(result, status, xhr) {
        $("#nav-counter").html(result)
      })
      .fail(function(xhr, status, error) {
        $("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
      });
  }

</script>
