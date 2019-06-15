<?php
    include('includes/header.php');
    require_once('includes/connect.inc.php');
    //flag to detect user's credentials
    $logged_in = false;


    //checking if there is a user logged in
    session_start();
    $email = $_SESSION['email'];

    //$password = $_SESSION['password'];//need to encript it
    $token = $_SESSION['token'];
    $user_id = null;

if (isset($token) && !empty($token))
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
  $glyphicon_log_in = '<li><a href="login.php"  ><span class="glyphicon glyphicon-log-out"></span> Log out</a></li>';
}
else
{
  $email = '';
  $password = '';
  $token = '';
  session_destroy();
  $glyphicon_log_in =
    '<li><a href="#"  onclick="event.preventDefault();"><span class="glyphicon glyphicon-new-window"></span> Sign Up</a></li>
    <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Log in</a></li>';
}

$book = array(array());
$counter = 0;
$query = "SELECT * FROM books WHERE id > 0";
$run = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($run))
{
    $book[$counter]['id']= $row['id'];
    $book[$counter]['title']= $row['title'];
    $book[$counter]['author']= $row['author'];
    $book[$counter]['cover']= $row['cover'];
    $book[$counter]['bio']= $row['bio'];
    $book[$counter]['description']= $row['description'];
    $book[$counter]['price']= $row['price'];
    $book[$counter]['release_date']= $row['release_date'];
    $book[$counter]['sales']= $row['sales'];
    $book[$counter]['category']= $row['category'];
    $counter++;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Book Details Grid View</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/gridviewThumbnails.js"></script>
    <link rel="stylesheet" type="text/css" href="css/gridview.css">

</head>

<body>
    <div class="container text-left" id="books">
        <div class="row">
            <?php $c = count($book);
            for($i = 0; $i < $c; $i++): ?>

            <div id="parent-card" class="col-xs col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <section class="card">
                    <article class="image-section">
                        <a class="img-thumbnail">
                            <img src="<?php echo $book[$i]['cover'];?>">
                        </a>
                        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <img src="" class="imagepreview" style="width: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article class="info-section">
                        <div class="title-container">
                            <h2 class="title"><?php echo $book[$i]['title'];?></h2>
                        </div>
                        <div class="author-container">
                            <h2 class="author"><?php echo $book[$i]['author'];?></h2>
                        </div>
                        <div class="category-container">
                            <h3 class="category"><?php echo $book[$i]['category'];?></h3>
                        </div>
                    </article> <!-- "info-section" -->

                    <article class="button-section">
                        <button id="description-button" type="button" class="btn btn-outline-secondary btn-block btn-sm" data-toggle="modal" data-target="#description<?php echo $i ?>ModalLong">
                            Book Description
                        </button>
                        <div class="modal fade" id="description<?php echo $i ?>ModalLong" tabindex="-1" role="dialog" aria-labelledby="description<?php echo $i ?>ModalLongTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document" id="description<?php echo $i ?>ModalLong">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="descriptionModalLongTitle"><?php echo $book[$i]['title'] ?> by <?php echo $book[$i]['author']?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo $book[$i]['description'];?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button id="authorbio-button" type="button" class="btn btn-outline-secondary btn-block btn-sm" data-toggle="modal" data-target="#authorbioModalLong">
                            Author Bio
                        </button>
                        <div class="modal fade" id="authorbioModalLong" tabindex="-1" role="dialog" aria-labelledby="authorbioModalLongTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="authorbioModalLongTitle">Author Bio</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo $book[$i]['bio'];?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Midiel: Add to cart button -->
                        <form method="POST" action="cart.php">
                            <button type="submit" name="move_to_cart" value="<?php echo $book[$i]['id'];?>" class="btn btn-primary btn-sm mt-1">ADD TO CART </button>
                        </form>
                        <!-- end add to cart -->

                    </article> <!-- "button-section" -->
                </section> <!-- "card" -->
            </div> <!-- column end -->
            <?php endfor; ?>
        </div> <!-- row end -->
    </div> <!-- section end -->


</body>

</html>
