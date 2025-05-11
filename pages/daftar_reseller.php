<?php
# ============================================================
# PROCESS
# ============================================================
if (isset($_POST['btn_daftar_reseller'])) {
  # ============================================================
  # ANTI SQL INJECTIONS
  # ============================================================
  foreach ($_POST as $k => $v) $_POST[$k] = strip_tags(str_replace('\'', '`', strtoupper($v)));


  # ============================================================
  # ADD | GET KECAMATAN 
  # ============================================================
  $kec_kab = "KEC $_POST[kecamatan] $_POST[kab_kota] $_POST[kabupaten]";
  $id_kec = $_POST['select_kecamatan'];
  if ($id_kec == 'NEW') {
    $s = "INSERT INTO tb_lokasi (kec_kab) VALUES ('$kec_kab')";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    sukses("Tambah kecamatan [ $_POST[kecamatan] ] sukses.");
  } else {
    $s = "SELECT * FROM tb_lokasi WHERE id = $id_kec";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q)) stop("Data kecamatan dengan id [$id_kec] tidak ditemukan.");
    $d = mysqli_fetch_assoc($q);
    $kec_kab = $d['kec_kab'];
    $t = str_replace('KEC ', '', $kec_kab);
    $t2 = explode(' KAB ', $t);
    $KAB = 'KAB';
    if (!isset($t2[1]) || !$t2[1]) {
      $KAB = 'KOTA';
      $t2 = explode(' KOTA ', $t);
    }
    $_POST['kecamatan'] = $t2[0];
    $_POST['kabupaten'] = "$KAB $t2[1]";
  }

  # ============================================================
  # VAR HANDLER
  # ============================================================
  $rt = sprintf('%02d', $_POST['alamat_rt']);
  $rw = sprintf('%02d', $_POST['alamat_rw']);
  $alamat_lengkap = "$_POST[alamat_jalan], RT $rt/$rw, DESA $_POST[desa], $kec_kab";
  $alamat_usaha = $_POST['alamat_usaha'] ? "'$_POST[alamat_usaha]'" : 'NULL';
  $free_kulkas = $_POST['free_kulkas'] ? "'$_POST[free_kulkas]'" : 'NULL';

  $s = "INSERT INTO tb_reseller (
    username,
    kecamatan,
    kabupaten,
    alamat_lengkap,
    tempat_usaha,
    alamat_usaha,
    free_kulkas,
    patokan
  ) VALUES (
    '$username',
    '$_POST[kecamatan]',
    '$_POST[kabupaten]',
    '$alamat_lengkap',
    '$_POST[tempat_usaha]',
    $alamat_usaha,
    $free_kulkas,
    '$_POST[patokan]'
  ) ON DUPLICATE KEY UPDATE
    created_at = CURRENT_TIMESTAMP 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl('?');
}

# ============================================================
# KEC KAB
# ============================================================
$s = "SELECT * FROM tb_lokasi WHERE verif_by is not null ORDER BY kec_kab ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opt_kec = '';
while ($d = mysqli_fetch_assoc($q)) {
  $opt_kec .= "<option value=$d[id]>$d[kec_kab] - $d[jarak]km</option>";
}



?>

