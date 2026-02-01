<?php
// Database connection
include("db_connect.php");

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM busoki_income_project2 WHERE NO=$id");
    header("Location: view_bushoki_income_project2.php");
    exit;
}

// Handle update request
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    $sql = "UPDATE busoki_income_project2 SET DATE=?, DETAILS=?, AMOUNT=? WHERE NO=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $date, $details, $amount, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: view_bushoki_income_project2.php");
    exit;
}

// Fetch all income records
$result_income = $conn->query("SELECT * FROM busoki_income_project2 ORDER BY DATE DESC");

// Calculate totals
$total_income = 0;
$res = $conn->query("SELECT SUM(AMOUNT) AS total FROM busoki_income_project2");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $total_income = $row['total'] ?? 0;
}

// If you have a related expenses table for Bushoki
$total_expenses = 0;
$res_exp = $conn->query("SELECT SUM(AMOUNT) AS total FROM busoki_expenses_project2");
if ($res_exp && $res_exp->num_rows > 0) {
    $row = $res_exp->fetch_assoc();
    $total_expenses = $row['total'] ?? 0;
}

$profit = $total_income - $total_expenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bushoki Income Project 2 - Records</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        padding: 20px;
    }
    h2 {
        text-align: center;
        color: #0284c7;
    }
    table {
        width: 90%;
        margin: 20px auto;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }
    th {
        background: #0284c7;
        color: white;
    }
    .btn {
        padding: 5px 10px;
        border: none;
        cursor: pointer;
        color: white;
        border-radius: 4px;
    }
    .update { background-color: #16a34a; }
    .delete { background-color: #dc2626; }
    .summary {
        text-align: center;
        font-size: 1.2em;
        margin-top: 20px;
    }
    .profit { color: green; font-weight: bold; }
    .loss { color: red; font-weight: bold; }
    form.update-form {
        display: flex;
        gap: 5px;
        justify-content: center;
        flex-wrap: wrap;
    }
    input {
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    
    
    .links {
        margin-top: 15px;
        text-align: center;
    }

    .links a {
        color: #0066cc;
        text-decoration: none;
        font-weight: bold;
        margin: 0 5px;
    }

    .links a:hover {
        text-decoration: underline;
    }
    
</style>
</head>
<body>

<h2>🐔 Bushoki Income Project 2 - Records</h2>

<table>
    <tr>
        <th>NO</th>
        <th>DATE</th>
        <th>DETAILS</th>
        <th>AMOUNT (RWF)</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result_income->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['NO']; ?></td>
        <td><?php echo $row['DATE']; ?></td>
        <td><?php echo htmlspecialchars($row['DETAILS']); ?></td>
        <td><?php echo number_format($row['AMOUNT'], 2); ?></td>
        <td>
            <form class="update-form" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['NO']; ?>">
                <input type="date" name="date" value="<?php echo $row['DATE']; ?>" required>
                <input type="text" name="details" value="<?php echo htmlspecialchars($row['DETAILS']); ?>" required>
                <input type="number" name="amount" step="0.01" value="<?php echo $row['AMOUNT']; ?>" required>
                <button type="submit" name="update" class="btn update">Update</button>
                <a href="?delete=<?php echo $row['NO']; ?>" class="btn delete" onclick="return confirm('Delete this record?');">Delete</a>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>
    
     <div class="links">
        <a href="view_bushoki_expenses_project2.php">📊 View All Transactions</a> |
        <a href="dashboard.php">🏠 Dashboard</a>
    </div>

<div class="summary">
    <p><strong>Total Income:</strong> <?php echo number_format($total_income, 2); ?> RWF</p>
    <p><strong>Total Expenses:</strong> <?php echo number_format($total_expenses, 2); ?> RWF</p>
    <p><strong>Profit/Loss:</strong>
        <span class="<?php echo ($profit >= 0) ? 'profit' : 'loss'; ?>">
            <?php echo number_format($profit, 2); ?> RWF
        </span>
    </p>
</div>

</body>
</html>
