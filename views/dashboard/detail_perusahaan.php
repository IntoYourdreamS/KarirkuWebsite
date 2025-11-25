<?php 
  // --- 1. LOAD LIBRARY ---
  require_once 'supabase.php';

  // --- 2. AMBIL DATA ---
  $id_perusahaan = isset($_GET['id']) ? $_GET['id'] : 0;
  $data = null;

  try {
      $response = $client->get('perusahaan?id_perusahaan=eq.' . $id_perusahaan . '&select=*');
      if ($response->getStatusCode() == 200) {
          $result = json_decode($response->getBody(), true);
          if (!empty($result)) { $data = $result[0]; }
      }
  } catch (Exception $e) { }

  // Fallback Data
  if (!$data) {
      $nama_pt = "Data Tidak Ditemukan";
      $deskripsi = "-"; $alamat = "-"; $website = "#"; $logo_url = ""; $rating = "0.0";
      $makna_logo = "-"; $visi_misi = "-"; $struktur = "-"; $kebijakan = "-"; $anak_perusahaan = "-";
      $email = "-"; $telepon = "-"; $npwp = "-";
  } else {
      $nama_pt = $data['nama_perusahaan'] ?? '-';
      $website = $data['website'] ?? '#';
      $logo_url = $data['logo'] ?? ''; 
      $rating = $data['rating'] ?? '4.5';
      $deskripsi = $data['deskripsi'] ?? '-';
      $alamat = $data['alamat'] ?? '-';
      $makna_logo = $data['makna_logo'] ?? '-';
      $visi_misi = $data['visi_misi'] ?? '-';
      $struktur = $data['struktur_organisasi'] ?? '-';
      $kebijakan = $data['kebijakan_sm'] ?? '-';
      $anak_perusahaan = $data['anak_perusahaan'] ?? '-';
      $email = $data['email'] ?? '-'; 
  }
  
  $activePage = 'data_perusahaan';

  include 'header.php';
  include 'topbar.php';
?>

