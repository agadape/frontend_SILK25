<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Poli {{ $namapoli }}</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      background-color: #f8f9fa;
      color: #333;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #007bff;
    }

    .card {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      padding: 25px;
      margin: 0 auto;
      max-width: 800px;
    }

    .info {
      margin-bottom: 8px;
    }

    .info strong {
      width: 150px;
      display: inline-block;
      color: #555;
    }

    .badge {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      color: white;
    }

    .badge-success {
      background-color: #28a745;
    }

    .badge-danger {
      background-color: #dc3545;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      font-size: 14px;
      text-align: center;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .btn {
      display: inline-block;
      background-color: #007bff;
      color: white;
      padding: 7px 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 13px;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    .btn-back {
      display: inline-block;
      margin-top: 20px;
      background-color: #6c757d;
    }

    .btn-back:hover {
      background-color: #5a6268;
    }

    .loading {
      text-align: center;
      font-style: italic;
      color: #666;
    }

  </style>
</head>

<body>
  <h1>Detail Poli: {{ $namapoli }}</h1>

  <div id="detailContainer" class="card">
    <p class="loading">Memuat detail poli...</p>
  </div>

  <div style="text-align:center;">
    <a href="/poli" class="btn btn-back">← Kembali ke Daftar Poli</a>
  </div>

  <script>
    const API_URL = 'http://localhost/rumah_sakit/public/api/poli_dengan_pasien';
    const namaPoli = @json($namapoli);

    async function loadDetailPoli() {
      const container = document.getElementById('detailContainer');
      container.innerHTML = `<p class="loading">Memuat detail poli...</p>`;

      try {
        const response = await fetch(API_URL);
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        const data = await response.json();

        // Cari poli berdasarkan nama
        const poli = data.find(p => p.nama_poli.toLowerCase() === namaPoli.toLowerCase());

        if (!poli) {
          container.innerHTML = `<p class="loading" style="color:red;">Poli "${namaPoli}" tidak ditemukan.</p>`;
          return;
        }

        // Tampilkan detail poli
        container.innerHTML = `
          <div class="info"><strong>Nama Poli:</strong> ${poli.nama_poli}</div>
          <div class="info"><strong>Lokasi:</strong> ${poli.lokasi}</div>
          <div class="info"><strong>No. Telepon:</strong> ${poli.no_Telp}</div>
          <div class="info"><strong>Keterangan:</strong> ${poli.keterangan}</div>
          <div class="info">
            <strong>Jumlah Pasien Aktif:</strong>
            <span class="badge ${poli.jumlah_pasien_aktif > 0 ? 'badge-success' : 'badge-danger'}">
              ${poli.jumlah_pasien_aktif}
            </span>
          </div>
        `;

        // Tabel pasien aktif
        if (poli.pasien_aktif && poli.pasien_aktif.length > 0) {
          const table = document.createElement('table');
          table.innerHTML = `
            <thead>
              <tr>
                <th>No RM</th>
                <th>Nama Pasien</th>
                <th>No Antrian</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              ${poli.pasien_aktif.map(p => `
                <tr>
                  <td>${p.id_rm}</td>
                  <td>${p.nama_pasien}</td>
                  <td>${p.nomor_antrian}</td>
                  <td>${p.status}</td>
                  <td>
                    <a href="/formedit/${p.id_rm}" class="btn">Periksa</a>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          `;
          container.appendChild(table);
        } else {
          const noPatient = document.createElement('p');
          noPatient.style.fontStyle = 'italic';
          noPatient.style.color = '#777';
          noPatient.textContent = 'Belum ada pasien aktif di poli ini.';
          container.appendChild(noPatient);
        }

      } catch (error) {
        console.error('Gagal memuat detail poli:', error);
        container.innerHTML = `<p class="loading" style="color:red;">Gagal memuat data dari server.</p>`;
      }
    }

    window.onload = loadDetailPoli;
  </script>
</body>
</html>
