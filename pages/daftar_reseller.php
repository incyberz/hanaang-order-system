<?php
if (isset($_POST['btn_daftar_reseller'])) {
  $kecamatan = strip_tags(addslashes($_POST['kecamatan']));
  $kabupaten = strip_tags(addslashes($_POST['kabupaten']));
  $alamat_lengkap = strip_tags(addslashes($_POST['alamat_lengkap']));

  $s = "INSERT INTO tb_reseller (
    username,
    kecamatan,
    kabupaten,
    alamat_lengkap
  ) VALUES (
    '$username',
    '$kecamatan',
    '$kabupaten',
    '$alamat_lengkap'
  ) ON DUPLICATE KEY UPDATE
    created_at = CURRENT_TIMESTAMP 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl('?');
}

?>

<div class="card mb-3">
  <div class="card-header bg-info text-white text-center">
    <h5>Daftar Reseller</h5>
  </div>
  <form method=post class="card-body gradasi-toska">
    <p class="text-primary">Agar Anda dapat melakukan Pemesanan, silahkan Anda isi Data Prasyarat Reseller terlebih dahulu!</p>

    <div class="my-3">
      <label for="alamat_lengkap" class="mb-1 f14">Alamat Lengkap</label>
      <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control" placeholder=" Kampung atau Jalan, RT, RW, Desa" rows="3" required minlength="10" maxlength="100"></textarea>
    </div>

    <div class="my-3">
      <label for="kecamatan" class="mb-1 f14">Kecamatan</label>
      <input name="kecamatan" id="kecamatan" class="form-control" placeholder="Nama Kecamatan" required minlength="3" maxlength="30">
    </div>

    <div class="my-3">
      <label for="kabupaten" class="mb-1 f14">Kabupaten</label>
      <input name="kabupaten" id="kabupaten" class="form-control" placeholder="Nama Kabupaten" required minlength="3" maxlength="30">
    </div>

    <div class="f14 miring abu">Lokasi kecamatan sangat menentukan harga khusus untuk Anda.</div>

    <button class="btn btn-primary w-100 mt-3" name=btn_daftar_reseller>Daftar Reseller</button>



  </form>

</div>