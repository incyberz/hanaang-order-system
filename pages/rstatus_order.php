<?php
$s = "SELECT * FROM tb_status_order";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rstatus_order = [];
while ($d = mysqli_fetch_assoc($q)) {
  $rstatus_order[$d['status']] = $d;
}
