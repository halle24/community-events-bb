<?php
include 'includes/mysql_connect.php';
include 'includes/header.php';

// Get filter inputs
$search = isset($_GET['search']) ? mysqli_real_escape_string($dbcon, $_GET['search']) : '';
$location = isset($_GET['location']) ? mysqli_real_escape_string($dbcon, $_GET['location']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_asc';

// Base Query
$query = "SELECT * FROM events WHERE 1=1";

// Filter by Search Name
if ($search != '') {
    $query .= " AND EventName LIKE '%$search%'";
}

// Filter by Location
if ($location != '') {
    $query .= " AND Location = '$location'";
}

// Filter by Status (Cancelled vs Active)
if ($status == 'active') {
    $query .= " AND isCancelled = 0";
} elseif ($status == 'cancelled') {
    $query .= " AND isCancelled = 1";
}

// Apply Sorting
switch ($sort) {
    case 'az':
        $query .= " ORDER BY EventName ASC";
        break;
    case 'za':
        $query .= " ORDER BY EventName DESC";
        break;
    case 'date_desc':
        $query .= " ORDER BY EventDate DESC";
        break;
    default:
        $query .= " ORDER BY EventDate ASC";
        break;
}

$result = mysqli_query($dbcon, $query);

// Get unique locations for the dropdown
$locQuery = mysqli_query($dbcon, "SELECT DISTINCT Location FROM events");
?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-primary fw-bold">Upcoming Community Events</h2>
        <p class="lead">Discover fun and free events happening in Christ Church</p>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="small text-muted">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Event name..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-md-2">
                <label class="small text-muted">Location</label>
                <select name="location" class="form-select">
                    <option value="">All Locations</option>
                    <?php while ($loc = mysqli_fetch_assoc($locQuery)): ?>
                        <option value="<?= htmlspecialchars($loc['Location']) ?>" <?= $location == $loc['Location'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($loc['Location']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="all" <?= $status == 'all' ? 'selected' : '' ?>>All Statuses</option>
                    <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Active Only</option>
                    <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Cancelled Only</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="small text-muted">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="date_asc" <?= $sort == 'date_asc' ? 'selected' : '' ?>>Soonest</option>
                    <option value="date_desc" <?= $sort == 'date_desc' ? 'selected' : '' ?>>Latest</option>
                    <option value="az" <?= $sort == 'az' ? 'selected' : '' ?>>A-Z</option>
                    <option value="za" <?= $sort == 'za' ? 'selected' : '' ?>>Z-A</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                <a href="events.php" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>
<?php if (mysqli_num_rows($result) > 0): ?>
    <div class="row g-4">
        <?php while ($row = mysqli_fetch_assoc($result)):
            $isCancelled = $row['isCancelled'] == 1;
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 <?= $isCancelled ? 'event-cancelled' : '' ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary">
                            <?= htmlspecialchars($row['EventName']) ?>
                            <?php if ($isCancelled): ?>
                                <span class="badge bg-danger ms-2">CANCELLED</span>
                            <?php endif; ?>
                        </h5>

                        <?php if ($isCancelled): ?>
                            <button class="btn btn-secondary mt-auto" disabled>Event Cancelled</button>
                        <?php else: ?>

                            <p class="card-text">
                                <strong>Date:</strong> <?= $row['EventDate'] ?><br>
                                <strong>Time:</strong> <?= $row['StartTime'] ?> - <?= $row['EndTime'] ?>
                            </p>

                            <p class="card-text">
                                <strong>Location:</strong> <?= htmlspecialchars($row['Location']) ?>
                            </p>

                            <p class="card-text flex-grow-1">
                                <?= htmlspecialchars(substr($row['Description'], 0, 150)) ?>...
                            </p>

                            <a href="event-details.php?id=<?= $row['EventID'] ?>"
                                class="btn btn-primary mt-auto">
                                View Details & Register
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        No upcoming events at the moment. Please check back later!
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>