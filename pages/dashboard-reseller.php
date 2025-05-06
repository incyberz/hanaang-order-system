<!-- <h2 class="text-center my-3">Dashboard Reseller</h2> -->

<?php
set_h2('Reseller Dashboard');
$bisa_add_order = true;
include 'my_order.php';

if ($bisa_add_order) {
  $btn_add_order = "<a href='?add_order' class='btn btn-success w-md-auto w-100' onclick='return confirm(`Add Order?`)'> Add Order (Tambah Pesanan)</a>";
} else {
  $btn_add_order = "<span class='btn btn-secondary w-md-auto w-100' onclick='alert(`Masih ada pesanan yang belum Anda proses atau Pesanan yang menunggu proses dari Petugas.`)'>Belum bisa Add Order</span>";
}
echo "
  <div class='mt-4 mb-1'>
    $btn_add_order
  </div>
  <div class='f14 text-secondary text-center mt-2 mb-5'>Tidak bisa Add Order jika masih ada Order yang belum diproses oleh Petugas atau belum terbayar.</div>
";
?>