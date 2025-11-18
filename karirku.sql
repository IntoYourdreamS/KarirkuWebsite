--
-- Database: karirku
--

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE chat (
  id_chat SERIAL PRIMARY KEY,
  id_pengirim INTEGER,
  id_penerima INTEGER,
  pesan TEXT NOT NULL,
  dikirim_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  sudah_dibaca BOOLEAN DEFAULT FALSE
);

-- --------------------------------------------------------

--
-- Table structure for table `cv`
--

CREATE TABLE cv (
  id_cv SERIAL PRIMARY KEY,
  id_pencaker INTEGER NOT NULL,
  nama_file VARCHAR(255) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  parsed_text TEXT,
  matching_keywords JSONB,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `favorit_lowongan`
--

CREATE TABLE favorit_lowongan (
  id_favorit SERIAL PRIMARY KEY,
  id_pencaker INTEGER,
  id_lowongan INTEGER,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `lamaran`
--

CREATE TABLE lamaran (
  id_lamaran SERIAL PRIMARY KEY,
  id_lowongan INTEGER,
  id_pencaker INTEGER,
  cv_url VARCHAR(255),
  cover_letter TEXT,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `lowongan`
--

CREATE TABLE lowongan (
  id_lowongan SERIAL PRIMARY KEY,
  id_perusahaan INTEGER,
  judul VARCHAR(150) NOT NULL,
  deskripsi TEXT NOT NULL,
  kualifikasi TEXT,
  lokasi VARCHAR(150),
  tipe_pekerjaan VARCHAR(20) CHECK (tipe_pekerjaan IN ('full-time','part-time','contract','internship')),
  gaji_range VARCHAR(100),
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  batas_tanggal DATE,
  status VARCHAR(10) DEFAULT 'open' CHECK (status IN ('open','closed')),
  kategori VARCHAR(100),
  mode_kerja VARCHAR(20) DEFAULT 'On-site' CHECK (mode_kerja IN ('On-site','Hybrid','Remote','Shift','Lapangan'))
);

--
-- Dumping data for table `lowongan`
--

INSERT INTO lowongan (id_perusahaan, judul, deskripsi, kualifikasi, lokasi, tipe_pekerjaan, gaji_range, batas_tanggal, status, kategori, mode_kerja) VALUES
(1, 'Petugas Penagihan Lapangan', 'Menagih pembayaran kredit dari nasabah dan melakukan pencatatan hasil kunjungan.', 'Memiliki SIM C, mampu berkomunikasi dengan baik.', 'Bendebesah City', 'full-time', '3-4 juta', '2025-12-31', 'open', 'Keuangan', 'On-site'),
(2, 'Customer Service Perbankan', 'Melayani kebutuhan nasabah secara online maupun langsung di kantor cabang.', 'Minimal D3, ramah, dan komunikatif.', 'Jember', 'contract', '4-5 juta', '2025-12-15', 'open', 'Perbankan', 'Remote'),
(3, 'Admin Data Keuangan', 'Melakukan input data transaksi dan pembuatan laporan keuangan bulanan.', 'Menguasai Excel, teliti dan disiplin.', 'Surabaya', 'part-time', '2-3 juta', '2025-12-20', 'open', 'Administrasi', 'On-site'),
(4, 'Frontend Developer', 'Membangun dan mengembangkan antarmuka aplikasi Android menggunakan React atau Kotlin.', 'Menguasai HTML, CSS, JS, dan framework frontend.', 'Malang', 'full-time', '7-9 juta', '2025-12-31', 'open', 'Teknologi', 'Hybrid'),
(5, 'Quality Control Pabrik', 'Melakukan pengecekan kualitas produk sebelum dikirim ke pelanggan.', 'Teliti dan paham standar kualitas produksi.', 'Sidoarjo', 'contract', '4-6 juta', '2025-11-30', 'open', 'Produksi', 'Shift'),
(6, 'UI/UX Designer', 'Merancang tampilan antarmuka aplikasi dan memastikan pengalaman pengguna yang optimal.', 'Menguasai Figma dan Adobe XD.', 'Jakarta', 'full-time', '6-8 juta', '2025-12-25', 'open', 'Desain', 'Remote'),
(7, 'Data Analyst', 'Menganalisis data dan memberikan insight untuk strategi bisnis.', 'Menguasai SQL dan tools analitik.', 'Bandung', 'full-time', '8-10 juta', '2025-12-31', 'open', 'Teknologi', 'Hybrid'),
(8, 'Ahli Agronomi', 'Menganalisis kualitas tanah dan membantu peningkatan hasil panen.', 'Lulusan pertanian, siap kerja lapangan.', 'Yogyakarta', 'contract', '5-7 juta', '2025-12-10', 'open', 'Pertanian', 'Lapangan'),
(9, 'Teknisi Panel Surya', 'Memasang dan memelihara sistem panel surya di lokasi proyek.', 'Punya pengalaman teknis dan paham sistem listrik dasar.', 'Semarang', 'full-time', '4-6 juta', '2025-12-05', 'open', 'Teknik', 'On-site'),
(10, 'Instruktur Komputer', 'Mengajar pelatihan dasar komputer dan pemrograman.', 'Menguasai MS Office dan logika pemrograman dasar.', 'Surakarta', 'part-time', '3-4 juta', '2025-12-20', 'open', 'Pendidikan', 'On-site');

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE notifikasi (
  id_notifikasi SERIAL PRIMARY KEY,
  id_pengguna INTEGER,
  pesan TEXT NOT NULL,
  tipe VARCHAR(10) CHECK (tipe IN ('lamaran','pesan','system')),
  sudah_dibaca BOOLEAN DEFAULT FALSE,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `pencaker`
--

CREATE TABLE pencaker (
  id_pencaker SERIAL PRIMARY KEY,
  id_pengguna INTEGER UNIQUE,
  tanggal_lahir DATE,
  gender VARCHAR(10) CHECK (gender IN ('male','female','other')),
  alamat TEXT,
  pengalaman_tahun INTEGER,
  cv_url VARCHAR(255),
  cv_parsed JSONB,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `pencaker_skill`
--

CREATE TABLE pencaker_skill (
  id_pencaker INTEGER NOT NULL,
  id_skill INTEGER NOT NULL,
  level VARCHAR(20) DEFAULT 'beginner' CHECK (level IN ('beginner','intermediate','expert')),
  PRIMARY KEY (id_pencaker, id_skill)
);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE pengguna (
  id_pengguna SERIAL PRIMARY KEY,
  nama_lengkap VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  no_hp VARCHAR(20),
  role VARCHAR(10) CHECK (role IN ('pencaker','perusahaan','admin')),
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--
-- Dumping data for table `pengguna`
--

INSERT INTO pengguna (nama_lengkap, email, password_hash, no_hp, role) VALUES
('nando', 'akunalif774@gmail.com', 'nando12345', '0892653782', 'pencaker'),
('nando', 'nandofer774@gmail.com', 'nando12', '089876676565', 'pencaker'),
('nando', 'teknisimetic@gmail.com', 'nando12', '9338838', 'pencaker'),
('sobsob', 'sobsob@gmail.com', 'sobsob12', '9373938', 'pencaker'),
('putro', 'putro@gmail.com', 'putro12', '9373944', 'pencaker');

-- --------------------------------------------------------

--
-- Table structure for table `perusahaan`
--

CREATE TABLE perusahaan (
  id_perusahaan SERIAL PRIMARY KEY,
  id_pengguna INTEGER UNIQUE,
  nama_perusahaan VARCHAR(150) NOT NULL,
  deskripsi TEXT,
  website VARCHAR(150),
  lokasi VARCHAR(150),
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--
-- Dumping data for table `perusahaan`
--

INSERT INTO perusahaan (nama_perusahaan, deskripsi, website, lokasi) VALUES
('PT. Mekar Sentosa', 'Perusahaan pembiayaan mikro untuk masyarakat kecil.', 'https://mekarsentosa.id', 'Bendebesah City'),
('PT. Sejahtera Abadi', 'Perusahaan perbankan digital terpercaya.', 'https://sejahteraabadi.com', 'Jember'),
('CV. Maju Bersama', 'Jasa administrasi dan akuntansi bisnis kecil.', NULL, 'Surabaya'),
('PT. Mitra Digital', 'Software house yang fokus pada pengembangan aplikasi mobile.', 'https://mitradigital.tech', 'Malang'),
('PT. Cahaya Abadi', 'Perusahaan manufaktur produk plastik berkualitas tinggi.', NULL, 'Sidoarjo'),
('PT. Arta Mandiri', 'Agensi desain grafis dan digital marketing.', NULL, 'Jakarta'),
('CV. Data Insight', 'Konsultan data dan analitik bisnis.', 'https://datainsight.co.id', 'Bandung'),
('PT. Hijau Lestari', 'Perusahaan pertanian berkelanjutan.', NULL, 'Yogyakarta'),
('CV. Sumber Energi', 'Penyedia solensi energi terbarukan.', 'https://sumberenergi.id', 'Semarang'),
('PT. Global Edukasi', 'Lembaga pendidikan dan pelatihan teknologi.', 'https://globaledukasi.id', 'Surakarta');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_notifikasi`
--

CREATE TABLE riwayat_notifikasi (
  id_riwayat_notifikasi SERIAL PRIMARY KEY,
  id_pengguna INTEGER,
  pesan TEXT NOT NULL,
  tipe VARCHAR(10) CHECK (tipe IN ('lamaran','pesan','system')),
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pencarian`
--

CREATE TABLE riwayat_pencarian (
  id_pencarian SERIAL PRIMARY KEY,
  id_pencaker INTEGER,
  keyword VARCHAR(150),
  lokasi VARCHAR(150),
  tipe_pekerjaan VARCHAR(20) CHECK (tipe_pekerjaan IN ('full-time','part-time','contract','internship')),
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_status_lamaran`
--

CREATE TABLE riwayat_status_lamaran (
  id_status SERIAL PRIMARY KEY,
  id_lamaran INTEGER,
  status VARCHAR(20) CHECK (status IN ('applied','rejected')),
  catatan TEXT,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `skill`
--

CREATE TABLE skill (
  id_skill SERIAL PRIMARY KEY,
  nama_skill VARCHAR(100) UNIQUE
);

--
-- Indexes for dumped tables
--

-- Indexes for table `chat`
CREATE INDEX fk_chat_pengirim ON chat(id_pengirim);
CREATE INDEX fk_chat_penerima ON chat(id_penerima);

-- Indexes for table `cv`
CREATE INDEX fk_cv_pencaker ON cv(id_pencaker);

-- Indexes for table `favorit_lowongan`
CREATE INDEX fk_favorit_pencaker ON favorit_lowongan(id_pencaker);
CREATE INDEX fk_favorit_lowongan ON favorit_lowongan(id_lowongan);

-- Indexes for table `lamaran`
CREATE INDEX fk_lamaran_lowongan ON lamaran(id_lowongan);
CREATE INDEX fk_lamaran_pencaker ON lamaran(id_pencaker);

-- Indexes for table `lowongan`
CREATE INDEX fk_lowongan_perusahaan ON lowongan(id_perusahaan);

-- Indexes for table `notifikasi`
CREATE INDEX fk_notifikasi_pengguna ON notifikasi(id_pengguna);

-- Indexes for table `pencaker_skill`
CREATE INDEX fk_ps_skill ON pencaker_skill(id_skill);

-- Indexes for table `riwayat_notifikasi`
CREATE INDEX fk_riwayat_notifikasi_pengguna ON riwayat_notifikasi(id_pengguna);

-- Indexes for table `riwayat_pencarian`
CREATE INDEX fk_riwayat_pencarian_pencaker ON riwayat_pencarian(id_pencaker);

-- Indexes for table `riwayat_status_lamaran`
CREATE INDEX fk_status_lamaran ON riwayat_status_lamaran(id_lamaran);

--
-- Constraints for dumped tables
--

-- Constraints for table `chat`
ALTER TABLE chat
  ADD CONSTRAINT fk_chat_pengirim FOREIGN KEY (id_pengirim) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE,
  ADD CONSTRAINT fk_chat_penerima FOREIGN KEY (id_penerima) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE;

-- Constraints for table `cv`
ALTER TABLE cv
  ADD CONSTRAINT fk_cv_pencaker FOREIGN KEY (id_pencaker) REFERENCES pencaker(id_pencaker) ON DELETE CASCADE;

-- Constraints for table `favorit_lowongan`
ALTER TABLE favorit_lowongan
  ADD CONSTRAINT fk_favorit_lowongan FOREIGN KEY (id_lowongan) REFERENCES lowongan(id_lowongan) ON DELETE CASCADE,
  ADD CONSTRAINT fk_favorit_pencaker FOREIGN KEY (id_pencaker) REFERENCES pencaker(id_pencaker) ON DELETE CASCADE;

-- Constraints for table `lamaran`
ALTER TABLE lamaran
  ADD CONSTRAINT fk_lamaran_lowongan FOREIGN KEY (id_lowongan) REFERENCES lowongan(id_lowongan) ON DELETE CASCADE,
  ADD CONSTRAINT fk_lamaran_pencaker FOREIGN KEY (id_pencaker) REFERENCES pencaker(id_pencaker) ON DELETE CASCADE;

-- Constraints for table `lowongan`
ALTER TABLE lowongan
  ADD CONSTRAINT fk_lowongan_perusahaan FOREIGN KEY (id_perusahaan) REFERENCES perusahaan(id_perusahaan) ON DELETE CASCADE;

-- Constraints for table `notifikasi`
ALTER TABLE notifikasi
  ADD CONSTRAINT fk_notifikasi_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE;

-- Constraints for table `pencaker`
ALTER TABLE pencaker
  ADD CONSTRAINT fk_pencaker_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE;

-- Constraints for table `pencaker_skill`
ALTER TABLE pencaker_skill
  ADD CONSTRAINT fk_ps_pencaker FOREIGN KEY (id_pencaker) REFERENCES pencaker(id_pencaker) ON DELETE CASCADE,
  ADD CONSTRAINT fk_ps_skill FOREIGN KEY (id_skill) REFERENCES skill(id_skill) ON DELETE CASCADE;

-- Constraints for table `perusahaan`
ALTER TABLE perusahaan
  ADD CONSTRAINT fk_perusahaan_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE;

-- Constraints for table `riwayat_notifikasi`
ALTER TABLE riwayat_notifikasi
  ADD CONSTRAINT fk_riwayat_notifikasi_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE;

-- Constraints for table `riwayat_pencarian`
ALTER TABLE riwayat_pencarian
  ADD CONSTRAINT fk_riwayat_pencarian_pencaker FOREIGN KEY (id_pencaker) REFERENCES pencaker(id_pencaker) ON DELETE CASCADE;

-- Constraints for table `riwayat_status_lamaran`
ALTER TABLE riwayat_status_lamaran
  ADD CONSTRAINT fk_status_lamaran FOREIGN KEY (id_lamaran) REFERENCES lamaran(id_lamaran) ON DELETE CASCADE;

-- Trigger untuk update timestamp (mengganti ON UPDATE CURRENT_TIMESTAMP)
CREATE OR REPLACE FUNCTION update_diperbarui_pada()
RETURNS TRIGGER AS $$
BEGIN
    NEW.diperbarui_pada = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_diperbarui_pada
    BEFORE UPDATE ON pengguna
    FOR EACH ROW
    EXECUTE FUNCTION update_diperbarui_pada();