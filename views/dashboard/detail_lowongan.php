<?php
// --- 1. SETUP KONEKSI & LOGIC PHP ---
require __DIR__ . '/../../vendor/autoload.php';
use GuzzleHttp\Client;

$supabaseUrl = 'https://tkjnbelcgfwpbhppsnrl.supabase.co';
$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRram5iZWxjZ2Z3cGJocHBzbnJsIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2MTc0MDc2MiwiZXhwIjoyMDc3MzE2NzYyfQ.vZoNXxMWtoG4ktg7K6Whqv8EFzCv7qbS3OAHEfxVoR0';

// Cek ID
if (!isset($_GET['id'])) {
    header("Location: lowongan.php");
    exit;
}

$id_loker = $_GET['id'];
$data = null;

try {
    $client = new Client([
        'base_uri' => $supabaseUrl . '/rest/v1/',
        'headers' => [
            'apikey' => $supabaseKey, 
            'Authorization' => 'Bearer ' . $supabaseKey,
        ],
        'http_errors' => false
    ]);

    // QUERY: Ambil detail lowongan DAN detail perusahaan
    $queryUrl = 'lowongan?id_lowongan=eq.' . $id_loker . '&select=*,perusahaan(*)';
    
    $res = $client->get($queryUrl);
    if ($res->getStatusCode() == 200) {
        $result = json_decode($res->getBody(), true);
        if (!empty($result)) {
            $data = $result[0]; 
        }
    }
} catch (Exception $e) { }

if (!$data) {
    header("Location: lowongan.php");
    exit;
}

// --- DATA PREPARATION ---
$judul = $data['judul'];
$deskripsi = $data['deskripsi']; 
$gaji_min = isset($data['gaji_min']) ? number_format($data['gaji_min'], 0, ',', '.') : '0';
$gaji_max = isset($data['gaji_max']) ? number_format($data['gaji_max'], 0, ',', '.') : '0';
$tipe = $data['tipe_pekerjaan'] ?? 'Full Time';
$lokasi = $data['lokasi'] ?? 'Indonesia';
$tgl_post_raw = isset($data['created_at']) ? strtotime($data['created_at']) : time();
$now = time();
$diff = $now - $tgl_post_raw;
$days_ago = floor($diff / (60 * 60 * 24));
if ($days_ago == 0) {
    $formatted_tgl_post = "Hari Ini";
} elseif ($days_ago == 1) {
    $formatted_tgl_post = "1 Hari Lalu";
} else {
    $formatted_tgl_post = $days_ago . " Hari Lalu";
}

// Data Perusahaan
$nama_pt = $data['perusahaan']['nama_perusahaan'] ?? 'Perusahaan';
$logo_pt = $data['perusahaan']['logo'] ?? '';
$alamat_pt = $data['perusahaan']['alamat'] ?? '-';
$website_pt = $data['perusahaan']['website'] ?? '#';

include 'header.php'; 
// Tidak include topbar.php agar header banner bisa full width dan custom.
?>

