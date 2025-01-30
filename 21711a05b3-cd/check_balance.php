<?php
session_start();
require 'config.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's balance
$stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Balance - Core Banking</title>
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
    color: #ff7f50;
    font-size: 24px;
    margin-bottom: 20px;
}

p {
    font-size: 20px;
    margin-bottom: 30px;
}

a {
    background-color: #ff7f50;
    border: none;
    border-radius: 4px;
    color: white;
    display: inline-block;
    font-size: 16px;
    padding: 10px 20px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

a:hover {
    background-color: #e56347;
}

</style>
</head>
<body>
    <div class="container">
        <h2>Check Balance</h2>
        <p>Your current balance is: <?php echo htmlspecialchars($balance); ?></p>
        <p><a href="index.php">Back to Home</a></p>
    </div>
</body>
</html>
