<?php
session_start();
// session_destroy();

# ============================================================
# SESSION
# ============================================================
$username = $_SESSION['hanaang_username'] ?? '';

# ============================================================
# ZZZ DEBUG SESSION
# ============================================================
$username = 'iin';

# ============================================================
# CONFIGIRATION FILE
# ============================================================
// include 'config.php';
include 'conn.php';
include 'global_vars.php';

$dotdot = $is_live ? '.' : '..';
if ($username) {

  # ============================================================
  # INCLUDES
  # ============================================================
  include "$dotdot/includes/alert.php";
  include "$dotdot/includes/insho_styles.php";
  include "$dotdot/includes/img_icon.php";
  include "$dotdot/includes/jsurl.php";
} // end if $username

# ============================================================
# USER DATA
# ============================================================
$user = [];
$nama_user = '';
$role = null;
$is_adm = false;
include 'pages/user.php';



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hanaang App</title>
  <?php include 'includes/html_head.php'; ?>
</head>

<body>
  <div class="container">
    <?php if ($username) include 'pages/header.php'; ?>
    <main>
      <section>
        <?php include 'routing.php'; ?>
      </section>
    </main>
  </div>
  <?php
  if ($role) {
    // if ($role == 'AKD') {
    //   # ============================================================
    //   # SET SESSION IF NOT SET
    //   # ============================================================
    //   if ($get_id_prodi and $get_id_prodi != $session_id_prodi) $_SESSION['id_prodi'] = $get_id_prodi;
    //   if ($get_id_shift and $get_id_shift != $session_id_shift) $_SESSION['id_shift'] = $get_id_shift;
    //   if ($get_semester and $get_semester != $session_semester) $_SESSION['semester'] = $get_semester;
    //   if ($get_counter and $get_counter != $session_counter) $_SESSION['counter'] = $get_counter;
    // }
    // include 'pages/ontops.php';
  }
  ?>
</body>

<?php include "$dotdot/includes/script_btn_aksi.php"; ?>

</html>
<script>
  $(function() {
    $('.ondev').click(function() {
      alert(`Fitur ini masih dalam tahap pengembangan. Terimakasih sudah mencoba!\n\n\ninfo lanjut: silahkan hubungi developer!`)
    })
  })
</script>