<?php
if ($username and $param != 'daftar_reseller' and $param != 'logout') {
  $tb2 = $role ? 'petugas' : 'reseller';
  # ============================================================
  # DATA USER + RESELLER/PETUGAS
  # ============================================================
  $s = "SELECT 
  a.role,
  a.nama,
  a.image, 
  a.whatsapp, 
  a.active_status, 
  a.whatsapp_status,
  b.*
  FROM tb_user a 
  JOIN tb_$tb2 b ON a.username=b.username 
  WHERE a.username = '$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $user = mysqli_fetch_assoc($q);
  if (!$user) {
    // alert("Belum ada data user (reseller) dengan username [$username]", 'info');
    # ============================================================
    # REDIRECT TO DAFTAR RESELLER
    # ============================================================
    jsurl('?daftar_reseller');
  }
  $nama_user = $user['nama'];
  $role = $user['role'];
  $is_adm = $role == 'admin' ? 1 : 0;
}
