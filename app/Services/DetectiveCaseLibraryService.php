<?php

namespace App\Services;

use App\Models\DetectiveCase;
use Illuminate\Support\Str;

class DetectiveCaseLibraryService
{
    /**
     * Ensure all 10 built-in detective cases exist in the database.
     */
    public static function ensureDefaultCasesExist(): void
    {
        if (DetectiveCase::where('is_default', true)->count() >= 10) {
            return;
        }

        foreach (self::defaultCases() as $caseData) {
            DetectiveCase::firstOrCreate(
                ['slug' => Str::slug($caseData['title'])],
                [...$caseData, 'is_default' => true]
            );
        }
    }

    /**
     * Return all 10 rich detective cases with >=10 consistent investigation facts each.
     *
     * @return list<array<string, mixed>>
     */
    public static function defaultCases(): array
    {
        return [
            [
                'title' => 'Case 01 — The Slow Website',
                'category' => 'Web Performance',
                'difficulty' => 'easy',
                'description' => 'Website toko online perusahaan membutuhkan waktu 25 detik untuk terbuka penuh. Keluhan pelanggan melonjak sejak kemarin sore. Pemilik perusahaan berencana membeli server baru seharga Rp10 juta hari ini karena yakin server saat ini sudah tidak kuat.',
                'initial_assumption' => 'Pemilik perusahaan menduga kapasitas CPU & RAM server sudah penuh dan perlu upgrade server Rp10 juta.',
                'mission_objectives' => [
                    'Identifikasi komponen mana yang sebenarnya menyebabkan waktu muat 25 detik.',
                    'Buktikan dengan data mengapa upgrade server Rp10 juta tidak akan menyelesaikan masalah.',
                    'Susun solusi perbaikan teknis beserta cara memvalidasinya.',
                ],
                'hidden_facts' => [
                    'Penggunaan CPU server rata-rata hanya 12% bahkan saat jam sibuk.',
                    'Penggunaan RAM server stabil di angka 30% (masih tersisa 70% kosong).',
                    'Waktu eksekusi seluruh query database hanya 0,08 detik.',
                    'Kecepatan jaringan & ping server menuju ISP dalam kondisi normal (< 25 ms).',
                    'Total ukuran halaman (page weight) yang diunduh browser mencapai 32 MB.',
                    'Terdapat 1 gambar banner utama berformat PNG mentah berukuran 28 MB.',
                    'Tim desain baru mengganti gambar banner promo tersebut kemarin sore.',
                    'Saat gambar banner dinonaktifkan di DevTools, halaman terbuka penuh dalam 1,4 detik.',
                    'Server tidak mengaktifkan kompresi gambar otomatis atau CDN untuk file statis.',
                    'Pengguna mengakses lewat jaringan seluler 4G rata-rata 8–10 Mbps sehingga unduhan 32 MB memakan waktu >20 detik.',
                ],
                'evidence' => [
                    'Metrik server: CPU 12%, RAM 30%, Query DB 0,08 detik.',
                    'Network tab browser: total payload 32 MB dengan 1 aset banner.png sebesar 28 MB.',
                    'Uji coba tanpa banner menghasilkan waktu muat 1,4 detik.',
                ],
                'false_leads' => [
                    'Jumlah pengunjung harian naik 15% dibanding minggu lalu (masih jauh di bawah kapasitas server).',
                    'Ada 2 plugin analytics terpasang, namun total script-nya hanya 45 KB.',
                ],
                'special_hints' => [
                    'Coba periksa perbandingan antara beban kerja backend (CPU/RAM/DB) dengan ukuran total file yang dikirim ke browser pengguna.',
                    'Ada perubahan aset visual yang dilakukan tepat sebelum keluhan muncul kemarin.',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Tim marketing baru saja mengonfirmasi bahwa kemarin sore mereka mengunggah poster promo resolusi 8K langsung dari software desain tanpa dikompres.',
                ],
                'root_cause' => 'Gambar banner utama berukuran 28 MB tidak dioptimasi sehingga membebani total ukuran halaman menjadi 32 MB dan memperlambat pengunduhan di browser pengguna.',
                'recommended_solutions' => [
                    'Kompres dan konversi gambar banner ke format modern (WebP/AVIF) dengan target ukuran < 250 KB.',
                    'Terapkan batas maksimal ukuran upload gambar pada CMS dan gunakan lazy-loading / responsive srcset.',
                    'Pasang CDN serta HTTP caching headers untuk aset statis.',
                ],
                'validation_method' => 'Ukur ulang menggunakan Chrome DevTools Network & Lighthouse sebelum dan sesudah optimasi banner; verifikasi total payload turun di bawah 3 MB dan waktu load (LCP) di bawah 2,5 detik pada simulasi Fast 3G/4G.',
                'default_duration' => 20,
            ],
            [
                'title' => 'Case 02 — The Missing Money',
                'category' => 'Business & Finance',
                'difficulty' => 'easy',
                'description' => 'Sebuah toko retail populer melaporkan kerugian sebesar Rp5 juta setiap bulan selama dua bulan terakhir padahal penjualan sangat ramai. Pemilik toko mencurigai kasir atau staf gudang diam-diam mencuri uang kas harian.',
                'initial_assumption' => 'Pemilik menuduh adanya pencurian uang kas oleh karyawan karena omzet ramai tetapi saldo akhir bulan minus Rp5 juta.',
                'mission_objectives' => [
                    'Verifikasi apakah benar terjadi kehilangan fisik uang atau selisih kas.',
                    'Bongkar penyebab pasti defisit Rp5 juta per bulan menggunakan data unit economics.',
                    'Rekomendasikan langkah penyelamatan margin yang terukur.',
                ],
                'hidden_facts' => [
                    'Audit rekonsiliasi menunjukkan uang kas di laci dan rekening selalu cocok 100% dengan struk transaksi POS.',
                    'Rekaman CCTV kasir selama 60 hari tidak menunjukkan adanya pencurian maupun transaksi fiktif.',
                    'Stok fisik barang di gudang cocok dengan catatan barang keluar.',
                    'Harga jual produk unggulan ke pelanggan masih dipatok Rp20.000 per unit.',
                    'Modal pembelian produk (HPP) saat toko pertama buka enam bulan lalu adalah Rp15.000 per unit.',
                    'Sejak dua bulan lalu, supplier menaikkan modal bahan/barang menjadi Rp22.000 per unit akibat kenaikan biaya logistik.',
                    'Admin gudang mencatat faktur pembelian baru, tetapi tim manajemen tidak memperbarui harga jual di sistem kasir.',
                    'Volume penjualan produk tersebut stabil di angka 2.500 unit per bulan.',
                    'Selisih rugi per produk adalah Rp20.000 - Rp22.000 = -Rp2.000 per unit.',
                    'Total kerugian bersih: 2.500 produk × Rp2.000 = Rp5.000.000 setiap bulan.',
                ],
                'evidence' => [
                    'Rekonsiliasi kas & stok fisik 100% klop tanpa selisih.',
                    'Faktur supplier 2 bulan terakhir menunjukkan HPP naik dari Rp15.000 menjadi Rp22.000.',
                    'Harga jual di POS masih Rp20.000 × 2.500 terjual = defisit tepat Rp5.000.000/bulan.',
                ],
                'false_leads' => [
                    'Ada 1 karyawan baru masuk 2 bulan lalu (namun kinerjanya disiplin dan tidak memegang otoritas diskon).',
                    'Toko sempat mengganti mesin kasir ke aplikasi baru.',
                ],
                'special_hints' => [
                    'Jika uang di laci cocok dengan struk tetapi toko tetap merugi tepat Rp5 juta dengan penjualan 2.500 unit, hitung selisih per unitnya.',
                    'Periksa perubahan harga beli dari supplier dibandingkan harga jual saat ini.',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Bagian akuntansi baru menemukan tumpukan faktur supplier 2 bulan terakhir yang menunjukkan kenaikan biaya pokok per barang, namun daftar harga kasir belum pernah diubah.',
                ],
                'root_cause' => 'Harga jual (Rp20.000) lebih rendah daripada modal terbaru (Rp22.000) sehingga setiap unit terjual menghasilkan kerugian Rp2.000 (2.500 unit × Rp2.000 = Rp5.000.000/bulan).',
                'recommended_solutions' => [
                    'Segera sesuaikan harga jual di atas HPP terbaru (misalnya Rp27.000–Rp29.000) dengan menghitung margin operasional.',
                    'Terapkan sistem peringatan otomatis di POS jika margin produk turun di bawah batas minimum.',
                    'Evaluasi alternatif supplier atau negosiasi pembelian grosir untuk menekan modal.',
                ],
                'validation_method' => 'Pantau laporan laba rugi mingguan selama 1 bulan setelah penyesuaian harga; pastikan gross margin per unit kembali positif dan arus kas bulanan surplus.',
                'default_duration' => 20,
            ],
            [
                'title' => 'Case 03 — The Broken Application',
                'category' => 'Backend & DevOps',
                'difficulty' => 'easy',
                'description' => 'Aplikasi web baru berjalan mulus di laptop seluruh developer tanpa satu pun error, tetapi langsung mengalami Fatal Error HTTP 500 begitu dideploy ke server production. Manajer proyek menyalahkan framework Laravel yang dianggap tidak stabil.',
                'initial_assumption' => 'Manajer menduga framework Laravel bermasalah atau source code yang di-upload ke server corrupt.',
                'mission_objectives' => [
                    'Bandingkan lingkungan lokal developer dengan lingkungan production.',
                    'Temukan ketidakcocokan spesifik yang menyebabkan aplikasi crash di production.',
                    'Buat langkah perbaikan dan pencegahan agar kejadian tidak terulang.',
                ],
                'hidden_facts' => [
                    'Aplikasi dibangun menggunakan framework Laravel versi terbaru.',
                    'Seluruh laptop developer menjalankan runtime PHP 8.4.',
                    'Server production ternyata masih dikonfigurasi menjalankan PHP 8.2.',
                    'Checksum dan commit Git pada source code lokal dan production identik 100%.',
                    'Koneksi database, kredensial .env, dan migrasi tabel di server production berjalan normal.',
                    'Beberapa package dependency Composer terbaru mensyaratkan minimal PHP 8.4 serta sintaks PHP 8.3/8.4.',
                    'Log error server mencatat "ParseError / Composer detected issues in your platform: requires PHP >= 8.4".',
                    'Pengujian ulang di staging container dengan PHP 8.4 berhasil 100% tanpa error.',
                    'Pipeline deployment sebelumnya tidak menjalankan check platform PHP sebelum memindahkan file.',
                    'Developer menggunakan fitur bahasa PHP 8.4 (seperti property hooks / syntax baru) di salah satu service.',
                ],
                'evidence' => [
                    'Local PHP version: 8.4 vs Production PHP version: 8.2.',
                    'Hash commit Git identik dan koneksi DB production normal.',
                    'Testing pada server dengan PHP 8.4 langsung sukses.',
                ],
                'false_leads' => [
                    'File .env production baru diedit kemarin (ternyata hanya mengubah APP_NAME).',
                    'SSL certificate server sempat diperpanjang minggu lalu.',
                ],
                'special_hints' => [
                    'Jika source code sama persis namun perilaku berbeda antar mesin, periksa versi runtime dan environment server.',
                    'Cek error log Composer platform check atau PHP ParseError di production.',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Admin server baru melaporkan bahwa panel hosting masih mengatur domain aplikasi tersebut pada handler PHP 8.2, sementara server sebenarnya sudah mendukung PHP 8.4.',
                ],
                'root_cause' => 'Ketidaksesuaian versi PHP antara environment development (PHP 8.4) dan server production (PHP 8.2), sementara source code dan dependency membutuhkan PHP 8.4.',
                'recommended_solutions' => [
                    'Upgrade runtime PHP di server production ke versi PHP 8.4 agar sesuai dengan requirement aplikasi.',
                    'Kunci versi PHP di composer.json ("php": "^8.4") dan aktifkan check versi di CI/CD pipeline.',
                    'Gunakan Docker atau container staging dengan spesifikasi identik sebelum rilis ke production.',
                ],
                'validation_method' => 'Jalankan php -v di production untuk memastikan PHP 8.4 aktif, bersihkan cache aplikasi, dan jalankan smoke test end-to-end hingga seluruh endpoint mengembalikan HTTP 200.',
                'default_duration' => 20,
            ],
            [
                'title' => 'Case 04 — The Empty Restaurant',
                'category' => 'Operations & Service',
                'difficulty' => 'medium',
                'description' => 'Sebuah restoran yang biasanya penuh sesak kehilangan 70% pelanggan tetapnya dalam sebulan terakhir. Pemilik restoran yakin penyebabnya adalah mereka belum punya aplikasi pemesanan mobile dan ingin mengeluarkan dana besar untuk membuatnya.',
                'initial_assumption' => 'Pemilik mengira pelanggan kabur karena restoran belum memiliki aplikasi pemesanan online.',
                'mission_objectives' => [
                    'Temukan alasan sebenarnya pelanggan berhenti datang ke restoran.',
                    'Buktikan mengapa membuat aplikasi pemesanan justru bisa memperburuk kondisi saat ini.',
                    'Susun solusi operasional yang tepat sasaran.',
                ],
                'hidden_facts' => [
                    'Jumlah pengunjung awal yang datang ke pintu restoran sebenarnya tetap tinggi pada awal bulan, namun mereka pulang atau tidak kembali lagi.',
                    'Waktu tunggu makanan tersaji melonjak dari rata-rata 12 menit menjadi 55 menit.',
                    'Survei ulasan Google Maps 30 hari terakhir rata-rata mengeluhkan "makanan terlalu lama keluar dan sering dingin".',
                    'Rasa dan resep makanan dari pelanggan yang sudah makan dinilai tetap enak.',
                    'Harga menu restoran tidak mengalami kenaikan dalam 6 bulan terakhir.',
                    'Dua dari tiga koki utama restoran mengundurkan diri sebulan lalu karena kelelahan.',
                    'Manajemen belum merekrut pengganti sehingga hanya tersisa 1 koki menangani 40 meja sekaligus.',
                    'Pesanan ojek online tetap ramai masuk, semakin menumpuk antrean di dapur.',
                    'Pelayan di ruang makan cukup banyak dan cepat melayani, tetapi tertahan menunggu dapur.',
                    'Jika aplikasi pemesanan baru diluncurkan tanpa menambah kapasitas dapur, antrean masak akan makin panjang.',
                ],
                'evidence' => [
                    'Dua dari tiga koki resign bulan lalu, menyisakan 1 koki.',
                    'Waktu tunggu sajian naik dari 12 menit menjadi 55 menit.',
                    'Keluhan utama pelanggan di ulasan adalah lamanya penyajian, bukan tidak adanya aplikasi.',
                ],
                'false_leads' => [
                    'Kompetitor di seberang jalan baru saja mengecat ulang tokonya dan memasang neon box.',
                    'Menu minuman baru yang dirilis bulan lalu kurang laku.',
                ],
                'special_hints' => [
                    'Amati alur dari pelanggan datang hingga makanan tiba di meja: titik mana yang mengalami penumpukan?',
                    'Cari tahu perubahan SDM di bagian produksi/dapur dalam 1 bulan terakhir.',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Kepala pelayan melaporkan 15 tamu baru saja membatalkan pesanan dan pergi hari ini setelah menunggu lebih dari 45 menit karena dapur kewalahan.',
                ],
                'root_cause' => 'Kapasitas produksi dapur menurun drastis setelah dua dari tiga koki mengundurkan diri, menyebabkan waktu tunggu makanan melonjak menjadi ~55 menit dan pelanggan kecewa.',
                'recommended_solutions' => [
                    'Rekrut segera 2 koki baru atau asisten dapur terlatih dan sederhanakan sementara menu yang proses masaknya terlalu lama.',
                    'Evaluasi beban kerja serta kompensasi tim dapur agar tidak terjadi burnout.',
                    'Atur estimasi waktu tunggu secara transparan kepada tamu sebelum memesan.',
                ],
                'validation_method' => 'Pantau metrik average ticket time (waktu pesan hingga saji) agar kembali di bawah 15 menit dan ukur tingkat repeat customer serta rating ulasan dalam 30 hari.',
                'default_duration' => 20,
            ],
            [
                'title' => 'Case 05 — The Campus Internet',
                'category' => 'Networking',
                'difficulty' => 'medium',
                'description' => 'Koneksi internet di gedung kampus sangat lambat hingga tidak bisa membuka halaman web pada malam hari (pukul 19.00–22.00), padahal pada dashboard ISP total bandwidth kampus masih tersisa lebih dari 65%. Admin berencana menambah paket bandwidth ISP.',
                'initial_assumption' => 'Admin jaringan berasumsi langganan bandwidth internet dari ISP kurang besar dan harus upgrade paket bulanan.',
                'mission_objectives' => [
                    'Identifikasi mengapa bandwidth 65% tersisa namun mahasiswa tidak bisa mengakses internet.',
                    'Temukan bottleneck sebenarnya pada topologi jaringan lokal kampus.',
                    'Rancang solusi arsitektur jaringan dan cara pembuktiannya.',
                ],
                'hidden_facts' => [
                    'Total kapasitas bandwidth fiber optic kampus adalah 1 Gbps dan pemakaian malam hari hanya ~320 Mbps (32%).',
                    'Koneksi menggunakan kabel LAN langsung di ruang server berjalan dengan kecepatan penuh 900 Mbps.',
                    'Keluhan lambat hanya dialami mahasiswa yang menggunakan WiFi di area lobi, kantin malam, dan selasar lantai 2.',
                    'Terdapat 280 mahasiswa yang aktif belajar kelompok di area tersebut pada malam hari, masing-masing membawa laptop dan HP (total ~520 perangkat).',
                    'Seluruh 520 perangkat di area itu hanya dilayani oleh 2 buah Access Point (AP) kelas rumahan/SOHO.',
                    'Satu Access Point standar hanya mampu menangani sekitar 30–40 koneksi aktif secara stabil sebelum mengalami airtime contention.',
                    'Ping ke gateway lokal melalui WiFi melonjak dari 2 ms menjadi 1.800 ms + packet loss 35% pada jam malam.',
                    'Di ruang kelas lain yang kosong pada malam hari, WiFi tetap sangat cepat.',
                    'Channel WiFi kedua AP menggunakan frekuensi 2.4 GHz yang saling tumpang tindih (channel interference).',
                    'Menggandakan bandwidth ISP dari 1 Gbps menjadi 2 Gbps tidak berpengaruh karena sumbatan ada pada Access Point WiFi lokal.',
                ],
                'evidence' => [
                    'Bandwidth ISP terpakai hanya 32% (68% masih kosong) dan tes kabel LAN tembus 900 Mbps.',
                    '~520 perangkat terkumpul hanya pada 2 Access Point kapasitas kecil.',
                    'Packet loss 35% dan ping ke router lokal melonjak 1.800 ms saat malam.',
                ],
                'false_leads' => [
                    'Ada mahasiswa yang mengunduh update sistem operasi 4 GB (tetapi total trafik tetap di bawah 35%).',
                    'Lampu indikator modem ISP sempat berkedip kuning kemarin pagi.',
                ],
                'special_hints' => [
                    'Bandingkan kualitas koneksi lewat kabel LAN langsung vs koneksi lewat WiFi pada jam yang sama.',
                    'Hitung rasio jumlah perangkat aktif malam hari dengan jumlah Access Point di area berkumpul mahasiswa.',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Hasil ping internal menunjukkan koneksi dari laptop mahasiswa ke router lokal di gedung sudah mengalami 35% packet loss bahkan sebelum paket data keluar ke ISP!',
                ],
                'root_cause' => 'Kepadatan perangkat (~520 device) melampaui kapasitas jumlah dan spesifikasi Access Point lokal (hanya 2 AP), menyebabkan tabrakan sinyal (airtime contention) dan packet loss.',
                'recommended_solutions' => [
                    'Tambah Access Point kelas enterprise high-density dengan dukungan dual-band 5 GHz & WiFi 6 secara merata di area padat.',
                    'Atur pembagian channel non-overlapping dan aktifkan band steering ke 5 GHz serta limitasi klien per AP.',
                    'Lakukan survei heatmap WiFi di jam sibuk malam hari.',
                ],
                'validation_method' => 'Lakukan stress test pada jam 19.00–21.00; pastikan jumlah klien per AP < 45 perangkat, packet loss ke gateway < 1%, dan ping lokal stabil < 15 ms.',
                'default_duration' => 25,
            ],
            [
                'title' => 'Case 06 — The Failed Exam',
                'category' => 'Education & System',
                'difficulty' => 'medium',
                'description' => 'Dalam ujian tengah semester mata kuliah Pemrograman Web, 80% mahasiswa mendapat nilai di bawah standar kelulusan. Dosen menyimpulkan angkatan tahun ini malas belajar dan tidak serius mengikuti perkuliahan.',
                'initial_assumption' => 'Pengajar beranggapan mayoritas mahasiswa malas belajar, jarang berlatih, dan meremehkan ujian.',
                'mission_objectives' => [
                    'Periksa kesesuaian antara proses pembelajaran, kehadiran, dan soal ujian.',
                    'Temukan akar masalah mengapa 80% mahasiswa yang aktif pun ikut gagal.',
                    'Usulkan langkah perbaikan yang adil bagi sistem pembelajaran dan evaluasi.',
                ],
                'hidden_facts' => [
                    'Data absensi menunjukkan tingkat kehadiran mahasiswa di kelas mencapai 92% sepanjang semester.',
                    'Rata-rata nilai tugas praktik mingguan mahasiswa adalah 84 dari 100.',
                    'Mahasiswa yang biasanya selalu meraih peringkat 5 besar nilai tugas juga gagal di ujian ini.',
                    'Silabus kelas dan 6 pertemuan awal hanya mengajarkan HTML, CSS, dan dasar JavaScript DOM.',
                    'Soal ujian akhir bagian bobot terbesar (70% nilai) ternyata menanyakan implementasi backend REST API, otentikasi JWT, dan query SQL lanjutan.',
                    'Materi backend REST API dan SQL baru dijadwalkan untuk diajarkan SETELAH UTS (pertemuan 9–14).',
                    'Dosen menggunakan bank soal ujian lama dari kurikulum tahun lalu tanpa memeriksa urutan silabus semester ini.',
                    'Tidak ada kisi-kisi soal ujian yang dibagikan sebelum pelaksanaan tes.',
                    'Mayoritas mahasiswa berhasil menjawab benar 95% pada 30% soal yang memang tentang HTML/CSS.',
                    'Waktu pengerjaan ujian cukup (90 menit), namun mahasiswa tidak pernah menerima materi untuk 70% soal tersebut.',
                ],
                'evidence' => [
                    'Kehadiran kelas 92% dan rata-rata nilai tugas praktik 84/100.',
                    '70% bobot soal ujian menanyakan topik REST API & SQL yang baru dijadwalkan pasca-UTS.',
                    'Pada soal materi yang sudah diajarkan (HTML/CSS), mahasiswa menjawab benar 95%.',
                ],
                'false_leads' => [
                    'Ujian dilaksanakan pukul 08.00 pagi setelah libur panjang.',
                    'Ada 3 mahasiswa yang terlambat 10 menit masuk ruang ujian.',
                ],
                'special_hints' => [
                    'Cek perbandingan nilai mahasiswa pada bagian soal A vs bagian soal B, lalu cocokkan dengan jadwal RPS/silabus kelas.',
                    'Jika mahasiswa rajin dengan nilai tugas tinggi ikut gagal serentak, curigai instrumen ujiannya.',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Ketua kelas baru mengirim tangkapan layar RPS resmi yang membuktikan topik REST API & Database memang baru berada di silabus pertemuan minggu ke-9 hingga ke-14!',
                ],
                'root_cause' => 'Materi yang diujikan pada 70% bobot soal ujian tidak sesuai dengan materi yang telah diajarkan sebelum UTS karena dosen memakai bank soal kurikulum lama tanpa sinkronisasi silabus.',
                'recommended_solutions' => [
                    'Lakukan ujian ulang atau penyesuaian bobot dengan soal yang selaras 100% dengan RPS pertemuan 1–7.',
                    'Terapkan review silabus (blueprint soal) sebelum naskah ujian disetujui dan diterbitkan.',
                    'Gunakan latihan evaluasi formatif sebelum ujian besar untuk memvalidasi kesiapan materi.',
                ],
                'validation_method' => 'Audit peta soal (test blueprint) terhadap log materi yang diajarkan tiap pertemuan sebelum ujian susulan/berikutnya; pastikan distribusi tingkat kelulusan kembali wajar.',
                'default_duration' => 20,
            ],
            [
                'title' => 'Case 07 — The Abandoned Application',
                'category' => 'Product & UX',
                'difficulty' => 'medium',
                'description' => 'Sebuah startup meluncurkan aplikasi manajemen keuangan yang memiliki 45 fitur super lengkap. Ribuan orang mengunduh setelah iklan besar-besaran, namun 87% pengguna langsung menutup dan meninggalkan aplikasi pada hari pertama.',
                'initial_assumption' => 'Tim product menduga fitur aplikasi masih kurang lengkap atau kompetitor menawarkan promo cashback lebih besar.',
                'mission_objectives' => [
                    'Telusuri di titik mana tepatnya 87% pengguna berhenti memakai aplikasi.',
                    'Temukan hambatan utama pengalaman pengguna (UX) di hari pertama.',
                    'Rancang ulang alur awal agar pengguna langsung merasakan nilai produk.',
                ],
                'hidden_facts' => [
                    'Biaya iklan dan jumlah unduhan harian sangat tinggi (5.000 install per hari), artinya marketing berhasil.',
                    'Crash rate aplikasi sangat rendah (99,8% crash-free), tidak ada error teknis fatal.',
                    'Data funnel analytics menunjukkan 87% user berhenti SEBELUM pernah mencatat 1 transaksi pun.',
                    'Saat pertama kali membuka aplikasi, pengguna dipaksa mengisi 14 form data diri lengkap termasuk NPWP, foto KTP, penghasilan detail, dan survei 8 halaman.',
                    'Pengguna harus menonton 6 layar tutorial wajib yang tidak bisa di-skip sebelum melihat beranda aplikasi.',
                    'Rata-rata waktu untuk melewati proses registrasi awal mencapai 9 menit sebelum bisa mencoba fitur utama.',
                    'Pada halaman beranda yang penuh 45 tombol ikon sekaligus, pengguna baru bingung tombol mana yang harus diklik pertama kali.',
                    'Ulasan bintang 1 di Play Store berisi: "Baru buka udah disuruh isi KTP & formulir panjang, ribet banget, langsung uninstall."',
                    '13% pengguna yang berhasil melewati proses registrasi ternyata aktif menggunakan aplikasi tiap hari dengan retensi tinggi.',
                    'Aplikasi kompetitor mengizinkan user langsung mencoba catat pengeluaran dalam 10 detik tanpa wajib registrasi penuh di awal.',
                ],
                'evidence' => [
                    'Funnel menunjukkan 87% drop-off terjadi saat onboarding sebelum masuk ke beranda utama.',
                    'Registrasi awal mewajibkan 14 kolom sensitif + 6 slide tutorial tanpa skip (~9 menit).',
                    'User yang berhasil melewati onboarding justru memiliki tingkat retensi harian tinggi.',
                ],
                'false_leads' => [
                    'Warna tema aplikasi menggunakan biru tua.',
                    'Ukuran file installer aplikasi adalah 38 MB.',
                ],
                'special_hints' => [
                    'Periksa data funnel dari titik Install -> Buka Aplikasi -> Selesai Daftar -> Transaksi Pertama.',
                    'Mengapa 13% yang lolos justru betah, sementara 87% kabur di 10 menit pertama?',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Hasil rekaman sesi usability test baru masuk — 9 dari 10 partisipan menutup aplikasi di menit ke-3 saat diminta mengisi formulir penghasilan dan foto identitas sebelum melihat isi aplikasi!',
                ],
                'root_cause' => 'Proses onboarding terlalu panjang, memaksa pengisian data sensitif berlebih di awal, dan tampilan awal terlalu rumit sehingga pengguna menyerah sebelum merasakan manfaat inti aplikasi.',
                'recommended_solutions' => [
                    'Terapkan onboarding progresif: izinkan pengguna langsung mencatat transaksi pertama dalam < 30 detik (guest mode / login 1 klik).',
                    'Minta data tambahan hanya secara bertahap saat fitur relevan benar-benar digunakan.',
                    'Sederhanakan layar awal dengan menonjolkan 3 aksi utama alih-alih menampilkan 45 tombol sekaligus.',
                ],
                'validation_method' => 'Lakukan A/B testing alur onboarding ringkas terhadap alur lama selama 14 hari; ukur kenaikan Day-1 Activation Rate dari 13% menjadi target > 55%.',
                'default_duration' => 25,
            ],
            [
                'title' => 'Case 08 — The Late Delivery',
                'category' => 'Logistics & Systems',
                'difficulty' => 'hard',
                'description' => 'Perusahaan ekspedisi menerima keluhan paket selalu terlambat sampai ke pelanggan. Manajemen sudah menambah jumlah kurir pengantar lapangan hingga 2 kali lipat, tetapi waktu pengiriman tetap saja terlambat.',
                'initial_assumption' => 'Manajemen yakin keterlambatan terjadi karena kurangnya jumlah kurir pengantar di jalan.',
                'mission_objectives' => [
                    'Petakan seluruh rantai proses paket dari gudang masuk hingga sampai ke tangan pembeli.',
                    'Temukan titik bottleneck yang membuat penambahan kurir menjadi sia-sia.',
                    'Rancang solusi perbaikan sistemik yang menghilangkan antrean tersembunyi.',
                ],
                'hidden_facts' => [
                    'Kecepatan rata-rata kurir mengantar setelah keluar dari gudang ternyata sangat cepat (100% paket yang keluar pagi selesai diantar hari itu).',
                    'Sebagian besar kurir baru justru duduk menganggur selama 4 jam di pagi hari menunggu paket siap dibawa.',
                    'Paket yang masuk dari penjual di malam hari baru bisa keluar dari gudang 2 hari kemudian.',
                    'Seluruh paket dari 15 kota harus masuk ke satu meja sortir manual di gudang pusat.',
                    'Proses sortir alamat masih dilakukan dengan membaca label kertas satu per satu secara manual oleh 4 petugas.',
                    'Jumlah paket masuk naik dari 3.000 menjadi 12.000 paket/hari, sementara kapasitas sortir manual maksimal hanya 4.500 paket/hari.',
                    'Terdapat tumpukan 7.500 paket yang belum tersortir menumpuk di lantai gudang setiap hari.',
                    'Sekitar 12% label alamat tulisan tangan kurir/penjual buram sehingga petugas harus menelepon atau menebak kecamatan tujuan.',
                    'Tidak ada barcode routing zona otomatis pada resi pengiriman.',
                    'Menambah jumlah kurir lapangan tidak mempercepat laju keluar gudang karena kurir tidak boleh mengambil paket sebelum disortir per rute.',
                ],
                'evidence' => [
                    'Kurir menganggur 4 jam setiap pagi menunggu paket selesai disortir.',
                    'Kapasitas sortir manual maksimal 4.500 paket/hari vs paket masuk 12.000 paket/hari.',
                    'Penumpukan terjadi di dalam gudang pusat, bukan di jalanan pengantaran.',
                ],
                'false_leads' => [
                    'Harga BBM sempat naik tipis awal bulan.',
                    'Ada 2 motor operasional kurir yang sedang diservis rutin.',
                ],
                'special_hints' => [
                    'Jika menambah kurir membuat mereka menganggur di pagi hari, masalah bukan di tahap pengantaran terakhir.',
                    'Hitung kapasitas harian setiap tahap sebelum paket berada di tas kurir.',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Kepala gudang baru mengirim foto bahwa rak gudang sudah penuh sesak dengan 8.000 paket yang belum terbagi ke rute kurir, sementara 20 kurir baru sedang menunggu di parkiran!',
                ],
                'root_cause' => 'Proses sortir barang manual di gudang menjadi bottleneck utama (kapasitas 4.500 vs volume masuk 12.000/hari) sebelum paket dapat didistribusikan ke kurir.',
                'recommended_solutions' => [
                    'Terapkan sistem resi berkode batang (barcode) otomatis dengan kode zona/kecamatan tercetak jelas.',
                    'Pasang conveyor & scanner sortir semi-otomatis atau perluas stasiun sortir terdesentralisasi.',
                    'Pisahkan jalur sortir prioritas/instan dan tambah petugas sortir pada shift malam sesuai volume masuk.',
                ],
                'validation_method' => 'Pantau metrik warehouse dwell time (lama paket berada di gudang) agar turun di bawah 4 jam dan pastikan zero backlog pada akhir shift sortir pagi.',
                'default_duration' => 25,
            ],
            [
                'title' => 'Case 09 — The Smart Farm Failure',
                'category' => 'IoT & Hardware',
                'difficulty' => 'hard',
                'description' => 'Sebuah proyek pertanian pintar (Smart Farm) mengalami gagal panen karena ratusan tanaman mengering dan mati, padahal dashboard monitoring menunjukkan sistem penyiraman otomatis "AKTIF & OPTIMAL" setiap hari.',
                'initial_assumption' => 'Tim agronomi menduga jenis bibit atau pupuk yang dibeli berkualitas buruk atau terkena hama.',
                'mission_objectives' => [
                    'Evaluasi keandalan sistem otomatisasi IoT dibanding kondisi fisik tanaman di lapangan.',
                    'Temukan mengapa dashboard menampilkan kondisi optimal sementara tanaman mati kekeringan.',
                    'Susun solusi teknis & failsafe agar sistem tidak tertipu oleh pembacaan datanya sendiri.',
                ],
                'hidden_facts' => [
                    'Pemeriksaan laboratorium mengonfirmasi bibit unggul, nutrisi pupuk bagus, dan bebas dari serangan hama maupun penyakit.',
                    'Tanah di pot dan bedengan tanaman saat disentuh langsung ternyata kering kerontang seperti gurun.',
                    'Log sistem mencatat pompa air hanya menyala rata-rata 2 detik per hari karena aturan otomatis membaca "kelembapan tanah sudah 82%".',
                    'Sistem penyiraman otomatis diprogram hanya menyiram jika pembacaan sensor kelembapan turun di bawah 45%.',
                    'Sensor kelembapan tanah yang digunakan adalah tipe kapasitif murah yang tidak pernah dikalibrasi sejak dipasang 8 bulan lalu.',
                    'Permukaan probe sensor mengalami korosi dan penumpukan kerak mineral pupuk sehingga terus mengirim sinyal tegangan tinggi (dibaca sistem sebagai "basah 82%").',
                    'Dashboard hanya menampilkan angka dari sensor tanpa kamera visual atau sensor aliran air (flow meter).',
                    'Pompa air, pipa, dan tangki cadangan air sebenarnya dalam kondisi 100% berfungsi normal saat dites manual.',
                    'Saat sensor baru yang sudah dikalibrasi dicoba pada tanah yang sama, pembacaan langsung turun ke angka asli 11%.',
                    'Tidak ada mekanisme sanity check atau alert jika volume air yang keluar dari pompa hampir 0 liter selama berhari-hari.',
                ],
                'evidence' => [
                    'Fisik tanah kering kerontang (11% kelembapan asli), tetapi sensor terbaca 82% akibat kerak/korosi dan tidak terkalibrasi.',
                    'Pompa, pipa, tangki air, bibit, dan pupuk semuanya normal.',
                    'Log pompa air hanya aktif 2 detik/hari karena tertipu angka sensor.',
                ],
                'false_leads' => [
                    'Suhu ruangan greenhouse naik 1 derajat saat siang hari.',
                    'Koneksi WiFi mikrokontroler sempat putus 5 menit seminggu lalu.',
                ],
                'special_hints' => [
                    'Bandingkan apa yang dilaporkan dashboard ("kelembapan 82%") dengan kondisi fisik sebenarnya saat menyentuh tanah.',
                    'Periksa seberapa sering dan berapa liter air benar-benar dipompa ke tanaman setiap hari.',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Teknisi lapangan baru mencabut sensor tanah ke udara terbuka yang kering — dan layar monitoring tetap menunjukkan kelembapan tanah 79%!',
                ],
                'root_cause' => 'Sensor kelembapan tanah mengalami korosi dan tidak pernah dikalibrasi sehingga menghasilkan pembacaan palsu (82% saat tanah kering), membuat sistem otomatis tidak pernah menyalakan pompa air.',
                'recommended_solutions' => [
                    'Ganti dengan sensor tahan korosi industrial-grade dan lakukan jadwal kalibrasi berkala setiap bulan.',
                    'Pasang flow meter pada pipa pompa serta aturan failsafe (alert otomatis jika pemakaian air harian di bawah ambang minimum).',
                    'Gunakan lebih dari satu sensor per zona (redundansi voting) untuk mendeteksi anomali pembacaan sensor.',
                ],
                'validation_method' => 'Uji sensor dengan 2 titik referensi (udara kering & air jenuh), pasang water flow sensor, dan pastikan volume penyiraman harian sesuai kebutuhan tanaman selama 14 hari.',
                'default_duration' => 25,
            ],
            [
                'title' => 'Case 10 — The Endless Project',
                'category' => 'Software Engineering & Management',
                'difficulty' => 'hard',
                'description' => 'Sebuah proyek pengembangan aplikasi internal perusahaan sudah terlambat 6 bulan dari jadwal awal. Manajemen sudah menambah jumlah programmer dari 4 orang menjadi 12 orang, namun tanggal rilis justru semakin mundur.',
                'initial_assumption' => 'Manajemen menganggap para programmer bekerja lambat dan masih kekurangan tenaga developer.',
                'mission_objectives' => [
                    'Analisis mengapa penambahan 3x lipat jumlah programmer tidak mempercepat rilis aplikasi.',
                    'Identifikasi pola kerusakan proses manajemen proyek (scope, komunikasi, prioritas).',
                    'Susun strategi penyelamatan proyek agar bisa segera dirilis ke pengguna.',
                ],
                'hidden_facts' => [
                    'Seluruh fitur awal dari kontrak bulan ke-1 sebenarnya sudah selesai 100% pada bulan ke-3.',
                    'Setiap kali demo mingguan, stakeholder dari berbagai divisi menambahkan 5–8 permintaan fitur baru tanpa menghapus jadwal rilis.',
                    'Total jumlah fitur bertambah dari 12 fitur inti menjadi 67 fitur (scope creep > 450%).',
                    'Tidak ada satu pun dokumen persetujuan perubahan (Change Request) atau Product Owner tunggal yang menyaring permintaan.',
                    'Seluruh 67 fitur ditandai berstatus "PRIORITAS TINGGI / URGENT" tanpa urutan mana yang wajib rilis lebih dulu.',
                    'Penambahan 8 developer baru justru menyita 50% waktu 4 developer senior untuk onboarding, menjelaskan struktur kode, dan memperbaiki merge conflict.',
                    'Persyaratan satu modul sering berubah mendadak di tengah sprint sehingga kode yang sudah selesai terpaksa ditulis ulang berulang kali.',
                    'Tim tidak memiliki definisi "Minimum Viable Product (MVP)" untuk rilis tahap pertama.',
                    'Meeting sinkronisasi harian membengkak dari 15 menit menjadi 2 jam karena terlalu banyak orang dalam satu tim tanpa pembagian modul jelas.',
                    'Log Git menunjukkan puluhan branch fitur setengah jadi yang saling bertabrakan setiap pekan.',
                ],
                'evidence' => [
                    'Scope awal 12 fitur sudah beres di bulan ke-3, namun membengkak menjadi 67 fitur karena tambahan tanpa filter.',
                    'Semua tugas berlabel "Urgent" tanpa prioritas atau batas MVP.',
                    'Penambahan 8 developer baru memicu overhead komunikasi & merge conflict.',
                ],
                'false_leads' => [
                    'Dua developer menggunakan editor kode berbeda (VS Code vs PhpStorm).',
                    'Server Git sempat di-upgrade ke paket berbayar bulan ke-4.',
                ],
                'special_hints' => [
                    'Tanyakan seberapa jauh progres fitur dari kontrak awal bulan pertama dibanding daftar fitur hari ini.',
                    'Telusuri apa yang dilakukan oleh 4 developer senior setelah 8 developer baru bergabung.',
                ],
                'breaking_news' => [
                    'BREAKING NEWS: Di tengah rapat investigasi ini, Divisi Keuangan baru saja mengirim email meminta tambahan 4 modul baru lagi yang "harus masuk sebelum peluncuran besok lusa"!',
                ],
                'root_cause' => 'Terjadi scope creep ekstrem (fitur membengkak dari 12 ke 67 tanpa kontrol), perubahan kebutuhan tanpa henti, tidak adanya prioritas MVP, serta komunikasi overhead akibat penambahan developer mendadak.',
                'recommended_solutions' => [
                    'Bekukan penambahan fitur (feature freeze) dan tetapkan 1 Product Owner yang berwenang menyaring permintaan.',
                    'Rilis segera MVP berisi fitur inti yang paling dibutuhkan pengguna, lalu masukkan fitur tambahan ke fase rilis berikutnya (sprint backlog).',
                    'Terapkan proses Change Management formal (setiap tambahan fitur wajib menggeser tanggal rilis atau mengganti fitur lain).',
                ],
                'validation_method' => 'Kunci scope MVP untuk rilis versi 1.0 dalam 2 minggu, pantau burndown chart tanpa perubahan backlog di tengah sprint, dan validasi kesuksesan rilis bersama pengguna inti.',
                'default_duration' => 30,
            ],
        ];
    }
}
