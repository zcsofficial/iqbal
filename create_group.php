<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $destination = trim($_POST['destination']);
    $date = $_POST['date'];
    $admin_id = $_SESSION['user_id'];
    $group_icon = null;

    // Handle file upload
    if (isset($_FILES['group_icon']) && $_FILES['group_icon']['error'] == 0) {
        $target_dir = "uploads/";
        $group_icon = $target_dir . basename($_FILES['group_icon']['name']);
        move_uploaded_file($_FILES['group_icon']['tmp_name'], $group_icon);
    }

    // Insert into groups table
    $stmt = $conn->prepare("INSERT INTO `groups` (name, destination, group_icon, date, admin_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssi', $name, $destination, $group_icon, $date, $admin_id);

    if ($stmt->execute()) {
        $group_id = $stmt->insert_id;

        // Add the admin as a member of the group
        $member_stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'admin')");
        $member_stmt->bind_param('ii', $group_id, $admin_id);
        $member_stmt->execute();

        header('Location: group.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
