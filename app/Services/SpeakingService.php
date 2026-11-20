<?php

namespace App\Services;

class SpeakingService
{
    /**
     * Ratusan topik bahasa Indonesia untuk game Speaking.
     * Dikelompokkan per kategori agar bervariasi setiap sesi.
     *
     * @var array<string, list<string>>
     */
    protected const TOPICS = [
        'Kehidupan Sehari-hari' => [
            'Pagi hari yang menyenangkan',
            'Kebiasaan sarapan keluarga',
            'Rutinitas sebelum tidur',
            'Hal yang paling bikin semangat di pagi hari',
            'Transportasi favorit ke sekolah/kantor',
            'Cara mengatur waktu belajar',
            'Makanan yang selalu ada di rumah',
            'Aktivitas akhir pekan bersama keluarga',
            'Pengalaman naik angkutan umum',
            'Belanja di pasar tradisional',
            'Kebiasaan membersihkan kamar',
            'Hal yang dilakukan saat bosan di rumah',
            'Cara menghemat uang jajan',
            'Membantu orang tua di rumah',
            'Pengalaman memasak pertama kali',
        ],
        'Teknologi & Digital' => [
            'Media sosial yang paling sering dipakai',
            'Dampak HP pada kehidupan remaja',
            'Aplikasi favorit di HP',
            'Pengalaman belajar online',
            'Game online yang pernah dimainkan',
            'Manfaat internet untuk belajar',
            'Bahaya cyberbullying',
            'Konten yang sering ditonton di YouTube',
            'Perbedaan belanja online vs offline',
            'Masa depan teknologi di Indonesia',
            'Kecanduan media sosial',
            'Privasi data di era digital',
            'AI dan dampaknya bagi pelajar',
            'Cara melindungi diri dari penipuan online',
            'Podcast favorit dan apa yang didapat',
        ],
        'Pendidikan & Karier' => [
            'Cita-cita dan alasannya',
            'Mata pelajaran yang paling disukai',
            'Cara belajar yang efektif',
            'Guru yang paling berkesan',
            'Pengalaman ujian yang menegangkan',
            'Perbedaan belajar sendiri vs berkelompok',
            'Mimpi kuliah di mana',
            'Pekerjaan impian 10 tahun lagi',
            'Keterampilan paling penting di abad 21',
            'Pengalaman ikut lomba atau kompetisi',
            'Pentingnya membaca buku',
            'Cara mengatasi malas belajar',
            'Arti sukses bagi saya',
            'Rencana setelah lulus sekolah',
            'Hal yang ingin dipelajari selain di sekolah',
        ],
        'Lingkungan & Alam' => [
            'Dampak sampah plastik di lautan',
            'Cara menjaga lingkungan dari rumah',
            'Pengalaman wisata alam yang tak terlupakan',
            'Kenapa hutan hujan Indonesia harus dijaga',
            'Perubahan iklim dan dampaknya',
            'Cara menghemat air bersih',
            'Keindahan alam Indonesia',
            'Hewan yang terancam punah di Indonesia',
            'Berkebun di rumah',
            'Energi terbarukan untuk masa depan',
            'Dampak polusi udara bagi kesehatan',
            'Pengalaman bersih-bersih lingkungan',
            'Makanan organik vs biasa',
            'Wisata ramah lingkungan',
            'Peran pemuda dalam menjaga alam',
        ],
        'Budaya & Seni' => [
            'Budaya daerah yang paling membanggakan',
            'Musik tradisional Indonesia',
            'Makanan khas daerah asal',
            'Festival budaya yang pernah dihadiri',
            'Pakaian adat dan maknanya',
            'Film Indonesia favorit',
            'Lagu yang punya makna mendalam',
            'Seni yang ingin dipelajari',
            'Perbedaan budaya Indonesia dan luar negeri',
            'Dongeng atau cerita rakyat favorit',
            'Peran budaya dalam membangun karakter',
            'Kesenian lokal yang hampir punah',
            'Pengalaman belajar bahasa daerah',
            'Cara melestarikan budaya di era modern',
            'Kuliner Indonesia di mata dunia',
        ],
        'Sosial & Pertemanan' => [
            'Sahabat sejati itu seperti apa',
            'Cara menyelesaikan konflik dengan teman',
            'Pengalaman punya teman baru',
            'Hal yang membuat persahabatan langgeng',
            'Perbedaan teman online vs offline',
            'Cara mendukung teman yang sedang sedih',
            'Pengalaman kerja kelompok yang seru',
            'Momen persahabatan yang tak terlupakan',
            'Cara menjadi pendengar yang baik',
            'Pentingnya jujur dalam pertemanan',
            'Cara bergaul di lingkungan baru',
            'Toxic friendship dan cara menghadapinya',
            'Teman yang paling banyak pengaruhnya',
            'Cara minta maaf yang tulus',
            'Arti kebersamaan dalam kelompok',
        ],
        'Kesehatan & Olahraga' => [
            'Olahraga favorit dan alasannya',
            'Manfaat olahraga rutin bagi pelajar',
            'Makanan sehat yang tetap enak',
            'Cara menjaga kesehatan mental',
            'Pengalaman sakit dan pelajaran yang diambil',
            'Tidur cukup dan dampaknya ke belajar',
            'Olahraga apa yang ingin dicoba',
            'Diet sehat ala anak muda',
            'Bahaya junk food yang sering diabaikan',
            'Cara menjaga kebersihan diri',
            'Atlet Indonesia yang menginspirasi',
            'Meditasi dan manfaatnya',
            'Kesehatan mata di era layar digital',
            'Cara bangkit setelah kalah dalam pertandingan',
            'Sport competition terheboh yang pernah ditonton',
        ],
        'Impian & Inspirasi' => [
            'Tokoh yang paling menginspirasi',
            'Buku yang mengubah cara pandang',
            'Hal yang ingin dicapai sebelum umur 25',
            'Jika bisa bertemu siapapun di dunia',
            'Mimpi terbesar dalam hidup',
            'Pelajaran hidup dari kesalahan',
            'Kalau jadi pemimpin negara, apa yang diubah',
            'Hal kecil yang membuat bahagia',
            'Cara tetap positif di situasi sulit',
            'Orang yang paling berjasa dalam hidup saya',
            'Kata-kata motivasi yang selalu dipegang',
            'Jika bisa ke satu negara mana yang dipilih',
            'Warisan apa yang ingin ditinggalkan',
            'Hal yang membuat bangga dengan diri sendiri',
            'Perbedaan bermimpi besar vs realistis',
        ],
        'Humor & Santai' => [
            'Hal paling memalukan yang pernah terjadi',
            'Lelucon favorit yang selalu berhasil',
            'Karakter anime atau film yang ingin jadi',
            'Hal aneh yang selalu dilakukan saat sendiri',
            'Makanan yang tidak akan pernah dimakan',
            'Jika jadi superhero, kekuatan apa yang dipilih',
            'Hewan peliharaan impian',
            'Momen paling ngakak bersama teman',
            'Hal random yang sering dipikirkan sebelum tidur',
            'Kalau bisa terbang, pergi ke mana pertama',
            'Makanan yang bikin nagih tapi malu ngakuin',
            'Karakter game yang ingin dimainkan di dunia nyata',
        ],
        'Masa Depan & Inovasi' => [
            'Indonesia di tahun 2050',
            'Pekerjaan yang akan hilang karena AI',
            'Inovasi yang ingin diciptakan',
            'Kendaraan masa depan impian',
            'Peran pemuda dalam inovasi bangsa',
            'Smart city dan kehidupan di dalamnya',
            'Apakah manusia akan tinggal di Mars?',
            'Robot vs manusia di tempat kerja',
            'Pendidikan masa depan seperti apa',
            'Makanan masa depan yang sehat dan murah',
            'Teknologi medis yang mengubah dunia',
            'Dunia tanpa plastik, mungkinkah?',
            'Cara anak muda bisa mengubah dunia',
            'Kolaborasi manusia dan AI',
            'Startup dari Indonesia yang mendunia',
        ],
    ];

    /**
     * Ambil sejumlah topik acak, diambil lintas kategori agar variatif.
     *
     * @return list<array{topic: string, category: string}>
     */
    public function getRandomTopics(int $count = 10): array
    {
        $all = [];

        foreach (self::TOPICS as $category => $topics) {
            foreach ($topics as $topic) {
                $all[] = ['topic' => $topic, 'category' => $category];
            }
        }

        shuffle($all);

        return array_slice($all, 0, min($count, count($all)));
    }

    /**
     * Hitung total jumlah topik yang tersedia.
     */
    public function totalTopics(): int
    {
        return array_sum(array_map(fn (array $t) => count($t), self::TOPICS));
    }
}
