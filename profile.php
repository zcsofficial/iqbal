<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Fetch the user's joined trips
$joined_trips_stmt = $conn->prepare("SELECT t.id, t.destination, t.trip_date FROM trips t JOIN trip_members tm ON t.id = tm.trip_id WHERE tm.user_id = ?");
$joined_trips_stmt->bind_param('i', $user_id);
$joined_trips_stmt->execute();
$joined_trips = $joined_trips_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle profile update
if (isset($_POST['update_profile'])) {
    // Initialize variables
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : $user['first_name'];
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : $user['last_name'];
    $place = isset($_POST['place']) ? trim($_POST['place']) : $user['place'];
    $state = isset($_POST['state']) ? trim($_POST['state']) : $user['state'];
    $age = isset($_POST['age']) ? trim($_POST['age']) : $user['age'];
    $contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : $user['contact_number'];
    $email = isset($_POST['email']) ? trim($_POST['email']) : $user['email'];

    // Basic validation (can be extended)
    $errors = [];
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!is_numeric($age) || $age < 18 || $age > 120) {
        $errors[] = "Age must be a valid number between 18 and 120.";
    }

    if (!empty($errors)) {
        // Display errors
        echo "<script>Swal.fire({icon: 'error', title: 'Profile Update Failed', text: '" . implode('<br>', $errors) . "'});</script>";
    } else {
        // Prepare the update query with individual fields
        $update_stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, place = ?, state = ?, age = ?, contact_number = ?, email = ? WHERE id = ?");
        $update_stmt->bind_param('ssssisis', $first_name, $last_name, $place, $state, $age, $contact_number, $email, $user_id);
        
        if ($update_stmt->execute()) {
            // Refresh the page after update
            echo "<script>Swal.fire({icon: 'success', title: 'Profile Updated', text: 'Your profile has been updated successfully.'}).then(() => { window.location.reload(); });</script>";
        } else {
            echo "<script>Swal.fire({icon: 'error', title: 'Profile Update Failed', text: 'Something went wrong. Please try again later.'});</script>";
        }
    }
}

// Handle account deletion
if (isset($_POST['delete_account'])) {
    // Delete user from all tables
    $delete_user_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete_user_stmt->bind_param('i', $user_id);
    $delete_user_stmt->execute();

    // Destroy session and redirect to login page
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Syncgo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        :root {
            --primary-color: #006400; /* Dark Green */
            --secondary-color: #ffffff;
            --text-color: #333;
        }
        body {
            background-color: var(--secondary-color);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: var(--primary-color);
        }
        .navbar .brand-logo {
            font-size: 2rem;
            color: white;
        }
        .navbar .brand-logo:hover {
            color: white;
        }
        .navbar .nav-wrapper a {
            color: white;
        }
        .container {
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn {
            background-color: var(--primary-color) !important;
        }
        .input-field input {
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="navbar">
            <div class="nav-wrapper">
                <a href="index.php" class="brand-logo">Syncgo</a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="chat.php">Chat</a></li>
                    <li><a href="group.php">Groups</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <div class="container">
        <h4 class="center-align">Your Profile</h4>

        <!-- Edit Profile Form -->
        <div class="card">
            <div class="card-content">
                <form action="profile.php" method="POST">
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                            <label for="first_name">First Name</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                            <label for="last_name">Last Name</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="text" id="place" name="place" value="<?= htmlspecialchars($user['place']) ?>" required>
                            <label for="place">Place</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="text" id="state" name="state" value="<?= htmlspecialchars($user['state']) ?>" required>
                            <label for="state">State</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="number" id="age" name="age" value="<?= htmlspecialchars($user['age']) ?>" required>
                            <label for="age">Age</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($user['contact_number']) ?>" required>
                            <label for="contact_number">Contact Number</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            <label for="email">Email</label>
                        </div>
                        <button type="submit" name="update_profile" class="btn">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Account Button -->
        <div class="card">
            <div class="card-content">
                <form action="profile.php" method="POST">
                    <button type="submit" name="delete_account" class="btn red">Delete Account</button>
                </form>
            </div>
        </div>

        <!-- Joined Trips -->
        <h5 class="center-align">Your Joined Trips</h5>
        <div class="card">
            <div class="card-content">
                <table class="striped">
                    <thead>
                        <tr>
                            <th>Destination</th>
                            <th>Trip Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($joined_trips as $trip): ?>
                            <tr>
                                <td><?= htmlspecialchars($trip['destination']) ?></td>
                                <td><?= htmlspecialchars($trip['trip_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SweetAlert Script -->
    <script>
        $(document).ready(function() {
            <?php if (isset($_POST['delete_account'])): ?>
                Swal.fire({
                    title: 'Account Deleted!',
                    text: 'Your account has been deleted successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            <?php endif; ?>
        });
    </script>

    <!-- Bootstrap and Materialize JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
