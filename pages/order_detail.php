<?php
include 'includes/btn_home.php';
include "$dotdot/includes/key2kolom.php";
$null = '<i class=abu>null</i>';

$id_order = $_GET['id_order'] ?? null;



if (!$id_order || !$username) {
  jsurl('?');
}

# ============================================================
# ORDER DETAIL
# ============================================================
$s = "SELECT 
a.id as order_id,
a.tanggal,
(SELECT CONCAT(status,' - ',nama_status) FROM tb_status_order WHERE status=a.status) status_order, 
a.tanggal_bayar,
a.tanggal_cek,
a.tanggal_kirim,
a.tanggal_terima,
(SELECT nama FROM tb_user WHERE username=a.petugas) petugas_admin, 
(SELECT nama FROM tb_user WHERE username=a.qc) petugas_qc, 
(SELECT nama FROM tb_user WHERE username=a.kurir) petugas_kurir,
a.*

FROM tb_order a WHERE id='$id_order'
AND a.username = '$username' -- order milik sendiri
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $order = mysqli_fetch_assoc($q);
  foreach ($order as $key => $value) {
    if (
      $key == 'id'
      || $key == 'username'
      || $key == 'status'
      || $key == 'petugas'
      || $key == 'qc'
      || $key == 'kurir'
      || $key == 'delete_at'
    ) continue;

    $kolom = key2kolom($key);
    $value = $value !== null ? $value : $null;

    $tr .= "
      <tr>
        <td class='miring abu'>$kolom</td>
        <td>$value</td>
      </tr>
    ";
  }

  # ============================================================
  # FINAL ECHO ORDER DETAIL
  # ============================================================
  echo "
    <h2 class='text-center text-md-start'>Order Detail</h2>
    <table class=table>
      $tr
    </table>
  ";
} else {
  alert("Data order id [$id_order] tidak ditemukan.");
}
