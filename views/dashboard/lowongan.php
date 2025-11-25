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

      // Query: Ambil data lowongan + nama perusahaan
      $queryUrl = 'lowongan?select=*,perusahaan(nama_perusahaan)&order=id_lowongan.desc';

      if (!empty($keyword)) {
          $queryUrl .= '&judul=ilike.*' . urlencode($keyword) . '*';
      }

      $response = $client->get($queryUrl);
      
      if ($response->getStatusCode() == 200) {
          $daftar_lowongan = json_decode($response->getBody(), true);
      }
  } catch (Exception $e) { }

  $activePage = 'lowongan'; 
  include 'header.php';
  include 'sidebar.php';
  include 'topbar.php';
?>

<style>
  /* --- GLOBAL STYLE --- */
  body { background-color: #F4F7FE; font-family: 'Inter', sans-serif; }
  
  .main-content { 
      margin-top: 55px !important; 
      margin-left: 240px !important; 
      /* PADDING ATAS 0 AGAR MEPET */
      padding: 0px 35px 30px 35px !important; 
      transition: all 0.3s;
  }
  @media (max-width: 992px) { .main-content { margin-left: 0 !important; padding: 15px !important; } }

  /* --- TOOLBAR (SEARCH & FILTER) --- */
  .top-action-wrapper {
      display: flex; gap: 15px; 
      margin-bottom: 20px; 
      /* Margin Top 15px agar nempel rapi di bawah Topbar */
      margin-top: 15px; 
  }
  .search-bar-large {
      flex-grow: 1; background: white; border-radius: 30px; 
      padding: 8px 20px; display: flex; align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.02); border: 1px solid #E0E5F2;
  }
  .search-bar-large input {
      border: none; outline: none; width: 100%; font-size: 14px; color: #444; background: transparent;
  }
  .search-btn-transparent {
      background: transparent; border: none; color: #5967FF; font-size: 18px;
      cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 5px;
  }

  .btn-filter, .btn-category {
      background: white; border: 1px solid #E0E5F2; border-radius: 12px;
      padding: 0 20px; display: flex; align-items: center; gap: 8px;
      color: #666; font-weight: 600; font-size: 14px; cursor: pointer; 
      height: 46px; text-decoration: none; transition: 0.2s; white-space: nowrap;
  }
  .btn-filter:hover, .btn-category:hover { transform: translateY(-2px); }
  .badge-purple {
      background: #5967FF; color: white; font-size: 10px; padding: 2px 6px; 
      border-radius: 50%; margin-left: 5px;
  }

  /* --- CARD CONTAINER PUTIH --- */
  .content-card-wrapper {
      background: white; border-radius: 20px; padding: 25px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.02);
      min-height: 80vh; display: flex; flex-direction: column;
  }

  .section-title {
      font-size: 18px; font-weight: 700; color: #11047A; margin-bottom: 25px;
  }

  /* --- HEADER TABLE --- */
  .list-header {
      display: flex; padding: 0 25px 10px 25px;
      border-bottom: 1px solid #E0E5F2; margin-bottom: 15px;
  }
  .col-header {
      color: #A3AED0; font-size: 14px; font-weight: 500;
  }

  /* --- ROW ITEM (ITEM LOWONGAN) --- */
  .list-row {
      background: white; border: 1px solid #5967FF; border-radius: 16px;
      padding: 20px 25px; margin-bottom: 15px;
      display: flex; align-items: center; 
      transition: all 0.2s;
  }
  .list-row:hover { background-color: #F8F9FF; transform: translateX(5px); }

  /* --- PEMBAGIAN KOLOM --- */
  /* 1. Nama Lowongan */
  .col-name { flex: 2; min-width: 250px; padding-right: 15px; }
  .job-title-text { display: block; font-weight: 700; font-size: 16px; color: #11047A; margin-bottom: 4px; }
  .job-company-text { font-size: 13px; color: #A3AED0; font-weight: 500; }

  /* 2. Lokasi */
  .col-loc { flex: 1; font-weight: 700; font-size: 14px; color: #2B3674; }

  /* 3. Berlaku */
  .col-date { flex: 1; font-size: 13px; color: #A3AED0; line-height: 1.4; }

  /* 4. Status */
  .col-status { width: 100px; text-align: center; }
  .badge-active {
      background-color: #E6F9EB; color: #05CD99; padding: 6px 15px; 
      border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-block;
  }

  /* 5. Action */
  .col-action { width: 100px; text-align: right; }
  .btn-detail-blue {
      background-color: #11047A; color: white; text-decoration: none;
      padding: 10px 25px; border-radius: 8px; font-size: 12px; font-weight: 600; 
      display: inline-block;
  }
  .btn-detail-blue:hover { background-color: #201396; color: white; }

  /* SCROLLBAR CUSTOM */
  .scroll-container {
      overflow-y: auto; max-height: 65vh; padding-right: 10px;
  }
  .scroll-container::-webkit-scrollbar { width: 6px; }
  .scroll-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
  .scroll-container::-webkit-scrollbar-thumb { background: #11047A; border-radius: 10px; }

</style>

<div class="main-content">
    
    <div class="top-action-wrapper">
        <form action="" method="GET" style="flex-grow:1; display:flex;">
            <div class="search-bar-large">
                <input type="text" name="q" placeholder="Cari judul lowongan..." value="<?= htmlspecialchars($keyword) ?>" autocomplete="off">
                <button type="submit" class="search-btn-transparent"><i class="fas fa-search"></i></button>
            </div>
        </form>
        
        <div class="btn-filter">
            <i class="fas fa-sliders-h"></i> Filter 
            <span class="badge-purple">5</span>
        </div>
        
        <div class="btn-category">Kategori</div>
    </div>

    <div class="content-card-wrapper">
        <h4 class="section-title">Lowongan</h4>

        <div class="list-header">
            <div class="col-header col-name">Nama lowongan</div>
            <div class="col-header col-loc">Lokasi</div>
            <div class="col-header col-date">Berlaku</div>
            <div class="col-header col-status">Status</div>
            <div class="col-header col-action"></div>
        </div>

        <div class="scroll-container">
            
            <?php if (empty($daftar_lowongan)): ?>
                <div class="text-center py-5 text-muted">
                    <p>Tidak ada data lowongan.</p>
                </div>
            <?php else: ?>
                
                <?php foreach ($daftar_lowongan as $row): 
                    $id = $row['id_lowongan'];
                    $judul = $row['judul'];
                    
                    // Nama Perusahaan (Relasi)
                    $pt = isset($row['perusahaan']['nama_perusahaan']) ? $row['perusahaan']['nama_perusahaan'] : 'Perusahaan';
                    
                    // Lokasi (Default Jakarta jika kosong)
                    $lokasi = !empty($row['lokasi']) ? $row['lokasi'] : 'Jakarta';
                    
                    // Tanggal
                    $tgl_mulai = !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '-';
                    $tgl_akhir = !empty($row['tanggal_berakhir']) ? date('d M Y', strtotime($row['tanggal_berakhir'])) : '-';
                    $rentang_tgl = "$tgl_mulai - <br> $tgl_akhir";

                    // Status (Default Aktif)
                    $status = 'Aktif'; 
                ?>

                <div class="list-row">
                    
                    <div class="col-name">
                        <span class="job-title-text"><?= htmlspecialchars($judul) ?></span>
                        <span class="job-company-text"><?= htmlspecialchars($pt) ?></span>
                    </div>

                    <div class="col-loc"><?= htmlspecialchars($lokasi) ?></div>

                    <div class="col-date"><?= $rentang_tgl ?></div>

                    <div class="col-status">
                        <span class="badge-active"><?= $status ?></span>
                    </div>

                    <div class="col-action">
                        <a href="detail_lowongan.php?id=<?= $id ?>" class="btn-detail-blue">Detail</a>
                    </div>

                </div>

                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php include 'footer.php'; ?>