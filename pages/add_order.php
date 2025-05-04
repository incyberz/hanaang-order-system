<?php
include 'order-add-styles.php';
include 'order-add-process.php';
include 'includes/btn_home.php';


# ============================================================
# AUTO ADD ORDER JIKA TIDAK ADA ORDER PENDING 
# ============================================================
$s = "SELECT * FROM tb_order WHERE status is null";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$order = mysqli_fetch_assoc($q);
if ($order) {
  jsurl("?order_detail&id_order=$order[id]");
} else {
  # ============================================================
  # AUTO INSERT NEW ORDER
  # ============================================================
  $s = "INSERT INTO tb_order (username) VALUES ('$username')";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}




# ============================================================
# LIST PRODUK AVAILABLE
# ============================================================
$s = "SELECT *,
(
  SELECT harga FROM tb_harga_reseller 
  WHERE username = '$username' -- reseller yang ini
  AND id_produk = a.id -- produk yang ini
  ORDER BY created_at DESC LIMIT 1 -- hanya harga terbaru 
  ) harga_reseller 
FROM tb_produk a 
WHERE a.status=1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$row_produk = '';
$i = 0;
$total_bayar = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $id = $d['id'];
  $i++;
  $tr_class = '';
  if ($d['harga_reseller']) {

    $qty = ''; // ZZZ
    $jumlah = 0; // ZZZ



    $jumlah_show = "<input disabled class='form-control nominal jumlah input-show' id='jumlah--$id' value='$jumlah' />";

    $kalkulasi_row = "
      <div class='nominal d-flex gap-2'>
        <div>$d[harga_reseller]</div>
        <div>x</div>
        <div class=hideit id=harga_reseller--$id>$d[harga_reseller]</div>
        <div>
          <input 
            placeholder='QTY'
            type=number 
            class='form-control qty' 
            name='qty[$id]'
            id='qty--$id'
            min='$d[min_order]' 
            max='$d[max_order]' 
            value='$qty'
          />
        </div>
        <div>=</div>
        <div class=flex-fill>$jumlah_show</div>
      </div>    
    ";
  } else {
    $tr_class = 'tanyakan';
    $kalkulasi_row = "<a href=?tanyakan&hal=ZZZ class='miring'>Tanyakan Harga untuk Anda</a>";
    $input_qty = '-';
    $jumlah_show = '-';
  }
  $row_produk .= "
    <div id=row--$id class='$tr_class row-produk'>
      <div class='d-flex gap-2'>
        <div class='pt1'>
          <input type=checkbox class='checkbox' id=checkbox--$id>
        </div>
        <div>
          <div class='mb-2 darkblue bold'>
            <label for=checkbox--$id class=hover>$d[nama]</label>
          </div>
          <div class='blok-kalkulasi hideit' id=blok-kalkulasi--$id>
            $kalkulasi_row
            <div class='f12 abu miring mt1 mb1'>min: $d[min_order], max: $d[max_order]</div>
          </div>
        </div>
      </div>
    </div>
  ";
}





?>
<link rel="stylesheet" href="assets/css/order-add.css">
<div class="blok-order d-flex justify-content-center align-items-center">

  <form method="POST" class="form-order">
    <div class="text-center">
      <h2 class="">Form Pemesanan</h2>
      <div class="mb-4 f14 abu">(Add Order)</div>
      <p>Silahkan Anda ceklis produk yang tersedia lalu masukan QTY!</p>
    </div>

    <div class="blok-row-produk">
      <?= $row_produk ?>
      <div class="bordered text-center p-2 br5 mt-2">
        <div colspan="4" class="">Total Bayar</div>
        <div class="nominal f30" id=total_bayar><?= $total_bayar ?></div>
      </div>
    </div>

    <div class="d-flex gap-2 f14 abu my-2">
      <div>Alamat pengiriman sesuai biodata.</div>
      <div class="text-primary hover" id=ubah-alamat>Ubah</div>
    </div>

    <div class="my-3 hideit" id=blok-alamat-custom>
      <label for="alamat-default" class="form-label">Alamat biodata:</label>
      <textarea
        class="form-control mb-2"
        id="alamat-default"
        rows="3"
        disabled>Kampung Lebakmaja RT 02/12 Desa Tanjungsari Kec Tanjungsari</textarea>
      <label for="alamat-custom" class="form-label">Alamat lain dalam satu kecamatan:</label>
      <textarea
        placeholder="Masukan Alamat lain dalam satu kecamatan..."
        class="form-control"
        id="alamat-custom"
        name="alamat-custom"
        rows="3"></textarea>
    </div>


    <div class="text-center my-4">
      <div id="blok_btn_silahkan_isi" class="">
        <span class="btn btn-primary w-100" onclick="alert(`Silahkan isi QTY terlebih dahulu.`)">Silahkan isi QTY...</span>
      </div>
      <div id="blok_btn_submit_pesanan" class="hideit">
        <button class="btn btn-primary w-100" id=btn_submit_pesanan name=btn_submit_pesanan value="<?= $id_order ?>">Submit Pesanan</button>
      </div>
    </div>


    <div id=blok-info class="hideit">
      <?php include 'info_pembayaran.php'; ?>
    </div>
    <div class="mb-3 f14 text-primary text-center" id=lihat-info>Lihat Info Pembayaran</div>

  </form>
</div>


<script>
  function hitung_total() {
    let total = 0;
    $(".qty").each((value, index) => {
      let qty = parseInt(index.value);
      let rid = index.id.split("--");
      let id = rid[1];
      let harga = parseInt($('#harga_reseller--' + id).text());

      if (!isNaN(harga) && !isNaN(qty)) {
        let jumlah = harga * qty;
        $('#jumlah--' + id).val(jumlah);
        total += jumlah;
        // console.log(qty, jumlah, total);
      }
    })

    $('#total_bayar').text(total);
    if (total) {
      $('#blok_btn_silahkan_isi').slideUp();
      $('#blok_btn_submit_pesanan').slideDown();
    } else {
      $('#blok_btn_submit_pesanan').slideUp();
      $('#blok_btn_silahkan_isi').slideDown();

    }

  }

  $(function() {
    $(".qty").keyup(function() {
      hitung_total();
    });

    $(".qty").change(function() {
      hitung_total();
    });

    $("#ubah-alamat").click(function() {
      $('#blok-alamat-custom').slideToggle();
    });

    $(".checkbox").click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id = rid[1];
      $('#blok-kalkulasi--' + id).slideToggle();
      let checked = $(this).prop('checked');
      if (!checked) {
        $('#qty--' + id).val('');
        $('#jumlah--' + id).val('0');
        hitung_total();
        $('#row--' + id).removeClass('gradasi-toska');
      } else {
        $('#row--' + id).addClass('gradasi-toska');

      }
    });

    $("#lihat-info").click(function() {
      $('#blok-info').slideToggle();
    });

  });
</script>