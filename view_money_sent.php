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
    $conn->query("DELETE FROM `money_sent` WHERE NO='$no'");
    header("Location: view_money_sent.php");
    exit();
}

// Fetch all money sent
$result = $conn->query("SELECT * FROM `money_sent` ORDER BY DATE ASC");

// Calculate total money sent
$total_row = $conn->query("SELECT SUM(AMOUNT) AS total_sent FROM `money_sent`");
$total_sent = $total_row->fetch_assoc()['total_sent'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view Money Sent Transactions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f7f7f7; }
        h2 { color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 6px 15px rgba(0,0,0,0.05); }
        th, td { padding: 12px 15px; text-align: left; }
        th { background: #007bff; color: #fff; }
        tr:nth-child(even) { background: #f2f2f2; }
        tr:hover { background: #e6f0ff; }
        .btn-delete { padding: 6px 10px; background: #dc3545; color: #fff; border-radius: 6px; border: none; cursor: pointer; transition: 0.3s; }
        .btn-delete:hover { background: #a71d2a; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        .total-box { margin-top: 15px; padding: 12px 18px; background: #ffcc00; font-weight: bold; border-radius: 10px; display: inline-block; font-size: 18px; }
    </style>
</head>
<body>
     <div><a href="dashboard.php">🏠 Dashboard</a></div>

<h2>📊 All Money Sent Transactions</h2>

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
                    <a href="view_money_sent.php?delete=<?= $row['NO'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                        <button class="btn-delete">🗑 Delete</button>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No transactions found.</td></tr>
    <?php endif; ?>
</table>

<!-- Total Money Sent -->
<div class="total-box">💰 TOTAL MONEY SENT: <?= number_format($total_sent, 2) ?></div>

<br><br>
<a href="money_sent.php">➕ Add New Transaction</a> | 
<a href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>

