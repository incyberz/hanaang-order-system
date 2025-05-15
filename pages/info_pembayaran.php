<?php

if (!$order) stop("Info Pembayaran memerlukan data order.");
if ($order['status_bayar'] == 'SB') {
  $tanggal = tanggal($order['tanggal_lunas']);
  $konten = "
    <div class='text-success center p-3'>
      <b>Order ini lunas terbayar</b> $img_check
      <div class='f12 text-normal pt-2'>Tanggal: $tanggal</div>
    </div>
  ";
  $bg = 'success';
} elseif ($order['status_order'] == 100) {
  $tanggal = tanggal($order['tanggal_terima']);
  $konten = "
    <div class='text-warning center p-3'>
      <span>Sisa Pembayaran Anda sedang diantarkan kurir atau sedang dicek oleh Petugas...</span> $img_loading
      <div class='f12 text-normal pt-2'>Tanggal Terima Barang: $tanggal</div>
    </div>
  ";
  $bg = 'warning';
} else {
  $bg = 'info';
  $konten = '';

  $li_infos = '';
  $s = "SELECT * FROM tb_info WHERE untuk='info_bayar' ORDER BY nomor";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $li_infos = '';
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $t = explode('=', $d['info'], 2);
    $li_infos .= "<li><b>$t[0]</b>: $t[1]</li>";
  }

  $konten = "
    <div>Jika Metode Pembayaran:</div>
    <ul>
      <li><b>DP</b>: silahkan Anda transfer uang muka minimal 20% dari total bayar</li>
      <li><b>Konsinyasi</b>: silahkan Anda hubungi Admin terlebih dahulu</li>
      <li><b>Full</b>: silahkan Anda transfer sesuai Total Bayar</li>
    </ul>
    <div>Informasi Transfer Pembayaran:</div>
    <ul>
      $li_infos
    </ul>
    <div class='text-primary'>foto bukti transfer Anda, lalu kirim ke Whatsapp Admin.</div>
  ";
}



?>
<div class="mb-3">
  <div class="card">
    <div class="card-header bg-<?= $bg ?> text-white">Info Pembayaran</div>
    <div class="card-body">
      <?= $konten ?>
    </div>
  </div>
</div>