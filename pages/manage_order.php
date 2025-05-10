<?php
include 'manage_order-styles.php';
if ($role == 'admin' and $get_username and $user_reseller) {
  set_title("Manage Order ($user_reseller[nama])");

  $is_done_pembayaran = null;
  $is_done_pengiriman = null;
  $is_done_konfirmasi = null;
  $blok_pengiriman = '-';
  $blok_konfirmasi = '-';
  if ($order['status_bayar']) {
    $is_done_pembayaran = $img_check;

    $dp_show = number_format($order['dp']);
    $sisa_bayar_show = number_format($total_bayar - $order['dp']);

    $blok_pembayaran = "
      <h4 class='text-center f22 border-bottom pb-2'>$order[metode_bayar]</h4>
      <div class='d-flex justify-content-between gap-2'><div>Total Bayar</div><div class='consolas kanan'>$total_bayar_show</div></div>
      <div class='d-flex justify-content-between gap-2'><div>Uang Muka</div><div class='consolas kanan'>$dp_show</div></div>
      <div class='d-flex justify-content-between gap-2 py-2 gradasi-merah bold' style='margin: 10px -15px 0 -15px; padding: 15px'><div>Sisa Bayar</div><div class='consolas kanan'>$sisa_bayar_show</div></div>
    ";

    # ============================================================
    # MANAGE PENGIRIMAN
    # ============================================================
    include 'manage_order-pengiriman.php';
  } else {
    $blok_pembayaran = "
      <div class=mb-3><b>Total Bayar</b>: Rp $total_bayar_show,-</div>
      <div class='f14 mb-2'>Uang Muka atau Pelunasan</div>
      <input name=total_bayar value='$total_bayar' type=hidden>
      <input class='form-control form-control-lg text-center consolas' type=number min=0 max=$total_bayar step=1000 required name=nominal_bayar>

      <div class=' miring my-2 mb-3'>lihat pada Bukti Bayar dari reseller, masukan 0 jika konsinyasi</div>
      <button class='btn btn-primary btn-sm' name=btn_set_metode_bayar value=$order[id] onclick='return confirm(`Confirm Pembayaran?`)'>Set Metode Bayar</button>
    ";
  }

  echo "
    <form method=post class='card gradasi-kuning'>
      <div class='card-header bg-primary text-white'>Manage Order</div>
      <div class='card-body'>
        <div class=row>
          <div class=col-lg-4>
            <h3 class='text-center f24'>Metode Pembayaran $is_done_pembayaran</h3>
            <div class='card gradasi-toska p-3 mt-3'>
              $blok_pembayaran
            </div>
          </div>
          <div class=col-lg-4>
            <h3 class='text-center f24'>Pengiriman $is_done_pengiriman</h3>
            <div class='card gradasi-toska p-3 mt-3'>
              $blok_pengiriman
            </div>
          </div>
          <div class=col-lg-4>
            <h3 class='text-center f24'>Konfirmasi $is_done_konfirmasi</h3>
            <div class='card gradasi-toska p-3 mt-3'>
              $blok_konfirmasi
            </div>
          </div>
        </div>

      </div>
    </form>
  ";
} else {
  stop("Invalid role or user reseller.");
}
