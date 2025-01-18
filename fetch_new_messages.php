<?php
session_start();
include('db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit();
}

$user_id = $_SESSION['user_id'];
$chat_user_id = $_GET['chat_user_id'] ?? null;

// Validate the chat user ID
if (!$chat_user_id) {
    echo "Invalid chat user ID.";
    exit();
}

// Fetch chat user's details
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param('i', $chat_user_id);
$user_stmt->execute();
$chat_user = $user_stmt->get_result()->fetch_assoc();

if (!$chat_user) {
    echo "Chat user not found.";
    exit();
}

// Fetch new messages between the two users
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

// Display the messages
foreach ($messages as $msg) {
    echo '<div class="message ' . ($msg['sender_id'] === $user_id ? 'sent' : 'received') . '">';
    echo '<div class="sender">' . ($msg['sender_id'] === $user_id ? 'You' : htmlspecialchars($chat_user['first_name'] . ' ' . $chat_user['last_name'])) . '</div>';
    echo '<div class="text">' . htmlspecialchars($msg['message']) . '</div>';
    echo '</div>';
}
?>
