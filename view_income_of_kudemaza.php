<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM income_of_kudemaza WHERE NO='$id'");
    header("Location: view_income_of_kudemaza.php");
    exit();
}

// Fetch all incomes
$income_result = $conn->query("SELECT * FROM income_of_kudemaza ORDER BY DATE ASC");

// Calculate totals
$income_total = $conn->query("SELECT SUM(AMOUNT) AS total_income FROM income_of_kudemaza")->fetch_assoc()['total_income'] ?? 0;
$expense_total = $conn->query("SELECT SUM(AMOUNT) AS total_expense FROM kudemaza")->fetch_assoc()['total_expense'] ?? 0;

$profit = $income_total - $expense_total;
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Kudemaza Incomes</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg, #f1f8e9, #c8e6c9);
            padding: 40px;
        }
        h2 {
            text-align: center;
            color: #2e7d32;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th {
            background: #2e7d32;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
        }
        tr:hover { background: #f5f5f5; }
        a.delete {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }
        a.delete:hover { text-decoration: underline; }
        .totals {
            margin-top: 30px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 28%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .box h3 { margin: 0; }
        .income { color: green; }
        .expense { color: red; }
        .profit { color: <?= $profit >= 0 ? 'green' : 'red' ?>; }
    </style>
</head>
<body>

<h2>📈 Kudemaza Project - Income Summary</h2>

<table>
    <tr>
        <th>No</th>
        <th>Date</th>
        <th>Details</th>
        <th>Amount</th>
        <th>Action</th>
    </tr>
    <?php if ($income_result->num_rows > 0): ?>
        <?php while ($row = $income_result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['NO'] ?></td>
                <td><?= $row['DATE'] ?></td>
                <td><?= $row['DETAILS'] ?></td>
                <td><?= number_format($row['AMOUNT'], 2) ?></td>
                <td><a class="delete" href="?delete=<?= $row['NO'] ?>" onclick="return confirm('Delete this income?')">🗑 Delete</a></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5" style="text-align:center;">No income records found.</td></tr>
    <?php endif; ?>
</table>

<div class="totals">
    <div class="box income">
        <h3>Total Income:</h3>
        <p><b><?= number_format($income_total, 2) ?> RWF</b></p>
    </div>
    <div class="box expense">
        <h3>Total Expenses:</h3>
        <p><b><?= number_format($expense_total, 2) ?> RWF</b></p>
    </div>
    <div class="box profit">
        <h3>Profit:</h3>
        <p><b><?= number_format($profit, 2) ?> RWF</b></p>
    </div>
</div>

<br>
<a href="income_of_kudemaza.php">➕ Add Income</a> | 
<a href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>
