<?php require '../includes/auth.php'; need('admin'); $title = 'Data Anggota';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {            // Hapus anggota
  csrf_check(); $pdo->prepare("DELETE FROM anggota WHERE id=?")->execute([(int)$_POST['hapus']]);
  $_SESSION['flash'] = 'Anggota dihapus.'; header('Location: anggota.php'); exit; }
$q = trim($_GET['q'] ?? ''); $s = $_GET['status'] ?? '';
$sql = "SELECT * FROM anggota WHERE (nama LIKE :q OR nim LIKE :q OR prodi LIKE :q)" . ($s ? " AND status=:s" : "") . " ORDER BY nama";
$st = $pdo->prepare($sql); $p = [':q' => "%$q%"]; if ($s) $p[':s'] = $s; $st->execute($p); $rows = $st->fetchAll();
include '../includes/header.php'; ?>
<form class="row g-2 mb-3"><div class="col-md-4"><input name="q" value="<?= e($q) ?>" class="form-control" placeholder="Cari nama / NIM / prodi"></div>
<div class="col-auto"><select name="status" class="form-select"><option value="">Semua status</option><option value="aktif" <?= $s=='aktif'?'selected':'' ?>>Aktif</option><option value="nonaktif" <?= $s=='nonaktif'?'selected':'' ?>>Nonaktif</option></select></div>
<div class="col-auto"><button class="btn btn-dark">Cari</button> <a class="btn btn-warning" href="anggota_form.php">+ Tambah anggota</a></div></form>
<table class="table table-hover bg-white"><tr><th>NIM</th><th>Nama</th><th>Prodi</th><th>Jabatan</th><th>Status</th><th></th></tr>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['nim']) ?></td><td><?= e($r['nama']) ?></td><td><?= e($r['prodi']) ?></td><td><?= e($r['jabatan']) ?></td><td><?= $r['status'] ?></td>
<td class="text-nowrap"><a class="btn btn-sm btn-outline-secondary" href="anggota_detail.php?id=<?= $r['id'] ?>">Detail</a>
<a class="btn btn-sm btn-outline-primary" href="anggota_form.php?id=<?= $r['id'] ?>">Edit</a>
<form method="post" class="d-inline" onsubmit="return confirm('Hapus anggota ini?')"><input type="hidden" name="csrf" value="<?= csrf() ?>"><button name="hapus" value="<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>
<?php endforeach; if (!$rows) echo '<tr><td colspan=6 class="text-muted">Data tidak ditemukan.</td></tr>'; ?></table>
<?php include '../includes/footer.php'; ?>
