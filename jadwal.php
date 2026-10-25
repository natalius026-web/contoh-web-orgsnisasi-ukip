<?php require '../includes/auth.php'; need('admin'); $title = 'Jadwal & Piket';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check();
  $pdo->prepare("DELETE FROM jadwal WHERE id=?")->execute([(int)$_POST['hapus']]); $_SESSION['flash'] = 'Jadwal dihapus.'; header('Location: jadwal.php'); exit; }
$rows = $pdo->query("SELECT * FROM jadwal ORDER BY tanggal DESC, jam_mulai")->fetchAll();
include '../includes/header.php'; ?>
<a href="jadwal_form.php" class="btn btn-warning mb-3">+ Buat jadwal</a>
<table class="table bg-white"><tr><th>Tanggal</th><th>Kegiatan</th><th>Jenis</th><th>Jam</th><th>Lokasi</th><th></th></tr>
<?php foreach ($rows as $r): ?><tr><td><?= $r['tanggal'] ?></td><td><?= e($r['judul']) ?></td><td><?= $r['jenis'] ?></td><td><?= substr($r['jam_mulai'],0,5) ?>–<?= substr($r['jam_selesai'],0,5) ?></td><td><?= e($r['lokasi']) ?></td>
<td class="text-nowrap"><a class="btn btn-sm btn-dark" href="jadwal_qr.php?id=<?= $r['id'] ?>">QR</a>
<a class="btn btn-sm btn-outline-success" href="absensi_manual.php?id=<?= $r['id'] ?>">Absensi</a>
<a class="btn btn-sm btn-outline-primary" href="jadwal_form.php?id=<?= $r['id'] ?>">Edit</a>
<form method="post" class="d-inline" onsubmit="return confirm('Hapus jadwal?')"><input type="hidden" name="csrf" value="<?= csrf() ?>"><button name="hapus" value="<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr><?php endforeach; ?></table>
<?php include '../includes/footer.php'; ?>
