<?php
    require_once('includes/connect.inc.php');
    //flag to detect user's credentials
    $logged_in = false;
    $DISPLAY_PER_PAGE = 9;
    $DISPLAY_PER_ROW = 3;

    //Pagination Code
    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] != 1)
    {
      $page = $_GET['page'];
    }
    else
    {
      $page = 1;
    }

    //checking if there is a user logged in
    session_start();
    $email = $_SESSION['email'];
  //  $password = $_SESSION['password'];//need to encript it
    $token = $_SESSION['token'];
    $user_id = null;

    if(isset($token) && !empty($token))
    {
      $query = "SELECT remember_token FROM users WHERE email = '$email'";
      $run = mysqli_query($con, $query);
      while($row = mysqli_fetch_assoc($run)){
        if ($row['remember_token'] == $token)
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

    //Displaying books
    $book = array(array());
    $counter = 0;
    $display_from = ($page - 1) * $DISPLAY_PER_PAGE;
    $display_to = $display_from + ($DISPLAY_PER_PAGE * 3);
    $query = "SELECT * FROM books WHERE id > 0 LIMIT $display_from, $display_to";
    $run = mysqli_query($con, $query);
    while($row = mysqli_fetch_assoc($run))
    {
        $book[$counter]['id']= $row['id'];
        $book[$counter]['name']= $row['name'];
        $book[$counter]['author']= $row['author'];
        $book[$counter]['cover']= $row['cover'];
        $book[$counter]['bio']= $row['bio'];
        $book[$counter]['description']= $row['description'];
        $book[$counter]['price']= $row['price'];
        $book[$counter]['release_date']= $row['release_date'];
        $book[$counter]['sales']= $row['sales'];
        $book[$counter]['genre']= $row['genre'];
        $counter++;
    }


    //Pagination Code
    $pages_available = $counter / $DISPLAY_PER_PAGE;
    if($page == 1)
    {
      if($pages_available <= 2)
      {
        $pagination = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Previous</a></li>';
        $pagination .= '<li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>';
        $pagination .= '<li class="page-item "><a class="page-link" href="index.php?page=2">2</a></li>';
        $pagination .= '<li class="page-item"><a class="page-link" href="index.php?page=2">Next</a></li>';
      }
      else
      {
        $pagination = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Previous</a></li>';
        $pagination .= '<li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>';
        $pagination .= '<li class="page-item "><a class="page-link" href="index.php?page=2">2</a></li>';
        $pagination .= '<li class="page-item "><a class="page-link" href="index.php?page=3">3</a></li>';
        $pagination .= '<li class="page-item"><a class="page-link" href="index.php?page=2">Next</a></li>';
      }
    }
    else
    {
        $pagination = '<li class="page-item"><a class="page-link" href="index.php?page='.($page - 1).'">Previous</a></li>';
        $pagination .= '<li class="page-item "><a class="page-link" href="index.php?page='.($page - 1).'">'.($page - 1).'</a></li>';
        $pagination .= '<li class="page-item active"><a class="page-link" href="index.php?page='.($page).'">'.($page).'</a></li>';
        $pagination .= '<li class="page-item "><a class="page-link" href="index.php?page='.($page + 1).'">'.($page + 1).'</a></li>';
        $pagination .= '<li class="page-item"><a class="page-link" href="index.php?page='.($page + 1).'">Next</a></li>';
    }

    //updating pagination when reach the end of the books
    $last_page = false;
    if($counter < ($DISPLAY_PER_PAGE + 1))
    {
      if($page == 1)
      {
        $pagination = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Previous</a></li>';
        $pagination .= '<li class="page-item active"><a class="page-link" href="index.php?page='.($page).'">'.($page).'</a></li>';
        $pagination .= '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Next</a></li>';
      }
      else if ($page == 2)
      {
        $pagination = '<li class="page-item"><a class="page-link" href="index.php?page='.($page - 1).'">Previous</a></li>';
        $pagination .= '<li class="page-item "><a class="page-link" href="index.php?page='.($page - 1).'">'.($page - 1).'</a></li>';
        $pagination .= '<li class="page-item active"><a class="page-link" href="index.php?page='.($page).'">'.($page).'</a></li>';
        $pagination .= '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Next</a></li>';
      }
      else
      {
        $pagination = '<li class="page-item"><a class="page-link" href="index.php?page='.($page - 1).'">Previous</a></li>';
        $pagination .= '<li class="page-item "><a class="page-link" href="index.php?page='.($page - 2).'">'.($page - 2).'</a></li>';
        $pagination .= '<li class="page-item "><a class="page-link" href="index.php?page='.($page - 1).'">'.($page - 1).'</a></li>';
        $pagination .= '<li class="page-item active"><a class="page-link" href="index.php?page='.($page).'">'.($page).'</a></li>';
        $pagination .= '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Next</a></li>';
      }
      $last_page = true;
    }


    //Displaying books
    $book_string = '';
    if($last_page != true)
    {
        for($book_number = 0; $book_number < $DISPLAY_PER_PAGE; $book_number++)
        {
            if($book_number % $DISPLAY_PER_ROW == 0)
            {
                $book_string .= '<div class="row">';
            }
            $book_string .= '<div class="col-sm-4">';
            $book_string .= 'Name: '.$book[$book_number]['name'].'<br />';
            $book_string .= '<img src="'.$book[$book_number]['cover'].'" class="img-rounded" alt="'.$book[$book_number]['name'].'" width="304" height="236">';
            $book_string .= 'Description: '.$book[$book_number]['description'].'<br />';
            $book_string .= 'Genre: '.$book[$book_number]['genre'];
            $book_string .= '</div>';

            if($book_number % $DISPLAY_PER_ROW == ($DISPLAY_PER_ROW - 1))
            {
                $book_string .= '</div>';
            }
        }
    }
    else
    {
        if(sizeof($book) > 0)
        {
            for($book_number = 0; $book_number < sizeof($book); $book_number++)
            {
                if($book_number % $DISPLAY_PER_ROW == 0)
                {
                    $book_string .= '<div class="row">';
                }
                $book_string .= '<div class="col-sm-4">';
                $book_string .= 'Name: '.$book[$book_number]['name'].'<br />';
                $book_string .= '<img src="'.$book[$book_number]['cover'].'" class="img-rounded" alt="'.$book[$book_number]['name'].'" width="304" height="236">';
                $book_string .= 'Description: '.$book[$book_number]['description'].'<br />';
                $book_string .= 'Genre: '.$book[$book_number]['genre'];
                $book_string .= '</div>';

                if($book_number % $DISPLAY_PER_ROW == ($DISPLAY_PER_ROW - 1))
                {
                    $book_string .= '</div>';
                }
            }
        }
    }


 ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Book Store</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#" onclick="event.preventDefault();"> Team 8 </a>
    </div>
    <ul class="navbar-nav">
      <li class="nav-item active"><a class="nav-link" href="#" onclick="event.preventDefault();"> Home </a> </li>
      <li> <a class="nav-link" href="#">Top Sellers</a> </li>
    </ul>
    <ul class="navbar-nav navbar-right">
      <li class="nav-item"><a class="nav-link" href="#"  onclick="event.preventDefault();"><span class="fa fa-user"></span> <?php echo $email;?> </a> </li>
      <!--pulling information from the user's credentials-->
      <?php echo $glyphicon_log_in;?>
      <li class="nav-item"><a class="nav-link" href="#" onclick="event.preventDefault();"><span class="fa fa-shopping-cart"> <?php echo $items_in_cart;?> </span></a> </li>
    </ul>
  </div>
</nav>

<div class="container">
  <?php echo $book_string; ?>
</div>

<!--page navegation-->
<div class="container">
  <ul class="pagination justify-content-center">

      <?php echo $pagination; ?>

    </ul>
  </div>

</body>
</html>
