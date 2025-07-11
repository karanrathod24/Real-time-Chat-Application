<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require __DIR__ . '/../vendor/autoload.php';

class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $rooms;
    private $pdo;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->rooms = [];

        try {
            $this->pdo = new \PDO("mysql:host=localhost;port=3307;dbname=chat_app", "root", "karanrathod");
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            echo "Database connection established.\n";
        } catch (\PDOException $e) {
            echo "Failed to connect to DB: " . $e->getMessage() . "\n";
            exit;
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Parse room_id and username from query string
        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $params);
        $roomId = $params['room_id'] ?? null;
        $username = $params['username'] ?? 'Guest';

        if (!$roomId || !$username) {
            echo "Connection {$conn->resourceId} rejected (missing room_id or username).\n";
            $conn->close();
            return;
        }

        $conn->roomId = $roomId;
        $conn->username = $username;
        $this->clients->attach($conn);

        // Create room if it doesn't exist
        if (!isset($this->rooms[$roomId])) {
            $this->rooms[$roomId] = new \SplObjectStorage;
        }
        $this->rooms[$roomId]->attach($conn);

        echo "New connection! (ID: {$conn->resourceId}) joined Room {$roomId} as {$username}\n";

        // Add user to active_users table
        $this->addActiveUser($roomId, $username);

        // Broadcast updated active user list to the room
        $this->broadcastActiveUsers($roomId);
    }

    public function onMessage(ConnectionInterface $from, $msg)
{
    $roomId = $from->roomId;

    $data = json_decode($msg, true);
    if (!$data || !isset($data['type'])) {
        echo "Invalid message received (missing type).\n";
        return;
    }

    $type = $data['type'];

    if ($type === 'chat') {
        if (!isset($data['username']) || !isset($data['message'])) {
            echo "Invalid chat message received.\n";
            return;
        }

        $username = $data['username'];
        $message = $data['message'];

        $this->saveMessage($roomId, $username, $message);

        foreach ($this->rooms[$roomId] as $client) {
            $client->send($msg);
        }

        echo "Message from {$username} in Room {$roomId}: {$message}\n";

    } elseif ($type === 'logout') {
        if (!isset($data['username'])) {
            echo "Invalid logout request.\n";
            return;
        }

        $username = $data['username'];

        $this->removeActiveUser($roomId, $username);
        $this->broadcastActiveUsers($roomId);

        echo "{$username} logged out from Room {$roomId}\n";

        $from->close();

    } else {
        echo "Unknown message type: " . json_encode($type) . "\n";
    }
}


    public function onClose(ConnectionInterface $conn)
    {
        $roomId = $conn->roomId ?? null;
        $username = $conn->username ?? null;

        $this->clients->detach($conn);

        if ($roomId && isset($this->rooms[$roomId])) {
            $this->rooms[$roomId]->detach($conn);

            // Remove user from active_users table
            $this->removeActiveUser($roomId, $username);

            // Remove room if empty
            if (count($this->rooms[$roomId]) === 0) {
                unset($this->rooms[$roomId]);
            } else {
                // Broadcast updated user list to remaining users
                $this->broadcastActiveUsers($roomId);
            }
        }

        echo "Connection {$conn->resourceId} ({$username}) disconnected from Room {$roomId}.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error on connection {$conn->resourceId}: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function saveMessage($roomId, $username, $message)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO messages (room_id, username, message) VALUES (?, ?, ?)");
            $stmt->execute([$roomId, $username, $message]);
        } catch (\PDOException $e) {
            echo "Database Error: " . $e->getMessage() . "\n";
        }
    }

    private function broadcastActiveUsers($roomId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT username FROM active_users WHERE room_id = ?");
            $stmt->execute([$roomId]);
            $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $payload = json_encode([
                'type' => 'user_list',
                'users' => $users
            ]);

            foreach ($this->clients as $client) {
                if ($client->roomId == $roomId) {
                    $client->send($payload);
                }
            }
        } catch (\PDOException $e) {
            echo "Database Error: " . $e->getMessage() . "\n";
        }
    }

    private function addActiveUser($roomId, $username)
    {
        try {
            // Avoid duplicate entries
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM active_users WHERE room_id = ? AND username = ?");
            $stmt->execute([$roomId, $username]);
            $exists = $stmt->fetchColumn();

            if (!$exists) {
                $stmt = $this->pdo->prepare("INSERT INTO active_users (room_id, username) VALUES (?, ?)");
                $stmt->execute([$roomId, $username]);
            }
        } catch (\PDOException $e) {
            echo "Database Error: " . $e->getMessage() . "\n";
        }
    }

    private function removeActiveUser($roomId, $username)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM active_users WHERE room_id = ? AND username = ?");
            $stmt->execute([$roomId, $username]);
        } catch (\PDOException $e) {
            echo "Database Error: " . $e->getMessage() . "\n";
        }
    }
}
