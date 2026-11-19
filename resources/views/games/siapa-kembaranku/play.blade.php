<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Main: SIAPA KEMBARANKU? — MainYuk!</title>

  <!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- QR Code & Scanner -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script src="https://unpkg.com/html5-qrcode"></script>

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
      background-size: 20px 20px;
      color: #111111;
      touch-action: manipulation;
    }

    .font-comic {
      font-family: 'Luckiest Guy', cursive;
      letter-spacing: 0.03em;
    }

    .font-doodle {
      font-family: 'Patrick Hand', cursive;
    }

    .sticker-card {
      border: 3px solid #111111;
      box-shadow: 4px 4px 0px #111111;
    }

    .sticker-btn {
      border: 2.5px solid #111111;
      box-shadow: 3px 3px 0px #111111;
      transition: all 0.15s ease;
      cursor: pointer;
    }

    .sticker-btn:active:not(:disabled) {
      transform: translate(2px, 2px);
      box-shadow: 0px 0px 0px #111111;
    }

    .sticker-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      box-shadow: 1px 1px 0px #111111;
    }

    .selected-card {
      background-color: #FFD21E !important;
      border-color: #111111 !important;
      box-shadow: 4px 4px 0px #0B44D6 !important;
      transform: translateY(-2px);
    }
  </style>
