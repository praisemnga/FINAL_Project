function updateDashboard() {
  const tasks = document.querySelectorAll(".task-list li");
  const total = tasks.length;
  let selesai = 0;

  tasks.forEach(task => {
    const checkbox = task.querySelector("input[type='checkbox']");
    if (checkbox.checked) selesai++;
  });

  const belum = total - selesai;

  document.getElementById("total-tugas").textContent = total;
  document.getElementById("tugas-selesai").textContent = selesai;
  document.getElementById("tugas-belum").textContent = belum;

  const progressFill = document.querySelector(".progress-fill");
  const persen = total > 0 ? Math.round((selesai / total) * 100) : 0;
  progressFill.style.width = persen + "%";
  progressFill.textContent = persen + "%";
}

document.querySelector("form")?.addEventListener("submit", function(e) {
  if (userRole !== "ketua") {
    alert("Hanya ketua yang dapat menambah tugas!");
    e.preventDefault();
    return;
  }

  if (!taskName || !deadline) {
    alert("Isi semua kolom!");
    return;
  }

  const taskList = document.querySelector(".task-list");

  const li = document.createElement("li");
  li.innerHTML = `
    <div class="task-info">
      <input type="checkbox" />
      <span>${taskName}</span>
    </div>
    <span class="deadline">Deadline: ${deadline}</span>
  `;

  taskList.appendChild(li);

  document.getElementById("task").value = "";
  document.getElementById("deadline").value = "";

  updateDashboard();
});

document.querySelector(".task-list").addEventListener("change", function(e) {
  if (e.target.type === "checkbox") {
    updateDashboard();
  }
});

window.addEventListener("DOMContentLoaded", updateDashboard);

// Ambil role dari PHP (inject ke JS)
const userRole = document.body.getAttribute('data-role') || 'anggota';