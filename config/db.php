<?php
// Database configuration

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

$conn = $conn = pg_connect("host=aws-0-eu-west-1.pooler.supabase.com port=5432 dbname=postgres user=postgres.ayubevnxrmxvamdwuecx password=Y7hBnPc6uS6EJmMVr");
if (!$conn){
    error_log("PostgreSQL connection failed");
    die("Unable to connect to database. Please try again later.");
}
?>
