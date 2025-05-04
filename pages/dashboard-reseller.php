<h2 class="text-center my-3">Dashboard Reseller</h2>

<?php
set_title('Reseller Dashboard');
include 'my_order.php';
?>
<div class="mt-4 mb-1">
  <a href="?order&p=add" class="btn btn-success w-md-auto w-100" onclick="return confirm(`Add Order?`)"> Add Order (Tambah Pesanan)</a>
</div>
<div class="f14 text-secondary">Tidak bisa Add Order jika masih ada Order yang belum diproses oleh Petugas atau belum terbayar.</div>