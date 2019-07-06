
<?php

    //include('includes/header.php');
	require_once('includes/connect.inc.php');
	//include_once('includes/cart_ajax.php');
    include_once("includes/navbar_libs.php"); 
    include_once("includes/navbar.php");

	// hard code session token to 123 (user1) if session is not set, user not logged in
	// if(!(isset($_SESSION['token']))) {
	// 	$_SESSION['token'] = 'c6734b5d94fdf74db73175ba5429ec11';
	// };
	//$_SESSION['token'] = '456';		//user2

    //echo "session: ".$_SESSION['token']."<br>";
    

	//print_r($_POST);
	//echo "<br>";


	if(isset($_POST['add_to_cart'])) {

		// (token, book_id, qty)
		$query = $con->prepare('CALL addToCart(?,?,?)');
		$query->bind_param('sii', $_SESSION['token'], $_POST['book_id'], $_POST['qty']);
		$query->execute();
		$query->close();

	} else if(isset($_POST['delete'])) {

		$query = $con->prepare('CALL deleteFromCart(?,?)');
		$query->bind_param('si', $_SESSION['token'], $_POST['book_id']);
		$query->execute();
		$query->close();

	} else if(isset($_POST['save_for_later'])) {

		$query = $con->prepare('CALL saveForLater(?,?)');
		$query->bind_param('si', $_SESSION['token'], $_POST['book_id']);
		$query->execute();
		$query->close();

	} else if(isset($_POST['move_to_cart'])) {

		$query = $con->prepare('CALL moveToCart(?,?)');
		$query->bind_param('si', $_SESSION['token'], $_POST['book_id']);
		$query->execute();
		$query->close();


	} else if(isset($_POST['change_qty'])) {

		$query = $con->prepare('CALL changeQty(?,?,?)');
		$query->bind_param('sii', $_SESSION['token'], $_POST['book_id'], $_POST['qty']);
		$query->execute();
		$query->close();
	}


	//echo "token type: " . gettype($_SESSION['token']);

	
    $books_on_cart = array();
    
    if(isset($_SESSION['token'])) {
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
    }

	

	// temporary variables for subtotal
	$subtotal = 0;
	$num_items = 0;

	$_SESSION['cart_qty'] = $num_items;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
        include("includes/navbar_libs.php"); 
        include_once("includes/navbar.php");
    ?>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Shopping Cart</title>
    
			
</head>


<body>
    


<br><br><br>
<div class="container">

<!-- Display the shopping cart -->
<div class="container" id="all">
	<table class="table-responsive">
		<thead>
			<tr>
			<th scope="col" width="15%" class="text-left"><h5>Shopping Cart</h5></th>
			<th scope="col" width="45%" class="text-left"></th>
			<th scope="col" width="15%" class="text-center">Price</th>
			<th scope="col" width="5%" class="text-right">Quantity</th>
			</tr>
		</thead>
		<?php foreach($books_on_cart as $book) :
			if(!$book['saved_for_later']) {?>					<!-- only display not saved books, aka not in saved list -->

			<tbody id="tableBody">
				<tr>
					<th scope="row">
						<div class="col-sm-3 hidden-xs"><img src="<?php echo $book['image_url']; ?>" width="100" height="100" alt="..." class="img-responsive"/></div>
					</th>
					<td>
						<div class="container" >
							<div class="row">
								<div class="col">
									Title: <?php echo $book['title']; ?><br>
									Author: <?php echo $book['authors']; ?><br><br>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<form name="deleteForm" id="<?php echo $book['book_id']; ?><br>" onsubmit="verifyDeletion(); return false;">
										<input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
										<input type="hidden" name="delete" value="true">
										<input type="submit" class="btn btn-outline-danger btn-sm" value="Delete">
									</form>
								</div>
								<div class="col-sm-4">
									<form name="saveForLaterForm" method="POST" action="cart.php">
											<input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
											<input type="hidden" name="save_for_later" value="true">
											<input type="submit" class="btn btn-outline-secondary btn-sm" value="Save for Later">
									</form>
								</div>
							</div>
						</div>
					</td>
					<td class="text-center">
						$<?php echo $book['price']; ?>
					</td>
					<td>
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
							<?php $num_items += $book['qty'];
							$temp = $book['qty'] * $book['price'];
							$subtotal += $temp;
						?>

					</td>
				</tr>
				
			</tbody>
		<?php } endforeach; ?>
	</table>

	<br>

	<!-- Continue shopping and subtotal line -->
	<div class="row justify-content-between">
		<div class="col-4">
			<td>
				<a href="index.php" class="btn btn-success btn-sm">
					<i class="fa fa-angle-left"></i> Continue Shopping</a></td>
					<td colspan="2" class="hidden-xs"></td>
		</div>
		<div class="col-3" id="subtotal">
			<strong>Subtotal (<?php echo $num_items;
									if($num_items<2) {
										echo " item):";
									} else {
										echo " items):";
									}
									?></strong> $<?php echo $subtotal;?>
		</div>
  	</div>
</div>

<br><br><br><br><br>


<!-- Start of Saved for Later list -->
<br>
<div class="container">
	<table class="table">
	<thead>
		<tr>
			<th scope="col" width="15%" class="text-left"><h5>Saved for Later</h5></th>
			<th scope="col" width="45%" class="text-left"></th>
			<th scope="col" width="15%" class="text-left">Price</th>
		</tr>
	</thead>

	<?php foreach($books_on_cart as $book) :
			if($book['saved_for_later']) {?>					<!-- only display not saved books, aka not in saved list -->

	<tbody>
		<tr>
			<th scope="row">
				<div class="col-sm-3 hidden-xs"><img src="<?php echo $book['image_url']; ?>" width="100" height="100" alt="..." class="img-responsive"/></div>
			</th>
			<td>
				<div class="container" >
					<div class="row">
						<div class="col">
							Title: <?php echo $book['title']; ?><br>
							Author: <?php echo $book['authors']; ?><br><br>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2">
							<form name="deleteForm" method="POST" action="cart.php">
								<input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
								<input type="hidden" name="delete" value="true">
								<input type="submit" class="btn btn-outline-danger btn-sm" value="Delete">
							</form>
						</div>
						<div class="col-sm-4">
							<form name="deleteForm" method="POST" action="cart.php">
									<input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
									<input type="hidden" name="move_to_cart" value="true">
									<input type="submit" class="btn btn-link" value="Move to Cart">
							</form>
						</div>
					</div>
				</div>
			</td>
			<td>
				$<?php echo $book['price']; ?>
			</td>
		</tr>
	</tbody>
	<?php } endforeach; ?>
	</table>
</div>

</div>


	<!-- Modal to verify deletion-->
	<div class="modal fade" id="verifyDeleteModal" tabindex="-1" role="dialog" aria-labelledby="verifyDeleteModalTitle" aria-hidden="true">

    </div>


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
    
   

</body>

</html>


