<?php
require __DIR__ . '/../config/supabase.php';

session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $_SESSION['user_name'] ?? '';

function getDaftarLokasi()
{
    try {
        // Query untuk mendapatkan lokasi unik dari lowongan yang statusnya open
        $params = [
            'select' => 'lokasi',
            'status' => 'eq.open',
            'order' => 'lokasi.asc'
        ];

        $response = supabaseQuery('lowongan', $params);

        if (!$response['success']) {
            throw new Exception('Failed to fetch locations: ' . ($response['error'] ?? 'Unknown error'));
        }

        $data = $response['data'];

        // Ekstrak lokasi unik
        $lokasiUnik = [];
        foreach ($data as $row) {
            if (!empty($row['lokasi']) && !in_array($row['lokasi'], $lokasiUnik)) {
                $lokasiUnik[] = $row['lokasi'];
            }
        }

        // Urutkan secara alfabet
        sort($lokasiUnik);

        return $lokasiUnik;
    } catch (Exception $e) {
        error_log("Error in getDaftarLokasi: " . $e->getMessage());
        return [];
    }
}

// Fungsi untuk melakukan pencarian lowongan
function searchLowongan($keyword = '', $lokasi = '', $page = 1, $limit = 5)
{
    // Validasi parameter
    $page = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $offset = ($page - 1) * $limit;

    try {
        // Build parameter query
        $params = [
            'select' => '*',
            'limit' => $limit,
            'offset' => $offset,
            'order' => 'dibuat_pada.desc',
            'status' => 'eq.open'
        ];

        // Filter berdasarkan keyword
        if (!empty($keyword)) {
            $params['or'] = "(judul.ilike.%{$keyword}%,kategori.ilike.%{$keyword}%,deskripsi.ilike.%{$keyword}%,kualifikasi.ilike.%{$keyword}%)";
        }

        // Filter berdasarkan lokasi
        if (!empty($lokasi) && $lokasi !== 'semua') {
            $params['lokasi'] = 'ilike.%' . $lokasi . '%';
        }

        // Eksekusi query untuk data
        $response = supabaseQuery('lowongan', $params);

        if (!$response['success']) {
            throw new Exception('Failed to fetch data: ' . ($response['error'] ?? 'Unknown error'));
        }

        $data = $response['data'];

        // Hitung total data untuk pagination - PERBAIKAN DI SINI
        $countParams = [
            'select' => 'id_lowongan',
            'status' => 'eq.open'
        ];

        if (!empty($keyword)) {
            $countParams['or'] = "(judul.ilike.%{$keyword}%,kategori.ilike.%{$keyword}%,deskripsi.ilike.%{$keyword}%,kualifikasi.ilike.%{$keyword}%)";
        }

        if (!empty($lokasi) && $lokasi !== 'semua') {
            $countParams['lokasi'] = 'ilike.%' . $lokasi . '%';
        }

        // Gunakan supabaseQuery dengan option count
        $countResponse = supabaseQuery('lowongan', $countParams, ['count' => 'exact']);

        // Ambil total dari count response
        $totalData = $countResponse['count'] ?? count($data);
        $totalPages = $totalData > 0 ? ceil($totalData / $limit) : 1;

        return [
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_data' => $totalData,
                'limit' => $limit,
                'offset' => $offset
            ],
            'search_params' => [
                'keyword' => $keyword,
                'lokasi' => $lokasi
            ]
        ];
    } catch (Exception $e) {
        error_log("Error in searchLowongan: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'data' => [],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => 1,
                'total_data' => 0,
                'limit' => $limit,
                'offset' => $offset
            ]
        ];
    }
}

// Ambil daftar lokasi dari database
$daftarLokasi = getDaftarLokasi();

// Ambil parameter pencarian dari URL
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;

// Jalankan pencarian
$result = searchLowongan($keyword, $lokasi, $page, $limit);

// Extract data dari result
$data = $result['data'] ?? [];
$totalPages = $result['pagination']['total_pages'] ?? 1;
$currentPage = $result['pagination']['current_page'] ?? 1;
$totalData = $result['pagination']['total_data'] ?? 0;
$searchKeyword = $result['search_params']['keyword'] ?? '';
$searchLokasi = $result['search_params']['lokasi'] ?? '';

