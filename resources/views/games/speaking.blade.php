<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SPEAKING ROULETTE — MainYuk! Topik Ngobrol Acak</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('img/icon.png') }}">

  <!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    :root {
      --ink: #111111;
      --sunny: #FFD21E;
      --cobalt: #0B44D6;
      --vermilion: #EB3B24;
      --pastel-pink: #F9B4C4;
      --paper: #FAFAFA;
      --white: #FFFFFF;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--paper);
      color: var(--ink);
      min-height: 100vh;
      overflow-x: hidden;
    }

    .font-comic { font-family: 'Luckiest Guy', cursive; }
    .font-doodle { font-family: 'Patrick Hand', cursive; }

    /* Neo-Brutalism base */
    .neo-card {
      border: 3px solid var(--ink);
      box-shadow: 6px 6px 0px var(--ink);
    }

    .neo-btn {
      border: 2.5px solid var(--ink);
      box-shadow: 4px 4px 0px var(--ink);
      cursor: pointer;
      transition: transform 0.1s, box-shadow 0.1s;
      font-weight: 800;
      font-family: 'Luckiest Guy', cursive;
      letter-spacing: 0.05em;
    }
    .neo-btn:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0px var(--ink); }
    .neo-btn:active { transform: translate(2px, 2px); box-shadow: 2px 2px 0px var(--ink); }

    /* ===== TOPIC CARD ===== */
    #topicCard {
      transition: transform 0.15s cubic-bezier(.4,0,.2,1), opacity 0.15s;
    }
    #topicCard.exit-left  { transform: translateX(-120%) rotate(-8deg); opacity: 0; }
    #topicCard.exit-right { transform: translateX(120%) rotate(8deg); opacity: 0; }
    #topicCard.enter      { transform: translateX(0) rotate(0); opacity: 1; }

    /* ===== TIMER RING ===== */
    .timer-ring { transition: stroke-dashoffset 1s linear; }

    /* ===== PROGRESS DOTS ===== */
    .dot {
      width: 14px; height: 14px;
      border-radius: 50%;
      border: 2px solid var(--ink);
      background: white;
      transition: background 0.3s;
    }
    .dot.done { background: var(--sunny); }
    .dot.active { background: var(--cobalt); }
    .dot.up { background: var(--vermilion); }

    /* ===== SLIDE NUMBERS ===== */
    .slide-num {
      font-family: 'Luckiest Guy', cursive;
      font-size: 0.75rem;
    }

    /* ===== CONFETTI CANVAS ===== */
    #confettiCanvas {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      pointer-events: none;
      z-index: 50;
    }

    /* ===== PHASE INDICATOR ===== */
    .phase-badge {
      animation: pulseBadge 1s infinite alternate;
    }
    @keyframes pulseBadge {
      from { box-shadow: 4px 4px 0 var(--ink); }
      to   { box-shadow: 6px 6px 0 var(--ink), 0 0 12px rgba(11,68,214,0.3); }
    }

    /* ===== BACK NAV ===== */
    .back-link {
      display: inline-flex; align-items: center; gap: 6px;
      font-weight: 800; font-size: 0.8rem; text-decoration: none;
      color: var(--ink);
      border: 2px solid var(--ink);
      padding: 6px 14px;
      border-radius: 999px;
      background: white;
      box-shadow: 3px 3px 0 var(--ink);
      transition: transform 0.1s, box-shadow 0.1s;
    }
    .back-link:hover { transform: translate(-1px,-1px); box-shadow: 4px 4px 0 var(--ink); }
  </style>
</head>
<body>

<canvas id="confettiCanvas"></canvas>

<!-- ============ HEADER NAV ============ -->
<header style="background:var(--cobalt);border-bottom:3px solid var(--ink);" class="px-5 py-3 flex items-center justify-between">
  <a href="{{ route('home') }}" class="back-link">
    ← BACK
  </a>
  <div class="font-comic text-white text-lg sm:text-2xl tracking-wide">
    SPEAKING <span style="color:var(--sunny)">ROULETTE</span>
  </div>
  <div class="font-doodle text-white text-xs sm:text-sm font-bold opacity-80">
    {{ $totalTopics }}+ TOPIK
  </div>
