<?php
$host = 'localhost';
$dbname = 'chat_app';
$username = 'root';
$password = 'karanrathod';

try {
    $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname", $username, $password);
    // Set error reporting mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


?>
