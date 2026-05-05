<?php session_start();

// Detect if we are inside /admin folder
$is_admin_page = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;

// Set base path
$base = $is_admin_page ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Events - Christ Church, Barbados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $base ?>images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $base ?>images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $base ?>images/favicon-16x16.png">
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
    <script src="<?= $base ?>assets/js/script.js" defer></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= $base ?>index.php">Community Events BB</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= $base ?>index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base ?>events.php">Events</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $base ?>my-registrations.php">My Registrations</a></li>

                        <?php if (!empty($_SESSION['is_admin'])): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= $base ?>admin/dashboard.php">Admin Dashboard</a></li>
                        <?php endif; ?>

                        <li class="nav-item"><a class="nav-link" href="<?= $base ?>logout.php">
                                Logout (<?= htmlspecialchars($_SESSION['username']) ?>)
                            </a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $base ?>login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $base ?>register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">