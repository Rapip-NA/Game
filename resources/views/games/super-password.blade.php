<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SUPER PASSWORD — MainYuk! Game Tebak Kata</title>

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
    :root {
      box-sizing: border-box;
      padding-top: env(safe-area-inset-top, 0px);
      padding-bottom: env(safe-area-inset-bottom, 0px);
      --bg: #FAFAFA;
      --card: #FFFFFF;
      --text: #111111;
      --muted: #334155;
      --line: #111111;
      --cobalt: #0B44D6;
      --sunny: #FFD21E;
      --vermilion: #EB3B24;
      --pink: #F9B4C4;
      --ok: #22C55E;
      --ok-t: #111111;
      --skip: #FFD21E;
      --skip-t: #111111;
      --bad: #EB3B24;
      color-scheme: light;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      background-color: var(--bg);
      background-image: radial-gradient(#CBD5E1 1.4px, transparent 1.4px);
      background-size: 22px 22px;
      color: var(--text);
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow-x: hidden;
    }

    /* Top Navigation Bar */
    .top-nav {
      width: 100%;
      max-width: 960px;
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

    main {
      width: 100%;
      max-width: 920px;
      padding: 16px 16px 40px;
      display: flex;
      flex-direction: column;
      gap: 18px;
      align-items: center;
      text-align: center;
      position: relative;
    }

    /* Header Judul Komik + Doodle */
    .header-wrap {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-top: 4px;
      position: relative;
    }

    h1 {
      margin: 0;
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(34px, 7.5vw, 56px);
      letter-spacing: 0.04em;
      line-height: 1;
      color: var(--text);
      text-shadow: 3px 3px 0px var(--sunny);
    }

    h1 span.blue {
      color: var(--cobalt);
    }

    .badge-sticker {
      background: var(--vermilion);
      color: #FFFFFF;
      font-family: 'Patrick Hand', cursive;
      font-size: 16px;
      font-weight: 700;
      padding: 4px 14px;
      border: 2.5px solid var(--line);
      border-radius: 999px;
      box-shadow: 3px 3px 0px var(--line);
      transform: rotate(-3deg);
      display: inline-block;
    }

    p {
      margin: 0;
      color: var(--muted);
      line-height: 1.5;
      font-weight: 600;
    }

    /* Kartu Neo-Brutalism */
    .card {
      background: var(--card);
      border: 3px solid var(--line);
      border-radius: 26px;
      padding: 24px;
      width: 100%;
      box-shadow: 6px 6px 0px var(--line);
      position: relative;
    }

    /* Kartu Notepad Bersih Tanpa Garis-Garis untuk Start Screen */
    .notepad-card {
      background-color: #FFFFFF;
    }

    .hidden {
      display: none !important;
    }

    /* Tombol & Select Stiker Neo-Brutalism */
    button,
    select {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-weight: 800;
      border-radius: 14px;
      border: 3px solid var(--line);
      background: #FFFFFF;
      color: var(--text);
      padding: 12px 20px;
      cursor: pointer;
      box-shadow: 4px 4px 0px var(--line);
      transition: transform 0.14s ease, box-shadow 0.14s ease;
    }

    button:hover,
    select:hover {
      transform: translate(-2px, -2px);
      box-shadow: 6px 6px 0px var(--line);
    }

    button:active {
      transform: translate(3px, 3px);
      box-shadow: 0px 0px 0px var(--line);
    }

    button.primary {
      background: var(--cobalt);
      color: #FFFFFF;
      font-size: 16px;
      letter-spacing: 0.02em;
    }

    button.okb {
      background: var(--ok);
      color: var(--ok-t);
    }

    button.skb {
      background: var(--skip);
      color: var(--skip-t);
    }

    /* Aturan Main bergaya Scrapbook */
    .intro-banner {
      background: var(--sunny);
      border: 2.5px solid var(--line);
      border-radius: 16px;
      padding: 12px 16px;
      color: var(--text);
      font-size: 15px;
      box-shadow: 3px 3px 0px var(--line);
      margin-bottom: 14px;
    }

    .rules {
      text-align: left;
      display: grid;
      gap: 10px;
      margin: 16px 0 22px;
      padding-left: 24px;
      font-family: 'Patrick Hand', cursive;
      font-size: 20px;
    }

    .rules li {
      color: var(--text);
      line-height: 1.35;
    }

    .rules li::marker {
      color: var(--vermilion);
      font-weight: 900;
    }

    .kbd-pill {
      display: inline-block;
      background: #FFFFFF;
      border: 2px solid var(--line);
      border-radius: 8px;
      padding: 0px 8px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px;
      font-weight: 800;
      box-shadow: 2px 2px 0px var(--line);
    }

    .row {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
    }

    /* Top Bar Layar Game */
    .top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      gap: 12px;
      background: var(--cobalt);
      border: 3px solid var(--line);
      border-radius: 22px;
      padding: 12px 18px;
      box-shadow: 5px 5px 0px var(--line);
      color: #FFFFFF;
    }

    .timer {
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(34px, 7.5vw, 54px);
      line-height: 1;
      letter-spacing: 0.04em;
      font-variant-numeric: tabular-nums;
      color: var(--sunny);
      text-shadow: 3px 3px 0px var(--line);
    }

    .timer.low {
      color: #FFFFFF;
      background: var(--vermilion);
      padding: 4px 14px;
      border-radius: 14px;
      border: 2.5px solid var(--line);
      animation: pulseWarn 0.5s infinite alternate;
    }

    @keyframes pulseWarn {
      from { transform: scale(1) rotate(-1deg); }
      to { transform: scale(1.06) rotate(1deg); }
    }

    .score {
      background: #FFFFFF;
      color: var(--text);
      border: 2.5px solid var(--line);
      border-radius: 999px;
      padding: 6px 14px;
      font-weight: 800;
      font-size: 15px;
      box-shadow: 3px 3px 0px var(--line);
    }

    label.score {
      background: var(--pink);
    }

    /* 10 Tile Huruf */
    .tiles {
      display: grid;
      grid-template-columns: repeat(10, 1fr);
      gap: 8px;
      width: 100%;
    }

    .tile {
      aspect-ratio: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      border: 3px solid var(--line);
      background: #FFFFFF;
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(16px, 3.8vw, 28px);
      box-shadow: 3px 3px 0px var(--line);
      transition: transform 0.15s ease, background-color 0.15s ease;
    }

    .tile.ok {
      background: var(--ok);
      color: var(--ok-t);
    }

    .tile.skip {
      background: var(--skip);
      color: var(--skip-t);
    }

    .tile.cur {
      background: var(--pink);
      transform: translateY(-4px) scale(1.08) rotate(-2deg);
      box-shadow: 5px 5px 0px var(--line);
    }

    /* Stage Kartu Utama */
    .stage {
      padding: 32px 20px;
      background: #FFFFFF;
      border: 3px solid var(--line);
      box-shadow: 8px 8px 0px var(--line);
      position: relative;
      overflow: hidden;
    }

    .stage::before {
      content: "RAHASIA";
      position: absolute;
      top: 14px;
      right: 16px;
      background: var(--sunny);
      color: var(--text);
      font-family: 'Patrick Hand', cursive;
      font-weight: 700;
      font-size: 15px;
      padding: 2px 10px;
      border: 2px solid var(--line);
      border-radius: 999px;
      transform: rotate(4deg);
    }

    .big {
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(74px, 21vw, 148px);
      line-height: 0.95;
      color: var(--cobalt);
      -webkit-text-stroke: 2.5px var(--line);
      text-shadow: 5px 5px 0px var(--sunny);
    }

    .word {
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(34px, 9vw, 68px);
      letter-spacing: 0.03em;
      margin-top: 10px;
      color: var(--text);
      word-break: break-word;
    }

    .hint {
      margin-top: 14px;
      font-family: 'Patrick Hand', cursive;
      font-size: 19px;
      color: var(--text);
      background: #F1F5F9;
      border: 2px dashed var(--line);
      border-radius: 12px;
      padding: 6px 14px;
      display: inline-block;
    }

    /* Tombol Aksi Bawah */
    .actions {
      display: flex;
      gap: 14px;
      width: 100%;
      justify-content: center;
      flex-wrap: wrap;
    }

    .actions button {
      flex: 1 1 200px;
      font-size: 19px;
      padding: 18px 20px;
      border-radius: 18px;
      box-shadow: 5px 5px 0px var(--line);
    }

    .cover {
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(28px, 6.5vw, 42px);
      letter-spacing: 0.03em;
      color: var(--text);
    }

    /* Layar Selesai (End Screen) */
    #end {
      background: var(--sunny);
    }

    .final {
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(48px, 11vw, 82px);
      color: var(--cobalt);
      -webkit-text-stroke: 2px var(--line);
      text-shadow: 4px 4px 0px #FFFFFF;
      margin: 6px 0;
    }

    .res {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      margin-top: 16px;
    }

    .chip {
      padding: 8px 14px;
      border-radius: 12px;
      border: 2.5px solid var(--line);
      background: #FFFFFF;
      color: var(--text);
      font-weight: 800;
      font-size: 14px;
      box-shadow: 3px 3px 0px var(--line);
    }

    .chip.ok {
      background: var(--ok);
      color: var(--ok-t);
    }

    .chip.no {
      background: var(--vermilion);
      color: #FFFFFF;
    }

    /* Stiker Selotip Dekoratif */
    .tape {
      width: 72px;
      height: 18px;
      background: rgba(235, 59, 36, 0.88);
      border: 2px solid var(--line);
      position: absolute;
      top: -10px;
      left: 50%;
      transform: translateX(-50%) rotate(-2deg);
    }

    @media (max-width: 520px) {
      .card {
        padding: 18px 14px;
        border-radius: 20px;
      }

      .tiles {
        gap: 4px;
      }

      .tile {
        border-width: 2px;
        border-radius: 8px;
        box-shadow: 2px 2px 0px var(--line);
      }

      .top {
        padding: 10px 12px;
      }
    }
  </style>
