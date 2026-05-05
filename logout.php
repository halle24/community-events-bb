<?php
session_start();
include 'includes/mysql_connect.php';
require_once 'helper/activity-log.php';

logActivity($dbcon, 'logout', [
    'userId'  => $_SESSION['user_id']  ?? null,
    'adminId' => $_SESSION['admin_id'] ?? null,
]);

session_destroy();
header("Location: index.php");
exit;
?>