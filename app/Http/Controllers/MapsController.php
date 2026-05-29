<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Kecamatan;
use App\Models\Tempat;
use Illuminate\Http\Request;

class MapsController extends Controller
{
    private const SUPABASE_STORAGE = 'https://rnafixrgoucrplssoqtm.supabase.co/storage/v1/object/public/tempat_images/';

    /**
     * Maps Gabungan (Point + Polygon + Line)
     * GET /maps
     */
    public function index()
    {
        $kategoris  = Kategori::orderBy('id')->get();
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('maps.index', compact('kategoris', 'kecamatans'));
    }

    /**
     * API endpoint — return JSON titik lokasi untuk Leaflet marker
     * GET /api/tempat?kategori=1&rating_min=3&lat=3.59&lng=98.67&radius=5
     */
    public function apiTempat(Request $request)
    {
        $query = Tempat::with(['kategori', 'kecamatan']);

        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->filled('rating_min')) {
            $query->where('review_rating', '>=', (float) $request->rating_min);
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, Tempat> $tempats */
        $tempats = $query->get();

        if ($request->filled('lat') && $request->filled('lng') && $request->filled('radius')) {
            $lat    = (float) $request->lat;
            $lng    = (float) $request->lng;
            $radius = (float) $request->radius;

            $tempats = $tempats->filter(function (Tempat $t) use ($lat, $lng, $radius) {
                return $t->distanceFrom($lat, $lng) <= $radius;
            })->values();
        }

        return response()->json(
            $tempats->map(function (Tempat $t) use ($request) {
                $data = [
                    'id'           => $t->id,
                    'nama_tempat'  => $t->nama_tempat,
                    'detail'       => $t->detail_tempat,
                    'jalan'        => $t->jalan,
                    'kecamatan'    => $t->kecamatan?->nama_kecamatan,
                    'kecamatan_id' => $t->kecamatan_id,
                    'kategori'     => $t->kategori?->nama_kategori,
                    'kategori_id'  => $t->kategori_id,
                    'rating'       => $t->review_rating,
                    'kontak'       => $t->kontak,
                    'media_url'    => $t->media
                                        ? (str_starts_with($t->media, 'http')
                                            ? $t->media
                                            : self::SUPABASE_STORAGE . $t->media)
                                        : null,
                    'latitude'     => $t->latitude,
                    'longitude'    => $t->longitude,
                ];

                if ($request->filled('lat') && $request->filled('lng')) {
                    $data['jarak_km'] = round($t->distanceFrom((float) $request->lat, (float) $request->lng), 2);
                }

                return $data;
            })
        );
    }

    /**
     * API endpoint — return JSON kecamatan + geojson + stats tempat
     * GET /api/kecamatan/polygon
     */
    public function apiKecamatanPolygon()
    {
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();

        $tempats = Tempat::selectRaw('kecamatan_id, kategori_id, COUNT(*) as jumlah')
            ->groupBy('kecamatan_id', 'kategori_id')
            ->get();

        $statsMap = [];
        foreach ($tempats as $row) {
            $statsMap[$row->kecamatan_id][$row->kategori_id] = (int) $row->jumlah;
        }

        $data = $kecamatans->map(function (Kecamatan $kec) use ($statsMap) {
            $byKat = $statsMap[$kec->id] ?? [];
            return [
                'id'             => $kec->id,
                'nama_kecamatan' => $kec->nama_kecamatan,
                'geojson'        => $kec->geojson,
                'stats'          => [
                    'total'            => array_sum($byKat),
                    'kuliner'          => $byKat[1] ?? 0,
                    'wisata'           => $byKat[2] ?? 0,
                    'kesehatan'        => $byKat[3] ?? 0,
                    'kemasyarakatan'   => $byKat[4] ?? 0,
                    'transportasi'     => $byKat[5] ?? 0,
                ],
            ];
        });

        return response()->json($data);
    }
}