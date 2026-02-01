<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ✅ Handle delete request
if (isset($_GET['delete'])) {
    $no = $_GET['delete'];
    $conn->query("DELETE FROM kudemaza WHERE NO='$no'");
    header("Location: view_kudemaza.php");
    exit();
}

// ✅ Fetch all transactions
$result = $conn->query("SELECT * FROM kudemaza ORDER BY NO ASC");

// ✅ Calculate total amount
$total_result = $conn->query("SELECT SUM(AMOUNT) AS total_amount FROM kudemaza");
$total_row = $total_result->fetch_assoc();
$total_amount = $total_row['total_amount'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Kudemaza Transactions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #fafafa; }
        table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #0066cc; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        a { text-decoration: none; color: #0066cc; margin-right: 10px; }
        a:hover { text-decoration: underline; }
        h2 { color: #333; }
        .total-box {
            background: yellow;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>

<h2>📊 KUDEMAZA PROJECT - All TRANSACTIONS INKOKO 810</h2>

<table>
    <tr>
        <th>NO</th>
        <th>DATE</th>
        <th>DETAILS</th>
        <th>AMOUNT</th>
        <th>Action</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['NO'] ?></td>
                <td><?= $row['DATE'] ?></td>
                <td><?= $row['DETAILS'] ?></td>
                <td><?= $row['AMOUNT'] ?></td>
                <td>
                    <a href="view_kudemaza.php?delete=<?= $row['NO'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">🗑 Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No transactions found.</td></tr>
    <?php endif; ?>
</table>

<!-- ✅ Total displayed below the table -->
<?php if ($total_amount > 0): ?>
    <div class="total-box">💰 TOTAL MONEY SPENT: <?= number_format($total_amount, 2) ?></div>
<?php endif; ?>

<br><br>
<a href="kudemaza.php">➕ Add New Transaction</a> | 
<a href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>
