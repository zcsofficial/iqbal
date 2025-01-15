<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $place = htmlspecialchars(trim($_POST['place']));
    $state = htmlspecialchars(trim($_POST['state']));
    $age = intval($_POST['age']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $contactNumber = htmlspecialchars(trim($_POST['contact_number']));
    $password = $_POST['password'];
    $destinations = isset($_POST['destinations']) ? implode(', ', array_map('htmlspecialchars', $_POST['destinations'])) : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }

    // Validate contact number
    if (!preg_match('/^[0-9]{10}$/', $contactNumber)) {
        echo json_encode(['success' => false, 'message' => 'Invalid contact number. Please enter a 10-digit number.']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, place, state, age, email, contact_number, password, destinations) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssissss", $firstName, $lastName, $place, $state, $age, $email, $contactNumber, $hashedPassword, $destinations);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'You have successfully registered!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }

    $conn->close();
}
?>