<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $job_id = $_POST['job_id'];
    $use_current_cv = isset($_POST['use_current_cv']) ? 1 : 0;

    // Handle file upload if a new CV is uploaded
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $cv = $_FILES['cv']['name'];
        $cv_tmp = $_FILES['cv']['tmp_name'];
        $cv_dir = 'uploads/cvs/';
        move_uploaded_file($cv_tmp, $cv_dir . $cv);
    } else {
        $cv = $_SESSION['cv'];
    }

    // Insert or update job application
    $stmt = $pdo->prepare("INSERT INTO job_applications (user_id, job_id, status) VALUES (?, ?, 'In Progress')
                           ON DUPLICATE KEY UPDATE status = 'In Progress'");
    $stmt->execute([$user_id, $job_id]);

    echo 'Berhasil Apply!';
}
?>