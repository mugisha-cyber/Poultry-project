<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    die("You must be logged in!");
}

if (!empty($_POST['message'])) {
    $msg = $_POST['message'];
    $user = $_SESSION['username'];
    $stmt = $conn->prepare("INSERT INTO reminders (username, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $msg);
    $stmt->execute();
}

header("Location: dashboard.php");
exit();
?>
