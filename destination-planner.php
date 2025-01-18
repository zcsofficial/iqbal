<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch available trips
$trips_stmt = $conn->prepare("SELECT * FROM trips WHERE joined_members_count < allowed_members_count");
$trips_stmt->execute();
$trips = $trips_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Check if the user has already joined any trip
$joined_trips_stmt = $conn->prepare("SELECT trip_id FROM trip_members WHERE user_id = ?");
$joined_trips_stmt->bind_param('i', $user_id);
$joined_trips_stmt->execute();
$joined_trips = $joined_trips_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Convert the joined trips into an array of trip IDs for easier checking
$joined_trip_ids = array_map(function($trip) { return $trip['trip_id']; }, $joined_trips);

// Handle status messages for SweetAlert
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destination Planner - Syncgo</title>
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
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
        .card .card-content {
            display: flex;
            align-items: center;
        }
        .card .profile-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            margin-right: 15px;
        }
        .card-action {
            text-align: right;
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
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="group.php">Groups</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Destination Planner -->
    <div class="container">
        <h4 class="center-align">Destination Planner</h4>

        <!-- Create New Trip Form -->
        <div class="row">
            <form action="destination-planner.php" method="POST">
                <div class="input-field col s12">
                    <input type="text" id="destination" name="destination" required>
                    <label for="destination">Destination</label>
                </div>
                <div class="input-field col s12">
                    <input type="date" id="trip_date" name="trip_date" required>
                    <label for="trip_date">Trip Date</label>
                </div>
                <div class="input-field col s12">
                    <input type="number" id="allowed_members_count" name="allowed_members_count" required>
                    <label for="allowed_members_count">Allowed Members</label>
                </div>
                <button type="submit" name="create_trip" class="btn">Create Trip</button>
            </form>
        </div>

        <!-- Handle SweetAlert Notifications -->
        <script>
            $(document).ready(function() {
                <?php if ($status == 'success'): ?>
                    Swal.fire({
                        title: 'Success!',
                        text: 'You have successfully joined the trip!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                <?php elseif ($status == 'full'): ?>
                    Swal.fire({
                        title: 'Error!',
                        text: 'Sorry, this trip is already full.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                <?php elseif ($status == 'already_joined'): ?>
                    Swal.fire({
                        title: 'Already Joined!',
                        text: 'You have already joined this trip.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                <?php endif; ?>
            });
        </script>

        <!-- Available Trips -->
        <h5 class="center-align">Available Trips</h5>
        <div id="trip-cards">
            <?php foreach ($trips as $trip): ?>
                <div class="card">
                    <div class="card-content">
                        <div>
                            <p><strong>Destination:</strong> <?= htmlspecialchars($trip['destination']) ?></p>
                            <p><strong>Trip Date:</strong> <?= htmlspecialchars($trip['trip_date']) ?></p>
                            <p><strong>Allowed Members:</strong> <?= htmlspecialchars($trip['allowed_members_count']) ?></p>
                            <p><strong>Joined Members:</strong> <?= htmlspecialchars($trip['joined_members_count']) ?></p>
                        </div>
                        <div class="card-action">
                            <?php if (in_array($trip['id'], $joined_trip_ids)): ?>
                                <span class="btn-small" style="background-color: grey;">Joined</span>
                            <?php else: ?>
                                <a href="join_trip.php?trip_id=<?= $trip['id'] ?>" class="btn-small" id="join-<?= $trip['id'] ?>">Join Trip</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Disable Join Trip button if the user has already joined
        <?php foreach ($trips as $trip): ?>
            <?php if (in_array($trip['id'], $joined_trip_ids)): ?>
                $('#join-<?= $trip['id'] ?>').prop('disabled', true).text('Joined').css('background-color', 'grey');
            <?php endif; ?>
        <?php endforeach; ?>
    </script>

</body>
</html>
