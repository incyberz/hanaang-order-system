<?php
$info_bayars = '';
$s = "SELECT * FROM tb_info WHERE untuk='info_bayar' ORDER BY nomor";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$info_bayars = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $t = explode('=', $d['info'], 2);
  $info_bayars .= "<li><b>$t[0]</b>: $t[1]</li>";
}


?>
<div class="mb-3">
  <div class="card">
    <div class="card-header bg-info text-white">Info Pembayaran</div>
    <div class="card-body">
      <div>Mohon ditransfer sesuai total bayar ke:</div>
      <ul>
        <?= $info_bayars ?>
      </ul>
      <div class="text-primary">foto bukti transfer Anda, lalu kirim ke Whatsapp Admin.</div>
    </div>
  </div>
</div>