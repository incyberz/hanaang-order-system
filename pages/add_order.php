<style>
  .li-harga {
    list-style: square;
    cursor: pointer;
    transition: .2s;
  }

  .li-harga:hover {
    background: yellow;
    letter-spacing: .5px;
  }
</style>
<?php
include 'add_order-styles.php';
include 'add_order-process.php';
include 'includes/hari_tanggal.php';

$img_stars = img_icon('stars');
$default_qty = 50; // penawaran QTY default untuk reseller

# ============================================================
# AUTO TAMBAH ORDER JIKA TIDAK ADA ORDER PENDING 
# ============================================================
$s = "SELECT a.*,
(
  SELECT SUM(qty) FROM tb_order_items 
  WHERE id_order=a.id) sum_qty 
FROM tb_order a 
WHERE a.status_order = 0 
AND username = '$username' 
AND a.delete_at is null -- belum dihapus
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$order = mysqli_fetch_assoc($q);

if ($order) {
  if ($order['sum_qty']) {
    // stop("Order status kosong tapi ada QTY nya. id [$order[id]]");
    jsurl("?order_detail&id_order=$order[id]");
  } else {
    $id_order = $order['id'];
  }
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
  SELECT harga FROM tb_harga_fixed 
  WHERE username = '$username' -- reseller yang ini
  AND id_produk = a.id -- produk yang ini
  ORDER BY created_at DESC LIMIT 1 -- hanya harga terbaru 
  ) harga_fixed 
