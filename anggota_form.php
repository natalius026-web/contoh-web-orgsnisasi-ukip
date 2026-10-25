<?php require '../includes/auth.php'; need('admin');
$id = (int)($_GET['id'] ?? 0); $title = $id ? 'Edit Anggota' : 'Tambah Anggota'; $err = [];
$d = ['nim'=>'','nama'=>'','prodi'=>'','angkatan'=>date('Y'),'jabatan'=>'Anggota','no_hp'=>'','email'=>'','status'=>'aktif'];
if ($id) { $st = $pdo->prepare("SELECT * FROM anggota WHERE id=?"); $st->execute([$id]); $d = $st->fetch() ?: die('Tidak ditemukan'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); foreach ($d as $k => $_) $d[$k] = trim($_POST[$k] ?? '');
  // Validasi input
  if (!preg_match('/^\d{5,20}$/', $d['nim'])) $err[] = 'NIM harus angka 5–20 digit.';
  if ($d['nama'] === '' || $d['prodi'] === '') $err[] = 'Nama dan prodi wajib diisi.';
  if ($d['email'] && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $err[] = 'Format email salah.';
  if ($d['no_hp'] && !preg_match('/^[0-9+]{8,16}$/', $d['no_hp'])) $err[] = 'No HP tidak valid.';
  $cek = $pdo->prepare("SELECT COUNT(*) FROM anggota WHERE nim=? AND id<>?"); $cek->execute([$d['nim'], $id]);
  if ($cek->fetchColumn()) $err[] = 'NIM sudah terdaftar.';
  if (!$err) {
    $v = [$d['nim'],$d['nama'],$d['prodi'],(int)$d['angkatan'],$d['jabatan'],$d['no_hp'],$d['email'],$d['status']];
    if ($id) { $pdo->prepare("UPDATE anggota SET nim=?,nama=?,prodi=?,angkatan=?,jabatan=?,no_hp=?,email=?,status=? WHERE id=?")->execute([...$v, $id]); }
    else {
      $pdo->prepare("INSERT INTO anggota (nim,nama,prodi,angkatan,jabatan,no_hp,email,status) VALUES (?,?,?,?,?,?,?,?)")->execute($v);
      // Akun login otomatis: username = NIM, password awal = NIM (di-hash)
      $pdo->prepare("INSERT INTO users (username,password,role,anggota_id) VALUES (?,?,'anggota',?)")->execute([$d['nim'], password_hash($d['nim'], PASSWORD_DEFAULT), $pdo->lastInsertId()]);
    }
    $_SESSION['flash'] = 'Data anggota disimpan.'; header('Location: anggota.php'); exit; } }
include '../includes/header.php'; foreach ($err as $x) echo '<div class="alert alert-danger py-2">' . e($x) . '</div>'; ?>
<form method="post" class="bg-white p-3 rounded" style="max-width:620px"><input type="hidden" name="csrf" value="<?= csrf() ?>">
<?php foreach (['nim'=>'NIM','nama'=>'Nama lengkap','prodi'=>'Program studi','angkatan'=>'Angkatan','jabatan'=>'Jabatan','no_hp'=>'No HP','email'=>'Email'] as $k => $l): ?>
<label class="form-label mb-0 mt-2"><?= $l ?></label><input name="<?= $k ?>" value="<?= e($d[$k]) ?>" class="form-control"><?php endforeach; ?>
<label class="form-label mb-0 mt-2">Status</label><select name="status" class="form-select"><option value="aktif" <?= $d['status']=='aktif'?'selected':'' ?>>Aktif</option><option value="nonaktif" <?= $d['status']=='nonaktif'?'selected':'' ?>>Nonaktif</option></select>
<button class="btn btn-warning mt-3">Simpan anggota</button> <a href="anggota.php" class="btn btn-link">Batal</a></form>
<?php include '../includes/footer.php'; ?>
