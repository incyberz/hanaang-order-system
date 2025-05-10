<?php
if ($tb == 'pesanan_baru') {
  if ($d['qty_order']) { // hanya yang sudah jelas qty nya
    $aksi = "<a class='btn btn-sm btn-primary' href='?order_detail&id_order=$d[id]&username=$d[username]'>Manage</a>";
  } else {
    $aksi = '<span class="btn btn-secondary btn-sm" onclick="alert(`Reseller belum memasukan QTY Order.`)">Manage</span>';
  }
} else {
  $aksi = '<i class="red" >null</i>';
}
