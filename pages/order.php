<?php
$p = $_GET['p'] ?? '';

if ($p == 'add') {
  include 'add_order.php';
} else {
  $id_order = $_GET['id_order'] ?? null;
  if ($id_order) {
    include "order_detail.php";
  } else {
    include 'order-terbaru.php';
    include 'order-histori.php';
  }
}
