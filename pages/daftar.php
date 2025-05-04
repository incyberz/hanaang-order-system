<?php
# ============================================================
# DILARANG DAFTAR JIKA SEDANG LOGIN
# ============================================================
if ($username) die('<script>location.replace("?")</script>');
include 'includes/insho_styles.php';
include 'includes/alert.php';
include 'daftar-process.php';

?>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <form method="POST" enctype="multipart/form-data" class="card gradasi-toska flex-fill" style="max-width: 500px;">
    <div class="card-header bg-info text-white text-center">
      <h1 class="f30">Pendaftaran Akun</h1>
      <div class="">Akun Reseller digunakan untuk melakukan Pemesanan Online.</div>
    </div>
    <div class="card-body">

      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input
          type="text"
          class="form-control lower"
          id="username"
          name="username"
          required
          minlength="3"
          maxlength="20"
          placeholder="tanpa spasi, tanpa karakter khusus" />
        <div class="hideita text-danger mt-2" id="username_error"></div>
      </div>

      <div class="mb-3">
        <label for="nama" class="form-label">Nama Lengkap</label>
        <input
          type="text"
          class="form-control proper"
          id="nama"
          name="nama"
          minlength="3"
          maxlength="30"
          required
          placeholder="Contoh: Ibu Apong binti Jumasik" />
      </div>

      <div class="mb-3">
        <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
        <input
          type="tel"
          class="form-control"
          id="whatsapp"
          name="whatsapp"
          placeholder="Contoh: 6281234567890"
          required />
        <div class="hideit mt-2 text-primary" id="whatsapp_info">Seluruh informasi penting akan kami kirimkan via whatsapp, masukanlah Nomor WhatsApp Anda yang aktif!</div>
      </div>

      <div class="text-center mt-4 ">
        <button type="submit" class="btn btn-primary w-100" name=btn_daftar_akun>Daftar Akun</button>
      </div>
    </div>
  </form>
</div>

<script>
  $(function() {
    $("#nama").on("keyup", function() {
      $(this).val(
        $(this).val()
        .replace(/'/g, "`") // Ubah tanda petik menjadi backtick
        .replace(/[^a-zA-Z` ]/g, "") // Hanya huruf, spasi, dan tanda backtick
        .replace("  ", " ") // Dilarang double spasi
        .toLowerCase()
      ); // Ubah ke uppercase
    });

    $("#whatsapp").on("keyup", function() {
      let val = $(this).val().replace(/[^0-9]/g, ""); // Hanya angka
      if (val.startsWith("08")) {
        val = "628" + val.substring(2);
      } else if (!val.startsWith("628") && val.length >= 4) {
        val = "";
      }
      $(this).val(val);
    });

    $("#whatsapp").focus(function() {
      $('#whatsapp_info').slideDown();
    });

    $("#whatsapp").focusout(function() {
      $('#whatsapp_info').slideUp();
    });

    $("#username").on("keyup", function() {
      let val = $(this).val();
      val = val.replace(/[^a-zA-Z0-9]/g, ""); // Hanya huruf kecil dan angka
      $(this).val(val.toLowerCase()); // Ubah ke lowercase
    });

    $("#username").focusout(function() {
      let username = $(this).val();
      let link_ajax = "pages/daftar-cek_available_username.php?username=" + username;
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'OK') {
            $('#username_error').html('<span class=text-success>username OK.</span>');
          } else {
            $('#username').val('');
            $('#username_error').html(a);
          }
        }
      })
    });
  });
</script>