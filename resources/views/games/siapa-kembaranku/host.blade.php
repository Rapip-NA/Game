<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Host: SIAPA KEMBARANKU? — MainYuk!</title>

  <!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- QR Code & Confetti -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

  <!-- Pusher & Echo (for Reverb) -->
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            display: ['"Luckiest Guy"', 'cursive'],
            hand: ['"Patrick Hand"', 'cursive'],
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            paper: '#FAFAFA',
            cobalt: '#0B44D6',
            sunny: '#FFD21E',
            vermilion: '#EB3B24',
            pastelPink: '#F9B4C4',
            ink: '#111111',
            mint: '#22C55E',
          }
        }
      }
    }
  </script>

  <style>
    body {
      background-color: #FAFAFA;
      background-image: radial-gradient(#CBD5E1 1.4px, transparent 1.4px);
      background-size: 22px 22px;
      color: #111111;
      overflow-x: hidden;
    }

    .font-comic {
      font-family: 'Luckiest Guy', cursive;
      letter-spacing: 0.03em;
    }

    .font-doodle {
      font-family: 'Patrick Hand', cursive;
    }

    .sticker-card {
      border: 3.5px solid #111111;
      box-shadow: 6px 6px 0px #111111;
      transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .sticker-card-lg {
      border: 4px solid #111111;
      box-shadow: 8px 8px 0px #111111;
    }

    .sticker-btn {
      border: 3px solid #111111;
      box-shadow: 4px 4px 0px #111111;
      transition: all 0.15s ease;
      cursor: pointer;
    }

    .sticker-btn:hover:not(:disabled) {
      transform: translate(-2px, -2px);
      box-shadow: 6px 6px 0px #111111;
    }

    .sticker-btn:active:not(:disabled) {
      transform: translate(2px, 2px);
      box-shadow: 1px 1px 0px #111111;
    }

    .sticker-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      box-shadow: 2px 2px 0px #111111;
    }

    @keyframes pulseSoft {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.03); }
    }
    .animate-pulse-soft {
      animation: pulseSoft 2s infinite ease-in-out;
    }

    @keyframes wiggle {
      0%, 100% { transform: rotate(-1.5deg); }
      50% { transform: rotate(1.5deg); }
    }
    .animate-wiggle {
      animation: wiggle 3s infinite ease-in-out;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col font-sans selection:bg-sunny selection:text-ink">

  <!-- TOP BAR -->
  <header class="border-b-4 border-ink bg-white sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" class="sticker-btn bg-paper px-3 py-1.5 rounded-xl font-extrabold text-xs flex items-center gap-1">
          <i data-lucide="arrow-left" class="w-4 h-4"></i> PORTAL
        </a>
        <div class="flex items-center gap-2">
          <span class="text-2xl">🎭</span>
          <h1 class="font-comic text-2xl sm:text-3xl tracking-wide text-ink">SIAPA KEMBARANKU?</h1>
          <span class="bg-sunny border-2 border-ink text-ink font-comic text-xs px-2.5 py-0.5 rounded-full rotate-2">HOST SCREEN</span>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <!-- Sound toggle -->
        <button id="btnSound" onclick="toggleSound()" class="sticker-btn bg-white px-3 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1.5">
          <span id="soundIcon">🔊</span> <span id="soundLabel">SUARA ON</span>
        </button>

        <!-- Room Code Badge -->
        <div class="bg-cobalt text-white border-2 border-ink px-4 py-1.5 rounded-xl shadow-[3px_3px_0px_#111111] flex items-center gap-2">
          <span class="text-[10px] font-extrabold tracking-wider uppercase opacity-80">ROOM:</span>
          <span id="roomCodeText" class="font-comic text-xl tracking-wider text-sunny">{{ $game->code }}</span>
        </div>
      </div>
    </div>
  </header>

  <!-- MAIN CONTAINER -->
  <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 py-6">

    <!-- 1. LOBBY VIEW (WAITING FOR PLAYERS) -->
    <div id="viewLobby" class="space-y-6">
      
      <!-- Banner Join & QR -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        <!-- Left: Large QR Code for Joining -->
        <div class="lg:col-span-5 sticker-card-lg bg-white rounded-3xl p-6 flex flex-col items-center justify-center text-center relative overflow-hidden">
          <div class="absolute -top-3 left-6 bg-vermilion text-white font-doodle font-bold text-base px-4 py-1 rounded-full border-2 border-ink rotate-[-2deg]">
            📱 SCAN DENGAN KAMERA HP!
          </div>

          <div class="mt-4 p-4 bg-white border-4 border-ink rounded-2xl shadow-[5px_5px_0px_#111111]">
            <div id="joinQrContainer" class="flex items-center justify-center"></div>
          </div>

          <div class="mt-4 w-full">
            <p class="font-bold text-xs uppercase text-slate-500">Atau buka link ini di browser HP:</p>
            <div class="mt-1 flex items-center justify-center gap-2">
              <span id="joinUrlText" class="font-comic text-lg text-cobalt bg-blue-50 border-2 border-ink px-3 py-1 rounded-xl break-all"></span>
              <button onclick="copyJoinUrl()" class="sticker-btn bg-sunny px-3 py-1 rounded-xl text-xs font-black">SALIN</button>
            </div>
            <p class="mt-2 text-xs font-semibold text-slate-600">Kode Room: <strong class="text-ink font-black text-base" id="joinCodeDisplay">{{ $game->code }}</strong></p>
          </div>
        </div>

        <!-- Right: Lobby Settings & Start Button -->
        <div class="lg:col-span-7 sticker-card-lg bg-sunny rounded-3xl p-6 sm:p-8 flex flex-col justify-between">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="font-comic text-3xl sm:text-4xl text-ink">LOBBY PERMAINAN</h2>
                <p class="font-semibold text-sm text-ink/80">Ajak semua teman sekelas scan QR di sebelah kiri untuk masuk!</p>
              </div>
              <span class="text-4xl">🎪</span>
            </div>

            <!-- Game Rules Box -->
            <div class="bg-white border-3 border-ink rounded-2xl p-4 shadow-[4px_4px_0px_#111111]">
              <h3 class="font-comic text-base text-ink mb-1">CARA MAIN SINGKAT:</h3>
              <ul class="text-xs sm:text-sm font-semibold text-slate-700 space-y-1 list-disc list-inside">
                <li>Tiap pemain dapat <strong>1 logo rahasia</strong> yang <strong>tidak bisa dilihat sendiri</strong>.</li>
                <li>Pemain harus saling scan QR code teman untuk melihat logo orang lain.</li>
                <li>Diskusi & cari tahu siapa yang punya <strong>logo sama (kembaran)</strong> denganmu!</li>
                <li>Pemain dengan perolehan vote terendah akan <strong>tereliminasi</strong> tiap ronde!</li>
              </ul>
            </div>

            <!-- Host Settings: Timer & Tie-break -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
              <div class="bg-white border-2 border-ink p-3 rounded-xl shadow-[3px_3px_0px_#111111]">
                <label class="block font-bold text-xs uppercase text-slate-600 mb-1">⏱️ Waktu Diskusi:</label>
                <select id="selectTimer" class="w-full bg-paper border-2 border-ink rounded-lg font-bold text-sm px-2.5 py-1.5 focus:outline-none">
                  <option value="120">2 Menit (Kilat)</option>
                  <option value="180">3 Menit (Standar)</option>
                  <option value="300" selected>5 Menit (Santai)</option>
                  <option value="420">7 Menit (Kelas Besar)</option>
                </select>
              </div>

              <div class="bg-white border-2 border-ink p-3 rounded-xl shadow-[3px_3px_0px_#111111]">
                <label class="block font-bold text-xs uppercase text-slate-600 mb-1">⚖️ Jika Suara Seri Terendah:</label>
                <select id="selectTieBreak" class="w-full bg-paper border-2 border-ink rounded-lg font-bold text-sm px-2.5 py-1.5 focus:outline-none">
                  <option value="kick_all" selected>Keluarkan Semua (Lebih Cepat)</option>
                  <option value="random">Pilih Acak 1 Orang</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Bottom Action Buttons -->
          <div class="mt-6 flex flex-wrap items-center gap-4">
            <button id="btnStartGame" onclick="startGame()" class="sticker-btn flex-1 bg-cobalt text-white font-comic text-xl sm:text-2xl py-4 px-6 rounded-2xl flex items-center justify-center gap-2 hover:bg-blue-700">
              <span>MULAI PERMAINAN</span>
              <span>➔</span>
            </button>
            <button onclick="createNewRoom()" class="sticker-btn bg-white text-ink font-bold text-xs px-4 py-3.5 rounded-2xl">
              🔄 RESET ROOM
            </button>
          </div>
        </div>

      </div>

      <!-- Player List In Lobby -->
      <div class="sticker-card bg-white rounded-3xl p-6">
        <div class="flex items-center justify-between mb-4 border-b-2 border-ink pb-3">
          <div class="flex items-center gap-3">
            <h3 class="font-comic text-2xl text-ink">DAFTAR PEMAIN DI LOBBY</h3>
            <span id="playerCountBadge" class="bg-vermilion text-white font-comic text-sm px-3 py-0.5 rounded-full border-2 border-ink">
              0 PEMAIN
            </span>
          </div>
          <span class="text-xs font-bold text-slate-500">Minimal 3 pemain untuk mulai</span>
        </div>

        <div id="lobbyPlayerGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
          <!-- Populated via JS -->
        </div>

        <div id="emptyLobbyNotice" class="text-center py-10">
          <span class="text-5xl block mb-2">⏳</span>
          <p class="font-comic text-xl text-ink">BELUM ADA PEMAIN YANG JOIN</p>
          <p class="font-semibold text-xs text-slate-500 mt-1">Minta teman-teman membuka link atau scan QR di atas untuk bergabung!</p>
        </div>
      </div>

    </div>

    <!-- 2. DISCUSSION PHASE VIEW (ROUND IN PROGRESS) -->
    <div id="viewDiscussion" class="hidden space-y-6">
      
      <!-- Top Status Banner -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
        
        <!-- Round Info -->
        <div class="md:col-span-4 sticker-card bg-cobalt text-white rounded-3xl p-6 flex flex-col justify-between">
          <div class="flex items-center justify-between">
            <span class="bg-sunny text-ink font-comic text-xs px-3 py-1 rounded-full border-2 border-ink">
              FASE 1: DISKUSI & SCAN
            </span>
            <span class="text-2xl">🔍</span>
          </div>
          <div class="mt-4">
            <p class="text-xs uppercase font-extrabold text-blue-200">RONDE KE:</p>
            <h2 id="currentRoundDisplay" class="font-comic text-5xl text-sunny">1</h2>
          </div>
          <p class="text-xs font-bold text-blue-100 mt-2">Pemain saling scan QR dan menebak siapa kembarannya!</p>
        </div>

        <!-- Giant High-Contrast Countdown Timer -->
        <div class="md:col-span-8 sticker-card-lg bg-white rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
          <div class="text-center sm:text-left">
            <span class="text-xs uppercase font-black text-slate-500 tracking-wider">SISA WAKTU DISKUSI</span>
            <div id="timerDisplay" class="font-comic text-6xl sm:text-7xl lg:text-8xl text-ink tracking-tight font-black leading-none my-1">
              05:00
            </div>
            <p id="timerStatusNotice" class="font-semibold text-xs text-slate-500">Waktu berjalan... Ayo gerak dan scan temanmu!</p>
          </div>

          <div class="flex flex-col gap-3 w-full sm:w-auto">
            <button onclick="startVotingPhase()" class="sticker-btn bg-vermilion text-white font-comic text-lg sm:text-xl px-6 py-4 rounded-2xl flex items-center justify-center gap-2">
              <span>SELESAI DISKUSI & MULAI VOTING ➔</span>
            </button>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-600 justify-center">
              <span>⚡ Pemain Aktif: <strong id="activePlayersCountDisc" class="text-ink">0</strong> orang</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Live Scan Activity Feed & Players Wall -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Players on Board -->
        <div class="lg:col-span-8 sticker-card bg-white rounded-3xl p-6">
          <div class="flex items-center justify-between mb-4 border-b-2 border-ink pb-3">
            <h3 class="font-comic text-2xl text-ink">PEMAIN DALAM GAME</h3>
            <span class="text-xs font-bold text-slate-500">Logo dirahasiakan sampai tahap Reveal</span>
          </div>

          <div id="discussionPlayerGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <!-- Populated via JS -->
          </div>
        </div>

        <!-- Live Scan Feed -->
        <div class="lg:col-span-4 sticker-card bg-paper rounded-3xl p-6 flex flex-col">
          <div class="flex items-center justify-between mb-3 border-b-2 border-ink pb-2">
            <h3 class="font-comic text-lg text-ink flex items-center gap-1.5">
              <span>⚡</span> AKTIVITAS SCAN
            </h3>
            <span id="scanCountBadge" class="bg-cobalt text-white font-black text-xs px-2 py-0.5 rounded-full">0 SCAN</span>
          </div>
          <div id="scanFeedList" class="flex-1 space-y-2 overflow-y-auto max-h-[360px] pr-1">
            <p class="text-xs font-semibold text-slate-400 italic text-center py-8">Belum ada scan yang terjadi...</p>
          </div>
        </div>

      </div>

    </div>

    <!-- 3. VOTING PHASE VIEW -->
    <div id="viewVoting" class="hidden space-y-6">
      
      <div class="sticker-card-lg bg-sunny rounded-3xl p-6 sm:p-8 text-center relative overflow-hidden">
        <span class="bg-vermilion text-white font-comic text-xs px-4 py-1 rounded-full border-2 border-ink inline-block mb-3">
          FASE 2: PEMILIHAN KEMBARAN
        </span>
        <h2 class="font-comic text-4xl sm:text-5xl lg:text-6xl text-ink uppercase">
          WAKTUNYA VOTING! 🗳️
        </h2>
        <p class="font-semibold text-base sm:text-lg text-ink/90 max-w-2xl mx-auto mt-2">
          Semua pemain sedang memilih <strong>3 orang</strong> di HP masing-masing yang mereka yakini punya logo yang sama!
        </p>

        <!-- Progress Bar -->
        <div class="max-w-xl mx-auto mt-6 bg-white border-4 border-ink rounded-full p-1.5 shadow-[4px_4px_0px_#111111]">
          <div id="voteProgressBar" class="bg-cobalt text-white font-comic text-xs py-2 rounded-full transition-all duration-500 text-center" style="width: 0%">
            0%
          </div>
        </div>
        <p id="voteProgressText" class="font-comic text-xl text-ink mt-3">0 / 0 PEMAIN SUDAH VOTING</p>

        <div class="mt-6 flex justify-center">
          <button id="btnRevealResults" onclick="revealResults()" class="sticker-btn bg-vermilion text-white font-comic text-xl sm:text-2xl px-8 py-4 rounded-2xl flex items-center gap-2">
            <span>TAMPILKAN HASIL & REVEAL! 💥</span>
          </button>
        </div>
      </div>

      <!-- Voting Players Status -->
      <div class="sticker-card bg-white rounded-3xl p-6">
        <h3 class="font-comic text-2xl text-ink mb-4 border-b-2 border-ink pb-3">STATUS VOTING PEMAIN</h3>
        <div id="votingPlayerGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
          <!-- Populated via JS -->
        </div>
      </div>

    </div>

    <!-- 4. REVEAL / RESULTS VIEW (HIGH DRAMA PROJECTION) -->
    <div id="viewReveal" class="hidden space-y-8">
      
      <!-- Winner Banner if Finished -->
      <div id="winnerBanner" class="hidden sticker-card-lg bg-sunny rounded-3xl p-8 text-center animate-wiggle">
        <span class="text-6xl block mb-2">👑 🏆 👑</span>
        <h2 class="font-comic text-5xl sm:text-6xl text-ink uppercase">KITA PUNYA PEMENANG!</h2>
        <p class="font-bold text-lg text-slate-700 mt-2">Permainan selesai! Inilah pemain yang berhasil bertahan sampai akhir:</p>
        <div id="winnersList" class="flex flex-wrap items-center justify-center gap-4 mt-6"></div>
      </div>

      <!-- Elimination Notice -->
      <div id="eliminationBanner" class="sticker-card-lg bg-vermilion text-white rounded-3xl p-6 sm:p-8 text-center relative overflow-hidden">
        <div class="inline-block bg-white text-vermilion font-comic text-sm px-4 py-1 rounded-full border-2 border-ink mb-2">
          HASIL ELIMINASI RONDE INI
        </div>
        <h2 class="font-comic text-4xl sm:text-5xl lg:text-6xl uppercase tracking-wide">
          YANG TERELIMINASI (KICK)! 💥
        </h2>
        <div id="kickedPlayersGrid" class="flex flex-wrap items-center justify-center gap-4 mt-6">
          <!-- Kicked cards -->
        </div>
      </div>

      <!-- True Logos Revealed (Grouped by Logo / Twin Grouping) -->
      <div class="sticker-card-lg bg-white rounded-3xl p-6 sm:p-8">
        <div class="text-center mb-6">
          <span class="bg-cobalt text-white font-comic text-xs px-4 py-1 rounded-full border-2 border-ink">
            REVEAL LOGO RAHASIA
          </span>
          <h3 class="font-comic text-3xl sm:text-4xl text-ink mt-2">BONGKAR SEMUA KEMBARAN! 🎭</h3>
          <p class="font-semibold text-sm text-slate-600">Berikut logo asli tiap pemain dan kelompok kembarannya:</p>
        </div>

        <div id="revealLogoGroups" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Groups populated via JS -->
        </div>
      </div>

      <!-- Action Buttons: Next Round or New Game -->
      <div class="flex flex-wrap items-center justify-center gap-4 py-4">
        <button id="btnNextRound" onclick="nextRound()" class="sticker-btn bg-cobalt text-white font-comic text-2xl px-8 py-5 rounded-2xl flex items-center gap-3">
          <span>RONDE BERIKUTNYA</span>
          <span>➔</span>
        </button>
        <button onclick="createNewRoom()" class="sticker-btn bg-sunny text-ink font-comic text-xl px-6 py-5 rounded-2xl">
          🔄 MAIN DARI AWAL (ROOM BARU)
        </button>
      </div>

    </div>

  </main>

  <!-- JAVASCRIPT LOGIC -->
  <script>
    // State
    const GAME_CODE = "{{ $game->code }}";
    let gameState = @json($game);
    let soundEnabled = true;
    let timerInterval = null;
    let secondsLeft = 300;
    let echoInstance = null;
    let currentView = 'lobby'; // lobby, discussion, voting, reveal

    // Simple Web Audio API Synthesizer
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

    function playTone(freq, type, duration, delay = 0) {
      if (!soundEnabled) return;
      try {
        const ctx = getAudioContext();
        setTimeout(() => {
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          osc.type = type;
          osc.frequency.setValueAtTime(freq, ctx.currentTime);
          gain.gain.setValueAtTime(0.15, ctx.currentTime);
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

    function soundPop() { playTone(540, 'sine', 0.12); }
    function soundTick() { playTone(880, 'triangle', 0.05); }
    function soundBeepHigh() { playTone(1200, 'sine', 0.2); }
    function soundGong() { playTone(180, 'sawtooth', 0.8); }
    function soundFanfare() {
      playTone(440, 'triangle', 0.2, 0);
      playTone(554, 'triangle', 0.2, 180);
      playTone(659, 'triangle', 0.4, 360);
      playTone(880, 'triangle', 0.6, 540);
    }

    function toggleSound() {
      soundEnabled = !soundEnabled;
      document.getElementById('soundIcon').textContent = soundEnabled ? '🔊' : '🔇';
      document.getElementById('soundLabel').textContent = soundEnabled ? 'SUARA ON' : 'SUARA OFF';
    }

    // Generate Join QR
    function initJoinQR() {
      const joinUrl = `${window.location.origin}/siapa-kembaranku/play?code=${GAME_CODE}`;
      document.getElementById('joinUrlText').textContent = `${window.location.host}/play?code=${GAME_CODE}`;
      
      const container = document.getElementById('joinQrContainer');
      container.innerHTML = '';
      new QRCode(container, {
        text: joinUrl,
        width: 190,
        height: 190,
        colorDark: "#111111",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
      });
    }

    function copyJoinUrl() {
      const joinUrl = `${window.location.origin}/siapa-kembaranku/play?code=${GAME_CODE}`;
      navigator.clipboard.writeText(joinUrl).then(() => {
        alert("Link join berhasil disalin ke clipboard!");
      });
    }

    // View Switching
    function setView(viewName) {
      currentView = viewName;
      document.getElementById('viewLobby').classList.toggle('hidden', viewName !== 'lobby');
      document.getElementById('viewDiscussion').classList.toggle('hidden', viewName !== 'discussion');
      document.getElementById('viewVoting').classList.toggle('hidden', viewName !== 'voting');
      document.getElementById('viewReveal').classList.toggle('hidden', viewName !== 'reveal');
      lucide.createIcons();
    }

    // Render Lobby Players
    function renderLobby(players) {
      const grid = document.getElementById('lobbyPlayerGrid');
      const empty = document.getElementById('emptyLobbyNotice');
      const badge = document.getElementById('playerCountBadge');
      const btnStart = document.getElementById('btnStartGame');

      badge.textContent = `${players.length} PEMAIN`;
      btnStart.disabled = players.length < 3;

      if (players.length === 0) {
        grid.innerHTML = '';
        empty.classList.remove('hidden');
        return;
      }

      empty.classList.add('hidden');
      grid.innerHTML = players.map((p, idx) => `
        <div class="sticker-card bg-white p-3 rounded-2xl flex items-center gap-2.5 animate-pulse-soft">
          <div class="w-8 h-8 rounded-xl bg-sunny border-2 border-ink flex items-center justify-center font-comic text-xs">
            ${idx + 1}
          </div>
          <div class="truncate">
            <p class="font-extrabold text-sm text-ink truncate">${escapeHtml(p.username)}</p>
            <span class="text-[10px] font-bold text-mint flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-mint inline-block"></span> SIAP
            </span>
          </div>
        </div>
      `).join('');
    }

    // Render Discussion Screen
    function renderDiscussion(gameData) {
      document.getElementById('currentRoundDisplay').textContent = gameData.current_round || 1;
      
      const activePlayers = (gameData.players || []).filter(p => !p.is_kicked);
      document.getElementById('activePlayersCountDisc').textContent = activePlayers.length;

      const grid = document.getElementById('discussionPlayerGrid');
      grid.innerHTML = (gameData.players || []).map(p => {
        const isKicked = p.is_kicked;
        return `
          <div class="sticker-card ${isKicked ? 'bg-slate-200 opacity-60' : 'bg-white'} p-4 rounded-2xl flex flex-col justify-between min-h-[110px] relative">
            ${isKicked ? '<span class="absolute top-2 right-2 bg-vermilion text-white text-[9px] font-black px-2 py-0.5 rounded-full border border-ink">KICKED</span>' : ''}
            <div>
              <p class="font-extrabold text-base text-ink truncate">${escapeHtml(p.username)}</p>
              <p class="text-[11px] font-semibold text-slate-500">${isKicked ? 'Tereliminasi' : 'Aktif mencari kembaran'}</p>
            </div>
            <div class="mt-2 flex items-center justify-between text-xs font-bold text-slate-600">
              <span>Status:</span>
              <span class="${isKicked ? 'text-vermilion' : 'text-cobalt'} font-black">${isKicked ? 'K.O.' : 'Mencari 🔍'}</span>
            </div>
          </div>
        `;
      }).join('');
    }

    // Timer Logic
    function startTimer(seconds) {
      clearInterval(timerInterval);
      secondsLeft = seconds;
      updateTimerDisplay();

      timerInterval = setInterval(() => {
        secondsLeft--;
        updateTimerDisplay();

        if (secondsLeft <= 10 && secondsLeft > 0) {
          soundBeepHigh();
        }

        if (secondsLeft <= 0) {
          clearInterval(timerInterval);
          soundGong();
          document.getElementById('timerStatusNotice').textContent = "Waktu habis! Silakan lanjut ke sesi voting.";
        }
      }, 1000);
    }

    function updateTimerDisplay() {
      const mins = Math.floor(Math.max(0, secondsLeft) / 60);
      const secs = Math.max(0, secondsLeft) % 60;
      const formatted = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
      const el = document.getElementById('timerDisplay');
      el.textContent = formatted;

      if (secondsLeft <= 30) {
        el.className = "font-comic text-6xl sm:text-7xl lg:text-8xl text-vermilion tracking-tight font-black leading-none my-1 animate-pulse";
      } else {
        el.className = "font-comic text-6xl sm:text-7xl lg:text-8xl text-ink tracking-tight font-black leading-none my-1";
      }
    }

    // Render Voting Screen
    function renderVoting(gameData) {
      const activePlayers = (gameData.players || []).filter(p => !p.is_kicked);
      const total = activePlayers.length;
      const voted = gameData.voted_players_count || 0;
      const percent = total > 0 ? Math.round((voted / total) * 100) : 0;

      const bar = document.getElementById('voteProgressBar');
      bar.style.width = `${percent}%`;
      bar.textContent = `${percent}%`;
      document.getElementById('voteProgressText').textContent = `${voted} / ${total} PEMAIN SUDAH VOTING`;

      const grid = document.getElementById('votingPlayerGrid');
      grid.innerHTML = activePlayers.map(p => `
        <div class="sticker-card bg-white p-3 rounded-2xl flex items-center justify-between">
          <p class="font-extrabold text-sm text-ink truncate">${escapeHtml(p.username)}</p>
          <span class="text-xs">🗳️</span>
        </div>
      `).join('');
    }

    // Render Reveal Screen
    function renderReveal(revealData) {
      soundFanfare();
      confetti({
        particleCount: 120,
        spread: 80,
        origin: { y: 0.6 }
      });

      // Winners Banner
      const winBanner = document.getElementById('winnerBanner');
      const winList = document.getElementById('winnersList');
      if (revealData.is_finished && revealData.winners && revealData.winners.length > 0) {
        winBanner.classList.remove('hidden');
        winList.innerHTML = revealData.winners.map(w => `
          <div class="sticker-card bg-white p-4 rounded-2xl flex items-center gap-3">
            <span class="text-4xl">${w.logo}</span>
            <div class="text-left">
              <p class="font-comic text-2xl text-ink leading-tight">${escapeHtml(w.username)}</p>
              <span class="bg-sunny text-ink font-bold text-xs px-2 py-0.5 rounded-full border border-ink">JUARA 1</span>
            </div>
          </div>
        `).join('');
        document.getElementById('btnNextRound').classList.add('hidden');
      } else {
        winBanner.classList.add('hidden');
        document.getElementById('btnNextRound').classList.remove('hidden');
      }

      // Kicked Players
      const kickedGrid = document.getElementById('kickedPlayersGrid');
      if (revealData.kicked_players && revealData.kicked_players.length > 0) {
        kickedGrid.innerHTML = revealData.kicked_players.map(k => `
          <div class="sticker-card bg-white text-ink p-4 rounded-2xl min-w-[200px] flex items-center gap-3 shadow-[5px_5px_0px_#111111]">
            <span class="text-4xl">${k.logo}</span>
            <div class="text-left">
              <p class="font-comic text-2xl text-ink">${escapeHtml(k.username)}</p>
              <p class="text-xs font-bold text-vermilion">Vote Diterima: ${k.votes}</p>
            </div>
          </div>
        `).join('');
      } else {
        kickedGrid.innerHTML = `<p class="font-comic text-xl text-white">Tidak ada pemain yang tereliminasi!</p>`;
      }

      // Group players by logo
      const groups = {};
      (revealData.players || []).forEach(p => {
        const logo = p.secret_logo || '❓';
        if (!groups[logo]) groups[logo] = [];
        groups[logo].push(p);
      });

      const groupContainer = document.getElementById('revealLogoGroups');
      groupContainer.innerHTML = Object.entries(groups).map(([logo, members]) => `
        <div class="sticker-card bg-blue-50 border-3 border-ink rounded-2xl p-5">
          <div class="flex items-center gap-3 mb-3 border-b-2 border-ink pb-2">
            <span class="text-4xl">${logo}</span>
            <div>
              <h4 class="font-comic text-xl text-ink">${members[0]?.logo_name || 'Kelompok Kembaran'}</h4>
              <p class="text-xs font-semibold text-slate-500">${members.length} Pemegang Logo</p>
            </div>
          </div>

          <div class="space-y-2">
            ${members.map(m => `
              <div class="p-2.5 rounded-xl border-2 border-ink ${m.is_kicked ? 'bg-rose-100' : 'bg-white'} flex items-center justify-between text-xs font-bold">
                <span class="truncate ${m.is_kicked ? 'line-through text-slate-400' : 'text-ink'}">${escapeHtml(m.username)}</span>
                <span class="${m.is_kicked ? 'text-vermilion' : 'text-cobalt'} font-black">
                  ${m.is_kicked ? 'Kena Kick ❌' : 'Lolos ✅'} (${m.votes_received} vote)
                </span>
              </div>
            `).join('')}
          </div>
        </div>
      `).join('');
    }

    // Host Actions
    async function startGame() {
      const timerSec = parseInt(document.getElementById('selectTimer').value, 10);
      const tieBreak = document.getElementById('selectTieBreak').value;

      try {
        const res = await fetch("{{ route('games.siapa-kembaranku.start') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            code: GAME_CODE,
            timer_seconds: timerSec,
            tie_break_rule: tieBreak
          })
        });

        const data = await res.json();
        if (data.success) {
          soundPop();
          gameState = data.game;
          setView('discussion');
          renderDiscussion(gameState);
          startTimer(timerSec);
        } else {
          alert(data.message || 'Gagal memulai game');
        }
      } catch (e) {
        console.error(e);
        alert('Terjadi kesalahan koneksi');
      }
    }

    async function startVotingPhase() {
      if (!confirm('Apakah kamu yakin ingin menyelesaikan diskusi dan membuka voting untuk semua pemain?')) return;

      clearInterval(timerInterval);
      try {
        const res = await fetch("{{ route('games.siapa-kembaranku.start-voting') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ code: GAME_CODE })
        });

        const data = await res.json();
        if (data.success) {
          soundPop();
          gameState = data.game;
          setView('voting');
          renderVoting(gameState);
        }
      } catch (e) {
        console.error(e);
      }
    }

    async function revealResults() {
      try {
        const res = await fetch("{{ route('games.siapa-kembaranku.reveal') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ code: GAME_CODE })
        });

        const data = await res.json();
        if (data.success) {
          setView('reveal');
          renderReveal(data.reveal);
        } else {
          alert(data.message || 'Gagal reveal hasil');
        }
      } catch (e) {
        console.error(e);
      }
    }

    async function nextRound() {
      try {
        const res = await fetch("{{ route('games.siapa-kembaranku.next-round') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ code: GAME_CODE })
        });

        const data = await res.json();
        if (data.success) {
          soundPop();
          gameState = data.game;
          setView('discussion');
          renderDiscussion(gameState);
          startTimer(gameState.timer_seconds || 300);
        } else {
          alert(data.message || 'Gagal memulai ronde baru');
        }
      } catch (e) {
        console.error(e);
      }
    }

    async function createNewRoom() {
      if (!confirm('Buat room game baru? Pemain saat ini akan di-reset.')) return;
      try {
        const res = await fetch("{{ route('games.siapa-kembaranku.create') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        const data = await res.json();
        if (data.success) {
          window.location.href = "{{ route('games.siapa-kembaranku.host') }}?code=" + data.game.code;
        }
      } catch (e) {
        console.error(e);
      }
    }

    // Polling Sync (Fallback & Realtime Sync)
    async function syncState() {
      try {
        const res = await fetch(`{{ route('games.siapa-kembaranku.state') }}?code=${GAME_CODE}`);
        if (!res.ok) return;
        const data = await res.json();
        if (data.success && data.game) {
          const remoteGame = data.game;
          gameState = remoteGame;

          // If current state doesn't match view, update view
          if (remoteGame.status === 'lobby' && currentView !== 'lobby') {
            setView('lobby');
          } else if (remoteGame.status === 'discussion' && currentView !== 'discussion') {
            setView('discussion');
            renderDiscussion(remoteGame);
          } else if (remoteGame.status === 'voting' && currentView !== 'voting') {
            setView('voting');
            renderVoting(remoteGame);
          } else if ((remoteGame.status === 'reveal' || remoteGame.status === 'finished') && currentView !== 'reveal' && remoteGame.last_reveal_data) {
            setView('reveal');
            renderReveal(remoteGame.last_reveal_data);
          }

          // In-view updates
          if (currentView === 'lobby') {
            renderLobby(remoteGame.players || []);
          } else if (currentView === 'voting') {
            renderVoting(remoteGame);
          }
        }
      } catch (e) {
        console.warn('Sync poll error:', e);
      }
    }

    // WebSocket / Reverb Setup
    function initWebSocket() {
      try {
        if (window.Echo) {
          echoInstance = new window.Echo({
            broadcaster: 'reverb',
            key: "{{ env('REVERB_APP_KEY') }}",
            wsHost: "{{ env('REVERB_HOST', 'localhost') }}",
            wsPort: {{ env('REVERB_PORT', 8080) }},
            wssPort: {{ env('REVERB_PORT', 8080) }},
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
          });

          echoInstance.channel(`twin-game.${GAME_CODE}`)
            .listen('.state.changed', (e) => {
              console.log('WS Event received:', e);
              if (e.type === 'player_joined') {
                soundPop();
                syncState();
              } else if (e.type === 'scan_recorded') {
                soundTick();
                addScanFeed(e.payload);
              } else if (e.type === 'vote_submitted') {
                soundPop();
                syncState();
              } else if (e.type === 'round_revealed') {
                setView('reveal');
                renderReveal(e.payload);
              } else {
                syncState();
              }
            });
        }
      } catch (err) {
        console.warn('Reverb WebSocket init skipped/error:', err);
      }
    }

    function addScanFeed(payload) {
      const feed = document.getElementById('scanFeedList');
      const badge = document.getElementById('scanCountBadge');
      
      const item = document.createElement('div');
      item.className = "bg-white p-2.5 rounded-xl border-2 border-ink text-xs font-bold text-ink shadow-[2px_2px_0px_#111111] animate-pulse";
      item.innerHTML = `⚡ <span class="text-cobalt">${escapeHtml(payload.scanner_name)}</span> baru saja scan <span class="text-vermilion">${escapeHtml(payload.scanned_name)}</span>!`;
      
      // If placeholder exists
      if (feed.querySelector('p')) {
        feed.innerHTML = '';
      }

      feed.prepend(item);
      const count = feed.children.length;
      badge.textContent = `${count} SCAN`;
    }

    function escapeHtml(text) {
      if (!text) return '';
      return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    // Init on Load
    window.addEventListener('DOMContentLoaded', () => {
      initJoinQR();
      initWebSocket();
      
      // Initial render based on existing status
      if (gameState.status === 'discussion') {
        setView('discussion');
        renderDiscussion(gameState);
      } else if (gameState.status === 'voting') {
        setView('voting');
        renderVoting(gameState);
      } else if (gameState.status === 'reveal' || gameState.status === 'finished') {
        if (gameState.last_reveal_data) {
          setView('reveal');
          renderReveal(gameState.last_reveal_data);
        } else {
          setView('lobby');
          renderLobby(gameState.players || []);
        }
      } else {
        setView('lobby');
        renderLobby(gameState.players || []);
      }

      // Polling every 2.5 seconds as robust fallback
      setInterval(syncState, 2500);
      lucide.createIcons();
    });
  </script>

</body>
</html>
