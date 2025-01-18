<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$group_id = $_GET['group_id'] ?? null;
$user_id = $_SESSION['user_id'];

// Fetch group details
$group_stmt = $conn->prepare("SELECT * FROM `groups` WHERE id = ?");
$group_stmt->bind_param('i', $group_id);
$group_stmt->execute();
$group = $group_stmt->get_result()->fetch_assoc();

if (!$group) {
    echo "Group not found.";
    exit();
}

// Check user role in the group
$role_stmt = $conn->prepare("SELECT role FROM group_members WHERE group_id = ? AND user_id = ?");
$role_stmt->bind_param('ii', $group_id, $user_id);
$role_stmt->execute();
$user_role = $role_stmt->get_result()->fetch_assoc()['role'] ?? null;

if (!$user_role) {
    echo "You are not a member of this group.";
    exit();
}

// Fetch all group members
$members_stmt = $conn->prepare("
    SELECT u.id, u.first_name, u.last_name, gm.role 
    FROM group_members gm 
    JOIN users u ON gm.user_id = u.id 
    WHERE gm.group_id = ?
");
$members_stmt->bind_param('i', $group_id);
$members_stmt->execute();
$members = $members_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $msg_stmt = $conn->prepare("INSERT INTO group_messages (group_id, user_id, message) VALUES (?, ?, ?)");
        $msg_stmt->bind_param('iis', $group_id, $user_id, $message);
        $msg_stmt->execute();
    }
    header("Location: group_details.php?group_id=$group_id");
    exit();
}

// Handle admin actions (remove member, promote/demote)
if (isset($_POST['action']) && $user_role === 'admin') {
    $action = $_POST['action'];
    $target_user_id = $_POST['user_id'];

    if ($action === 'remove') {
        $remove_stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
        $remove_stmt->bind_param('ii', $group_id, $target_user_id);
        $remove_stmt->execute();
    } elseif ($action === 'promote') {
        $promote_stmt = $conn->prepare("UPDATE group_members SET role = 'admin' WHERE group_id = ? AND user_id = ?");
        $promote_stmt->bind_param('ii', $group_id, $target_user_id);
        $promote_stmt->execute();
    } elseif ($action === 'demote') {
        $demote_stmt = $conn->prepare("UPDATE group_members SET role = 'user' WHERE group_id = ? AND user_id = ?");
        $demote_stmt->bind_param('ii', $group_id, $target_user_id);
        $demote_stmt->execute();
    }
    header("Location: group_details.php?group_id=$group_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($group['name']) ?> - Group Details</title>
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f0f0f0;
            font-family: 'Poppins', sans-serif;
        }
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 80vh;
            overflow: hidden;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .chat-header {
            padding: 10px 20px;
            background: #006400;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-header a {
            color: white;
            font-size: 1.5rem;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .chat-messages .message {
            margin-bottom: 15px;
        }
        .chat-messages .message .user {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .chat-footer {
            display: flex;
            padding: 10px 20px;
            background: #f7f7f7;
        }
        .chat-footer input {
            flex: 1;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .chat-footer button {
            margin-left: 10px;
            background: #006400;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .members-list {
            margin-top: 20px;
        }
        .members-list .member {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .members-list .member .actions button {
            margin-left: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="chat-container">
        <div class="chat-header">
            <a href="group.php" class="left"><i class="fas fa-arrow-left"></i></a>
            <h5><?= htmlspecialchars($group['name']) ?></h5>
            <p><?= htmlspecialchars($group['destination']) ?></p>
        </div>
        <div class="chat-messages" id="messages-container">
            <!-- Messages will be loaded here via AJAX -->
        </div>
        <form method="POST" class="chat-footer" id="message-form">
            <input type="text" name="message" placeholder="Type a message..." required>
            <button type="submit">Send</button>
        </form>
    </div>

    <div class="members-list">
        <h5>Group Members</h5>
        <?php foreach ($members as $member): ?>
            <div class="member">
                <span><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?> (<?= ucfirst($member['role']) ?>)</span>
                <?php if ($user_role === 'admin' && $member['id'] !== $user_id): ?>
                    <div class="actions">
                        <?php if ($member['role'] === 'user'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $member['id'] ?>">
                                <input type="hidden" name="action" value="promote">
                                <button class="btn-small green">Make Admin</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $member['id'] ?>">
                                <input type="hidden" name="action" value="demote">
                                <button class="btn-small orange">Remove Admin</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?= $member['id'] ?>">
                            <input type="hidden" name="action" value="remove">
                            <button class="btn-small red">Remove</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    // Function to fetch messages in real-time
    function fetchMessages() {
        $.get('fetch_messages.php?group_id=<?= $group_id ?>', function(data) {
            $('#messages-container').html(data);
        });
    }

    // Fetch messages every 2 seconds
    setInterval(fetchMessages, 2000);

    // Handle message submission
    $('#message-form').submit(function(e) {
        e.preventDefault();
        var message = $('input[name="message"]').val();
        if (message) {
            $.post('group_details.php?group_id=<?= $group_id ?>', { message: message }, function() {
                $('input[name="message"]').val('');
                fetchMessages();
            });
        }
    });
</script>

</body>
</html>
