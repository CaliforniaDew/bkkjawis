<?php
include 'config.php';

$correct_access_code_jobseeker = "SiswaBKK2025"; // Kode akses rahasia untuk jobseeker
$correct_access_code_hr = "HRBKK2025"; // Kode akses rahasia untuk HR

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $graduation_year = $_POST["graduation_year"];
    $access_code = $_POST["access_code"];

    // Determine the role based on the access code
    if ($access_code === $correct_access_code_jobseeker) {
        $role = "jobseeker";
    } elseif ($access_code === $correct_access_code_hr) {
        $role = "HR";
    } else {
        echo "<script>alert('Kode akses salah!'); window.location.href='register.php';</script>";
        exit;
    }

    $sql = "INSERT INTO users (full_name, email, password, role, graduation_year) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$full_name, $email, $password, $role, $graduation_year]);

    echo "<script>alert('Registrasi sukses! Silakan login.'); window.location.href='login.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center h-screen flex items-center justify-center" style="background-image: url('https://i.ibb.co.com/wN3T6LSL/photo-2024-05-14-09-04-17.jpg');">
    <div class="bg-white bg-opacity-80 p-8 rounded-lg shadow-md w-96 text-center">
        <img src="https://i.ibb.co.com/q3Tg5GDM/logo-jawis.png" alt="Logo" class="w-24 mx-auto mb-4">
        <h2 class="text-2xl font-bold mb-4">Daftar Akun</h2>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Nama Lengkap" required class="w-full p-2 border rounded mb-3">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 border rounded mb-3">
            <input type="password" name="password" placeholder="Password" required class="w-full p-2 border rounded mb-3">
            <input type="text" name="graduation_year" placeholder="Tahun Lulus" required class="w-full p-2 border rounded mb-3">
            <input type="text" name="access_code" placeholder="Kode Akses" required class="w-full p-2 border rounded mb-3">
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Daftar</button>
        </form>
        <p class="mt-3">Sudah punya akun? <a href="login.php" class="text-blue-500">Login</a></p>
    </div>
</body>
</html>

