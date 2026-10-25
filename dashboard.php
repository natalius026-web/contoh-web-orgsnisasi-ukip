<?php require '../includes/auth.php'; need('anggota'); $title = 'Data Diri & Riwayat Absensi';
$aid = $_SESSION['user']['anggota_id'];
$st = $pdo->prepare("SELECT * FROM anggota WHERE id=?"); $st->execute([$aid]); $m = $st->fetch();
$st = $pdo->prepare("SELECT j.tanggal,j.judul,a.status,a.metode,a.waktu FROM absensi a JOIN jadwal j ON j.id=a.jadwal_id WHERE a.anggota_id=? ORDER BY j.tanggal DESC");
$st->execute([$aid]); $h = $st->fetchAll(); include '../includes/header.php'; ?>
<div class="bg-white p-3 rounded mb-4"><b><?= e($m['nama']) ?></b><br>NIM <?= e($m['nim']) ?> · <?= e($m['prodi']) ?> · Angkatan <?= e($m['angkatan']) ?> · <?= e($m['jabatan']) ?></div>
<h5>Riwayat absensi</h5><table class="table bg-white"><tr><th>Tanggal</th><th>Kegiatan</th><th>Status</th><th>Metode</th></tr>
<?php foreach ($h as $r): ?><tr><td><?= $r['tanggal'] ?></td><td><?= e($r['judul']) ?></td><td><?= $r['status'] ?></td><td><?= $r['metode'] ?></td></tr><?php endforeach;
if (!$h) echo '<tr><td colspan=4 class="text-muted">Belum ada riwayat. Buka menu Scan Absensi saat kegiatan berlangsung.</td></tr>'; ?></table>
<?php include '../includes/footer.php'; ?>
