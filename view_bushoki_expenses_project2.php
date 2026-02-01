<?php
session_start();
include('db_connect.php');

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM busoki_expenses_project2 WHERE NO = $id");
    header("Location: view_bushoki_expenses_project2.php");
    exit();
}

// Handle Update Request
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    $sql = "UPDATE busoki_expenses_project2 
            SET DATE = ?, DETAILS = ?, AMOUNT = ? 
            WHERE NO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $date, $details, $amount, $id);
    $stmt->execute();
    header("Location: view_bushoki_expenses_project2.php");
    exit();
}

// Fetch all transactions
$result = $conn->query("SELECT * FROM busoki_expenses_project2 ORDER BY DATE DESC");

// Calculate total amount
$total_result = $conn->query("SELECT SUM(AMOUNT) AS total FROM busoki_expenses_project2");
$total = $total_result->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BUSHOKI PROJECT 2 — View Transactions</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 20px;
        background: #f0f2f5;
    }
    h2 {
        text-align: center;
        color: #222;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    th, td {
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: #0066cc;
        color: #fff;
    }
    tr:hover {
        background: #f9f9f9;
    }
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
    .links {
        text-align: center;
        margin-top: 15px;
    }
    .links a {
        text-decoration: none;
        color: #0066cc;
        font-weight: bold;
    }
</style>
</head>
<body>

<h2>🐔 BUSHOKI PROJECT 2 — All Transactions</h2>
    <center><a href = "dashboard.php">GO TO DASHBOARD</a></center>

<?php
// Show edit form if 'edit' is clicked
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_query = $conn->query("SELECT * FROM busoki_expenses_project2 WHERE NO = $id");
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
        <th>No</th>
        <th>Date</th>
        <th>Details</th>
        <th>Amount (RWF)</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['NO']; ?></td>
        <td><?php echo $row['DATE']; ?></td>
        <td><?php echo htmlspecialchars($row['DETAILS']); ?></td>
        <td><?php echo number_format($row['AMOUNT'], 2); ?></td>
        <td>
            <a href="?edit=<?php echo $row['NO']; ?>" class="btn btn-update">✏️ Edit</a>
            <a href="?delete=<?php echo $row['NO']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this transaction?');">🗑️ Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>

<div class="total">
    💰 <strong>Total Expenses:</strong> <?php echo number_format($total, 2); ?> RWF
</div>

<div class="links">
    <a href="bushoki_expenses_project2.php">➕ Add New Transaction</a> |
    <a href="dashboard.php">🏠 Back to Dashboard</a>
</div>

</body>
</html>
