<?php
include("../includes/session.php");
include("../includes/auth.php");

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) && $_GET['status'] === 'paid' ? 'paid' : 'unpaid';

if (!$id) {
    header("Location: ../pages/invoices.php");
    exit();
}

pg_prepare($conn, "update_invoice_status", "UPDATE invoices SET status=$1 WHERE id=$2 AND user_id=$3");
pg_execute($conn, "update_invoice_status", array($status, $id, $user_id));

header("Location: ../pages/invoices.php");
exit();
?>