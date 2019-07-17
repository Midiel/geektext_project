<?php 
session_start();

// check if session properly initialized
if (empty($_SESSION['user_id']) || empty($_SESSION['token']))
{
	// not logged in, redirect
	$_SESSION['not_logged_in'] = true;
    header('Location: login.php');
    exit();
}


// check session token against db token
require_once('includes/connect.inc.php');
$stmt = mysqli_prepare($con, "SELECT user_id, token FROM user WHERE user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $db_user_id, $db_token);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (empty($db_token) || $db_token !== $_SESSION['token'])
{
	// not logged in, redirect
	$_SESSION['not_logged_in'] = true;
    header('Location: login.php');
    exit();
}

// check if user POSTed
if (!empty($_POST['address_id']))
{
	$stmt = "";
	$edit = false;
	$new = false;
	$delete = false;

	if (!empty($_POST['name']) && !empty($_POST['street_address']) 
		&& !empty($_POST['state']) && !empty($_POST['city']) 
		&& !empty($_POST['zip_code']) && !empty($_POST['country']) 
		&& !empty($_POST['primary_phone']))
	{
		// remove non-numeric characters from phone #
		$_POST['primary_phone'] = preg_replace("/[^0-9]/", "", $_POST['primary_phone']);
		
		if ($_POST['address_id'] === "new")
		{
			// insert new address
			$new = true;
			$stmt = mysqli_prepare($con, "INSERT INTO address (user_id, name, street_address, state, city, zip_code, country, primary_phone) VALUES (" . $db_user_id . ", ?, ?, ?, ?, ?, ?, ?)");
	        mysqli_stmt_bind_param($stmt, "sssssss", $_POST['name'], $_POST['street_address'], $_POST['state'], $_POST['city'], $_POST['zip_code'], $_POST['country'], $_POST['primary_phone']);
		}
		else
		{
			// update selected address
			$edit = true;
			$stmt = mysqli_prepare($con, "UPDATE address SET name = ?, street_address = ?, state = ?, city = ?, zip_code = ?, country = ?, primary_phone = ? WHERE user_id = '$db_user_id' AND address_id = ?");
	        mysqli_stmt_bind_param($stmt, "sssssssi", $_POST['name'], $_POST['street_address'], $_POST['state'], $_POST['city'], $_POST['zip_code'], $_POST['country'], $_POST['primary_phone'], $_POST['address_id']);
		}
	}
	else if (!empty($_POST['action']) && $_POST['action'] === "delete")
	{
		// delete selected address
		$delete = true;
		$stmt = mysqli_prepare($con, "DELETE FROM address WHERE user_id = '$db_user_id' AND address_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $_POST['address_id']);
	}

	$success = "";
	$error = "";

	// produce dialog messages depending on action
	if (mysqli_stmt_execute($stmt))
	{
		if ($edit)
		{
			$success = "The selected address was updated successfully";
		}
		else if ($new)
		{
			$success = "The new address has been added to your records";
		}
		else if ($delete)
		{
			$success = "The selected address has been deleted successfully";
		}
	}
	else
	{
		if ($edit)
		{
			$error = "The selected address could not be updated";
		}
		else if ($new)
		{
			$error = "The new address could not be added to your records";
		}
		else if ($delete)
		{
			$error = "The selected address could not be deleted from your records";
		}
	}
    mysqli_stmt_close($stmt);
}

// fetch all addresses under this ID
$stmt = mysqli_query($con, "SELECT * FROM address WHERE user_id = '$db_user_id'");
$addresses = mysqli_fetch_all($stmt, MYSQLI_ASSOC);
mysqli_free_result($stmt);

// import code => country array
include('includes/countries.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>My Addresses</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php include("includes/navbar_libs.php"); ?>
	<link rel="stylesheet" href="css/geektext-pac.css">
	<link rel="stylesheet" href="css/geektext-ac.css">
</head>
<body>
	<?php include('includes/navbar.php');?>
	<div class="geektext-container">
		<h3 class="geektext-title">Addresses</h3>
		<div class="geektext-dialog geektext-error" style="display: <?php echo empty($error) ? 'none' : 'block'; ?>;">
			<i class="fa fa-exclamation-triangle fa-2x geektext-dialog-icon" aria-hidden="true"></i>
	        <h5>There was a problem</h5>
	        <div><?php echo $error;?></div>
		</div>
		<div class="geektext-dialog" style="display: <?php echo empty($success) ? 'none' : 'block'; ?>;">
			<i class="fa fa-check-square-o fa-2x geektext-dialog-icon" aria-hidden="true"></i>
	        <h5>Change successful</h5>
	        <div><?php echo $success;?></div>
		</div>
		<?php foreach ($addresses as $address): ?>
		<div class="card">
			<div class="row">
				<div class="col-md-8">
					<h4><?php echo $address['name']; ?></h4>
					<div class="card-element"><?php echo $address['street_address']; ?></div>
					<div class="card-element"><?php echo $address['city'] . ", " . $address['state'] . " " . $address['zip_code']; ?></div>
					<div class="card-element"><?php echo $countries[$address['country']]; ?></div>
					<div class="card-element">Phone: <?php echo $address['primary_phone']; ?></div>
				</div>
				<form class="col-md-4 d-flex align-items-start justify-content-start justify-content-md-end" method="post">
					<button type="button" class="btn btn-outline-primary geektext-btn geektext-edit geektext-btn-submit">Edit</button>
					<button type="submit" class="btn btn-outline-danger geektext-btn" name="address_id" value="<?php echo $address['address_id']; ?>">Delete</button>
					<input type="hidden" name="action" value="delete">
				</form>
			</div>
			<form class="row hidden" method="post">
				<div class="col-md-12">
					<h4>Edit Address</h4>
					<div class="font-weight-bold">Name:</div>
					<input type="text" class="form-control" name="name" value="<?php echo $address['name']; ?>" maxlength="100" required>
					<div class="font-weight-bold">Street Address:</div>
					<input type="text" class="form-control" name="street_address" value="<?php echo $address['street_address']; ?>" maxlength="100" required>
					<div class="font-weight-bold">City:</div>
					<input type="text" class="form-control" name="city" value="<?php echo $address['city']; ?>" maxlength="40" required>
					<div class="font-weight-bold">State/Province/Region:</div>
					<input type="text" class="form-control" name="state" value="<?php echo $address['state']; ?>" maxlength="40" required>
					<div class="font-weight-bold">ZIP:</div>
					<input type="text" class="form-control" name="zip_code" value="<?php echo $address['zip_code']; ?>" maxlength="10" required>
					<div class="font-weight-bold">Country/Region:</div>
					<select class="form-control" name="country">
					<?php 
					foreach ($countries as $code => $country)
					{
						if ($address['country'] === $code)
						{
							echo '<option value="' . $code . '" selected="selected">' . $country . '</option>';
						}
						else
						{
							echo '<option value="' . $code . '">' . $country . '</option>';
						}
					}
					?>
					</select>
					<div class="font-weight-bold">Phone:</div>
					<input type="text" class="form-control" name="primary_phone" value="<?php echo $address['primary_phone']; ?>" maxlength="20" required>
					<button type="submit" class="btn btn-outline-success geektext-btn geektext-btn-submit" name="address_id" value="<?php echo $address['address_id']; ?>">Submit</button>
					<button type="button" class="btn btn-outline-danger geektext-btn geektext-edit">Cancel</button>
				</div>
			</form>
		</div>
		<?php endforeach; ?>
		<div class="card hidden" id="new-address">
			<form class="row" method="post">
				<div class="col-md-12">
					<h4>New Address</h4>
					<div class="font-weight-bold">Name:</div>
					<input type="text" class="form-control" name="name" maxlength="100" required>
					<div class="font-weight-bold">Street Address:</div>
					<input type="text" class="form-control" name="street_address" maxlength="100" required>
					<div class="font-weight-bold">City:</div>
					<input type="text" class="form-control" name="city" maxlength="40" required>
					<div class="font-weight-bold">State/Province/Region:</div>
					<input type="text" class="form-control" name="state" maxlength="40" required>
					<div class="font-weight-bold">ZIP:</div>
					<input type="text" class="form-control" name="zip_code" maxlength="10" required>
					<div class="font-weight-bold">Country/Region:</div>
					<select class="form-control" name="country">
					<?php 
					foreach ($countries as $code => $country)
					{
						echo '<option value="' . $code . '">' . $country . '</option>';
					}
					?>
					</select>
					<div class="font-weight-bold">Phone:</div>
					<input type="text" class="form-control" name="primary_phone" maxlength="20" required>
					<button type="submit" class="btn btn-outline-success geektext-btn geektext-btn-submit" name="address_id" value="new">Submit</button>
					<button type="button" class="btn btn-outline-danger geektext-btn" id="new-cancel">Cancel</button>
				</div>
			</form>
		</div>
		<div class="card geektext-add-card" id="add-address">
			<div class="geektext-add-icon">+</div>
			<div class="geektext-add-subtext">Add Address</div>
		</div>
	</div>
	<script src="js/address.js"></script>
</body>
</html>