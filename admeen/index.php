<?php
session_start();
include '../config.php';

// Redirect to login if not an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Query to get total number of users
$user_count_query = "SELECT COUNT(*) FROM users";
$user_count_stmt = $pdo->prepare($user_count_query);
$user_count_stmt->execute();
$total_users = $user_count_stmt->fetchColumn();

// Query to get total number of jobs
$job_count_query = "SELECT COUNT(*) FROM jobs";
$job_count_stmt = $pdo->prepare($job_count_query);
$job_count_stmt->execute();
$total_jobs = $job_count_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
</head>
<body class="bg-gray-100 min-h-screen flex">
    <div class="flex-1 p-6">
        <h2 class="text-2xl font-bold mb-4">Admin Dashboard</h2>
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold">Total Users: <?= htmlspecialchars($total_users); ?></h3>
            <h3 class="text-lg font-semibold">Total Jobs: <?= htmlspecialchars($total_jobs); ?></h3>
        </div>
    </div>
</body>
</html>