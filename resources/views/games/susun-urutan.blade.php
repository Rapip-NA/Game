<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SUSUN URUTAN — MainYuk! Game Tim Tanpa Bicara</title>

<!-- Favicon -->
<link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('img/icon.png') }}">

<!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<style>
:root{
  box-sizing:border-box;
  padding-top:env(safe-area-inset-top,0px);
  padding-bottom:env(safe-area-inset-bottom,0px);
  --bg:#FAFAFA;
  --card:#FFFFFF;
  --text:#111111;
  --muted:#334155;
  --line:#111111;
  --acc:#0B44D6;
  --acc-t:#FFFFFF;
  --sunny:#FFD21E;
  --vermilion:#EB3B24;
  --pink:#F9B4C4;
  --ok:#22C55E;
  --warn:#EB3B24;
  color-scheme:light;
}
*{box-sizing:border-box}
body{
  margin:0;
  min-height:100vh;
  background-color:var(--bg);
  background-image:radial-gradient(#CBD5E1 1.4px, transparent 1.4px);
  background-size:22px 22px;
  color:var(--text);
  font-family:'Plus Jakarta Sans',system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;
  display:flex;
  flex-direction:column;
  align-items:center;
  overflow-x:hidden;
}

/* Top Navigation Bar */
.top-nav {
  width: 100%;
  max-width: 1100px;
  padding: 16px 16px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-weight: 800;
  font-size: 13px;
  text-decoration: none;
  color: var(--text);
  background: #FFFFFF;
  border: 2.5px solid var(--line);
  border-radius: 999px;
  padding: 6px 14px;
  box-shadow: 3px 3px 0px var(--line);
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.back-btn:hover {
  transform: translate(-2px, -2px);
  box-shadow: 5px 5px 0px var(--line);
  background: var(--sunny);
}

.back-btn:active {
  transform: translate(2px, 2px);
  box-shadow: 0px 0px 0px var(--line);
}

.audio-toggle-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-weight: 800;
  font-size: 13px;
  cursor: pointer;
  color: var(--text);
  background: #FFFFFF;
  border: 2.5px solid var(--line);
  border-radius: 999px;
  padding: 6px 14px;
  box-shadow: 3px 3px 0px var(--line);
  transition: all 0.15s ease;
}

.audio-toggle-btn:hover {
  transform: translate(-2px, -2px);
  box-shadow: 5px 5px 0px var(--line);
}

main{
  width:100%;
  max-width:1100px;
  padding:16px 16px 40px;
  display:flex;
  flex-direction:column;
  gap:18px;
  align-items:center;
  text-align:center;
  position:relative;
}

/* Judul & Doodle Header */
.header-wrap{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:12px;
  margin-top:4px;
}
h1{
  margin:0;
  font-family:'Luckiest Guy',cursive;
  font-size:clamp(34px,6.5vw,56px);
  letter-spacing:0.04em;
  line-height:1;
  color:var(--text);
  text-shadow:3px 3px 0px var(--sunny);
}
h1 span.blue{color:var(--acc)}
h2{
  margin:0;
  font-family:'Luckiest Guy',cursive;
  font-size:clamp(26px,5vw,40px);
  letter-spacing:0.03em;
  color:var(--text);
}
p{
  margin:0;
  color:var(--muted);
  line-height:1.5;
  font-weight:600;
}

/* Kartu Neo-Brutalism */
.card{
  background:var(--card);
  border:3px solid var(--line);
  border-radius:28px;
  padding:26px;
  width:100%;
  box-shadow:7px 7px 0px var(--line);
  position:relative;
}

/* Kartu Notepad Bersih Tanpa Garis-Garis untuk Layar Mulai */
.notepad-card{
  background-color:#FFFFFF;
}

/* Selotip Stiker */
.tape{
  width:76px;
  height:18px;
  background:var(--vermilion);
  border:2px solid var(--line);
  position:absolute;
  top:-10px;
  left:50%;
  transform:translateX(-50%) rotate(-2deg);
}

.hidden{display:none!important}

/* Tombol & Select Neo-Brutalism */
button,select{
  font-family:'Plus Jakarta Sans',sans-serif;
  font-weight:800;
  border-radius:14px;
  border:3px solid var(--line);
  background:#FFFFFF;
  color:var(--text);
  padding:12px 20px;
  cursor:pointer;
  box-shadow:4px 4px 0px var(--line);
  transition:transform .12s ease, box-shadow .12s ease;
}
button:hover,select:hover{
  transform:translate(-2px,-2px);
  box-shadow:6px 6px 0px var(--line);
}
button:active{
  transform:translate(3px,3px);
  box-shadow:0px 0px 0px var(--line);
}
button.p{
  background:var(--acc);
  color:var(--acc-t);
}
button.sec{
  background:var(--sunny);
  color:var(--text);
}

.row{
  display:flex;
  gap:12px;
  flex-wrap:wrap;
  justify-content:center;
  align-items:center;
}

.intro-banner{
  background:var(--sunny);
  border:2.5px solid var(--line);
  border-radius:16px;
  padding:12px 16px;
  color:var(--text);
  font-size:15px;
  box-shadow:3px 3px 0px var(--line);
  margin-bottom:14px;
}

ol.rules{
  text-align:left;
  color:var(--text);
  line-height:1.45;
  margin:14px 0 22px;
  padding-left:26px;
  font-family:'Patrick Hand',cursive;
  font-size:21px;
  display:grid;
  gap:8px;
}
ol.rules li::marker{
  color:var(--vermilion);
  font-weight:900;
}

.pill{
  display:inline-block;
  padding:6px 18px;
  border-radius:999px;
  background:var(--sunny);
  color:var(--text);
  border:2.5px solid var(--line);
  font-family:'Patrick Hand',cursive;
  font-weight:700;
  font-size:19px;
  box-shadow:3px 3px 0px var(--line);
  transform:rotate(-1deg);
}

.badge{
  display:inline-block;
  padding:5px 14px;
  border-radius:999px;
  background:var(--pink);
  color:var(--text);
  border:2.5px solid var(--line);
  font-size:13px;
  font-weight:800;
  box-shadow:3px 3px 0px var(--line);
  text-transform:uppercase;
}

.select-group{
  display:flex;
  align-items:center;
  gap:8px;
  background:var(--pink);
  padding:6px 12px;
  border-radius:16px;
  border:2.5px solid var(--line);
  box-shadow:3px 3px 0px var(--line);
}
.select-group label{
  color:var(--text);
  font-weight:800;
  font-size:14px;
}
.select-group select{
  padding:8px 12px;
  border-width:2px;
  box-shadow:2px 2px 0px var(--line);
}

/* Tampilan Layar Besar Tema (Cobalt Pop-Art Banner) */
#themeScreen{
  background:var(--acc);
  color:#FFFFFF;
}
#themeScreen p{
  color:#DBEAFE;
}
.theme-display-box{
  margin:24px 0;
  padding:36px 22px;
  background:var(--sunny);
  border:3px solid var(--line);
  border-radius:24px;
  box-shadow:6px 6px 0px var(--line);
  transform:rotate(-0.8deg);
}
.theme-title{
  font-family:'Luckiest Guy',cursive;
  font-size:clamp(32px,6.5vw,64px);
  color:var(--text);
  letter-spacing:0.03em;
  text-transform:uppercase;
  line-height:1.15;
  text-shadow:3px 3px 0px #FFFFFF;
}