</header>

<!-- ============ MAIN ============ -->
<main class="max-w-2xl mx-auto px-4 pt-8 pb-20">

  <!-- STATE: IDLE (sebelum mulai) -->
  <div id="stateIdle">
    <div class="neo-card rounded-3xl bg-white p-8 text-center mt-6">

      <!-- Art Dekoratif -->
      <div class="flex justify-center mb-6">
        <div class="neo-card rounded-2xl bg-sunny w-24 h-24 flex items-center justify-center text-5xl rotate-3">
          🎤
        </div>
      </div>

      <h1 class="font-comic text-3xl sm:text-5xl text-ink mb-3 leading-tight">
        SPEAKING<br><span style="color:var(--cobalt)">ROULETTE!</span>
      </h1>

      <p class="font-doodle text-base sm:text-lg text-ink/80 mb-2 font-bold leading-relaxed">
        Topik bahasa Indonesia acak muncul tiap <strong>2 menit</strong>.<br>
        Total waktu <strong>6 menit</strong> (3 topik).<br>
        Ceritakan pengalamanmu dan sambung dengan topik berikutnya!
      </p>

      <div class="flex gap-3 justify-center flex-wrap mt-5 mb-6 text-xs font-extrabold">
        <span class="neo-card rounded-full px-3 py-1 bg-sunny">⏱ 2 Menit / Topik</span>
        <span class="neo-card rounded-full px-3 py-1 bg-pastelPink" style="border-color:var(--ink)">🗣 3 Topik Total</span>
        <span class="neo-card rounded-full px-3 py-1" style="background:var(--cobalt);color:white">🎲 Acak Tiap Sesi</span>
      </div>

      <!-- Timer & Topik per sesi (opsional ubah) -->
      <div class="neo-card rounded-2xl bg-paper p-4 mb-6 text-left">
        <p class="font-extrabold text-sm mb-3">⚙️ Pengaturan Sesi</p>
        <div class="flex flex-wrap gap-4 items-center">
          <div>
            <label class="text-xs font-bold text-ink/60 block mb-1">Menit per Topik</label>
            <select id="minutesPerTopic" class="neo-card rounded-xl px-3 py-2 font-bold text-sm bg-white cursor-pointer">
              <option value="1">1 menit</option>
              <option value="2" selected>2 menit</option>
              <option value="3">3 menit</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-bold text-ink/60 block mb-1">Jumlah Topik</label>
            <select id="topicCount" class="neo-card rounded-xl px-3 py-2 font-bold text-sm bg-white cursor-pointer">
              <option value="2">2 topik</option>
              <option value="3" selected>3 topik</option>
              <option value="4">4 topik</option>
              <option value="5">5 topik</option>
            </select>
          </div>
        </div>
      </div>

      <button id="btnStart" onclick="startGame()" class="neo-btn bg-vermilion text-white rounded-2xl px-10 py-4 text-2xl w-full">
        🎤 MULAI SPEAKING!
      </button>
    </div>

    <!-- Preview beberapa topik acak -->
    <div class="mt-6 neo-card rounded-2xl bg-white p-5">
      <p class="font-comic text-base text-ink mb-3">CONTOH TOPIK HARI INI:</p>
      <ul id="topicPreview" class="space-y-2">
        <!-- filled by JS -->
      </ul>
    </div>
  </div>

  <!-- STATE: PLAYING -->
  <div id="statePlaying" class="hidden">

    <!-- Progress dots -->
    <div class="flex items-center justify-center gap-3 my-4" id="progressDots"></div>

    <!-- Phase label -->
    <div class="flex justify-center mb-4">
      <div id="phaseBadge" class="phase-badge neo-card rounded-full px-5 py-2 font-comic text-sm text-white" style="background:var(--cobalt)">
        TOPIK 1 dari 3
      </div>
    </div>

    <!-- Main Topic Card -->
    <div class="relative overflow-hidden">
      <div id="topicCard" class="neo-card rounded-3xl p-8 sm:p-10 text-center min-h-[220px] flex flex-col items-center justify-center" style="background:var(--sunny)">
        <p id="topicCategory" class="text-xs font-extrabold text-ink/60 uppercase tracking-widest mb-3"></p>
        <h2 id="topicText" class="font-comic text-2xl sm:text-4xl text-ink leading-tight"></h2>
        <div id="transitionHint" class="hidden mt-4 text-xs font-bold text-ink/60 font-doodle">
          Sambungkan dengan topik sebelumnya! 🔗
        </div>
      </div>
    </div>

    <!-- Timer Ring + Countdown -->
    <div class="flex flex-col items-center mt-6">
      <div class="relative w-32 h-32">
        <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
          <!-- Track -->
          <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
          <!-- Progress ring -->
          <circle id="timerRing" cx="60" cy="60" r="52" fill="none"
            stroke="var(--cobalt)" stroke-width="10"
            stroke-linecap="round"
            stroke-dasharray="326.7"
            stroke-dashoffset="0"
            class="timer-ring"/>
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
          <span id="timerDisplay" class="font-comic text-3xl text-ink">2:00</span>
          <span class="text-[10px] font-bold text-ink/50 uppercase tracking-wide">tersisa</span>
        </div>
      </div>

      <!-- Manual next / pause buttons -->
      <div class="flex gap-3 mt-5">
        <button id="btnPause" onclick="togglePause()" class="neo-btn bg-white text-ink rounded-xl px-5 py-2.5 text-sm">
          ⏸ PAUSE
        </button>
        <button id="btnNext" onclick="nextTopicManual()" class="neo-btn bg-cobalt text-white rounded-xl px-5 py-2.5 text-sm">
          TOPIK LAIN →
        </button>
      </div>
    </div>

    <!-- Kategori hint -->
    <div class="mt-6 neo-card rounded-2xl bg-white p-4 text-center">
      <p class="font-doodle text-sm font-bold text-ink/70">
        💡 Ceritakan pendapat, pengalaman, atau ide kamu selama waktu masih ada!
      </p>
    </div>
  </div>

  <!-- STATE: FINISH -->
  <div id="stateFinish" class="hidden">
    <div class="neo-card rounded-3xl bg-white p-8 text-center mt-6">
      <div class="text-6xl mb-4">🎉</div>
      <h2 class="font-comic text-3xl sm:text-5xl text-ink mb-3">WAKTU HABIS!</h2>
      <p class="font-doodle text-base text-ink/70 font-bold mb-6">
        Kamu udah speaking selama <span id="totalTimeDisplay" class="text-cobalt">6 menit</span>!<br>
        Topik yang dibahas:
      </p>

      <ul id="finishTopicList" class="space-y-2 text-left mb-7"></ul>

      <div class="flex gap-3 justify-center flex-wrap">
        <button onclick="restartGame()" class="neo-btn bg-sunny text-ink rounded-2xl px-7 py-3 text-lg">
          🔄 MAIN LAGI
        </button>
        <a href="{{ route('home') }}" class="neo-btn bg-white text-ink rounded-2xl px-7 py-3 text-lg inline-block text-center" style="text-decoration:none">
          🏠 MENU
        </a>
      </div>
    </div>
  </div>

