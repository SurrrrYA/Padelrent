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
$allowedExt = ['jpg','jpeg','png','webp'];
$maxFileSize = 2 * 1024 * 1024; // 2MB
$formError = '';

function uploadGambarBerita($file, &$error){
  global $allowedExt, $maxFileSize;

  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if(!in_array($ext, $allowedExt)){
    $error = 'Format gambar harus JPG, JPEG, PNG, atau WEBP.';
    return false;
  }
  if($file['size'] > $maxFileSize){
    $error = 'Ukuran gambar maksimal 2MB.';
    return false;
  }

  $namaBersih = preg_replace('/[^A-Za-z0-9_-]/', '', pathinfo($file['name'], PATHINFO_FILENAME));
  $gambar = time().'_'.$namaBersih.'.'.$ext;

  if(!move_uploaded_file($file['tmp_name'], "../assets/img/berita/".$gambar)){
    $error = 'Gagal menyimpan gambar.';
    return false;
  }
  return $gambar;
}

/* ================= SIMPAN ================= */
if($aksi == 'simpan' && $_SERVER['REQUEST_METHOD'] === 'POST'){
  $judul = trim($_POST['judul']);
  $isi   = trim($_POST['isi']);

  if(!$judul || !$isi || empty($_FILES['gambar']['name'])){
    $formError = 'Judul, isi, dan gambar wajib diisi.';
    $aksi = 'tambah';
  } else {
    $gambar = uploadGambarBerita($_FILES['gambar'], $formError);
    if($gambar){
      $stmt = mysqli_prepare($conn, "INSERT INTO berita (judul,isi,gambar,tanggal) VALUES (?,?,?,CURDATE())");
      mysqli_stmt_bind_param($stmt, "sss", $judul, $isi, $gambar);
      mysqli_stmt_execute($stmt);

      header("Location: berita.php");
      exit;
    }
    $aksi = 'tambah';
  }
}

/* ================= UPDATE ================= */
if($aksi == 'update' && $_SERVER['REQUEST_METHOD'] === 'POST'){
  $id    = (int)$_POST['id'];
  $judul = trim($_POST['judul']);
  $isi   = trim($_POST['isi']);

  if(!empty($_FILES['gambar']['name'])){
    $gambar = uploadGambarBerita($_FILES['gambar'], $formError);

    if($gambar){
      $stmtOld = mysqli_prepare($conn, "SELECT gambar FROM berita WHERE id=?");
      mysqli_stmt_bind_param($stmtOld, "i", $id);
      mysqli_stmt_execute($stmtOld);
      $old = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtOld));
      if($old && $old['gambar'] && file_exists("../assets/img/berita/".$old['gambar'])){
        unlink("../assets/img/berita/".$old['gambar']);
      }

      $stmt = mysqli_prepare($conn, "UPDATE berita SET judul=?, isi=?, gambar=? WHERE id=?");
      mysqli_stmt_bind_param($stmt, "sssi", $judul, $isi, $gambar, $id);
      mysqli_stmt_execute($stmt);

      header("Location: berita.php");
      exit;
    }
    $aksi = 'edit';
    $_GET['id'] = $id;
  } else {
    $stmt = mysqli_prepare($conn, "UPDATE berita SET judul=?, isi=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssi", $judul, $isi, $id);
    mysqli_stmt_execute($stmt);

    header("Location: berita.php");
    exit;
  }
}

