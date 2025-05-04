<?php
unset($_SESSION['hanaang_username']);
unset($_SESSION['hanaang_role']);
if ($_SESSION) {
  echo '<pre>';
  print_r($_SESSION);
  echo '<b style=color:red>DEBUGING: masih ada data SESSION yang belum clear</b></pre>';
  exit;
}
jsurl('?');
