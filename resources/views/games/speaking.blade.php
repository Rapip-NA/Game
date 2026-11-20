<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SPEAKING ROULETTE — MainYuk! Topik Ngobrol Acak</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('img/icon.png') }}">

  <!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">

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
      --green: #22C55E;
      color-scheme: light;
    }

    * { box-sizing: border-box; }

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

    /* ---- NAV ---- */
    .top-nav {
      width: 100%;
      max-width: 720px;
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

    /* ---- MAIN ---- */
    main {
      width: 100%;
      max-width: 720px;
      padding: 16px 16px 60px;
      display: flex;
      flex-direction: column;
      gap: 24px;
      align-items: center;
      text-align: center;
    }

    #stateIdle, #statePlaying, #stateFinish {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 24px;
      align-items: center;
    }

    h1 {
      margin: 0;
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(38px, 9vw, 64px);
      letter-spacing: 0.04em;
      line-height: 1;
      color: var(--text);
      text-shadow: 3px 3px 0px var(--sunny);
    }

    h1 span.blue { color: var(--cobalt); }

    p {
      margin: 0;
      color: var(--muted);
      line-height: 1.5;
      font-weight: 600;
    }

    /* ---- CARD ---- */
    .card {
      background: var(--card);
      border: 3px solid var(--line);
      border-radius: 26px;
      padding: 28px;
      width: 100%;
      box-shadow: 6px 6px 0px var(--line);
      position: relative;
    }

    .hidden { display: none !important; }

    /* ---- BUTTONS ---- */
    button, select {
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
    button:hover, select:hover {
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
      font-size: 18px;
      letter-spacing: 0.03em;
      font-family: 'Luckiest Guy', cursive;
    }
    button.danger { background: var(--vermilion); color: #FFFFFF; }
    button.yellow { background: var(--sunny); color: var(--text); }

    /* ---- BADGE / CHIPS ---- */
    .badge {
      display: inline-block;
      font-family: 'Patrick Hand', cursive;
      font-size: 15px;
      font-weight: 700;
      padding: 4px 14px;
      border: 2.5px solid var(--line);
      border-radius: 999px;
      box-shadow: 3px 3px 0px var(--line);
    }

    .row {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      width: 100%;
    }

    .col-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      width: 100%;
    }

    /* ---- SETTINGS ---- */
    .setting-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 0;
      border-bottom: 2px dashed #E2E8F0;
    }
    .setting-row:last-child { border-bottom: none; }
    .setting-label {
      font-weight: 800;
      font-size: 14px;
      color: var(--text);
    }

    /* ---- TOPIC PREVIEW LIST ---- */
    .preview-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
    .preview-item {
      display: flex;
      align-items: center;
      gap: 10px;
      text-align: left;
      background: #F8FAFC;
      border: 2px solid #E2E8F0;
      border-radius: 14px;
      padding: 10px 14px;
      font-size: 14px;
      font-weight: 700;
      color: var(--text);
    }
    .preview-num {
      flex-shrink: 0;
      width: 28px; height: 28px;
      border-radius: 999px;
      border: 2.5px solid var(--line);
      background: var(--sunny);
      font-family: 'Luckiest Guy', cursive;
      font-size: 14px;
      display: flex; align-items: center; justify-content: center;
    }
    .preview-cat { font-size: 12px; color: var(--muted); font-weight: 600; }

    /* ---- PHASE BADGE ---- */
    .phase-pill {
      background: var(--cobalt);
      color: #FFFFFF;
      font-family: 'Luckiest Guy', cursive;
      font-size: 16px;
      letter-spacing: 0.05em;
      padding: 6px 20px;
      border: 3px solid var(--line);
      border-radius: 999px;
      box-shadow: 4px 4px 0px var(--line);
      display: inline-block;
    }

    /* ---- TOPIC CARD MAIN ---- */
    .stage {
      padding: 36px 24px;
      border: 3px solid var(--line);
      border-radius: 26px;
      width: 100%;
      box-shadow: 8px 8px 0px var(--line);
      position: relative;
      overflow: hidden;
      transition: background-color 0.3s ease;
    }

    .stage-category {
      font-family: 'Patrick Hand', cursive;
      font-size: 15px;
      font-weight: 700;
      opacity: 0.65;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      margin-bottom: 12px;
    }

    .stage-topic {
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(28px, 7vw, 52px);
      line-height: 1.1;
      letter-spacing: 0.03em;
      word-break: break-word;
    }

    .chain-hint {
      display: inline-block;
      margin-top: 16px;
      font-family: 'Patrick Hand', cursive;
      font-size: 17px;
      background: rgba(255,255,255,0.3);
      border: 2px dashed rgba(0,0,0,0.25);
      border-radius: 12px;
      padding: 6px 14px;
    }

    /* ---- TIMER ---- */
    .timer-wrap {
      position: relative;
      width: 140px;
      height: 140px;
      flex-shrink: 0;
    }
    .timer-wrap svg { width: 100%; height: 100%; transform: rotate(-90deg); }
    .timer-track { fill: none; stroke: #E2E8F0; stroke-width: 10; }
    .timer-ring  {
      fill: none;
      stroke: var(--cobalt);
      stroke-width: 10;
      stroke-linecap: round;
      stroke-dasharray: 376;
      stroke-dashoffset: 0;
      transition: stroke-dashoffset 1s linear, stroke 0.4s;
    }
    .timer-label {
      position: absolute; inset: 0;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
    }
    .timer-digits {
      font-family: 'Luckiest Guy', cursive;
      font-size: 32px;
      line-height: 1;
      color: var(--text);
      letter-spacing: 0.03em;
    }
    .timer-sub {
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      margin-top: 2px;
    }

    /* ---- PROGRESS DOTS ---- */
    .dots { display: flex; gap: 10px; align-items: center; justify-content: center; }
    .dot {
      width: 16px; height: 16px;
      border-radius: 999px;
      border: 2.5px solid var(--line);
      background: #FFFFFF;
      box-shadow: 2px 2px 0 var(--line);
      transition: background 0.3s, transform 0.3s;
    }
    .dot.done { background: var(--green); }
    .dot.active { background: var(--cobalt); transform: scale(1.3); }

    /* ---- SLIDE ANIMATION ---- */
    #stageCard {
      transition: transform 0.2s cubic-bezier(.4,0,.2,1), opacity 0.2s;
    }
    #stageCard.exit-left  { transform: translateX(-130%) rotate(-6deg); opacity: 0; }
    #stageCard.exit-right { transform: translateX(130%) rotate(6deg); opacity: 0; }

    /* ---- FINISH ---- */
    .finish-card { background: var(--sunny); }
    .finish-title {
      font-family: 'Luckiest Guy', cursive;
      font-size: clamp(44px, 10vw, 72px);
      color: var(--cobalt);
      -webkit-text-stroke: 2px var(--line);
      text-shadow: 4px 4px 0 #FFFFFF;
      line-height: 1;
    }
    .recap-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      border: 3px solid var(--line);
      border-radius: 16px;
      text-align: left;
      box-shadow: 4px 4px 0 var(--line);
      margin-bottom: 10px;
    }
    .recap-num {
      flex-shrink: 0;
      width: 36px; height: 36px;
      border-radius: 999px;
      border: 2.5px solid var(--line);
      font-family: 'Luckiest Guy', cursive;
      font-size: 18px;
      display: flex; align-items: center; justify-content: center;
    }

    /* ---- CONFETTI ---- */
    #confetti-canvas {
      position: fixed; inset: 0;
      pointer-events: none;
      z-index: 999;
    }

    /* ---- RULE HINT ---- */
    .rules-list {
      list-style: none; padding: 0; margin: 0;
      display: grid; gap: 10px;
      text-align: left;
      font-family: 'Patrick Hand', cursive;
      font-size: 18px;
      color: var(--text);
    }
    .rules-list li {
      display: flex; align-items: flex-start; gap: 10px;
    }
    .rules-list li .ico {
      flex-shrink: 0;
      width: 30px; height: 30px;
      border: 2.5px solid var(--line);
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px;
      box-shadow: 2px 2px 0 var(--line);
    }

    @media (max-width: 480px) {
      .col-2 { grid-template-columns: 1fr; }
      .card { padding: 18px 14px; border-radius: 20px; }
    }
  </style>
