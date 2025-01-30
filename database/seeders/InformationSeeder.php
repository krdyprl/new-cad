<?php

namespace Database\Seeders;

use App\Models\Information;
use Illuminate\Database\Seeder;

class InformationSeeder extends Seeder
{    public function run()
    {
        $articles = [
            [
                'title' => 'Pameran Keramik Dinoyo Menyambut Hari Kemerdekaan',
                'content' => 'Kampung Keramik Dinoyo menggelar pameran spesial dalam rangka merayakan Hari Kemerdekaan Indonesia ke-78. Pameran ini menampilkan berbagai karya keramik dengan nuansa merah putih yang menarik perhatian pengunjung.

Para pengrajin telah mempersiapkan karya-karya terbaik mereka selama berbulan-bulan. Mulai dari vas bunga dengan motif tradisional Indonesia, hingga genteng berkualitas tinggi yang menjadi andalan produksi lokal.

"Kami bangga bisa turut merayakan kemerdekaan dengan cara kami sendiri, melalui karya seni keramik yang telah menjadi warisan budaya turun temurun," kata Pak Sujono, salah satu pengrajin senior di Kampung Keramik Dinoyo.

Pameran ini berlangsung selama sebulan penuh dan menawarkan diskon khusus untuk produk-produk pilihan. Pengunjung juga dapat menyaksikan langsung proses pembuatan keramik dan bahkan mencoba workshop singkat.',
                'status' => 'published',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'title' => 'Workshop Keramik untuk Anak-Anak: Kreativitas Tanpa Batas',
                'content' => 'Setiap akhir pekan, Kampung Keramik Dinoyo mengadakan workshop khusus untuk anak-anak usia 6-15 tahun. Program ini bertujuan untuk memperkenalkan seni keramik kepada generasi muda dan mengembangkan kreativitas mereka.

Dalam workshop ini, anak-anak akan belajar:
- Teknik dasar membentuk tanah liat
- Cara menggunakan roda putar sederhana
- Proses pewarnaan dengan glasir
- Menghias keramik dengan berbagai motif

"Melihat antusiasme anak-anak sangat menggembirakan. Mereka tidak hanya belajar teknik, tapi juga nilai-nilai budaya dan kesabaran," ungkap Bu Sari, instruktur workshop.

Biaya workshop sangat terjangkau, hanya Rp 50.000 per anak untuk satu sesi 3 jam. Sudah termasuk bahan, peralatan, dan hasil karya yang bisa dibawa pulang setelah proses pengeringan selesai.',
                'status' => 'published',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'title' => 'Inovasi Terbaru: Keramik Ramah Lingkungan dari Dinoyo',
                'content' => 'Para pengrajin Kampung Keramik Dinoyo terus berinovasi dengan mengembangkan keramik ramah lingkungan. Inovasi terbaru ini menggunakan bahan-bahan organik dan proses pembakaran yang lebih efisien.

Tim peneliti dari Universitas Brawijaya bekerja sama dengan pengrajin lokal untuk mengembangkan formula baru yang mengurangi emisi carbon dioxide hingga 30% selama proses produksi.

Keunggulan keramik ramah lingkungan ini:
- Daya tahan yang sama dengan keramik konvensional
- Proses produksi hemat energi
- Menggunakan bahan tambahan dari limbah organik
- Tidak mengandung zat kimia berbahaya

"Ini adalah langkah penting untuk menjaga kelestarian lingkungan tanpa mengurangi kualitas produk," jelas Pak Bambang, ketua kelompok pengrajin.

Produk pertama yang menggunakan teknologi ini adalah genteng ramah lingkungan yang akan mulai dipasarkan bulan depan.',
                'status' => 'published',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'title' => 'Kunjungan Wisatawan Mancanegara Meningkat Drastis',
                'content' => 'Kampung Keramik Dinoyo mencatat peningkatan kunjungan wisatawan mancanegara sebesar 150% dalam 6 bulan terakhir. Mayoritas pengunjung berasal dari Jepang, Korea Selatan, dan Australia.

Data dari Dinas Pariwisata Kota Malang menunjukkan:
- Januari-Juni 2024: 2.500 wisatawan asing
- Periode yang sama tahun lalu: 1.000 wisatawan
- Rata-rata kunjungan per bulan: 400-500 orang

"Mereka sangat tertarik dengan proses pembuatan keramik tradisional dan ingin belajar langsung dari para master," kata Ibu Retno, koordinator wisata.

Untuk mengakomodasi lonjakan wisatawan ini, pihak pengelola telah:
- Menyediakan guide berbahasa Inggris
- Membuat paket wisata khusus
- Menambah fasilitas toilet dan parkir
- Menyiapkan area demo yang lebih luas

Wisatawan asing juga sangat antusias membeli produk keramik sebagai oleh-oleh, dengan rata-rata pembelian Rp 500.000 per orang.',
                'status' => 'published',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'title' => 'Pelestarian Teknik Tradisional di Era Modern',
                'content' => 'Di tengah kemajuan teknologi, Kampung Keramik Dinoyo tetap mempertahankan teknik-teknik tradisional yang telah diwariskan secara turun-temurun. Hal ini menjadi daya tarik tersendiri bagi pengunjung yang ingin melihat keaslian proses pembuatan keramik.

Teknik-teknik tradisional yang masih dipertahankan:

1. Teknik Putar Manual
Menggunakan roda putar yang digerakkan dengan kaki, bukan mesin listrik. Ini memberikan kontrol yang lebih baik dan hasil yang lebih artistik.

2. Pembakaran Tungku Tradisional  
Menggunakan tungku berbahan bakar kayu dan sekam padi yang memberikan karakter khusus pada hasil akhir keramik.

3. Glasir Alami
Memanfaatkan bahan-bahan alami dari lingkungan sekitar untuk membuat glasir dengan warna-warna khas.

"Kami tidak menolak teknologi modern, tapi tetap mempertahankan cara-cara lama karena itu adalah jati diri kami," jelas Pak Mukri, pengrajin generasi ketiga.

Upaya pelestarian ini juga didukung dengan:
- Program magang untuk pemuda
- Dokumentasi proses tradisional
- Pelatihan untuk generasi muda
- Kerjasama dengan lembaga pendidikan',
                'status' => 'published',
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'title' => 'Festival Keramik Nusantara 2024 Segera Dimulai',
                'content' => 'Kampung Keramik Dinoyo akan menjadi tuan rumah Festival Keramik Nusantara 2024 pada bulan November mendatang. Acara akbar ini akan menghadirkan pengrajin keramik dari seluruh Indonesia.

Agenda Festival:
- 1-3 November: Pameran keramik dari 15 daerah
- 4-5 November: Kompetisi membuat keramik
- 6-7 November: Seminar dan workshop
- 8-10 November: Bazaar dan pertunjukan seni

Peserta yang akan hadir meliputi:
- Pengrajin keramik Kasongan (Yogyakarta)
- Sentra keramik Plered (Purwakarta)
- Komunitas keramik Bali
- Pengrajin gerabah Lombok
- Dan masih banyak lagi

"Festival ini akan menjadi ajang silaturahmi dan berbagi ilmu antar pengrajin Nusantara," ungkap panitia.

Masyarakat umum dapat mengikuti berbagai kegiatan dengan tiket yang sangat terjangkau. Tiket terusan untuk 10 hari hanya Rp 100.000, sudah termasuk akses ke semua acara dan workshop gratis.

Pendaftaran workshop sudah dibuka melalui website resmi atau datang langsung ke sekretariat.',
                'status' => 'published',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ]
        ];

        foreach ($articles as $article) {
            Information::create($article);
        }
    }
}
