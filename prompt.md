# SPEAK UP! — Interactive Speaking Game

Buat sebuah website game edukasi interaktif bernama **SPEAK UP!** dengan tagline **"Think Fast. Speak Smart."**

Website ini bertujuan melatih kemampuan komunikasi, public speaking, improvisasi, spontanitas, basa-basi, storytelling, serta kemampuan menyampaikan informasi secara singkat, jelas, dan tepat sasaran.

Website akan digunakan dalam kegiatan kelas secara langsung, ditampilkan melalui proyektor atau layar besar, dengan seorang mentor sebagai game master yang mengendalikan permainan.

## 1. Game Modes

### 01 — Short vs Long

Pemain mendapatkan satu topik atau pertanyaan secara acak dan harus menjawabnya menggunakan dua gaya berbeda.

- **Direct Mode:** Berbicara maksimal 15 detik, langsung ke inti pembicaraan.
- **Elaboration Mode:** Berbicara selama 90 detik dengan mengembangkan pembicaraan melalui konteks, alasan, contoh, dan cerita.
- Gunakan topik yang sama untuk kedua mode.
- Durasi dapat disesuaikan oleh game master.

Contoh topik:

- Kenapa kamu memilih menjadi programmer?
- Apa tujuan hidupmu?
- Mengapa seseorang harus belajar bahasa Inggris?
- Apakah uang bisa membeli kebahagiaan?

### 02 — Forbidden Words

Pemain harus menjelaskan sebuah topik tanpa menyebutkan kata-kata yang dilarang.

Tampilkan:

- Topik pembicaraan.
- 3–5 kata terlarang.
- Countdown timer.
- Tiga nyawa.
- Tombol pengurangan nyawa untuk game master.

Contoh:

Topik: Jelaskan apa itu internet.

Kata terlarang: WiFi, jaringan, online, komputer.

Pemain kehilangan satu nyawa setiap kali menyebutkan kata terlarang.

### 03 — Awkward Situation

Pemain diberikan sebuah situasi sosial secara acak dan harus memberikan respons secara spontan.

Setiap situasi memiliki konteks, karakter lawan bicara, dan misi komunikasi.

Contoh situasi:

- Memulai percakapan dengan orang asing.
- Menolak ajakan teman tanpa menyinggung perasaannya.
- Meminta kenaikan gaji kepada atasan.
- Menghadapi pelanggan yang marah.
- Mencairkan suasana ketika percakapan terasa canggung.
- Berbasa-basi dengan seseorang yang baru dikenal.
- Mengakhiri percakapan dengan sopan.
- Memperkenalkan diri kepada seorang CEO.

Sediakan mode individu dan roleplay dua pemain.

### 04 — Random Connection

Sistem menampilkan tiga kata acak yang tidak berkaitan satu sama lain.

Pemain harus menghubungkan ketiga kata tersebut menjadi satu cerita yang masuk akal, menarik, dan memiliki alur dalam waktu 60 detik.

Contoh kombinasi:

- Laptop, pisang, roket.
- Kucing, investasi, sekolah.
- Kopi, presiden, sandal.
- Programmer, gunung, pernikahan.

Sediakan tingkat kesulitan berdasarkan jumlah kata:

- Easy: 2 kata.
- Medium: 3 kata.
- Hard: 5 kata.
- Extreme: 7 kata.

### 05 — Speaking Switch

Pemain mendapatkan satu topik dan harus terus berbicara tanpa berhenti ketika sistem mengubah gaya berbicaranya secara tiba-tiba.

Gaya berbicara yang tersedia:

- To the point.
- Basa-basi.
- Formal.
- Santai.
- Persuasif.
- Storytelling.
- Informatif.
- Emosional.
- Humor.
- Motivasi.

Setiap 10–30 detik, sistem secara otomatis mengganti instruksi gaya berbicara.

Berikan efek visual dan suara ketika instruksi berubah.

Pemain harus mempertahankan topik utama meskipun gaya berbicaranya berubah.

Sediakan pengaturan perpindahan instruksi secara otomatis maupun manual.

### 06 — Different Audience

Pemain diberikan satu topik dan harus menjelaskannya kepada audiens yang berbeda.

Contoh topik: Artificial Intelligence.

Contoh audiens:

