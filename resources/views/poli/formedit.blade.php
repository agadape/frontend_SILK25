<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Pemeriksaan Pasien</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body {font-family: Arial, sans-serif;margin: 40px;background-color: #f8f9fa}
    .form-card {background:#fff;padding:25px;border-radius:12px;max-width:600px;margin:auto;box-shadow:0 2px 10px rgba(0,0,0,.1)}
    h1{text-align:center;color:#007bff}
    .form-group{margin-bottom:12px}
    label{font-weight:bold}
    textarea,input,select{width:100%;padding:8px;border:1px solid #ccc;border-radius:6px}
    .btn{background:#007bff;color:#fff;border:0;padding:10px 15px;border-radius:6px;cursor:pointer}
    .btn:hover{background:#0056b3}
    .btn-back{background:#555;color:white;padding:8px 15px;border-radius:6px;text-decoration:none}
    .success{color:green;font-weight:bold}
    .error{color:red;font-weight:bold}
  </style>
</head>

<body>
<h1>Form Pemeriksaan Pasien</h1>

<div id="formContainer" class="form-card">Memuat data...</div>

<div style="text-align:center;margin-top:10px;">
  <a href="javascript:history.back()" class="btn-back">← Kembali</a>
</div>

<script>
const API_URL = 'http://localhost/rumah_sakit/public/api/poli_dengan_pasien';
const idRM = @json($id_rm);

async function loadForm() {
  const box = document.getElementById('formContainer');
  box.innerHTML = "Memuat data...";

  try {
    const res = await fetch(API_URL);
    const data = await res.json();

    let pasien = null, poli = null;
    for (const p of data) {
      const found = p.pasien_aktif.find(ps => ps.id_rm == idRM);
      if (found) { pasien = found; poli = p;
console.log(pasien);
        break; }

    }

    if (!pasien) return box.innerHTML = `<p class="error">Pasien tidak ditemukan</p>`;

    box.innerHTML = `
      <strong>Poli:</strong> ${poli.nama_poli}<br>
      <strong>Pasien:</strong> ${pasien.nama_pasien}<br>
      <strong>No RM:</strong> ${pasien.id_rm}<br>
      <hr>

      <form id="periksaForm">
        <input type="hidden" id="id_rm" value="${pasien.id_rm}">
        <input type="hidden" id="id_antrian" value="${pasien.id_antrian}">
        <input type="hidden" id="id_poli" value="${poli.id_poli}">

        <div class="form-group">
          <label>Keluhan</label>
          <textarea id="keluhan" required></textarea>
        </div>

        <div class="form-group">
          <label>Diagnosa</label>
          <textarea id="diagnosa" required></textarea>
        </div>

        <div class="form-group">
          <label>Tindakan</label>
          <textarea id="tindakan"></textarea>
        </div>

        <div class="form-group">
          <label>Status</label>
          <select id="status" required>
            <option value="menunggu">Menunggu</option>
            <option value="sedang diperiksa">Diperiksa</option>
            <option value="selesai">Selesai</option>
          </select>
        </div>

        <button class="btn">Simpan</button>
      </form>

      <div id="msg"></div>
    `;

    document.getElementById('periksaForm').addEventListener('submit', simpanPemeriksaan);

  } catch (e) {
    box.innerHTML = `<p class="error">Gagal memuat data!</p>`;
  }
}

async function simpanPemeriksaan(e) {
  e.preventDefault();
  const msg = document.getElementById('msg');

  const payload = {
    id_rm: document.getElementById('id_rm').value,
    id_antrian: document.getElementById('id_antrian').value,
    id_poli: document.getElementById('id_poli').value,
    diagnosa: document.getElementById('diagnosa').value,
    tindakan: document.getElementById('tindakan').value,
    status: document.getElementById('status').value
  };

  msg.innerHTML = "Menyimpan...";

  try {
    const res = await fetch("http://localhost/rumah_sakit/public/api/simpan_pemeriksaan", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    const result = await res.json();

    if (res.ok) {
      msg.innerHTML = `<p class="success">✅ Berhasil disimpan!</p>`;
      setTimeout(() => window.location.href = "/poli", 1500);
    } else {
      msg.innerHTML = `<p class="error">❌ ${result.message || 'Gagal menyimpan'}</p>`;
    }

  } catch {
    msg.innerHTML = `<p class="error">❌ Error koneksi server</p>`;
  }
}

window.onload = loadForm;
</script>
</body>
</html>
