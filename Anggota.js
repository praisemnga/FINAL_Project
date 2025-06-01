const anggota = [
  {
    nama: "PRAISE MARIA PERMATA MONIAGA",
    nim: "230211060088",
    email: "praisemoniaga026@student.unsrat.ac.id",
    telp: "0821-9131-5664",
  },
  {
    nama: "VANIA ESTHER SALELETANG",
    nim: "230211060012",
    email: "vaniasaleletang026@student.unsrat.ac.id",
    telp: "0813-4214-9692",
  },
  // Tambahkan anggota lain sesuai kelompokmu
];

const anggotaList = document.getElementById('anggotaList');
anggota.forEach(a => {
  const li = document.createElement('li');
  li.innerHTML = `
    <span class="anggota-nama">${a.nama}</span>
    <span class="anggota-nim">NIM: ${a.nim}</span>
    <span class="anggota-email">Email: ${a.email}</span>
    <span class="anggota-telp">Telp: ${a.telp}</span>
  `;
  anggotaList.appendChild(li);
});