</head>

<body>

<canvas id="confetti-canvas"></canvas>

<!-- ======= TOP NAV ======= -->
<header class="top-nav">
  <div style="display:flex;align-items:center;gap:10px;">
    <a href="{{ route('home') }}" class="back-btn">
      <i data-lucide="arrow-left" style="width:16px;height:16px;stroke-width:3;"></i>
      KEMBALI
    </a>
    <button id="btnSound" onclick="toggleSound()" class="back-btn" style="cursor:pointer;">
      <span id="soundIcon">🔊</span> <span id="soundLabel">SUARA ON</span>
    </button>
  </div>
  <div style="font-family:'Patrick Hand',cursive;font-weight:700;font-size:14px;color:var(--muted);">
    {{ $totalTopics }}+ topik tersedia
  </div>
</header>

<!-- ======= MAIN ======= -->
<main>

  <!-- ===== STATE: IDLE ===== -->
  <div id="stateIdle">

    <!-- Judul -->
    <div style="margin-top:8px;">
      <h1>SPEAKING <span class="blue">ROULETTE!</span></h1>
      <p style="margin-top:8px;font-size:15px;">Topik acak muncul tiap waktu yang ditentukan. Ceritakan pengalamanmu dan sambung dengan topik berikutnya!</p>
    </div>

    <!-- Badge info -->
    <div class="row">
      <span class="badge" style="background:var(--sunny);">⏱ 2 Menit / Topik</span>
      <span class="badge" style="background:var(--pink);">🗣 3 Topik Total</span>
      <span class="badge" style="background:var(--cobalt);color:#fff;">🎲 Acak Tiap Sesi</span>
    </div>

    <!-- Settings Card -->
    <div class="card">
      <div style="font-family:'Luckiest Guy',cursive;font-size:20px;margin-bottom:16px;color:var(--cobalt);">⚙️ PENGATURAN SESI</div>

      <div class="setting-row">
        <span class="setting-label">⏱ Menit per Topik</span>
        <select id="minutesPerTopic">
          <option value="1">1 menit</option>
          <option value="2" selected>2 menit</option>
          <option value="3">3 menit</option>
        </select>
      </div>
      <div class="setting-row">
        <span class="setting-label">🗣 Jumlah Topik</span>
        <select id="topicCount">
          <option value="2">2 topik</option>
          <option value="3" selected>3 topik</option>
          <option value="4">4 topik</option>
          <option value="5">5 topik</option>
        </select>
      </div>

      <div style="margin-top:20px;">
        <button class="primary" style="width:100%;padding:16px;" onclick="startGame()">
          🎤 MULAI SPEAKING!
        </button>
      </div>
    </div>

    <!-- Aturan main -->
    <div class="card">
      <div style="font-family:'Luckiest Guy',cursive;font-size:20px;margin-bottom:14px;">📋 CARA MAIN</div>
      <ul class="rules-list">
        <li><span class="ico" style="background:var(--sunny);">🎯</span>Tekan Mulai — topik pertama akan muncul</li>
        <li><span class="ico" style="background:var(--pink);">⏱</span>Bicara tentang topik tersebut selama waktu yang sudah dipilih</li>
        <li><span class="ico" style="background:var(--cobalt);color:#fff;">🔗</span>Saat topik berganti, sambungkan ceritamu dengan topik baru!</li>
        <li><span class="ico" style="background:var(--green);">🎉</span>Selesaikan semua topik untuk memenangkan sesi!</li>
      </ul>
    </div>

  </div><!-- /stateIdle -->

  <!-- ===== STATE: PLAYING ===== -->
  <div id="statePlaying" class="hidden">

    <!-- Progress dots + phase label -->
    <div class="dots" id="progressDots"></div>
    <div><span class="phase-pill" id="phaseBadge">TOPIK 1 dari 3</span></div>

    <!-- Topic stage -->
    <div id="stageCard" class="stage" style="background:var(--sunny);">
      <p class="stage-category" id="topicCategory"></p>
      <div class="stage-topic" id="topicText"></div>
      <div class="chain-hint hidden" id="chainHint">🔗 Sambungkan dengan topik sebelumnya!</div>
    </div>

    <!-- Timer + controls -->
    <div style="display:flex;align-items:center;justify-content:center;gap:28px;flex-wrap:wrap;">
      <div class="timer-wrap">
        <svg viewBox="0 0 120 120">
          <circle class="timer-track" cx="60" cy="60" r="50"/>
          <circle class="timer-ring" id="timerRing" cx="60" cy="60" r="50"/>
        </svg>
        <div class="timer-label">
          <span class="timer-digits" id="timerDigits">2:00</span>
          <span class="timer-sub">tersisa</span>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:10px;">
        <button id="btnPause" onclick="togglePause()" style="min-width:140px;">⏸ PAUSE</button>
        <button onclick="nextTopicManual()" style="min-width:140px;background:var(--cobalt);color:#fff;">TOPIK LAIN →</button>
      </div>
    </div>

    <!-- Hint -->
    <div class="card" style="padding:14px 18px;">
      <p style="font-family:'Patrick Hand',cursive;font-size:17px;color:var(--muted);">
        💡 Ceritakan pendapat, pengalaman, atau ide kamu selama waktu masih ada!
      </p>
    </div>

  </div><!-- /statePlaying -->

  <!-- ===== STATE: FINISH ===== -->
  <div id="stateFinish" class="hidden">
    <div class="card finish-card">
      <div style="font-size:56px;margin-bottom:8px;">🎉</div>
      <div class="finish-title">WAKTU HABIS!</div>
      <p style="margin-top:10px;font-size:15px;color:var(--muted);">
        Topik yang sudah dibahas:
      </p>
    </div>

    <div style="width:100%;" id="finishRecap"></div>

    <div class="row">
      <button class="yellow" style="flex:1;min-width:140px;" onclick="restartGame()">🔄 MAIN LAGI</button>
      <a href="{{ route('home') }}" style="flex:1;min-width:140px;text-decoration:none;">
        <button style="width:100%;">🏠 MENU UTAMA</button>
      </a>
    </div>
  </div><!-- /stateFinish -->

