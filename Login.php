<?php
session_start();

// Koneksi ke database
require_once 'koneksi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

        $stmt = $mysqli->prepare("SELECT username, password, role, group_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_user, $db_pass, $db_role, $db_group_id);
            $stmt->fetch();
            if (password_verify($pass, $db_pass)) {
                $_SESSION['username'] = $db_user;
                $_SESSION['role'] = $db_role;
                $_SESSION['group_id'] = $db_group_id;
                header('Location: index.php');
                exit;
            }
        }
    $error = 'Username atau password salah!'; } 
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
        <p>Belum punya akun? <a href="Register.php">Daftar di sini</a></p>
<p><a href="lupa_password.php">Lupa Password?</a></p>
    </div>
</body>
</html>