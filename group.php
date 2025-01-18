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
$groups_query = $conn->query("SELECT * FROM `groups`");
$groups = [];
if ($groups_query) {
    $groups = $groups_query->fetch_all(MYSQLI_ASSOC);
}

// Fetch user-specific groups
$user_groups_query = $conn->prepare("SELECT g.*, gm.role FROM group_members gm JOIN `groups` g ON gm.group_id = g.id WHERE gm.user_id = ?");
$user_groups_query->bind_param('i', $user_id);
$user_groups_query->execute();
$user_groups_result = $user_groups_query->get_result();
$user_groups = [];
if ($user_groups_result) {
    $user_groups = $user_groups_result->fetch_all(MYSQLI_ASSOC);
}
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
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.6/dist/sweetalert2.min.css" rel="stylesheet">
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
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            padding: 10px;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .group-icon {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 20px;
        }
        .card-content {
            flex-grow: 1;
        }
        .card-content h5 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .card-content p {
            margin: 5px 0;
        }
        .card-action {
            padding: 10px 20px;
            background-color: #f1f1f1;
            border-radius: 0 0 15px 15px;
        }
        .btn-small {
            margin-right: 5px;
        }
        .btn-green {
            background-color: #28a745;
        }
        .btn-red {
            background-color: #dc3545;
        }
        .btn-whatsapp {
            background-color: #25D366;
        }
        .right-arrow {
            font-size: 18px;
            margin-left: 10px;
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
    <!-- Create Group Button -->
    <a href="#createGroupModal" class="btn-floating btn-large waves-effect waves-light green modal-trigger">
        <i class="material-icons">add</i>
    </a>
    <div class="row">
        <?php if (count($user_groups) > 0): ?>
            <?php foreach ($user_groups as $group): ?>
                <div class="col s12 m6 l4">
                    <div class="card">
                        <div class="group-info">
                            <img src="<?= $group['group_icon'] ?: 'https://via.placeholder.com/300' ?>" class="group-icon">
                            <div class="card-content">
                                <h5><?= htmlspecialchars($group['name']) ?></h5>
                                <p><strong>Destination:</strong> <?= htmlspecialchars($group['destination']) ?></p>
                                <p><strong>Date:</strong> <?= htmlspecialchars($group['date']) ?></p>
                                <p><strong>Role:</strong> <?= ucfirst($group['role']) ?></p>
                            </div>
                        </div>
                        <div class="card-action">
                            <a href="group_details.php?group_id=<?= $group['id'] ?>" class="btn-small btn-whatsapp">
                                View
                                <?php if ($group['role'] == 'user'): ?>
                                    <i class="fas fa-arrow-right right-arrow"></i>
                                <?php endif; ?>
                            </a>
                            <?php if ($group['role'] == 'admin'): ?>
                                <a href="javascript:void(0);" class="btn-small btn-red" onclick="confirmDelete(<?= $group['id'] ?>)">Delete</a>
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
                    <div class="group-info">
                        <img src="<?= $group['group_icon'] ?: 'https://via.placeholder.com/300' ?>" class="group-icon">
                        <div class="card-content">
                            <h5><?= htmlspecialchars($group['name']) ?></h5>
                            <p><strong>Destination:</strong> <?= htmlspecialchars($group['destination']) ?></p>
                            <p><strong>Date:</strong> <?= htmlspecialchars($group['date']) ?></p>
                        </div>
                    </div>
                    <div class="card-action">
                        <a href="join_group.php?group_id=<?= $group['id'] ?>" class="btn-small btn-green" onclick="return confirmJoin()">Join</a>
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
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.6/dist/sweetalert2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modals = document.querySelectorAll('.modal');
        M.Modal.init(modals);
    });

    // SweetAlert for Join Group Confirmation
    function confirmJoin() {
        event.preventDefault();
        const groupLink = event.target.getAttribute('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to join this group?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, Join!',
            cancelButtonText: 'No, Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = groupLink;
            }
        });
    }

    // SweetAlert for Delete Group Confirmation
    function confirmDelete(groupId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this group?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#28a745',
            confirmButtonText: 'Yes, Delete!',
            cancelButtonText: 'No, Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'delete_group.php?group_id=' + groupId;
            }
        });
    }
</script>
</body>
</html>
