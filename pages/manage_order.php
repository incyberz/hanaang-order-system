<?php
include 'manage_order-styles.php';
if ($role == 'admin' and $get_username and $user_reseller) {
  set_title("Manage Order ($user_reseller[nama])");

  # ============================================================
  # INISIALISASI
  # ============================================================
  $is_done_pembayaran = null;
  $is_done_pengiriman = null;
  $is_done_konfirmasi = null;
  $blok_pembayaran = '-';
  $blok_pengiriman = '-';
  $blok_konfirmasi = '-';

  # ============================================================
  # MANAGE PEMBAYARAN
  # ============================================================
  include 'manage_order-pembayaran.php';

  echo "
    <form method=post class='card gradasi-kuning'>
      <div class='card-header bg-primary text-white'>Manage Order</div>
      <div class='card-body'>
        <div class=row>
          <div class=col-lg-4>
            <h3 class='text-center f24'>Pembayaran $is_done_pembayaran</h3>
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
        <button class='btn btn-danger dev-mode my-5' name=btn_reset_order value=$order[id] onclick='return confirm(`Warning! Reset Order menyebabkan sebagian data order hilang.`)'>Reset Order [Dev Mode]</button>
      </div>
    </form>
  ";
} else {
  stop("Invalid role or user reseller.");
}

include 'realtime_update_order.php';
