<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Pemeriksaan Pasien</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa
        }

        .form-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1)
        }

        h1 {
            text-align: center;
            color: #007bff
        }

        .form-group {
            margin-bottom: 12px
        }

        label {
            font-weight: bold
        }

        textarea,
        input,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px
        }

        .btn {
            background: #007bff;
            color: #fff;
            border: 0;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer
        }

        .btn:hover {
            background: #0056b3
        }

        .btn-back {
            background: #555;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none
        }

        .success {
            color: green;
            font-weight: bold
        }

        .error {
            color: red;
            font-weight: bold
        }

        /* tambahan untuk list autocomplete */
        ul.autocomplete {
            display: none;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
            list-style: none;
            padding: 0;
            max-height: 150px;
            overflow-y: auto;
            background: white;
        }

        ul.autocomplete li {
            padding: 8px;
            cursor: pointer;
        }

        ul.autocomplete li:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <h1>Form Pemeriksaan Pasien</h1>

    <div id="formContainer" class="form-card">Memuat data...</div>

    <div style="text-align:center;margin-top:10px;">
        <a href="javascript:history.back()" class="btn-back">← Kembali</a>
    </div>

    <script>
        const API_URL = 'http://localhost/silk2025_api/public/api/poli_dengan_pasien';
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
      if (found) { pasien = found; poli = p; break; }
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

        <!-- 🔹 Input Dokter -->
        <div class="form-group">
            <label for="dokterInput">Dokter</label>
            <input type="text" id="dokterInput" placeholder="Ketik nama dokter...">
            <ul id="dokterList" class="autocomplete"></ul>
            <input type="hidden" id="id_dokter" name="id_dokter">
        </div>

        <!-- 🔹 Keluhan -->
        <div class="form-group">
          <label>Keluhan</label>
          <textarea id="keluhan" required></textarea>
        </div>

        <!-- 🔹 Diagnosa -->
        <div class="form-group">
          <label>Diagnosa</label>
          <textarea id="diagnosa" required></textarea>
        </div>

        <!-- 🔹 Tindakan -->
        <div class="form-group">
          <label>Tindakan</label>
          <textarea id="tindakan"></textarea>
        </div>

        <!-- 💊 🔹 Search Obat -->
        <div class="form-group">
          <label for="obatSearch">Cari Obat</label>
          <input type="text" id="obatSearch" placeholder="Ketik nama obat..." autocomplete="off">
          <ul id="obatList" class="autocomplete"></ul>
          <div id="selectedObat" style="margin-top:10px;">
            <strong>Obat yang dipilih:</strong>
            <ul id="obatTerpilihList"></ul>
          </div>
        </div>

        <!-- 🔹 Status -->
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

            // Ambil data form
            const payload = {
                id_rm: document.getElementById('id_rm').value,
                id_antrian: document.getElementById('id_antrian').value,
                id_poli: document.getElementById('id_poli').value,
                id_dokter: document.getElementById('id_dokter').value,
                diagnosa: document.getElementById('diagnosa').value,
                tindakan: document.getElementById('tindakan').value,
                status: document.getElementById('status').value,

                // 💊 Kirim juga data resep (obat yang dipilih)
                resep: obatTerpilih.map(o => ({
                    id_obat: o.id_obat,
                    jumlah: 1, // bisa kamu ubah ke input jumlah kalau mau nanti
                    dosis: o.dosis || '',
                    label: o.label || ''
                }))
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
                    msg.innerHTML = `<p class="success">✅ Pemeriksaan & resep berhasil disimpan!</p>`;
                    setTimeout(() => window.location.href = "/poli", 1500);
                } else {
                    msg.innerHTML = `<p class="error">❌ ${result.message || 'Gagal menyimpan data pemeriksaan'}</p>`;
                }

            } catch (err) {
                console.error(err);
                msg.innerHTML = `<p class="error">❌ Terjadi kesalahan koneksi ke server.</p>`;
            }
        }


        window.onload = loadForm;
    </script>

    <!-- 🔹 Script Autocomplete Dokter -->
    <script>
        const API_DOKTER = 'http://localhost/silk2025_api/public/api/dokter';
        const dokterInput = document.getElementById('dokterInput');
        const dokterList = document.getElementById('dokterList');
        const idDokterHidden = document.getElementById('id_dokter');

        dokterInput.addEventListener('input', async () => {
            const keyword = dokterInput.value.trim();
            if (keyword.length < 1) {
                dokterList.style.display = 'none';
                return;
            }

            try {
                const res = await fetch(`${API_DOKTER}?q=${encodeURIComponent(keyword)}`);
                const data = await res.json();

                dokterList.innerHTML = '';
                data.forEach(d => {
                    const li = document.createElement('li');
                    li.textContent = `${d.nama_dokter} (${d.spesialisasi || 'Umum'})`;
                    li.addEventListener('click', () => {
                        dokterInput.value = d.nama_dokter;
                        idDokterHidden.value = d.id_dokter;
                        dokterList.style.display = 'none';
                    });
                    dokterList.appendChild(li);
                });

                dokterList.style.display = data.length ? 'block' : 'none';
            } catch (err) {
                console.error('Gagal ambil data dokter:', err);
            }
        });
    </script>

    <!-- 💊 Script Autocomplete Obat -->
    <script>
        const API_OBAT = 'http://localhost/silk2025_api/public/api/obat';
        const obatInput = document.getElementById('obatSearch');
        const obatList = document.getElementById('obatList');
        const obatTerpilihList = document.getElementById('obatTerpilihList');
        const obatTerpilih = [];

        obatInput.addEventListener('input', async () => {
            const keyword = obatInput.value.trim();
            if (keyword.length < 2) {
                obatList.style.display = 'none';
                return;
            }

            try {
                const res = await fetch(`${API_OBAT}?q=${encodeURIComponent(keyword)}`);
                const data = await res.json();

                obatList.innerHTML = '';
                data.forEach(o => {
                    const li = document.createElement('li');
                    li.textContent = `${o.nama_obat} (${o.jenis_obat || '-'})`;
                    li.addEventListener('click', () => {
                        if (!obatTerpilih.find(x => x.id_obat === o.id_obat)) {
                            obatTerpilih.push(o);
                            renderObatTerpilih();
                        }
                        obatInput.value = '';
                        obatList.style.display = 'none';
                    });
                    obatList.appendChild(li);
                });

                obatList.style.display = data.length ? 'block' : 'none';
            } catch (err) {
                console.error('Gagal ambil data obat:', err);
            }
        });

        function renderObatTerpilih() {
            obatTerpilihList.innerHTML = '';
            obatTerpilih.forEach((o, index) => {
                const li = document.createElement('li');
                li.textContent = `${o.nama_obat} (${o.satuan || '-'})`;
                const hapusBtn = document.createElement('button');
                hapusBtn.textContent = '❌';
                hapusBtn.style.marginLeft = '10px';
                hapusBtn.style.cursor = 'pointer';
                hapusBtn.addEventListener('click', () => {
                    obatTerpilih.splice(index, 1);
                    renderObatTerpilih();
                });
                li.appendChild(hapusBtn);
                obatTerpilihList.appendChild(li);
            });
        }
    </script>

</body>

</html>
