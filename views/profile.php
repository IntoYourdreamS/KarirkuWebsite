<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Profile - Karirku</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

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
    <link href="../assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../assets/css/profile.css" rel="stylesheet">

    <style>
        /* Container */
        .profile-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Main Profile Card */
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            position: relative;
        }

        .profile-header {
            display: flex;
            gap: 80px;
            margin-bottom: 30px;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f0f0f0;
        }

        .profile-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .profile-name {
            font-size: 22px;
            font-weight: 700;
            color: #003399;
            text-align: center;
        }

        .profile-info {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 5px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-size: 13px;
            color: #6c757d;
            font-weight: 500;
        }

        .info-value {
            font-size: 15px;
            color: #2b3940;
            font-weight: 600;
        }

        .info-value a {
            color: #003399;
            text-decoration: none;
        }

        .info-value a:hover {
            text-decoration: underline;
        }

        /* Status Badges */
        .status-badges {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .badge-custom {
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            text-align: center;
            min-width: 150px;
        }

        .badge-yellow {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-green {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-red {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Bottom Cards */
        .bottom-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .info-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #2b3940;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .card-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            transition: 0.2s;
        }

        .card-item:last-child {
            border-bottom: none;
        }

        .card-item:hover {
            padding-left: 10px;
        }

        .card-item-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-icon {
            color: #003399;
            font-size: 20px;
        }

        .card-item-text {
            font-size: 15px;
            color: #2b3940;
            font-weight: 500;
        }

        .card-arrow {
            color: #6c757d;
            font-size: 18px;
        }

        .menu-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            color: #6c757d;
            cursor: pointer;
        }

        @media (max-width: 992px) {
            .bottom-cards {
                grid-template-columns: 1fr;
            }

            .profile-header {
                flex-direction: column;
                align-items: center;
            }

            .profile-info {
                grid-template-columns: 1fr;
            }

            .status-badges {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
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
                    <a href="../index.php" class="nav-item nav-link">Home</a>
                    <a href="job-list.php" class="nav-item nav-link">Cari Pekerjaan</a>
                </div>
                <div class="auth-buttons d-flex align-items-center">
                    <a href="register.php" class="btn-register">Register</a>
                    <a href="login.php" class="btn-login">Login</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <div class="profile-container">
        <!-- Main Profile Card -->
        <div class="profile-card">
            <i class="bi bi-three-dots-vertical menu-icon"></i>
            
            <div class="profile-header">
                <div class="profile-left">
                    <img src="../assets/img/nando.jpg" alt="Profile" class="profile-image">
                    <h2 class="profile-name">Sultan Fachri Aziz</h2>
                </div>

                <div class="profile-info">
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value">081331909435</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><a href="mailto:Sul12345@gmail.com">Sul12345@gmail.com</a></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Lokasi</span>
                        <span class="info-value">Indonesia</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Usia, jenis kelamin</span>
                        <span class="info-value">20, Laki-laki</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Pendidikan terakhir</span>
                        <span class="info-value">Kuli</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Pengalaman kerja</span>
                        <span class="info-value">Mentri keuangan</span>
                    </div>
                </div>
            </div>

            <div class="status-badges">
                <div class="badge-custom badge-yellow">Pengajuan</div>
                <div class="badge-custom badge-green">Diterima</div>
                <div class="badge-custom badge-red">Ditolak</div>
            </div>
        </div>

        <!-- Bottom Cards -->
        <div class="bottom-cards">
            <!-- Aktivitas Card -->
            <div class="info-card">
                <h3 class="card-title">Aktivitas</h3>
                <div class="card-item">
                    <div class="card-item-left">
                        <i class="bi bi-bookmark card-icon"></i>
                        <span class="card-item-text">Disimpan</span>
                    </div>
                    <i class="bi bi-chevron-right card-arrow"></i>
                </div>
                <div class="card-item">
                    <div class="card-item-left">
                        <i class="bi bi-heart card-icon"></i>
                        <span class="card-item-text">Suka</span>
                    </div>
                    <i class="bi bi-chevron-right card-arrow"></i>
                </div>
                <div class="card-item">
                    <div class="card-item-left">
                        <i class="bi bi-star card-icon"></i>
                        <span class="card-item-text">Favorit</span>
                    </div>
                    <i class="bi bi-chevron-right card-arrow"></i>
                </div>
                <div class="card-item">
                    <div class="card-item-left">
                        <i class="bi bi-file-text card-icon"></i>
                        <span class="card-item-text">CV</span>
                    </div>
                    <i class="bi bi-chevron-right card-arrow"></i>
                </div>
            </div>

            <!-- Tampilan Card -->
            <div class="info-card">
                <h3 class="card-title">Tampilan</h3>
                <div class="card-item">
                    <div class="card-item-left">
                        <i class="bi bi-palette card-icon"></i>
                        <span class="card-item-text">Tema</span>
                    </div>
                    <i class="bi bi-chevron-right card-arrow"></i>
                </div>
            </div>

            <!-- Akun Card -->
            <div class="info-card">
                <h3 class="card-title">Akun</h3>
                <div class="card-item">
                    <div class="card-item-left">
                        <i class="bi bi-shield-check card-icon"></i>
                        <span class="card-item-text">Beralih akun</span>
                    </div>
                    <i class="bi bi-chevron-right card-arrow"></i>
                </div>
                <div class="card-item">
                    <div class="card-item-left">
                        <i class="bi bi-box-arrow-right card-icon"></i>
                        <span class="card-item-text">Keluar</span>
                    </div>
                    <i class="bi bi-chevron-right card-arrow"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/lib/wow/wow.min.js"></script>
    <script src="../assets/lib/easing/easing.min.js"></script>
    <script src="../assets/lib/waypoints/waypoints.min.js"></script>

    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
</body>

</html>