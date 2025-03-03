<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT id, full_name, email, password, role, graduation_year FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["graduation_year"] = $user["graduation_year"];

        header("Location: dashboard.php");
        exit;
    } else {
        $error_message = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BKK SMK Jayawisata 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center h-screen flex items-center justify-center" style="background-image: url('https://i.ibb.co.com/wN3T6LSL/photo-2024-05-14-09-04-17.jpg');">
    <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-lg w-96 text-center">
        <img src="https://i.ibb.co/q3Tg5GDM/logo-jawis.png" alt="Logo" class="w-24 mx-auto mb-4">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Login</h2>
        
        <!-- Pesan error tampil di sini -->
        <?php if (!empty($error_message)): ?>
            <p class="text-red-500 font-semibold mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded hover:bg-blue-600 transition duration-300">Login</button>
        </form>
        <p class="mt-4 text-gray-600">Belum punya akun? <a href="register.php" class="text-blue-500 hover:underline">Daftar</a></p>
    </div>
</body>
</html>
