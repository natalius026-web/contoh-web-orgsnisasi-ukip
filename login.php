<?php require 'includes/auth.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $st = $pdo->prepare("SELECT * FROM users WHERE username=?");
  $st->execute([trim($_POST['username'] ?? '')]);
  $u = $st->fetch();
  if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {   // cek hash
    session_regenerate_id(true);
    $_SESSION['user'] = ['id'=>$u['id'],'username'=>$u['username'],'role'=>$u['role'],'anggota_id'=>$u['anggota_id']];
    header('Location: ' . BASE_URL . '/' . $u['role'] . '/dashboard.php'); exit;
  }
  $err = 'Username atau password salah.';
} ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masuk | SI Organisasi UKIP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/style.css" rel="stylesheet"></head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;background:#0f3d3e">
<form method="post" class="bg-white p-4 rounded shadow" style="width:340px">
<h4>Masuk</h4><p class="text-muted small">Organisasi Mahasiswa UKIP Makassar</p>
<?php if ($err): ?><div class="alert alert-danger py-2"><?= e($err) ?></div><?php endif; ?>
<input type="hidden" name="csrf" value="<?= csrf() ?>">
<input class="form-control mb-2" name="username" placeholder="Username / NIM" required autofocus>
<input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
<button class="btn btn-warning w-100">Masuk</button></form></body></html>
