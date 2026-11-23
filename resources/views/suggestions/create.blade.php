<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasih Saran Game — MainYuk! Game Seru Anti Gabut</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icon.png') }}">

    <!-- Google Fonts: Luckiest Guy, Patrick Hand & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Patrick+Hand&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS (Vite / Fallback CDN) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#FFFDF9',
                        ink: '#111111',
                        cobalt: '#0B44D6',
                        sunny: '#FFD21E',
                        vermilion: '#EB3B24',
                        pastelPink: '#F9B4C4',
                        pastelBlue: '#C2E2FA',
                        pastelGreen: '#C6F6D5',
                    },
                    fontFamily: {
                        comic: ['"Luckiest Guy"', 'cursive'],
                        doodle: ['"Patrick Hand"', 'cursive'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #FFFDF9;
            background-image: radial-gradient(#D4D4D8 1.5px, transparent 1.5px);
            background-size: 24px 24px;
            color: #111111;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sticker-card {
            border: 3.5px solid #111111;
            border-radius: 28px;
            box-shadow: 6px 6px 0px #111111;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .sticker-btn {
            border: 3px solid #111111;
            box-shadow: 4px 4px 0px #111111;
            transition: all 0.15s ease;
        }
        .sticker-btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px #111111;
        }
        .sticker-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 1px 1px 0px #111111;
        }

        /* Rich Editor Content Styling */
        #editorArea:empty:before {
            content: attr(data-placeholder);
            color: #94A3B8;
            pointer-events: none;
            display: block;
        }
        #editorArea ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }
        #editorArea ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }
        #editorArea blockquote {
            border-left: 4px solid #111111;
            padding-left: 0.75rem;
            margin: 0.5rem 0;
            font-style: italic;
            color: #475569;
        }
        #editorArea h2 {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0.75rem 0 0.25rem;
        }
        #editorArea h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0.5rem 0 0.25rem;
        }
        #editorArea b, #editorArea strong {
            font-weight: 800;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased selection:bg-sunny selection:text-ink">

    <!-- Top Navigation -->
    <header class="w-full max-w-4xl mx-auto px-4 pt-6 sm:pt-8">
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}" class="sticker-btn bg-white hover:bg-slate-100 text-ink font-extrabold text-xs sm:text-sm px-4 sm:px-5 py-2.5 rounded-full flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>KEMBALI KE BERANDA</span>
            </a>

            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-vermilion border-2 border-ink"></span>
                <span class="w-3 h-3 rounded-full bg-sunny border-2 border-ink"></span>
                <span class="w-3 h-3 rounded-full bg-cobalt border-2 border-ink"></span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-3xl mx-auto px-4 py-8 flex-1">
        
        <!-- Header Banner Title -->
        <div class="text-center space-y-3 mb-8">
            <span class="px-3.5 py-1 rounded-full bg-sunny text-ink border-2 border-ink text-xs font-black uppercase inline-flex items-center gap-1.5 shadow-[2px_2px_0_#111]">
                <span>💡</span> FORMULIR SARAN GAME
            </span>

            <h1 class="font-comic text-4xl sm:text-5xl lg:text-6xl text-ink leading-[1.05] tracking-wide">
                KASIH SARAN <span class="text-cobalt">GAME BARU</span>!
            </h1>

            <p class="text-sm sm:text-base font-semibold text-slate-700 max-w-xl mx-auto leading-relaxed">
                Punya ide game seru buat dimainkan bareng teman atau kelas? Ceritakan konsepnya di sini. Tim MainYuk akan membaca dan mempertimbangkan idemu!
            </p>
        </div>

        <!-- Alert Success Notification -->
        @if (session('suggestion_success'))
            <div class="sticker-card bg-emerald-100 border-[3.5px] border-ink p-5 mb-8 flex items-start gap-3 text-ink">
                <span class="text-3xl shrink-0">🎉</span>
                <div class="space-y-1">
                    <h3 class="font-comic text-xl text-emerald-900">SUKSES TERKIRIM!</h3>
                    <p class="text-xs sm:text-sm font-bold text-emerald-800 leading-relaxed">
                        {{ session('suggestion_success') }}
                    </p>
                    <a href="{{ route('home') }}" class="inline-block mt-2 font-black text-xs text-cobalt underline hover:text-ink">
                        ← Kembali ke daftar game untuk main sekarang
                    </a>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="sticker-card bg-rose-100 border-[3.5px] border-ink p-5 mb-8 space-y-1.5 text-rose-900">
                <div class="flex items-center gap-2 font-comic text-lg">
                    <span>⚠️</span> ADA KESALAHAN PADA FORMULIR
                </div>
                <ul class="list-disc pl-5 text-xs sm:text-sm font-bold space-y-1 text-rose-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Form Saran Game -->
        <div class="sticker-card bg-white p-6 sm:p-9 relative overflow-hidden">
            <!-- Doodle Aksesoris Pojok -->
            <div class="absolute -top-3 -right-3 w-12 h-12 bg-pastelPink rounded-full border-2 border-ink pointer-events-none opacity-60"></div>

            <form id="formSaranGame" action="{{ route('game-suggestions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- 1. Judul Game (Opsional) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="game_title" class="font-comic text-lg sm:text-xl text-ink flex items-center gap-2">
                            <span>🎮</span> NAMA / JUDUL GAME
                        </label>
                        <span class="text-[11px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-100 border border-slate-300 text-slate-500">
                            OPSIONAL
                        </span>
                    </div>
                    <input 
                        type="text" 
                        name="game_title" 
                        id="game_title" 
                        value="{{ old('game_title') }}"
                        placeholder="Contoh: Sambung Pantun, Tebak Lagu Daerah, Game Debat Kocak..." 
                        maxlength="150"
                        class="w-full bg-slate-50 border-[2.5px] border-ink rounded-2xl px-4 py-3 text-sm sm:text-base font-bold text-ink placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-sunny transition"
                    >
                    <p class="text-[11px] font-semibold text-slate-500">
                        Boleh dikosongkan jika belum kepikiran nama keren gamenya.
                    </p>
                </div>

                <!-- 2. Deskripsi & Konsep Game (Wajib, Rich Text Formatting) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="font-comic text-lg sm:text-xl text-ink flex items-center gap-2">
                            <span>📝</span> KONSEP & CARA MAIN GAME
                            <span class="text-vermilion">*</span>
                        </label>
                        <span class="text-[11px] font-black uppercase px-2 py-0.5 rounded-full bg-sunny border border-ink text-ink">
                            WAJIB DIISI
                        </span>
                    </div>

                    <!-- Toolbar Formatting (Bold, Italic, List, Heading, dll) -->
                    <div class="flex flex-wrap items-center gap-1.5 p-2 bg-slate-100 border-[2.5px] border-ink rounded-xl">
                        <button type="button" onclick="formatDoc('bold')" title="Tebal (Bold)" class="px-2.5 py-1 bg-white border border-ink rounded-lg font-black text-xs hover:bg-sunny transition shadow-[1px_1px_0_#111]">
                            <b>B</b>
                        </button>
                        <button type="button" onclick="formatDoc('italic')" title="Miring (Italic)" class="px-2.5 py-1 bg-white border border-ink rounded-lg font-serif italic text-xs hover:bg-sunny transition shadow-[1px_1px_0_#111]">
                            <i>I</i>
                        </button>
                        <button type="button" onclick="formatDoc('underline')" title="Garis Bawah (Underline)" class="px-2.5 py-1 bg-white border border-ink rounded-lg underline text-xs hover:bg-sunny transition shadow-[1px_1px_0_#111]">
                            <u>U</u>
                        </button>

                        <div class="h-5 w-[1.5px] bg-slate-300 mx-1"></div>

                        <button type="button" onclick="formatDoc('insertUnorderedList')" title="Daftar Bullet" class="px-2.5 py-1 bg-white border border-ink rounded-lg font-bold text-xs flex items-center gap-1 hover:bg-sunny transition shadow-[1px_1px_0_#111]">
                            <span>•</span> List
                        </button>
                        <button type="button" onclick="formatDoc('insertOrderedList')" title="Daftar Angka (Nomor)" class="px-2.5 py-1 bg-white border border-ink rounded-lg font-bold text-xs flex items-center gap-1 hover:bg-sunny transition shadow-[1px_1px_0_#111]">
                            <span>1.</span> List
                        </button>

                        <div class="h-5 w-[1.5px] bg-slate-300 mx-1"></div>

                        <button type="button" onclick="formatDoc('formatBlock', 'h2')" title="Sub-judul" class="px-2.5 py-1 bg-white border border-ink rounded-lg font-black text-xs hover:bg-sunny transition shadow-[1px_1px_0_#111]">
                            H2
                        </button>
                        <button type="button" onclick="formatDoc('formatBlock', 'blockquote')" title="Kutipan" class="px-2.5 py-1 bg-white border border-ink rounded-lg font-bold text-xs hover:bg-sunny transition shadow-[1px_1px_0_#111]">
                            "Quote"
                        </button>
                    </div>

                    <!-- Area Tulis Rich Text (Contenteditable) -->
                    <div 
                        id="editorArea" 
                        contenteditable="true" 
                        data-placeholder="Jelaskan idemu secara bebas! Misalnya:
