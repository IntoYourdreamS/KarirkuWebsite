<?php
session_start();
require_once __DIR__ . '/../function/supabase.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user dari tabel pengguna
$user = getUserById($user_id);
if (!$user) {
    header('Location: login.php');
    exit;
}

// Ambil data profil dari tabel pencaker
$pencaker = getPencakerByUserId($user_id);

// Ambil lowongan yang disimpan (favorit)
$savedJobs = [];
if ($pencaker) {
    $result = supabaseQuery('favorit_lowongan', [
        'select' => '*, lowongan(*, perusahaan(nama_perusahaan, logo_url))',
        'id_pencaker' => 'eq.' . $pencaker['id_pencaker'],
        'order' => 'dibuat_pada.desc'
    ]);

    if ($result['success']) {
        $savedJobs = $result['data'];
    }
}

// Ambil riwayat lamaran
$applications = [];
if ($pencaker) {
    $result = supabaseQuery('lamaran', [
        'select' => '*, lowongan(judul, lokasi, perusahaan(nama_perusahaan, logo_url))',
        'id_pencaker' => 'eq.' . $pencaker['id_pencaker'],
        'order' => 'dibuat_pada.desc'
    ]);

    if ($result['success']) {
        $applications = $result['data'];
    }
}

// Handle Upload CV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $file = $_FILES['cv_file'];

    // Validasi file
    $allowedTypes = ['application/pdf'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowedTypes)) {
        $error = "Hanya file PDF yang diperbolehkan.";
    } elseif ($file['size'] > $maxSize) {
        $error = "Ukuran file maksimal 5MB.";
    } else {
        // Upload ke Supabase Storage
        $fileName = 'cv_' . $user_id . '_' . time() . '.pdf';
        $uploadResult = supabaseStorageUpload('cv', $fileName, $file);

        if ($uploadResult['success']) {
            $cvUrl = getStoragePublicUrl('cv', $fileName);

            // Update profil pencaker dengan URL CV
            if ($pencaker) {
                // Hapus CV lama jika ada
                if (!empty($pencaker['cv_url'])) {
                    $oldPath = str_replace(getStoragePublicUrl('cv', ''), '', $pencaker['cv_url']);
                    supabaseStorageDelete('cv', $oldPath);
                }

                $updateResult = updatePencakerProfile($pencaker['id_pencaker'], [
                    'cv_url' => $cvUrl
                ]);

                if ($updateResult['success']) {
                    $success = "CV berhasil diupload!";
                    $pencaker['cv_url'] = $cvUrl;
                } else {
                    $error = "Gagal menyimpan CV ke database.";
                }
            }
        } else {
            $error = "Gagal mengupload CV.";
        }
    }
}

