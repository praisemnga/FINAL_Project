function updateDashboard() {
  // Data dari PHP yang sudah diinject
  const total = typeof totalTugas !== "undefined" ? totalTugas : 0;
  const selesai = typeof tugasSelesai !== "undefined" ? tugasSelesai : 0;
  const belum = total - selesai;

  // Update dashboard
  document.getElementById("total-tugas").textContent = total;
  document.getElementById("tugas-selesai").textContent = selesai;
  document.getElementById("tugas-belum").textContent = belum;

  // Update progress bar
  const progressFill = document.querySelector(".progress-fill");
  const persen = total > 0 ? Math.round((selesai / total) * 100) : 0;
  progressFill.style.width = persen + "%";
  progressFill.textContent = persen + "%";
}

window.addEventListener("DOMContentLoaded", updateDashboard);