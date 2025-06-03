<?php
session_start();

// Koneksi ke database
require_once 'koneksi.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'anggota';
    $group_id = $_POST['group_id'] ?? '';
    $group_name = trim($_POST['group_name'] ?? '');

    // Validasi password
    if (
        strlen($pass) < 8 ||
        !preg_match('/[A-Z]/', $pass) ||
        !preg_match('/[a-z]/', $pass) ||
        !preg_match('/[0-9]/', $pass) ||
        !preg_match('/[\W_]/', $pass)
    ) {
        $error = 'Password minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter spesial.';
    } else {
        // Cek username sudah ada
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Username sudah terdaftar!';
        } else {
            if ($role === 'ketua') {
                // Ketua: cek group_id belum pernah dipakai
                $stmt = $mysqli->prepare("SELECT id FROM groups WHERE group_id = ?");
                $stmt->bind_param("i", $group_id);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $error = 'ID Kelompok sudah dipakai oleh kelompok lain!';
                } elseif (empty($group_name)) {
                    $error = 'Nama kelompok harus diisi!';
                } else {
                    // Insert ke tabel groups
                    $stmt->close();
                    $stmt = $mysqli->prepare("INSERT INTO groups (group_id, group_name) VALUES (?, ?)");
                    $stmt->bind_param("is", $group_id, $group_name);
                    $stmt->execute();
                    // Insert user ketua
                    $hash = password_hash($pass, PASSWORD_DEFAULT);
                    $stmt = $mysqli->prepare("INSERT INTO users (username, password, role, group_id) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("sssi", $uname, $hash, $role, $group_id);
                    if ($stmt->execute()) {
                        $success = 'Registrasi berhasil! Silakan login.';
                    } else {
                        $error = 'Gagal registrasi. Coba lagi.';
                    }
                }
            } else {
                // Anggota: cek group_id sudah ada
                $stmt = $mysqli->prepare("SELECT id FROM groups WHERE group_id = ?");
                $stmt->bind_param("i", $group_id);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows == 0) {
                    $error = 'ID Kelompok tidak ditemukan. Minta ketua untuk membuatnya!';
                } else {
                    // Insert user anggota
                    $hash = password_hash($pass, PASSWORD_DEFAULT);
                    $stmt = $mysqli->prepare("INSERT INTO users (username, password, role, group_id) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("sssi", $uname, $hash, $role, $group_id);
                    if ($stmt->execute()) {
                        $success = 'Registrasi berhasil! Silakan login.';
                    } else {
                        $error = 'Gagal registrasi. Coba lagi.';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Sistem Manajemen Tugas Kelompok</title>
    <link rel="stylesheet" href="style.css">
    <style>
      .login-box {max-width:350px;margin:60px auto;padding:2rem;background:#fff;border-radius:10px;box-shadow:0 2px 8px #0001;}
      .login-box input, .login-box select {width:100%;margin-bottom:1rem;}
      .error {color:#d32f2f;}
      .success {color:#388e3c;}
    </style>
    <script>
    function toggleGroupName() {
      var role = document.querySelector('select[name="role"]').value;
      document.getElementById('group_name_box').style.display = (role === 'ketua') ? 'block' : 'none';
    }
    </script>
</head>
<body>
    <div class="login-box">
        <h2>Register</h2>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <small>Password minimal 8 karakter, huruf besar, huruf kecil, angka, dan karakter spesial.</small>
            <select name="role" onchange="toggleGroupName()">
                <option value="anggota">Anggota</option>
                <option value="ketua">Ketua</option>
            </select>
            <input type="number" name="group_id" placeholder="ID Kelompok" required>
            <div id="group_name_box" style="display:none;">
                <input type="text" name="group_name" placeholder="Nama Kelompok (khusus ketua)">
            </div>
            <button class="btn" type="submit">Register</button>
        </form>
        <p>Sudah punya akun? <a href="Login.php">Login di sini</a></p>
    </div>
    <script>toggleGroupName();</script>
</body>
</html>