// Handle Delete CV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cv'])) {
    if ($pencaker && !empty($pencaker['cv_url'])) {
        $oldPath = str_replace(getStoragePublicUrl('cv', ''), '', $pencaker['cv_url']);
        $deleteResult = supabaseStorageDelete('cv', $oldPath);

        if ($deleteResult['success']) {
            $updateResult = updatePencakerProfile($pencaker['id_pencaker'], [
                'cv_url' => null
            ]);

            if ($updateResult['success']) {
                $success = "CV berhasil dihapus!";
                $pencaker['cv_url'] = null;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Aktivitas - Karirku</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="../assets/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../assets/lib/animate/animate.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/aktivitas.css">
</head>

<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <div class="container-fluid px-4 px-lg-5 d-flex align-items-center justify-content-between">
            <a href="../index.php" class="navbar-brand d-flex align-items-center text-center py-0">
                <img src="../assets/img/logo.png" alt="Karirku">
            </a>

            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ms-0 mt-1">
                    <a href="../index.php" class="nav-item nav-link">Home</a>
                    <a href="job-list.php" class="nav-item nav-link">Cari Pekerjaan</a>
                </div>
                <div class="auth-buttons d-flex align-items-center">
                    <span class="me-3">Halo, <?php echo htmlspecialchars($user['nama_lengkap']); ?></span>
                    <a href="logout.php" class="btn-login">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <div class="activity-container">
        <!-- Back Button -->
        <a href="profile.php" class="back-button">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Cards Grid -->
        <div class="cards-grid">
            <!-- Card 1: Lamaran Saya -->
            <div class="activity-card">
                <div class="card-header">
                    <h6>Lamaran Saya</h6>
                </div>

                <div class="applications-table">
                    <?php if (empty($applications)): ?>
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Belum ada lamaran</p>
                        </div>
                    <?php else: ?>
                        <div id="applicationList">
                            <?php foreach ($applications as $index => $app):
                                $job = $app['lowongan'];
                                $company = $job['perusahaan'];
                                $isHidden = $index >= 5 ? 'style="display:none;"' : '';
                            ?>
                                <div class="application-row" <?php echo $isHidden; ?>>
                                    <img src="<?php echo htmlspecialchars($company['logo_url'] ?? '../assets/img/default-company.png'); ?>"
                                        alt="<?php echo htmlspecialchars($company['nama_perusahaan']); ?>"
                                        class="job-logo">
                                    <div class="job-info">
                                        <div class="job-title"><?php echo htmlspecialchars($job['judul']); ?></div>
                                        <div class="job-company"><?php echo htmlspecialchars($company['nama_perusahaan']); ?></div>
                                        <div class="job-location">
                                            <i class="bi bi-calendar"></i>
                                            <?php echo date('d M Y', strtotime($app['dibuat_pada'])); ?>
                                        </div>
                                    </div>
                                    <span class="status-badge status-<?php echo $app['status']; ?>">
                                        <?php
                                        $statusText = [
                                            'diproses' => 'Diproses',
                                            'diterima' => 'Diterima',
                                            'ditolak' => 'Ditolak',
                                            'lanjutan' => 'Lanjutan'
                                        ];
                                        echo $statusText[$app['status']] ?? 'Unknown';
                                        ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($applications) > 5): ?>
                            <div class="show-toggle">
                                <button class="btn-toggle" id="toggleApplicationBtn" onclick="toggleApplications()">
                                    Tampilkan Lebih Banyak
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card 2: Lowongan Disimpan -->
            <div class="activity-card">
                <div class="card-header">
                    <h6>Lowongan Disimpan</h6>
                </div>

                <div class="saved-jobs-table">
                    <?php if (empty($savedJobs)): ?>
                        <div class="empty-state">
                            <i class="bi bi-bookmark"></i>
                            <p>Belum ada lowongan yang disimpan</p>
                        </div>
                    <?php else: ?>
                        <div id="jobList">
                            <?php foreach ($savedJobs as $index => $saved):
                                $job = $saved['lowongan'];
                                $company = $job['perusahaan'];
                                $isHidden = $index >= 5 ? 'style="display:none;"' : '';
                            ?>
                                <div class="job-row" <?php echo $isHidden; ?>>
                                    <img src="<?php echo htmlspecialchars($company['logo_url'] ?? '../assets/img/default-company.png'); ?>"
                                        alt="<?php echo htmlspecialchars($company['nama_perusahaan']); ?>"
                                        class="job-logo">
                                    <div class="job-info">
                                        <div class="job-title"><?php echo htmlspecialchars($job['judul']); ?></div>
                                        <div class="job-company"><?php echo htmlspecialchars($company['nama_perusahaan']); ?></div>
                                        <div class="job-location">
                                            <i class="bi bi-geo-alt"></i>
                                            <?php echo htmlspecialchars($job['lokasi']); ?>
                                        </div>
                                    </div>
                                    <div class="job-actions">
                                        <button class="btn-view" onclick="window.location.href='job-detail.php?id=<?php echo $job['id_lowongan']; ?>'">
                                            Lihat
                                        </button>
                                        <button class="btn-remove" onclick="removeFavorite(<?php echo $saved['id_favorit']; ?>)">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($savedJobs) > 5): ?>
                            <div class="show-toggle">
                                <button class="btn-toggle" id="toggleJobBtn" onclick="toggleJobs()">
                                    Tampilkan Lebih Banyak
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card 3: CV Saya -->
            <div class="activity-card">
                <div class="card-header">
                    <h6>CV Saya</h6>
                </div>

                <?php if (!$pencaker || empty($pencaker['cv_url'])): ?>
                    <!-- Upload CV -->
                    <div class="cv-upload-section">
                        <i class="bi bi-cloud-upload cv-icon"></i>
                        <h3 class="cv-title">Upload CV Anda</h3>
                        <p class="cv-text">Maksimal ukuran file 5MB (Format PDF)</p>

                        <form method="POST" enctype="multipart/form-data" id="cvForm">
                            <div class="file-input-wrapper">
                                <input type="file" name="cv_file" id="cvFile" accept=".pdf" onchange="document.getElementById('cvForm').submit()">
                                <label for="cvFile" class="btn-upload">
                                    <i class="bi bi-upload"></i>
                                    Pilih File CV
                                </label>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Display CV -->
                    <div class="cv-display">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <div class="cv-filename">
                            <?php
                            $cvPath = parse_url($pencaker['cv_url'], PHP_URL_PATH);
                            $cvName = basename($cvPath);
                            echo htmlspecialchars($cvName);
                            ?>
                        </div>
                        <div class="cv-actions">
                            <a href="<?php echo htmlspecialchars($pencaker['cv_url']); ?>" target="_blank" class="btn-download">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <form method="POST" style="display: inline;">
                                <button type="submit" name="delete_cv" class="btn-delete"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus CV?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let showingAllJobs = false;
        let showingAllApplications = false;

        function toggleJobs() {
            const jobRows = document.querySelectorAll('.job-row');
            const toggleBtn = document.getElementById('toggleJobBtn');

            showingAllJobs = !showingAllJobs;

            jobRows.forEach((row, index) => {
                if (index >= 5) {
                    row.style.display = showingAllJobs ? 'flex' : 'none';
                }
            });

            toggleBtn.textContent = showingAllJobs ? 'Tampilkan Lebih Sedikit' : 'Tampilkan Lebih Banyak';
        }

        function toggleApplications() {
            const appRows = document.querySelectorAll('.application-row');
            const toggleBtn = document.getElementById('toggleApplicationBtn');

            showingAllApplications = !showingAllApplications;

            appRows.forEach((row, index) => {
                if (index >= 5) {
                    row.style.display = showingAllApplications ? 'flex' : 'none';
                }
            });

            toggleBtn.textContent = showingAllApplications ? 'Tampilkan Lebih Sedikit' : 'Tampilkan Lebih Banyak';
        }

        function removeFavorite(id) {
            if (confirm('Apakah Anda yakin ingin menghapus lowongan ini dari favorit?')) {
                window.location.href = 'remove-favorite.php?id=' + id;
            }
        }
    </script>
</body>

</html>