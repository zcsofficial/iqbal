<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check for session expiration (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
$_SESSION['last_activity'] = time();

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Syncgo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f8f9;
        }
        .header-bar {
            background-color: #006400; /* Dark Green */
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-bar h1 {
            margin: 0;
            font-size: 24px;
        }
        .header-bar .btn-logout {
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 5px 15px;
            transition: background-color 0.3s ease;
        }
        .header-bar .btn-logout:hover {
            background-color: #c9302c;
        }
        .navbar {
            background-color: #006400; /* Dark Green */
        }
        .navbar-nav .nav-link {
            color: white !important;
        }
        .navbar-nav .nav-link:hover {
            color: #d9534f !important;
        }
        .profile-card, .feature-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-card img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .features-section {
            padding: 40px 20px;
        }
        .features-section h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .feature-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .feature-card i {
            font-size: 40px;
            margin-bottom: 15px;
        }
        .feature-card h5 {
            font-size: 18px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand text-white" href="#">Syncgo</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" onclick="logout()" href="#">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <!-- Profile Card -->
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="profile-card">
                <img src="https://img.freepik.com/free-vector/hand-drawn-marie-curie-illustration_52683-161864.jpg?ga=GA1.1.1097622617.1729950327&semt=ais_hybrid" alt="User Avatar">
                <h5><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                <p>Age: <?php echo htmlspecialchars($user['age']); ?></p>
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features-section">
        <h2>Features</h2>
        <div class="row g-4">
            <?php
            $features = [
                ['icon' => 'fas fa-users text-primary', 'title' => 'Community Groups', 'description' => 'Connect with like-minded travelers and form communities.'],
                ['icon' => 'fas fa-map-marker-alt text-danger', 'title' => 'Destination Planner', 'description' => 'Plan trips and explore destinations with ease.'],
                ['icon' => 'fas fa-comments text-success', 'title' => 'Chats', 'description' => 'Stay connected with your group through real-time chat.'],
                ['icon' => 'fas fa-calendar-alt text-warning', 'title' => 'Event Scheduling', 'description' => 'Organize events and track schedules effortlessly.'],
                ['icon' => 'fas fa-bell text-info', 'title' => 'Notifications', 'description' => 'Get timely updates and reminders for your plans.'],
                ['icon' => 'fas fa-user-cog text-secondary', 'title' => 'Profile Management', 'description' => 'Customize your profile and manage preferences.']
            ];
            foreach ($features as $feature) {
                echo '<div class="col-md-4">
                    <div class="card feature-card">
                        <div class="card-body">
                            <i class="' . $feature['icon'] . '"></i>
                            <h5>' . $feature['title'] . '</h5>
                            <p>' . $feature['description'] . '</p>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }
</script>
</body>
</html>
