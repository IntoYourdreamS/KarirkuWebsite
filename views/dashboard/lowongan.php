<?php 
  // --- 1. SETUP KONEKSI KE SUPABASE ---
  require __DIR__ . '/../../vendor/autoload.php';
  use GuzzleHttp\Client;

  // KONFIGURASI
  $supabaseUrl = 'https://tkjnbelcgfwpbhppsnrl.supabase.co';
  $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRram5iZWxjZ2Z3cGJocHBzbnJsIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2MTc0MDc2MiwiZXhwIjoyMDc3MzE2NzYyfQ.vZoNXxMWtoG4ktg7K6Whqv8EFzCv7qbS3OAHEfxVoR0';

  $daftar_lowongan = [];
  $keyword = isset($_GET['q']) ? $_GET['q'] : '';

  try {
      $client = new Client([
          'base_uri' => $supabaseUrl . '/rest/v1/',
          'headers' => [
              'apikey'        => $supabaseKey,
              'Authorization' => 'Bearer ' . $supabaseKey,
              'Content-Type'  => 'application/json',
          ],
          'http_errors' => false
      ]);

      // --- QUERY PENTING: RELASI ANTAR TABEL ---
      // select=*,perusahaan(nama_perusahaan)
      // Artinya: Ambil semua kolom lowongan, DAN ambil kolom 'nama_perusahaan' dari tabel sebelah.
      $queryUrl = 'lowongan?select=*,perusahaan(nama_perusahaan)&order=id_lowongan.desc';

      if (!empty($keyword)) {
          // Cari berdasarkan Judul Lowongan
          $queryUrl .= '&judul=ilike.*' . urlencode($keyword) . '*';
      }

      $response = $client->get($queryUrl);
      
      if ($response->getStatusCode() == 200) {
          $daftar_lowongan = json_decode($response->getBody(), true);
      }
  } catch (Exception $e) { }

  $activePage = 'lowongan'; // Pastikan di sidebar.php kamu highlight menu ini
  include 'header.php';
  include 'sidebar.php';
  include 'topbar.php';
?>

