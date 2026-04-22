<?php
// Database configuration

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");
if (!$conn){
    die("Connection failed: " . pg_last_error());
}
?>
