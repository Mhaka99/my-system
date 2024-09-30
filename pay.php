<?php
require 'connection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from session
$userId = $_SESSION['user_id'] ?? null; 

// Retrieve loan data from POST request
$loanId = $_POST['loan_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$months = $_POST['months'] ?? null;
$interest = $_POST['interest'] ?? null;
$monthlyPayment = $_POST['monthly_payment'] ?? null;
$purpose = $_POST['purpose'] ?? null;
$email = $_POST['email'] ?? null;

// Instamojo credentials
$token = "test_57a512afd72b4d5730dc2fcf99e"; // Use your actual token
$mojoURL = "test.instamojo.com";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://$mojoURL/v2/payment_requests/");
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $token"));

$payload = array(
    'purpose' => $purpose ?? 'Loan Payment',
    'amount' => $amount,
    'email' => htmlspecialchars($email), // Use the email from POST request
    'phone' => htmlspecialchars($row['phone']), // Use the user's phone from the database
    'redirect_url' => '', // Update to your redirect URL
    'send_email' => 'True',
    'webhook' => '', // Update to your webhook URL
    'allow_repeated_payments' => 'False',
);


curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
$response = curl_exec($ch);
curl_close($ch); 

// Process the response
$responseData = json_decode($response, true);
if (isset($responseData['payment_request']['longurl'])) {
    // Redirect the user to the payment URL
    header('Location: ' . $responseData['payment_request']['longurl']);
    exit();
} else {
    // Handle errors
    echo 'Error: ' . ($responseData['message'] ?? 'Unknown error');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="pay.css"> <!-- Add your CSS file if needed -->
</head>
<body>
    <h2>Payment Details</h2>
    <p><strong>User ID:</strong> <?php echo htmlspecialchars($userId); ?></p>
    <p><strong>Loan ID:</strong> <?php echo htmlspecialchars($loanId); ?></p>
    <p><strong>Amount:</strong> <?php echo htmlspecialchars($amount); ?></p>
    <p><strong>Months:</strong> <?php echo htmlspecialchars($months); ?></p>
    <p><strong>Interest:</strong> <?php echo htmlspecialchars($interest); ?>%</p>
    <p><strong>Monthly Payment:</strong> <?php echo htmlspecialchars($monthlyPayment); ?></p>
    <p><strong>Purpose:</strong> <?php echo htmlspecialchars($purpose); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

    <div>
        <a href="user-approved_loan.php">Back to Approved Loans</a>
    </div>
</body>
</html>
