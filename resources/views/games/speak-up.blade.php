<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>SPEAK UP! — Think Fast. Speak Smart.</title>

  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('img/icon.png') }}">

  <!-- Google Fonts: Luckiest Guy & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Tailwind CSS, Lucide Icons, SweetAlert2 -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --su-bg: #0D0D11;
      --su-card: #16161D;
      --su-text: #FAF9F6;
      --su-muted: #A1A1AA;
      --su-border: #2E2E3A;
      --su-input-bg: #1E1E27;
      --su-btn-neutral-bg: #23232E;
      --su-btn-neutral-text: #FAF9F6;
      --su-yellow: #FFD21E;
      --su-red: #EB3B24;
    }

    body.theme-light {
      --su-bg: #FAF9F6;
      --su-card: #FFFFFF;
      --su-text: #111111;
      --su-muted: #475569;
      --su-border: #111111;
      --su-input-bg: #F8FAFC;
      --su-btn-neutral-bg: #FFFFFF;
      --su-btn-neutral-text: #111111;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      background-color: var(--su-bg);
      color: var(--su-text);
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    .font-comic { font-family: 'Luckiest Guy', cursive; letter-spacing: 0.03em; }
    .su-card { background-color: var(--su-card); border-radius: 20px; }
    .border-su-border { border-color: var(--su-border); }
    .su-muted { color: var(--su-muted); }
    .su-text-strong { color: var(--su-text); }

    body.theme-light .text-\[\#FFD21E\] {
      color: #D97706;
    }
    body.theme-light .bg-\[\#FFD21E\] .text-\[\#FFD21E\],
    body.theme-light #gameplayTimerValue {
      color: #D97706;
    }
    body.theme-light #gameplayTimerValue.text-\[\#EB3B24\] {
      color: #EB3B24;
    }

    .su-input {
      background-color: var(--su-input-bg);
      color: var(--su-text);
      border: 2px solid var(--su-border);
      outline: none;
    }
    .su-input:focus {
      border-color: var(--su-yellow);
    }

    .su-btn {
      border-radius: 12px;
      cursor: pointer;
      transition: transform 0.12s ease, opacity 0.12s ease, background-color 0.12s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }
    .su-btn:hover { transform: translateY(-1px); }
    .su-btn:active { transform: translateY(1px); }

    .su-btn-yellow {
      background-color: var(--su-yellow);
      color: #111111;
      border: 2px solid #111111;
    }

    .su-btn-neutral {
      background-color: var(--su-btn-neutral-bg);
      color: var(--su-btn-neutral-text);
      border: 2px solid var(--su-border);
    }

    .su-btn-danger {
      background-color: var(--su-red);
      color: #FFFFFF;
      border: 2px solid #111111;
    }

    .nav-pill {
      padding: 8px 13px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 800;
      color: var(--su-muted);
      transition: all 0.15s ease;
      white-space: nowrap;
    }
    .nav-pill:hover {
      color: var(--su-text);
      background-color: var(--su-btn-neutral-bg);
    }
    .nav-pill.nav-active {
      background-color: var(--su-yellow);
      color: #111111;
    }

    /* SweetAlert2 Styling */
    .swal2-popup.speakup-swal-popup {
      border: 3px solid #111111 !important;
      border-radius: 20px !important;
      font-family: 'Plus Jakarta Sans', sans-serif !important;
      background: #FFFFFF !important;
      color: #111111 !important;
    }
    .swal2-title.speakup-swal-title {
      font-family: 'Luckiest Guy', cursive !important;
      color: #111111 !important;
      letter-spacing: 0.04em !important;
    }
    .swal2-confirm.speakup-swal-confirm {
      background-color: #FFD21E !important;
      color: #111111 !important;
      border: 2.5px solid #111111 !important;
      border-radius: 12px !important;
      font-weight: 900 !important;
      padding: 10px 22px !important;
      margin: 4px !important;
      cursor: pointer !important;
    }
    .swal2-cancel.speakup-swal-cancel {
      background-color: #F1F5F9 !important;
      color: #111111 !important;
      border: 2.5px solid #111111 !important;
      border-radius: 12px !important;
      font-weight: 800 !important;
      padding: 10px 20px !important;
      margin: 4px !important;
      cursor: pointer !important;
    }
  </style>
</head>
<body class="theme-dark antialiased selection:bg-[#FFD21E] selection:text-black">

  <!-- Top Navigation Bar (Disembunyikan saat Fullscreen Presentation Mode) -->
  <header id="speakUpTopNav" class="border-b-2 border-su-border sticky top-0 z-40" style="background-color: var(--su-bg);">
    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" class="su-btn su-btn-neutral px-3 py-1.5 text-xs font-extrabold" title="Kembali ke MainYuk!">
          ← Portal
        </a>
        <button onclick="SpeakUpApp.navigate('home')" class="flex items-center gap-2 text-left cursor-pointer">
          <span class="px-2.5 py-1 rounded-lg bg-[#FFD21E] text-black font-comic text-lg leading-none border-2 border-black">
            SPEAK UP!
          </span>
          <span class="hidden sm:inline text-xs font-extrabold su-muted">Think Fast. Speak Smart.</span>
        </button>
      </div>

      <nav class="flex items-center gap-1 overflow-x-auto max-w-full py-1">
        <button data-nav="home" onclick="SpeakUpApp.navigate('home')" class="nav-pill">Home</button>
        <button data-nav="selection" onclick="SpeakUpApp.navigate('selection')" class="nav-pill">Game Modes</button>
        <button data-nav="setup" onclick="SpeakUpApp.navigate('setup')" class="nav-pill">Setup</button>
        <button data-nav="players" onclick="SpeakUpApp.navigate('players')" class="nav-pill">Players</button>
        <button data-nav="gameplay" onclick="SpeakUpApp.navigate('gameplay')" class="nav-pill">Gameplay</button>
        <button data-nav="leaderboard" onclick="SpeakUpApp.navigate('leaderboard')" class="nav-pill">Leaderboard</button>
        <button data-nav="library" onclick="SpeakUpApp.navigate('library')" class="nav-pill">Library</button>
        <button data-nav="settings" onclick="SpeakUpApp.navigate('settings')" class="nav-pill">Settings</button>
      </nav>

      <button onclick="SpeakUpApp.toggleSound()" class="su-btn su-btn-neutral px-3 py-1.5 text-xs font-extrabold shrink-0">
        <span id="navSoundIcon">🔊</span>
        <span id="navSoundText" class="hidden md:inline">Sound On</span>
      </button>
    </div>
  </header>

  <!-- Main App Stage -->
  <main id="speakUpRoot" class="px-4 pb-16">
    <!-- Rendered dynamically by SpeakUpApp -->
  </main>

  <!-- Modular Game Engine Scripts -->
  <script src="{{ asset('js/speak-up/data.js') }}"></script>
  <script src="{{ asset('js/speak-up/sound.js') }}"></script>
  <script src="{{ asset('js/speak-up/app.js') }}"></script>
</body>
</html>
