<?php
session_start();
include('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM rukundo WHERE NO = $delete_id");
    header("Location: view_rukundo.php");
    exit();
}

// Handle update
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    $sql = "UPDATE rukundo SET date = ?, details = ?, amount = ? WHERE NO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $date, $details, $amount, $id);
    $stmt->execute();

    header("Location: view_rukundo.php");
    exit();
}

// Fetch row for edit
$editRow = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $query = $conn->query("SELECT * FROM rukundo WHERE NO = $id LIMIT 1");
    if ($query && $query->num_rows > 0) {
        $editRow = $query->fetch_assoc();
    }
}

// Fetch transactions
$result = $conn->query("SELECT * FROM rukundo ORDER BY date ASC");

// Calculate total
$total_result = $conn->query("SELECT SUM(amount) AS total_amount FROM rukundo");
$total_row = $total_result->fetch_assoc();
$total_amount = $total_row['total_amount'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bushoki Project 1 Transactions</title>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f2f5;
    padding: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    margin-top: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

th {
    background: #0066cc;
    color: #fff;
}

.btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    color: white;
}

.btn-update {
    background: #28a745;
}

.edit-form {
    width: 400px;
    margin: 30px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.edit-form input,
.edit-form textarea {
    width: 100%;
    margin-bottom: 10px;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.edit-form button {
    background: #0066cc;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.edit-form button:hover {
    background: #004d99;
}

.total-box {
    background-color: yellow;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 18px;
    font-weight: bold;
    display: inline-block;
    margin-top: 20px;
}
</style>
</head>

<body>

<?php if ($editRow) { ?>
<div class="edit-form">
    <h3>✏️ Edit Transaction #<?php echo $editRow['NO']; ?></h3>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $editRow['NO']; ?>">

        <label>Date</label>
        <input type="date" name="date" value="<?php echo htmlspecialchars($editRow['date']); ?>" required>

        <label>Details</label>
        <textarea name="details" required><?php echo htmlspecialchars($editRow['details']); ?></textarea>

        <label>Amount</label>
        <input type="number" name="amount" step="0.01" value="<?php echo htmlspecialchars($editRow['amount']); ?>" required>

        <button type="submit" name="update">💾 Update</button>
    </form>
</div>
<?php } ?>

<a href="dashboard.php">🏠 Dashboard</a>
<h2>📋 All Busoki project 1 [100] Transactions</h2>

<?php
if ($result && $result->num_rows > 0) {
    echo "<table>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Details</th>
            <th>Amount</th>
            <th>Action</th>
        </tr>";

    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>".$no++."</td>
            <td>".htmlspecialchars($row['date'])."</td>
            <td>".htmlspecialchars($row['details'])."</td>
            <td>".number_format($row['amount'],2)."</td>
            <td>
                <a href='?edit=".$row['NO']."' class='btn btn-update'>✏️ Edit</a>
                <a href='view_rukundo.php?delete_id=".$row['NO']."' onclick='return confirm(\"Are you sure?\");'>
                    <button>Delete</button>
                </a>
            </td>
        </tr>";
    }
    echo "</table>";

    echo "<div class='total-box'>💰 TOTAL AMOUNT: ".number_format($total_amount,2)."</div>";
} else {
    echo "<p>⚠️ No transactions found.</p>";
}
?>

<br><br>
<a href="rukundo_project.php">➕ Add New Transaction</a>

</body>
</html>
