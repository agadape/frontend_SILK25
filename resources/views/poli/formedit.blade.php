<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemeriksaan Pasien</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
        }

        .form-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        textarea,
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .btn {
            background: #007bff;
            color: #fff;
            border: 0;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 14px;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .btn-back {
            background: #555;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background: #333;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        /* AUTOCOMPLETE */
        .autocomplete-wrapper {
            position: relative;
        }

        .autocomplete-input-wrapper {
            position: relative;
        }

        .autocomplete-input-wrapper input {
            padding-right: 40px;
        }

        .dropdown-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 8px;
            color: #666;
            font-size: 16px;
            transition: transform 0.2s, color 0.2s;
        }

        .dropdown-toggle:hover {
            color: #007bff;
        }

        .dropdown-toggle.open {
            transform: translateY(-50%) rotate(180deg);
        }

        ul.autocomplete {
            display: none;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
            list-style: none;
            padding: 0;
            max-height: 250px;
            overflow-y: auto;
            background: white;
            position: absolute;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        ul.autocomplete.show {
            display: block;
        }

        ul.autocomplete li {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        ul.autocomplete li:last-child {
            border-bottom: none;
        }

        ul.autocomplete li:hover {
            background-color: #f0f7ff;
        }

        ul.autocomplete li.selected {
            background-color: #e3f2fd;
            border-left: 3px solid #007bff;
        }

        ul.autocomplete li.no-results {
            color: #999;
            cursor: default;
            text-align: center;
            font-style: italic;
        }

        ul.autocomplete li.no-results:hover {
            background-color: white;
        }

        ul.autocomplete li strong {
            display: block;
            color: #333;
            margin-bottom: 3px;
            font-size: 14px;
        }

        ul.autocomplete li small {
            color: #666;
            font-size: 12px;
        }

        /* Obat terpilih */
        .obat-terpilih-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 8px;
            border: 1px solid #e0e0e0;
        }

        .btn-hapus {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.2s;
        }

        .btn-hapus:hover {
            background: #c82333;
        }

        .info-text {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
            font-style: italic;
        }

        .patient-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .patient-info strong {
            display: inline-block;
            min-width: 120px;
        }
    </style>
</head>

<body>
    <h1>📋 Form Pemeriksaan Pasien</h1>

    <div id="formContainer" class="form-card">
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 40px; margin-bottom: 10px;">⏳</div>
            <p>Memuat data pasien...</p>
        </div>
    </div>

    <div style="text-align:center;margin-top:10px;">
        <a href="javascript:history.back()" class="btn-back">← Kembali</a>
    </div>

    <script>
        // Konfigurasi API
        const API_BASE = 'http://localhost/silk2025_api/public/api';
        const API_URL = `${API_BASE}/poli_dengan_pasien`;
        const API_DOKTER = `${API_BASE}/dokter`;
        const API_OBAT = `${API_BASE}/obat`;
        const API_SIMPAN = `${API_BASE}/simpan_pemeriksaan`;

        const idRM = @json($id_rm); // dari Controller Laravel
        let currentPoliId = null;

        // Array untuk menyimpan obat yang dipilih
        const obatTerpilih = [];

        // ========================================
        // LOAD FORM
        // ========================================
        async function loadForm() {
            const box = document.getElementById('formContainer');
            box.innerHTML = `
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 40px; margin-bottom: 10px;">⏳</div>
                    <p>Memuat data pasien...</p>
                </div>
            `;

            try {
                const res = await fetch(API_URL);
                const data = await res.json();

                let pasien = null, poli = null;
                for (const p of data) {
                    const found = p.pasien_aktif.find(ps => ps.id_rm == idRM);
                    if (found) {
                        pasien = found;
                        poli = p;
                        currentPoliId = p.id_poli;
                        break;
                    }
                }

                if (!pasien) {
                    box.innerHTML = `
                        <div style="text-align: center; padding: 20px;">
                            <div style="font-size: 50px; color: #dc3545;">❌</div>
                            <p class="error">Pasien dengan ID RM ${idRM} tidak ditemukan dalam antrian</p>
                        </div>
                    `;
                    return;
                }

                box.innerHTML = `
                    <div class="patient-info">
                        <div><strong>🏥 Poli:</strong> ${poli.nama_poli}</div>
                        <div><strong>👤 Pasien:</strong> ${pasien.nama_pasien}</div>
                        <div><strong>📋 No RM:</strong> ${pasien.id_rm}</div>
                        <div><strong>🎫 No Antrian:</strong> ${pasien.nomor_antrian}</div>
                    </div>

                    <form id="periksaForm">
                        <input type="hidden" id="id_rm" value="${pasien.id_rm}">
                        <input type="hidden" id="id_antrian" value="${pasien.id_antrian}">
                        <input type="hidden" id="id_poli" value="${poli.id_poli}">

                        <!-- 👨‍⚕️ Dokter -->
                        <div class="form-group">
                            <label for="dokterInput">👨‍⚕️ Dokter Pemeriksa <span style="color:red;">*</span></label>
                            <div class="autocomplete-wrapper">
                                <div class="autocomplete-input-wrapper">
                                    <input type="text" id="dokterInput" placeholder="Klik atau ketik untuk memilih dokter..." autocomplete="off" required>
                                    <button type="button" class="dropdown-toggle" id="dokterToggle">▼</button>
                                </div>
                                <ul id="dokterList" class="autocomplete"></ul>
                                <input type="hidden" id="id_dokter" name="id_dokter">
                            </div>
                            <div class="info-text">Menampilkan dokter di ${poli.nama_poli}</div>
                        </div>

                        <!-- 🩺 Keluhan -->
                        <div class="form-group">
                            <label for="keluhan">🩺 Keluhan <span style="color:red;">*</span></label>
                            <textarea id="keluhan" rows="3" required></textarea>
                        </div>

                        <!-- 🔬 Diagnosa -->
                        <div class="form-group">
                            <label for="diagnosa">🔬 Diagnosa <span style="color:red;">*</span></label>
                            <textarea id="diagnosa" rows="3" required></textarea>
                        </div>

                        <!-- 💉 Tindakan -->
                        <div class="form-group">
                            <label for="tindakan">💉 Tindakan</label>
                            <textarea id="tindakan" rows="3"></textarea>
                        </div>

                        <!-- 🏥 Toggle Rujukan -->
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="buat_rujukan">
                                🏥 Buat Surat Rujukan
                            </label>
                        </div>

                        <!-- 🏥 Detail Rujukan -->
                        <div class="form-group">
                            <label for="jenis_rujukan">🏥 Jenis Rujukan</label>
                            <select id="jenis_rujukan" disabled>
                                <option value="">Tidak Ada Rujukan</option>
                                <option value="Rawat Jalan">Rawat Jalan</option>
                                <option value="IGD">IGD (Gawat Darurat)</option>
                                <option value="Rawat Inap">Rawat Inap</option>
                                <option value="Poli Lain">Poli Lain (Internal)</option>
                                <option value="RS Eksternal">Rumah Sakit Eksternal</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tujuan_rujukan">🎯 Tujuan Rujukan</label>
                            <input type="text" id="tujuan_rujukan" placeholder="Contoh: RSUD Kota X / Poli Mata" disabled>
                        </div>

                        <div class="form-group">
                            <label for="alasan_rujukan">📝 Alasan / Keterangan Rujukan</label>
                            <textarea id="alasan_rujukan" rows="3" disabled></textarea>
                        </div>

                        <!-- 💊 Search Obat -->
                        <div class="form-group">
                            <label for="obatSearch">💊 Cari Obat</label>
                            <input type="text" id="obatSearch" placeholder="Ketik nama obat untuk mencari..." autocomplete="off">
                            <ul id="obatList" class="autocomplete"></ul>
                            <div class="info-text">Ketik minimal 2 karakter untuk mencari obat</div>
                        </div>

                        <!-- 📦 Obat Terpilih -->
                        <div class="form-group">
                            <label>📦 Resep Obat yang Dipilih:</label>
                            <div id="obatTerpilihContainer">
                                <div style="padding: 15px; background: #f8f9fa; border-radius: 4px; text-align: center; color: #666; border: 2px dashed #ddd;">
                                    Belum ada obat dipilih
                                </div>
                            </div>
                        </div>

                        <!-- 🧾 Keterangan Resep -->
                        <div class="form-group">
                            <label for="ketResep">🧾 Keterangan Resep (opsional)</label>
                            <textarea id="ketResep" rows="2" placeholder="Contoh: Obat diminum selama 5 hari..."></textarea>
                        </div>

                        <!-- ✅ Status -->
                        <div class="form-group">
                            <label for="status">✅ Status Pemeriksaan <span style="color:red;">*</span></label>
                            <select id="status" required>
                                <option value="menunggu">Menunggu</option>
                                <option value="sedang diperiksa">Sedang Diperiksa</option>
                                <option value="selesai" selected>Selesai</option>
                            </select>
                        </div>

                        <button type="submit" class="btn">💾 Simpan Pemeriksaan</button>
                    </form>

                    <div id="msg" style="margin-top: 15px;"></div>
                `;

                // After HTML injected:
                setupDokterAutocompleteListbox();
                setupObatAutocomplete();
                setupToggleRujukan();
                document.getElementById('periksaForm').addEventListener('submit', simpanPemeriksaan);

            } catch (e) {
                console.error('❌ Error loading form:', e);
                box.innerHTML = `
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 50px; color: #dc3545;">⚠️</div>
                        <p class="error">Gagal memuat data! Silakan refresh halaman.</p>
                    </div>
                `;
            }
        }

        // Toggle enable/disable field rujukan
        function setupToggleRujukan() {
            const cb = document.getElementById('buat_rujukan');
            const jenis = document.getElementById('jenis_rujukan');
            const tujuan = document.getElementById('tujuan_rujukan');
            const alasan = document.getElementById('alasan_rujukan');

            function apply() {
                const en = cb.checked;
                jenis.disabled = !en;
                tujuan.disabled = !en;
                alasan.disabled = !en;
            }

            cb.addEventListener('change', apply);
            apply(); // init
        }

        // ========================================
        // SETUP DOKTER AUTOCOMPLETE + LISTBOX
        // ========================================
        function setupDokterAutocompleteListbox() {
            const dokterInput = document.getElementById('dokterInput');
            const dokterList = document.getElementById('dokterList');
            const dokterToggle = document.getElementById('dokterToggle');
            const idDokterHidden = document.getElementById('id_dokter');

            let allDokter = [];
            let isOpen = false;
            let debounceTimer;

            async function loadAllDokter() {
                try {
                    const res = await fetch(`${API_DOKTER}?id_poli=${currentPoliId}`);
                    allDokter = await res.json();
                } catch (err) {
                    console.error('❌ Gagal load dokter:', err);
                }
            }

            loadAllDokter();

            dokterToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                if (isOpen) {
                    closeDokterList();
                } else {
                    openDokterList();
                    renderDokterList(allDokter);
                }
            });

            dokterInput.addEventListener('focus', () => {
                openDokterList();
                renderDokterList(allDokter);
            });

            dokterInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                const keyword = dokterInput.value.trim().toLowerCase();

                if (keyword.length > 0) {
                    idDokterHidden.value = '';
                }

                debounceTimer = setTimeout(() => {
                    if (keyword.length === 0) {
                        renderDokterList(allDokter);
                    } else {
                        const filtered = allDokter.filter(d =>
                            d.nama_dokter.toLowerCase().includes(keyword) ||
                            (d.spesialisasi && d.spesialisasi.toLowerCase().includes(keyword))
                        );
                        renderDokterList(filtered);
                    }
                    openDokterList();
                }, 200);
            });

            function renderDokterList(dokterArray) {
                dokterList.innerHTML = '';

                if (dokterArray.length === 0) {
                    const li = document.createElement('li');
                    li.className = 'no-results';
                    li.textContent = '🔍 Tidak ada dokter ditemukan';
                    dokterList.appendChild(li);
                } else {
                    dokterArray.forEach(d => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <strong>👨‍⚕️ ${d.nama_dokter}</strong>
                            <small>📌 ${d.spesialisasi || 'Umum'}</small>
                        `;
                        if (idDokterHidden.value == d.id_dokter) {
                            li.classList.add('selected');
                        }
                        li.addEventListener('click', () => {
                            dokterInput.value = d.nama_dokter;
                            idDokterHidden.value = d.id_dokter;
                            closeDokterList();
                        });
                        dokterList.appendChild(li);
                    });
                }
            }

            function openDokterList() {
                dokterList.classList.add('show');
                dokterToggle.classList.add('open');
                isOpen = true;
            }

            function closeDokterList() {
                dokterList.classList.remove('show');
                dokterToggle.classList.remove('open');
                isOpen = false;
            }

            document.addEventListener('click', (e) => {
                if (!dokterInput.contains(e.target) &&
                    !dokterList.contains(e.target) &&
                    !dokterToggle.contains(e.target)) {
                    closeDokterList();
                }
            });
        }

        // ========================================
        // SETUP OBAT AUTOCOMPLETE
        // ========================================
        function setupObatAutocomplete() {
            const obatInput = document.getElementById('obatSearch');
            const obatList = document.getElementById('obatList');
            let debounceTimer;

            obatInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                const keyword = obatInput.value.trim();

                if (keyword.length < 2) {
                    obatList.style.display = 'none';
                    return;
                }

                debounceTimer = setTimeout(async () => {
                    try {
                        const res = await fetch(`${API_OBAT}?q=${encodeURIComponent(keyword)}`);
                        const data = await res.json();

                        obatList.innerHTML = '';

                        if (data.length === 0) {
                            const li = document.createElement('li');
                            li.className = 'no-results';
                            li.textContent = '🔍 Tidak ada obat ditemukan';
                            obatList.appendChild(li);
                        } else {
                            data.forEach(o => {
                                const li = document.createElement('li');
                                li.innerHTML = `
                                    <strong>💊 ${o.nama_obat}</strong>
                                    <small>📦 ${o.jenis_obat || '-'} | 📊 Stok: ${o.stok} ${o.satuan || ''}</small>
                                `;
                                li.addEventListener('click', () => {
                                    tambahObat(o);
                                    obatInput.value = '';
                                    obatList.style.display = 'none';
                                });
                                obatList.appendChild(li);
                            });
                        }

                        obatList.style.display = 'block';
                    } catch (err) {
                        console.error('❌ Gagal ambil data obat:', err);
                    }
                }, 300);
            });

            document.addEventListener('click', (e) => {
                if (!obatInput.contains(e.target) && !obatList.contains(e.target)) {
                    obatList.style.display = 'none';
                }
            });
        }

        // ========================================
        // OBAT FUNCTIONS
        // ========================================
        function tambahObat(obat) {
            if (obatTerpilih.find(x => x.id_obat === obat.id_obat)) {
                alert('⚠️ Obat sudah ada dalam daftar!');
                return;
            }
            obatTerpilih.push(obat);
            renderObatTerpilih();
        }

        function renderObatTerpilih() {
            const container = document.getElementById('obatTerpilihContainer');

            if (obatTerpilih.length === 0) {
                container.innerHTML = `
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 4px; text-align: center; color: #666; border: 2px dashed #ddd;">
                        Belum ada obat dipilih
                    </div>
                `;
                return;
            }

            container.innerHTML = obatTerpilih.map((o, index) => `
                <div class="obat-terpilih-item">
                    <div style="width: 70%;">
                        <strong>💊 ${o.nama_obat}</strong><br>
                        <small>📦 ${o.jenis_obat || '-'} | 📏 ${o.satuan || '-'} | 📊 Stok: ${o.stok}</small>
                        <div style="margin-top:5px;">
                            Jumlah: <input type="number" min="1" id="jumlah_${index}" value="1" style="width:60px;">
                            <br>
                            Dosis: <input type="text" id="dosis_${index}" placeholder="3x1">
                            <br>
                            Label: <input type="text" id="label_${index}" placeholder="Sesudah makan">
                        </div>
                    </div>
                    <button type="button" class="btn-hapus" onclick="hapusObat(${index})">❌ Hapus</button>
                </div>
            `).join('');
        }

        function hapusObat(index) {
            obatTerpilih.splice(index, 1);
            renderObatTerpilih();
        }

        // ========================================
        // SIMPAN PEMERIKSAAN
        // ========================================
        async function simpanPemeriksaan(e) {
            e.preventDefault();
            const msg = document.getElementById('msg');
            const btn = e.target.querySelector('button[type="submit"]');

            if (!document.getElementById('id_dokter').value) {
                msg.innerHTML = `<p class="error">❌ Silakan pilih dokter terlebih dahulu!</p>`;
                return;
            }

            const buatRujukan = document.getElementById('buat_rujukan').checked;

            const payload = {
                id_rm: document.getElementById('id_rm').value,
                id_antrian: document.getElementById('id_antrian').value,
                id_poli: document.getElementById('id_poli').value,
                id_dokter: document.getElementById('id_dokter').value,

                keluhan: document.getElementById('keluhan').value,
                diagnosa: document.getElementById('diagnosa').value,
                tindakan: document.getElementById('tindakan').value,
                status: document.getElementById('status').value,

                // Rujukan
                buat_rujukan: buatRujukan ? 1 : 0,
                jenis_rujukan: buatRujukan ? (document.getElementById('jenis_rujukan').value || null) : null,
                tujuan_rujukan: buatRujukan ? (document.getElementById('tujuan_rujukan').value || null) : null,
                rujukan: buatRujukan ? (document.getElementById('alasan_rujukan').value || null) : null,

                // Resep
                resep: obatTerpilih.map((o, i) => ({
                    id_obat: o.id_obat,
                    jumlah: document.getElementById(`jumlah_${i}`).value,
                    dosis: document.getElementById(`dosis_${i}`).value,
                    label: document.getElementById(`label_${i}`).value
                })),

                keterangan_resep: document.getElementById('ketResep') ? document.getElementById('ketResep').value : null
            };

            msg.innerHTML = "⏳ Menyimpan data pemeriksaan...";
            btn.disabled = true;
            btn.textContent = '⏳ Menyimpan...';

            try {
                const res = await fetch(API_SIMPAN, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();

                if (res.ok && result.success) {
                    msg.innerHTML = `<p class="success">✅ ${result.message}</p>`;

                    // jika rujukan dibuat, buka PDF
                    if (buatRujukan && result.id_rm_detil) {
                        window.open(`${API_BASE}/cetak_rujukan/${result.id_rm_detil}`, '_blank');
                    }

                    setTimeout(() => {
                        alert('✅ Data berhasil disimpan! Akan kembali ke halaman poli.');
                        window.location.href = "/poli";
                    }, 1500);
                } else {
                    msg.innerHTML = `<p class="error">❌ ${result.message || 'Gagal menyimpan data pemeriksaan'}</p>`;
                    btn.disabled = false;
                    btn.textContent = '💾 Simpan Pemeriksaan';
                }

            } catch (err) {
                console.error('❌ Error:', err);
                msg.innerHTML = `<p class="error">❌ Terjadi kesalahan koneksi ke server.</p>`;
                btn.disabled = false;
                btn.textContent = '💾 Simpan Pemeriksaan';
            }
        }
        function setupToggleRujukan() {
    const cb = document.getElementById('buat_rujukan');
    const jenis = document.getElementById('jenis_rujukan');
    const tujuan = document.getElementById('tujuan_rujukan');
    const alasan = document.getElementById('alasan_rujukan');

    function apply() {
        const aktif = cb.checked;

        jenis.disabled = !aktif;
        tujuan.disabled = !aktif;
        alasan.disabled = !aktif;

        // tambah wajib jika rujukan dibuat
        if (aktif) {
            jenis.setAttribute('required', 'required');
            tujuan.setAttribute('required', 'required');
        } else {
            jenis.removeAttribute('required');
            tujuan.removeAttribute('required');
        }
    }

    cb.addEventListener('change', apply);
    apply(); // initial
}


        window.onload = loadForm;
    </script>
</body>

</html>
