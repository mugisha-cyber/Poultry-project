<?php
$servername = "sql300.infinityfree.com";   // your host
$username = "if0_40156116";            // your database username
$password = "mugisha3030";            // your database password
$dbname = "if0_40156116_poulty_db";                  // the database name you created

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
