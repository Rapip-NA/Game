<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAIN YUK! — Portal Game Web Seru Anti Gabut</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

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
                    <a href="#kategori" class="hover:text-cobalt transition-colors">KATEGORI</a>
                    <a href="#koleksi" class="hover:text-cobalt transition-colors">KOLEKSI GAME</a>
                    <a href="#ulasan" class="hover:text-cobalt transition-colors">ULASAN</a>
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
            <a href="#kategori" class="block py-2 px-3 font-extrabold text-sm rounded-xl hover:bg-white border border-transparent hover:border-ink">KATEGORI</a>
            <a href="#koleksi" class="block py-2 px-3 font-extrabold text-sm rounded-xl hover:bg-white border border-transparent hover:border-ink">KOLEKSI GAME</a>
            <a href="#ulasan" class="block py-2 px-3 font-extrabold text-sm rounded-xl hover:bg-white border border-transparent hover:border-ink">ULASAN</a>
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
                            Kumpulan mini-game web ringan penuh warna yang siap bikin hari-harimu makin asyik—langsung main di browser tanpa ribet!
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
                                KOPI + GAME = MANTAP ☕🎮
                            </p>
                        </div>
                    </div>

                    <!-- Tengah: HALO, GAMERS! & Deskripsi Portal -->
                    <div class="lg:col-span-5 text-white space-y-4 text-center lg:text-left">
                        <h2 class="font-comic text-3xl sm:text-4xl lg:text-5xl tracking-wide text-white">
                            HALO, <span class="text-sunny">GAMERS!</span>
                        </h2>
                        <p class="text-blue-50 text-sm sm:text-base leading-relaxed font-medium">
                            <strong>MainYuk</strong> adalah markas digital buat kamu yang butuh hiburan kilat di sela-sela kesibukan. Semua game di sini dirancang ringan, penuh warna, dan bisa langsung dimainkan tanpa perlu install aplikasi apa pun.
                        </p>
                        <p class="text-blue-100 text-sm sm:text-base leading-relaxed">
                            Kami percaya game terbaik adalah yang bikin kamu senyum, tertantang, dan gampang diajak mabar bareng teman!
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

                    <!-- Kanan: Kartu Notepad Kertas Bergaris Miring "KENAPA MAIN DI SINI?" -->
                    <div class="lg:col-span-4">
                        <div class="lined-paper border-[3px] border-ink rounded-2xl p-6 shadow-[6px_6px_0px_#111111] rotate-1 hover:rotate-0 transition-transform relative">
                            <h3 class="font-comic text-xl sm:text-2xl text-ink mb-4 tracking-wide">
                                KENAPA MAIN DI SINI?
                            </h3>

                            <ul class="space-y-3 font-doodle text-lg sm:text-xl text-ink font-bold">
                                <li class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-sunny border-2 border-ink flex items-center justify-center text-sm shrink-0">⚡</span>
                                    <span>Tanpa Download (Anti Lemot)</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-pastelPink border-2 border-ink flex items-center justify-center text-sm shrink-0">🎁</span>
                                    <span>Gratis 100% Selamanya</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-cobalt text-white border-2 border-ink flex items-center justify-center text-sm shrink-0">📱</span>
                                    <span>Ringan di HP & PC</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-vermilion text-white border-2 border-ink flex items-center justify-center text-sm shrink-0">🔥</span>
                                    <span>Update Game Tiap Minggu</span>
                                </li>
                            </ul>

                            <!-- Doodle Hati Merah di Sudut Kanan Bawah -->
                            <svg class="w-9 h-9 absolute bottom-3 right-4 rotate-12" viewBox="0 0 36 36" fill="none">
                                <path d="M18 31C18 31 5 22 5 12.5C5 8.5 8 5.5 11.8 5.5C14.3 5.5 16.6 6.9 18 9C19.4 6.9 21.7 5.5 24.2 5.5C28 5.5 31 8.5 31 12.5C31 22 18 31 18 31Z" fill="#EB3B24" stroke="#111111" stroke-width="2.5"/>
                            </svg>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 4. KATEGORI GAME (4 KARTU WARNA-WARNI BERJEJER) -->
        <section id="kategori" class="py-16 md:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-8 items-center">
                
                <!-- Kolom Judul Kiri "KATEGORI GAME" -->
                <div class="lg:col-span-3 text-center lg:text-left space-y-3">
                    <h2 class="font-comic text-4xl sm:text-5xl text-ink leading-none -rotate-2 inline-block">
                        KATEGORI<br>GAME
                    </h2>
                    <!-- Garis Bawah Doodle -->
                    <svg class="w-36 h-4 mx-auto lg:mx-0" viewBox="0 0 140 14" fill="none">
                        <path d="M3 10C45 3 95 3 137 9" stroke="#0B44D6" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                    <p class="text-sm font-semibold text-slate-600">
                        Klik salah satu kartu kategori untuk langsung menyaring game favoritmu!
                    </p>
                    <!-- Doodle Mahkota -->
                    <svg class="w-10 h-8 mx-auto lg:mx-0 text-ink" viewBox="0 0 44 32" fill="none">
                        <path d="M4 26L8 8L18 18L22 6L26 18L36 8L40 26H4Z" fill="#FFD21E" stroke="#111111" stroke-width="2.5" stroke-linejoin="round"/>
                    </svg>
                </div>

                <!-- 4 Kartu Kategori Berwarna -->
                <div class="lg:col-span-9 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    <!-- Kartu 1 (Kuning): AKSI & REFLEKS -->
                    <button onclick="selectCategoryAndScroll('Aksi & Refleks')" class="sticker-card bg-sunny text-ink rounded-3xl p-5 text-left flex flex-col justify-between min-h-[235px] cursor-pointer group">
                        <div class="w-14 h-14 rounded-2xl bg-white border-2 border-ink flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none">
                                <polygon points="18,2 5,18 15,18 13,30 27,13 17,13" fill="#FFD21E" stroke="#111111" stroke-width="2.5" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base tracking-tight uppercase">AKSI & REFLEKS</h3>
                            <p class="text-xs font-semibold text-ink/85 mt-1.5 leading-relaxed">
                                Uji kecepatan jari dan ketangkasan matamu dalam tantangan serba cepat.
                            </p>
                        </div>
                        <div class="mt-4 flex justify-end font-black text-lg group-hover:translate-x-1 transition-transform">→</div>
                    </button>

                    <!-- Kartu 2 (Merah-Oranye): TEKA-TEKI & LOGIKA -->
                    <button onclick="selectCategoryAndScroll('Teka-Teki & Logika')" class="sticker-card bg-vermilion text-white rounded-3xl p-5 text-left flex flex-col justify-between min-h-[235px] cursor-pointer group">
                        <div class="w-14 h-14 rounded-2xl bg-white border-2 border-ink flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none">
                                <path d="M16 4C11 4 7 8 7 13C7 16.5 9 19.2 11.5 21H20.5C23 19.2 25 16.5 25 13C25 8 21 4 16 4Z" fill="#FFD21E" stroke="#111111" stroke-width="2.5"/>
                                <rect x="12" y="21" width="8" height="5" rx="2" fill="#FFFFFF" stroke="#111111" stroke-width="2.5"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base tracking-tight uppercase">TEKA-TEKI & LOGIKA</h3>
                            <p class="text-xs font-semibold text-white/90 mt-1.5 leading-relaxed">
                                Asah otak dengan tebakan angka, memori pola, dan puzzle penuh kejutan.
                            </p>
                        </div>
                        <div class="mt-4 flex justify-end font-black text-lg group-hover:translate-x-1 transition-transform">→</div>
                    </button>

                    <!-- Kartu 3 (Biru Cobalt): ARKADE KLASIK -->
                    <button onclick="selectCategoryAndScroll('Arkade Klasik')" class="sticker-card bg-cobalt text-white rounded-3xl p-5 text-left flex flex-col justify-between min-h-[235px] cursor-pointer group">
                        <div class="w-14 h-14 rounded-2xl bg-white border-2 border-ink flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none">
                                <rect x="4" y="10" width="24" height="14" rx="7" fill="#F9B4C4" stroke="#111111" stroke-width="2.5"/>
                                <path d="M10 15V19M8 17H12" stroke="#111111" stroke-width="2.2" stroke-linecap="round"/>
                                <circle cx="21" cy="16" r="1.8" fill="#111111"/>
                                <circle cx="24" cy="18" r="1.8" fill="#111111"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base tracking-tight uppercase">ARKADE KLASIK</h3>
                            <p class="text-xs font-semibold text-blue-100 mt-1.5 leading-relaxed">
                                Nostalgia mesin dingdong retro dengan kontrol simpel yang bikin ketagihan.
                            </p>
                        </div>
                        <div class="mt-4 flex justify-end font-black text-lg group-hover:translate-x-1 transition-transform">→</div>
                    </button>

                    <!-- Kartu 4 (Kuning): BALAPAN & SKOR -->
                    <button onclick="selectCategoryAndScroll('Balapan & Skor')" class="sticker-card bg-sunny text-ink rounded-3xl p-5 text-left flex flex-col justify-between min-h-[235px] cursor-pointer group">
                        <div class="w-14 h-14 rounded-2xl bg-white border-2 border-ink flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none">
                                <path d="M9 6H23V13C23 16.9 19.9 20 16 20C12.1 20 9 16.9 9 13V6Z" fill="#EB3B24" stroke="#111111" stroke-width="2.5"/>
                                <path d="M16 20V25M11 26H21" stroke="#111111" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base tracking-tight uppercase">BALAPAN & SKOR</h3>
                            <p class="text-xs font-semibold text-ink/85 mt-1.5 leading-relaxed">
                                Kebut waktu dan kumpulkan poin maksimal untuk memecahkan rekor tertinggi!
                            </p>
                        </div>
                        <div class="mt-4 flex justify-end font-black text-lg group-hover:translate-x-1 transition-transform">→</div>
                    </button>

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
                    SEMUA GAME (3)
                </button>
                <button data-cat="Teka-Teki & Logika" class="filter-pill sticker-btn bg-white text-ink font-extrabold text-xs px-4 py-2 rounded-full shrink-0">
                    🧩 TEKA-TEKI & LOGIKA
                </button>
                <button data-cat="Aksi & Refleks" class="filter-pill sticker-btn bg-white text-ink font-extrabold text-xs px-4 py-2 rounded-full shrink-0">
                    ⚡ AKSI & TIM
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

        <!-- 6. TESTIMONI PEMAIN (BANNER MERAH-ORANYE LEBAR PENUH) -->
        <section id="ulasan" class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-vermilion border-[3px] border-ink rounded-[36px] p-6 sm:p-10 shadow-[8px_8px_0px_#111111]">
                <div class="grid lg:grid-cols-12 gap-8 items-center">
                    
                    <!-- Kiri: Tulisan KATA PEMAIN + Garis Zig-Zag Kuning -->
                    <div class="lg:col-span-3 text-center lg:text-left space-y-3">
                        <h2 class="font-comic text-4xl sm:text-5xl text-white leading-none -rotate-2 inline-block">
                            KATA<br>PEMAIN
                        </h2>
                        <!-- Garis Zig-Zag Kuning -->
                        <svg class="w-36 h-6 mx-auto lg:mx-0" viewBox="0 0 140 24" fill="none">
                            <path d="M4 16L24 6L44 18L64 6L84 18L104 6L124 18L136 10" stroke="#FFD21E" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="text-xs sm:text-sm font-bold text-white/95">
                            Ribuan pemain udah ketagihan main di sela jam istirahat!
                        </p>
                    </div>

                    <!-- Kanan: 3 Kartu Testimoni Putih Berbingkai Hitam -->
                    <div class="lg:col-span-9 grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Testimoni 1 -->
                        <div class="bg-white border-[3px] border-ink rounded-2xl p-5 shadow-[4px_4px_0px_#111111] flex flex-col justify-between">
                            <p class="text-xs sm:text-sm font-bold text-ink leading-relaxed">
                                “Penyelamat banget pas lagi nunggu kelas atau istirahat kerja! Tinggal klik langsung main tanpa kuota jebol.”
                            </p>
                            <div class="flex items-center gap-3 mt-4 pt-3 border-t-2 border-slate-100">
                                <div class="w-10 h-10 rounded-full bg-sunny border-2 border-ink flex items-center justify-center font-comic text-base text-ink shrink-0">
                                    DR
                                </div>
                                <div>
                                    <p class="font-extrabold text-xs text-ink">DIMAS R.</p>
                                    <p class="text-[11px] font-semibold text-slate-500">MAHASISWA & GAMER KASUAL</p>
                                </div>
                            </div>
                        </div>

                        <!-- Testimoni 2 -->
                        <div class="bg-white border-[3px] border-ink rounded-2xl p-5 shadow-[4px_4px_0px_#111111] flex flex-col justify-between">
                            <p class="text-xs sm:text-sm font-bold text-ink leading-relaxed">
                                “Desain webnya ceria banget kayak komik! Game Super Password sama Susun Urutan seru banget dimainkan bareng teman.”
                            </p>
                            <div class="flex items-center gap-3 mt-4 pt-3 border-t-2 border-slate-100">
                                <div class="w-10 h-10 rounded-full bg-pastelPink border-2 border-ink flex items-center justify-center font-comic text-base text-ink shrink-0">
                                    NA
                                </div>
                                <div>
                                    <p class="font-extrabold text-xs text-ink">NADIA A.</p>
                                    <p class="text-[11px] font-semibold text-slate-500">ILLUSTRATOR FREELANCE</p>
                                </div>
                            </div>
                        </div>

                        <!-- Testimoni 3 -->
                        <div class="bg-white border-[3px] border-ink rounded-2xl p-5 shadow-[4px_4px_0px_#111111] flex flex-col justify-between">
                            <p class="text-xs sm:text-sm font-bold text-ink leading-relaxed">
                                “Sering adu skor tertinggi bareng teman sekantor tiap jam makan siang. Simpel tapi kompetitif banget!”
                            </p>
                            <div class="flex items-center gap-3 mt-4 pt-3 border-t-2 border-slate-100">
                                <div class="w-10 h-10 rounded-full bg-cobalt border-2 border-ink flex items-center justify-center font-comic text-base text-white shrink-0">
                                    RP
                                </div>
                                <div>
                                    <p class="font-extrabold text-xs text-ink">REZA P.</p>
                                    <p class="text-[11px] font-semibold text-slate-500">UI/UX DESIGNER</p>
                                </div>
                            </div>
                        </div>
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
                        <li><a href="#kategori" class="hover:text-sunny transition-colors">Kategori</a></li>
                        <li><a href="#koleksi" class="hover:text-sunny transition-colors">Koleksi Game</a></li>
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
                url: "{{ route('games.siapa-kembaranku.host') }}",
                desc: "Game sosial multi-device untuk kelas! Scan QR rahasia temanmu lewat HP dan tebak siapa kembaran logomu.",
                art: `<svg viewBox="0 0 180 110" class="w-full h-full"><rect x="25" y="22" width="60" height="66" rx="14" fill="#FFD21E" stroke="#111111" stroke-width="2.5"/><text x="55" y="65" text-anchor="middle" font-size="34">🎭</text><rect x="95" y="22" width="60" height="66" rx="14" fill="#0B44D6" stroke="#111111" stroke-width="2.5"/><text x="125" y="65" text-anchor="middle" font-size="34">🔍</text><circle cx="90" cy="55" r="16" fill="#FFFFFF" stroke="#111111" stroke-width="2.5"/><text x="90" y="60" text-anchor="middle" font-family="Plus Jakarta Sans" font-weight="900" font-size="11" fill="#111111">QR</text></svg>`
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
            grid.innerHTML = filtered.map(game => `
                <article class="group cursor-pointer" onclick="window.location.href='${game.url}'">
                    <!-- Poster Card -->
                    <div class="sticker-card ${game.cardBg} ${game.textColor} rounded-3xl p-5 h-60 flex flex-col justify-between relative overflow-hidden">
                        <div class="flex items-center justify-between z-10">
                            <span class="px-2.5 py-1 rounded-full bg-white text-ink border-2 border-ink text-[10px] font-extrabold uppercase">
                                ${game.tagText}
                            </span>
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
                        <a href="${game.url}" class="text-xs font-extrabold underline decoration-2 underline-offset-2 text-ink group-hover:text-vermilion">
                            MAIN →
                        </a>
                    </div>
                </article>
            `).join("");
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
        });
    </script>
</body>
</html>