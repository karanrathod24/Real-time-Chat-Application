# 📱 Real-Time Chat Application

A real-time, multi-room chat web application built with **PHP**, **MySQL**, and **WebSockets (Ratchet)**.  
It allows users to register, log in, join chat rooms, and exchange messages instantly without page reloads.  
Responsive and tested on both desktop and mobile devices.

---

## 🚀 Features

- User registration and login system
- Multiple chat rooms
- Real-time messaging using WebSockets (Ratchet)
- Responsive mobile-first design
- Message timestamps and sender info
- Live message streaming without refresh

---

## 🛠️ Setup Instructions

### 1️⃣ Clone the Repository

```bash
git clone :
https://github.com/karanrathod24/Real-time-Chat-Application
cd chat_app
```


### 2️⃣ Set Up XAMPP
```
Install and start XAMPP

Start Apache and MySQL

Place the project folder inside htdocs
```

### 3️⃣ Import the Database
```
Open phpMyAdmin

Create a new database named chat_app

Import the database.sql file located in the project directory
```

### 4️⃣ Install Ratchet WebSocket Server
```bash
Open a terminal in your project directory

Run:
composer require cboden/ratchet

```

### 5️⃣ Start the WebSocket Server
```
In the terminal (inside your project folder), run:
php chatserver.php

This will start the WebSocket server on 
ws://localhost:8080
```

### 6️⃣ Access the Application
```
Open your browser and go to:
http://localhost:3307/chat_app/login.php

Adjust the port number (3307) based on your XAMPP configuration.
```

### 🔐 Sample Login Credentials
```
Email       	           Password
kr2412205@gmail.com         karan123    
kr7510085@gmail.com	        12345678

(You can register your own new users too.)
```

### 📸 Screenshots
```

```
"# Real-time-Chat-Application" 
"# Real-time-Chat-Application" 
