<?php
// v.2.0.1  single includes path
// v.1.3.13 eta functions move to date managements
// v.1.3.12 update fungsi hari_tanggal
// v.1.3.11 add data AOS to set_h2
// v.1.3.10 add hari_tanggal
// v.1.3.9 update eta function
// v.1.3.8 autoset title when set_judul
// v.1.3.7 set_h2 id, set_judul, set_sub_judul
// v.1.3.6 function gender
// v.1.3.5 eta2 updated
// v.1.3.4 seth2 dan key2kolom
// v.1.3.3 tr_col colspan=100%
// v.1.3.2 baca_csv update

include 'set_h2.php';
include 'hari_tanggal.php';
include 'jsurl.php';
include 'erid.php';
include 'key2kolom.php';





function tr_col($pesan, $td_class = '', $tr_class = '', $jumlah_col = 100)
{
  $colspan = $jumlah_col < 10 ? $jumlah_col : "$jumlah_col%";
  return "<tr class='$tr_class'><td class='$td_class' colspan='$colspan'>$pesan</td></tr>";
}


function baca_csv($file, $separator = ',')
{

  if (file_exists($file)) {
    $file = fopen($file, 'r');
    $data = array();

    while (!feof($file)) {
      $data[] = fgetcsv($file, null, $separator);
    }

    fclose($file);
    return $data;
  } else {
    die("File <b class='consolas'>$file</b> tidak ditemukan.");
  }
}

function th($rank)
{
  if ($rank % 10 == 1) {
    return 'st';
  } elseif ($rank % 10 == 2) {
    return 'nd';
  } elseif ($rank % 10 == 3) {
    return 'rd';
  } else {
    return 'th';
  }
}

function hm($nilai)
{
  if ($nilai >= 85) {
    return 'A';
  } elseif ($nilai >= 70) {
    return 'B';
  } elseif ($nilai >= 60) {
    return 'C';
  } elseif ($nilai >= 40) {
    return 'D';
  } elseif ($nilai >= 1) {
    return 'E';
  } elseif ($nilai == 0) {
    return 'TL';
  } else {
    return false;
  }
}








function set_judul($text, $sub_judul = '')
{
  $set_sub_judul = !$sub_judul ? '' : "$('#sub_judul').text('$sub_judul');";
  echo "
    <script>
      $(function(){
        $('#judul').text('$text');
        $set_sub_judul
      })
    </script>
  ";
}


function gender($l_p)
{
  if ((strtolower($l_p) == 'l')) {
    return 'laki-laki';
  } elseif (strtolower($l_p) == 'p') {
    return 'perempuan';
  } elseif (strtolower($l_p) == '') {
    return '<i>null</i>';
  } else {
    return "<i style=color:red>gender $l_p undefined</i>";
  }
}




function clean_sql($a)
{
  $a = trim($a);
  $a = str_replace('\'', '`', $a);
  $a = str_replace('"', '`', $a);
  $a = str_replace(';', ',', $a);
  return $a;
}
