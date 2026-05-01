-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Bulan Mei 2026 pada 06.34
-- Versi server: 10.4.6-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `psychotest`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `exams`
--

INSERT INTO `exams` (`id`, `name`, `type`, `duration_minutes`, `description`, `created_at`, `updated_at`) VALUES
(1, 'DISC Test', 'disc', 15, 'DISC Soal Attitude Test', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(2, 'VAK Test', 'vak', 15, 'VAK Lembar Soal & Jawaban Working Style Test', '2026-05-01 04:33:56', '2026-05-01 04:33:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `exam_sessions`
--

CREATE TABLE `exam_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `exam_user`
--

CREATE TABLE `exam_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `exam_user`
--

INSERT INTO `exam_user` (`id`, `user_id`, `exam_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-05-01 04:34:02', '2026-05-01 04:34:02'),
(2, 1, 2, '2026-05-01 04:34:05', '2026-05-01 04:34:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2026_04_28_160046_create_exams_table', 1),
(5, '2026_04_28_160051_create_exam_sessions_table', 1),
(6, '2026_04_28_160055_create_user_answers_table', 1),
(7, '2026_04_28_160058_create_proctoring_logs_table', 1),
(8, '2026_04_28_165437_create_exam_user_table', 1),
(9, '2026_04_28_174046_add_role_to_users_table', 1),
(10, '2026_04_29_050035_create_questions_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `proctoring_logs`
--

CREATE TABLE `proctoring_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_session_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `number` int(11) NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ;

--
-- Dumping data untuk tabel `questions`
--

INSERT INTO `questions` (`id`, `exam_id`, `number`, `question_text`, `options`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Box 1', '{\"A\":\"Lembut, ramah\",\"B\":\"Membujuk, meyakinkan\",\"C\":\"Sederhana, mudah menerima, rendah hati\",\"D\":\"Asli, berdaya cipta, individualis\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(2, 1, 2, 'Box 2', '{\"A\":\"Menarik, mempesona, menarik bagi orang lain\",\"B\":\"Dapat bekerja sama, mudah menyetujui\",\"C\":\"Keras kepala, tidak mudah menyerah\",\"D\":\"Manis, memuaskan\\/menyenangkan\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(3, 1, 3, 'Box 3', '{\"A\":\"Mau dipimpin, cenderung mengikuti\\/pengikut\",\"B\":\"Tangguh, berani\",\"C\":\"Loyal, setia, mengabdi\",\"D\":\"Mempesona, menyenangkan\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(4, 1, 4, 'Box 4', '{\"A\":\"Bepandangan terbuka, mau menerima\",\"B\":\"Berani, suka menolong\",\"C\":\"Pemelihara, berkemauan keras\",\"D\":\"Periang, selalu bergembira\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(5, 1, 5, 'Box 5', '{\"A\":\"Periang, suka bergurau\",\"B\":\"Teliti, tepat\",\"C\":\"Kasar, berani, kurang sopan, tidak mudah malu\",\"D\":\"Tenang, emosi yang terkendali, tidak mudah heboh\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(6, 1, 6, 'Box 6', '{\"A\":\"Kompetitif, selalu ingin berhasil\",\"B\":\"Timbang rasa, peduli, bijaksana\",\"C\":\"Terbuka, ramah, suka bersenang-senang\",\"D\":\"Harmonis, mudah menyetujui\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(7, 1, 7, 'Box 7', '{\"A\":\"Rewel, cerewet, sulit untuk dipuaskan hatinya\",\"B\":\"Taat, melakukan apa yang diperintahkan, patuh\",\"C\":\"Tidak mudah mundur, fokus akan satu hal, ulet\",\"D\":\"Suka melucu, lincah, periang\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(8, 1, 8, 'Box 8', '{\"A\":\"Berani, tidak gentar, tangguh\",\"B\":\"Membangkitkan semangat, memotivasi\",\"C\":\"Patuh, berhasil, menyerah\",\"D\":\"Takut-takut, malu, pendiam\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(9, 1, 9, 'Box 9', '{\"A\":\"Suka bergaul dan bersosialisasi\",\"B\":\"Sabar, penuh keyakinan, bersikap toleransi\",\"C\":\"Percaya diri, mandiri\",\"D\":\"Berwatak halus\\/lembut, pendiam, suka menyendiri\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(10, 1, 10, 'Box 10', '{\"A\":\"Menyukai hal-hal baru, suka tantangan\",\"B\":\"Terbuka dan mau menerima ide-ide baru dan saran\",\"C\":\"Ramah, hangat, bersahabat\",\"D\":\"Moderat, menghindari hal-hal yang ekstrim atau aneh\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(11, 1, 11, 'Box 11', '{\"A\":\"Banyak bicara, cerewet\",\"B\":\"Terkendali, mandiri\",\"C\":\"Melakukan hal-hal yang sudah biasa, tidak berlebihan\",\"D\":\"Tegas, cepat dalalm membuat keputusan\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(12, 1, 12, 'Box 12', '{\"A\":\"Berbudi bahasa halus, tingkah laku yang halus\",\"B\":\"Berani, suka mengambil resiko\",\"C\":\"Diplomatik, bijaksana\",\"D\":\"Mudah puas atau senang\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(13, 1, 13, 'Box 13', '{\"A\":\"Agresif, suka tantangan, penuh inisiatif\",\"B\":\"Menyukai hiburan, ramah, suka pesta\\/acara kumpul\",\"C\":\"Pengikut, mudah diguna-dayakan oleh orang lain\",\"D\":\"Gelisah, khawatir\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(14, 1, 14, 'Box 14', '{\"A\":\"Berhati-hati\",\"B\":\"Fokus pada satu hal tertentu, tidak mudah goyah\",\"C\":\"Meyakinkan\",\"D\":\"Baik hati, menyenangkan\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(15, 1, 15, 'Box 15', '{\"A\":\"Rela berkorban, mengikuti arus\",\"B\":\"Antusias, selalu ingin tahu\",\"C\":\"Mudah menyetujui\",\"D\":\"Lincah, antusias\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(16, 1, 16, 'Box 16', '{\"A\":\"Percaya diri, yakin pada diri sendiri\",\"B\":\"Simpatik, orang yang pengertian\",\"C\":\"Toleran\",\"D\":\"Tegas, agresif\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(17, 1, 17, 'Box 17', '{\"A\":\"Disiplin, terkendali\",\"B\":\"Dermawan, suka berbagi\",\"C\":\"Suka berekspresi\",\"D\":\"Gigih, tidak mudah menyerah\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(18, 1, 18, 'Box 18', '{\"A\":\"Terpuji, dapat dikagumi, patut dipuji\",\"B\":\"Ramah, senang menolong\",\"C\":\"Mudah menyerah\\/menerima pendapat yang lain\",\"D\":\"Memiliki karakter kuat, tangguh\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(19, 1, 19, 'Box 19', '{\"A\":\"Menunjukkan rasa hormat\",\"B\":\"Pelopor, perintis, giat, mau berusaha\",\"C\":\"Optimis, pandangan positif\",\"D\":\"Selalu siap membantu\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(20, 1, 20, 'Box 20', '{\"A\":\"Dapat berargumentasi\",\"B\":\"Fleksibel, mudah beradaptasi\",\"C\":\"Naif, acuh tak acuh, tidak perhatian\",\"D\":\"Riang, tiada yang dipikirkan sama sekali\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(21, 1, 21, 'Box 21', '{\"A\":\"Dapat dipercaya, percaya kepada orang lain\",\"B\":\"Mudah puas, selalu merasa cukup\",\"C\":\"Selalu positif, tidak diragukan\",\"D\":\"Tenang, pendiam\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(22, 1, 22, 'Box 22', '{\"A\":\"Mudah bergaul, suka berteman\",\"B\":\"Berbudaya, memiliki banyak pengetahuan\",\"C\":\"Bersemangat, giat\",\"D\":\"Toleransi, tidak tegas\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(23, 1, 23, 'Box 23', '{\"A\":\"Menyenangkan, ramah\",\"B\":\"Teliti, akurat\",\"C\":\"Terus terang, bicara bebas\",\"D\":\"Terkendali, emosi terkendali\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(24, 1, 24, 'Box 24', '{\"A\":\"Resah, tidak bisa santai\",\"B\":\"Baik hati, ramah\",\"C\":\"Populer, disukai banyak orang\",\"D\":\"Rapi, teratur\"}', '2026-05-01 04:33:23', '2026-05-01 04:33:23'),
(25, 2, 1, '1. Ketika mengoperasikan peralatan baru, saya cenderung untuk:', '{\"A\":\"membaca instruksi terlebih dahulu\",\"B\":\"mendengarkan penjelasan dari orang lain yang sudah menggunakan\",\"C\":\"mengutak-atik sendiri, saya bisa tahu kemudian seiring saya menggunakannya\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(26, 2, 2, '2. Ketika saya membutuhkan arahan dalam perjalanan, saya biasanya:', '{\"A\":\"bertanya kepada orang lain\",\"B\":\"melihat peta\",\"C\":\"mengikuti kata hati dan mungkin menggunakan kompas\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(27, 2, 3, '3. Ketika saya memasak menu makanan baru, saya suka:', '{\"A\":\"meminta teman untuk menjelaskan bagaimana cara memasak menu tersebut\",\"B\":\"mengikuti naluri saya, coba-coba sendiri\",\"C\":\"mengikuti resep yang ada\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(28, 2, 4, '4. Jika saya mengajari seseorang sesuatu hal yang baru, saya cenderung untuk:', '{\"A\":\"menuliskan instruksi untuk mereka\",\"B\":\"memberikan penjelasan secara lisan\",\"C\":\"mendemonstrasikan terlebih dahulu, lalu membiarkan mereka mencoba sendiri\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(29, 2, 5, '5. Saya cenderung untuk mengatakan:', '{\"A\":\"perhatikan bagaimana saya melakukannya\",\"B\":\"kamu coba sendiri dahulu\",\"C\":\"dengarkan penjelasan saya\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(30, 2, 6, '6. Saya sangat menikmati waktu luang saya dengan:', '{\"A\":\"Berolahraga atau bermain puzzle\",\"B\":\"pergi ke bioskop dan menonton film\",\"C\":\"mendengarkan musik dan berbincang dengan teman-teman\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(31, 2, 7, '7. Ketika saya pergi berbelanja pakaian, saya cenderung untuk:', '{\"A\":\"membayangkan bagaimana pakaian tersebut akan terlihat ketika saya pakai\",\"B\":\"menanyakan pendapat kepada pelayan toko\",\"C\":\"mencoba pakaian tersebut dan melihat apakah cocok untuk saya\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(32, 2, 8, '8. Ketika saya memilih untuk berlibur, biasanya saya akan:', '{\"A\":\"membaca banyak brosur\",\"B\":\"mendengarkan rekomendasi dari teman\",\"C\":\"membayangkan bagaimana rasanya berada di tempat tersebut\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(33, 2, 9, '9. Jika saya membeli mobil baru, saya akan:', '{\"A\":\"mendiskusikan apa yang saya butuhkan dengan teman-teman\",\"B\":\"membaca review dari koran dan majalah\",\"C\":\"menguji coba berbagai tipe mobil yang berbeda-beda\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(34, 2, 10, '10. Ketika saya mempelajari keterampilan baru, saya paling nyaman:', '{\"A\":\"berbicara langsung dengan ahlinya hal apa saja yang harus saya lakukan\",\"B\":\"mencoba dahulu sendiri dan mempelajarinya seiring berjalannya waktu\",\"C\":\"melihat apa yang dilakukan oleh ahlinya\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(35, 2, 11, '11. Ketika akan memilih makanan dari menu, saya cenderung untuk:', '{\"A\":\"membayangkan makanan tersebut akan terlihat seperti apa\",\"B\":\"mendiskusikan dengan teman saya\",\"C\":\"membayangkan bagaimana rasa makanan tersebut\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(36, 2, 12, '12. Ketika saya mendengarkan sebuah band, saya suka:', '{\"A\":\"mendengarkan lirik dan irama musik\",\"B\":\"melihat para anggota band dan kerumunan penonton\",\"C\":\"terhanyut dalam irama musik\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(37, 2, 13, '13. Ketika saya berkonsentrasi, saya paling sering:', '{\"A\":\"mendiskusikan permasalahan yang ada dan solusi yang memungkinkan di dalam pikiran saya sendiri\",\"B\":\"bergerak mondar-mandir ke sekeliling, bermain-main dengan pensil atau pulpen dan menyentuh\",\"C\":\"fokus dengan kata-kata atau gambar yang ada di depan saya\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(38, 2, 14, '14. Dalam memilih perabotan rumah tangga, saya suka:', '{\"A\":\"warna dan bentuk perabotan tersebut\",\"B\":\"penjelasan yang diberikan bagian penjualan kepada saya\",\"C\":\"tekstur dari perabotan dan bagaimana rasanya menyentuh barang tersebut\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(39, 2, 15, '15. Saya lebih mudah mengingat bila:', '{\"A\":\"melihat sesuatu hal\",\"B\":\"melakukan sesuatu hal\",\"C\":\"berbicara tentang sesuatu hal\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(40, 2, 16, '16. Ketika saya merasa cemas, saya:', '{\"A\":\"tidak bisa duduk diam, berjalan bolak-balik di sekitar terus menerus\",\"B\":\"membayangkan skenario terburuk\",\"C\":\"berbicara pada diri sendiri di dalam kepala hal apa yang paling membuat saya khawatir\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(41, 2, 17, '17. Saya merasa sangat terhubung dengan orang lain karena:', '{\"A\":\"bagaimana mereka terlihat\",\"B\":\"apa yang mereka katakan kepada saya\",\"C\":\"bagaimana mereka membuat saya merasa nyaman\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(42, 2, 18, '18. Ketika saya harus merevisi ulang sebuah presentasi, saya biasanya:', '{\"A\":\"menulis banyak catatan dan diagram untuk revisi\",\"B\":\"membahas catatan saya, sendiri atau dengan orang lain\",\"C\":\"membayangkan bagaimana membuat ide-ide baru\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(43, 2, 19, '19. Jika saya menjelaskan sesuatu ke seseorang, saya cenderung untuk:', '{\"A\":\"menjelaskan kepada mereka dengan berbagai cara sampai mereka mengerti\",\"B\":\"memperlihatkan kepada mereka apa maksud saya\",\"C\":\"mendorong mereka untuk mencoba dan berbicara mengenai ide-ide saya ketika mereka mencoba\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(44, 2, 20, '20. Saya sangat suka:', '{\"A\":\"mendengarkan musik, radio atau berbicara dengan teman-teman\",\"B\":\"mengambil bagian dalam kegiatan olahraga, mencoba restoran baru dan berdansa\",\"C\":\"menonton film, fotografi, melihat seni\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(45, 2, 21, '21. Sebagian besar waktu luang saya habiskan dengan:', '{\"A\":\"menonton film dan youtube\",\"B\":\"mengobrol dengan teman-teman\",\"C\":\"berolahraga seperti berenang atau bersepeda\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(46, 2, 22, '22. Ketika saya harus menghubungi teman baru yang sudah pernah saya temui beberapa kali sebelumnya, saya', '{\"A\":\"berbicara lewat telepon\",\"B\":\"langsung menemuinya\",\"C\":\"melakukan aktivitas bersama\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(47, 2, 23, '23. Saya pertama kali melihat orang dari:', '{\"A\":\"cara mereka berbicara\",\"B\":\"cara mereka berdiri dan bergerak\",\"C\":\"cara mereka berpenampilan\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(48, 2, 24, '24. Jika saya marah, saya cenderung untuk:', '{\"A\":\"terus menerus berpikir hal apa yang membuat saya marah\",\"B\":\"menaikan suara saya dan memberitahukan ke orang-orang apa yang saya rasakan\",\"C\":\"membanting pintu dan secara fisik menunjukan kemarahan saya\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(49, 2, 25, '25. Saya merasa lebih mudah untuk mengingat:', '{\"A\":\"wajah\",\"B\":\"hal-hal yang telah saya lakukan\",\"C\":\"nama\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(50, 2, 26, '26. Anda bisa merasakan seseorang sedang berbohong jika:', '{\"A\":\"mereka membuat lelucon\",\"B\":\"mereka menghindari untuk melihat Anda\",\"C\":\"nada suara mereka berubah\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(51, 2, 27, '27. Ketika saya bertemu dengan teman lama:', '{\"A\":\"saya mengatakan \\u201cSenang bertemu denganmu\\u201d\",\"B\":\"saya mengatakan \\u201cSenang mendengar kabarmu\\u201d\",\"C\":\"saya memeluk atau berjabat tangan dengan mereka\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(52, 2, 28, '28. Saya dapat mengingat secara baik dengan cara:', '{\"A\":\"menulis catatan atau mencetak secara rinci\",\"B\":\"mengatakan dengan keras-keras atau mengulang kata-kata dan poin di dalam kepala\",\"C\":\"berlatih atau membayangkan hal tersebut sudah dilakukan\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(53, 2, 29, '29. Jika saya harus komplain tentang barang yang rusak, saya paling nyaman untuk:', '{\"A\":\"komplain lewat telepon\",\"B\":\"menulis surat\",\"C\":\"membawa barang tersebut kembali ke toko atau mengirimkan ke kantor pusat\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56'),
(54, 2, 30, '30. Saya cenderung untuk mengatakan:', '{\"A\":\"saya mendengar apa yang kamu katakan\",\"B\":\"saya tahu apa yang kamu rasakan\",\"C\":\"saya mengerti apa yang kamu maksud\"}', '2026-05-01 04:33:56', '2026-05-01 04:33:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Bryan', 'bryanambraham@gmail.com', 'admin', '2026-05-01 03:49:04', '$2y$10$/.gAq9q5aTVPvtm2on1V1uf6zQisHMGgMXxynYMEHNIKiYVJ/Iiwq', 'r97pucLAPnZG6bWa8Jf4nWN5eiWBZAbjKx0aKwtBUTbJcWHAEXvkL4zja3MT', '2026-05-01 03:49:04', '2026-05-01 03:49:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_answers`
--

CREATE TABLE `user_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_session_id` bigint(20) UNSIGNED NOT NULL,
  `question_number` int(11) NOT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `exam_sessions`
--
ALTER TABLE `exam_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_sessions_user_id_foreign` (`user_id`),
  ADD KEY `exam_sessions_exam_id_foreign` (`exam_id`);

--
-- Indeks untuk tabel `exam_user`
--
ALTER TABLE `exam_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_user_user_id_foreign` (`user_id`),
  ADD KEY `exam_user_exam_id_foreign` (`exam_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `proctoring_logs`
--
ALTER TABLE `proctoring_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proctoring_logs_exam_session_id_foreign` (`exam_session_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `exam_sessions`
--
ALTER TABLE `exam_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `exam_user`
--
ALTER TABLE `exam_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `proctoring_logs`
--
ALTER TABLE `proctoring_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `exam_sessions`
--
ALTER TABLE `exam_sessions`
  ADD CONSTRAINT `exam_sessions_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `exam_user`
--
ALTER TABLE `exam_user`
  ADD CONSTRAINT `exam_user_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `proctoring_logs`
--
ALTER TABLE `proctoring_logs`
  ADD CONSTRAINT `proctoring_logs_exam_session_id_foreign` FOREIGN KEY (`exam_session_id`) REFERENCES `exam_sessions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
