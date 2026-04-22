<?php
include("../includes/session.php");
include("../includes/auth.php");


if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../pages/emails.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$contact_id = !empty($_POST["contact_id"]) ? (int)$_POST["contact_id"] : null;
$subject = trim($_POST["subject"]);
$body = trim($_POST["body"]);

if(!$contact_id){
    header("Location: ../pages/emails.php?error=Contact is required");
    exit();
}
pg_prepare($conn, 'insert_email', 'INSERT INTO emails (user_id, contact_id, subject, body) VALUES ($1, $2, $3, $4)');
pg_execute($conn, 'insert_email', array($user_id, $contact_id, $subject, $body));

header("Location: ../pages/emails.php");
exit();
