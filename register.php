<?php
session_start();
require 'connect.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(!empty($username) && !empty($email) && !empty($password)){
        //hash password
        $hashedPassword = password_hash($password , PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?) ");

        try{
            $stmt->execute([$username, $email, $hashedPassword]);
            $success = "Registration Successfull!.";
             header("Location: login.php");
            exit();
        } catch (PDOException $e){
            $error = "Something went wrong: " . $e->getMessage();
        }
    } else {
        $error = "Please fill all fields.";
    }
}

?>

<!-- html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
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

.register-container {
    max-width: 350px;
    width: 100%;
    padding: 30px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    text-align: center;
}

.register-container h2 {
    margin-bottom: 25px;
    color: #222;
    font-size: 26px;
    font-weight: 600;
}

form input[type="text"],
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

form input[type="text"]:focus,
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

.register-container p {
    margin-top: 15px;
    font-size: 13px;
    color: #555;
}

.register-container p a {
    color: #007bff;
    text-decoration: none;
}

.register-container p a:hover {
    text-decoration: underline;
}

.message {
    margin-top: 12px;
    color: red;
    font-size: 13px;
}

.success {
    margin-top: 12px;
    color: green;
    font-size: 13px;
}

</style>

</head>
<body>
    <div class="register-container">
    <h2>Registration</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter username" required>
        <input type="email" name="email" placeholder="Enter email" required>
        <input type="password" name="password" placeholder="Enter password" minlength="8" required>
        <input type="submit" value="Register">
    </form>

    <p>Alredy a user ? <a href="login.php">login</a> </p>

    <?php if (isset($error)) echo "<p class='message'> $error </p> " ?>
    <?php if (isset($success)) echo "<p class='success'> $success </p> " ?>

    </div>
</body>
</html>