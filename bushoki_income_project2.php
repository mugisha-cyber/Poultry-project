<?php
include('db_connect.php');

// Add income
if (isset($_POST['add_income'])) {
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];
    $sql = "INSERT INTO busoki_income_project2 (DATE, DETAILS, AMOUNT) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $date, $details, $amount);
    $stmt->execute();
    $stmt->close();
}

// Add expense
if (isset($_POST['add_expense'])) {
    $date = $_POST['date'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];
    $sql = "INSERT INTO busoki_income_project2 (DATE, DETAILS, AMOUNT) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $date, $details, $amount);
    $stmt->execute();
    $stmt->close();
}

// Totals
$total_income = 0;
$total_expenses = 0;

$result_income = $conn->query("SELECT SUM(AMOUNT) AS total FROM busoki_income_project2");
if ($result_income->num_rows > 0) {
    $row = $result_income->fetch_assoc();
    $total_income = $row['total'] ?? 0;
}

$result_expense = $conn->query("SELECT SUM(AMOUNT) AS total FROM busoki_expenses_project2");
if ($result_expense->num_rows > 0) {
    $row = $result_expense->fetch_assoc();
    $total_expenses = $row['total'] ?? 0;
}

$profit = $total_income - $total_expenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bushoki Income & Expenses Project 2</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        form {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 400px;
            margin: auto;
        }
        input, button {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #0284c7;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        .summary {
            text-align: center;
            font-size: 1.2em;
            margin-top: 30px;
        }
        .profit {
            color: green;
            font-weight: bold;
        }
        .loss {
            color: red;
            font-weight: bold;
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
     <div><a href="dashboard.php">🏠 Dashboard</a></div>
    <h2>Bushoki Income & Expenses Project 2</h2>

    <form method="POST">
        <h3>Add Income</h3>
        <input type="date" name="date" required>
        <input type="text" name="details" placeholder="Details" required>
        <input type="number" name="amount" placeholder="Amount" step="0.01" required>
        <button type="submit" name="add_income">Add Income</button>
    </form>

    <form method="POST">
        <h3>Add Expense</h3>
        <input type="date" name="date" required>
        <input type="text" name="details" placeholder="Details" required>
        <input type="number" name="amount" placeholder="Amount" step="0.01" required>
        <button type="submit" name="add_expense">Add Expense</button>
    </form>
    
    <div class="links">
        <a href="view_bushoki_income_project2.php">📊 View All income Transactions</a> |
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
