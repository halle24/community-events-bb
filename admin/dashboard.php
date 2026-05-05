<?php 
include '../includes/mysql_connect.php';
include '../includes/header.php'; 
require_once '../helper/activity-log.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    logActivity($dbcon, 'admin_unauthorized_access_attempt', [
        'userId' => $_SESSION['user_id'] ?? null,
        'status' => 'failure',
        'targetType' => 'dashboard_page',
        'details'    => [
            'page'   => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'reason' => isset($_SESSION['user_id']) ? 'not_admin' : 'not_logged_in'
        ]
    ]);
    header("Location: ../login.php?error=unauthorized");
    exit;
}
?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-primary fw-bold">Admin Dashboard</h2>
        <p class="lead">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">Create New Event</h5>
                <p class="card-text">Add a new community event to the calendar.</p>
                <a href="create-event.php" class="btn btn-success btn-lg w-100">Create Event</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">Manage Events</h5>
                <p class="card-text">View, edit or cancel existing events.</p>
                <a href="manage-events.php" class="btn btn-warning btn-lg w-100">Manage Events</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">View Attendees</h5>
                <p class="card-text">See who has registered for each event.</p>
                <a href="view-attendees.php" class="btn btn-primary btn-lg w-100">View Attendees</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">Activity Logs</h5>
                <p class="card-text">Review user and admin activity for accountability.</p>
                <a href="activity-logs.php" class="btn btn-info btn-lg w-100">View Logs</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>