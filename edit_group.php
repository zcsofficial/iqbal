<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_id = $_POST['group_id'];
    $name = trim($_POST['name']);
    $destination = trim($_POST['destination']);
    $date = $_POST['date'];
    $admin_id = $_SESSION['user_id'];
    $group_icon = null;

    // Check if the user is the admin of the group
    $check_admin_stmt = $conn->prepare("SELECT * FROM `groups` WHERE id = ? AND admin_id = ?");
    $check_admin_stmt->bind_param('ii', $group_id, $admin_id);
    $check_admin_stmt->execute();
    $result = $check_admin_stmt->get_result();

    if ($result->num_rows > 0) {
        // Handle file upload
        if (isset($_FILES['group_icon']) && $_FILES['group_icon']['error'] == 0) {
            $target_dir = "uploads/";
            $group_icon = $target_dir . basename($_FILES['group_icon']['name']);
            move_uploaded_file($_FILES['group_icon']['tmp_name'], $group_icon);
        }

        // Update group details
        $update_stmt = $conn->prepare("
            UPDATE `groups` 
            SET name = ?, destination = ?, group_icon = IFNULL(?, group_icon), date = ? 
            WHERE id = ?
        ");
        $update_stmt->bind_param('sssii', $name, $destination, $group_icon, $date, $group_id);

        if ($update_stmt->execute()) {
            header('Location: group.php');
        } else {
            echo "Error: " . $update_stmt->error;
        }
    } else {
        echo "Unauthorized action.";
    }
}
?>
