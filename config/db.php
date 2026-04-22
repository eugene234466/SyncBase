<?php
// Database configuration

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn){
    error_log("PostgreSQL connection failed");
    die("Unable to connect to database. Please try again later.");
}
?>
