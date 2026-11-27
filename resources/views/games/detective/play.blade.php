<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Detective Terminal — The Problem Detective</title>

  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

  <style>
    body {
      background-color: #09090B;
      color: #FAFAFA;
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    }
    .font-mono-code { font-family: 'JetBrains Mono', monospace; }
    .case-card {
      background-color: #111114;
      border: 1px solid #27272A;
      border-radius: 12px;
    }
    .player-tab.active {
      background-color: #FAFAFA;
      color: #09090B;
      border-color: #FAFAFA;
    }
    .swal2-popup {
      background: #141418 !important;
      color: #FAFAFA !important;
      border: 1px solid #3F3F46 !important;
    }
  </style>
</head>
<body class="min-h-screen antialiased pb-20">

  <!-- JOIN SCREEN -->
  <div id="joinScreen" class="min-h-screen flex flex-col justify-center items-center px-4 py-10">
    <div class="w-full max-w-md case-card p-6 sm:p-8 space-y-6">
      <div class="flex items-center justify-between">
        <a href="{{ route('games.problem-detective.hub') }}" class="text-xs font-bold text-zinc-400 hover:text-white">← Kembali</a>
        <span class="font-mono-code text-xs text-emerald-400">DETECTIVE TERMINAL</span>
      </div>

      <div>
        <h1 class="text-2xl font-extrabold text-white">Gabung Investigasi</h1>
        <p class="text-xs text-zinc-400 mt-1">Ask the right questions. Find the real problem.</p>
      </div>

      <form onsubmit="submitJoin(event)" class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Room Code</label>
          <div class="flex gap-2">
            <input id="joinRoomCode" type="text" maxlength="6" required value="{{ $initialCode }}" placeholder="6 KARAKTER" oninput="onRoomCodeInput()" class="flex-1 bg-zinc-950 border border-zinc-700 rounded-lg px-3.5 py-2.5 text-sm font-mono-code uppercase tracking-widest text-white focus:outline-none focus:border-white">
            <button type="button" onclick="lookupRoomTeams()" class="px-3.5 py-2.5 rounded-lg border border-zinc-700 text-xs font-bold text-zinc-300 hover:bg-zinc-800 cursor-pointer">
              Cek Room
            </button>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Nama Detective</label>
          <input id="joinPlayerName" type="text" maxlength="40" required placeholder="Masukkan namamu..." class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3.5 py-2.5 text-sm font-semibold text-white focus:outline-none focus:border-white">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Pilih Kelompok</label>
          <select id="joinTeamSelect" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3.5 py-2.5 text-sm font-bold text-white">
            <option value="">Masukkan Kode Room terlebih dahulu...</option>
          </select>
        </div>

        <button type="submit" id="btnJoinSubmit" class="w-full py-3 rounded-lg bg-white text-black font-extrabold text-sm hover:bg-zinc-200 cursor-pointer">
          Masuk ke Ruang Investigasi →
        </button>
      </form>
    </div>
  </div>

  <!-- ACTIVE PLAYER WORKSPACE -->
  <div id="playWorkspace" class="hidden">
    <!-- Top Dossier Bar -->
    <header class="sticky top-0 z-40 border-b border-zinc-800 bg-zinc-950/95">
      <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2.5">
          <span id="topRoomCode" class="px-2.5 py-1 rounded bg-white text-black font-mono-code text-xs font-extrabold tracking-widest">-</span>
          <span id="topTeamBadge" class="px-2.5 py-1 rounded border border-zinc-700 bg-zinc-900 text-xs font-extrabold text-white">-</span>
          <span id="topPhaseBadge" class="px-2.5 py-1 rounded bg-zinc-800 font-mono-code text-xs font-bold uppercase text-emerald-400">-</span>
        </div>

        <div class="flex items-center gap-3">
          <div id="topLivesBadge" class="px-3 py-1 rounded-lg border border-red-900/60 bg-red-950/30 font-mono-code text-sm font-extrabold text-red-400">
            ♥ 10 / 10
          </div>
          <div id="topTimerBadge" class="px-3 py-1 rounded-lg border border-zinc-700 bg-zinc-900 font-mono-code text-base font-extrabold tabular-nums text-white">
            00:00
          </div>
          <button onclick="leaveRoomSession()" class="text-xs text-zinc-500 hover:text-zinc-300 cursor-pointer" title="Keluar / Ganti Akun">
            Keluar
          </button>
        </div>
      </div>
    </header>

    <!-- Breaking News Live Ticker -->
    <div id="breakingNewsBanner" class="hidden bg-red-950/90 border-b border-red-700">
      <div class="max-w-6xl mx-auto px-4 py-2.5 flex items-center gap-3">
        <span class="px-2 py-0.5 rounded bg-red-600 text-white font-mono-code text-[10px] font-extrabold uppercase shrink-0">BREAKING NEWS</span>
        <p id="breakingNewsLatestText" class="text-xs sm:text-sm font-bold text-red-100">-</p>
      </div>
    </div>

    <main class="max-w-6xl mx-auto px-4 pt-6 space-y-6">
      <!-- PHASE 00: LOBBY -->
      <section id="playerLobbyView" class="hidden space-y-6">
        <div class="case-card p-6 flex flex-wrap items-center justify-between gap-4">
          <div>
            <span class="font-mono-code text-xs uppercase text-zinc-500">LOBBY MENUNGGU GAME MASTER</span>
            <h2 class="text-2xl font-extrabold text-white mt-1">Halo, <span id="lobbyMeName">-</span>!</h2>
            <p class="text-xs text-zinc-400 mt-1">Pastikan kelompokmu sesuai sebelum Game Master memulai Phase 01.</p>
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <select id="lobbySwitchTeamSelect" onchange="switchMyTeam(this.value)" class="bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-xs font-bold text-white">
            </select>
            <button id="btnToggleReady" onclick="toggleMyReady()" class="px-5 py-2.5 rounded-lg bg-emerald-500 text-black font-extrabold text-xs uppercase cursor-pointer">
              ✓ Siap Investigasi
            </button>
          </div>
        </div>

        <div id="lobbyTeamsOverview" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        </div>
      </section>

      <!-- ACTIVE GAMEPLAY VIEWS (Briefing, Discussion, Investigation, Deduction) -->
      <section id="playerActiveView" class="hidden space-y-5">
        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2">
          <button data-ptab="briefing" onclick="switchPlayerTab('briefing')" class="player-tab active px-4 py-2 rounded-lg border border-zinc-800 text-xs font-extrabold uppercase cursor-pointer">
            📋 Case Briefing
          </button>
          <button data-ptab="board" onclick="switchPlayerTab('board')" class="player-tab px-4 py-2 rounded-lg border border-zinc-800 text-xs font-extrabold uppercase text-zinc-400 cursor-pointer">
            🔍 Investigasi & Evidence Board
          </button>
          <button data-ptab="deduction" onclick="switchPlayerTab('deduction')" class="player-tab px-4 py-2 rounded-lg border border-zinc-800 text-xs font-extrabold uppercase text-zinc-400 cursor-pointer">
            📝 Laporan Akhir <span id="submittedCheckBadge" class="hidden ml-1 text-emerald-500">✓</span>
          </button>
        </div>

        <!-- PTAB 1: BRIEFING -->
        <div id="ptab-briefing" class="case-card p-6 sm:p-8 space-y-5">
          <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-800 pb-4">
            <div>
              <span class="font-mono-code text-xs uppercase text-zinc-500">CASE FILE BRIEFING</span>
              <h2 id="plCaseTitle" class="text-2xl sm:text-3xl font-extrabold text-white mt-1">-</h2>
            </div>
            <span id="plCaseMeta" class="font-mono-code text-xs uppercase px-3 py-1 rounded border border-zinc-700 text-zinc-300">-</span>
          </div>

          <div class="space-y-2">
            <h3 class="font-mono-code text-xs uppercase text-zinc-400">Deskripsi Permasalahan</h3>
            <p id="plCaseDesc" class="text-sm sm:text-base text-zinc-200 leading-relaxed"></p>
          </div>

          <div class="p-4 rounded-xl bg-zinc-950 border border-amber-900/50 space-y-1">
            <span class="font-mono-code text-xs uppercase text-amber-400 font-bold">⚠ DUGAAN AWAL KLIEN (BELUM TENTU BENAR!)</span>
            <p id="plCaseAssumption" class="text-sm text-zinc-300"></p>
          </div>

          <div class="space-y-2">
            <h3 class="font-mono-code text-xs uppercase text-emerald-400">Misi Investigasi Kelompok</h3>
            <ul id="plCaseObjectives" class="list-disc pl-5 text-sm text-zinc-200 space-y-1.5"></ul>
          </div>
        </div>

        <!-- PTAB 2: INVESTIGATION & COLLABORATIVE EVIDENCE BOARD -->
        <div id="ptab-board" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
          <!-- Left: Question Form & Q&A Feed (6 cols) -->
          <div class="lg:col-span-6 space-y-4">
            <div class="case-card p-5 space-y-3">
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-white">Kirim Pertanyaan Investigasi</h3>
                <span id="qLockStatus" class="font-mono-code text-[11px] text-zinc-400">Phase 03</span>
              </div>

              <form onsubmit="submitQuestion(event)" class="space-y-3">
                <textarea id="questionInput" rows="3" required placeholder="Rumuskan pertanyaan berbasis fakta untuk mengungkap akar masalah..." class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-white"></textarea>
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <label class="flex items-center gap-2 text-xs text-amber-300 font-bold cursor-pointer">
                    <input id="expertCheck" type="checkbox" class="accent-amber-500">
                    <span>Expert Consultation (Petunjuk Khusus: -2 ♥)</span>
                  </label>
                  <button type="submit" id="btnAskQuestion" class="px-5 py-2.5 rounded-lg bg-white text-black font-extrabold text-xs uppercase hover:bg-zinc-200 cursor-pointer">
                    Kirim Pertanyaan (-1 ♥)
                  </button>
                </div>
              </form>
            </div>

            <div class="case-card p-5 space-y-3">
              <h3 class="text-sm font-extrabold text-white border-b border-zinc-800 pb-2">Log Pertanyaan & Jawaban</h3>
              <div id="playerQuestionsFeed" class="space-y-2.5 max-h-[500px] overflow-y-auto pr-1">
              </div>
            </div>
          </div>

          <!-- Right: Collaborative Evidence Board (6 cols) -->
          <div class="lg:col-span-6 case-card p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-800 pb-2">
              <div>
                <h3 class="text-sm font-extrabold text-white">Evidence Board Kelompok (Realtime Sync)</h3>
                <p class="text-[11px] text-zinc-500">Disinkronkan langsung ke seluruh anggota kelompokmu.</p>
              </div>
              <button onclick="saveEvidenceBoardNow()" class="px-3 py-1.5 rounded bg-emerald-500 text-black text-xs font-extrabold cursor-pointer">
                Simpan Board
              </button>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase text-zinc-400 mb-1">Hipotesis Saat Ini</label>
              <textarea id="ebHypothesis" rows="2" onfocus="boardEditing=true" onblur="boardEditing=false; saveEvidenceBoardNow()" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-xs text-white" placeholder="Apa dugaan sementara kelompok kalian?"></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase text-zinc-400 mb-1">Catatan Kelompok</label>
              <textarea id="ebNotes" rows="3" onfocus="boardEditing=true" onblur="boardEditing=false; saveEvidenceBoardNow()" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-xs text-white" placeholder="Tulis analisis atau benang merah..."></textarea>
            </div>

            <div class="space-y-2">
              <label class="block text-xs font-bold uppercase text-emerald-400">Bukti Terkumpul (Pin Poin Penting)</label>
              <div class="flex gap-2">
                <input id="ebNewEvidence" type="text" placeholder="Tambah poin bukti..." class="flex-1 bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-1.5 text-xs text-white">
                <button type="button" onclick="addBoardItem('evidence')" class="px-3 py-1.5 rounded border border-zinc-700 text-xs font-bold text-white cursor-pointer">+ Pin</button>
              </div>
              <ul id="ebEvidenceList" class="space-y-1 text-xs"></ul>
            </div>

            <div class="space-y-2">
              <label class="block text-xs font-bold uppercase text-red-400">Kemungkinan Penyebab yang Dieliminasi</label>
              <div class="flex gap-2">
                <input id="ebNewEliminated" type="text" placeholder="Contoh: CPU server penuh (eliminasi, CPU cuma 12%)" class="flex-1 bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-1.5 text-xs text-white">
                <button type="button" onclick="addBoardItem('eliminated')" class="px-3 py-1.5 rounded border border-zinc-700 text-xs font-bold text-white cursor-pointer">+ Eliminasi</button>
              </div>
              <ul id="ebEliminatedList" class="space-y-1 text-xs"></ul>
            </div>
          </div>
        </div>

        <!-- PTAB 3: FINAL DEDUCTION REPORT -->
        <div id="ptab-deduction" class="hidden case-card p-6 sm:p-8 space-y-5">
          <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-800 pb-4">
            <div>
              <span class="font-mono-code text-xs uppercase text-emerald-400">PHASE 04 // FINAL DEDUCTION</span>
              <h3 class="text-xl font-extrabold text-white">Laporan Investigasi Akhir Kelompok</h3>
              <p class="text-xs text-zinc-400">Hanya 1 laporan akhir per kelompok. Setelah dikirim, kelompok tidak bisa mengirim pertanyaan tambahan.</p>
            </div>
            <button type="button" id="btnAutofillReport" onclick="autofillFromEvidenceBoard()" class="px-3 py-2 rounded-lg border border-zinc-700 text-xs font-bold text-zinc-200 hover:bg-zinc-800 cursor-pointer">
              ⚡ Isi Otomatis dari Evidence Board
            </button>
          </div>

          <div id="submittedReceiptBanner" class="hidden p-4 rounded-xl bg-emerald-950/40 border border-emerald-700 text-xs text-emerald-200 font-bold">
            ✓ Laporan akhir kelompok telah dikumpulkan! Bersiaplah untuk mempresentasikannya pada Phase 05 — Defense.
          </div>

          <form id="finalReportForm" onsubmit="submitFinalReport(event)" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-bold text-zinc-300 mb-1">1. Initial Hypothesis (Dugaan Awal) *</label>
                <textarea id="frHypothesis" rows="2" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
              </div>
              <div>
                <label class="block font-bold text-zinc-300 mb-1">2. Eliminated Possibilities (Yang Berhasil Disingkirkan) *</label>
                <textarea id="frEliminated" rows="2" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
              </div>
            </div>

            <div>
              <label class="block font-bold text-zinc-300 mb-1">3. Evidence Collected (Bukti Kunci yang Ditemukan) *</label>
              <textarea id="frEvidence" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
            </div>

            <div>
              <label class="block font-bold text-emerald-400 mb-1">4. Root Cause (Akar Permasalahan Sebenarnya) *</label>
              <textarea id="frRootCause" rows="2" required class="w-full bg-zinc-950 border border-emerald-800 rounded-lg px-3 py-2 text-white font-semibold"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-bold text-zinc-300 mb-1">5. Proposed Solution (Solusi yang Diusulkan) *</label>
                <textarea id="frSolution" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
              </div>
              <div>
                <label class="block font-bold text-zinc-300 mb-1">6. Validation Plan (Cara Membuktikan Solusi Berhasil) *</label>
                <textarea id="frValidation" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
              </div>
            </div>

            <div class="p-4 rounded-xl bg-zinc-950 border border-zinc-800 space-y-2">
              <div class="flex items-center justify-between">
                <label class="font-bold text-zinc-300 uppercase">7. Confidence Level (Tingkat Keyakinan)</label>
                <span id="frConfidenceVal" class="font-mono-code text-lg font-extrabold text-emerald-400">75%</span>
              </div>
              <input id="frConfidence" type="range" min="0" max="100" value="75" oninput="document.getElementById('frConfidenceVal').textContent=this.value+'%'" class="w-full accent-emerald-500">
            </div>

            <div class="flex justify-end">
              <button type="submit" id="btnSubmitFinalReport" class="px-6 py-3 rounded-lg bg-emerald-500 text-black font-extrabold text-sm hover:bg-emerald-400 cursor-pointer">
                Kumpulkan Laporan Investigasi Akhir →
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- PHASE 05 / 06 / 07 DISPLAY -->
      <section id="playerEndgameView" class="hidden space-y-6">
        <!-- Rendered dynamically for Defense, Truth Reveal, and Leaderboard -->
      </section>
    </main>
  </div>

  <script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let roomCode = @json($initialCode);
    let playerToken = roomCode ? localStorage.getItem('detective_player_token_' + roomCode) : null;
    let roomState = null;
    let remainingSeconds = 0;
    let currentPlayerTab = 'briefing';
    let lastBreakingNewsId = null;
    let boardEditing = false;

    function escapeHtml(str) {
      return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    function formatTimer(sec) {
      const s = Math.max(0, Number(sec) || 0);
      const m = Math.floor(s / 60);
      const r = s % 60;
      return `${String(m).padStart(2, '0')}:${String(r).padStart(2, '0')}`;
    }

    function switchPlayerTab(tab) {
      currentPlayerTab = tab;
      document.querySelectorAll('.player-tab').forEach((b) => {
        const isActive = b.getAttribute('data-ptab') === tab;
        b.classList.toggle('active', isActive);
        b.classList.toggle('text-zinc-400', !isActive);
      });
      ['briefing', 'board', 'deduction'].forEach((t) => {
        document.getElementById('ptab-' + t).classList.toggle('hidden', t !== tab);
      });
    }

    function onRoomCodeInput() {
      const val = document.getElementById('joinRoomCode').value.trim().toUpperCase();
      if (val.length === 6) {
        lookupRoomTeams();
      }
    }

    async function lookupRoomTeams() {
      const code = document.getElementById('joinRoomCode').value.trim().toUpperCase();
      if (code.length !== 6) return;
      try {
        const res = await fetch(`{{ route('games.problem-detective.state') }}?code=${code}&role=player`, {
          headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        const sel = document.getElementById('joinTeamSelect');
        if (data.success && data.room) {
          sel.innerHTML = data.room.teams.map((t) => {
            const full = t.players.length >= t.max_capacity;
            return `<option value="${t.id}" ${full ? 'disabled' : ''}>${escapeHtml(t.name)} (${t.players.length}/${t.max_capacity})${full ? ' — PENUH' : ''}</option>`;
          }).join('');
        } else {
          sel.innerHTML = `<option value="">Room tidak ditemukan</option>`;
        }
      } catch (e) {}
    }

    async function submitJoin(e) {
      e.preventDefault();
      const code = document.getElementById('joinRoomCode').value.trim().toUpperCase();
      const name = document.getElementById('joinPlayerName').value.trim();
      const detective_team_id = Number(document.getElementById('joinTeamSelect').value);

      const res = await fetch("{{ route('games.problem-detective.join') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ code, name, detective_team_id }),
      });
      const data = await res.json();
      if (data.success) {
        roomCode = code;
        playerToken = data.player_token;
        localStorage.setItem('detective_player_token_' + roomCode, playerToken);
        enterWorkspace();
      } else {
        Swal.fire('Gagal Bergabung', data.message || 'Periksa kembali kode room dan nama.', 'error');
      }
    }

    function leaveRoomSession() {
      if (roomCode) localStorage.removeItem('detective_player_token_' + roomCode);
      playerToken = null;
      document.getElementById('playWorkspace').classList.add('hidden');
      document.getElementById('joinScreen').classList.remove('hidden');
    }

    function enterWorkspace() {
      document.getElementById('joinScreen').classList.add('hidden');
      document.getElementById('playWorkspace').classList.remove('hidden');
      fetchPlayerState();
    }

    async function fetchPlayerState() {
      if (!roomCode || !playerToken) return;
      try {
        const res = await fetch(`{{ route('games.problem-detective.state') }}?code=${roomCode}&role=player`, {
          headers: { 'Accept': 'application/json', 'X-Player-Token': playerToken }
        });
        const data = await res.json();
        if (!data.success || !data.room || !data.room.me) {
          leaveRoomSession();
          return;
        }
        roomState = data.room;
        remainingSeconds = data.remaining_seconds ?? 0;
        renderPlayerView();
      } catch (e) {}
    }

    function renderPlayerView() {
      if (!roomState || !roomState.me) return;

      document.getElementById('topRoomCode').textContent = roomState.code;
      document.getElementById('topTeamBadge').textContent = roomState.my_team ? roomState.my_team.name : '-';
      document.getElementById('topPhaseBadge').textContent = roomState.phase.toUpperCase();
      document.getElementById('topTimerBadge').textContent = formatTimer(remainingSeconds);

      const myTeam = roomState.my_team;
      if (myTeam) {
        const livesEl = document.getElementById('topLivesBadge');
        livesEl.textContent = `♥ ${myTeam.lives} / ${roomState.default_lives}`;
        livesEl.classList.toggle('bg-red-600', myTeam.lives <= 2);
        livesEl.classList.toggle('text-white', myTeam.lives <= 2);
      }

      // Breaking News Notification
      const bnFeed = roomState.breaking_news_feed || [];
      const bnBanner = document.getElementById('breakingNewsBanner');
      if (bnFeed.length > 0) {
        bnBanner.classList.remove('hidden');
        document.getElementById('breakingNewsLatestText').textContent = bnFeed[0].message;
        if (lastBreakingNewsId && lastBreakingNewsId !== bnFeed[0].id) {
          Swal.fire({
            icon: 'warning',
            title: 'BREAKING NEWS!',
            text: bnFeed[0].message,
            confirmButtonText: 'Catat Info',
          });
        }
        lastBreakingNewsId = bnFeed[0].id;
      } else {
        bnBanner.classList.add('hidden');
      }

      const phase = roomState.phase;
      const lobbyView = document.getElementById('playerLobbyView');
      const activeView = document.getElementById('playerActiveView');
      const endgameView = document.getElementById('playerEndgameView');

      lobbyView.classList.toggle('hidden', phase !== 'lobby');
      activeView.classList.toggle('hidden', !['briefing', 'discussion', 'investigation', 'deduction'].includes(phase));
      endgameView.classList.toggle('hidden', !['defense', 'reveal', 'leaderboard'].includes(phase));

      if (phase === 'lobby') {
        renderLobbyView();
      } else if (['briefing', 'discussion', 'investigation', 'deduction'].includes(phase)) {
        renderActiveGameplayView();
      } else {
        renderEndgameView();
      }
    }

    function renderLobbyView() {
      document.getElementById('lobbyMeName').textContent = roomState.me.name;
      const btnReady = document.getElementById('btnToggleReady');
      btnReady.textContent = roomState.me.is_ready ? '✓ Siap (Klik Batal)' : 'Tandai Siap Investigasi';
      btnReady.className = roomState.me.is_ready
        ? 'px-5 py-2.5 rounded-lg bg-emerald-500 text-black font-extrabold text-xs uppercase cursor-pointer'
        : 'px-5 py-2.5 rounded-lg border border-zinc-600 text-white font-extrabold text-xs uppercase cursor-pointer';

      const sw = document.getElementById('lobbySwitchTeamSelect');
      sw.innerHTML = roomState.teams.map((t) =>
        `<option value="${t.id}" ${t.id === roomState.me.team_id ? 'selected' : ''}>${escapeHtml(t.name)} (${t.players.length}/${t.max_capacity})</option>`
      ).join('');

      document.getElementById('lobbyTeamsOverview').innerHTML = roomState.teams.map((t) => `
        <div class="case-card p-4 space-y-2 ${t.id === roomState.me.team_id ? 'border-emerald-500' : ''}">
          <div class="flex items-center justify-between text-xs font-extrabold">
            <span>${escapeHtml(t.name)}</span>
            <span class="font-mono-code text-zinc-400">${t.players.length}/${t.max_capacity}</span>
          </div>
          <div class="space-y-1 text-xs text-zinc-300">
            ${t.players.length === 0 ? '<span class="text-zinc-600 italic">Kosong</span>' : t.players.map((p) => `
              <div>${p.is_ready ? '<span class="text-emerald-400">●</span>' : '<span class="text-zinc-600">○</span>'} ${escapeHtml(p.name)}</div>
            `).join('')}
          </div>
        </div>
      `).join('');
    }

    function renderActiveGameplayView() {
      const c = roomState.case;
      if (c) {
        document.getElementById('plCaseTitle').textContent = c.title;
        document.getElementById('plCaseMeta').textContent = `${c.category} • ${c.difficulty}`;
        document.getElementById('plCaseDesc').textContent = c.description;
        document.getElementById('plCaseAssumption').textContent = c.initial_assumption;
        document.getElementById('plCaseObjectives').innerHTML = (c.mission_objectives || []).map((m) => `<li>${escapeHtml(m)}</li>`).join('');
      }

      const t = roomState.my_team;
      if (!t) return;

      // Question submission availability
      const canAsk = roomState.phase === 'investigation' && !t.submitted_at && t.lives > 0;
      const btnAsk = document.getElementById('btnAskQuestion');
      const isExpert = document.getElementById('expertCheck').checked;
      btnAsk.disabled = !canAsk;
      btnAsk.classList.toggle('opacity-40', !canAsk);
      btnAsk.textContent = isExpert ? 'Kirim Expert Consultation (-2 ♥)' : 'Kirim Pertanyaan (-1 ♥)';

      const lockLabel = document.getElementById('qLockStatus');
      if (t.submitted_at) lockLabel.textContent = 'Terkunci (Laporan Terkirim)';
      else if (roomState.phase !== 'investigation') lockLabel.textContent = 'Dibuka pada Phase 03 — Investigation';
      else if (t.lives <= 0) lockLabel.textContent = 'Nyawa Habis';
      else lockLabel.textContent = `Tersedia (${t.lives} Nyawa)`;

      // Questions Feed (automatically added to Evidence Board)
      const feed = document.getElementById('playerQuestionsFeed');
      const qs = (roomState.questions || []).slice().reverse();
      feed.innerHTML = qs.length === 0
        ? `<div class="py-8 text-center text-xs text-zinc-500">Belum ada pertanyaan investigasi.</div>`
        : qs.map((q) => `
            <div class="p-3 rounded-lg bg-zinc-950 border border-zinc-800 space-y-1.5 text-xs">
              <div class="flex items-center justify-between text-[11px] text-zinc-500">
                <span class="font-bold text-zinc-300">${escapeHtml(q.team_name)} • ${escapeHtml(q.player_name)}</span>
                <span class="font-mono-code uppercase ${q.status === 'answered' ? 'text-emerald-400' : (q.status === 'pending' ? 'text-amber-400' : 'text-red-400')}">${q.status}</span>
              </div>
              <p class="font-bold text-white">Q: ${escapeHtml(q.question_text)}</p>
              ${q.answer_text ? `<p class="p-2 rounded bg-zinc-900 border border-zinc-800 text-emerald-300 font-semibold">A: ${escapeHtml(q.answer_text)}</p>` : ''}
            </div>
          `).join('');

      // Sync Collaborative Evidence Board (avoid overwriting active focus input)
      if (!boardEditing) {
        const hypEl = document.getElementById('ebHypothesis');
        const notesEl = document.getElementById('ebNotes');
        if (document.activeElement !== hypEl) hypEl.value = t.hypothesis || '';
        if (document.activeElement !== notesEl) notesEl.value = t.notes || '';
      }

      document.getElementById('ebEvidenceList').innerHTML = (t.evidence_items || []).map((item, idx) => `
        <li class="flex items-center justify-between gap-2 p-2 rounded bg-zinc-950 border border-zinc-800 text-emerald-300">
          <span>✓ ${escapeHtml(item)}</span>
          ${!t.submitted_at ? `<button type="button" onclick="removeBoardItem('evidence', ${idx})" class="text-zinc-500 hover:text-red-400 cursor-pointer">✕</button>` : ''}
        </li>
      `).join('');

      document.getElementById('ebEliminatedList').innerHTML = (t.eliminated_causes || []).map((item, idx) => `
        <li class="flex items-center justify-between gap-2 p-2 rounded bg-zinc-950 border border-zinc-800 text-red-300">
          <span>✕ ${escapeHtml(item)}</span>
          ${!t.submitted_at ? `<button type="button" onclick="removeBoardItem('eliminated', ${idx})" class="text-zinc-500 hover:text-red-400 cursor-pointer">✕</button>` : ''}
        </li>
      `).join('');

      // Final Report Receipt State
      const isSub = Boolean(t.submitted_at);
      document.getElementById('submittedCheckBadge').classList.toggle('hidden', !isSub);
      document.getElementById('submittedReceiptBanner').classList.toggle('hidden', !isSub);
      document.getElementById('btnAutofillReport').classList.toggle('hidden', isSub);
      document.getElementById('btnSubmitFinalReport').classList.toggle('hidden', isSub);

      if (isSub && t.final_report) {
        const r = t.final_report;
        document.getElementById('frHypothesis').value = r.initial_hypothesis || '';
        document.getElementById('frEliminated').value = r.eliminated_possibilities || '';
        document.getElementById('frEvidence').value = r.evidence_collected || '';
        document.getElementById('frRootCause').value = r.root_cause || '';
        document.getElementById('frSolution').value = r.proposed_solution || '';
        document.getElementById('frValidation').value = r.validation_plan || '';
        document.getElementById('frConfidence').value = r.confidence_level ?? 75;
        document.getElementById('frConfidenceVal').textContent = (r.confidence_level ?? 75) + '%';
        document.querySelectorAll('#finalReportForm textarea, #finalReportForm input').forEach((el) => { el.disabled = true; });
      }
    }

    function renderEndgameView() {
      const wrap = document.getElementById('playerEndgameView');
      const phase = roomState.phase;

      if (phase === 'defense') {
        const defTeam = roomState.teams.find((t) => t.id === roomState.defense_team_id) || roomState.teams[0];
        const r = defTeam?.final_report;
        wrap.innerHTML = `
          <div class="case-card p-6 sm:p-8 space-y-4">
            <span class="font-mono-code text-xs uppercase text-emerald-400">PHASE 05 // DEFENSE SPOTLIGHT</span>
            <h2 class="text-2xl font-extrabold text-white">Presentasi: ${escapeHtml(defTeam?.name || '-')}</h2>
            ${r ? `
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="p-4 rounded-lg bg-zinc-950 border border-zinc-800"><span class="text-xs text-zinc-500 uppercase font-bold">Root Cause</span><p class="text-white font-bold mt-1">${escapeHtml(r.root_cause)}</p></div>
                <div class="p-4 rounded-lg bg-zinc-950 border border-zinc-800"><span class="text-xs text-zinc-500 uppercase font-bold">Confidence</span><p class="font-mono-code text-emerald-400 font-bold text-xl mt-1">${r.confidence_level}%</p></div>
                <div class="sm:col-span-2 p-4 rounded-lg bg-zinc-950 border border-zinc-800"><span class="text-xs text-zinc-500 uppercase font-bold">Evidence Collected</span><p class="text-zinc-300 mt-1">${escapeHtml(r.evidence_collected)}</p></div>
                <div class="p-4 rounded-lg bg-zinc-950 border border-zinc-800"><span class="text-xs text-zinc-500 uppercase font-bold">Proposed Solution</span><p class="text-zinc-300 mt-1">${escapeHtml(r.proposed_solution)}</p></div>
                <div class="p-4 rounded-lg bg-zinc-950 border border-zinc-800"><span class="text-xs text-zinc-500 uppercase font-bold">Validation Plan</span><p class="text-zinc-300 mt-1">${escapeHtml(r.validation_plan)}</p></div>
              </div>
            ` : `<p class="text-sm text-zinc-400">Kelompok ini belum mengumpulkan laporan akhir.</p>`}
          </div>
        `;
      } else if (phase === 'reveal') {
        const c = roomState.case;
        wrap.innerHTML = `
          <div class="case-card p-6 sm:p-8 space-y-5 border-emerald-600/50">
            <span class="font-mono-code text-xs uppercase text-emerald-400 font-bold">PHASE 06 // TRUTH REVEAL</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">${escapeHtml(c?.title)}</h2>
            <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-700">
              <div class="font-mono-code text-xs uppercase text-emerald-400 font-bold">AKAR PERMASALAHAN SEBENARNYA</div>
              <p class="text-base sm:text-lg font-extrabold text-white mt-1">${escapeHtml(c?.root_cause)}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div class="p-4 rounded-xl bg-zinc-950 border border-zinc-800 space-y-2">
                <div class="font-mono-code text-xs uppercase text-zinc-400">Fakta Kunci</div>
                <ul class="list-disc pl-5 text-xs text-zinc-300 space-y-1">
                  ${(c?.hidden_facts || []).map((f) => `<li>${escapeHtml(f)}</li>`).join('')}
                </ul>
              </div>
              <div class="p-4 rounded-xl bg-zinc-950 border border-zinc-800 space-y-3">
                <div>
                  <div class="font-mono-code text-xs uppercase text-red-400">Kesalahan Asumsi Awal</div>
                  <p class="text-xs text-zinc-300 mt-1">${escapeHtml(c?.initial_assumption)}</p>
                </div>
                <div>
                  <div class="font-mono-code text-xs uppercase text-emerald-400">Rekomendasi Solusi</div>
                  <ul class="list-disc pl-5 text-xs text-zinc-300 space-y-1 mt-1">
                    ${(c?.recommended_solutions || []).map((s) => `<li>${escapeHtml(s)}</li>`).join('')}
                  </ul>
                </div>
              </div>
            </div>
          </div>
        `;
      } else {
        const sorted = (roomState.teams || []).slice().sort((a, b) => (b.total_score || 0) - (a.total_score || 0));
        wrap.innerHTML = `
          <div class="case-card p-6 sm:p-8 space-y-5">
            <span class="font-mono-code text-xs uppercase text-emerald-400 font-bold">PHASE 07 // FINAL LEADERBOARD</span>
            <h2 class="text-2xl font-extrabold text-white">Hasil Penilaian Investigasi</h2>
            <div class="space-y-3">
              ${sorted.map((t, i) => `
                <div class="p-4 rounded-xl bg-zinc-950 border ${i === 0 ? 'border-emerald-500' : 'border-zinc-800'} flex flex-wrap items-center justify-between gap-3">
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="font-mono-code text-sm font-extrabold text-zinc-400">#${i + 1}</span>
                      <span class="text-base font-extrabold text-white">${escapeHtml(t.name)}</span>
                    </div>
                    ${(t.awards || []).length ? `<div class="flex flex-wrap gap-1.5 mt-1">${t.awards.map((a) => `<span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-bold">★ ${escapeHtml(a)}</span>`).join('')}</div>` : ''}
                  </div>
                  <div class="text-right">
                    <div class="font-mono-code text-2xl font-extrabold text-emerald-400">${t.total_score ?? 0} <span class="text-xs text-zinc-500">/100</span></div>
                    <div class="text-[11px] text-zinc-500 font-mono-code">${t.questions_asked_count} Pertanyaan • Sisa ${t.lives} ♥</div>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>
        `;
      }
    }

    async function switchMyTeam(teamId) {
      const res = await fetch("{{ route('games.problem-detective.switch-team') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ code: roomCode, player_token: playerToken, detective_team_id: Number(teamId) }),
      });
      const data = await res.json();
      if (!data.success) Swal.fire('Gagal', data.message, 'warning');
      await fetchPlayerState();
    }

    async function toggleMyReady() {
      await fetch("{{ route('games.problem-detective.ready') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ code: roomCode, player_token: playerToken }),
      });
      await fetchPlayerState();
    }

    document.getElementById('expertCheck')?.addEventListener('change', () => renderActiveGameplayView());

    async function submitQuestion(e) {
      e.preventDefault();
      const qText = document.getElementById('questionInput').value.trim();
      const isExpert = document.getElementById('expertCheck').checked;
      if (qText.length < 5) {
        Swal.fire('Terlalu Singkat', 'Rumuskan pertanyaan yang jelas minimal 5 karakter.', 'warning');
        return;
      }

      const res = await fetch("{{ route('games.problem-detective.questions.ask') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({
          code: roomCode,
          player_token: playerToken,
          question_text: qText,
          is_expert_consultation: isExpert,
        }),
      });
      const data = await res.json();
      if (data.success) {
        document.getElementById('questionInput').value = '';
        document.getElementById('expertCheck').checked = false;
        await fetchPlayerState();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Pertanyaan terkirim ke Game Master!', showConfirmButton: false, timer: 2000 });
      } else {
        Swal.fire('Gagal', data.message || 'Tidak bisa mengirim pertanyaan.', 'error');
      }
    }

    async function saveEvidenceBoardNow(customPayload = {}) {
      if (!roomState || !roomState.my_team || roomState.my_team.submitted_at) return;
      const payload = {
        code: roomCode,
        player_token: playerToken,
        hypothesis: document.getElementById('ebHypothesis').value,
        notes: document.getElementById('ebNotes').value,
        ...customPayload,
      };
      await fetch("{{ route('games.problem-detective.board') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify(payload),
      });
    }

    async function addBoardItem(kind) {
      if (!roomState?.my_team) return;
      if (kind === 'evidence') {
        const inp = document.getElementById('ebNewEvidence');
        const val = inp.value.trim();
        if (!val) return;
        const arr = [...(roomState.my_team.evidence_items || []), val];
        inp.value = '';
        await saveEvidenceBoardNow({ evidence_items: arr });
      } else {
        const inp = document.getElementById('ebNewEliminated');
        const val = inp.value.trim();
        if (!val) return;
        const arr = [...(roomState.my_team.eliminated_causes || []), val];
        inp.value = '';
        await saveEvidenceBoardNow({ eliminated_causes: arr });
      }
      await fetchPlayerState();
    }

    async function removeBoardItem(kind, idx) {
      if (!roomState?.my_team) return;
      if (kind === 'evidence') {
        const arr = (roomState.my_team.evidence_items || []).filter((_, i) => i !== idx);
        await saveEvidenceBoardNow({ evidence_items: arr });
      } else {
        const arr = (roomState.my_team.eliminated_causes || []).filter((_, i) => i !== idx);
        await saveEvidenceBoardNow({ eliminated_causes: arr });
      }
      await fetchPlayerState();
    }

    function autofillFromEvidenceBoard() {
      const t = roomState?.my_team;
      if (!t) return;
      const answeredQs = (roomState.questions || [])
        .filter((q) => q.team_id === t.id && q.answer_text)
        .map((q) => `- ${q.answer_text}`);

      const combinedEvidence = [
        ...(t.evidence_items || []).map((e) => `- ${e}`),
        ...answeredQs,
      ].join('\n');

      if (!document.getElementById('frHypothesis').value) {
        document.getElementById('frHypothesis').value = roomState.case?.initial_assumption || '';
      }
      if (!document.getElementById('frEvidence').value) {
        document.getElementById('frEvidence').value = combinedEvidence;
      }
      if (!document.getElementById('frEliminated').value) {
        document.getElementById('frEliminated').value = (t.eliminated_causes || []).map((c) => `- ${c}`).join('\n');
      }
      if (!document.getElementById('frRootCause').value && t.hypothesis) {
        document.getElementById('frRootCause').value = t.hypothesis;
      }
    }

    async function submitFinalReport(e) {
      e.preventDefault();
      const conf = await Swal.fire({
        title: 'Kumpulkan Laporan Akhir?',
        text: 'Setelah dikumpulkan, kelompok tidak dapat mengajukan pertanyaan investigasi tambahan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kumpulkan!',
        cancelButtonText: 'Cek Lagi',
      });
      if (!conf.isConfirmed) return;

      const res = await fetch("{{ route('games.problem-detective.report') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({
          code: roomCode,
          player_token: playerToken,
          initial_hypothesis: document.getElementById('frHypothesis').value,
          evidence_collected: document.getElementById('frEvidence').value,
          eliminated_possibilities: document.getElementById('frEliminated').value,
          root_cause: document.getElementById('frRootCause').value,
          proposed_solution: document.getElementById('frSolution').value,
          validation_plan: document.getElementById('frValidation').value,
          confidence_level: Number(document.getElementById('frConfidence').value),
        }),
      });
      const data = await res.json();
      if (data.success) {
        await fetchPlayerState();
        Swal.fire('Berhasil!', data.message, 'success');
      } else {
        Swal.fire('Gagal', data.message || 'Periksa kembali kelengkapan laporan.', 'error');
      }
    }

    window.addEventListener('DOMContentLoaded', () => {
      if (roomCode && roomCode.length === 6) {
        lookupRoomTeams();
        if (playerToken) {
          enterWorkspace();
        }
      }

      setInterval(() => {
        if (roomState && !roomState.is_paused && remainingSeconds > 0) {
          remainingSeconds--;
          document.getElementById('topTimerBadge').textContent = formatTimer(remainingSeconds);
        }
      }, 1000);

      setInterval(fetchPlayerState, 2200);
    });
  </script>
</body>
</html>
