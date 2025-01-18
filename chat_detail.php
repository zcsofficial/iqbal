<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$chat_user_id = $_GET['user_id'] ?? null;

// Fetch chat user's data
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param('i', $chat_user_id);
$user_stmt->execute();
$chat_user = $user_stmt->get_result()->fetch_assoc();

if (!$chat_user) {
    echo "User not found.";
    exit();
}

// Fetch chat messages between the two users
$messages_stmt = $conn->prepare("
    SELECT * 
    FROM private_messages 
    WHERE (sender_id = ? AND receiver_id = ?) 
       OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$messages_stmt->bind_param('iiii', $user_id, $chat_user_id, $chat_user_id, $user_id);
$messages_stmt->execute();
$messages = $messages_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle sending a new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $send_stmt = $conn->prepare("INSERT INTO private_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $send_stmt->bind_param('iis', $user_id, $chat_user_id, $message);
        $send_stmt->execute();
    }
    header("Location: chat_detail.php?user_id=$chat_user_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?= htmlspecialchars($chat_user['first_name'] . ' ' . $chat_user['last_name']) ?></title>
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #006400;
            --secondary-color: #ffffff;
        }
        body {
            background-color: var(--secondary-color);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 90vh;
            margin: 20px auto;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .chat-header {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 10px 10px 0 0;
        }
        .chat-header .back-btn {
            color: white;
            text-decoration: none;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .message {
            margin-bottom: 15px;
        }
        .message .sender {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .message .text {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
            max-width: 70%;
        }
        .message.sent .text {
            background-color: var(--primary-color);
            color: white;
            margin-left: auto;
        }
        .chat-footer {
            display: flex;
            padding: 10px 20px;
            background-color: #f7f7f7;
            border-radius: 0 0 10px 10px;
        }
        .chat-footer input {
            flex: 1;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .chat-footer button {
            margin-left: 10px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <a href="chat.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h5><?= htmlspecialchars($chat_user['first_name'] . ' ' . $chat_user['last_name']) ?></h5>
        </div>
        <div class="chat-messages" id="chat-messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?= $msg['sender_id'] === $user_id ? 'sent' : 'received' ?>">
                    <div class="sender">
                        <?= $msg['sender_id'] === $user_id ? 'You' : htmlspecialchars($chat_user['first_name'] . ' ' . $chat_user['last_name']) ?>
                    </div>
                    <div class="text"><?= htmlspecialchars($msg['message']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" class="chat-footer">
            <input type="text" name="message" placeholder="Type a message..." required>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        // Function to fetch new messages every 3 seconds
        function fetchNewMessages() {
            $.ajax({
                url: 'fetch_new_messages.php', // PHP script to fetch new messages
                type: 'GET',
                data: {
                    user_id: <?= $user_id ?>,
                    chat_user_id: <?= $chat_user_id ?>
                },
                success: function(response) {
                    const chatMessages = $('#chat-messages');
                    chatMessages.html(response); // Update chat messages
                    chatMessages.scrollTop(chatMessages[0].scrollHeight); // Auto-scroll
                }
            });
        }

        // Poll for new messages every 3 seconds
        setInterval(fetchNewMessages, 3000);
    </script>
</body>
</html>
