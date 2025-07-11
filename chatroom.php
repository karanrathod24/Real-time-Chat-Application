<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

if (!isset($_GET['room_id'])) {
  die("No chat room selected.");
}

$_SESSION['room_id'] = $_GET['room_id'];
$roomId = $_SESSION['room_id'];
$username = $_SESSION['username'];

$pdo = new PDO("mysql:host=localhost;port=3307;dbname=chat_app", "root", "karanrathod");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch chat room name
$stmt = $pdo->prepare("SELECT name FROM chat_rooms WHERE id = ?");
$stmt->execute([$roomId]);
$room = $stmt->fetch();
if (!$room) {
  die("Chat room not found.");
}
$roomName = $room['name'];

// Fetch previous messages
$stmt = $pdo->prepare("SELECT username, message, created_at FROM messages WHERE room_id = ? ORDER BY created_at ASC");
$stmt->execute([$roomId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Chat Room - <?php echo htmlspecialchars($roomName); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
   * {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', sans-serif;
}

body {
  background: #f0f2f5;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 10px;
}

.container {
  display: flex;
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 1000px;
  flex: 1;
  min-height: 600px;
}

.active-users {
  background: #f9f9f9;
  width: 220px;
  padding: 16px;
  border-right: 1px solid #ddd;
  display: flex;
  flex-direction: column;
}

.active-users h4 {
  font-size: 18px;
  color: #333;
  margin-bottom: 12px;
}

#activeUserList {
  list-style: none;
  padding: 0;
  flex: 1;
  overflow-y: auto;
}

#activeUserList li {
  padding: 8px;
  margin-bottom: 6px;
  background: #e8f0fe;
  border-radius: 6px;
  font-size: 14px;
  color: #333;
}

.chat-container {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.header {
  background: #4a90e2;
  color: #fff;
  padding: 18px;
  font-size: 20px;
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.username-info {
  font-size: 13px;
}

#messages {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: #f7f7f7;
}

.message-wrapper {
  display: flex;
  flex-direction: column;
  max-width: 75%;
}

.align-left {
  align-items: flex-start;
}

.align-right {
  align-items: flex-end;
  margin-left: auto;
}

.message-sender {
  font-size: 11px;
  color: #666;
  margin-bottom: 3px;
}

.message {
  padding: 12px 16px;
  border-radius: 20px;
  word-wrap: break-word;
  font-size: 14px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.my-message {
  background: #d0e7ff;
  color: #333;
  border-bottom-right-radius: 6px;
}

.other-message {
  background: #e4e6eb;
  color: #333;
  border-bottom-left-radius: 6px;
}

.input-area {
  display: flex;
  padding: 16px;
  gap: 10px;
  border-top: 1px solid #ddd;
  background: #fff;
}

#message {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #ccc;
  border-radius: 30px;
  outline: none;
  font-size: 14px;
}

button {
  background: #4a90e2;
  color: #fff;
  border: none;
  padding: 12px 20px;
  border-radius: 30px;
  cursor: pointer;
  font-size: 14px;
  transition: 0.2s ease;
}

button:hover {
  background: #357ABD;
}
.control-bar {
  display: flex;
  justify-content: flex-start;
  gap: 10px;
  margin: 10px 0;
}

#clearBtn, #logoutBtn {
  padding: 10px 18px;
  border-radius: 30px;
  font-size: 14px;
  border: none;
  cursor: pointer;
  color: #fff;
}

#clearBtn {
  background-color: #e74c3c;
}

#clearBtn:hover {
  background-color: #c0392b;
}

#logoutBtn {
  background-color: #333;
}

#logoutBtn:hover {
  background-color: #555;
}



/* Responsive Styles */
@media (max-width: 768px) {
  .container {
    flex-direction: column;
    min-height: 100vh;
  }

  .active-users {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid #ddd;
    flex-direction: row;
    overflow-x: auto;
  }

  .active-users h4 {
    margin-right: 10px;
    flex-shrink: 0;
  }

  #activeUserList {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    overflow-x: auto;
  }

  #activeUserList li {
    margin-right: 8px;
    margin-bottom: 0;
  }

  .chat-container {
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .header {
    flex-wrap: wrap;
    gap: 10px;
  }

  #clearMessagesBtn {
    margin: 8px 16px;
    align-self: flex-start;
  }

  .input-area {
    flex-wrap: wrap;
  }

  #logoutBtn {
    margin-left: auto;
    margin-right: 0;
    margin-top: 10px;
    align-self: flex-start;
  }
}

  </style>
</head>

