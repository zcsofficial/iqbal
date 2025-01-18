<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'] ?? null;

// Check if the group ID is provided
if (!$group_id) {
    echo "Invalid group ID.";
    exit();
}

// Check if the group exists
$group_stmt = $conn->prepare("SELECT * FROM `groups` WHERE id = ?");
$group_stmt->bind_param('i', $group_id);
$group_stmt->execute();
$group = $group_stmt->get_result()->fetch_assoc();

if (!$group) {
    echo "Group not found.";
    exit();
}

// Check if the user is already a member of the group
$member_check_stmt = $conn->prepare("SELECT * FROM group_members WHERE group_id = ? AND user_id = ?");
$member_check_stmt->bind_param('ii', $group_id, $user_id);
$member_check_stmt->execute();
$is_member = $member_check_stmt->get_result()->num_rows > 0;

if ($is_member) {
    echo "You are already a member of this group.";
    exit();
}

// Add the user to the group
$join_stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'user')");
$join_stmt->bind_param('ii', $group_id, $user_id);

if ($join_stmt->execute()) {
    header("Location: group_details.php?group_id=$group_id");
    exit();
} else {
    echo "Failed to join the group. Please try again.";
    exit();
}
?>
