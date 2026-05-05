<?php
require_once '../includes/mysql_connect.php';
require_once '../includes/header.php';
require_once '../helper/activity-log.php';

// Security check — admins only
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    logActivity($dbcon, 'admin_unauthorized_access_attempt', [
        'userId'     => $_SESSION['user_id'] ?? null,
        'status'     => 'failure',
        'targetType' => 'activity_logs_page',
        'details'    => [
            'page'   => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'reason' => isset($_SESSION['user_id']) ? 'not_admin' : 'not_logged_in'
        ]
    ]);
    header("Location: ../login.php?error=unauthorized");
    exit;
}

// Filter inputs
$filterAction = isset($_GET['action']) ? mysqli_real_escape_string($dbcon, $_GET['action']) : '';
$filterStatus = isset($_GET['status']) ? mysqli_real_escape_string($dbcon, $_GET['status']) : '';
$limit        = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
if ($limit < 1 || $limit > 500) $limit = 100;

// Build query
$where = [];
if ($filterAction !== '') $where[] = "Action = '$filterAction'";
if ($filterStatus !== '') $where[] = "Status = '$filterStatus'";
$whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

$sql = "SELECT * FROM ActivityLogs $whereClause ORDER BY CreatedAt DESC LIMIT $limit";
$result = mysqli_query($dbcon, $sql);

// Pull distinct action names for the filter dropdown
$actionList = mysqli_query($dbcon, "SELECT DISTINCT Action FROM ActivityLogs ORDER BY Action");
?>

<div class="container mt-4">
    <h2>Activity Logs</h2>
    <p class="text-muted">Showing the most recent activity recorded in the system.</p>

    <!-- Filter form -->
    <form method="get" class="row g-2 mb-3">
        <div class="col-auto">
            <select name="action" class="form-select">
                <option value="">All actions</option>
                <?php while ($a = mysqli_fetch_assoc($actionList)): ?>
                    <option value="<?= htmlspecialchars($a['Action']) ?>" <?= $filterAction === $a['Action'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['Action']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="success" <?= $filterStatus === 'success' ? 'selected' : '' ?>>Success</option>
                <option value="failure" <?= $filterStatus === 'failure' ? 'selected' : '' ?>>Failure</option>
            </select>
        </div>
        <div class="col-auto">
            <select name="limit" class="form-select">
                <?php foreach ([50, 100, 200, 500] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?> rows</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="activity-logs.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Results table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover table-sm">
            <thead class="table-dark">
                <tr>
                    <th>Time</th>
                    <th>Actor</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>Status</th>
                    <th>IP</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr><td colspan="7" class="text-center text-muted">No log entries match your filters.</td></tr>
                <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['CreatedAt']) ?></td>
                            <td>
                                <?php if ($row['AdminID']): ?>
                                    <span class="badge bg-danger">Admin #<?= (int)$row['AdminID'] ?></span>
                                <?php elseif ($row['UserID']): ?>
                                    <span class="badge bg-primary">User #<?= (int)$row['UserID'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Anonymous</span>
                                <?php endif; ?>
                            </td>
                            <td><code><?= htmlspecialchars($row['Action']) ?></code></td>
                            <td>
                                <?php if ($row['TargetType']): ?>
                                    <?= htmlspecialchars($row['TargetType']) ?>
                                    <?php if ($row['TargetID']): ?>
                                        #<?= (int)$row['TargetID'] ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['Status'] === 'success'): ?>
                                    <span class="badge bg-success">success</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">failure</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= htmlspecialchars($row['IPAddress'] ?? '—') ?></small></td>
                            <td>
                                <?php if ($row['Details']): ?>
                                    <small><code><?= htmlspecialchars($row['Details']) ?></code></small>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>