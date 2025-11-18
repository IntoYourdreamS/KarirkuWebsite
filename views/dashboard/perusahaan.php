<?php 
  // --- 1. SETUP KONEKSI KE SUPABASE ---
  require __DIR__ . '/../../vendor/autoload.php';
  use GuzzleHttp\Client;

  // KONFIGURASI
  $supabaseUrl = 'https://tkjnbelcgfwpbhppsnrl.supabase.co';
  $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRram5iZWxjZ2Z3cGJocHBzbnJsIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2MTc0MDc2MiwiZXhwIjoyMDc3MzE2NzYyfQ.vZoNXxMWtoG4ktg7K6Whqv8EFzCv7qbS3OAHEfxVoR0';

  $daftar_perusahaan = [];
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

      // --- PERBAIKAN QUERY (MENAMBAHKAN HITUNGAN LOWONGAN) ---
      // Kita minta: select=*,lowongan(count)
      // Artinya: Ambil semua data perusahaan, DAN tolong hitung data di tabel 'lowongan' yang nyambung
      $queryUrl = 'perusahaan?select=*,lowongan(count)&order=id_perusahaan.desc';

      if (!empty($keyword)) {
          // Kalau cari, tetap hitung lowongannya
          $queryUrl .= '&nama_perusahaan=ilike.*' . urlencode($keyword) . '*';
      }

      $response = $client->get($queryUrl);
      if ($response->getStatusCode() == 200) {
          $daftar_perusahaan = json_decode($response->getBody(), true);
      }
  } catch (Exception $e) {}

  $activePage = 'perusahaan';
  include 'header.php';
  include 'sidebar.php';
  include 'topbar.php';
?>

<style>
  .main-content { background-color: #F7F8FC; padding: 24px; min-height: 100vh; }
  .main-content > h2.mb-4, .main-content > .card { display: none; }

  /* Style Pencarian */
  .search-form-box {
    background-color: #FFFFFF; border-radius: 12px; border: 1px solid #EFEFEF;
    padding: 0 16px; height: 50px; display: flex; align-items: center; margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02); width: 100%;
  }
  .search-input {
    border: none; outline: none; flex-grow: 1; font-size: 15px; color: #333;
    height: 100%; background: transparent;
  }
  .search-input::placeholder { color: #AAAAAA; }
  .search-submit-btn { 
    background: none; border: none; cursor: pointer; padding: 0;
    display: flex; align-items: center; justify-content: center;
    height: 100%; margin-left: 10px;
  }
  .search-submit-btn svg {
      fill: #7B61FF; width: 20px; height: 20px; transition: transform 0.2s;
  }
  .search-submit-btn:hover svg { transform: scale(1.1); fill: #5a45cc; }

  /* Filter Bar */
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

  /* Card List */
  .company-list-scroll-wrapper { max-height: 70vh; overflow-y: auto; padding-right: 10px; }
  .company-card-list { display: flex; flex-direction: column; gap: 16px; }
  .company-card {
    background-color: #FFFFFF; border-radius: 12px; border: 1px solid #EFEFEF;
    padding: 20px 24px; display: flex; align-items: center; justify-content: flex-start;
    gap: 24px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
  }
  .company-name { font-weight: 600; font-size: 15px; color: #2D3748; min-width: 180px; }
  .job-count { font-weight: 600; font-size: 14px; color: #333; }
  .join-date { font-size: 14px; color: #718096; }
  .card-actions-right {
    display: flex; align-items: center; gap: 16px; margin-left: auto;
  }
  .status-badge {
    background-color: #E6F6EC !important; color: #38A169 !important;
    font-weight: 600; padding: 6px 14px; border-radius: 6px; font-size: 14px;
  }
  .btn-detail { 
    padding: 8px 16px; border: none; border-radius: 8px; font-weight: 600; font-size: 14px;
    background-color: #5967FF; color: white; text-decoration: none; transition: all 0.2s ease;
  }
  .btn-detail:hover { background-color: #4754E5; color: white; }
</style>

<div class="main-content">
  <h2 class="mb-4" style="display:block;">Data Perusahaan</h2>

  <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_sukses') : ?>
    <div class="alert alert-success mb-3" role="alert" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px;">
        ✅ Data perusahaan berhasil dihapus!
    </div>
  <?php endif; ?>

  <div class="container-fluid p-0">
    <div class="row">
      <div class="col-lg-8">
        <form action="" method="GET" class="search-form-box">
            <input type="text" name="q" class="search-input" placeholder="Cari nama perusahaan..." value="<?php echo htmlspecialchars($keyword); ?>" autocomplete="off">
            <button type="submit" class="search-submit-btn" title="Cari">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0s208 93.1 208 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                </svg>
            </button>
        </form>
      </div>
      <div class="col-lg-4">
        <div class="filter-bar-container">
          <button class="btn-filter">
            <i class="fas fa-filter"></i> Total Data <span class="filter-count"><?php echo count($daftar_perusahaan); ?></span>
          </button>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-lg-8">
        <div class="company-list-scroll-wrapper">
          <div class="company-card-list">
            
            <?php 
            if (empty($daftar_perusahaan)) {
                echo "<div style='padding: 40px; text-align: center; color: #888;'>Belum ada data perusahaan.</div>";
            } else {
                foreach ($daftar_perusahaan as $row) { 
                  $nama_pt = isset($row['nama_perusahaan']) ? $row['nama_perusahaan'] : 'Tanpa Nama';
                  $id_pt   = isset($row['id_perusahaan']) ? $row['id_perusahaan'] : '#';

                  // --- LOGIKA HITUNG LOWONGAN ---
                  // Supabase mengembalikan hitungan dalam array 'lowongan' -> index 0 -> 'count'
                  $jumlah_lowongan = 0;
                  if (isset($row['lowongan']) && is_array($row['lowongan']) && !empty($row['lowongan'])) {
                      $jumlah_lowongan = $row['lowongan'][0]['count'];
                  }
            ?>

            <div class="company-card">
              <span class="company-name"><?php echo htmlspecialchars($nama_pt); ?></span>
              
              <span class="job-count"><?php echo $jumlah_lowongan; ?> Lowongan</span>
              
              <span class="join-date" style="color: #aaa; font-size: 13px;">Mitra Perusahaan</span>
              
              <div class="card-actions-right">
                <span class="badge status-badge">Aktif</span>
                <a href="detail_perusahaan.php?id=<?php echo $id_pt; ?>" class="btn-action btn-detail">Detail</a>
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