// Debug info
if (isset($_GET['debug'])) {
    echo "<!-- Debug: Success=" . ($result['success'] ? 'true' : 'false') .
        ", Data Count=" . count($data) .
        ", Lokasi Count=" . count($daftarLokasi) .
        ", Error=" . ($result['error'] ?? 'None') . " -->";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>JobEntry - Job Portal Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="../assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../assets/css/style.css" rel="stylesheet">

    <style>
        .search-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .search-input i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .search-input input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .location-select {
            min-width: 180px;
        }

        .location-select select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #666;
        }

        .filter-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #666;
        }

        .search-button {
            background-color: #001f66;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-button:hover {
            background-color: #002c77;
        }

        .search-results-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-left: 4px solid #001f66;
        }

        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }

            .search-input,
            .location-select {
                min-width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <div class="container-fluid px-4 px-lg-5 d-flex align-items-center justify-content-between">
            <a href="index.php" class="navbar-brand d-flex align-items-center text-center py-0">
                <img src="../assets/img/logo.png" alt="">
            </a>

            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ms-0 mt-1">
                    <a href="../index.php" class="nav-item nav-link active">HOME</a>
                    <a href="#" class="nav-item nav-link">LOKER</a>
                </div>

                <div class="auth-buttons d-flex align-items-center">
                    <?php if ($isLoggedIn): ?>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i><?= htmlspecialchars($userName) ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i>Profil</a></li>
                                <li><a class="dropdown-item" href="my-applications.php"><i class="fas fa-briefcase me-2"></i>Lamaran Saya</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="return confirmLogout()">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="register.php" class="btn-register">Register</a>
                        <a href="login.php" class="btn-login">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header End -->
    <div class="container-xxl py-5 bg-dark page-header mb-5">
        <div class="container my-5 pt-5 pb-4">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Job List</h1>
            <nav aria-label="breadcrumb">
            </nav>
        </div>
    </div>
    <!-- Header End -->

    <!-- Jobs Start -->
    <div class="container py-5">
        <!-- Search Section -->
        <h2 class="text-center mb-4 fw-bold">Daftar Lowongan</h2>

        <!-- Form Pencarian -->
        <div class="search-container">
            <form class="search-form" method="GET" action="">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="keyword" placeholder="Cari judul pekerjaan, perusahaan, kata kunci"
                        value="<?= htmlspecialchars($searchKeyword) ?>">
                </div>

                <div class="location-select">
                    <select name="lokasi">
                        <option value="" <?= empty($searchLokasi) ? 'selected' : '' ?>>Semua Lokasi</option>
                        <?php if (!empty($daftarLokasi)): ?>
                            <?php foreach ($daftarLokasi as $lok): ?>
                                <option value="<?= htmlspecialchars(strtolower($lok)) ?>"
                                    <?= $searchLokasi == strtolower($lok) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($lok) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback jika tidak ada data lokasi -->
                            <option value="jakarta" <?= $searchLokasi == 'jakarta' ? 'selected' : '' ?>>Jakarta</option>
                            <option value="surabaya" <?= $searchLokasi == 'surabaya' ? 'selected' : '' ?>>Surabaya</option>
                            <option value="bandung" <?= $searchLokasi == 'bandung' ? 'selected' : '' ?>>Bandung</option>
                            <option value="malang" <?= $searchLokasi == 'malang' ? 'selected' : '' ?>>Malang</option>
                            <option value="jember" <?= $searchLokasi == 'jember' ? 'selected' : '' ?>>Jember</option>
                        <?php endif; ?>
                    </select>
                </div>

                <button type="submit" class="search-button">
                    <i class="fas fa-search me-2"></i>Cari
                </button>
            </form>
        </div>

        <!-- Info Hasil Pencarian -->
        <?php if (!empty($searchKeyword) || (!empty($searchLokasi) && $searchLokasi !== 'semua')): ?>
            <div class="search-results-info">
                <h6 class="mb-2">Hasil Pencarian:</h6>
                <?php if (!empty($searchKeyword)): ?>
                    <span class="badge bg-primary me-2">Kata kunci: "<?= htmlspecialchars($searchKeyword) ?>"</span>
                <?php endif; ?>
                <?php if (!empty($searchLokasi) && $searchLokasi !== 'semua'): ?>
                    <span class="badge bg-secondary">Lokasi: <?= htmlspecialchars(ucfirst($searchLokasi)) ?></span>
                <?php endif; ?>
                <span class="badge bg-info ms-2">Total: <?= $totalData ?> lowongan</span>
            </div>
        <?php endif; ?>

        <!-- Daftar Lowongan -->
        <?php if (!empty($data)) : ?>
            <?php foreach ($data as $row): ?>
                <div class="job-item p-4 mb-4 border rounded shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="../assets/img/logo.png" alt="logo" class="img-fluid" style="max-height: 60px;">
                        </div>
                        <div class="col-md-7">
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($row['judul'] ?? 'Judul tidak tersedia') ?></h5>
                            <p class="mb-1 text-muted">
                                <i class="fa fa-map-marker-alt me-2"></i><?= htmlspecialchars($row['lokasi'] ?? 'Lokasi tidak tersedia') ?> |
                                <i class="fa fa-tags me-2"></i><?= htmlspecialchars($row['kategori'] ?? 'Kategori tidak tersedia') ?>
                            </p>
                            <p class="mb-1 text-muted">
                                <i class="fa fa-briefcase me-2"></i><?= htmlspecialchars($row['tipe_pekerjaan'] ?? 'Tipe tidak tersedia') ?> |
                                <i class="fa fa-coins me-2"></i><?= htmlspecialchars($row['gaji_range'] ?? 'Gaji tidak tersedia') ?> |
                                <i class="fa fa-building me-2"></i><?= htmlspecialchars($row['mode_kerja'] ?? 'Mode kerja tidak tersedia') ?>
                            </p>
                            <p class="mb-0"><?= htmlspecialchars(substr($row['deskripsi'] ?? '', 0, 150)) ?>...</p>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="#" class="btn btn-primary rounded-pill px-4">Apply Now</a>
                            <div class="text-muted mt-2">
                                <i class="fa fa-hourglass-half me-2"></i>
                                Batas: <?= !empty($row['batas_tanggal']) ? date('d M Y', strtotime($row['batas_tanggal'])) : 'Tidak ditentukan' ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada lowongan yang ditemukan</h5>
                <?php if ($totalData === 0 && empty($searchKeyword) && empty($searchLokasi)): ?>
                    <p class="text-muted">Belum ada lowongan yang tersedia saat ini</p>
                    <!-- Debug info untuk developer -->
                    <?php if (isset($_GET['debug'])): ?>
                        <div class="alert alert-warning mt-3">
                            <small>
                                Debug Info:<br>
                                Success: <?= $result['success'] ? 'true' : 'false' ?><br>
                                Error: <?= $result['error'] ?? 'None' ?><br>
                                Data Count: <?= count($data) ?><br>
                                Total Data: <?= $totalData ?>
                            </small>
                        </div>
                    <?php endif; ?>
                <?php elseif (!empty($searchKeyword) || (!empty($searchLokasi) && $searchLokasi !== 'semua')): ?>
                    <p class="text-muted">Coba ubah kata kunci atau filter lokasi Anda</p>
                    <a href="?keyword=&lokasi=" class="btn btn-primary mt-2">Tampilkan Semua Lowongan</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="d-flex justify-content-center mt-4">
                <nav>
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=<?= $currentPage - 1 ?>&keyword=<?= urlencode($searchKeyword) ?>&lokasi=<?= urlencode($searchLokasi) ?>">
                                    « Prev
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link"
                                    href="?page=<?= $i ?>&keyword=<?= urlencode($searchKeyword) ?>&lokasi=<?= urlencode($searchLokasi) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=<?= $currentPage + 1 ?>&keyword=<?= urlencode($searchKeyword) ?>&lokasi=<?= urlencode($searchLokasi) ?>">
                                    Next »
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
    <!-- Jobs End -->

    <?php include "include/footer.php" ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/lib/wow/wow.min.js"></script>
    <script src="../assets/lib/easing/easing.min.js"></script>
    <script src="../assets/lib/waypoints/waypoints.min.js"></script>
    <script src="../assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    <?php include "include/logout-modal.php" ?>
</body>

</html>