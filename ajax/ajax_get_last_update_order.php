<?php
include '../conn.php';
$get_last_update = $_GET['last_update'] ?? die('Undefined index [last_update]');
$id_order = $_GET['id_order'] ?? die('Undefined index [id_order]');

if (!$get_last_update) die('Empty index [last_update]');
if (!$id_order) die('Empty index [id_order]');

$s = "SELECT last_update FROM tb_order WHERE id = $id_order";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
if (!$d) die('Data order tidak ditemukan.');

if ($d['last_update'] == $get_last_update) {
  die('0'); // do nothing
} else {
  die($d['last_update']); // show refresh form
}
