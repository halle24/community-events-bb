<?php
include 'includes/mysql_connect.php';
include 'includes/header.php';
require_once 'helper/activity-log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
    logActivity($dbcon, 'user_unauthorized_access_attempt', [
        'userId' => $_SESSION['user_id'] ?? null,
        'status' => 'failure',
        'targetType' => 'my_registrations_page',
        'details'    => [
            'page'   => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'reason' => isset($_SESSION['user_id']) ? 'is_admin' : 'not_logged_in'
        ]
    ]);
    header("Location: login.php?error=unauthorized");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle withdrawal
if (isset($_GET['withdraw'])) {
    $event_id = (int)$_GET['withdraw'];

    $sql = "DELETE FROM attendees 
            WHERE UserID = $user_id AND EventID = $event_id";

    if (mysqli_query($dbcon, $sql)) {
        echo '<div class="alert alert-warning">You have successfully withdrawn from the event.</div>';
        logActivity($dbcon, 'withdraw_event', [
            'userId'     => $user_id,
            'targetType' => 'event',
            'targetId'   => $event_id,
        ]);
    } else {
        echo '<div class="alert alert-danger">Error withdrawing from event.</div>';
        logActivity($dbcon, 'withdraw_event_failed', [
            'userId'     => $user_id,
            'status'     => 'failure',
            'targetType' => 'event',
            'targetId'   => $event_id,
            'details'    => ['error' => mysqli_error($dbcon)],
        ]);
    }
}

$query = "SELECT e.*, a.DateRegistered FROM attendees a 
          JOIN events e ON a.EventID = e.EventID 
          WHERE a.UserID = $user_id 
          ORDER BY e.EventDate";
$result = mysqli_query($dbcon, $query);
?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-primary fw-bold">My Registered Events</h2>
        <p class="lead">Here are the events you have signed up for.</p>
    </div>
</div>

<?php if (mysqli_num_rows($result) > 0): ?>
    <div class="row g-4">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary">
                            <?= htmlspecialchars($row['EventName']) ?>
                            <?php if ($row['isCancelled'] == 1): ?>
                                <span class="badge bg-danger ms-2">CANCELLED</span>
                            <?php endif; ?>
                        </h5>

                        <p>
                            <strong>Date:</strong> <?= $row['EventDate'] ?><br>
                            <strong>Time:</strong> <?= $row['StartTime'] ?> - <?= $row['EndTime'] ?>
                        </p>

                        <p><strong>Location:</strong> <?= htmlspecialchars($row['Location']) ?></p>

                        <p class="mt-2">
                            <strong>Registered on:</strong> <?= $row['DateRegistered'] ?>
                        </p>

                        <a href="?withdraw=<?= $row['EventID'] ?>"
                            class="btn btn-danger mt-auto"
                            onclick="return confirm('Are you sure you want to withdraw from this event?')">
                            Withdraw from Event
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        You have no active registrations at the moment.
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>