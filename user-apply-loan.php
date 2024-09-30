<?php
require 'connection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get the username from session
$username = $_SESSION['user'];

// Fetch user information to get the user ID
$sql = "SELECT * FROM `user` WHERE `user` = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("User not found.");
}

// Fetch user ID
$userData = mysqli_fetch_assoc($result);
$userId = $userData['id'];

// Fetch loan plans
$loanPlansQuery = "SELECT * FROM `loan_plan`";
$loanPlansResult = mysqli_query($conn, $loanPlansQuery);

if (!$loanPlansResult) {
    die("Failed to fetch loan plans: " . mysqli_error($conn));
}

// Handle loan application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applyLoan'])) {
    // Get the loan application details
    $amount = $_POST['loanAmount'];
    $months = $_POST['loanMonths'];
    $interest = $_POST['loanInterest'];
    $purpose = $_POST['loanPurpose'];

    // Calculate the monthly payment
    $monthlyPayment = calculateMonthlyPayment($amount, $interest, $months);

    // Insert into loan applications table using user ID
    $sql = "INSERT INTO `loan_application` (user_id, amount, months, interest, monthly_payment, purpose) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ididss', $userId, $amount, $months, $interest, $monthlyPayment, $purpose);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = 'Loan Application was successful!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Failed to apply for loan: " . mysqli_error($conn);
    }
}

// Calculate monthly payment function
function calculateMonthlyPayment($principal, $annualInterestRate, $months) {
    $monthlyInterestRate = $annualInterestRate / 100 / 12;
    return ($principal * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$months));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-ui</title>
    <link rel="stylesheet" href="user-apply-loan2.css">
    <script>
        function showLoanForm(months, interest) {
            const form = document.getElementById('loanForm');
            form.style.display = 'block';
            document.getElementById('loanMonths').value = months;
            document.getElementById('loanInterest').value = interest;
            document.getElementById('monthlyPayment').innerText = ''; // Reset payment text
        }

        function calculatePayment() {
            const amount = parseFloat(document.getElementById('loanAmount').value);
            const months = parseInt(document.getElementById('loanMonths').value);
            const interest = parseFloat(document.getElementById('loanInterest').value);
            if (amount > 0 && months > 0 && interest >= 0) {
                const monthlyPayment = calculateMonthlyPayment(amount, interest, months);
                document.getElementById('monthlyPayment').innerText = "Monthly Payment: " + monthlyPayment.toFixed(2);
            }
        }

        function calculateMonthlyPayment(principal, annualInterestRate, months) {
            const monthlyInterestRate = annualInterestRate / 100 / 12;
            return (principal * monthlyInterestRate) / (1 - Math.pow(1 + monthlyInterestRate, -months));
        }
    </script>
</head>
<body>
    <?php if ($userData): ?>
    <div class="header">
        <div class="profile">
            <?php if ($userData['photo']): ?>
                <img class="profile_pic" src="<?php echo htmlspecialchars($userData['photo']); ?>" alt="User Photo" width="150px" height="120px" style="border-radius: 50px; border: 3px solid white; margin-top: 25px; margin-left: 20px;">
            <?php else: ?>
                No Photo
            <?php endif; ?>
        </div>
        <div class="title">
            <div class="user_fullname">
                <h2><?php echo htmlspecialchars($userData['lname']) . ", " . htmlspecialchars($userData['fname']); ?></h2>
            </div>
            <div class="user_number">
                <h4><?php echo("Contact No: ") . htmlspecialchars($userData['phone']); ?></h4>
            </div>
            <div class="user_email">
                <h4><?php echo("Email: ") . htmlspecialchars($userData['email']); ?></h4>
            </div>
        </div>
        <div class="logout">
            <a href="index.php" class="logoutbutton">Logout</a>
        </div>
    </div>

    <div class="main-form">
        <h2>All Loan Plans</h2>
        <table>
            <thead>
                <tr>
                    <th>Duration</th>
                    <th>Interest (%)</th>
                    <th>Penalty (%)</th>
                    <th>Action</th>
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
                        <td>
                            <button type="button" onclick="showLoanForm('<?php echo $months; ?>', '<?php echo $loanPlan['interest']; ?>')">Apply</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div id="loanForm" style="display:none;">
            <h2>Apply for Loan</h2>
            <form id="loanApplicationForm" method="post">
                <label for="loanAmount">Loan Amount:</label>
                <input type="number" id="loanAmount" name="loanAmount" required>
                <label for="loanPurpose">Purpose:</label>
                <input type="text" id="loanPurpose" name="loanPurpose" required>
                <input type="hidden" id="loanMonths" name="loanMonths">
                <input type="hidden" id="loanInterest" name="loanInterest">
                <button type="button" onclick="calculatePayment()">Calculate Payment</button>
                <p id="monthlyPayment"></p>
                <button type="submit" name="applyLoan">Confirm Application</button>
            </form>
        </div>
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
    </div>

    <?php
    // Check for success message and display alert
    if (isset($_SESSION['success_message'])) {
        echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
        unset($_SESSION['success_message']); // Clear the message
    }
    ?>

    <?php else: ?>
        <p>User not found.</p>
    <?php endif; ?>
</body>
</html>