FROM tb_produk a 
WHERE a.status=1 -- masih diproduksi
ORDER BY 
a.tanggal_produksi DESC -- urutkan berdasarkan tanggal produksi
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$row_produk = '';
$i = 0;
$total_bayar = 0;
$terbaru = " $img_stars (terbaru) $img_stars";
while ($d = mysqli_fetch_assoc($q)) {
  $id = $d['id'];
  $i++;
  $tr_class = '';

  if ($d['harga_fixed']) { // harga fixed

    $qty = ''; // ZZZ
    $jumlah = 0; // ZZZ




    $kalkulasi_row = "
      <div class='nominal d-flex gap-2'>
        <div>$d[harga_fixed]</div>
        <div>x</div>
        <div class=hideit id=harga_fixed--$id>$d[harga_fixed]</div>
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
        <div class=flex-fill>
          <input disabled class='form-control nominal jumlah input-show' id='jumlah--$id' value='$jumlah' />
        </div>
      </div>    
    ";
  } else { // tidak ada harga fixed, gunakan harga default by system
    if ($user['jarak']) {
    } else {
      stop("Alamat Kecamatan Anda belum diketahui jaraknya, sehingga belum ada harga pasti untuk Anda. Silahkan hubungi admin terlebih dahulu.
      <a class='btn btn-success w-100 mt-2' href=?tanya_harga>Tanya Harga untuk Anda</a> 
      ");
    }
    $s2 = "SELECT * FROM tb_rule WHERE max_jarak >= $user[jarak]";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $li_harga = '';
    $rharga_jual = [];
    $min = null;
    $max = null;
    while ($d2 = mysqli_fetch_assoc($q2)) {
      $min = $min ?? $d2['min_order'];
      $max = $d2['min_order'];
      $harga_jual = round($d['harga_beli'] * $d2['persen_up'] / 10000) * 100;
      $rharga_jual[$d2['min_order']] = $harga_jual;
      $li_harga .= "<li class=li-harga id=li-harga--$d2[min_order]--$d[id]--$harga_jual>min. order: $d2[min_order] => <span class='harga-jual' id=harga-jual--$d2[min_order]--$d[id]>$harga_jual</span></li>";
    }


    $tr_class = 'tanyakan';
    // $kalkulasi_row = "<a href=?tanyakan&hal=ZZZ class='miring'>Tanyakan Harga untuk Anda</a>";

    $harga_default = $rharga_jual[$default_qty] ?? '???';

    $kalkulasi_row = "
      <div class='nominal d-flex gap-2'>
        <div id=harga_fixed--$id>$harga_default</div>
        <div>x</div>
        <div>
          <input 
            placeholder='QTY'
            type=number 
            class='form-control qty' 
            name='qty[$id]'
            id='qty--$id'
            min='$min' 
            max='$max' 
          />
        </div>
        <div>=</div>
        <div class=flex-fill>
          <input disabled class='form-control nominal jumlah input-show' id='jumlah--$id' value='0' />
        </div>
        <div class='hideit' id=clear--$id>
          <span class='btn btn-sm btn-secondary btn-clear' id=btn-clear--$id>C</span>
        </div>        
      </div>    


      <ul class='my-3' id=ul--$id>
        $li_harga
      </ul>
      <a href=?tanyakan&hal=ZZZ class='miring'>Tanyakan Harga untuk Anda</a>
    ";


    $input_qty = '-';
    $jumlah_show = '-';
  }

  $tanggal_produksi = tanggal($d['tanggal_produksi']);

  $row_produk .= "
    <div id=row--$id class='$tr_class row-produk'>
      <div class='d-flex gap-2'>
        <div class='pt1'>
          <input type=checkbox class='checkbox' id=checkbox--$id >
        </div>
        <div>
          <div class='mb-2 darkblue '>
            <label for=checkbox--$id class='hover bold'>$d[nama] $terbaru</label>
            <div class='f12 abu'>
              <b>Produksi</b>: $tanggal_produksi
            </div>
          </div>
          <div class='blok-kalkulasi hideit' id=blok-kalkulasi--$id>
            $kalkulasi_row
          </div>
        </div>
      </div>
    </div>
  ";
  $terbaru = '';
}





?>
<link rel="stylesheet" href="assets/css/add_order.css">
<div class="blok-order d-flex justify-content-center align-items-center">

  <form method="POST" class="form-order">
    <div class="text-center">
      <h2 class="">Form Pemesanan</h2>
      <div class="mb-4 f14 abu">(Add Order)</div>
      <?php
      if (!$user['jarak']) {
        echo '<pre>';
        print_r('zzz');
        echo '<b style=color:red>Developer SEDANG DEBUGING: exit(true)</b></pre>';
        exit;
      }


      ?>
      <p>Silahkan Anda ceklis produk yang tersedia lalu masukan QTY!</p>
      <p><b>Jarak</b>: <?= $user['jarak'] ?>km</p>
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
        <span class="btn btn-primary w-100" onclick="alert(`Silahkan isi QTY terlebih dahulu.`)">Silahkan Ceklis Produk dan isi QTY...</span>
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
      let selectedHargaJual = 0;
      // ===============================================
      // PENCARIAN HARGA TERDEKAT BERDASARKAN QTY
      // ===============================================
      // console.log(id);
      $('.harga-jual').each((val, idx) => {
        let rid2 = idx.id.split("--");
        let minOrder = parseInt(rid2[1]);
        let idProduk = rid2[2];

        if (id == idProduk) {
          let hargaJual = parseInt($(`#harga-jual--${minOrder}--${idProduk}`).text());
          if (minOrder <= qty) {
            selectedHargaJual = hargaJual;
          }
          // console.log(id, minOrder, idProduk, hargaJual, selectedHargaJual);

        }



      })




      let harga = parseInt($('#harga_fixed--' + id).text());
      console.log(id, selectedHargaJual, harga);
      // let harga = selectedHargaJual;
      $('#harga_fixed--' + id).text(selectedHargaJual);

      if (!isNaN(selectedHargaJual) && !isNaN(qty)) {
        let jumlah = selectedHargaJual * qty;
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
    $(".li-harga").click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let minOrder = rid[1];
      let idProduk = rid[2];
      let harga = rid[3];
      // console.log(aksi, id, id2);

      $('#harga_fixed--' + idProduk).text(harga);
      $('#qty--' + idProduk).val(minOrder);
      $('#ul--' + idProduk).slideUp();
      $('#clear--' + idProduk).fadeIn();

      hitung_total();
    });

    $(".btn-clear").click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let idProduk = rid[1];
      $('#qty--' + idProduk).val('');
      $('#jumlah--' + idProduk).val('0');
      $('#ul--' + idProduk).slideDown();
      $('#clear--' + idProduk).fadeOut();

      hitung_total();
    });

  });
</script>