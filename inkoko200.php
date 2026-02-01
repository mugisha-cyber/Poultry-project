<?php
    error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';
session_start();
include('db_connect.php');

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$DATE = $DETAILS = $AMOUNT = "";
$msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    $DATE = isset($_POST['DATE']) ? $_POST['DATE'] : '';
    $DETAILS = isset($_POST['DETAILS']) ? $_POST['DETAILS'] : '';
    $AMOUNT = isset($_POST['AMOUNT']) ? $_POST['AMOUNT'] : '';

    if (!empty($DATE) && !empty($DETAILS) && !empty($AMOUNT)) {
        $sql = "INSERT INTO expenses_of_200 (DATE, DETAILS, AMOUNT) VALUES ('$DATE', '$DETAILS', '$AMOUNT')";
        if ($conn->query($sql)) {
            $msg = "✅ Transaction saved successfully!";
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
    <title>Inkoko 200 Project expenses</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #fafafa; }
        h2 { color: #333; }
        form { margin-bottom: 20px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px #ccc; }
        input, textarea { padding: 6px; margin: 5px 0; width: 250px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 8px 15px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #004d99; }
        a { text-decoration: none; color: #0066cc; margin-right: 10px; }
        a:hover { text-decoration: underline; }
        p { font-weight: bold; }
    </style>
</head>
<body>
     <div><a href="dashboard.php">🏠 Dashboard</a></div>
<h2>🐔 Inkoko 200 Project - Add New Transaction</h2>

<?php if (!empty($msg)) echo "<p>$msg</p>"; ?>

<form method="POST">
    <label>DATE:</label><br>
    <input type="date" name="DATE" required><br>

    <label>DETAILS:</label><br>
    <textarea name="DETAILS" required></textarea><br>

    <label>AMOUNT:</label><br>
    <input type="number" name="AMOUNT" step="0.01" required><br><br>

    <button type="submit" name="save">💾 Save Transaction</button>
</form>

<a href="view_inkoko200.php">📊 View All Transactions</a> | 
<a href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>
