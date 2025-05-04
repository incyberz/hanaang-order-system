<?php
if ($_POST) {
  if (isset($_POST['btn_delete_item'])) {
    $s = "DELETE FROM tb_order_items WHERE id='$_POST[btn_delete_item]'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    jsurl();
  }
  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
  stop('unhandled data POST.');
}
