<?php
// summary_report.php
// Single file that shows a full summary and can download DOCX or PDF
session_start();
include('db_connect.php'); //  DB connection

// Security: require logged-in user
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Helper: safe query with fallback if table missing
function safe_sum($conn, $table, $col = 'AMOUNT') {
    $table = $conn->real_escape_string($table);
    // check table exists
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if (!$check || $check->num_rows == 0) return 0.0;
    $res = $conn->query("SELECT SUM($col) AS s FROM `$table`");
    if (!$res) return 0.0;
    $row = $res->fetch_assoc();
    return floatval($row['s'] ?? 0);
}

function safe_fetch_all($conn, $table) {
    $table = $conn->real_escape_string($table);
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if (!$check || $check->num_rows == 0) return [];
    $res = $conn->query("SELECT * FROM `$table` ORDER BY DATE ASC,NO ASC");
    if (!$res) return [];
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    return $rows;
}

// Fetch all relevant tables
$tables = [
    'money_sent' => safe_fetch_all($conn, 'money_sent'),
    'rukundo_expenses' => safe_fetch_all($conn, 'rukundo'), // your expenses table name
    'income_of_rukundo' => safe_fetch_all($conn, 'income_of_rukundo'),
    'expenses_of_200' => safe_fetch_all($conn, 'expenses_of_200'),
    'income_of_200' => safe_fetch_all($conn, 'income_of_200'),
    'expenses_of_75' => safe_fetch_all($conn, 'expenses_of_75'),
    'income_of_75' => safe_fetch_all($conn, 'income_of_75'),
    'income_of_kudemaza' => safe_fetch_all($conn, 'income_of_kudemaza'),
    'other_expenses' => safe_fetch_all($conn, 'other_expenses'),
    'mugisha_personal_use' => safe_fetch_all($conn, 'mugisha_personal_use'),
    'mugisha_income' => safe_fetch_all($conn, 'mugisha_income'),
    'kudemaza' => safe_fetch_all($conn, 'kudemaza'),
    'busoki_expenses_project2' => safe_fetch_all($conn, 'busoki_expenses_project2'),
    'kabere_expenses_project3' => safe_fetch_all($conn, 'kabere_expenses_project3'),
    'busoki_income_project2' => safe_fetch_all($conn, 'busoki_income_project2'),
    'kabere_income_project3' => safe_fetch_all($conn, 'kabere_income_project3'),
    'gihozo_personal_use' => safe_fetch_all($conn, 'gihozo_personal_use'),
    'gihozo_income' => safe_fetch_all($conn, 'gihozo_income'),
    'rukundo_personal_use' => safe_fetch_all($conn, 'rukundo_personal_use'),
];

// Totals
$total_expenses_of_kudemaza=safe_sum($conn,'kudemaza');
$total_money_sent = safe_sum($conn, 'money_sent');
$total_rukundo_expenses = safe_sum($conn, 'rukundo');
$total_income_rukundo = safe_sum($conn, 'income_of_rukundo');
$profit_rukundo = $total_income_rukundo - $total_rukundo_expenses;

$total_income_200 = safe_sum($conn, 'income_of_200');
$total_expenses_200 = safe_sum($conn, 'expenses_of_200');
$profit_200 = $total_income_200 - $total_expenses_200;

$total_income_75 = safe_sum($conn, 'income_of_75');
$total_expenses_75 = safe_sum($conn, 'expenses_of_75');
$profit_75 = $total_income_75 - $total_expenses_75;

$total_income_kud = safe_sum($conn, 'income_of_kudemaza');
$total_other_expenses = safe_sum($conn, 'other_expenses');
$profit_kud = $total_income_kud - $total_expenses_of_kudemaza;


$total_personal_use = safe_sum($conn, 'mugisha_personal_use');
$total_income_mugisha = safe_sum($conn, 'mugisha_income');
$profit_mugisha = $total_income_mugisha - $total_personal_use;  

