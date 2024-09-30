<?php
  include("connection.php");
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <link rel="stylesheet" href="./css/index-admin.css">

</head>
<body>
    <form class="login_form" action="" method="post" >
        <div class="formname">
            Welcome User!
        </div>

        <div class="userholder">
            <input class="user-input" placeholder="Enter Username" name="user" id="user">
        </div>

        <div class="passholder">
            <input class="pass-input" placeholder="Enter password" name="pass" id="pass">
        </div>

        <div class="login-holder">
            <button class="LoginButton" type="submit" name="submit">Login</button>
            <a class="regButton" href="user-regestration.php">Register</a>
        </div>
        
        <div class="adminholder">
            <label class="clickhere">Login as Admin? Click Here </label>
                <a class="adminbutton" href="admin.php" >Admin Login</a>
        </div>
    </form>

    <?php
    require 'connection.php';

if (isset($_POST['submit'])) {
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);

    $sql = "SELECT * FROM `user` WHERE `user` = '$user' AND `pass` = '$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user'] = $user;
        header("Location: user-main.php");
    } else {
        echo "<script>alert('Wrong Username or Password');</script>";
    }
}
?>







</form>
</body>
</html>