/* Tampilan Single QR Code */
.single-qr-wrapper{
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:12px;
  margin:18px 0;
}
.qr-frame{
  background:#FFFFFF;
  padding:20px;
  border:3px solid var(--line);
  border-radius:22px;
  box-shadow:6px 6px 0px var(--acc);
  line-height:0;
  display:inline-block;
}
.qr-badge-num{
  font-family:'Luckiest Guy',cursive;
  font-size:clamp(32px,5vw,48px);
  color:var(--vermilion);
  text-shadow:2px 2px 0px var(--sunny);
}
.qr-inst{
  font-family:'Patrick Hand',cursive;
  font-size:clamp(20px,2.8vw,25px);
  font-weight:700;
  color:var(--text);
}
.qr-url-hint{
  font-size:13px;
  font-weight:700;
  color:var(--muted);
  word-break:break-all;
  max-width:500px;
  margin-top:4px;
  background:#F1F5F9;
  padding:6px 12px;
  border-radius:10px;
  border:2px dashed var(--line);
}

/* Tampilan Kartu Peserta (Saat di-scan dari HP) */
.clue-card{
  max-width:540px;
  margin:10px auto;
  padding:30px 22px;
  border:3px solid var(--line);
  border-radius:26px;
  background:#FFFFFF;
  box-shadow:8px 8px 0px var(--acc);
  position:relative;
}
.clue-team-pill{
  font-family:'Luckiest Guy',cursive;
  font-size:20px;
  letter-spacing:1px;
  background:var(--acc);
  color:#FFFFFF;
}
.clue-role{
  font-family:'Patrick Hand',cursive;
  font-size:26px;
  font-weight:700;
  color:var(--text);
  margin-top:10px;
}
.clue-word-box{
  margin:22px 0;
  padding:24px 16px;
  background:var(--sunny);
  border-radius:20px;
  border:3px solid var(--line);
  box-shadow:5px 5px 0px var(--line);
}
.clue-word{
  font-family:'Luckiest Guy',cursive;
  font-size:clamp(30px,7.5vw,46px);
  letter-spacing:0.03em;
  color:var(--text);
  line-height:1.2;
}
.clue-warning{
  background:var(--vermilion);
  color:#FFFFFF;
  border:2.5px solid var(--line);
  border-radius:14px;
  padding:12px;
  font-weight:800;
  font-size:14px;
  margin-top:16px;
  box-shadow:3px 3px 0px var(--line);
}

