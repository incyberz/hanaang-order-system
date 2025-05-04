<?php
# ============================================================
# dilarang login jika sudah login
# ============================================================
if ($param == 'login' and $username) die('<script>location.replace("?")</script>');

# ============================================================
# HOME AND LOGOUT 
# ============================================================
if ($username) {
  if ($param) include 'includes/btn_home.php';
  include 'includes/btn_logout.php';
}


# ============================================================
# ADDRESS ROUTE 
# ============================================================
$home = 'welcome';
if ($username) {
  if ($role == '') {
    $home = 'dashboard-reseller';
  } else {
    $home = "dashboard-$role";
  }
}
$arr_route = [
  '' => $home,
  '?' => $home,
];


# ============================================================
# SWITCH PARAMETER
# ============================================================
$konten = null;
if (key_exists($param, $arr_route)) {
  $param = $arr_route[$param];
}

$konten = $konten ?? $param;

// default konten berada di folder pages
if (!file_exists($konten)) $konten = "pages/$konten.php";

if (file_exists($konten)) {
  if ($username) {
    include $konten;
  } else {
    if (
      $param == 'daftar'
      || $param == 'lupa_password'
      || $param == 'verifikasi_whatsapp'
      || $param == 'welcome'
    ) {
      // include page tersebut
      include "pages/$param.php";
    } else {
      # ============================================================
      # JIKA PARAMETER INVALID ARAHKAN KE LOGIN
      # ============================================================
      include 'pages/login.php';
    }
  }
} else {
  include 'na.php';
}
