<?php
// --- LOGIKA PHP UNTUK MENENTUKAN MENU AKTIF ---

// Pastikan variabel $activePage tersedia
$activePage = isset($activePage) ? $activePage : ''; 
$count_pending_perusahaan = isset($count_pending_perusahaan) ? $count_pending_perusahaan : 0;

// 1. TENTUKAN APAKAH MENU INDUK (PERUSAHAAN) HARUS TERBUKA?
// Jika sedang di halaman 'data_perusahaan' ATAU 'verifikasi', menu induk terbuka.
$isPerusahaanOpen = ($activePage == 'data_perusahaan' || $activePage == 'verifikasi');
?>

<style>
/* --- STYLE SIDEBAR --- */
.sidebar {
    width: 240px; height: 100vh; position: fixed; top: 0; left: 0;
    background: #FFFFFF; border-right: 1px solid #EFEFEF;
    padding-top: 80px; padding-left: 15px; padding-right: 15px;
    z-index: 1020; overflow-y: auto; transition: all 0.3s;
    display: flex; flex-direction: column; gap: 5px;
}

/* Menu Item Utama */
.sidebar .nav-item {
    display: flex; align-items: center; padding: 12px 15px;
    text-decoration: none; color: #64748B; font-weight: 500;
    border-radius: 10px; font-size: 14px; transition: all 0.2s ease;
    border: 1px solid transparent; cursor: pointer;
}
.sidebar .nav-item i:not(.arrow-indicator) { margin-right: 12px; width: 20px; text-align: center; font-size: 18px; }

/* Hover & Active */
.sidebar .nav-item:hover { background-color: #F8FAFC; color: #5967FF; }
.sidebar .nav-item.active { background-color: #EFF6FF; color: #5967FF; font-weight: 700; border-color: #DBEAFE; }

/* Panah */
.arrow-indicator { font-size: 12px; margin-left: auto; transition: transform 0.3s ease; opacity: 0.6; }
.nav-item.active .arrow-indicator { transform: rotate(180deg); opacity: 1; }

/* --- SUBMENU --- */
.submenu {
    overflow: hidden; max-height: 0; transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background-color: transparent; margin-left: 10px; border-left: 2px solid #F1F5F9;
}
.submenu.show { max-height: 300px; }

/* Item Submenu */
.submenu-item {
    padding: 10px 15px !important; font-size: 13px !important;
    color: #64748B; background: transparent !important; border: none !important;
    border-radius: 0 8px 8px 0 !important; margin-left: 2px; display: flex;
}
.submenu-item:hover { color: #5967FF; transform: translateX(5px); }

/* Submenu Aktif */
.submenu-item.active { color: #5967FF !important; font-weight: 700 !important; }
.submenu-item.active::before { content: '•'; margin-right: 8px; font-size: 20px; line-height: 0; color: #5967FF; }

/* Badge */
.badge-notif {
    background-color: #FF5252; color: white; font-size: 10px; font-weight: 700;
    padding: 2px 6px; border-radius: 6px; margin-left: auto;
}
</style>

<div class="sidebar">
    
    <a href="index.php" class="nav-item <?= ($activePage == 'dashboard') ? 'active' : '' ?>">
        <i class="fas fa-th-large"></i>
        <span>Halaman utama</span>
    </a>

    <a href="lowongan.php" class="nav-item <?= ($activePage == 'lowongan') ? 'active' : '' ?>">
        <i class="fas fa-briefcase"></i>
        <span>Lowongan</span>
    </a>

    <a href="javascript:void(0)" class="nav-item has-submenu <?= ($isPerusahaanOpen) ? 'active' : '' ?>" id="btnPerusahaan">
        <i class="fas fa-building"></i> 
        <span>Perusahaan</span>
        
        <?php if ($count_pending_perusahaan > 0): ?>
            <span class="badge-notif ms-2"><?= $count_pending_perusahaan ?></span>
        <?php endif; ?>

        <i class="fas fa-chevron-down arrow-indicator"></i>
    </a>
    
    <div class="submenu <?= ($isPerusahaanOpen) ? 'show' : '' ?>" id="submenuPerusahaan">
        
        <a href="data_perusahaan.php" class="nav-item submenu-item <?= ($activePage == 'data_perusahaan') ? 'active' : '' ?>">
            <span>Data Perusahaan</span>
        </a>

        <a href="verifikasi.php" class="nav-item submenu-item <?= ($activePage == 'verifikasi') ? 'active' : '' ?>">
            <span>Verifikasi</span>
            
            <?php if ($count_pending_perusahaan > 0): ?>
                <span class="badge-notif"><?= $count_pending_perusahaan ?></span>
            <?php endif; ?>
        </a>
    </div>

    <a href="laporan.php" class="nav-item <?= ($activePage == 'laporan') ? 'active' : '' ?>">
        <i class="fas fa-chart-bar"></i>
        <span>Laporan</span>
    </a>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnPerusahaan = document.getElementById('btnPerusahaan');
    const submenu = document.getElementById('submenuPerusahaan');

    if (btnPerusahaan && submenu) {
        
        // Set tinggi submenu jika status awalnya sudah terbuka dari PHP
        if (submenu.classList.contains('show')) {
            submenu.style.maxHeight = submenu.scrollHeight + "px";
        }

        btnPerusahaan.addEventListener('click', function(e) {
            e.preventDefault();
            const isOpen = submenu.classList.contains('show');

            if (isOpen) {
                // TUTUP
                submenu.style.maxHeight = '0';
                submenu.classList.remove('show');
                btnPerusahaan.classList.remove('active');
            } else {
                // BUKA
                submenu.classList.add('show');
                submenu.style.maxHeight = submenu.scrollHeight + "px";
                btnPerusahaan.classList.add('active');
            }
        });
    }
});
</script>