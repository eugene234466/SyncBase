<?php
include("../includes/session.php");
include("../includes/auth.php");

if (!isLoggedIn()) { header("Location: ../login.php"); exit(); }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: ../pages/invoices.php"); exit(); }

$user_id = $_SESSION["user_id"];
$title = trim($_POST["title"]);
$amount = !empty($_POST["amount"]) ? (float)$_POST["amount"] : 0;
$contact_id = !empty($_POST["contact_id"]) ? (int)$_POST["contact_id"] : null;
$due_date = !empty($_POST["due_date"]) ? $_POST["due_date"] : null;
$status = $_POST["status"] === "paid" ? "paid" : "unpaid";

if (empty($title)) { header("Location: ../pages/invoices.php?error=Title is required"); exit(); }
if ($amount <= 0) { header("Location: ../pages/invoices.php?error=Amount must be greater than 0"); exit(); }

pg_query_params($conn, "INSERT INTO invoices (user_id, contact_id, title, amount, status, due_date) VALUES ($1, $2, $3, $4, $5, $6)", array($user_id, $contact_id, $title, $amount, $status, $due_date));

header("Location: ../pages/invoices.php");
exit();
?>
