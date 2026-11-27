<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Host Command Center [{{ $room->code }}] — The Problem Detective</title>

  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
    .tab-btn.active {
      background-color: #FAFAFA;
      color: #09090B;
      border-color: #FAFAFA;
    }
    .phase-btn.active {
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
<body class="min-h-screen antialiased pb-16">

  <!-- Top Sticky Header -->
  <header class="sticky top-0 z-40 border-b border-zinc-800 bg-zinc-950/95">
    <div class="max-w-[1440px] mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('games.problem-detective.hub') }}" class="px-3 py-1.5 rounded-lg border border-zinc-800 text-xs font-bold text-zinc-400 hover:text-white">
          ← Hub
        </a>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-1 rounded bg-white text-black font-mono-code text-xs font-extrabold tracking-widest">
            ROOM: {{ $room->code }}
          </span>
          <span id="headerPhaseBadge" class="px-2.5 py-1 rounded border border-zinc-700 bg-zinc-900 font-mono-code text-xs font-bold uppercase text-zinc-200">
            {{ strtoupper($room->phase) }}
          </span>
          <span id="headerPlayerCount" class="text-xs font-bold text-zinc-400">
            0/{{ $room->target_players }} Players
          </span>
        </div>
      </div>

      <!-- Countdown & Timer Quick Bar -->
      <div class="flex flex-wrap items-center gap-2">
        <div id="hostTimerDisplay" class="px-3.5 py-1.5 rounded-lg bg-zinc-900 border border-zinc-700 font-mono-code text-lg font-extrabold tabular-nums text-white">
          00:00
        </div>
        <button onclick="controlTimer('pause')" id="btnPauseTimer" class="px-2.5 py-1.5 rounded-lg border border-zinc-700 text-xs font-bold text-zinc-200 hover:bg-zinc-800 cursor-pointer">⏸ Pause</button>
        <button onclick="controlTimer('resume')" id="btnResumeTimer" class="hidden px-2.5 py-1.5 rounded-lg bg-emerald-500 text-black text-xs font-extrabold hover:bg-emerald-400 cursor-pointer">▶ Resume</button>
        <button onclick="controlTimer('add', 60)" class="px-2.5 py-1.5 rounded-lg border border-zinc-700 text-xs font-bold text-zinc-200 hover:bg-zinc-800 cursor-pointer">+1m</button>
        <button onclick="controlTimer('reduce', 60)" class="px-2.5 py-1.5 rounded-lg border border-zinc-700 text-xs font-bold text-zinc-200 hover:bg-zinc-800 cursor-pointer">-1m</button>
        <button onclick="controlTimer('reset')" class="px-2.5 py-1.5 rounded-lg border border-zinc-700 text-xs font-bold text-zinc-200 hover:bg-zinc-800 cursor-pointer">↺ Reset</button>
      </div>

      <!-- Utility Links -->
      <div class="flex flex-wrap items-center gap-2">
        <button onclick="copyJoinLink()" class="px-3 py-1.5 rounded-lg border border-zinc-700 text-xs font-bold text-zinc-200 hover:bg-zinc-800 cursor-pointer">
          🔗 Copy Join Link
        </button>
        <a href="{{ route('games.problem-detective.projector', ['code' => $room->code]) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-zinc-800 border border-zinc-600 text-xs font-extrabold text-white hover:bg-zinc-700">
          ⛶ Projector Mode
        </a>
        <button onclick="confirmResetRoom()" class="px-3 py-1.5 rounded-lg border border-red-900/70 text-xs font-bold text-red-400 hover:bg-red-950/40 cursor-pointer">
          Reset Room
        </button>
        <button onclick="createBrandNewRoom()" class="px-3 py-1.5 rounded-lg bg-white text-black text-xs font-extrabold hover:bg-zinc-200 cursor-pointer">
          + Room Baru
        </button>
      </div>
    </div>

    <!-- Phase Controller Bar -->
    <div class="max-w-[1440px] mx-auto px-4 pb-3 flex items-center gap-1.5 overflow-x-auto">
      @php
        $phaseSteps = [
          'lobby' => '00. Lobby',
          'briefing' => '01. Briefing',
          'discussion' => '02. Discussion',
          'investigation' => '03. Investigation',
          'deduction' => '04. Deduction',
          'defense' => '05. Defense',
          'reveal' => '06. Truth Reveal',
          'leaderboard' => '07. Leaderboard',
        ];
      @endphp
      @foreach ($phaseSteps as $pKey => $pLabel)
        <button data-phase="{{ $pKey }}" onclick="setPhase('{{ $pKey }}')" class="phase-btn px-3 py-1.5 rounded-lg border border-zinc-800 text-xs font-bold text-zinc-400 hover:text-white whitespace-nowrap transition cursor-pointer">
          {{ $pLabel }}
        </button>
      @endforeach
    </div>
  </header>

  <!-- Main Workspace Tabs -->
  <div class="max-w-[1440px] mx-auto px-4 pt-5">
    <div class="flex flex-wrap items-center gap-2 border-b border-zinc-800 pb-4 mb-6">
      <button data-tab="command" onclick="switchTab('command')" class="tab-btn active px-4 py-2 rounded-lg border border-zinc-800 text-xs font-extrabold uppercase tracking-wider cursor-pointer">
        🎛 Live Command Center <span id="pendingQuestionsPill" class="hidden ml-1.5 px-1.5 py-0.5 rounded bg-red-500 text-white text-[10px]">0</span>
      </button>
      <button data-tab="lobby" onclick="switchTab('lobby')" class="tab-btn px-4 py-2 rounded-lg border border-zinc-800 text-xs font-extrabold uppercase tracking-wider text-zinc-400 cursor-pointer">
        👥 Lobby, QR & Konfigurasi
      </button>
      <button data-tab="dossier" onclick="switchTab('dossier')" class="tab-btn px-4 py-2 rounded-lg border border-zinc-800 text-xs font-extrabold uppercase tracking-wider text-zinc-400 cursor-pointer">
        📁 Informasi Rahasia Kasus
      </button>
      <button data-tab="builder" onclick="switchTab('builder')" class="tab-btn px-4 py-2 rounded-lg border border-zinc-800 text-xs font-extrabold uppercase tracking-wider text-zinc-400 cursor-pointer">
        🛠 Custom Case Builder (<span id="caseCountBadge">{{ $cases->count() }}</span>)
      </button>
    </div>

    <!-- TAB 1: LIVE COMMAND CENTER -->
    <section id="tab-command" class="space-y-6">
      <!-- Top Row: Active Case Banner + Breaking News Broadcaster -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <div class="lg:col-span-7 case-card p-5 space-y-2">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <span class="font-mono-code text-xs uppercase text-zinc-500">ACTIVE CASE FILE</span>
            <span id="cmdCaseMeta" class="font-mono-code text-xs uppercase px-2 py-0.5 rounded border border-zinc-700 text-zinc-300">-</span>
          </div>
          <h2 id="cmdCaseTitle" class="text-xl font-extrabold text-white">-</h2>
          <p id="cmdCaseDesc" class="text-xs text-zinc-400 leading-relaxed">-</p>
          <div class="pt-1 text-xs text-emerald-400 font-semibold">
            Root Cause (Rahasia GM): <span id="cmdRootCause" class="text-zinc-200">-</span>
          </div>
        </div>

        <div class="lg:col-span-5 case-card p-5 space-y-3 border-red-950/60">
          <div class="flex items-center justify-between">
            <span class="font-mono-code text-xs uppercase text-red-400 font-bold">● BREAKING NEWS BROADCASTER</span>
            <span class="text-[11px] text-zinc-500">Muncul di layar pemain & proyektor</span>
          </div>
          <div class="flex gap-2">
            <input id="breakingNewsInput" type="text" placeholder="Ketik info mendadak untuk seluruh tim..." class="flex-1 bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500">
            <button onclick="sendBreakingNews()" class="px-4 py-2 rounded-lg bg-red-600 text-white text-xs font-extrabold hover:bg-red-500 cursor-pointer shrink-0">
              Kirim Alert
            </button>
          </div>
          <div id="presetBreakingNewsWrap" class="flex flex-wrap gap-1.5">
            <!-- Populated from case breaking_news -->
          </div>
        </div>
      </div>

      <!-- Teams Grid -->
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="font-mono-code text-xs uppercase tracking-widest text-zinc-400">STATUS KELOMPOK, SISA NYAWA & PENILAIAN</h3>
          <span id="investigationModeLabel" class="font-mono-code text-xs uppercase text-zinc-400">MODE: PRIVATE</span>
        </div>
        <div id="cmdTeamsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
          <!-- Rendered by JS -->
        </div>
      </div>

      <!-- Incoming Questions Queue + Secret Quick Facts Side-by-Side -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Questions Inbox (8 cols) -->
        <div class="lg:col-span-8 case-card p-5 space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-800 pb-3">
            <div>
              <h3 class="text-base font-extrabold text-white">Antrean Pertanyaan Investigasi (Realtime)</h3>
              <p class="text-xs text-zinc-400">Jawab cepat menggunakan fakta rahasia, balas manual, atau tandai status pertanyaan.</p>
            </div>
            <select id="questionFilterTeam" onchange="renderCommandCenter()" class="bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-200">
              <option value="all">Semua Kelompok</option>
            </select>
          </div>
          <div id="questionsInbox" class="space-y-3 max-h-[640px] overflow-y-auto pr-1">
            <!-- Rendered by JS -->
          </div>
        </div>

        <!-- Quick Secret Facts Reference (4 cols) -->
        <div class="lg:col-span-4 case-card p-5 space-y-3">
          <div class="border-b border-zinc-800 pb-2.5">
            <h3 class="font-mono-code text-xs uppercase tracking-widest text-emerald-400 font-bold">QUICK FACTS & HINTS</h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Klik fakta untuk menyalin ke clipboard atau klik "Gunakan Fakta" di kartu pertanyaan.</p>
          </div>
          <div id="quickFactsSidebar" class="space-y-2 max-h-[580px] overflow-y-auto pr-1 text-xs">
            <!-- Rendered by JS -->
          </div>
        </div>
      </div>
    </section>

    <!-- TAB 2: LOBBY, QR & ROOM CONFIG -->
    <section id="tab-lobby" class="hidden space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: QR Code & Lobby Players (5 cols) -->
        <div class="lg:col-span-5 space-y-5">
          <div class="case-card p-6 flex flex-col items-center text-center space-y-4">
            <span class="font-mono-code text-xs uppercase tracking-widest text-zinc-500">SCAN QR UNTUK BERGABUNG</span>
            <div class="bg-white p-4 rounded-xl">
              <div id="hostQrCode"></div>
            </div>
            <div class="space-y-1">
              <div class="font-mono-code text-3xl font-extrabold tracking-widest text-white">{{ $room->code }}</div>
              <div id="joinUrlDisplay" class="text-xs text-zinc-400 break-all font-mono-code"></div>
            </div>
          </div>

          <div class="case-card p-5 space-y-3">
            <div class="flex items-center justify-between border-b border-zinc-800 pb-2">
              <h3 class="text-sm font-extrabold text-white">Pemain Terhubung (<span id="lobbyTotalConnected">0</span>)</h3>
              <span id="lobbyReadySummary" class="text-xs font-mono-code text-emerald-400">0 Siap</span>
            </div>
            <div id="lobbyRosterList" class="space-y-3 max-h-[420px] overflow-y-auto">
              <!-- Rendered by JS -->
            </div>
          </div>
        </div>

        <!-- Right: Room Configuration (7 cols) -->
        <div class="lg:col-span-7 case-card p-6 space-y-5">
          <div class="border-b border-zinc-800 pb-3">
            <h3 class="text-lg font-extrabold text-white">Konfigurasi Room & Kelompok</h3>
            <p class="text-xs text-zinc-400">Sesuaikan kasus, jumlah pemain, nama/kapasitas kelompok, nyawa awal, dan durasi fase.</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Pilih Kasus Investigasi</label>
              <select id="cfgCaseId" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2.5 text-sm font-bold text-white">
                @foreach ($cases as $c)
                  <option value="{{ $c->id }}" {{ $room->detective_case_id === $c->id ? 'selected' : '' }}>
                    {{ $c->title }} ({{ strtoupper($c->difficulty) }})
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Mode Investigasi</label>
              <select id="cfgInvestigationMode" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2.5 text-sm font-bold text-white">
                <option value="private" {{ $room->investigation_mode === 'private' ? 'selected' : '' }}>Private Investigation (Default)</option>
                <option value="public" {{ $room->investigation_mode === 'public' ? 'selected' : '' }}>Public Investigation (Terbuka)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Nyawa Investigasi Awal</label>
              <input id="cfgDefaultLives" type="number" min="1" max="50" value="{{ $room->default_lives }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2.5 text-sm font-bold text-white">
            </div>

            <div>
              <label class="block text-xs font-bold uppercase text-zinc-400 mb-1.5">Target Jumlah Pemain</label>
              <input id="cfgTargetPlayers" type="number" min="1" max="100" value="{{ $room->target_players }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2.5 text-sm font-bold text-white">
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Briefing (dtk)</label>
                <input id="cfgDurBriefing" type="number" min="30" max="1800" value="{{ $room->phase_durations['briefing'] ?? 120 }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-2.5 py-2 text-xs font-bold text-white">
              </div>
              <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Discussion (dtk)</label>
                <input id="cfgDurDiscussion" type="number" min="30" max="1800" value="{{ $room->phase_durations['discussion'] ?? 180 }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-2.5 py-2 text-xs font-bold text-white">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:col-span-2">
              <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Investigation (dtk)</label>
                <input id="cfgDurInvestigation" type="number" min="60" max="3600" value="{{ $room->phase_durations['investigation'] ?? 600 }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-2.5 py-2 text-xs font-bold text-white">
              </div>
              <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Final Deduction (dtk)</label>
                <input id="cfgDurDeduction" type="number" min="60" max="3600" value="{{ $room->phase_durations['deduction'] ?? 300 }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-2.5 py-2 text-xs font-bold text-white">
              </div>
            </div>
          </div>

          <!-- Dynamic Teams Config -->
          <div class="space-y-3 pt-2 border-t border-zinc-800">
            <div class="flex items-center justify-between">
              <label class="text-xs font-bold uppercase text-zinc-400">Pengaturan Kelompok</label>
              <button type="button" onclick="addConfigTeamRow()" class="px-3 py-1 rounded border border-zinc-700 text-xs font-bold text-zinc-200 hover:bg-zinc-800 cursor-pointer">
                + Tambah Kelompok
              </button>
            </div>
            <div id="cfgTeamsList" class="space-y-2">
              <!-- Populated by JS -->
            </div>
          </div>

          <div class="pt-3 flex justify-end gap-3">
            <button onclick="saveRoomConfig()" class="px-6 py-3 rounded-lg bg-white text-black font-extrabold text-sm hover:bg-zinc-200 cursor-pointer">
              💾 Simpan Konfigurasi Room
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- TAB 3: SECRET CASE DOSSIER -->
    <section id="tab-dossier" class="hidden space-y-5">
      <div id="dossierContent" class="case-card p-6 sm:p-8 space-y-6">
        <!-- Rendered by JS -->
      </div>
    </section>

    <!-- TAB 4: CUSTOM CASE BUILDER -->
    <section id="tab-builder" class="hidden space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-extrabold text-white">Custom Case Builder</h2>
          <p class="text-xs text-zinc-400">Buat, edit, duplikasi, atau hapus kasus pembelajaran dan simpan ke database.</p>
        </div>
        <button onclick="openCaseForm()" class="px-5 py-2.5 rounded-lg bg-white text-black font-extrabold text-xs uppercase hover:bg-zinc-200 cursor-pointer">
          + Buat Kasus Baru
        </button>
      </div>

      <div id="builderCasesList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Rendered by JS -->
      </div>
    </section>
  </div>

  <!-- MODAL: INSPECT & SCORE TEAM -->
  <div id="scoreModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80">
    <div class="case-card max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-5 border-zinc-600">
      <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
        <div>
          <span class="font-mono-code text-xs uppercase text-zinc-400">LAPORAN & PENILAIAN</span>
          <h3 id="scoreModalTeamName" class="text-xl font-extrabold text-white">-</h3>
        </div>
        <button onclick="closeScoreModal()" class="px-3 py-1 rounded border border-zinc-700 text-xs font-bold text-zinc-300 hover:bg-zinc-800 cursor-pointer">✕ Tutup</button>
      </div>

      <div id="scoreModalReportBox" class="space-y-3 text-xs bg-zinc-950 border border-zinc-800 rounded-xl p-4">
        <!-- Team's Final Report -->
      </div>

      <form id="scoreForm" onsubmit="submitTeamScore(event)" class="space-y-4">
        <input type="hidden" id="scoreTeamId">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-zinc-300 mb-1">Kualitas Pertanyaan Investigasi (0–20)</label>
            <input id="scQuestionQuality" type="number" min="0" max="20" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-sm font-bold text-white" oninput="recalcTotalScore()">
          </div>
          <div>
            <label class="block text-xs font-bold text-zinc-300 mb-1">Ketepatan Akar Permasalahan (0–30)</label>
            <input id="scRootCause" type="number" min="0" max="30" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-sm font-bold text-white" oninput="recalcTotalScore()">
          </div>
          <div>
            <label class="block text-xs font-bold text-zinc-300 mb-1">Kekuatan Bukti Pendukung (0–20)</label>
            <input id="scEvidence" type="number" min="0" max="20" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-sm font-bold text-white" oninput="recalcTotalScore()">
          </div>
          <div>
            <label class="block text-xs font-bold text-zinc-300 mb-1">Efektivitas Solusi (0–20)</label>
            <input id="scSolution" type="number" min="0" max="20" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-sm font-bold text-white" oninput="recalcTotalScore()">
          </div>
          <div>
            <label class="block text-xs font-bold text-zinc-300 mb-1">Efisiensi Investigasi (0–10, syarat kesimpulan benar)</label>
            <input id="scEfficiency" type="number" min="0" max="10" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-sm font-bold text-white" oninput="recalcTotalScore()">
          </div>
          <div class="flex items-end">
            <div class="w-full p-2.5 rounded-lg bg-zinc-900 border border-zinc-700 text-center">
              <span class="text-xs text-zinc-400 font-bold">TOTAL SKOR:</span>
              <span id="scTotalDisplay" class="font-mono-code text-2xl font-extrabold text-emerald-400 ml-2">0</span>
              <span class="text-xs text-zinc-500">/ 100</span>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-zinc-300 mb-2">Penghargaan Khusus (Opsional)</label>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
            @foreach (['Master Detective', 'Sharpest Mind', 'Evidence Hunter', 'Most Efficient Team', 'Best Problem Solver', 'Best Defense'] as $award)
              <label class="flex items-center gap-2 p-2 rounded border border-zinc-800 bg-zinc-950 cursor-pointer">
                <input type="checkbox" name="awards[]" value="{{ $award }}" class="accent-emerald-500">
                <span class="font-semibold">{{ $award }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-zinc-300 mb-1">Catatan Game Master</label>
          <textarea id="scFeedback" rows="2" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-xs text-white" placeholder="Umpan balik singkat untuk kelompok..."></textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeScoreModal()" class="px-4 py-2 rounded-lg border border-zinc-700 text-xs font-bold text-zinc-300 cursor-pointer">Batal</button>
          <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-500 text-black text-xs font-extrabold cursor-pointer">💾 Simpan Nilai</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: CUSTOM CASE BUILDER FORM -->
  <div id="caseBuilderModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80">
    <div class="case-card max-w-4xl w-full max-h-[92vh] overflow-y-auto p-6 space-y-5 border-zinc-600">
      <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
        <h3 id="caseBuilderModalTitle" class="text-lg font-extrabold text-white">Buat Kasus Baru</h3>
        <button onclick="closeCaseForm()" class="px-3 py-1 rounded border border-zinc-700 text-xs font-bold text-zinc-300 cursor-pointer">✕ Tutup</button>
      </div>
      <form id="caseBuilderForm" onsubmit="saveCustomCase(event)" class="space-y-4 text-xs">
        <input type="hidden" id="cbId">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="sm:col-span-2">
            <label class="block font-bold text-zinc-300 mb-1">Case Title *</label>
            <input id="cbTitle" type="text" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white font-semibold">
          </div>
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Category *</label>
            <input id="cbCategory" type="text" required placeholder="Backend / UX / System" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white font-semibold">
          </div>
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Difficulty *</label>
            <select id="cbDifficulty" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white font-semibold">
              <option value="easy">Easy</option>
              <option value="medium" selected>Medium</option>
              <option value="hard">Hard</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Default Duration (Menit)</label>
            <input id="cbDuration" type="number" min="5" max="120" value="20" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white font-semibold">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Case Description *</label>
            <textarea id="cbDescription" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Initial Assumption (Dugaan Awal) *</label>
            <textarea id="cbAssumption" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Mission Objectives (1 baris per poin) *</label>
            <textarea id="cbObjectives" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
          <div>
            <label class="block font-bold text-emerald-400 mb-1">Hidden Facts / Informasi Rahasia (1 baris per fakta) *</label>
            <textarea id="cbFacts" rows="5" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Evidence (Bukti Pembeda, 1 per baris) *</label>
            <textarea id="cbEvidence" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
          <div>
            <label class="block font-bold text-zinc-300 mb-1">False Leads (Pengalih, 1 per baris)</label>
            <textarea id="cbFalseLeads" rows="3" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Special Hints (1 per baris)</label>
            <textarea id="cbHints" rows="2" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
          <div>
            <label class="block font-bold text-red-400 mb-1">Breaking News Preset (1 per baris)</label>
            <textarea id="cbBreaking" rows="2" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div>
            <label class="block font-bold text-emerald-400 mb-1">Root Cause *</label>
            <textarea id="cbRootCause" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Recommended Solutions (1 per baris) *</label>
            <textarea id="cbSolutions" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
          <div>
            <label class="block font-bold text-zinc-300 mb-1">Validation Method *</label>
            <textarea id="cbValidation" rows="3" required class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-white"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeCaseForm()" class="px-4 py-2 rounded-lg border border-zinc-700 font-bold text-zinc-300 cursor-pointer">Batal</button>
          <button type="submit" class="px-5 py-2.5 rounded-lg bg-white text-black font-extrabold cursor-pointer">💾 Simpan Kasus</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const ROOM_CODE = @json($room->code);
    const HOST_TOKEN = @json($room->host_token);
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const JOIN_URL = @json(route('games.problem-detective.play', ['code' => $room->code]));

    localStorage.setItem('detective_host_token_' + ROOM_CODE, HOST_TOKEN);

    let roomState = null;
    let remainingSeconds = 0;
    let casesCache = @json($cases);
    let configTeams = [];
    let currentTab = 'command';

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

    function switchTab(tab) {
      currentTab = tab;
      document.querySelectorAll('.tab-btn').forEach((b) => {
        b.classList.toggle('active', b.getAttribute('data-tab') === tab);
        if (b.getAttribute('data-tab') !== tab) b.classList.add('text-zinc-400');
        else b.classList.remove('text-zinc-400');
      });
      ['command', 'lobby', 'dossier', 'builder'].forEach((t) => {
        document.getElementById('tab-' + t).classList.toggle('hidden', t !== tab);
      });
    }

    async function hostApi(url, method = 'POST', body = {}) {
      const res = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'X-Host-Token': HOST_TOKEN,
        },
        body: method === 'GET' ? undefined : JSON.stringify({ code: ROOM_CODE, ...body }),
      });
      return await res.json();
    }

    async function fetchState() {
      try {
        const res = await fetch(`{{ route('games.problem-detective.state') }}?code=${ROOM_CODE}&role=host`, {
          headers: {
            'Accept': 'application/json',
            'X-Host-Token': HOST_TOKEN,
          }
        });
        const data = await res.json();
        if (data.success && data.room) {
          roomState = data.room;
          remainingSeconds = data.remaining_seconds ?? 0;
          renderAll();
        }
      } catch (e) {
        console.warn('Sync error:', e);
      }
    }

    function renderAll() {
      if (!roomState) return;

      // Header
      document.getElementById('headerPhaseBadge').textContent = roomState.phase.toUpperCase();
      document.getElementById('headerPlayerCount').textContent = `${roomState.total_players}/${roomState.target_players} Players`;

      const timerEl = document.getElementById('hostTimerDisplay');
      timerEl.textContent = formatTimer(remainingSeconds);
      timerEl.classList.toggle('text-red-500', remainingSeconds > 0 && remainingSeconds <= 15);

      document.getElementById('btnPauseTimer').classList.toggle('hidden', roomState.is_paused);
      document.getElementById('btnResumeTimer').classList.toggle('hidden', !roomState.is_paused);

      document.querySelectorAll('.phase-btn').forEach((btn) => {
        btn.classList.toggle('active', btn.getAttribute('data-phase') === roomState.phase);
      });

      const pendingCount = (roomState.questions || []).filter((q) => q.status === 'pending').length;
      const pill = document.getElementById('pendingQuestionsPill');
      pill.textContent = pendingCount;
      pill.classList.toggle('hidden', pendingCount === 0);

      renderCommandCenter();
      renderLobbyTab();
      renderDossierTab();
      renderCaseBuilder();
    }

    function renderCommandCenter() {
      const c = roomState.case;
      document.getElementById('cmdCaseTitle').textContent = c ? c.title : 'Belum Ada Kasus';
      document.getElementById('cmdCaseDesc').textContent = c ? c.description : '-';
      document.getElementById('cmdCaseMeta').textContent = c ? `${c.category} • ${c.difficulty}` : '-';
      document.getElementById('cmdRootCause').textContent = c ? c.root_cause : '-';
      document.getElementById('investigationModeLabel').textContent = `INVESTIGATION: ${roomState.investigation_mode.toUpperCase()}`;

      // Preset Breaking News buttons
      const bnWrap = document.getElementById('presetBreakingNewsWrap');
      const presets = (c && c.breaking_news) ? c.breaking_news : [];
      bnWrap.innerHTML = presets.map((msg) => `
        <button onclick='sendPresetBreakingNews(${JSON.stringify(msg)})' class="px-2.5 py-1 rounded border border-red-900/60 bg-red-950/30 text-[11px] font-bold text-red-300 hover:bg-red-900/50 cursor-pointer truncate max-w-full">
          ⚡ ${escapeHtml(msg)}
        </button>
      `).join('');

      // Teams Grid
      const grid = document.getElementById('cmdTeamsGrid');
      grid.innerHTML = (roomState.teams || []).map((t) => {
        const isDefending = roomState.phase === 'defense' && roomState.defense_team_id === t.id;
        return `
          <div class="case-card p-4 space-y-3 ${isDefending ? 'border-emerald-500' : ''}">
            <div class="flex items-start justify-between gap-2">
              <div>
                <h4 class="font-extrabold text-sm text-white">${escapeHtml(t.name)}</h4>
                <div class="text-[11px] text-zinc-400">${t.players.length}/${t.max_capacity} anggota</div>
              </div>
              <span class="px-2 py-0.5 rounded font-mono-code text-xs font-extrabold ${t.lives <= 2 ? 'bg-red-600 text-white' : 'bg-zinc-800 text-red-400'}">
                ♥ ${t.lives}
              </span>
            </div>

            <div class="flex items-center justify-between text-[11px] font-mono-code text-zinc-400 border-t border-zinc-800 pt-2">
              <span>Q: ${t.questions_asked_count}</span>
              <span class="${t.submitted_at ? 'text-emerald-400 font-bold' : 'text-zinc-500'}">
                ${t.submitted_at ? '✓ Submitted' : 'Investigating'}
              </span>
              <span class="text-white font-bold">${t.total_score} pts</span>
            </div>

            <div class="flex flex-wrap gap-1.5 pt-1">
              <button onclick="adjustLives(${t.id}, -1)" class="px-2 py-1 rounded border border-zinc-700 text-[11px] font-bold text-red-400 hover:bg-zinc-800 cursor-pointer">-1 ♥</button>
              <button onclick="adjustLives(${t.id}, 1)" class="px-2 py-1 rounded border border-zinc-700 text-[11px] font-bold text-emerald-400 hover:bg-zinc-800 cursor-pointer">+1 ♥</button>
              <button onclick="openScoreModal(${t.id})" class="flex-1 px-2 py-1 rounded bg-white text-black text-[11px] font-extrabold hover:bg-zinc-200 cursor-pointer">
                Nilai / Laporan
              </button>
              ${roomState.phase === 'defense' ? `
                <button onclick="setDefenseTeam(${t.id})" class="w-full px-2 py-1 rounded ${isDefending ? 'bg-emerald-500 text-black' : 'border border-zinc-700 text-zinc-300'} text-[11px] font-extrabold cursor-pointer">
                  ${isDefending ? '★ Sedang Presentasi' : 'Tampilkan di Proyektor'}
                </button>
              ` : ''}
            </div>
          </div>
        `;
      }).join('');

      // Team filter dropdown
      const sel = document.getElementById('questionFilterTeam');
      const prevVal = sel.value;
      sel.innerHTML = `<option value="all">Semua Kelompok</option>` +
        (roomState.teams || []).map((t) => `<option value="${t.id}" ${String(t.id) === prevVal ? 'selected' : ''}>${escapeHtml(t.name)}</option>`).join('');

      // Questions Inbox (pending first)
      const filterTeam = sel.value;
      const questions = (roomState.questions || [])
        .filter((q) => filterTeam === 'all' || String(q.team_id) === filterTeam)
        .slice()
        .sort((a, b) => {
          if (a.status === 'pending' && b.status !== 'pending') return -1;
          if (a.status !== 'pending' && b.status === 'pending') return 1;
          return b.id - a.id;
        });

      const inbox = document.getElementById('questionsInbox');
      const facts = c ? (c.hidden_facts || []) : [];

      if (questions.length === 0) {
        inbox.innerHTML = `<div class="py-12 text-center text-xs text-zinc-500 font-mono-code">BELUM ADA PERTANYAAN MASUK DARI KELOMPOK</div>`;
      } else {
        inbox.innerHTML = questions.map((q) => {
          const isPending = q.status === 'pending';
          const statusBadge = {
            pending: '<span class="px-2 py-0.5 rounded bg-red-600 text-white text-[10px] font-extrabold uppercase">MENUNGGU JAWABAN</span>',
            answered: '<span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-extrabold uppercase">TERJAWAB</span>',
            irrelevant: '<span class="px-2 py-0.5 rounded bg-zinc-800 text-zinc-400 text-[10px] font-extrabold uppercase">TIDAK RELEVAN</span>',
            unknown: '<span class="px-2 py-0.5 rounded bg-zinc-800 text-amber-400 text-[10px] font-extrabold uppercase">BELUM DIKETAHUI</span>',
            rejected_direct: '<span class="px-2 py-0.5 rounded bg-red-950 text-red-400 border border-red-800 text-[10px] font-extrabold uppercase">DITOLAK (MINTA JAWABAN)</span>',
          }[q.status] || '';

          return `
            <div class="p-4 rounded-xl border ${isPending ? 'border-red-500/60 bg-zinc-900/90' : 'border-zinc-800 bg-zinc-950'} space-y-3">
              <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                  <span class="px-2 py-0.5 rounded bg-white text-black text-[11px] font-extrabold">${escapeHtml(q.team_name)}</span>
                  <span class="text-xs font-bold text-zinc-300">${escapeHtml(q.player_name)}</span>
                  ${q.is_expert_consultation ? '<span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-extrabold">EXPERT CONSULTATION (-2 ♥)</span>' : ''}
                </div>
                <div class="flex items-center gap-2">
                  ${statusBadge}
                  <span class="font-mono-code text-[11px] text-zinc-500">${q.created_at}</span>
                </div>
              </div>

              <p class="text-sm font-bold text-white leading-snug">“${escapeHtml(q.question_text)}”</p>

              ${q.answer_text ? `
                <div class="p-3 rounded-lg bg-zinc-900 border border-zinc-800 text-xs text-emerald-300 font-semibold">
                  <span class="font-mono-code text-[10px] uppercase text-zinc-500 block mb-0.5">Jawaban Game Master:</span>
                  ${escapeHtml(q.answer_text)}
                </div>
              ` : ''}

              ${isPending ? `
                <div class="space-y-2 pt-1">
                  <div class="flex flex-wrap gap-1.5">
                    <select onchange="fillQuickFact(${q.id}, this.value); this.selectedIndex=0;" class="bg-zinc-950 border border-zinc-700 rounded-lg px-2.5 py-1.5 text-xs text-zinc-200 max-w-full">
                      <option value="">⚡ Pilih Quick Reply dari Informasi Rahasia Kasus...</option>
                      ${facts.map((f, idx) => `<option value="${escapeHtml(f)}">Fakta #${idx + 1}: ${escapeHtml(f.slice(0, 80))}${f.length > 80 ? '...' : ''}</option>`).join('')}
                      ${(c.special_hints || []).map((h, idx) => `<option value="${escapeHtml(h)}">💡 Petunjuk #${idx + 1}: ${escapeHtml(h.slice(0, 75))}</option>`).join('')}
                    </select>
                  </div>
                  <div class="flex gap-2">
                    <input id="ansInput_${q.id}" type="text" placeholder="Ketik jawaban manual atau pilih fakta di atas..." class="flex-1 bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                    <button onclick="sendAnswer(${q.id}, 'answered')" class="px-4 py-2 rounded-lg bg-emerald-500 text-black text-xs font-extrabold hover:bg-emerald-400 cursor-pointer">
                      Kirim Jawaban
                    </button>
                  </div>
                  <div class="flex flex-wrap gap-1.5">
                    <button onclick="sendAnswer(${q.id}, 'irrelevant')" class="px-2.5 py-1 rounded border border-zinc-700 text-[11px] font-bold text-zinc-300 hover:bg-zinc-800 cursor-pointer">Tandai Tidak Relevan</button>
                    <button onclick="sendAnswer(${q.id}, 'unknown')" class="px-2.5 py-1 rounded border border-zinc-700 text-[11px] font-bold text-amber-300 hover:bg-zinc-800 cursor-pointer">Belum Diketahui</button>
                    <button onclick="sendAnswer(${q.id}, 'rejected_direct')" class="px-2.5 py-1 rounded border border-red-900/70 text-[11px] font-bold text-red-400 hover:bg-red-950/40 cursor-pointer">Tolak Tanya Jawaban Langsung</button>
                  </div>
                </div>
              ` : ''}
            </div>
          `;
        }).join('');
      }

      // Quick Facts Sidebar
      const sidebar = document.getElementById('quickFactsSidebar');
      sidebar.innerHTML = [
        ...facts.map((f, i) => `
          <div class="p-2.5 rounded-lg bg-zinc-950 border border-zinc-800 text-zinc-300 leading-relaxed">
            <span class="font-mono-code text-[10px] text-emerald-400 font-bold block mb-0.5">FAKTA #${i + 1}</span>
            ${escapeHtml(f)}
          </div>
        `),
        ...(c?.special_hints || []).map((h, i) => `
          <div class="p-2.5 rounded-lg bg-zinc-950 border border-amber-900/40 text-amber-200 leading-relaxed">
            <span class="font-mono-code text-[10px] text-amber-400 font-bold block mb-0.5">SPECIAL HINT #${i + 1}</span>
            ${escapeHtml(h)}
          </div>
        `),
      ].join('');
    }

    function renderLobbyTab() {
      document.getElementById('lobbyTotalConnected').textContent = roomState.total_players;
      let readyTotal = 0;
      const rosterHtml = (roomState.teams || []).map((t) => {
        const playersHtml = t.players.length === 0
          ? '<span class="text-xs text-zinc-600 italic">Belum ada anggota</span>'
          : t.players.map((p) => {
              if (p.is_ready) readyTotal++;
              return `
                <div class="flex items-center justify-between py-1 px-2 rounded bg-zinc-950 border border-zinc-800/80 text-xs">
                  <span class="font-semibold text-zinc-200">
                    ${p.is_ready ? '<span class="text-emerald-400 mr-1">●</span>' : '<span class="text-zinc-600 mr-1">○</span>'}
                    ${escapeHtml(p.name)}
                  </span>
                  <button onclick="kickPlayer(${p.id})" class="text-[10px] text-red-400 hover:underline cursor-pointer">Kick</button>
                </div>
              `;
            }).join('');

        return `
          <div class="p-3 rounded-lg border border-zinc-800 bg-zinc-900/40 space-y-2">
            <div class="flex items-center justify-between text-xs font-extrabold">
              <span class="text-white">${escapeHtml(t.name)}</span>
              <span class="font-mono-code text-zinc-400">${t.players.length}/${t.max_capacity}</span>
            </div>
            <div class="space-y-1">${playersHtml}</div>
          </div>
        `;
      }).join('');

      document.getElementById('lobbyReadySummary').textContent = `${readyTotal} Siap`;
      document.getElementById('lobbyRosterList').innerHTML = rosterHtml;

      // Sync config team editor ONLY if user is not actively typing inside it
      if (!document.getElementById('cfgTeamsList').dataset.dirty) {
        configTeams = (roomState.teams || []).map((t) => ({ id: t.id, name: t.name, max_capacity: t.max_capacity }));
        renderConfigTeamsEditor();
      }
    }

    function renderConfigTeamsEditor() {
      const container = document.getElementById('cfgTeamsList');
      container.innerHTML = configTeams.map((t, idx) => `
        <div class="flex items-center gap-2">
          <input type="text" value="${escapeHtml(t.name)}" onchange="configTeams[${idx}].name=this.value; document.getElementById('cfgTeamsList').dataset.dirty='1';" class="flex-1 bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-xs font-bold text-white" placeholder="Nama Kelompok">
          <input type="number" min="1" max="30" value="${t.max_capacity}" onchange="configTeams[${idx}].max_capacity=Number(this.value); document.getElementById('cfgTeamsList').dataset.dirty='1';" class="w-20 bg-zinc-950 border border-zinc-800 rounded-lg px-2.5 py-2 text-xs font-bold text-white" title="Kapasitas">
          <button type="button" onclick="removeConfigTeamRow(${idx})" class="px-2.5 py-2 rounded-lg border border-red-900/60 text-xs text-red-400 cursor-pointer">✕</button>
        </div>
      `).join('');
    }

    function addConfigTeamRow() {
      document.getElementById('cfgTeamsList').dataset.dirty = '1';
      configTeams.push({ id: null, name: 'Team ' + String.fromCharCode(65 + configTeams.length), max_capacity: 3 });
      renderConfigTeamsEditor();
    }

    function removeConfigTeamRow(idx) {
      if (configTeams.length <= 1) {
        Swal.fire('Minimal 1 Kelompok', 'Harus ada minimal 1 kelompok dalam room.', 'warning');
        return;
      }
      document.getElementById('cfgTeamsList').dataset.dirty = '1';
      configTeams.splice(idx, 1);
      renderConfigTeamsEditor();
    }

    async function saveRoomConfig() {
      const payload = {
        detective_case_id: Number(document.getElementById('cfgCaseId').value),
        investigation_mode: document.getElementById('cfgInvestigationMode').value,
        default_lives: Number(document.getElementById('cfgDefaultLives').value),
        target_players: Number(document.getElementById('cfgTargetPlayers').value),
        phase_durations: {
          briefing: Number(document.getElementById('cfgDurBriefing').value),
          discussion: Number(document.getElementById('cfgDurDiscussion').value),
          investigation: Number(document.getElementById('cfgDurInvestigation').value),
          deduction: Number(document.getElementById('cfgDurDeduction').value),
        },
        teams: configTeams,
      };
      const res = await hostApi("{{ route('games.problem-detective.config') }}", 'POST', payload);
      if (res.success) {
        delete document.getElementById('cfgTeamsList').dataset.dirty;
        await fetchState();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Konfigurasi room disimpan!', showConfirmButton: false, timer: 2000 });
      }
    }

    function renderDossierTab() {
      const c = roomState.case;
      if (!c) return;
      document.getElementById('dossierContent').innerHTML = `
        <div class="border-b border-zinc-800 pb-4 flex flex-wrap items-center justify-between gap-2">
          <div>
            <span class="font-mono-code text-xs uppercase text-red-400 font-bold">TOP SECRET // GAME MASTER DOSSIER</span>
            <h2 class="text-2xl font-extrabold text-white mt-1">${escapeHtml(c.title)}</h2>
          </div>
          <span class="px-3 py-1 rounded border border-zinc-700 font-mono-code text-xs uppercase">${escapeHtml(c.category)} • ${escapeHtml(c.difficulty)}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
          <div class="space-y-4">
            <div>
              <h4 class="font-mono-code text-xs uppercase text-zinc-400 mb-1">Permasalahan (Publik)</h4>
              <p class="text-zinc-200 leading-relaxed">${escapeHtml(c.description)}</p>
            </div>
            <div>
              <h4 class="font-mono-code text-xs uppercase text-amber-400 mb-1">Dugaan Awal Klien (False Lead)</h4>
              <p class="text-zinc-300 leading-relaxed">${escapeHtml(c.initial_assumption)}</p>
            </div>
            <div>
              <h4 class="font-mono-code text-xs uppercase text-emerald-400 mb-1">Root Cause Sebenarnya</h4>
              <p class="p-3 rounded-lg bg-emerald-950/30 border border-emerald-800/60 text-emerald-200 font-bold">${escapeHtml(c.root_cause)}</p>
            </div>
            <div>
              <h4 class="font-mono-code text-xs uppercase text-zinc-400 mb-1">Rekomendasi Solusi & Validasi</h4>
              <ul class="list-disc pl-5 text-zinc-300 space-y-1">
                ${(c.recommended_solutions || []).map((s) => `<li>${escapeHtml(s)}</li>`).join('')}
              </ul>
              <p class="text-xs text-zinc-400 mt-2"><b>Validasi:</b> ${escapeHtml(c.validation_method)}</p>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <h4 class="font-mono-code text-xs uppercase text-emerald-400 mb-1.5">10+ Informasi Rahasia Kasus</h4>
              <ol class="list-decimal pl-5 text-xs text-zinc-200 space-y-1.5">
                ${(c.hidden_facts || []).map((f) => `<li>${escapeHtml(f)}</li>`).join('')}
              </ol>
            </div>
            <div>
              <h4 class="font-mono-code text-xs uppercase text-zinc-400 mb-1">Bukti Kunci</h4>
              <ul class="list-disc pl-5 text-xs text-zinc-300 space-y-1">
                ${(c.evidence || []).map((e) => `<li>${escapeHtml(e)}</li>`).join('')}
              </ul>
            </div>
            <div>
              <h4 class="font-mono-code text-xs uppercase text-red-400 mb-1">Pengalih Perhatian (False Leads)</h4>
              <ul class="list-disc pl-5 text-xs text-zinc-400 space-y-1">
                ${(c.false_leads || []).map((fl) => `<li>${escapeHtml(fl)}</li>`).join('')}
              </ul>
            </div>
          </div>
        </div>
      `;
    }

    function renderCaseBuilder() {
      document.getElementById('caseCountBadge').textContent = casesCache.length;
      const list = document.getElementById('builderCasesList');
      list.innerHTML = casesCache.map((c) => `
        <div class="case-card p-5 flex flex-col justify-between space-y-4">
          <div>
            <div class="flex items-center justify-between gap-2 mb-2">
              <span class="font-mono-code text-[10px] uppercase px-2 py-0.5 rounded border border-zinc-700 text-zinc-300">${escapeHtml(c.difficulty)}</span>
              <span class="text-[11px] text-zinc-500">${escapeHtml(c.category)}</span>
            </div>
            <h4 class="font-extrabold text-base text-white">${escapeHtml(c.title)}</h4>
            <p class="text-xs text-zinc-400 mt-1 line-clamp-3">${escapeHtml(c.description)}</p>
          </div>
          <div class="flex flex-wrap gap-1.5 pt-3 border-t border-zinc-800">
            <button onclick="openCaseForm(${c.id})" class="px-2.5 py-1 rounded border border-zinc-700 text-xs font-bold text-zinc-200 hover:bg-zinc-800 cursor-pointer">Edit</button>
            <button onclick="duplicateCustomCase(${c.id})" class="px-2.5 py-1 rounded border border-zinc-700 text-xs font-bold text-zinc-200 hover:bg-zinc-800 cursor-pointer">Duplikasi</button>
            <button onclick="deleteCustomCase(${c.id})" class="px-2.5 py-1 rounded border border-red-900/70 text-xs font-bold text-red-400 hover:bg-red-950/40 cursor-pointer">Hapus</button>
          </div>
        </div>
      `).join('');
    }

    // Actions
    async function setPhase(phase) {
      await hostApi("{{ route('games.problem-detective.phase') }}", 'POST', { phase });
      await fetchState();
    }

    async function controlTimer(action, seconds = 60) {
      await hostApi("{{ route('games.problem-detective.timer') }}", 'POST', { action, seconds });
      await fetchState();
    }

    async function adjustLives(teamId, delta) {
      await hostApi("{{ route('games.problem-detective.lives') }}", 'POST', { team_id: teamId, delta });
      await fetchState();
    }

    async function setDefenseTeam(teamId) {
      await hostApi("{{ route('games.problem-detective.defense-team') }}", 'POST', { team_id: teamId });
      await fetchState();
    }

    function fillQuickFact(qId, text) {
      if (!text) return;
      const inp = document.getElementById('ansInput_' + qId);
      if (inp) {
        inp.value = inp.value ? inp.value + ' ' + text : text;
        inp.focus();
      }
    }

    async function sendAnswer(qId, status) {
      const inp = document.getElementById('ansInput_' + qId);
      const answer_text = inp ? inp.value.trim() : '';
      if (status === 'answered' && !answer_text) {
        Swal.fire('Jawaban Kosong', 'Ketik jawaban atau pilih fakta rahasia terlebih dahulu.', 'warning');
        return;
      }
      const res = await hostApi("{{ route('games.problem-detective.questions.answer') }}", 'POST', {
        question_id: qId,
        status,
        answer_text,
      });
      if (res.success) {
        await fetchState();
      }
    }

    async function sendBreakingNews() {
      const inp = document.getElementById('breakingNewsInput');
      const msg = inp.value.trim();
      if (!msg) return;
      const res = await hostApi("{{ route('games.problem-detective.breaking-news') }}", 'POST', { message: msg });
      if (res.success) {
        inp.value = '';
        await fetchState();
        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Breaking News terkirim!', showConfirmButton: false, timer: 2000 });
      }
    }

    async function sendPresetBreakingNews(msg) {
      await hostApi("{{ route('games.problem-detective.breaking-news') }}", 'POST', { message: msg });
      await fetchState();
      Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Breaking News terkirim!', showConfirmButton: false, timer: 2000 });
    }

    async function kickPlayer(playerId) {
      const conf = await Swal.fire({
        title: 'Keluarkan Pemain?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kick',
        cancelButtonText: 'Batal',
      });
      if (!conf.isConfirmed) return;
      await hostApi("{{ route('games.problem-detective.kick') }}", 'POST', { player_id: playerId });
      await fetchState();
    }

    // Score Modal
    function openScoreModal(teamId) {
      const team = roomState.teams.find((t) => t.id === teamId);
      if (!team) return;
      document.getElementById('scoreTeamId').value = team.id;
      document.getElementById('scoreModalTeamName').textContent = `${team.name} (Sisa Nyawa: ${team.lives}/${roomState.default_lives})`;

      const r = team.final_report;
      const reportBox = document.getElementById('scoreModalReportBox');
      if (!r) {
        reportBox.innerHTML = `<div class="text-zinc-500 italic">Kelompok ini belum mengumpulkan Laporan Akhir (Final Deduction).</div>`;
      } else {
        reportBox.innerHTML = `
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div><span class="text-zinc-500 uppercase font-bold">Initial Hypothesis:</span><p class="text-zinc-200 mt-0.5">${escapeHtml(r.initial_hypothesis)}</p></div>
            <div><span class="text-zinc-500 uppercase font-bold">Confidence Level:</span><p class="text-emerald-400 font-mono-code font-bold text-base mt-0.5">${r.confidence_level}% (oleh ${escapeHtml(r.submitted_by)})</p></div>
            <div class="sm:col-span-2"><span class="text-zinc-500 uppercase font-bold">Evidence Collected:</span><p class="text-zinc-200 mt-0.5">${escapeHtml(r.evidence_collected)}</p></div>
            <div class="sm:col-span-2"><span class="text-zinc-500 uppercase font-bold">Eliminated Possibilities:</span><p class="text-zinc-200 mt-0.5">${escapeHtml(r.eliminated_possibilities)}</p></div>
            <div class="sm:col-span-2 p-2.5 rounded bg-zinc-900 border border-zinc-700"><span class="text-emerald-400 uppercase font-bold">Root Cause:</span><p class="text-white font-bold mt-0.5">${escapeHtml(r.root_cause)}</p></div>
            <div><span class="text-zinc-500 uppercase font-bold">Proposed Solution:</span><p class="text-zinc-200 mt-0.5">${escapeHtml(r.proposed_solution)}</p></div>
            <div><span class="text-zinc-500 uppercase font-bold">Validation Plan:</span><p class="text-zinc-200 mt-0.5">${escapeHtml(r.validation_plan)}</p></div>
          </div>
        `;
      }

      const s = team.scores || {};
      const suggestedEfficiency = Math.min(10, Math.round((team.lives / Math.max(1, roomState.default_lives)) * 10));
      document.getElementById('scQuestionQuality').value = s.question_quality ?? 15;
      document.getElementById('scRootCause').value = s.root_cause_accuracy ?? 25;
      document.getElementById('scEvidence').value = s.evidence_strength ?? 15;
      document.getElementById('scSolution').value = s.solution_effectiveness ?? 15;
      document.getElementById('scEfficiency').value = s.efficiency ?? suggestedEfficiency;
      document.getElementById('scFeedback').value = s.feedback ?? '';

      document.querySelectorAll('#scoreForm input[name="awards[]"]').forEach((cb) => {
        cb.checked = (team.awards || []).includes(cb.value);
      });
      recalcTotalScore();

      const modal = document.getElementById('scoreModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeScoreModal() {
      const modal = document.getElementById('scoreModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    function recalcTotalScore() {
      const sum = Number(document.getElementById('scQuestionQuality').value || 0)
        + Number(document.getElementById('scRootCause').value || 0)
        + Number(document.getElementById('scEvidence').value || 0)
        + Number(document.getElementById('scSolution').value || 0)
        + Number(document.getElementById('scEfficiency').value || 0);
      document.getElementById('scTotalDisplay').textContent = Math.min(100, sum);
    }

    async function submitTeamScore(e) {
      e.preventDefault();
      const awards = Array.from(document.querySelectorAll('#scoreForm input[name="awards[]"]:checked')).map((el) => el.value);
      const res = await hostApi("{{ route('games.problem-detective.score') }}", 'POST', {
        team_id: Number(document.getElementById('scoreTeamId').value),
        question_quality: Number(document.getElementById('scQuestionQuality').value),
        root_cause_accuracy: Number(document.getElementById('scRootCause').value),
        evidence_strength: Number(document.getElementById('scEvidence').value),
        solution_effectiveness: Number(document.getElementById('scSolution').value),
        efficiency: Number(document.getElementById('scEfficiency').value),
        awards,
        feedback: document.getElementById('scFeedback').value,
      });
      if (res.success) {
        closeScoreModal();
        await fetchState();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Nilai kelompok disimpan!', showConfirmButton: false, timer: 2000 });
      }
    }

    // Custom Case Builder
    function openCaseForm(caseId = null) {
      const existing = caseId ? casesCache.find((c) => c.id === caseId) : null;
      document.getElementById('caseBuilderModalTitle').textContent = existing ? 'Edit Kasus' : 'Buat Kasus Baru';
      document.getElementById('cbId').value = existing ? existing.id : '';
      document.getElementById('cbTitle').value = existing ? existing.title : '';
      document.getElementById('cbCategory').value = existing ? existing.category : 'Web & System';
      document.getElementById('cbDifficulty').value = existing ? existing.difficulty : 'medium';
      document.getElementById('cbDuration').value = existing ? existing.default_duration : 20;
      document.getElementById('cbDescription').value = existing ? existing.description : '';
      document.getElementById('cbAssumption').value = existing ? existing.initial_assumption : '';
      document.getElementById('cbObjectives').value = existing ? (existing.mission_objectives || []).join('\n') : '';
      document.getElementById('cbFacts').value = existing ? (existing.hidden_facts || []).join('\n') : '';
      document.getElementById('cbEvidence').value = existing ? (existing.evidence || []).join('\n') : '';
      document.getElementById('cbFalseLeads').value = existing ? (existing.false_leads || []).join('\n') : '';
      document.getElementById('cbHints').value = existing ? (existing.special_hints || []).join('\n') : '';
      document.getElementById('cbBreaking').value = existing ? (existing.breaking_news || []).join('\n') : '';
      document.getElementById('cbRootCause').value = existing ? existing.root_cause : '';
      document.getElementById('cbSolutions').value = existing ? (existing.recommended_solutions || []).join('\n') : '';
      document.getElementById('cbValidation').value = existing ? existing.validation_method : '';

      const modal = document.getElementById('caseBuilderModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeCaseForm() {
      const modal = document.getElementById('caseBuilderModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    const linesToArray = (id) => document.getElementById(id).value.split('\n').map((s) => s.trim()).filter(Boolean);

    async function saveCustomCase(e) {
      e.preventDefault();
      const id = document.getElementById('cbId').value;
      const payload = {
        title: document.getElementById('cbTitle').value.trim(),
        category: document.getElementById('cbCategory').value.trim(),
        difficulty: document.getElementById('cbDifficulty').value,
        default_duration: Number(document.getElementById('cbDuration').value) || 20,
        description: document.getElementById('cbDescription').value.trim(),
        initial_assumption: document.getElementById('cbAssumption').value.trim(),
        mission_objectives: linesToArray('cbObjectives'),
        hidden_facts: linesToArray('cbFacts'),
        evidence: linesToArray('cbEvidence'),
        false_leads: linesToArray('cbFalseLeads'),
        special_hints: linesToArray('cbHints'),
        breaking_news: linesToArray('cbBreaking'),
        root_cause: document.getElementById('cbRootCause').value.trim(),
        recommended_solutions: linesToArray('cbSolutions'),
        validation_method: document.getElementById('cbValidation').value.trim(),
      };

      const url = id
        ? `{{ url('/games/problem-detective/cases') }}/${id}`
        : `{{ route('games.problem-detective.cases.store') }}`;
      const method = id ? 'PUT' : 'POST';

      const res = await hostApi(url, method, payload);
      if (res.success) {
        closeCaseForm();
        await refreshCasesList();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Kasus disimpan!', showConfirmButton: false, timer: 2000 });
      } else {
        Swal.fire('Gagal', res.message || 'Periksa kembali semua kolom.', 'error');
      }
    }

    async function duplicateCustomCase(id) {
      const res = await hostApi(`{{ url('/games/problem-detective/cases') }}/${id}/duplicate`, 'POST', {});
      if (res.success) {
        await refreshCasesList();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Kasus diduplikasi!', showConfirmButton: false, timer: 1800 });
      }
    }

    async function deleteCustomCase(id) {
      const conf = await Swal.fire({
        title: 'Hapus Kasus Ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
      });
      if (!conf.isConfirmed) return;
      const res = await hostApi(`{{ url('/games/problem-detective/cases') }}/${id}`, 'DELETE', {});
      if (res.success) {
        await refreshCasesList();
      } else {
        Swal.fire('Tidak Bisa Hapus', res.message || 'Gagal menghapus kasus.', 'error');
      }
    }

    async function refreshCasesList() {
      const res = await fetch(`{{ route('games.problem-detective.cases.index') }}?code=${ROOM_CODE}`, {
        headers: { 'Accept': 'application/json', 'X-Host-Token': HOST_TOKEN }
      });
      const data = await res.json();
      if (data.success) {
        casesCache = data.cases;
        const sel = document.getElementById('cfgCaseId');
        const cur = sel.value;
        sel.innerHTML = casesCache.map((c) => `<option value="${c.id}" ${String(c.id) === String(cur) ? 'selected' : ''}>${escapeHtml(c.title)} (${c.difficulty.toUpperCase()})</option>`).join('');
        renderCaseBuilder();
      }
    }

    async function confirmResetRoom() {
      const conf = await Swal.fire({
        title: 'Reset Room ke Lobby?',
        text: 'Pertanyaan, jawaban, laporan, dan skor akan dikosongkan. Pemain tetap terhubung.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal',
      });
      if (!conf.isConfirmed) return;
      await hostApi("{{ route('games.problem-detective.reset') }}", 'POST', {});
      await fetchState();
    }

    async function createBrandNewRoom() {
      const res = await fetch("{{ route('games.problem-detective.rooms.create') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success) {
        localStorage.setItem('detective_host_token_' + data.code, data.host_token);
        window.location.href = data.host_url;
      }
    }

    function copyJoinLink() {
      navigator.clipboard.writeText(JOIN_URL).then(() => {
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Link join disalin!', showConfirmButton: false, timer: 1800 });
      });
    }

    // Init QR, local 1s timer tick, Echo, & polling fallback
    window.addEventListener('DOMContentLoaded', () => {
      document.getElementById('joinUrlDisplay').textContent = JOIN_URL;
      new QRCode(document.getElementById('hostQrCode'), {
        text: JOIN_URL,
        width: 190,
        height: 190,
        colorDark: '#09090B',
        colorLight: '#FFFFFF',
      });

      fetchState();

      setInterval(() => {
        if (roomState && !roomState.is_paused && remainingSeconds > 0) {
          remainingSeconds--;
          const timerEl = document.getElementById('hostTimerDisplay');
          timerEl.textContent = formatTimer(remainingSeconds);
          timerEl.classList.toggle('text-red-500', remainingSeconds <= 15);
        }
      }, 1000);

      setInterval(fetchState, 2200);

      // Optional Reverb Echo subscription
      try {
        const key = @json(env('VITE_REVERB_APP_KEY', env('REVERB_APP_KEY')));
        if (key && window.Echo && window.Pusher) {
          window.Pusher = Pusher;
          const echo = new Echo({
            broadcaster: 'reverb',
            key,
            wsHost: @json(env('VITE_REVERB_HOST', request()->getHost())),
            wsPort: @json((int) env('VITE_REVERB_PORT', 8080)),
            wssPort: @json((int) env('VITE_REVERB_PORT', 443)),
            forceTLS: window.location.protocol === 'https:',
            enabledTransports: ['ws', 'wss'],
          });
          echo.channel('detective-room.' + ROOM_CODE).listen('.room.updated', () => fetchState());
        }
      } catch (e) {}
    });
  </script>
</body>
</html>
