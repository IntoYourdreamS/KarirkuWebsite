<?php
// tambah-lowongan.php

// Set variabel dasar terlebih dahulu
$base_url = '../../';
$activePage = 'tambah-lowongan';

// Perbaikan path include supabase.php - gunakan path yang sama seperti di config.php
$supabase_path = __DIR__ . '/../../function/supabase.php';
if (file_exists($supabase_path)) {
    include $supabase_path;
} else {
    // Debug: tampilkan path yang dicoba
    error_log("Supabase path not found: " . $supabase_path);
    die("File supabase.php tidak ditemukan di: " . $supabase_path);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id_perusahaan' => 1, // Ganti dengan ID perusahaan yang sesuai
        'judul' => $_POST['judul'] ?? '',
        'deskripsi' => $_POST['deskripsi'] ?? '',
        'kualifikasi' => $_POST['kualifikasi'] ?? '',
        'lokasi' => $_POST['lokasi'] ?? '',
        'tipe_pekerjaan' => $_POST['tipe_pekerjaan'] ?? 'full-time',
        'gaji_range' => $_POST['gaji_range'] ?? '',
        'batas_tanggal' => $_POST['batas_tanggal'] ?? null,
        'status' => 'ditinjau', // Default status saat dibuat
        'kategori' => $_POST['kategori'] ?? '',
        'mode_kerja' => $_POST['mode_kerja'] ?? 'On-site',
        'benefit' => $_POST['benefit'] ?? ''
    ];

    $result = supabaseInsert('lowongan', $data);
    
    if ($result['success']) {
        header('Location: lowongan.php?success=1');
        exit;
    } else {
        $error = "Gagal menambah lowongan: " . ($result['data']['message'] ?? 'Unknown error');
        error_log("Error inserting job: " . print_r($result, true));
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lowongan Baru - Karirku</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/company.css">
</head>

<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <?php 
        // Include sidebar dengan path yang benar
        $sidebar_path = __DIR__ . '/sidebar.php';
        if (file_exists($sidebar_path)) {
            include $sidebar_path;
        } else {
            echo "Sidebar tidak ditemukan";
        }
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                </div>

                <div class="topbar-right">
                    <button class="notification-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="notification-badge"></span>
                    </button>

                    <div class="user-profile">
                        <div class="user-avatar">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span style="font-size: 14px; font-weight: 500;">Admin</span>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="content-header">
                    <h2>Tambah Lowongan Baru</h2>
                    <p>Buat lowongan pekerjaan baru untuk perusahaan Anda</p>
                </div>

                <!-- Form Tambah Lowongan -->
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 32px;">
                    <?php if (isset($error)): ?>
                        <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                            Lowongan berhasil ditambahkan!
                        </div>
                    <?php endif; ?>

                    <form method="POST" style="max-width: 600px;">
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Judul Lowongan</label>
                            <input type="text" name="judul" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;" placeholder="Contoh: Frontend Developer" required>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Lokasi</label>
                            <input type="text" name="lokasi" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;" placeholder="Contoh: Jakarta, Remote, dll.">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Gaji</label>
                            <input type="text" name="gaji_range" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;" placeholder="Contoh: Rp 5.000.000 - Rp 8.000.000">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Jenis Pekerjaan</label>
                            <select name="tipe_pekerjaan" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                                <option value="full-time">Full Time</option>
                                <option value="part-time">Part Time</option>
                                <option value="contract">Contract</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Mode Kerja</label>
                            <select name="mode_kerja" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                                <option value="On-site">On-site</option>
                                <option value="Hybrid">Hybrid</option>
                                <option value="Remote">Remote</option>
                                <option value="Shift">Shift</option>
                                <option value="Lapangan">Lapangan</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Kategori</label>
                            <input type="text" name="kategori" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;" placeholder="Contoh: IT & Development">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Batas Tanggal</label>
                            <input type="date" name="batas_tanggal" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Deskripsi Pekerjaan</label>
                            <textarea name="deskripsi" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; min-height: 120px;" placeholder="Jelaskan tentang pekerjaan ini..." required></textarea>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Kualifikasi</label>
                            <textarea name="kualifikasi" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; min-height: 100px;" placeholder="Syarat dan kualifikasi yang dibutuhkan..."></textarea>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Benefit</label>
                            <textarea name="benefit" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; min-height: 80px;" placeholder="Fasilitas dan benefit yang ditawarkan..."></textarea>
                        </div>
                        
                        <div style="display: flex; gap: 12px;">
                            <button type="submit" style="background: #2563eb; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                                Simpan Lowongan
                            </button>
                            <a href="lowongan.php" style="background: #6b7280; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; text-decoration: none; display: inline-block;">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        }

        // Initialize sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const collapse = document.getElementById('lowonganCollapse');
            const icon = document.getElementById('collapseIcon');
            
            if (collapse && collapse.classList.contains('expanded')) {
                const items = collapse.querySelectorAll('.nav-collapse-item');
                const itemHeight = 36;
                const totalHeight = items.length * itemHeight;
                collapse.style.maxHeight = totalHeight + 'px';
            }
        });
    </script>
</body>
</html>