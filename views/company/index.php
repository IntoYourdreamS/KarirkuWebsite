<?php
$activePage = 'halaman-utama'; // Set halaman aktif
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karirku</title>
    <link rel="stylesheet" href="../../assets/css/company.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

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
                    <h2>Selamat Datang di Dashboard</h2>
                    <p>Kelola perusahaan dan lowongan pekerjaan Anda</p>
                </div>

                <!-- Stats Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
                    <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h3 style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">Total Lowongan</h3>
                        <p style="font-size: 24px; font-weight: bold; color: #1f2937;">15</p>
                    </div>
                    <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h3 style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">Pelamar Baru</h3>
                        <p style="font-size: 24px; font-weight: bold; color: #1f2937;">8</p>
                    </div>
                    <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h3 style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">Lowongan Aktif</h3>
                        <p style="font-size: 24px; font-weight: bold; color: #1f2937;">12</p>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Aktivitas Terbaru</h3>
                    <div style="color: #6b7280;">
                        <p>• 5 pelamar baru untuk posisi Frontend Developer</p>
                        <p>• Lowongan UI/UX Designer telah dipublikasi</p>
                        <p>• 3 lowongan membutuhkan perhatian</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hidden');
        }

        // Pastikan nav collapse dalam state yang benar saat load
        document.addEventListener('DOMContentLoaded', function() {
            const collapse = document.getElementById('lowonganCollapse');
            const icon = document.getElementById('collapseIcon');
            
            // Jika halaman aktif adalah lowongan, expand menu
            if (collapse.classList.contains('expanded')) {
                const items = collapse.querySelectorAll('.nav-collapse-item');
                const itemHeight = 36;
                const totalHeight = items.length * itemHeight;
                collapse.style.maxHeight = totalHeight + 'px';
            }
        });
    </script>
</body>
</html>