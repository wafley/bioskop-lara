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
                'description' => 'Seorang pria bermasalah secara mental berubah menjadi sosok penjahat ikonik di Gotham.',
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
                'description' => 'Keluarga miskin menyusup ke rumah keluarga kaya dengan berbagai tipu daya.',
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
                'description' => 'Peter Parker menghadapi multiverse setelah identitasnya terbongkar.',
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
                'description' => 'Batman menghadapi teka-teki mematikan dari The Riddler yang mengancam Gotham.',
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
                'description' => 'Pasangan paranormal Ed dan Lorraine Warren menyelidiki kasus kerasukan di Inggris.',
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
                'description' => 'Kisah keluarga mafia Corleone yang berkuasa di dunia kejahatan Amerika.',
                'genre' => ['crime', 'drama'],
                'director' => 'francis ford coppola',
                'cast' => ['marlon brando', 'al pacino', 'james caan'],
                'release_date' => '24-03-1972',
                'duration' => 175,
                'poster' => 'the-godfather.jpg',
                'status' => 'now_showing',
            ],
        ];

        $statuses = ['coming_soon', 'now_showing', 'ended'];

        foreach ($movies as $movie) {
            $movie['status'] = $statuses[array_rand($statuses)];
            Movie::create($movie);
        }
    }
}
