<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle Add Income
if (isset($_POST['add'])) {
    $date = $_POST['date'];
    $source = $_POST['source'];
    $amount = floatval($_POST['amount']);

    $stmt = $conn->prepare("INSERT INTO gihozo_income (DATE, SOURCE, AMOUNT) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $date, $source, $amount);
    $stmt->execute();
    $stmt->close();
    header("Location: gihozo-income.php");
    exit();
}

// Handle Update Income
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $source = $_POST['source'];
    $amount = floatval($_POST['amount']);

    $stmt = $conn->prepare("UPDATE gihozo_income SET DATE=?, SOURCE=?, AMOUNT=? WHERE NO=?");
    $stmt->bind_param("ssdi", $date, $source, $amount, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: view-gihozo-income.php");
    exit();
}

// Handle Delete Income
if (isset($_GET['delete'])) {
    $no = intval($_GET['delete']);
    $conn->query("DELETE FROM gihozo_income WHERE NO = $no");
    header("Location: view-gihozo-income.php");
    exit();
}

// Prefill edit form if edit clicked
$edit_row = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_query = $conn->query("SELECT * FROM gihozo_income WHERE NO = $edit_id");
    $edit_row = $edit_query->fetch_assoc();
}

// Fetch all records
$result = $conn->query("SELECT * FROM gihozo_income ORDER BY DATE ASC");

// Calculate total income
$total_income = $conn->query("SELECT SUM(AMOUNT) AS total FROM gihozo_income")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gihozo Income Records</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #fff9c4, #fff176); padding: 30px; text-align: center; }
        h2 { color: #f57f17; margin-bottom: 30px; }

        /* Form Styling */
        form { 
            background: #ffffff; padding: 25px 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); 
            width: 400px; margin: 20px auto;
        }
        form h3 { color: #fbc02d; margin-bottom: 20px; text-align: center; }
        form label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        form input[type="date"], form input[type="number"], form input[type="text"] {
            width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px;
        }
        form button { width: 100%; padding: 12px; background: #fbc02d; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        form button:hover { background: #f9a825; }

        /* Table Styling */
        table { margin: auto; border-collapse: collapse; width: 90%; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        th { background: #f57f17; color: white; padding: 12px; font-size: 16px; }
        td { padding: 10px; border-bottom: 1px solid #ddd; color: #333; }
        tr:hover { background: #fff3e0; }
        a.delete { color: #d32f2f; font-weight: bold; text-decoration: none; }
        a.delete:hover { text-decoration: underline; }
        a.edit { color: #0288d1; font-weight: bold; text-decoration: none; }
        a.edit:hover { text-decoration: underline; }

        /* Summary Box */
        .summary-box { margin-top: 20px; background: white; display: inline-block; padding: 20px 50px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .summary-box h3 { color: #f57f17; }
        .summary-box p { font-size: 22px; color: #f57f17; font-weight: bold; }

        /* Navigation */
        a.nav { margin-top: 20px; display: inline-block; color: #f57f17; font-weight: bold; text-decoration: none; }
        a.nav:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div><a class="nav" href="dashboard.php">🏠 Dashboard</a></div>

<h2>📋 Gihozo Income Records</h2>

<!-- Add / Update Form -->
<form method="POST" action="">
    <?php if ($edit_row): ?>
        <h3>Edit Income #<?= $edit_row['NO'] ?></h3>
        <input type="hidden" name="id" value="<?= $edit_row['NO'] ?>">

        <label>Date:</label>
        <input type="date" name="date" value="<?= $edit_row['DATE'] ?>" required>

        <label>Source:</label>
        <input type="text" name="source" value="<?= htmlspecialchars($edit_row['SOURCE']) ?>" required>

        <label>Amount:</label>
        <input type="number" name="amount" step="0.01" value="<?= $edit_row['AMOUNT'] ?>" required>

        <button type="submit" name="update">💾 Update</button>
    <?php else: ?>
        <h3>Add New Income</h3>

        <label>Date:</label>
        <input type="date" name="date" required>

        <label>Source:</label>
        <input type="text" name="source" required>

        <label>Amount:</label>
        <input type="number" name="amount" step="0.01" required>

        <button type="submit" name="add">➕ Add Income</button>
    <?php endif; ?>
</form>

<!-- Records Table -->
<table>
    <tr>
        <th>No</th>
        <th>Date</th>
        <th>Source</th>
        <th>Amount</th>
        <th>Action</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['NO'] ?></td>
                <td><?= $row['DATE'] ?></td>
                <td><?= htmlspecialchars($row['DETAILS']) ?></td>
                <td><?= number_format($row['AMOUNT'], 2) ?> RWF</td>
                <td>
                    <a class="edit" href="?edit=<?= $row['NO'] ?>">✏️ Edit</a> | 
                    <a class="delete" href="?delete=<?= $row['NO'] ?>" onclick="return confirm('Are you sure you want to delete this income?');">🗑 Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5" style="text-align:center;">No income records found.</td></tr>
    <?php endif; ?>
</table>

<!-- Total Income -->
<div class="summary-box">
    <h3>Total Income</h3>
    <p><?= number_format($total_income, 2) ?> RWF</p>
</div>

</body>
</html>