$total_personal_use_gihozo = safe_sum($conn, 'gihozo_personal_use');
$total_income_gihozo = safe_sum($conn, 'gihozo_income');
$profit_gihozo = $total_income_gihozo - $total_personal_use_gihozo;

$total_personal_use_rukundo = safe_sum($conn, 'rukundo_personal_use');

$total_BUSHOKO_PROJECT2_expenses = safe_sum($conn, 'busoki_expenses_project2');
$total_BUSHOKO_PROJECT2_income = safe_sum($conn, 'busoki_income_project2');
$profit_bushoki_project2 = $total_BUSHOKO_PROJECT2_income - $total_BUSHOKO_PROJECT2_expenses;

$total_kabere_PROJECT3_expenses = safe_sum($conn, 'kabere_expenses_project3');
$total_kabere_PROJECT3_income = safe_sum($conn, 'kabere_income_project3');
$profit_kabere_project3 = $total_kabere_PROJECT3_income - $total_kabere_PROJECT3_expenses;



// Grand totals (example)
$grand_inflow = $total_money_sent + $total_income_rukundo + $total_income_200 + $total_income_75 + $total_income_kud + $total_BUSHOKO_PROJECT2_income + $total_kabere_PROJECT3_income + $total_income_mugisha;
$grand_outflow = $total_rukundo_expenses + $total_expenses_200 + $total_expenses_75 + $total_other_expenses + $total_personal_use + $total_expenses_of_kudemaza + $total_BUSHOKO_PROJECT2_expenses + $total_kabere_PROJECT3_expenses + $total_personal_use_gihozo + $total_personal_use_rukundo;
$net_balance = $grand_inflow - $grand_outflow;

// Build an HTML report (used for display and PDF)
function hnum($n) { return number_format(floatval($n), 2); }

$report_title = "COMPREHENSIVE FINANCIAL REPORT";
$now = date('Y-m-d H:i');
$recent_cutoff = date('Y-m-d', strtotime('-4 days')); // 4 days ago


ob_start();
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="utf-8">
  <title><?=htmlspecialchars($report_title)?></title>
  <style>
    /* Clean printable style */
 
      /* Base */
body {
    font-family: Arial, sans-serif;
    color: #222;
    background: #f5f7fb;
    margin: 0;
    padding: 120px 30px 30px; /* space for fixed header */
}

/* Header (now handled by .fixed-header layout) */
.header {
    margin: 0;
}

.header h1 {
    margin: 0;
    color: #0b5ed7;
    letter-spacing: 1px;
}

.meta {
    color: #aaa;
    margin-top: 4px;
    font-size: 12px;
}

/* Sections */
.section {
    background: #fff;
    padding: 16px;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    margin-bottom: 18px;
}

.section h2 {
    margin: 0 0 10px 0;
    color: #0b5ed7;
    font-size: 18px;
}

/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    padding: 8px 10px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

th {
    background: #e9f0ff;
    color: #0b5ed7;
}

