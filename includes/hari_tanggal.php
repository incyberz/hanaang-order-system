<?php
function hari_tanggal($datetime = '', $long_mode = 1, $with_day = 1, $with_hour = 1, $with_second = false, $separator = ' ')
{
  $time = $datetime ? strtotime($datetime) : strtotime('now');
  $nama_hari = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  $nama_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  if (!$long_mode) {
    $nama_hari = ['Ah', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'];
    $nama_bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
  }
  $hari_show = $with_day ? $nama_hari[date('w', $time)] . ', ' : '';
  $year_format = $with_hour ? 'Y, H:i' : 'Y';
  $year_format = $with_second ? 'Y, H:i:s' : $year_format;
  $tanggal_show =  date('d', $time) . $separator . $nama_bulan[intval(date('m', $time)) - 1] . $separator . date($year_format, $time);


  return $hari_show . $tanggal_show;
}

function tanggal($datetime)
{
  return hari_tanggal($datetime, 0, 0, 0);
}
