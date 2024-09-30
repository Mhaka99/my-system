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

// Fetch all loan applications
$loanApplicationsQuery = "SELECT * FROM `loan_application`";
$loanApplicationsResult = mysqli_query($conn, $loanApplicationsQuery);

if (!$loanApplicationsResult) {
    die("Failed to fetch loan applications: " . mysqli_error($conn));
}

// Handle loan approval
if (isset($_POST['approve'])) {
    $loanId = $_POST['loan_id'];
    
    // Fetch the loan details
    $loanDetailsQuery = "SELECT * FROM `loan_application` WHERE `id` = ?";
    $stmt = mysqli_prepare($conn, $loanDetailsQuery);
    mysqli_stmt_bind_param($stmt, 'i', $loanId);
    mysqli_stmt_execute($stmt);
    $loanDetailsResult = mysqli_stmt_get_result($stmt);
    
    if ($loanDetails = mysqli_fetch_assoc($loanDetailsResult)) {
        // Insert into loan_approved table
        $insertQuery = "INSERT INTO `loan_approved` (user_id, amount, months, interest, monthly_payment, purpose) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, 'ididis', $loanDetails['user_id'], $loanDetails['amount'], $loanDetails['months'], $loanDetails['interest'], $loanDetails['monthly_payment'], $loanDetails['Purpose']);
        mysqli_stmt_execute($insertStmt);

                // Delete the loan application after approval
        $deleteQuery = "DELETE FROM `loan_application` WHERE `id` = ?";
        $deleteStmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, 'i', $loanId);
        mysqli_stmt_execute($deleteStmt);
        
        
        // Refresh the page to see updates
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-ui</title>
    <link rel="stylesheet" href="admin-view-loans.css">
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
                <h2><?php echo htmlspecialchars($row['user']); ?></h2>
            </div>
        </div>
        <div class="logout">
            <a href="admin.php" class="logoutbutton">Logout</a>
        </div>
    </div>

    <div class="main-form">
        <table>
            <thead>
                <tr>   
                    <th>View Info</th>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Amount</th>
                    <th>Months</th>
                    <th>Interest (%)</th>
                    <th>Monthly Payment</th>
                    <th>Purpose</th>
                    <th>Date Applied</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($loanApplication = mysqli_fetch_assoc($loanApplicationsResult)): ?>
                    <tr style="background-color: pink; justify-content: start;">
                        <td>
                            <button>view info</button>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="loan_id" value="<?php echo htmlspecialchars($loanApplication['id']); ?>">
                                <button type="submit" name="approve">Approve</button>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($loanApplication['id']); ?></td>
                        <td><?php echo htmlspecialchars($loanApplication['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($loanApplication['amount']); ?></td>
                        <td><?php echo htmlspecialchars($loanApplication['months']); ?></td>
                        <td><?php echo htmlspecialchars($loanApplication['interest']); ?></td>
                        <td><?php echo htmlspecialchars($loanApplication['monthly_payment']); ?></td>
                        <td><?php echo htmlspecialchars($loanApplication['Purpose']); ?></td>
                        <td><?php echo htmlspecialchars($loanApplication['created_at']); ?></td>
                        <td><button>Deny</button></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="main-function">
        <div class="home">
            <a class="home-button" href="admin-main.php">Home</a>
        </div>
        <div class="payment">
            <a class="Payment-button" href="payment.php">Borrowers List</a>
        </div>
        <div class="account_details">
            <a class="view-button" href="admin-view-loans.php">Loan Applications</a>
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
        <p>User not found.</p>
    <?php endif; ?>
</body>
</html>
