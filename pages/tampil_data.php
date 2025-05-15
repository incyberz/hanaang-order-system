<?php
include 'includes/key2kolom.php';
include 'rstatus_bayar.php';

$tb = $_GET['tb'] ?? kosong('tb');
$fields = $_GET['fields'] ?? '*';
$get_hide_fields = $_GET['hide_fields'] ?? null;
$JOINS = $_GET['joins'] ?? '';
$WHERE = $_GET['wheres'] ?? '';
$ORDER_BY = $_GET['order_by'] ?? '';
$title = $_GET['title'] ?? null;
$sub_title = $_GET['sub_title'] ?? null;



















# ============================================================
# SKIP FIELDS
# ============================================================
$rskips = [];
$rskips = [
  'order' => [
    'username',
    'kecamatan',
    'kabupaten',
    'alamat_lengkap',
  ],
];
$rskip = $rskips[$tb] ?? [];




















# ============================================================
# GLOBAL SELECT
# ============================================================
if ($tb == 'ZZZ') {
  # ============================================================
  # DEFAULT GLOBAL SELECT
  # ============================================================
  $s = "SELECT 
  $fields 
  FROM tb_$tb a 
  $JOINS
  $WHERE 
  $ORDER_BY
  ";
} elseif ($tb == 'user_awal') {
  # ============================================================
  # USER AWAL
  # ============================================================
  $sub_title = 'Berikut adalah user akun yang belum di-cek oleh calon reseller, mereka belum melakukan Pendaftaran Reseller via website';
  $s = "SELECT 
    username,
    nama,
    whatsapp 
  FROM tb_user WHERE active_status is null AND created_by is not null";
} elseif ($tb == 'reseller_aktif') {
  $sub_title = 'Reseller Aktif adalah reseller yang status keaktifan true dan status whatsapp true';
  $s = "SELECT 
    a.username,
    nama,
    whatsapp,
    kecamatan,
    kabupaten,
    alamat_lengkap,
    jarak,
    foto_lokasi 
  FROM tb_reseller a JOIN tb_user b ON a.username=b.username WHERE b.active_status=1 AND b.whatsapp_status=1";
} elseif ($tb == 'unverified_whatsapp') {
  $sub_title = 'Reseller dengan Unverified Whatsapp adalah reseller yang melakukan Pendaftaran Online tanpa konfirmasi ke Admin terlebih dahulu.';
  $s = "SELECT 
    a.username,
    nama,
    whatsapp,
    kecamatan,
    kabupaten,
    alamat_lengkap,
    jarak,
    foto_lokasi 
  FROM tb_reseller a JOIN tb_user b ON a.username=b.username WHERE b.whatsapp_status is null";

  // revisi select
  $s = "SELECT 
    username,
    nama,
    whatsapp
  FROM tb_user WHERE whatsapp_status is null";



  # ============================================================
  # PESANAN | ORDER
  # ============================================================
} elseif (substr($tb, 0, 8) == 'pesanan_') {
  $fields_tambahan = '';
  $rskip = $rskips['order']; // skip fields - all jenis order
  $title = 'Tampil Semua Pesanan'; // default semua penanan tampil
  $sql_where = '1';
  if ($tb == 'pesanan_baru') {
    # ============================================================
    # PESANAN BARU
    # ============================================================
    $title = 'Pesanan Baru';
    $sub_title = 'Pesanan Baru adalah order dg status 0 dan status bayar juga 0. Artinya pembayaran belum jelas, apakah Konsinyasi, DP, atau Lunas. Admin harus segera memutuskan!';
    $sql_where = " a.status_order = 0 ";
  } elseif ($tb == 'pesanan_inprogress') {
    # ============================================================
    # PESANAN INPROGRESS
    # ============================================================
    $title = 'Pesanan Inprogress';
    $sub_title = 'Pesanan Inorder adalah order yang metode pembayarannya sudah jelas dan sedang proses pengiriman. Admin harus menunjuk siapa QC dan Kurir-nya!';
    $sql_where = "a.status_bayar!='BB' AND a.status_order > 0  AND a.status_order < 100";
    $fields_tambahan = "
      (
        SELECT q.nama FROM tb_petugas p 
        JOIN tb_user q ON p.username=q.username 
        WHERE p.username=a.qc) petugas_qc,
      (
        SELECT q.nama FROM tb_petugas p 
        JOIN tb_user q ON p.username=q.username 
        WHERE p.username=a.kurir) petugas_kurir,
      (
        SELECT CONCAT(status,' - ',nama_status) FROM tb_status_order 
        WHERE status=a.status_order) status_pemesanan,
      a.penerima,
    ";
  } elseif ($tb == 'pesanan_sukses') {
    # ============================================================
    # PESANAN SUKSES
    # ============================================================
    $title = 'Pesanan Sukses';
    $sub_title = 'Pesanan Sukses adalah pesanan dengan status 100 dan sudah diterima oleh reseller';
    $sql_where = "a.status_order = 100";
    $fields_tambahan = "a.penerima,";
  } elseif ($tb == 'pesanan_dibatalkan') {
    # ============================================================
    # PESANAN DIBATALKAN
    # ============================================================
    $title = 'Pesanan Dibatalkan';
    $sub_title = 'Pesanan Dibatalkan adalah pesanan yang: 1) tidak dibayar dalam 3 hari; 2) dipending oleh reseller; 3)  alasan lain';
    $sql_where = "a.status_order < 0";
    $fields_tambahan = "(
      SELECT CONCAT(status,' - ',nama_status) FROM tb_status_order 
      WHERE status=a.status_order) status_pemesanan,
      a.info_status,
    ";
  } elseif (substr($tb, 0, 15) == 'pesanan_bayar--') {
    # ============================================================
    # METODE BAYAR PEMESANAN
    # ============================================================
    $status_bayar = substr($tb, 15);
    $title = $rstatus_bayar[$status_bayar]['nama_status'] ?? stop("null index rstatus_bayar[nama_status]");
    $sub_title = $rstatus_bayar[$status_bayar]['keterangan'] ?? stop("null index rstatus_bayar[keterangan]");
    $sql_where = "a.status_order >= 0 AND a.status_bayar = '$status_bayar'";
  }

  # ============================================================
  # SELECT ORDER
  # ============================================================
  $s = "SELECT
  a.username,
  a.tanggal_order,
  c.nama as reseller,
  a.dp as uang_muka,
  a.alamat_kirim,
  b.kabupaten,
  b.kecamatan,
  b.alamat_lengkap,
  (SELECT SUM(qty)FROM tb_order_items WHERE id_order = a.id) qty_order,
  $fields_tambahan
  a.id

  FROM tb_order a 
  JOIN tb_reseller b ON a.username=b.username
  JOIN tb_user c ON b.username=c.username
  WHERE $sql_where 
  AND a.delete_at is null
  ORDER BY tanggal_order DESC";
} else {

  echo '<pre>';
  print_r($_GET);
  echo "<b style=color:red>Tampil Data untuk tabel [$tb] belum didefinisikan.</b></pre>";
  exit;
}
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$num_rows = mysqli_num_rows($q);







