<?php
$s = "SELECT a.*,
b.nama,
c.* 
FROM tb_petugas a
JOIN tb_user b ON a.username=b.username 
JOIN tb_role c ON b.role=c.role
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rpetugas = [];
while ($d = mysqli_fetch_assoc($q)) {
  $rpetugas[$d['username']] = $d;
}
