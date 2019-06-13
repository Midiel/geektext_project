<?php
    /* clear session (i.e. soft log out) */
    session_start();
    $_SESSION = array();
    // session_destroy();

    $email_exists = false;

    /* if form submitted */
    if(isset($_POST['email']) && !empty($_POST['email'])
        && isset($_POST['password']) && !empty($_POST['password'])
        && isset($_POST['name']) && !empty($_POST['name']))
    {
        require_once('includes/connect.inc.php');
        $email = $_POST['email'];
        $password = $_POST['password'];     //need to encrypt it
        $name = $_POST['name'];

        /* check if email exists */
        if ($stmt = mysqli_prepare($con, "SELECT email FROM users WHERE
            email = ?"))
        {
            /* sql manipulation */
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $db_email);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            /* if not in db, "register" new user */
            if (empty($db_email) && $stmt = mysqli_prepare($con, "INSERT INTO
                users (email, fname, lname, nickname, password) VALUES (?, ?, ?, ?, ?)"))
            {
                /* split name */
                $names = explode(" ", $name);
                $nickname = $names[0];
                $fname = $names[0];
                $lname = "";

                if (isset($names[1])) //if lname exists
                {
                    $names = array_slice($names, 1); // remove fname
                    $lname = implode(" ", $names);
                }

                /* insert user row into db */
                mysqli_stmt_bind_param($stmt, "sssss", $email, $fname, $lname, $nickname, $password);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                /* redirect to login */
                $_SESSION['new_account'] = true;
                header('Location: login.php');
                exit();
            }
            else
            {
                $email_exists = true;
            }
        } // else stmt prep failure
    }// else don't register, fail silently as input check handled in front end
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>GeekText Registration</title>
    <?php include("includes/head-data.php");?>
    <link rel="stylesheet" href="css/geektext-lr.css">
</head>

<body>
    <h1 style="text-align: center;" alt="logo placeholder">GeekText</h1>
    <div class="geektext-lr geektext-lr-error" style="display: <?php echo ($email_exists) ? 'block' : 'none'; ?>;">
        <h2 style="float: left; padding: 10px 20px; margin:0px;" alt="err icon placeholder">!</h2>
        <h4>There was a problem</h4>
        <p>An account with this email already exists</p>
    </div>
    <form method="post" action="register.php" class="container geektext-lr">
        <h3 style="padding-bottom: 5px">Create account</h3>
        <div class="form-group">
            <label>Your name</label>
            <input class="form-control" name="name">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input class="form-control" type="email" name="email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control">
        </div>
        <!-- <p>password notes</p> -->
        <div class="form-group">
            <label>Re-enter password</label>
            <input type="password" class="form-control" name="password">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Create your GeekText account</button>
        <div style="padding-top: 16px; padding-bottom: 4px">Already have an account? <a href="login.php">Sign in</a></div>
    </form>
</body>

</html>