- Anak berusia 5 tahun.
- Orang tua.
- Mahasiswa.
- Programmer.
- CEO perusahaan.
- Guru sekolah.
- Orang yang tidak memahami teknologi.
- Calon investor.

Pemain harus menyesuaikan bahasa, kosakata, analogi, dan cara penyampaiannya berdasarkan karakter audiens.

### 07 — Small Talk Challenge

Mode khusus untuk melatih kemampuan berbasa-basi dan membangun percakapan secara natural.

Dua pemain diberikan sebuah situasi dan harus mempertahankan percakapan selama waktu yang ditentukan.

Contoh situasi:

- Bertemu orang asing di lift.
- Duduk bersama seseorang di ruang tunggu.
- Bertemu CEO dalam sebuah acara.
- Bertemu teman lama setelah lima tahun.
- Berkenalan dengan seseorang saat seminar.
- Makan bersama rekan kerja baru.
- Menunggu kendaraan umum bersama orang asing.

Tambahkan tantangan acak:

- Jangan menjawab hanya dengan ya atau tidak.
- Ajukan minimal tiga pertanyaan lanjutan.
- Temukan satu kesamaan dengan lawan bicara.
- Pindahkan topik pembicaraan secara natural.
- Hindari keheningan lebih dari lima detik.
- Akhiri percakapan dengan sopan.

### 08 — Say It Better

Tampilkan sebuah kalimat yang tidak efektif, terlalu panjang, terlalu kasar, atau sulit dipahami.

Pemain harus menyampaikan ulang kalimat tersebut sesuai instruksi.

Kategori instruksi:

- Buat lebih singkat.
- Buat lebih sopan.
- Buat lebih profesional.
- Buat lebih meyakinkan.
- Buat lebih mudah dipahami.
- Buat lebih menarik.

Contoh:

Kalimat awal: "Jadi sebenarnya mungkin menurut saya kalau misalkan kita lihat dari kondisi yang sekarang ini, sepertinya kita harus mencoba mencari solusi lain."

Instruksi: Sampaikan dalam satu kalimat secara tegas dan langsung ke inti.

## 2. Sistem Permainan

Buat alur permainan sebagai berikut:

1. Game master membuka website.
2. Membuat sesi permainan.
3. Memasukkan daftar nama pemain atau kelompok.
4. Memilih game mode.
5. Sistem memilih pemain secara acak.
6. Sistem memberikan tantangan secara acak.
7. Pemain mendapatkan waktu persiapan.
8. Countdown dimulai.
9. Pemain menyelesaikan tantangan.
10. Game master memberikan penilaian.
11. Poin masuk ke leaderboard.
12. Permainan dilanjutkan ke pemain berikutnya.

Pastikan sistem dapat menghindari pemilihan pemain dan tantangan yang sama secara berulang sebelum semua pilihan mendapatkan giliran.

## 3. Game Master Controls

Sediakan kontrol permainan:

- Start.
- Pause.
- Resume.
- Reset timer.
- Next challenge.
- Skip challenge.
- Random player.
- Change game mode.
- Change difficulty.
- Add time.
- Reduce time.
- End round.
- Fullscreen.
- Toggle sound.
- Keyboard shortcuts.

Game master dapat mengatur durasi persiapan, durasi berbicara, jumlah ronde, dan tingkat kesulitan.

## 4. Sistem Penilaian

Gunakan sistem penilaian manual oleh game master.

Aspek penilaian:

- Kejelasan berbicara: 20 poin.
- Kelancaran berbicara: 20 poin.
- Kesesuaian instruksi: 20 poin.
- Struktur pembicaraan: 20 poin.
- Kepercayaan diri: 20 poin.

Total maksimal 100 poin per ronde.

Sesuaikan indikator penilaian berdasarkan game mode. Contohnya, pada Small Talk Challenge, nilai kemampuan mendengarkan, mengembangkan percakapan, dan merespons lawan bicara.

Sediakan leaderboard individu dan kelompok, riwayat permainan, total poin, rata-rata nilai, dan jumlah tantangan yang diselesaikan.

## 5. Challenge Library

Buat kumpulan tantangan dengan minimal 50 data yang bervariasi untuk setiap game mode.

Kategori topik:

