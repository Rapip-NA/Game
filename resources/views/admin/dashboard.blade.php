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

    <!-- Flash Alerts -->
    @if (session('success'))
      <div class="sticker-card bg-emerald-100 border-black p-4 flex items-center gap-3 text-emerald-950 font-bold text-sm">
        <span class="text-xl">🎉</span>
        <div>{{ session('success') }}</div>
      </div>
    @endif

    @if (session('error'))
      <div class="sticker-card bg-rose-100 border-black p-4 flex items-center gap-3 text-rose-950 font-bold text-sm">
        <span class="text-xl">⚠️</span>
        <div>{{ session('error') }}</div>
      </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      <div class="sticker-card bg-white p-5 flex flex-col justify-between">
        <span class="text-xs font-extrabold text-slate-500 uppercase">Total Game</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="font-comic text-3xl sm:text-4xl text-black">{{ $stats['total_games'] }}</span>
          <span class="text-2xl">🎮</span>
        </div>
      </div>

      <div class="sticker-card bg-emerald-50 p-5 flex flex-col justify-between">
        <span class="text-xs font-extrabold text-emerald-700 uppercase">Game Aktif</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="font-comic text-3xl sm:text-4xl text-emerald-600">{{ $stats['active_games'] }}</span>
          <span class="text-2xl">🟢</span>
        </div>
      </div>

      <div class="sticker-card {{ $stats['in_maintenance'] > 0 ? 'bg-amber-100' : 'bg-white' }} p-5 flex flex-col justify-between">
        <span class="text-xs font-extrabold text-amber-800 uppercase">Sedang Maintenance</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="font-comic text-3xl sm:text-4xl text-amber-600">{{ $stats['in_maintenance'] }}</span>
          <span class="text-2xl">🛠️</span>
        </div>
      </div>

      <div class="sticker-card bg-blue-50 p-5 flex flex-col justify-between">
        <span class="text-xs font-extrabold text-blue-700 uppercase">Saran Game Masuk</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="font-comic text-3xl sm:text-4xl text-cobalt">{{ $stats['total_suggestions'] }}</span>
          <span class="text-2xl">💡</span>
        </div>
      </div>
    </div>

    <!-- SECTION 1: KONTROL STATUS GAME / MAINTENANCE -->
    <section class="space-y-4">
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

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($gameStatuses as $key => $game)
          <div class="sticker-card p-5 flex flex-col justify-between relative overflow-hidden {{ $game->is_maintenance ? 'bg-amber-50 border-amber-900' : 'bg-white' }}">
            @if ($game->is_maintenance)
              <div class="absolute top-3 right-3">
                <span class="px-2.5 py-1 rounded-full bg-vermilion text-white border-2 border-black font-extrabold text-[10px] tracking-wide animate-pulse">
                  🔒 MAINTENANCE
                </span>
              </div>
            @else
              <div class="absolute top-3 right-3">
                <span class="px-2.5 py-1 rounded-full bg-emerald-500 text-white border-2 border-black font-extrabold text-[10px] tracking-wide">
                  🟢 BISA DIMAINKAN
                </span>
              </div>
            @endif

            <div>
              <!-- Game Icon based on key -->
              <div class="w-12 h-12 rounded-2xl border-2 border-black flex items-center justify-center text-2xl mb-3 shadow-[2px_2px_0_#000] {{ $game->is_maintenance ? 'bg-amber-200' : 'bg-sunny' }}">
                @if ($key === 'super-password')
                  🔑
                @elseif ($key === 'susun-urutan')
                  🔢
                @elseif ($key === 'siapa-kembaranku')
                  🎭
                @elseif ($key === 'speaking')
                  🎤
                @else
                  🎲
                @endif
              </div>

              <h3 class="font-comic text-xl text-black leading-tight">
                {{ $game->game_name }}
              </h3>
              <p class="text-[11px] font-mono text-slate-500 mt-0.5">slug: {{ $key }}</p>

              <!-- Pesan Maintenance Saat Ini -->
              <div class="mt-4 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 text-xs">
                <p class="font-extrabold text-[10px] text-slate-500 uppercase mb-1">Pesan untuk pemain:</p>
                <p class="font-doodle text-sm font-bold text-slate-700 italic leading-snug">
                  "{{ $game->message ?: 'maaf ya gamenya lagi di maintanance sama admin, jadi gak bisa dipake dlu' }}"
                </p>
              </div>
            </div>

            <!-- Action Controls -->
            <div class="mt-5 pt-4 border-t border-slate-200 space-y-3">
              <!-- Toggle Button -->
              <form action="{{ route('admin.maintenance.toggle', $key) }}" method="POST">
                @csrf
                @if ($game->is_maintenance)
                  <button type="submit" class="w-full sticker-btn bg-emerald-500 text-white font-extrabold text-xs py-2.5 px-3 rounded-xl flex items-center justify-center gap-2">
                    <i data-lucide="unlock" class="w-4 h-4"></i>
                    BUKA KUNCI GAME (AKTIFKAN)
                  </button>
                @else
                  <button type="submit" class="w-full sticker-btn bg-vermilion text-white font-extrabold text-xs py-2.5 px-3 rounded-xl flex items-center justify-center gap-2">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                    KUNCI GAME (MAINTENANCE)
                  </button>
                @endif
              </form>

              <!-- Edit message modal toggle / accordion -->
              <details class="text-xs group">
                <summary class="cursor-pointer font-bold text-cobalt hover:underline flex items-center justify-between">
                  <span>Ubah Pesan Maintenance</span>
                  <span class="group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <form action="{{ route('admin.maintenance.message', $key) }}" method="POST" class="mt-2 space-y-2">
                  @csrf
                  <textarea 
                    name="message" 
                    rows="2" 
                    class="w-full p-2 border-2 border-black rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-cobalt"
                    placeholder="Tulis pesan maintenance..."
                  >{{ $game->message }}</textarea>
                  <button type="submit" class="sticker-btn bg-sunny text-black font-extrabold text-[11px] px-3 py-1.5 rounded-lg w-full">
                    Simpan Pesan
                  </button>
                </form>
              </details>
            </div>
          </div>
        @endforeach
      </div>
    </section>

    <!-- SECTION 2: SARAN & REQUEST GAME DARI USER -->
    <section class="space-y-4 pt-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b-2 border-black pb-3">
        <div>
          <h2 class="font-comic text-2xl sm:text-3xl text-black flex items-center gap-2">
            <span>💡</span> SARAN & REQUEST GAME DARI PEMAIN
            <span class="px-2.5 py-0.5 rounded-full bg-sunny border-2 border-black font-extrabold text-xs text-black">
              {{ $suggestions->count() }} Saran
            </span>
          </h2>
          <p class="text-xs sm:text-sm font-semibold text-slate-600">
            Ide-ide kreatif dari pemain yang dikirim melalui formulir "Saran Game" di landing page.
          </p>
        </div>
      </div>

      @if ($suggestions->isEmpty())
        <div class="sticker-card bg-white p-12 text-center">
          <div class="w-16 h-16 rounded-2xl bg-amber-100 border-2 border-black mx-auto flex items-center justify-center text-3xl mb-3 shadow-[2px_2px_0_#000]">
            📭
          </div>
          <h3 class="font-comic text-2xl text-black">BELUM ADA SARAN GAME</h3>
          <p class="text-sm font-semibold text-slate-500 mt-1">
            Saat pemain mengirim request game di landing page, semua idenya akan otomatis muncul di sini!
          </p>
        </div>
      @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach ($suggestions as $item)
            <div class="sticker-card bg-white p-5 flex flex-col justify-between">
              <div>
                <!-- Top metadata -->
                <div class="flex items-center justify-between gap-2 pb-3 border-b-2 border-slate-100">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-pastelPink border-2 border-black flex items-center justify-center font-comic text-sm text-black shrink-0">
                      {{ strtoupper(substr($item->name, 0, 1)) }}
                    </div>
                    <div>
                      <p class="font-extrabold text-xs text-black leading-tight">{{ $item->name }}</p>
                      <p class="text-[10px] font-semibold text-slate-400">{{ $item->created_at->diffForHumans() }}</p>
                    </div>
                  </div>

                  <!-- Delete form -->
                  <form action="{{ route('admin.suggestions.delete', $item->id) }}" method="POST" onsubmit="return confirm('Hapus saran game ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="sticker-btn bg-white hover:bg-rose-100 text-rose-600 p-1.5 rounded-lg text-xs" title="Hapus saran ini">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </form>
                </div>

                <!-- Game Title -->
                <div class="mt-3">
                  <span class="inline-block px-2 py-0.5 rounded-md bg-sunny border border-black text-[10px] font-extrabold uppercase mb-1">
                    Ide Game
                  </span>
                  <h4 class="font-comic text-lg text-black leading-snug">
                    {{ $item->game_title }}
                  </h4>
                </div>

                <!-- Concept / Description -->
                <div class="mt-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200">
                  <p class="font-doodle text-base font-bold text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $item->description }}
                  </p>
                </div>
              </div>

              <!-- Footer info -->
              <div class="mt-4 pt-2 flex items-center justify-between text-[11px] font-semibold text-slate-400">
                <span>ID: #{{ $item->id }}</span>
                <span>{{ $item->created_at->format('d M Y, H:i') }}</span>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </section>

  </main>

  <script>
    window.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
    });
  </script>
</body>
</html>
