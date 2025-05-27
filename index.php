<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sistem Manajemen Tugas Kelompok</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body data-role="<?= htmlspecialchars($role) ?>">
  <header>
    <h1>Sistem Manajemen Tugas Kelompok</h1>
    <p>Kelola tugas dengan efisien — untuk anggota dan ketua tim</p>
    <a href="profile.php" style="float:right;color:#fff;text-decoration:none;margin-top:-2rem;margin-right:90px;">Profil</a>
    <a href="logout.php" style="float:right;color:#fff;text-decoration:none;margin-top:-2rem;">Logout</a>
  </header>

  <div class="container">
    <div class="section">
      <h2>📋 Tugas Anggota</h2>
      <ul class="task-list">
        <li>
          <div class="task-info">
            <input type="checkbox" />
            <span>Kerjakan laporan bab 1</span>
          </div>
          <span class="deadline">Deadline: 30 Mei</span>
        </li>
        <li>
          <div class="task-info">
            <input type="checkbox" checked />
            <span>Diskusi materi presentasi</span>
          </div>
          <span class="deadline">Deadline: 28 Mei</span>
        </li>
      </ul>
    </div>

    <?php if ($role === 'ketua'): ?>
    <div class="section">
      <h2>⚙️ Panel Admin (Ketua)</h2>
      <form>
        <div class="form-group">
          <label for="task">Tugas Baru</label>
          <input type="text" id="task" placeholder="Masukkan nama tugas..." />
        </div>
        <div class="form-group">
          <label for="deadline">Deadline</label>
          <input type="date" id="deadline" />
        </div>
        <button type="submit" class="btn">Tambah Tugas</button>
      </form>

      <div class="progress-container">
        <p>📊 Progres Tugas Kelompok</p>
        <div class="progress-bar">
          <div class="progress-fill">0%</div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="section dashboard-section">
      <h2>📈 Dashboard Tugas</h2>
      <div class="dashboard-cards">
        <div class="card">
          <h3 id="total-tugas">0</h3>
          <p>Total Tugas</p>
        </div>
        <div class="card">
          <h3 id="tugas-selesai">0</h3>
          <p>Selesai</p>
        </div>
        <div class="card">
          <h3 id="tugas-belum">0</h3>
          <p>Belum Selesai</p>
        </div>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>