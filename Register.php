<?php
session_start();

$usersFile = 'users.json';
// Jika file belum ada, buat file dengan array kosong
if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([]));
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $users = [];
    if (file_exists($usersFile)) {
        $json = file_get_contents($usersFile);
        $users = json_decode($json, true);
        if (!is_array($users)) $users = [];
    }
    foreach ($users as $user) {
        if ($user['username'] === $uname && password_verify($pass, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Username atau password salah!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Manajemen Tugas Kelompok</title>
    <link rel="stylesheet" href="style.css">
    <style>
      .login-box {max-width:350px;margin:60px auto;padding:2rem;background:#fff;border-radius:10px;box-shadow:0 2px 8px #0001;}
      .login-box input {width:100%;margin-bottom:1rem;}
      .error {color:#d32f2f;}
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn" type="submit">Login</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>
</html>