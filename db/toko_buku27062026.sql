-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2026 at 09:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_buku`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `judul`, `penulis`, `kategori`, `harga`, `gambar`, `deskripsi`) VALUES
(6, 'Cantik Itu Luka', 'Eka Kurniawan', 'Non-Fiksi', 30000, '1776690089_Cantik-itu-Luka-Cover-Besar.jpg', 'Kisah dalam buku ini diawali oleh seorang perempuan yang bangkit dari kuburannya setelah dua puluh satu tahun kematiannya. Kebangkitannya ini menguak kutukan dan tragedi keluarga yang menimpanya di akhir masa kolonial.\r\n\r\nPerpaduan yang epik, dari kisah kekasih yang lenyap ditelan kabut, hingga ada seorang ibu yang menginginkan bayi buruk rupa karena baginya kecantikan hanyalah sebuah luka.'),
(7, 'Laut Bercerita', 'Leila S. Chudori', 'Fiksi', 45000, '1776692009_Laut-Bercerita4-1.jpg', 'Laut Bercerita mengisahkan arti keluarga yang kehilangan, sekumpulan sahabat yang merasakan kekosongan di dada, sekelompok orang yang gemar menyiksa dan lancar berkhianat, sejumlah keluarga yang mencari kejelasan makam anaknya, dan tentang cinta yang tak akan luntur oleh waktu.'),
(8, 'Saman', 'Ayu Utami', NULL, 35000, '1776692129_9786024243999_Saman-2018.jpg', 'Dwilogi Saman dan Larung ini berawal dari kisah empat perempuan yang bersahabat sejak kecil, ada Shakuntala yang pemberontak, Cok yang nakal, Yasmin yang jaim, serta Laila yang lugu dan sedang bimbang apakah dirinya harus menyerahkan diri kepada lelaki yang telah beristri ataukah tidak.\r\n\r\nDiam-diam tanpa diketahui siapapun, dari keempat sahabat itu ada dua orang diantaranya yang menyimpan perasaan pada seorang pemuda sejak dahulu kala, yaitu Yasmin dan Laila. Sementara sosok pemuda yang diidamkan dua orang itu bernama Saman, ia merupakan seorang aktivis yang menjadi buron pada masa militer Orde Baru.'),
(13, 'Ronggeng Dukuh Paruk', 'Ahmad Tohari', 'Non-Fiksi', 45000, '1776694872_9789792201963_Ronggeng-Dukuh-Paruk.jpg', 'Ronggeng Dukuh Paruk kisahkan penari ronggeng baru bernama Srintil, yang baru saja dinobatkan sebagai ronggeng terakhir setelah kematian 12 tahun ronggeng lamanya.\r\n\r\nBagi pendukuhan yang terpencil ini, ronggeng merupakan lambang, tanpa ronggeng Dukuh sudah seperti kehilangan jati dirinya. Setelah penobatannya, Srintil menjadi terkenal dan digandrungi oleh semua kalangan, mulai dari pejabat hingga orang-orang desanya.\r\n\r\nNamun, malapetaka politik yang terjadi pada tahun 1965 membuat dukuh tersebut hancur, baik secara fisik dan mental. Mereka semua divonis sebagai manusia yang menghancurkan negara, penabuh culung dan ronggeng ditahan, serta pedukuhan itu dibakar.\r\n\r\nSelama masa tahanan ini, Srintil tidak diperlakukan semena-mena oleh para penguasa karena kecantikannya yang sangat rupawan, tapi akhirnya hal ini membuat Srintil sadar akan harkat, martabat, dan haknya sebagai seorang manusia.  '),
(20, 'Sumur', 'Eka Kurniawan', 'Non-Fiksi', 34998, '1776706299_9786020653242_SUMUR--1-.jpg', 'Sumur merupakan cerita pendek yang berhasil memasuki nominasi Man Booker International Prize 2016 dan peraih Prince Clause Laureate 2018. \r\n\r\nCerita pendek berisi 60 halaman ini, pertama kali terbit dalam bahasa Inggris dalam buku Tales of Two Planets yang diterbitkan oleh Penguin Books pada tahun 2020.\r\n\r\nKisah kekeringan berkepanjangan di suatu kampung, membuat satu per satu orang meninggalkan kampung ini dan pindah ke tempat lain. Bagi yang pindah, ada dari mereka yang pergi ke kota hanya untuk mencari pekerjaan atau memilih menetap di sana. \r\n\r\nSementara itu bagi yang tersisa, mereka harus bisa bertahan memperoleh air bersih setiap harinya dari sumur ini. Selain itu, sumur ini juga merupakan saksi bisu dari sebuah kisah romansa sekaligus tragedi kelam yang mengiringinya.'),
(21, 'Bumi Manusia ', ' Pramoedya Ananta Toer', 'Non-Fiksi', 60000, '1776706390_Bumi-Manusia-1.jpg', 'Bumi Manusia merupakan bagian dari Tetralogi Buru yang pertama dan mengambil latar serta cikal bakal bangsa Indonesia pada awal abad ke-20. \r\n\r\nDalam periode ini, mengisahkan Minke, sang kreator yang berdarah priyayi, di mana ia ingin bebas dan merdeka seperti bangsa Eropa yang selalu menjadi kiblat dan simbol pengetahuan serta peradaban pada masanya.\r\n\r\nBuku yang awalnya menceritakan pergerakan nasional, pertautan rasa, kegamangan jiwa, percintaan, dan pertarungan kekuatan para srikandi yang mengawal penyemaian bangunan nasional hingga melahirkan bangsa Indonesia modern ini, memiliki banyak adegan sentimentil yang ditulis oleh Pram. \r\n\r\nBumi Manusia merupakan buku legendaris yang berhasil bersinar di luar negeri maupun mancanegara, berkat kisah Minke dan ayahnya yang membuat banyak orang terkesima.'),
(22, 'One Piece', 'Eichiiro Oda ', 'Komik', 67000, '1776706574_326451.jpg', 'One Piece adalah sebuah seri manga Jepang yang ditulis dan diilustrasikan oleh Eiichiro Oda. Manga ini Menceritakan petualangan Monkey D. Luffy, seorang anak laki-laki yang memiliki kemampuan tubuh elastis seperti karet setelah memakan Buah Iblis secara tidak disengaja.\r\n\r\nLuffy bersama kru bajak lautnya, yang dinamakan Bajak Laut Topi Jerami, menjelajahi Grand Line untuk mencari harta karun terbesar di dunia yang dikenal sebagai &quot;One Piece&quot; dalam rangka untuk menjadi Raja Bajak Laut yang berikutnya.'),
(23, 'Laskar Pelangi', ' Andrea Hirata', NULL, 45000, '1776706752_img212.jpg', 'Laskar Pelangi mengisahkan tentang sebelas anak yang berasal dari keluarga miskin, di mana mereka harus selalu berjuang dalam menempuh pendidikannya di sekolah sederhana yang ada di desa Balitong. Novel ini menegaskan bahwa setiap orang berhak memiliki cita-cita dan impian, serta setiap orang pasti memiliki kesempatan untuk bisa mewujudkan impiannya.'),
(24, ' Perjalanan Menuju Pulang', 'Lala Bohang, Lala Noberg', 'Fiksi', 50000, '1776706834_WhatsApp_Image_2021-07-12_at_16.09.11.jpeg', 'Novel ini mengisahkan pula tahap baru kehidupan dalam sejarah saat dunia sudah meninggalkan kolonialisme dan pascakolonialime. \r\n\r\nMenurut Lala dan Lara, buku ini merupakan bukti nyata dari dua penulis yang lahir di generasi ketiga dan di negara yang berbeda yaitu Belanda dan Indonesia. Menariknya, keseluruhan novel ini berdialog tentang pencarian bukti, ada kah ikatan mereka dan para leluhurnya kepada sang nenek bernama Marion Bloem..'),
(25, 'Re:Zero : Starting Life in Another World', 'Tappei Nagatsuki', 'Komik', 80000, '1776707168_MV5BNTY1M2NjMTItOGFhNi00NDU3LWExNzQtZGY2YWJlYzExNmU3XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'Re:Zero - Starting Life in Another World (Re:Zero kara Hajimeru Isekai Seikatsu) adalah seri isekai gelap yang mengisahkan Subaru Natsuki, seorang pemuda yang tiba-tiba berpindah ke dunia fantasi. \r\n\r\nIa memiliki kemampuan unik &quot;Return by Death&quot;, yang memungkinkannya memutar balik waktu setelah kematian untuk menyelamatkan diri dan teman-temannya dari nasib tragis'),
(29, 'Petualangan Don Quixote', 'Miguel De Cervantes', 'Fiksi', 60000, '1782129490_PETUALANGAN-DON-QUIXOTE.jpg', 'Novel Petualangan Don Quixote ini berkisah tentang kegilaan Alonzo Quinjano akibat terlalu banyak membaca buku-buku petualangan para kesatria. Gagasan-gagasan kepahlawanan dan kehebatan para kesatria yang ada di buku-buku yang dibaca itu membuat tuan tanah ini terkagum-kagum. Ia memutuskan untuk menjadi kesatria yang membela kebenaran melalui peristiwa-peristiwa yang heroik. Dalam novel pendek ini Cerpantes menceritakan petualangan Don Quixote yang sangat absurd.'),
(30, 'Menunggu Musim Kupu-kupu', 'Adi Zamzam', 'Fiksi', 45000, '1782129687_Menunggu-Musim-Kupu-kupu.jpg', 'Sebagian besar karya-karya saya dalam buku ini mengingatkan saya pada masa-masa awal tersedot magnet dunia kepenulisan. Ada banyak idealisme yang ingin saya capai ketika itu. Hingga akhirnya sampailah saya di sini, pada tahap ini, pada umur, pada posisi, pada situasi, dan pada kesempatan ini, ketika akhirnya buku ini terbit.\r\n\r\nMenelisik ulang karya-karya dalam buku ini mengingatkan saya pada cita-cita awal yang absurd itu, tentang apa yang disebut oleh Mongare Serote, penyair Afrika Selatan, sebagai kompleksitas kebenaran, yang konon adalah buruan termahal dari para penulis sastra. Betapa bahagianya saya jika masih bisa menjadi bagian dari deretan mereka yang berburu kompleksitas kebenaran itu, dan kemudian mengembalikannya kepada dunia.'),
(31, 'Romeo Juliet', 'William Shakespeare', 'Fiksi', 48000, '1782130080_Romeo-Juliet-BUKU-BIJAK.jpg', '“Oh, Romeo! Romeo! Mengapa namamu Romeo? Ingkarilah nama ayahmu dan juga namamu! Atau, kalau engkau tidak mau, demi cintaku, aku bersedia tidak menjadi seorang Capulet.”\r\n\r\nApa artinya sebuah nama bagi seorang pencinta. Ketulusan adalah poin yang paling utama. Bagi Juliet, Romeo akan tetap menjadi Romeo dengan pribadi yang disukainya, kekasih yang dicintainya, meski namanya bukan lagi Romeo Montague. Seperti mawar yang akan tetap wangi meskipun disebut dengan nama lainnya. Bagi seorang pencinta seperti Juliet, sebuah nama tiada artinya.\r\nSelamat membaca dan menyelami pedihnya kisah cinta Romeo dan Juliet.'),
(32, 'Perempuan Batih', ' A.R. Rizal ', 'Non-Fiksi', 60000, '1782130190_Perempuan-Batih.jpg', '“Yang aku takutkan bukan kematian, melainkan bila dibunuh sepi.” ***\r\nGadis, seorang perempuan kampung yang hidup dalam masyarakat matrilineal Minangkabau, Sumatra Barat. Sebagai anak perempuan satu-satunya, seharusnya kehidupan Gadis baik-baik saja. Tapi tidak. Ia justru harus menantang hidup yang sangat berat. Menjawab tantangan kehidupan akan takdirnya sebagai perempuan.\r\n\r\nAnak perempuan di Minangkabau seharusnya tinggal di rumah kaumnya. Namun, anak-anak perempuan Gadis memilih meninggalkan rumah. Mereka ingin menjadi manusia mandiri. Padahal, dalam keluarga batih, perempuan mengambil peran yang sangat strategis dan menentukan.\r\n\r\nTinggal di kampung yang berbatasan dengan hiruk-pikuk kota di Sumatra Barat, Gadis hidup dalam gilingan perubahan zaman.\r\nBenarlah kata pepatah, “Kasih anak sepanjang galah, kasih ibu sepanjang jalan.”'),
(33, ' Silly Gilly Daily: Stay At Home', ' Naela Ali ', 'Non-Fiksi', 62000, '1782130360_sillygillydailystayathome-1.jpg', 'Buku ini merupakan edisi baru keseharian Gilly di masa pandemi.  Naela Ali selaku kembali memilih tema yang dekat dengan dirinya, sehingga apa yang ia sampaikan terasa sangat jujur dan relate dengan kehidupan pembaca.\r\n\r\nSebagai seorang introvert, tentunya pembaca akan dibuat merasa memiliki banyak kesamaan dengan tokoh Gilly. Selalu merasa cemas saat akan bertemu dengan klien, merasa lelah saat berada di keramaian, memilih jalan memutar demi agar tidak bertemu dengan orang lain, dan masih banyak lagi sifat khas yang dimiliki orang-orang introvert.\r\n\r\nTapi bagaimana keseharian Gilly di masa pandemi sebagai seorang introvert? Apakah Gilly akan merasa cemas saat bertemu dengan klien melalui zoom, atau Gilly merasa lebih nyaman saat berada di rumah di masa pandemi ini?'),
(34, ' SAPIENS GRAFIS: Kelahiran Umat Manusia', 'Yuval Noah Harari,', 'Sains', 55000, '1782131072_9786024815653_Sapiens_Grafis_Spread_1_Au4pg9r-1.jpeg', 'Buku ini pertama kali diterbitkan dengan bahasa Ibrani pada tahun 2011, dan sudah diterjemahkan ke dalam 60 bahasa, sampai akhir tahun 2020 lalu. Sapiens menjelaskan sejarah manusia dan perkembangannya dengan kacamata ilmiah, biologi, dan sejarah, dengan data-data yang mungkin sama sekali belum kamu ketahui sebelumnya. Buku ini tersusun secara urut dan sistematis dalam menelaah sejarah, di mana Yuval mendudukkan konteks waktu sebagai pemeran penting.\r\n\r\nSecara garis besar, Yuval membagi kisah sejarah Sapiens menjadi tiga bagian. Pertama, Revolusi Kognitif, yang terjadi sekitar 70.000 SM, ketika imajinasi mulai berevolusi dalam peradaban baru kognisi Sapiens. Kedua, Revolusi Agrikultur, terjadi sekitar 12.000 SM saat dimulainya gaya hidup menetap dan bertani. Terakhir pada Revolusi Ilmiah, yaitu dimulai sekitar tahun 500 tahun lalu, saat munculnya sains. Revolusi ini menyebabkan tumbuhnya populasi manusia dan pertumbuhan ekonomi secara signifikan dalam waktu yang amat cepat. Seluruh peristiwa tersebut membawa perubahan, sangat berpengaruh pada sejarah manusia dan makhluk lain, serta apa yang menyatukan umat manusia.'),
(35, 'Dreams Are Made of A Box of Crayons', ' Naela Ali', 'Fiksi', 70000, '1782223647_Dreams-Are-Made-Of-A-Box-Of-Crayons.jpg', ' Dreams Are Made Of A Box Of Crayons berisi kumpulan cerita singkat, tentang impian seorang anak yang muncul ketika ia mulai belajar menggambar dengan krayon. Seiring berjalannya waktu, impian tersebut mulai terlupakan karena kesibukan si anak. Juga hilangnya kepercayaan diri, karena berkali-kali mengalami kegagalan. Buku ini merangkum perjalanan si anak untuk meraih mimpinya.'),
(36, 'Frieren : Beyond Journey\'s End ', 'Kanehito Yamada', 'Komik', 90000, '1782224993_frieren-beyond-journey-s-end-vol-10.jpg', 'Frieren dimulai dari akhir sebuah cerita — Frieren adalah seorang penyihir elf yang merupakan bagian dari kelompok yang kembali sebagai pahlawan setelah membunuh raja iblis. Sementara semua orang menjadi tua, kecuali dirinya, lima puluh tahun berlalu begitu saja. Pemakaman salah satu rekannya membuatnya berhadapan langsung dengan keabadiannya sendiri — dan dia memulai perjalanan baru untuk mempelajari lebih lanjut tentang manusia dan menguraikan apa arti sebenarnya dari hidup abadi.'),
(37, 'Alya Sometimes Hides Her Feelings in Russian:', 'Sun Sun Sun', 'Komik', 75000, '1782226147_Alya-Sometimes-Hides-Her-Feeling-in-Russian-1.png', 'Cerita ini berfokus pada Masachika Kuze, seorang siswa SMA yang agak malas tetapi jeli, dan teman sebangkunya yang cantik dan cerdas, Alisa Mikhailovna Kujo (Alya), seorang gadis berdarah campuran Rusia dan Jepang.\r\n\r\nAlya yang dikagumi karena sikapnya yang dingin dan angkuh, sering melontarkan komentar sarkastik atau sinis kepada Masachika. Namun, ia juga membisikkan perasaannya yang sebenarnya dalam bahasa Rusia, dengan asumsi bahwa Masachika tidak mengerti.\r\n\r\nTanpa sepengetahuan Alya, Masachika diam-diam tahu bahasa Rusia, mengubah interaksi mereka menjadi campuran humor, romantis, dan momen-momen yang menyentuh hati. '),
(38, 'Your Name (Kimi no Na wa)', ' Makoto Shinkai ', 'Novel', 85000, '1782226440_Your_Name_poster.png', 'Mitsuha Miyamizu, seorang siswi sekolah menengah atas yang tinggal di desa fiktif bernama Itomori di daerah pegunungan Hida Prefektur Gifu, mulai bosan dengan kehidupannya di pedesaan tempat dia lahir dan berharap dapat terlahir menjadi pemuda tampan yang hidup di Tokyo pada kehidupan selanjutnya. Kemudian, Taki Tachibana, seorang siswa sekolah menengah atas yang tinggal di Tokyo, terbangun dari tidurnya dan menyadari bahwa dirinya adalah Mitsuha, yang entah bagaimana bisa masuk ke dalam tubuh Taki.\r\n\r\nTaki dan Mitsuha menyadari bahwa mereka berdua saling memasuki tubuh satu sama lain. Mereka mulai berkomunikasi satu sama lain dengan saling meninggalkan catatan di kertas maupun melalui memo di ponsel mereka. Seiring dengan berjalannya waktu, mereka semakin terbiasa dengan pertukaran tubuh ini serta mulai mencampuri kehidupan satu sama lain. '),
(39, 'BINTANG', 'Tere Liye', 'Fiksi', 60000, '1782230722_cover-novel-bintang-karya-tere-liye.jpg', 'Novel Bintang mengisahkan petualangan ketiga remaja SMA–bisa dikatakan persahabatan ketiga anak–yang memiliki rasa ingin tahu sangat tinggi dan banyak, mereka bertiga adalah Raib, Seli, dan Ali. Di novel sebelumnya dari serial Bumi sudah menjelaskan terkait kisah Raib, kemudian di novel berikutnya menceritakan kisah Seli, dan pada novel ini mengisahkan seorang yang sangat genius bisa diibaratkan seorang cendekiawan, bernama Ali.'),
(40, 'BUMI', 'Tere Liye', 'Fiksi', 70000, '1782230963_9786020332956_Bumi-New-Cover.jpg', ' Novel ini mengisahkan tentang petualangan 3 remaja yang berusia 15 tahun bernama Raib, Ali dan Seli. Namun mereka bukanlah remaja biasa, melainkan remaja yang memiliki kekuatan khusus seperti Raib yang bisa menghilang, Seli yang bisa mengeluarkan petir dan Ali seorang pelajar yang sangat jenius. Petualangan menjelajah dunia paralel mereka dimulai dari sini, dunia paralel yang pertama mereka jelajahi adalah Klan Bulan. \r\n\r\nTetapi mereka tidak hanya sekedar menjelajah saja, melainkan mereka harus bertarung untuk menyelamatkan dunia paralel dari orang-orang jahat. Orang-orang jahat tersebut yakni bernama Tamus. Tamus memiliki ambisi untuk menguasai dunia, oleh karena itu ia berusaha untuk membebaskan seorang pangeran yang sangat kuat dan memiliki ambisi yang sama. Pangeran tersebut sedang dipenjara yang disebut &quot;Penjara Bayangan dibawah Bayangan&quot;. Pangeran tersebut bernama Si Tanpa Mahkota. '),
(41, '86 -EIGHTY SIX', ' Asato Asato', 'Komik', 82000, '1782231233_86_light_novel_volume_1_cover.jpg', 'Republik San Magnolia telah berperang dengan Kekaisaran Giad selama sembilan tahun. Meskipun awalnya menderita kerugian yang menghancurkan dari Legion mekanik otonom Kekaisaran, Republik telah mengembangkan unit otonomnya sendiri, yang disebut Juggernaut, yang diarahkan dari jarak jauh oleh seorang Handler. Sementara di permukaan publik percaya perang sedang terjadi di antara mesin, kenyataannya jauh lebih mengerikan. Pada kenyataannya, Juggernaut diujicobakan oleh manusia, sebutan yang diberikan kepada minoritas Colorata San Magnolia yang awalnya memiliki hak yang sama sebagai ras Alba yang dominan tetapi dianiaya dan dikambinghitamkan oleh pemerintah Alba yang dipimpin rasis untuk intinya Colorata dianggap bukan manusia, tidak diizinkan memiliki nama dan dipaksa tinggal di kamp interniran di Bangsal 86 sementara dipaksa bertempur dalam perang Republik dengan Kekaisaran untuk mendapatkan kemajuan.'),
(42, 'Solo Leveling', ' Chu-Gong', 'Komik', 95000, '1782231954_image005-5-364x517.jpg', 'Novel Solo Leveling sendiri memiliki alur cerita masa lalu dimulai dari 10 tahun sebelum alur cerita utamanya dimulai. Pada saat itu, terjadi kemunculan gate atau seperti portal di seluruh penjuru dunia. Gate ini pada dasarnya sendiri merupakan sebuah portal yang menghubungkan dunia para monster dan dunia manusia.\r\n\r\nSetelah kemunculan gate tersebut, perlahan-lahan mulai tersebar secara acak, sebagian orang di dunia ini kemudian menerima kemampuan khusus yang membuat mereka mampu mengalahkan monster di suatu gate. Orang dengan kemampuan khusus ini, kemudian mulai memburu monster yang ada di dalam gate atau disebut juga sebagai Hunter.\r\n\r\nSung Jin-Woo sebagai karakter utama di cerita ini ialah seorang Hunter lemah dengan peringkat terendah. Bahkan, seringkali ia hampir mati di penjelajahan gate yang membuatnya keluar masuk rumah sakit.\r\n\r\nNamun, semua hal itu kemudian berubah ketika ia turut ikut di dalam insiden double dungeon dan menjadikannya sebagai Player. Dari situlah Sung Jin-Woo, kemudian memulai petualangannya sebagai seorang Hunter serta mengungkap misteri di balik title Player-nya. '),
(43, 'Seporsi Mie Ayam Sebelum Mati', 'Brian Khrisna', 'Fiksi', 75000, '1782232048_semv-ptm-e.avif', 'Seperti malam-malam lain, aku pulang selepas lembur. Orang-orang di kantor yang sudah menikah, mereka akan pulang ke keluarganya masing-masing. Sementara aku yang tidak punya siapa-siapa ini, sekarang masih duduk sendirian di parkiran mobil yang sudah lengang, bersama sebotol bir, rokok murah, dan sepotong kue ulang tahunku sendiri yang kubeli dari toko manisan dekat kantor.\r\n\r\nAku takut kalau ternyata selama ini aku tidak pernah berhasil menjalani hidup seperti sebagaimana seharusnya. Di kepalaku sekarang, pertanyaan ini semakin lama semakin membesar. “Pantaskah hidup ini kulanjutkan?”\r\n\r\nAku berdiri menatap ke langit malam.\r\nKini tekadku sudah bulat.\r\nAku akan bunuh diri 24 jam dari sekarang.'),
(44, 'Top 10 MBG Tercantik', 'Piperou', 'Fiksi', 10000000, '1782503203_myphrolova.jpg', 'Buku ini adalah penjelasan karakter Anime/Game yang termasuk dalam kategori karakter tercantik menurut Saya.');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipe_pengiriman` varchar(50) DEFAULT 'Ambil di Toko',
  `alamat_pengiriman` text DEFAULT NULL,
  `biaya_antar` int(11) DEFAULT 0,
  `status` enum('Lunas','Selesai') DEFAULT 'Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `book_id`, `total_harga`, `metode_pembayaran`, `tanggal`, `tipe_pengiriman`, `alamat_pengiriman`, `biaya_antar`, `status`) VALUES