<body>

  <div class="container">

    <div class="active-users">
      <h4>Active Users</h4>
      <ul id="activeUserList"></ul>
    </div>

    <div class="chat-container">
      <div class="header">
        Room: <?php echo htmlspecialchars($roomName); ?>
        <div class="username-info">Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong></div>
      </div>
    <div class="control-bar">
      <button id="clearMessagesBtn">Clear Messages</button>
      <button id="logoutBtn">Logout</button>
  </div>
      <div id="messages"></div>

      <div class="input-area">
        <input type="text" id="message" placeholder="Type your message..." autocomplete="off">
        <button onclick="sendMessage()">Send</button>
      </div>
    </div>

  </div>

  


  <script>
    let username = "<?php echo $username; ?>";
    let initialMessages = <?php echo json_encode($messages); ?>;

    const urlParams = new URLSearchParams(window.location.search);
    const roomId = urlParams.get('room_id');
    const currentUsername = "<?php echo $username; ?>";


    let ws = new WebSocket(`ws://192.168.0.101:8080?room_id=${roomId}&username=${encodeURIComponent(username)}`);


    ws.onopen = () => {
      console.log("Connected to WebSocket server.");
      loadInitialMessages();
      scrollToBottom();
    };


    ws.onmessage = (e) => {
      try {
        let data = JSON.parse(e.data);

        if (data.type === "user_list") {
          let userList = document.getElementById("activeUserList");
          userList.innerHTML = "";
          data.users.forEach(user => {
            let li = document.createElement("li");
            li.textContent = user;
            userList.appendChild(li);
          });
        } else {
          let sender = data.username;
          let message = data.message;
          let time = formatTime(new Date());
          let alignment = (sender === username) ? 'align-right' : 'align-left';
          let messageClass = (sender === username) ? 'my-message' : 'other-message';
          addMessage(sender, message, messageClass, alignment, time);
        }
      } catch (error) {
        console.error("Invalid message format", error);
      }
    };

    ws.onclose = function() {
      window.location.href = 'login.php';
    };


    function sendMessage() {
      const input = document.getElementById('message');
      const msg = input.value.trim();
      if (msg !== '') {
        let payload = {
          type: "chat",
          username: username,
          message: msg
        };
        ws.send(JSON.stringify(payload));
        input.value = '';
      }
    }

    document.getElementById("message").addEventListener("keyup", function(event) {
      if (event.key === "Enter") {
        sendMessage();
      }
    });


    function addMessage(sender, message, messageClass, alignmentClass, time) {
      const wrapper = document.createElement('div');
      wrapper.classList.add('message-wrapper', alignmentClass);

      const senderDiv = document.createElement('div');
      senderDiv.classList.add('message-sender');
      senderDiv.innerText = `${sender} • ${time}`;

      const msgDiv = document.createElement('div');
      msgDiv.classList.add('message', messageClass);
      msgDiv.innerText = message;

      wrapper.appendChild(senderDiv);
      wrapper.appendChild(msgDiv);
      document.getElementById('messages').appendChild(wrapper);
      scrollToBottom();
    }

    function scrollToBottom() {
      const messagesDiv = document.getElementById('messages');
      messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function formatTime(dateObj) {
      let hours = dateObj.getHours();
      let minutes = dateObj.getMinutes();
      let ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12 || 12;
      minutes = minutes < 10 ? '0' + minutes : minutes;
      return `${hours}:${minutes} ${ampm}`;
    }

    function loadInitialMessages() {
      initialMessages.forEach((msg) => {
        let sender = msg.username;
        let message = msg.message;
        let time = formatTime(new Date(msg.created_at));
        let alignment = (sender === username) ? 'align-right' : 'align-left';
        let messageClass = (sender === username) ? 'my-message' : 'other-message';
        addMessage(sender, message, messageClass, alignment, time);
      });
    }


    //     // Get room_id from query string
    // const urlParams = new URLSearchParams(window.location.search);
    // const roomId = urlParams.get('room_id');


    document.getElementById("clearMessagesBtn").addEventListener("click", function() {
      if (confirm("Are you sure you want to clear all messages in this room?")) {
        fetch("clear_messages.php?room_id=" + encodeURIComponent(roomId))
          .then(response => response.text())
          .then(data => {
            alert(data);
            // Clear chat messages from display
            document.getElementById("messages").innerHTML = "";
          })
          .catch(error => {
            console.error("Error:", error);
          });
      }
    });

    document.getElementById('logoutBtn').addEventListener('click', function() {
      const logoutPayload = JSON.stringify({
        type: 'logout',
        username: currentUsername
      });

      ws.send(logoutPayload);
    });
  </script>

</body>

</html>