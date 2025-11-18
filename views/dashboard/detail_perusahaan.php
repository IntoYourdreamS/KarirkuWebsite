<?php 
  // --- 1. KONEKSI DATABASE ---
  require __DIR__ . '/../../vendor/autoload.php';
  use GuzzleHttp\Client;

  $supabaseUrl = 'https://tkjnbelcgfwpbhppsnrl.supabase.co';
  $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRram5iZWxjZ2Z3cGJocHBzbnJsIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2MTc0MDc2MiwiZXhwIjoyMDc3MzE2NzYyfQ.vZoNXxMWtoG4ktg7K6Whqv8EFzCv7qbS3OAHEfxVoR0';

  if (!isset($_GET['id'])) { header("Location: perusahaan.php"); exit; }
  $id_perusahaan = $_GET['id'];
  $data = null;

  try {
      $client = new Client([
          'base_uri' => $supabaseUrl . '/rest/v1/',
          'headers' => [
              'apikey' => $supabaseKey, 'Authorization' => 'Bearer ' . $supabaseKey,
          ],
          'http_errors' => false
      ]);

      $response = $client->get('perusahaan?id_perusahaan=eq.' . $id_perusahaan . '&select=*');
      if ($response->getStatusCode() == 200) {
          $result = json_decode($response->getBody(), true);
          if (!empty($result)) { $data = $result[0]; }
      }
  } catch (Exception $e) {}

  if (!$data) { echo "Data tidak ditemukan."; exit; }

  // --- DATA HANDLING ---
  $nama_pt = $data['nama_perusahaan'] ?? 'Tanpa Nama';
  $website = $data['website'] ?? '#';
  $logo_url = $data['logo'] ?? ''; 
  $rating = $data['rating'] ?? '4.5';
  
  $deskripsi = $data['deskripsi'] ?? 'Belum ada deskripsi perusahaan.';
  $alamat = $data['alamat'] ?? 'Alamat belum tersedia.';
  $makna_logo = $data['makna_logo'] ?? 'Data belum tersedia.';
  $visi_misi = $data['visi_misi'] ?? 'Data belum tersedia.';
  $struktur = $data['struktur_organisasi'] ?? 'Data belum tersedia.';
  $kebijakan = $data['kebijakan_sm'] ?? 'Data belum tersedia.';
  $anak_perusahaan = $data['anak_perusahaan'] ?? 'Data belum tersedia.';

  // --- DETEKSI GAMBAR BACKGROUND OTOMATIS ---
  // Kita cek apakah file ada di folder assets/img/
  // __DIR__ adalah posisi file ini (views/dashboard)
  // Kita mundur 2 langkah ke folder root, lalu masuk assets/img
  $path_fisik = __DIR__ . '/../../assets/img/background.png';
  $bg_css = '';

  if (file_exists($path_fisik)) {
      // Jika file ada, pakai Relative Path
      $bg_css = "url('../../assets/img/background.png')";
  } else {
      // Jika file TIDAK ada/gagal dibaca, pakai Placeholder Internet (Biar ketahuan errornya)
      // Nanti ganti link ini kalau mau
      $bg_css = "url('https://placehold.co/1200x400/5967FF/FFFFFF.png?text=Gambar+Tidak+Ditemukan')";
  }

  include 'header.php';
  include 'topbar.php';
?>

