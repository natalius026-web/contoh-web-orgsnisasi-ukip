<?php require '../includes/auth.php'; need('admin'); $title = 'Detail Anggota';
$st = $pdo->prepare("SELECT * FROM anggota WHERE id=?"); $st->execute([(int)($_GET['id'] ?? 0)]); $m = $st->fetch() ?: die('Tidak ditemukan');
include '../includes/header.php'; ?>
<table class="table bg-white" style="max-width:600px"><?php foreach ($m as $k => $v): ?><tr><th><?= e($k) ?></th><td><?= e($v) ?></td></tr><?php endforeach; ?></table>
<a href="anggota.php" class="btn btn-secondary">Kembali</a>
<?php include '../includes/footer.php'; ?>
