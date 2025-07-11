<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

//database connections 
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

//fetch chat rooms
$stmt = $pdo->query("SELECT * FROM chat_rooms");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Chat Rooms</title>
</head>
<style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', sans-serif;
}

body {
  background: #f0f2f5;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 40px 20px;
  min-height: 100vh;
}

.header {
  background: linear-gradient(135deg, #a8e0c0, #8ec5a5);
  color: #fff;
  padding: 18px 30px;
  border-radius: 20px;
  font-size: 26px;
  font-weight: 600;
  margin-bottom: 35px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.room-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 25px;
  max-width: 1000px;
}

.room-card {
  background: #ffffff;
  border-radius: 20px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  padding: 25px 20px;
  width: 280px;
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: 0.3s ease;
  border: 1px solid #e6e6e6;
}

.room-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.room-card a {
  text-decoration: none;
  font-size: 22px;
  color: #333;
  font-weight: 600;
  margin-bottom: 10px;
  transition: 0.3s;
}

.room-card a:hover {
  color: #007bff;
}

.room-card p {
  color: #666;
  font-size: 14px;
  margin-top: 5px;
}

.no-room-msg {
  font-size: 18px;
  color: #777;
  text-align: center;
  margin-top: 40px;
}

  </style>
<body>
   
  <div class="header">Available Chat Rooms</div>
    <div class="container">
    <?php 

        if(count($rooms) > 0){
           foreach($rooms as $room) {
            echo "<div class='room-card'>";
            echo "<a href='chatroom.php?room_id=" . htmlspecialchars($room['id']) . "'>" . htmlspecialchars($room['name']) . "</a>";
            echo "<p>" . htmlspecialchars($room['description']) . "</p>";
            echo "</div>";
           } 
        } else {
            echo "No chat Room available!";
        }


    ?>
  </div>
</body>
</html>
