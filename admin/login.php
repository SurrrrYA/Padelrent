<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include '../config/database.php';

$error = '';

if(isset($_POST['login'])){
  $u = trim($_POST['username']);
  $p = md5($_POST['password']);

  $stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE username=? AND password=?");
  mysqli_stmt_bind_param($stmt, "ss", $u, $p);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if(mysqli_num_rows($result) > 0){
    $_SESSION['admin'] = true;
    header("Location: dashboard.php");
    exit;
  } else {
    $error = 'Username atau password salah.';
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin — PadelRent</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --court-navy-950: #0B1F2E;
      --court-navy-900: #0F2A3D;
      --ink: #12202B;
      --paper: #FAF9F6;
      --line: #DEDACE;
      --ball: #D6F62F;
      --ball-ink: #2B3A00;
      --clay: #E85D2C;
    }
    *{ box-sizing: border-box; }
    html, body{
      margin: 0;
      padding: 0;
      height: 100%;
      overflow-x: hidden;
    }
    body{
      min-height: 100vh;
      font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background:
        radial-gradient(1100px 600px at 15% 15%, rgba(214,246,47,.08), transparent 60%),
        var(--court-navy-950);
      color: var(--ink);
    }

    /* garis lapangan tipis sebagai dekorasi, samar */
    body::before{
      content: "";
      position: fixed; inset: 0;
      background-image:
        linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
      background-size: 64px 64px;
      pointer-events: none;
    }

    .login-wrap{
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: calc(100% - 48px);
      max-width: 400px;
    }

    .brand{
      display: flex;
      align-items: center;
      gap: 8px;
      justify-content: center;
      margin-bottom: 28px;
      font-family: "Anton", sans-serif;
      font-size: 1.4rem;
      color: #fff;
      letter-spacing: .02em;
    }
    .brand::before{
      content: "";
      width: 10px; height: 10px;
      border-radius: 50%;
      background: var(--ball);
    }

    .login-card{
      background: var(--paper);
      border-radius: 4px;
      padding: 36px 32px 32px;
      box-shadow: 0 24px 60px -20px rgba(0,0,0,.55);
    }

    .login-card h2{
      font-family: "Anton", sans-serif;
      font-weight: 400;
      text-transform: uppercase;
      font-size: 1.6rem;
      color: var(--court-navy-950);
      margin: 0 0 6px;
    }
    .login-card .sub{
      color: #6B7A85;
      font-size: .9rem;
      margin: 0 0 26px;
    }

    .field{ margin-bottom: 16px; }
    .field label{
      display: block;
      font-size: .78rem;
      font-weight: 600;
      color: var(--court-navy-950);
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: .03em;
    }
    .field input{
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid var(--line);
      border-radius: 3px;
      font-size: .95rem;
      font-family: inherit;
      color: var(--ink);
      background: #fff;
      transition: border-color .15s ease, box-shadow .15s ease;
    }
    .field input:focus{
      outline: none;
      border-color: var(--court-navy-900);
      box-shadow: 0 0 0 3px rgba(15,42,61,.1);
    }

    .btn-login{
      width: 100%;
      margin-top: 8px;
      padding: 13px 14px;
      background: var(--ball);
      color: var(--ball-ink);
      border: none;
      border-radius: 3px;
      font-weight: 700;
      font-size: .95rem;
      cursor: pointer;
      transition: filter .15s ease;
    }
    .btn-login:hover{ filter: brightness(.94); }
    .btn-login:focus-visible{ outline: 2px solid var(--court-navy-900); outline-offset: 2px; }

    .alert-error{
      background: #FBE3D8;
      color: #7A2E10;
      border-left: 3px solid var(--clay);
      padding: 10px 14px;
      border-radius: 3px;
      font-size: .88rem;
      margin-bottom: 20px;
    }

    .back-link{
      display: block;
      text-align: center;
      margin-top: 20px;
      color: rgba(255,255,255,.55);
      font-size: .85rem;
      text-decoration: none;
    }
    .back-link:hover{ color: #fff; }
  </style>
</head>
<body>

  <div class="login-wrap">
    <div class="brand">PadelRent</div>

    <div class="login-card">
      <h2>Login Admin</h2>
      <p class="sub">Masuk untuk kelola raket, jadwal, dan berita.</p>

      <?php if($error): ?>
        <div class="alert-error"><?= htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="field">
          <label for="username">Username</label>
          <input id="username" name="username" placeholder="Masukkan username" autocomplete="username" required autofocus>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input id="password" type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
        </div>
        <button class="btn-login" name="login">Login</button>
      </form>
    </div>

    <a class="back-link" href="../index.php">← Kembali ke halaman utama</a>
  </div>

</body>
</html>