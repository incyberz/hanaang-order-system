<div class="text-center" style="position: sticky; top:0; left:0; right:0; background:white; z-index:99">
  <h1 class="d-none d-md-block">Sistem Informasi Pemesanan Online</h1>
  <p class=" border-bottom pb-2 pt2">
    <?php
    $role_show = $role ? "Anda sebagai <b class='text-primary'>$role</b>" : '';
    if (!$user) {
      # ============================================================
      # DATA USER ONLY
      # ============================================================
      $s = "SELECT * FROM tb_user WHERE username='$username'";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      if (!mysqli_num_rows($q)) stop("Data user [$username] tidak ditemukan.");
      $user = mysqli_fetch_assoc($q);
    }
    ?>
    Welcome <?= $user['nama'] ?? 'User' ?>!
    <span class="d-none d-md-inline">
      <?= $role_show ?>
    </span>
  </p>
</div>