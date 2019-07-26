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
if (!empty($_POST['card_id']))
{
	$stmt = "";
	$new = false;
	$delete = false;

	if ($_POST['card_id'] === "new" && !empty($_POST['number']) 
		&& !empty($_POST['cardholder']) && !empty($_POST['exp_month']) 
		&& !empty($_POST['exp_year']) && !empty($_POST['security_code'])
		&& !empty($_POST['type']))
	{	
		// insert new card
		$new = true;
		if (empty($_POST['nickname']))
		{ 
			$_POST['nickname'] = "";
		}
		
		$stmt = mysqli_prepare($con, "INSERT INTO credit_card (user_id, number, nickname, exp_month, exp_year, security_code, type, cardholder) VALUES (" . $db_user_id . ", ?, ?, ?, ?, ?, ?, ?)");
	    mysqli_stmt_bind_param($stmt, "sssssss", $_POST['number'], $_POST['nickname'], $_POST['exp_month'], $_POST['exp_year'], $_POST['security_code'], $_POST['type'], $_POST['cardholder']);
	}
	else if (!empty($_POST['action']) && $_POST['action'] === "delete")
	{
		// delete selected card
		$delete = true;
		$stmt = mysqli_prepare($con, "DELETE FROM credit_card WHERE user_id = '$db_user_id' AND card_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $_POST['card_id']);
	}

	$success = "";
	$error = "";

	// produce dialog messages depending on action
	if (mysqli_stmt_execute($stmt))
	{
		if ($new)
		{
			$success = "The new payment method has been added to your records";
		}
		else if ($delete)
		{
			$success = "The selected payment method has been deleted successfully";
		}
	}
	else
	{
		if ($new)
		{
			$error = "The new payment method could not be added to your records";
		}
		else if ($delete)
		{
			$error = "The selected payment method could not be deleted from your records";
		}
	}
    mysqli_stmt_close($stmt);
}

// fetch all cards under this ID
$stmt = mysqli_query($con, "SELECT * FROM credit_card WHERE user_id = '$db_user_id'");
$cards = mysqli_fetch_all($stmt, MYSQLI_ASSOC);
mysqli_free_result($stmt);

// card icon array
$card_icon_arr = [
	"Amex" => "fa-cc-amex",
	"Visa" => "fa-cc-visa",
	"MasterCard" => "fa-cc-mastercard",
	"Discover" => "fa-cc-discover",
	"Diner's Club" => "fa-cc-diners-club",
	"JCB" => "fa-cc-jcb",
	"Card" => "fa-credit-card"
];
?>
<!DOCTYPE html>
<html>
<head>
	<title>My Payment Methods</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php include("includes/navbar_libs.php"); ?>
	<link rel="stylesheet" href="css/geektext-pac.css">
	<link rel="stylesheet" href="css/geektext-ac.css">
	<style type="text/css">
		form .col-1 {
			padding-left: 0px;
		}
		.row .font-weight-bold {
			white-space: nowrap;
		}
	</style>
</head>
<body>
	<?php include('includes/navbar.php');?>
	<div class="geektext-container">
		<h3 class="geektext-title">Payment Methods</h3>
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
		<?php foreach ($cards as $card): ?>
		<div class="card">
			<div class="row">
				<div class="col-md-8">
					<h4><i class="fa <?php echo $card_icon_arr[$card['type']]; ?>" aria-hidden="true"></i><?php
						$nickname = "";
						if (!empty($card['nickname']))
						{
							$nickname = " \"" . $card['nickname'] . "\"";
						}

						echo " " . $card['type'] . " " . substr($card['number'], -4) . $nickname; 
						?></h4>
					<div class="card-element">Cardholder Name: <?php echo $card['cardholder']; ?></div>
					<div class="card-element">Exp. Date: <?php echo $card['exp_month']. "/" . substr($card['exp_year'], -2); ?></div>
				</div>
				<form class="col-md-4 d-flex align-items-start justify-content-start justify-content-md-end" method="post">
					<button type="submit" class="btn btn-outline-danger geektext-btn" name="card_id" value="<?php echo $card['card_id']; ?>">Delete</button>
					<input type="hidden" name="action" value="delete">
				</form>
			</div>
		</div>
		<?php endforeach; ?>
		<form class="card hidden" id="new-card" method="post">
			<h4>New Credit/Debit Card</h4>
			<div class="font-weight-bold">Card Number:</div>
			<div class="row">
				<div class="col-11">
					<input id="card-number" type="text" class="form-control" name="number" maxlength="30" pattern="[0-9.]+" autocomplete="off" required>
				</div>
				<div class="col-1">
					<i id="card-icon" class="fa fa-2x" aria-hidden="true"></i>
					<input id="card-type" type="hidden" name="type" value="">
				</div>
			</div>
			<div class="font-weight-bold">Cardholder Name:</div>
			<input type="text" class="form-control" name="cardholder" maxlength="100" required>
			<div class="font-weight-bold">Card Nickname (Optional):</div>
			<input type="text" class="form-control" name="nickname" maxlength="100">
			<div class="row">
				<div class="col-sm-8">
					<div class="font-weight-bold">Exp. Month:</div>
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
				<div class="col-sm-2">
					<div class="font-weight-bold">Exp. Year:</div>
					<select class="form-control" name="exp_year">
					<?php 
					for ($year = (int)date("Y"); $year < ((int)date("Y") + 12); $year++)
					{
    					echo '<option value="' . $year . '">' . $year . '</option>';
					} 
					?>
					</select>
				</div>
				<div class="col-sm-2">
					<div class="font-weight-bold">Security Code:</div>
					<input type="text" class="form-control" name="security_code" pattern="[0-9.]+" maxlength="5" autocomplete="off" required>
				</div>
			</div>
			<div>
				<button type="submit" class="btn btn-outline-success geektext-btn geektext-btn-submit" name="card_id" value="new">Submit</button>
				<button type="button" class="btn btn-outline-danger geektext-btn" id="new-cancel">Cancel</button>
			</div>
		</form>
		<div class="card geektext-add-card" id="add-card">
			<div class="geektext-add-icon">+</div>
			<div class="geektext-add-subtext">Add Payment Method</div>
		</div>
	</div>
	<script src="js/paymethods.js"></script>
</body>
</html>