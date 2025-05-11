<?php
if ($order['status_bayar'] != 'BB') {

  $dp_show = number_format($order['dp']);
  $sisa_bayar = $total_bayar - $order['dp'];
  $sisa_bayar_show = number_format($sisa_bayar);
  $tmp_sisa_bayar = number_format($order['tmp_sisa_bayar']);

  $bg_sisa_bayar = $sisa_bayar == $order['tmp_sisa_bayar'] ? 'hijau' : 'merah';
  $is_done_pembayaran = $sisa_bayar ? $img_warning : $img_check;

  $terima_sisa_bayar = '';
  if ($sisa_bayar and $sisa_bayar == $order['tmp_sisa_bayar']) {
    $is_done_pembayaran = $img_check;
    $terima_sisa_bayar = "
      <div class='d-flex justify-content-between gap-2'>
        <div>Terima Sisa Bayar</div>
        <div class='consolas kanan'>$tmp_sisa_bayar</div>
      </div>
    ";
    $sisa_bayar_show = '0';
  }


  # ============================================================
  # PEMBAYARAN TAHAP 1 (DP)
  # ============================================================
  $blok_pembayaran = "
    <h4 class='text-center f22 border-bottom pb-2'>$order[status_pembayaran]</h4>
    <div class='d-flex justify-content-between gap-2'><div>Total Bayar</div><div class='consolas kanan'>$total_bayar_show</div></div>
    <div class='d-flex justify-content-between gap-2'><div>Uang Muka</div><div class='consolas kanan'>$dp_show</div></div>
    $terima_sisa_bayar
    <div 
      class='d-flex justify-content-between gap-2 py-2 my-2 gradasi-$bg_sisa_bayar bold' 
      style='margin: 10px -15px 0 -15px; padding: 15px'
      >
      <div>Sisa Bayar</div>
      <div class='consolas kanan'>$sisa_bayar_show</div>
    </div>
    <div class='d-flex justify-content-between gap-2'><div>Metode Bayar</div><div class='consolas kanan'>$order[metode_pembayaran]</div></div>
  ";

  # ============================================================
  # PEMBAYARAN TAHAP 2 (PELUNASAN) SETELAH KURIR KEMBALI
  # ============================================================
  $blok_pembayaran .= '';
  if ($order['tanggal_terima'] and $order['status_bayar'] != 'SB') {
    $blok_pembayaran .= "
      <div class='mt-5'>
        <h4>Pelunasan Pembayaran</h4>
        <div>Saldo Masuk $order[metode_pembayaran] Rp</div>
        <div class='consolas kanan'>
          <input type=number required class='form-control form-control-lg text-center my-2' name=tmp_sisa_bayar id=tmp_sisa_bayar>
          <div id=set_nominal class='hover text-primary tengah my-2'>
            Set Nominal <span id=sisa_bayar>$sisa_bayar</span>
          </div>
          <button class='btn btn-primary w-100' name=btn_set_lunas value=$order[id]>Set Lunas</button>
        </div>
      </div>
    ";
  } elseif ($order['tanggal_terima'] and $order['status_bayar'] == 'SB') {
    if ($sisa_bayar == $order['tmp_sisa_bayar']) {
      $is_done_pembayaran = $img_check;
      $bg_sisa_bayar = 'hijau';
    }
  }


  # ============================================================
  # MANAGE PENGIRIMAN
  # ============================================================
  include 'manage_order-pengiriman.php';
} else {

  include 'rmetode_bayar.php';
  $radio_metode_bayar = '';
  foreach ($rmetode_bayar as $metode => $v) {
    $radio_metode_bayar .= "
      <label class='d-block my-1'>
        <input required name=metode_bayar id=metode_bayar--$metode type=radio value='$metode'> $metode - $v[nama_metode] 
      </label> 
    ";
  }

  # ============================================================
  # BLOK BELUM PEMBAYARAN
  # ============================================================
  $blok_pembayaran = "
    <div class=mb-3><b>Total Bayar</b>: Rp $total_bayar_show,- <span id=total_bayar class=hideit>$total_bayar</span></div>
    <div class='f14 mb-2'>Uang Muka atau Pelunasan</div>
    <input name=total_bayar value='$total_bayar' type=hidden>
    <input class='form-control form-control-lg text-center consolas' type=number min=0 max=$total_bayar step=1000 required name=nominal_bayar id=nominal_bayar>
    <div class=' miring my-2 mb-3'>lihat pada Bukti Bayar dari reseller, masukan 0 jika konsinyasi</div>


    <div class=mb-1 id=info_metode_bayar>Pelunasan dilakukan via:</div>
    <div class=mb-3>$radio_metode_bayar</div>
    <button class='btn btn-primary btn-sm' name=btn_set_metode_bayar value=$order[id] onclick='return confirm(`Confirm Pembayaran?`)'>Set Metode Bayar</button>
  ";
}
?>
<script>
  $(function() {
    let total_bayar = parseInt($('#total_bayar').text());
    $('#nominal_bayar').keyup(function() {
      let val = $(this).val();
      if (val >= total_bayar) {
        $(this).val(total_bayar);
        $('#info_metode_bayar').text('Pelunasan dilakukan via:');
        $('#metode_bayar--COD').prop('disabled', 1);
        $('#metode_bayar--COD').prop('checked', 0);
      } else {
        let sisa_bayar = total_bayar - val;
        $('#info_metode_bayar').text(`Sisa pembayaran Rp ${sisa_bayar},- via:`);
        $('#metode_bayar--COD').prop('disabled', 0);

      }
    })

    $('#set_nominal').click(function() {
      $('#tmp_sisa_bayar').val(
        $('#sisa_bayar').text()
      );
      $(this).slideUp();
    })
  })
</script>