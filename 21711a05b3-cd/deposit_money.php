<?php
session_start();
require 'config.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount']);

    // Validate input
    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $error = 'Please enter a valid amount.';
    } else {
        // Update user's balance
        $stmt_update_balance = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        if (!$stmt_update_balance) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt_update_balance->bind_param("di", $amount, $_SESSION['user_id']);

            if ($stmt_update_balance->execute()) {
                $stmt_update_balance->close();

                // Insert transaction record
                $stmt_insert_transaction = $conn->prepare("INSERT INTO transactions (user_id, transaction_type, amount) VALUES (?, 'credit', ?)");
                if (!$stmt_insert_transaction) {
                    $error = 'Database error: ' . $conn->error;
                } else {
                    $stmt_insert_transaction->bind_param("id", $_SESSION['user_id'], $amount);
                    
                    if ($stmt_insert_transaction->execute()) {
                        $stmt_insert_transaction->close();
                        $success = 'Deposit successful!';
                    } else {
                        $error = 'Error inserting transaction record.';
                    }
                }
            } else {
                $error = 'Error updating balance.';
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deposit Money - Core Banking</title>
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
    max-width: 400px;
    padding: 30px;
    text-align: center;
    width: 100%;
}

h2 {
    color: #333;
    font-size: 24px;
    margin-bottom: 25px;
}

.alert {
    border-radius: 5px;
    font-size: 14px;
    margin-bottom: 20px;
    padding: 10px 15px;
    text-align: left;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.form-group {
    margin-bottom: 20px;
    text-align: left;
}

label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
}

input[type="text"] {
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 16px;
    padding: 10px;
    width: 100%;
}

button {
    background-color: #74b9ff;
    border: none;
    border-radius: 5px;
    color: white;
    cursor: pointer;
    font-size: 16px;
    padding: 12px 20px;
    width: 100%;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0984e3;
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
        <h2>Deposit Money</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form action="deposit_money.php" method="post">
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="text" name="amount" id="amount" required>
            </div>
            <button type="submit">Deposit</button>
        </form>
        <p><a href="index.php">Back to Home</a></p>
    </div>
</body>
</html>
