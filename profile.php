<?php
session_start();
include 'config.php';

// Redirect ke login kalau belum masuk
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$fullname = $_SESSION["full_name"] ?? 'User';
$profile_picture = $_SESSION["profile_picture"] ?? 'https://img.freepik.com/premium-vector/default-avatar-profile-icon-social-media-user-image-gray-avatar-icon-blank-profile-silhouette-vector-illustration_561158-3383.jpg';
$cv = $_SESSION["cv"] ?? '';

// Fetch additional user data
$stmt = $pdo->prepare("SELECT phone_number, status_lulusan, nama_perusahaan, posisi_kerja, status_kerja, pekerjaan_sesuai_bidang, perjanjian_kerja, lokasi FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$phone_number = $user_data['phone_number'] ?? '';
$status_lulusan = $user_data['status_lulusan'] ?? '';
$nama_perusahaan = $user_data['nama_perusahaan'] ?? '';
$posisi_kerja = $user_data['posisi_kerja'] ?? '';
$status_kerja = $user_data['status_kerja'] ?? '';
$pekerjaan_sesuai_bidang = $user_data['pekerjaan_sesuai_bidang'] ?? '';
$perjanjian_kerja = $user_data['perjanjian_kerja'] ?? '';
$lokasi = $user_data['lokasi'] ?? '';

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        $profile_picture = $target_file;
        $_SESSION["profile_picture"] = $profile_picture;

        // Update profile picture in the database
        $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->execute([$profile_picture, $user_id]);
    }
}

// Handle CV upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['cv'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["cv"]["name"]);
    if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
        $cv = $target_file;
        $_SESSION["cv"] = $cv;

        // Update CV in the database
        $stmt = $pdo->prepare("UPDATE users SET cv = ? WHERE id = ?");
        $stmt->execute([$cv, $user_id]);
    }
}

// Handle additional profile updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_FILES['profile_picture']) && !isset($_FILES['cv'])) {
    $phone_number = $_POST['phone_number'];
    $status_lulusan = $_POST['status_lulusan'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $posisi_kerja = $_POST['posisi_kerja'];
    $status_kerja = $_POST['status_kerja'];
    $pekerjaan_sesuai_bidang = $_POST['pekerjaan_sesuai_bidang'];
    $perjanjian_kerja = $_POST['perjanjian_kerja'];
    $lokasi = $_POST['lokasi'];

    $stmt = $pdo->prepare("UPDATE users SET phone_number = ?, status_lulusan = ?, nama_perusahaan = ?, posisi_kerja = ?, status_kerja = ?, pekerjaan_sesuai_bidang = ?, perjanjian_kerja = ?, lokasi = ? WHERE id = ?");
    $stmt->execute([$phone_number, $status_lulusan, $nama_perusahaan, $posisi_kerja, $status_kerja, $pekerjaan_sesuai_bidang, $perjanjian_kerja, $lokasi, $user_id]);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-4">Profile</h2>
        <div class="flex flex-col items-center mb-6">
            <img src="<?= htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="w-24 h-24 rounded-full mb-4">
            <h3 class="text-xl font-bold"><?= htmlspecialchars($fullname); ?></h3>
        </div>
        <form method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            <div>
                <label for="profile_picture" class="block text-gray-700">Change Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" class="border p-2 rounded w-full">
            </div>
            <div>
                <label for="cv" class="block text-gray-700">Upload CV</label>
                <input type="file" name="cv" id="cv" class="border p-2 rounded w-full">
            </div>
            <div>
                <label for="phone_number" class="block text-gray-700">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" value="<?= htmlspecialchars($phone_number); ?>" class="border p-2 rounded w-full">
            </div>
            <div>
                <label for="status_lulusan" class="block text-gray-700">Status Lulusan</label>
                <select name="status_lulusan" id="status_lulusan" class="border p-2 rounded w-full">
                    <option value="Bekerja" <?= $status_lulusan == 'Bekerja' ? 'selected' : ''; ?>>Bekerja</option>
                    <option value="Menganggur" <?= $status_lulusan == 'Menganggur' ? 'selected' : ''; ?>>Menganggur</option>
                </select>
            </div>
            <div>
                <label for="nama_perusahaan" class="block text-gray-700">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" id="nama_perusahaan" value="<?= htmlspecialchars($nama_perusahaan); ?>" class="border p-2 rounded w-full">
            </div>
            <div>
                <label for="posisi_kerja" class="block text-gray-700">Posisi Kerja</label>
                <input type="text" name="posisi_kerja" id="posisi_kerja" value="<?= htmlspecialchars($posisi_kerja); ?>" class="border p-2 rounded w-full">
            </div>
            <div>
                <label for="status_kerja" class="block text-gray-700">Status Kerja</label>
                <input type="text" name="status_kerja" id="status_kerja" value="<?= htmlspecialchars($status_kerja); ?>" class="border p-2 rounded w-full">
            </div>
            <div>
                <label for="pekerjaan_sesuai_bidang" class="block text-gray-700">Pekerjaan Sesuai Bidang</label>
                <select name="pekerjaan_sesuai_bidang" id="pekerjaan_sesuai_bidang" class="border p-2 rounded w-full">
                    <option value="Ya" <?= $pekerjaan_sesuai_bidang == 'Ya' ? 'selected' : ''; ?>>Ya</option>
                    <option value="Tidak" <?= $pekerjaan_sesuai_bidang == 'Tidak' ? 'selected' : ''; ?>>Tidak</option>
                </select>
            </div>
            <div>
                <label for="perjanjian_kerja" class="block text-gray-700">Perjanjian Kerja</label>
                <select name="perjanjian_kerja" id="perjanjian_kerja" class="border p-2 rounded w-full">
                    <option value="Kontrak" <?= $perjanjian_kerja == 'Kontrak' ? 'selected' : ''; ?>>Kontrak</option>
                    <option value="Tetap" <?= $perjanjian_kerja == 'Tetap' ? 'selected' : ''; ?>>Tetap</option>
                </select>
            </div>
            <div>
                <label for="lokasi" class="block text-gray-700">Lokasi</label>
                <input type="text" name="lokasi" id="lokasi" value="<?= htmlspecialchars($lokasi); ?>" class="border p-2 rounded w-full">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
        </form>
        <?php if ($cv): ?>
            <div class="mt-4">
                <a href="<?= htmlspecialchars($cv); ?>" target="_blank" class="text-blue-500">View Uploaded CV</a>
            </div>
        <?php endif; ?>
        <div class="mt-4">
            <a href="dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>