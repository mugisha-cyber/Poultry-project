<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Initialize message
$msg = "";

// Handle form submission
if (isset($_POST['save'])) {
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    if (!empty($date) && !empty($details) && !empty($amount)) {
        $sql = "INSERT INTO kudemaza (DATE, DETAILS, AMOUNT) VALUES ('$date', '$details', '$amount')";
        if ($conn->query($sql)) {
            $msg = "✅ Transaction added successfully!";
        } else {
            $msg = "❌ Database error: " . $conn->error;
        }
    } else {
        $msg = "⚠️ Please fill all fields before saving.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Kudemaza Transaction expenses</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #fafafa; }
        h2 { color: #333; }
        form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px #ccc; width: 400px; }
        input, textarea { padding: 6px; margin: 5px 0; width: 100%; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 8px 15px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #004d99; }
        .msg { font-weight: bold; margin-top: 10px; }
        a { text-decoration: none; color: #0066cc; margin-top: 10px; display: inline-block; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
     <div><a href="dashboard.php">🏠 Dashboard</a></div>

<h2>➕ Add New Kudemaza Transaction</h2>

<?php if (!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>

<form method="POST">
    <label>Date:</label><br>
    <input type="date" name="date" required><br>

    <label>Details:</label><br>
    <textarea name="details" required></textarea><br>

    <label>Amount:</label><br>
    <input type="number" name="amount" step="0.01" required><br><br>

    <button type="submit" name="save">💾 Save Transaction</button>
</form>

<a href="view_kudemaza.php">📊 View All Transactions</a> | 
<a href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>
