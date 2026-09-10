<?php
include '../config/database.php';

$aksi = $_GET['aksi'] ?? 'list';

/* ================= TAMBAH ================= */
if($aksi == 'simpan'){
  $judul = $_POST['judul'];
  $desk  = $_POST['deskripsi'];

  $gambar = time().$_FILES['gambar']['name'];
  move_uploaded_file($_FILES['gambar']['tmp_name'],
    "../assets/img/slider/".$gambar
  );

  mysqli_query($conn,"
    INSERT INTO slider VALUES (NULL,'$judul','$desk','$gambar')
  ");

  header("Location: slider.php");
  exit;
}

/* ================= UPDATE ================= */
if($aksi == 'update'){
  $id    = $_POST['id'];
  $judul = $_POST['judul'];
  $desk  = $_POST['deskripsi'];

  if($_FILES['gambar']['name'] != ''){
    $old = mysqli_fetch_assoc(
      mysqli_query($conn,"SELECT gambar FROM slider WHERE id='$id'")
    );
    unlink("../assets/img/slider/".$old['gambar']);

    $gambar = time().$_FILES['gambar']['name'];
    move_uploaded_file($_FILES['gambar']['tmp_name'],
      "../assets/img/slider/".$gambar
    );

    mysqli_query($conn,"
      UPDATE slider SET
      judul='$judul', deskripsi='$desk', gambar='$gambar'
      WHERE id='$id'
    ");
  }else{
    mysqli_query($conn,"
      UPDATE slider SET
      judul='$judul', deskripsi='$desk'
      WHERE id='$id'
    ");
  }

  header("Location: slider.php");
  exit;
}

/* ================= HAPUS ================= */
if($aksi == 'hapus'){
  $id = $_GET['id'];
  $s  = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT gambar FROM slider WHERE id='$id'")
  );
  unlink("../assets/img/slider/".$s['gambar']);
  mysqli_query($conn,"DELETE FROM slider WHERE id='$id'");
  header("Location: slider.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Slider</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <h3 class="mb-4">Manajemen Slider</h3>

<?php if($aksi=='list'): ?>

  <a href="?aksi=tambah" class="btn btn-success mb-3">+ Tambah Slider</a>

  <table class="table table-bordered align-middle">
    <tr class="table-dark text-center">
      <th>Gambar</th>
      <th>Judul</th>
      <th>Deskripsi</th>
      <th>Aksi</th>
    </tr>

    <?php
    $data = mysqli_query($conn,"SELECT * FROM slider ORDER BY id DESC");
    while($s=mysqli_fetch_assoc($data)):
    ?>
    <tr>
      <td width="150">
        <img src="../assets/img/slider/<?= $s['gambar']; ?>" class="img-fluid rounded">
      </td>
      <td><?= $s['judul']; ?></td>
      <td><?= $s['deskripsi']; ?></td>
      <td class="text-center" width="160">
        <a href="?aksi=edit&id=<?= $s['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
        <a href="?aksi=hapus&id=<?= $s['id']; ?>"
           onclick="return confirm('Hapus slider?')"
           class="btn btn-danger btn-sm">Hapus</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

<?php elseif($aksi=='tambah'): ?>

  <form method="post" enctype="multipart/form-data" action="?aksi=simpan">
    <div class="mb-3">
      <label>Judul</label>
      <input type="text" name="judul" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Deskripsi</label>
      <textarea name="deskripsi" class="form-control"></textarea>
    </div>

    <div class="mb-3">
      <label>Gambar</label>
      <input type="file" name="gambar" class="form-control" required>
    </div>

    <button class="btn btn-success">Simpan</button>
    <a href="slider.php" class="btn btn-secondary">Batal</a>
  </form>

<?php elseif($aksi=='edit'):
  $id = $_GET['id'];
  $s  = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT * FROM slider WHERE id='$id'")
  );
?>

  <form method="post" enctype="multipart/form-data" action="?aksi=update">
    <input type="hidden" name="id" value="<?= $s['id']; ?>">

    <div class="mb-3">
      <label>Judul</label>
      <input type="text" name="judul" value="<?= $s['judul']; ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label>Deskripsi</label>
      <textarea name="deskripsi" class="form-control"><?= $s['deskripsi']; ?></textarea>
    </div>

    <div class="mb-3">
      <label>Gambar (opsional)</label><br>
      <img src="../assets/img/slider/<?= $s['gambar']; ?>" width="200" class="mb-2 rounded">
      <input type="file" name="gambar" class="form-control">
    </div>

    <button class="btn btn-success">Update</button>
    <a href="slider.php" class="btn btn-secondary">Batal</a>
  </form>

<?php endif; ?>

</div>

</body>
</html>