/* Mode Susun & Timer */
#play{
  background:var(--sunny);
}
.timer{
  font-family:'Luckiest Guy',cursive;
  font-size:clamp(74px,20vw,175px);
  font-variant-numeric:tabular-nums;
  line-height:1;
  color:var(--acc);
  -webkit-text-stroke:2.5px var(--line);
  text-shadow:6px 6px 0px #FFFFFF;
  margin:14px 0;
}
.timer.low{
  color:var(--vermilion);
  animation:shakeTimer 0.4s infinite alternate;
}
@keyframes shakeTimer{
  from{transform:scale(1) rotate(-1deg)}
  to{transform:scale(1.04) rotate(1deg)}
}
.big{
  font-family:'Luckiest Guy',cursive;
  font-size:clamp(30px,7vw,56px);
  color:var(--vermilion);
  text-shadow:2px 2px 0px #FFFFFF;
}

/* Jawaban & Skor */
.ord{
  display:flex;
  gap:12px;
  flex-wrap:wrap;
  justify-content:center;
  margin:18px 0;
}
.ord div{
  background:var(--acc);
  color:#FFFFFF;
  border:3px solid var(--line);
  padding:12px 16px;
  border-radius:16px;
  font-weight:800;
  font-size:clamp(15px,2.8vw,21px);
  box-shadow:4px 4px 0px var(--line);
}
.ord small{
  display:inline-block;
  background:var(--sunny);
  color:var(--text);
  border:2px solid var(--line);
  border-radius:999px;
  padding:1px 9px;
  font-family:'Luckiest Guy',cursive;
  font-size:14px;
  margin-bottom:6px;
}
.sc{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
  gap:12px;
  margin-top:14px;
}
.sc div{
  border:3px solid var(--line);
  background:var(--pink);
  border-radius:18px;
  padding:14px;
  display:flex;
  flex-direction:column;
  gap:8px;
  align-items:center;
  box-shadow:4px 4px 0px var(--line);
}
.sc b{
  font-family:'Luckiest Guy',cursive;
  font-size:22px;
  letter-spacing:0.03em;
}

/* Hasil Akhir */
#fin{
  background:var(--sunny);
}
.pts{
  font-family:'Luckiest Guy',cursive;
  font-size:36px;
  color:var(--acc);
}
.rank{
  font-family:'Patrick Hand',cursive;
  font-size:clamp(24px,5vw,38px);
  font-weight:700;
  margin:10px 0;
  background:#FFFFFF;
  border:3px solid var(--line);
  border-radius:16px;
  padding:10px 18px;
  box-shadow:4px 4px 0px var(--line);
}

@media (max-width:520px){
  .card{padding:20px 14px;border-radius:22px}
}
</style>
</head>
<body>

<!-- Top Navigation -->
<header class="top-nav">
  <a href="{{ route('home') }}" class="back-btn">
    <i data-lucide="arrow-left" style="width: 16px; height: 16px; stroke-width: 3;"></i>
    KEMBALI KE PORTAL
  </a>

  <button id="audioToggle" class="audio-toggle-btn" aria-label="Toggle Suara">
    <span id="audioIcon">🔊</span>
    <span id="audioLabel">SUARA ON</span>
  </button>
</header>

