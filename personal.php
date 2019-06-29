<?php
	session_start();

	// check if session properly initialized
	if (empty($_SESSION['user_id']) || empty($_SESSION['token']))
	{
		// not logged in, redirect
        header('Location: login.php');
        exit();
	}


	// check against login against and session token
	require_once('includes/connect.inc.php');
	$stmt = mysqli_prepare($con, "SELECT * FROM user WHERE user_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_array(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    $user['name'] = $user['f_name'] . " " . $user['l_name'];
    mysqli_stmt_close($stmt);

    if (empty($user) || $user['token'] !== $_SESSION['token'])
    {
		// not logged in, redirect
        header('Location: login.php');
        exit();
    }

    // POST status vars
    $error = "";
    $success = "";

	// check if user is POSTing
	if (!empty($_POST['action']))
	{
		$stmt = "";

		if ($_POST['action'] === "name")
		{
		    /* split name */
            $names = explode(" ", $_POST['name']);
            $fname = $names[0];
            $lname = "";

            if (isset($names[1])) //if lname exists
            {
                $names = array_slice($names, 1); // remove fname
                $lname = implode(" ", $names);
            }

            $stmt = mysqli_prepare($con, "UPDATE user SET f_name = ?, l_name = ? WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $fname, $lname, $_SESSION['user_id']);
		}
		else if ($_POST['action'] === "nickname")
		{
			$stmt = mysqli_prepare($con, "UPDATE user SET nickname = ? WHERE user_id = ?");
			mysqli_stmt_bind_param($stmt, "si", $_POST['nickname'], $_SESSION['user_id']);

		}
		else if ($_POST['action'] === "email")
		{
			$stmt = mysqli_prepare($con, "UPDATE user SET email = ? WHERE user_id = ?");
			mysqli_stmt_bind_param($stmt, "si", $_POST['email'], $_SESSION['user_id']);
		}
		else if ($_POST['action'] === "password")
		{
			/* hash password */
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
			$stmt = mysqli_prepare($con, "UPDATE user SET password = '$password' WHERE user_id = ?");
			mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
		}

		if(mysqli_stmt_execute($stmt)) // on successful insert
		{
			// show success dialog
			$success = ucfirst($_POST['action']) . " updated successfully";

			// update user detail view
			$user[$_POST['action']] = $_POST[$_POST['action']];
		}
		else // else bad insert
		{
			// show error dialog
			$error = "The new " . $_POST['action'] . " you have entered is not valid";
		}
		mysqli_stmt_close($stmt);
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Personal & Account</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/geektext-personal.css">
</head>
<body>
	<?php //include('includes/navbarm.php');?>
	<div class="geektext-container">
		<h3 class="geektext-title">Personal information & security</h3>
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
		<div class="card">
			<div class="geektext-entry">
				<div class="row">
					<div class="col-md-10">
						<div class="font-weight-bold">Name:</div>
						<div><?php echo $user["name"];?></div>					
					</div>
					<div class="col-md-2 d-flex align-items-center justify-content-start justify-content-md-end">
						<button type="button" class="btn btn-outline-primary geektext-btn geektext-edit">Edit</button>
					</div>
				</div>
				<form class="row hidden" method="post">
					<div class="col-md-8">
						<div class="font-weight-bold">New Name:</div>
						<input type="text" class="form-control" name="name" value="<?php echo $user["name"];?>">
					</div>
					<div class="col-md-4 d-flex align-items-center justify-content-start justify-content-md-end">
						<button type="submit" class="btn btn-outline-success geektext-btn geektext-btn-submit" name="action" value="name">Submit</button>
						<button type="button" class="btn btn-outline-danger geektext-btn geektext-edit">Cancel</button>
					</div>
				</form>
			</div>
			<div class="geektext-entry">
				<div class="row">
					<div class="col-md-10">
						<div class="font-weight-bold">Nickname:</div>
						<div><?php echo $user["nickname"];?></div>					
					</div>
					<div class="col-md-2 d-flex align-items-center justify-content-start justify-content-md-end">
						<button type="button" class="btn btn-outline-primary geektext-btn geektext-edit">Edit</button>
					</div>
				</div>
				<form class="row hidden" method="post">
					<div class="col-md-8">
						<div class="font-weight-bold">New Nickname:</div>
						<input type="text" class="form-control" name="nickname" value="<?php echo $user["nickname"];?>">
					</div>
					<div class="col-md-4 d-flex align-items-center justify-content-start justify-content-md-end">
						<button type="submit" class="btn btn-outline-success geektext-btn geektext-btn-submit" name="action" value="nickname">Submit</button>
						<button type="button" class="btn btn-outline-danger geektext-btn geektext-edit">Cancel</button>
					</div>
				</form>
			</div>
			<div class="geektext-entry">
				<div class="row">
					<div class="col-md-10">
						<div class="font-weight-bold">E-mail:</div>
						<div><?php echo $user["email"];?></div>					
					</div>
					<div class="col-md-2 d-flex align-items-center justify-content-start justify-content-md-end">
						<button type="button" class="btn btn-outline-primary geektext-btn geektext-edit">Edit</button>
					</div>
				</div>
				<form class="row hidden" method="post">
					<div class="col-md-8">
						<div class="font-weight-bold">New E-mail:</div>
						<input type="email" class="form-control" name="email" value="<?php echo $user["email"];?>">
					</div>
					<div class="col-md-4 d-flex align-items-center justify-content-start justify-content-md-end">
						<button type="submit" class="btn btn-outline-success geektext-btn geektext-btn-submit" name="action" value="email">Submit</button>
						<button type="button" class="btn btn-outline-danger geektext-btn geektext-edit">Cancel</button>
					</div>
				</form>
			</div>
			<div class="geektext-entry">
				<div class="row">
					<div class="col-md-10">
						<div class="font-weight-bold">Password:</div>
						<div>********</div>					
					</div>
					<div class="col-md-2 d-flex align-items-center justify-content-start justify-content-md-end">
						<button type="button" class="btn btn-outline-primary geektext-btn geektext-edit">Edit</button>
					</div>
				</div>
				<form class="row hidden" method="post" id="password-form">
					<div class="col-md-4">
						<div class="font-weight-bold">New Password:</div>
						<input type="password" class="form-control" id="password" name="password">
					</div>
					<div class="col-md-4 password-check">
			            <div><i id="sixlen-check" class="fa fa-circle geektext-icon"></i>At least 6 characters</div>
			            <div><i id="upperlower-check" class="fa fa-circle geektext-icon"></i>Upper/lowercase letters</div>
			            <div><i id="numpunc-check" class="fa fa-circle geektext-icon"></i>Number or punctuation</div>
					</div>
					<div class="col-md-4 d-flex align-items-center justify-content-start justify-content-md-end">
						<button type="submit" class="btn btn-outline-success geektext-btn geektext-btn-submit" name="action" value="password">Submit</button>
						<button type="button" class="btn btn-outline-danger geektext-btn geektext-edit">Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script src="js/personal.js"></script>
</body>
</html>