<style>
    /* --- 1. RESET LAYOUT --- */
    body { background-color: #F4F7FE; font-family: 'Inter', sans-serif; margin: 0; padding: 0; }
    .sidebar, .left-sidebar, #sidebar { display: none !important; }
    .main-content {
        margin: 0 !important; padding: 0 !important;
        width: 100% !important; max-width: 100% !important;
        background-color: #F4F7FE; min-height: 100vh;
    }

    /* --- 2. HERO BANNER (BACKGROUND DIPAKSA) --- */
    .hero-banner {
        position: relative; width: 100%; height: 300px;
        
        /* JALUR GAMBAR ABSOLUT (PASTI MUNCUL) */
        background-image: url('/KarirKuWebsite/assets/img/backgroundamin.png') !important;
        
        background-size: cover; background-position: center;
        display: flex; align-items: center; justify-content: flex-start; 
        padding-left: 10%;
    }
    .hero-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(43, 54, 116, 0.4); 
    }

    /* Tulisan Judul Kaca */
    .glass-title-box {
        position: relative; z-index: 10;
        background: rgba(255, 255, 255, 0.35);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
        padding: 15px 40px;
        border-radius: 50px;
        display: flex; align-items: center; gap: 15px;
    }
    .glass-bar { width: 8px; height: 35px; background-color: #11047A; border-radius: 10px; }
    .glass-text { font-size: 28px; font-weight: 800; color: #1B2559; }

    /* Tombol Kembali */
    .btn-back-simple {
        position: absolute; top: 30px; left: 40px; z-index: 50;
        color: white; font-size: 14px; font-weight: 600;
        background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 50px;
        text-decoration: none; display: flex; align-items: center; gap: 8px;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .btn-back-simple:hover { background: rgba(255,255,255,0.4); }

    /* --- 3. CONTAINER --- */
    .profile-container {
        max-width: 1100px; margin: 0 auto; padding: 40px 25px;
    }

    /* --- 4. ACCORDION (KLIK MUNCUL KE BAWAH) --- */
    .company-main-title {
        font-size: 32px; font-weight: 800; color: #1B2559; margin-bottom: 30px;
    }

    .accordion-item {
        margin-bottom: 15px;
        border-bottom: 1px solid #E0E5F2; /* Garis pemisah */
        padding-bottom: 10px;
    }

    .accordion-header {
        display: flex; align-items: center; cursor: pointer;
        padding: 10px 0; transition: 0.2s;
    }
    .accordion-header:hover { opacity: 0.7; }

    .acc-icon { font-size: 18px; width: 25px; text-align: center; margin-right: 12px; }
    .acc-title { font-size: 16px; font-weight: 700; color: #2B3674; flex: 1; }
    
    /* Panah Indikator */
    .acc-arrow { font-size: 14px; color: #A3AED0; transition: transform 0.3s ease; }
    
    /* Konten Tersembunyi */
    .accordion-body {
        display: none; /* Default Hilang */
        padding: 10px 0 10px 37px; 
        font-size: 14px; color: #707EAE; line-height: 1.6;
    }

    /* CLASS ACTIVE (Saat Diklik) */
    .accordion-item.active .accordion-body { display: block; animation: fadeIn 0.3s ease; }
    .accordion-item.active .acc-arrow { transform: rotate(180deg); }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

    /* Warna Ikon */
    .ic-org { color: #FF754C; } /* Orange */
    .ic-blu { color: #4318FF; } /* Biru */
    .ic-gry { color: #A3AED0; } /* Abu */

    /* --- 5. KANAN: KOTAK ANALISIS --- */
    .analysis-card {
        background: white; border-radius: 20px; padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    }
    .analysis-title { font-size: 18px; font-weight: 700; color: #1B2559; margin-bottom: 20px; }
    
    .check-item { margin-bottom: 15px; }
    .check-label { font-size: 14px; font-weight: 700; color: #2B3674; margin-bottom: 5px; }
    .check-val { font-size: 14px; color: #4A5568; display: flex; align-items: center; gap: 8px; }
    .check-val i { color: #05CD99; }
    
    .rating-box { margin-top: 10px; font-size: 16px; color: #2B3674; font-weight: 700; }
    .rating-box i { color: #FFB547; margin-right: 5px; }

    .btn-delete-full {
        display: block; width: 100%; padding: 15px; margin-top: 25px;
        background: #9E0A0A; color: white; border-radius: 12px; font-weight: 700; text-align: center;
        text-decoration: none; font-size: 14px; box-shadow: 0 4px 10px rgba(158, 10, 10, 0.2);
    }
    .btn-delete-full:hover { background: #7a0606; }

</style>

<div class="main-content">
    
    <div class="hero-banner">
        <div class="hero-overlay"></div>
        
        <a href="data_perusahaan.php" class="btn-back-simple">
            <i class="fas fa-chevron-left"></i> Kembali
        </a>

        <div class="glass-title-box">
            <div class="glass-bar"></div>
            <div class="glass-text">Detail Perusahaan</div>
        </div>
    </div>

    <div class="profile-container">
        <div class="row">
            
            <div class="col-lg-7">
                
                <h1 class="company-main-title"><?php echo htmlspecialchars($nama_pt); ?></h1>

                <div class="accordion-item" onclick="toggleAcc(this)">
                    <div class="accordion-header">
                        <i class="fas fa-map-marker-alt acc-icon ic-org"></i>
                        <span class="acc-title">Alamat Perusahaan</span>
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        <?php echo htmlspecialchars($alamat); ?>
                    </div>
                </div>

                <div class="accordion-item" onclick="toggleAcc(this)">
                    <div class="accordion-header">
                        <i class="fas fa-align-left acc-icon ic-blu"></i>
                        <span class="acc-title">Deskripsi Perusahaan</span>
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        <?php echo nl2br(htmlspecialchars($deskripsi)); ?>
                    </div>
                </div>

                <div class="accordion-item" onclick="toggleAcc(this)">
                    <div class="accordion-header">
                        <i class="fas fa-image acc-icon ic-gry"></i>
                        <span class="acc-title">Logo</span>
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        <?php if(!empty($logo_url)): ?>
                            <img src="<?php echo htmlspecialchars($logo_url); ?>" style="width: 100px; border-radius: 8px; border: 1px solid #eee; padding: 5px;">
                        <?php else: ?>
                            <span class="text-muted">Tidak ada logo.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="accordion-item" onclick="toggleAcc(this)">
                    <div class="accordion-header">
                        <i class="fas fa-envelope acc-icon ic-gry"></i>
                        <span class="acc-title">Email</span>
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        <?php echo htmlspecialchars($email); ?>
                    </div>
                </div>

                <div class="accordion-item" onclick="toggleAcc(this)">
                    <div class="accordion-header">
                        <i class="fas fa-bullseye acc-icon ic-gry"></i>
                        <span class="acc-title">Visi & Misi</span>
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        <?php echo nl2br(htmlspecialchars($visi_misi)); ?>
                    </div>
                </div>

                <div class="accordion-item" onclick="toggleAcc(this)">
                    <div class="accordion-header">
                        <i class="fas fa-sitemap acc-icon ic-gry"></i>
                        <span class="acc-title">Struktur Organisasi</span>
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        <?php echo nl2br(htmlspecialchars($struktur)); ?>
                    </div>
                </div>

                <div class="accordion-item" onclick="toggleAcc(this)">
                    <div class="accordion-header">
                        <i class="fas fa-book acc-icon ic-gry"></i>
                        <span class="acc-title">Kebijakan SM</span>
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        <?php echo nl2br(htmlspecialchars($kebijakan)); ?>
                    </div>
                </div>

                <div class="accordion-item" onclick="toggleAcc(this)">
                    <div class="accordion-header">
                        <i class="fas fa-network-wired acc-icon ic-gry"></i>
                        <span class="acc-title">Anak Perusahaan</span>
                        <i class="fas fa-chevron-down acc-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        <?php echo nl2br(htmlspecialchars($anak_perusahaan)); ?>
                    </div>
                </div>

            </div>

            <div class="col-lg-5">
                <div class="analysis-card">
                    <div class="analysis-title">Analisis Perusahaan</div>
                    
                    <div class="check-item">
                        <div class="check-val"><i class="fas fa-check-circle"></i> Info perusahaan lengkap</div>
                    </div>

                    <div class="check-item">
                        <div class="check-label">Penyedia Lowongan</div>
                        <div class="check-val"><i class="fas fa-check-circle"></i> Terpercaya</div>
                    </div>

                    <div class="check-item">
                        <div class="check-label">Rating Perusahaan</div>
                        <div class="rating-box"><i class="fas fa-star"></i> <?php echo $rating; ?></div>
                    </div>
                </div>

                <a href="hapus_perusahaan.php?id=<?php echo $id_perusahaan; ?>" 
                   class="btn-delete-full"
                   onclick="return confirm('⚠️ Hapus perusahaan ini secara permanen?');">
                    Hapus Perusahaan <i class="fas fa-trash-alt ms-2"></i>
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    // FUNGSI UNTUK MEMBUKA/MENUTUP KONTEN
    function toggleAcc(element) {
        // Tambah/Hapus class 'active' saat diklik
        element.classList.toggle("active");
    }
</script>

<?php include 'footer.php'; ?>