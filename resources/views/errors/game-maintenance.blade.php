<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Game Sedang Maintenance — MainYuk!</title>

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
      --bg: #FAFAFA;
      --card: #FFFFFF;
      --text: #111111;
      --muted: #475569;
      --line: #111111;
      --cobalt: #0B44D6;
      --sunny: #FFD21E;
      --vermilion: #EB3B24;
      --pink: #F9B4C4;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      min-height: 100vh;
      background-color: var(--bg);
      background-image: radial-gradient(#CBD5E1 1.4px, transparent 1.4px);
      background-size: 22px 22px;
      color: var(--text);
      font-family: 'Plus Jakarta Sans', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 30px 16px;
      text-align: center;
    }

    .font-comic { font-family: 'Luckiest Guy', cursive; }
    .font-doodle { font-family: 'Patrick Hand', cursive; }

    .container {
      width: 100%;
      max-width: 580px;
    }

    .neo-card {
      background: var(--card);
      border: 3.5px solid var(--line);
      border-radius: 32px;
      padding: 40px 24px;
      box-shadow: 8px 8px 0px var(--line);
      position: relative;
    }

    .tape {
      width: 110px;
      height: 24px;
      background: rgba(235, 59, 36, 0.9);
      border: 2px solid var(--line);
      position: absolute;
      top: -14px;
      left: 50%;
      transform: translateX(-50%) rotate(-3deg);
    }

    .neo-badge {
      display: inline-block;
      font-family: 'Luckiest Guy', cursive;
      font-size: 14px;
      letter-spacing: 0.05em;
      padding: 6px 16px;
      border: 2.5px solid var(--line);
      border-radius: 999px;
      box-shadow: 3px 3px 0px var(--line);
      margin-bottom: 18px;
    }

    .neo-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 14px 24px;
      font-family: 'Luckiest Guy', cursive;
      font-size: 16px;
      letter-spacing: 0.04em;
      text-decoration: none;
      color: var(--text);
      background: var(--sunny);
      border: 3px solid var(--line);
      border-radius: 16px;
      box-shadow: 4px 4px 0px var(--line);
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .neo-btn:hover {
      transform: translate(-2px, -2px);
      box-shadow: 6px 6px 0px var(--line);
      background: #facc15;
    }

    .neo-btn:active {
      transform: translate(2px, 2px);
      box-shadow: 1px 1px 0px var(--line);
    }

    .neo-btn-secondary {
      background: #FFFFFF;
      color: var(--text);
    }

    .neo-btn-secondary:hover {
      background: #F1F5F9;
    }

    .illustration {
      width: 100px;
      height: 100px;
      margin: 0 auto 20px;
      border: 3.5px solid var(--line);
      border-radius: 28px;
      background: var(--sunny);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 48px;
      box-shadow: 5px 5px 0px var(--line);
      transform: rotate(2deg);
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="neo-card">
      <div class="tape"></div>

      <!-- Icon & Game Badge -->
      <div class="illustration">
        🛠️
      </div>

      <div>
        <span class="neo-badge" style="background: var(--vermilion); color: #FFFFFF;">
          ⚠️ {{ strtoupper($gameName ?? 'GAME') }}
        </span>
      </div>

      <!-- Main Heading -->
      <h1 class="font-comic" style="font-size: clamp(28px, 6vw, 42px); line-height: 1.1; color: var(--text); margin-bottom: 16px;">
        MAAF YA, GAMENYA LAGI DI MAINTANANCE!
      </h1>

      <!-- User Message -->
      <div style="background: #FFFBEB; border: 2.5px solid var(--line); border-radius: 18px; padding: 18px; box-shadow: 4px 4px 0 var(--line); margin-bottom: 24px;">
        <p class="font-doodle" style="font-size: 21px; font-weight: 700; color: #78350F; line-height: 1.4;">
          “{{ $message ?? 'maaf ya gamenya lagi di maintanance sama admin, jadi gak bisa dipake dlu' }}”
        </p>
      </div>

      <p style="font-size: 14px; font-weight: 700; color: var(--muted); margin-bottom: 28px;">
        Admin sedang memperbarui atau menyempurnakan game ini. Silakan coba game seru lainnya dulu ya!
      </p>

      <!-- Action Buttons -->
      <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
        <a href="{{ route('home') }}" class="neo-btn">
          <i data-lucide="arrow-left" style="width: 18px; height: 18px; stroke-width: 3;"></i>
          KEMBALI KE BERANDA
        </a>
        <a href="{{ route('home') }}#koleksi" class="neo-btn neo-btn-secondary">
          🎲 PILIH GAME LAIN
        </a>
      </div>
    </div>
  </div>

  <script>
    window.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
    });
  </script>
</body>
</html>
