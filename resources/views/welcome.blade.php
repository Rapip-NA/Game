<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAIN YUK! — Portal Game Web Seru Anti Gabut</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icon.png') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons & SweetAlert2 -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #FAFAFA;
            color: #111111;
            overflow-x: hidden;
        }

        /* Gaya Tulisan Komik / Doodle Tebal */
        .font-comic {
            font-family: 'Luckiest Guy', cursive;
            letter-spacing: 0.03em;
        }

        .font-doodle {
            font-family: 'Patrick Hand', cursive;
        }

        /* Neo-Brutalism / Sticker Cards */
        .sticker-card {
            border: 3px solid #111111;
            box-shadow: 5px 5px 0px #111111;
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
        }

        .sticker-card:hover {
            transform: translate(-3px, -3px) rotate(-0.5deg);
            box-shadow: 8px 8px 0px #111111;
        }

        .sticker-btn {
            border: 2.5px solid #111111;
            box-shadow: 3px 3px 0px #111111;
            transition: all 0.15s ease;
        }

        .sticker-btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0px #111111;
        }

        .sticker-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 0px 0px 0px #111111;
        }

        /* SweetAlert2 Neo-Brutalism Customization */
        div:where(.swal2-container) .swal2-popup.mainyuk-swal {
            border: 3.5px solid #111111 !important;
            border-radius: 28px !important;
            box-shadow: 6px 6px 0px #111111 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background: #FFFDF9 !important;
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
            border-radius: 16px !important;
            box-shadow: 3.5px 3.5px 0px #111111 !important;
            font-weight: 900 !important;
            font-family: 'Luckiest Guy', cursive !important;
            font-size: 1.05rem !important;
            padding: 12px 28px !important;
            letter-spacing: 0.03em !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }
        div:where(.swal2-container) .swal2-confirm.mainyuk-swal-confirm:hover {
            transform: translate(-2px, -2px) !important;
            box-shadow: 5px 5px 0px #111111 !important;
        }

        /* Latar Kertas Bergaris Notepad */
        .lined-paper {
            background-color: #FFFFFF;
            background-image: 
                linear-gradient(to right, rgba(235, 59, 36, 0.2) 1px, transparent 1px),
                repeating-linear-gradient(transparent, transparent 31px, #CBD5E1 31px, #CBD5E1 32px);
            background-size: 36px 100%, 100% 32px;
            background-position: 28px 0, 0 8px;
        }

        /* Animasi Melayang Halus untuk Stiker */
        @keyframes wiggleFloat {
            0%, 100% { transform: translateY(0px) rotate(-2deg); }
            50% { transform: translateY(-6px) rotate(2deg); }
        }
        .animate-sticker {
            animation: wiggleFloat 4s ease-in-out infinite;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #FAFAFA; border-left: 2px solid #111111; }
        ::-webkit-scrollbar-thumb { background: #0B44D6; border: 2px solid #111111; border-radius: 999px; }
    </style>
</head>
<body class="antialiased selection:bg-sunny selection:text-ink">

    <!-- 1. NAVBAR -->
    <header class="sticky top-0 z-40 bg-paper/95 backdrop-blur-md border-b-2 border-ink">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">
                
                <!-- Logo MAIN YUK dengan Smiley Doodle -->
                <a href="#beranda" class="flex items-center gap-2 group shrink-0">
                    <div class="leading-none">
                        <span class="font-comic text-2xl sm:text-3xl text-ink block tracking-wide">MAIN</span>
                        <span class="font-comic text-2xl sm:text-3xl text-ink flex items-center gap-1 -mt-1">
                            YUK
                            <!-- Smiley Doodle SVG -->
                            <svg class="w-7 h-7 inline-block group-hover:rotate-12 transition-transform" viewBox="0 0 36 36" fill="none">
                                <circle cx="18" cy="18" r="15" fill="#FFD21E" stroke="#111111" stroke-width="2.8"/>
                                <circle cx="13" cy="14" r="2" fill="#111111"/>
                                <circle cx="23" cy="14" r="2" fill="#111111"/>
                                <path d="M11 21C13 25 23 25 25 21" stroke="#111111" stroke-width="2.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden lg:flex items-center gap-6 text-xs font-extrabold tracking-wider text-ink">
                    <a href="#beranda" class="text-cobalt underline decoration-2 underline-offset-4">BERANDA</a>
                    <a href="#tentang" class="hover:text-cobalt transition-colors">TENTANG</a>
                    <a href="#koleksi" class="hover:text-cobalt transition-colors">KOLEKSI GAME</a>
                    <a href="#saran-game" class="hover:text-cobalt transition-colors">SARAN GAME</a>
                </nav>

                <!-- Tombol Kuning GAS MAIN -> -->
                <div class="flex items-center gap-3">
                    <a href="#koleksi" class="sticker-btn bg-sunny text-ink font-doodle font-bold text-base sm:text-lg px-4 sm:px-5 py-2 rounded-2xl -rotate-1 hover:rotate-0 flex items-center gap-1.5">
                        GAS MAIN <span class="font-sans font-black">→</span>
                    </a>

                    <!-- Mobile Menu Toggle -->
                    <button id="mobileMenuBtn" aria-label="Menu" class="lg:hidden sticker-btn bg-white p-2.5 rounded-xl text-ink">
                        <i data-lucide="menu" class="w-5 h-5 stroke-[2.5]"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobileMenu" class="hidden lg:hidden bg-sunny border-t-2 border-ink px-4 py-4 space-y-2">
            <a href="#beranda" class="block py-2 px-3 font-extrabold text-sm rounded-xl hover:bg-white border border-transparent hover:border-ink">BERANDA</a>
            <a href="#tentang" class="block py-2 px-3 font-extrabold text-sm rounded-xl hover:bg-white border border-transparent hover:border-ink">TENTANG</a>
            <a href="#koleksi" class="block py-2 px-3 font-extrabold text-sm rounded-xl hover:bg-white border border-transparent hover:border-ink">KOLEKSI GAME</a>
            <a href="#saran-game" class="block py-2 px-3 font-extrabold text-sm rounded-xl hover:bg-white border border-transparent hover:border-ink">SARAN GAME</a>
        </div>
    </header>

    <main>
        <!-- 2. HERO SECTION -->
        <section id="beranda" class="relative pt-10 pb-0 md:pt-14 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-8 items-end">
                    
                    <!-- Kolom Kiri: Headline Ekspresif & CTA -->
                    <div class="lg:col-span-6 pb-10 lg:pb-16 relative z-10 text-center lg:text-left">
                        
                        <!-- Ornamen Starburst Merah Kiri Atas -->
                        <svg class="w-11 h-11 text-vermilion mx-auto lg:mx-0 mb-2 animate-spin" style="animation-duration: 18s;" viewBox="0 0 48 48" fill="none">
                            <path d="M24 4V14M24 34V44M4 24H14M34 24H44M10 10L17 17M31 31L38 38M38 10L31 17M17 31L10 38" stroke="#EB3B24" stroke-width="3.5" stroke-linecap="round"/>
                            <circle cx="24" cy="24" r="3" fill="#EB3B24"/>
                        </svg>

                        <h1 class="font-comic text-5xl sm:text-6xl xl:text-[74px] leading-[0.98] text-ink uppercase">
                            GAMES <span class="text-cobalt">SERU</span><br>
                            ANTI GABUT<span class="text-vermilion">.</span>
                        </h1>

                        <p class="mt-5 text-base sm:text-lg font-semibold text-slate-700 max-w-md mx-auto lg:mx-0 leading-relaxed">
                            Tempat untuk mengumpulkan, menyimpan, dan menjelajahi berbagai ide game yang ingin dimainkan. Temukan inspirasi baru, dan wujudkan keseruan bermain dalam satu tempat!
                        </p>

                        <!-- Tombol Merah PILIH GAME -> & Link TENTANG KAMI -->
                        <div class="mt-7 flex flex-wrap items-center justify-center lg:justify-start gap-5">
                            <a href="#koleksi" class="sticker-btn bg-vermilion text-white font-extrabold text-sm sm:text-base px-6 py-3.5 rounded-full flex items-center gap-2.5 tracking-wide">
                                PILIH GAME
                                <span class="text-lg leading-none">→</span>
                            </a>

                            <a href="#tentang" class="font-extrabold text-sm text-ink underline decoration-2 underline-offset-4 hover:text-cobalt transition-colors">
                                TENTANG KAMI
                            </a>
                        </div>

                        <!-- Ornamen Petir Kuning di Kiri Bawah -->
                        <div class="hidden sm:block mt-6 ml-4">
                            <svg class="w-10 h-12 -rotate-12" viewBox="0 0 44 54" fill="none">
                                <polygon points="24,2 4,30 20,30 16,52 40,22 23,22" fill="#FFD21E" stroke="#111111" stroke-width="3" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Ilustrasi Karakter Gamer, Balon Kata & Doodle -->
                    <div class="lg:col-span-6 relative flex justify-center items-end pt-4">
                        
                        <!-- Balon Kata Hitam "YUK MAIN!" -->
                        <div class="absolute top-0 left-2 sm:left-8 z-20 animate-sticker">
                            <div class="bg-ink text-white px-5 py-3 rounded-[28px] border-2 border-ink shadow-[4px_4px_0px_#FFD21E] relative">
                                <p class="font-comic text-xl sm:text-2xl tracking-wide leading-none text-sunny">YUK</p>
                                <p class="font-comic text-xl sm:text-2xl tracking-wide leading-none text-white">MAIN!</p>
                                <!-- Ekor Balon Kata -->
                                <svg class="w-6 h-6 absolute -bottom-4 right-5 text-ink" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M2 0 L22 0 L18 20 Z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Karakter Merah Bermata Kartun di Kanan Atas -->
                        <div class="absolute top-4 right-2 sm:right-6 z-20 animate-bounce" style="animation-duration: 3s;">
                            <svg class="w-20 h-20 sm:w-24 sm:h-24" viewBox="0 0 100 100" fill="none">
                                <path d="M50 5L61 26L84 18L76 41L96 52L75 63L82 86L59 77L48 96L38 76L15 84L23 61L4 49L25 38L18 15L41 24L50 5Z" fill="#EB3B24" stroke="#111111" stroke-width="3" stroke-linejoin="round"/>
                                <circle cx="42" cy="48" r="8" fill="#FFFFFF" stroke="#111111" stroke-width="2.5"/>
                                <circle cx="58" cy="48" r="8" fill="#FFFFFF" stroke="#111111" stroke-width="2.5"/>
                                <circle cx="44" cy="48" r="3.5" fill="#111111"/>
                                <circle cx="56" cy="48" r="3.5" fill="#111111"/>
                                <path d="M44 63Q50 68 56 63" stroke="#111111" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>

                        <!-- Badge Doodle "SERU TANPA INSTALL" -->
                        <div class="absolute bottom-24 right-0 sm:right-4 z-20 bg-white border-2 border-ink px-3.5 py-2 rounded-2xl rotate-6 shadow-[4px_4px_0px_#0B44D6]">
                            <p class="font-doodle font-bold text-base sm:text-lg leading-tight text-ink text-center">
                                SERU TANPA<br>
                                <span class="px-2 py-0.5 border-2 border-ink rounded-full inline-block bg-sunny text-sm">INSTALL!</span>
                            </p>
                        </div>

                        <!-- Ilustrasi Utama Karakter Gamer -->
                        <div class="relative z-10 w-72 sm:w-80 md:w-96 flex justify-center items-end">
                            <img 
                                src="{{ asset('img/ava.png') }}" 
                                alt="MainYuk Gamer Avatar" 
                                class="w-full h-auto object-contain drop-shadow-[0_8px_0_#111111] pointer-events-none select-none hover:scale-105 transition-transform duration-300"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. ABOUT BANNER (BLOK BIRU COBALT LEBAR PENUH DENGAN SUDUT MEMBULAT) -->
        <section id="tentang" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
            <div class="bg-cobalt border-[3px] border-ink rounded-[36px] p-6 sm:p-10 lg:p-12 shadow-[8px_8px_0px_#111111] relative">
                
                <!-- Klip Kertas Hitam di Atas Kanan -->
                <div class="hidden md:block absolute -top-7 right-24 z-30">
                    <svg class="w-12 h-14" viewBox="0 0 48 56" fill="none">
                        <path d="M14 22V12C14 6.5 18.5 2 24 2C29.5 2 34 6.5 34 12V22" stroke="#111111" stroke-width="4"/>
                        <path d="M8 22H40L44 48H4L8 22Z" fill="#111111" stroke="#FFFFFF" stroke-width="2"/>
                    </svg>
                </div>

                <div class="grid lg:grid-cols-12 gap-8 items-center">
                    
                    <!-- Kiri: Kartu Polaroid / Stiker Miring + Label Stiker Kuning -->
                    <div class="lg:col-span-3 flex flex-col items-center">
                        <div class="relative bg-white p-3.5 pb-8 rounded-2xl border-[3px] border-ink shadow-[5px_5px_0px_#111111] -rotate-3 hover:rotate-0 transition-transform w-52">
                            <!-- Selotip Merah di Atas -->
                            <div class="w-14 h-5 bg-vermilion/90 border-2 border-ink absolute -top-3 left-1/2 -translate-x-1/2 rotate-2"></div>
                            
                            <!-- Ilustrasi Gamepad & Bunga Smiley -->
                            <div class="h-36 bg-cobalt/15 border-2 border-ink rounded-xl flex items-center justify-center p-3">
                                <svg class="w-24 h-24" viewBox="0 0 100 100" fill="none">
                                    <circle cx="50" cy="24" r="14" fill="#FFFFFF" stroke="#111111" stroke-width="3"/>
                                    <circle cx="74" cy="38" r="14" fill="#FFFFFF" stroke="#111111" stroke-width="3"/>
                                    <circle cx="70" cy="66" r="14" fill="#FFFFFF" stroke="#111111" stroke-width="3"/>
                                    <circle cx="30" cy="66" r="14" fill="#FFFFFF" stroke="#111111" stroke-width="3"/>
                                    <circle cx="26" cy="38" r="14" fill="#FFFFFF" stroke="#111111" stroke-width="3"/>
                                    <circle cx="50" cy="48" r="18" fill="#FFD21E" stroke="#111111" stroke-width="3"/>
                                    <circle cx="44" cy="44" r="2.2" fill="#111111"/>
                                    <circle cx="56" cy="44" r="2.2" fill="#111111"/>
                                    <path d="M43 52Q50 58 57 52" stroke="#111111" stroke-width="2.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Label Stiker Kuning "KOPI + GAME = MANTAP" -->
                        <div class="bg-sunny border-[2.5px] border-ink px-4 py-2 rounded-xl shadow-[4px_4px_0px_#111111] rotate-2 -mt-4 z-10">
                            <p class="font-doodle font-bold text-base sm:text-lg text-ink leading-tight flex items-center gap-1.5">
                                P Ice Breaking!
                            </p>
                        </div>
                    </div>

                    <!-- Tengah: HALO, GAMERS! & Deskripsi Portal -->
                    <div class="lg:col-span-5 text-white space-y-4 text-center lg:text-left">
                        <h2 class="font-comic text-3xl sm:text-4xl lg:text-5xl tracking-wide text-white">
                            HALO, <span class="text-sunny">SOBAT!</span>
                        </h2>
                        <p class="text-blue-50 text-sm sm:text-base leading-relaxed font-medium">
                            <strong>MainYuk</strong> Tempat kumpulan game dan ice breaking seru untuk menemani sela-sela kesibukan belajar. Dirancang untuk mencairkan suasana, membangun keakraban, dan menghadirkan keseruan di tengah kegiatan belajar maupun aktivitas bersama.
                        </p>
                        <p class="text-blue-100 text-sm sm:text-base leading-relaxed">
                            Kami percaya game terbaik adalah yang bikin kamu senyum, tertantang, dan gampang diajak maen bareng teman!
                        </p>
                        <div class="pt-2 flex items-center justify-center lg:justify-start gap-4">
                            <a href="#koleksi" class="sticker-btn bg-white text-ink font-extrabold text-xs sm:text-sm px-6 py-3 rounded-full tracking-wide">
                                JELAJAHI SEMUA GAME
                            </a>
                            <!-- Panah Doodle Putih -->
                            <svg class="w-12 h-8 text-white hidden sm:block" viewBox="0 0 60 36" fill="none">
                                <path d="M4 12C22 6 38 14 52 28M52 28L40 28M52 28L50 16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Kanan: Kartu Notepad Kertas Bergaris Miring -->
                    <div class="lg:col-span-4">
                        <div class="lined-paper border-[3px] border-ink rounded-2xl p-6 shadow-[6px_6px_0px_#111111] rotate-1 hover:rotate-0 transition-transform relative">
                            <h3 class="font-comic text-xl sm:text-2xl text-ink mb-4 tracking-wide">
                                DIBUAT KARENA GABUT NAMPUNG IDE DIMANA 🙂
                            </h3>

                            
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 5. KOLEKSI GAME PILIHAN ("GAME PILIHAN" + FILTER + TOMBOL GAME ACAK) -->
        <section id="koleksi" class="py-10 md:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b-2 border-ink">
                <div class="flex items-center gap-3">
                    <h2 class="font-comic text-3xl sm:text-4xl text-ink tracking-wide">GAME PILIHAN</h2>
                    <span class="px-3 py-1 rounded-full bg-sunny border-2 border-ink font-extrabold text-xs">SIAP DIMAINKAN!</span>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Pencarian Cepat -->
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-ink absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input 
                            type="text" 
                            id="searchInput" 
                            placeholder="Cari nama game..." 
                            class="bg-white border-2 border-ink rounded-full pl-9 pr-4 py-2 text-xs sm:text-sm font-bold text-ink placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cobalt"
                        >
                    </div>

                    <!-- Tombol GAME ACAK -> -->
                    <button onclick="openRandomGame()" class="sticker-btn bg-cobalt text-white font-extrabold text-xs sm:text-sm px-4 py-2.5 rounded-full flex items-center gap-1.5">
                        🎲 GAME ACAK <span>→</span>
                    </button>
                </div>
            </div>

            <!-- Filter Pills -->
            <div id="filterBar" class="flex items-center gap-2.5 overflow-x-auto py-5">
                <button data-cat="Semua" class="filter-pill active sticker-btn bg-ink text-white font-extrabold text-xs px-4 py-2 rounded-full shrink-0">
                    SEMUA GAME (5)
                </button>
                <button data-cat="Teka-Teki & Logika" class="filter-pill sticker-btn bg-white text-ink font-extrabold text-xs px-4 py-2 rounded-full shrink-0">
                    🧩 TEKA-TEKI & LOGIKA
                </button>
                <button data-cat="Aksi & Refleks" class="filter-pill sticker-btn bg-white text-ink font-extrabold text-xs px-4 py-2 rounded-full shrink-0">
                    ⚡ AKSI & TIM
                </button>
                <button data-cat="Speaking" class="filter-pill sticker-btn bg-white text-ink font-extrabold text-xs px-4 py-2 rounded-full shrink-0">
                    🎤 SPEAKING
                </button>
            </div>

            <!-- Grid Kartu Game Poster Pop-Art -->
            <div id="gamesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-2">
                <!-- Diisi otomatis via JavaScript -->
            </div>

            <!-- Empty State jika pencarian kosong -->
            <div id="emptyState" class="hidden text-center py-14 bg-white border-[3px] border-ink rounded-3xl mt-4">
                <p class="font-comic text-2xl text-ink">WADUH, GAME TIDAK DITEMUKAN!</p>
                <p class="text-sm font-semibold text-slate-600 mt-1">Coba kata kunci lain atau tampilkan semua koleksi game seru kami.</p>
                <button onclick="resetFilter()" class="mt-4 sticker-btn bg-sunny text-ink font-extrabold text-xs px-5 py-2.5 rounded-full">
                    TAMPILKAN SEMUA GAME
                </button>
            </div>
        </section>

        <!-- 6. KOTAK KASIH SARAN GAME (CTA KE HALAMAN SARAN) -->
        <section id="saran-game" class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-vermilion border-[3.5px] border-ink rounded-[36px] p-8 sm:p-12 shadow-[8px_8px_0px_#111111] relative overflow-hidden text-white">
                
                <!-- Ornamen Kartun Background -->
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-sunny rounded-full border-4 border-ink pointer-events-none opacity-40"></div>
                <div class="absolute top-4 right-12 hidden sm:block animate-bounce" style="animation-duration: 3.5s;">
                    <span class="text-5xl">💡</span>
                </div>

                <div class="max-w-3xl relative z-10 space-y-5 text-center sm:text-left">
                    <span class="px-3.5 py-1 rounded-full bg-sunny text-ink border-2 border-ink text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-[2px_2px_0_#111]">
                        <span>🚀</span> KOTAK SARAN & IDE GAME
                    </span>

                    <h2 class="font-comic text-4xl sm:text-5xl lg:text-6xl text-white leading-tight drop-shadow-[2px_2px_0_#111]">
                        PUNYA IDE GAME SERU?<br>
                        <span class="text-sunny underline decoration-ink decoration-wavy decoration-2">KASIH TAHU KAMI!</span>
                    </h2>

                    <p class="text-base sm:text-lg font-bold text-white/95 max-w-2xl leading-relaxed">
                        Mau game seperti apa lagi yang ingin kamu mainkan bareng teman atau kelas di MainYuk? Klik tombol di bawah untuk kirimkan konsep game impianmu secara gratis dan tanpa ribet!
                    </p>

                    <div class="pt-2 flex flex-wrap items-center justify-center sm:justify-start gap-4">
                        <a 
                            href="{{ route('game-suggestions.create') }}" 
                            class="sticker-btn bg-sunny hover:bg-yellow-400 text-ink font-comic text-lg sm:text-2xl px-8 py-4 rounded-2xl inline-flex items-center gap-3 tracking-wide"
                        >
                            <span>KASIH SARAN GAME</span>
                            <span class="text-2xl">✨</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. CTA BANNER (BANNER KUNING LEBAR PENUH) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <div class="bg-sunny border-[3px] border-ink rounded-[36px] p-6 sm:p-10 shadow-[8px_8px_0px_#111111] relative overflow-hidden">
                <div class="grid lg:grid-cols-12 gap-6 items-center">
                    
                    <!-- Kiri: Maskot Doodle Biru Bermata Kartun -->
                    <div class="lg:col-span-2 flex justify-center">
                        <svg class="w-24 h-24 sm:w-28 sm:h-28 animate-sticker" viewBox="0 0 100 100" fill="none">
                            <path d="M50 6L62 26L85 18L76 41L95 53L74 64L81 87L58 78L47 96L37 76L14 84L22 61L4 48L25 37L18 14L41 23L50 6Z" fill="#0B44D6" stroke="#111111" stroke-width="3.5" stroke-linejoin="round"/>
                            <circle cx="42" cy="49" r="9" fill="#FFFFFF" stroke="#111111" stroke-width="2.5"/>
                            <circle cx="58" cy="49" r="9" fill="#FFFFFF" stroke="#111111" stroke-width="2.5"/>
                            <circle cx="44" cy="49" r="4" fill="#111111"/>
                            <circle cx="56" cy="49" r="4" fill="#111111"/>
                        </svg>
                    </div>

                    <!-- Tengah: Teks Besar & Tombol Hitam -->
                    <div class="lg:col-span-6 text-center lg:text-left space-y-3">
                        <h2 class="font-comic text-3xl sm:text-4xl lg:text-5xl text-ink leading-none">
                            SIAP KALAHKAN <span class="underline decoration-cobalt decoration-4 underline-offset-4">SKOR TERTINGGI?</span>
                        </h2>
                        <p class="text-sm sm:text-base font-bold text-ink/90">
                            Pilih game pertamamu sekarang dan buktikan seberapa cepat refleksmu!
                        </p>
                        <div class="pt-1">
                            <a href="#koleksi" class="sticker-btn bg-ink text-white font-extrabold text-sm sm:text-base px-7 py-3.5 rounded-full inline-flex items-center gap-2.5">
                                MULAI MAIN SEKARANG <span>→</span>
                            </a>
                        </div>
                    </div>

                    <!-- Kanan: Stiker Gamepad Retro & Stiker Putih Miring "GRATIS SELAMANYA!" -->
                    <div class="lg:col-span-4 flex items-center justify-center gap-3">
                        <!-- Stiker Gamepad Merah Retro -->
                        <div class="bg-white p-3 rounded-2xl border-[3px] border-ink shadow-[4px_4px_0px_#111111] -rotate-6">
                            <svg class="w-20 h-16" viewBox="0 0 80 60" fill="none">
                                <rect x="8" y="14" width="64" height="36" rx="14" fill="#EB3B24" stroke="#111111" stroke-width="3"/>
                                <path d="M24 26V38M18 32H30" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round"/>
                                <circle cx="52" cy="29" r="4" fill="#FFD21E" stroke="#111111" stroke-width="2"/>
                                <circle cx="60" cy="36" r="4" fill="#FFFFFF" stroke="#111111" stroke-width="2"/>
                            </svg>
                        </div>

                        <!-- Stiker Putih Miring "GRATIS SELAMANYA!" -->
                        <div class="bg-white border-[3px] border-ink px-4 py-3 rounded-full shadow-[4px_4px_0px_#111111] rotate-6 text-center">
                            <p class="font-doodle font-bold text-base sm:text-lg leading-none text-ink">
                                GRATIS<br>SELAMANYA!
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- 9. FOOTER (BLOK BIRU COBALT) -->
    <footer class="bg-cobalt border-t-[3px] border-ink text-white pt-14 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 items-start">
                
                <!-- Kolom 1: Logo MAIN YUK & Deskripsi -->
                <div class="lg:col-span-4 space-y-3">
                    <div class="font-comic text-3xl sm:text-4xl text-white leading-none flex items-center gap-2">
                        MAIN YUK
                        <span class="inline-flex w-8 h-8 rounded-full bg-sunny border-2 border-ink items-center justify-center text-ink text-base">😄</span>
                    </div>
                    <p class="text-blue-100 text-sm font-medium max-w-xs leading-relaxed">
                        Portal kumpulan game web kasual & indie penuh keceriaan yang bisa dimainkan gratis kapan saja.
                    </p>
                    <p class="text-xs text-blue-200 pt-2">
                        © 2026 MainYuk Studio. Hak Cipta Dilindungi.
                    </p>
                </div>

                <!-- Kolom 2: Navigasi -->
                <div class="lg:col-span-2 space-y-2.5 text-sm font-bold">
                    <h4 class="text-sunny font-extrabold tracking-wider text-xs uppercase">NAVIGASI</h4>
                    <ul class="space-y-2 text-blue-50">
                        <li><a href="#beranda" class="hover:text-sunny transition-colors">Beranda</a></li>
                        <li><a href="#tentang" class="hover:text-sunny transition-colors">Tentang Kami</a></li>
                        <li><a href="#koleksi" class="hover:text-sunny transition-colors">Koleksi Game</a></li>
                        <li><a href="#saran-game" class="hover:text-sunny transition-colors">Saran Game</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-sunny text-amber-300 transition-colors">Login Admin 🔐</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Info Kontak & Sosial Media -->
                <div class="lg:col-span-3 space-y-2.5 text-sm font-bold">
                    <h4 class="text-sunny font-extrabold tracking-wider text-xs uppercase">TERHUBUNG YUK!</h4>
                    <ul class="space-y-2 text-blue-50">
                        <li class="flex items-center gap-2"><i data-lucide="mail" class="w-4 h-4 text-sunny"></i> halo@mainyuk.id</li>
                        <li class="flex items-center gap-2"><i data-lucide="instagram" class="w-4 h-4 text-sunny"></i> @mainyuk.games</li>
                        <li class="flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4 text-sunny"></i> Jakarta & Bekasi, Indonesia</li>
                    </ul>
                </div>

                <!-- Kolom 4: Stiker Putih Miring "MAKASIH UDAH MAMPIR! ❤️" -->
                <div class="lg:col-span-3 flex justify-start lg:justify-end">
                    <div class="relative bg-white text-ink border-[3px] border-ink rounded-2xl px-6 py-4 shadow-[6px_6px_0px_#111111] -rotate-3 hover:rotate-0 transition-transform">
                        <!-- Selotip Merah -->
                        <div class="w-12 h-4 bg-vermilion border-2 border-ink absolute -top-2.5 right-6 rotate-3"></div>
                        <p class="font-doodle font-bold text-xl sm:text-2xl leading-tight text-center">
                            MAKASIH UDAH<br>MAMPIR! ❤️
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <script>
        // Data Game Pop-Art Warna-Warni: Super Password & Susun Urutan
        const gamesCatalog = [
            {
                id: 'super-password',
                title: "SUPER PASSWORD",
                category: "Teka-Teki & Logika",
                cardBg: "bg-sunny",
                textColor: "text-ink",
                tagText: "TEBAK KATA 2P",
                isMaintenance: {{ ($gameStatuses['super-password']->is_maintenance ?? false) ? 'true' : 'false' }},
                url: "{{ route('games.super-password') }}",
                desc: "Game tebak kata seru 2 pemain! Satu pemberi petunjuk, satu penebak kata rahasia sebelum waktu habis.",
                art: `<svg viewBox="0 0 180 110" class="w-full h-full"><rect x="36" y="22" width="108" height="66" rx="16" fill="#0B44D6" stroke="#111111" stroke-width="3"/><rect x="44" y="30" width="92" height="50" rx="10" fill="#FFFFFF" stroke="#111111" stroke-width="2.5"/><text x="90" y="66" text-anchor="middle" font-family="Luckiest Guy" font-size="34" fill="#EB3B24">A - Z</text><circle cx="138" cy="24" r="14" fill="#FFD21E" stroke="#111111" stroke-width="2.5"/><text x="138" y="30" text-anchor="middle" font-size="14">🔑</text></svg>`
            },
            {
                id: 'susun-urutan',
                title: "SUSUN URUTAN",
                category: "Aksi & Refleks",
                cardBg: "bg-cobalt",
                textColor: "text-white",
                tagText: "GAME TIM 2-5 TIM",
                isMaintenance: {{ ($gameStatuses['susun-urutan']->is_maintenance ?? false) ? 'true' : 'false' }},
                url: "{{ route('games.susun-urutan') }}",
                desc: "Game tim tanpa bicara! Tiap orang scan 1 kartu rahasia via QR di HP dan berbaris dalam urutan yang tepat.",
                art: `<svg viewBox="0 0 180 110" class="w-full h-full"><rect x="25" y="28" width="36" height="52" rx="8" fill="#FFD21E" stroke="#111111" stroke-width="2.5"/><text x="43" y="62" text-anchor="middle" font-family="Luckiest Guy" font-size="24" fill="#111111">1</text><rect x="72" y="22" width="36" height="58" rx="8" fill="#EB3B24" stroke="#111111" stroke-width="2.5"/><text x="90" y="58" text-anchor="middle" font-family="Luckiest Guy" font-size="24" fill="#FFFFFF">2</text><rect x="119" y="28" width="36" height="52" rx="8" fill="#F9B4C4" stroke="#111111" stroke-width="2.5"/><text x="137" y="62" text-anchor="middle" font-family="Luckiest Guy" font-size="24" fill="#111111">3</text><path d="M43 90H137M137 90L127 84M137 90L127 96" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`
            },
            {
                id: 'siapa-kembaranku',
                title: "SIAPA KEMBARANKU?",
                category: "Aksi & Refleks",
                cardBg: "bg-vermilion",
                textColor: "text-white",
                tagText: "MULTI-DEVICE 5-20P",
                isMaintenance: {{ ($gameStatuses['siapa-kembaranku']->is_maintenance ?? false) ? 'true' : 'false' }},
                url: "{{ route('games.siapa-kembaranku.host') }}",
                desc: "Game sosial multi-device untuk kelas! Scan QR rahasia temanmu lewat HP dan tebak siapa kembaran logomu.",
                art: `<svg viewBox="0 0 180 110" class="w-full h-full"><rect x="25" y="22" width="60" height="66" rx="14" fill="#FFD21E" stroke="#111111" stroke-width="2.5"/><text x="55" y="65" text-anchor="middle" font-size="34">🎭</text><rect x="95" y="22" width="60" height="66" rx="14" fill="#0B44D6" stroke="#111111" stroke-width="2.5"/><text x="125" y="65" text-anchor="middle" font-size="34">🔍</text><circle cx="90" cy="55" r="16" fill="#FFFFFF" stroke="#111111" stroke-width="2.5"/><text x="90" y="60" text-anchor="middle" font-family="Plus Jakarta Sans" font-weight="900" font-size="11" fill="#111111">QR</text></svg>`
            },
            {
                id: 'speaking',
                title: "SPEAKING ROULETTE",
                category: "Speaking",
                cardBg: "bg-cobalt",
                textColor: "text-white",
                tagText: "SPEAKING KELAS",
                isMaintenance: {{ ($gameStatuses['speaking']->is_maintenance ?? false) ? 'true' : 'false' }},
                url: "{{ route('games.speaking') }}",
                desc: "Topik speaking bahasa Indonesia acak muncul tiap 2 menit! Cocok untuk latihan berbicara di kelas.",
                art: `<svg viewBox="0 0 180 110" class="w-full h-full"><rect x="30" y="20" width="120" height="70" rx="18" fill="#FFD21E" stroke="#111111" stroke-width="2.5"/><text x="90" y="65" text-anchor="middle" font-size="44">🎤</text><rect x="60" y="94" width="60" height="8" rx="4" fill="#FFD21E" stroke="#111111" stroke-width="2"/><rect x="85" y="88" width="10" height="8" rx="2" fill="#FFD21E" stroke="#111111" stroke-width="2"/></svg>`
            },
            {
                id: 'speak-up',
                title: "SPEAK UP!",
                category: "Speaking",
                cardBg: "bg-sunny",
                textColor: "text-ink",
                tagText: "8 MODE • GAME SHOW",
                isMaintenance: {{ ($gameStatuses['speak-up']->is_maintenance ?? false) ? 'true' : 'false' }},
                url: "{{ route('games.speak-up') }}",
                desc: "Think Fast. Speak Smart! Game show public speaking interaktif dengan 8 mode tantangan, timer proyektor, & leaderboard.",
                art: `<svg viewBox="0 0 180 110" class="w-full h-full"><rect x="26" y="18" width="128" height="74" rx="16" fill="#111111" stroke="#111111" stroke-width="3"/><text x="90" y="54" text-anchor="middle" font-family="Luckiest Guy" font-size="22" fill="#FFD21E">SPEAK UP!</text><rect x="46" y="64" width="88" height="16" rx="8" fill="#EB3B24" stroke="#FFFFFF" stroke-width="2"/><text x="90" y="76" text-anchor="middle" font-family="Plus Jakarta Sans" font-weight="900" font-size="9" fill="#FFFFFF">8 MODE INTERAKTIF</text></svg>`
            }
        ];

        let activeCategory = "Semua";
        let searchQuery = "";

        // Render Katalog Game
        function renderGames() {
            const grid = document.getElementById("gamesGrid");
            const empty = document.getElementById("emptyState");

            const filtered = gamesCatalog.filter(g => {
                const matchCat = activeCategory === "Semua" || g.category === activeCategory;
                const matchSearch = g.title.toLowerCase().includes(searchQuery.toLowerCase()) || g.category.toLowerCase().includes(searchQuery.toLowerCase());
                return matchCat && matchSearch;
            });

            if (filtered.length === 0) {
                grid.innerHTML = "";
                empty.classList.remove("hidden");
                return;
            }

            empty.classList.add("hidden");
            grid.innerHTML = filtered.map(game => {
                const badge = game.isMaintenance 
                    ? `<span class="px-2.5 py-1 rounded-full bg-vermilion text-white border-2 border-ink text-[10px] font-extrabold uppercase animate-pulse">🔒 MAINTENANCE</span>`
                    : `<span class="px-2.5 py-1 rounded-full bg-white text-ink border-2 border-ink text-[10px] font-extrabold uppercase">${game.tagText}</span>`;
                
                const actionBtn = game.isMaintenance
                    ? `<span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-vermilion text-white border border-ink">MAINTENANCE 🛠️</span>`
                    : `<a href="${game.url}" class="text-xs font-extrabold underline decoration-2 underline-offset-2 text-ink group-hover:text-vermilion">MAIN →</a>`;

                return `
                <article class="group cursor-pointer" onclick="window.location.href='${game.url}'">
                    <!-- Poster Card -->
                    <div class="sticker-card ${game.cardBg} ${game.textColor} rounded-3xl p-5 h-60 flex flex-col justify-between relative overflow-hidden">
                        <div class="flex items-center justify-between z-10">
                            ${badge}
                            <span class="w-8 h-8 rounded-full bg-ink text-white flex items-center justify-center text-xs font-bold group-hover:scale-110 transition-transform shrink-0">
                                ▶
                            </span>
                        </div>

                        <div class="h-24 my-auto group-hover:scale-105 transition-transform flex items-center justify-center">
                            ${game.art}
                        </div>

                        <h3 class="font-comic text-xl sm:text-2xl tracking-wide leading-tight z-10 pb-1">
                            ${game.title}
                        </h3>
                    </div>

                    <!-- Caption di Bawah Poster -->
                    <div class="mt-3 px-1 flex items-center justify-between">
                        <div>
                            <h4 class="font-extrabold text-sm text-ink group-hover:text-cobalt transition-colors">${game.title}</h4>
                            <p class="text-xs font-semibold text-slate-500">${game.category}</p>
                        </div>
                        ${actionBtn}
                    </div>
                </article>
                `;
            }).join("");
        }

        // Filter Kategori
        function setFilter(cat) {
            activeCategory = cat;
            document.querySelectorAll(".filter-pill").forEach(btn => {
                if (btn.dataset.cat === cat) {
                    btn.className = "filter-pill active sticker-btn bg-ink text-white font-extrabold text-xs px-4 py-2 rounded-full shrink-0";
                } else {
                    btn.className = "filter-pill sticker-btn bg-white text-ink font-extrabold text-xs px-4 py-2 rounded-full shrink-0";
                }
            });
            renderGames();
        }

        function selectCategoryAndScroll(cat) {
            setFilter(cat);
            document.getElementById("koleksi").scrollIntoView({ behavior: "smooth" });
        }

        function resetFilter() {
            searchQuery = "";
            document.getElementById("searchInput").value = "";
            setFilter("Semua");
        }

        document.querySelectorAll(".filter-pill").forEach(btn => {
            btn.addEventListener("click", () => setFilter(btn.dataset.cat));
        });

        document.getElementById("searchInput").addEventListener("input", (e) => {
            searchQuery = e.target.value;
            renderGames();
        });

        // Mobile Menu Toggle
        document.getElementById("mobileMenuBtn").addEventListener("click", () => {
            document.getElementById("mobileMenu").classList.toggle("hidden");
        });

        function openRandomGame() {
            const rand = gamesCatalog[Math.floor(Math.random() * gamesCatalog.length)];
            if (rand && rand.url) {
                window.location.href = rand.url;
            }
        }

        // Inisialisasi Awal
        window.addEventListener("DOMContentLoaded", () => {
            renderGames();
            lucide.createIcons();

            @if (session('success'))
                Swal.fire({
                    customClass: {
                        popup: 'mainyuk-swal',
                        title: 'mainyuk-swal-title',
                        confirmButton: 'mainyuk-swal-confirm'
                    },
                    title: 'BERHASIL! 🎉',
                    text: @json(session('success')),
                    icon: 'success',
                    confirmButtonText: 'OKE',
                    buttonsStyling: false
                });
            @elseif (session('error'))
                Swal.fire({
                    customClass: {
                        popup: 'mainyuk-swal',
                        title: 'mainyuk-swal-title',
                        confirmButton: 'mainyuk-swal-confirm'
                    },
                    title: 'PERHATIAN! ⚠️',
                    text: @json(session('error')),
                    icon: 'warning',
                    confirmButtonText: 'OKE',
                    buttonsStyling: false
                });
            @endif
        });
    </script>
</body>
</html>