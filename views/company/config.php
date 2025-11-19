<?php
// config.php
$base_url = '../../';

// Include supabase.php dengan path yang benar
$supabase_path = __DIR__ . '/../../function/supabase.php';
if (file_exists($supabase_path)) {
    include $supabase_path;
} else {
    // Fallback ke data statis jika supabase tidak ditemukan
    $jobsData = [
        'semua' => [
            [
                'id' => 1,
                'title' => 'UI/UX Designer',
                'company' => 'PT Karirku Nusantara',
                'salary' => 'Rp 5.000.000 - Rp 8.000.000',
                'posted' => 'Diposting 10 hari lalu (Jul 14 at 9:41)',
                'applicants' => 'Berjumlah 50 Pelamar',
                'status' => 'publish',
                'statusClass' => 'status-publish',
                'statusLabel' => 'Publish'
            ],
            [
                'id' => 2,
                'title' => 'Frontend Developer',
                'company' => 'PT Teknologi Maju',
                'salary' => 'Rp 7.000.000 - Rp 12.000.000',
                'posted' => 'Diposting 5 hari lalu',
                'applicants' => 'Berjumlah 35 Pelamar',
                'status' => 'ditinjau',
                'statusClass' => 'status-review',
                'statusLabel' => 'Ditinjau'
            ],
            [
                'id' => 3,
                'title' => 'Backend Developer',
                'company' => 'PT Digital Indonesia',
                'salary' => 'Rp 8.000.000 - Rp 15.000.000',
                'posted' => 'Diposting 2 hari lalu',
                'applicants' => 'Berjumlah 0 Pelamar',
                'status' => 'ditolak',
                'statusClass' => 'status-rejected',
                'statusLabel' => 'Ditolak'
            ]
        ],
        'live' => [],
        'perlu' => [],
        'sedang' => []
    ];
    $jobsDataJson = json_encode($jobsData);
}

// Jika supabase berhasil diinclude, ambil data dari database
if (isset($supabase_url) && function_exists('supabaseQuery')) {
    // Ambil data lowongan dari database
    function getJobsDataFromDatabase() {
        // Query untuk mengambil data lowongan dengan join perusahaan
        $result = supabaseQuery('lowongan', [
            'select' => '*, perusahaan(nama_perusahaan)',
            'order' => 'dibuat_pada.desc'
        ]);

        if (!$result['success']) {
            error_log("Error fetching jobs: " . print_r($result, true));
            return [
                'semua' => [],
                'live' => [],
                'perlu' => [],
                'sedang' => []
            ];
        }

        $allJobs = [];
        
        foreach ($result['data'] as $job) {
            // Hitung jarak hari
            $createdDate = new DateTime($job['dibuat_pada']);
            $currentDate = new DateTime();
            $interval = $currentDate->diff($createdDate);
            $daysAgo = $interval->days;
            
            // Format tanggal posting
            $formattedDate = date('M j \a\t g:i', strtotime($job['dibuat_pada']));
            $postedText = "Diposting {$daysAgo} hari lalu ({$formattedDate})";
            
            // Format gaji
            $salary = $job['gaji_range'] ?: 'Gaji tidak ditampilkan';
            
            // Tentukan status class dan label berdasarkan status di database
            $status = $job['status'] ?? 'ditinjau';
            $statusClass = '';
            $statusLabel = '';
            
            switch ($status) {
                case 'publish':
                    $statusClass = 'status-publish';
                    $statusLabel = 'Publish';
                    break;
                case 'ditinjau':
                    $statusClass = 'status-review';
                    $statusLabel = 'Ditinjau';
                    break;
                case 'ditolak':
                    $statusClass = 'status-rejected';
                    $statusLabel = 'Ditolak';
                    break;
                default:
                    $statusClass = 'status-review';
                    $statusLabel = 'Ditinjau';
            }

            // Untuk performa, tampilkan jumlah pelamar untuk status publish, lainnya "Belum dipublikasi"
            $applicants = ($status === 'publish') ? 'Berjumlah 0 Pelamar' : 'Belum dipublikasi';

            $jobData = [
                'id' => $job['id_lowongan'],
                'title' => $job['judul'],
                'company' => $job['perusahaan']['nama_perusahaan'] ?? 'Perusahaan',
                'salary' => $salary,
                'posted' => $postedText,
                'applicants' => $applicants,
                'status' => $status,
                'statusClass' => $statusClass,
                'statusLabel' => $statusLabel,
                'location' => $job['lokasi'] ?? '',
                'type' => $job['tipe_pekerjaan'] ?? ''
            ];

            $allJobs[] = $jobData;
        }

        return $allJobs;
    }

    // Ambil semua data lowongan dari database
    $allJobs = getJobsDataFromDatabase();
    
    // Siapkan data untuk JSON (semua data, filtering dilakukan di JavaScript)
    $jobsDataForJson = [
        'semua' => $allJobs,
        'live' => array_values(array_filter($allJobs, function($job) { 
            return $job['status'] === 'publish'; 
        })),
        'perlu' => array_values(array_filter($allJobs, function($job) { 
            return $job['status'] === 'ditinjau'; 
        })),
        'sedang' => array_values(array_filter($allJobs, function($job) { 
            return $job['status'] === 'ditolak'; 
        }))
    ];

    $jobsDataJson = json_encode($jobsDataForJson);
    
    // Data untuk tampilan count di PHP (untuk ditampilkan di tab)
    $jobsData = [
        'semua' => $allJobs,
        'live' => array_filter($allJobs, function($job) { 
            return $job['status'] === 'publish'; 
        }),
        'perlu' => array_filter($allJobs, function($job) { 
            return $job['status'] === 'ditinjau'; 
        }),
        'sedang' => array_filter($allJobs, function($job) { 
            return $job['status'] === 'ditolak'; 
        })
    ];
}
?>