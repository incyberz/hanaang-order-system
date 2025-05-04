<div
  class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h2>Dashboard Reseller</h2>
</div>

<?php include 'my_order.php'; ?>
<div class="mt-4 mb-1">
  <a href="?order&p=add" class="btn btn-success w-md-auto w-100" onclick="return confirm(`Add Order?`)"> Add Order (Tambah Pesanan)</a>
</div>
<div class="f14 text-secondary">Tidak bisa Add Order jika masih ada Order yang belum diproses oleh Petugas atau belum terbayar.</div>