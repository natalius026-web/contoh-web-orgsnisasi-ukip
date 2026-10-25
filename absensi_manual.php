<?php require '../includes/auth.php'; need('admin'); $title = 'Input Absensi';
$id = (int)($_GET['id'] ?? 0); $st = $pdo->prepare("SELECT * FROM jadwal WHERE id=?"); $st->execute([$id]); $j = $st->fetch() ?: die('Tidak ditemukan');
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check();
  $up = $pdo->prepare("INSERT INTO absensi (jadwal_id,anggota_id,status,metode,keterangan) VALUES (?,?,?,'manual',?) ON DUPLICATE KEY UPDATE status=VALUES(status),keterangan=VALUES(keterangan)");
  foreach ($_POST['status'] ?? [] as $aid => $s) if (in_array($s, ['hadir','izin','sakit','alfa'])) $up->execute([$id, (int)$aid, $s, trim($_POST['ket'][$aid] ?? '')]);
  $_SESSION['flash'] = 'Absensi disimpan.'; header("Location: absensi_manual.php?id=$id"); exit; }
$st = $pdo->prepare("SELECT m.id, m.nim, m.nama, a.status, a.keterangan, a.metode FROM anggota m LEFT JOIN absensi a ON a.anggota_id=m.id AND a.jadwal_id=? WHERE m.status='aktif' ORDER BY m.nama");
$st->execute([$id]); $rows = $st->fetchAll(); include '../includes/header.php'; ?>
<p><b><?= e($j['judul']) ?></b> — <?= $j['tanggal'] ?></p>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>">
<table class="table bg-white"><tr><th>NIM</th><th>Nama</th><th>Status</th><th>Keterangan</th></tr>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['nim']) ?></td><td><?= e($r['nama']) ?><?= $r['metode']=='qr' ? ' <span class="badge bg-success">QR</span>' : '' ?></td>
<td><select name="status[<?= $r['id'] ?>]" class="form-select form-select-sm"><option value="">- belum -</option>
<?php foreach (['hadir','izin','sakit','alfa'] as $s): ?><option <?= $r['status']==$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></td>
<td><input name="ket[<?= $r['id'] ?>]" value="<?= e($r['keterangan']) ?>" class="form-control form-control-sm"></td></tr><?php endforeach; ?></table>
<button class="btn btn-warning">Simpan absensi</button> <a href="jadwal.php" class="btn btn-link">Kembali</a></form>
<?php include '../includes/footer.php'; ?>
