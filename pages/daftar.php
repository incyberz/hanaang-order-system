<?php
# ============================================================
# DILARANG DAFTAR JIKA SEDANG LOGIN
# ============================================================
if ($username) die('<script>location.replace("?")</script>');

?>
<div class="container mt-5">
  <h2 class="text-center">Formulir Pendaftaran Calon Reseller</h2>
  <form action="#" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input
        type="text"
        class="form-control"
        id="username"
        name="username"
        required
        placeholder="tanpa spasi, tanpa karakter khusus" />
    </div>

    <div class="mb-3">
      <label for="name" class="form-label">Nama Lengkap</label>
      <input type="text" class="form-control" id="name" name="name" required />
    </div>

    <div class="mb-3">
      <label for="address" class="form-label">Alamat</label>
      <textarea
        class="form-control"
        id="address"
        name="address"
        rows="3"
        required></textarea>
    </div>

    <div class="mb-3">
      <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
      <input
        type="tel"
        class="form-control"
        id="whatsapp"
        name="whatsapp"
        placeholder="Contoh: +6281234567890"
        required />
    </div>

    <div class="mb-3">
      <label for="photo" class="form-label">Upload Foto Warung/Rumah</label>
      <input
        type="file"
        class="form-control"
        id="photo"
        name="photo"
        accept="image/*"
        required />
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-primary">Daftar</button>
    </div>
  </form>
</div>