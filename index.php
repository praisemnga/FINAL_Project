<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];

// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "tugas_project_akhir");
if ($mysqli->connect_errno) {
    die("Gagal koneksi MySQL: " . $mysqli->connect_error);
}

// Proses broadcast pesan
if ($role === 'ketua' && isset($_POST['kirim_broadcast'])) {
    $pesan = trim($_POST['broadcast'] ?? '');
    if ($pesan) {
        $stmt = $mysqli->prepare("INSERT INTO broadcast (message) VALUES (?)");
        $stmt->bind_param("s", $pesan);
        $stmt->execute();
        $stmt->close();
    }
}
// Ambil pesan broadcast terbaru
$pesan_broadcast = '';
$waktu_broadcast = '';
$res = $mysqli->query("SELECT message, created_at FROM broadcast ORDER BY id DESC LIMIT 1");
if ($row = $res->fetch_assoc()) {
    $pesan_broadcast = $row['message'];
    $waktu_broadcast = date('d M Y H:i', strtotime($row['created_at']));
}
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
  <?php if ($pesan_broadcast): ?>
    <div style="background:#e3e0ff;color:#2d217c;padding:1rem 1.5rem;border-radius:8px;margin-bottom:1.5rem;box-shadow:0 2px 8px #0001;">
      <strong>📢 Broadcast:</strong> <?= htmlspecialchars($pesan_broadcast) ?>
      <span style="float:right;font-size:0.95em;color:#6b6b6b;"><?= $waktu_broadcast ?></span>
    </div>
  <?php endif; ?>

  <div class="section">
      <h2>📋 Tugas Anggota</h2>
      <ul class="task-list">
        <?php
          $tasks = [
            ['name' => 'Kerjakan laporan bab 1', 'deadline' => '30 Mei'],
            ['name' => 'Diskusi materi presentasi', 'deadline' => '28 Mei']
          ];
          // Ambil lampiran dari database
          $attachments = [];
          $res = $mysqli->query("SELECT * FROM attachments");
          while ($row = $res->fetch_assoc()) {
            $attachments[$row['task_name']][] = $row;
          }
          foreach ($tasks as $task) :
            $taskName = $task['name'];
        ?>
        <li>
          <div class="task-info">
            <input type="checkbox" />
            <span><?= htmlspecialchars($taskName) ?></span>
          </div>
          <span class="deadline">Deadline: <?= htmlspecialchars($task['deadline']) ?></span>
          <!-- Daftar lampiran -->
          <?php if (!empty($attachments[$taskName])): ?>
            <div style="margin-top:8px;">
              <strong>Lampiran:</strong>
              <ul style="margin:0;padding-left:18px;">
                <?php foreach ($attachments[$taskName] as $att): ?>
                  <li>
                    <a href="uploads/<?= htmlspecialchars($att['filename']) ?>" target="_blank"><?= htmlspecialchars($att['filename']) ?></a>
                    <span style="color:#888;font-size:0.9em;">(<?= htmlspecialchars($att['uploaded_by']) ?>)</span>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
          
          <!-- Form upload lampiran -->
          <form action="Uploads/upload_file.php" method="post" enctype="multipart/form-data" style="margin-top:8px;">            <input type="hidden" name="task" value="<?= htmlspecialchars($taskName) ?>">
            <input type="file" name="lampiran" required>
            <button type="submit" class="btn" style="padding:2px 10px;font-size:0.95em;">Upload</button>
          </form>
        </li>
        <?php endforeach; ?>
      </ul>


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

      <form method="post" style="margin-top:1.5rem;">
        <div class="form-group">
          <label for="broadcast">Broadcast Pesan ke Semua Anggota</label>
          <textarea name="broadcast" id="broadcast" rows="2" placeholder="Tulis pesan..." required></textarea>
        </div>
        <button type="submit" name="kirim_broadcast" class="btn">Kirim Broadcast</button>
      </form>`
    <?php endif; ?>

    <div class="progress-container">
            <p>📊 Progres Tugas Kelompok</p>
            <div class="progress-bar">
              <div class="progress-fill">0%</div>
            </div>
          </div>

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

  <script src="script.js"></script>
</body>
</html>