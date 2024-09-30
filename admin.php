<?php
session_start(); // Ensure the session is started

// Include the database connection file
include 'connection.php'; // Adjust the path if necessary

if (isset($_POST['sub'])) { // Check if the form is submitted
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    // Escape user input to prevent SQL injection
    $user = mysqli_real_escape_string($conn, $user);
    $pass = mysqli_real_escape_string($conn, $pass);

    // Prepare and execute the SQL statement
    $sql = "SELECT * FROM admin WHERE user = '$user' AND pass = '$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        // Set the session variable and redirect
        $_SESSION['user'] = $user;
        header("Location: admin-main.php");
        exit(); // Ensure no further code is executed after redirect
    } else {
        echo "<script>alert('Wrong Username or Password');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="./css/index-admin.css">
</head>
<body>
    <form class="login_form" action="" method="post">
        <div class="formname">
            Welcome Admin!
        </div>

        <div class="userholder">
            <input class="user-input" placeholder="Enter Username" name="user" id="user" required>
        </div>

        <div class="passholder">
            <input class="pass-input" placeholder="Enter Password" name="pass" id="pass" required>
        </div>

        <div class="login-holder">
            <button class="LoginButton2" type="submit" name="sub">Login</button> 
        </div>
        
        <div class="adminholder">
            <label class="clickhere">Login as User? Click Here</label>
            <a class="adminbutton" href="index.php">User Login</a>
            
        </div>
    </form>
</body>
</html>
