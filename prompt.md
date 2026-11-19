Buatkan web app game sosial multi-device bernama "Siapa Kembaranku?" untuk kelas kuliah (sekitar 5-20 orang), dimainkan lewat HP masing-masing, dengan satu layar host di laptop yang disambung ke proyektor.

KONSEP
Tiap pemain diberi satu logo rahasia (segitiga, kotak, hati, bulat, bintang, dll). Satu logo bisa dipegang 2 pemain atau lebih. Pemain TIDAK bisa melihat logonya sendiri, tapi pemain lain bisa melihatnya dengan scan QR code miliknya. Pemain harus saling scan dan bertanya untuk menebak siapa yang punya logo sama dengannya.

ALUR
1. Host membuka halaman host di laptop. Muncul QR code/link join untuk semua pemain.
2. Pemain buka link di HP, masukkan username, lalu masuk lobby. Server menyimpan username dan ID logo (logo diacak otomatis saat host menekan Mulai, dengan distribusi supaya tiap logo dipegang minimal 2 orang).
3. Layar HP pemain menampilkan QR code pribadinya. QR berisi ID pemain, BUKAN logonya. Layar HP pemain tidak pernah menampilkan logonya sendiri.
4. Pemain memakai tombol "Scan QR" di HP untuk memindai QR pemain lain. Setelah scan, layar HP menampilkan username dan logo orang itu (logo diambil dari server berdasarkan ID). Simpan daftar "orang yang sudah kulihat" di HP pemain.
5. Fase diskusi (timer, misal 5 menit, diatur host): pemain bebas ngobrol dan scan.
6. Fase voting: tiap pemain memilih 3 pemain lain yang menurutnya punya logo SAMA dengannya. Tidak boleh memilih diri sendiri dan tidak boleh memilih orang yang sama dua kali.
7. Hasil: server menghitung jumlah vote yang diterima tiap pemain. Pemain dengan vote terendah dikeluarkan (kick). Jika seri, keluarkan semuanya atau pilih acak (buat opsi di host). Layar host menampilkan animasi reveal: logo asli semua pemain, siapa kena kick, dan siapa yang tebakannya benar.
8. Ulangi ronde dengan pemain tersisa (logo diacak ulang) sampai tersisa 2-3 pemain sebagai pemenang. Pemain yang kena kick tetap bisa menonton.

ATURAN KEAMANAN LOGIKA
- Logo pemain tidak boleh pernah dikirim ke device pemain itu sendiri. Server hanya mengirim logo orang lain saat ada scan yang valid.
- Voting divalidasi di server.

HALAMAN YANG DIBUTUHKAN
- /host: QR join, daftar pemain realtime, kontrol (mulai, timer, mulai voting, tampilkan hasil, ronde berikutnya), layar reveal besar yang terbaca dari jauh.
- /play: input username, lobby, QR pribadi, scanner kamera, daftar orang yang sudah dilihat, form voting 3 orang, status (menunggu / kena kick / menang).

TEKNIS
- Realtime multi-device (Socket.IO).
- QR: library qrcode untuk generate, html5-qrcode untuk scan lewat kamera HP.
- Frontend mobile-first. Layar host berkontras tinggi dengan font besar.
- Logo berupa Asset yang akan diberikan.
- Semua teks dalam Bahasa Indonesia, nada santai.

Mulai dengan rencana singkat struktur data (players, rounds, scans, votes), lalu kodenya.

pasangin keamanan seperti user sendiri gak bisa liat logo dia, tapi orang bisa, dengan cara scan qr yang mereka punya di hpnya sendiri