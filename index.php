<?php
include 'config/database.php';

$slider = mysqli_query($conn,"SELECT * FROM slider ORDER BY id DESC");
$raket  = mysqli_query($conn,"SELECT * FROM raket");
$berita = mysqli_query($conn,"SELECT * FROM berita ORDER BY id DESC");

$jadwal = mysqli_query($conn,"
  SELECT 
    booking.tanggal,
    booking.hari,
    booking.jam_mulai,
    booking.jam_selesai,
    raket.nama_raket
  FROM booking
  JOIN raket ON booking.raket_id = raket.id
  ORDER BY booking.tanggal, booking.jam_mulai
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Sewa Raket Padel</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">PadelRent</a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#raket">Raket</a></li>
        <li class="nav-item"><a class="nav-link" href="#jadwal">Jadwal Booking</a></li>
        <li class="nav-item"><a class="nav-link" href="#berita">Berita</a></li>
        <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
        <li class="nav-item">
          <a class="btn btn-success ms-2" href="https://wa.me/628123456789" target="_blank">
            Pesan via WA
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- ================= HOME SLIDER ================= -->
<section id="home">
  <div id="padelSlider" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">

    <!-- INDICATORS -->
    <div class="carousel-indicators">
      <?php
      $i = 0;
      mysqli_data_seek($slider,0);
      while(mysqli_fetch_assoc($slider)):
      ?>
        <button type="button"
                data-bs-target="#padelSlider"
                data-bs-slide-to="<?= $i; ?>"
                class="<?= ($i==0)?'active':''; ?>"></button>
      <?php $i++; endwhile; ?>
    </div>

    <!-- SLIDER CONTENT -->
    <div class="carousel-inner">
      <?php
      $active = 'active';
      mysqli_data_seek($slider,0);
      while($s = mysqli_fetch_assoc($slider)):
      ?>
        <div class="carousel-item <?= $active; ?>">
          <div class="slider-bg"
               style="background-image:url('assets/img/slider/<?= $s['gambar']; ?>')">
            <div class="slider-content text-center">
              <h1><?= $s['judul']; ?></h1>
              <p><?= $s['deskripsi']; ?></p>
              
            </div>
          </div>
        </div>
      <?php
        $active = '';
      endwhile;
      ?>
    </div>

    <!-- NAV -->
    <button class="carousel-control-prev" type="button"
            data-bs-target="#padelSlider" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button"
            data-bs-target="#padelSlider" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>

  </div>
</section>


<!-- ================= KATALOG RAKET ================= --><!-- ================= KATALOG RAKET ================= -->
<section id="raket" class="py-5">
  <div class="container">
    <h2 class="text-center mb-4">Katalog Raket</h2>

    <div class="row g-4">
      <?php while($r = mysqli_fetch_assoc($raket)): ?>
        <div class="col-md-3 col-sm-6">
          <div class="card shadow-sm h-100 raket-card"
               style="cursor:pointer"
               onclick="openModal(<?= $r['id']; ?>)">

            <img src="assets/img/raket/<?= $r['gambar']; ?>"
                 class="card-img-top"
                 style="height:200px; object-fit:cover">

            <div class="card-body text-center">
              <h6 class="fw-bold mb-1"><?= $r['nama_raket']; ?></h6>

              <small class="text-muted d-block">
                Rp<?= number_format($r['harga_jam']); ?>/jam
              </small>

              <small class="text-muted">
                Rp<?= number_format($r['harga_hari']); ?>/hari
              </small>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>


<!-- ================= JADWAL BOOKING ================= -->
<section id="jadwal" class="py-5">
  <div class="container">
    <h2 class="text-center mb-4">Jadwal Raket Dipinjam</h2>

    <div class="card shadow-lg">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-borderless text-center align-middle mb-0">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Jam</th>
                <th>Raket</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
            <?php if(mysqli_num_rows($jadwal) > 0): ?>
              <?php while($j = mysqli_fetch_assoc($jadwal)): ?>
                <tr>
                  <td><?= date('d-m-Y', strtotime($j['tanggal'])); ?></td>
                  <td><?= $j['hari']; ?></td>
                  <td><?= substr($j['jam_mulai'],0,5); ?> - <?= substr($j['jam_selesai'],0,5); ?></td>
                  <td><strong><?= $j['nama_raket']; ?></strong></td>
                  <td><span class="badge bg-danger">Dipinjam</span></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-muted">Belum ada booking</td>
              </tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= MODAL RAKET ================= -->
<div class="modal fade" id="modalRaket" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-raket">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title">Detail Raket</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-3" id="modalContent"></div>
    </div>
  </div>
</div>


<!-- ================= BERITA ================= -->
<section id="berita" class="py-5">
  <div class="container">
    <h2 class="text-center mb-4">Berita & Kegiatan</h2>
    <div class="row g-4">
      <?php while($b = mysqli_fetch_assoc($berita)): ?>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <img src="assets/img/berita/<?= $b['gambar']; ?>" class="card-img-top">
            <div class="card-body">
              <h5><?= $b['judul']; ?></h5>
              <p><?= substr(strip_tags($b['isi']),0,100); ?>...</p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<!-- ================= KONTAK ================= -->
<section id="kontak" class="py-5 text-center">
  <div class="container">
    <h2>Kontak</h2>
    <p>📍 Lapangan Padel XYZ</p>
    <p>📞 WhatsApp: 0812-3456-789</p>
  </div>
</section>

<footer class="bg-dark text-white text-center py-3">
  <small>© <?= date('Y'); ?> PadelRent</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
