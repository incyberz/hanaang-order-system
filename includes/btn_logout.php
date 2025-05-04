<style>
  .btn-logout {
    position: fixed;
    z-index: 1;
    top: 15px;
    right: 15px;
  }
</style>
<div class="btn-logout">
  <a href="?logout" onclick="return confirm(`Logout?`)"><?= img_icon('logout') ?></a>
</div>