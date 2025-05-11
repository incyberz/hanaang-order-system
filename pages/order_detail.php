<?php
include "$dotdot/includes/key2kolom.php";
include 'includes/script_btn_aksi.php';
include 'includes/hari_tanggal.php';
include 'order-process.php';
include 'rstatus_order.php';

$null = '<i class=abu>null</i>';

$id_order = $_GET['id_order'] ?? null;
if (!$id_order || !$username)  jsurl('?');

$get_username = $_GET['username'] ?? null;
$user_reseller = [];
if ($role == 'admin') {
  if (!$get_username) kosong('get_username');
  $s = "SELECT * FROM tb_user a 
  JOIN tb_reseller b ON a.username=b.username 
  WHERE a.username = '$get_username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $user_reseller = mysqli_fetch_assoc($q);
}


set_title('Order Detail');

# ============================================================
# ORDER DETAIL
# ============================================================
$sql_username = $role == 'admin' ? '1' : "a.username = '$username' -- order milik sendiri ";
$s = "SELECT 
a.id as order_id,
a.tanggal_order,
(SELECT CONCAT(status,' - ',nama_status) FROM tb_status_order WHERE status=a.status_order) status_pemesanan, 
a.tanggal_lunas,
a.tanggal_cek,
a.tanggal_kirim,
a.tanggal_terima,
(SELECT nama FROM tb_user WHERE username=a.petugas) petugas_admin, 
(SELECT nama FROM tb_user WHERE username=a.qc) petugas_qc, 
(SELECT nama FROM tb_user WHERE username=a.kurir) petugas_kurir,
(SELECT SUM(qty) FROM tb_order_items WHERE id_order=a.id) sum_qty,
(SELECT nama_status FROM tb_status_bayar WHERE status=a.status_bayar) status_pembayaran,
(SELECT nama_metode FROM tb_metode_bayar WHERE metode=a.metode_bayar) metode_pembayaran,
a.*

FROM tb_order a WHERE id='$id_order'
AND $sql_username
AND a.delete_at is null -- belum dihapus
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q) > 1) stop('Multiple Data Order pada order detail.');
if (!mysqli_num_rows($q)) stop('Data Order Null pada order detail.');
$order = mysqli_fetch_assoc($q);
$status_order = $order['status_order'];

