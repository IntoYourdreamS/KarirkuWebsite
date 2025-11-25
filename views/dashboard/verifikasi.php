<?php
require_once __DIR__ . '/../../function/supabase.php';

// --- ACTION: ACC atau TOLAK PERUSAHAAN ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'acc') {
        $status = "disetujui";
        $result = supabaseUpdate('perusahaan', ['status_persetujuan' => $status], 'id_perusahaan', $id);
    } elseif ($action == 'tolak') {
        $status = "ditolak";
        $result = supabaseUpdate('perusahaan', ['status_persetujuan' => $status], 'id_perusahaan', $id);
    }
    
    if ($result['success']) {
        header('Location: verifikasi.php');
        exit;
    }
}

// --- QUERY LANGSUNG UNTUK DATA MENUNGGU ---
$pending_result = supabaseQuery('perusahaan', [
    'select' => 'id_perusahaan,nama_perusahaan,status_persetujuan,dibuat_pada,deskripsi,alamat,lokasi,logo,website',
    'status_persetujuan' => 'eq.menunggu', // Filter langsung di query
    'order' => 'dibuat_pada.desc'
]);

// --- QUERY UNTUK DATA LAINNYA ---
$accepted_result = supabaseQuery('perusahaan', [
    'select' => 'id_perusahaan,nama_perusahaan,status_persetujuan,dibuat_pada,deskripsi,alamat,lokasi,logo,website',
    'status_persetujuan' => 'eq.disetujui',
    'order' => 'dibuat_pada.desc'
]);

$rejected_result = supabaseQuery('perusahaan', [
    'select' => 'id_perusahaan,nama_perusahaan,status_persetujuan,dibuat_pada,deskripsi,alamat,lokasi,logo,website',
    'status_persetujuan' => 'eq.ditolak',
    'order' => 'dibuat_pada.desc'
]);

// --- PROSES DATA ---
$list_pending = $pending_result['success'] ? $pending_result['data'] : [];
$list_accepted = $accepted_result['success'] ? $accepted_result['data'] : [];
$list_rejected = $rejected_result['success'] ? $rejected_result['data'] : [];

// --- HITUNG OVERDUE ---
$list_overdue = [];
$now = time();
$five_days = 5 * 24 * 60 * 60;

foreach ($list_pending as $row) {
    if (!empty($row['dibuat_pada'])) {
        $tgl_daftar = strtotime($row['dibuat_pada']);
        if ($tgl_daftar && ($now - $tgl_daftar) > $five_days) {
            $list_overdue[] = $row;
        }
    }
}

// Setup untuk sidebar
$activePage = 'verifikasi';
$count_pending_perusahaan = count($list_pending);