</head>

<body>

  <!-- Top Nav Bar -->
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
      <!-- Doodle Starburst Kiri -->
      <svg width="34" height="34" viewBox="0 0 48 48" fill="none" aria-hidden="true">
        <path d="M24 4V14M24 34V44M4 24H14M34 24H44M10 10L17 17M31 31L38 38M38 10L31 17M17 31L10 38" stroke="#EB3B24" stroke-width="4" stroke-linecap="round"/>
      </svg>

      <h1>SUPER <span class="blue">PASSWORD</span></h1>

      <!-- Doodle Petir Kuning Kanan -->
      <svg width="30" height="36" viewBox="0 0 44 54" fill="none" aria-hidden="true">
        <polygon points="24,2 4,30 20,30 16,52 40,22 23,22" fill="#FFD21E" stroke="#111111" stroke-width="3.5" stroke-linejoin="round"/>
      </svg>
    </div>

    <div class="badge-sticker">🎉 MODE TEBAK KATA KILAT — 2 PEMAIN!</div>

    <!-- LAYAR MULAI (START) -->
    <section id="start" class="card notepad-card">
      <div class="tape"></div>
      <p class="intro-banner">
        Dua pemain: <b>Pemberi Petunjuk</b> menghadap layar, <b>Penebak</b> duduk membelakangi layar!
      </p>
      <ul class="rules">
        <li>Layar menampilkan 10 huruf acak, masing-masing dengan satu kata rahasia (bahasa Inggris).</li>
        <li>Pemberi Petunjuk menyebutkan kata-kata yang <b>mendekati</b> kata rahasia, tanpa menyebut kata itu sendiri atau turunannya.</li>
        <li>Jika tebakan benar, tekan <b>Benar / Next</b>: hurufnya jadi hijau. Jika sulit, tekan <b>Skip</b>; huruf itu muncul lagi nanti.</li>
        <li>Pintasan keyboard: <span class="kbd-pill">Enter</span> = Benar, <span class="kbd-pill">S</span> = Skip, <span class="kbd-pill">Spasi</span> = Jeda.</li>
        <li>Dilengkapi efek suara seru: tik-tok tiap detik, beep di 10 detik terakhir, alarm saat waktu habis, dan lonceng Benar / Skip!</li>
      </ul>
      <div class="row">
        <label for="dur" class="score">⏱️ Waktu:</label>
        <select id="dur">
          <option value="60">60 detik</option>
          <option value="90">90 detik</option>
          <option value="120" selected>120 detik</option>
          <option value="180">180 detik</option>
        </select>
        <button class="primary" id="go">MULAI GAME →</button>
      </div>
    </section>

    <!-- LAYAR PERMAINAN (GAME) -->
    <section id="game" class="hidden" style="width:100%;display:flex;flex-direction:column;gap:18px;align-items:center">
      <div class="top">
        <div class="score" id="score">0 / 10</div>
        <div class="timer" id="timer">02:00</div>
        <button id="pause">Jeda</button>
      </div>

      <div class="tiles" id="tiles"></div>

      <div class="card stage" id="stage">
        <div class="big" id="letter">A</div>
        <div class="word" id="word">Apple</div>
        <p class="hint">🤫 Hanya untuk Pemberi Petunjuk. Penebak jangan menoleh!</p>
      </div>

      <div class="card stage hidden" id="paused">
        <div class="cover">⏸️ GAME DIJEDA</div>
        <p class="hint">Kata rahasia disembunyikan sementara. Tekan Lanjut untuk meneruskan!</p>
      </div>

      <div class="actions">
        <button class="skb" id="skip">Skip (S)</button>
        <button class="okb" id="ok">Benar / Next (Enter)</button>
      </div>
    </section>

    <!-- LAYAR SELESAI (END) -->
    <section id="end" class="card hidden">
      <div class="tape" style="background:#0B44D6;"></div>
      <div id="endtitle" class="cover"></div>
      <div class="final" id="endscore"></div>
      <div class="res" id="endres"></div>
      <div class="row" style="margin-top:24px">
        <button class="primary" id="again">🔄 MAIN LAGI</button>
        <a href="{{ route('home') }}" class="back-btn" style="padding: 12px 20px; font-size: 15px; border-radius: 14px;">
          🏠 KEMBALI KE PORTAL
        </a>
      </div>
    </section>
  </main>

  <script>
    // Data kata dan round awal dikirim langsung dari backend Laravel
    const initialRoundData = @json($initialRound);
    const backendWordBank = @json($wordBank);
    const roundApiUrl = "{{ route('games.super-password.round') }}";

    let WORDS = backendWordBank || {};
    let used = [];
    try {
      used = JSON.parse(localStorage.getItem("sp_used") || "[]");
    } catch (e) {
      used = [];
    }

    function saveUsed() {
      try {
        localStorage.setItem("sp_used", JSON.stringify(used));
      } catch (e) {}
    }

    const $ = id => document.getElementById(id);
    let items = initialRoundData || [], cur = 0, end = 0, left = 0, timerId = null, paused = false, total = 120, lastSec = 0;
    let audioMuted = false;

    // ---------- Suara (Web Audio API) ----------
    let ac = null;
    function initAudio() {
      if (audioMuted) return;
      try {
        if (!ac) ac = new (window.AudioContext || window.webkitAudioContext)();
        if (ac.state === "suspended") ac.resume();
      } catch (e) {
        ac = null;
      }
    }

    function tone(freq, dur, type, vol, delay) {
      if (!ac || audioMuted) return;
      try {
        const t = ac.currentTime + (delay || 0);
        const o = ac.createOscillator(), g = ac.createGain();
        o.type = type || "sine";
        o.frequency.setValueAtTime(freq, t);
        g.gain.setValueAtTime(0.0001, t);
        g.gain.exponentialRampToValueAtTime(vol || 0.2, t + 0.01);
        g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
        o.connect(g);
        g.connect(ac.destination);
        o.start(t);
        o.stop(t + dur + 0.02);
      } catch (e) {}
    }

    const sfx = {
      tick: n => tone(n % 2 ? 700 : 520, 0.04, "square", 0.05),
      warn: () => tone(1200, 0.14, "sine", 0.3),
      ok: () => {
        tone(660, 0.1, "triangle", 0.25);
        tone(990, 0.16, "triangle", 0.25, 0.09);
      },
      skip: () => tone(260, 0.14, "sawtooth", 0.12),
      alarm: () => {
        for (let i = 0; i < 6; i++) {
          tone(i % 2 ? 660 : 880, 0.2, "square", 0.25, i * 0.25);
        }
      },
      win: () => [523, 659, 784, 1046].forEach((f, i) => tone(f, 0.18, "triangle", 0.28, i * 0.12))
    };

    function toggleAudio() {
      audioMuted = !audioMuted;
      $("audioIcon").textContent = audioMuted ? "🔇" : "🔊";
      $("audioLabel").textContent = audioMuted ? "SUARA OFF" : "SUARA ON";
      if (!audioMuted) initAudio();
    }

    function show(id) {
      ["start", "game", "end"].forEach(s => $(s).classList.toggle("hidden", s !== id));
    }

    function fmt(s) {
      s = Math.max(0, Math.ceil(s));
      return String(Math.floor(s / 60)).padStart(2, "0") + ":" + String(s % 60).padStart(2, "0");
    }

    // Mengambil putaran baru dari backend Laravel (atau fallback ke client-side)
    async function fetchNewRound() {
      try {
        const res = await fetch(roundApiUrl + "?exclude_words=" + encodeURIComponent(JSON.stringify(used)));
        if (res.ok) {
          const json = await res.json();
          if (json.items && json.items.length) {
            json.items.forEach(it => {
              if (!used.includes(it.word)) used.push(it.word);
            });
            saveUsed();
            return json.items;
          }
        }
      } catch (err) {
        console.warn("Backend round fetch failed, fallback to client word bank:", err);
      }

      // Fallback generator dari bank kata Laravel
      const letters = Object.keys(WORDS);
      for (let i = letters.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [letters[i], letters[j]] = [letters[j], letters[i]];
      }
      return letters.slice(0, 10).map(l => {
        let pool = (WORDS[l] || []).filter(w => !used.includes(w));
        if (!pool.length) {
          used = used.filter(w => !(WORDS[l] || []).includes(w));
          pool = WORDS[l] || [];
        }
        const w = pool[Math.floor(Math.random() * pool.length)] || l;
        used.push(w);
        return { letter: l, word: w, status: "pending" };
      });
    }

    async function startGame() {
      initAudio();
      total = parseInt($("dur").value, 10);
      items = await fetchNewRound();
      cur = 0;
      paused = false;
      left = total;
      lastSec = total;
      end = Date.now() + total * 1000;
      show("game");
      $("paused").classList.add("hidden");
      $("stage").classList.remove("hidden");
      $("pause").textContent = "Jeda";
      render();
      clearInterval(timerId);
      timerId = setInterval(tick, 200);
      tick();
    }

    function tick() {
      if (paused) return;
      left = (end - Date.now()) / 1000;
      $("timer").textContent = fmt(left);
      $("timer").classList.toggle("low", left <= 10);
      const sec = Math.ceil(left);
      if (sec !== lastSec && sec > 0) {
        lastSec = sec;
        if (sec <= 10) sfx.warn();
        else sfx.tick(sec);
      }
      if (left <= 0) finish(false);
    }

    function render() {
      if (!items || !items[cur]) return;
      $("tiles").innerHTML = items.map((it, i) => `
        <div class="tile ${it.status === 'ok' ? 'ok' : it.status === 'skip' ? 'skip' : ''} ${i === cur ? 'cur' : ''}">
          ${it.letter}
        </div>
      `).join("");
      $("letter").textContent = items[cur].letter;
      $("word").textContent = items[cur].word;
      $("score").textContent = items.filter(i => i.status === "ok").length + " / 10";
    }

    function advance() {
      for (let k = 1; k <= items.length; k++) {
        const i = (cur + k) % items.length;
        if (items[i].status !== "ok") {
          cur = i;
          return true;
        }
      }
      return false;
    }

    function correct() {
      if (paused) return;
      items[cur].status = "ok";
      if (!advance()) {
        render();
        finish(true);
        return;
      }
      sfx.ok();
      render();
    }

    function skip() {
      if (paused) return;
      if (items[cur].status !== "ok") {
        items[cur].status = "skip";
      }
      sfx.skip();
      advance();
      render();
    }

    function togglePause() {
      if ($("game").classList.contains("hidden")) return;
      paused = !paused;
      if (paused) {
        left = (end - Date.now()) / 1000;
      } else {
        end = Date.now() + left * 1000;
      }
      $("paused").classList.toggle("hidden", !paused);
      $("stage").classList.toggle("hidden", paused);
      $("pause").textContent = paused ? "Lanjut" : "Jeda";
    }

    function finish(all) {
      clearInterval(timerId);
      all ? sfx.win() : sfx.alarm();
      const n = items.filter(i => i.status === "ok").length;
      $("endtitle").textContent = all ? "Semua huruf terjawab! 🏆" : n >= 7 ? "Waktu habis, hebat! 🎉" : "Waktu habis! Tetap semangat!";
      $("endscore").textContent = n + " / 10";
      $("endres").innerHTML = items.map(i => `
        <span class="chip ${i.status === 'ok' ? 'ok' : 'no'}">
          ${i.letter} · ${i.word}
        </span>
      `).join("");
      show("end");
    }

    $("go").onclick = startGame;
    $("again").onclick = () => show("start");
    $("ok").onclick = correct;
    $("skip").onclick = skip;
    $("pause").onclick = togglePause;
    $("audioToggle").onclick = toggleAudio;

    document.addEventListener("keydown", e => {
      if ($("game").classList.contains("hidden")) return;
      if (e.key === "Enter") {
        e.preventDefault();
        correct();
      } else if (e.key === "s" || e.key === "S") {
        skip();
      } else if (e.key === " ") {
        e.preventDefault();
        togglePause();
      }
    });

    window.addEventListener("DOMContentLoaded", () => {
      lucide.createIcons();
    });
  </script>
</body>

</html>
