<?php
include('db.php');
$group_id = $_GET['group_id'] ?? null;

if ($group_id) {
    $stmt = $conn->prepare("SELECT gm.message, gm.created_at, u.first_name, u.last_name FROM group_messages gm JOIN users u ON gm.user_id = u.id WHERE gm.group_id = ? ORDER BY gm.created_at ASC");
    $stmt->bind_param('i', $group_id);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($messages as $msg) {
        echo '<div class="message">';
        echo '<div class="user">' . htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']) . '</div>';
        echo '<div class="text">' . htmlspecialchars($msg['message']) . '</div>';
        echo '<div class="time">' . htmlspecialchars($msg['created_at']) . '</div>';
        echo '</div>';
    }
}
?>
