<?php
    error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    // Check if username already exists
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('This username already exists. Please choose another.');</script>";
    } else {
        // Hash password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Signup successful! You can now log in.');
                    window.location.href='login.php';
                  </script>";
        } else {
            echo "<script>alert('Something went wrong. Try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Signup - Poultry Project</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        height: 100vh;
        overflow: auto;
    }

    .bg-slideshow {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        animation: changeBg 30s infinite;
        background-size: cover;
        background-position: center;
    }

    @keyframes changeBg {
        0% { background-image: url('MUGISHA PASSPORT PHOTO.jpg'); }
        33% { background-image: url('window image.png'); }
        66% { background-image: url('MUGISHA PASSPORT PHOTO.jpg'); }
        100% { background-image: url('window image.png'); }
    }

    .signup-container {
        background: rgba(255, 255, 255, 0.85);
        width: 350px;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        margin: 100px auto;
        text-align: center;
    }

    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    input, select {
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
        background: #008cba;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background: #005f73;
    }

    p {
        margin-top: 10px;
    }

    a {
        color: #008cba;
        text-decoration: none;
        font-weight: bold;
    }
</style>
</head>
<body>

<div class="bg-slideshow"></div>

<div class="signup-container">
    <h2>Signup</h2>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Enter username" required>
        <input type="password" name="password" placeholder="Enter password" required>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="boss">Boss</option>
        </select>
        <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Log In</a></p>
    <p><button onclick="window.location.href='index.php'" style="margin-top:10px; background:#555; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
    Back to Home
</button>
</p>
</div>

</body>
</html>

