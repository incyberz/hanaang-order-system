<?php
if ($_POST) {
  if (isset($_POST['btn_delete_item'])) {
    $s = "DELETE FROM tb_order_items WHERE id='$_POST[btn_delete_item]'";

    # ============================================================
    # SET METODE PEMBAYARAN
    # ============================================================
  } elseif (isset($_POST['btn_set_metode_bayar'])) {
    $nominal_bayar = intval($_POST['nominal_bayar']);
    $total_bayar = intval($_POST['total_bayar']);
    if (!$total_bayar) stop('Total bayar tidak boleh 0 @order-process');
    if ($nominal_bayar) {
      $status_bayar = $total_bayar == $nominal_bayar ? 3 : 2; // Lunas | DP
    } else {
      $status_bayar = 1; // konsinyasi
    }
    $status_order = 1; // persiapan kirim


    $s = "UPDATE tb_order SET 
      tmp_total_bayar = $total_bayar, 
      dp = $nominal_bayar,
      status_bayar = $status_bayar, 
      status_order = $status_order, 
      petugas = '$username',
      tanggal_cek = CURRENT_TIMESTAMP

    WHERE id='$_POST[btn_set_metode_bayar]'";

    # ============================================================
    # SET PETUGAS PENGIRIMAN
    # ============================================================
  } elseif (isset($_POST['btn_set_petugas_pengiriman'])) {
    $s = "UPDATE tb_order SET 
      qc = '$_POST[qc]', 
      kurir = '$_POST[kurir]'
    WHERE id='$_POST[btn_set_petugas_pengiriman]'";

    # ============================================================
    # SET QC OK
    # ============================================================
  } elseif (isset($_POST['btn_set_qc_ok'])) {
    $s = "UPDATE tb_order SET tanggal_qc = CURRENT_TIMESTAMP WHERE id='$_POST[btn_set_qc_ok]'";

    # ============================================================
    # SET KURIR BERANGKAT
    # ============================================================
  } elseif (isset($_POST['btn_set_kurir_berangkat'])) {
    $s = "UPDATE tb_order SET tanggal_kirim = CURRENT_TIMESTAMP WHERE id='$_POST[btn_set_kurir_berangkat]'";

    # ============================================================
    # UNDEFINED HANDLER
    # ============================================================
  } else {
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    stop('unhandled data POST.');
  }
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}