(5, 1, 22, 67000, 'COD', '2026-04-20 17:47:30', 'Ambil di Toko', NULL, 0, 'Lunas'),
(6, 5, 25, 75000, 'Transfer Bank', '2026-04-25 01:56:43', 'Ambil di Toko', NULL, 0, 'Lunas'),
(7, 1, 25, 69995, 'E-Wallet', '2026-05-25 05:46:29', 'Ambil di Toko', NULL, 0, 'Lunas'),
(8, 1, 25, 69995, 'COD', '2026-06-21 11:16:13', 'Ambil di Toko', NULL, 0, 'Lunas'),
(9, 1, 25, 69995, 'Transfer Bank', '2026-06-21 11:39:32', 'Ambil di Toko', NULL, 0, 'Lunas'),
(10, 3, 23, 45000, 'E-Wallet', '2026-06-21 12:18:24', 'Ambil di Toko', NULL, 0, 'Lunas'),
(11, 3, 22, 67000, 'COD', '2026-06-22 05:52:13', 'Ambil di Toko', NULL, 0, 'Lunas'),
(12, 3, 24, 50000, 'E-Wallet', '2026-06-22 10:48:17', 'Ambil di Toko', NULL, 0, 'Selesai'),
(13, 3, 8, 35000, 'Transfer Bank', '2026-06-22 11:11:17', 'Ambil di Toko', NULL, 0, 'Selesai'),
(14, 3, 34, 60000, 'COD', '2026-06-22 16:01:25', 'Antar ke Rumah', 'Jln Ceri 3 No 17 Perum Trias, Bekasi Jawa Barat', 15000, 'Selesai'),
(15, 3, 31, 48000, 'Transfer Bank', '2026-06-23 06:18:24', 'Ambil di Toko', '', 0, 'Selesai'),
(16, 3, 43, 90000, 'Transfer Bank', '2026-06-24 09:21:25', 'Antar ke Rumah', 'Jln Ceri 10 Perum Trias Wanasari, Cibitung', 15000, 'Lunas'),
(17, 13, 44, 10015000, 'COD', '2026-06-26 19:55:52', 'Antar ke Rumah', 'Perumahan Roya Frostland', 15000, 'Lunas');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client') DEFAULT 'client',
  `saldo` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `saldo`) VALUES
