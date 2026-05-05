<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;
use App\Models\Kecamatan;

class Tempat extends Model
{
    protected $table = 'tempat';

    protected $fillable = [
        'nama_tempat', 'detail_tempat', 'jalan', 'kecamatan_id',
        'latitude', 'longitude', 'kategori_id', 'review_rating',
        'kontak', 'media', 'user_id',
    ];

    protected $casts = [
        'latitude'      => 'float',
        'longitude'     => 'float',
        'review_rating' => 'float',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * URL gambar dari Supabase Storage atau placeholder
     */
    public function getMediaUrlAttribute(): string
    {
        if ($this->media) {
            $storageUrl = config('services.supabase.storage_url');
            return $storageUrl . '/' . $this->media;
        }
        return asset('images/placeholder.jpg');
    }

    /**
     * Hitung jarak (km) dari koordinat user ke tempat ini (Haversine)
     */
    public function distanceFrom(float $lat, float $lng): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($this->latitude - $lat);
        $dLng = deg2rad($this->longitude - $lng);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat)) * cos(deg2rad($this->latitude)) * sin($dLng / 2) ** 2;
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}