</main>

<script>
  // ===== DATA TOPIK DARI SERVER =====
  let allTopics = @json($topics);
  let sessionTopics = [];

  let currentIdx  = 0;
  let totalPhases = 3;
  let secPerPhase = 120; // 2 menit
  let secLeft     = 0;
  let timerInterval = null;
  let isPaused    = false;
  let usedTopics  = [];

  const CARD_COLORS = [
    { bg: '#FFD21E', text: '#111111' },
    { bg: '#0B44D6', text: '#FFFFFF' },
    { bg: '#EB3B24', text: '#FFFFFF' },
    { bg: '#F9B4C4', text: '#111111' },
    { bg: '#10b981', text: '#FFFFFF' },
  ];

  // ===== PREVIEW TOPIK =====
  function renderPreview() {
    const ul = document.getElementById('topicPreview');
    const sample = allTopics.slice(0, 5);
    ul.innerHTML = sample.map((t, i) => `
      <li class="flex items-start gap-2 text-sm font-bold">
        <span class="shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-xs font-black neo-card" style="background:var(--sunny)">${i+1}</span>
        <span>${t.topic} <span class="text-ink/40 font-semibold text-xs">— ${t.category}</span></span>
      </li>
    `).join('');
  }

  // ===== MULAI GAME =====
  function startGame() {
    totalPhases = parseInt(document.getElementById('topicCount').value);
    secPerPhase = parseInt(document.getElementById('minutesPerTopic').value) * 60;

    // Ambil topik acak sejumlah totalPhases
    let shuffled = [...allTopics].sort(() => Math.random() - 0.5);
    sessionTopics = shuffled.slice(0, totalPhases);
    usedTopics = [];
    currentIdx = 0;
    isPaused = false;

    document.getElementById('stateIdle').classList.add('hidden');
    document.getElementById('stateFinish').classList.add('hidden');
    document.getElementById('statePlaying').classList.remove('hidden');

    buildProgressDots();
    showTopic(0);
    startTimer();
  }

  // ===== BUILD PROGRESS DOTS =====
  function buildProgressDots() {
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
      d.classList.remove('done', 'active', 'up');
      if (i < currentIdx) d.classList.add('done');
      else if (i === currentIdx) d.classList.add('active');
    }
  }

  // ===== TAMPILKAN TOPIK =====
  function showTopic(idx, direction = 'right') {
    const card   = document.getElementById('topicCard');
    const tText  = document.getElementById('topicText');
    const tCat   = document.getElementById('topicCategory');
    const hint   = document.getElementById('transitionHint');
    const badge  = document.getElementById('phaseBadge');

    const topic = sessionTopics[idx];
    const color = CARD_COLORS[idx % CARD_COLORS.length];

    // Animate out
    card.classList.remove('enter');
    card.classList.add(direction === 'right' ? 'exit-left' : 'exit-right');

    setTimeout(() => {
      tText.textContent = topic.topic;
      tCat.textContent  = topic.category;
      card.style.background = color.bg;
      card.style.color = color.text;
      tCat.style.color = color.text === '#FFFFFF' ? 'rgba(255,255,255,0.6)' : 'rgba(0,0,0,0.5)';

      badge.textContent = `TOPIK ${idx + 1} dari ${totalPhases}`;

      if (idx > 0) {
        hint.classList.remove('hidden');
        hint.style.color = color.text === '#FFFFFF' ? 'rgba(255,255,255,0.6)' : 'rgba(0,0,0,0.5)';
      } else {
        hint.classList.add('hidden');
      }

      // Animate in
      card.classList.remove('exit-left', 'exit-right');
      card.classList.add('enter');

      updateDots();
    }, 180);

    usedTopics.push(topic);
  }

  // ===== TIMER =====
  const circumference = 2 * Math.PI * 52; // 326.7

  function startTimer() {
    secLeft = secPerPhase;
    renderTimer();
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(tick, 1000);
  }

  function tick() {
    if (isPaused) return;
    secLeft--;
    renderTimer();

    if (secLeft <= 0) {
      nextTopic();
    }
  }

  function renderTimer() {
    const pct    = secLeft / secPerPhase;
    const offset = circumference * (1 - pct);
    document.getElementById('timerRing').style.strokeDashoffset = offset;

    const m = Math.floor(secLeft / 60);
    const s = secLeft % 60;
    document.getElementById('timerDisplay').textContent =
      `${m}:${s.toString().padStart(2, '0')}`;

    // Warna ring sesuai sisa waktu
    const ring = document.getElementById('timerRing');
    if (pct > 0.5)       ring.style.stroke = 'var(--cobalt)';
    else if (pct > 0.25) ring.style.stroke = 'var(--sunny)';
    else                 ring.style.stroke = 'var(--vermilion)';
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
    startTimer();
    showTopic(currentIdx);
  }

  function nextTopicManual() {
    if (currentIdx >= totalPhases - 1) {
      endGame();
      return;
    }
    clearInterval(timerInterval);
    nextTopic();
  }

  // ===== END GAME =====
  function endGame() {
    clearInterval(timerInterval);
    document.getElementById('statePlaying').classList.add('hidden');
    document.getElementById('stateFinish').classList.remove('hidden');

    const totalSec = totalPhases * secPerPhase;
    const totalMin = Math.floor(totalSec / 60);
    document.getElementById('totalTimeDisplay').textContent = `${totalMin} menit`;

    const ul = document.getElementById('finishTopicList');
    ul.innerHTML = usedTopics.map((t, i) => `
      <li class="neo-card rounded-xl px-4 py-3 flex items-center gap-3" style="background:${CARD_COLORS[i % CARD_COLORS.length].bg}; color:${CARD_COLORS[i % CARD_COLORS.length].text}">
        <span class="font-comic text-lg">${i + 1}</span>
        <div>
          <div class="font-bold text-sm">${t.topic}</div>
          <div class="text-xs opacity-70">${t.category}</div>
        </div>
      </li>
    `).join('');

    launchConfetti();
  }

  // ===== RESTART =====
  function restartGame() {
    // Minta topik baru dari server
    fetch("{{ route('games.speaking.topics') }}")
      .then(r => r.json())
      .then(data => {
        allTopics = data.topics;
        renderPreview();
      })
      .catch(() => {/* pakai topik lama */});

    document.getElementById('stateFinish').classList.add('hidden');
    document.getElementById('stateIdle').classList.remove('hidden');
  }

  // ===== CONFETTI =====
  function launchConfetti() {
    const canvas  = document.getElementById('confettiCanvas');
    const ctx     = canvas.getContext('2d');
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;

    const pieces = Array.from({ length: 120 }, () => ({
      x: Math.random() * canvas.width,
      y: -20,
      w: 8 + Math.random() * 10,
      h: 5 + Math.random() * 7,
      r: Math.random() * Math.PI * 2,
      vx: (Math.random() - 0.5) * 4,
      vy: 2 + Math.random() * 4,
      vr: (Math.random() - 0.5) * 0.2,
      color: ['#FFD21E','#0B44D6','#EB3B24','#F9B4C4','#10b981','#ffffff'][Math.floor(Math.random()*6)],
    }));

    let frame;
    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      let alive = false;
      pieces.forEach(p => {
        p.x  += p.vx;
        p.y  += p.vy;
        p.r  += p.vr;
        p.vy += 0.1;
        if (p.y < canvas.height + 20) alive = true;
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate(p.r);
        ctx.fillStyle = p.color;
        ctx.strokeStyle = '#111';
        ctx.lineWidth = 1;
        ctx.fillRect(-p.w/2, -p.h/2, p.w, p.h);
        ctx.strokeRect(-p.w/2, -p.h/2, p.w, p.h);
        ctx.restore();
      });
      if (alive) frame = requestAnimationFrame(animate);
      else ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
    animate();
    setTimeout(() => { cancelAnimationFrame(frame); ctx.clearRect(0,0,canvas.width,canvas.height); }, 5000);
  }

  // ===== INIT =====
  window.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    renderPreview();
  });
</script>
</body>
</html>
