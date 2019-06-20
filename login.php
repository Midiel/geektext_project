<?php
    session_start();
    $login_error = false;
    $new_account = false;

    /* if form submitted */
    if(isset($_POST['email']) && !empty($_POST['email'])
        && isset($_POST['password']) && !empty($_POST['password']))
    {
        require_once('includes/connect.inc.php');
        $email = $_POST['email'];
        $password = $_POST['password'];
        if ($stmt = mysqli_prepare($con, "SELECT email, password FROM user WHERE
            email = ?"))
        {
            /* bind parameters for markers */
            mysqli_stmt_bind_param($stmt, "s", $email);

            /* execute query */
            mysqli_stmt_execute($stmt);

            /* bind vars to columns */
            mysqli_stmt_bind_result($stmt, $db_email, $passhash); // instead: get_result to fetch_assoc

            /* fetch 1st row into vars */
            mysqli_stmt_fetch($stmt); // instead: while loop fetch_assoc

            /* close statement */
            mysqli_stmt_close($stmt);

            if (password_verify($password, $passhash)) // logged in
            {
                /* creating remember token */
                $token = md5(rand(10,100000));

                $query = "UPDATE user SET token = '$token' WHERE
                    email = '$db_email'";
                mysqli_query($con, $query);

                $_SESSION['token'] = $token;
                $_SESSION['email'] = $db_email;

                /* redirect browser */
                header('Location: index.php');
                exit();
            }
            else /* login detail error */
            {
                $login_error = true;
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
    <link rel="stylesheet" href="css/geektext-lr.css">
</head>

<body>
    <h1 style="text-align: center;" alt="logo placeholder">GeekText</h1>
    <div class="geektext-lr geektext-lr-error" style="display: <?php echo ($login_error) ? 'block' : 'none'; ?>;">
        <h2 style="float: left; padding: 10px 20px; margin:0px;" alt="err icon placeholder">!</h2>
        <h4>There was a problem</h4>
        <p>Invalid username or password</p>
    </div>
    <div class="geektext-lr geektext-lr-ok" style="display: <?php echo ($new_account) ? 'block' : 'none'; ?>;">
        <h3 style="float: left; padding: 10px 16px; margin:0px; alt="reg icon placeholder">OK</h3>
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
