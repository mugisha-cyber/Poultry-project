<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';
session_start();

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $system_password = trim($_POST['system_password']);

    // Your secret master password
    $correct_system_password = "123"; // CHANGE THIS

    // First check system password
    if ($system_password !== $correct_system_password) {
        $error = "Incorrect system password!";
    } else {

        // Check user in database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {

                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                echo "<script>
                        alert('Welcome back, {$user['username']}!');
                        window.location.href = 'dashboard.php';
                      </script>";
                exit();

            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Username not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Poultry Management System</title>
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        height: 100vh;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(to right, #0077b6, #0096c7, #00b4d8);
    }

    .bg-slideshow {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        animation: changeBg 25s infinite;
        background-size: cover;
        background-position: center;
    }

    @keyframes changeBg {
        0% { background-image: url('MUGISHA PASSPORT PHOTO.jpg'); }
        33% { background-image: url(' Image_20251030213022_12_2.jpg'); }
        66% { background-image: url(' Image_20251030213020_11_2.jpg'); }
        100% { background-image: url('window image.png'); }
    }

    .login-container {
        background: rgba(255, 255, 255, 0.9);
        width: 350px;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.4);
        text-align: center;
        z-index: 1;
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        color: #0077b6;
        margin-bottom: 15px;
    }

    input {
        width: 90%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    button {
        width: 95%;
        padding: 10px;
        background: #0077b6;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s;
    }
    
    
    #systempasswprd {
    background: rgba(0, 119, 182, 0.15);
    border-left: 4px solid #0077b6;
    padding: 12px 15px;
    margin-top: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    font-size: 14px;
    color: #004b6f;
    font-weight: 500;
    text-align: left;
    box-shadow: 0 0 8px rgba(0,0,0,0.1);
}


    button:hover {
        background: #005f73;
    }

    p {
        margin-top: 15px;
        font-size: 14px;
    }

    a {
        color: #0077b6;
        text-decoration: none;
        font-weight: bold;
    }

    .error {
        color: red;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .whatsapp {
        display: block;
        margin-top: 10px;
        background: #25D366;
        color: white;
        padding: 8px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
    }

    .whatsapp:hover {
        background: #128C7E;
    }
</style>
</head>
<body>

<div class="bg-slideshow"></div>

<div class="login-container">
    <h2>Login</h2>
    <?php if(isset($error)) { echo "<div class='error'>$error</div>"; } ?>
    
    <form method="POST">
          ENTER USER NAME<br>
        <input type="text" name="username" placeholder="Enter username" required><br>
        ENTER YOUR PASSWORD<br>
        <input type="password" name="password" placeholder="Enter password" required><br>
        <section id="systempasswprd">
            <p>
  Dear user, this system is designed for a single authorized user. 
  For security and privacy reasons, please enter the system password 
  in the provided prompt to confirm your access rights. If you do not 
  have the system password, please contact the system administrator at 
  <a href="tel:+250798624380">0798624380</a> or via email at 
  <a href="mailto:eliseusmugisha@gmail.com">eliseusmugisha@gmail.com</a>.
</p>

        
        </section>
        <p><i> ENTER SYSTEM PASSWORD PLEASE</i></p> <br>
        <input type="password" name="system_password" placeholder="Enter system password" required><br>
        <button type="submit" name="login">Login</button>
    </form>

    <p>Don’t have an account? <a href="signup.php">Sign up here</a></p>
    <a href="https://chat.whatsapp.com/jonzBF51jda2NnJ9vc5gh6?mode=ems_copy_t" class="whatsapp">Join Our WhatsApp Group</a>

    <p>
        <button onclick="window.location.href='index.php'" style="margin-top:10px; background:#555; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
            Back to Home
        </button>
    </p>
</div>

</body>
</html>