• Berapa jumlah pemainnya?
• Seperti apa aturan main dan cara menangnya?
• Fitur apa yang bikin game ini seru dan menantang?"
                        class="min-h-[180px] max-h-[420px] overflow-y-auto bg-slate-50 border-[2.5px] border-ink rounded-2xl p-4 text-sm sm:text-base font-medium text-ink focus:outline-none focus:ring-4 focus:ring-sunny transition"
                    >{!! old('description') !!}</div>

                    <!-- Hidden Input untuk form submit -->
                    <input type="hidden" name="description" id="hiddenDescription" required>

                    <p class="text-[11px] font-semibold text-slate-500">
                        Kamu bisa gunakan tombol toolbar di atas untuk menebalkan teks atau membuat poin list.
                    </p>
                </div>

                <!-- 3. Upload Gambar Sketsa / Mockup (Opsional) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="imageInput" class="font-comic text-lg sm:text-xl text-ink flex items-center gap-2">
                            <span>🖼️</span> LAMPIRAN GAMBAR / SKETSA
                        </label>
                        <span class="text-[11px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-100 border border-slate-300 text-slate-500">
                            OPSIONAL
                        </span>
                    </div>

                    <!-- Dropzone Container -->
                    <div id="dropZone" class="border-2 border-dashed border-ink rounded-2xl p-5 bg-slate-50 text-center hover:bg-amber-50/50 transition cursor-pointer relative">
                        <input 
                            type="file" 
                            name="image" 
                            id="imageInput" 
                            accept="image/png, image/jpeg, image/jpg, image/webp, image/gif" 
                            class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10"
                        >
                        
                        <div id="uploadPrompt" class="space-y-1.5">
                            <div class="w-12 h-12 mx-auto rounded-full bg-sunny border-2 border-ink flex items-center justify-center text-xl shadow-[2px_2px_0_#111]">
                                📁
                            </div>
                            <p class="font-extrabold text-sm text-ink">
                                Klik atau seret gambar ke sini
                            </p>
                            <p class="text-xs font-semibold text-slate-500">
                                PNG, JPG, GIF, atau WEBP (Maksimal 5MB)
                            </p>
                        </div>

                        <!-- Preview Area -->
                        <div id="previewArea" class="hidden mt-3 pt-3 border-t-2 border-slate-200">
                            <div class="relative inline-block max-w-full">
                                <img id="previewImg" src="" alt="Preview Gambar" class="max-h-64 rounded-xl border-2 border-ink shadow-[3px_3px_0_#111] object-contain mx-auto">
                                <button type="button" id="removeImgBtn" class="mt-2 text-xs font-black px-3 py-1 bg-vermilion text-white rounded-full border border-ink shadow-[2px_2px_0_#111] hover:bg-rose-700">
                                    ✕ Hapus Gambar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button 
                        type="submit" 
                        id="btnSubmit" 
                        class="w-full sticker-btn bg-sunny hover:bg-yellow-400 text-ink font-comic text-xl sm:text-2xl py-3.5 sm:py-4 rounded-2xl flex items-center justify-center gap-3 tracking-wide"
                    >
                        <span>KIRIM SARAN GAME</span>
                        <span class="text-2xl">🚀</span>
                    </button>
                    <p class="text-center text-xs font-bold text-slate-500 mt-2.5">
                        Identitas pengirim akan otomatis dicatat sebagai <b>Anonim</b> demi kenyamananmu.
                    </p>
                </div>
            </form>
        </div>

    </main>

    <!-- Footer Simple -->
    <footer class="w-full max-w-4xl mx-auto px-4 py-8 text-center text-xs font-bold text-slate-500">
        <p>© {{ date('Y') }} MainYuk! — Tempat Ngumpul & Main Game Seru.</p>
    </footer>

    <script>
        // Init Lucide
        if (window.lucide) {
            lucide.createIcons();
        }

        // Rich Text Formatting using execCommand
        function formatDoc(cmd, value = null) {
            document.execCommand(cmd, false, value);
            const editor = document.getElementById('editorArea');
            editor.focus();
            syncDescription();
        }

        const editor = document.getElementById('editorArea');
        const hiddenInput = document.getElementById('hiddenDescription');
        const form = document.getElementById('formSaranGame');

        function syncDescription() {
            hiddenInput.value = editor.innerHTML.trim();
        }

        editor.addEventListener('input', syncDescription);
        editor.addEventListener('blur', syncDescription);

        // Form Submit Validation
        form.addEventListener('submit', function(e) {
            syncDescription();
            const textContent = editor.innerText.trim();
            if (!textContent) {
                e.preventDefault();
                alert('Tolong ceritain konsep gamenya dulu ya sebelum mengirim!');
                editor.focus();
                return false;
            }
        });

        // Image Preview Handler
        const imageInput = document.getElementById('imageInput');
        const previewArea = document.getElementById('previewArea');
        const previewImg = document.getElementById('previewImg');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const removeImgBtn = document.getElementById('removeImgBtn');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran gambar terlalu besar! Maksimal 5MB.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewArea.classList.remove('hidden');
                    uploadPrompt.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        removeImgBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            imageInput.value = '';
            previewImg.src = '';
            previewArea.classList.add('hidden');
            uploadPrompt.classList.remove('hidden');
        });
    </script>
</body>
</html>
