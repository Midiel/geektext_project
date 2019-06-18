
<?php
    include('includes/header.php');
    require_once('includes/connect.inc.php');
	//require_once('includes/connect.inc.php');
    //require_once('config/db.php');

	// start the session
	//session_start();

	// hard code session token to 123 (user1) if session is not set, user not logged in
	if(!(isset($_SESSION['token']))) {
		$_SESSION['token'] = '123';
	};
	//$_SESSION['token'] = '456';		//user2

	echo "session: ".$_SESSION['token']."<br>";

	print_r($_POST);
	echo "<br>";



	if(isset($_POST['delete'])) {
		//$item = htmlentities($_POST['delete']);
		$book_id = mysqli_real_escape_string($con, $_POST['delete']);

		//print_r($_POST);

		//$stmt = $mysqli->prepare("DELETE FROM carts WHERE book_id = ? AND user_id = 1");
		//$stmt->bind_param("si", $_POST['delete']);
		//$stmt->execute();
		//$stmt->close();

		$query = "DELETE FROM cart WHERE book_id = " . $book_id . " AND user_id IN (SELECT user_id FROM user WHERE token = " . $_SESSION['token'] .")";
		$result = mysqli_query($con, $query);
    echo mysqli_error($con);


	} else if(isset($_POST['save_for_later'])) {
		$book_id = mysqli_real_escape_string($con, $_POST['save_for_later']);

		//print_r($_POST);

		//$stmt = $mysqli->prepare("DELETE FROM carts WHERE book_id = ? AND user_id = 1");
		//$stmt->bind_param("si", $_POST['delete']);
		//$stmt->execute();
		//$stmt->close();


		//echo "<br>token = " . $_SESSION['token'] . "<br>";
		//echo "book_id = " . $book_id . "<br>";

		$query = "UPDATE cart SET saved_for_later = 1, qty = 1
				WHERE book_id = " . $book_id ."
				AND user_id IN (SELECT user_id FROM user WHERE token = " . $_SESSION['token'] .")";

		//echo "\n" . $query;

		$result = mysqli_query($con, $query);
    echo mysqli_error($con);

	} else if(isset($_POST['move_to_cart'])) {
		$book_id = mysqli_real_escape_string($con, $_POST['move_to_cart']);

		print_r($_POST);

		//$stmt = $mysqli->prepare("DELETE FROM carts WHERE book_id = ? AND user_id = 1");
		//$stmt->bind_param("si", $_POST['delete']);
		//$stmt->execute();
		//$stmt->close();

		$query = "UPDATE cart SET saved_for_later = 0 WHERE book_id = ". $book_id . " AND user_id IN (SELECT user_id FROM user WHERE token = " . $_SESSION['token'] .")";
		$result = mysqli_query($con, $query);
    echo mysqli_error($con);


	} else if(isset($_POST['change_qty'])) {
		$book_id = mysqli_real_escape_string($con, $_POST['change_qty']);
		$qty = mysqli_real_escape_string($con, $_POST['qty']);

		//print_r($_POST);

		//$stmt = $mysqli->prepare("DELETE FROM carts WHERE book_id = ? AND user_id = 1");
		//$stmt->bind_param("si", $_POST['delete']);
		//$stmt->execute();
		//$stmt->close();

		$query = "UPDATE cart SET qty = " . $qty . " WHERE book_id = ". $book_id . " AND user_id IN (SELECT user_id FROM user WHERE token = " . $_SESSION['token'] .")";
		$result = mysqli_query($con, $query);
    echo mysqli_error($con);
	}


	$query = "SELECT title, author, price, image_url, cart.qty, price, cart.book_id, saved_for_later\n"
			. "	FROM cart, book WHERE book.book_id = cart.book_id AND cart.user_id = 1";


	//$query = 'SELECT title, author, price, image_url FROM carts WHERE user_id = 1';
	// Get Result
	$result = mysqli_query($con, $query);


	// Fetch Data. Not supported in Yasmany's MySQL version
	// $books_on_cart = mysqli_fetch_all($result, MYSQLI_ASSOC);		// deleted

	$books_on_cart = array();
	
	while($row = mysqli_fetch_assoc($result)) {
		echo mysqli_error($con);
		array_push($books_on_cart, $row);
	}

	//var_dump($books);

	// Free Result
	mysqli_free_result($result);

	// Close Connection
	mysqli_close($con);

?>




<?php $subtotal = 0;
	  $num_items = 0;
?>


<br><br>


<br>


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
										<input type="hidden" name="delete" value="<?php echo $book['book_id']; ?>">
										<input type="submit" class="btn btn-outline-danger" value="Delete">
									</form>
								</div>
								<div class="col-sm-4">
									<form name="deleteForm" method="POST" action="cart.php">
											<input type="hidden" name="save_for_later" value="<?php echo $book['book_id']; ?>">
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
							<input type="hidden" id="custId" name="change_qty" value="<?php echo $book['book_id']; ?>">
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
										<input type="hidden" name="delete" value="<?php echo $book['book_id']; ?>">
										<input type="submit" class="btn btn-outline-danger" value="Delete">
									</form>
								</div>
								<div class="col-sm-4">
									<form name="deleteForm" method="POST" action="cart.php">
											<input type="hidden" name="move_to_cart" value="<?php echo $book['book_id']; ?>">
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
