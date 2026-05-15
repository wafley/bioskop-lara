<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            [
                'title' => 'Joker',
                'description' => 'Arthur Fleck adalah seorang pria dengan gangguan mental yang hidup di tengah kerasnya kehidupan Gotham City. Bekerja sebagai badut sewaan, ia sering mengalami penolakan, hinaan, dan kekerasan dari lingkungan sekitarnya. Dalam pencariannya akan pengakuan dan kebahagiaan, Arthur perlahan kehilangan kendali atas dirinya. Serangkaian peristiwa tragis dan tekanan hidup yang terus menerus mendorongnya berubah menjadi sosok kriminal ikonik yang dikenal sebagai Joker, simbol kekacauan dan pemberontakan terhadap masyarakat yang tidak adil.',
                'genre' => ['thriller', 'drama'],
                'director' => 'todd phillips',
                'cast' => ['joaquin phoenix', 'robert de niro', 'zazie beetz'],
                'release_date' => '04-10-2019',
                'duration' => 122,
                'poster' => 'joker.jpg',
                'status' => 'now_showing',
            ],
            [
                'title' => 'Parasite',
                'description' => 'Keluarga Kim hidup dalam kemiskinan di sebuah apartemen sempit di Seoul. Kesempatan datang ketika putra sulung mereka berhasil mendapatkan pekerjaan sebagai tutor di rumah keluarga Park yang kaya raya. Dengan kecerdikan dan tipu daya, seluruh anggota keluarga Kim perlahan menyusup ke kehidupan keluarga Park dengan identitas palsu. Namun, rencana mereka yang tampak sempurna mulai runtuh ketika rahasia gelap terungkap, memicu konflik tak terduga yang mengubah kehidupan kedua keluarga tersebut secara drastis.',
                'genre' => ['thriller', 'drama'],
                'director' => 'bong joon-ho',
                'cast' => ['song kang-ho', 'lee sun-kyun', 'cho yeo-jeong'],
                'release_date' => '30-05-2019',
                'duration' => 132,
                'poster' => 'parasite.jpg',
                'status' => 'now_showing',
            ],
            [
                'title' => 'Spiderman: No Way Home',
                'description' => 'Setelah identitasnya sebagai Spider-Man terbongkar ke publik, kehidupan Peter Parker berubah drastis. Ia tidak lagi bisa menjalani hidup normal, dan orang-orang terdekatnya ikut terancam. Dalam upaya memperbaiki keadaan, Peter meminta bantuan Doctor Strange untuk membuat semua orang melupakan identitasnya. Namun, mantra yang gagal justru membuka pintu multiverse, menghadirkan musuh-musuh berbahaya dari dimensi lain. Peter harus menghadapi konsekuensi besar dan belajar arti pengorbanan sebagai seorang pahlawan sejati.',
                'genre' => ['action', 'adventure', 'sci_fi'],
                'director' => 'jon watts',
                'cast' => ['tom holland', 'zendaya', 'benedict cumberbatch'],
                'release_date' => '17-12-2021',
                'duration' => 148,
                'poster' => 'spiderman-no-way-home.jpg',
                'status' => 'now_showing',
            ],
            [
                'title' => 'The Batman',
                'description' => 'Di tahun-tahun awalnya sebagai pelindung Gotham, Bruce Wayne menghadapi salah satu ancaman paling berbahaya dalam kariernya: The Riddler, seorang pembunuh berantai yang meninggalkan teka-teki rumit di setiap aksinya. Saat menyelidiki serangkaian pembunuhan yang menargetkan elit kota, Batman menemukan jaringan korupsi yang melibatkan tokoh-tokoh penting Gotham, termasuk masa lalu keluarganya sendiri. Dengan bantuan Catwoman dan Komisaris Gordon, Batman harus mengungkap kebenaran sebelum kota tenggelam dalam kekacauan.',
                'genre' => ['action', 'crime', 'mystery'],
                'director' => 'matt reeves',
                'cast' => ['robert pattinson', 'zoë kravitz', 'paul dano'],
                'release_date' => '04-03-2022',
                'duration' => 176,
                'poster' => 'the-batman.jpg',
                'status' => 'now_showing',
            ],
            [
                'title' => 'The Conjuring 2',
                'description' => 'Ed dan Lorraine Warren, pasangan penyelidik paranormal terkenal, kembali menghadapi salah satu kasus paling menakutkan dalam karier mereka. Mereka melakukan perjalanan ke Enfield, Inggris, untuk membantu seorang ibu tunggal dan anak-anaknya yang mengalami gangguan supranatural di rumah mereka. Ketika kejadian aneh berubah menjadi teror yang mengerikan, keluarga tersebut menjadi target kekuatan jahat yang kuat. Warrens harus mempertaruhkan keselamatan mereka sendiri untuk mengungkap misteri dan mengalahkan entitas jahat yang mengancam jiwa.',
                'genre' => ['horror', 'mystery', 'thriller'],
                'director' => 'james wan',
                'cast' => ['vera farmiga', 'patrick wilson', 'madison wolfe'],
                'release_date' => '10-06-2016',
                'duration' => 134,
                'poster' => 'the-conjuring-2.jpeg',
                'status' => 'now_showing',
            ],
            [
                'title' => 'The Godfather',
                'description' => 'Film ini mengisahkan keluarga mafia Corleone yang berkuasa di Amerika Serikat, dipimpin oleh Don Vito Corleone. Ketika bisnis keluarga menghadapi ancaman dari kelompok kriminal lain dan konflik internal, putranya Michael Corleone yang awalnya ingin hidup normal terpaksa terlibat dalam dunia kejahatan. Seiring waktu, Michael berubah menjadi sosok yang kejam dan penuh perhitungan, mengambil alih kepemimpinan keluarga. Kisah ini menggambarkan kekuasaan, pengkhianatan, dan harga yang harus dibayar untuk mempertahankan kehormatan keluarga.',
                'genre' => ['crime', 'drama'],
                'director' => 'francis ford coppola',
                'cast' => ['marlon brando', 'al pacino', 'james caan'],
                'release_date' => '24-03-1972',
                'duration' => 175,
                'poster' => 'the-godfather.jpg',
                'status' => 'now_showing',
            ],
        ];

        $totalMovies = count($movies);

        $nowShowingCount = round($totalMovies * 0.6);
        $comingSoonCount = round($totalMovies * 0.2);
        $endedCount = $totalMovies - $nowShowingCount - $comingSoonCount;

        $statuses = array_merge(
            array_fill(0, $nowShowingCount, 'now_showing'),
            array_fill(0, $comingSoonCount, 'coming_soon'),
            array_fill(0, $endedCount, 'ended')
        );

        shuffle($statuses);

        foreach ($movies as $index => $movie) {
            $movie['status'] = $statuses[$index];
            Movie::create($movie);
        }
    }
}
