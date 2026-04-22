<?php
include("includes/session.php");
include("includes/auth.php");

if (isLoggedIn()) {
    header("Location: pages/dashboard.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}   

?>
