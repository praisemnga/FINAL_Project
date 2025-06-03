<?php
session_start();

// Koneksi ke database
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$group_id = $_SESSION['group_id'] ?? null;

// Proses broadcast pesan
if ($role === 'ketua' && isset($_POST['kirim_broadcast'])) {
    $pesan = trim($_POST['broadcast'] ?? '');
    if ($pesan && $group_id) {
        $stmt = $mysqli->prepare("INSERT INTO broadcast (message, group_id) VALUES (?, ?)");
        $stmt->bind_param("si", $pesan, $group_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Ambil pesan broadcast terbaru
$pesan_broadcast = '';
$waktu_broadcast = '';
if ($group_id) {
    $stmt = $mysqli->prepare("SELECT message, created_at FROM broadcast WHERE group_id=? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $stmt->bind_result($pesan_broadcast, $created_at);
    if ($stmt->fetch()) {
        $waktu_broadcast = date('d M Y H:i', strtotime($created_at));
    }
    $stmt->close();
}

$group_name = '';
if ($group_id) {
    $stmt = $mysqli->prepare("SELECT group_name FROM groups WHERE group_id=?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $stmt->bind_result($group_name);
    $stmt->fetch();
    $stmt->close();
}

// Ambil data anggota kelompok
$anggota_kelompok = [];
if ($group_id) {
    $stmt = $mysqli->prepare("SELECT username, role FROM users WHERE group_id=? ORDER BY role DESC, username ASC");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $anggota_kelompok[] = $row;
    }
    $stmt->close();
}

// Proses Upload Dokumen
if ($role === 'ketua' && isset($_POST['simpan_docs'])) {
    $taskDocs = $_POST['task_docs'] ?? '';
    $docsLink = trim($_POST['docs_link'] ?? '');
    if ($taskDocs && $docsLink) {
        $stmt = $mysqli->prepare("SELECT id FROM docs_links WHERE task_name=?");
        $stmt->bind_param("s", $taskDocs);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            $stmt = $mysqli->prepare("UPDATE docs_links SET link=? WHERE task_name=?");
            $stmt->bind_param("ss", $docsLink, $taskDocs);
            $stmt->execute();
        } else {
            $stmt->close();
            $stmt = $mysqli->prepare("INSERT INTO docs_links (task_name, link) VALUES (?, ?)");
            $stmt->bind_param("ss", $taskDocs, $docsLink);
            $stmt->execute();
        }
        $stmt->close();
        header("Location: index.php");
        exit;
    }
}

// Tambah tugas
if ($role === 'ketua' && isset($_POST['tambah_tugas'])) {
    $taskName = trim($_POST['task'] ?? '');
    $deadline = $_POST['deadline'] ?? null;
    $group_id = $_SESSION['group_id'];
    if ($taskName && $deadline) {
        $stmt = $mysqli->prepare("INSERT INTO tasks (name, deadline, group_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $taskName, $deadline, $group_id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php"); 
        exit;
    }
}

// Proses hapus tugas
if ($role === 'ketua' && isset($_POST['hapus_tugas'])) {
    $hapusTask = $_POST['hapus_tugas'];
    $stmt = $mysqli->prepare("DELETE FROM tasks WHERE name=?");
    $stmt->bind_param("s", $hapusTask);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Proses edit tugas (tampilkan form edit)
$editTaskData = null;
if ($role === 'ketua' && isset($_POST['edit_tugas'])) {
    $editName = $_POST['edit_tugas'];
    $stmt = $mysqli->prepare("SELECT name, deadline FROM tasks WHERE name=?");
    $stmt->bind_param("s", $editName);
    $stmt->execute();
    $stmt->bind_result($ename, $edeadline);
    if ($stmt->fetch()) {
        $editTaskData = ['name' => $ename, 'deadline' => $edeadline];
    }
    $stmt->close();
}

// Proses update tugas
if ($role === 'ketua' && isset($_POST['update_tugas'])) {
    $lama = $_POST['update_tugas_lama'];
    $baru = $_POST['update_tugas_baru'];
    $deadline = $_POST['update_deadline'];
    $stmt = $mysqli->prepare("UPDATE tasks SET name=?, deadline=? WHERE name=?");
    $stmt->bind_param("sss", $baru, $deadline, $lama);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Anggota menandai tugas sudah dikerjakan
if ($role === 'anggota' && isset($_POST['anggota_selesai'])) {
    $anggotaTask = $_POST['anggota_selesai'];
    $stmt = $mysqli->prepare("UPDATE tasks SET anggota_done=1 WHERE name=?");
    $stmt->bind_param("s", $anggotaTask);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Ketua menyelesaikan tugas
if (isset($_POST['selesai_tugas'])) {
    $selesaiTask = $_POST['selesai_tugas'];
    $stmt = $mysqli->prepare("UPDATE tasks SET is_done=1 WHERE name=?");
    $stmt->bind_param("s", $selesaiTask);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Ambil data tugas
$group_id = $_SESSION['group_id'] ?? null;
$tasks = [];
if ($group_id) {
    $stmt = $mysqli->prepare("SELECT name, deadline, is_done, anggota_done FROM tasks WHERE is_done=0 AND group_id=?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $tasks[] = $row;
    }
    $stmt->close();
}

$docs_links = [];
$res = $mysqli->query("SELECT task_name, link FROM docs_links");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $docs_links[$row['task_name']] = $row['link'];
    }
}

// Filter lampiran berdasarkan group_id
$attachments = [];
if ($group_id) {
    $stmt = $mysqli->prepare(
        "SELECT a.* FROM attachments a
         JOIN tasks t ON a.task_name = t.name
         WHERE t.group_id = ? AND a.group_id = ?"
    );
    $stmt->bind_param("ii", $group_id, $group_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $attachments[$row['task_name']][] = $row;
    }
    $stmt->close();
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
    <a href="anggota.html" style="float:right;color:#fff;text-decoration:none;margin-top:-2rem;margin-right:180px;">Tentang Website</a>
    <a href="profile.php" style="float:right;color:#fff;text-decoration:none;margin-top:-2rem;margin-right:90px;">Profil</a>
    <a href="logout.php" style="float:right;color:#fff;text-decoration:none;margin-top:-2rem;">Logout</a>
  </header>

  <div class="container">
  <?php if ($pesan_broadcast): ?>
  <div class="broadcast-box">
    <strong>📢 Broadcast:</strong> <?= htmlspecialchars($pesan_broadcast) ?>
    <span class="broadcast-time"><?= $waktu_broadcast ?></span>
  </div>
  <?php endif; ?>

  <div class="section">
      <h2>📋 Tugas Anggota</h2>
      <h3 style="color:#4f46e5;margin-top:-10px;">Kelompok: <?= htmlspecialchars($group_name) ?></h3>

        <?php if (!empty($anggota_kelompok)): ?>
          <div style="margin-bottom:1rem;">
            <strong>Anggota Kelompok:</strong>
            <ul style="margin:0.5rem 0 0 1.2rem;padding:0;">
              <?php foreach ($anggota_kelompok as $anggota): ?>
                <li>
                  <?= htmlspecialchars($anggota['username']) ?>
                  <?= $anggota['role'] === 'ketua' ? '<span style="color:#4f46e5;font-weight:bold;"> (Ketua)</span>' : '' ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($editTaskData): ?>
          <form method="post" class="section" style="background:#fffbe7;border:1px solid #ffe082;padding:1rem 1.5rem;border-radius:8px;margin-bottom:1.5rem;">
            <h3>Edit Tugas</h3>
            <input type="hidden" name="update_tugas_lama" value="<?= htmlspecialchars($editTaskData['name']) ?>">
            <div class="form-group">
              <label>Nama Tugas</label>
              <input type="text" name="update_tugas_baru" value="<?= htmlspecialchars($editTaskData['name']) ?>" required>
            </div>
            <div class="form-group">
              <label>Deadline</label>
              <input type="date" name="update_deadline" value="<?= htmlspecialchars($editTaskData['deadline']) ?>" required>
            </div>
            <button type="submit" name="update_tugas" class="btn" style="background:#ffb300;">Simpan Perubahan</button>
            <a href="index.php" class="btn" style="background:#e0e0e0;color:#222;">Batal</a>
          </form>
        <?php endif; ?>

      <ul class="task-list">
        <?php foreach ($tasks as $task): 
        $taskName = $task['name'];
        $isDone = isset($task['is_done']) ? $task['is_done'] : 0;
        $anggotaDone = isset($task['anggota_done']) ? $task['anggota_done'] : 0;
      ?>
        <li>
          <div class="task-row">
            <div class="task-info">
              <span class="task-title<?= $isDone ? ' selesai' : '' ?>">
                <?= htmlspecialchars($taskName) ?><?= $isDone ? ' (Selesai)' : '' ?>
              </span>
            </div>
            <span class="deadline"><?= htmlspecialchars($task['deadline']) ? "Deadline: " . htmlspecialchars($task['deadline']) : "" ?></span>
            
            <!-- Status tugas oleh anggota -->
            <?php if (!$isDone): ?>
              <?php if ($role === 'anggota' && !$anggotaDone): ?>
                <form method="post">
                  <input type="hidden" name="anggota_selesai" value="<?= htmlspecialchars($taskName) ?>">
                  <button type="submit" class="btn" style="background:#0288d1;">Sudah Dikerjakan</button>
                </form>
              <?php elseif ($role === 'anggota' && $anggotaDone): ?>
                <span style="color:#0288d1;font-weight:bold;">Sudah Dikerjakan</span>
              <?php endif; ?>

              <!-- Tampilkan status ke ketua -->
              <?php if ($role === 'ketua'): ?>
                <?php if ($anggotaDone): ?>
                  <span style="color:#0288d1;font-weight:bold;">✔ Sudah Dikerjakan oleh Anggota</span>
                <?php else: ?>
                  <span style="color:#d32f2f;font-weight:bold;">✖ Belum Dikerjakan oleh Anggota</span>
                <?php endif; ?>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="selesai_tugas" value="<?= htmlspecialchars($taskName) ?>">
                  <button type="submit" class="btn" style="background:#43a047;" <?= !$anggotaDone ? 'disabled style="background:#bdbdbd;cursor:not-allowed;"' : '' ?>>Selesaikan</button>
                </form>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <?php if ($role === 'ketua'): ?>
            <form method="post" style="display:inline;">
              <input type="hidden" name="edit_tugas" value="<?= htmlspecialchars($taskName) ?>">
              <button type="submit" class="btn" style="background:#ffb300;">Edit</button>
            </form>
            <form method="post" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus tugas ini?');">
              <input type="hidden" name="hapus_tugas" value="<?= htmlspecialchars($taskName) ?>">
              <button type="submit" class="btn" style="background:#e53935;">Hapus</button>
            </form>
          <?php endif; ?>

          <!-- Upload instruksi (khusus ketua) -->
          <?php if ($role === 'ketua'): ?>
            <form action="Uploads/upload_file.php" method="post" enctype="multipart/form-data" class="upload-form">
              <input type="hidden" name="task" value="<?= htmlspecialchars($taskName) ?>">
              <input type="hidden" name="type" value="instruksi">
              <input type="file" name="lampiran" required>
              <button type="submit" class="btn" style="background:#4f46e5;">Upload Instruksi</button>
            </form>
          <?php endif; ?>

          <!-- Upload hasil tugas (anggota & ketua) -->
          <form action="Uploads/upload_file.php" method="post" enctype="multipart/form-data" class="upload-form">
            <input type="hidden" name="task" value="<?= htmlspecialchars($taskName) ?>">
            <input type="hidden" name="type" value="hasil">
            <input type="file" name="lampiran" required>
            <button type="submit" class="btn" style="background:#388e3c;">Upload Hasil</button>
          </form>

          <!-- Form input link Google Docs (khusus ketua) -->
          <?php if ($role === 'ketua'): ?>
            <form method="post" class="docs-link-form" style="margin-top:8px;">
              <input type="hidden" name="task_docs" value="<?= htmlspecialchars($taskName) ?>">
              <input type="url" name="docs_link" class="docs-link-input" placeholder="Paste link Google Docs di sini" required>
              <button type="submit" name="simpan_docs" class="btn docs-link-save">Simpan Link Docs</button>
            </form>
          <?php endif; ?>

          <!-- Tampikan Link Google Docs -->
          <?php if (!empty($docs_links[$taskName])): ?>
            <div class="docs-link-box">
              <span class="docs-link-label">Google Docs Kolaborasi:</span>
              <a class="docs-link-btn" href="<?= htmlspecialchars($docs_links[$taskName]) ?>" target="_blank">
                <img src="https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_document_x32.png" alt="Google Docs" class="docs-link-icon">
                Buka Google Docs
              </a>
            </div>
          <?php endif; ?>

          <!-- Daftar lampiran instruksi -->
          <?php if (!empty($attachments[$taskName])): ?>
            <div class="attachments">
              <strong>Instruksi:</strong>
              <ul class="attachment-list">
                <?php foreach ($attachments[$taskName] as $att): ?>
                  <?php if ($att['type'] === 'instruksi'): ?>
                    <li>
                      <a href="uploads/<?= htmlspecialchars($att['filename']) ?>" target="_blank"><?= htmlspecialchars($att['filename']) ?></a>
                      <span class="uploaded-by">(oleh <?= htmlspecialchars($att['uploaded_by']) ?>)</span>
                    </li>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <!-- Daftar lampiran hasil -->
          <?php if (!empty($attachments[$taskName])): ?>
            <div class="attachments">
              <strong>Hasil Tugas:</strong>
              <ul class="attachment-list">
                <?php foreach ($attachments[$taskName] as $att): ?>
                  <?php if ($att['type'] === 'hasil'): ?>
                    <li>
                      <a href="uploads/<?= htmlspecialchars($att['filename']) ?>" target="_blank"><?= htmlspecialchars($att['filename']) ?></a>
                      <span class="uploaded-by">(oleh <?= htmlspecialchars($att['uploaded_by']) ?>)</span>
                    </li>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
      </ul>          

    <?php if ($role === 'ketua'): ?>
    <div class="section">
      <h2>⚙️ Panel Admin (Ketua)</h2>
      <?php if (!empty($error_tugas)): ?>
        <div class="error" style="color:#d32f2f;margin-bottom:1rem;"><?= $error_tugas ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="form-group">
          <label for="task">Tugas Baru</label>
          <input type="text" id="task" name="task" placeholder="Masukkan nama tugas..." required />
        </div>
        <div class="form-group">
          <label for="deadline">Deadline</label>
          <input type="date" id="deadline" name="deadline" required />
        </div>
        <button type="submit" name="tambah_tugas" class="btn">Tambah Tugas</button>
      </form>

      <form method="post">
        <div class="form-group">
          <label for="broadcast">Broadcast Pesan ke Semua Anggota</label>
          <textarea name="broadcast" id="broadcast" rows="2" placeholder="Tulis pesan..." required></textarea>
        </div>
        <button type="submit" name="kirim_broadcast" class="btn">Kirim Broadcast</button>
      </form>
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
    <script>
      const totalTugas = <?= count($tasks) ?>;
      const tugasSelesai = <?= count(array_filter($tasks, fn($t) => !empty($t['is_done']))); ?>;
    </script>
    <script src="script.js"></script>
  </body>
</html>