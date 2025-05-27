<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'];
$role = $_SESSION['role'];
$roleLabel = $role === 'ketua' ? 'Ketua Kelompok' : 'Anggota';
$roleColor = $role === 'ketua' ? '#4f46e5' : '#388e3c';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="style.css">
    <style>
      body {
        background: #f4f6f8;
      }
      .profile-box {
        max-width: 420px;
        margin: 60px auto;
        padding: 2.5rem 2rem 2rem 2rem;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 16px #0002;
        text-align: center;
        position: relative;
      }
      .avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5 60%, #a5b4fc 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
        color: #fff;
        margin: -70px auto 1rem auto;
        border: 4px solid #fff;
        box-shadow: 0 2px 8px #0001;
      }
      .profile-box h2 {
        margin-top: 0.5rem;
        margin-bottom: 0.2rem;
        font-size: 1.7rem;
        color: #333;
      }
      .role-badge {
        display: inline-block;
        padding: 0.3rem 1.1rem;
        border-radius: 20px;
        font-size: 1rem;
        font-weight: 600;
        color: #fff;
        background: <?= $roleColor ?>;
        margin-bottom: 1.2rem;
        margin-top: 0.2rem;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px #0001;
      }
      .profile-info {
        margin: 1.5rem 0 2rem 0;
        text-align: left;
        font-size: 1.1rem;
      }
      .profile-info p {
        margin: 0.7rem 0;
        padding-left: 1.2rem;
        position: relative;
      }
      .profile-info p::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.7rem;
        width: 7px;
        height: 7px;
        background: #4f46e5;
        border-radius: 50%;
      }
      .btn {
        display: inline-block;
        margin-top: 1rem;
        background: #4f46e5;
        color: #fff;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.2s;
        border: none;
        cursor: pointer;
      }
      .btn:hover {
        background: #4338ca;
      }
    </style>
</head>
<body>
    <div class="profile-box">
        <div class="avatar">
            <?= strtoupper(substr($username, 0, 1)) ?>
        </div>
        <h2><?= htmlspecialchars($username) ?></h2>
        <div class="role-badge"><?= $roleLabel ?></div>
        <div class="profile-info">
            <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars(ucfirst($role)) ?></p>
        </div>
        <a href="index.php" class="btn">Kembali ke Dashboard</a>
    </div>
</body>
</html>