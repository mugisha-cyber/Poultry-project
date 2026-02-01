<?php
    error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';
include 'db_connect.php';

if (isset($_GET['NO'])) {
    $no = $_GET['NO'];
    mysqli_query($conn, "DELETE FROM money_sent WHERE NO='$no'");
}

header("Location: view_money_sent.php");
exit();
?>
