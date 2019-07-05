<?php
    session_start();
    $login_error = "";
    $new_account = false;

    /* if form submitted */
    if(isset($_POST['email']) && !empty($_POST['email'])
        && isset($_POST['password']) && !empty($_POST['password']))
    {
        require_once('includes/connect.inc.php');
        $email = $_POST['email'];
        $password = $_POST['password'];
        if ($stmt = mysqli_prepare($con, "SELECT user_id, email, password FROM user WHERE
            email = ? LIMIT 1"))
        {
            /* bind parameters for markers */
            mysqli_stmt_bind_param($stmt, "s", $email);

            /* execute query */
            mysqli_stmt_execute($stmt);

            /* bind vars to columns */
            mysqli_stmt_bind_result($stmt, $db_user_id, $db_email, $passhash); // instead: get_result to fetch_assoc

            /* fetch 1st row into vars */
            mysqli_stmt_fetch($stmt); // instead: while loop fetch_assoc

            /* close statement */
            mysqli_stmt_close($stmt);

            if (password_verify($password, $passhash)) // logged in
            {
                /* creating remember token */
                $token = md5(rand(10,100000));

                $query = "UPDATE user SET token = '$token' WHERE
                    email = '$db_email'";                           // Midiel: Maybe change to user_id?
                mysqli_query($con, $query);

                $_SESSION['token'] = $token;
                $_SESSION['email'] = $db_email;
                $_SESSION['user_id'] = $db_user_id;

                /* redirect browser */
                header('Location: index.php');
                exit();
            }
            else /* login detail error */
            {
                $login_error = "Invalid username or password";
            }
        } // else stmt prep failure
    }
    else if (isset($_SESSION['new_account']) && $_SESSION['new_account']) /* if new account */
    {
        unset($_SESSION['new_account']);
        $new_account = true;
    }
    else /* log out user */
    {
        if (isset($_SESSION['not_logged_in']) && $_SESSION['not_logged_in'])
        {
            $login_error = "Please login to continue";
        }
        $_SESSION = array();
        session_destroy();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>GeekText Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/geektext-lr.css">
</head>

<body>
    <h1 style="text-align: center;" alt="logo placeholder">GeekText</h1>
    <div class="geektext-lr geektext-dialog geektext-error" style="display: <?php echo empty($login_error) ? 'none' : 'block' ?>;">
        <i class="fa fa-exclamation-triangle fa-2x geektext-dialog-icon" aria-hidden="true"></i>
        <h4>There was a problem</h4>
        <p><?php echo $login_error; ?></p>
    </div>
    <div class="geektext-lr geektext-dialog geektext-success" style="display: <?php echo ($new_account) ? 'block' : 'none'; ?>;">
        <i class="fa fa-check-square-o fa-2x geektext-dialog-icon" aria-hidden="true"></i>
        <h4>Account created</h4>
        <p>Login with your new account</p>
    </div>
    <form method="post" class="container geektext-lr">
        <h3 style="padding-bottom: 5px">Sign in</h3>
        <div class="form-group">
            <label>Email</label>
            <input class="form-control" name="email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign in</button>
        <div style="padding-top: 16px; padding-bottom: 4px">New user?</div>
        <a href="register.php" type="submit" class="btn btn-default btn-block">Create new account</a>
    </form>
</body>

</html>