<style>
  /* --- LAYOUT --- */
  .main-content { 
      background-color: #F7F8FC; padding: 30px; min-height: 100vh; 
      margin-left: 0 !important; width: 100% !important; max-width: 100% !important;
  }
  .sidebar, .left-sidebar { display: none !important; }

  .btn-back {
      display: inline-flex; align-items: center; gap: 8px;
      color: #666; text-decoration: none; font-weight: 600; margin-bottom: 24px;
      font-size: 15px;
  }
  .btn-back:hover { color: #000; }

  .card-box {
      background-color: white; border-radius: 16px; padding: 32px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px;
      border: 1px solid #F0F0F0;
  }

  /* --- HEADER DENGAN PHP AUTO DETECT --- */
  .company-header { 
      display: flex; 
      align-items: center; 
      gap: 24px; 
      
      /* WARNA DASAR UNGU */
      background-color: #eef2ff; 

      /* BACKGROUND DARI PHP */
      /* Saya pasang lapisan putih tipis sekali (0.7 ke 0.2) biar gambar jelas */
      background-image: 
          linear-gradient(to right, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.2)),
          <?php echo $bg_css; ?>; 
      
      background-size: cover; 
      background-position: center; 
      background-repeat: no-repeat;
      
      padding: 40px; 
      border-radius: 16px;
      margin-bottom: 30px; 
      border: 1px solid #E0E0E0;
  }

  .logo-container {
      width: 100px; height: 100px; border-radius: 16px;
      background-color: #FFFFFF; 
      border: 1px solid #E9ECEF;
      display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  }
  .logo-img { width: 100%; height: 100%; object-fit: cover; }
  .logo-initial { font-size: 36px; font-weight: 700; color: #5967FF; }

  .header-info h1 { margin: 0 0 8px 0; font-size: 28px; color: #1A202C; font-weight: 800; }
  .header-info p { margin: 0; color: #4A5568; font-size: 15px; font-weight: 600; text-shadow: 0 1px 0 rgba(255,255,255,0.8); }
  
  .website-btn { 
      background-color: rgba(255,255,255,0.9); 
      color: #5967FF; border: 1px solid #5967FF;
      text-decoration: none; font-size: 14px; font-weight: 600; 
      display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; 
      padding: 8px 16px; border-radius: 50px;
      transition: all 0.2s;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  }
  .website-btn:hover { background-color: #5967FF; color: white; }

  /* --- ACCORDION STYLE --- */
  .content-section { border-bottom: 1px solid #F1F1F1; }
  .content-section:last-child { border-bottom: none; }

  .accordion-header {
      display: flex; justify-content: space-between; align-items: center;
      padding: 20px 0; cursor: pointer; user-select: none;
  }
  .section-title { font-size: 16px; font-weight: 700; color: #2D3748; margin: 0; }
  .accordion-header:hover .section-title { color: #5967FF; }

  .chevron-icon { 
      width: 20px; height: 20px; 
      fill: #A0AEC0; 
      transition: transform 0.3s ease; 
  }
  .accordion-header.active .chevron-icon { transform: rotate(180deg); fill: #5967FF; }

  .text-content {
      max-height: 0; overflow: hidden; opacity: 0;
      transition: max-height 0.4s ease, opacity 0.3s ease;
      color: #4A5568; font-size: 15px; line-height: 1.7;
  }
  .text-content.open { max-height: 1000px; opacity: 1; padding-bottom: 20px; }

  /* --- SIDEBAR KANAN --- */
  .right-panel-title { font-size: 16px; font-weight: 700; color: #1A202C; margin-bottom: 16px; }
  .check-list-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #F1F1F1; font-size: 14px; color: #4A5568; font-weight: 500; }
  .icon-check { color: #38A169; font-size: 16px; }
  .rating-wrapper { font-size: 18px; font-weight: 700; color: #2D3748; display: flex; align-items: center; gap: 6px; }
  .icon-star { color: #ECC94B; }
  .btn-delete { width: 100%; background-color: #FF5252; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 20px; transition: background 0.2s; text-decoration: none; }
  .btn-delete:hover { background-color: #E53E3E; color: white; }

</style>

<div class="main-content">
    <div class="container" style="max-width: 1140px;">
        
        <a href="perusahaan.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>

        <div class="row">
            
            <div class="col-lg-8">
                <div class="card-box">
                    
                    <div class="company-header">
                        <div class="logo-container">
                            <?php if (!empty($logo_url)): ?>
                                <img src="<?php echo htmlspecialchars($logo_url); ?>" class="logo-img">
                            <?php else: ?>
                                <span class="logo-initial"><?php echo strtoupper(substr($nama_pt, 0, 1)); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="header-info">
                            <h1><?php echo htmlspecialchars($nama_pt); ?></h1>
                            <p>ID Perusahaan: <?php echo $id_perusahaan; ?></p>
                            <?php if ($website != '#'): ?>
                                <a href="<?php echo $website; ?>" target="_blank" class="website-btn">
                                    <i class="fas fa-globe"></i> Kunjungi Website
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="accordion-header" onclick="toggleAccordion('desc-box', this)">
                            <h4 class="section-title">Deskripsi Perusahaan</h4>
                            <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                        </div>
                        <div id="desc-box" class="text-content">
                            <?php echo nl2br(htmlspecialchars($deskripsi)); ?>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="accordion-header" onclick="toggleAccordion('alamat-box', this)">
                            <h4 class="section-title">Alamat Perusahaan</h4>
                            <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                        </div>
                        <div id="alamat-box" class="text-content">
                            <?php echo nl2br(htmlspecialchars($alamat)); ?>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="accordion-header" onclick="toggleAccordion('makna-box', this)">
                            <h4 class="section-title">Makna Logo</h4>
                            <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                        </div>
                        <div id="makna-box" class="text-content">
                            <?php echo nl2br(htmlspecialchars($makna_logo)); ?>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="accordion-header" onclick="toggleAccordion('visi-box', this)">
                            <h4 class="section-title">Visi, Misi, Tata Nilai</h4>
                            <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                        </div>
                        <div id="visi-box" class="text-content">
                            <?php echo nl2br(htmlspecialchars($visi_misi)); ?>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="accordion-header" onclick="toggleAccordion('struktur-box', this)">
                            <h4 class="section-title">Struktur Organisasi</h4>
                            <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                        </div>
                        <div id="struktur-box" class="text-content">
                            <?php echo nl2br(htmlspecialchars($struktur)); ?>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="accordion-header" onclick="toggleAccordion('kebijakan-box', this)">
                            <h4 class="section-title">Kebijakan SM Terintegrasi</h4>
                            <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                        </div>
                        <div id="kebijakan-box" class="text-content">
                            <?php echo nl2br(htmlspecialchars($kebijakan)); ?>
                        </div>
                    </div>

                    <div class="content-section">
                        <div class="accordion-header" onclick="toggleAccordion('anak-box', this)">
                            <h4 class="section-title">Anak Perusahaan & Usaha Patungan</h4>
                            <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                        </div>
                        <div id="anak-box" class="text-content">
                            <?php echo nl2br(htmlspecialchars($anak_perusahaan)); ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-4">
                <div class="card-box" style="position: sticky; top: 20px;">
                    <div class="right-panel-title">Analisis Perusahaan</div>
                    <div class="check-list-item">Info perusahaan lengkap <i class="fas fa-check-circle icon-check"></i></div>
                    <div class="check-list-item">Penyedia Lowongan <i class="fas fa-check-circle icon-check"></i></div>
                    <div class="check-list-item">Terpercaya <i class="fas fa-check-circle icon-check"></i></div>
                    <br>
                    <div class="right-panel-title">Rating Perusahaan</div>
                    <div class="check-list-item" style="border: none; padding-top:0;">
                        <div class="rating-wrapper"><?php echo htmlspecialchars($rating); ?> <i class="fas fa-star icon-star"></i></div>
                    </div>
                    <a href="hapus_perusahaan.php?id=<?php echo $id_perusahaan; ?>" class="btn-delete" onclick="return confirm('Hapus <?php echo $nama_pt; ?>?');"><i class="fas fa-trash-alt"></i> Hapus Perusahaan</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function toggleAccordion(contentId, headerElement) {
    var content = document.getElementById(contentId);
    headerElement.classList.toggle('active');
    content.classList.toggle('open');
}
</script>

<?php include 'footer.php'; ?>  