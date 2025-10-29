<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Kunjungan Poli</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  <h1>Tambah Kunjungan Poli Rawat Jalan</h1>

  <form id="formKunjungan">
    <label>No RM:</label>
    <input type="text" name="no_rm" required>

    <label>Nama Pasien:</label>
    <input type="text" name="nama_pasien" required>

    <label>Poli:</label>
    <input type="text" name="poli" required>

    <label>Dokter:</label>
    <input type="text" name="dokter" required>

    <label>Keluhan:</label>
    <textarea name="keluhan" required></textarea>

    <label>Tanggal Kunjungan:</label>
    <input type="date" name="tanggal_kunjungan" required>

    <button type="submit">Simpan</button>
  </form>

  <a href="{{ url('/poli') }}" class="btn">Kembali</a>

  <script>
    document.getElementById('formKunjungan').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData.entries());

      const response = await fetch('http://localhost/slim/api/poli_rawat_jalan', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      if (response.ok) {
        alert('Data berhasil ditambahkan!');
        window.location.href = '/poli';
      } else {
        alert('Gagal menambahkan data.');
      }
    });
  </script>
</body>
</html>
