<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle delete (mugisha_personal_use)
if (isset($_GET['delete'])) {
    $no = $_GET['delete'];
    $conn->query("DELETE FROM mugisha_personal_use WHERE NO='$no'");
    header("Location: view_personal_use.php");
    exit();
}

// Handle update (mugisha_personal_use)
if (isset($_POST['update'])){
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];

    $sql = "UPDATE mugisha_personal_use SET DATE = ?, DETAILS = ?, AMOUNT = ? WHERE NO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $date, $details, $amount, $id);
    $stmt->execute();

    header("Location: view_personal_use.php");
    exit();
}

// Fetch row for edit (mugisha_personal_use)
$editRow = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $query = $conn->query("SELECT * FROM mugisha_personal_use WHERE NO = $id LIMIT 1");
    if ($query && $query->num_rows > 0) {
        $editRow = $query->fetch_assoc();
    }
}

// Fetch all records (mugisha_personal_use)
$result = $conn->query("SELECT * FROM mugisha_personal_use ORDER BY DATE ASC");

// Calculate total personal use
$total_use = $conn->query("SELECT SUM(AMOUNT) AS total FROM mugisha_personal_use")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Personal Use Transaction</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
            padding: 40px;
            text-align: center;
        }
        h2 {
            color: #004d40;
            margin-bottom: 30px;
        }
        table {
            margin: auto;
            border-collapse: collapse;
            width: 80%;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        th {
            background: #00796b;
            color: white;
            padding: 12px;
            font-size: 16px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            color: #333;
        }
        tr:hover {
            background: #e0f2f1;
        }
        a.delete {
            color: #d32f2f;
            font-weight: bold;
            text-decoration: none;
        }
        a.delete:hover {
            text-decoration: underline;
        }
        .summary-box {
            margin-top: 30px;
            background: white;
            display: inline-block;
            padding: 20px 50px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .summary-box h3 {
            color: #00796b;
        }
        .summary-box p {
            font-size: 22px;
            color: #004d40;
            font-weight: bold;
        }
        a.nav {
            margin-top: 20px;
            display: inline-block;
            color: #00796b;
            font-weight: bold;
            text-decoration: none;
        }
        a.nav:hover {
            text-decoration: underline;
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
            background: #00796b;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .edit-form button:hover {
            background: #004d40;
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
        <input type="date" name="date" value="<?php echo htmlspecialchars($editRow['DATE']); ?>" required>

        <label>Details</label>
        <textarea name="details" required><?php echo htmlspecialchars($editRow['DETAILS']); ?></textarea>

        <label>Amount</label>
        <input type="number" name="amount" step="0.01" value="<?php echo htmlspecialchars($editRow['AMOUNT']); ?>" required>

        <button type="submit" name="update">💾 Update</button>
    </form>
</div>
<?php } ?>

<div><a href="dashboard.php">🏠 Dashboard</a></div>

<h2>📋 Money for Personal Use - All Records</h2>

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
                <td><?= $row['DETAILS'] ?></td>
                <td><?= number_format($row['AMOUNT'], 2) ?> RWF</td>
                <td>
                    <a href="?edit=<?= $row['NO'] ?>" class="btn btn-update">✏️ Edit</a>
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
<a class="nav" href="personal_use.php">➕ Add New Record</a> | 
<a class="nav" href="dashboard.php">🏠 Back to Dashboard</a>

</body>
</html>
