<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Modul Poli Rawat Jalan</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  <h1>Data Poli Rawat Jalan</h1>
  <a href="{{ url('/poli/tambah') }}" class="btn">+ Tambah Kunjungan</a>

  <table id="tabelPoli">
    <thead>
      <tr>
        <th>No RM</th>
        <th>Nama Pasien</th>
        <th>Poli</th>
        <th>Dokter</th>
        <th>Keluhan</th>
        <th>Tanggal Kunjungan</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script src="{{ asset('js/poli.js') }}"></script>
</body>
</html>
