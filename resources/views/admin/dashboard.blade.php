<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Panel — MainYuk!</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('img/icon.png') }}">

  <!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            cobalt: '#0B44D6',
            sunny: '#FFD21E',
            vermilion: '#EB3B24',
            pastelPink: '#F9B4C4',
            paper: '#FAFAFA',
            ink: '#111111'
          }
        }
      }
    }
  </script>

  <style>
    :root {
      --bg: #FAFAFA;
      --card: #FFFFFF;
      --text: #111111;
      --muted: #475569;
      --line: #111111;
      --cobalt: #0B44D6;
      --sunny: #FFD21E;
      --vermilion: #EB3B24;
      --pink: #F9B4C4;
      --green: #22C55E;
    }

    * { box-sizing: border-box; }

    body {
      background-color: var(--bg);
      background-image: radial-gradient(#CBD5E1 1.4px, transparent 1.4px);
      background-size: 22px 22px;
      color: var(--text);
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
    }

    .font-comic { font-family: 'Luckiest Guy', cursive; }
    .font-doodle { font-family: 'Patrick Hand', cursive; }

    .sticker-card {
      background: var(--card);
      border: 3px solid var(--line);
      box-shadow: 6px 6px 0px var(--line);
      border-radius: 24px;
      transition: transform 0.15s ease;
    }

    .sticker-btn {
      border: 2.5px solid var(--line);
      box-shadow: 3.5px 3.5px 0px var(--line);
      transition: all 0.15s ease;
      cursor: pointer;
    }
    .sticker-btn:hover {
      transform: translate(-2px, -2px);
      box-shadow: 5.5px 5.5px 0px var(--line);
    }
    .sticker-btn:active {
      transform: translate(2px, 2px);
      box-shadow: 1px 1px 0px var(--line);
    }
  </style>
</head>
<body class="pb-24">

  <!-- Header Nav -->
  <header class="bg-cobalt border-b-[3.5px] border-black text-white py-4 px-4 sm:px-8 sticky top-0 z-40 shadow-[0_4px_0_#111]">
    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-sunny border-2 border-black flex items-center justify-center text-xl text-black shadow-[2px_2px_0_#000]">
          ⚡
        </span>
        <div>
          <h1 class="font-comic text-2xl sm:text-3xl tracking-wide leading-none text-white">
            MAINYUK! <span class="text-sunny">ADMIN</span>
          </h1>
          <p class="text-xs font-semibold text-blue-200 mt-0.5">Control Panel & Pengaturan Game</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" target="_blank" class="sticker-btn bg-white text-black font-extrabold text-xs sm:text-sm px-4 py-2 rounded-full flex items-center gap-1.5">
          <i data-lucide="external-link" class="w-4 h-4"></i>
          LIHAT WEBSITE
        </a>

        <form action="{{ route('logout') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="sticker-btn bg-vermilion text-white font-extrabold text-xs sm:text-sm px-4 py-2 rounded-full flex items-center gap-1.5">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            LOGOUT
          </button>
        </form>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 space-y-8">

    <!-- Flash Alert Container -->
    <div id="flashContainer" class="@if (!session('success') && !session('error')) hidden @endif">
      @if (session('success'))
        <div class="sticker-card bg-emerald-100 border-black p-4 flex items-center justify-between gap-3 font-bold text-sm text-emerald-950">
          <div class="flex items-center gap-3">
            <span class="text-xl">🎉</span>
            <div>{{ session('success') }}</div>
          </div>
          <button onclick="this.parentElement.parentElement.classList.add('hidden')" class="text-xs font-extrabold underline">Tutup</button>
        </div>
      @elseif (session('error'))
        <div class="sticker-card bg-rose-100 border-black p-4 flex items-center justify-between gap-3 font-bold text-sm text-rose-950">
          <div class="flex items-center gap-3">
            <span class="text-xl">⚠️</span>
            <div>{{ session('error') }}</div>
          </div>
          <button onclick="this.parentElement.parentElement.classList.add('hidden')" class="text-xs font-extrabold underline">Tutup</button>
        </div>
      @endif
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      <div class="sticker-card bg-white p-5 flex flex-col justify-between">
        <span class="text-xs font-extrabold text-slate-500 uppercase">Total Game</span>
        <div class="flex items-baseline justify-between mt-2">
          <span id="statTotal" class="font-comic text-3xl sm:text-4xl text-black">{{ $stats['total_games'] }}</span>
          <span class="text-2xl">🎮</span>
        </div>
      </div>

      <div class="sticker-card bg-emerald-50 p-5 flex flex-col justify-between">
        <span class="text-xs font-extrabold text-emerald-700 uppercase">Game Aktif</span>
        <div class="flex items-baseline justify-between mt-2">
          <span id="statActive" class="font-comic text-3xl sm:text-4xl text-emerald-600">{{ $stats['active_games'] }}</span>
          <span class="text-2xl">🟢</span>
        </div>
      </div>

      <div id="cardMaint" class="sticker-card {{ $stats['in_maintenance'] > 0 ? 'bg-amber-100' : 'bg-white' }} p-5 flex flex-col justify-between">
        <span class="text-xs font-extrabold text-amber-800 uppercase">Sedang Maintenance</span>
        <div class="flex items-baseline justify-between mt-2">
          <span id="statMaint" class="font-comic text-3xl sm:text-4xl text-amber-600">{{ $stats['in_maintenance'] }}</span>
          <span class="text-2xl">🛠️</span>
        </div>
      </div>

      <div class="sticker-card bg-blue-50 p-5 flex flex-col justify-between">
        <span class="text-xs font-extrabold text-blue-700 uppercase">Saran Game Masuk</span>
        <div class="flex items-baseline justify-between mt-2">
          <span id="statSugg" class="font-comic text-3xl sm:text-4xl text-cobalt">{{ $stats['total_suggestions'] }}</span>
          <span class="text-2xl">💡</span>
        </div>
      </div>
    </div>

    <!-- SECTION 1: KONTROL STATUS GAME / MAINTENANCE -->
    <section id="kelola-game" class="space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-black pb-3">
        <div>
          <h2 class="font-comic text-2xl sm:text-3xl text-black flex items-center gap-2">
            <span>🛠️</span> KELOLA STATUS GAME (KUNCI / BUKA)
          </h2>
          <p class="text-xs sm:text-sm font-semibold text-slate-600">
            Kunci game jika sedang maintenance. Saat dikunci, pemain yang membuka game akan otomatis dialihkan ke halaman info maintenance.
          </p>
        </div>
      </div>

      <div id="gamesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Kartu Status Game dirender via JavaScript -->
      </div>
    </section>

    <!-- SECTION 2: SARAN & REQUEST GAME DARI USER -->
    <section class="space-y-4 pt-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b-2 border-black pb-3">
        <div>
          <h2 class="font-comic text-2xl sm:text-3xl text-black flex items-center gap-2">
            <span>💡</span> SARAN & REQUEST GAME DARI PEMAIN
            <span id="suggBadge" class="px-2.5 py-0.5 rounded-full bg-sunny border-2 border-black font-extrabold text-xs text-black">
              {{ $stats['total_suggestions'] }} Saran
            </span>
          </h2>
          <p class="text-xs sm:text-sm font-semibold text-slate-600">
            Ide-ide kreatif dari pemain yang dikirim melalui formulir "Saran Game" di landing page.
          </p>
        </div>

        <button onclick="openAddSuggestionModal()" class="sticker-btn bg-sunny text-black font-extrabold text-xs px-4 py-2.5 rounded-full flex items-center gap-1.5 self-start sm:self-auto">
          <i data-lucide="plus-circle" class="w-4 h-4"></i>
          TAMBAH REQUEST GAME
        </button>
      </div>

      <div id="suggestionsContainer">
        <!-- Kartu Saran dirender via JavaScript -->
      </div>
    </section>

  </main>

  <!-- MODAL KONFIRMASI HAPUS SARAN -->
  <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
    <div class="sticker-card bg-white max-w-md w-full p-6 text-center space-y-4">
      <div class="w-14 h-14 rounded-2xl bg-rose-100 border-2 border-black mx-auto flex items-center justify-center text-3xl shadow-[3px_3px_0_#000]">
        🗑️
      </div>
      <h3 class="font-comic text-2xl text-black">HAPUS SARAN GAME INI?</h3>
      <p class="text-sm font-semibold text-slate-600">
        Saran game dari pemain ini akan dihapus dari daftar antrean request.
      </p>
      <div class="flex justify-center gap-3 pt-2">
        <button onclick="closeDeleteModal()" class="sticker-btn bg-white text-black font-extrabold text-xs px-5 py-2.5 rounded-xl">
          BATAL
        </button>
        <button id="confirmDeleteBtn" class="sticker-btn bg-vermilion text-white font-extrabold text-xs px-5 py-2.5 rounded-xl">
          YA, HAPUS!
        </button>
      </div>
    </div>
  </div>

  <!-- MODAL TAMBAH SARAN BARU -->
  <div id="addModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
    <div class="sticker-card bg-white max-w-md w-full p-6 space-y-4">
      <div class="flex items-center justify-between border-b-2 border-black pb-3">
        <h3 class="font-comic text-2xl text-black">💡 TAMBAH REQUEST GAME</h3>
        <button onclick="closeAddModal()" class="sticker-btn bg-vermilion text-white p-1.5 rounded-lg">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <form id="addSuggestionForm" class="space-y-3 text-left">
        <div>
          <label class="block text-xs font-extrabold uppercase mb-1">Nama Pemain</label>
          <input type="text" id="inputPlayerName" required placeholder="Contoh: Raka Pratama" class="w-full p-2.5 border-2 border-black rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-cobalt">
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase mb-1">Judul Ide Game</label>
          <input type="text" id="inputGameTitle" required placeholder="Contoh: Tebak Gambar Kilat" class="w-full p-2.5 border-2 border-black rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-cobalt">
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase mb-1">Deskripsi Konsep</label>
          <textarea id="inputGameDesc" rows="3" required placeholder="Jelaskan cara main singkatnya..." class="w-full p-2.5 border-2 border-black rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-cobalt"></textarea>
        </div>
        <div class="pt-2 flex justify-end gap-2">
          <button type="button" onclick="closeAddModal()" class="sticker-btn bg-white text-black font-extrabold text-xs px-4 py-2 rounded-xl">Batal</button>
          <button type="submit" id="btnSubmitAddSugg" class="sticker-btn bg-cobalt text-white font-extrabold text-xs px-5 py-2 rounded-xl">Kirim Saran →</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Data State Status Game dari Backend
    const rawGameStatuses = @json($gameStatuses);
    const gameIcons = {
      'super-password': '🔑',
      'susun-urutan': '🔢',
      'siapa-kembaranku': '🎭',
      'speaking': '🎤'
    };

    let gameStatuses = {};
    for (const [key, item] of Object.entries(rawGameStatuses)) {
      gameStatuses[key] = {
        game_name: item.game_name || key,
        icon: gameIcons[key] || '🎲',
        is_maintenance: Boolean(item.is_maintenance),
        message: item.message || 'maaf ya gamenya lagi di maintanance sama admin, jadi gak bisa dipake dlu'
      };
    }

    // Data State Saran Game dari Backend
    const rawSuggestions = @json($suggestions);
    let suggestions = rawSuggestions.map(item => ({
      id: item.id,
      name: item.name,
      timeAgo: formatTimeAgo(new Date(item.created_at)),
      dateStr: formatDate(new Date(item.created_at)),
      game_title: item.game_title,
      description: item.description
    }));

    let pendingDeleteId = null;

    // Helper format waktu
    function formatTimeAgo(date) {
      const now = new Date();
      const seconds = Math.floor((now - date) / 1000);
      if (seconds < 60) return 'Baru saja';
      const minutes = Math.floor(seconds / 60);
      if (minutes < 60) return `${minutes} menit yang lalu`;
      const hours = Math.floor(minutes / 60);
      if (hours < 24) return `${hours} jam yang lalu`;
      const days = Math.floor(hours / 24);
      return `${days} hari yang lalu`;
    }

    function formatDate(date) {
      const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
      const day = date.getDate();
      const month = months[date.getMonth()];
      const year = date.getFullYear();
      const hours = String(date.getHours()).padStart(2, '0');
      const mins = String(date.getMinutes()).padStart(2, '0');
      return `${day} ${month} ${year}, ${hours}:${mins}`;
    }

    // Tampilkan Flash Alert Neo-Brutalism
    function showToast(msg, type = 'success') {
      const container = document.getElementById('flashContainer');
      const bgClass = type === 'error' ? 'bg-rose-100 text-rose-950' : type === 'info' ? 'bg-amber-100 text-amber-950' : 'bg-emerald-100 text-emerald-950';
      const emoji = type === 'error' ? '⚠️' : type === 'info' ? '⚡' : '🎉';

      container.className = 'block';
      container.innerHTML = `
        <div class="sticker-card ${bgClass} border-black p-4 flex items-center justify-between gap-3 font-bold text-sm mb-6">
          <div class="flex items-center gap-3">
            <span class="text-xl">${emoji}</span>
            <div>${escapeHtml(msg)}</div>
          </div>
          <button onclick="this.parentElement.parentElement.classList.add('hidden')" class="text-xs font-extrabold underline">Tutup</button>
        </div>
      `;
    }

    // Update Kartu Statistik Atas
    function updateMetrics() {
      const keys = Object.keys(gameStatuses);
      const total = keys.length;
      const maintCount = keys.filter(k => gameStatuses[k].is_maintenance).length;
      const activeCount = total - maintCount;

      document.getElementById('statTotal').textContent = total;
      document.getElementById('statActive').textContent = activeCount;
      document.getElementById('statMaint').textContent = maintCount;
      document.getElementById('statSugg').textContent = suggestions.length;
      document.getElementById('suggBadge').textContent = `${suggestions.length} Saran`;

      const cardMaint = document.getElementById('cardMaint');
      cardMaint.className = `sticker-card ${maintCount > 0 ? 'bg-amber-100' : 'bg-white'} p-5 flex flex-col justify-between`;
    }

    // Render Grid Status Game
    function renderGames() {
      const grid = document.getElementById('gamesGrid');
      grid.innerHTML = Object.entries(gameStatuses).map(([key, game]) => {
        const isMaint = game.is_maintenance;
        return `
          <div class="sticker-card p-5 flex flex-col justify-between relative overflow-hidden ${isMaint ? 'bg-amber-50 border-amber-900' : 'bg-white'}">
            <div class="absolute top-3 right-3">
              ${isMaint
                ? `<span class="px-2.5 py-1 rounded-full bg-vermilion text-white border-2 border-black font-extrabold text-[10px] tracking-wide animate-pulse">🔒 MAINTENANCE</span>`
                : `<span class="px-2.5 py-1 rounded-full bg-emerald-500 text-white border-2 border-black font-extrabold text-[10px] tracking-wide">🟢 BISA DIMAINKAN</span>`
              }
            </div>

            <div>
              <div class="w-12 h-12 rounded-2xl border-2 border-black flex items-center justify-center text-2xl mb-3 shadow-[2px_2px_0_#000] ${isMaint ? 'bg-amber-200' : 'bg-sunny'}">
                ${game.icon}
              </div>

              <h3 class="font-comic text-xl text-black leading-tight">
                ${escapeHtml(game.game_name)}
              </h3>
              <p class="text-[11px] font-mono text-slate-500 mt-0.5">slug: ${key}</p>

              <!-- Pesan Maintenance Saat Ini -->
              <div class="mt-4 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 text-xs">
                <p class="font-extrabold text-[10px] text-slate-500 uppercase mb-1">Pesan untuk pemain:</p>
                <p class="font-doodle text-sm font-bold text-slate-700 italic leading-snug">
                  "${escapeHtml(game.message)}"
                </p>
              </div>
            </div>

            <!-- Action Controls -->
            <div class="mt-5 pt-4 border-t border-slate-200 space-y-3">
              <button type="button" onclick="toggleMaintenance('${key}')" id="btn-toggle-${key}" class="w-full sticker-btn ${isMaint ? 'bg-emerald-500' : 'bg-vermilion'} text-white font-extrabold text-xs py-2.5 px-3 rounded-xl flex items-center justify-center gap-2">
                <i data-lucide="${isMaint ? 'unlock' : 'lock'}" class="w-4 h-4"></i>
                ${isMaint ? 'BUKA KUNCI GAME (AKTIFKAN)' : 'KUNCI GAME (MAINTENANCE)'}
              </button>

              <details class="text-xs group">
                <summary class="cursor-pointer font-bold text-cobalt hover:underline flex items-center justify-between select-none">
                  <span>Ubah Pesan Maintenance</span>
                  <span class="group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <form onsubmit="saveMaintenanceMessage(event, '${key}')" class="mt-2 space-y-2">
                  <textarea
                    id="msg-${key}"
                    rows="2"
                    class="w-full p-2 border-2 border-black rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-cobalt"
                    placeholder="Tulis pesan maintenance..."
                  >${escapeHtml(game.message)}</textarea>
                  <button type="submit" class="sticker-btn bg-sunny text-black font-extrabold text-[11px] px-3 py-1.5 rounded-lg w-full">
                    Simpan Pesan
                  </button>
                </form>
              </details>
            </div>
          </div>
        `;
      }).join('');

      updateMetrics();
      lucide.createIcons();
    }

    // Toggle Status Kunci/Buka Game (Persist ke Backend via AJAX)
    async function toggleMaintenance(key) {
      const game = gameStatuses[key];
      if (!game) return;
      const btn = document.getElementById(`btn-toggle-${key}`);
      if (btn) btn.disabled = true;

      try {
        const response = await fetch(`/admin/maintenance/${key}/toggle`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const result = await response.json();

        if (response.ok && result.status === 'success') {
          game.is_maintenance = Boolean(result.game.is_maintenance);
          renderGames();
          if (game.is_maintenance) {
            showToast(`Game "${game.game_name}" berhasil DIKUNCI (Mode Maintenance aktif).`, 'info');
          } else {
            showToast(`Game "${game.game_name}" telah DIBUKA kembali dan siap dimainkan!`, 'success');
          }
        } else {
          showToast(result.message || 'Gagal mengubah status game.', 'error');
        }
      } catch (err) {
        showToast('Terjadi kesalahan jaringan.', 'error');
      } finally {
        if (btn) btn.disabled = false;
      }
    }

    // Simpan Pesan Maintenance (Persist ke Backend via AJAX)
    async function saveMaintenanceMessage(e, key) {
      e.preventDefault();
      const textarea = document.getElementById(`msg-${key}`);
      const newVal = textarea.value.trim();
      const game = gameStatuses[key];

      try {
        const formData = new FormData();
        formData.append('message', newVal);

        const response = await fetch(`/admin/maintenance/${key}/message`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        });
        const result = await response.json();

        if (response.ok && result.status === 'success') {
          game.message = result.game.message;
          renderGames();
          showToast(`Pesan maintenance untuk "${game.game_name}" berhasil diperbarui!`, 'success');
        } else {
          showToast(result.message || 'Gagal memperbarui pesan.', 'error');
        }
      } catch (err) {
        showToast('Terjadi kesalahan saat menyimpan pesan.', 'error');
      }
    }

    // Render Daftar Saran Game dari User
    function renderSuggestions() {
      const container = document.getElementById('suggestionsContainer');

      if (suggestions.length === 0) {
        container.innerHTML = `
          <div class="sticker-card bg-white p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 border-2 border-black mx-auto flex items-center justify-center text-3xl mb-3 shadow-[2px_2px_0_#000]">
              📭
            </div>
            <h3 class="font-comic text-2xl text-black">BELUM ADA SARAN GAME</h3>
            <p class="text-sm font-semibold text-slate-500 mt-1">
              Saat pemain mengirim request game di landing page, semua idenya akan otomatis muncul di sini!
            </p>
          </div>
        `;
        updateMetrics();
        return;
      }

      container.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          ${suggestions.map(item => `
            <div class="sticker-card bg-white p-5 flex flex-col justify-between">
              <div>
                <div class="flex items-center justify-between gap-2 pb-3 border-b-2 border-slate-100">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-pastelPink border-2 border-black flex items-center justify-center font-comic text-sm text-black shrink-0">
                      ${escapeHtml(item.name.charAt(0).toUpperCase())}
                    </div>
                    <div>
                      <p class="font-extrabold text-xs text-black leading-tight">${escapeHtml(item.name)}</p>
                      <p class="text-[10px] font-semibold text-slate-400">${escapeHtml(item.timeAgo)}</p>
                    </div>
                  </div>

                  <button type="button" onclick="openDeleteModal(${item.id})" class="sticker-btn bg-white hover:bg-rose-100 text-rose-600 p-1.5 rounded-lg text-xs" title="Hapus saran ini">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </div>

                <div class="mt-3">
                  <span class="inline-block px-2 py-0.5 rounded-md bg-sunny border border-black text-[10px] font-extrabold uppercase mb-1">
                    Ide Game
                  </span>
                  <h4 class="font-comic text-lg text-black leading-snug">
                    ${escapeHtml(item.game_title)}
                  </h4>
                </div>

                <div class="mt-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200">
                  <p class="font-doodle text-base font-bold text-slate-700 leading-relaxed whitespace-pre-line">${escapeHtml(item.description)}</p>
                </div>
              </div>

              <div class="mt-4 pt-2 flex items-center justify-between text-[11px] font-semibold text-slate-400">
                <span>ID: #${item.id}</span>
                <span>${escapeHtml(item.dateStr)}</span>
              </div>
            </div>
          `).join('')}
        </div>
      `;

      updateMetrics();
      lucide.createIcons();
    }

    // Modal Konfirmasi Hapus
    function openDeleteModal(id) {
      pendingDeleteId = id;
      const modal = document.getElementById('deleteModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeDeleteModal() {
      pendingDeleteId = null;
      const modal = document.getElementById('deleteModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
      if (pendingDeleteId !== null) {
        const idToDelete = pendingDeleteId;
        closeDeleteModal();

        try {
          const response = await fetch(`/admin/suggestions/${idToDelete}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
          const result = await response.json();

          if (response.ok && result.status === 'success') {
            suggestions = suggestions.filter(s => s.id !== idToDelete);
            renderSuggestions();
            showToast('Saran game berhasil dihapus dari daftar.', 'success');
          } else {
            showToast(result.message || 'Gagal menghapus saran game.', 'error');
          }
        } catch (err) {
          showToast('Terjadi kesalahan saat menghapus saran.', 'error');
        }
      }
    });

    // Modal Tambah Saran
    function openAddSuggestionModal() {
      const modal = document.getElementById('addModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      lucide.createIcons();
    }

    function closeAddModal() {
      const modal = document.getElementById('addModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    document.getElementById('addSuggestionForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btnSubmitAddSugg');
      btn.disabled = true;

      const name = document.getElementById('inputPlayerName').value.trim();
      const game_title = document.getElementById('inputGameTitle').value.trim();
      const description = document.getElementById('inputGameDesc').value.trim();

      try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('game_title', game_title);
        formData.append('description', description);

        const response = await fetch("{{ route('game-suggestions.store') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        });
        const result = await response.json();

        if (response.ok && result.status === 'success') {
          const newItem = result.data;
          suggestions.unshift({
            id: newItem.id,
            name: newItem.name,
            timeAgo: 'Baru saja',
            dateStr: formatDate(new Date(newItem.created_at || Date.now())),
            game_title: newItem.game_title,
            description: newItem.description
          });

          e.target.reset();
          closeAddModal();
          renderSuggestions();
          showToast(`Request game baru "${game_title}" dari ${name} berhasil ditambahkan!`, 'success');
        } else {
          showToast(result.message || 'Gagal menambahkan saran.', 'error');
        }
      } catch (err) {
        showToast('Terjadi kesalahan koneksi.', 'error');
      } finally {
        btn.disabled = false;
      }
    });

    function escapeHtml(str) {
      return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    window.addEventListener('DOMContentLoaded', () => {
      renderGames();
      renderSuggestions();
      lucide.createIcons();
    });
  </script>
</body>
</html>