- Kehidupan sehari-hari.
- Pendidikan.
- Teknologi.
- Programming.
- Karier.
- Bisnis.
- Hubungan sosial.
- Opini.
- Logika.
- Humor.
- Situasi profesional.
- Pengalaman pribadi.
- Imajinasi.
- Pertanyaan filosofis.

Game master dapat menambahkan, mengedit, menghapus, mengaktifkan, dan menonaktifkan tantangan.

Pastikan setiap tantangan memiliki variasi yang bermakna, bukan sekadar pengulangan dengan kata-kata berbeda.

## 6. Halaman Website

Buat halaman berikut:

- Home.
- Game Selection.
- Game Setup.
- Player Management.
- Gameplay.
- Round Result.
- Leaderboard.
- Challenge Library.
- Settings.

## 7. UI/UX Design

Gunakan desain modern, minimalis, playful, dan interaktif seperti sebuah game show, bukan seperti dashboard administrasi.

Design direction:

- Typography besar dan tegas.
- Background hitam atau off-white.
- Warna utama kuning sebagai aksen.
- Kontras tinggi.
- Flat design.
- Card yang sederhana.
- Ikon minimalis.
- Animasi transisi yang halus.
- Hindari gradient berlebihan.
- Hindari glassmorphism.
- Hindari elemen dekoratif yang tidak diperlukan.
- Hindari tampilan generik seperti website buatan AI.

Gameplay harus menjadi fokus utama.

Pada halaman gameplay, tampilkan:

- Nama game mode.
- Nama pemain aktif.
- Nomor ronde.
- Topik atau tantangan berukuran besar.
- Instruksi permainan.
- Countdown timer berukuran sangat besar.
- Progress indicator.
- Kontrol permainan yang tidak mengganggu fokus.

Optimalkan tampilan untuk proyektor dengan rasio 16:9 dan pastikan teks dapat terbaca dari jarak jauh.

Sediakan fullscreen presentation mode yang menyembunyikan navigasi dan kontrol yang tidak diperlukan.

## 8. Sound & Animation

Tambahkan efek suara untuk:

- Pemilihan pemain.
- Kemunculan tantangan.
- Countdown.
- Lima detik terakhir.
- Waktu habis.
- Pergantian gaya berbicara.
- Jawaban berhasil.
- Kehilangan nyawa.
- Pengumuman pemenang.

Tambahkan animasi shuffle, countdown, pergantian ronde, dan confetti untuk pemenang.

Pastikan seluruh efek suara dapat dinonaktifkan.

## 9. Technical Requirements

Gunakan:

- HTML5.
- Tailwind CSS.
- Vanilla JavaScript.
- LocalStorage untuk penyimpanan data.

Untuk versi awal, jangan gunakan backend, database eksternal, autentikasi, API berbayar, AI, maupun speech recognition.

Pastikan:

- Website dapat berjalan secara lokal.
- Seluruh fitur benar-benar berfungsi.
- Data pemain, pengaturan, tantangan, dan skor tetap tersimpan setelah refresh.
- Timer tetap akurat ketika tab browser tidak aktif.
- Tidak ada pemain atau tantangan yang berulang sebelum siklus pengacakan selesai.
- Tersedia konfirmasi sebelum menghapus data atau mereset permainan.
- Kode modular, terstruktur, reusable, dan mudah dikembangkan.
- Tidak ada tombol dummy, placeholder, atau fitur yang hanya berfungsi secara visual.
- Website responsif pada desktop, tablet, dan smartphone, dengan prioritas utama layar proyektor.

Lakukan pengujian terhadap seluruh alur permainan, timer, penyimpanan data, pengacakan, leaderboard, dan kontrol permainan sebelum menyatakan website selesai.

## 10. Product Philosophy

Website ini bukan hanya sebuah game untuk menentukan siapa yang paling pandai berbicara.

Website ini harus membantu pemain memahami kapan mereka harus berbicara secara singkat, kapan harus mengembangkan pembicaraan, bagaimana mencairkan suasana, bagaimana menghadapi situasi sosial, dan bagaimana menyesuaikan cara berkomunikasi dengan lawan bicara.

**Speaking is not about how much you say, but knowing what to say, how to say it, and when to stop.**

Bangun website yang lengkap, fungsional, menyenangkan, kompetitif, dan benar-benar layak digunakan dalam kegiatan pembelajaran secara langsung.