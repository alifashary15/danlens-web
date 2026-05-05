<?php

namespace App\Http\Controllers;

use App\Models\Tempat;  

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tempat'         => Tempat::count(),
            'total_kuliner'        => Tempat::where('kategori_id', 1)->count(),
            'total_wisata'         => Tempat::where('kategori_id', 2)->count(),
            'total_kesehatan'      => Tempat::where('kategori_id', 3)->count(),
            'total_kemasyarakatan' => Tempat::where('kategori_id', 4)->count(),
            'total_transportasi'   => Tempat::where('kategori_id', 5)->count(),
        ];

        $team = [
            [
                'nama'   => 'Alif Faishal Ashary',
                'peran'  => 'Project Lead & Web Developer',
                'avatar' => 'AA',
                'warna'  => '#4a7c59',
                'foto'   => 'alif.jpeg',
                'nim'    => '2305181052'
            ],
            [
                'nama'   => 'Hikmal Akbar',
                'peran'  => 'Mobile Developer & Helper',
                'avatar' => 'HA',
                'warna'  => '#4a7c59',
                'foto'   => 'hikmal.png',
                'nim'    => '2305181024'
            ],
            [
                'nama'   => 'Mhd. Ihsan Harianto Harahap',
                'peran'  => 'Mobile Developer & Data Researcher',
                'avatar' => 'IH',
                'warna'  => '#4a7c59',
                'foto'   => 'ihsan.png',
                'nim'    => '2305181096'
            ],
            [
                'nama'   => 'Fadil Givari',
                'peran'  => 'Mobile Developer & Data Researcher',
                'avatar' => 'FG',
                'warna'  => '#4a7c59',
                'foto'   => 'fadil.png',
                'nim'    => '2305181044'
            ],
            [
                'nama'   => 'Feny Mawarni',
                'peran'  => 'Database Designer & Documentation',
                'avatar' => 'FM',
                'warna'  => '#4a7c59',
                'foto'   => 'fenny.png',
                'nim'    => '2305181020'
            ],
            [
                'nama'   => 'Putri Yaumi Askira',
                'peran'  => 'Database Designer & Documentation',
                'avatar' => 'PA',
                'warna'  => '#4a7c59',
                'foto'   => 'putri.png',
                'nim'    => '2305181016'
            ],
        ];

        return view('home.index', compact('stats', 'team'));
    }
}