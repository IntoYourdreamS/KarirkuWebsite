<?php 
include '../../function/supabase.php'; 

// --- DEBUG & DATA FETCHING ---
$debug_messages = [];

// 1. Cek Total Data
$total_pengguna   = supabaseQuery('pengguna', ['select' => 'id_pengguna'], ['count' => 'exact'])['count'] ?? 0;
$total_perusahaan = supabaseQuery('perusahaan', ['select' => 'id_perusahaan'], ['count' => 'exact'])['count'] ?? 0;
$total_lowongan   = supabaseQuery('lowongan', ['select' => 'id_lowongan'], ['count' => 'exact'])['count'] ?? 0;
$total_pelamar    = supabaseQuery('lamaran', ['select' => 'id_lamaran'], ['count' => 'exact'])['count'] ?? 0;

// 2. Cek Lowongan (Coba Query AMAN tanpa JOIN dulu)
// Kita hapus ', perusahaan(nama)' untuk mengetes apakah data lowongan itu ada.
$res_lowongan = supabaseQuery('lowongan', [
    'select' => '*', // Ambil semua kolom lowongan saja
    'order' => 'created_at.desc',
    'limit' => 3
]);

if (isset($res_lowongan['error'])) {
    $debug_messages[] = "Error Lowongan: " . print_r($res_lowongan, true);
    $list_lowongan = [];
} else {
    $list_lowongan = $res_lowongan['data'] ?? [];
    // Cek manual apakah kosong
    if (empty($list_lowongan)) {
        $debug_messages[] = "Info: Tabel Lowongan terbaca sukses, tapi isinya KOSONG (0 baris).";
    }
}

// 3. Cek Perusahaan
$res_perusahaan = supabaseQuery('perusahaan', [
    'select' => '*',
    'order' => 'created_at.desc',
    'limit' => 3
]);

if (isset($res_perusahaan['error'])) {
    $debug_messages[] = "Error Perusahaan: " . print_r($res_perusahaan, true);
    $list_perusahaan = [];
} else {
    $list_perusahaan = $res_perusahaan['data'] ?? [];
     if (empty($list_perusahaan)) {
        $debug_messages[] = "Info: Tabel Perusahaan terbaca sukses, tapi isinya KOSONG (0 baris).";
    }
}

// 4. Data Chart
$res_chart = supabaseQuery('pengguna', ['select' => 'created_at']);
$chart_data = array_fill(0, 12, 0); 
if (isset($res_chart['success']) && $res_chart['success'] && is_array($res_chart['data'])) {
    foreach ($res_chart['data'] as $user) {
        if (is_array($user) && isset($user['created_at'])) {
            $m = (int)date('n', strtotime($user['created_at'])) - 1;
            if(isset($chart_data[$m])) $chart_data[$m]++;
        }
    }
}

include 'header.php'; 
include 'topbar.php'; 
include 'sidebar.php'; 
?>

<div class="main-content">

  <?php if (!empty($debug_messages)): ?>
      <div class="alert alert-danger mb-4">
          <strong>Debug Info (Kenapa data tidak muncul?):</strong>
          <ul class="mb-0 mt-2">
              <?php foreach($debug_messages as $msg): ?>
                  <li><pre><?= htmlspecialchars($msg) ?></pre></li>
              <?php endforeach; ?>
          </ul>
      </div>
  <?php endif; ?>
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card text-center p-4 h-100 border-0 shadow-sm">
        <h6 class="text-muted mb-2">Total Pengguna</h6>
        <h2 class="fw-bold text-dark mb-0"><?= number_format($total_pengguna) ?></h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center p-4 h-100 border-0 shadow-sm">
        <h6 class="text-muted mb-2">Total Perusahaan</h6>
        <h2 class="fw-bold text-dark mb-0"><?= number_format($total_perusahaan) ?></h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center p-4 h-100 border-0 shadow-sm">
        <h6 class="text-muted mb-2">Lowongan Aktif</h6>
        <h2 class="fw-bold text-dark mb-0"><?= number_format($total_lowongan) ?></h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center p-4 h-100 border-0 shadow-sm">
        <h6 class="text-muted mb-2">Total Pelamar</h6>
        <h2 class="fw-bold text-dark mb-0"><?= number_format($total_pelamar) ?></h2>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-md-6">
      <div class="card p-4 border-0 shadow-sm h-100">
        <h6 class="fw-bold mb-4">Pertumbuhan Pengguna</h6>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="userChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-4 border-0 shadow-sm h-100">
        <h6 class="fw-bold mb-4">Statistik Lamaran</h6>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="applyChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="card p-4 mb-4 border-0 shadow-sm">
    <h6 class="fw-bold mb-4">Lowongan Terbaru</h6>
    <?php if (!empty($list_lowongan) && is_array($list_lowongan)): ?>
        <?php foreach($list_lowongan as $job): ?>
             <?php if (is_array($job)): ?>
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <div>
                        <span class="fw-bold d-block text-dark"><?= htmlspecialchars($job['judul'] ?? 'No Title') ?></span>
                        <small class="text-muted">
                            <?= htmlspecialchars($job['lokasi'] ?? 'Lokasi') ?>
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success rounded-pill px-3">Aktif</span>
                        <small class="d-block text-muted mt-1"><?= isset($job['created_at']) ? date('d M Y', strtotime($job['created_at'])) : '-' ?></small>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning">
            Belum ada data lowongan yang ditemukan. <br>
            Pastikan tabel 'lowongan' di Supabase sudah diisi data (Insert Row).
        </div>
    <?php endif; ?>
  </div>

  <div class="card p-4 border-0 shadow-sm">
    <h6 class="fw-bold mb-4">Perusahaan Baru</h6>
    <?php if (!empty($list_perusahaan) && is_array($list_perusahaan)): ?>
        <?php foreach($list_perusahaan as $comp): ?>
            <?php if (is_array($comp)): ?>
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light rounded p-2">
                            <i class="bi bi-building text-secondary fs-4"></i>
                        </div>
                        <div>
                            <span class="fw-bold d-block"><?= htmlspecialchars($comp['nama'] ?? 'Nama PT') ?></span>
                            <small class="text-muted">Bergabung: <?= isset($comp['created_at']) ? date('d M Y', strtotime($comp['created_at'])) : '-' ?></small>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark border">Mitra</span>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning">
            Belum ada data perusahaan yang ditemukan. <br>
            Pastikan tabel 'perusahaan' di Supabase sudah diisi data.
        </div>
    <?php endif; ?>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const userData = <?= json_encode(array_values($chart_data)) ?>;
const ctxUser = document.getElementById('userChart');
if (ctxUser) {
    new Chart(ctxUser, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'User Baru',
                data: userData,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] } }, x: { grid: { display: false } } } }
    });
}
const ctxApply = document.getElementById('applyChart');
if (ctxApply) {
    new Chart(ctxApply, {
        type: 'bar',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{ label: 'Lamaran', data: [0,0,0,0,0,0,0], backgroundColor: '#343a40', borderRadius: 4 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } }
    });
}
</script>
<?php include 'footer.php'; ?>