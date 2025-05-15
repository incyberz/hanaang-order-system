<?php
require_once 'includes/key2kolom.php';
require_once 'includes/eta.php';

$s = "SELECT 
a.id,
a.username,
a.tanggal_order,
a.status_bayar,
a.status_order,
c.nama as reseller,
(SELECT SUM(qty) FROM tb_order_items WHERE id_order=a.id) qty_order,
d.nama_status as status_pembayaran,
e.nama_status as status_pemesanan
FROM tb_order a 
JOIN tb_reseller b ON a.username=b.username
JOIN tb_user c ON b.username=c.username
JOIN tb_status_bayar d ON a.status_bayar=d.status
JOIN tb_status_order e ON a.status_order=e.status
WHERE a.status_order>=0 
AND NOT (a.status_order = 100 AND a.status_bayar = 'SB') 
ORDER BY tanggal_order DESC
LIMIT 10 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $no = 0;
  $th = '';
  while ($d = mysqli_fetch_assoc($q)) {
    if (!$d['qty_order']) continue; // biarkan reseller yang mengisi QTY
    $no++;
    $td = '';
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'username'
        || $key == 'date_created'
        || $key == 'status_bayar'
        || $key == 'status_order'
      ) {
        continue;
      } elseif ($key == 'tanggal_order') {
        $value =  $arr_hari[date('w', strtotime($value))]
          . ', '
          . date('d-M', strtotime($value))
          . ' <i class="f12 abu">'
          . date('H:i', strtotime($value))
          . "<br>"
          . eta2($d[$key])
          . '</i>';
      } elseif ($key == 'status_pembayaran' || $key == 'status_pemesanan') {
        $bg = $key == 'status_pembayaran' ? $rstatus_bayar[$d['status_bayar']]['bg'] : $rstatus_order[$d['status_order']]['bg'];
        $value = "<span class='badge bg-$bg'>$value</span>";
      }
      if ($no == 1) {
        $kolom = key2kolom($key);
        $th .= "<th>$kolom</th>";
      }
      $td .= "<td>$value</td>";
    }

    $primary = 'primary';
    if ($d['status_order'] == 100 and $d['status_bayar'] == 'SB') {
      $primary = 'secondary';
    } elseif (($d['status_order'] == 100 and $d['status_bayar'] != 'SB') || $d['status_order'] == 0) {
      $primary = 'danger';
    }

    $tr .= "
      <tr>
        <td>$no</td>
        $td
        <td><a target=_blank class='btn btn-sm btn-$primary' href='?order_detail&id_order=$d[id]&username=$d[username]'>Manage</a></td>
      </tr>
    ";
  }
}





?>

<div class="row">
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header bg-danger text-white">
        Review Order / <span class="f12">Pesanan yang Perlu Penanganan</span>
      </div>
      <div class="card-body gradasi-toska">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <?= $th ?>
              <th>Manage</th>
            </tr>
          </thead>
          <tbody>
            <?= $tr ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>