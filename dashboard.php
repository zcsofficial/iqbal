<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
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
        .profile-card {
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
        .profile-card h5 {
            font-size: 22px;
            margin: 10px 0;
        }
        .profile-card p {
            color: #666;
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
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .feature-card .card-body {
            text-align: center;
        }
        .feature-card i {
            font-size: 40px;
            margin-bottom: 15px;
        }
        .feature-card h5 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .feature-card p {
            color: #666;
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
                <img src="https://via.placeholder.com/100" alt="Default Avatar">
                <h5>John Doe</h5>
                <p>Age: 25</p>
                <p>Email: johndoe@example.com</p>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features-section">
        <h2>Features</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body">
                        <i class="fas fa-users text-primary"></i>
                        <h5>Community Groups</h5>
                        <p>Connect with like-minded travelers and form communities.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <h5>Destination Planner</h5>
                        <p>Plan trips and explore destinations with ease.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body">
                        <i class="fas fa-comments text-success"></i>
                        <h5>Chats</h5>
                        <p>Stay connected with your group through real-time chat.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body">
                        <i class="fas fa-calendar-alt text-warning"></i>
                        <h5>Event Scheduling</h5>
                        <p>Organize events and track schedules effortlessly.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body">
                        <i class="fas fa-bell text-info"></i>
                        <h5>Notifications</h5>
                        <p>Get timely updates and reminders for your plans.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body">
                        <i class="fas fa-user-cog text-secondary"></i>
                        <h5>Profile Management</h5>
                        <p>Customize your profile and manage preferences.</p>
                    </div>
                </div>
            </div>
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
