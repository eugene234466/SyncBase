<?php
include("../includes/session.php");
include("../includes/auth.php");

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/deals.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$title = trim($_POST["title"]);
$value = !empty($_POST["value"]) ? (float)$_POST["value"] : 0;
$contact_id = !empty($_POST["contact_id"]) ? (int)$_POST["contact_id"] : null;
$stage = trim($_POST["stage"]);

if (empty($title)) {
    header("Location: ../pages/deals.php?error=Title is required");
    exit();
}

$valid_stages = ["lead", "negotiation", "closed"];
if (!in_array($stage, $valid_stages)) $stage = "lead";

pg_query_params($conn, "INSERT INTO deals (user_id, contact_id, title, value, stage) VALUES ($1, $2, $3, $4, $5)", array($user_id, $contact_id, $title, $value, $stage));

header("Location: ../pages/deals.php");
exit();
?>
