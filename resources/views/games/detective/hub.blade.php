<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>THE PROBLEM DETECTIVE — MainYuk!</title>

  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    }
    .swal2-popup {
      background: #141418 !important;
      color: #FAFAFA !important;
      border: 1px solid #3F3F46 !important;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased selection:bg-white selection:text-black">

  <header class="border-b border-zinc-800 bg-zinc-950/90">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-white border border-zinc-800 px-3.5 py-2 rounded-lg transition">
        ← Kembali ke Portal
      </a>
      <span class="font-mono-code text-xs uppercase tracking-widest text-zinc-500">CASE FILE // MULTIPLAYER</span>
    </div>
  </header>

  <main class="max-w-5xl mx-auto w-full px-4 py-12 space-y-10">
    <!-- Hero Dossier Header -->
    <div class="space-y-4">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded border border-zinc-700 bg-zinc-900 text-xs font-mono-code uppercase tracking-widest text-zinc-300">
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        FIRST PRINCIPLES & ROOT CAUSE INVESTIGATION
      </div>

      <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white uppercase">
        THE PROBLEM DETECTIVE
      </h1>
      <p class="text-lg sm:text-xl text-zinc-300 font-semibold">
        Ask the right questions. Find the real problem.
      </p>
      <p class="text-sm text-zinc-400 max-w-2xl leading-relaxed">
        Game investigasi multiplayer untuk melatih First Principles Thinking, Critical Thinking, dan Root Cause Analysis secara langsung di kelas. Jangan menebak jawaban — buktikan dengan fakta.
      </p>
    </div>

    <!-- 3 Action Cards: Host, Player, Projector -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
      <!-- 1. Game Master (Host) -->
      <div class="case-card rounded-xl p-6 flex flex-col justify-between space-y-6">
        <div class="space-y-2">
          <div class="font-mono-code text-xs uppercase tracking-widest text-zinc-500">ROLE 01 // GAME MASTER</div>
          <h2 class="text-xl font-extrabold text-white">Buat Room Host</h2>
          <p class="text-sm text-zinc-400 leading-relaxed">
            Kendalikan kasus, durasi fase, jawab pertanyaan tim secara realtime, kirim Breaking News, dan nilai laporan investigasi.
          </p>
        </div>

        <div class="space-y-2.5">
          <button id="createRoomBtn" onclick="createNewRoom()" class="w-full py-3 px-4 rounded-lg bg-white text-black font-extrabold text-sm hover:bg-zinc-200 transition cursor-pointer">
            + Buat Room Baru
          </button>
          <div class="flex gap-2">
            <input id="hostCodeInput" type="text" maxlength="6" placeholder="Kode Room Host" class="flex-1 bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-xs font-mono-code uppercase text-white focus:outline-none focus:border-zinc-500">
            <button onclick="openHostRoom()" class="px-3 py-2 rounded-lg border border-zinc-700 text-xs font-bold text-zinc-300 hover:bg-zinc-800 cursor-pointer">
              Buka
            </button>
          </div>
        </div>
      </div>

      <!-- 2. Player (Detective) -->
      <div class="case-card rounded-xl p-6 flex flex-col justify-between space-y-6 border-zinc-700">
        <div class="space-y-2">
          <div class="font-mono-code text-xs uppercase tracking-widest text-emerald-400">ROLE 02 // DETECTIVE</div>
          <h2 class="text-xl font-extrabold text-white">Gabung Sebagai Pemain</h2>
          <p class="text-sm text-zinc-400 leading-relaxed">
            Masukkan 6 digit Room Code dari dosen/mentor atau scan QR Code di layar proyektor untuk bergabung ke tim investigasi.
          </p>
        </div>

        <div class="space-y-2.5">
          <input id="joinCodeInput" type="text" maxlength="6" placeholder="KODE ROOM (6 KARAKTER)" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3.5 py-2.5 text-sm font-mono-code uppercase text-center tracking-widest text-white focus:outline-none focus:border-white">
          <button onclick="goToPlay()" class="w-full py-3 px-4 rounded-lg bg-emerald-500 text-black font-extrabold text-sm hover:bg-emerald-400 transition cursor-pointer">
            Masuk Ruang Investigasi →
          </button>
        </div>
      </div>

      <!-- 3. Projector Mode -->
      <div class="case-card rounded-xl p-6 flex flex-col justify-between space-y-6">
        <div class="space-y-2">
          <div class="font-mono-code text-xs uppercase tracking-widest text-zinc-500">DISPLAY // 16:9 CLASSROOM</div>
          <h2 class="text-xl font-extrabold text-white">Projector Mode</h2>
          <p class="text-sm text-zinc-400 leading-relaxed">
            Layar publik khusus proyektor kelas: menampilkan kasus, timer besar, sisa nyawa tim, Truth Reveal, dan Leaderboard tanpa bocoran rahasia.
          </p>
        </div>

        <div class="space-y-2.5">
          <input id="projCodeInput" type="text" maxlength="6" placeholder="KODE ROOM PROYEKTOR" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3.5 py-2.5 text-sm font-mono-code uppercase text-center tracking-widest text-white focus:outline-none focus:border-zinc-500">
          <button onclick="goToProjector()" class="w-full py-3 px-4 rounded-lg border border-zinc-600 text-white font-extrabold text-sm hover:bg-zinc-800 transition cursor-pointer">
            Buka Layar Proyektor ⛶
          </button>
        </div>
      </div>
    </div>

    <!-- Objective Statement -->
    <div class="case-card rounded-xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="text-sm font-semibold text-zinc-300">
        <span class="font-mono-code text-xs uppercase text-red-400 mr-2">[PRINSIP UTAMA]</span>
        "The objective is not to guess the answer. The objective is to prove it."
      </div>
      <span class="font-mono-code text-xs text-zinc-500 shrink-0">10 KASUS BAWAAN + CUSTOM BUILDER</span>
    </div>
  </main>

  <footer class="border-t border-zinc-900 py-6 text-center text-xs text-zinc-600 font-mono-code">
    THE PROBLEM DETECTIVE • MAINYUK! CLASSROOM EDITION
  </footer>

  <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function createNewRoom() {
      const btn = document.getElementById('createRoomBtn');
      btn.disabled = true;
      btn.textContent = 'Membuat Room...';

      try {
        const res = await fetch("{{ route('games.problem-detective.rooms.create') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          }
        });
        const data = await res.json();
        if (data.success) {
          localStorage.setItem('detective_host_token_' + data.code, data.host_token);
          window.location.href = data.host_url;
        } else {
          Swal.fire('Gagal', 'Tidak dapat membuat room baru.', 'error');
        }
      } catch (err) {
        Swal.fire('Error', 'Koneksi bermasalah saat membuat room.', 'error');
      } finally {
        btn.disabled = false;
        btn.textContent = '+ Buat Room Baru';
      }
    }

    function openHostRoom() {
      const code = document.getElementById('hostCodeInput').value.trim().toUpperCase();
      if (code.length !== 6) {
        Swal.fire('Kode Tidak Valid', 'Masukkan 6 karakter kode room.', 'warning');
        return;
      }
      window.location.href = "{{ route('games.problem-detective.host') }}?code=" + encodeURIComponent(code);
    }

    function goToPlay() {
      const code = document.getElementById('joinCodeInput').value.trim().toUpperCase();
      if (code.length !== 6) {
        Swal.fire('Kode Diperlukan', 'Masukkan 6 karakter Room Code untuk bergabung.', 'warning');
        return;
      }
      window.location.href = "{{ route('games.problem-detective.play') }}?code=" + encodeURIComponent(code);
    }

    function goToProjector() {
      const code = document.getElementById('projCodeInput').value.trim().toUpperCase();
      if (code.length !== 6) {
        Swal.fire('Kode Diperlukan', 'Masukkan 6 karakter Room Code untuk Projector Mode.', 'warning');
        return;
      }
      window.location.href = "{{ route('games.problem-detective.projector') }}?code=" + encodeURIComponent(code);
    }
  </script>
</body>
</html>
