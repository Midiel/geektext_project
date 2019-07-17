<?php
    session_start();
	if (empty($_SESSION['user_id']) || empty($_SESSION['token']))
	{
		// not logged in, redirect
		$_SESSION['not_logged_in'] = true;
        header('Location: login.php');
        exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Account</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include("includes/navbar_libs.php"); ?>
    <link rel="stylesheet" href="css/geektext-account.css">
</head>
<body>
	<?php include('includes/navbar.php');?>
	<div class="row" id="menu-container">
		<div class="col-lg-4">
	    	<a href="personal.php" class="card">
	      	<span class="card-body">
	      		<img class="geektext-img" src="images/account-user.svg">
	        	<h4>Personal & Security</h4>
	      	</span>
	    	</a>
	  	</div>
		<div class="col-lg-4">
	    	<a href="address.php" class="card">
	      	<span class="card-body">
	      		<img class="geektext-img" src="images/account-mailbox.svg">
	        	<h4>Addresses</h4>
	      	</span>
	    	</a>
	  	</div>
		<div class="col-lg-4">
	    	<a href="paymethods.php" class="card">
	      	<span class="card-body">
	      		<img class="geektext-img" src="images/account-cc.svg">
	        	<h4>Payment Methods</h4>
	      	</span>
	    	</a>
	  	</div>
	</div>
</body>
</html>