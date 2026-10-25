<?php require '../includes/auth.php'; need('admin'); $title = 'QR Code Absensi';
$st = $pdo->prepare("SELECT * FROM jadwal WHERE id=?"); $st->execute([(int)($_GET['id'] ?? 0)]); $j = $st->fetch() ?: die('Tidak ditemukan');
include '../includes/header.php'; ?>
<div class="bg-white p-4 rounded text-center" style="max-width:420px">
<h5><?= e($j['judul']) ?></h5><p class="text-muted"><?= $j['tanggal'] ?>, <?= substr($j['jam_mulai'],0,5) ?>–<?= substr($j['jam_selesai'],0,5) ?></p>
<div id="qr" class="d-inline-block"></div><p class="small mt-2">Anggota memindai QR ini lewat menu Scan Absensi.</p>
<button onclick="print()" class="btn btn-outline-dark no-print">Cetak QR</button></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>new QRCode(document.getElementById('qr'), {text: "<?= $j['token'] ?>", width: 260, height: 260});</script>
<?php include '../includes/footer.php'; ?>
