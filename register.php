<?php
include("includes/session.php");
include("includes/auth.php");

if (isLoggedIn()) {
    header("Location: pages/dashboard.php");
    exit();
}
$error = "";
$success = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    elseif(strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    }
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }
    else {
        $result = registerUser($username, $email, $password);
        if ($result == "success") {
            $success = "Registration successful! You can now <a href='login.php'>login</a>.";
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
        <title>Register - SyncBase</title>
    </head>
    <body>
        <div class="auth-container">
            <h1 class="brand">SyncBase</h1>
            <p class="brand-sub">Create your account to get started.</p>
            <h2>Register</h2>
            <?php if ($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php elseif ($success): ?>
                <p class="success-msg"><?php echo $success; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Your username" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email address" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                <button type="submit">Register</button>
            </form>
            <div class="link-row">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </body>
</html>