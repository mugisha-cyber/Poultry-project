<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MUGISHA Poultry Management System</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<!-- Font Awesome for Social Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<style>
/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    min-height: 100vh;
    overflow-x: hidden;
    overflow-y: auto;
    background: linear-gradient(120deg, #f0f8ff, #e0f7fa, #c8e6c9);
}

/* Floating Shapes for animation */
.floating-shapes span {
    position: absolute;
    display: block;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    animation: float 20s linear infinite;
    pointer-events: none;
}

@keyframes float {
    0% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-1000px) rotate(360deg); }
    100% { transform: translateY(0) rotate(720deg); }
}

/* Page container */
.page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
    text-align: center;
    position: relative;
    z-index: 2;
}

/* Title */
h1 {
    font-size: 3rem;
    font-weight: 700;
    background: linear-gradient(90deg, #0077b6, #00b4d8, #90e0ef);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: textGlow 3s ease-in-out infinite alternate;
}

@keyframes textGlow {
    0% { text-shadow: 2px 2px 10px rgba(0,0,0,0.2); }
    100% { text-shadow: 2px 2px 25px rgba(0,180,216,0.8); }
}

/* Tips Paragraph */
.tips {
    font-size: 1.2rem;
    margin: 20px auto;
    padding: 20px;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.85);
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    line-height: 1.8;
    transition: transform 0.3s, background 0.3s;
}

.tips strong {
    color: #0077b6;
}

.tips:hover {
    transform: scale(1.02);
    background: rgba(255,255,255,0.95);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Buttons */
.buttons {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
}

.btn {
    background: linear-gradient(135deg, #0077b6, #00b4d8);
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 15px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

/* Invite Section */
.invite {
    margin-top: 40px;
    background: linear-gradient(135deg, #00b4d8, #90e0ef);
    padding: 20px;
    border-radius: 15px;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    transition: transform 0.3s;
    cursor: pointer;
}

.invite:hover {
    transform: scale(1.05);
}

/* Footer */
footer {
    margin-top: 50px;
    width: 100%;
    text-align: center;
    padding: 25px 20px;
    color: #333;
    background: linear-gradient(135deg, #e0f7fa, #c8e6c9);
    border-top: 2px solid #0077b6;
    box-shadow: 0 -2px 15px rgba(0,0,0,0.05);
}

.social-icons a {
    margin: 0 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 12px;
    border-radius: 50px;
    color: white;
    font-weight: bold;
    transition: all 0.3s;
    gap: 5px;
}

.social-icons a.facebook { background: #3b5998; }
.social-icons a.instagram { background: #e4405f; }
.social-icons a.twitter { background: #1da1f2; }
.social-icons a.whatsapp { background: #25D366; }

.social-icons a:hover {
    transform: translateY(-5px);
    opacity: 0.85; } 
    
    /* Background Slideshow Container */
body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;  /* full width */
    height: 100%; /* full height */
    z-index: -1;
    background: url('image1.jpg') no-repeat center center; /* center the image */
    background-size: contain; /* makes image smaller and fit */
    animation: slideBg 30s linear infinite, swapBg 60s linear infinite;
}

/* Slide side-to-side animation */
@keyframes slideBg {
    0% { background-position: left center; }
    50% { background-position: right center; }
    100% { background-position: left center; }
}

/* Swap background images */
@keyframes swapBg {
    0% { background-image: url('MUGISHA PASSPORT PHOTO.jpg'); }
    33% { background-image: url('Image_20251030213022_12_2.jpg'); }
    66% { background-image: url('window image.png'); }
    100% { background-image: url('Image_20251030213020_11_2.jpg'); }
}

    
    
    
    
    
    
    
    
    
 
/* Floating WhatsApp button */
.whatsapp-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #25D366;
    color: white;
    padding: 12px 20px;
    border-radius: 50px;
    font-weight: bold;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    text-decoration: none;
    z-index: 1000;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.whatsapp-float:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.4);
}

@media (max-width:768px){
    h1{ font-size:2.2rem;}
    .tips{ font-size:1rem; padding:15px;}
    .btn{ padding:10px 20px; font-size:0.95rem;}
    .invite{ font-size:1rem; padding:15px;}
}
</style>
</head>
<body>

<!-- Floating Shapes -->
<div class="floating-shapes">
    <span style="left:5%; width:60px; height:60px;"></span>
    <span style="left:20%; width:40px; height:40px;"></span>
    <span style="left:50%; width:50px; height:50px;"></span>
    <span style="left:70%; width:70px; height:70px;"></span>
    <span style="left:85%; width:45px; height:45px;"></span>
</div>

<div class="page-container">
    <h1>Welcome to Poultry Management System</h1>
    <p class="tips">
        This system helps you manage your poultry projects effectively — from tracking expenses and income to monitoring chick growth and health.<br><br>
        🐥 <strong>Tip 1:</strong> Keep chicks warm and dry at all times.<br>
        🥚 <strong>Tip 2:</strong> Provide clean water and balanced feed daily.<br>
        🧼 <strong>Tip 3:</strong> Maintain hygiene in the coop to prevent diseases.<br>
        📊 <strong>Tip 4:</strong> Record every expense and income to track your profits.
    </p>

    <div class="buttons">
        <button class="btn" onclick="window.location.href='login.php'">Login</button>
        <button class="btn" onclick="window.location.href='signup.php'">Sign Up</button>
        <button class="btn" onclick="window.location.href='about.php'">Learn More</button>
    </div>

    <div class="invite" onclick="window.location.href='https://chat.whatsapp.com/jonzBF51jda2NnJ9vc5gh6'">
        Join My Coding Class & Message Me on WhatsApp
    </div>
</div>

<footer>
    <p>All Rights Reserved © <?php echo date("Y"); ?> | Designed by <strong>Mugisha Elyse</strong></p>
    <div class="social-icons">
        <a href="https://www.facebook.com/" class="facebook" target="_blank"><i class="fab fa-facebook"></i>Facebook</a>
        <a href="https://www.instagram.com/" class="instagram" target="_blank"><i class="fab fa-instagram"></i>Instagram</a>
        <a href="https://twitter.com/" class="twitter" target="_blank"><i class="fab fa-twitter"></i>Twitter</a>
        <a href="https://wa.me/250798624380" class="whatsapp" target="_blank"><i class="fab fa-whatsapp"></i>WhatsApp</a>
    </div>
</footer>

<a href="https://wa.me/250798624380" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i> Chat Now
</a>

</body>
</html>

