<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['lampiran'])) {
    $task = $_POST['task'] ?? '';
    $user = $_SESSION['username'];
    $file = $_FILES['lampiran'];

    if ($task && $file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid('lampiran_', true) . '.' . $ext;
        $target = __DIR__ . '/../uploads/' . $newName; // perbaiki path

        if (move_uploaded_file($file['tmp_name'], $target)) {
            // Koneksi ke database
            $mysqli = new mysqli("localhost", "root", "", "tugas_project_akhir");
            $type = $_POST['type'] ?? 'hasil'; // default hasil
            $stmt = $mysqli->prepare("INSERT INTO attachments (task_name, filename, uploaded_by, type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $task, $newName, $user, $type);
            $stmt->execute();
            $stmt->close();
            header("Location: ../index.php?upload=success"); // perbaiki redirect
            exit;
        } else {
            header("Location: ../index.php?upload=fail");
            exit;
        }
    }
}
header("Location: ../index.php?upload=fail");
exit;