<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch logged-in user's data
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$logged_in_user = $user_stmt->get_result()->fetch_assoc();

// Fetch all users excluding the logged-in user
$users_stmt = $conn->prepare("SELECT * FROM users WHERE id != ?");
$users_stmt->bind_param('i', $user_id);
$users_stmt->execute();
$users = $users_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Matchmaking: Suggest users with similar destinations
$match_stmt = $conn->prepare("
    SELECT * 
    FROM users 
    WHERE id != ? 
    AND destinations LIKE ?
");
$search_pattern = '%' . $logged_in_user['destinations'] . '%';
$match_stmt->bind_param('is', $user_id, $search_pattern);
$match_stmt->execute();
$matches = $match_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Syncgo</title>
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
        .chat-container {
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
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
        }
        .btn {
            background-color: var(--primary-color) !important;
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

    <!-- Chat Container -->
    <div class="container chat-container">
        <h4 class="center-align">Chat</h4>
        <div class="search-container">
            <input type="text" id="search" placeholder="Search users...">
        </div>
        <div id="user-cards">
            <?php foreach ($users as $user): ?>
                <div class="card">
                    <div class="card-content">
                        <div class="profile-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p><strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong></p>
                            <p><?= htmlspecialchars($user['place'] . ', ' . $user['state']) ?></p>
                        </div>
                        <div class="card-action">
                            <a href="chat_detail.php?user_id=<?= $user['id'] ?>" class="btn-small">
                                Chat <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h5 class="center-align">Suggested Matches</h5>
        <div id="match-cards">
            <?php foreach ($matches as $match): ?>
                <div class="card">
                    <div class="card-content">
                        <div class="profile-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p><strong><?= htmlspecialchars($match['first_name'] . ' ' . $match['last_name']) ?></strong></p>
                            <p><?= htmlspecialchars($match['place'] . ', ' . $match['state']) ?></p>
                        </div>
                        <div class="card-action">
                            <a href="chat_detail.php?user_id=<?= $match['id'] ?>" class="btn-small">
                                Chat <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- jQuery Search Script -->
    <script>
        $(document).ready(function () {
            $('#search').on('input', function () {
                const query = $(this).val().toLowerCase();
                $('.card').each(function () {
                    const name = $(this).find('strong').text().toLowerCase();
                    $(this).toggle(name.includes(query));
                });
            });
        });
    </script>
</body>
</html>
