<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$msg = "";

// Handle form submission
if (isset($_POST['save'])) {
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    if (!empty($date) && !empty($details) && !empty($amount)) {
        $sql = "INSERT INTO `expenses_of_75` (DATE, DETAILS, AMOUNT) VALUES ('$date', '$details', '$amount')";
        if ($conn->query($sql)) {
            $msg = "✅ Expense added successfully!";
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

    <title>Add expense of 75</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f0f0f0; }
        h2 { color: #333; margin-bottom: 20px; }
        form { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); width: 400px; }
        input, textarea { padding: 8px; margin: 6px 0; width: 100%; border: 1px solid #ccc; border-radius: 6px; }
        button { padding: 10px 18px; background: #dc3545; color: #fff; border: none; border-radius: 6px; cursor: pointer; transition: 0.3s; }
        button:hover { background: #a71d2a; }
        .msg { font-weight: bold; margin-bottom: 10px; }
        a { text-decoration: none; color: #dc3545; margin-top: 10px; display: inline-block; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
     <div><a href="dashboard.php">🏠 Dashboard</a></div>

<h2>➕ Add Inkoko 75 Expense</h2>

<?php if (!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>

<form method="POST">
    <label>Date:</label><br>
    <input type="date" name="date" required><br>

    <label>Details:</label><br>
    <textarea name="details" required></textarea><br>

    <label>Amount:</label><br>
    <input type="number" name="amount" step="0.01" required><br><br>

    <button type="submit" name="save">💾 Save Expense</button>
</form>

<a href="view_expenses_inkoko75.php">📊 View All Expenses</a> | 
<a href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>
