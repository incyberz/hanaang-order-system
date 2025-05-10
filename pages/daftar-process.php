<?php
if (isset($_POST['btn_set_passwordZZZ'])) {
  // $s = "UPDATE tb_akun SET password=md5('$_POST[password]') WHERE username='$_POST[btn_set_password]'";
  // $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // $pesan = 'passwordOK';
} elseif (isset($_POST['btn_daftar_akun'])) {
  $post_username = strip_tags(addslashes($_POST['username']));
  $post_whatsapp = strip_tags(addslashes($_POST['whatsapp']));
  $post_nama = ucwords(strtolower(strip_tags(addslashes($_POST['nama']))));

  # ============================================================
  # DEFAULT PASSWORD = 4 DIGIT TERAKHIR WHATSAPP
  # ============================================================
  $password = substr($post_whatsapp, -4);


  # ============================================================
  # SELECT NOMOR WHATSAPP AT DB
  # ============================================================
  $s = "SELECT username as username_db FROM tb_user 
  WHERE whatsapp = '$post_whatsapp' 
  AND whatsapp_status = 1 
  AND active_status is null 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);
  if ($d) {
    $username_db = $d['username_db'];
    $s = "UPDATE tb_user SET 
      username = '$post_username',
      -- password = md5('$password'), -- sudah ada password by admin
      nama = '$post_nama',
      active_status = 1 
    WHERE username = '$username_db'";
  } else {
    $s = "INSERT INTO tb_user (
      username,
      nama,
      whatsapp,
      password
    ) VALUES (
      '$post_username',
      '$post_nama',
      '$post_whatsapp',
      md5('$password')
    ) ON DUPLICATE KEY UPDATE 
      created_at = CURRENT_TIMESTAMP
    ";
  }

  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  sukses("
    Halo $post_nama! <br>Akun Anda berhasil dibuat.
    <hr>
    Catat baik-baik Akun Reseller Anda: 
    <ul class='mt-2'>
      <li><b>Username</b>: $post_username</li>
      <li><b>Password</b>: $password</li>
    </ul>
    <a class='btn btn-primary w-100' href=?login&username=$post_username>Login</a>

  ");
  exit;
} elseif (isset($_POST['btn_upload_berkas']) || isset($_POST['btn_replace_berkas'])) {
  if (isset($_POST['btn_replace_berkas'])) {
    $t = explode('--', $_POST['btn_replace_berkas']);
    $jenis_berkas = $t[0] ?? die('jenis_berkas undefined.');
    $src = $t[1] ?? die('src undefined.');
    # ============================================================
    # DELETE DB
    # ============================================================
    $s = "DELETE FROM tb_berkas WHERE jenis_berkas='$jenis_berkas' AND username='$username'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    # ============================================================
    # DELETE FILE SEBELUMNYA
    # ============================================================
    unlink($src);
    alert('Delete file sebelumnya berhasil.', 'success');
  } else {
    $jenis_berkas = $_POST['btn_upload_berkas'];
  }
  include 'includes/resize_img.php';

  $file = $_FILES['file'];
  $path = 'uploads/berkas';
  $time = date('ymdHis');
  $new_file = strtolower("$username-$jenis_berkas-$time.jpg");
  $to = "$path/$new_file";

  if (move_uploaded_file($file['tmp_name'], $to)) {

    $nomor_berkas = isset($_POST['nomor_berkas']) ? "'$_POST[nomor_berkas]'" : 'NULL';
    $tanggal_berkas = isset($_POST['tanggal_berkas']) ? "'$_POST[tanggal_berkas]'" : 'NULL';
    $nominal = isset($_POST['nominal']) ? "'$_POST[nominal]'" : 'NULL';

    resize_img($to);
    # ============================================================
    # INSERT DB
    # ============================================================
    $s = "INSERT INTO tb_berkas (
      username,
      jenis_berkas,
      file,
      nomor_berkas,
      tanggal_berkas,
      nominal,
      status
    ) VALUES (
      '$username',
      '$jenis_berkas',
      '$new_file',
      $nomor_berkas,
      $tanggal_berkas,
      $nominal,
      NULL
    )";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    alert('Upload sukses.', 'success');
  }

  jsurl();
} elseif ($_POST) {

  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
  stop('Belum ada handler untuk data POST diatas. Hubungi Developer!');
  exit;
}
