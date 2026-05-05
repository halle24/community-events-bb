<?php
include 'includes/mysql_connect.php';
include 'includes/header.php';
require_once 'helper/activity-log.php';
if (isset($_POST['register'])) {
    // 1. Sanitize Inputs
    $username = mysqli_real_escape_string($dbcon, $_POST['username']);
    $password = $_POST['password'];
    $firstName = mysqli_real_escape_string($dbcon, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($dbcon, $_POST['lastName']);
    $phoneNo = mysqli_real_escape_string($dbcon, $_POST['phoneNo']);
    $email = mysqli_real_escape_string($dbcon, $_POST['email']);

    $errors = [];

    // 2. Validation Logic
    // Check Username for special characters
    if (preg_match('/[@!$%^&*()?<>,\'";:\/`~-]/', $username)) {
        $errors[] = "Username cannot contain special characters.";
    }

    // Check Email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Check Password Strength (min 8 chars, at least one letter and one number)
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must be at least 8 characters long and include both letters and numbers.";
    }


    // 3. Split Duplicate Check for specificity
    $userCheck = mysqli_query($dbcon, "SELECT * FROM USERLOGIN WHERE username='$username'");
    $emailCheck = mysqli_query($dbcon, "SELECT * FROM USERLOGIN WHERE email='$email'");
    $adminUserCheck = mysqli_query($dbcon, "SELECT * FROM ADMINLOGIN WHERE username='$username'");
    $adminEmailCheck = mysqli_query($dbcon, "SELECT * FROM ADMINLOGIN WHERE email='$email'");

    if (mysqli_num_rows($userCheck) > 0 || mysqli_num_rows($adminUserCheck) > 0) {
        $errors[] = "That username has already been taken.";
    }
    if (mysqli_num_rows($emailCheck) > 0 || mysqli_num_rows($adminEmailCheck) > 0) {
        $errors[] = "That email address is already registered.";
    }

    // 4. Final Execution
    if (empty($errors)) {
        $safe_password = mysqli_real_escape_string($dbcon, $password);
        $sql = "INSERT INTO USERLOGIN (username, password, firstName, lastName, phoneNo, email) 
                VALUES ('$username', '$safe_password', '$firstName', '$lastName', '$phoneNo', '$email')";

        if (mysqli_query($dbcon, $sql)) {
            echo '<div class="alert alert-success">Registration successful! <a href="login.php" class="alert-link">Login now</a></div>';
            logActivity($dbcon, 'user_registration', [
                'userId' => mysqli_insert_id($dbcon),
                'details' => [
                    'page' => $_SERVER['REQUEST_URI'] ?? 'unknown'
                ]
            ]);
        } else {
            echo '<div class="alert alert-danger">System Error: ' . htmlspecialchars(mysqli_error($dbcon) ). '</div>';
            logActivity($dbcon, 'user_registration_failed', [
                'status' => 'failure',
                'details' => [
                    'page' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                    'error' => mysqli_error($dbcon)
                ]
            ]);
        }
    } else {
        // Display all errors
        foreach ($errors as $error) {
            echo '<div class="alert alert-danger">' . $error . '</div>';
        }
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-primary text-center mb-4">User Registration</h2>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" name="firstName" class="form-control" placeholder="First Name"
                                value="<?= isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : '' ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="text" name="lastName" class="form-control" placeholder="Last Name" 
                                value="<?= isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : '' ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <input type="tel" pattern="[0-9]{3}-[0-9]{4}" name="phoneNo" class="form-control" placeholder="Phone No" value="<?= isset($_POST['phoneNo']) ? htmlspecialchars($_POST['phoneNo']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-success w-100 btn-lg">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>