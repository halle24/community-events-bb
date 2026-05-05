<?php
include 'includes/mysql_connect.php';
include 'includes/header.php';
require_once 'helper/activity-log.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($dbcon, $_POST['username']);
    $password = mysqli_real_escape_string($dbcon, $_POST['password']);

    // Check ADMINLOGIN FIRST
    $query = "SELECT * FROM ADMINLOGIN WHERE username='$username' AND password='$password'";
    $result = mysqli_query($dbcon, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin_id'] = $row['AdminID'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['is_admin'] = true;
        logActivity($dbcon, 'admin_login', [
            'adminId' => $row['AdminID'],
            'details' => [
                'page' => $_SERVER['REQUEST_URI'] ?? 'unknown'
            ]
        ]);
        header("Location: admin/dashboard.php");
        exit();
    }

    // THEN check USERLOGIN
    $query = "SELECT * FROM USERLOGIN WHERE username='$username' AND password='$password'";
    $result = mysqli_query($dbcon, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['UserID'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['is_admin'] = false;
        logActivity($dbcon, 'user_login', [
            'userId' => $row['UserID'],
            'details' => [
                'page' => $_SERVER['REQUEST_URI'] ?? 'unknown'
            ]
        ]);
        header("Location: index.php");
        exit();
    }

    logActivity($dbcon, 'login_failed', [
        'status'   => 'failure',
        'details'  => [
            'page' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'attemptedUsername' => $username,
        ]
    ]);
    echo '<div class="alert alert-danger">Invalid username or password.</div>';
}
?>
<?php
if (isset($_GET['error']) && $_GET['error'] == 'unauthorized') {
    echo '<div class="container mt-3"><div class="alert alert-danger text-center shadow-sm">
            <strong>Permission Denied:</strong> You must be logged in with the correct account type to view that page.
          </div></div>';
}
?>
<div class="row justify-content-center">
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm no-hover">
            <div class="card-body">
                <h2 class="text-primary text-center mb-4">Login</h2>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100 btn-lg">Login</button>
                </form>
                
                <p class="text-center mt-4">
                    Don't have an account? 
                    <a href="register.php" class="text-success">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>