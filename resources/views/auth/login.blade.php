<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login Admin — MainYuk!</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('img/icon.png') }}">

  <!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Lucide Icons & SweetAlert2 -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
      padding: 24px 16px;
    }

    .font-comic { font-family: 'Luckiest Guy', cursive; }
    .font-doodle { font-family: 'Patrick Hand', cursive; }

    .login-container {
      width: 100%;
      max-width: 440px;
      margin: auto;
    }

    .neo-card {
      background: var(--card);
      border: 3.5px solid var(--line);
      border-radius: 28px;
      padding: 32px 24px;
      box-shadow: 7px 7px 0px var(--line);
      position: relative;
    }

    .neo-badge {
      display: inline-block;
      font-family: 'Patrick Hand', cursive;
      font-size: 16px;
      font-weight: 700;
      padding: 4px 14px;
      border: 2px solid var(--line);
      border-radius: 999px;
      box-shadow: 2.5px 2.5px 0px var(--line);
    }

    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }

    label {
      display: block;
      font-size: 14px;
      font-weight: 800;
      margin-bottom: 8px;
      color: var(--text);
    }

    .neo-input {
      width: 100%;
      padding: 13px 16px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 15px;
      font-weight: 700;
      color: var(--text);
      background: #FFFFFF;
      border: 2.5px solid var(--line);
      border-radius: 14px;
      box-shadow: 3px 3px 0px var(--line);
      outline: none;
      transition: all 0.15s ease;
    }

    .neo-input:focus {
      border-color: var(--cobalt);
      box-shadow: 5px 5px 0px var(--line);
      transform: translate(-1px, -1px);
    }

    .neo-btn {
      width: 100%;
      padding: 14px 20px;
      font-family: 'Luckiest Guy', cursive;
      font-size: 18px;
      letter-spacing: 0.05em;
      color: #FFFFFF;
      background: var(--cobalt);
      border: 3px solid var(--line);
      border-radius: 16px;
      box-shadow: 4px 4px 0px var(--line);
      cursor: pointer;
      transition: transform 0.1s, box-shadow 0.1s;
    }

    .neo-btn:hover {
      transform: translate(-2px, -2px);
      box-shadow: 6px 6px 0px var(--line);
      background: #0937b0;
    }

    .neo-btn:active {
      transform: translate(2px, 2px);
      box-shadow: 1px 1px 0px var(--line);
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-weight: 800;
      font-size: 13px;
      text-decoration: none;
      color: var(--text);
      background: #FFFFFF;
      border: 2.5px solid var(--line);
      border-radius: 999px;
      padding: 8px 16px;
      box-shadow: 3px 3px 0px var(--line);
      transition: all 0.15s ease;
      margin-bottom: 20px;
    }

    .back-btn:hover {
      transform: translate(-2px, -2px);
      box-shadow: 5px 5px 0px var(--line);
      background: var(--sunny);
    }

    .alert {
      padding: 12px 14px;
      border: 2.5px solid var(--line);
      border-radius: 14px;
      box-shadow: 3px 3px 0px var(--line);
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 20px;
      text-align: left;
    }

    .alert-error {
      background: #FEE2E2;
      color: #991B1B;
    }

    /* SweetAlert2 Neo-Brutalism Customization */
    div:where(.swal2-container) .swal2-popup.mainyuk-swal {
      border: 3.5px solid #111111 !important;
      border-radius: 24px !important;
      box-shadow: 6px 6px 0px #111111 !important;
      font-family: 'Plus Jakarta Sans', sans-serif !important;
      background: #FFFFFF !important;
      color: #111111 !important;
      padding: 24px !important;
    }
    div:where(.swal2-container) .swal2-title.mainyuk-swal-title {
      font-family: 'Luckiest Guy', cursive !important;
      letter-spacing: 0.04em !important;
      color: #111111 !important;
      font-size: 1.6rem !important;
    }
    div:where(.swal2-container) .swal2-html-container {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
      font-weight: 700 !important;
      color: #334155 !important;
      font-size: 0.95rem !important;
    }
    div:where(.swal2-container) .swal2-confirm.mainyuk-swal-confirm {
      background-color: #FFD21E !important;
      color: #111111 !important;
      border: 3px solid #111111 !important;
      border-radius: 14px !important;
      box-shadow: 3.5px 3.5px 0px #111111 !important;
      font-weight: 900 !important;
      font-family: 'Luckiest Guy', cursive !important;
      font-size: 1.05rem !important;
      padding: 10px 24px !important;
      letter-spacing: 0.03em !important;
      cursor: pointer !important;
      transition: all 0.15s ease !important;
    }
    div:where(.swal2-container) .swal2-confirm.mainyuk-swal-confirm-danger {
      background-color: #EB3B24 !important;
      color: #FFFFFF !important;
      border: 3px solid #111111 !important;
      border-radius: 14px !important;
      box-shadow: 3.5px 3.5px 0px #111111 !important;
      font-weight: 900 !important;
      font-family: 'Luckiest Guy', cursive !important;
      font-size: 1.05rem !important;
      padding: 10px 24px !important;
      letter-spacing: 0.03em !important;
      cursor: pointer !important;
      transition: all 0.15s ease !important;
    }

    .alert-success {
      background: #DCFCE7;
      color: #166534;
    }

    .tape {
      width: 80px;
      height: 20px;
      background: rgba(255, 210, 30, 0.9);
      border: 2px solid var(--line);
      position: absolute;
      top: -12px;
      left: 50%;
      transform: translateX(-50%) rotate(-2deg);
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div style="text-align: left;">
      <a href="{{ route('home') }}" class="back-btn">
        <i data-lucide="arrow-left" style="width: 16px; height: 16px; stroke-width: 3;"></i>
        KEMBALI KE PORTAL
      </a>
    </div>

    <div class="neo-card">
      <div class="tape"></div>

      <!-- Icon & Title -->
      <div style="text-align: center; margin-bottom: 22px;">
        <div style="width: 60px; height: 60px; border-radius: 18px; border: 3px solid var(--line); background: var(--sunny); display: inline-flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 3px 3px 0 var(--line); margin-bottom: 12px;">
          🔐
        </div>
        <h1 class="font-comic" style="font-size: 32px; letter-spacing: 0.04em; color: var(--text); line-height: 1;">
          LOGIN <span style="color: var(--cobalt);">ADMIN</span>
        </h1>
        <p class="font-doodle" style="font-size: 17px; font-weight: 700; color: var(--muted); margin-top: 6px;">
          Kelola status game & lihat saran ide game dari pemain
        </p>
      </div>

      <!-- Alerts -->
      @if (session('success'))
        <div class="alert alert-success">
          ✅ {{ session('success') }}
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-error">
          ⚠️ {{ session('error') }}
        </div>
      @endif

      @if ($errors->has('credentials'))
        <div class="alert alert-error">
          ❌ {{ $errors->first('credentials') }}
        </div>
      @endif

      <!-- Form -->
      <form action="{{ route('login.post') }}" method="POST">
        @csrf

        <div class="form-group">
          <label for="username">👤 USERNAME ADMIN</label>
          <input 
            type="text" 
            name="username" 
            id="username" 
            class="neo-input" 
            value="{{ old('username') }}" 
            placeholder="Masukkan username..." 
            required 
            autofocus
          >
          @error('username')
            <p style="color: var(--vermilion); font-size: 12px; font-weight: 700; margin-top: 4px;">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group">
          <label for="password">🔑 PASSWORD</label>
          <input 
            type="password" 
            name="password" 
            id="password" 
            class="neo-input" 
            placeholder="Masukkan password..." 
            required
          >
          @error('password')
            <p style="color: var(--vermilion); font-size: 12px; font-weight: 700; margin-top: 4px;">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" class="neo-btn" style="margin-top: 10px;">
          MASUK KE PANEL →
        </button>
      </form>

      <div style="margin-top: 20px; padding-top: 16px; border-top: 2px dashed #E2E8F0; text-align: center;">
        <span class="neo-badge" style="background: #F1F5F9; font-size: 13px;">
          🔒 Akses Khusus Pengelola MainYuk!
        </span>
      </div>
    </div>
  </div>

  <script>
    window.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();

      @if ($errors->has('credentials'))
        Swal.fire({
          customClass: {
            popup: 'mainyuk-swal',
            title: 'mainyuk-swal-title',
            confirmButton: 'mainyuk-swal-confirm-danger'
          },
          title: 'LOGIN GAGAL! ❌',
          text: @json($errors->first('credentials')),
          icon: 'error',
          confirmButtonText: 'COBA LAGI',
          buttonsStyling: false
        });
      @elseif (session('error'))
        Swal.fire({
          customClass: {
            popup: 'mainyuk-swal',
            title: 'mainyuk-swal-title',
            confirmButton: 'mainyuk-swal-confirm-danger'
          },
          title: 'PERHATIAN! ⚠️',
          text: @json(session('error')),
          icon: 'warning',
          confirmButtonText: 'OKE',
          buttonsStyling: false
        });
      @elseif (session('success'))
        Swal.fire({
          customClass: {
            popup: 'mainyuk-swal',
            title: 'mainyuk-swal-title',
            confirmButton: 'mainyuk-swal-confirm'
          },
          title: 'BERHASIL! ✅',
          text: @json(session('success')),
          icon: 'success',
          confirmButtonText: 'OKE',
          buttonsStyling: false
        });
      @endif
    });
  </script>
</body>
</html>
