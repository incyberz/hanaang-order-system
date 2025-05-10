<?php
if ($_POST) {
  if (isset($_POST['btn_tambah_user'])) {
    jsurl("?proses_tambah_user_reseller&whatsapp_calon_reseller=$_POST[whatsapp_calon_reseller]");
  } elseif (isset($_POST['btn_tambah_user_default'])) {
    $nama = strip_tags(addslashes(strtoupper($_POST['nama'])));
    $md5_passwd = md5($_POST['password']);

    $s = "INSERT INTO tb_user (
      username,
      password,
      nama,
      whatsapp,
      active_status,
      whatsapp_status,
      created_by
    ) VALUES (
      '$_POST[username]',
      '$md5_passwd',
      '$nama',
      '$_POST[whatsapp]',
      NULL,
      1,
      '$username'
    )";

    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    jsurl('?');
  } else {
    echo '<pre>';
    print_r($_POST);
    echo '<b style=color:red>Belum ada handler untuk data POST diatas.</b></pre>';
    exit;
  }
}


$whatsapp_calon_reseller = $_GET['whatsapp_calon_reseller'] ?? kosong('whatsapp_calon_reseller');
$passwd = substr($whatsapp_calon_reseller, -4);

$s = "SELECT COUNT(1) + 1 as new_id FROM tb_user";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$new_id = $d['new_id'];
$new_username = 'user' . sprintf('%04d', $new_id);



echo "
  <form method=post class='card my-5 gradasi-toska mx-auto' style='max-width:500px'>
    <div class='card-header text-center text-white bg-primary'>Tambah Data User Awal bagi Calon Reseller</div>
    <div class='card-body '>
      <p>Jika whatsapp yang diinputkan calon reseler sama dengan $whatsapp_calon_reseller, maka data default ini akan tergantikan dengan data dari calon reseller.</p>
      <div class=my-3>
        <label class='f14 abu mb-1'>Username</label>
        <input class='form-control' disabled value=$new_username>
        <input class='form-control' type=hidden name=username value=$new_username>
      </div>
      <div class=my-3>
        <label class='f14 abu mb-1'>Password</label>
        <input class='form-control' disabled value=$passwd>
        <input class='form-control' type=hidden name=password value=$passwd>
      </div>
      <div class=my-3>
        <label class='f14 abu mb-1'>Nama Reseller</label>
        <input class='form-control' name=nama value='Nama Reseller Baru'>
      </div>
      <div class=my-3>
        <label class='f14 abu mb-1'>Whatsapp</label>
        <input class='form-control' disabled value=$whatsapp_calon_reseller>
        <input type=hidden class='form-control' name=whatsapp value=$whatsapp_calon_reseller>
      </div>
      <button class='btn btn-primary w-100' name=btn_tambah_user_default>Tambah User Awal</button>
    </div>
  </form>    
";
