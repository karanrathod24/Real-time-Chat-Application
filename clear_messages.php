<?php
if (!isset($_GET['room_id'])) {
    echo "Room ID missing.";
    exit;
}

$roomId = $_GET['room_id'];

try {
    $pdo = new PDO("mysql:host=localhost;port=3307;dbname=chat_app", "root", "karanrathod");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete messages for the specific room
    $stmt = $pdo->prepare("DELETE FROM messages WHERE room_id = ?");
    $stmt->execute([$roomId]);

    echo "Messages for Room {$roomId} cleared successfully.";
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>
