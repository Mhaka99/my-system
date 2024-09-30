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
$sql = "SELECT * FROM `user` WHERE `user` = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$userId = $row['id']; // Assuming 'id' is the primary key in user table

// Fetch loans for the logged-in user from loan_application table
$loanSql = "SELECT * FROM `loan_application` WHERE `user_id` = ?";
$loanStmt = mysqli_prepare($conn, $loanSql);
mysqli_stmt_bind_param($loanStmt, 's', $userId);
mysqli_stmt_execute($loanStmt);
$loanResult = mysqli_stmt_get_result($loanStmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-ui</title>
    <link rel="stylesheet" href="user-myloan.css">
</head>
<body>
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
                <h2><?php echo htmlspecialchars($row['lname']) . ", " . htmlspecialchars($row['fname']); ?></h2>
            </div>
            <div class="user_number">
                <h4><?php echo("Contact No: "); echo htmlspecialchars($row['phone']); ?></h4>
            </div>
            <div class="user_email">
                <h4><?php echo("Email: "); echo htmlspecialchars($row['email']); ?></h4>
            </div>
        </div>
        <div class="logout">
            <a href="index.php" class="logoutbutton">Logout</a>
        </div>
    </div>

    <div class="main-form">
        <h3>Your Loan Applications:</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Months</th>
                    <th>Interest</th>
                    <th>Monthly Payment</th>
                    <th>Purpose</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($loanRow = mysqli_fetch_assoc($loanResult)): ?>
                    <tr style="background-color: pink;">
                        <td><?php echo htmlspecialchars($loanRow['id']); ?></td>
                        <td><?php echo htmlspecialchars($loanRow['amount']); ?></td>
                        <td><?php echo htmlspecialchars($loanRow['months']); ?></td>
                        <td><?php echo htmlspecialchars($loanRow['interest']); ?></td>
                        <td><?php echo htmlspecialchars($loanRow['monthly_payment']); ?></td>
                        <td><?php echo htmlspecialchars($loanRow['Purpose']); ?></td>
                        <td><?php echo htmlspecialchars($loanRow['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($loanResult) == 0): ?>
                    <tr>
                        <td colspan="7">No loan applications found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="main-function">
        <div class="home">
            <a class="home-button" href="user-main.php">Home</a>
        </div>
        <div class="myloan">
            <a class="myloan-button" href="user-myloan.php">Pending Loan</a>
        </div>
        <div class="loan">
            <a class="Loan-button" href="user-apply-loan.php">Apply Loan</a>
        </div>
        <div class="payment">
            <a class="Payment-button" href="user-pay-loan.php">Payment</a>
        </div>
        <div class="account_details">
            <a class="Account-button" href="user_account.php">Account</a>
        </div>
        <div class="approved-loan">
                <a class="approved-button" href="user-approved_loan.php">Approved Loan</a>
                </div>
    </div>
</body>
</html>
