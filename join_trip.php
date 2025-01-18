<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the trip_id is provided in the URL
if (isset($_GET['trip_id'])) {
    $trip_id = $_GET['trip_id'];

    // Fetch trip details
    $trip_stmt = $conn->prepare("SELECT * FROM trips WHERE id = ?");
    $trip_stmt->bind_param('i', $trip_id);
    $trip_stmt->execute();
    $trip = $trip_stmt->get_result()->fetch_assoc();

    // Check if the trip exists and has space for more members
    if ($trip && $trip['joined_members_count'] < $trip['allowed_members_count']) {
        // Update the joined members count
        $new_joined_count = $trip['joined_members_count'] + 1;
        $update_stmt = $conn->prepare("UPDATE trips SET joined_members_count = ? WHERE id = ?");
        $update_stmt->bind_param('ii', $new_joined_count, $trip_id);
        $update_stmt->execute();

        // Insert notification for the user
        $notification_msg = "You have successfully joined the trip to " . $trip['destination'] . " on " . $trip['trip_date'] . ".";
        $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notification_stmt->bind_param('is', $user_id, $notification_msg);
        $notification_stmt->execute();

        // Redirect to the trip planner page with a success message
        header('Location: destination-planner.php?status=success');
        exit();
    } else {
        // Redirect to the trip planner page with an error message if no space is available
        header('Location: destination-planner.php?status=full');
        exit();
    }
} else {
    // Redirect if no trip_id is provided
    header('Location: destination-planner.php');
    exit();
}
?>

