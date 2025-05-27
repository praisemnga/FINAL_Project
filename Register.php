<?php
session_start();

$usersFile = 'users.json';
if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([]));
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
        !preg_match('/[A-Z]/', $pass) ||         // huruf besar
        !preg_match('/[a-z]/', $pass) ||         // huruf kecil
        !preg_match('/[0-9]/', $pass) ||         // angka
        !preg_match('/[\W_]/', $pass)            // karakter spesial
    ) {
        $error = 'Password minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter spesial.';
    } else {
        $users = json_decode(file_get_contents($usersFile), true);

        // Cek username sudah ada
        foreach ($users as $user) {
            if ($user['username'] === $uname) {
                $error = 'Username sudah terdaftar!';
                break;
            }
        }

        if (!$error) {
            $users[] = [
                'username' => $uname,
                'password' => password_hash($pass, PASSWORD_DEFAULT),
                'role' => $role
            ];
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            $success = 'Registrasi berhasil! Silakan login.';
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