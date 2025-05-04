<?php
if (isset($_POST['btn_delete_order'])) {
  $s = "UPDATE tb_order SET delete_at = CURRENT_TIMESTAMP WHERE id=$_POST[btn_delete_order]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}
