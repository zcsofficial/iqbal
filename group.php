<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all groups
$groups_query = $conn->prepare("SELECT * FROM `groups`");
$groups_query->execute();
$groups = $groups_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch user-specific groups
$user_groups_query = $conn->prepare("
    SELECT g.*, gm.role 
    FROM group_members gm 
    JOIN `groups` g ON gm.group_id = g.id 
    WHERE gm.user_id = ?
");
$user_groups_query->execute([$user_id]);
$user_groups = $user_groups_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups - Syncgo</title>
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f8f9;
        }
        .navbar {
            background-color: #006400; /* Dark Green */
        }
        .navbar .brand-logo {
            font-size: 24px;
            font-weight: bold;
        }
        .card {
            border-radius: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .group-icon {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }
        .btn-small {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-wrapper">
        <a href="#" class="brand-logo center">Syncgo Groups</a>
        <ul id="nav-mobile" class="right">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <!-- User Groups Section -->
    <h4>Your Groups</h4>
    <div class="row">
        <?php if (count($user_groups) > 0): ?>
            <?php foreach ($user_groups as $group): ?>
                <div class="col s12 m6 l4">
                    <div class="card">
                        <div class="card-image">
                            <img src="<?= $group['group_icon'] ?: 'https://via.placeholder.com/300' ?>" class="group-icon">
                            <?php if ($group['role'] == 'admin'): ?>
                                <a href="#editGroupModal<?= $group['id'] ?>" class="btn-floating halfway-fab waves-effect waves-light red modal-trigger">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h5><?= htmlspecialchars($group['name']) ?></h5>
                            <p>Destination: <?= htmlspecialchars($group['destination']) ?></p>
                            <p>Date: <?= htmlspecialchars($group['date']) ?></p>
                            <p>Role: <?= ucfirst($group['role']) ?></p>
                        </div>
                        <div class="card-action">
                            <a href="group_details.php?group_id=<?= $group['id'] ?>" class="btn-small green">View</a>
                            <?php if ($group['role'] == 'admin'): ?>
                                <a href="delete_group.php?group_id=<?= $group['id'] ?>" class="btn-small red" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No groups found. <a href="#createGroupModal" class="modal-trigger">Create one!</a></p>
        <?php endif; ?>
    </div>

    <!-- All Groups Section -->
    <h4>All Groups</h4>
    <div class="row">
        <?php foreach ($groups as $group): ?>
            <div class="col s12 m6 l4">
                <div class="card">
                    <div class="card-image">
                        <img src="<?= $group['group_icon'] ?: 'https://via.placeholder.com/300' ?>" class="group-icon">
                    </div>
                    <div class="card-content">
                        <h5><?= htmlspecialchars($group['name']) ?></h5>
                        <p>Destination: <?= htmlspecialchars($group['destination']) ?></p>
                        <p>Date: <?= htmlspecialchars($group['date']) ?></p>
                    </div>
                    <div class="card-action">
                        <a href="join_group.php?group_id=<?= $group['id'] ?>" class="btn-small green">Join</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Group Modal -->
<div id="createGroupModal" class="modal">
    <div class="modal-content">
        <h4>Create Group</h4>
        <form action="create_group.php" method="POST" enctype="multipart/form-data">
            <div class="input-field">
                <input type="text" name="name" id="name" required>
                <label for="name">Group Name</label>
            </div>
            <div class="input-field">
                <input type="text" name="destination" id="destination" required>
                <label for="destination">Destination</label>
            </div>
            <div class="input-field">
                <input type="date" name="date" id="date" required>
            </div>
            <div class="file-field input-field">
                <div class="btn">
                    <span>Upload Icon</span>
                    <input type="file" name="group_icon" accept="image/*">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Upload group icon">
                </div>
            </div>
            <button type="submit" class="btn green">Create</button>
        </form>
    </div>
</div>

<!-- Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modals = document.querySelectorAll('.modal');
        M.Modal.init(modals);
    });
</script>
</body>
</html>
