<style>
  .stats .sub_title {
    font-size: 12px;
  }
</style>
<?php
set_h2('Admin Dashboard');
include 'rstatus_order.php';
include 'rstatus_bayar.php';

# ============================================================
# TAMBAH RESELLER 
# ============================================================
// include 'tambah_reseller.php';


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
# ARRAY STATUS ORDER & STATUS BAYAR
# ============================================================
$rstats = [];
$rorder = [];
foreach ($rstatus_order as $status => $d) $rorder[$d['status']] = 0;
$rbayar = [];
foreach ($rstatus_bayar as $status => $d) $rbayar[$d['status']] = 0;


$s = "SELECT 1 FROM tb_reseller a JOIN tb_user b ON a.username=b.username WHERE b.active_status = 1 ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_reseller = mysqli_num_rows($q);

$s = "$s AND b.whatsapp_status is null";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$unverified_reseller = mysqli_num_rows($q);

$rstats['reseller'] = [
  'reseller-baru' => [
    'bg' => 'danger',
    'title' => 'Unverified Reseller',
    'count' => $unverified_reseller,
  ],
  'total-reseller' => [
    'bg' => 'info',
    'title' => 'Total Reseller',
    'count' => $total_reseller,
  ],
  'tambah-reseller' => [
    'title' => 'Tambah Reseller',
    'href' => '?tambah_reseller',
  ],
];

$s = "SELECT status_order, status_bayar FROM tb_order WHERE delete_at is null";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_order = mysqli_num_rows($q);
while ($d = mysqli_fetch_assoc($q)) {
  $rorder[$d['status_order']]++;
  $rbayar[$d['status_bayar']]++;
}

$sum_rorder = array_sum($rorder);

$order_inprogres = $rorder[1] + $rorder[2] + $rorder[3];
$order_dibatalkan = $rorder[-1] + $rorder[-2] + $rorder[-3];

$rstats['pesanan'] = [
  'pesanan-baru' => [
    'bg' => 'danger',
    'title' => 'Pesanan Baru',
    'count' => $rorder[0],
  ],
  'pesanan-in-progress' => [
    'bg' => 'warning',
    'title' => 'In-Progress',
    'count' => $order_inprogres,
  ],
  'pesanan-sukses' => [
    'bg' => 'success',
    'title' => 'Pesanan Sukses',
    'count' => $rorder[100],
    'satuan_count' => 'Pesanan',
    'total_count' => $rorder[0] + $order_inprogres,
  ],
  'pesanan-dibatalkan' => [
    'bg' => 'secondary',
    'title' => 'Pesanan Dibatalkan',
    'count' => $order_dibatalkan,
    'satuan_count' => 'Pesanan',
    'total_count' => $total_order,
  ],
];


# ============================================================
# STATS UI STATUS BAYAR 
# ============================================================
foreach ($rstatus_bayar as $status_bayar => $v) {
  $rstats['pembayaran'][$status_bayar] = [
    'bg' => $v['bg'],
    'title' => $v['nama_status'],
    'count' => $rbayar[$status_bayar],
  ];
}

$stats = '';
foreach ($rstats as $key => $rstat_group) {
  $stat_groups = '';
  foreach ($rstat_group as $stat => $v) {
    // $bg = $v['count'] == $v['total_count'] ? $v['bg'] : 'danger';
    if (isset($v['count'])) {
      $bg = $v['count'] ? $v['bg'] : 'secondary';
      $of_total = isset($v['total_count']) ? "of $v[total_count]" : '';
      $satuan_count = isset($v['satuan_count']) ? "<span class='satuan-count'>$v[satuan_count]</span>" : '';
      $sub_title = isset($v['sub_title']) ? "<div class='sub_title'>$v[sub_title]</div>" : '';
      $stat_groups .= "
        <div class='col-md-6 col-xl-3 mb-2'>
          <div class='card'>
            <div class='card-body bg-$bg text-white'>
              <h5 class='card-title'>$v[title]</h5>
              <div class='card-text'><span class='display-4'>$v[count]</span> $of_total $satuan_count</div>
              $sub_title
            </div>
          </div>
        </div>  
      ";
    } elseif ($stat == 'tambah-reseller') {
      $stat_groups .= "
        <form class='col-md-6 col-xl-3 mb-2'>
          <p>Jika ada calon reseller baru via whatsapp, segera daftarkan agar tidak perlu lagi verifikasi whatsapp-nya.</p>
          <button class='btn btn-success' name=btn_tambah_reseller>Tambah Reseller</button>
        </form>  
      ";
    } else {
      echo '<pre>';
      print_r($v);
      stop('<b style=color:red>Belum ada handler untuk konten stats diatas.</b></pre>');
    }
  }

  # ============================================================
  # WRAPPING TIAP STATS DENGAN CARD
  # ============================================================
  $stats .= "
    <div class='card mt-2 mb-4 gradasi-toska'>
      <div class='card-header upper text-center text-white bg-info'>$key</div>
      <div class='card-body '>
        <div class=row>$stat_groups</div>
      </div>
    </div>
  ";
}

echo "
  $stats
";

include 'pesanan_terbaru.php';
include 'includes/script_btn_aksi.php';
