<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';
session_start();

// Delete old reminders older than 5 days
$conn->query("DELETE FROM reminders WHERE created_at < NOW() - INTERVAL 5 DAY");

// Check if user is logged in
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Poultry Project Dashboard</title>
<style>
body, html {
    height: 100%;
    margin: 0;
    font-family: Arial, sans-serif;
    color: #fff;
    overflow-x: hidden;
}

/* Background slideshow */
.slideshow {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 0;
}
.slideshow img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}
.slideshow img.active { opacity: 1; }

/* Container holding both menu + reminder */
.dashboard-wrapper {
    display: flex;
    height: 100vh;
    position: relative;
    z-index: 2;
}

/* Left Menu — larger area */
.dashboard {
    flex: 1.3;
    background: rgba(0,0,0,0.6); /* lighter black, semi-transparent */
    padding: 20px;
    overflow-y: auto;
}

.dashboard h2, .dashboard h3 {
    margin: 10px 0;
}
.dashboard ul {
    list-style: none;
    padding: 0;
}
.dashboard ul li {
    margin: 10px 0;
}
.dashboard ul li a {
    display: block;
    background: rgba(255,255,255,0.2);
    padding: 10px;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.3s;
}
.dashboard ul li a:hover {
    background: rgba(255,255,255,0.7);
    color: #000;
}

/* Right reminder/chat box */
#reminder-popup {
    flex: 0.7; /* smaller width */
    background: rgba(11,94,215,0.95);
    padding: 15px;
    border-radius: 0;
    overflow-y: auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.5);
}
.reminder-item {
    padding: 5px 0;
    border-bottom: 1px solid rgba(255,255,255,0.3);
}
#reminder-form {
    display: flex;
    gap: 6px;
    margin-top: 10px;
}
#reminder-form input {
    flex: 1;
    padding: 5px 10px;
    border-radius: 6px;
    border: none;
}
#reminder-form button {
    background: #ffc107;
    color: #222;
    border: none;
    padding: 5px 12px;
    border-radius: 6px;
    cursor: pointer;
}

/* Logout */
.logout-btn {
    display: block;
    background-color: #ff4d4d;
    color: white;
    padding: 10px;
    border-radius: 30px;
    font-weight: bold;
    text-decoration: none;
    text-align: center;
}
.logout-btn:hover { background-color: green; }
</style>
</head>
<body>

<!-- Slideshow -->
<div class="slideshow">
    <img src="MUGISHA PASSPORT PHOTO.jpg" class="active" alt="Chick 1">
    <img src="window image.png" alt="Chick 2">
    <img src=" Image_20251030213020_11_2.jpg" alt="Chick 3">
</div>

<!-- Dashboard & Chat -->
<div class="dashboard-wrapper">

    <!-- Left Menu -->
    <div class="dashboard">
        <h2>Welcome, <?php echo ucfirst($username); ?> 👋</h2>
        <p>Role: <b><?php echo ucfirst($role); ?></b></p>
        <h3>Main Menu</h3>
        <ul>
            <?php if($role=='admin'||$role=='boss'): ?>
            <li><a href="money_sent.php">💰All money received</a></li>
            <p><h1>BUShOKI PROJECT</h1></p1>
            <li><a href="rukundo_project.php">🐔 Expenses of bushoki project1-- 100</a></li>
                
            <li><a href="income_rukundo.php">🚜 Income from bushoki project 1- 100</a></li>
           
             <li><a href="bushoki_expenses_project2.php">🐔 Expenses of bushoki projrct 2-250</a></li>
        
            <li><a href=" bushoki_income_project2.php">🚜 Income from bushoki project 2-250</a></li>
            <p><h1>KABERE PROJECT</h1></p1>
            <li><a href="expenses_of_75.php">💰 Expenses of kabere project 1_ 75</a></li>
    
            <li><a href="income_of_75.php">🐥 Income from kabere project 1- 75</a></li>
           
            <li><a href="inkoko200.php">🐓 Expenses of kabere project  2- 200</a></li>
    
            <li><a href="income_200.php">🚜 Income from kabere project 2- 200</a></li>
            
             <li><a href="kabere_expenses_project3.php">🐓 Expenses of kabere project 3- 537</a></li>
    
            <li><a href="kabere_income_project3.php">🚜 income from kabere project 3- 537</a></li>
            <p><h1>GITABA PROJECT</h1></p1>
            <li><a href="kudemaza.php">🚜 Expenses of gitaba project  1- 810</a></li>
    
            <li><a href="income_of_kudemaza.php">🚜 Income from gitaba project 1- 810</a></li>
              <p><h1>EXPENSES NOT RELATED TO PROJECT</h1></p1>
            <li><a href="other_expenses.php">📦 Theogene expenses</a></li>
    
            
            <li><a href="personal_use.php">📦 Expenses for Mugisha </a></li>
     <li><a href="mugisha-income.php">📦 income for Mugisha </a></li>
    
     <li><a href="rukundo-personal-use.php">📦 Expenses for rukundo </a></li>
    
     <li><a href="gihozo-personal-use.php">📦 Expenses for gihozo </a></li>
     <li><a href="gihozo_income.php">📦 INCOME for gihozo </a></li>
            
            <li><a href="summary_report.php">🚜 Summary Table of all project</a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="logout-btn">🚪 Log out -----></a></li>
        </ul>
    </div>

    <!-- Right Chat / Reminder -->
    <div id="reminder-popup">
        <h3>📝 LEAVE MESSAGE HERE </h3>
        <div id="reminder-messages">
            <?php
            $res = $conn->query("SELECT * FROM reminders ORDER BY created_at ASC");
            while($r = $res->fetch_assoc()){
                echo "<div class='reminder-item'><strong>".htmlspecialchars($r['username']).":</strong> ".htmlspecialchars($r['message'])."</div>";
            }
            ?>
        </div>
        <form id="reminder-form" method="POST" action="add_reminder.php">
            <input type="text" name="message" placeholder="Type a quick message here i will se it!!!..." required>
            <button type="submit">Send</button>
        </form>
    </div>

</div>

<script>
// Slideshow
const slides = document.querySelectorAll('.slideshow img');
let current = 0;
function nextSlide() {
    slides[current].classList.remove('active');
    current = (current + 1) % slides.length;
    slides[current].classList.add('active');
}
setInterval(nextSlide, 10000);

// Auto-refresh reminders
setInterval(() => {
    fetch('get_reminders.php')
        .then(res => res.text())
        .then(data => {
            document.getElementById('reminder-messages').innerHTML = data;
        });
}, 5000);
</script>

</body>
</html>
