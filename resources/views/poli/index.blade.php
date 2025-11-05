<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Poli dan Pasien Aktif</title>
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
      margin-bottom: 30px;
      color: #222;
    }

    .container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 20px;
    }

    .card {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .card h2 {
      margin-top: 0;
      color: #007bff;
      font-size: 20px;
    }

    .info {
      margin-bottom: 10px;
    }

    .info strong {
      display: inline-block;
      width: 140px;
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
      margin-top: 10px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      font-size: 14px;
    }

    th {
      background-color: #007bff;
      color: white;
      text-align: left;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .loading {
      text-align: center;
      font-style: italic;
      padding: 20px;
      color: #555;
    }

    .btn-masuk {
      display: inline-block;
      margin-top: 15px;
      background-color: #007bff;
      color: white;
      padding: 8px 15px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .btn-masuk:hover {
      background-color: #0056b3;
    }

  </style>
</head>

<body>
  <h1>Pilih Poli</h1>
  <div id="poliContainer" class="container">
    <p class="loading">Memuat data dari server...</p>
  </div>

  <script>
    const API_URL = 'http://localhost/rumah_sakit/public/api/poli_dengan_pasien';

    async function loadPoli() {
      const container = document.getElementById('poliContainer');
      container.innerHTML = `<p class="loading">Memuat data dari server...</p>`;

      try {
        const response = await fetch(API_URL);
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        const data = await response.json();

        if (data.length === 0) {
          container.innerHTML = `<p class="loading">Tidak ada data poli ditemukan.</p>`;
          return;
        }

        container.innerHTML = ""; // kosongkan kontainer

        data.forEach(poli => {
          const card = document.createElement('div');
          card.classList.add('card');

          // Buat isi kartu poli
          card.innerHTML = `
            <h2>${poli.nama_poli}</h2>
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

          // Tabel pasien aktif (jika ada)
          if (poli.pasien_aktif && poli.pasien_aktif.length > 0) {
            const table = document.createElement('table');
            table.innerHTML = `
              <thead>
                <tr>
                  <th>No RM</th>
                  <th>Nama Pasien</th>
                  <th>No Antrian</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                ${poli.pasien_aktif.map(p => `
                  <tr>
                    <td>${p.id_rm}</td>
                    <td>${p.nama_pasien}</td>
                    <td>${p.nomor_antrian}</td>
                    <td>${p.status}</td>
                  </tr>
                `).join('')}
              </tbody>
            `;
            card.appendChild(table);
          } else {
            const noPatient = document.createElement('p');
            noPatient.style.fontStyle = 'italic';
            noPatient.style.color = '#777';
            noPatient.textContent = 'Tidak ada pasien aktif saat ini.';
            card.appendChild(noPatient);
          }

          // Tombol masuk ke halaman detail poli
          const btnMasuk = document.createElement('a');
          btnMasuk.href = `/detailpoli/${encodeURIComponent(poli.nama_poli)}`;
          btnMasuk.classList.add('btn-masuk');
          btnMasuk.textContent = "Masuk";
          card.appendChild(btnMasuk);

          container.appendChild(card);
        });

      } catch (error) {
        console.error('Gagal memuat data:', error);
        container.innerHTML = `<p class="loading" style="color:red;">Gagal memuat data dari server.</p>`;
      }
    }

    window.onload = loadPoli;
  </script>
</body>
</html>