<div class="card mb-3" style="position: relative;">
  <div class="card-header bg-info text-white text-center">
    <h5>Daftar Reseller</h5>
  </div>
  <form method=post class="card-body gradasi-toska">
    <p class="text-primary">Agar Anda dapat melakukan Pemesanan, silahkan Anda isi Data Prasyarat Reseller terlebih dahulu!</p>

    <div style="position: sticky;top:43px; background: #fff; padding: 10px; margin: 0 -15px;">
      <div class="tengah f12 abu mb1">Progress: <span id="progress">0</span> of <span id="total-progress">8</span> pengisian</div>
      <div class="progress">
        <div class="progress-bar" style="width: 0%;" id="progres-pengisian"></div>
      </div>
    </div>

    <div class="my-3">
      <label for="alamat_jalan" class="mb-1 f14">Alamat Jalan/Dusun/Blok</label>
      <input name="alamat_jalan" id="alamat_jalan" class="form-control input-wajib" placeholder="Masukan Alamat Jalan/Dusun/Blok" required minlength="3" maxlength="30">
    </div>

    <div class="my-3">
      <label for="alamat_rt" class="mb-1 f14">RT / RW</label>
      <div class="d-flex gap-2">
        <div class="flex-fill">
          <input name="alamat_rt" id="alamat_rt" class="form-control input-wajib" placeholder="RT..." required min=1 max=99 type=number>
        </div>
        <div>/</div>
        <div class="flex-fill">
          <input name="alamat_rw" id="alamat_rw" class="form-control input-wajib" placeholder="RW..." required min=1 max=99 type=number>
        </div>
      </div>
    </div>



    <div class="my-3">
      <label for="desa" class="mb-1 f14">Desa</label>
      <input name="desa" id="desa" class="form-control input-wajib" placeholder="Nama Desa..." required minlength="3" maxlength="30">
    </div>

    <div class="my-3">
      <label for="select_kecamatan" class="mb-1 f14">Pilih Kecamatan</label>
      <select name="select_kecamatan" id="select_kecamatan" class="form-control input-wajib" required>
        <option value="">--Pilih Kecamatan--</option>
        <option value="new">--Masukan Kecamatan Baru--</option>
        <?= $opt_kec ?>
      </select>
    </div>

    <script>
      $(function() {
        $('#select_kecamatan').change(function() {
          if ($(this).val() == 'new') {
            $('#blok-kecamatan-baru').slideDown();
            $('.input-kecamatan-baru').prop('required', 1);
          } else {
            $('#blok-kecamatan-baru').slideUp();
            $('.input-kecamatan-baru').prop('required', 0);
          }
        })
      })
    </script>

    <div class="hideit" id=blok-kecamatan-baru>
      <div class="card p-2 gradasi-kuning">
        <div class="mb-2">
          <label for="kecamatan" class="mb-1 f14">Kecamatan</label>
          <input name="kecamatan" id="kecamatan" class="form-control input-kecamatan-baru" placeholder="Nama Kecamatan..." minlength="3" maxlength="30">
        </div>

        <div class="mb-2">
          <label for="kabupaten" class="mb-1 f14">Kabupaten</label>
          <div class="mb-2 d-flex gap-2">
            <div>
              <select name="kab_kota" id="kab_kota" class="form-control input-kecamatan-baru">
                <option>KAB</option>
                <option>KOTA</option>
              </select>
            </div>
            <div>
              <input name="kabupaten" id="kabupaten" class="form-control input-kecamatan-baru" placeholder="Nama Kabupaten..." minlength="3" maxlength="30">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="my-3">
      <label for="tempat_usaha" class="mb-1 f14">Lokasi Tempat Usaha</label>
      <select name="tempat_usaha" id="tempat_usaha" class="form-control input-wajib" required>
        <option value="">--Pilih Lokasi Tempat Usaha--</option>
        <option value="rumah">Jualan di Rumah (Tidak Buka Warung)</option>
        <option value="warung">Warung (Skala Rumahan)</option>
        <option value="toko">Warung/Toko (Terpisah dari Rumah)</option>
        <option value="mart">Minimart / Supermarket</option>
      </select>
    </div>

    <script>
      $(function() {
        $('#tempat_usaha').change(function() {
          if ($(this).val() == 'toko' || $(this).val() == 'mart') {
            $('#blok-lokasi-usaha').slideDown();
            $('.input-lokasi-usaha').prop('required', 1);
          } else {
            $('#blok-lokasi-usaha').slideUp();
            $('.input-lokasi-usaha').prop('required', 0);
          }
        })
      })
    </script>

    <div class="hideit" id=blok-lokasi-usaha>
      <div class="card p-2 gradasi-kuning">
        <div class="mb-2">
          <label for="alamat_usaha" class="mb-1 f14">Alamat Usaha</label>
          <input name="alamat_usaha" id="alamat_usaha" class="form-control input-lokasi-usaha" placeholder="Alamat Usaha Anda..." minlength="3" maxlength="30">
        </div>

        <div class="mb-2">
          <label for="free_kulkas" class="mb-1 f14">Opsi Free Kulkas (minimum order: 300cup)</label>
          <select name="free_kulkas" id="free_kulkas" class="form-control input-lokasi-usaha" required>
            <option value="">--Pilih Opsi Free Kulkas--</option>
            <option value="1">Saya tertarik</option>
            <option value="-1">Belum Tertarik</option>
            <option value="-2">Tidak Tertarik (Tidak memungkinkan)</option>
          </select>

        </div>
      </div>
    </div>


    <div class="my-3">
      <label for="patokan" class="mb-1 f14">Patokan Alamat</label>
      <input name="patokan" id="patokan" class="form-control input-wajib" placeholder="Misal: seberang alun2 desa..." required minlength="3" maxlength="30">
    </div>


    <div class="f14 miring abu">Lokasi kecamatan sangat menentukan harga khusus untuk Anda.</div>

    <button class="btn btn-primary w-100 mt-3" name=btn_daftar_reseller>Daftar Reseller</button>



  </form>

</div>

<script>
  $(function() {
    $('.input-wajib').focusout(function() {
      let progress = 0;
      let totalProgress = $('.input-wajib').length;
      $('#total-progress').text(totalProgress);
      $('.input-wajib').each((idx, e) => {
        if ($('#' + e.id).val()) progress++;
      });
      $('#progress').text(progress);
      let persen = Math.round(progress * 100 / totalProgress);
      $('#progres-pengisian').prop('style', `width:${persen}%`);


    })
  })
</script>