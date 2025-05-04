<style>
  .row-order {
    border-top: solid 1px #ccc;
    padding: 15px 0;
  }
</style>
<?php
include 'my_order-process.php';
include 'rstatus_order.php';
include 'rpetugas.php';

$img_batal = img_icon('batal');


$s = "SELECT a.*,
(SELECT SUM(qty) FROM tb_order_items WHERE id_order=a.id) sum_qty 
FROM tb_order a 
WHERE a.username = '$username'
AND delete_at is null
ORDER BY a.tanggal 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
$div = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $id = $d['id'];
  $status = $d['status'];
  $i++;
  $tanggal = date('d-M-Y', strtotime($d['tanggal']));
  $jam = date('H:i', strtotime($d['tanggal']));

  # ============================================================
  # FITUR DELETE
  # ============================================================
  $btn_delete = "<span class=hover onclick='alert(`Tidak bisa delete order ini.`)'>$img_delete_disabled</span>";
  if ($status <= 0) {
    $btn_delete = "<button class=transparan onclick='alert(`Delete order ini.`)' value=$id name=btn_delete_order>$img_delete</button>";
  }


  # ============================================================
  # MANAJEMEN STATUS ORDER
  # ============================================================
  $status_show = "<span class='badge bg-danger'>Belum Anda Proses</span>";
  $info_status = '';
  if ($d['sum_qty']) {
    $status_show = "<span class='badge bg-warning'>Sedang Diproses Petugas</span>";

    if ($status !== null) {
      $petugas = $rpetugas[$d['petugas']]['nama'];
      $info_status .= "<div class='f12 text-success'><b>Admin</b>: $petugas</div>";

      $status_show = $rstatus_order[$status]['nama_status'];
      $bg = $rstatus_order[$status]['bg'];
      $status_show = "<span class='badge bg-$bg'>$status - $status_show</span>";
      if ($status == -1) {
        $info_status .= "<div class='f12 text-danger'>$d[info_status]</div>";
      } elseif ($status >= 1) {
        $qc = $rpetugas[$d['qc']]['nama'];
        $info_status .= "<div class='f12 text-success'><b>QC</b>: $qc</div>";
        if ($status >= 2) {
          $kurir = $rpetugas[$d['kurir']]['nama'];
          $info_status .= "<div class='f12 text-success'><b>Kurir</b>: $kurir</div>";
          if ($status == 100) {
            $penerima = $d['nama_penerima'] ? ucwords(strtolower($d['nama_penerima'])) : 'reseller';
            $info_status .= "<div class='f12 text-success'><b>Penerima</b>: $penerima</div>";
          }
        }
      }
    }
  }
  $sum_qty = number_format($d['sum_qty']);

  $aksi = "
    <a href='?order_detail&id_order=$id'>$img_detail</a>
    $btn_delete
  ";

  $tr .= "
    <tr>
      <td>$i</td>
      <td>
        $tanggal
        <div class='f14 abu'>$jam</div>
      </td>
      <td>$sum_qty</td>
      <td>
        $status_show
        <div class='m1'>$info_status</div>
      </td>
      <td>$aksi</td>
    </tr>
  ";

  $div .= "
    <div class='row-order'>
      <div class='d-flex justify-content-between'>
        <div class='miring abu'>$i</div>
        <div>$aksi</div>
      </div>
      <div>
        <b>Tanggal</b>:
        $tanggal
        <span class='f14 abu'>$jam</span>
      </div>
      <div><b>Total Items</b>: $sum_qty</div>
      <div class='d-flex gap-2 my-3'>
        <div>
          <b>Status</b>:
        </div>

        <div>
          $status_show
          <div class='m1'>$info_status</div>
        </div>
      </div>
    </div>
  ";
}




?>

<div class="card mb-3">
  <div class="card-header bg-info text-white">
    <h5>My Order (Pesanan Saya)</h5>
  </div>
  <form method=post class="card-body gradasi-toska">
    <div class="d-none d-lg-block">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Total Items</th>
            <th>Status Transaksi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?= $tr ?>
        </tbody>
      </table>
    </div>

    <div class="d-lg-none">
      <?= $div ?>
    </div>
  </form>

</div>