$tr = '';
$th = '';
if (mysqli_num_rows($q)) {
  $no = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $no++;

    // fill default alamat kirim dengan alamat biodata
    if (substr($tb, 0, 8) == 'pesanan_') {
      $d['alamat_kirim'] = $d['alamat_kirim'] ?? ucwords(strtolower("$d[alamat_lengkap], Kec $d[kecamatan], $d[kabupaten]"));
    }


    $td = '';
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'date_created'
        || $key == 'created_at'
        || $key == 'delete_at'
        || in_array($key, $rskip)
      ) {
        continue;
      } elseif ($key == 'tanggal_order') {
        $value = date('d-M-Y', strtotime($value)) . '<br><i class="f12 abu">' . date('H:i', strtotime($value)) . " - id. $d[id]</i>";
      } elseif ($key == 'reseller') {
        $value = "$value<br><i class='f12 abu'>$d[username]</i>";
      } else {
        $value = $value ? $value : $null;
      }

      if ($no == 1) {
        $kolom = key2kolom($key);
        $th .= "<th>$kolom</th>";
      }
      $td .= "<td>$value</td>";
    }

    # ============================================================
    # AKSI TIAP ROW
    # ============================================================
    $aksi = '-';
    // include 'tampil_data-aksi_tiap_row.php';
    if (isset($d['qty_order'])) {
      if ($d['qty_order']) { // hanya yang sudah jelas qty nya
        $aksi = "<a class='btn btn-sm btn-primary' href='?order_detail&id_order=$d[id]&username=$d[username]'>Manage</a>";
      } else {
        $aksi = '<span class="btn btn-secondary btn-sm" onclick="alert(`Reseller belum memasukan QTY Order.`)">Manage</span>';
      }
    } else {
      if (isset($d['kecamatan'])) { // Reseller Aktif
        $aksi = "<a class='btn btn-sm btn-secondary' href='#ZZZ'>Recek Whatsapp</a>";
      } else {
        $aksi = "<a class='btn btn-sm btn-primary' href='#ZZZ'>Cek Whatsapp</a>";
      }
    }

    # ============================================================
    # FINAL TR
    # ============================================================
    $tr .= "
      <tr>
        <td>$no</td>
        $td
        <td>$aksi</td>
      </tr>
    ";
  }
}


$Tb = ucwords(strtolower(str_replace('_', ' ', $tb)));
set_h2($title ? $title : 'Tampil Data ' . $Tb, $sub_title);

echo !$num_rows ? alert("Tidak ada data pada Tampil Data [ $title ] | <a href=?>Back to Home</a>") : "
  <form method=post>
    <table class='table'>
      <thead class='bg-dark text-white'>
        <th>No</th>
        $th
        <th>Aksi</th>
      </thead>
      $tr
    </table>
  </form>
";
