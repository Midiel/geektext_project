
<?php 

    session_start();
    require_once('includes/connect.inc.php');

    $shippingInfo = array();

    //$query = " SELECT image_url, title, authors FROM book WHERE book_id = '" . $book_id ."'";
    //$query = "CALL getCart('" . $_SESSION['token'] . "')";

    $query = "SELECT f_name, l_name, street_address, state, city, zip_code FROM user, address WHERE user.user_id = address.user_id AND user.user_id IN (SELECT user_id FROM user WHERE token = '" . $_SESSION['token'] . "')";

    if($result = mysqli_query($con, $query)) {
        while($row = mysqli_fetch_assoc($result)) {
            //echo mysqli_error($con);
            array_push($shippingInfo, $row);
        }
    }

    //print_r($bookInfo);

    // Free Result
    mysqli_free_result($result);

    // Close Connection
    //mysqli_close($con);

    $info = array();
    $counter = 0;
    foreach($shippingInfo as $book) :
        $info[$counter] = $book;
        $counter++;
        //print_r($info);
    endforeach;

    //print_r($info[0]['street_address']);

    //return $info;


    // get card info

    $cards = array();
    $query = "SELECT type, credit_card.nickname FROM credit_card, user WHERE credit_card.user_id = user.user_id AND user.user_id IN (SELECT user_id FROM user WHERE token = '" . $_SESSION['token'] . "')";

    if($result = mysqli_query($con, $query)) {
        while($row = mysqli_fetch_assoc($result)) {
            //echo mysqli_error($con);
            array_push($cards, $row);
        }
    }

    //print_r($bookInfo);

    // Free Result
    mysqli_free_result($result);

    // Close Connection
    //mysqli_close($con);

    $card = array();
    $counter2 = 0;
    foreach($cards as $book) :
        $card[$counter2] = $book;
        $counter2++;
        //print_r($card);
    endforeach;

    //print_r($card[0]);


    // gets all items in the cart

    $books_on_cart = array();

    $query = "CALL getCart('" . $_SESSION['token'] . "')";

        if($result = mysqli_query($con, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                echo mysqli_error($con);
                array_push($books_on_cart, $row);
            }
        }

        // Free Result
        mysqli_free_result($result);

        // Close Connection
        mysqli_close($con);



    // get cart subtotal

    


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php 
        include("includes/navbar_libs.php"); 
        //include_once("includes/navbar.php");
    ?>
    <title>Checkout</title>
    		
</head>

