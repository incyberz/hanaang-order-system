<?php
if ($order['qc']) {
  # ============================================================
  # SHOW CEKLIS QC DAN KEBERANGKATAN KURIR
  # ============================================================
  $qc_aksi = $order['tanggal_qc'] ? "
    <div><b>QC at</b>: <i class=green>" . date('d M, Y, H:i', strtotime($order['tanggal_qc'])) . "</i> $img_check</div>
  
  " : "
    <div><b>QC Status</b>: <i class=red>Belum QC</i></div>
    <a class='btn btn-primary w-100 mt-2' href=?follow_up&tujuan=qc&qc=$order[qc]>Follow Up QC</a>
    <button class='btn btn-success w-100 mt-2' name=btn_set_qc_ok value=$id_order>Set QC Produk OK</button>      
  ";

  $kurir_aksi = $order['tanggal_kirim'] ? "
  <div><b>Kirim at</b>: <i class=green>" . date('d M, Y, H:i', strtotime($order['tanggal_kirim'])) . "</i> $img_check</div>

" : "
  <div><b>Kurir Status</b>: <i class=red>Belum Berangkat</i></div>
  <a class='btn btn-primary w-100 mt-2' href=?follow_up&tujuan=kurir&kurir=$order[kurir]>Follow Up QC</a>
  <button class='btn btn-success w-100 mt-2' name=btn_set_kurir_berangkat value=$id_order>Kurir Sudah Berangkat</button>
";

  $blok_pengiriman = "
  <div class='card p-3 mb-3'>
    <div><b>QC</b>: $order[petugas_qc]</div>
    $qc_aksi
  </div>
  <div class='card p-3'>
    <div><b>Kurir</b>: $order[petugas_kurir]</div>
    $kurir_aksi
  </div>
";



  # ============================================================
  # SET PETUGAS QC DAN KURIR
  # ============================================================
} else {
  $s = "SELECT * FROM tb_petugas a JOIN tb_user b ON a.username=b.username";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $pilih_petugas = [];
  $pilih_petugas['qc'] = '';
  $pilih_petugas['kurir'] = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $id = $d['username'];
    $panggilan = ucwords(strtolower($d['panggilan']));
    $pilih_petugas['qc'] .= "
      <div class='text-center'>
        <label class='hover label-petugas label--qc' for=qc--$id id=label--qc--$id>
          <input class='hideit radio-petugas radio--qc' type=radio name=qc id=qc--$id required value=$id>
          <img class='profil profil-petugas profil-petugas-qc' />
          <div class='nama-petugas'>$panggilan</div>
          <div class='status-petugas'>$d[role]</div>
        </label>      
      </div>      
    ";
    $pilih_petugas['kurir'] .= "
      <div class='text-center'>
        <label class='hover label-petugas label--kurir' for=kurir--$id  id=label--kurir--$id>
          <input class='hideit radio-petugas radio--kurir' type=radio name=kurir id=kurir--$id required value=$id>
          <img class='profil profil-petugas profil-petugas-kurir' />
          <div class='nama-petugas'>$panggilan</div>
          <div class='status-petugas'>$d[role]</div>
        </label>      
      </div>      
    ";
  }


  $blok_pengiriman = "
    <div class='my-3 card p-2'>
      <div class='text-center'>Petugas QC</div>
      <div class='d-flex flex-wrap gap-3 justify-content-center my-3'>
        $pilih_petugas[qc]
      </div>
    </div>
    <div class='my-3 card p-2'>
      <div class='text-center'>Kurir</div>
      <div class='d-flex flex-wrap gap-3 justify-content-center my-3'>
        $pilih_petugas[kurir]
      </div>
    </div>
    <div id=blok-pilih-dahulu>
      <span class='btn btn-secondary w-100' >Pilih Dahulu Petugas Pengiriman</span>
    </div>
    <div id=blok-set-petugas class=hideit>
      <button class='btn btn-primary w-100' name=btn_set_petugas_pengiriman value=$id_order>Set Petugas Pengiriman</button>
    </div>
  ";
?>
  <script>
    $(function() {
      let qc = null;
      let kurir = null;
      $('.radio-petugas').click(function() {
        let tid = $(this).prop('id');
        let rid = tid.split('--');
        let field = rid[0];
        let id = rid[1];
        $('.label--' + field).removeClass('label-petugas-selected');
        $('#label--' + field + '--' + id).addClass('label-petugas-selected');
        if (field == 'qc') qc = id;
        if (field == 'kurir') kurir = id;
        if (qc && kurir) {
          $('#blok-pilih-dahulu').slideUp();
          $('#blok-set-petugas').slideDown();
        } else {
          $('#blok-set-petugas').slideUp();
          $('#blok-pilih-dahulu').slideDown();
        }

      })

    })
  </script>
<?php } ?>