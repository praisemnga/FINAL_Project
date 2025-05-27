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
        $target = __DIR__ . '/Uploads/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            // Simpan info ke database
            // $mysqli = new mysqli("localhost", "root", "", "tugas_project_akhir");
            $stmt = $mysqli->prepare("INSERT INTO attachments (task_name, filename, uploaded_by) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $task, $newName, $user);
            $stmt->execute();
            $stmt->close();
            header("Location: index.php?upload=success");
            exit;
        } else {
            header("Location: index.php?upload=fail");
            exit;
        }
    }
}
header("Location: index.php?upload=fail");
exit;