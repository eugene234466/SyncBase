<?php
include(dirname(__DIR__) . "/config/db.php");

function registerUser($username, $email, $password) {
    global $conn;

    pg_prepare($conn, "check_user", "SELECT id FROM users WHERE email = $1 OR username = $2");
    $result = pg_execute($conn, "check_user", array($email, $username));

    if ($result && pg_num_rows($result) > 0) {
        return "User already exists.";
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    pg_prepare($conn, "insert_user", "INSERT INTO users (username, email, password) VALUES ($1, $2, $3)");
    pg_execute($conn, "insert_user", array($username, $email, $hashed_password));

    return "success";
}


function loginUser($email, $password) {
    global $conn;

    pg_prepare($conn, "get_user", "SELECT id, username, password FROM users WHERE email = $1");
    $result = pg_execute($conn, "get_user", array($email));

    if (!$result || pg_num_rows($result) === 0) {
        return "Invalid credentials";
    }

    $user = pg_fetch_assoc($result);

    if (!password_verify($password, $user['password'])) {
        return "Invalid credentials";
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    return "success";
}


function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: /syncbase/login.php");
    exit();
}
?>