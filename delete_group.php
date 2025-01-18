<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];
    $admin_id = $_SESSION['user_id'];

    // Check if the user is the admin of the group
    $check_admin_stmt = $conn->prepare("SELECT * FROM `groups` WHERE id = ? AND admin_id = ?");
    $check_admin_stmt->bind_param('ii', $group_id, $admin_id);
    $check_admin_stmt->execute();
    $result = $check_admin_stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the group
        $delete_stmt = $conn->prepare("DELETE FROM `groups` WHERE id = ?");
        $delete_stmt->bind_param('i', $group_id);

        if ($delete_stmt->execute()) {
            header('Location: group.php');
        } else {
            echo "Error: " . $delete_stmt->error;
        }
    } else {
        echo "Unauthorized action.";
    }
}
?>
