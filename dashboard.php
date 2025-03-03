<?php
session_start();
include 'config.php';

// Redirect ke login kalau belum masuk
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Ambil data session
$fullname = $_SESSION["full_name"] ?? 'User';
$role = $_SESSION["role"] ?? 'Unknown';
$graduation_year = $_SESSION["graduation_year"] ?? 'N/A';
$cv = $_SESSION["cv"] ?? '';

// Ambil pencarian dari GET
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';

// Pagination settings
$limit = 12; // Jumlah job per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query ambil lowongan kerja
$query = "SELECT jobs.*, job_applications.status AS application_status FROM jobs
          LEFT JOIN job_applications ON jobs.id = job_applications.job_id AND job_applications.user_id = ?";
$params = [$_SESSION["user_id"]];

if (!empty($search)) {
    $query .= " WHERE (job_title LIKE ? OR company_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($location)) {
    $query .= " AND job_location LIKE ?";
    $params[] = "%$location%";
}

$query .= " LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total data untuk pagination
$count_query = "SELECT COUNT(*) FROM jobs WHERE (job_title LIKE ? OR company_name LIKE ?)";
$count_params = ["%$search%", "%$search%"];

if (!empty($location)) {
    $count_query .= " AND job_location LIKE ?";
    $count_params[] = "%$location%";
}

