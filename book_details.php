<?php

    // start session
    session_start();


    if(isset($_GET['book_id'])) {

        // to connect to the database
        require_once('includes/connect.inc.php');
        //include_once('includes/cart_ajax.php');
        include_once("includes/navbar_libs.php"); 
        include_once("includes/navbar.php");


        $book_id = $_GET['book_id'];

        // to store all books from the shopping cart
		$book = array();
	
		// get shooping cart from database
		$query = "SELECT * from book WHERE book_id ='". $book_id ."'";

		if($result = mysqli_query($con, $query)) {
			while($row = mysqli_fetch_assoc($result)) {
				$book = $row;
			}
		}

		// Free Result
		mysqli_free_result($result);

		// Close Connection
        mysqli_close($con);
        

    } else {

        header("Location: login.php");
		exit;
    }


?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Book Details</title>
		
</head>

<body>

<div class="container mt-5" id="main_container">

    <div class="row">       <!-- first row -->
        <div class="media">
            <img src="<?php echo $book['image_url']; ?>" class="mr-3" alt="...">
            <div class="media-body">
                <h5 class="mt-0"><?php echo $book['title']; ?></h5>
                
                <form class="form-inline" name="author" method="GET" action="index.php">
                    <input type="hidden" name="search" value="<?php echo $book['authors'];?>">
                    by<input type="submit" class="btn btn-link" value="<?php echo $book['authors'];?>">
                </form>
                <strong>Price</strong>: $<?php echo $book['price']; ?><br><br>

                <!-- Midiel: Add to cart button -->
                <form  class="form-inline" name="add_to_cart" method="POST" action="cart.php">
                    <div class="form-group btn-quantity">
                        <input type="hidden" name="add_to_cart" value="true">
                        <input type="hidden" name="book_id" value="<?php echo $book['book_id'];?>">
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
                
            </div>
        </div>

    </div>


    <div class="row mt-5">
        <div class="col">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-details-tab" data-toggle="tab" href="#nav-details" role="tab" aria-controls="nav-details" aria-selected="true">Product Details</a>
                    <a class="nav-item nav-link" id="nav-description-tab" data-toggle="tab" href="#nav-description" role="tab" aria-controls="nav-description" aria-selected="false">Product Description</a>
                    <a class="nav-item nav-link" id="nav-author-tab" data-toggle="tab" href="#nav-author" role="tab" aria-controls="nav-author" aria-selected="false">About the Author</a>  
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-details" role="tabpanel" aria-labelledby="nav-details-tab">
                    <div class="container m-3">
                        <div class="row">
                        <div calss="col-4">
                            <strong>ISBN</strong>:              <?php echo $book['isbn_13'];?> <br>
                            <strong>Publisher</strong>:	        <?php echo $book['publisher'];?>    <br>
                            <strong>Publication date</strong>:	<?php echo $book['published_date'];?>   <br>
                            <strong>Pages</strong>:             <?php echo $book['page_count'];?>   <br>
                            <strong>Category</strong>:	        <?php echo $book['category'];?> <br>
                        </div>
                        </div>
                        
                    </div>  
                </div>
                <div class="tab-pane fade" id="nav-description" role="tabpanel" aria-labelledby="nav-description-tab">
                    <div class="container m-3">
                        <p>
                            <?php echo $book['description'];?>
                        </p>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-author" role="tabpanel" aria-labelledby="nav-author-tab">
                    <div class="container m-3">
                        <p>
                            <?php echo $book['authorbio'];?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>

</body>
</html>