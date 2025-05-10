<?php
# ============================================================
# HARGA PRODUK PER LOKASI
# ============================================================
$undefined_harga = 0;
$s = "SELECT * FROM tb_lokasi ORDER BY jarak, kec_kab";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_lokasi = mysqli_num_rows($q);
$tr = '';
$th = '';
$no = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $no++;
  $s2 = "SELECT * FROM tb_produk";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $td_harga_produk = '';
  while ($d2 = mysqli_fetch_assoc($q2)) {
    if ($no == 1) $th .= "<th>$d2[nama]</th>";
    $td_harga_produk .= "
<td>$d2[harga]ZZZ</td>
";
  }

  $tr .= "
<tr>
  <td>$no</td>
  <td>$d[kec_kab]</td>
  <td>$d[jarak]km</td>
  $td_harga_produk
</tr>
";
}

echo "
<table class=table>
  <thead>
    <th>No</th>
    <th>Lokasi</th>
    <th>Jarak</th>
    <th>Harga</th>
    $th
  </thead>
  $tr
</table>
";
