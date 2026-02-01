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
    $conn->query("DELETE FROM `income_of_75` WHERE NO='$no'");
    header("Location: view-income_of_inkoko75.php");
    exit();
}

// Handle Update Request
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    $sql = "UPDATE income_of_75 
            SET DATE = ?, DETAILS = ?, AMOUNT = ? 
            WHERE NO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $date, $details, $amount, $id);
    $stmt->execute();
    header("Location: view-income_of_inkoko75.php");
    exit();
}

// Fetch all income
$result = $conn->query("SELECT * FROM `income_of_75` ORDER BY DATE ASC, NO ASC");

// Calculate total income
$total_income_row = $conn->query("SELECT SUM(AMOUNT) AS total_income FROM `income_of_75`");
$total_income = $total_income_row->fetch_assoc()['total_income'] ?? 0;

// Calculate total expenses
$total_expenses_row = $conn->query("SELECT SUM(AMOUNT) AS total_expenses FROM `expenses_of_75`");
$total_expenses = $total_expenses_row->fetch_assoc()['total_expenses'] ?? 0;

// Calculate profit
$profit = $total_income - $total_expenses;
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view Inkoko 75 Income & Profit</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f7f7f7; }
        h2 { color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 6px 15px rgba(0,0,0,0.05); }
        th, td { padding: 12px 15px; text-align: left; }
        th { background: #28a745; color: #fff; }
        tr:nth-child(even) { background: #f2f2f2; }
        tr:hover { background: #e6f7ed; }
        .btn-delete { padding: 6px 10px; background: #dc3545; color: #fff; border-radius: 6px; border: none; cursor: pointer; transition: 0.3s; }
        .btn-delete:hover { background: #a71d2a; }
        .total-box { margin-top: 15px; padding: 12px 18px; background: #ffcc00; font-weight: bold; border-radius: 10px; display: inline-block; font-size: 18px; margin-right: 15px; }
        .profit-box { margin-top: 10px; padding: 12px 18px; font-weight: bold; border-radius: 10px; display: inline-block; font-size: 18px; color: #fff; }
        .profit-positive { background: #28a745; }
        .profit-negative { background: #dc3545; }
        
    
        .btn {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        color: #fff;
    }
    .btn-update {
        background: #28a745;
    }
    .btn-delete {
        background: #dc3545;
    }
    .btn:hover {
        opacity: 0.85;
    }
    .total {
        text-align: right;
        font-weight: bold;
        margin-top: 20px;
        background: #fff;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .edit-form {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        width: 400px;
        margin: 30px auto;
    }
    .edit-form input, .edit-form textarea {
        width: 100%;
        margin-bottom: 10px;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    .edit-form button {
        padding: 10px 15px;
        background: #0066cc;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .edit-form button:hover {
        background: #004d99;
    }
     
        
    </style>
</head>
<body>
     <div><a href="dashboard.php">🏠 Dashboard</a></div>

<h2>📊 Inkoko 75 Income Transactions</h2>

    <?php
// Show edit form if 'edit' is clicked
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_query = $conn->query("SELECT * FROM income_of_75 WHERE NO = $id");
    $row = $edit_query->fetch_assoc();
?>
<div class="edit-form">
    <h3>Edit Transaction #<?php echo $id; ?></h3>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $row['NO']; ?>">
        <label>Date:</label>
        <input type="date" name="date" value="<?php echo $row['DATE']; ?>" required>
        <label>Details:</label>
        <textarea name="details" required><?php echo $row['DETAILS']; ?></textarea>
        <label>Amount:</label>
        <input type="number" name="amount" step="0.01" value="<?php echo $row['AMOUNT']; ?>" required>
        <button type="submit" name="update">💾 Update</button>
    </form>
</div>
<?php
}
?>
    
    
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
                     <a href="?edit=<?php echo $row['NO']; ?>" class="btn btn-update">✏️ Edit</a>
                    <a href="view-income_of_inkoko75.php?delete=<?= $row['NO'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                        <button class="btn-delete">🗑 Delete</button>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No income transactions found.</td></tr>
    <?php endif; ?>
</table>

<div class="total-box">💰 TOTAL INCOME: <?= number_format($total_income, 2) ?></div>
<div class="total-box">💸 TOTAL EXPENSES: <?= number_format($total_expenses, 2) ?></div>
<div class="profit-box <?= $profit >= 0 ? 'profit-positive' : 'profit-negative' ?>">📈 PROFIT: <?= number_format($profit, 2) ?></div>

<br><br>
<a href="income_of_75.php">➕ Add New Income</a> | 
<a href="expenses_of_75.php">➕ Add New Expense</a> | 
<a href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>

