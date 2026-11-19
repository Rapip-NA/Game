<?php

namespace App\Services;

class SusunUrutanService
{
    /**
     * Preset themes with their sequenced steps.
     *
     * @var list<array{theme: string, steps: list<string>}>
     */
    protected const THEMES = [
        [
            'theme' => 'Membuat Kopi',
            'steps' => ['Siapkan cangkir', 'Giling biji kopi', 'Panaskan air', 'Seduh kopi', 'Aduk rata', 'Tuang ke cangkir', 'Minum santai'],
        ],
        [
            'theme' => 'Urutan Tata Surya',
            'steps' => ['Matahari', 'Merkurius', 'Venus', 'Bumi', 'Mars', 'Jupiter', 'Saturnus'],
        ],
        [
            'theme' => 'Naik Pesawat',
            'steps' => ['Beli tiket', 'Check-in bandara', 'Pemeriksaan bagasi', 'Menunggu di boarding gate', 'Masuk ke pesawat', 'Pesawat lepas landas', 'Mendarat di tujuan'],
        ],
        [
            'theme' => 'Membuat Nasi Goreng',
            'steps' => ['Siapkan wajan', 'Haluskan bumbu', 'Panaskan minyak & tumis', 'Masukkan telur & nasi', 'Beri kecap & garam', 'Aduk rata', 'Sajikan di piring'],
        ],
        [
            'theme' => 'Perjalanan Hidup Manusia',
            'steps' => ['Bayi', 'Balita', 'Anak-anak', 'Remaja', 'Dewasa muda', 'Dewasa', 'Lanjut usia'],
        ],
        [
            'theme' => 'Alur Proyek Tim',
            'steps' => ['Brainstorming ide', 'Tentukan tujuan', 'Bagi tugas anggota', 'Eksekusi pekerjaan', 'Evaluasi & review', 'Revisi hasil', 'Presentasi akhir'],
        ],
        [
            'theme' => 'Siklus Kupu-kupu',
            'steps' => ['Telur menempel', 'Telur menetas', 'Ulat makan daun', 'Ulat menjadi kepompong', 'Metamorfosis dalam kokon', 'Kupu-kupu keluar', 'Terbang bebas'],
        ],
        [
            'theme' => 'Bangun Pagi & Berangkat Kerja',
            'steps' => ['Alarm berbunyi', 'Bangun tidur', 'Mandi pagi', 'Pakai pakaian kerja', 'Sarapan pagi', 'Persiapkan kendaraan', 'Berangkat kerja'],
        ],
    ];

    /**
     * Get all preset themes.
     *
     * @return list<array{theme: string, steps: list<string>}>
     */
    public function getThemes(): array
    {
        return self::THEMES;
    }

    /**
     * Get a randomized selection of themes for rounds.
     *
     * @return list<array{theme: string, steps: list<string>}>
     */
    public function getRandomThemes(int $count = 3): array
    {
        $pool = self::THEMES;
        shuffle($pool);

        return array_slice($pool, 0, min($count, count($pool)));
    }
}
