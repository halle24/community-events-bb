<?php 
include '../includes/mysql_connect.php';
include '../includes/header.php'; 
require_once '../helper/activity-log.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    logActivity($dbcon, 'admin_unauthorized_access_attempt', [
        'userId' => $_SESSION['user_id'] ?? null,
        'status' => 'failure',
        'targetType' => 'create_event_page',
        'details'    => [
            'page'   => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'reason' => isset($_SESSION['user_id']) ? 'not_admin' : 'not_logged_in'
        ]
    ]);
    header("Location: ../login.php?error=unauthorized");
    exit;
}

$adminID = $_SESSION['admin_id'] ?? null;

if (isset($_POST['create'])) {
    $eventName = mysqli_real_escape_string($dbcon, $_POST['EventName']);
    $eventDate = $_POST['EventDate'];
    $startTime = $_POST['StartTime'];
    $endTime = $_POST['EndTime'];
    $organiser = mysqli_real_escape_string($dbcon, $_POST['Organiser']);
    $location = mysqli_real_escape_string($dbcon, $_POST['Location']);
    $description = mysqli_real_escape_string($dbcon, $_POST['Description']);
    $adminID = $_SESSION['admin_id'];

    // Compare dates
    $today = date('Y-m-d');
    if (!empty($eventDate) && strtotime($eventDate) < strtotime($today)) {
        echo '<div class="alert alert-danger">Date must not be in the past.</div>';
    } else {

        $sql = "INSERT INTO events (AdminID, EventDate, StartTime, EndTime, Organiser, Location, Description, isCancelled, EventName) 
                VALUES ($adminID, '$eventDate', '$startTime', '$endTime', '$organiser', '$location', '$description', 0, '$eventName')";
        
        if (mysqli_query($dbcon, $sql)) {
            echo '<div class="alert alert-success">Event created successfully!</div>';
            logActivity($dbcon, 'create_event', [
                'adminId' => $adminID,
                'targetType' => 'event',
                'targetId' => mysqli_insert_id($dbcon),
                'details' => [
                    'eventName' => $eventName,
                    'eventDate' => $eventDate,
                    'startTime' => $startTime,
                    'endTime' => $endTime
                ]
            ]);
        } else {
            echo '<div class="alert alert-danger">Error: ' . mysqli_error($dbcon) . '</div>';
            logActivity($dbcon, 'create_event_failed', [
                'adminId' => $adminID,
                'status' => 'failure',
                'targetType' => 'event',
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
        <h2 class="text-primary fw-bold mb-4">Create New Event</h2>
        
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="EventName" class="form-control" placeholder="Event Name" required>
            </div>
            <div class="mb-3">
                <input type="date" name="EventDate" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" name="StartTime" class="form-control" placeholder="Start Time (e.g. 4:10 PM)" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="text" name="EndTime" class="form-control" placeholder="End Time (e.g. 7:00 PM)" required>
                </div>
            </div>
            <div class="mb-3">
                <input type="text" name="Organiser" class="form-control" placeholder="Organiser" required>
            </div>
            <div class="mb-3">
                <input type="text" name="Location" class="form-control" placeholder="Location" required>
            </div>
            <div class="mb-3">
                <textarea name="Description" class="form-control" rows="6" placeholder="Description" required></textarea>
            </div>
            <button type="submit" name="create" class="btn btn-success btn-lg px-5">Create Event</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>