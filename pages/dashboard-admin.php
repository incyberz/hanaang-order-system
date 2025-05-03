<div
  class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h2>Dashboard Admin</h2>
</div>

<!-- Dashboard Stats -->
<div class="row">
  <!-- Total Pesanan -->
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-body bg-info text-white">
        <h5 class="card-title">Total Pesanan</h5>
        <p class="card-text">120 Pesanan Masuk</p>
      </div>
    </div>
  </div>
  <!-- Total Pembayaran -->
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-body bg-success text-white">
        <h5 class="card-title">Total Pembayaran</h5>
        <p class="card-text">100 Pembayaran Terverifikasi</p>
      </div>
    </div>
  </div>
  <!-- Total Surat Jalan -->
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-body bg-success text-white">
        <h5 class="card-title">Surat Jalan</h5>
        <p class="card-text">85 Surat Jalan Dicetak</p>
      </div>
    </div>
  </div>
</div>

<!-- Recent Orders -->
<div class="row">
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header bg-info text-white">
        <h5>Pesanan Terbaru</h5>
      </div>
      <div class="card-body gradasi-toska">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Reseller</th>
              <th>Pesanan</th>
              <th>Status Pembayaran</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>John Doe</td>
              <td>20 Cup Minuman A</td>
              <td>
                <span class="badge bg-warning">Menunggu Pembayaran</span>
              </td>
              <td>
                <button class="btn btn-primary">
                  Teruskan ke Keuangan
                </button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Jane Smith</td>
              <td>50 Cup Minuman B</td>
              <td><span class="badge bg-success">Dibayar</span></td>
              <td>
                <button class="btn btn-success">
                  Cetak Surat Jalan
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>