</main>

<script>
  // ===== SOUND SYNTHESIZER (WEB AUDIO API) =====
  let soundEnabled = true;
  const AudioCtx = window.AudioContext || window.webkitAudioContext;
  let audioCtx = null;

  function getAudioContext() {
    if (!audioCtx) {
      audioCtx = new AudioCtx();
    }
    if (audioCtx.state === 'suspended') {
      audioCtx.resume();
    }
    return audioCtx;
  }

  function playTone(freq, type, duration, delay = 0, vol = 0.15) {
    if (!soundEnabled) return;
    try {
      const ctx = getAudioContext();
      setTimeout(() => {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = type;
        osc.frequency.setValueAtTime(freq, ctx.currentTime);
        gain.gain.setValueAtTime(vol, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start();
        osc.stop(ctx.currentTime + duration);
      }, delay);
    } catch (e) {
      console.warn('Audio error:', e);
    }
  }

  function soundTick() {
    playTone(700, 'triangle', 0.04, 0, 0.08);
  }

  function soundWarningTick() {
    playTone(950, 'sine', 0.08, 0, 0.18);
  }

  function soundNextTopic() {
    // Whoosh + 2-tone melodic chime
    playTone(440, 'triangle', 0.12, 0, 0.2);
    playTone(660, 'triangle', 0.15, 90, 0.2);
    playTone(880, 'sine', 0.25, 180, 0.22);
  }

  function soundFinish() {
    playTone(523.25, 'triangle', 0.18, 0, 0.25);
    playTone(659.25, 'triangle', 0.18, 160, 0.25);
    playTone(783.99, 'triangle', 0.2, 320, 0.25);
    playTone(1046.50, 'sine', 0.5, 480, 0.3);
  }

  function toggleSound() {
    soundEnabled = !soundEnabled;
    const icon = document.getElementById('soundIcon');
    const label = document.getElementById('soundLabel');
    if (icon) icon.textContent = soundEnabled ? '🔊' : '🔇';
    if (label) label.textContent = soundEnabled ? 'SUARA ON' : 'SUARA OFF';
    if (soundEnabled) {
      getAudioContext();
      soundTick();
    }
  }

  // ===== DATA =====
  let allTopics   = @json($topics);
  let sessionTopics = [];
  let currentIdx  = 0;
  let totalPhases = 3;
  let secPerPhase = 120;
  let secLeft     = 0;
  let timerId     = null;
  let isPaused    = false;
  let usedTopics  = [];

  const CIRCUMFERENCE = 2 * Math.PI * 50; // 314.16

  const STAGE_COLORS = [
    { bg: '#FFD21E', text: '#111111' },
    { bg: '#0B44D6', text: '#FFFFFF' },
    { bg: '#EB3B24', text: '#FFFFFF' },
    { bg: '#F9B4C4', text: '#111111' },
    { bg: '#10B981', text: '#FFFFFF' },
  ];
  const RECAP_COLORS = ['#FFD21E','#0B44D6','#EB3B24','#F9B4C4','#10B981'];

  // ===== PREVIEW =====
  function renderPreview() {
    const ul = document.getElementById('topicPreview');
    ul.innerHTML = allTopics.slice(0, 5).map((t, i) => `
      <li class="preview-item">
        <span class="preview-num">${i + 1}</span>
        <div>
          <div>${t.topic}</div>
          <div class="preview-cat">${t.category}</div>
        </div>
      </li>
    `).join('');
  }

  // ===== START GAME =====
  function startGame() {
    getAudioContext();
    soundNextTopic();
    totalPhases = parseInt(document.getElementById('topicCount').value);
    secPerPhase = parseInt(document.getElementById('minutesPerTopic').value) * 60;

    sessionTopics = [...allTopics].sort(() => Math.random() - 0.5).slice(0, totalPhases);
    usedTopics = [];
    currentIdx = 0;
    isPaused = false;

    show('statePlaying');
    hide('stateIdle');
    hide('stateFinish');

    buildDots();
    showTopic(0);
    startTimer();
  }

  // ===== DOTS =====
  function buildDots() {
    const container = document.getElementById('progressDots');
    container.innerHTML = '';
    for (let i = 0; i < totalPhases; i++) {
      const d = document.createElement('div');
      d.className = 'dot';
      d.id = `dot-${i}`;
      container.appendChild(d);
    }
    updateDots();
  }

  function updateDots() {
    for (let i = 0; i < totalPhases; i++) {
      const d = document.getElementById(`dot-${i}`);
      if (!d) continue;
      d.className = 'dot' + (i < currentIdx ? ' done' : i === currentIdx ? ' active' : '');
    }
  }

  // ===== SHOW TOPIC =====
  function showTopic(idx) {
    const card   = document.getElementById('stageCard');
    const tText  = document.getElementById('topicText');
    const tCat   = document.getElementById('topicCategory');
    const hint   = document.getElementById('chainHint');
    const badge  = document.getElementById('phaseBadge');

    const topic = sessionTopics[idx];
    const color = STAGE_COLORS[idx % STAGE_COLORS.length];

    card.classList.add('exit-left');

    setTimeout(() => {
      tText.textContent = topic.topic;
      tCat.textContent  = topic.category;

      card.style.background = color.bg;
      card.style.color      = color.text;
      tCat.style.opacity    = '0.65';

      if (idx > 0) {
        hint.classList.remove('hidden');
        hint.style.background = 'rgba(255,255,255,0.25)';
        hint.style.borderColor = 'rgba(0,0,0,0.2)';
      } else {
        hint.classList.add('hidden');
      }

      badge.textContent = `TOPIK ${idx + 1} dari ${totalPhases}`;
      card.classList.remove('exit-left');
      updateDots();
    }, 220);

    usedTopics.push(topic);
  }

  // ===== TIMER =====
  function startTimer() {
    secLeft = secPerPhase;
    renderTimer();
    if (timerId) clearInterval(timerId);
    timerId = setInterval(tick, 1000);
  }

  function tick() {
    if (isPaused) return;
    secLeft--;
    renderTimer();

    // Suara timer detik
    if (secLeft > 0) {
      if (secLeft <= 5) {
        // 5 detik terakhir bunyi warning lebih tinggi & tegas
        soundWarningTick();
      } else if (secLeft <= 10) {
        // 10 detik terakhir tick normal tiap detik
        soundTick();
      }
    }

    if (secLeft <= 0) nextTopic();
  }

  function renderTimer() {
    const pct    = secLeft / secPerPhase;
    const offset = CIRCUMFERENCE * (1 - pct);
    const ring   = document.getElementById('timerRing');
    ring.style.strokeDashoffset = offset;

    if (pct > 0.5)       ring.style.stroke = 'var(--cobalt)';
    else if (pct > 0.25) ring.style.stroke = 'var(--sunny)';
    else                 ring.style.stroke = 'var(--vermilion)';

    const m = Math.floor(secLeft / 60);
    const s = secLeft % 60;
    document.getElementById('timerDigits').textContent = `${m}:${s.toString().padStart(2, '0')}`;
  }

  function togglePause() {
    isPaused = !isPaused;
    document.getElementById('btnPause').textContent = isPaused ? '▶ LANJUT' : '⏸ PAUSE';
  }

  // ===== NEXT TOPIC =====
  function nextTopic() {
    currentIdx++;
    if (currentIdx >= totalPhases) {
      endGame();
      return;
    }
    soundNextTopic();
    startTimer();
    showTopic(currentIdx);
  }

  function nextTopicManual() {
    clearInterval(timerId);
    nextTopic();
  }

  // ===== END =====
  function endGame() {
    clearInterval(timerId);
    soundFinish();
    hide('statePlaying');
    show('stateFinish');

    const recap = document.getElementById('finishRecap');
    recap.innerHTML = usedTopics.map((t, i) => `
      <div class="recap-item" style="background:${RECAP_COLORS[i % RECAP_COLORS.length]};">
        <div class="recap-num" style="background:#fff;">${i + 1}</div>
        <div>
          <div style="font-weight:800;font-size:15px;">${t.topic}</div>
          <div style="font-size:12px;opacity:0.7;font-weight:600;">${t.category}</div>
        </div>
      </div>
    `).join('');

    launchConfetti();
  }

  // ===== RESTART =====
  function restartGame() {
    fetch("{{ route('games.speaking.topics') }}")
      .then(r => r.json())
      .then(data => { allTopics = data.topics; })
      .catch(() => {});

    hide('stateFinish');
    show('stateIdle');
  }

  // ===== HELPERS =====
  function show(id) { document.getElementById(id).classList.remove('hidden'); }
  function hide(id) { document.getElementById(id).classList.add('hidden'); }

  // ===== CONFETTI =====
  function launchConfetti() {
    const canvas = document.getElementById('confetti-canvas');
    const ctx    = canvas.getContext('2d');
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;

    const pieces = Array.from({ length: 100 }, () => ({
      x: Math.random() * canvas.width,
      y: -20,
      w: 8 + Math.random() * 10,
      h: 5 + Math.random() * 7,
      r: Math.random() * Math.PI * 2,
      vx: (Math.random() - 0.5) * 4,
      vy: 2 + Math.random() * 4,
      vr: (Math.random() - 0.5) * 0.2,
      color: ['#FFD21E','#0B44D6','#EB3B24','#F9B4C4','#10B981'][Math.floor(Math.random() * 5)],
    }));

    let frame;
    (function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      let alive = false;
      pieces.forEach(p => {
        p.x += p.vx; p.y += p.vy; p.r += p.vr; p.vy += 0.1;
        if (p.y < canvas.height + 20) alive = true;
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate(p.r);
        ctx.fillStyle = p.color;
        ctx.strokeStyle = '#111';
        ctx.lineWidth = 1;
        ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
        ctx.strokeRect(-p.w / 2, -p.h / 2, p.w, p.h);
        ctx.restore();
      });
      frame = alive ? requestAnimationFrame(animate) : null;
      if (!alive) ctx.clearRect(0, 0, canvas.width, canvas.height);
    })();

    setTimeout(() => { if (frame) cancelAnimationFrame(frame); ctx.clearRect(0, 0, canvas.width, canvas.height); }, 5000);
  }

  // ===== INIT =====
  window.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    // Set the ring circumference
    document.getElementById('timerRing').style.strokeDasharray = CIRCUMFERENCE;
  });
</script>
</body>
</html>
