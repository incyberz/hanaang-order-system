<?php
function show_status_order($status, $bg = 'danger')
{
  if ($status === null) {
    return "<span class='badge bg-danger'>Belum Anda Proses</span>";
  } elseif ($status === '') {
    return "<span class='badge bg-warning'>Sedang Diproses Petugas</span>";
  } else {
    return "<span class='badge bg-$bg'>$status</span>";
    // die("Invalid status kode [$status] pada show_status_order()"); 
  }
}