<style>
    /* --- 1. RESET LAYOUT UTAMA (FULL WIDTH) --- */
    body { background-color: #F4F7FE; font-family: 'DM Sans', 'Inter', sans-serif; margin: 0; padding: 0; overflow-x: hidden; }
    
    /* Sembunyikan Sidebar secara paksa */
    .sidebar, .left-sidebar, #sidebar, .header-navbar { display: none !important; }

    /* Reset Main Content agar Full Width */
    .main-content {
        margin-left: 0 !important;
        margin-top: 0 !important; 
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        background-color: #F4F7FE;
        min-height: 100vh;
    }

    /* --- 2. HERO BANNER SECTION (MIRIP GAMBAR KEDUA DENGAN BACKGROUND IMAGE) --- */
    .hero-banner {
        position: relative;
        width: 100vw; 
        height: 200px; /* Tinggi Banner */
        
        /* GAMBAR BACKGROUND SESUAI PERMINTAAN DARI GAMBAR KEDUA */
        background-image: url('../../assets/img/background.png'); 
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        
        /* Full Width Hack */
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
    }

    .hero-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.4); /* Overlay gelap agar teks jelas */
    }

    .hero-content {
        position: absolute; /* Posisikan absolut di dalam banner */
        bottom: 30px; /* Jarak dari bawah */
        left: 50%;
        transform: translateX(-50%); /* Pusatkan secara horizontal */
        width: 100%;
        max-width: 1100px; /* Lebar maksimal konten */
        padding: 0 15px;
        z-index: 2;
        text-align: left; /* Teks rata kiri */
    }
    .hero-title {
        font-size: 32px; font-weight: 800; color: white; /* Teks putih */
        font-family: 'DM Sans', sans-serif;
        margin: 0;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.5); /* Shadow untuk teks lebih jelas */
    }
    .hero-subtitle {
        color: #E0E5F2; /* Warna subtitle */
        font-size: 14px; 
        margin-top: 5px; 
        font-weight: 500;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    /* --- 3. DETAIL CONTAINER --- */
    .detail-container {
        max-width: 1100px;
        margin: -80px auto 50px auto; /* Naik ke atas menutupi banner sedikit */
        position: relative; z-index: 10;
        padding: 0 15px;
    }

    /* KARTU KIRI (Konten Utama) */
    .main-card {
        background: white; border-radius: 20px; padding: 40px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #fff;
    }

    /* Header Job (Logo & Judul) */
    .job-header { display: flex; gap: 25px; align-items: flex-start; margin-bottom: 30px; }
    .job-logo-box {
        width: 80px; height: 80px; background: #F4F7FE; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid #E0E5F2; overflow: hidden; flex-shrink: 0;
    }
    .job-main-info h1 { 
        font-size: 24px; font-weight: 700; color: #1B2559; margin: 0 0 5px 0; 
        font-family: 'DM Sans', sans-serif;
    }
    .company-name { font-size: 16px; font-weight: 600; color: #11047A; }
    .post-date { font-size: 12px; color: #A3AED0; font-weight: 400; margin-left: 10px; }

    /* Badges (Fulltime, Gaji) */
    .tags-wrapper { display: flex; gap: 10px; margin-top: 12px; }
    .tag-badge { 
        padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; 
        display: flex; align-items: center; gap: 6px;
    }
    .tag-purple { background: #F4F7FE; color: #4318FF; }
    .tag-green { background: #E6F9EB; color: #05CD99; }

    /* Section Content (DIJADIKAN SATU) */
    .content-section { margin-top: 30px; color: #4A5568; line-height: 1.8; font-size: 14px; }
    .content-label { 
        font-size: 16px; font-weight: 700; color: #1B2559; margin-bottom: 10px; display: block; 
        font-family: 'DM Sans', sans-serif;
    }
    .content-text { text-align: justify; margin-bottom: 20px; }

    /* KARTU KANAN (Sidebar Action) */
    .action-card {
        background: white; border-radius: 20px; padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03); text-align: center;
        position: sticky; top: 20px;
    }
    
    .btn-hapus-large {
        background-color: #D32F2F; color: white; width: 100%;
        padding: 15px; border-radius: 30px; font-weight: 700; font-size: 14px;
        border: none; cursor: pointer; text-decoration: none; display: block;
        box-shadow: 0 4px 10px rgba(211, 47, 47, 0.3); transition: 0.2s;
        font-family: 'DM Sans', sans-serif;
    }
    .btn-hapus-large:hover { background-color: #B71C1C; transform: translateY(-2px); color: white; }

    /* Placeholder Kotak Abu-abu di desain */
    .placeholder-box {
        background: #E0E5F2; height: 200px; width: 100%; 
        border-radius: 16px; margin-top: 20px;
    }

</style>

<div class="main-content">
    <div class="hero-banner">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-title">DETAIL LOWONGAN</div>
            <div class="hero-subtitle">HOME / PAGES / DETAIL LOWONGAN</div>
        </div>
    </div>

    <div class="detail-container">
        <div class="row">
            
            <div class="col-lg-8 mb-4">
                <div class="main-card">
                    
                    <div class="job-header">
                        <div class="job-logo-box">
                            <?php if(!empty($logo_pt)): ?>
                                <img src="<?= htmlspecialchars($logo_pt) ?>" style="width:100%; height:100%; object-fit:contain;">
                            <?php else: ?>
                                <i class="fas fa-building fa-2x" style="color:#CBD5E0;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="job-main-info">
                            <h1>
                                <?= htmlspecialchars($judul) ?> 
                                <span class="post-date"><?= $formatted_tgl_post ?></span>
                            </h1>
                            <div class="company-name"><?= htmlspecialchars($nama_pt) ?></div>
                            
                            <div class="tags-wrapper">
                                <div class="tag-badge tag-purple">
                                    <i class="far fa-clock"></i> <?= htmlspecialchars($tipe) ?>
                                </div>
                                <div class="tag-badge tag-green">
                                    <i class="fas fa-money-bill-wave"></i> Rp <?= $gaji_min ?> - <?= $gaji_max ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color:#F4F7FE;">

                    <div class="content-section mt-4">
                        <span class="content-label"><i class="fas fa-map-marker-alt text-danger me-2"></i> Alamat</span>
                        <p class="content-text"><?= nl2br(htmlspecialchars($alamat_pt)) ?></p>
                    </div>

                    <div class="content-section">
                        <span class="content-label">Deskripsi Pekerjaan</span>
                        <p class="content-text"><?= nl2br(htmlspecialchars($deskripsi)) ?></p>
                    </div>

                    </div>
            </div>

            <div class="col-lg-4">
                <div class="action-card">
                    <h5 class="mb-4 fw-bold text-dark">Tindakan</h5>
                    
                    <a href="hapus_lowongan.php?id=<?= $id_loker ?>" 
                       class="btn-hapus-large"
                       onclick="return confirm('Yakin ingin menghapus lowongan ini secara permanen?');">
                        Hapus Lowongan Ini
                    </a>

                    <div class="placeholder-box"></div>
                    <div class="placeholder-box" style="height: 150px; opacity: 0.5;"></div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
```http://googleusercontent.com/image_generation_content/0