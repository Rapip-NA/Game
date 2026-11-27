# Build Game: THE PROBLEM DETECTIVE

**Tagline: Ask the right questions. Find the real problem.**

Buat sebuah game edukasi multiplayer berbasis web bernama **The Problem Detective** yang bertujuan melatih First Principles Thinking, Critical Thinking, Problem Solving, Root Cause Analysis, dan kemampuan mengambil keputusan berdasarkan bukti.

Game ini akan digunakan dalam kegiatan pembelajaran mahasiswa programming secara langsung di kelas.

Integrasikan game ke dalam platform game yang sudah tersedia. Pelajari terlebih dahulu struktur project, database, komponen, teknologi, dan design system yang digunakan. Jangan mengubah atau merusak game lain yang sudah tersedia.

## 1. Konsep permainan

Pemain berperan sebagai tim detektif yang ditugaskan menyelesaikan suatu permasalahan.

Setiap kasus memiliki permasalahan, dugaan awal yang belum tentu benar, informasi tersembunyi, bukti pendukung, akar permasalahan, dan kemungkinan solusi.

Pemain tidak diberikan seluruh informasi sejak awal. Mereka harus mengajukan pertanyaan investigasi untuk mendapatkan informasi, menghubungkan bukti, mengeliminasi asumsi, dan menemukan akar permasalahan.

Pemenang bukan sekadar kelompok tercepat, melainkan kelompok dengan kualitas investigasi dan argumentasi terbaik.

## 2. Role pengguna

### Game Master (Host)

- Membuat room permainan.
- Mengatur jumlah kelompok.
- Mengatur nama kelompok.
- Memilih kasus yang akan dimainkan.
- Mengatur jumlah nyawa investigasi.
- Mengatur durasi setiap fase permainan.
- Menerima pertanyaan dari kelompok secara realtime.
- Menjawab pertanyaan berdasarkan informasi rahasia kasus.
- Memberikan petunjuk tambahan.
- Mengurangi atau menambahkan nyawa.
- Membaca hasil investigasi kelompok.
- Memberikan penilaian.
- Mengontrol seluruh fase permainan.
- Menampilkan leaderboard.
- Mengakhiri atau mengulang permainan.

### Player (Detective)

- Bergabung menggunakan room code.
- Memasukkan nama pemain.
- Memilih kelompok yang tersedia.
- Membaca kasus.
- Berdiskusi dengan anggota kelompok.
- Mengirimkan pertanyaan investigasi.
- Menerima jawaban dan bukti.
- Melihat sisa nyawa investigasi.
- Mencatat hasil investigasi.
- Mengirimkan kesimpulan akhir.
- Melihat hasil penilaian dan leaderboard.

## 3. Multiplayer room

Gunakan sistem room code yang dapat dibagikan oleh host.

Default konfigurasi:

- Jumlah pemain: 17 orang.
- Jumlah kelompok: 5 kelompok.
- Team Alpha: 4 pemain.
- Team Bravo: 4 pemain.
- Team Charlie: 3 pemain.
- Team Delta: 3 pemain.
- Team Echo: 3 pemain.

Seluruh konfigurasi harus dapat diubah oleh host.

Sediakan lobby yang menampilkan daftar pemain, anggota kelompok, status kesiapan, dan jumlah pemain yang terhubung.

Sediakan QR Code untuk mempermudah pemain bergabung.

Sinkronkan seluruh aktivitas permainan secara realtime.

## 4. Game flow

### Phase 01 — Case Briefing

Durasi default: 2 menit.

Tampilkan judul kasus, deskripsi permasalahan, dugaan awal dari klien, dan misi investigasi.

Jangan tampilkan akar permasalahan atau informasi rahasia kepada pemain.

### Phase 02 — Team Discussion

Durasi default: 3 menit.

Berikan waktu kepada kelompok untuk mendiskusikan kemungkinan penyebab dan menyusun pertanyaan investigasi.

### Phase 03 — Investigation

Durasi default: 10 menit.

Setiap kelompok memiliki 10 Investigation Points atau nyawa.

Setiap pertanyaan yang dikirimkan mengurangi satu nyawa kelompok.