<body>

    <!-- navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">GeekText</a>

        <div class="mx-auto order-0">
            <h5 class="navbar-brand mx-auto" href="#">Checkout</h5>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>



    <div class="container border border-primary mt-5 pt-5 pb-5">
        <div class="row border">       <!-- only one row -->
           
            <div class="border border-info col-sm-8">         <!-- left column begins -->
                left column

                <div class="row border">        <!-- row 1 begins -->
                    <div class="border border-info col-1">
                        <h5>1</h5>
                    </div>
                    <div class="border border-info col-3">
                        <h5>Shipping address</h5>
                    </div>
                    <div class="border border-info col-sm-6">
                        <strong>Address</strong> <br>
                        <?php echo $info[0]['f_name'] . ", " . $info[0]['l_name'];?> <br>
                        <?php echo $info[0]['street_address'];?> <br>
                        <?php echo $info[0]['city'] . ", " . $info[0]['state'] ." " . $info[0]['zip_code'];?> <br>

                        
                    </div>
                    <div class="border border-info col-sm-2">
                        <strong>Change</strong>
                    </div>

                </div>

                <div class="row border">        <!-- row 2 begins -->
                    <div class="border border-info col-sm-1">
                        <h5>2</h5>
                    </div>
                    <div class="border border-info col-sm-3">
                        <h5>Payment method</h5>
                    </div>
                    <div class="border border-info col-sm-6">
                        <strong>Card info</strong><br>
                        <?php echo $card[0]['type'];?> <br>
                        <?php echo $card[0]['nickname'];?> <br>

                    </div>
                    <div class="border border-info col-sm-2">
                        <strong>Change</strong>
                    </div>

                </div>

                <div class="row border">        <!-- row 3 begins -->
                    <div class="border border-info col-1">
                        <h5>3</h5>
                    </div>
                    <div class="border border-info col-11">
                        <h5>Review items</h5>

                        <!-- start of list of items needs to be looped-->
                        <?php foreach($books_on_cart as $book) :
			                if(!$book['saved_for_later']) {?>					<!-- only display not saved books, aka not in saved list -->

                            <div class="row border">
                                <div class="border border-info col-sm-3">
                                    <img src="<?php echo $book['image_url']; ?>" width="100" height="100" alt="..." class="img-responsive"/>

                                </div>
                                <div class="border border-info col-sm-9">
                                    <strong>Title</strong>: <?php echo $book['title']; ?><br>
									<strong>Author</strong>: <?php echo $book['authors']; ?><br><br>

                                    <div class="row border border-info">
                                        <div class="border border-info pt-2 col-2">
                                            <strong>Quantity</strong>:
                                        </div>
                                        <div class="border border-info col-3">
                                        <form>
                                            <div class="form-group">
                                            <input type="hidden" id="custId" name="book_id" value="<?php echo $book['book_id']; ?>">
                                                <select class="form-control" name="qty" id="<?php echo $book['book_id']; ?>" onchange="changeqty()">
                                                    <option value="" selected disabled hidden><?php echo $book['qty']; ?></option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="change_qty" value="true">
                                        </form>
                                        </div>

                                    </div>

                                </div>


                            </div>

                        <?php } endforeach; ?>


                    </div>
                   

                </div>




            </div>     <!-- end of left column -->

            <div class="border border-info col-sm-4">         <!-- right column -->
                Order Summary here

                <div class="row">
                    <div class="d-flex border border-info col-7">
                        Items:<br>
                        Shipping:<br>
                        Subtotal:<br>
                        Tax:<br>

                    </div>

                    <div class="d-flex border border-info col-5">
                        <p class="text-right">
                            $90.00<br>
                            $0.00<br>
                            $90.00<br>
                            $6.22<br>
                        </p>
                        
                    </div>
                </div>

                <div class="border border-warning row">
                    <div class="d-flex border border-info col-7">
                        <h5 class="mt-3">Order total:</h5>
                    </div>

                    <div class="d-flex border border-info col-5">
                        <p>
                            <h5 class="mt-3 text-right">$96.22</h5>
                        </p>
                    </div>

                </div>

                <div class="border border-info row">
                    <div class="border border-info col-sm align-self-end">

                        <a href="checkout.php" class="btn btn-default btn-block btn-warning mt-2 mb-2">
                            Checkout
                        </a>
                        
                    </div>
                </div>


            </div>
        
        </div>
            

    </div>
        

</body>

<script>

	// change number of items/qty
	function changeqty(e) {
		var thisid = event.target.id;
		
		$.post("includes/cart_ajax.php",
		{
			book_id: thisid,
			changeQty: $("#"+thisid).val()
		})
		.done(function (result, status, xhr) {
			$("#"+thisid).html(result)
			updateSubtotal();
		})
		.fail(function (xhr, status, error) {
			$("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
		});
	};

	function updateSubtotal(e) {
		$.post("includes/cart_ajax.php",
		{
			get_subtotal: true
		})
		.done(function (result, status, xhr) {
			$("#subtotal").html(result)
			updateNavbar();
		})
		.fail(function (xhr, status, error) {
			$("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
		});
	};


	// update cart counter
	function updateNavbar(e) {

		$.post("includes/cart_ajax.php",
			{
				update_nav: true,
				damn: true
				
			})
			.done(function (result, status, xhr) {
				$("#nav-counter").html(result)
			})
			.fail(function (xhr, status, error) {
				$("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
			});
	};

	function verifyDeletion(e) {

		var thisid = event.target.id;

		$.post("includes/cart_ajax.php",
		{
			book_id: thisid,
			verify_delete: true
		})
		.done(function (result, status, xhr) {
			$("#verifyDeleteModal").html(result);
			$("#verifyDeleteModal").modal('show');
			//updateSubtotal();
		})
		.fail(function (xhr, status, error) {
			$("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
		});
	}
	


</script>

</html>