<?php
include '../includes/mysql_connect.php';
include '../includes/header.php';
require_once '../helper/activity-log.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    logActivity($dbcon, 'admin_unauthorized_access_attempt', [
        'userId' => $_SESSION['user_id'] ?? null,
        'status' => 'failure',
        'targetType' => 'manage_events_page',
        'details'    => [
            'page'   => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'reason' => isset($_SESSION['user_id']) ? 'not_admin' : 'not_logged_in'
        ]
    ]);
    header("Location: ../login.php?error=unauthorized");
    exit;
}

if (isset($_GET['cancel'])) {
    $id = (int)$_GET['cancel'];
    if (mysqli_query($dbcon, "UPDATE events SET isCancelled=1 WHERE EventID=$id")) {
        echo '<div class="alert alert-warning">Event has been cancelled.</div>';
        logActivity($dbcon, 'cancel_event', [
            'adminId'    => $_SESSION['admin_id'] ?? null,
            'targetType' => 'event',
            'targetId'   => $id,
        ]);
    } else {
        echo '<div class="alert alert-danger">Failed to cancel event: ' . htmlspecialchars(mysqli_error($dbcon)) . '</div>';
        logActivity($dbcon, 'cancel_event_failed', [
            'adminId'    => $_SESSION['admin_id'] ?? null,
            'status'     => 'failure',
            'targetType' => 'event',
            'targetId'   => $id,
            'details'    => ['error' => mysqli_error($dbcon)],
        ]);
    }
}

if (isset($_GET['uncancel'])) {
    $id = (int)$_GET['uncancel'];
    if (mysqli_query($dbcon, "UPDATE events SET isCancelled=0 WHERE EventID=$id")) {
        echo '<div class="alert alert-success">Event has been restored!</div>';
        logActivity($dbcon, 'uncancel_event', [
            'adminId'    => $_SESSION['admin_id'] ?? null,
            'targetType' => 'event',
            'targetId'   => $id,
        ]);
    } else {
        echo '<div class="alert alert-danger">Failed to restore event: ' . htmlspecialchars(mysqli_error($dbcon)) . '</div>';
        logActivity($dbcon, 'uncancel_event_failed', [
            'adminId'    => $_SESSION['admin_id'] ?? null,
            'status'     => 'failure',
            'targetType' => 'event',
            'targetId'   => $id,
            'details'    => ['error' => mysqli_error($dbcon)],
        ]);
    }
}

$result = mysqli_query($dbcon, "SELECT * FROM events ORDER BY EventDate");
?>
<h2 class="text-primary fw-bold mb-4">Manage Events</h2>

<table class="table table-striped table-hover align-middle">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Event Name</th>
            <th>Date</th>
            <th>Cancelled?</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['EventID'] ?></td>
                <td><?= htmlspecialchars($row['EventName']) ?></td>
                <td><?= $row['EventDate'] ?></td>
                <td><?= $row['isCancelled'] ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>' ?></td>
                <td>
                    <a href="edit-event.php?id=<?= $row['EventID'] ?>" class="btn btn-warning btn-sm">Edit</a>

                    <?php if (!$row['isCancelled']): ?>
                        <a href="?cancel=<?= $row['EventID'] ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to cancel this event?')">
                            Cancel
                        </a>
                    <?php else: ?>
                        <a href="?uncancel=<?= $row['EventID'] ?>"
                            class="btn btn-success btn-sm">
                            Uncancel
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>