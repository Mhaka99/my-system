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

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, 's', $username);
if (!mysqli_stmt_execute($stmt)) {
    die("Execute failed: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

if (!$row = mysqli_fetch_assoc($result)) {
    die("No user found.");
}

// Fetch all loan plans
$loanPlansQuery = "SELECT * FROM `loan_plan`";
$loanPlansResult = mysqli_query($conn, $loanPlansQuery);

if (!$loanPlansResult) {
    die("Failed to fetch loan plans: " . mysqli_error($conn));
}

if (isset($_POST['submit2'])) {
    // Collect form data
    $month = $_POST['month'];
    $interest = $_POST['interest'];
    $penalty = $_POST['penalty'];

    $sql = "INSERT INTO `loan_plan` (month, interest, penalty) VALUES ('$month', '$interest', '$penalty')";

    if (mysqli_query($conn, $sql)) {
        // Redirect to the same page to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Failed to Insert: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-ui</title>
    <link rel="stylesheet" href="create-loan.css">
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
                <h2><?php echo htmlspecialchars($row['user']); ?></h2>
            </div>
        </div>
        <div class="logout">
            <a href="admin.php" class="logoutbutton">Logout</a>
        </div>
    </div>

    <form class="main-form" action="" method="post">
        <div class="div1">
            <div class="tittle2">
                <h2>Plan Form</h2>
            </div>
            <div class="Plan-months">
                <label class="monte">Plan (Months):</label>
                <input class="month-input" type="number" name="month" id="month" min="1" required>
            </div>
            <div class="Plan-Interest">
                <label class="Inte">Interest:</label>
                <input class="Interest" type="number" name="interest" id="interest" step="0.01" required>
                <a class="percent-logo">%</a>
            </div>
            <div class="Plan-Penalty">
                <label class="Penal">Monthly Overdue Penalty:</label>
                <input class="Penalty" type="number" name="penalty" id="penalty" step="0.01"><a class="percent-logo">%</a>
            </div>
            <div class="but">
                <button class="Save" type="submit" name="submit2">Save</button>
                <button type="button" class="clear" onclick="document.getElementById('clear').value = ''">Cancel</button>
            </div>
        </div>
//
        <div class="loan-plans">
        <h2>All Loan Plans</h2>
        <table>
            <thead>
                <tr>
                    <th>Duration</th>
                    <th>Interest (%)</th>
                    <th>Penalty (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($loanPlan = mysqli_fetch_assoc($loanPlansResult)): ?>
                <tr>
                    <?php 
                        $months = $loanPlan['month'];
                        $years = floor($months / 12);
                        $remainingMonths = $months % 12;
                        $duration = '';

                        if ($years > 0) {
                            $duration .= $years . ' year' . ($years > 1 ? 's' : '');
                        }
                        if ($remainingMonths > 0) {
                            if ($duration) {
                                $duration .= ' and ';
                            }
                            $duration .= $remainingMonths . ' month' . ($remainingMonths > 1 ? 's' : '');
                        }
                    ?>
                    <td><?php echo htmlspecialchars($duration); ?></td>
                    <td><?php echo htmlspecialchars($loanPlan['interest']); ?></td>
                    <td><?php echo htmlspecialchars($loanPlan['penalty']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    </form>



    <div class="main-function">
        <div class="home">
            <a class="home-button" href="admin-main.php">Home</a>
        </div>
        <div class="payment">
            <a class="Payment-button" href="payment.php">Borrowers List</a>
        </div>
        <div class="account_details">
            <a class="Account-button" href="admin-view-loans.php">Loan Applications</a>
        </div>
        <div class="loan">
            <a class="Loan-button" href="loan.php">Create Loan Plan</a>
        </div>
        <div class="account_details">
            <a class="Account-button" href="user_account.php">Recent Payment</a>
        </div>
        <div class="account_details">
            <a class="Account-button" href="user_account.php">Account</a>
        </div>
    </div>
</body>
</html>
