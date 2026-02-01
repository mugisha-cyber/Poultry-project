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
    $conn->query("DELETE FROM expenses_of_200 WHERE NO = $delete_no");
    header("Location: view_inkoko200.php");
    exit();
}

// Handle update
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    $sql = "UPDATE expenses_of_200 SET DATE = ?, DETAILS = ?, AMOUNT = ? WHERE NO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $date, $details, $amount, $id);
    $stmt->execute();
    header("Location: view_inkoko200.php");
    exit();
}

// Fetch all transactions
$result = $conn->query("SELECT * FROM expenses_of_200 ORDER BY DATE DESC");

// Calculate total
$totalResult = $conn->query("SELECT SUM(AMOUNT) as total_amount FROM expenses_of_200");
$totalRow = $totalResult->fetch_assoc();
$total = $totalRow['total_amount'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inkoko 200 Expenses</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f4f4;
        margin: 0;
        padding: 30px 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    h2 { text-align: center; color: #333; margin-bottom: 20px; }

    .actions { margin-bottom: 20px; text-align: center; }
    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        color: #fff;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
        text-decoration: none;
        margin-right: 10px;
    }
    .btn-add { background: #0066cc; } .btn-add:hover { background: #004d99; }
    .btn-dashboard { background: #444; } .btn-dashboard:hover { background: #222; }
    .btn-delete { background: #cc0000; } .btn-delete:hover { background: #990000; }
    .btn-update { background: #28a745; } .btn-update:hover { opacity: 0.85; }

    table {
        border-collapse: collapse;
        width: 100%;
        max-width: 900px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
    th { background: #0066cc; color: white; }
    tr:hover { background: #f1f1f1; }
    tfoot td { font-weight: bold; background: #eee; font-size: 16px; }

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

<h2>🐔 Inkoko 200 - Expenses</h2>

<div class="actions">
    <a href="inkoko200.php" class="btn btn-add">➕ Add New Expense</a>
    <a href="dashboard.php" class="btn btn-dashboard">🏠 Back to Dashboard</a>
</div>

<?php
// Show edit form if 'edit' is clicked
if (isset($_GET['edit_no'])) {
    $edit_id = intval($_GET['edit_no']);
    $edit_query = $conn->query("SELECT * FROM expenses_of_200 WHERE NO = $edit_id");
    $edit_row = $edit_query->fetch_assoc();
?>
<div class="edit-form">
    <h3>Edit Expense #<?php echo $edit_id; ?></h3>
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
        <th>Date</th>
        <th>Details</th>
        <th>Amount</th>
        <th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['NO']; ?></td>
        <td><?php echo $row['DATE']; ?></td>
        <td><?php echo htmlspecialchars($row['DETAILS']); ?></td>
        <td><?php echo number_format($row['AMOUNT'], 2); ?></td>
        <td>
            <a href="view_inkoko200.php?edit_no=<?php echo $row['NO']; ?>" class="btn btn-update">✏️ Edit</a>
            <a href="view_inkoko200.php?delete_no=<?php echo $row['NO']; ?>" 
               class="btn btn-delete" 
               onclick="return confirm('Are you sure you want to delete this expense?');">
               🗑 Delete
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
    <tfoot>
        <tr>
            <td colspan="3">Total</td>
            <td colspan="2"><?php echo number_format($total, 2); ?></td>
        </tr>
    </tfoot>
</table>

</body>
</html>
