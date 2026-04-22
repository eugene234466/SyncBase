<?php
// Database configuration

$conn = pg_connect("host=localhost dbname=syncbase user=postgres password=dbv5nono8rKKGMPf");
if (!$conn){
    die("Connection failed:   ". pg_last_error());
}
?>