<main>
  <!-- Header dengan Ornamen Doodle SVG -->
  <div class="header-wrap">
    <svg width="34" height="34" viewBox="0 0 48 48" fill="none" aria-hidden="true">
      <path d="M24 4V14M24 34V44M4 24H14M34 24H44M10 10L17 17M31 31L38 38M38 10L31 17M17 31L10 38" stroke="#EB3B24" stroke-width="4" stroke-linecap="round"/>
    </svg>
    <h1>SUSUN <span class="blue">URUTAN</span></h1>
    <svg width="32" height="32" viewBox="0 0 36 36" fill="none" aria-hidden="true">
      <circle cx="18" cy="18" r="15" fill="#FFD21E" stroke="#111111" stroke-width="2.8"/>
      <circle cx="13" cy="14" r="2" fill="#111111"/>
      <circle cx="23" cy="14" r="2" fill="#111111"/>
      <path d="M11 21C13 25 23 25 25 21" stroke="#111111" stroke-width="2.8" stroke-linecap="round"/>
    </svg>
  </div>

  <div id="info" class="pill hidden"></div>

  <!-- HALAMAN UTAMA / SETTING GAME -->
  <section id="start" class="card notepad-card {{ ($isPlayerView ?? false) ? 'hidden' : '' }}">
    <div class="tape"></div>
    <p class="intro-banner">
      Tim berbaris sesuai urutan yang benar, <b>TANPA BICARA!</b> Tiap orang hanya tahu satu kartu rahasia di HP-nya.
    </p>
    <ol class="rules">
      <li>Pilih <b>Jumlah Tim</b> (default 2 tim) dan <b>Jumlah Pemain per Tim</b> (3 hingga 7 orang).</li>
      <li>Saat ronde dimulai, <b>Tema</b> akan ditampilkan di layar besar beserta bunyi penanda.</li>
      <li>QR Code akan dimunculkan <b>1 per 1</b> sesuai jumlah pemain. Ketika di-scan, HP pemain membuka route berisi kata rahasianya.</li>
      <li>Rahasiakan kartumu! Setelah semua scan, timer 60 detik mulai dan semua <b>dilarang bicara</b>.</li>
      <li>Susun barisan dari urutan pertama sampai terakhir. Posisi yang tepat dapat 1 poin, urutan sempurna bonus +2!</li>
    </ol>
    <div class="row" style="gap:14px;margin-top:16px">
      <div class="select-group">
        <label for="nt">👥 Jumlah tim:</label>
        <select id="nt"><option value="2" selected>2 Tim</option><option value="3">3 Tim</option><option value="4">4 Tim</option><option value="5">5 Tim</option></select>
      </div>
      <div class="select-group" style="background:var(--sunny)">
        <label for="np">🕹️ Pemain per tim:</label>
        <select id="np">
          <option value="3">3 Orang</option>
          <option value="4">4 Orang</option>
          <option value="5" selected>5 Orang</option>
          <option value="6">6 Orang</option>
          <option value="7">7 Orang</option>
        </select>
      </div>
      <button class="p" id="go" style="font-size:16px;padding:14px 24px">MULAI GAME →</button>
    </div>
  </section>

  <!-- LAYAR BESAR: TEMA RONDE -->
  <section id="themeScreen" class="card hidden">
    <div class="badge" id="thRoundBadge" style="background:var(--vermilion);color:#FFFFFF">RONDE 1</div>
    <p style="margin-top:12px;font-size:18px;font-weight:700">Tema ronde ini adalah:</p>
    <div class="theme-display-box">
      <div class="theme-title" id="themeText">-</div>
    </div>
    <p style="font-size:15px">Pastikan semua peserta memperhatikan tema di atas sebelum scan kartu!</p>
    <div class="row" style="margin-top:24px">
      <button class="sec" id="btnNextToQR" style="font-size:18px;padding:14px 28px">MULAI SCAN QR CODE →</button>
    </div>
  </section>

  <!-- SCAN QR 1 PER 1 -->
  <section id="share" class="card hidden">
    <div class="tape" style="background:var(--acc)"></div>
    <div class="row" style="gap:8px">
      <span class="badge" id="shTeamBadge">Tim</span>
      <span class="badge" id="shThemeBadge" style="background:var(--sunny)">Tema</span>
    </div>
    <h2 id="shT" style="margin-top:12px"></h2>
    
    <div class="single-qr-wrapper">
      <div class="qr-badge-num" id="qrLabel">Orang 1</div>
      <div class="qr-inst">📱 Silakan scan QR di bawah dengan HP:</div>
      <div class="qr-frame" id="singleQr"></div>
      <div class="qr-url-hint" id="qrUrlHint"></div>
    </div>

    <div class="row" style="margin-top:16px">
      <button class="sec" id="prevQr">← Kembali</button>
      <button class="p" id="nx" style="min-width:160px">Next QR →</button>
    </div>
  </section>

  <!-- HALAMAN KHUSUS PEMAIN (ROUTE /timX/Y atau #/timX/Y) -->
  <section id="playerView" class="card {{ ($isPlayerView ?? false) ? '' : 'hidden' }}" style="background:transparent;border:none;box-shadow:none;padding:0">
    <div class="clue-card">
      <div class="tape"></div>
      <div class="pill clue-team-pill" id="pvTeam">TIM {{ $teamNum ?? 1 }}</div>
      <div class="clue-role" id="pvPlayer">Pemain Orang ke-{{ $playerNum ?? 1 }}</div>
      <p style="margin-top:12px;color:var(--muted)">Tema: <strong id="pvTheme" style="color:var(--text)">{{ $themeName ?? '-' }}</strong></p>
      <div class="clue-word-box">
        <div style="font-size:12px;letter-spacing:1px;font-weight:800;color:var(--text);margin-bottom:8px">🎫 KARTU / KATA RAHASIA ANDA</div>
        <div class="clue-word" id="pvWord">{{ $word ?? '-' }}</div>
      </div>
      <div class="clue-warning">
        🤫 JANGAN DIBERITAHUKAN KE SIAPAPUN! DILARANG BERBICARA SAAT TIMER DIMULAI!
      </div>
      <div class="row" style="margin-top:20px">
        <a href="{{ route('games.susun-urutan') }}" class="back-btn" style="border-radius:14px;padding:10px 18px">
          ← Ke Halaman Host Game
        </a>
      </div>
    </div>
  </section>

  <!-- MODE SUSUN & TIMER -->
  <section id="play" class="card hidden">
    <div class="tape"></div>
    <div class="big">🤫 TANPA BICARA!</div>
    <div class="timer" id="tm">01:00</div>
    <p style="color:var(--text);font-size:17px">Susun barisan sesuai urutan yang benar menurut kartu kalian!</p>
    <div class="row" style="margin-top:20px"><button class="p" id="stop">Selesai Lebih Cepat ✓</button></div>
  </section>

  <!-- JAWABAN & SKOR -->
  <section id="rev" class="card hidden">
    <div class="tape" style="background:var(--sunny)"></div>
    <h2 id="rvT"></h2>
    <div class="ord" id="ord"></div>
    <p style="color:var(--text);font-size:16px">Isi jumlah posisi yang benar untuk tiap tim:</p>
    <div class="sc" id="inp"></div>
    <div class="row" style="margin-top:20px"><button class="p" id="sv">SIMPAN SKOR →</button></div>
  </section>

  <!-- HASIL AKHIR -->
  <section id="fin" class="card hidden">
    <div class="tape" style="background:var(--acc)"></div>
    <h2>🏆 HASIL AKHIR</h2>
    <div id="rk" style="margin-top:14px"></div>
    <div class="row" style="margin-top:22px">
      <button class="p" id="again">🔄 MAIN LAGI</button>
      <a href="{{ route('home') }}" class="back-btn" style="padding: 12px 20px; font-size: 15px; border-radius: 14px;">
        🏠 KEMBALI KE PORTAL
      </a>
    </div>
  </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// Data tema diteruskan dari backend Laravel
const rawThemes = @json($themes);
const THEMES = (rawThemes && rawThemes.length) ? rawThemes.map(item => ({
  t: item.theme,
  s: item.steps
})) : [
  {t:"Membuat Kopi",s:["Siapkan cangkir","Giling biji kopi","Panaskan air","Seduh kopi","Aduk rata","Tuang ke cangkir","Minum santai"]},
  {t:"Urutan Tata Surya",s:["Matahari","Merkurius","Venus","Bumi","Mars","Jupiter","Saturnus"]},
  {t:"Naik Pesawat",s:["Beli tiket","Check-in bandara","Pemeriksaan bagasi","Menunggu di boarding gate","Masuk ke pesawat","Pesawat lepas landas","Mendarat di tujuan"]},
  {t:"Membuat Nasi Goreng",s:["Siapkan wajan","Haluskan bumbu","Panaskan minyak & tumis","Masukkan telur & nasi","Beri kecap & garam","Aduk rata","Sajikan di piring"]},
  {t:"Perjalanan Hidup Manusia",s:["Bayi","Balita","Anak-anak","Remaja","Dewasa muda","Dewasa","Lanjut usia"]},
  {t:"Alur Proyek Tim",s:["Brainstorming ide","Tentukan tujuan","Bagi tugas anggota","Eksekusi pekerjaan","Evaluasi & review","Revisi hasil","Presentasi akhir"]},
  {t:"Siklus Kupu-kupu",s:["Telur menempel","Telur menetas","Ulat makan daun","Ulat menjadi kepompong","Metamorfosis dalam kokon","Kupu-kupu keluar","Terbang bebas"]},
  {t:"Bangun Pagi & Berangkat Kerja",s:["Alarm berbunyi","Bangun tidur","Mandi pagi","Pakai pakaian kerja","Sarapan pagi","Persiapkan kendaraan","Berangkat kerja"]}
];

const NAMES=["Tim 1","Tim 2","Tim 3","Tim 4","Tim 5"];
const $=id=>document.getElementById(id);
const SECT=["start","themeScreen","share","playerView","play","rev","fin"];
const show=id=>SECT.forEach(s=>$(s).classList.toggle("hidden",s!==id));
const shuffle=a=>{a=a.slice();for(let i=a.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[a[i],a[j]]=[a[j],a[i]]}return a};
const fmt=s=>{s=Math.max(0,Math.ceil(s));return String(Math.floor(s/60)).padStart(2,"0")+":"+String(s%60).padStart(2,"0")};

// Base64 helper yang aman untuk karakter UTF-8 / Emoji
function utf8ToBase64(str) {
  return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
    return String.fromCharCode('0x' + p1);
  }));
}
function base64ToUtf8(str) {
  return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  }).join(''));
}

