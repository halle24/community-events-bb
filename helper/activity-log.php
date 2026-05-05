<?php

function logActivity(mysqli $conn, string $action, array $opts = []): bool {
    $userId     = $opts['userId']     ?? null;
    $adminId    = $opts['adminId']    ?? null;
    $targetType = $opts['targetType'] ?? null;
    $targetId   = $opts['targetId']   ?? null;
    $status     = $opts['status']     ?? 'success';
    $details    = isset($opts['details']) ? json_encode($opts['details']) : null;

    $ip        = $_SERVER['REMOTE_ADDR']     ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    if ($userAgent !== null) {
        $userAgent = substr($userAgent, 0, 255);
    }

    $sql = "INSERT INTO ActivityLogs (userId, adminId, action, targetType, targetId, status, details, ipAddress, userAgent) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    $types = 'iississss';

    $stmt->bind_param($types, $userId, $adminId, $action, $targetType, $targetId, $status, $details, $ip, $userAgent);

    $ok = $stmt->execute();
    if (!$ok) {
        error_log("logActivity execute failed: " . $stmt->error);
    }
    $stmt->close();
    return $ok;
}