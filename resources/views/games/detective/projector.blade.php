<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Projector Display — The Problem Detective</title>

  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
      border-radius: 14px;
    }
  </style>
</head>
<body class="min-h-screen antialiased flex flex-col justify-between">

  <!-- Room Code Gate if opened without ?code -->
  <div id="projCodePrompt" class="hidden min-h-screen flex items-center justify-center px-4">
    <div class="case-card p-8 max-w-md w-full space-y-4 text-center">
      <h1 class="text-2xl font-extrabold uppercase">Projector Mode</h1>
      <p class="text-xs text-zinc-400">Masukkan 6 karakter Room Code untuk menampilkan layar kelas.</p>
      <input id="projInputCode" type="text" maxlength="6" placeholder="XXXXXX" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-3 text-center font-mono-code text-lg uppercase tracking-widest text-white">
      <button onclick="openProjCode()" class="w-full py-3 rounded-lg bg-white text-black font-extrabold text-sm cursor-pointer">Tampilkan Proyektor →</button>
    </div>
  </div>

  <!-- Main 16:9 Classroom Projector Display -->
  <div id="projDisplay" class="hidden min-h-screen flex flex-col">
    <!-- Top Header -->
    <header class="border-b border-zinc-800 bg-zinc-950 px-6 sm:px-10 py-4 flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <span class="px-3 py-1.5 rounded bg-white text-black font-extrabold text-sm tracking-tight uppercase">
          THE PROBLEM DETECTIVE
        </span>
        <span id="projRoomCodeBadge" class="font-mono-code text-lg font-extrabold tracking-widest text-zinc-300">-</span>
        <span id="projPhaseBadge" class="px-3 py-1 rounded border border-zinc-700 bg-zinc-900 font-mono-code text-sm font-bold uppercase text-emerald-400">
          LOBBY
        </span>
      </div>

      <div class="flex items-center gap-6">
        <div id="projPlayerCount" class="text-sm font-mono-code text-zinc-400">0 Players</div>
        <div id="projTimerDisplay" class="font-mono-code text-4xl sm:text-6xl font-extrabold tabular-nums text-white leading-none">
          00:00
        </div>
      </div>
    </header>

    <!-- Breaking News Banner -->
    <div id="projBreakingBanner" class="hidden bg-red-950 border-b border-red-600 px-6 sm:px-10 py-3">
      <div class="max-w-[1600px] mx-auto flex items-center gap-4">
        <span class="px-3 py-1 rounded bg-red-600 text-white font-mono-code text-xs font-extrabold uppercase shrink-0">BREAKING NEWS</span>
        <p id="projBreakingText" class="text-base sm:text-xl font-extrabold text-white">-</p>
      </div>
    </div>

    <!-- Stage Content -->
    <main id="projStage" class="flex-1 max-w-[1600px] w-full mx-auto px-6 sm:px-10 py-8 space-y-6">
      <!-- Dynamic Phase Layout -->
    </main>
  </div>

  <script>
    let projCode = @json($initialCode);
    let roomState = null;
    let remainingSeconds = 0;

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

    function openProjCode() {
      const code = document.getElementById('projInputCode').value.trim().toUpperCase();
      if (code.length !== 6) return;
      window.location.href = "{{ route('games.problem-detective.projector') }}?code=" + encodeURIComponent(code);
    }

    async function fetchProjState() {
      if (!projCode || projCode.length !== 6) return;
      try {
        const res = await fetch(`{{ route('games.problem-detective.state') }}?code=${projCode}&role=projector`, {
          headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success && data.room) {
          roomState = data.room;
          remainingSeconds = data.remaining_seconds ?? 0;
          renderProjector();
        }
      } catch (e) {}
    }

    function renderProjector() {
      if (!roomState) return;
      document.getElementById('projRoomCodeBadge').textContent = 'CODE: ' + roomState.code;
      document.getElementById('projPhaseBadge').textContent = roomState.phase.toUpperCase();
      document.getElementById('projPlayerCount').textContent = `${roomState.total_players}/${roomState.target_players} Detectives`;

      const tEl = document.getElementById('projTimerDisplay');
      tEl.textContent = formatTimer(remainingSeconds);
      tEl.classList.toggle('text-red-500', remainingSeconds > 0 && remainingSeconds <= 15);

      const bn = roomState.breaking_news_feed || [];
      const bnEl = document.getElementById('projBreakingBanner');
      if (bn.length > 0) {
        bnEl.classList.remove('hidden');
        document.getElementById('projBreakingText').textContent = bn[0].message;
      } else {
        bnEl.classList.add('hidden');
      }

      const stage = document.getElementById('projStage');
      const phase = roomState.phase;
      const c = roomState.case;

      if (phase === 'lobby') {
        const joinUrl = "{{ route('games.problem-detective.play') }}?code=" + roomState.code;
        stage.innerHTML = `
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-4 case-card p-8 flex flex-col items-center text-center space-y-4">
              <span class="font-mono-code text-xs uppercase text-zinc-400">SCAN QR TO JOIN INVESTIGATION</span>
              <div class="bg-white p-4 rounded-xl"><div id="projQrTarget"></div></div>
              <div class="font-mono-code text-4xl font-extrabold tracking-widest text-white">${roomState.code}</div>
              <div class="text-xs text-zinc-400 font-mono-code break-all">${escapeHtml(joinUrl)}</div>
            </div>
            <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              ${roomState.teams.map((t) => `
                <div class="case-card p-5 space-y-3">
                  <div class="flex items-center justify-between border-b border-zinc-800 pb-2">
                    <span class="text-lg font-extrabold text-white">${escapeHtml(t.name)}</span>
                    <span class="font-mono-code text-sm text-zinc-400">${t.players.length}/${t.max_capacity}</span>
                  </div>
                  <div class="space-y-1 text-sm text-zinc-300">
                    ${t.players.length === 0 ? '<span class="text-zinc-600 italic text-xs">Menunggu anggota...</span>' : t.players.map((p) => `
                      <div>${p.is_ready ? '<span class="text-emerald-400 mr-1.5">●</span>' : '<span class="text-zinc-600 mr-1.5">○</span>'}${escapeHtml(p.name)}</div>
                    `).join('')}
                  </div>
                </div>
              `).join('')}
            </div>
          </div>
        `;
        const qrTarget = document.getElementById('projQrTarget');
        if (qrTarget && !qrTarget.hasChildNodes()) {
          new QRCode(qrTarget, { text: joinUrl, width: 210, height: 210, colorDark: '#09090B', colorLight: '#FFFFFF' });
        }
        return;
      }

      if (phase === 'reveal') {
        stage.innerHTML = `
          <div class="case-card p-8 space-y-6 border-emerald-600">
            <span class="font-mono-code text-sm uppercase text-emerald-400 font-bold">PHASE 06 // TRUTH REVEAL</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">${escapeHtml(c?.title)}</h1>
            <div class="p-6 rounded-xl bg-emerald-950/50 border border-emerald-600">
              <div class="font-mono-code text-xs uppercase text-emerald-400 font-bold">ROOT CAUSE SEBENARNYA</div>
              <p class="text-2xl sm:text-3xl font-extrabold text-white mt-2">${escapeHtml(c?.root_cause)}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="p-5 rounded-xl bg-zinc-950 border border-zinc-800 space-y-2">
                <h3 class="font-mono-code text-xs uppercase text-zinc-400">Fakta & Hubungan Bukti</h3>
                <ul class="list-disc pl-5 text-sm text-zinc-200 space-y-1.5">
                  ${(c?.hidden_facts || []).map((f) => `<li>${escapeHtml(f)}</li>`).join('')}
                </ul>
              </div>
              <div class="p-5 rounded-xl bg-zinc-950 border border-zinc-800 space-y-4">
                <div>
                  <h3 class="font-mono-code text-xs uppercase text-red-400">Kesalahan Asumsi Awal</h3>
                  <p class="text-sm text-zinc-300 mt-1">${escapeHtml(c?.initial_assumption)}</p>
                </div>
                <div>
                  <h3 class="font-mono-code text-xs uppercase text-emerald-400">Rekomendasi Solusi & Validasi</h3>
                  <ul class="list-disc pl-5 text-sm text-zinc-200 space-y-1 mt-1">
                    ${(c?.recommended_solutions || []).map((s) => `<li>${escapeHtml(s)}</li>`).join('')}
                  </ul>
                  <p class="text-xs text-zinc-400 mt-2"><b>Validasi:</b> ${escapeHtml(c?.validation_method)}</p>
                </div>
              </div>
            </div>
          </div>
        `;
        return;
      }

      if (phase === 'leaderboard') {
        const sorted = roomState.teams.slice().sort((a, b) => (b.total_score || 0) - (a.total_score || 0));
        stage.innerHTML = `
          <div class="case-card p-8 space-y-6">
            <div class="flex items-center justify-between">
              <div>
                <span class="font-mono-code text-sm uppercase text-emerald-400 font-bold">PHASE 07 // FINAL LEADERBOARD</span>
                <h1 class="text-3xl font-extrabold text-white mt-1">Klasemen Akhir Investigasi</h1>
              </div>
              <span class="font-mono-code text-sm text-zinc-400">${escapeHtml(c?.title)}</span>
            </div>
            <div class="space-y-3">
              ${sorted.map((t, i) => `
                <div class="p-5 rounded-xl bg-zinc-950 border ${i === 0 ? 'border-emerald-500' : 'border-zinc-800'} flex flex-wrap items-center justify-between gap-4">
                  <div class="flex items-center gap-4">
                    <span class="font-mono-code text-3xl font-extrabold ${i === 0 ? 'text-emerald-400' : 'text-zinc-500'}">#${i + 1}</span>
                    <div>
                      <div class="text-2xl font-extrabold text-white">${escapeHtml(t.name)}</div>
                      <div class="flex flex-wrap gap-2 mt-1">
                        ${(t.awards || []).map((a) => `<span class="px-2.5 py-0.5 rounded bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold">★ ${escapeHtml(a)}</span>`).join('')}
                      </div>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="font-mono-code text-4xl font-extrabold text-emerald-400">${t.total_score} <span class="text-base text-zinc-500">/100</span></div>
                    <div class="font-mono-code text-xs text-zinc-400 mt-1">${t.questions_asked_count} Pertanyaan • Sisa ${t.lives} ♥</div>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>
        `;
        return;
      }

      // Active Phases (Briefing, Discussion, Investigation, Deduction, Defense)
      const defTeam = phase === 'defense' ? roomState.teams.find((t) => t.id === roomState.defense_team_id) : null;

      stage.innerHTML = `
        <div class="case-card p-6 sm:p-8 space-y-3">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <span class="font-mono-code text-xs uppercase text-zinc-400">${escapeHtml(c?.category)} • ${escapeHtml(c?.difficulty)}</span>
            <span class="font-mono-code text-xs uppercase text-zinc-400">MODE: ${roomState.investigation_mode.toUpperCase()}</span>
          </div>
          <h1 class="text-3xl sm:text-4xl font-extrabold text-white">${escapeHtml(c?.title)}</h1>
          <p class="text-base sm:text-lg text-zinc-300 leading-relaxed">${escapeHtml(c?.description)}</p>
        </div>

        ${defTeam ? `
          <div class="case-card p-6 border-emerald-500 space-y-3">
            <div class="font-mono-code text-xs uppercase text-emerald-400 font-bold">PRESENTASI DEFENSE: ${escapeHtml(defTeam.name)}</div>
            ${defTeam.final_report ? `
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><span class="text-xs text-zinc-500 uppercase font-bold">Root Cause:</span><p class="text-lg font-extrabold text-white mt-0.5">${escapeHtml(defTeam.final_report.root_cause)}</p></div>
                <div><span class="text-xs text-zinc-500 uppercase font-bold">Confidence:</span><p class="font-mono-code text-2xl font-extrabold text-emerald-400 mt-0.5">${defTeam.final_report.confidence_level}%</p></div>
                <div><span class="text-xs text-zinc-500 uppercase font-bold">Evidence Collected:</span><p class="text-zinc-300 mt-0.5">${escapeHtml(defTeam.final_report.evidence_collected)}</p></div>
                <div><span class="text-xs text-zinc-500 uppercase font-bold">Proposed Solution:</span><p class="text-zinc-300 mt-0.5">${escapeHtml(defTeam.final_report.proposed_solution)}</p></div>
              </div>
            ` : `<p class="text-sm text-zinc-400">Kelompok ini belum mengumpulkan laporan akhir.</p>`}
          </div>
        ` : ''}

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          ${roomState.teams.map((t) => `
            <div class="case-card p-5 space-y-3 ${defTeam && defTeam.id === t.id ? 'border-emerald-500' : ''}">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-extrabold text-white">${escapeHtml(t.name)}</h3>
                <span class="px-2.5 py-1 rounded font-mono-code text-sm font-extrabold ${t.lives <= 2 ? 'bg-red-600 text-white' : 'bg-zinc-900 text-red-400 border border-zinc-800'}">
                  ♥ ${t.lives}
                </span>
              </div>
              <div class="text-xs font-mono-code text-zinc-400 space-y-1 border-t border-zinc-800 pt-2">
                <div class="flex justify-between"><span>Pertanyaan</span><span class="text-white font-bold">${t.questions_asked_count}</span></div>
                <div class="flex justify-between"><span>Antrean GM</span><span class="${t.pending_questions_count > 0 ? 'text-amber-400 font-bold' : ''}">${t.pending_questions_count}</span></div>
                <div class="flex justify-between"><span>Status</span><span class="${t.submitted ? 'text-emerald-400 font-bold' : 'text-zinc-400'}">${t.submitted ? '✓ Submitted' : 'Active'}</span></div>
              </div>
            </div>
          `).join('')}
        </div>

        ${(roomState.public_questions || []).length > 0 ? `
          <div class="case-card p-6 space-y-3">
            <h3 class="font-mono-code text-xs uppercase text-zinc-400">PUBLIC INVESTIGATION LOG</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              ${roomState.public_questions.slice(-6).reverse().map((q) => `
                <div class="p-3 rounded-lg bg-zinc-950 border border-zinc-800 text-xs space-y-1">
                  <div class="font-bold text-white">${escapeHtml(q.team_name)}: “${escapeHtml(q.question_text)}”</div>
                  <div class="text-emerald-400">→ ${escapeHtml(q.answer_text)}</div>
                </div>
              `).join('')}
            </div>
          </div>
        ` : ''}
      `;
    }

    window.addEventListener('DOMContentLoaded', () => {
      if (!projCode || projCode.length !== 6) {
        document.getElementById('projCodePrompt').classList.remove('hidden');
      } else {
        document.getElementById('projDisplay').classList.remove('hidden');
        fetchProjState();
      }

      setInterval(() => {
        if (roomState && !roomState.is_paused && remainingSeconds > 0) {
          remainingSeconds--;
          const tEl = document.getElementById('projTimerDisplay');
          if (tEl) {
            tEl.textContent = formatTimer(remainingSeconds);
            tEl.classList.toggle('text-red-500', remainingSeconds <= 15);
          }
        }
      }, 1000);

      setInterval(fetchProjState, 2200);
    });
  </script>
</body>
</html>
