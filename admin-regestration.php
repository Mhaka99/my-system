<?php
    require 'connection.php';
    
    if (isset($_POST['submit'])) {
        $user = $_POST['user'];
        $pass = $_POST['pass'];

        // File upload handling
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "./image/";
            $target_file = $target_dir . basename($_FILES['photo']['name']);
            
            // Check if the file is uploaded without errors
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                $photo_path = $target_file;
            } else {
                die("Failed to upload the file.");
            }
        } else {
            die("No file was uploaded or there was an upload error.");
        }

        // Prepare and execute the SQL query
        $sql = "INSERT INTO `admin` (user, pass, photo)
                VALUES ('$user', '$pass', '$photo_path')";

        if (mysqli_query($conn, $sql)) {
            echo '<script>alert("Admin Registered Successfully")</script>';
        } else {
            echo "Failed to register: " . mysqli_error($conn);
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrration Form</title>
    <link rel="stylesheet" href="./css/userreg.css" >
</head>
<body>

    <form class="regform" action="" method="post" enctype="multipart/form-data">

 
        <div class="div4">
        <input type="file" name="photo" class="int" required> 
        <a>Account Details</a>
            <input class="int" name="user" placeholder="Username"required> 
            <input class="int" type="password" name="pass" placeholder="Password"required>
            <button class="regbutton" type="submit" name="submit" href="index.php">Register</button>
            <a class="adminbutton" href="admin.php">__________Go Back Login_______</a>
        </div>
    </form>


</body>
</html>