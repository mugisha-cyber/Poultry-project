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

    $stmt = $conn->prepare("INSERT INTO mugisha_personal_use (DATE, DETAILS, AMOUNT) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $date, $details, $amount);
    $stmt->execute();

    echo "<script>alert('✅ Personal use record added successfully!'); window.location='view_personal_use.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Personal Use page</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            width: 400px;
        }
        h2 {
            color: #00796b;
            margin-bottom: 20px;
        }
        label {
            display: block;
            text-align: left;
            font-weight: 600;
            margin-top: 10px;
            color: #004d40;
        }
        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }
        button {
            margin-top: 20px;
            background: #00796b;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
        }
        button:hover {
            background: #004d40;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #00796b;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
     <div><a href="dashboard.php">🏠 Dashboard</a></div>

<div class="form-container">
    <h2>💰 Add Money for Personal Use</h2>
    <form method="POST">
        <label>Date:</label>
        <input type="date" name="DATE" required>

        <label>Details:</label>
        <input type="text" name="DETAILS" placeholder="Enter use details" required>

        <label>Amount:</label>
        <input type="number" name="AMOUNT" step="0.01" placeholder="Enter amount" required>

        <button type="submit">Add Record</button>
    </form>
    <a href="view_personal_use.php">📊 View Personal Use</a>
</div>

</body>
</html>
