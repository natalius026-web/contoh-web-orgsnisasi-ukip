<?php require '../includes/auth.php'; need('anggota');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {          // endpoint AJAX: proses hasil scan
  header('Content-Type: application/json'); csrf_check();
  $st = $pdo->prepare("SELECT * FROM jadwal WHERE token=?"); $st->execute([trim($_POST['token'] ?? '')]); $j = $st->fetch();
  if (!$j) exit(json_encode(['ok'=>false,'msg'=>'QR tidak dikenali.']));
  if ($j['tanggal'] !== date('Y-m-d') || date('H:i:s') < $j['jam_mulai'] || date('H:i:s') > $j['jam_selesai'])
    exit(json_encode(['ok'=>false,'msg'=>'Di luar waktu absensi untuk jadwal ini.']));
  $ins = $pdo->prepare("INSERT IGNORE INTO absensi (jadwal_id,anggota_id,status,metode) VALUES (?,?,'hadir','qr')");
  $ins->execute([$j['id'], $_SESSION['user']['anggota_id']]);
  exit(json_encode(['ok'=>true,'msg'=> $ins->rowCount() ? 'Absensi tercatat: ' . $j['judul'] : 'Anda sudah absen pada jadwal ini.']));
}
$title = 'Scan Absensi'; include '../includes/header.php'; ?>
<div class="bg-white p-3 rounded" style="max-width:420px"><div id="reader"></div><div id="hasil" class="mt-3"></div></div>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// Catatan: akses kamera butuh HTTPS atau http://localhost
const scanner = new Html5Qrcode("reader");
scanner.start({facingMode:"environment"}, {fps:10, qrbox:240}, async (text) => {
  await scanner.stop();
  const f = new FormData(); f.append('token', text); f.append('csrf', '<?= csrf() ?>');
  const r = await (await fetch('absen.php', {method:'POST', body:f})).json();
  document.getElementById('hasil').innerHTML = `<div class="alert alert-${r.ok?'success':'danger'}">${r.msg}</div>`;
});
</script>
<?php include '../includes/footer.php'; ?>
