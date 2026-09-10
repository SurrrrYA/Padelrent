<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include '../config/database.php';
if(!isset($_SESSION['admin'])){
  header("Location: login.php");
  exit;
}

/** @var mysqli $conn */

$aksi = $_GET['aksi'] ?? 'list';
$formError = '';

/* ================= SIMPAN ================= */
if($aksi == 'simpan' && $_SERVER['REQUEST_METHOD'] === 'POST'){
  $raket   = (int)$_POST['raket_id'];
  $tanggal = $_POST['tanggal'];
  $jam1    = $_POST['jam_mulai'];
  $jam2    = $_POST['jam_selesai'];

  if($raket && $tanggal && $jam1 && $jam2){
    $hari = date('l', strtotime($tanggal));
    $hari = str_replace(
      ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
      ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'],
      $hari
    );

    $stmt = mysqli_prepare($conn, "INSERT INTO booking (raket_id,tanggal,hari,jam_mulai,jam_selesai) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "issss", $raket, $tanggal, $hari, $jam1, $jam2);
    mysqli_stmt_execute($stmt);

    header("Location: booking.php");
    exit;
  }
  $formError = 'Semua kolom wajib diisi.';
  $aksi = 'tambah';
}

/* ================= HAPUS ================= */
if($aksi == 'hapus'){
  $id = (int)($_GET['id'] ?? 0);

  $stmt = mysqli_prepare($conn, "DELETE FROM booking WHERE id=?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);

  header("Location: booking.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Booking — PadelRent</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --court-navy-950: #0B1F2E;
      --court-navy-900: #0F2A3D;
      --ink: #12202B;
      --paper: #FAF9F6;
      --paper-dim: #F0EEE7;
      --line: #DEDACE;
      --ball: #D6F62F;
      --ball-ink: #2B3A00;
      --clay: #E85D2C;
      --clay-dim: #FBE3D8;
    }
    *{ box-sizing: border-box; }
    html, body{ margin: 0; padding: 0; overflow-x: hidden; }
    body{
      min-height: 100vh;
      font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: var(--paper);
      color: var(--ink);
    }

    /* ===== TOPBAR ===== */
    .topbar{ background: var(--court-navy-950); padding: 16px 0; }
    .topbar-inner{
      max-width: 1040px;
      margin: 0 auto;
      padding: 0 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .brand{
      display: flex;
      align-items: center;
      gap: 8px;
      font-family: "Anton", sans-serif;
      font-size: 1.25rem;
      color: #fff;
      letter-spacing: .02em;
    }
    .brand::before{ content:""; width:9px; height:9px; border-radius:50%; background: var(--ball); }
    .brand-tag{ color: rgba(255,255,255,.45); font-family:"Inter",sans-serif; font-size:.8rem; font-weight:500; margin-left:8px; }
    .topbar-links{ display:flex; align-items:center; gap:10px; }
    .topbar-links a{
      color: rgba(255,255,255,.7);
      font-size: .88rem;
      font-weight: 600;
      text-decoration: none;
      padding: 8px 14px;
      border: 1px solid rgba(255,255,255,.18);
      border-radius: 3px;
      transition: color .15s ease, border-color .15s ease;
    }
    .topbar-links a:hover{ color:#fff; border-color: rgba(255,255,255,.4); }

    /* ===== MAIN ===== */
    main{ max-width: 1040px; margin: 0 auto; padding: 40px 24px 64px; }

    .page-head{
      display:flex;
      align-items:flex-end;
      justify-content: space-between;
      gap:16px;
      margin-bottom: 28px;
      flex-wrap: wrap;
    }
    .page-head .eyebrow{ color: var(--clay); font-weight:700; font-size:.82rem; margin:0 0 6px; }
    .page-head h1{
      font-family:"Anton",sans-serif; font-weight:400; text-transform:uppercase;
      font-size: clamp(1.6rem, 3.6vw, 2.2rem); color: var(--court-navy-950); margin:0;
    }

    .btn{
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 11px 18px;
      border-radius: 3px;
      font-weight: 700;
      font-size: .9rem;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: filter .15s ease, background-color .15s ease;
    }
    .btn-primary{ background: var(--ball); color: var(--ball-ink); }
    .btn-primary:hover{ filter: brightness(.94); }
    .btn-secondary{ background: #fff; color: var(--ink); border: 1.5px solid var(--line); }
    .btn-secondary:hover{ border-color: var(--court-navy-900); }
    .btn-sm{ padding: 6px 12px; font-size: .8rem; }
    .btn-delete{ background: var(--clay-dim); color: #9A3412; }
    .btn-delete:hover{ background: var(--clay); color: #fff; }

    /* ===== TABLE CARD ===== */
    .panel{
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 4px;
      overflow: hidden;
    }
    table{ width: 100%; border-collapse: collapse; }
    thead th{
      background: var(--court-navy-950);
      color: rgba(255,255,255,.65);
      text-align: left;
      font-size: .76rem;
      font-weight: 600;
      letter-spacing: .03em;
      padding: 13px 18px;
    }
    thead th.center{ text-align:center; }
    tbody td{
      padding: 14px 18px;
      border-bottom: 1px solid var(--paper-dim);
      font-size: .92rem;
      vertical-align: middle;
    }
    tbody tr:last-child td{ border-bottom: none; }
    tbody tr:hover{ background: var(--paper-dim); }
    .jam{ font-variant-numeric: tabular-nums; font-weight:600; color: var(--court-navy-950); }
    .empty-state{ padding: 48px 18px; text-align:center; color:#6B7A85; font-size:.92rem; }

    /* ===== FORM ===== */
    .form-panel{
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 4px;
      padding: 28px;
    }
    .form-wrap{ max-width: 500px; margin: 0 auto; }
    .field{ margin-bottom: 18px; }
    .field label{
      display:block; font-size:.78rem; font-weight:600; color: var(--court-navy-950);
      margin-bottom:6px; text-transform:uppercase; letter-spacing:.03em;
    }
    .field input, .field select{
      width:100%; padding: 11px 13px; border:1.5px solid var(--line); border-radius:3px;
      font-size:.94rem; font-family:inherit; color: var(--ink); background:#fff;
      transition: border-color .15s ease, box-shadow .15s ease;
    }
    .field input:focus, .field select:focus{
      outline:none; border-color: var(--court-navy-900); box-shadow: 0 0 0 3px rgba(15,42,61,.1);
    }
    .field-row{ display:grid; grid-template-columns: 1fr 1fr; gap:14px; }
    .form-actions{ display:flex; gap:10px; margin-top:6px; }

    .alert-error{
      background: var(--clay-dim); color:#7A2E10; border-left:3px solid var(--clay);
      padding: 10px 14px; border-radius:3px; font-size:.88rem; margin-bottom:18px;
    }

    @media (max-width: 640px){
      .field-row{ grid-template-columns: 1fr; }
      .panel{ overflow-x:auto; }
      table{ min-width: 520px; }
    }
  </style>
</head>
<body>

  <div class="topbar">
    <div class="topbar-inner">
      <div class="brand">PadelRent <span class="brand-tag">Admin Panel</span></div>
      <div class="topbar-links">
        <a href="dashboard.php">← Dashboard</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>

  <main>

  <?php if($aksi == 'list'): ?>

    <div class="page-head">
      <div>
        <p class="eyebrow">Kelola Data</p>
        <h1>Manajemen Booking</h1>
      </div>
      <a href="?aksi=tambah" class="btn btn-primary">+ Tambah Booking</a>
    </div>

    <div class="panel">
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Hari</th>
            <th>Jam</th>
            <th>Raket</th>
            <th class="center" width="120">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $data = mysqli_query($conn,"
          SELECT booking.*, raket.nama_raket
          FROM booking
          JOIN raket ON booking.raket_id = raket.id
          ORDER BY tanggal DESC, jam_mulai
        ");
        if(mysqli_num_rows($data) > 0):
          while($b=mysqli_fetch_assoc($data)):
        ?>
          <tr>
            <td><?= date('d-m-Y',strtotime($b['tanggal'])); ?></td>
            <td><?= htmlspecialchars($b['hari']); ?></td>
            <td class="jam"><?= substr($b['jam_mulai'],0,5); ?> – <?= substr($b['jam_selesai'],0,5); ?></td>
            <td><strong><?= htmlspecialchars($b['nama_raket']); ?></strong></td>
            <td>
              <a href="?aksi=hapus&id=<?= (int)$b['id']; ?>"
                 onclick="return confirm('Hapus booking ini?')"
                 class="btn btn-delete btn-sm">Hapus</a>
            </td>
          </tr>
        <?php
          endwhile;
        else:
        ?>
          <tr><td colspan="5" class="empty-state">Belum ada booking</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  <?php elseif($aksi=='tambah'): ?>

    <div class="form-wrap">
    <div class="page-head">
      <div>
        <p class="eyebrow">Kelola Data</p>
        <h1>Tambah Booking</h1>
      </div>
    </div>

    <div class="form-panel">
      <?php if($formError): ?>
        <div class="alert-error"><?= htmlspecialchars($formError); ?></div>
      <?php endif; ?>

      <form method="post" action="?aksi=simpan">
        <div class="field">
          <label>Raket</label>
          <select name="raket_id" required>
            <option value="">— Pilih Raket —</option>
            <?php
            $r = mysqli_query($conn,"SELECT * FROM raket ORDER BY nama_raket");
            while($rk=mysqli_fetch_assoc($r)):
            ?>
              <option value="<?= (int)$rk['id']; ?>"><?= htmlspecialchars($rk['nama_raket']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="field">
          <label>Tanggal</label>
          <input type="date" name="tanggal" required>
        </div>

        <div class="field-row">
          <div class="field">
            <label>Jam Mulai</label>
            <select name="jam_mulai">
              <?php for($i=8;$i<=22;$i++): ?>
                <option><?= sprintf('%02d:00',$i); ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="field">
            <label>Jam Selesai</label>
            <select name="jam_selesai">
              <?php for($i=9;$i<=23;$i++): ?>
                <option><?= sprintf('%02d:00',$i); ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="form-actions">
          <button class="btn btn-primary">Simpan</button>
          <a href="booking.php" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    </div>

  <?php endif; ?>

  </main>

</body>
</html>