Pertanyaan harus berbentuk free text, bukan pilihan ganda. Pemain harus merumuskan pertanyaannya sendiri.

Pertanyaan masuk ke dashboard Game Master. Game Master dapat:

- Menjawab menggunakan informasi rahasia yang tersedia.
- Memberikan jawaban secara manual.
- Menandai pertanyaan sebagai tidak relevan.
- Menandai informasi sebagai belum diketahui.
- Menolak pertanyaan yang meminta jawaban akhir secara langsung.

Jawaban yang diberikan otomatis masuk ke Evidence Board kelompok.

Sediakan dua mode investigasi:

- Public Investigation: seluruh kelompok dapat melihat pertanyaan dan jawaban.
- Private Investigation: hanya kelompok yang bertanya dapat melihat jawabannya.

Gunakan Private Investigation sebagai mode default agar setiap kelompok benar-benar melakukan investigasi sendiri.

### Phase 04 — Final Deduction

Durasi default: 5 menit.

Setiap kelompok mengumpulkan satu laporan akhir yang berisi:

- Initial Hypothesis: dugaan awal.
- Evidence Collected: bukti yang ditemukan.
- Eliminated Possibilities: kemungkinan penyebab yang berhasil disingkirkan.
- Root Cause: akar permasalahan.
- Proposed Solution: solusi yang diusulkan.
- Validation Plan: cara membuktikan solusi berhasil.
- Confidence Level: tingkat keyakinan 0–100%.

Kelompok yang sudah mengumpulkan jawaban tidak dapat mengirim pertanyaan tambahan.

### Phase 05 — Defense

Game Master menampilkan hasil investigasi kelompok satu per satu.

Kelompok harus menjelaskan dan mempertahankan argumentasinya secara langsung di kelas.

Kelompok lain dapat mengajukan keberatan atau bantahan.

### Phase 06 — Truth Reveal

Tampilkan seluruh fakta, akar permasalahan sebenarnya, hubungan antarpetunjuk, kesalahan asumsi awal, dan rekomendasi solusi.

### Phase 07 — Leaderboard

Tampilkan hasil penilaian setiap kelompok, peringkat, jumlah pertanyaan yang digunakan, dan penghargaan khusus.

## 5. Game mechanics

### Investigation Points

Setiap kelompok memiliki 10 nyawa investigasi yang digunakan bersama oleh seluruh anggota.

- Mengirim pertanyaan: -1 nyawa.
- Membeli petunjuk khusus: -2 nyawa.
- Bonus dari Game Master: +1 nyawa.
- Nyawa habis: tidak dapat mengajukan pertanyaan lagi, tetapi tetap dapat mengumpulkan kesimpulan.

Pastikan pengurangan nyawa diproses di server secara atomik agar tidak terjadi pengurangan ganda atau penyalahgunaan ketika beberapa anggota mengirim pertanyaan bersamaan.

### Evidence Board

Sediakan papan investigasi yang berisi:

- Pertanyaan yang diajukan.
- Jawaban Game Master.
- Bukti yang ditemukan.
- Catatan kelompok.
- Hipotesis.
- Kemungkinan penyebab yang dieliminasi.

Anggota kelompok dapat berkolaborasi pada Evidence Board secara realtime.

### Confidence Level

Sediakan slider 0–100% untuk mengukur tingkat keyakinan kelompok terhadap kesimpulannya.

### Breaking News

Game Master dapat mengirimkan informasi baru secara tiba-tiba di tengah investigasi.

Tampilkan notifikasi khusus kepada pemain.

### False Lead

Setiap kasus dapat memiliki dugaan awal yang menyesatkan tetapi masuk akal.

Pemain harus membedakan asumsi dengan fakta.

### Expert Consultation

Kelompok dapat menukarkan dua nyawa untuk mendapatkan satu petunjuk khusus dari Game Master.

## 6. Scoring system

Gunakan sistem penilaian maksimal 100 poin:

- Kualitas pertanyaan investigasi: 20 poin.
- Ketepatan akar permasalahan: 30 poin.
- Kekuatan bukti pendukung: 20 poin.
- Efektivitas solusi: 20 poin.
- Efisiensi investigasi: 10 poin.

