<?php
$detik = strtotime('now') - strtotime($order['tanggal_kirim']);

$jam = sprintf('%02d', intval($detik / 3660));
$mnt = sprintf('%02d', intval($detik / 60) % 60);
$dtk = sprintf('%02d', $detik % 60);

// $sisa_bayar = 0;
if ($sisa_bayar) {

  include 'rmetode_bayar.php';
  $radio_metode_bayar = '';
  foreach ($rmetode_bayar as $metode => $v) {
    $radio_metode_bayar .= "
      <label class='d-block my-1'>
        <input required name=metode_bayar id=metode_bayar--$metode type=radio value='$metode'> $metode - $v[nama_metode] 
      </label> 
    ";
  }

  $info_pelunasan = "
    Saya sudah menerima Sisa Pembayaran / Bukti Bayar dengan nominal: 
    <div class='my-1 f30 darkred'>Rp $sisa_bayar_show,-</div>
    <div class='my-2'>$radio_metode_bayar</div>
  ";
} else {
  $info_pelunasan = "
    Saya sudah menginformasikan ke reseller bahwa <u class=green>Pembayaran Sudah Lunas</u> $img_check
  ";
}

if ($order['tanggal_terima']) {
  $is_done_konfirmasi = $img_check;
  $sisa_bayar_show = number_format($sisa_bayar);

  $penerima = $order['penerima'] ? "$order[penerima] (orang serumah)" : "$user_reseller[nama] (reseller)";
  $blok_konfirmasi = "
  <div>
    <div><b>Status Pengiriman</b>: <span class=green>Sukses Terkirim</span> $img_check</div>
    <div><b>Penerima</b>: $penerima</div>
    <div><b>Terima Sisa Bayar</b>: Rp $sisa_bayar_show,-</div>
    <div><b>Metode Bayar</b>: $order[metode_pembayaran]</div>
  </div>

  ";
} else {
  $blok_konfirmasi = "
    <div>
      <div><b>Status</b>: <span class=red>Sedang Diperjalanan</span> $img_loading</div>
      <div><b>Jarak</b>: $user_reseller[jarak]km</div>
      <div><b>Tujuan</b>: $alamat_kirim</div>
    </div>
    <div class='card my-2 p-3 text-center'>
      <div class='d-flex justify-content-center consolas f30'>
        <div id=jam>$jam</div>
        <div>:</div>
        <div id=mnt>$mnt</div>
        <div>:</div>
        <div id=dtk>$dtk</div>
      </div>
    </div>
    <a class='btn btn-primary w-100 mt-2' href=?follow_up&tujuan=kurir&kurir=$order[kurir]>Follow Up Kurir</a>
    <a class='btn btn-primary w-100 mt-2' href=?follow_up&tujuan=reseller&username=$user_reseller[username]>Follow Up Reseller</a>
    <span class='btn btn-success w-100 mt-2 btn-aksi' id=blok-set-barang-tiba--toggle>Set Barang Sudah Tiba</span>
    <div class='hideit' id=blok-set-barang-tiba>
      <div class='my-2 card p-3 gradasi-kuning' id=blok-set-barang-tiba>
        <div>Diterima Oleh</div>
        <label class=hover>
          <input class=radio-penerima required type=radio name=penerima value=''> $user_reseller[nama] (reseller)
        </label>
        <label class=hover>
          <input class=radio-penerima required type=radio name=penerima value='orang-serumah'> Orang lain:
        </label>
    
        <div id=orang-serumah class=hideit>
          <input class='form-control my-2' name=nama_penerima id=nama_penerima minlength=3 maxlength=30 placeholder='Penerima selain reseller...'>
        </div>
    
        <label class='hover border-top pt-2 d-flex gap-2 mt-3'>
          <div class=pt-2><input required type=checkbox ></div> 
          <div>Saya sudah menghitung dan menyerahkan pesanan:<div class=darkblue><i>Total Item</i> <span class=f24>$total_item</span> cup</div></div> 
        </label>
    
        <label class='hover border-top pt-2 d-flex gap-2 mt-3'>
          <div class=pt-2><input required type=checkbox ></div> 
          <div>Saya sudah menyimpan dan menatanya pada Lemari Pendingin</div> 
        </label>
    
        <label class='hover border-top pt-2 d-flex gap-2 mt-3'>
          <div class=pt-2><input required type=checkbox ></div> 
          <div>$info_pelunasan</div> 
        </label>
    
        <button class='btn btn-success w-100 mt-3' name=btn_set_barang_diterima value=$id_order>Barang Sudah Diterima</button>
      </div>
    </div>
  ";
}

?>
<script>
  $(function() {
    let jam = $('#jam').text();
    let mnt = $('#mnt').text();
    let dtk = $('#dtk').text();

    setInterval(() => {
      if (dtk >= 59) {
        dtk = 0;
        mnt++;
        if (mnt >= 59) {
          mnt = 0;
          jam++;
        }
      } else {
        dtk++;
      }
      $('#jam').text(jam.toString().padStart(2, '0'));
      $('#mnt').text(mnt.toString().padStart(2, '0'));
      $('#dtk').text(dtk.toString().padStart(2, '0'));
    }, 1000);

    $('.radio-penerima').click(function() {
      if ($(this).val() == 'orang-serumah') {
        $('#orang-serumah').slideDown();
        $('#nama_penerima').prop('required', 1);
      } else {
        $('#orang-serumah').slideUp();
        $('#nama_penerima').prop('required', 0);
        $('#nama_penerima').val('');
      }
    })
  })
</script>