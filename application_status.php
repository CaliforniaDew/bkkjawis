<?php
session_start();
include 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Fetch job applications for the logged-in student
$user_id = $_SESSION["user_id"];
$query = "SELECT jobs.job_title, jobs.company_name, jobs.job_location, jobs.salary, job_applications.status, job_applications.applied_at
          FROM job_applications
          JOIN jobs ON job_applications.job_id = jobs.id
          WHERE job_applications.user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Aplikasi Pekerjaan - SMK JAYAWISATA 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <div class="bg-white w-64 p-6 shadow-lg">
        <div class="flex items-center mb-6">
            <img src="<?= htmlspecialchars($_SESSION['profile_picture'] ?? 'https://img.freepik.com/premium-vector/default-avatar-profile-icon-social-media-user-image-gray-avatar-icon-blank-profile-silhouette-vector-illustration_561158-3383.jpg'); ?>" alt="Profile Picture" class="w-12 h-12 rounded-full mr-4">
            <div>
                <h2 class="text-xl font-bold">Welcome, <?= htmlspecialchars($_SESSION["full_name"]); ?>!</h2>
                <p class="text-gray-600"><?= htmlspecialchars($_SESSION["graduation_year"]); ?></p>
                <p class="text-gray-600"><?= ucfirst(htmlspecialchars($_SESSION["role"])); ?></p>
            </div>
        </div>
        <nav class="flex flex-col gap-4">
            <a href="profile.php" class="text-gray-700 hover:text-blue-500">Profile</a>
            <a href="dashboard.php" class="text-gray-700 hover:text-blue-500">Dashboard</a>
            <a href="application_status.php" class="text-gray-700 hover:text-blue-500">Status Aplikasi</a>
            <a href="logout.php" class="text-gray-700 hover:text-red-500">Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <div class="mt-6 w-full max-w-6xl mx-auto">
            <h2 class="text-2xl font-bold mb-4">Status Aplikasi Pekerjaan</h2>
            <?php if (empty($applications)): ?>
                <p class="text-gray-500">Anda belum melamar pekerjaan.</p>
            <?php else: ?>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Judul Pekerjaan</th>
                            <th class="py-2 px-4 border-b">Perusahaan</th>
                            <th class="py-2 px-4 border-b">Lokasi</th>
                            <th class="py-2 px-4 border-b">Gaji</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Tanggal Melamar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($application['job_title']); ?></td>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($application['company_name']); ?></td>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($application['job_location']); ?></td>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($application['salary']); ?></td>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($application['status']); ?></td>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($application['applied_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>