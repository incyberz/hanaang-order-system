<style>
  .stats .sub_title {
    font-size: 12px;
  }
</style>
<?php
set_h2('Admin Dashboard');
include 'rstatus_order.php';

$rs = [];
$s = "SELECT 1 FROM tb_reseller a 
  JOIN tb_user b ON a.username=b.username 
  WHERE b.active_status = 1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$reseller_total = mysqli_num_rows($q);

$s = "$s AND b.whatsapp_status=1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$reseller_aktif = mysqli_num_rows($q);






# ============================================================
# ARRAY ORDER
# ============================================================
$rorder = [];
$rorder['null'] = 0;
foreach ($rstatus_order as $status => $d) $rorder[$d['status']] = 0;

$s = "SELECT status_order FROM tb_order WHERE delete_at is null";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_order = mysqli_num_rows($q);
while ($d = mysqli_fetch_assoc($q)) {
  $d['status_order'] = $d['status_order'] === null ? 'null' : $d['status_order'];
  $rorder[$d['status_order']]++;
}

$sum_rorder = array_sum($rorder);

$new_order = $rorder['null'] + $rorder[0];
$order_inprogres = $rorder[1] + $rorder[2] + $rorder[3];
$order_dibatalkan = $rorder[-1] + $rorder[-2] + $rorder[-3];

$rstat = [
  'pesanan-baru' => [
    'bg' => 'danger',
    'title' => 'Pesanan Baru',
    'sub_title' => 'Status 0 dan null',
    'count' => $new_order,
  ],
  'pesanan-in-progress' => [
    'bg' => 'warning',
    'title' => 'Pesanan In-Progress',
    'sub_title' => 'Status 1, 2, dan 3',
    'count' => $order_inprogres,
  ],
  'pesanan-sukses' => [
    'bg' => 'success',
    'title' => 'Pesanan Sukses',
    'sub_title' => 'Status 100',
    'count' => $rorder[100],
    'satuan_count' => 'Pesanan',
    'total_count' => $new_order + $order_inprogres,
  ],
  'pesanan-dibatalkan' => [
    'bg' => 'secondary',
    'title' => 'Pesanan Dibatalkan',
    'sub_title' => 'Status negatif',
    'count' => $order_dibatalkan,
    'satuan_count' => 'Pesanan',
    'total_count' => $total_order,
  ],
  'pembayaran' => [
    'bg' => 'success',
    'title' => 'Verified Pembayaran',
    'count' => $pembayaran_count,
    'satuan_count' => 'Bukti Bayar',
    'total_count' => $total_pembayaran_count,
  ],
  'DO' => [
    'bg' => 'success',
    'title' => 'Surat Jalan',
    'count' => $DO_count,
    'satuan_count' => 'Pengiriman',
    'total_count' => $total_DO_count,
  ],
  'reseller' => [
    'bg' => 'info',
    'title' => 'Reseller Aktif',
    'count' => $reseller_aktif,
    'satuan_count' => 'Reseller',
    'total_count' => $reseller_total,
  ],
];

$stats = '';
foreach ($rstat as $stat => $v) {
  // $bg = $v['count'] == $v['total_count'] ? $v['bg'] : 'danger';
  $bg = $v['bg'];
  $of_total = isset($v['total_count']) ? "of $v[total_count]" : '';
  $satuan_count = isset($v['satuan_count']) ? "<span class='satuan-count'>$v[satuan_count]</span>" : '';
  $sub_title = isset($v['sub_title']) ? "<div class='sub_title'>$v[sub_title]</div>" : '';
  $stats .= "
    <div class='col-md-4 col-xl-3 mb-4'>
      <div class='card'>
        <div class='card-body bg-$bg text-white'>
          <h5 class='card-title'>$v[title]</h5>
          <div class='card-text'><span class='display-4'>$v[count]</span> $of_total $satuan_count</div>
          $sub_title
        </div>
      </div>
    </div>  
  ";
}

echo "
  <div class='row stats'>$stats</div>
";
?>


<!-- Recent Orders -->
<div class="row">
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header bg-info text-white">
        <h5>Pesanan Terbaru</h5>
      </div>
      <div class="card-body gradasi-toska">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Reseller</th>
              <th>Pesanan</th>
              <th>Status Pembayaran</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>John Doe</td>
              <td>20 Cup Minuman A</td>
              <td>
                <span class="badge bg-warning">Menunggu Pembayaran</span>
              </td>
              <td>
                <button class="btn btn-primary">
                  Teruskan ke Keuangan
                </button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Jane Smith</td>
              <td>50 Cup Minuman B</td>
              <td><span class="badge bg-success">Dibayar</span></td>
              <td>
                <button class="btn btn-success">
                  Cetak Surat Jalan
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>