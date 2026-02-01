<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete_no'])) {
    $delete_no = intval($_GET['delete_no']);
    $conn->query("DELETE FROM other_expenses WHERE NO = $delete_no");
    header("Location: view_other_expenses.php");
    exit();
}

// Handle update
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("UPDATE other_expenses SET DATE = ?, DETAILS = ?, AMOUNT = ? WHERE NO = ?");
    $stmt->bind_param("ssdi", $date, $details, $amount, $id);
    $stmt->execute();
    header("Location: view_other_expenses.php");
    exit();
}

// Fetch all expenses
$result = $conn->query("SELECT * FROM other_expenses ORDER BY DATE ASC");

// Calculate total
$totalResult = $conn->query("SELECT SUM(AMOUNT) AS total_amount FROM other_expenses");
$totalRow = $totalResult->fetch_assoc();
$total = $totalRow['total_amount'] ?? 0;

// Prefill edit form if edit clicked
$edit_row = null;
if (isset($_GET['edit_no'])) {
    $edit_id = intval($_GET['edit_no']);
    $edit_query = $conn->query("SELECT * FROM other_expenses WHERE NO = $edit_id");
    $edit_row = $edit_query->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Other Expenses Overview</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; padding: 30px; }
h2 { text-align: center; color: #c62828; margin-bottom: 20px; }

table { width: 100%; max-width: 1000px; margin: 20px auto; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
th { background: #c62828; color: #fff; }
tr:hover { background: #f9f9f9; }
.actions { text-align: center; margin-bottom: 20px; }
.actions a { text-decoration: none; font-weight: bold; margin: 0 10px; color: #c62828; }
.total { text-align: right; margin: 20px auto; width: 1000px; font-weight: bold; }

/* Buttons */
.btn { padding: 6px 12px; border-radius: 6px; color: #fff; text-decoration: none; font-weight: bold; margin: 0 2px; display: inline-block; }
.btn-edit { background: #28a745; }
.btn-edit:hover { opacity: 0.85; }
.btn-delete { background: #dc3545; }
.btn-delete:hover { opacity: 0.85; }

/* Edit Form */
.edit-form {
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    width: 400px;
    margin: 20px auto;
}
.edit-form h3 {
    text-align: center;
    color: #0066cc;
    margin-bottom: 20px;
}
.edit-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}
.edit-form input[type="date"],
.edit-form input[type="number"],
.edit-form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}
.edit-form textarea {
    min-height: 80px;
    resize: vertical;
}
.edit-form button {
    width: 100%;
    padding: 12px;
    background: #28a745;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
.edit-form button:hover {
    background: #218838;
}
</style>
</head>
<body>

<h2>📊 Other Expenses Overview</h2>

<div class="actions">
    <a href="add_other_expense.php">➕ Add New Expense</a>
    <a href="dashboard.php">🏠 Dashboard</a>
</div>

<?php if ($edit_row): ?>
<div class="edit-form">
    <h3>Edit Expense #<?= $edit_row['NO'] ?></h3>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= $edit_row['NO'] ?>">
        <label>Date:</label>
        <input type="date" name="date" value="<?= $edit_row['DATE'] ?>" required>
        <label>Details:</label>
        <textarea name="details" required><?= htmlspecialchars($edit_row['DETAILS']) ?></textarea>
        <label>Amount:</label>
        <input type="number" name="amount" step="0.01" value="<?= $edit_row['AMOUNT'] ?>" required>
        <button type="submit" name="update">💾 Update</button>
    </form>
</div>
<?php endif; ?>

<table>
    <tr>
        <th>No</th>
        <th>Date</th>
        <th>Details</th>
        <th>Amount</th>
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
                    <a href="?edit_no=<?= $row['NO'] ?>" class="btn btn-edit">✏️ Edit</a>
                    <a href="?delete_no=<?= $row['NO'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this expense?');">🗑️ Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No expenses found.</td></tr>
    <?php endif; ?>
</table>

<div class="total">
    💰 Total Expenses: <?= number_format($total, 2) ?>
</div>

</body>
</html>
