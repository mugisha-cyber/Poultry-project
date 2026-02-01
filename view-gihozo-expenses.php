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
    $conn->query("DELETE FROM gihozo_personal_use WHERE NO='$no'");
    header("Location: view-gihozo-expenses.php");
    exit();
}

// Handle update
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = floatval($_POST['amount']);

    $stmt = $conn->prepare("UPDATE gihozo_personal_use SET DATE=?, DETAILS=?, AMOUNT=? WHERE NO=?");
    $stmt->bind_param("ssdi", $date, $details, $amount, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: view-gihozo-expenses.php");
    exit();
}

// Prefill edit form if edit clicked
$edit_row = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_query = $conn->query("SELECT * FROM gihozo_personal_use WHERE NO = $edit_id");
    $edit_row = $edit_query->fetch_assoc();
}

// Fetch all records
$result = $conn->query("SELECT * FROM gihozo_personal_use ORDER BY DATE ASC");

// Calculate total personal use
$total_use = $conn->query("SELECT SUM(AMOUNT) AS total FROM gihozo_personal_use")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Personal Use Transactions - Gihozo</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #e0f7fa, #b2ebf2); padding: 40px; text-align: center; }
        h2 { color: #004d40; margin-bottom: 30px; }

        table { margin: auto; border-collapse: collapse; width: 80%; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        th { background: #00796b; color: white; padding: 12px; font-size: 16px; }
        td { padding: 10px; border-bottom: 1px solid #ddd; color: #333; }
        tr:hover { background: #e0f2f1; }

        a.delete { color: #d32f2f; font-weight: bold; text-decoration: none; }
        a.delete:hover { text-decoration: underline; }
        a.edit { color: #0288d1; font-weight: bold; text-decoration: none; }
        a.edit:hover { text-decoration: underline; }

        .summary-box { margin-top: 30px; background: white; display: inline-block; padding: 20px 50px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .summary-box h3 { color: #00796b; }
        .summary-box p { font-size: 22px; color: #004d40; font-weight: bold; }

        a.nav { margin-top: 20px; display: inline-block; color: #00796b; font-weight: bold; text-decoration: none; }
        a.nav:hover { text-decoration: underline; }

        /* Edit Form */
        .edit-form {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            width: 400px;
            margin: 20px auto;
        }
        .edit-form h3 { text-align: center; color: #0288d1; margin-bottom: 20px; }
        .edit-form label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .edit-form input[type="date"], .edit-form input[type="number"], .edit-form textarea {
            width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px;
        }
        .edit-form textarea { min-height: 80px; resize: vertical; }
        .edit-form button {
            width: 100%; padding: 12px; background: #0288d1; color: #fff; border: none; border-radius: 8px;
            font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        .edit-form button:hover { background: #0277bd; }
    </style>
</head>
<body>

<div><a href="dashboard.php">🏠 Dashboard</a></div>

<h2>📋 Personal Use Records - Gihozo</h2>

<?php if ($edit_row): ?>
<div class="edit-form">
    <h3>Edit Record #<?= $edit_row['NO'] ?></h3>
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
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['NO'] ?></td>
                <td><?= $row['DATE'] ?></td>
                <td><?= htmlspecialchars($row['DETAILS']) ?></td>
                <td><?= number_format($row['AMOUNT'], 2) ?> RWF</td>
                <td>
                    <a class="edit" href="?edit=<?= $row['NO'] ?>">✏️ Edit</a> | 
                    <a class="delete" href="?delete=<?= $row['NO'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">🗑 Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5" style="text-align:center;">No personal use records found.</td></tr>
    <?php endif; ?>
</table>

<div class="summary-box">
    <h3>Total Personal Use</h3>
    <p><?= number_format($total_use, 2) ?> RWF</p>
</div>

<br><br>
<a class="nav" href="gihozo-personal-use.php">➕ Add New Record</a> | 
<a class="nav" href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>
