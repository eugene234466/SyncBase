<?php
// Database configuration

$conn = pg_connect("host=aws-0-eu-west.pooler.supabase.com dbname=postgres user=
postgres.ayubevnxrmxvamdwuecx password=x@0H_jnXU;j\lGZI");
if (!$conn){
    die("Connection failed:   ". pg_last_error());
}
?>
