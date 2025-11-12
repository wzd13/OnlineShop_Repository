<?php
include_once "config/db.php";

$error = "";
$success = "";

try {
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $username = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_pass = trim($_POST['confirm_pass'] ?? '');

        if (empty($username) || empty($email) || empty($password) || empty($confirm_pass)) {
            throw new Exception('All fields are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        if ($password !== $confirm_pass) {
            throw new Exception("Passwords do not match.");
        }

        $checkSQL = "SELECT id FROM users WHERE email = ?";
        $checkStmt = $conn->prepare($checkSQL);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            throw new Exception("Account already registered.");
        }

        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        $role = "customer";
        $status = "active";
        $phone = "";

        $insertSQL = "INSERT INTO users (`name`, `email`, `password`, `role`, `status`, `phone`, `created_at`)
                      VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $insertStmt = $conn->prepare($insertSQL);
        $insertStmt->bind_param("ssssss", $username, $email, $hashedPass, $role, $status, $phone);

        if (!$insertStmt->execute()) {
            throw new Exception("Database insert failed: " . $conn->error);
        }

        $success = "✅ Account registered successfully! Redirecting to login...";
        // 自动跳转
        header("refresh:3;url=login.php"); // 3秒后跳转
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Registration</title>
<style>
    body {
        background-color: #121212;
        color: #f1f1f1;
        font-family: 'Segoe UI', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .container {
        background-color: #1e1e1e;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.5);
        width: 360px;
        text-align: center;
    }
    h2 {
        margin-bottom: 20px;
        color: #00bcd4;
    }
    input {
        width: 90%;
        padding: 10px;
        margin: 8px 0;
        border: none;
        border-radius: 6px;
        background-color: #2c2c2c;
        color: #f1f1f1;
        font-size: 15px;
    }
    input:focus {
        outline: 2px solid #00bcd4;
        background-color: #333;
    }
    button {
        background-color: #00bcd4;
        border: none;
        padding: 10px 20px;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 10px;
        width: 95%;
        transition: 0.3s;
    }
    button:hover {
        background-color: #0097a7;
    }
    .message {
        margin-top: 15px;
        font-size: 14px;
        padding: 8px;
        border-radius: 6px;
    }
    .error {
        background-color: #ff3d00;
        color: white;
    }
    .success {
        background-color: #00c853;
        color: white;
    }
</style>
</head>
<body>
    <div class="container">
        <h2>🖤 Create Account</h2>
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="email" name="email" placeholder="Email Address" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm_pass" placeholder="Confirm Password" required><br>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