let teams=[],themes=[],r=0,si=0,qi=0,end=0,tid=null,last=0,ac=null;
let playerCount=5;
let audioMuted = false;
const ROUNDS=3,DUR=60;

function tone(f,d,ty,v,dl){
  if(!ac || audioMuted) return;
  try{
    const t=ac.currentTime+(dl||0),o=ac.createOscillator(),g=ac.createGain();
    o.type=ty||"sine";
    o.frequency.setValueAtTime(f,t);
    g.gain.setValueAtTime(.0001,t);
    g.gain.exponentialRampToValueAtTime(v||.2,t+.01);
    g.gain.exponentialRampToValueAtTime(.0001,t+d);
    o.connect(g);g.connect(ac.destination);
    o.start(t);o.stop(t+d+.02);
  }catch(e){}
}

function playStartSound(){
  tone(392,.18,"triangle",.3,0);
  tone(523.25,.18,"triangle",.3,.12);
  tone(659.25,.18,"triangle",.3,.24);
  tone(783.99,.4,"sine",.35,.36);
}

function playNextSound(){
  tone(587.33,.08,"sine",.2,0);
  tone(880,.12,"sine",.25,.07);
}

function playGoTimerSound(){
  tone(440,.12,"square",.3,0);
  tone(554.37,.12,"square",.3,.12);
  tone(659.25,.12,"square",.3,.24);
  tone(880,.35,"square",.35,.36);
}