/* Totals */
.totals {
    display: flex;
    gap: 14px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.box {
    background: #fff3cd;
    padding: 12px 16px;
    border-radius: 8px;
    font-weight: bold;
    box-shadow: 0 3px 8px rgba(0,0,0,0.04);
}

.box.green {
    background: #d4edda;
    color: #155724;
}

.box.red {
    background: #f8d7da;
    color: #721c24;
}

/* Utilities */
.right {
    text-align: right;
}

/* Download bar (inside fixed header now) */
.download-bar {
    display: flex;
    gap: 10px;
}

/* Buttons */
.btn,
.download-bar button {
    background: #0b5ed7;
    color: #fff;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    border: none;
    cursor: pointer;
    font-size: 13px;
}

.btn.pdf {
    background: #6c757d;
}

/* Recent highlight */
.recent {
    background-color: #fff3cd;
    font-weight: bold;
    border-left: 4px solid #f0ad4e;
    padding: 10px;
}

/* Print */
@media print {
    .fixed-header {
        display: none;
    }

    body {
        padding: 20px;
        background: #fff;
    }
}

      
      
      .fixed-header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: #111;
    color: #fff;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 9999;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

/* Left side */
.header-left h1 {
    margin: 0;
    font-size: 22px;
}

.meta {
    font-size: 12px;
    color: #aaa;
}

/* Right side */
.header-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* Bottom link */
.top {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 600;
}

/* Download bar buttons */
.download-bar {
    display: flex;
    gap: 10px;
    padding-left 50px;
}

.download-bar .btn,
.download-bar button {
    background: #fff;
    color: #000;
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
}

.download-bar .pdf {
    background: #e63946;
    color: #fff;
}

      
      
      .top {
    position: fixed;          /* stays visible on scroll */
    top: 100px;                /* distance from top */
    left: 100px;               /* distance from left */

    display: inline-block;
    padding: 10px 16px;

    background-color: #007bff;
    color: white;
    text-decoration: none;    /* remove underline */
    font-size: 14px;
    font-weight: 600;

    border-radius: 50%;
    cursor: pointer;

    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}

.top:hover {
    background-color: #0056b3;
}
      
      #heading_format {
      background-color: black;
          color: white;
          height: 50px; 
          text-align: center;
          
      }  
      
      .thick-line {
  border: none;
  height: 12px;         
  background-color: green;
}

     
#bottom {
    background-color: black;
    font-size: 20px; 
    color: green;
    padding: 20px;
    display: flex-end; 
    justify-content: center; 
    align-items: center;
}
      
      .summary-wrapper {
    text-align: center;
}

.totals {
    display: flex;
    gap: 15px;
}


.bottom-button {
    background-color: black;
    padding: 20px; 
    display: flex;
    justify-content: center; 
    gap: 20px; /* space between buttons */
}

/* Buttons inside lower div */
.control-button-bottom {
    padding: 10px 20px; 
    background-color: transparent;
    color: white;
    border: 3px solid white;
    border-radius: 10px;
    cursor: pointer;
    text-decoration: none;
    font-size: 16px;
}

  </style>
</head>
<body>
    
    
  <div class="fixed-header" id="top topplink">
    <div class="header-left">
        <h1><?= htmlspecialchars($report_title) ?></h1>
        <div class="meta">
            Generated by <?= htmlspecialchars($_SESSION['username'] ?? 'System') ?>
            — <?= htmlspecialchars($now) ?>
        </div>
    </div>

    <div class="header-right">
        <a href="#bottom" class="top">V BOTTOM</a>

        <div class="download-bar">
            <a class="btn" href="dashboard.php">Dashboard</a>
            <a class="btn" href="?download=docx">DOCX</a>
            <a class="btn pdf" href="?download=pdf">PDF</a>
            <button onclick="window.print()">Print</button>
        </div>
    </div>
</div>

  <!-- Money Sent -->
  <div class="section">
    <h2 id="heading_format">Money You Sent</h2>
<table>
  <thead><tr><th>No</th><th>Date</th><th>Amount</th><th>Details</th></tr></thead>
  <tbody>
    <?php if (!empty($tables['money_sent'])): $i=1; foreach($tables['money_sent'] as $r): ?>
      <?php
        $recent_class = '';
        if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
          $recent_class = 'recent';
        }
      ?>
      <tr class="<?= $recent_class ?>">
        <td><?= $r['NO'] ?? $i ?></td>
        <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
        <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
      </tr>
    <?php $i++; endforeach; else: ?>
      <tr><td colspan="4">No records found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
<div class="totals">
  <div class="box">Total Money Sent: <?= hnum($total_money_sent) ?></div>
</div>