/* ================= HAPUS ================= */
if($aksi == 'hapus'){
  $id = (int)($_GET['id'] ?? 0);

  $stmt = mysqli_prepare($conn, "SELECT gambar FROM berita WHERE id=?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

  if($r && $r['gambar'] && file_exists("../assets/img/berita/".$r['gambar'])){
    unlink("../assets/img/berita/".$r['gambar']);
  }

  $stmt = mysqli_prepare($conn, "DELETE FROM berita WHERE id=?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);

  header("Location: berita.php");
  exit;
}

/* ================= DATA UNTUK EDIT ================= */
$editData = null;
if($aksi == 'edit'){
  $id = (int)($_GET['id'] ?? 0);
  $stmt = mysqli_prepare($conn, "SELECT * FROM berita WHERE id=?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $editData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
  if(!$editData){
    header("Location: berita.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Berita — PadelRent</title>
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
    .btn-edit{ background: var(--court-navy-950); color: #fff; }
    .btn-edit:hover{ filter: brightness(1.2); }
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
    .thumb{ width: 60px; height: 60px; object-fit: cover; border-radius: 3px; display: block; }
    .tanggal{ color:#6B7A85; font-size:.86rem; white-space:nowrap; }
    .excerpt{ color:#6B7A85; font-size:.86rem; }
    .actions{ display:flex; gap:8px; }
    .empty-state{ padding: 48px 18px; text-align:center; color:#6B7A85; font-size:.92rem; }

    /* ===== FORM ===== */
    .form-panel{
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 4px;
      padding: 28px;
    }
    .form-wrap{ max-width: 620px; margin: 0 auto; }
    .field{ margin-bottom: 18px; }
    .field label{
      display:block; font-size:.78rem; font-weight:600; color: var(--court-navy-950);
      margin-bottom:6px; text-transform:uppercase; letter-spacing:.03em;
    }
    .field input[type=text], .field textarea{
      width:100%; padding: 11px 13px; border:1.5px solid var(--line); border-radius:3px;
      font-size:.94rem; font-family:inherit; color: var(--ink); background:#fff;
      transition: border-color .15s ease, box-shadow .15s ease;
    }
    .field input:focus, .field textarea:focus{
      outline:none; border-color: var(--court-navy-900); box-shadow: 0 0 0 3px rgba(15,42,61,.1);
    }
    .field textarea{ min-height: 160px; resize: vertical; }
    .field input[type=file]{
      width:100%; padding: 9px; border:1.5px dashed var(--line); border-radius:3px; font-size:.88rem; background: var(--paper-dim);
    }
    .current-img{ display:flex; align-items:center; gap:12px; margin-bottom:10px; }
    .current-img img{ width:72px; height:72px; object-fit:cover; border-radius:3px; border:1px solid var(--line); }
    .current-img span{ font-size:.82rem; color:#6B7A85; }
    .form-actions{ display:flex; gap:10px; margin-top:6px; }

    .alert-error{
      background: var(--clay-dim); color:#7A2E10; border-left:3px solid var(--clay);
      padding: 10px 14px; border-radius:3px; font-size:.88rem; margin-bottom:18px;
    }

    @media (max-width: 640px){
      .panel{ overflow-x:auto; }
      table{ min-width: 560px; }
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
        <h1>Manajemen Berita</h1>
      </div>
      <a href="?aksi=tambah" class="btn btn-primary">+ Tambah Berita</a>
    </div>

    <div class="panel">
      <table>
        <thead>
          <tr>
            <th>Gambar</th>
            <th>Judul</th>
            <th>Tanggal</th>
            <th class="center" width="160">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $data = mysqli_query($conn,"SELECT * FROM berita ORDER BY id DESC");
        if(mysqli_num_rows($data) > 0):
          while($b=mysqli_fetch_assoc($data)):
        ?>
          <tr>
            <td><img class="thumb" src="../assets/img/berita/<?= htmlspecialchars($b['gambar']); ?>" alt=""></td>
            <td>
              <div><strong><?= htmlspecialchars($b['judul']); ?></strong></div>
              <div class="excerpt"><?= htmlspecialchars(mb_substr(strip_tags($b['isi']),0,80)); ?>...</div>
            </td>
            <td class="tanggal"><?= date('d-m-Y', strtotime($b['tanggal'])); ?></td>
            <td>
              <div class="actions">
                <a href="?aksi=edit&id=<?= (int)$b['id']; ?>" class="btn btn-edit btn-sm">Edit</a>
                <a href="?aksi=hapus&id=<?= (int)$b['id']; ?>"
                   onclick="return confirm('Hapus berita ini?')"
                   class="btn btn-delete btn-sm">Hapus</a>
              </div>
            </td>
          </tr>
        <?php
          endwhile;
        else:
        ?>
          <tr><td colspan="4" class="empty-state">Belum ada berita.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  <?php elseif($aksi=='tambah'): ?>

    <div class="form-wrap">
    <div class="page-head">
      <div>
        <p class="eyebrow">Kelola Data</p>
        <h1>Tambah Berita</h1>
      </div>
    </div>

    <div class="form-panel">
      <?php if($formError): ?>
        <div class="alert-error"><?= htmlspecialchars($formError); ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" action="?aksi=simpan">
        <div class="field">
          <label>Judul</label>
          <input type="text" name="judul" required>
        </div>

        <div class="field">
          <label>Isi Berita</label>
          <textarea name="isi" required></textarea>
        </div>

        <div class="field">
          <label>Gambar (JPG/PNG/WEBP, maks 2MB)</label>
          <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp" required>
        </div>

        <div class="form-actions">
          <button class="btn btn-primary">Publish</button>
          <a href="berita.php" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    </div>

  <?php elseif($aksi=='edit' && $editData): ?>

    <div class="form-wrap">
    <div class="page-head">
      <div>
        <p class="eyebrow">Kelola Data</p>
        <h1>Edit Berita</h1>
      </div>
    </div>

    <div class="form-panel">
      <?php if($formError): ?>
        <div class="alert-error"><?= htmlspecialchars($formError); ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" action="?aksi=update">
        <input type="hidden" name="id" value="<?= (int)$editData['id']; ?>">

        <div class="field">
          <label>Judul</label>
          <input type="text" name="judul" value="<?= htmlspecialchars($editData['judul']); ?>" required>
        </div>

        <div class="field">
          <label>Isi Berita</label>
          <textarea name="isi" required><?= htmlspecialchars($editData['isi']); ?></textarea>
        </div>

        <div class="field">
          <label>Gambar (opsional — kosongkan jika tidak diganti)</label>
          <div class="current-img">
            <img src="../assets/img/berita/<?= htmlspecialchars($editData['gambar']); ?>" alt="">
            <span>Gambar saat ini</span>
          </div>
          <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp">
        </div>

        <div class="form-actions">
          <button class="btn btn-primary">Update</button>
          <a href="berita.php" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    </div>

  <?php endif; ?>

  </main>

</body>
</html>