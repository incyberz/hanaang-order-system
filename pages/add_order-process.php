<?php
if (isset($_POST['btn_submit_pesanan'])) {
  $id_order = $_POST['btn_submit_pesanan'];

  if ($_POST['qty']) {
    foreach ($_POST['qty'] as $id_produk => $qty) {
      if ($qty) {
        $s = "INSERT INTO tb_order_items (
          id,
          id_order,
          id_produk,
          qty
        ) VALUES (
          '$id_order-$id_produk',
          $id_order,
          $id_produk,
          $qty
        ) ON DUPLICATE KEY UPDATE
          qty = $qty
        ";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      }
    }
  }
  jsurl();
}
