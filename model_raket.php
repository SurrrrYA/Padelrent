<?php
include 'config/database.php';
if(!isset($_GET['id'])) exit;

$id = mysqli_real_escape_string($conn,$_GET['id']);
$r  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM raket WHERE id='$id'"));
?>

<!-- NAMA RAKET -->
<h6 class="fw-bold text-center mb-2">
  <?= $r['nama_raket']; ?>
</h6>

<!-- GAMBAR -->
<div class="text-center mb-3">
  <img src="assets/img/raket/<?= $r['gambar']; ?>"
       class="img-fluid rounded shadow-sm"
       style="max-height:180px;object-fit:contain;">
</div>

<!-- DESKRIPSI -->
<p class="raket-desc-modal text-center mb-3">
  <?= $r['deskripsi']; ?>
</p>


<!-- HARGA -->
<div class="row text-center mb-3">
  <div class="col-6">
    <div class="border rounded py-2">
      <small class="text-muted">Per Jam</small><br>
      <strong class="text-success">
        Rp<?= number_format($r['harga_jam']); ?>
      </strong>
    </div>
  </div>
  <div class="col-6">
    <div class="border rounded py-2">
      <small class="text-muted">Per Hari</small><br>
      <strong class="text-success">
        Rp<?= number_format($r['harga_hari']); ?>
      </strong>
    </div>
  </div>
</div>

<hr class="my-3">

<!-- FORM PEMESANAN -->
<h6 class="fw-semibold mb-2">Form Pemesanan</h6>

<input type="hidden" id="nama_raket" value="<?= $r['nama_raket']; ?>">

<div class="mb-2">
  <label class="form-label small">Tanggal</label>
  <input type="date" id="tanggal" class="form-control form-control-sm">
</div>

<div class="row g-2">
  <div class="col-6">
    <label class="form-label small">Jam Mulai</label>
    <select id="jam_mulai" class="form-select form-select-sm">
      <?php for($i=8;$i<=22;$i++): ?>
        <option><?= sprintf('%02d:00',$i); ?></option>
      <?php endfor; ?>
    </select>
  </div>

  <div class="col-6">
    <label class="form-label small">Jam Selesai</label>
    <select id="jam_selesai" class="form-select form-select-sm">
      <?php for($i=9;$i<=23;$i++): ?>
        <option><?= sprintf('%02d:00',$i); ?></option>
      <?php endfor; ?>
    </select>
  </div>
</div>

<button onclick="pesanWA()"
        class="btn btn-success w-100 mt-3">
  📲 Pesan via WhatsApp
</button>
