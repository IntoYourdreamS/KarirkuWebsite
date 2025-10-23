<?php
session_start();
require_once '../config/database.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Proses Login
if (isset($_POST['login_submit'])) {
    $username = $_POST['login-username'];
    $password = $_POST['login-password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE email = ? OR nama_lengkap = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id_pengguna'];
            $_SESSION['user_name'] = $user['nama_lengkap'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_phone'] = $user['no_hp'];

            header("Location: ../index.php");
            exit();
        } else {
            $login_error = "Email/Nama atau password salah!";
        }
    } catch (PDOException $e) {
        $login_error = "Terjadi kesalahan sistem!";
    }
}

// Proses Register
if (isset($_POST['register_submit'])) {
    $name = trim($_POST['register-name']);
    $email = trim($_POST['register-email']);
    $phone = trim($_POST['register-phone']);
    $password = $_POST['register-password'];
    $confirm_password = $_POST['register-confirm-password'];
    $role = 'pencaker';

    // Validasi
    if (empty($name) || empty($email) || empty($password)) {
        $register_error = "Semua field wajib diisi!";
    } elseif ($password !== $confirm_password) {
        $register_error = "Password dan Konfirmasi Password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $register_error = "Password minimal 6 karakter!";
    } else {
        try {
            // Cek apakah email sudah terdaftar
            $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $register_error = "Email sudah terdaftar!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO pengguna (nama_lengkap, email, no_hp, password_hash, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $phone, $hashed_password, $role]);

                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;
                $_SESSION['user_phone'] = $phone;

                header("Location: ../index.php");
                exit();
            }
        } catch (PDOException $e) {
            $register_error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Job Board - Login & Register</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- CSS here -->
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/flaticon.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/price_rangs.css">
    <link rel="stylesheet" href="../assets/css/slicknav.css">
    <link rel="stylesheet" href="../assets/css/animate.min.css">
    <link rel="stylesheet" href="../assets/css/magnific-popup.css">
    <link rel="stylesheet" href="../assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/css/slick.css">
    <link rel="stylesheet" href="../assets/css/nice-select.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="../assets/img/logo/logo.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader Start -->

    <header>
        <!-- Header Start -->
        <div class="header-area header-transparrent">
            <div class="headder-top header-sticky">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-md-2">
                            <!-- Logo -->
                            <div class="logo" style="scale: 0.5; margin-left: -100px;">
                                <a href="index.html"><img src="assets/img/logo/logokarirku.png" alt=""></a>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-9">
                            <div class="menu-wrapper">
                                <!-- Main-menu -->
                                <div class="main-menu">
                                    <nav class="d-none d-lg-block">
                                        <ul id="navigation" style="margin: 0px">
                                            <li><a href="index.html">Home</a></li>
                                            <li><a href="job_listing.html">Find a Jobs</a></li>
                                            <li><a href="about.html">About</a></li>
                                            <li><a href="#">Page</a>
                                                <ul class="submenu">
                                                    <li><a href="blog.html">Blog</a></li>
                                                    <li><a href="single-blog.html">Blog Details</a></li>
                                                    <li><a href="elements.html">Elements</a></li>
                                                    <li><a href="job_details.html">job Details</a></li>
                                                </ul>
                                            </li>
                                            <li><a href="contact.html">Contact</a></li>
                                        </ul>
                                    </nav>
                                </div>
                                <!-- Header-btn -->
                                <div class="header-btn d-none f-right d-lg-block">
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <div class="dropdown">
                                            <button class="btn head-btn1 dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                                                <li><a class="dropdown-item" href="my_jobs.php"><i class="fas fa-briefcase me-2"></i>Lowongan Saya</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="?logout=true"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <a href="register.php" class="btn head-btn1">Register</a>
                                        <a href="auth/login.php" class="btn head-btn2">Login</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Mobile Menu -->
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>

    <main>
        <!-- Auth Area Start -->
        <div class="slider-area">
            <div class="slider-active">
                <div class="single-slider slider-height d-flex align-items-center" data-background="assets/img/hero/background1.png">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-xl-6 col-lg-8 col-md-10">
                                <div class="auth-container">
                                    <!-- Auth Tabs -->
                                    <div class="auth-tabs">
                                        <div class="auth-tab active" data-tab="login">Login</div>
                                        <div class="auth-tab" data-tab="register">Register</div>
                                    </div>

                                    <!-- Login Form -->
                                    <div id="login-content" class="auth-content active">
                                        <div class="auth-header">
                                            <h2>Masuk ke Akun Anda</h2>
                                            <p>Selamat datang kembali! Silakan masuk ke akun Anda</p>
                                        </div>

                                        <form id="login-form" method="POST">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="login-username" name="login-username" placeholder="Email atau Nama Lengkap" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="password" class="form-control" id="login-password" name="login-password" placeholder="Password" required>
                                            </div>
                                            <?php if (isset($login_error)): ?>
                                                <div class="alert alert-danger" style="padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                                                    <?php echo $login_error; ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="remember-me">
                                                <label class="form-check-label" for="remember-me">Ingat saya</label>
                                                <a href="#" style="float: right; color: #1F2B6C;">Lupa password?</a>
                                            </div>
                                            <button type="submit" name="login_submit" class="btn btn-auth">Masuk</button>
                                        </form>

                                        <div class="divider">
                                            <span>Atau masuk dengan</span>
                                        </div>

                                        <button class="btn btn-google">
                                            <img src="../assets/img/icon/google.png" alt="Google" class="google-icon">
                                            Masuk dengan Google
                                        </button>

                                        <div class="auth-footer">
                                            <p>Belum punya akun? <a href="#" class="switch-to-register">Daftar di sini</a></p>
                                        </div>
                                    </div>

                                    <!-- Register Form -->
                                    <div id="register-content" class="auth-content">
                                        <div class="auth-header">
                                            <h2>Buat Akun Baru</h2>
                                            <p>Bergabunglah dengan kami dan temukan karir impian Anda</p>
                                        </div>

                                        <form id="register-form" method="POST">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="register-name" name="register-name" placeholder="Nama Lengkap" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="email" class="form-control" id="register-email" name="register-email" placeholder="Alamat Email" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="tel" class="form-control" id="register-phone" name="register-phone" placeholder="Nomor Telepon" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="password" class="form-control" id="register-password" name="register-password" placeholder="Password" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="password" class="form-control" id="register-confirm-password" name="register-confirm-password" placeholder="Konfirmasi Password" required>
                                            </div>
                                            <?php if (isset($register_error)): ?>
                                                <div class="alert alert-danger" style="padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                                                    <?php echo $register_error; ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="agree-terms" required>
                                                <label class="form-check-label" for="agree-terms">Saya setuju dengan <a href="#" style="color: #1F2B6C;">Syarat & Ketentuan</a></label>
                                            </div>
                                            <button type="submit" name="register_submit" class="btn btn-auth">Daftar</button>
                                        </form>

                                        <div class="divider">
                                            <span>Atau daftar dengan</span>
                                        </div>

                                        <button class="btn btn-google">
                                            <img src="../assets/img/icon/google.png" alt="Google" class="google-icon">
                                            Daftar dengan Google
                                        </button>

                                        <div class="auth-footer">
                                            <p>Sudah punya akun? <a href="#" class="switch-to-login">Masuk di sini</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Auth Area End -->
    </main>

    <footer>
        <!-- Footer Start-->
        <div class="footer-area footer-bg footer-padding">
            <div class="container">
                <div class="row d-flex justify-content-between">
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                        <div class="single-footer-caption mb-50">
                            <div class="single-footer-caption mb-30">
                                <div class="footer-tittle">
                                    <h4>About Us</h4>
                                    <div class="footer-pera">
                                        <p>Heaven frucvitful doesn't cover lesser dvsays appear creeping seasons so behold.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Contact Info</h4>
                                <ul>
                                    <li>
                                        <p>Address :Your address goes
                                            here, your demo address.</p>
                                    </li>
                                    <li><a href="#">Phone : +8880 44338899</a></li>
                                    <li><a href="#">Email : info@colorlib.com</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Important Link</h4>
                                <ul>
                                    <li><a href="#"> View Project</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                    <li><a href="#">Testimonial</a></li>
                                    <li><a href="#">Proparties</a></li>
                                    <li><a href="#">Support</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Newsletter</h4>
                                <div class="footer-pera footer-pera2">
                                    <p>Heaven fruitful doesn't over lesser in days. Appear creeping.</p>
                                </div>
                                <!-- Form -->
                                <div class="footer-form">
                                    <div id="mc_embed_signup">
                                        <form target="_blank" action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01"
                                            method="get" class="subscribe_form relative mail_part">
                                            <input type="email" name="email" id="newsletter-form-email" placeholder="Email Address"
                                                class="placeholder hide-on-focus" onfocus="this.placeholder = ''"
                                                onblur="this.placeholder = ' Email Address '">
                                            <div class="form-icon">
                                                <button type="submit" name="submit" id="newsletter-submit"
                                                    class="email_icon newsletter-submit button-contactForm"><img src="../assets/img/icon/form.png" alt=""></button>
                                            </div>
                                            <div class="mt-10 info"></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
                <div class="row footer-wejed justify-content-between">
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                        <!-- logo -->
                        <div class="footer-logo mb-20">
                            <a href="index.html"><img src="../assets/img/logo/logo2_footer.png" alt=""></a>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <div class="footer-tittle-bottom">
                            <span>5000+</span>
                            <p>Talented Hunter</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <div class="footer-tittle-bottom">
                            <span>451</span>
                            <p>Talented Hunter</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <!-- Footer Bottom Tittle -->
                        <div class="footer-tittle-bottom">
                            <span>568</span>
                            <p>Talented Hunter</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- footer-bottom area -->
        <div class="footer-bottom-area footer-bg">
            <div class="container">
                <div class="footer-border">
                    <div class="row d-flex justify-content-between align-items-center">
                        <div class="col-xl-10 col-lg-10 ">
                            <div class="footer-copy-right">
                                <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                    Copyright &copy;<script>
                                        document.write(new Date().getFullYear());
                                    </script> All rights reserved | This template is made with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2">
                            <div class="footer-social f-right">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fas fa-globe"></i></a>
                                <a href="#"><i class="fab fa-behance"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End-->
    </footer>

    <!-- JS here -->
    <script src="../assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <!-- Jquery, Popper, Bootstrap -->
    <script src="../assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <!-- Jquery Mobile Menu -->
    <script src="../assets/js/jquery.slicknav.min.js"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/slick.min.js"></script>
    <script src="../assets/js/price_rangs.js"></script>

    <!-- One Page, Animated-HeadLin -->
    <script src="../assets/js/wow.min.js"></script>
    <script src="../assets/js/animated.headline.js"></script>
    <script src="../assets/js/jquery.magnific-popup.js"></script>

    <!-- Scrollup, nice-select, sticky -->
    <script src="../assets/js/jquery.scrollUp.min.js"></script>
    <script src="../assets/js/jquery.nice-select.min.js"></script>
    <script src="../assets/js/jquery.sticky.js"></script>

    <!-- contact js -->
    <script src="../assets/js/contact.js"></script>
    <script src="../assets/js/jquery.form.js"></script>
    <script src="../assets/js/jquery.validate.min.js"></script>
    <script src="../assets/js/mail-script.js"></script>
    <script src="../assets/js/jquery.ajaxchimp.min.js"></script>

    <!-- Jquery Plugins, main Jquery -->
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
        $(document).ready(function() {
            // Tab switching functionality
            $('.auth-tab').click(function() {
                const tabId = $(this).data('tab');

                // Update active tab
                $('.auth-tab').removeClass('active');
                $(this).addClass('active');

                // Update active content
                $('.auth-content').removeClass('active');
                $('#' + tabId + '-content').addClass('active');
            });

            // Switch to register from login footer
            $('.switch-to-register').click(function(e) {
                e.preventDefault();
                $('.auth-tab').removeClass('active');
                $('.auth-tab[data-tab="register"]').addClass('active');

                $('.auth-content').removeClass('active');
                $('#register-content').addClass('active');
            });

            // Switch to login from register footer
            $('.switch-to-login').click(function(e) {
                e.preventDefault();
                $('.auth-tab').removeClass('active');
                $('.auth-tab[data-tab="login"]').addClass('active');

                $('.auth-content').removeClass('active');
                $('#login-content').addClass('active');
            });

            // Dropdown functionality
            $('#userDropdown').click(function(e) {
                e.preventDefault();
                $('.dropdown-menu').toggleClass('show');
            });

            // Close dropdown when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').removeClass('show');
                }
            });

            // Client-side password validation
            $('#register-form').submit(function(e) {
                const password = $('#register-password').val();
                const confirmPassword = $('#register-confirm-password').val();

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Password dan Konfirmasi Password tidak cocok');
                    return false;
                }
                return true;
            });
        });
    </script>
</body>

</html>