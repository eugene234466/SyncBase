<?php
include("../includes/session.php");
include("../includes/auth.php");

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/contacts.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$name = trim($_POST["name"]);
$email = trim($_POST["email"]);
$phone = trim($_POST["phone"]);
$company = trim($_POST["company"]);
$notes = trim($_POST["notes"]);

if (empty($name)) {
    header("Location: ../pages/contacts.php?error=Name is required");
    exit();
}

pg_prepare($conn, "insert_contact", "INSERT INTO contacts (user_id, name, email, phone, company, notes) VALUES ($1, $2, $3, $4, $5, $6)");
pg_execute($conn, "insert_contact", array($user_id, $name, $email, $phone, $company, $notes));

header("Location: ../pages/contacts.php");
exit();
?>