include 'header.php';
include 'sidebar.php';
include 'topbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Perusahaan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* --- LAYOUT UTAMA --- */
        body { 
            background-color: #F7F8FC !important; 
            font-family: 'Inter', sans-serif !important;
            margin: 0;
            padding: 0;
        }
        
        .main-content { 
            background-color: #F7F8FC; 
            min-height: 100vh; 
            margin-top: 70px !important; 
            margin-left: 240px !important; 
            padding: 0px 30px 30px 30px !important; 
            transition: all 0.3s;
            box-sizing: border-box;
        }
        
        @media (max-width: 992px) {
            .main-content { 
                margin-left: 0 !important; 
                padding: 0px 15px 15px 15px !important; 
            }
        }

        /* HEADER */
        .page-title { 
            font-weight: 700; 
            color: #1e40af; 
            font-size: 20px; 
            margin: 0 !important; 
            padding: 20px 0 !important;
            text-align: left;
        }

        /* SPLIT GRID */
        .split-grid {
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 25px; 
            align-items: start;
            margin-top: 15px;
        }
        
        @media (max-width: 1200px) { 
            .split-grid { 
                grid-template-columns: 1fr; 
            } 
        }

        /* CARD & TABLE */
        .content-card {
            background: white; 
            border-radius: 12px; 
            border: 1px solid #EFEFEF; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.02); 
            overflow: hidden; 
            margin-bottom: 25px;
        }
        
        .card-header-custom {
            padding: 15px 20px; 
            border-bottom: 1px solid #F1F5F9;
            display: flex; 
            justify-content: center; 
            align-items: center;
            background-color: #F0F5FF;
        }
        
        .card-title { 
            font-size: 15px; 
            font-weight: 700; 
            margin: 0; 
            color: #2563EB; 
            text-align: center;
        }
        
        .table-scroll { 
            max-height: 400px; 
            overflow-y: auto; 
        }
        
        .custom-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        
        .custom-table th {
            background: #fff; 
            color: #64748B; 
            font-size: 11px; 
            text-transform: uppercase;
            padding: 12px 15px; 
            text-align: center; 
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
        
        .custom-table td {
            padding: 12px 15px; 
            border-bottom: 1px solid #F8FAFC; 
            color: #334155; 
            font-size: 13px; 
            vertical-align: middle;
            text-align: center;
        }
        
        .custom-table tr:last-child td { 
            border-bottom: none; 
        }

        /* BUTTONS */
        .btn-action-sm {
            padding: 5px 10px; 
            border-radius: 6px; 
            font-size: 11px; 
            font-weight: 600;
            text-decoration: none; 
            margin-left: 4px; 
            display: inline-block; 
            border: 1px solid transparent;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .btn-acc { 
            background: #DCFCE7; 
            color: #166534; 
        } 
        
        .btn-acc:hover { 
            background: #166534; 
            color: white; 
        }
        
        .btn-rej { 
            background: #FEE2E2; 
            color: #991B1B; 
        } 
        
        .btn-rej:hover { 
            background: #991B1B; 
            color: white; 
        }
        
        .btn-eye { 
            background: #F1F5F9; 
            color: #475569; 
        } 
        
        .btn-eye:hover { 
            background: #cbd5e1; 
        }

        .right-stack { 
            display: flex; 
            flex-direction: column; 
            gap: 25px; 
        }
        
        /* STATUS BADGES */
        .status-badge {
            padding: 3px 8px; 
            border-radius: 6px; 
            font-size: 10px; 
            font-weight: 600;
            display: inline-block;
        }
        
        .status-pending { 
            background: #FEF3C7; 
            color: #92400E; 
        }
        
        .status-overdue { 
            background: #FEE2E2; 
            color: #991B1B; 
        }

        /* TEXT UTILITIES */
        .text-center { text-align: center; }
        .text-muted { color: #6c757d; }
        .small { font-size: 0.875em; }
        .fw-bold { font-weight: bold; }
        .py-3 { padding-top: 1rem; padding-bottom: 1rem; }
        .py-5 { padding-top: 3rem; padding-bottom: 3rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .me-2 { margin-right: 0.5rem; }
        .opacity-25 { opacity: 0.25; }
    </style>
</head>
<body>
    <div class="main-content">
        <h4 class="page-title">Verifikasi Perusahaan</h4>

        <div class="split-grid">
            
            <!-- KOLOM KIRI: Permintaan Bergabung -->
            <div class="left-col">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h5 class="card-title">Permintaan Bergabung (<?= count($list_pending) ?>)</h5>
                    </div>
                    <div class="table-scroll">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Perusahaan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($list_pending)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small">
                                            <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i><br>
                                            Tidak ada permintaan bergabung.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($list_pending as $row): 
                                        $is_overdue = in_array($row, $list_overdue);
                                        $tgl_daftar = !empty($row['dibuat_pada']) ? date('d M Y', strtotime($row['dibuat_pada'])) : '-';
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_perusahaan']) ?></div>
                                            <small class="text-muted"><?= substr($row['deskripsi'] ?? 'Tidak ada deskripsi', 0, 35) ?>...</small>
                                        </td>
                                        <td class="text-muted small">
                                            <?= $tgl_daftar ?>
                                        </td>
                                        <td>
                                            <?php if ($is_overdue): ?>
                                                <span class="status-badge status-overdue">Overdue</span>
                                            <?php else: ?>
                                                <span class="status-badge status-pending">Menunggu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="detail_perusahaan.php?id=<?= $row['id_perusahaan'] ?>" class="btn-action-sm btn-eye" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="verifikasi.php?action=acc&id=<?= $row['id_perusahaan'] ?>" class="btn-action-sm btn-acc" 
                                               onclick="return confirm('Terima perusahaan <?= htmlspecialchars($row['nama_perusahaan']) ?>?')" title="Terima">
                                               ACC
                                            </a>
                                            <a href="verifikasi.php?action=tolak&id=<?= $row['id_perusahaan'] ?>" class="btn-action-sm btn-rej" 
                                               onclick="return confirm('Tolak perusahaan <?= htmlspecialchars($row['nama_perusahaan']) ?>?')" title="Tolak">
                                               X
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: Stack Vertikal -->
            <div class="right-stack">
                
                <!-- Permintaan Diterima -->
                <div class="content-card">
                    <div class="card-header-custom">
                        <h5 class="card-title">Permintaan Diterima (<?= count($list_accepted) ?>)</h5>
                    </div>
                    <div class="table-scroll" style="max-height: 200px;">
                        <table class="custom-table">
                            <tbody>
                                <?php if (empty($list_accepted)): ?>
                                    <tr>
                                        <td class="text-center py-3 text-muted small">
                                            Belum ada perusahaan yang diterima.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($list_accepted as $row): 
                                        $tgl_daftar = !empty($row['dibuat_pada']) ? date('d/m/y', strtotime($row['dibuat_pada'])) : '-';
                                    ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-check-circle text-success me-2"></i> 
                                            <strong><?= htmlspecialchars($row['nama_perusahaan']) ?></strong>
                                        </td>
                                        <td class="text-muted small">
                                            <?= $tgl_daftar ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Permintaan Ditolak -->
                <div class="content-card">
                    <div class="card-header-custom">
                        <h5 class="card-title">Permintaan Ditolak (<?= count($list_rejected) ?>)</h5>
                    </div>
                    <div class="table-scroll" style="max-height: 200px;">
                        <table class="custom-table">
                            <tbody>
                                <?php if (empty($list_rejected)): ?>
                                    <tr>
                                        <td class="text-center py-3 text-muted small">
                                            Belum ada perusahaan yang ditolak.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($list_rejected as $row): 
                                        $tgl_daftar = !empty($row['dibuat_pada']) ? date('d/m/y', strtotime($row['dibuat_pada'])) : '-';
                                    ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-times-circle text-danger me-2"></i> 
                                            <strong><?= htmlspecialchars($row['nama_perusahaan']) ?></strong>
                                        </td>
                                        <td class="text-muted small">
                                            <?= $tgl_daftar ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Lewat > 5 Hari -->
                <div class="content-card">
                    <div class="card-header-custom">
                        <h5 class="card-title">Lewat > 5 Hari (<?= count($list_overdue) ?>)</h5>
                    </div>
                    <div class="table-scroll" style="max-height: 250px;">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Perusahaan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($list_overdue)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center py-3 text-muted small">
                                            Tidak ada permintaan overdue.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($list_overdue as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($row['nama_perusahaan']) ?></div>
                                            <small class="text-danger">
                                                <i class="far fa-clock"></i> Telat Verifikasi
                                            </small>
                                        </td>
                                        <td>
                                            <a href="verifikasi.php?action=acc&id=<?= $row['id_perusahaan'] ?>" 
                                               class="btn-action-sm btn-acc"
                                               onclick="return confirm('Terima perusahaan <?= htmlspecialchars($row['nama_perusahaan']) ?>?')">
                                               ACC
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>