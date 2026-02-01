<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['DATE'];
    $details = $_POST['DETAILS'];
    $amount = $_POST['AMOUNT'];

    $stmt = $conn->prepare("INSERT INTO income_of_kudemaza (DATE, DETAILS, AMOUNT) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $date, $details, $amount);
    $stmt->execute();

    echo "<script>alert('✅ Income Added Successfully!'); window.location='view_income_of_kudemaza.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Kudemaza Income</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg, #e8f5e9, #c8e6c9);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 50px;
        }
        .form-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            color: #2e7d32;
            text-align: center;
        }
        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background: #1b5e20;
        }
        a {
            text-decoration: none;
            color: #2e7d32;
            display: inline-block;
            margin-top: 15px;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
     <div><a href="dashboard.php">🏠 Dashboard</a></div>

<div class="form-container">
    <h2>➕ Add Kudemaza Income</h2>
    <form method="POST">
        <label>Date:</label>
        <input type="date" name="DATE" required>
        <label>Details:</label>
        <input type="text" name="DETAILS" placeholder="Enter details" required>
        <label>Amount:</label>
        <input type="number" name="AMOUNT" step="0.01" placeholder="Enter amount" required>
        <button type="submit">Add Income</button>
    </form>
    <a href="view_income_of_kudemaza.php">📊 View All Incomes</a>
    <a href="dashboard.php">📊 GO TO HOME DASHBOARD</a>
</div>

</body>
</html>