<style>
  .main-content { background-color: #F7F8FC; padding: 24px; min-height: 100vh; }
  
  /* --- SEARCH BAR (Sama seperti Perusahaan) --- */
  .search-form-box {
    background-color: #FFFFFF; border-radius: 12px; border: 1px solid #EFEFEF;
    padding: 0 16px; height: 50px; display: flex; align-items: center; margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02); width: 100%;
  }
  .search-input {
    border: none; outline: none; flex-grow: 1; font-size: 15px; color: #333;
    height: 100%; background: transparent;
  }
  .search-submit-btn { 
    background: none; border: none; cursor: pointer; padding: 0;
    display: flex; align-items: center; justify-content: center;
    height: 100%; margin-left: 10px;
  }
  .search-submit-btn svg { fill: #7B61FF; width: 20px; height: 20px; transition: transform 0.2s; }
  .search-submit-btn:hover svg { transform: scale(1.1); fill: #5a45cc; }

  /* Filter */
  .filter-bar-container { 
    background-color: #FFFFFF; border-radius: 12px; border: 1px solid #EFEFEF;
    padding: 0 16px; height: 50px; display: flex; align-items: center; margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02); justify-content: space-between;
  }
  .btn-filter {
    background: none; border: none; font-size: 15px; font-weight: 500;
    display: flex; align-items: center; cursor: pointer; color: #333;
  }
  .btn-filter .filter-count {
    background-color: #7B61FF; color: white; border-radius: 6px;
    padding: 2px 8px; font-size: 12px; font-weight: bold; margin-left: 8px;
  }

  /* --- STYLE CARD LOWONGAN (Sedikit Beda) --- */
  .company-list-scroll-wrapper { max-height: 70vh; overflow-y: auto; padding-right: 10px; }
  .company-card-list { display: flex; flex-direction: column; gap: 16px; }

  .job-card {
    background-color: #FFFFFF; border-radius: 12px; border: 1px solid #EFEFEF;
    padding: 20px 24px; display: flex; align-items: center; justify-content: flex-start;
    gap: 24px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
  }
  
  .job-info { flex-grow: 1; }
  .job-title { display: block; font-weight: 700; font-size: 16px; color: #2D3748; margin-bottom: 4px; }
  .company-ref { font-size: 14px; color: #718096; font-weight: 500; display: flex; align-items: center; gap: 6px;}
  .company-ref i { color: #CBD5E0; }

  .job-type-badge {
      background-color: #EBF8FF; color: #3182CE;
      padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;
      margin-top: 8px; display: inline-block;
  }

  .card-actions-right { display: flex; align-items: center; gap: 12px; }
  
  .btn-detail { 
    padding: 8px 16px; border: none; border-radius: 8px; font-weight: 600; font-size: 14px;
    background-color: #5967FF; color: white; text-decoration: none; transition: all 0.2s ease;
  }
  .btn-detail:hover { background-color: #4754E5; color: white; }
  
  .btn-hapus { 
    padding: 8px 16px; border: none; border-radius: 8px; font-weight: 600; font-size: 14px;
    background-color: #FF4D4D; color: white; text-decoration: none; transition: all 0.2s ease;
  }
  .btn-hapus:hover { background-color: #D00000; color: white; }

</style>

<div class="main-content">
  <h2 class="mb-4" style="display:block;">Data Lowongan</h2>

  <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_sukses') : ?>
    <div class="alert alert-success mb-3" role="alert" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px;">
        ✅ Data lowongan berhasil dihapus!
    </div>
  <?php endif; ?>

  <div class="container-fluid p-0">
    <div class="row">
      <div class="col-lg-8">
        <form action="" method="GET" class="search-form-box">
            <input type="text" name="q" class="search-input" placeholder="Cari judul lowongan..." value="<?php echo htmlspecialchars($keyword); ?>" autocomplete="off">
            <button type="submit" class="search-submit-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0s208 93.1 208 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
            </button>
        </form>
      </div>
      
      <div class="col-lg-4">
        <div class="filter-bar-container">
          <button class="btn-filter">
            <i class="fas fa-filter"></i> Total Lowongan <span class="filter-count"><?php echo count($daftar_lowongan); ?></span>
          </button>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-lg-8">
        <div class="company-list-scroll-wrapper">
          <div class="company-card-list">
            
            <?php 
            if (empty($daftar_lowongan)) {
                echo "<div style='padding: 40px; text-align: center; color: #888;'>";
                if(!empty($keyword)){
                    echo "Tidak ditemukan lowongan dengan judul '<b>".htmlspecialchars($keyword)."</b>'";
                } else {
                    echo "Belum ada data lowongan.";
                }
                echo "</div>";
            } else {
                foreach ($daftar_lowongan as $row) { 
                  
                  // Ambil Judul
                  $judul = $row['judul'] ?? 'Tanpa Judul';
                  $id_loker = $row['id_lowongan'];

                  // AMBIL NAMA PERUSAHAAN DARI RELASI
                  // Karena kita pakai query select=*,perusahaan(nama_perusahaan)
                  // Maka datanya ada di dalam array 'perusahaan'
                  $nama_pt = 'Perusahaan Tidak Diketahui';
                  if (isset($row['perusahaan']) && isset($row['perusahaan']['nama_perusahaan'])) {
                      $nama_pt = $row['perusahaan']['nama_perusahaan'];
                  }

                  // Ambil Tipe Pekerjaan (Kalau ada kolomnya)
                  $tipe = $row['tipe_pekerjaan'] ?? 'Full Time'; 
            ?>

            <div class="job-card">
              <div class="job-info">
                  <span class="job-title"><?php echo htmlspecialchars($judul); ?></span>
                  
                  <div class="company-ref">
                      <i class="fas fa-building"></i> <?php echo htmlspecialchars($nama_pt); ?>
                  </div>

                  <span class="job-type-badge"><?php echo htmlspecialchars($tipe); ?></span>
              </div>
              
              <div class="card-actions-right">
                <a href="detail_lowongan.php?id=<?php echo $id_loker; ?>" class="btn-detail">Detail</a>
                
                <a href="hapus_lowongan.php?id=<?php echo $id_loker; ?>" 
                   class="btn-hapus"
                   onclick="return confirm('Hapus lowongan <?php echo $judul; ?>?');">
                   Hapus
                </a>
              </div>
            </div>

            <?php 
                } 
            } 
            ?>

          </div> 
        </div> 
      </div> 
      <div class="col-lg-4"></div>
    </div> 
  </div> 
</div>

<?php include 'footer.php'; ?>