<?php
session_start();
include('db_connect.php');

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle Add Expense
if (isset($_POST['add'])) {
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = floatval($_POST['amount']);

    $stmt = $conn->prepare("INSERT INTO other_expenses (DATE, DETAILS, AMOUNT) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $date, $details, $amount);
    if ($stmt->execute()) {
        header("Location: other_expenses.php");
        exit();
    } else {
        echo "Error adding expense: " . $conn->error;
    }
    $stmt->close();
}

// Fetch total expenses
$totalResult = $conn->query("SELECT SUM(AMOUNT) AS total_amount FROM other_expenses");
$totalRow = $totalResult->fetch_assoc();
$total = $totalRow['total_amount'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Other Expense</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; padding: 30px; }
h2 { text-align: center; color: #c62828; margin-bottom: 20px; }

form {
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    width: 400px;
    margin: 0 auto 20px auto;
}
form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}
form input[type="date"],
form input[type="number"],
form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}
form textarea { min-height: 80px; resize: vertical; }
form button {
    width: 100%;
    padding: 12px;
    background: #c62828;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
form button:hover { background: #a91f1f; }

.actions { text-align: center; margin-bottom: 20px; }
.actions a { text-decoration: none; font-weight: bold; margin: 0 10px; color: #c62828; }

.total { text-align: center; margin-top: 20px; font-weight: bold; font-size: 18px; }
</style>
</head>
<body>

<h2>➕ Add New Expense</h2>

<div class="actions">
    <a href="view_other_expenses.php">👁️ View All Expenses</a>
    <a href="dashboard.php">🏠 Dashboard</a>
</div>

<form method="POST" action="">
    <label>Date:</label>
    <input type="date" name="date" required>

    <label>Details:</label>
    <textarea name="details" required></textarea>

    <label>Amount:</label>
    <input type="number" name="amount" step="0.01" required>

    <button type="submit" name="add">💾 Add Expense</button>
</form>

<div class="total">
    💰 Total Expenses: <?= number_format($total, 2) ?>
</div>

</body>
</html>
