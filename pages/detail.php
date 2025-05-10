<?php
include 'includes/key2kolom.php';

$get_tb = $_GET['tb'] ?? kosong('tb');
$get_fields = $_GET['fields'] ?? '*';
$get_hide_fields = $_GET['hide_fields'] ?? null;
$JOINS = $_GET['joins'] ?? '';
$WHERE = $_GET['wheres'] ?? '';
$ORDER_BY = $_GET['order_by'] ?? '';

$Tb = ucwords(strtolower($get_tb));
set_h2($Tb . " Detail");

# ============================================================
# GLOBAL SELECT
# ============================================================
$s = "SELECT 
$get_fields 
FROM tb_$get_tb a 
$JOINS
$WHERE 
$ORDER_BY
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));


$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  $th = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $td = '';
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'date_created'
      ) continue;
      if ($i == 1) {
        $kolom = key2kolom($key);
        $th .= "<th>$kolom</th>";
      }
      $td .= "<td>$value</td>";
    }
    $tr .= "
      <tr>
        $td
      </tr>
    ";
  }
}



echo "
  <table class='table'>
    <thead class='bg-dark text-white'>$th</thead>
    $tr
  </table>
";
