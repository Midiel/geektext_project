<?php

    /*
    *   To handle the chechout page.
    */

    // start session protocol
    session_start();
  
    // if user not logged in, redirect to login page
    if(!isset($_SESSION['token'])){
        header("Location: login.php");
        exit;

    } else {

        // to connect to the database
        require_once('includes/connect.inc.php');

        // saves new address to database
        function saveAddress($address, $con) {

            print_r ($address);
            print_r ("name: " . $_SESSION['shipping_address']['name']);

            $query = $con->prepare('CALL addAddress(?,?,?,?,?,?,?)');
            $query->bind_param('sssssss', $_SESSION['token'], $_SESSION['shipping_address']['name'], $_SESSION['shipping_address']['street_address'], 
                                        $_SESSION['shipping_address']['state'], $_SESSION['shipping_address']['city'], $_SESSION['shipping_address']['zip_code'], 
                                        $_SESSION['shipping_address']['country']);
            $query->execute();
            $query->close();
        }

        // save new card to database
        function saveCard($card, $con) {

            $query = $con->prepare('CALL addCard(?,?,?,?,?,?,?)');
            $query->bind_param('sssssss', $_SESSION['token'], $card['cardholder'], $card['type'], $card['number'], $card['exp_month'], $card['exp_year'], $card['security_code']);
            $query->execute();
            $query->close();
        }



        // if shipping to new address
        if(isset($_POST['new_address']) && ($_POST['street_address'] != $_SESSION['shipping_address']['street_address'])){
          
            $_SESSION['shipping_address'] = $_POST;
            $_SESSION['shipping_address']['name'] = $_SESSION['shipping_address']['f_name'] . " " . $_SESSION['shipping_address']['l_name'];
            $_SESSION['shipping_address']['country'] = "US";
            //print_r ($_SESSION['shipping_address']);

            if(isset($_POST['saveAddress'])){
                saveAddress($_SESSION['shipping_address'], $con);
            }

            unset($_POST['new_address']);
            
        } else if(isset($_POST['new_card']) && ($_POST['number'] != $_SESSION['chechout_card']['number'])) {

            $_SESSION['chechout_card'] = $_POST;

            // set default card type if user enters incorrect card number
            if(strlen($_SESSION['chechout_card']['type']) < 1) {
                $_SESSION['chechout_card']['type'] = 'Card';
            }

            if(isset($_POST['saveCard'])){
                saveCard($_SESSION['chechout_card'], $con);
            }

            // only keep the last 4 digits
            $_SESSION['chechout_card']['number'] = $_SESSION['chechout_card']['number'] % 10000;
            unset($_POST['new_card']);

        }


        // to store the shipping address information of the logged in user
        $userInfo = array();

        $query = "SELECT address_id, f_name, l_name, name, street_address, state, city, zip_code FROM user, address WHERE user.user_id = address.user_id AND user.user_id IN (SELECT user_id FROM user WHERE token = '" . $_SESSION['token'] . "')";

        if($result = mysqli_query($con, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                array_push($userInfo, $row);
            }
        }

        // Free Result
        mysqli_free_result($result);

        // to store the shipping address information of the logged in user
        $shippingInfo = array();

        $counter = 0;
        foreach($userInfo as $record) :
            $shippingInfo[$counter] = $record;
            $counter++;
            //print_r($info);
        endforeach;

        // save list of addresses to global variable
        $_SESSION['addresses'] = $shippingInfo;

        // set gloval variable for address
        if(!isset($_SESSION['shipping_address'])){
            $_SESSION['shipping_address'] = $shippingInfo[0];
        }
        

        // get credid cards information
        $cards = array();

        $query = "SELECT card_id, type, credit_card.cardholder, number FROM credit_card, user WHERE credit_card.user_id = user.user_id AND user.user_id IN (SELECT user_id FROM user WHERE token = '" . $_SESSION['token'] . "')";

        if($result = mysqli_query($con, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                //echo mysqli_error($con);
                array_push($cards, $row);
            }
        }

        // Free Result
        mysqli_free_result($result);


        $card = array();
        $counter = 0;

        foreach($cards as $record) :
            $card[$counter] = $record;
            $card[$counter]['number'] = $card[$counter]['number'] % 10000;
            //echo "last 4: " .$card[$counter2]['number'];
            $counter++;
            //print_r($card);
        endforeach;

        // save list of cards to global variable
        $_SESSION['cards'] = $card;

    
        // set gloval variable for checkout card
        if(!isset($_SESSION['chechout_card'])){
            $_SESSION['chechout_card'] = $_SESSION['cards'][0];
        }

        
        // gets all items in the cart. Move to checkout_ajax.php
        $books_on_cart = array();

        $query = "CALL getCart('" . $_SESSION['token'] . "')";

        if($result = mysqli_query($con, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                //echo mysqli_error($con);
                array_push($books_on_cart, $row);
            }
        }

        // Free Result
        mysqli_free_result($result);

        // Close Connection
        mysqli_close($con);

        // set gloval variable for books in for checkout. I need to move this query to checkout_ajax.php
        $_SESSION['checkout_books'] = $books_on_cart;

    }

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

    <div id="main_div" class="container mt-5 pt-5 pb-5">
        <div class="row">       <!-- only one row -->          
            <div class="col-md-8 col-sm-12 mb-3">         <!-- left column begins -->  
                <div class="row border-info border-bottom mr-2">        <!-- row 1 begins -->
                    <div class=" pt-2 col-1">
                        <h5>1</h5>
                    </div>
                    <div class=" pt-2 col-3">
                        <h5>Shipping address</h5>
                    </div>
                    <div id="address_field" class=" col-8 pt-2 pb-2">
                        <div class="row">
                            <div id="selected_address" class=" col-sm-9 ">
                                <p>
                                <?php echo $_SESSION['shipping_address']['f_name'] . ", " . $_SESSION['shipping_address']['l_name'];?> <br>
                                <?php echo $_SESSION['shipping_address']['street_address'];?> <br>
                                <?php echo $_SESSION['shipping_address']['city'] . ", " . $_SESSION['shipping_address']['state'] ." " . $_SESSION['shipping_address']['zip_code'];?> <br>
                                </p>
                            </div>
                            <div class=" col-sm-3 pt-1 pb-1">
                                <input type="submit" class="btn btn-link" onclick="changeAddress(); return false" name="change_shipping" value="Change">                   
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row border-info border-bottom mr-2">        <!-- row 2 begins, payment method -->
                    <div class=" pt-2 col-1">
                        <h5>2</h5>
                    </div>
                    <div class=" pt-2 col-3">
                        <h5>Payment method</h5>
                    </div>
                    <div id="card_field" class="col-8">
                        <div class="row  pt-2 pb-2">
                            <div id="selected_card" class=" col-sm-9 ">
                                <p>
                                <?php echo $_SESSION['chechout_card']['type'];?> ending in <?php echo $_SESSION['chechout_card']['number'];?><br>
                                <strong>Name on card</strong>: <?php echo $_SESSION['chechout_card']['cardholder'];?> <br>
                                </p>
                            </div>
                            <div class=" col-sm-3 pt-1 pb-1">
                                <input type="submit" class="btn btn-link" onclick="changeCard(); return false" value="Change">           
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row ">        <!-- row 3 begins -->
                    <div class=" pt-2 col-1">
                        <h5>3</h5>
                    </div>
                    <div class="col-11 pt-2">         <!-- working here -->
                        <h5>Review items</h5>

                        <!-- start of list of items needs to be looped-->
                        <?php foreach($books_on_cart as $book) :
			                if(!$book['saved_for_later']) {?>					<!-- only display not saved books, aka not in saved list -->

                            <div class="row border border-info rounded shadow m-1 mt-3">
                                <div class="col-xl-3 col-md-4 col-xs-12">
                                    <img src="<?php echo $book['image_url']; ?>" width="100" height="100" alt="..." class="img-responsive m-2"/>

                                </div>
                                <div class="col-md-8 col-sm-12 mt-2">
                                    <strong>Title</strong>: <?php echo $book['title']; ?><br>
									<strong>Author</strong>: <?php echo $book['authors']; ?><br><br>

                                    <div class="row ">
                                        <div class="col-lg-3 col-md-4 col-sm-4 col-3 pt-2">
                                            <strong>Quantity</strong>:
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-4 col-3">
                                        <form>
                                            <div class="form-group">
                                            <input type="hidden" id="custId" name="book_id" value="<?php echo $book['book_id']; ?>">
                                                <select class="form-control" name="qty" id="<?php echo $book['book_id']; ?>" onchange="changeQty()">
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
            
            <div id="order_summary" class="col-md-4 col-sm-12">         <!-- right column, order summary rendered from checkout_ajax.php-->        
            </div>      
        </div>
    </div>

    
    <!-- modal/form to add new shipping address -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" role="dialog" aria-labelledby="addAddressModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notLoggedInModalTitle">Enter a new shipping address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modales">
                    <form name="delete-item" method="POST" action="checkout.php">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationDefault01">First name</label>
                                <input type="text" class="form-control" id="validationDefault01" name="f_name" placeholder="First name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validationDefault02">Last name</label>
                                <input type="text" class="form-control" id="validationDefault02" name="l_name" placeholder="Last name" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault022">Street Address</label>
                                <input type="text" class="form-control" id="validationDefault22" name="street_address" placeholder="Street address" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="validationDefault03">City</label>
                                <input type="text" class="form-control" id="validationDefault03" name="city" placeholder="City" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="validationDefault04">State</label>
                                <input type="text" class="form-control" id="validationDefault04" name="state" placeholder="State" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="validationDefault05">Zip</label>
                                <input type="text" class="form-control" id="validationDefault05" name="zip_code" placeholder="Zip" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="saveAddress" value="true" id="invalidCheck2">
                                <label class="form-check-label" for="invalidCheck2">
                                    Save address to account.
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="new_address" value="true">
                        <input type="hidden" name="address_id" value="new">
                        <button type="submit" class="btn btn-primary">Use this address</button>
                        <button type="submit" class="btn btn-secondary ml-4" data-dismiss="modal">Cancel</button>
                    </form>
                </div>   
            </div>
        </div>
    </div>

    <!-- modal/form to enter new credit card --> 
    <div class="modal fade" id="addCardModal" tabindex="-1" role="dialog" aria-labelledby="addCardModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notLoggedInModalTitle">Enter a new credit/debit card</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modales">
                    <form name="delete-item" method="POST" action="checkout.php">
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault01">Cardholder Name</label>
                                <input type="text" class="form-control" id="validationDefault01" name="cardholder" placeholder="Cardholder name" maxlength="100" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-10 mb-3">
                                <label for="validationDefault022">Card Number</label>
                                <input type="text" class="form-control" id="card-number" name="number" placeholder="Card number" maxlength="30" pattern="[0-9.]+" autocomplete="off" required>
                            </div>
                            <div class="col-1 mt-1">
                                <i id="card-icon" class="fa fa-2x" aria-hidden="true"></i>
                                <input id="card-type" type="hidden" name="type" value="">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="validationDefault03">Exp. Month</label>
                                <select class="form-control" name="exp_month">
                                    <option value="1">01 - January</option>
                                    <option value="2">02 - February</option>
                                    <option value="3">03 - March</option>
                                    <option value="4">04 - April</option>
                                    <option value="5">05 - May</option>
                                    <option value="6">06 - June</option>
                                    <option value="7">07 - July</option>
                                    <option value="8">08 - August</option>
                                    <option value="9">09 - September</option>
                                    <option value="10">10 - October</option>
                                    <option value="11">11 - November</option>
                                    <option value="12">12 - December</option>
                                </select>
                            </div>
                            <div class="col-sm-3 ml-1">
                                <label for="expYear">Exp. Year</label>
                                <select class="form-control" id="expYear" name="exp_year">
                                    <?php 
                                    for ($year = (int)date("Y"); $year < ((int)date("Y") + 12); $year++)
                                    {
                                        echo '<option value="' . $year . '">' . $year . '</option>';
                                    } 
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-3 ml-1">
                                <label for="secCode">Security Code</label>
                                <input type="text" class="form-control" id="secCode" name="security_code" pattern="[0-9.]+" maxlength="5" autocomplete="off" required>
                            </div>

                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="saveCard" value="true" id="invalidCheck2">
                                <label class="form-check-label" for="invalidCheck2">
                                    Save credit card to account.
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="new_card" value="true">
                        <input type="hidden" name="card_id" value="new">
                        <button type="submit" class="btn btn-primary">Use this credit card</button>
                        <button type="submit" class="btn btn-secondary ml-4" data-dismiss="modal">Cancel</button>
                    </form>
                </div>   
            </div>
        </div>
    </div>
  
</body>

<!-- to use John's add new card form -->
<script src="js/paymethods.js"></script>

<script>

    // show modal
    function newAddressModal(){
        $("#addAddressModal").modal('show');
    }

    // enter new credit card modal/form 
    function newCardModal(){
      
        $("#addCardModal").modal('show');
    }

    // call checkout_ajax.php to get the cart subtotal
    function getOrderSummary() {

        $.post("includes/checkout_ajax.php",
        {
            get_summary: true

        })
        .done(function (result, status, xhr) {
            $("#order_summary").html(result)
        })
        .fail(function (xhr, status, error) {
            $("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
        });
    }

    // populate order summary after page load
    $(document).ready(function() {
        getOrderSummary();
    });


    // order confirmation
    function submitOrder(e){

        $.post("includes/checkout_ajax.php",
        {
            submit_order: true    
        })
        .done(function (result, status, xhr) { 
            $("#main_div").html(result)
        })
        .fail(function (xhr, status, error) {
            $("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
        });
    }

    
	// change number of items/qty
	function changeQty(e) {
		var thisid = event.target.id;
		
		$.post("includes/cart_ajax.php",
		{
			book_id: thisid,
			changeQty: $("#"+thisid).val()
		})
		.done(function (result, status, xhr) {
			$("#"+thisid).html(result)
            getOrderSummary();
            //updateNavbar();                   // not including the navbar, so disabled
		})
		.fail(function (xhr, status, error) {
			$("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
		});
	};


	// update navbar, not been used
	function updateNavbar(e) {

		$.post("includes/cart_ajax.php",
			{
				update_nav: true			
			})
			.done(function (result, status, xhr) {
				$("#nav-counter").html(result)
			})
			.fail(function (xhr, status, error) {
				$("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
			});
	};

    // to change shipping address
    function changeAddress(){

        $.post("includes/checkout_ajax.php",
        {
            change_shipping_address: true
        })
        .done(function (result, status, xhr) {
            $("#address_field").html(result)            
        })
        .fail(function (xhr, status, error) {
            $("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
        });
    }

    // to update the shipping address with the selected one
    function updateAddress(){

        var selection = $('#address_selector input:radio:checked').val();

        $.post("includes/checkout_ajax.php",
        {
            update_address: true,
            address: selection
        })
        .done(function (result, status, xhr) {
            $("#address_field").html(result)            
        })
        .fail(function (xhr, status, error) {
            $("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
        });
    }

    // to change the payment card
    function changeCard(){

        $.post("includes/checkout_ajax.php",
        {
            change_payment_card: true

        })
        .done(function (result, status, xhr) {
            $("#card_field").html(result)
            
        })
        .fail(function (xhr, status, error) {
            $("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
        });
    }

    // update the payment card with selected card
    function updateCard(){

        var selection = $('#card_selector input:radio:checked').val();

        $.post("includes/checkout_ajax.php",
        {
            update_card: true,
            card: selection

        })
        .done(function (result, status, xhr) {
            $("#card_field").html(result)
            
        })
        .fail(function (xhr, status, error) {
            $("#message").html("Result: " + status + " " + error + " " + xhr.status + " " + xhr.statusText)
        });

    }
	
</script>

</html>