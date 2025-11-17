<?php
session_start();
require_once __DIR__ . '/../function/supabase.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$tanggal_lahir = !empty(trim($_POST['tanggal_lahir'] ?? '')) ? $_POST['tanggal_lahir'] : null;
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);
$pencaker = getPencakerByUserId($user_id);

if (!$pencaker) {
    header('Location: create_profile.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email_pencaker = trim($_POST['email_pencaker'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $gender = $_POST['gender'] ?? '';
    $pengalaman_tahun = $_POST['pengalaman_tahun'] ?? 0;

    // Data yang akan diupdate
    $updateData = [
        'nama_lengkap' => $nama_lengkap,
        'email_pencaker' => $email_pencaker,
        'no_hp' => $no_hp,
        'alamat' => $alamat,
        'tanggal_lahir' => $tanggal_lahir,
        'gender' => $gender,
        'pengalaman_tahun' => (int)$pengalaman_tahun
    ];

    // Handle upload foto profil
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_profil'];

        // Validasi file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            $error = 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.';
        } elseif ($file['size'] > $maxSize) {
            $error = 'Ukuran file terlalu besar. Maksimal 5MB.';
        } else {
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
            $filePath = $user_id . '/' . $filename;

            // Hapus foto lama jika ada
            if (!empty($pencaker['foto_profil_path'])) {
                $deleteResult = supabaseStorageDelete('profile-pictures', $pencaker['foto_profil_path']);
                if (!$deleteResult['success']) {
                    error_log("Gagal menghapus foto lama: " . print_r($deleteResult, true));
                }
            }

            // Upload foto baru
            $uploadResult = supabaseStorageUpload('profile-pictures', $filePath, $file);

            if ($uploadResult['success']) {
                $publicUrl = getStoragePublicUrl('profile-pictures', $filePath);
                $updateData['foto_profil_url'] = $publicUrl;
                $updateData['foto_profil_path'] = $filePath;
            } else {
                $error = 'Gagal mengupload foto profil. Silakan coba lagi.';

                // Debug info
                error_log("Upload error details: " . print_r($uploadResult, true));

                // Tampilkan error yang lebih spesifik
                if (isset($uploadResult['error']) && !empty($uploadResult['error'])) {
                    $error .= ' Error: ' . $uploadResult['error'];
                }
                if (isset($uploadResult['response'])) {
                    $responseData = json_decode($uploadResult['response'], true);
                    if (isset($responseData['error'])) {
                        $error .= ' Server: ' . $responseData['error'];
                    }
                }
            }
        }
    }

    // Update data ke database jika tidak ada error
    if (empty($error)) {
        $result = updatePencakerProfile($pencaker['id_pencaker'], $updateData);

        if ($result['success']) {
            $message = 'Profil berhasil diperbarui!';
            // Refresh data pencaker
            $pencaker = getPencakerByUserId($user_id);

            // Update session
            $_SESSION['nama_lengkap'] = $nama_lengkap;
        } else {
            $error = 'Gagal memperbarui profil. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Karirku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .profile-edit-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-image-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            border: 4px solid #f0f0f0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
            margin-bottom: 30px;
        }

        .upload-btn {
            border: 2px dashed #003399;
            color: #003399;
            background-color: #f8f9ff;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .upload-btn:hover {
            background-color: #003399;
            color: white;
        }

        .upload-btn-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .form-label {
            font-weight: 600;
            color: #2b3940;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #003399;
            box-shadow: 0 0 0 0.2rem rgba(0, 51, 153, 0.1);
        }

        .btn-primary {
            background-color: #003399;
            border: none;
            padding: 12px 40px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #002266;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 153, 0.3);
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 12px 40px;
            border-radius: 10px;
            font-weight: 600;
        }

        .page-title {
            color: #003399;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="profile-edit-container">
            <h2 class="text-center page-title">Edit Profil</h2>
            <p class="text-center page-subtitle">Perbarui informasi profil Anda</p>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <!-- Foto Profil -->
                <div class="text-center mb-4">
                    <img src="<?php echo !empty($pencaker['foto_profil_url']) ? htmlspecialchars($pencaker['foto_profil_url']) : '../assets/img/default-avatar.png'; ?>"
                        alt="Profile"
                        class="profile-image-preview"
                        id="imagePreview">
                    <div class="upload-btn-wrapper">
                        <button class="upload-btn" type="button">
                            <i class="bi bi-camera-fill me-2"></i> Pilih Foto Profil
                        </button>
                        <input type="file" name="foto_profil" accept="image/*" onchange="previewImage(event)">
                    </div>
                    <small class="text-muted">Format: JPG, PNG, GIF, WEBP. Maksimal: 5MB</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_lengkap"
                            value="<?php echo htmlspecialchars($pencaker['nama_lengkap'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email_pencaker"
                            value="<?php echo htmlspecialchars($pencaker['email_pencaker'] ?? $user['email']); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_hp"
                            value="<?php echo htmlspecialchars($pencaker['no_hp'] ?? ''); ?>"
                            placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir"
                            value="<?php echo htmlspecialchars($pencaker['tanggal_lahir'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select class="form-select" name="gender">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male" <?php echo ($pencaker['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="female" <?php echo ($pencaker['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Perempuan</option>
                            <option value="other" <?php echo ($pencaker['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pengalaman Kerja (Tahun)</label>
                        <input type="number" class="form-control" name="pengalaman_tahun"
                            min="0" max="50"
                            value="<?php echo htmlspecialchars($pencaker['pengalaman_tahun'] ?? '0'); ?>">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3"
                            placeholder="Masukkan alamat lengkap Anda"><?php echo htmlspecialchars($pencaker['alamat'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end mt-4">
                    <a href="profile.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                // Validasi ukuran file
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 5MB');
                    event.target.value = '';
                    return;
                }

                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung! Gunakan JPG, PNG, GIF, atau WEBP');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>