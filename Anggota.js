const anggota = [
  {
    nama: "PRAISE MARIA PERMATA MONIAGA",
    nim: "230211060088",
    email: "praisemoniaga026@student.unsrat.ac.id",
    telp: "0821-9131-5664",
    foto: "Image/foto praise.jpg" 
  },
  {
    nama: "VANIA ESTHER SALELETANG",
    nim: "230211060012",
    email: "vaniasaleletang026@student.unsrat.ac.id",
    telp: "0813-4214-9692",
    foto: "Image/foto vania.jpg" 
  },
  // Tambahkan anggota lain sesuai kelompokmu
];

const anggotaList = document.getElementById('anggotaList');
anggota.forEach(a => {
  const li = document.createElement('li');
  li.innerHTML = `
    <img class="anggota-foto" src="${a.foto}" alt="Foto ${a.nama}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(a.nama)}&background=4f46e5&color=fff'">
    <span class="anggota-nama">${a.nama}</span>
    <span class="anggota-nim">NIM: ${a.nim}</span>
    <span class="anggota-email">Email: ${a.email}</span>
    <span class="anggota-telp">Telp: ${a.telp}</span>
  `;
  anggotaList.appendChild(li);
});