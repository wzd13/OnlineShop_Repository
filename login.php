<?php

include_once "config/db.php";


$error = "";

try {
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $remember = isset($_POST['remember']); // ✅ 获取 Remember Me

        if (empty($email) || empty($password)) {
            throw new Exception("Please fill all fields.");
        }

        $sql = "SELECT id, name, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            throw new Exception("Account not found.");
        }

        $stmt->bind_result($id, $name, $hashedPass);
        $stmt->fetch();

        if (!password_verify($password, $hashedPass)) {
            throw new Exception("Invalid password.");
        }

        // 登录成功
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;

        // ✅ Remember Me 逻辑
        if ($remember) {
            $selector = bin2hex(random_bytes(6));
            $validator = bin2hex(random_bytes(32));
            $hashed_validator = hash('sha256', $validator);
            $expiry = date("Y-m-d H:i:s", time() + 7 * 24 * 60 * 60);

            $sql_token = "INSERT INTO user_tokens (user_id, selector, hashed_validator, expire_at) VALUES(?,?,?,?)";
            $stmt_token = $conn->prepare($sql_token);
            $stmt_token->bind_param('isss', $id, $selector, $hashed_validator, $expiry);
            $stmt_token->execute();

            setcookie('remember_me', $selector . ":" . $validator, [
                'expires' => time() + 7 * 24 * 60 * 60,
                'path' => '/',
                'httponly' => true,
                'secure' => isset($_SERVER['HTTPS']),
                'samesite' => 'Lax'
            ]);
        }

        header("Location: index.php");
        exit();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
    body {
        background-color: #121212;
        color: white;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .login-box {
        background: #1e1e1e;
        padding: 30px;
        border-radius: 15px;
        width: 350px;
        box-shadow: 0 0 15px rgba(0,0,0,0.6);
    }
    h2 {
        text-align: center;
        color: #00ffcc;
    }
    input {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: none;
        border-radius: 8px;
        background: #2a2a2a;
        color: white;
    }
    input:focus {
        outline: 2px solid #00ffcc;
    }
    button {
        width: 100%;
        padding: 10px;
        background: #00ffcc;
        color: black;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
    }
    button:hover {
        background: #00e6b8;
    }
    .msg {
        text-align: center;
        color: red;
        margin-bottom: 10px;
    }
    .remember {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        font-size: 14px;
        color: #f0f0f0;
    }
    .remember input {
        margin-right: 8px;
    }
</style>
</head>
<body>
<div class="login-box">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<div class='msg'>$error</div>"; ?>
    <?php if (isset($_GET['success'])) echo "<div class='msg' style='color:#00ffcc;'>Registration successful! Please login.</div>"; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <div class="remember">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember Me</label>
        </div>
        <button type="submit">Login</button>
    </form>
    <p style="text-align:center;margin-top:10px;">Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>