foreach ($order as $key => $value) {
  if (
    $key == 'id'
    || $key == 'username'
    || $key == 'status_order'
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
$order_detail = "
  <div class='hover text-primary btn-aksi my-3' id=order-detail--toggle>Show Order Details</div>
  <div class='hideit my-3' id=order-detail>
    <h2 class='text-center text-md-start'>Order Details</h2>
    <table class=table>
      $tr
    </table>
  </div>
";












# ============================================================
# ORDER ITEMS 
# ============================================================
$s = "SELECT 
b.nama as nama_produk,
b.harga_beli,
b.tanggal_produksi,
(
  SELECT harga FROM tb_harga_fixed 
  WHERE id_produk=a.id_produk -- produk yang ini
  AND username='$username' -- untuk reseller ini
  ) harga_fixed,
a.qty as jumlah_pesan,
a.* 
FROM tb_order_items a 
JOIN tb_produk b ON a.id_produk=b.id 
WHERE a.id_order=$id_order 
ORDER BY b.tanggal_produksi DESC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  $i = 0;
  $th = '';
  $tr = '';
  $div = '';
  $total_bayar = 0;
  $total_item = 0;
  $show = [];
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $td = '';
    $id = $d['id'];

    if ($d['harga_fixed']) {
      $d['jumlah_rp'] = $d['harga_fixed'] * $d['jumlah_pesan'];
      unset($d['harga']);
    } else {
      unset($d['harga_fixed']);
      # ============================================================
      # PENCARIAN HARGA BERDASARKAN JARAK
      # ============================================================
      $jarak = $role ? $user_reseller['jarak'] : $user['jarak'];
      $s2 = "SELECT * FROM tb_rule 
      WHERE max_jarak >= $jarak 
      AND min_order <= $d[jumlah_pesan] 
      ORDER BY min_order DESC 
      LIMIT 1
      ";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      if (mysqli_num_rows($q2) != 1) stop('Invalid num_rows for tb_rule');
      while ($d2 = mysqli_fetch_assoc($q2)) {
        $d['harga'] = round($d['harga_beli'] * $d2['persen_up'] / 10000) * 100;
      }

      $d['jumlah_rp'] = $d['harga'] * $d['jumlah_pesan'];
    }

    $total_item += $d['jumlah_pesan'];
    $total_bayar += $d['jumlah_rp'];

    foreach ($d as $key => $value) {
      $th_class = '';
      if (
        $key == 'id'
        || $key == 'date_created'
        || $key == 'id_order'
        || $key == 'id_produk'
        || $key == 'qty'
        || $key == 'harga_beli'
        || $key == 'tanggal_produksi'
      ) {
        continue;
      } elseif ($key == 'jumlah_pesan' || $key == 'harga_fixed' || $key == 'jumlah_rp' || $key == 'harga') {
        $value = number_format($value);
        $value = "<div class='consolas text-end'>$value</div>";
        $th_class = 'text-end';
      } elseif ($key == 'nama_produk') {

        $btn_delete = '&nbsp;';
        if (!$role) {
          $btn_delete = "<i class=hover onclick='alert(`Tidak bisa hapus item karena sudah ditangani Petugas.\n\nSilahkan Hapus Order untuk menghapus Order dan item-itemnya.`)'>$img_delete_disabled</i>";
          if (!$status_order) {
            $btn_delete = "<button class=transparan onclick='return confirm(`Delete Item ini?`)' name= btn_delete_item value='$id'>$img_delete</button>";
          } elseif ($status_order >= 3) { // tiba di tujuan
            $btn_delete = $img_check;
          }
        }

        $tanggal = tanggal($d['tanggal_produksi']);
        $value = "
          <div class='d-flex gap-2 justify-content-between'>
            <div>
              $value
              <div class='f12 abu'><b>Produksi</b>: $tanggal</div>
            </div>
            <div>$btn_delete</div>
          </div>
        ";
      }
      if ($i == 1) {
        $kolom = key2kolom($key);
        $th .= "<th class='$th_class'>$kolom</th>";
      }


      $show[$key] = $value;
      $td .= "<td>$value</td>";
    }

    # ============================================================
    # FINAL TR
    # ============================================================
    $tr .= "
      <tr>
        <td>$i</td>
        $td
      </tr>
    ";

    # ============================================================
    # FINAL DIV
    # ============================================================
    $div .= "
      <div class='row-data border-top gradasi-toska py-3 px-2' id=row-data--$id>
        <div class=f12>$i</div>
        <div class='bold darkblue'>$show[nama_produk]</div>
        <div class='d-flex justify-content-between border-top py-1'>
          <div><b>Jumlah Pesan</b>:</div> 
          $show[jumlah_pesan]
        </div>
        <div class='d-flex justify-content-between border-top py-1'>
          <div><b>Harga</b>:</div> 
          $show[harga_fixed]
        </div>
        <div class='d-flex justify-content-between border-top py-1'>
          <div><b>Jumlah Rp</b>:</div> 
          $show[jumlah_rp]
        </div>
      </div>
    ";
  }

  // echo '<pre>';
  // print_r($order);
  // echo '<b style=color:red>Developer SEDANG DEBUGING: exit(true)</b></pre>';
  // exit;

  $total_bayar_show = number_format($total_bayar);
  if ($order['status_order'] === null) {
    if ($order['sum_qty']) {
      $status_show = show_status_order('');
    } else {
      $status_show = show_status_order(null);
    }
  } else {
    $nama_status = $rstatus_order[$order['status_order']]['nama_status'];
    $bg_status = $rstatus_order[$order['status_order']]['bg'];
    $status_show = show_status_order("$order[status_order] - $nama_status", $bg_status);
  }

  $tanggal_order = tanggal($order['tanggal_order']);
  if ($role) {
    $reseller = $user_reseller['nama'];
    $jarak = $user_reseller['jarak'];
    $alamat_kirim = $order['alamat_kirim'] ?? $user_reseller['alamat_lengkap'];
  } else {
    $reseller = $user['nama'];
    $jarak = $user['jarak'];
    $alamat_kirim = $order['alamat_kirim'] ?? $user['alamat_lengkap'];
  }

  $alamat_kirim = str_replace('Rt ', 'RT ', ucwords(strtolower($alamat_kirim)));

  echo "
    <form method=post>
      <h2 class='text-center text-lg-start my-3'>Order Detail</h2>
      <div class='row'>
        <div class='col-md-6 col-xl-3'>
          <div class='border-top p-1 py-2'>
            <b>Tanggal</b>: $tanggal_order
          </div>
        </div>
        <div class='col-md-6 col-xl-3'>
          <div class='border-top p-1 py-2'>
            <b>Status Order</b>: $status_show
          </div>
        </div>
        <div class='col-md-6 col-xl-3'>
          <div class='border-top p-1 py-2'>
            <b>Petugas QC</b>: $order[petugas_qc]
            <div class='zzz hideit'>Ajukan Komplain Barang ke QC</div>
          </div>
        </div>
        <div class='col-md-6 col-xl-3'>
          <div class='border-top p-1 py-2'>
            <b>Petugas Kurir</b>: $order[petugas_kurir]
            <div class='zzz hideit'>Tanya Posisi Pengiriman</div>
          </div>
        </div>
        <div class='col-md-6 col-xl-3'>
          <div class='border-top p-1 py-2'>
            <b>Reseller</b>: $reseller
          </div>
        </div>
        <div class='col-md-6 col-xl-6'>
          <div class='border-top p-1 py-2'>
            <b>Alamat Kirim</b>: $alamat_kirim
          </div>
        </div>
        <div class='col-md-6 col-xl-3'>
          <div class='border-top p-1 py-2'>
            <b>Jarak</b>: $jarak km
          </div>
        </div>
      </div>
      <h3 class='text-center text-lg-start mb-3 mt-4'>Order Items</h3>

      <div class=d-md-none>
        $div
        <div class='text-center gradasi-kuning px-2 py-3'>
          <div>Total Bayar</div>
          <div class='f24'>$total_bayar_show</div>
        </div>
      </div>

      <div class='d-none d-md-block'>
        <table class='table gradasi-toska'>
          <thead class='bg-info text-white'>
            <th>No</th>
            $th
          </thead>
          $tr
          <tfoot class='gradasi-kuning'>
            <tr>
              <td colspan=4 class=text-end>Total Bayar</td>
              <td class='consolas text-end f24'>$total_bayar_show</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </form>

    $order_detail
  ";

  include $role ? 'manage_order.php' : 'info_pembayaran.php';
} else {
  alert("Tidak ada detail items dari order id [$id_order]");
  jsurl("?add_order", 3000);
}
