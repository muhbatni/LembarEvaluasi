<?php
// postgreSQL connection parameters
$host = "localhost";
$port = "5433";
$dbname = "evaluasi_pelatihan";
$user = "postgres";
$password = "cantikitu5";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Koneksi gagal: " . pg_last_error());
}
?>
