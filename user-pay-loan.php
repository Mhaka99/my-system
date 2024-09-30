<?php
require 'connection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the username from session
$username = $_SESSION['user'];

// Fetch user information based on the logged-in username
$sql = "SELECT * FROM user WHERE user = ?";
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
    <title>User-ui</title>
    <link rel="stylesheet" href="user-pay-loan.css">
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
        <div class="title">
            <div class="user_fullname">
                <h2><?php echo htmlspecialchars($row['lname']); echo(", ")?><?php echo htmlspecialchars($row['fname']); ?></h2>
            </div>
            <div class="user_number">
                <h4><?php echo("Contact No: "); echo htmlspecialchars($row['phone']);?></h4>
            </div>
            <div class="user_email">
                <h4><?php echo("Email: "); echo htmlspecialchars($row['email']);?></h4>
            </div>
        </div>
        <div class="logout">
            <a href="index.php" class="logoutbutton">Logout</a>
        </div>
    </div>
    <div class="main-form">
        <h1>WELCOME TO PAYMENT</h1>
    </div>
    <div class="main-function">
        <div class="home">
            <a class="home-button" href="user-main.php">Home</a>
        </div>
        <div class="payment">
            <a class="Loan-button" href="user-apply-loan.php">Apply Loan</a>
        </div>
        <div class="loan">
            <a class="Payment-button" href="user-pay-loan.php">Payment</a>
        </div>
        <div class="account_details">
            <a class="Account-button" href="user_account.php">Account</a>
        </div>
    </div>  
    <?php else: ?>
        <p>User not found.</p>
    <?php endif; ?>
</body>
</html>