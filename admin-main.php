<?php
require 'connection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: admin.php"); // Redirect to login page if not logged in
    exit();
}

// Get the username from session
$username = $_SESSION['user'];

// Fetch user information based on the logged-in username
$sql = "SELECT * FROM `admin` WHERE `user` = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-ui</title>
    <link rel="stylesheet" href="admin-main.css">
</head>
<body>
    <?php if ($row = mysqli_fetch_assoc($result)): ?>
    <div class="header">
        <div class="profile">
        <?php if ($row['photo']): ?>
                        <img class="profile_pic" src="<?php echo htmlspecialchars($row['photo']); ?>" alt="User Photo" width="150px" height="120px" style="border-radius: 50px; border: 3px solid white; margin-top: 25px; margin-left: 20px;">
                    <?php else: ?>
                        No Photo
                    <?php endif; ?>
        </div>
        <div class="tittle">
                        <div class="user_fullname">
                            <h2><?php echo htmlspecialchars($row['user']);?>
                        </div>
   
    
        </div>
        <div class="logout">
            <a href="admin.php" class="logoutbutton">Logout</a>

        </div>

    </div>
    <div class="main-form">



    </div>
    <div class="main-function">
                <div class="home">
                    <a class="home-button" href="admin-main.php">Home</a>
                </div>

                <div class="payment">
                <a class="Payment-button" href="admin-borrowerslist.php">Borrowers List</a>
                </div>
                <div class="account_details">
                <a class="Account-button" href="admin-view-loans.php">Loan Applications</a>
                </div>

                <div class="loan">
                <a class="Loan-button" href="Create-loan.php">Create Loan Plan</a>
                </div>
                <div class="account_details">
                <a class="Account-button" href="user_account.php">Recent Payment</a>
                </div>
                <div class="account_details">
                <a class="Account-button" href="user_account.php">Account</a>
                </div>
    
        </div>
    <?php else: ?>


    <?php endif; ?>


</body>
</html>