<hr class="thick-line">

    <!-- OTHER Theogene EXPENSES -->
  <div class="section">
  <h2 id="heading_format" >Theogene Expenses</h2>
  <table>
    <thead><tr><th>No</th><th>Date</th><th>Amount</th><th>Details</th></tr></thead>
    <tbody>
      <?php if (!empty($tables['other_expenses'])): $i=1; foreach($tables['other_expenses'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No records found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <div class="totals">
    <div class="box">TOTAL Theogene EXPENSES: <?= hnum($total_other_expenses) ?></div>
  </div>
</div>

    
    <hr class="thick-line">

  <!-- Rukundo: Income & Expenses -->
  <div class="section">
  <h2 id="heading_format" >Bushoki  project 1 -100 - INCOME & EXPENSES</h2>

  <table>
    <thead><tr><th>No</th><th>Date</th><th>Details</th><th class="right">Amount</th></tr></thead>
    <tbody>
      <tr><td id="heading_format" colspan="4" style="font-weight:bold;">Income (income_of_rukundo)</td></tr>
      <?php if (!empty($tables['income_of_rukundo'])): $i=1; foreach($tables['income_of_rukundo'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['date']) && $r['date'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['date'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['details'] ?? '') ?></td>
          <td class="right"><?= hnum($r['amount'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No income found.</td></tr>
      <?php endif; ?>

      <tr><td id="heading_format" colspan="4" style="font-weight:bold; padding-top:10px;">Expenses (rukundo)</td></tr>
      <?php if (!empty($tables['rukundo_expenses'])): $i=1; foreach($tables['rukundo_expenses'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['date']) && $r['date'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['date'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['details'] ?? '') ?></td>
          <td class="right"><?= hnum($r['amount'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No expenses found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="totals" style="margin-top:12px;">
    <div class="box green">Total Income: <?= hnum($total_income_rukundo) ?></div>
    <div class="box red">Total Expenses: <?= hnum($total_rukundo_expenses) ?></div>
    <div class="box <?= $profit_rukundo >=0 ? 'green' : 'red' ?>">Profit: <?= hnum($profit_rukundo) ?></div>
  </div>
</div>
      
      <hr class="thick-line">

      
       <!-- Inkoko 250 project 2 bushoki -->
<div class="section">
  <h2 id="heading_format"> bushoki Project 2-250 — Income & Expenses</h2>
  <table>
    <thead><tr><th>No</th><th>Date</th><th>Details</th><th class="right">Amount</th></tr></thead>
    <tbody>
      <tr><td id="heading_format" colspan="4" style="font-weight:bold;">Income (income_of_inkoko 250 project 2 bushoki)</td></tr>
      <?php if(!empty($tables['busoki_income_project2'])): $i=1; foreach($tables['busoki_income_project2'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No income found.</td></tr>
      <?php endif; ?>

      <tr><td id="heading_format" colspan="4" style="font-weight:bold; padding-top:10px;">Expenses (expenses_of_inkoko 250 project 2 bushoki)</td></tr>
      <?php if(!empty($tables['busoki_expenses_project2'])): $i=1; foreach($tables['busoki_expenses_project2'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No expenses found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="totals" style="margin-top:12px;">
    <div class="box green">Total Income: <?= hnum($total_BUSHOKO_PROJECT2_income) ?></div>
    <div class="box red">Total Expenses: <?= hnum($total_BUSHOKO_PROJECT2_expenses) ?></div>
    <div class="box <?= $profit_bushoki_project2 >=0 ? 'green' : 'red' ?>">Profit: <?= hnum($profit_bushoki_project2) ?></div>
  </div>
</div>
      
      
      
      <hr class="thick-line">



  <!-- Inkoko 200 -->
<div class="section">
  <h2 id="heading_format" >Kabere project 2-200 — Income & Expenses</h2>
  <table>
    <thead><tr><th>No</th><th>Date</th><th>Details</th><th class="right">Amount</th></tr></thead>
    <tbody>
      <tr><td colspan="4" id="heading_format" style="font-weight:bold;">Income (income_of_200)</td></tr>
      <?php if(!empty($tables['income_of_200'])): $i=1; foreach($tables['income_of_200'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No income found.</td></tr>
      <?php endif; ?>

      <tr><td colspan="4" id="heading_format" style="font-weight:bold; padding-top:10px;">Expenses (expenses_of_200)</td></tr>
      <?php if(!empty($tables['expenses_of_200'])): $i=1; foreach($tables['expenses_of_200'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No expenses found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="totals" style="margin-top:12px;">
    <div class="box green">Total Income: <?= hnum($total_income_200) ?></div>
    <div class="box red">Total Expenses: <?= hnum($total_expenses_200) ?></div>
    <div class="box <?= $profit_200 >=0 ? 'green' : 'red' ?>">Profit: <?= hnum($profit_200) ?></div>
  </div>
</div>

      
      
      <hr class="thick-line">

      
      
      <!-- Inkoko 537 project 3 kabere -->
<div class="section">
  <h2 id="heading_format" >Kabere project 3-537 — Income & Expenses</h2>
  <table>
    <thead><tr><th>No</th><th>Date</th><th>Details</th><th class="right">Amount</th></tr></thead>
    <tbody>
      <tr><td id="heading_format" colspan="4" style="font-weight:bold;">Income (income_of_inkoko 537 project 3)</td></tr>
      <?php if(!empty($tables['kabere_income_project3'])): $i=1; foreach($tables['kabere_income_project3'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No income found.</td></tr>
      <?php endif; ?>

      <tr><td colspan="4" id="heading_format" style="font-weight:bold; padding-top:10px;">Expenses (expenses_of_inkoko 537 project 3 kabere)</td></tr>
      <?php if(!empty($tables['kabere_expenses_project3'])): $i=1; foreach($tables['kabere_expenses_project3'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No expenses found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="totals" style="margin-top:12px;">
    <div class="box green">Total Income: <?= hnum($total_kabere_PROJECT3_income) ?></div>
    <div class="box red">Total Expenses: <?= hnum($total_kabere_PROJECT3_expenses) ?></div>
    <div class="box <?= $profit_kabere_project3 >=0 ? 'green' : 'red' ?>">Profit: <?= hnum($profit_kabere_project3) ?></div>
  </div>
</div>

     <hr class="thick-line">
   
      
  <!-- Inkoko 75 -->
  <div class="section">
  <h2 id="heading_format">Kabere project 1-75 — Income & Expenses</h2>
  <table>
    <thead><tr><th>No</th><th>Date</th><th>Details</th><th class="right">Amount</th></tr></thead>
    <tbody>
      <tr><td id="heading_format" colspan="4" style="font-weight:bold;">Income (income_of_75)</td></tr>
      <?php if(!empty($tables['income_of_75'])): $i=1; foreach($tables['income_of_75'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No income found.</td></tr>
      <?php endif; ?>

      <tr><td id="heading_format" colspan="4" style="font-weight:bold; padding-top:10px;">Expenses (expenses_of_75)</td></tr>
      <?php if(!empty($tables['expenses_of_75'])): $i=1; foreach($tables['expenses_of_75'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No expenses found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="totals" style="margin-top:12px;">
    <div class="box green">Total Income: <?= hnum($total_income_75) ?></div>
    <div class="box red">Total Expenses: <?= hnum($total_expenses_75) ?></div>
    <div class="box <?= $profit_75 >=0 ? 'green' : 'red' ?>">Profit: <?= hnum($profit_75) ?></div>
  </div>
</div>

    <hr class="thick-line">

  <!-- Kudemaza & Other Expenses & Personal Use -->
   <!-- kudemaza income and expenses -->
  <div class="section">
  <h2 id="heading_format">Gitaba project-810 — Income & Expenses</h2>
  <table>
    <thead>
      <tr><th>No</th><th>Date</th><th>Details</th><th class="right">Amount</th></tr>
    </thead>
    <tbody>
      <tr><td colspan="4" id="heading_format" style="font-weight:bold;">Income (income_of_inkoko_810)</td></tr>
      <?php
      $today = new DateTime();
      if(!empty($tables['income_of_kudemaza'])): $i=1; foreach($tables['income_of_kudemaza'] as $r):
          $row_date = !empty($r['DATE']) ? new DateTime($r['DATE']) : null;
          $highlight = ($row_date && $today->diff($row_date)->days <= 4) ? 'style="background: yellow;"' : '';
      ?>
        <tr <?= $highlight ?>>
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No income found.</td></tr>
      <?php endif; ?>

      <tr><td colspan="4" id="heading_format" style="font-weight:bold; padding-top:10px;">Expenses (expenses_of_inkoko_810)</td></tr>
      <?php
      if(!empty($tables['kudemaza'])): $i=1; foreach($tables['kudemaza'] as $r):
          $row_date = !empty($r['DATE']) ? new DateTime($r['DATE']) : null;
          $highlight = ($row_date && $today->diff($row_date)->days <= 4) ? 'style="background: blue;"' : '';
      ?>
        <tr <?= $highlight ?>>
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No expenses found.</td></tr>
      <?php endif; ?>
        
    </tbody>
  </table>
   <div class="totals">
       <div class="box green">TOTAL INCOME: <?= hnum($total_income_kud) ?></div>
      <div class="box">TOTAL EXPENSES ( INKOKO 810 ): <?= hnum($total_expenses_of_kudemaza) ?></div>
       <div class="box <?= $profit_kud >=0 ? 'green' : 'red' ?>">Profit: <?= hnum($profit_kud) ?></div>
    </div>
      
      <hr class="thick-line">

      
     <!-- mugisha personal use -->
<div class="section">
  <h2 id="heading_format">Mugisha — Income & Expenses</h2>
  <table>
    <thead><tr><th>No</th><th>Date</th><th>Details</th><th class="right">Amount</th></tr></thead>
    <tbody>
      <tr><td colspan="4" id="heading_format" style="font-weight:bold;">Income of mugisha</td></tr>
      <?php if(!empty($tables['mugisha_income'])): $i=1; foreach($tables['mugisha_income'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No income found.</td></tr>
      <?php endif; ?>

      <tr><td colspan="4" id="heading_format" style="font-weight:bold; padding-top:10px;">Expenses (expenses_of mugisha)</td></tr>
      <?php if(!empty($tables['mugisha_personal_use'])): $i=1; foreach($tables['mugisha_personal_use'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No expenses found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="totals" style="margin-top:12px;">
    <div class="box green">Total Income: <?= hnum($total_income_mugisha) ?></div>
    <div class="box red">Total Expenses: <?= hnum($total_personal_use) ?></div>
    <div class="box <?= $profit_mugisha >=0 ? 'green' : 'red' ?>">Profit: <?= hnum($profit_mugisha) ?></div>
  </div>
</div>


      
      
      
      
      
      
      
      <hr class="thick-line">

      
      
      
      
    
       <!-- Mugisha personal use 
  <div class="section">
    <h2>MUGISHA Expenses USE</h2>
    <table>
      <thead><tr><th>No</th><th>Date</th><th>Amount</th><th>Details</th></tr></thead>
      <tbody>
        <?php if (!empty($tables['mugisha_personal_use'])): $i=1; foreach($tables['mugisha_personal_use'] as $r): ?>
          <tr>
            <td><?= $r['NO'] ?? $i ?></td>
            <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
            <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
            <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          </tr>
        <?php $i++; endforeach; else: ?>
          <tr><td colspan="4">No records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="totals">
      <div class="box">TOTAL PERSONAL USE( MUGISHA ): <?= hnum($total_personal_use) ?></div>
    </div>
  </div>  -->
      
      
      
      
      <!-- GIHOZO personal use -->
  <div class="section">
    <h2 style="background-color: black; height: 50px; text-align: center;">GIHOZO Income and Expenses </h2>
    <table>
      <thead><tr><th>No</th><th>Date</th><th>Amount</th><th>Details</th></tr></thead>
      <tbody>
          
      <tr><td colspan="4" style="color: lightblue; font-weight:bold; background-color: black; font-color: yellow; height: 50px; text-align: center;">Income of GIHOZO</td></tr>
      <?php if(!empty($tables['gihozo_income'])): $i=1; foreach($tables['gihozo_income'] as $r): ?>
        <?php
          $recent_class = '';
          if (!empty($r['DATE']) && $r['DATE'] >= $recent_cutoff) {
            $recent_class = 'recent';
          }
        ?>
        <tr class="<?= $recent_class ?>">
          <td><?= $r['NO'] ?? $i ?></td>
          <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['AMOUNT'] ?? 0) ?></td>
         <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>

        </tr>
      <?php $i++; endforeach; else: ?>
        <tr><td colspan="4">No income found.</td></tr>
      <?php endif; ?>
          
          <tr><td colspan="4" style="font-weight:bold; background-color: black; height: 50px; color: lightblue; height: 50px; text-align: center;">Expenses of GIHOZO</td></tr>
        <?php if (!empty($tables['gihozo_personal_use'])): $i=1; foreach($tables['gihozo_personal_use'] as $r): ?>
          <tr>
            <td><?= $r['NO'] ?? $i ?></td>
            <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
            <td class="right"><?= hnum($r['AMOUNT'] ?? '') ?></td>
           <td><?= htmlspecialchars($r['DETAILS'] ?? 0) ?></td>>
          </tr>
        <?php $i++; endforeach; else: ?>
          <tr><td colspan="4">No records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="totals">
      <div class="box">TOTAL PERSONAL USE( GIHOZO ): <?= hnum($total_personal_use_gihozo) ?></div>
        <div class="box">TOTAL Income( GIHOZO ): <?= hnum($total_income_gihozo) ?></div>
        <div class="box">TOTAL Profit/Loss( GIHOZO ): <?= hnum($profit_gihozo) ?></div>
    </div>
  </div>

      
      <hr class="thick-line">

  <!-- rukundo personal use -->
  <div class="section">
    <h2 id="heading_format">RUKUNDO Expenses </h2>
    <table>
      <thead><tr><th>No</th><th>Date</th><th>Amount</th><th>Details</th></tr></thead>
      <tbody>
        <?php if (!empty($tables['rukundo_personal_use'])): $i=1; foreach($tables['rukundo_personal_use'] as $r): ?>
          <tr>
            <td><?= $r['NO'] ?? $i ?></td>
            <td><?= htmlspecialchars($r['DATE'] ?? '') ?></td>
            <td class="right"><?= hnum($r['AMOUNT'] ?? 0) ?></td>
            <td><?= htmlspecialchars($r['DETAILS'] ?? '') ?></td>
          </tr>
        <?php $i++; endforeach; else: ?>
          <tr><td colspan="4">No records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="totals">
      <div class="box">TOTAL PERSONAL USE( RUKUNDO ): <?= hnum($total_personal_use_rukundo) ?></div>
    </div>
  </div>
    
      
      
      <hr class="thick-line">

      

  <!-- Grand Summary -->
  <div class="section" id="bottom">
    <div class="summary-wrapper">
        <h2 id="heading_format">Overall Summary</h2>

        <div class="totals">
            <div class="box">Total Inflow: <?= hnum($grand_inflow) ?></div>
            <div class="box">Total Outflow: <?= hnum($grand_outflow) ?></div>
            <div class="box <?= $net_balance >=0 ? 'green' : 'red' ?>">
                Net Balance: <?= hnum($net_balance) ?>
            </div>
        </div>
    </div>
</div>

      
 <div class = "bottom-button">
      <a class = "control-button-bottom" href="dashboard.php">🏠 BACK TO DASHBOARD</a>
     <a class = "control-button-bottom" href="index.php">Back to Home</a>
     <a class = "control-button-bottom" href="#topplink" style = "border-radius: 50px;  width: 30px; height: 30px;border-radius: 50%;">TOP</a>
 </div>

</body>
</html>
<?php
$html = ob_get_clean();

// --- DOWNLOAD HANDLING ----------------------------------------------------
if (isset($_GET['download'])) {
    $type = $_GET['download'] === 'pdf' ? 'pdf' : 'docx';

    if ($type === 'docx') {
        // DOCX generation using PHPWord
        if (!class_exists('\PhpOffice\PhpWord\PhpWord')) {
            die("PhpWord not installed. Run: composer require phpoffice/phpword");
        }
        // create a Word document with sections mirroring the HTML
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addTitle(htmlspecialchars($report_title), 1);
        $section->addText("Generated by: " . ($_SESSION['username'] ?? 'System') . " — $now");
        $section->addTextBreak(1);

        // Helper to add a simple table from a PHP array
        function addTableFromArray($phpWord, $section, $title, $rows) {
            $section->addTextBreak(1);
            $section->addText($title, ['bold'=>true]);
            if (empty($rows)) {
                $section->addText("No records found.");
                return;
            }
            $styleTable = ['borderSize'=>6,'borderColor'=>'999999'];
            $phpWord->addTableStyle('table1', $styleTable);
            $table = $section->addTable('table1');
            // header row: try to get keys from first row
            $keys = array_keys($rows[0]);
            $table->addRow();
            foreach ($keys as $k) $table->addCell(2000)->addText($k);
            foreach ($rows as $r) {
                $table->addRow();
                foreach ($keys as $k) {
                    $val = is_null($r[$k]) ? '' : $r[$k];
                    $table->addCell(2000)->addText((string)$val);
                }
            }
        }

        // Add sections: Money Sent
        addTableFromArray($phpWord, $section, "Money Sent", $tables['money_sent']);
        addTableFromArray($phpWord, $section, "Rukundo Income", $tables['income_of_rukundo']);
        addTableFromArray($phpWord, $section, "Rukundo Expenses", $tables['rukundo_expenses']);
        addTableFromArray($phpWord, $section, "Inkoko 200 Income", $tables['income_of_200']);
        addTableFromArray($phpWord, $section, "Inkoko 200 Expenses", $tables['expenses_of_200']);
        addTableFromArray($phpWord, $section, "Inkoko 75 Income", $tables['income_of_75']);
        addTableFromArray($phpWord, $section, "Inkoko 75 Expenses", $tables['expenses_of_75']);
        addTableFromArray($phpWord, $section, "Kudemaza Income", $tables['income_of_kudemaza']);
        addTableFromArray($phpWord, $section, "Other Expenses", $tables['other_expenses']);
        addTableFromArray($phpWord, $section, "Personal Use", $tables['mugisha_personal_use']);

        // Add overall summary lines
        $section->addTextBreak();
        $section->addText("Overall Summary", ['bold'=>true]);
        $section->addText("Total Inflow: " . hnum($grand_inflow));
        $section->addText("Total Outflow: " . hnum($grand_outflow));
        $section->addText("Net Balance: " . hnum($net_balance));

        // Save to temporary file and send
        $filename = "financial_report_" . date('Ymd_His') . ".docx";
        $temp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($temp);

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($temp));
        readfile($temp);
        unlink($temp);
        exit();

    } else {
        // PDF generation using Dompdf
        if (!class_exists('\Dompdf\Dompdf')) {
            die("Dompdf not installed. Run: composer require dompdf/dompdf");
        }
        $dompdf = new \Dompdf\Dompdf();
        // set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdfOutput = $dompdf->output();
        $filename = "financial_report_" . date('Ymd_His') . ".pdf";
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $pdfOutput;
        exit();
    }
}

// If not downloading, just display HTML
echo $html;
