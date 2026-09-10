<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include '../config/database.php';
if(!isset($_SESSION['admin'])){
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin — PadelRent</title>
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
    .topbar{
      background: var(--court-navy-950);
      padding: 16px 0;
    }
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
    .brand::before{
      content: "";
      width: 9px; height: 9px;
      border-radius: 50%;
      background: var(--ball);
    }
    .brand-tag{
      color: rgba(255,255,255,.45);
      font-family: "Inter", sans-serif;
      font-size: .8rem;
      font-weight: 500;
      margin-left: 8px;
    }
    .logout-link{
      color: rgba(255,255,255,.7);
      font-size: .88rem;
      font-weight: 600;
      text-decoration: none;
      padding: 8px 14px;
      border: 1px solid rgba(255,255,255,.18);
      border-radius: 3px;
      transition: color .15s ease, border-color .15s ease;
    }
    .logout-link:hover{ color: #fff; border-color: rgba(255,255,255,.4); }

    /* ===== MAIN ===== */
    main{
      max-width: 1040px;
      margin: 0 auto;
      padding: 48px 24px 64px;
    }
    .page-head{
      margin-bottom: 32px;
    }
    .page-head .eyebrow{
      color: var(--clay);
      font-weight: 700;
      font-size: .82rem;
      margin: 0 0 6px;
    }
    .page-head h1{
      font-family: "Anton", sans-serif;
      font-weight: 400;
      text-transform: uppercase;
      font-size: clamp(1.8rem, 4vw, 2.4rem);
      color: var(--court-navy-950);
      margin: 0 0 8px;
    }
    .page-head p{
      color: #6B7A85;
      font-size: .98rem;
      margin: 0;
    }

    /* ===== MENU GRID ===== */
    .menu-grid{
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 18px;
    }
    .menu-card{
      display: block;
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 4px;
      padding: 24px 22px;
      text-decoration: none;
      color: inherit;
      transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }
    .menu-card:hover{
      transform: translateY(-3px);
      border-color: var(--court-navy-900);
      box-shadow: 0 14px 28px -18px rgba(15,42,61,.5);
    }
    .menu-card .icon{
      width: 40px; height: 40px;
      border-radius: 3px;
      background: var(--court-navy-950);
      color: var(--ball);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: "Anton", sans-serif;
      font-size: 1.1rem;
      margin-bottom: 16px;
    }
    .menu-card h3{
      font-family: "Inter", sans-serif;
      font-weight: 700;
      font-size: 1.05rem;
      margin: 0 0 6px;
      color: var(--ink);
    }
    .menu-card p{
      color: #6B7A85;
      font-size: .88rem;
      margin: 0;
    }
    a:focus-visible{ outline: 2px solid var(--court-navy-900); outline-offset: 2px; }
  </style>
</head>
<body>

  <div class="topbar">
    <div class="topbar-inner">
      <div class="brand">PadelRent <span class="brand-tag">Admin Panel</span></div>
      <a class="logout-link" href="logout.php">Logout</a>
    </div>
  </div>

  <main>
    <div class="page-head">
      <p class="eyebrow">Dashboard</p>
      <h1>Halo, Admin</h1>
      <p>Kelola raket, jadwal booking, dan berita dari satu tempat.</p>
    </div>

    <div class="menu-grid">
      <a class="menu-card" href="raket.php">
        <div class="icon">R</div>
        <h3>Kelola Raket</h3>
        <p>Tambah, ubah, atau hapus data raket dan harga sewa.</p>
      </a>

      <a class="menu-card" href="booking.php">
        <div class="icon">B</div>
        <h3>Kelola Booking</h3>
        <p>Lihat dan atur jadwal peminjaman raket.</p>
      </a>

      <a class="menu-card" href="berita.php">
        <div class="icon">N</div>
        <h3>Kelola Berita</h3>
        <p>Tulis dan perbarui berita atau kegiatan terbaru.</p>
      </a>
    </div>
  </main>

</body>
</html>