$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($count_params);
$total_jobs = $count_stmt->fetchColumn();
$total_pages = ceil($total_jobs / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard BKK - SMK JAYAWISATA 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .job-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            background-color: #ffffff;
            transition: box-shadow 0.3s ease;
        }
        .job-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .job-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .job-card p {
            margin-bottom: 0.5rem;
            color: #6b7280;
        }
        .job-card a {
            color: #3b82f6;
            text-decoration: none;
        }
        .job-card a:hover {
            text-decoration: underline;
        }
        .job-card .apply-button {
            background-color: #10b981;
            color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .job-card .apply-button:hover {
            background-color: #059669;
        }
        .job-card .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        .job-card img {
            max-width: 100px;
            max-height: 79px;
            object-fit: contain;
            margin-bottom: 0.5rem;
        }
        .slide-panel {
            position: fixed;
            right: -100%;
            top: 0;
            width: 30%;
            height: 100%;
            background-color: #fff;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
            transition: right 0.3s ease;
            z-index: 100;
            padding: 20px;
            overflow-y: auto;
        }
        .slide-panel.open {
            right: 0;
        }
        .slide-panel .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .slide-panel .close:hover,
        .slide-panel .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <div class="bg-white w-64 p-6 shadow-lg">
        <div class="flex items-center mb-6">
            <img src="<?= htmlspecialchars($_SESSION['profile_picture'] ?? 'https://img.freepik.com/premium-vector/default-avatar-profile-icon-social-media-user-image-gray-avatar-icon-blank-profile-silhouette-vector-illustration_561158-3383.jpg'); ?>" alt="Profile Picture" class="w-12 h-12 rounded-full mr-4">
            <div>
                <h2 class="text-xl font-bold">Welcome, <?= htmlspecialchars($fullname); ?>!</h2>
                <p class="text-gray-600"><?= htmlspecialchars($graduation_year); ?></p>
                <p class="text-gray-600"><?= ucfirst(htmlspecialchars($role)); ?></p>
            </div>
        </div>
        <nav class="flex flex-col gap-4">
    <a href="profile.php" class="text-gray-700 hover:text-blue-500">Profile</a>
    <a href="dashboard.php" class="text-gray-700 hover:text-blue-500">Dashboard</a>
    <a href="application_status.php" class="text-gray-700 hover:text-blue-500">Status Aplikasi</a>
    <a href="logout.php" class="text-gray-700 hover:text-red-500">Logout</a>
    <?php if ($_SESSION["role"] === 'HR'): ?>
        <a href="create_job.php" class="text-gray-700 hover:text-green-500">Buat Lowongan Baru</a>
    <?php endif; ?>
</nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <div class="mt-6 w-full max-w-6xl mx-auto">
            <h2 class="text-2xl font-bold mb-4">Lowongan Pekerjaan</h2>
            <form method="GET" class="flex gap-2 mb-4">
                <input type="text" name="search" placeholder="Cari pekerjaan..." value="<?= htmlspecialchars($search); ?>" class="border p-2 rounded w-full">
                <input type="text" name="location" placeholder="Lokasi..." value="<?= htmlspecialchars($location); ?>" class="border p-2 rounded w-full">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Cari</button>
            </form>
            
            <div class="bg-white shadow-md rounded-lg p-4 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php if (empty($jobs)): ?>
                    <p class="text-gray-500">Tidak ada lowongan yang ditemukan.</p>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                        <?php
                        $company_logo = $job['company_logo'] ?? 'https://upload.wikimedia.org/wikipedia/commons/c/ca/LinkedIn_logo_initials.png';
                        if (stripos($job['company_name'], 'Marriott') !== false) {
                            $company_logo = 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Marriott_hotels_logo14.svg/2560px-Marriott_hotels_logo14.svg.png';
                        }
                        ?>
                        <div class="job-card">
                            <img src="<?= htmlspecialchars($company_logo); ?>" alt="Company Logo">
                            <h3><?= htmlspecialchars($job['job_title'] ?? 'Tidak ada judul'); ?></h3>
                            <p><?= htmlspecialchars($job['company_name'] ?? 'Perusahaan tidak diketahui'); ?></p>
                            <p><?= htmlspecialchars($job['job_location'] ?? 'Lokasi tidak tersedia'); ?></p>
                            <p><?= htmlspecialchars($job['salary'] ?? 'Gaji tidak tersedia'); ?></p>
                            <p>Status: <?= htmlspecialchars($job['application_status'] ?? 'In Progress'); ?></p>
                            <div class="button-container">
                                <a href="javascript:void(0);" onclick="openDetailPanel('<?= htmlspecialchars($job['job_title']); ?>', '<?= htmlspecialchars($job['job_description']); ?>', '<?= htmlspecialchars($job['salary']); ?>', '<?= htmlspecialchars($job['url']); ?>')">Detail</a>
                                <div class="apply-button" onclick="openApplyModal('<?= htmlspecialchars($job['id']); ?>')">Apply</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination Controls -->
            <div class="flex justify-center mt-4">
                <?php if ($page > 1): ?>
                    <a href="?search=<?= urlencode($search); ?>&location=<?= urlencode($location); ?>&page=<?= $page - 1; ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded mr-2">Previous</a>
                <?php endif; ?>

                <span class="px-4 py-2 text-gray-700">Page <?= $page; ?> of <?= $total_pages; ?></span>

                <?php if ($page < $total_pages): ?>
                    <a href="?search=<?= urlencode($search); ?>&location=<?= urlencode($location); ?>&page=<?= $page + 1; ?>" class="px-4 py-2 bg-blue-500 text-white rounded">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Detail Slide Panel -->
    <div id="detailPanel" class="slide-panel">
        <span class="close" onclick="closeDetailPanel()">&times;</span>
        <h2 id="panelJobTitle" class="text-2xl font-bold mb-4"></h2>
        <p id="panelJobDescription" class="mb-4"></p>
        <p id="panelJobSalary" class="mb-4"></p>
        <a id="panelJobUrl" href="#" target="_blank" class="text-blue-500">Apply Here</a>
    </div>

    <!-- Apply Modal -->
    <div id="applyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-4">Apply for Job</h2>
            <form id="applyForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                <input type="hidden" name="job_id" id="job_id">
                <div>
                    <label for="cv" class="block text-gray-700">Upload New CV</label>
                    <input type="file" name="cv" id="cv" class="border p-2 rounded w-full">
                </div>
                <div>
                    <input type="checkbox" name="use_current_cv" id="use_current_cv" value="1">
                    <label for="use_current_cv" class="text-gray-700">Use Current CV</label>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Apply</button>
            </form>
            <button onclick="closeApplyModal()" class="bg-gray-500 text-white px-4 py-2 rounded mt-4">Cancel</button>
        </div>
    </div>

    <script>
        function openDetailPanel(jobTitle, jobDescription, jobSalary, jobUrl) {
            document.getElementById('panelJobTitle').innerText = jobTitle;
            document.getElementById('panelJobDescription').innerText = jobDescription;
            document.getElementById('panelJobSalary').innerText = 'Salary: ' + jobSalary;
            document.getElementById('panelJobUrl').href = jobUrl;
            document.getElementById('detailPanel').classList.add('open');
        }

        function closeDetailPanel() {
            document.getElementById('detailPanel').classList.remove('open');
        }

        function openApplyModal(jobId) {
            document.getElementById('job_id').value = jobId;
            document.getElementById('applyModal').classList.remove('hidden');
        }

        function closeApplyModal() {
            document.getElementById('applyModal').classList.add('hidden');
        }

        document.getElementById('applyForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('apply.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert('Berhasil Apply!');
                closeApplyModal();
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>