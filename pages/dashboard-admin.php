<?php
set_h2('Admin Dashboard');

$rs = [];
$rs['reseller_count'] = "SELECT 1 FROM tb_reseller a 
  JOIN tb_user b ON a.username=b.username 
  WHERE b.active_status = 1";
$rs['active_reseller_count'] = "$rs[reseller_count] AND b.whatsapp_status=1";


$active_reseller_count = 15;
$reseller_count = 19;
$order_count = 24;
$total_order_count = 26;
$pembayaran_count = 23;
$total_pembayaran_count = 23;
$DO_count = 19;
$total_DO_count = 23;

$rstat = [
  'reseller' => [
    'bg' => 'info',
    'title' => 'Reseller Aktif',
    'count' => $active_reseller_count,
    'satuan_count' => 'Reseller',
    'total_count' => $reseller_count,
  ],
  'order' => [
    'bg' => 'info',
    'title' => 'Pesanan Sukses',
    'count' => $order_count,
    'satuan_count' => 'Pesanan',
    'total_count' => $total_order_count,
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
];

$stats = '';
foreach ($rstat as $stat => $v) {
  $bg = $v['count'] == $v['total_count'] ? $v['bg'] : 'danger';
  $stats .= "
    <div class='col-md-4 col-xl-3 mb-4'>
      <div class='card'>
        <div class='card-body bg-$bg text-white'>
          <h5 class='card-title'>$v[title]</h5>
          <p class='card-text'><span class='display-4'>$v[count]</span> of $v[total_count] $v[satuan_count]</p>
        </div>
      </div>
    </div>  
  ";
}

echo "
  <div class='row'>$stats</div>
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