<?php
include "$dotdot/includes/key2kolom.php";
include 'includes/script_btn_aksi.php';
include 'order_detail-process.php';
include 'rstatus_order.php';

$null = '<i class=abu>null</i>';

$id_order = $_GET['id_order'] ?? null;
if (!$id_order || !$username)  jsurl('?');



set_title('Order Detail');

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
(SELECT SUM(qty) FROM tb_order_items WHERE id_order=a.id) sum_qty,
a.*

FROM tb_order a WHERE id='$id_order'
AND a.username = '$username' -- order milik sendiri 
AND a.delete_at is null -- belum dihapus
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q) > 1) stop('Multiple Data Order pada order detail.');
if (!mysqli_num_rows($q)) stop('Data Order Null pada order detail.');
$order = mysqli_fetch_assoc($q);
$status = $order['status'];

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
(SELECT nama FROM tb_produk WHERE id=a.id_produk) nama_produk,
(
  SELECT harga FROM tb_harga_reseller 
  WHERE id_produk=a.id_produk -- produk yang ini
  AND username='$username' -- untuk reseller ini
  ) harga_satuan,
a.qty as jumlah_pesan,
a.* 
FROM tb_order_items a
WHERE a.id_order=$id_order";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  $th = '';
  $total_rp = 0;
  $total_item = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $td = '';
    $id = $d['id'];


    $d['jumlah_rp'] = $d['harga_satuan'] * $d['jumlah_pesan'];

    $total_item += $d['jumlah_pesan'];
    $total_rp += $d['jumlah_rp'];

    foreach ($d as $key => $value) {
      $th_class = '';
      if (
        $key == 'id'
        || $key == 'date_created'
        || $key == 'id_order'
        || $key == 'id_produk'
        || $key == 'qty'
      ) {
        continue;
      } elseif ($key == 'jumlah_pesan' || $key == 'harga_satuan' || $key == 'jumlah_rp') {
        $value = number_format($value);
        $value = "<div class='consolas text-end'>$value</div>";
        $th_class = 'text-end';
      } elseif ($key == 'nama_produk') {

        $btn_delete = "<i class=hover onclick='alert(`Tidak bisa hapus item karena sudah ditangani Petugas.\n\nSilahkan Hapus Order untuk menghapus Order dan item-itemnya.`)'>$img_delete_disabled</i>";
        if (!$status) {
          $btn_delete = "<button class=transparan onclick='return confirm(`Delete Item ini?`)' name= btn_delete_item value='$id'>$img_delete</button>";
        } elseif ($status >= 3) { // tiba di tujuan
          $btn_delete = $img_check;
        }

        $value = "
          <div class='d-flex gap-2 justify-content-between'>
            <div>$value</div>
            <div>$btn_delete</div>
          </div>
        ";
      }
      if ($i == 1) {
        $kolom = key2kolom($key);
        $th .= "<th class='$th_class'>$kolom</th>";
      }


      $td .= "<td>$value</td>";
    }
    $tr .= "
      <tr>
        <td>$i</td>
        $td
      </tr>
    ";
  }

  // echo '<pre>';
  // print_r($order);
  // echo '<b style=color:red>Developer SEDANG DEBUGING: exit(true)</b></pre>';
  // exit;

  $total_rp_show = number_format($total_rp);
  if ($order['status'] === null) {
    if ($order['sum_qty']) {
      $status_show = show_status_order('');
    } else {
      $status_show = show_status_order(null);
    }
  } else {
    $nama_status = $rstatus_order[$order['status']]['nama_status'];
    $bg_status = $rstatus_order[$order['status']]['bg'];
    $status_show = show_status_order("$order[status] - $nama_status", $bg_status);
  }

  $tanggal = date('d-M-Y, H:i', strtotime($order['tanggal']));

  echo "
    <form method=post>
      <h2 class='text-center text-lg-start my-3'>Order Detail</h2>
      <div class='row'>
        <div class='col-md-6 col-xl-3'>
          <div class='border-top p-1 py-2'>
            <b>Tanggal</b>: $tanggal
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
      </div>
      <h3 class='text-center text-lg-start mb-3 mt-4'>Order Items</h3>
      <table class='table gradasi-toska'>
        <thead class='bg-info text-white'>
          <th>No</th>
          $th
        </thead>
        $tr
        <tfoot class='gradasi-kuning'>
          <tr>
            <td colspan=4 class=text-end>Total Bayar</td>
            <td class='consolas text-end f24'>$total_rp_show</td>
          </tr>
        </tfoot>

      </table>
    </form>

    $order_detail
  ";

  include 'info_pembayaran.php';
} else {
  alert("Tidak ada detail items dari order id [$id_order]");
  jsurl("?add_order", 3000);
}
