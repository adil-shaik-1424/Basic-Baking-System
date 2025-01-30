<?php
session_start();
require 'config.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$username = '';
$balance = 0;

// Fetch user details including balance
$stmt_user = $conn->prepare("SELECT username, balance FROM users WHERE id = ?");
if ($stmt_user) {
    $stmt_user->bind_param("i", $_SESSION['user_id']);
    $stmt_user->execute();
    $stmt_user->bind_result($username, $balance);
    $stmt_user->fetch();
    $stmt_user->close();
} else {
    $error = 'Database error: ' . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome - Core Banking</title>
<style>
  /* styles.css */

body {
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    padding: 40px 50px;
    text-align: center;
    width: 100%;
}

h2 {
    color: #333;
    font-size: 30px;
    margin-bottom: 25px;
}

.alert {
    border: 1px solid transparent;
    border-radius: 5px;
    font-size: 16px;
    margin-bottom: 20px;
    padding: 15px;
    text-align: left;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

p {
    font-size: 18px;
    margin-bottom: 20px;
}

a {
    background: #1e3c72;
    border-radius: 10px;
    color: #fff;
    display: inline-block;
    margin-bottom: 10px;
    padding: 15px 25px;
    text-decoration: none;
    transition: background 0.3s ease;
}

a:hover {
    background: #2a5298;
}

</style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $username; ?>!</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <p>Your current balance: ₹<?php echo number_format($balance, 2); ?></p>
        <p><a href="deposit_money.php">Deposit Money</a></p>
        <p><a href="send_money.php">Send Money</a></p>
        <p><a href="transactions.php">View Transactions</a></p>
        <p><a href="check_balance.php">Check Balance</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
