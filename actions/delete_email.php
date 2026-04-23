<?php
include("../includes/session.php");
include("../includes/auth.php");

if (!isLoggedIn()) { header("Location: ../login.php"); exit(); }

$user_id = $_SESSION["user_id"];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: ../pages/emails.php"); exit(); }

pg_query_params($conn, "DELETE FROM emails WHERE id=$1 AND user_id=$2", array($id, $user_id));

header("Location: ../pages/emails.php");
exit();
?>
