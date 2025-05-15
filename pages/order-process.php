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
      $status_bayar = $total_bayar == $nominal_bayar ? 'SB' : 'DP'; // Lunas | DP
    } else {
      $status_bayar = 'KS'; // konsinyasi
    }

    $s = "UPDATE tb_order SET 
      tmp_total_bayar = $total_bayar, 
      dp = $nominal_bayar,
      metode_bayar = '$_POST[metode_bayar]', 
      status_bayar = '$status_bayar', 
      status_order = 1, -- persiapan cek stok dan QC 
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
    $s = "UPDATE tb_order SET 
      tanggal_qc = CURRENT_TIMESTAMP, 
      status_order = 2 -- stok ready 
    WHERE id='$_POST[btn_set_qc_ok]'";

    # ============================================================
    # SET KURIR BERANGKAT
    # ============================================================
  } elseif (isset($_POST['btn_set_kurir_berangkat'])) {
    $s = "UPDATE tb_order SET 
      tanggal_kirim = CURRENT_TIMESTAMP,
      status_order = 3 -- sedang dikirim 
    WHERE id='$_POST[btn_set_kurir_berangkat]'";

    # ============================================================
    # BARANG DITERIMA
    # ============================================================
  } elseif (isset($_POST['btn_set_barang_diterima'])) {
    $nama_penerima = $_POST['nama_penerima'] ? "'$_POST[nama_penerima]'" : 'NULL';
    $s = "UPDATE tb_order SET 
      penerima = $nama_penerima,
      tanggal_terima = CURRENT_TIMESTAMP,
      metode_bayar = '$_POST[metode_bayar]', -- update metode bayar (mungkin berubah by reseller)
      -- status_bayar = 'SB', -- kurir hanya menerima cash, atau bukti bayar
      status_order = 100 -- ideal dulu, langsung status sukses 
    WHERE id='$_POST[btn_set_barang_diterima]'";

    # ============================================================
    # PEMBAYARAN TAHAP 2 (PELUNASAN)
    # ============================================================
  } elseif (isset($_POST['btn_set_lunas'])) {
    $s = "UPDATE tb_order SET 
      tanggal_lunas = CURRENT_TIMESTAMP,
      tmp_sisa_bayar = $_POST[tmp_sisa_bayar],
      status_bayar = 'SB' -- sudah bayar, by admin
    WHERE id='$_POST[btn_set_lunas]'";

    # ============================================================
    # RESET ORDER
    # ============================================================
  } elseif (isset($_POST['btn_reset_order'])) {
    $s = "UPDATE tb_order SET 
      status_order = 0,
      status_bayar = 'BB',
      metode_bayar = NULL,
      dp = NULL,
      tanggal_order = CURRENT_TIMESTAMP,
      tanggal_cek = NULL,
      tanggal_lunas = NULL,
      tanggal_qc = NULL,
      tanggal_kirim = NULL,
      tanggal_terima = NULL,
      petugas = NULL,
      qc = NULL,
      kurir = NULL,
      -- alamat_kirim = NULL, -- ditentukan reseller
      penerima = NULL,
      info_status = NULL,
      tmp_total_bayar = NULL  
    WHERE id='$_POST[btn_reset_order]'";

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
