<?php require '../includes/auth.php'; need('admin');
$id = (int)($_GET['id'] ?? 0); $title = $id ? 'Edit Jadwal' : 'Buat Jadwal'; $err = [];
$d = ['judul'=>'','jenis'=>'piket','tanggal'=>date('Y-m-d'),'jam_mulai'=>'08:00','jam_selesai'=>'10:00','lokasi'=>''];
if ($id) { $st = $pdo->prepare("SELECT * FROM jadwal WHERE id=?"); $st->execute([$id]); $d = $st->fetch() ?: die('Tidak ditemukan'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check();
  foreach (['judul','jenis','tanggal','jam_mulai','jam_selesai','lokasi'] as $k) $d[$k] = trim($_POST[$k] ?? '');
  if ($d['judul'] === '') $err[] = 'Judul wajib diisi.';
  if (!in_array($d['jenis'], ['piket','kegiatan','rapat'])) $err[] = 'Jenis tidak valid.';
  if (!strtotime($d['tanggal'])) $err[] = 'Tanggal tidak valid.';
  if ($d['jam_selesai'] <= $d['jam_mulai']) $err[] = 'Jam selesai harus setelah jam mulai.';
  if (!$err) { $v = [$d['judul'],$d['jenis'],$d['tanggal'],$d['jam_mulai'],$d['jam_selesai'],$d['lokasi']];
    if ($id) $pdo->prepare("UPDATE jadwal SET judul=?,jenis=?,tanggal=?,jam_mulai=?,jam_selesai=?,lokasi=? WHERE id=?")->execute([...$v, $id]);
    else $pdo->prepare("INSERT INTO jadwal (judul,jenis,tanggal,jam_mulai,jam_selesai,lokasi,token) VALUES (?,?,?,?,?,?,?)")->execute([...$v, bin2hex(random_bytes(16))]);  // token unik utk QR
    $_SESSION['flash'] = 'Jadwal disimpan.'; header('Location: jadwal.php'); exit; } }
include '../includes/header.php'; foreach ($err as $x) echo '<div class="alert alert-danger py-2">' . e($x) . '</div>'; ?>
<form method="post" class="bg-white p-3 rounded" style="max-width:560px"><input type="hidden" name="csrf" value="<?= csrf() ?>">
<label>Judul</label><input name="judul" value="<?= e($d['judul']) ?>" class="form-control">
<label class="mt-2">Jenis</label><select name="jenis" class="form-select"><?php foreach (['piket','kegiatan','rapat'] as $j): ?><option <?= $d['jenis']==$j?'selected':'' ?>><?= $j ?></option><?php endforeach; ?></select>
<label class="mt-2">Tanggal</label><input type="date" name="tanggal" value="<?= e($d['tanggal']) ?>" class="form-control">
<div class="row mt-2"><div class="col"><label>Mulai</label><input type="time" name="jam_mulai" value="<?= e(substr($d['jam_mulai'],0,5)) ?>" class="form-control"></div>
<div class="col"><label>Selesai</label><input type="time" name="jam_selesai" value="<?= e(substr($d['jam_selesai'],0,5)) ?>" class="form-control"></div></div>
<label class="mt-2">Lokasi</label><input name="lokasi" value="<?= e($d['lokasi']) ?>" class="form-control">
<button class="btn btn-warning mt-3">Simpan jadwal</button> <a href="jadwal.php" class="btn btn-link">Batal</a></form>
<?php include '../includes/footer.php'; ?>
