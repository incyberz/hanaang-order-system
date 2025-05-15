<style>
  h2 {
    text-align: center;
  }

  .stats .sub_title {
    font-size: 12px;
  }

  .link-detail {
    color: white;
  }

  .link-detail::after,
  .link-detail:hover {
    color: #ffc;
    /* background: #ffffffaa; */
  }
</style>
<?php
set_h2('Admin Dashboard');
$img_help = img_icon('help');







# ============================================================
# ARRAY STATUS ORDER & STATUS BAYAR
# ============================================================
$rstats = [];
$rorder = [];
$rbayar = [];
include 'rstatus_order.php';
include 'rstatus_bayar.php';
foreach ($rstatus_order as $status => $d) $rorder[$d['status']] = 0; // inisiasi
foreach ($rstatus_bayar as $status => $d) $rbayar[$d['status']] = 0; // inisiasi


























# ============================================================
# COUNT RESELLER 
# ============================================================
// $rs = [];
$s = "SELECT 1 FROM tb_reseller a 
  JOIN tb_user b ON a.username=b.username 
  WHERE b.active_status = 1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$reseller_total = mysqli_num_rows($q);

# ============================================================
# RESELLER AKTIF = VERIFIED WHATSAPP
# ============================================================
$s = "$s AND b.whatsapp_status=1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$reseller_aktif = mysqli_num_rows($q);

$s = "SELECT 1 FROM tb_user WHERE whatsapp_status is null";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$unverified_reseller = mysqli_num_rows($q);

$s = "SELECT 1 FROM tb_user 
WHERE role is null 
AND active_status is null 
AND created_by is not null";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$user_awal = mysqli_num_rows($q);


$rstats['reseller'] = [
  'reseller-aktif' => [
    'bg' => 'success',
    'title' => 'Reseller Aktif',
    'count' => $reseller_aktif,
    'href' => '?tampil_data&tb=reseller_aktif&title=Reseller Aktif',
  ],
  'reseller-baru' => [
    'bg' => 'danger',
    'title' => 'Unverified Whatsapp',
    'count' => $unverified_reseller,
    'href' => '?tampil_data&tb=unverified_whatsapp&title=Unverified Whatsapp',
    'satuan_count' => 'Akun',
  ],
  'user-default' => [
    'bg' => 'danger',
    'title' => 'User Awal',
    'count' => $user_awal,
    'href' => '?tampil_data&tb=user_awal&title=User Awal untuk Calon Reseller',
    'satuan_count' => 'User Akun',
  ],
  'tambah-reseller' => [
    'title' => 'Tambah Reseller',
    'href' => '?tambah_reseller',
  ],
];























# ============================================================
# COUNT ORDER
# ============================================================
$s = "SELECT status_order, status_bayar FROM tb_order WHERE delete_at is null";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_order = mysqli_num_rows($q);
$order_aktif = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $rorder[$d['status_order']]++;
  if ($d['status_order'] >= 0) {
    $order_aktif++;
    $rbayar[$d['status_bayar']]++;
  }
}

$order_inprogres = $rorder[1] + $rorder[2] + $rorder[3];
$order_dibatalkan = $rorder[-1] + $rorder[-2] + $rorder[-3];

$rstats['pesanan'] = [
  'pesanan-baru' => [
    'bg' => 'danger',
    'title' => 'Pesanan Baru',
    'count' => $rorder[0],
    'href' => '?tampil_data&tb=pesanan_baru',
  ],
  'pesanan-inprogress' => [
    'bg' => 'warning',
    'title' => 'In-Progress',
    'count' => $order_inprogres,
    'href' => '?tampil_data&tb=pesanan_inprogress',
  ],
  'pesanan-dibatalkan' => [
    'bg' => 'secondary',
    'title' => 'Pesanan Dibatalkan',
    'count' => $order_dibatalkan,
    'satuan_count' => 'Total Pesanan',
    'total_count' => $total_order,
    'href' => '?tampil_data&tb=pesanan_dibatalkan',
  ],
  'pesanan-sukses' => [
    'bg' => 'success',
    'title' => 'Pengiriman Sukses',
    'count' => $rorder[100],
    'satuan_count' => 'Pesanan Aktif',
    'total_count' => $rorder[0] + $order_inprogres + $rorder[100],
    'href' => '?tampil_data&tb=pesanan_sukses',
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
    'href' => '?tampil_data&tb=pesanan_bayar--' . $status_bayar,
    'total_count' => $order_aktif,
    'satuan_count' => 'Pesanan Aktif',
  ];
}






















# ============================================================
# FINAL UI
# ============================================================
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

      $konten = "
        <h5 class='card-title'>$v[title]</h5>
        <div class='card-text'><span class='display-4'>$v[count]</span> $of_total $satuan_count</div>
        $sub_title      
      ";

      if (isset($v['href'])) {
        $konten = "<a href='$v[href]' class='hover link-detail'>$konten</a>";
      }
      $stat_groups .= "
        <div class='col-md-6 col-xl-3 mb-2'>
          <div class='card'>
            <div class='card-body bg-$bg text-white'>
              $konten
            </div>
          </div>
        </div>  
      ";
    } elseif ($stat == 'tambah-reseller') {
      $stat_groups .= "
        <form action='?proses_tambah_user_reseller' method=post class='col-md-6 mb-2'>
          <div class=mb-1>Whatsapp calon reseller: <i class=hover onclick='alert(`Jika ada calon reseller baru via whatsapp, segera daftarkan user awal-nya agar tidak perlu lagi verifikasi akun whatsapp untuk reseller baru tersebut.`)'>$img_help</i></div>
          <div class='d-flex gap-2'>
            <input 
            style='font-size:30px; letter-spacing: 5px;'
            class='d-block form-control consolas' 
            id=whatsapp_calon_reseller 
            name=whatsapp_calon_reseller 
            placeholder='628...' 
            required 
            minlength=11 
            maxlength=14 
            />
            <button class='d-block btn btn-success' name=btn_tambah_user>Tambah User</button>
          </div>  
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

include 'review_order.php';
?>
<script>
  $(function() {

    $("#whatsapp_calon_reseller").keyup(function() {
      let val = $(this).val().replace(/[^0-9]/g, ""); // Hanya angka
      if (val.startsWith("08")) {
        val = "628" + val.substring(2);
      } else if (!val.startsWith("628") && val.length >= 4) {
        val = "";
      }
      $(this).val(val);
    });

  });
</script>