</head>
<body class="min-h-screen flex flex-col font-sans selection:bg-sunny selection:text-ink pb-12">

  <!-- TOP HEADER -->
  <header class="bg-white border-b-3 border-ink sticky top-0 z-40 px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <span class="text-xl">🎭</span>
      <div>
        <h1 class="font-comic text-xl text-ink leading-tight">SIAPA KEMBARANKU?</h1>
        <p id="headerSubtext" class="text-[10px] font-bold text-slate-500">GAME SOSIAL MULTI-DEVICE</p>
      </div>
    </div>

    <!-- Active Player / Room Badge -->
    <div id="playerStatusBadge" class="hidden flex items-center gap-2">
      <div class="bg-sunny border-2 border-ink px-2.5 py-0.5 rounded-full font-comic text-xs">
        <span id="playerBadgeName">Kamu</span>
      </div>
    </div>
  </header>

  <!-- MAIN VIEW CONTAINER -->
  <main class="flex-1 max-w-md w-full mx-auto px-4 py-5 space-y-4">

    <!-- 1. JOIN VIEW -->
    <div id="viewJoin" class="space-y-5">
      <div class="sticker-card bg-sunny rounded-3xl p-6 text-center">
        <span class="text-5xl block mb-2">🔍 👥 🎭</span>
        <h2 class="font-comic text-3xl text-ink leading-tight">GABUNG KE PERMAINAN</h2>
        <p class="font-semibold text-xs text-ink/80 mt-1">Masukkan nama dan kode room yang ada di layar laptop host!</p>
      </div>

      <div class="sticker-card bg-white rounded-3xl p-6 space-y-4">
        <div>
          <label class="block font-comic text-xs text-ink uppercase mb-1">Nama Panggilan Kamu:</label>
          <input 
            type="text" 
            id="inputUsername" 
            placeholder="Contoh: Dimas, Nadia, Siti..." 
            maxlength="20"
            class="w-full bg-paper border-2.5 border-ink rounded-xl px-4 py-3 font-bold text-sm text-ink placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cobalt"
          >
        </div>

        <div>
          <label class="block font-comic text-xs text-ink uppercase mb-1">Kode Room (6 Huruf):</label>
          <input 
            type="text" 
            id="inputRoomCode" 
            value="{{ $initialCode }}" 
            placeholder="Misal: KMBR88" 
            maxlength="6"
            class="w-full uppercase tracking-widest bg-paper border-2.5 border-ink rounded-xl px-4 py-3 font-comic text-lg text-cobalt placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cobalt"
          >
        </div>

        <button id="btnJoin" onclick="joinGame()" class="sticker-btn w-full bg-cobalt text-white font-comic text-xl py-3.5 rounded-xl flex items-center justify-center gap-2">
          <span>MASUK LOBBY</span>
          <span>➔</span>
        </button>

        <p id="joinErrorMsg" class="hidden text-xs font-bold text-vermilion bg-red-50 border border-vermilion p-2.5 rounded-xl text-center"></p>
      </div>
    </div>

    <!-- 2. LOBBY WAITING VIEW -->
    <div id="viewLobby" class="hidden space-y-4 text-center">
      <div class="sticker-card bg-white rounded-3xl p-6 space-y-4">
        <span class="text-5xl block animate-bounce" style="animation-duration: 2s;">🎪</span>
        <div>
          <span class="bg-sunny border-2 border-ink text-ink font-comic text-xs px-3 py-1 rounded-full">LOBBY BERHASIL!</span>
          <h2 class="font-comic text-2xl text-ink mt-2">HALO, <span id="lobbyPlayerName" class="text-cobalt">PEMAIN</span>!</h2>
          <p class="font-bold text-xs text-slate-500 mt-1">Kamu sudah terdaftar di room <strong id="lobbyGameCode" class="text-ink"></strong></p>
        </div>

        <!-- Waiting Animation -->
        <div class="py-4 bg-paper rounded-2xl border-2 border-dashed border-ink/40">
          <p class="font-comic text-base text-ink animate-pulse">⏳ MENUNGGU HOST MEMULAI GAME...</p>
          <p class="text-xs font-semibold text-slate-500 mt-1">Simak layar proyektor di depan kelas ya!</p>
        </div>

        <!-- Security Rule Reminder -->
        <div class="bg-blue-50 border-2 border-ink rounded-2xl p-4 text-left">
          <h4 class="font-comic text-xs text-cobalt mb-1 flex items-center gap-1">
            <span>🛡️</span> ATURAN RAHASIA PENTING:
          </h4>
          <p class="text-xs font-semibold text-slate-700 leading-relaxed">
            Saat game dimulai, kamu <strong>TIDAK BISA</strong> melihat logomu sendiri di layar ini. Temanmu yang harus scan QR di HP-mu untuk melihat logomu!
          </p>
        </div>
      </div>
    </div>

    <!-- 3. DISCUSSION & SCAN VIEW -->
    <div id="viewDiscussion" class="hidden space-y-4">
      
      <!-- Top Reminder Card -->
      <div class="sticker-card bg-sunny rounded-2xl p-3.5 flex items-center justify-between">
        <div>
          <span class="text-[10px] font-extrabold uppercase bg-ink text-white px-2 py-0.5 rounded-full">RONDE <span id="discRoundNum">1</span></span>
          <p class="font-comic text-lg text-ink leading-tight mt-1">CARI KEMBARANMU! 🔍</p>
        </div>
        <div class="text-right">
          <span class="text-[10px] font-bold text-slate-700 uppercase">SISA WAKTU</span>
          <div id="discTimer" class="font-comic text-2xl text-ink leading-tight">05:00</div>
        </div>
      </div>

      <!-- PERSONAL QR CODE CARD (SHOWN TO OTHERS) -->
      <div class="sticker-card bg-white rounded-3xl p-5 text-center relative overflow-hidden">
        <div class="bg-vermilion text-white font-comic text-xs px-4 py-1 rounded-full border-2 border-ink inline-block mb-3">
          📱 QR PRIBADI KAMU
        </div>

        <p class="text-xs font-semibold text-slate-600 mb-3">
          Tunjukkan QR ini ke temanmu agar mereka bisa scan logomu!
        </p>

        <!-- QR Canvas Frame -->
        <div class="inline-block p-3.5 bg-white border-3 border-ink rounded-2xl shadow-[4px_4px_0px_#111111]">
          <div id="personalQrContainer" class="flex items-center justify-center"></div>
        </div>

        <!-- Strict Security Notice -->
        <div class="mt-3 bg-paper border border-ink/40 rounded-xl p-2.5 text-[11px] font-bold text-slate-600">
          🔒 Layar ini <strong>TIDAK menampilkan logomu</strong>. Hanya teman yang scan QR-mu yang tahu!
        </div>
      </div>

      <!-- SCANNER BUTTON -->
      <button onclick="openScanner()" class="sticker-btn w-full bg-cobalt text-white font-comic text-xl py-4 rounded-2xl flex items-center justify-center gap-2.5">
        <span class="text-2xl">📷</span>
        <span>SCAN QR TEMAN LAIN</span>
      </button>

      <!-- LIST OF SCANNED PLAYERS (ORANG YANG SUDAH KULIHAT) -->
      <div class="sticker-card bg-white rounded-3xl p-4">
        <div class="flex items-center justify-between border-b-2 border-ink pb-2.5 mb-3">
          <h3 class="font-comic text-base text-ink flex items-center gap-1.5">
            <span>👀</span> DAFTAR ORANG YANG SUDAH KULIHAT
          </h3>
          <span id="scannedCountBadge" class="bg-sunny border border-ink text-ink font-black text-xs px-2 py-0.5 rounded-full">0 ORANG</span>
        </div>

        <div id="scannedListContainer" class="space-y-2">
          <p id="emptyScannedNotice" class="text-xs font-semibold text-slate-400 italic text-center py-4">
            Kamu belum scan teman siapa pun. Tekan tombol kamera di atas!
          </p>
        </div>
      </div>

    </div>

    <!-- 4. VOTING VIEW -->
    <div id="viewVoting" class="hidden space-y-4">
      <div class="sticker-card bg-sunny rounded-3xl p-5 text-center">
        <span class="text-3xl block mb-1">🗳️</span>
        <h2 class="font-comic text-2xl text-ink leading-tight">TEBAK KEMBARANMU!</h2>
        <p class="font-semibold text-xs text-ink/90 mt-1">
          Pilih <strong id="requiredVoteCountText">3 orang</strong> yang menurutmu punya logo SAMA denganmu!
        </p>
        <div class="mt-2 inline-block bg-white border-2 border-ink px-3 py-1 rounded-full font-comic text-xs">
          DIPILIH: <span id="votedCountDisplay" class="text-cobalt">0</span> / <span id="maxVoteDisplay">3</span> ORANG
        </div>
      </div>

      <!-- Candidate Player Cards Grid -->
      <div class="sticker-card bg-white rounded-3xl p-4 space-y-2.5">
        <p class="font-bold text-xs text-slate-500 mb-2">Sentuh kartu untuk memilih / membatalkan:</p>
        <div id="votingCandidatesGrid" class="space-y-2">
          <!-- Populated via JS -->
        </div>

        <button id="btnSubmitVote" onclick="submitVote()" disabled class="sticker-btn w-full bg-vermilion text-white font-comic text-lg py-3.5 rounded-xl flex items-center justify-center gap-2 mt-4">
          <span>KIRIM PILIHAN VOTE ➔</span>
        </button>
      </div>

      <!-- Voted Success State -->
      <div id="voteSubmittedNotice" class="hidden sticker-card bg-mint text-white rounded-3xl p-6 text-center space-y-2">
        <span class="text-5xl block">🎉</span>
        <h3 class="font-comic text-2xl">VOTE BERHASIL DIKIRIM!</h3>
        <p class="font-bold text-xs">Pilihanmu sudah tersimpan di server. Sekarang perhatikan layar laptop host / proyektor di depan kelas untuk pengumuman reveal!</p>
      </div>
    </div>

    <!-- 5. REVEAL / KICK STATUS VIEW -->
    <div id="viewReveal" class="hidden space-y-4 text-center">
      <div id="playerKickedCard" class="hidden sticker-card bg-vermilion text-white rounded-3xl p-6 space-y-3">
        <span class="text-6xl block">💥 😵 💥</span>
        <h2 class="font-comic text-3xl uppercase">KAMU TERELIMINASI!</h2>
        <p class="font-bold text-xs text-white/90">
          Perolehan vote kamu berada di urutan terendah pada ronde ini. Tapi jangan sedih, kamu tetap bisa nonton keseruan tebak-tebakan temanmu di layar depan!
        </p>
      </div>

      <div id="playerSafeCard" class="hidden sticker-card bg-mint text-white rounded-3xl p-6 space-y-3">
        <span class="text-6xl block">🎉 🥳 🎉</span>
        <h2 class="font-comic text-3xl uppercase">SELAMAT! KAMU LOLOS!</h2>
        <p class="font-bold text-xs text-white/95">
          Kamu berhasil bertahan di ronde ini! Lihat layar proyektor untuk membongkar logo rahasia dan siapa saja kembaranmu!
        </p>
      </div>

      <div id="playerWinnerCard" class="hidden sticker-card bg-sunny text-ink rounded-3xl p-6 space-y-3">
        <span class="text-6xl block">👑 🏆 👑</span>
        <h2 class="font-comic text-3xl uppercase">KAMU JUARANYA!</h2>
        <p class="font-bold text-xs text-slate-700">
          Luar biasa! Kamu adalah salah satu pemenang bertahan di game "Siapa Kembaranku?"!
        </p>
      </div>
    </div>

  </main>

  <!-- CAMERA SCANNER MODAL -->
  <div id="scannerModal" class="hidden fixed inset-0 z-50 bg-ink/90 backdrop-blur-sm flex flex-col items-center justify-between p-4">
    <div class="w-full flex items-center justify-between text-white py-2">
      <div class="flex items-center gap-2">
        <span class="text-xl">📷</span>
        <span class="font-comic text-lg">ARAHKAN KE QR TEMAN</span>
      </div>
      <button onclick="closeScanner()" class="sticker-btn bg-white text-ink font-bold text-xs px-3 py-1 rounded-xl">
        ✕ TUTUP
      </button>
    </div>

    <!-- Scanner Reader Viewport -->
    <div class="w-full max-w-sm aspect-square bg-black border-4 border-sunny rounded-3xl overflow-hidden relative shadow-[6px_6px_0px_#111111]">
      <div id="qrReader" class="w-full h-full"></div>
    </div>

    <div class="text-white text-center pb-6">
      <p class="text-xs font-bold text-sunny">Pastikan QR code teman berada di dalam kotak kamera.</p>
    </div>
  </div>

  <!-- JAVASCRIPT LOGIC -->
  <script>
    // State
    let playerSession = {
      token: localStorage.getItem('kmbr_token') || '',
      username: localStorage.getItem('kmbr_username') || '',
      gameCode: localStorage.getItem('kmbr_game_code') || "{{ $initialCode }}"
    };

    let localScannedPlayers = JSON.parse(localStorage.getItem('kmbr_scanned') || '[]');
    let selectedVoteIds = [];
    let requiredVoteCount = 3;
    let html5QrScanner = null;
    let syncInterval = null;
    let currentView = 'join';

    // Init View
    function setView(viewName) {
      currentView = viewName;
      document.getElementById('viewJoin').classList.toggle('hidden', viewName !== 'join');
      document.getElementById('viewLobby').classList.toggle('hidden', viewName !== 'lobby');
      document.getElementById('viewDiscussion').classList.toggle('hidden', viewName !== 'discussion');
      document.getElementById('viewVoting').classList.toggle('hidden', viewName !== 'voting');
      document.getElementById('viewReveal').classList.toggle('hidden', viewName !== 'reveal');
      lucide.createIcons();
    }

    // Join Game
    async function joinGame() {
      const usernameInput = document.getElementById('inputUsername').value.trim();
      const codeInput = document.getElementById('inputRoomCode').value.trim().toUpperCase();
      const errorMsg = document.getElementById('joinErrorMsg');

      errorMsg.classList.add('hidden');

      if (!usernameInput) {
        errorMsg.textContent = 'Harap isi nama panggilan kamu!';
        errorMsg.classList.remove('hidden');
        return;
      }

      if (!codeInput || codeInput.length !== 6) {
        errorMsg.textContent = 'Kode room harus 6 huruf!';
        errorMsg.classList.remove('hidden');
        return;
      }

      try {
        const res = await fetch("{{ route('games.siapa-kembaranku.join') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            username: usernameInput,
            code: codeInput
          })
        });

        const data = await res.json();
        if (data.success) {
          playerSession.token = data.player_token;
          playerSession.username = data.username;
          playerSession.gameCode = data.game_code;

          localStorage.setItem('kmbr_token', data.player_token);
          localStorage.setItem('kmbr_username', data.username);
          localStorage.setItem('kmbr_game_code', data.game_code);

          onPlayerJoined();
        } else {
          errorMsg.textContent = data.message || 'Gagal bergabung ke room';
          errorMsg.classList.remove('hidden');
        }
      } catch (e) {
        errorMsg.textContent = 'Terjadi kesalahan koneksi server.';
        errorMsg.classList.remove('hidden');
      }
    }

    function onPlayerJoined() {
      document.getElementById('playerStatusBadge').classList.remove('hidden');
      document.getElementById('playerBadgeName').textContent = playerSession.username;
      document.getElementById('lobbyPlayerName').textContent = playerSession.username;
      document.getElementById('lobbyGameCode').textContent = playerSession.gameCode;
      
      setView('lobby');
      startPolling();
    }

    // Generate Personal QR Code
    function renderPersonalQR() {
      const container = document.getElementById('personalQrContainer');
      container.innerHTML = '';
      
      // QR encodes formatted token string
      const qrData = `KMBR-SCAN:${playerSession.token}`;

      new QRCode(container, {
        text: qrData,
        width: 175,
        height: 175,
        colorDark: "#111111",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
      });
    }

    // Camera Scanner Handling
    function openScanner() {
      document.getElementById('scannerModal').classList.remove('hidden');
      
      if (!html5QrScanner) {
        html5QrScanner = new Html5Qrcode("qrReader");
      }

      const config = { fps: 10, qrbox: { width: 220, height: 220 } };

      html5QrScanner.start(
        { facingMode: "environment" },
        config,
        onQrSuccess,
        (errorMessage) => { /* ignore per-frame failures */ }
      ).catch(err => {
        alert("Gagal membuka kamera. Pastikan izin kamera aktif!");
        closeScanner();
      });
    }

    function closeScanner() {
      if (html5QrScanner) {
        html5QrScanner.stop().catch(() => {}).finally(() => {
          document.getElementById('scannerModal').classList.add('hidden');
        });
      } else {
        document.getElementById('scannerModal').classList.add('hidden');
      }
    }

    async function onQrSuccess(decodedText) {
      closeScanner();

      if (!decodedText.startsWith('KMBR-SCAN:')) {
        alert("QR ini bukan QR pemain Siapa Kembaranku!");
        return;
      }

      const targetToken = decodedText.replace('KMBR-SCAN:', '').trim();

      // Check self scan
      if (targetToken === playerSession.token) {
        alert("Eits! Kamu tidak bisa scan QR kamu sendiri!");
        return;
      }

      try {
        const res = await fetch("{{ route('games.siapa-kembaranku.scan') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            scanner_token: playerSession.token,
            target_token: targetToken
          })
        });

        const data = await res.json();
        if (data.success) {
          alert(`🎉 Kamu berhasil scan!\n\nNama: ${data.target_username}\nLogo Rahasianya: ${data.target_logo} (${data.logo_name})`);
          
          // Save locally
          const exists = localScannedPlayers.find(p => p.id === data.target_id);
          if (!exists) {
            localScannedPlayers.push({
              id: data.target_id,
              username: data.target_username,
              logo: data.target_logo,
              logo_name: data.logo_name
            });
            localStorage.setItem('kmbr_scanned', JSON.stringify(localScannedPlayers));
          }
          renderScannedList(localScannedPlayers);
        } else {
          alert(data.message || 'Gagal memproses QR code');
        }
      } catch (e) {
        alert('Gagal menghubungi server');
      }
    }

    function renderScannedList(list) {
      const container = document.getElementById('scannedListContainer');
      const badge = document.getElementById('scannedCountBadge');

      badge.textContent = `${list.length} ORANG`;

      if (list.length === 0) {
        container.innerHTML = `<p class="text-xs font-semibold text-slate-400 italic text-center py-4">Kamu belum scan teman siapa pun. Tekan tombol kamera di atas!</p>`;
        return;
      }

      container.innerHTML = list.map(item => `
        <div class="p-3 bg-paper border-2 border-ink rounded-xl flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <span class="text-2xl">${item.logo}</span>
            <div>
              <p class="font-extrabold text-sm text-ink">${escapeHtml(item.username)}</p>
              <p class="text-[10px] font-bold text-slate-500">${escapeHtml(item.logo_name || '')}</p>
            </div>
          </div>
          <span class="bg-sunny text-ink border border-ink font-bold text-[10px] px-2 py-0.5 rounded-full">KULIHAT</span>
        </div>
      `).join('');
    }

    // Voting Handling
    function setupVoting(otherPlayers, hasVoted) {
      if (hasVoted) {
        document.getElementById('votingCandidatesGrid').classList.add('hidden');
        document.getElementById('btnSubmitVote').classList.add('hidden');
        document.getElementById('voteSubmittedNotice').classList.remove('hidden');
        return;
      }

      document.getElementById('votingCandidatesGrid').classList.remove('hidden');
      document.getElementById('btnSubmitVote').classList.remove('hidden');
      document.getElementById('voteSubmittedNotice').classList.add('hidden');

      requiredVoteCount = Math.min(3, otherPlayers.length);
      document.getElementById('requiredVoteCountText').textContent = `${requiredVoteCount} orang`;
      document.getElementById('maxVoteDisplay').textContent = requiredVoteCount;

      const grid = document.getElementById('votingCandidatesGrid');
      grid.innerHTML = otherPlayers.map(p => {
        const scannedInfo = localScannedPlayers.find(s => s.id === p.id);
        return `
          <div 
            id="candidate-${p.id}" 
            onclick="toggleVoteCandidate(${p.id})" 
            class="sticker-card bg-paper p-3.5 rounded-2xl flex items-center justify-between cursor-pointer transition-all"
          >
            <div class="flex items-center gap-2.5">
              <span class="text-xl">${scannedInfo ? scannedInfo.logo : '❓'}</span>
              <div>
                <p class="font-extrabold text-sm text-ink">${escapeHtml(p.username)}</p>
                <p class="text-[10px] font-bold ${scannedInfo ? 'text-cobalt' : 'text-slate-400'}">
                  ${scannedInfo ? `Logo yang kamu lihat: ${scannedInfo.logo}` : 'Belum kamu scan'}
                </p>
              </div>
            </div>
            <div id="check-${p.id}" class="w-6 h-6 rounded-lg border-2 border-ink bg-white flex items-center justify-center font-black text-xs">
            </div>
          </div>
        `;
      }).join('');

      updateVoteButtonState();
    }

    function toggleVoteCandidate(id) {
      const idx = selectedVoteIds.indexOf(id);
      if (idx > -1) {
        selectedVoteIds.splice(idx, 1);
      } else {
        if (selectedVoteIds.length >= requiredVoteCount) {
          alert(`Maksimal memilih ${requiredVoteCount} orang!`);
          return;
        }
        selectedVoteIds.push(id);
      }

      // Update UI cards
      document.querySelectorAll('[id^="candidate-"]').forEach(el => {
        const cId = parseInt(el.id.replace('candidate-', ''), 10);
        const check = document.getElementById(`check-${cId}`);
        if (selectedVoteIds.includes(cId)) {
          el.classList.add('selected-card');
          check.innerHTML = '✓';
          check.classList.add('bg-cobalt', 'text-white');
        } else {
          el.classList.remove('selected-card');
          check.innerHTML = '';
          check.classList.remove('bg-cobalt', 'text-white');
        }
      });

      document.getElementById('votedCountDisplay').textContent = selectedVoteIds.length;
      updateVoteButtonState();
    }

    function updateVoteButtonState() {
      const btn = document.getElementById('btnSubmitVote');
      btn.disabled = selectedVoteIds.length !== requiredVoteCount;
    }

    async function submitVote() {
      if (selectedVoteIds.length !== requiredVoteCount) return;

      try {
        const res = await fetch("{{ route('games.siapa-kembaranku.vote') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            player_token: playerSession.token,
            voted_player_ids: selectedVoteIds
          })
        });

        const data = await res.json();
        if (data.success) {
          document.getElementById('votingCandidatesGrid').classList.add('hidden');
          document.getElementById('btnSubmitVote').classList.add('hidden');
          document.getElementById('voteSubmittedNotice').classList.remove('hidden');
        } else {
          alert(data.message || 'Gagal mengirim vote');
        }
      } catch (e) {
        alert('Gagal menghubungi server');
      }
    }

    // Polling State Sync
    async function syncPlayerState() {
      if (!playerSession.token || !playerSession.gameCode) return;

      try {
        const res = await fetch(`{{ route('games.siapa-kembaranku.state') }}?code=${playerSession.gameCode}&token=${playerSession.token}`);
        if (!res.ok) return;

        const data = await res.json();
        if (data.success) {
          const game = data.game;
          const player = data.player;

          // Header update
          document.getElementById('playerStatusBadge').classList.remove('hidden');
          document.getElementById('playerBadgeName').textContent = player.username;

          // Merge server scanned list with local list
          if (data.scanned_list && data.scanned_list.length > 0) {
            data.scanned_list.forEach(item => {
              if (!localScannedPlayers.find(l => l.id === item.id)) {
                localScannedPlayers.push(item);
              }
            });
            localStorage.setItem('kmbr_scanned', JSON.stringify(localScannedPlayers));
            renderScannedList(localScannedPlayers);
          }

          // Handle states
          if (game.status === 'lobby') {
            if (currentView !== 'lobby') {
              setView('lobby');
            }
          } else if (game.status === 'discussion') {
            document.getElementById('discRoundNum').textContent = game.current_round;
            if (currentView !== 'discussion') {
              setView('discussion');
              renderPersonalQR();
              renderScannedList(localScannedPlayers);
            }
          } else if (game.status === 'voting') {
            if (currentView !== 'voting') {
              setView('voting');
              setupVoting(data.other_active_players || [], data.has_voted);
            }
          } else if (game.status === 'reveal' || game.status === 'finished') {
            if (currentView !== 'reveal') {
              setView('reveal');
              document.getElementById('playerKickedCard').classList.toggle('hidden', !player.is_kicked);
              document.getElementById('playerSafeCard').classList.toggle('hidden', player.is_kicked || player.is_winner);
              document.getElementById('playerWinnerCard').classList.toggle('hidden', !player.is_winner);
            }
          }
        }
      } catch (e) {
        console.warn('Sync error:', e);
      }
    }

    function startPolling() {
      if (syncInterval) clearInterval(syncInterval);
      syncPlayerState();
      syncInterval = setInterval(syncPlayerState, 2500);
    }

    function escapeHtml(text) {
      if (!text) return '';
      return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    // Auto-reconnect if session exists
    window.addEventListener('DOMContentLoaded', () => {
      if (playerSession.token && playerSession.gameCode) {
        onPlayerJoined();
      } else {
        setView('join');
      }
    });
  </script>

</body>
</html>
