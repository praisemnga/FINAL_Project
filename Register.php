<?php
session_start();

// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "tugas_project_akhir");
if ($mysqli->connect_errno) {
    die("Gagal koneksi MySQL: " . $mysqli->connect_error);
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'anggota';

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
            // Insert user baru
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $uname, $hash, $role);
            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Gagal registrasi. Coba lagi.';
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
            <select name="role">
                <option value="anggota">Anggota</option>
                <option value="ketua">Ketua</option>
            </select>
            <button class="btn" type="submit">Register</button>
        </form>
        <p>Sudah punya akun? <a href="Login.php">Login di sini</a></p>
    </div>
</body>
</html>