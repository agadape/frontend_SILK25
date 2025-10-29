document.addEventListener("DOMContentLoaded", async () => {
  const tbody = document.querySelector("#tabelPoli tbody");

  try {
    const res = await fetch("http://localhost/slim/api/poli_rawat_jalan");
    const data = await res.json();

    data.forEach(item => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${item.no_rm}</td>
        <td>${item.nama_pasien}</td>
        <td>${item.poli}</td>
        <td>${item.dokter}</td>
        <td>${item.keluhan}</td>
        <td>${item.tanggal_kunjungan}</td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error("Gagal memuat data:", err);
  }
});
