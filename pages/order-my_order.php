<div class="card mb-3">
  <div class="card-header bg-info text-white">
    <h5>My Order (Pesanan Saya)</h5>
  </div>
  <div class="card-body gradasi-toska">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Total Item Pesanan</th>
          <th class="text-end">Total Bayar</th>
          <th>Status Transaksi</th>
          <th>Lihat Detail</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>1 April 2024</td>
          <td>100 cup</td>
          <td class="text-end">700.000</td>
          <td><span class="badge bg-success">Sukses</span></td>
          <td>
            <a href="?order-detail&id_order=ZZZ">
              <?= $img_detail ?>
            </a>
          </td>
        </tr>
        <tr>
          <td>2</td>
          <td>10 April 2024</td>
          <td>120 cup</td>
          <td class="text-end">0</td>
          <td><span class="badge bg-secondary">Anda Batalkan</span></td>
          <td>-</td>
        </tr>
        <tr>
          <td>3</td>
          <td>15 April 2024</td>
          <td>150 cup</td>
          <td class="text-end">1.050.000</td>
          <td><span class="badge bg-warning">Barang Sedang Dikirim</span></td>
          <td>
            <a href="?order-detail&id_order=ZZZ">
              <?= $img_detail ?>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>