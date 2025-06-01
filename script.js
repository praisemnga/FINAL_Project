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

// Data lengkap anggota kelompok pengembang website
const anggota = [
  {
    nama: "Esther T",
    nim: "12345678",
    role: "Ketua",
    email: "esther.t@example.com",
    telp: "0812-3456-7890",
    kontribusi: "Desain UI, Backend PHP, Database"
  },
  {
    nama: "Vania S",
    nim: "23456789",
    role: "Anggota",
    email: "vania.s@example.com",
    telp: "0821-2345-6789",
    kontribusi: "Frontend, Dokumentasi, Testing"
  },
  {
    nama: "Nama Lain",
    nim: "34567890",
    role: "Anggota",
    email: "nama.lain@example.com",
    telp: "0856-1234-5678",
    kontribusi: "Fitur Upload, Halaman Statis, Presentasi"
  }
  // Tambahkan anggota lain sesuai kelompokmu
];

// Render anggota ke dalam list
const anggotaList = document.getElementById('anggotaList');
anggota.forEach(a => {
  const li = document.createElement('li');
  li.innerHTML = `
    <span class="anggota-nama">${a.nama}</span>
    <span class="anggota-nim">NIM: ${a.nim}</span>
    <span class="anggota-role">${a.role}</span>
    <span class="anggota-email">Email: ${a.email}</span>
    <span class="anggota-telp">Telp: ${a.telp}</span>
    <span class="anggota-kontribusi">Kontribusi: ${a.kontribusi}</span>
  `;
  anggotaList.appendChild(li);
});