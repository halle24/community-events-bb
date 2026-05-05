<?php 
include '../includes/mysql_connect.php';
include '../includes/header.php'; 
require_once '../helper/activity-log.php';

// Security Check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    logActivity($dbcon, 'admin_unauthorized_access_attempt', [
        'userId' => $_SESSION['user_id'] ?? null,
        'status' => 'failure',
        'targetType' => 'edit_event_page',
        'details'    => [
            'page'   => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'reason' => isset($_SESSION['user_id']) ? 'not_admin' : 'not_logged_in'
        ]
    ]);
    header("Location: ../login.php?error=unauthorized");
    exit;
}

$id = (int)$_GET['id'];

// 1. Fetch Current Data
$result = mysqli_query($dbcon, "SELECT * FROM events WHERE EventID = $id");
$event = mysqli_fetch_assoc($result);

if (!$event) {
    echo "<div class='alert alert-danger'>Event not found.</div>";
    exit;
}
$adminID = $_SESSION['admin_id'] ?? null;

// 2. Handle Update Logic
if (isset($_POST['update'])) {
    $eventName = mysqli_real_escape_string($dbcon, $_POST['EventName']);
    $eventDate = $_POST['EventDate'];
    $startTime = $_POST['StartTime'];
    $endTime = $_POST['EndTime'];
    $location = mysqli_real_escape_string($dbcon, $_POST['Location']);
    $organiser = mysqli_real_escape_string($dbcon, $_POST['Organiser']);
    $description = mysqli_real_escape_string($dbcon, $_POST['Description']);

    $sql = "UPDATE events SET 
            EventName = '$eventName', 
            EventDate = '$eventDate', 
            StartTime = '$startTime', 
            EndTime = '$endTime', 
            Location = '$location', 
            Organiser = '$organiser', 
            Description = '$description' 
            WHERE EventID = $id";

    // Compare dates
    $today = date('Y-m-d');
    if (!empty($eventDate) && strtotime($eventDate) < strtotime($today)) {
        echo '<div class="alert alert-danger">Date must not be in the past.</div>';
    } else {
        if (mysqli_query($dbcon, $sql)) {
            echo '<div class="alert alert-success">Event updated successfully! <a href="manage-events.php">Back to list</a></div>';
            logActivity($dbcon, 'update_event', [
                'adminId' => $adminID,
                'targetType' => 'event',
                'targetId' => $id,
                'details' => [
                    'eventName' => $eventName,
                    'eventDate' => $eventDate,
                    'startTime' => $startTime,
                    'endTime' => $endTime
                ]
            ]);

            // Refresh local data to show in form
            $event = $_POST; 
        } else {
            echo '<div class="alert alert-danger">Update failed: ' . htmlspecialchars(mysqli_error($dbcon)) . '</div>';
            logActivity($dbcon, 'update_event_failed', [
                'adminId' => $adminID,
                'status' => 'failure',
                'targetType' => 'event',
                'targetId' => $id,
                'details' => [
                    'eventName' => $eventName,
                    'error' => htmlspecialchars(mysqli_error($dbcon))
                ]
            ]);

        }
    }
}
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold mb-0">Edit Event</h2>
            <a href="manage-events.php" class="btn btn-outline-secondary">Cancel</a>
        </div>

        <form method="POST" class="card shadow-sm p-4">
            <div class="mb-3">
                <label class="form-label fw-bold">Event Name</label>
                <input type="text" name="EventName" class="form-control" value="<?= htmlspecialchars($event['EventName']) ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" name="EventDate" class="form-control" value="<?= $event['EventDate'] ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Start Time</label>
                    <input type="text" name="StartTime" class="form-control" value="<?= htmlspecialchars($event['StartTime']) ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">End Time</label>
                    <input type="text" name="EndTime" class="form-control" value="<?= htmlspecialchars($event['EndTime']) ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Location</label>
                    <input type="text" name="Location" class="form-control" value="<?= htmlspecialchars($event['Location']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Organiser</label>
                    <input type="text" name="Organiser" class="form-control" value="<?= htmlspecialchars($event['Organiser']) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="Description" class="form-control" rows="5" required><?= htmlspecialchars($event['Description']) ?></textarea>
            </div>

            <button type="submit" name="update" class="btn btn-warning btn-lg w-100">Update Event Details</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>