<?php
    //squelch undefined index error
    error_reporting( error_reporting() & ~E_NOTICE);

    require_once('includes/connect.inc.php');
    //flag to detect user's credentials
    $logged_in = false;
    $DISPLAY_PER_PAGE = 12;
    $DISPLAY_PER_ROW = 3;
    $top_sellers = false;
    $sorting = false;
    $order == false;
    $temp_author_array = array();
    //sorting value
    if (isset($_GET['sort_by']) && !empty($_GET['sort_by']))
    {
      $sort_by = $_GET['sort_by'];
      $sorting = true;
    }
    if ($sort_by == 'average_rating_des')
    {
      $order = true;
    }

    //Pagination Code
    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] != 1)
    {
      $page = $_GET['page'];
    }
    else
    {
      $page = 1;
    }
    //top sellers
    if ($_GET['top_sellers'] == 'true' || $_SESSION['top_sellers'] == 'true')
    {
      $top_sellers = true;
    }
    //Displaying books
    $book = array(array());
    $counter = 0;
    $display_from = ($page - 1) * $DISPLAY_PER_PAGE;
    $display_to = $display_from + ($DISPLAY_PER_PAGE * 3);
    //Search code
    if (isset($_POST['search']) && !empty($_POST['search']))
    {
      $search = $_POST['search'];
      $search_array = explode(': ', $search);
      if(count($search_array) == 1)
      {
        $query = "SELECT * FROM book WHERE `title` LIKE '%%$search%%' OR `authors` LIKE '%%$search%%' OR `category` LIKE '%%$search%%' ORDER BY book_id DESC LIMIT $display_from, $display_to";
      }
      else
      {
        $search_by = $search_array[0];
        $search_for = $search_array[1];
        if ($search_by == 'Title')
        {
          $query = "SELECT * FROM book WHERE `title` LIKE '%%$search_for%%' ORDER BY title DESC LIMIT $display_from, $display_to";
        }
        else if ($search_by == 'Author')
        {
          $query = "SELECT * FROM book WHERE `authors` LIKE '%%$search_for%%' ORDER BY authors DESC LIMIT $display_from, $display_to";
        }
        else
        {
          $query = "SELECT * FROM book WHERE `category` LIKE '%%$search_for%%' ORDER BY category DESC LIMIT $display_from, $display_to";
        }
      }
    }
    else
    {
        //determine query
        if ($top_sellers == true && $sorting == false)
        {
          $query = "SELECT * FROM book WHERE book_id > 0 ORDER BY sales DESC LIMIT $display_from, $display_to";
        }
        else if ($top_sellers == false && $sorting == false)
        {
          $query = "SELECT * FROM book WHERE book_id > 0 ORDER BY book_id DESC LIMIT $display_from, $display_to";
        }
        else if ($top_sellers == true && $sorting == true && $order == false)
        {
          $query = "SELECT * FROM (SELECT * FROM book WHERE book_id > 0 ORDER BY sales DESC) as sub ORDER BY $sort_by ASC LIMIT $display_from, $display_to";
        }
        else if ($top_sellers == false && $sorting == true && $order == false)
        {
          $query = "SELECT * FROM book WHERE book_id > 0 ORDER BY $sort_by ASC LIMIT $display_from, $display_to";
        }
        else if ($top_sellers == true && $sorting == true && $order == true)
        {
          $query = "SELECT * FROM (SELECT * FROM book WHERE book_id > 0 ORDER BY sales DESC) as sub ORDER BY average_rating DESC LIMIT $display_from, $display_to";
        }
        else if ($top_sellers == false && $sorting == true && $order == true)
        {
          $query = "SELECT * FROM book WHERE book_id > 0 ORDER BY average_rating DESC LIMIT $display_from, $display_to";
        }
    }
    $run = mysqli_query($con, $query);
    while($row = mysqli_fetch_assoc($run))
    {
          $book[$counter]['book_id']= $row['book_id'];
          $book[$counter]['title']= $row['title'];
          $temp_author_array = explode(',', $row['authors']);
          $book[$counter]['author'] = '';
          for($i = 0; $i < count($temp_author_array); $i++)
          {
            if($i == (count($temp_author_array) - 1))
            {
            $book[$counter]['author'] .= '<a id="author" style="color:white" href="#" onclick="get_author(\''.$temp_author_array[$i].'\')" >'.$temp_author_array[$i].'</a>';
            }else {
            $book[$counter]['author'] .= '<a style="color:white" href="#" onmouseover="" onclick="get_author(\''.$temp_author_array[$i].'\')" >'.$temp_author_array[$i].',</a>';
            }
          }
          $book[$counter]['bio'] = $row['authorbio'];
          $book[$counter]['count'] = $row['rating_count'];
          $book[$counter]['rating'] = set_stars($row['average_rating']);
          $book[$counter]['image_url']= $row['image_url'];
          $book[$counter]['description']= $row['description'];
          $book[$counter]['price']= $row['price'];
          $book[$counter]['published_date']= $row['published_date'];
          $book[$counter]['sales']= $row['sales'];
          $book[$counter]['category']= $row['category'];
          $counter++;
      }
      //Pagination algorithm
  $path = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
  //appending or replacing path
  if(strpos($path,"?") > 0)
  {
    $path .= "&";
  }
  else
  {
    $path .= "?";
  }
  $pages_available = $counter / $DISPLAY_PER_PAGE;
  if($page == 1)
  {
    if($pages_available <= 2)
    {
      $pagination = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Previous</a></li>';
      $pagination .= '<li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>';
      $pagination .= '<li class="page-item "><a class="page-link" href="'.$path.'page=2">2</a></li>';
      $pagination .= '<li class="page-item"><a class="page-link" href="'.$path.'page=2">Next</a></li>';
    }
    else
    {
      $pagination = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Previous</a></li>';
      $pagination .= '<li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>';
      $pagination .= '<li class="page-item "><a class="page-link" href="'.$path.'page=2">2</a></li>';
      $pagination .= '<li class="page-item "><a class="page-link" href="'.$path.'page=3">3</a></li>';
      $pagination .= '<li class="page-item"><a class="page-link" href="'.$path.'page=2">Next</a></li>';
    }
  }
  else
  {
      $pagination = '<li class="page-item"><a class="page-link" href="'.$path.'page='.($page - 1).'">Previous</a></li>';
      $pagination .= '<li class="page-item "><a class="page-link" href="'.$path.'page='.($page - 1).'">'.($page - 1).'</a></li>';
      $pagination .= '<li class="page-item active"><a class="page-link" href="'.$path.'page='.($page).'">'.($page).'</a></li>';
      $pagination .= '<li class="page-item "><a class="page-link" href="'.$path.'page='.($page + 1).'">'.($page + 1).'</a></li>';
      $pagination .= '<li class="page-item"><a class="page-link" href="'.$path.'page='.($page + 1).'">Next</a></li>';
  }
  //updating pagination when reach the end of the books
  $last_page = false;
  if($counter < ($DISPLAY_PER_PAGE + 1))
  {
    if($page == 1)
    {
      $pagination = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Previous</a></li>';
      $pagination .= '<li class="page-item active"><a class="page-link" href="'.$path.'page='.($page).'">'.($page).'</a></li>';
      $pagination .= '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Next</a></li>';
    }
    else if ($page == 2)
    {
      $pagination = '<li class="page-item"><a class="page-link" href="'.$path.'page='.($page - 1).'">Previous</a></li>';
      $pagination .= '<li class="page-item "><a class="page-link" href="'.$path.'page='.($page - 1).'">'.($page - 1).'</a></li>';
      $pagination .= '<li class="page-item active"><a class="page-link" href="'.$path.'page='.($page).'">'.($page).'</a></li>';
      $pagination .= '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Next</a></li>';
    }
    else
    {
      $pagination = '<li class="page-item"><a class="page-link" href="'.$path.'page='.($page - 1).'">Previous</a></li>';
      $pagination .= '<li class="page-item "><a class="page-link" href="'.$path.'page='.($page - 2).'">'.($page - 2).'</a></li>';
      $pagination .= '<li class="page-item "><a class="page-link" href="'.$path.'page='.($page - 1).'">'.($page - 1).'</a></li>';
      $pagination .= '<li class="page-item active"><a class="page-link" href="'.$path.'page='.($page).'">'.($page).'</a></li>';
      $pagination .= '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">Next</a></li>';
    }
    $last_page = true;
  }
  function set_stars($start_double)
  {
    $result = '';
    for($i = 0; $i < 5; $i++)
    {
      if($i <= ($start_double - 1))
      {
        $result .= '<i class="fa fa-star"></i>';
      }
      else if($start_double > $i)
      {
        $result .= '<i class="fa fa-star-half-full"></i>';
      } else
      {
        $result .= '<i class="fa fa-star-o"></i>';
      }

    }

    return $result;
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Book Details Grid View</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include("includes/navbar_libs.php"); ?>
  <link rel="stylesheet" type="text/css" href="css/index.css">
  <link rel="stylesheet" type="text/css" href="css/rating.css">
  <script src="js/indexThumbnails.js"></script>
</head>

<body>
  <?php include_once("includes/navbar.php"); ?>
  <div class="container text-left" id="books">
    <div class="row">
      <?php if($last_page != true){
              $looping = $DISPLAY_PER_PAGE;
            }else {
              $looping = sizeof($book);
            }
              for($i = 0; $i < $looping; $i++): ?>

      <div class="col-xs col-sm-4 col-md-3 col-lg-2 col-xl-2">
        <div class="card flex-container">
          <div class="image-section">
            <a class="img-thumbnail">
              <img src="<?php echo $book[$i]['image_url'];?>">
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
          </div>

          <div>
            <div class="star-rating">
              <div class="price">
                <?php echo '$'.$book[$i]['price'];?>
              </div>
              <div class="stars">
                <?php echo $book[$i]['rating'].' ('.$book[$i]['count'].')';?>
              </div>
            </div>
            <!--end star-rating -->
          </div>

          <div class="info-section">
            <div class="title-container">
              <h2 class="title"><?php echo $book[$i]['title'];?></h2>
            </div>
            <div class="author-container">
              <h2 class="author"><?php echo $book[$i]['author'];?></h2>
            </div>
            <div class="category-container">
              <h3 class="category"><?php echo $book[$i]['category'];?></h3>
            </div>
          </div> <!-- "info-section" -->

          <div class="button-section">
            <button id="description-button" type="button" class="btn btn-outline-secondary btn-block btn-sm" data-toggle="modal" data-target="#description<?php echo $i ?>ModalLong">
              Book Description
            </button>
            <div class="modal fade" id="description<?php echo $i ?>ModalLong" tabindex="-1" role="dialog" aria-labelledby="description<?php echo $i ?>ModalLongTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document" id="description<?php echo $i ?>ModalLong">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5><?php echo $book[$i]['title'] ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <h5><?php echo $book[$i]['description'];?></h5>
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
                    <h5 class="modal-title" id="authorbioModalLongTitle"><?php echo $book[$i]['author']?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <h5><?php echo $book[$i]['bio'];?></h5>
                  </div>
                </div>
              </div>
            </div>

            <!-- Midiel: Add to cart button -->
            <form id="<?php echo $book[$i]['book_id'];?>" onsubmit="addToCart(); return false;">
              <div class="form-group btn-quantity">
                <input type="hidden" name="book_id" value="<?php echo $book[$i]['book_id'];?>">
                <select class="form-control" id="qty" name="qty">
                  <option value="1" selected="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                </select>
                <button type="submit" id="test" name="add_to_cart" value="true" class="btn btn-default btn-sm">ADD TO CART </button>
              </div>
            </form>
            <!-- end add to cart -->

          </div> <!-- "button-section" -->
        </div> <!-- "card" -->
      </div> <!-- column end -->
      <?php endfor; ?>
    </div> <!-- row end -->
  </div> <!-- section end -->

  <!--page navegation-->
  <div class="container">
    <ul class="pagination justify-content-center">

      <?php echo $pagination; ?>

    </ul>
  </div>


  <!-- Modal to show item was added to cart-->
  <div class="modal fade" id="addedToCartModal" tabindex="-1" role="dialog" aria-labelledby="addedToCartModalTitle" aria-hidden="true">
  </div>

  <!-- Modal for not logged in-->
  <div class="modal fade" id="notLoggedInModal" tabindex="-1" role="dialog" aria-labelledby="notLoggedInModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notLoggedInModalTitle">Not Logged In</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="modales">
          You need to be logged in to add items to the shopping cart.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-info" data-dismiss="modal" onclick="javascript:window.location='login.php'">Log in</button>
          <button type="button" class="btn btn-success" data-dismiss="modal" onclick="javascript:window.location='register.php'">Register</button>
        </div>
      </div>
    </div>
  </div>



  <!--hidden post -->
  <form id="hidden_form" method="POST" action="index.php">
    <input type="hidden" name="search" id="hidden_search" value="">
  </form>
  <p id="test"></p>
  <script>
    // add items to the cart
    function addToCart() {
      <?php if(!isset($_SESSION['token'])) { ?>
      $("#notLoggedInModal").modal('show');
      <?php } else { ?>

      var thisid = event.target.id;
      //window.alert(thisid);
      var values = $("#" + thisid).serializeArray();
      var inputs = {};
      $.each(values, function(k, v) {
        inputs[v.name] = v.value;
        //window.alert(v.name + " " + v.value);
      });
      $.post("includes/cart_ajax.php", {
          add_to_cart: true,
          book_id: inputs['book_id'],
          qty: inputs['qty']
        })
        .done(function(result, status, xhr) {
          $("#" + thisid).html(result)
          updateCartCounter(); // it's in the navbar
          addToCartModal(inputs);
          //$("#addedToCartModal").modal('show');
        })
        .fail(function(xhr, status, error) {
          $("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
        });
      <?php } ?>
    }
    // show modal for add to cart
    function addToCartModal(inputs) {
      $.post("includes/cart_ajax.php", {
          add_to_cart_modal: true,
          book_id: inputs['book_id'],
          qty: inputs['qty']
        })
        .done(function(result, status, xhr) {
          $("#addedToCartModal").html(result);
          $("#addedToCartModal").modal('show');
        })
        .fail(function(xhr, status, error) {
          $("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
        });
    }
    //hidden author's Code
    function get_author(author) {
      document.getElementById('hidden_search').value = author;
      document.getElementById('hidden_form').submit();
    }
  </script>

</body>

</html>
