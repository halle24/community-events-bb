<?php 
include '../includes/mysql_connect.php';
include '../includes/header.php'; 
require_once '../helper/activity-log.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    logActivity($dbcon, 'admin_unauthorized_access_attempt', [
        'userId' => $_SESSION['user_id'] ?? null,
        'status' => 'failure',
        'targetType' => 'view_attendees_page',
        'details'    => [
            'page'   => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'reason' => isset($_SESSION['user_id']) ? 'not_admin' : 'not_logged_in'
        ]
    ]);
    header("Location: ../login.php?error=unauthorized");
    exit;
}

$events = mysqli_query($dbcon, "SELECT * FROM events ORDER BY EventDate");
?>
<h2 class="text-primary fw-bold mb-4">View Attendees</h2>

<?php while ($event = mysqli_fetch_assoc($events)): ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><?= htmlspecialchars($event['EventName']) ?> 
                <small class="text-muted">(<?= $event['EventDate'] ?>)</small>
            </h5>
        </div>
        <div class="card-body">
            <?php
            $att = mysqli_query($dbcon, "SELECT u.firstName, u.lastName, u.email 
                                         FROM attendees a 
                                         JOIN USERLOGIN u ON a.UserID = u.UserID 
                                         WHERE a.EventID = {$event['EventID']} 
                                           AND a.AttendanceConfirmed = 1 
                                           AND a.WithdrawalDate IS NULL");
            ?>
            <?php if (mysqli_num_rows($att) > 0): ?>
                <ul class="list-group">
                <?php while ($user = mysqli_fetch_assoc($att)): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?> 
                        <span class="text-muted">— <?= htmlspecialchars($user['email']) ?></span>
                    </li>
                <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No confirmed attendees yet.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endwhile; ?>

<?php include '../includes/footer.php'; ?>