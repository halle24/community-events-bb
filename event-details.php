<?php 
include 'includes/mysql_connect.php';
include 'includes/header.php'; 
require_once 'helper/activity-log.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = (int)$_GET['id'];

$result = mysqli_query($dbcon, "SELECT * FROM events WHERE EventID = $id");
$event = mysqli_fetch_assoc($result);

if (isset($_POST['register_event'])) {
    if (!isset($_SESSION['user_id'])) {
        logActivity($dbcon, 'register_event_unauthenticated_attempt', [
            'status'     => 'failure',
            'targetType' => 'event',
            'targetId'   => $id,
        ]);
        header("Location: login.php?error=unauthorized");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $check = mysqli_query($dbcon, "SELECT * FROM attendees WHERE UserID=$user_id AND EventID=$id");

    if (mysqli_num_rows($check) == 0) {
        if (mysqli_query($dbcon, "INSERT INTO attendees (UserID, EventID, DateRegistered) VALUES ($user_id, $id, CURDATE())")) {
            echo '<div class="alert alert-success">You have successfully registered for this event!</div>';
            logActivity($dbcon, 'register_event', [
                'userId'     => $user_id,
                'targetType' => 'event',
                'targetId'   => $id,
            ]);
        } else {
            echo '<div class="alert alert-danger">Registration failed: ' . htmlspecialchars(mysqli_error($dbcon)) . '</div>';
            logActivity($dbcon, 'register_event_failed', [
                'userId'     => $user_id,
                'status'     => 'failure',
                'targetType' => 'event',
                'targetId'   => $id,
                'details'    => ['error' => mysqli_error($dbcon)],
            ]);
        }
    } else {
        echo '<div class="alert alert-info">You are already registered for this event.</div>';
        logActivity($dbcon, 'register_event_duplicate_attempt', [
            'userId'     => $user_id,
            'status'     => 'failure',
            'targetType' => 'event',
            'targetId'   => $id,
        ]);
    }
}
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <h2 class="text-primary fw-bold"><?= htmlspecialchars($event['EventName']) ?></h2>
        
        <div class="my-4 p-4 bg-white border rounded-3 shadow-sm">
            <p><strong>Date:</strong> <?= $event['EventDate'] ?> 
               | <strong>Time:</strong> <?= $event['StartTime'] ?> - <?= $event['EndTime'] ?></p>
            
            <p><strong>Organiser:</strong> <?= htmlspecialchars($event['Organiser']) ?></p>
            
            <p><strong>Location:</strong> <?= htmlspecialchars($event['Location']) ?></p>
        </div>

        <div class="mb-4">
            <h5 class="text-primary">About This Event</h5>
            <p class="lead"><?= nl2br(htmlspecialchars($event['Description'])) ?></p>
        </div>

        <?php if (isset($_SESSION['user_id']) && !$_SESSION['is_admin']): ?>
            <div class="text-center my-5">
                <form method="POST">
                    <button type="submit" name="register_event" 
                            class="btn btn-success btn-lg px-5">
                        Register for this Event
                    </button>
                </form>
            </div>
        <?php elseif (!isset($_SESSION['user_id'])): ?>
            <div class="alert alert-warning text-center">
                <strong><a href="login.php" class="alert-link">Login</a></strong> to register for this event.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>