<?php
if ($_POST) {
  if (isset($_POST['btn_delete_order'])) {
    $t = explode('--', $_POST['btn_delete_order']);
    if ($t[1]) { // ada sum_qty
      $s = "UPDATE tb_order SET delete_at = CURRENT_TIMESTAMP WHERE id=$t[0]";
    } else { // trx kosong
      $s = "DELETE FROM tb_order WHERE id=$t[0]";
    }
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    jsurl();
  }

  if (isset($_POST['btn_pause_order'])) {
    $t = explode('--', $_POST['btn_pause_order']);
    if ($t[1]) { // play
      $s = "UPDATE tb_order SET 
        status_order = info_status, -- kembalikan ke status awal 
        info_status = NULL -- reset info status
      WHERE id=$t[0]";
    } else { // pause
      $s = "UPDATE tb_order SET 
        info_status = status_order, -- untuk mengembalikan ke play
        status_order = -2 -- status pause 
      WHERE id=$t[0]";
    }
    echo '<pre>';
    print_r($s);
    echo '</pre>';
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    jsurl();
  }
  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
  stop('unhandled data POST.');
}
