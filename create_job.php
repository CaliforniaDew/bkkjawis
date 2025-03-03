<?php
session_start();
include 'config.php';

// Redirect ke login kalau belum masuk
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Check if the user has the HR role
if ($_SESSION["role"] !== 'HR') {
    header("Location: dashboard.php");
    exit;
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_title = $_POST["job_title"];
    $company_name = $_POST["company_name"];
    $job_location = $_POST["job_location"];
    $url = $_POST["url"];

    $sql = "INSERT INTO jobs (job_title, company_name, job_location, url) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$job_title, $company_name, $job_location, $url])) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error_message = "Gagal membuat lowongan kerja!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Listing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Buat Lowongan Baru</h2>
        
        <!-- Pesan error tampil di sini -->
        <?php if (!empty($error_message)): ?>
            <p class="text-red-500 font-semibold mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="create_job.php">
            <input type="text" name="job_title" placeholder="Job Title" required class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="text" name="company_name" placeholder="Company Name" required class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="text" name="job_location" placeholder="Job Location" required class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="url" name="url" placeholder="Job URL" required class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded hover:bg-blue-600 transition duration-300">Create</button>
        </form>
    </div>
</body>
</html>