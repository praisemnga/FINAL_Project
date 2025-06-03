<?php
session_start();

// Koneksi ke database
require_once 'koneksi.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($password !== $confirmPassword) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } elseif (strlen($password) < 8) {
        $error = 'Password harus minimal 8 karakter.';
    } else {
        // Validasi username
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId);
            $stmt->fetch();
            $stmt->close();

            // Update password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();
            $stmt->close();

            $success = 'Password berhasil diatur ulang. Anda dapat login sekarang.';
        } else {
            $error = 'Username tidak ditemukan.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-box">
        <h2>Reset Password</h2>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
        <?php if (!$success): ?>
        <form method="post">
            <input type="text" name="username" placeholder="Masukkan username Anda" required autofocus>
            <input type="password" name="password" placeholder="Password baru" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi password baru" required>
            <button class="btn" type="submit">Reset Password</button>
        </form>
        <?php endif; ?>
        <p><a href="Login.php">Kembali ke Login</a></p>
    </div>
</body>
</html>
