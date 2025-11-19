<?php
// lowongan.php
include 'config.php';
$activePage = 'lowongan-saya';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowongan Saya - Karirku</title>
    
    <!-- Critical CSS -->
    <style>
    .dashboard { display: flex; height: 100vh; }
    .sidebar { width: 260px; background: white; transition: all 0.3s; }
    .sidebar.hidden { width: 0; overflow: hidden; }
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    .hidden { display: none; }
    </style>
    
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/company.css">
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
                    <h2>Lowongan Terbaru</h2>
                    <div class="text-tabs">
                        <span class="text-tab active" onclick="changeTab('semua')">Semua</span>
                        <span class="text-tab" onclick="changeTab('live')">Live (<?php echo isset($jobsData['live']) ? count($jobsData['live']) : 0; ?>)</span>
                        <span class="text-tab" onclick="changeTab('perlu')">Perlu ditinjau (<?php echo isset($jobsData['perlu']) ? count($jobsData['perlu']) : 0; ?>)</span>
                        <span class="text-tab" onclick="changeTab('sedang')">Ditolak (<?php echo isset($jobsData['sedang']) ? count($jobsData['sedang']) : 0; ?>)</span>
                    </div>
                </div>

                <div class="tabs-container">
                    <div class="filters">
                        <div class="filters-left">
                            <div class="search-box">
                                <svg class="search-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" placeholder="Cari lowongan..." oninput="filterJobs()" id="searchInput">
                            </div>
                            <select class="filter-select" onchange="filterJobs()" id="categorySelect">
                                <option value="">Semua Kategori</option>
                                <option>IT & Development</option>
                                <option>Design</option>
                                <option>Marketing</option>
                            </select>
                            <select class="filter-select" onchange="filterJobs()" id="prioritySelect">
                                <option value="">Semua Prioritas</option>
                                <option>Tinggi</option>
                                <option>Sedang</option>
                                <option>Rendah</option>
                            </select>
                        </div>
                        <div class="filters-right">
                            <button class="btn" onclick="toggleView()">Tampilan</button>
                            <a href="tambah-lowongan.php" class="btn btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Buat Lowongan</span>
                            </a>
                        </div>
                    </div>

                    <div class="jobs-table-container">
                        <table class="jobs-table">
                            <thead>
                                <tr>
                                    <th>Lowongan</th>
                                    <th>Gaji</th>
                                    <th>Performa</th>
                                    <th>Status</th>
                                    <th>Analisis Lowongan</th>
                                </tr>
                            </thead>
                            <tbody id="jobsTableBody">
                                <!-- Jobs akan di-render oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data dari PHP - sekarang berisi data yang sudah difilter
        const jobsData = <?php echo $jobsDataJson; ?>;
        let currentTab = 'semua';
        let currentView = 'table';
        let allJobsData = jobsData.semua; // Simpan semua data untuk filtering

        // Fungsi untuk toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        }

        // Fungsi untuk change tab
        function changeTab(tab) {
            currentTab = tab;
            document.querySelectorAll('.text-tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            renderJobs();
        }

        // Fungsi untuk filter jobs
        function filterJobs() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const category = document.getElementById('categorySelect').value;
            
            let filteredJobs = allJobsData;
            
            // Apply search filter
            if (searchTerm) {
                filteredJobs = filteredJobs.filter(job => 
                    job.title.toLowerCase().includes(searchTerm) ||
                    job.company.toLowerCase().includes(searchTerm)
                );
            }
            
            // Apply category filter
            if (category) {
                filteredJobs = filteredJobs.filter(job => 
                    job.category && job.category.toLowerCase().includes(category.toLowerCase())
                );
            }
            
            // Update jobsData untuk tab saat ini
            const tempJobsData = {
                'semua': filteredJobs,
                'live': filteredJobs.filter(job => job.status === 'publish'),
                'perlu': filteredJobs.filter(job => job.status === 'ditinjau'),
                'sedang': filteredJobs.filter(job => job.status === 'ditolak')
            };
            
            // Render dengan data yang sudah difilter
            renderFilteredJobs(tempJobsData[currentTab] || []);
        }

        // Render jobs dengan data yang sudah difilter
        function renderFilteredJobs(jobs) {
            const jobsTableBody = document.getElementById('jobsTableBody');

            if (!jobsTableBody) {
                console.error('Element jobsTableBody tidak ditemukan');
                return;
            }

            let html = '';

            if (jobs.length === 0) {
                html = `
                <tr>
                    <td colspan="5" class="empty-state">
                        <svg width="48" height="48" fill="none" stroke="#d1d5db" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <p>Belum ada lowongan di kategori ini</p>
                    </td>
                </tr>
            `;
            } else {
                jobs.forEach(job => {
                    // Menampilkan jumlah pelamar atau "Belum dipublikasi"
                    const performanceText = job.status === 'publish' ? job.applicants : 'Belum dipublikasi';

                    html += `
                    <tr class="job-row">
                        <td>
                            <div class="job-info-cell">
                                <div class="job-details">
                                    <h3>${job.title}</h3>
                                    <p class="job-company">${job.company}</p>
                                    <div class="job-meta">
                                        <span>${job.posted}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="salary-cell">${job.salary}</div>
                        </td>
                        <td>
                            <div class="performance-cell">
                                <span class="performance-text">${performanceText}</span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge ${job.statusClass}">${job.statusLabel}</span>
                        </td>
                        <td>
                            <div class="analysis-cell">
                                <a href="#" class="analysis-link">Analisis</a>
                            </div>
                        </td>
                    </tr>
                `;
                });
            }

            jobsTableBody.innerHTML = html;
        }

        // Render jobs normal (tanpa filter)
        function renderJobs() {
            const jobs = jobsData[currentTab] || [];
            renderFilteredJobs(jobs);
        }

        // Fungsi untuk toggle view (jika ingin implementasi card view nanti)
        function toggleView() {
            // Untuk sementara tetap table view
            alert('Fitur tampilan card akan segera hadir!');
        }

        // Initial render
        document.addEventListener('DOMContentLoaded', function() {
            renderJobs();
            
            // Initialize sidebar state
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