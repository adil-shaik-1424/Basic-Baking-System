<?php
session_start();
require 'config.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user transactions
$stmt = $conn->prepare("SELECT id, transaction_type, amount, transaction_date, recipient_account_number FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($transaction_id, $transaction_type, $amount, $transaction_date, $recipient_account_number);

$transactions = [];
while ($stmt->fetch()) {
    $transactions[] = [
        'id' => $transaction_id,
        'transaction_type' => $transaction_type,
        'amount' => $amount,
        'transaction_date' => $transaction_date,
        'recipient_account_number' => $recipient_account_number
    ];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Previous Transactions - Core Banking</title>
<style>
/* styles.css */

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #74ebd5 0%, #acb6e5 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    max-width: 800px;
    padding: 30px;
    text-align: center;
    width: 100%;
}

h2 {
    color: #333;
    font-size: 24px;
    margin-bottom: 25px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #ff7f50;
    color: white;
}

tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

tbody tr:hover {
    background-color: #e0e0e0;
}

a {
    color: #0984e3;
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    color: #74b9ff;
    text-decoration: underline;
}

</style>
</head>
<body>
    <div class="container">
       
    <h2>Previous Transactions</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Recipient Account</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['recipient_account_number']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><a href="index.php">Back to Home</a></p>
    </div>
</body>
</html>
