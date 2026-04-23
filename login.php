<?php
include("includes/session.php");
include("includes/auth.php");

if (isLoggedIn()) {
    header("Location: pages/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $result = loginUser($email, $password);
        if ($result == "success") {
            header("Location: pages/dashboard.php");
            exit();
        } else {
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Sekuya&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/style.css">
        <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sekuya&family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">
        <title>Login - SyncBase</title>
    </head>
    <body>
        <div class="auth-container">
            <h1 class="brand">SyncBase</h1>
            <p class="brand-sub">Welcome back, please login to continue.</p>
            <h2>Login</h2>
            <?php if ($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <button type="submit" class="btn">Login</button>
            </form>
            <div class="link-row">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </body>
</html>
