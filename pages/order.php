<?php
$p = $_GET['p'] ?? '';

if ($p == 'add') {
  include 'order-add.php';
} else {
  include 'order-terbaru.php';
  include 'order-histori.php';
}
