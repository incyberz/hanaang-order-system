<?php
$s = "SELECT * FROM tb_metode_bayar";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rmetode_bayar = [];
while ($d = mysqli_fetch_assoc($q)) {
  $rmetode_bayar[$d['metode']] = $d;
}
