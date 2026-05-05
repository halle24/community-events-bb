<?php 
include 'includes/mysql_connect.php';
include 'includes/header.php'; 
?>
<div class="row">
    <div class="col-lg-8 mx-auto text-center mb-5">
        <h1 class="display-4 fw-bold text-primary mb-3">Welcome to Community Events</h1>
        <p class="lead mb-4">
            Connecting the Christ Church community through free and fun local events 
            at Sheraton Mall and beyond.
        </p>
        <a href="events.php" class="btn btn-lg btn-success px-4">Browse All Events</a>
    </div>
</div>

<div class="row g-4">
    <!-- Upcoming Events Card -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title text-primary">Upcoming Events</h5>
                <p class="card-text">Browse and register for exciting community events happening near you.</p>
                <a href="events.php" class="btn btn-primary">View All Events</a>
            </div>
        </div>
    </div>

    <!-- Featured Event -->
    <div class="col-md-6">
        <div class="card h-100 featured-card shadow">
            <div class="card-body">
                <span class="badge bg-warning text-dark mb-2">FEATURED</span>
                <h5 class="card-title text-primary">Interstellar Movie Screening</h5>
                <p class="card-text">
                    Free outdoor movie night on <strong>April 18th</strong>.<br>
                    First 100 people only! Children must be over 12.
                </p>
                <a href="event-details.php?id=1" class="btn btn-success">Learn More & Register</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>