function toggleAudio() {
  audioMuted = !audioMuted;
  $("audioIcon").textContent = audioMuted ? "🔇" : "🔊";
  $("audioLabel").textContent = audioMuted ? "SUARA OFF" : "SUARA ON";
  if (!audioMuted) {
    try{ac=ac||new(window.AudioContext||window.webkitAudioContext)();ac.resume()}catch(e){}
  }
}

function info(){
  $("info").classList.remove("hidden");
  $("info").textContent="🎯 Ronde "+(r+1)+" / "+ROUNDS;
}

// Server view data jika dibuka via route Laravel /games/susun-urutan/timX/Y atau /timX/Y
const serverIsPlayer = @json($isPlayerView ?? false);
const serverTeamNum = @json($teamNum ?? null);
const serverPlayerNum = @json($playerNum ?? null);
const serverWord = @json($word ?? null);
const serverTheme = @json($themeName ?? null);

// ROUTER: Mengecek apakah dibuka di HP pemain via route /timX/Y atau query param c
function checkRoute(){
  if (serverIsPlayer && serverTeamNum && serverPlayerNum) {
    renderPlayerClueView(serverTeamNum, serverPlayerNum, serverWord || "Data kata dari server", serverTheme || "Susun Urutan");
    return true;
  }

  const urlParams = new URLSearchParams(window.location.search);
  const cardDataParam = urlParams.get('c');

  // PRIORITAS 1: Membaca parameter payload kartu 'c' (Self-Contained URL)
  if(cardDataParam){
    try{
      const decoded = JSON.parse(base64ToUtf8(cardDataParam));
      renderPlayerClueView(decoded.team || 1, decoded.player || 1, decoded.word, decoded.theme);
      return true;
    }catch(e){
      console.error("Gagal mendecode kartu:", e);
    }
  }

  // PRIORITAS 2: Cek route path atau hash (#/tim1/1 atau /tim1/1)
  let path = window.location.pathname;
  let hash = window.location.hash;
  let match = path.match(/tim(\d+)\/(\d+)\/?$/i);
  if(!match && hash){
    match = hash.match(/#\/?tim(\d+)\/(\d+)\/?$/i);
  }

  if(match){
    const teamNum = parseInt(match[1], 10);
    const playerNum = parseInt(match[2], 10);
    
    let wordFromQuery = urlParams.get('w');
    let themeFromQuery = urlParams.get('t');

    let word = wordFromQuery;
    let themeName = themeFromQuery || "Susun Urutan";
    if(!word){
      let stored = localStorage.getItem("susun_urutan_game");
      if(stored){
        try{
          const data = JSON.parse(stored);
          themeName = data.theme || themeName;
          const team = data.teams && data.teams[teamNum - 1];
          if(team && team.cards && team.cards[playerNum - 1]){
            word = team.cards[playerNum - 1].x;
          }
        }catch(e){}
      }
    }

    renderPlayerClueView(teamNum, playerNum, word || "Data kartu belum tersedia. Pastikan game sudah dimulai di layar utama.", themeName);
    return true;
  }

  return false;
}

function renderPlayerClueView(teamNum, playerNum, word, themeName){
  $("pvTeam").textContent = "TIM " + teamNum;
  $("pvPlayer").textContent = "Pemain Orang ke-" + playerNum;
  $("pvTheme").textContent = themeName || "-";
  $("pvWord").textContent = word || "-";

  show("playerView");
  $("info").classList.add("hidden");
}

function startGame(){
  try{ac=ac||new(window.AudioContext||window.webkitAudioContext)();ac.resume()}catch(e){}
  const n=+$("nt").value;
  playerCount = +$("np").value;
  
  teams=NAMES.slice(0,n).map(name=>({name,score:0,cards:[]}));
  themes=shuffle(THEMES).slice(0,ROUNDS);
  r=0;
  beginRound();
}

function beginRound(){
  const th=themes[r];
  const steps = th.s.slice(0, playerCount);

  teams.forEach(t=>{
    t.cards = shuffle(steps.map((x,i)=>({x,i})));
  });

  si=0;
  qi=0;
  info();
  showThemeDisplay();
}

function showThemeDisplay(){
  const th=themes[r];
  $("thRoundBadge").textContent="RONDE "+(r+1)+" DARI "+ROUNDS;
  $("themeText").textContent=th.t;
  playStartSound();
  show("themeScreen");
}

function renderCurrentQr(){
  const t=teams[si], th=themes[r];
  const teamNum = si + 1;
  const playerNum = qi + 1;
  const card = t.cards[qi];

  $("shTeamBadge").textContent=t.name+" ("+(si+1)+"/"+teams.length+")";
  $("shThemeBadge").textContent="Tema: "+th.t;
  $("shT").textContent="Giliran "+t.name;
  $("qrLabel").textContent="Orang Ke-"+playerNum;

  const payload = {
    team: teamNum,
    player: playerNum,
    theme: th.t,
    word: card.x,
    round: r + 1
  };
  const token = utf8ToBase64(JSON.stringify(payload));
  const cleanBase = window.location.origin + "{{ route('games.susun-urutan') }}";
  const routeUrl = `${cleanBase}?c=${encodeURIComponent(token)}#/tim${teamNum}/${playerNum}`;

  const box=$("singleQr");
  box.innerHTML="";
  try{
    new QRCode(box,{
      text: routeUrl,
      width:260,
      height:260,
      colorDark:"#111111",
      colorLight:"#ffffff",
      correctLevel:QRCode.CorrectLevel.M
    });
  }catch(e){
    box.textContent="(Gagal memuat QR Code)";
  }

  $("qrUrlHint").innerHTML = `Route: <a href="${routeUrl}" target="_blank" style="color:var(--acc);font-weight:800">/tim${teamNum}/${playerNum}</a>`;

  $("prevQr").style.display=(si===0 && qi===0)?"none":"inline-block";

  const isLastQrOfTeam=(qi===t.cards.length-1);
  const isLastTeam=(si===teams.length-1);

  if(isLastQrOfTeam){
    if(isLastTeam){
      $("nx").textContent="Mulai Susun! 🚀";
    } else {
      $("nx").textContent="Lanjut ke "+teams[si+1].name+" →";
    }
  } else {
    $("nx").textContent="Next QR ("+(qi+2)+"/"+playerCount+") →";
  }
}

function showShare(){
  renderCurrentQr();
  show("share");
}

function nextQR(){
  playNextSound();
  const t=teams[si];
  if(qi < t.cards.length - 1){
    qi++;
    renderCurrentQr();
  } else {
    if(si < teams.length - 1){
      si++;
      qi=0;
      renderCurrentQr();
    } else {
      startPlay();
    }
  }
}

function prevQR(){
  playNextSound();
  if(qi > 0){
    qi--;
    renderCurrentQr();
  } else if(si > 0){
    si--;
    qi=teams[si].cards.length - 1;
    renderCurrentQr();
  }
}

function startPlay(){
  playGoTimerSound();
  end=Date.now()+DUR*1000;last=DUR;
  $("tm").textContent=fmt(DUR);$("tm").classList.remove("low");show("play");
  clearInterval(tid);tid=setInterval(tick,200);
}

function tick(){
  const left=(end-Date.now())/1000,sec=Math.ceil(left);
  $("tm").textContent=fmt(left);$("tm").classList.toggle("low",left<=10);
  if(sec!==last&&sec>0){last=sec;sec<=10?tone(1200,.14,"sine",.3):tone(sec%2?700:520,.04,"square",.05)}
  if(left<=0){for(let i=0;i<6;i++)tone(i%2?660:880,.2,"square",.25,i*.25);reveal()}
}

function reveal(){
  clearInterval(tid);
  const th=themes[r];
  const steps = th.s.slice(0, playerCount);
  $("rvT").textContent="Jawaban Benar: "+th.t;
  $("ord").innerHTML=steps.map((x,i)=>`<div><small>#${i+1}</small><br>${x}</div>`).join("");

  const scoreOptions = Array.from({length: playerCount + 1}, (_, idx) => `<option>${idx}</option>`).join("");
  $("inp").innerHTML=teams.map((t,i)=>`<div><b>${t.name}</b><select id="sc${i}">${scoreOptions}</select></div>`).join("");
  show("rev");
}

function save(){
  teams.forEach((t,i)=>{
    const n=+$("sc"+i).value;
    t.score+=n+(n===playerCount ? 2 : 0);
  });
  r++;
  if(r<ROUNDS){beginRound();return}
  $("info").classList.add("hidden");
  const rk=teams.slice().sort((a,b)=>b.score-a.score);
  $("rk").innerHTML=rk.map((t,i)=>`<div class="rank">${i===0?"🏆 ":(i+1)+". "}${t.name}: <span class="pts">${t.score}</span> poin</div>`).join("");
  tone(523,.18,"triangle",.28);tone(659,.18,"triangle",.28,.12);tone(784,.18,"triangle",.28,.24);tone(1046,.3,"triangle",.28,.36);
  show("fin");
}

// Event Listeners
$("go").onclick=startGame;
$("btnNextToQR").onclick=()=>{ playNextSound(); showShare(); };
$("nx").onclick=nextQR;
$("prevQr").onclick=prevQR;
$("stop").onclick=reveal;
$("sv").onclick=save;
$("again").onclick=()=>{$("info").classList.add("hidden");show("start")};
$("audioToggle").onclick = toggleAudio;

// Pantau route URL
window.addEventListener("hashchange", checkRoute);

// Inisialisasi awal
window.addEventListener("DOMContentLoaded", () => {
  lucide.createIcons();
  if(!checkRoute()){
    show("start");
  }
});
</script>
</body>
</html>
