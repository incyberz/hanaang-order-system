<?php
if ($role == 'admin' and $get_username) {
  set_title("Manage Order ($user_reseller[nama])");

  $total_bayar = 750000; // ZZZ
  $total_bayar_show = number_format($total_bayar);

  echo "
    <div class='card gradasi-kuning'>
      <div class='card-header bg-primary text-white'>Manage Order</div>
      <div class='card-body'>
        <span class='btn btn-primary btn-sm btn-aksi' id=blok-metode-bayar-$d[id]--toggle>Metode Pembayaran</span>
        <div id=blok-metode-bayar-$d[id] class='hideit'>
          <div class='card gradasi-kuning p-2 mt-3'>
            <div class=mb-4><b>Total Bayar</b>: Rp $total_bayar_show,-</div>
            <div class='f14 mb-1'>Uang Muka atau Pelunasan</div>
            <input class='form-control form-control-lg text-center f24 consolas' type=number min=0 max=$total_bayar step=1000 required>
            <div class='f12 miring mt-1 mb-3'>lihat pada Bukti Bayar dari reseller, <br>masukan 0 jika konsinyasi</div>
            <button class='btn btn-primary btn-sm' name=btn_pembayaran value=$d[id] onclick='return confirm(`Confirm Pembayaran?`)'>Submit Pembayaran</button>
          </div>
        </div>

      </div>
    </div>
  ";

  echo '<pre>';
  print_r($user_reseller);
  echo '</pre>';
} else {
  stop("Invalid role or user reseller.");
}
