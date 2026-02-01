<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $no = intval($_GET['delete']);
    $conn->query("DELETE FROM `income_of_200` WHERE NO = $no");
    header("Location: view_income_200.php");
    exit();
}

// Handle update
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    $sql = "UPDATE `income_of_200` SET DATE = ?, DETAILS = ?, AMOUNT = ? WHERE NO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $date, $details, $amount, $id);
    $stmt->execute();
    header("Location: view_income_200.php");
    exit();
}

// Fetch all income
$result = $conn->query("SELECT * FROM `income_of_200` ORDER BY NO DESC");

// Calculate totals
$total_income_row = $conn->query("SELECT SUM(AMOUNT) AS total_income FROM `income_of_200`");
$total_income = $total_income_row->fetch_assoc()['total_income'] ?? 0;

$total_expenses_row = $conn->query("SELECT SUM(AMOUNT) AS total_expenses FROM `expenses_of_200`");
$total_expenses = $total_expenses_row->fetch_assoc()['total_expenses'] ?? 0;

// Calculate profit
$profit = $total_income - $total_expenses;
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Inkoko 200 Income</title>
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
    .btn-update { padding: 6px 10px; background: #28a745; color: #fff; border-radius: 6px; border: none; cursor: pointer; transition: 0.3s; margin-right: 5px; }
    .btn-update:hover { opacity: 0.85; }

    .total-box { margin-top: 15px; padding: 12px 18px; background: #ffdd57; font-weight: bold; border-radius: 10px; display: inline-block; font-size: 18px; margin-right: 15px; }
    .profit-box { margin-top: 10px; padding: 12px 18px; font-weight: bold; border-radius: 10px; display: inline-block; font-size: 18px; color: #fff; }
    .profit-positive { background: #28a745; }
    .profit-negative { background: #dc3545; }

    /* Edit Form */
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
    .edit-form button:hover { background: #004d99; }
</style>
</head>
<body>

<h2>📊 Inkoko 200 Income Transactions</h2>

<?php
// Show edit form if 'edit_no' is clicked
if (isset($_GET['edit_no'])) {
    $edit_id = intval($_GET['edit_no']);
    $edit_query = $conn->query("SELECT * FROM `income_of_200` WHERE NO = $edit_id");
    $edit_row = $edit_query->fetch_assoc();
?>
<div class="edit-form">
    <h3>Edit Income #<?php echo $edit_id; ?></h3>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $edit_row['NO']; ?>">
        <label>Date:</label>
        <input type="date" name="date" value="<?php echo $edit_row['DATE']; ?>" required>
        <label>Details:</label>
        <textarea name="details" required><?php echo htmlspecialchars($edit_row['DETAILS']); ?></textarea>
        <label>Amount:</label>
        <input type="number" name="amount" step="0.01" value="<?php echo $edit_row['AMOUNT']; ?>" required>
        <button type="submit" name="update">💾 Update</button>
    </form>
</div>
<?php } ?>

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
                <td><?= htmlspecialchars($row['DETAILS']) ?></td>
                <td><?= number_format($row['AMOUNT'], 2) ?></td>
                <td>
                    <a href="view_income_200.php?edit_no=<?= $row['NO'] ?>">
                        <button class="btn-update">✏️ Edit</button>
                    </a>
                    <a href="view_income_200.php?delete=<?= $row['NO'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                        <button class="btn-delete">🗑 Delete</button>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No income transactions found.</td></tr>
    <?php endif; ?>
</table>

<!-- Totals and Profit -->
<div class="total-box">💰 TOTAL INCOME: <?= number_format($total_income, 2) ?></div>
<div class="total-box">💸 TOTAL EXPENSES: <?= number_format($total_expenses, 2) ?></div>
<div class="profit-box <?= $profit >= 0 ? 'profit-positive' : 'profit-negative' ?>">
    📈 PROFIT: <?= number_format($profit, 2) ?>
</div>

<br><br>
<a href="income_200.php">➕ Add New Income</a> | 
<a href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>
