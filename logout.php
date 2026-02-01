<?php
session_start();
session_unset();   // remove all session data
session_destroy(); // destroy session completely
header("Location: login.php"); // go back to login page
exit();
?>
