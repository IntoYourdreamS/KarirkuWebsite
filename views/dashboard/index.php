<?php include 'header.php'; ?>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card text-center p-3">
        <h6>Total pengguna</h6>
        <h3 class="fw-bold text-primary">250</h3>
        <small>terdaftar</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center p-3">
        <h6>Total perusahaan</h6>
        <h3 class="fw-bold text-primary">70</h3>
        <small>aktif</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center p-3">
        <h6>Total lowongan aktif</h6>
        <h3 class="fw-bold text-primary">750</h3>
        <small>terdaftar</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center p-3">
        <h6>Total pelamar</h6>
        <h3 class="fw-bold text-primary">1.720</h3>
        <small>terdaftar</small>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-md-6">
      <div class="card p-3">
        <h6 class="text-center fw-semibold mb-3">Pertumbuhan pengguna per bulan</h6>
        <canvas id="userChart" height="200"></canvas>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3">
        <h6 class="text-center fw-semibold mb-3">Jumlah lamaran per minggu</h6>
        <canvas id="applyChart" height="200"></canvas>
      </div>
    </div>
  </div>

  <div class="card p-3 mb-4">
    <h6 class="fw-bold border-bottom pb-2">Lowongan Terbaru</h6>
    <div class="d-flex justify-content-between border-bottom py-2">
      <span>UI/UX Designer</span><span>PT Kreatif Nusantara</span><span>Jakarta</span><span>5 Nov 2025</span><span class="badge bg-success">Aktif</span>
    </div>
    <div class="d-flex justify-content-between border-bottom py-2">
      <span>Software Engineer</span><span>PT Inovasi Digital</span><span>Jakarta</span><span>5 Nov 2025</span><span class="badge bg-secondary">Lewat</span>
    </div>
    <div class="d-flex justify-content-between py-2">
      <span>Marketing Executive</span><span>CV Sejahtera Abadi</span><span>Jakarta</span><span>5 Nov 2025</span><span class="badge bg-success">Aktif</span>
    </div>
  </div>

  <div class="card p-3">
    <h6 class="fw-bold border-bottom pb-2">Perusahaan Aktif</h6>
    <div class="d-flex justify-content-between border-bottom py-2">
      <span>PT Kreatif Nusantara</span><span>20 Lowongan</span><span>Bergabung 5 Nov 2025</span><span class="badge bg-success">Aktif</span>
    </div>
    <div class="d-flex justify-content-between border-bottom py-2">
      <span>PT Inovasi Digital</span><span>10 Lowongan</span><span>Bergabung 5 Nov 2025</span><span class="badge bg-danger">Ditutup</span>
    </div>
    <div class="d-flex justify-content-between py-2">
      <span>CV Sejahtera Abadi</span><span>2 Lowongan</span><span>Bergabung 5 Nov 2025</span><span class="badge bg-success">Aktif</span>
    </div>
  </div>
</div> <script src="./assets/script.js">
  
// Javascript Anda
// ...

</script>

<?php include 'footer.php'; ?>