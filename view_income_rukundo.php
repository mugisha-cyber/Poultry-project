<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $no = $_GET['delete'];
    $conn->query("DELETE FROM `income_of_rukundo` WHERE NO='$no'");
    header("Location: view_income_rukundo.php");
    exit();
}

// Fetch all income
$result = $conn->query("SELECT * FROM `income_of_rukundo` ORDER BY NO DESC");

// Calculate total income
$total_income_row = $conn->query("SELECT SUM(AMOUNT) AS total_income FROM `income_of_rukundo`");
$total_income = $total_income_row->fetch_assoc()['total_income'] ?? 0;

// Calculate total expenses
$total_expenses_row = $conn->query("SELECT SUM(AMOUNT) AS total_expenses FROM `rukundo`");
$total_expenses = $total_expenses_row->fetch_assoc()['total_expenses'] ?? 0;

// Calculate profit
$profit = $total_income - $total_expenses;
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view Rukundo Income</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f2f2f2; }
        h2 { color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 6px 15px rgba(0,0,0,0.05); }
        th, td { padding: 12px 15px; text-align: left; }
        th { background: #0066cc; color: #fff; }
        tr:nth-child(even) { background: #f2f2f2; }
        tr:hover { background: #e0f0ff; }
        a { text-decoration: none; color: #0066cc; }
        a:hover { text-decoration: underline; }
        .btn-delete { padding: 6px 10px; background: #cc0000; color: #fff; border-radius: 6px; border: none; cursor: pointer; transition: 0.3s; }
        .btn-delete:hover { background: #990000; }
        .total-box { margin-top: 15px; padding: 12px 18px; background: #ffdd57; font-weight: bold; border-radius: 10px; display: inline-block; font-size: 18px; }
        .profit-box { margin-top: 10px; padding: 12px 18px; font-weight: bold; border-radius: 10px; display: inline-block; font-size: 18px; color: #fff; }
        .profit-positive { background: #28a745; }
        .profit-negative { background: #dc3545; }
    </style>
</head>
<body>

<h2>📊 Rukundo Income Transactions</h2>

<table>
    <tr>
        <th>NO</th>
        <th>DATE</th>
        <th>DETAILS</th>
        <th>AMOUNT</th>
        <th>Action</th>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['NO'] ?></td>
                <td><?= $row['DATE'] ?></td>
                <td><?= $row['DETAILS'] ?></td>
                <td><?= number_format($row['AMOUNT'], 2) ?></td>
                <td>
                    <a href="view_income_rukundo.php?delete=<?= $row['NO'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                        <button class="btn-delete">🗑 Delete</button>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No income transactions found.</td></tr>
    <?php endif; ?>
</table>

<!-- Total Income -->
<div class="total-box">💰 TOTAL INCOME: <?= number_format($total_income, 2) ?></div>

<!-- Profit -->
<div class="profit-box <?= $profit >= 0 ? 'profit-positive' : 'profit-negative' ?>">
    📈 PROFIT: <?= number_format($profit, 2) ?>
</div>

<br><br>
<a href="income_rukundo.php">➕ Add New Income</a> | 
<a href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>