Efisiensi dihitung berdasarkan sisa nyawa, tetapi hanya diberikan apabila kesimpulan benar dan didukung bukti yang cukup.

Penilaian utama dilakukan secara manual oleh Game Master.

Sediakan penghargaan tambahan:

- Master Detective — skor tertinggi.
- Sharpest Mind — pertanyaan investigasi terbaik.
- Evidence Hunter — bukti pendukung terbaik.
- Most Efficient Team — investigasi paling efisien.
- Best Problem Solver — solusi terbaik.
- Best Defense — argumentasi terbaik.

## 7. Case library

Buat minimal 10 kasus dengan tingkat kesulitan Easy, Medium, dan Hard.

### Case 01 — The Slow Website

Permasalahan: Website membutuhkan waktu 25 detik untuk dibuka. Pemilik perusahaan ingin membeli server baru seharga Rp10 juta.

Informasi rahasia:

- CPU server 12%.
- RAM server 30%.
- Query database 0,08 detik.
- Jaringan normal.
- Ukuran halaman 32 MB.
- Gambar banner 28 MB.
- Banner diganti kemarin.
- Tanpa banner, halaman terbuka dalam 1,4 detik.

Root cause: Gambar banner tidak dioptimasi.

### Case 02 — The Missing Money

Permasalahan: Toko mengalami kerugian Rp5 juta setiap bulan. Pemilik mencurigai karyawannya mencuri uang.

Informasi rahasia:

- Uang kas sesuai transaksi.
- Tidak ditemukan bukti pencurian.
- Harga jual Rp20.000.
- Modal awal Rp15.000.
- Modal sekarang Rp22.000.
- Harga jual belum diperbarui.
- Penjualan 2.500 produk per bulan.

Root cause: Harga jual lebih rendah daripada modal.

### Case 03 — The Broken Application

Permasalahan: Aplikasi berjalan normal di laptop developer tetapi gagal di production. Manajer menyalahkan framework.

Informasi rahasia:

- Framework Laravel.
- PHP local 8.4.
- PHP production 8.2.
- Source code sama.
- Database normal.
- Dependency membutuhkan PHP 8.4.
- Pengujian menggunakan PHP 8.4 berhasil.

Root cause: Ketidaksesuaian versi PHP.

### Case 04 — The Empty Restaurant

Permasalahan: Restoran kehilangan 70% pelanggan. Pemilik ingin membuat aplikasi pemesanan.

Root cause: Kapasitas dapur menurun setelah dua dari tiga koki mengundurkan diri.

### Case 05 — The Campus Internet

Permasalahan: Internet kampus lambat pada malam hari meskipun bandwidth masih tersedia.

Root cause: Kepadatan perangkat dan keterbatasan kapasitas access point.

### Case 06 — The Failed Exam

Permasalahan: 80% mahasiswa gagal ujian. Pengajar menganggap mahasiswa malas belajar.

Root cause: Materi yang diujikan tidak sesuai dengan materi yang telah diajarkan.

### Case 07 — The Abandoned Application

Permasalahan: Aplikasi memiliki banyak fitur tetapi sebagian besar pengguna berhenti menggunakannya.

Root cause: Proses onboarding terlalu panjang dan membingungkan.

### Case 08 — The Late Delivery

Permasalahan: Pengiriman barang selalu terlambat meskipun perusahaan menambah kurir.

Root cause: Proses sortir barang menjadi bottleneck sebelum pengiriman.

### Case 09 — The Smart Farm Failure

Permasalahan: Tanaman mati meskipun sistem penyiraman otomatis berjalan setiap hari.

Root cause: Sensor kelembapan tidak dikalibrasi sehingga menghasilkan pembacaan yang keliru.

### Case 10 — The Endless Project

Permasalahan: Project selalu terlambat meskipun jumlah developer terus ditambah.

Root cause: Scope creep, perubahan kebutuhan yang tidak terkendali, dan tidak adanya prioritas pengerjaan.

Lengkapi seluruh kasus dengan minimal 10 informasi investigasi yang konsisten, petunjuk utama, informasi pengalih perhatian, kemungkinan penyebab alternatif, bukti yang membedakan penyebab, solusi, dan metode validasi.

Seluruh kasus merupakan simulasi fiktif.

