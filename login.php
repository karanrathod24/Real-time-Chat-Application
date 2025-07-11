<?php

session_start();
require 'connect.php';

$_SESSION['username'] = $username;


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    //checks if exists
    if (empty($email) && empty($password)) {
        $error = "Please fill in both fields";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM  users WHERE email= ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if ($user && password_verify($password, $user['password']))  {
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['user_id'];

                //redirect
                header("Location: rooms.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with this email";
        }
    }
}

?>


<!-- HTML -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
     <style>
        body {
    font-family: 'Segoe UI', sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
    display: flex;
    height: 100vh;
    align-items: center;
    justify-content: center;
}

.login-container {
    max-width: 350px;
    width: 100%;
    padding: 30px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    text-align: center;
}

.login-container h2 {
    margin-bottom: 25px;
    color: #222;
    font-size: 26px;
    font-weight: 600;
}

form input[type="email"],
form input[type="password"] {
    display: block;
    width: 100%;
    max-width: 320px;
    padding: 14px 16px;
    margin: 15px 0;
    border: 1px solid #c8c8c8;
    border-radius: 50px;
    background: #fdfdfd;
    font-size: 14px;
    transition: 0.3s;
}

form input[type="email"]:focus,
form input[type="password"]:focus {
    outline: none;
    border-color: #8ec5a5;
    box-shadow: 0 0 0 2px rgba(142, 197, 165, 0.2);
}

form input[type="submit"] {
    width: 100%;
    padding: 14px 16px;
    margin: 20px 0 10px;
    border: none;
    border-radius: 50px;
    background: linear-gradient(135deg, #a8e0c0, #8ec5a5);
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

form input[type="submit"]:hover {
    background: linear-gradient(135deg, #8ec5a5, #76ba9f);
}

.error {
    color: red;
    margin-bottom: 15px;
    font-size: 13px;
}

.registerLink {
    margin-top: 15px;
    font-size: 13px;
    color: #555;
}

.registerLink a {
    color: #007bff;
    text-decoration: none;
}

.registerLink a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
<div class="login-container">
<h2>Login Page</h2>
    
    <?php if (isset($error)) echo "<p class='error'> $error </p> " ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter email" required>
        <input type="password" name="password" placeholder="Enter password" required>
        <input type="submit" value="login">
    </form>

    <p class="registerLink">Don't have an account? <a href="register.php">Register here </a></p>
</div>
</body>
</html>