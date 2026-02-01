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
        $sql = "INSERT INTO `income_of_rukundo` (DATE, DETAILS, AMOUNT) VALUES ('$date', '$details', '$amount')";
        if ($conn->query($sql)) {
            $msg = "✅ Income added successfully!";
        } else {
            $msg = "❌ Database error: " . $conn->error;
        }
    } else {
        $msg = "⚠️ Please fill all fields before saving.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Rukundo Income</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: black;
    }

    .container {
        background: #fff;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        width: 400px;
        text-align: center;
    }

    h2 {
        color: #333;
        margin-bottom: 20px;
    }

    form {
        text-align: left;
    }

    label {
        font-weight: bold;
        color: #444;
    }

    input, textarea {
        padding: 10px;
        margin: 8px 0 15px 0;
        width: 100%;
        border: 1px solid #bbb;
        border-radius: 8px;
        font-size: 14px;
    }

    button {
        padding: 10px 18px;
        background: #0066cc;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        width: 100%;
        transition: 0.3s;
    }

    button:hover {
        background: #004d99;
    }

    .msg {
        font-weight: bold;
        margin-bottom: 15px;
        color: #333;
        background: #f1f1f1;
        padding: 8px;
        border-radius: 8px;
        text-align: center;
    }

    .links {
        margin-top: 15px;
        text-align: center;
    }

    .links a {
        color: #0066cc;
        text-decoration: none;
        font-weight: bold;
        margin: 0 5px;
    }

    .links a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
     <div><a href="dashboard.php">🏠 Dashboard</a></div>

<div class="container">
    <h2>➕ Add Rukundo Income</h2>

    <?php if (!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>

    <form method="POST">
        <label>Date:</label>
        <input type="date" name="date" required>

        <label>Details:</label>
        <textarea name="details" required></textarea>

        <label>Amount:</label>
        <input type="number" name="amount" step="0.01" required>

        <button type="submit" name="save">💾 Save Income</button>
    </form>

    <div class="links">
        <a href="view_income_rukundo.php">📊 View All Income</a> |
        <a href="dashboard.php">🏠 Dashboard</a>
    </div>
</div>

</body>
</html>
