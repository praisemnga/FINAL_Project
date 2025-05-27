<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="style.css">
    <style>
      .profile-box {max-width:400px;margin:60px auto;padding:2rem;background:#fff;border-radius:10px;box-shadow:0 2px 8px #0001;}
      .profile-box h2 {margin-top:0;}
      .profile-info {margin:1rem 0;}
      .profile-info strong {display:inline-block;width:100px;}
    </style>
</head>
<body>
    <div class="profile-box">
        <h2>Profil Pengguna</h2>
        <div class="profile-info">
            <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars(ucfirst($role)) ?></p>
        </div>
        <a href="index.php" class="btn">Kembali ke Dashboard</a>
    </div>
</body>
</html>