## 8. Custom Case Builder

Sediakan fitur agar Game Master dapat membuat, mengedit, menduplikasi, dan menghapus kasus buatannya sendiri.

Field yang tersedia:

- Case title.
- Case description.
- Category.
- Difficulty.
- Initial assumption.
- Mission objectives.
- Hidden facts.
- Evidence.
- False leads.
- Special hints.
- Breaking news.
- Root cause.
- Recommended solutions.
- Validation method.
- Default duration.

Simpan kasus ke database agar dapat digunakan kembali pada sesi pembelajaran berikutnya.

## 9. Game Master dashboard

Buat dashboard yang menampilkan:

- Current case.
- Current phase.
- Countdown timer.
- Daftar kelompok.
- Sisa nyawa setiap kelompok.
- Pertanyaan masuk secara realtime.
- Informasi rahasia kasus.
- Quick reply berdasarkan fakta kasus.
- Manual reply.
- Status pengumpulan jawaban.
- Form penilaian.
- Kontrol fase permainan.
- Breaking News.
- Leaderboard.

Pastikan Game Master dapat menjalankan permainan dengan mudah tanpa berpindah terlalu banyak halaman.

## 10. Projector mode

Sediakan halaman khusus untuk ditampilkan melalui proyektor kelas.

Tampilkan:

- Judul permainan.
- Judul dan deskripsi kasus.
- Fase permainan.
- Countdown timer berukuran besar.
- Nama kelompok.
- Sisa nyawa.
- Status investigasi.
- Status pengumpulan.
- Leaderboard.
- Truth Reveal.

Jangan pernah menampilkan informasi rahasia Game Master pada Projector Mode.

## 11. UI/UX design

Gunakan konsep visual modern detective investigation.

Design direction:

- Dark monochrome.
- Minimalist.
- Black, white, dan neutral gray.
- Aksen merah untuk nyawa dan peringatan.
- Aksen hijau untuk keberhasilan.
- Typography yang tegas.
- Case file inspired interface.
- Evidence board.
- Countdown yang jelas.
- Animasi transisi sederhana.
- Responsive pada desktop dan mobile.

Hindari desain yang terlalu ramai, terlalu banyak gradient, efek glow berlebihan, atau elemen dekoratif yang tidak memiliki fungsi.

Utamakan keterbacaan, kecepatan, dan pengalaman bermain.

## 12. Technical requirements

- Gunakan teknologi dan struktur project yang sudah tersedia.
- Integrasikan dengan database game yang sudah ada.
- Jangan membuat project terpisah apabila tidak diperlukan.
- Gunakan reusable components.
- Implementasikan multiplayer realtime yang benar-benar berfungsi.
- Simpan riwayat permainan, pertanyaan, jawaban, hasil investigasi, dan skor.
- Pastikan pemain dapat reconnect tanpa kehilangan progres.
- Terapkan validasi dan otorisasi pada server.
- Jangan mengirimkan informasi rahasia kasus ke client pemain.
- Pastikan pemain tidak dapat mengubah nyawa, skor, timer, atau fase permainan secara ilegal.
- Gunakan server sebagai sumber kebenaran utama untuk state permainan.
- Jangan gunakan mock data untuk fitur inti multiplayer.
- Jangan merusak fitur atau game yang sudah tersedia.

## 13. Final testing

Sebelum menyatakan game selesai, lakukan pengujian terhadap:

- Pembuatan room.
- Join room.
- Pembagian kelompok.
- Sinkronisasi realtime.
- Pengiriman pertanyaan.
- Pengurangan nyawa.
- Jawaban Game Master.
- Evidence Board.
- Pergantian fase.
- Countdown timer.
- Pengumpulan kesimpulan.
- Penilaian.
- Leaderboard.
- Reconnect pemain.
- Projector Mode.
- Keamanan informasi rahasia.
- Tampilan mobile dan desktop.

Pastikan seluruh tombol dan fitur benar-benar berfungsi, bukan sekadar tampilan UI.

Bangun game yang membuat mahasiswa berpikir, berdiskusi, mempertanyakan asumsi, mencari bukti, dan mempertahankan argumentasi.

**The objective is not to guess the answer. The objective is to prove it.**