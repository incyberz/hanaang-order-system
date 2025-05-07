<?php
$s = "SELECT * FROM tb_status_bayar";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rstatus_bayar = [];
while ($d = mysqli_fetch_assoc($q)) {
  $rstatus_bayar[$d['status']] = $d;
}
