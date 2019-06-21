
<?php
    include('includes/header.php');
    require_once('includes/connect.inc.php');

	// hard code session token to 123 (user1) if session is not set, user not logged in
	if(!(isset($_SESSION['token']))) {
		$_SESSION['token'] = '123';
	};
	//$_SESSION['token'] = '456';		//user2

	echo "session: ".$_SESSION['token']."<br>";

	print_r($_POST);
	echo "<br>";


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


	/*
	// get all items from shopping cart
	$query = $con->prepare('CALL getCart(?)');
	$query->bind_param('i', $_SESSION['token']);
	$query->execute();
	echo "61 ---------";
	// variable to hold all records in the cart
	$books_on_cart = array();
	
	
	// loop through all the records and save them to the books variable
	if($result = $query->get_result()) {
		while($row = mysqli_fetch_assoc($result)) {
			echo mysqli_error($con);
			array_push($books_on_cart, $row);
		}
	}
	*/

	$books_on_cart = array();

	$query = "CALL getCart(" . $_SESSION['token'] . ")";

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

	// temporary variables for subtotal
	$subtotal = 0;
	$num_items = 0;

?>


<br><br><br>

<!-- Display the shopping cart -->
<div class="container">
	<table class="table">
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
									Author: <?php echo $book['author']; ?><br><br>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<form name="deleteForm" method="POST" action="cart.php">
										<input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
										<input type="hidden" name="delete" value="true">
										<input type="submit" class="btn btn-outline-danger" value="Delete">
									</form>
								</div>
								<div class="col-sm-4">
									<form name="deleteForm" method="POST" action="cart.php">
											<input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
											<input type="hidden" name="save_for_later" value="true">
											<input type="submit" class="btn btn-outline-secondary" value="Save for Later">
									</form>
								</div>
							</div>
						</div>
					</td>
					<td class="text-center">
						$<?php echo $book['price']; ?>
					</td>
					<td>
						<form method="POST" action="cart.php">
							<div class="form-group">
							<input type="hidden" id="custId" name="book_id" value="<?php echo $book['book_id']; ?>">
								<select class="form-control" name="qty" id="sel1" onchange="if(this.value != 0) { this.form.submit(); }">
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
				<a href="index.php" class="btn btn-warning">
					<i class="fa fa-angle-left"></i> Continue Shopping</a></td>
					<td colspan="2" class="hidden-xs"></td>
		</div>
		<div class="col-3">
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
							Author: <?php echo $book['author']; ?><br><br>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2">
							<form name="deleteForm" method="POST" action="cart.php">
								<input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
								<input type="hidden" name="delete" value="true">
								<input type="submit" class="btn btn-outline-danger" value="Delete">
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


<?php include('includes/footer.php'); ?>
