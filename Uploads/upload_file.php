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

        // Pastikan folder .uploads ada
        $hiddenUploadsDir = __DIR__ . '/../.uploads';
        if (!is_dir($hiddenUploadsDir)) {
            mkdir($hiddenUploadsDir, 0755, true);
        }

        // Ubah target folder ke .uploads
        $target = $hiddenUploadsDir . '/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            // Koneksi ke database
            $mysqli = new mysqli("localhost", "root", "", "tugas_project_akhir");
            $type = $_POST['type'] ?? 'hasil'; // default hasil
            // Tambahkan group_id ke dalam proses upload
            $group_id = $_SESSION['group_id'];

            // Perbarui query untuk menyertakan group_id
            $stmt = $mysqli->prepare("INSERT INTO attachments (task_name, filename, uploaded_by, type, group_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $task, $newName, $user, $type, $group_id);
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