<?php
include 'db_connect.php';
$res = $conn->query("SELECT * FROM reminders ORDER BY created_at ASC");
while($r = $res->fetch_assoc()){
    echo "<div class='reminder-item'><strong>".htmlspecialchars($r['username']).":</strong> ".htmlspecialchars($r['message'])."</div>";
}
?>