(1, '202410715284@mhs.ubharajaya.ac.id', '$2y$10$76ilHpYgBQAAkYKQ8Tz.YeMaX49Vr31Zy2rxdo/jedNcTcv1DrFyK', 'client', 0),
(2, 'Zykurumii', '$2y$10$yORFkV6kLa5MaUppLHdQf.ucJavD3qn5FQNNSbP8ZqMJPCoVZDoLa', 'admin', 0),
(3, 'admin', '$2y$10$Esv4LAL4j7lYxZysy7KchuMsa1Wre0qnrhwR90Eu0e1cvzI0hwJiS', 'client', 4767000),
(5, 'ragabagus', '$2y$10$p/d2EX6ZXrd.UwZesYiepOHDweeQWOpX5O7tYoKkBI5LyXNeKf22C', 'client', 0),
(6, 'user1234', '$2y$10$N.exDJSkJq9bXy620TKVQ.2IW8t4Ob5T2gpYHVI26JXULym0A0.zO', 'client', 0),
(7, 'petugas', '$2y$10$bv.9.5mIU2LnWxfwO11ltu9.GUFGG68SRfe8LxYD9JFQBc248iuha', 'client', 0),
(8, 'user1', '$2y$10$F55Jl14VVAOAKL03pfodHOMT7VS4/lht/KfqTCZNCfpciSkDKDCf6', 'client', 100000),
(12, 'bagusraga', '$2y$10$Rc.SxaLu3tQYlbMIp4RB9e5Qzb2IoA7p4N82wo6z7o.UNlblx3hKe', 'client', 0),
(13, 'piperou', '$2y$10$dDMbIPI7jowU6j8FSvSEkOd.2xjH7p6m5CLTJHzYdJ2dE5MlAWec.', 'client', 985000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
