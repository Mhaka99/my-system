<?php
    require 'connection.php';
    
    if (isset($_POST['submit'])) {
        // Collect form data
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $mi = $_POST['mi'];
        $birthdate = $_POST['birthdate'];
        $sex = $_POST['sex'];
        $marital_status = $_POST['marital_status'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $employee_status = $_POST['employee_status'];
        $m_income = $_POST['m_income'];
        $add_income = $_POST['add_income'];
        $m_expenses = $_POST['m_expenses'];
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
        $sql = "INSERT INTO `user` (fname, lname, mi, birthdate, sex, marital_status, address, phone, email, employee_status, m_income, add_income, m_expenses, user, pass, photo)
                VALUES ('$fname', '$lname', '$mi', '$birthdate', '$sex', '$marital_status', '$address', '$phone', '$email', '$employee_status', '$m_income', '$add_income', '$m_expenses', '$user', '$pass', '$photo_path')";

        if (mysqli_query($conn, $sql)) {
            echo '<script>alert("Registered Successfully")</script>';
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

        <div class="div1">
        <a>Personal Information</a>
            <input class="int" name="fname" placeholder="First Name" required>
            <input class="int" name="lname" placeholder="Last Name" required> 
            <input class="int" name="mi" placeholder="Middle Initial" required> 
            <input class="int" name="exname"placeholder="Extention (Optional)" required>
            <a>Birth Date</a>
            <input class="bd"type="date" id="birthday" name="birthdate" required>
            <select class="sex" required name="sex">
              <option>Male</option>
              <option>Female</option>
            </select>

        </div>

        <div class="div2">
        <a>Contact Details</a>
        <select class="int" required name="marital_status">
              <option>Single</option>
              <option>Married</option>
            </select>
            <input class="int" name="address" placeholder="Residential Address" required> 
            <input class="int" name="phone" placeholder="Phone Number" required> 
            <input class="int" name="email"placeholder="Email Address" required> 
            <a>Upload Valid ID</a>
            <input type="file" name="photo" class="int" required> 
        </div>

        <div class="div3">
       <a> Employment and Income Information</a>
            <select class="int" name="employee_status" placeholder="Employment Status">
                <option>Self Employed</option>
                <option>Permanent Employees</option>
                <option>Casual</option>
                <option>Part-Time Employee</option>
                <option>Temporary Worker</option>
                <option>Contract Worker</option>
                <option>Freelancer</option>
                <option>Intern</option>
                <option>Casual Worker</option>
                <option>Permanent Employee</option>
                <option>On-Call Worker</option>
                <option>Remote Worker</option>
                <option>Gig Worker</option>
            </select>
            <input class="int" class="m_income" name="m_income" placeholder="Monthly/Annual Income"required> 
            <input class="int" class="add_income" name="add_income" placeholder="Additional Sources of Income"required> 
            <input class="int" class="m_expenses" name="m_expenses" placeholder="Monthly Expenses"required> 
        </div>
 
        <div class="div4">
        <a>Account Details</a>
            <input class="int" name="user" placeholder="Username"required> 
            <input class="int" type="password" name="pass" placeholder="Password"required>
            <button class="regbutton" type="submit" name="submit" href="index.php">Register</button>
            <a class="adminbutton" href="index.php">__________Go Back Login_______</a>
        </div>
    </form>


</body>
</html>