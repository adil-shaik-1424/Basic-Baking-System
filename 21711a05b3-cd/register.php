<?php
session_start();
require 'config.php';

// Redirect to index page if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // Validate inputs
    if (empty($username) || empty($password) || empty($email)) {
        $error = 'All fields are required.';
    } else {
        // Check if the username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Username or email already exists.';
        } else {
            // Generate a random bank account number
            $bank_account_number = generateBankAccountNumber();
            // Hash the password
            $hashed_password = hashPassword($password);

            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, bank_account_number) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $hashed_password, $email, $bank_account_number);

            if ($stmt->execute()) {
                $success = 'Account created successfully. You can now <a href="login.php">log in</a>.';
            } else {
                $error = 'Error creating account. Please try again.';
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Core Banking</title>
 <style>
 
/* styles.css */

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #1d2671 0%, #c33764 100%);
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
    display: block;
    font-weight: 600;
    margin-bottom: 10px;
}

input[type="text"],
input[type="password"],
input[type="email"] {
    border: 2px solid #ddd;
    border-radius: 10px;
    box-sizing: border-box;
    font-size: 16px;
    padding: 14px;
    width: calc(100% - 28px);
}

button[type="submit"] {
    background-color: #1d2671;
    border: none;
    border-radius: 10px;
    color: #fff;
    cursor: pointer;
    font-size: 18px;
    padding: 15px;
    transition: background-color 0.3s ease;
    width: 100%;
}

button[type="submit"]:hover {
    background-color: #c33764;
}

p {
    font-size: 16px;
    margin-top: 20px;
}

a {
    color: #1d2671;
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    color: #c33764;
    text-decoration: underline;
}


 </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>
