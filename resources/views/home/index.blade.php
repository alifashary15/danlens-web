@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    /* ── HERO ── */
    .hero {
        min-height: 92vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        background: var(--white);
        padding: 80px 40px 120px;
    }
    .hero-wave {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        line-height: 0;
        pointer-events: none;
        z-index: 1;
    }
    .hero-wave svg { display: block; width: 100%; height: auto; }

    /* Panel kanan: blob SVG organik menggantikan clip-path ellipse yang kaku */
    .hero-right-panel {
        position: absolute;
        top: 0; right: -4%;
        width: 58%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        overflow: visible;
    }
    .hero-right-panel svg {
        width: 100%;
        height: 100%;
        display: none;
    }

    .hero-inner {
        position: relative;
        z-index: 2;
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 40px;
        align-items: center;
    }

    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        background: var(--matcha-pale);
        border-radius: 100px;
        font-size: .78rem;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--matcha-deep);
        margin-bottom: 24px;
    }
    .hero-eyebrow .pulse {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: var(--matcha);
        animation: pulse 2s ease-in-out infinite;
    }

    .hero-title {
        font-family: var(--font-display);
        font-size: clamp(2.4rem, 4.5vw, 3.6rem);
        font-weight: 700;
        line-height: 1.12;
        color: var(--ink);
        margin-bottom: 22px;
        letter-spacing: -.02em;
    }
    .hero-title .accent { color: var(--matcha); }

    .hero-desc {
        font-size: 1.05rem;
        color: var(--ink-soft);
        line-height: 1.75;
        max-width: 460px;
        margin-bottom: 36px;
    }

    .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

    .hero-visual {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    .hero-illus-wrap { position: relative; width: 100%; }
    .hero-wave-frame { position: relative; width: 110%; margin-left: -5%; }
    .hero-wave-svg   { width: 100%; height: auto; display: block; }

    .hero-float-badge {
        position: absolute;
        z-index: 4;
        background: rgba(255,255,255,.95);
        border-radius: var(--radius-md);
        padding: 10px 16px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: .8rem;
        font-weight: 600;
        color: var(--ink-mid);
        white-space: nowrap;
        backdrop-filter: blur(8px);
    }
    .hero-float-badge .badge-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        background: var(--matcha-pale);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .hero-float-badge-1 { bottom: 18%; left: -2%; animation: floatY 3s ease-in-out infinite; }
    .hero-float-badge-2 { top: 12%; right: 3%; animation: floatY 3.5s ease-in-out infinite reverse; }

    /* ── STATS ── */
    .stats-bar {
        background: var(--white);
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        padding: 36px 40px;
    }
    .stats-inner {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1px;
    }
    .stat-item { padding: 0 32px; border-right: 1px solid var(--border); text-align: center; }
    .stat-item:first-child { padding-left: 0; }
    .stat-item:last-child  { border-right: none; }
    .stat-number {
        font-family: var(--font-display);
        font-size: 2.4rem;
        font-weight: 700;
        color: var(--matcha-deep);
        line-height: 1;
        margin-bottom: 6px;
    }
    .stat-label {
        font-size: .82rem;
        font-weight: 500;
        color: var(--ink-soft);
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    /* ── FEATURES ── */
    .features { padding: 100px 40px; background: var(--matcha-ghost); }
    .section-header { text-align: center; max-width: 560px; margin: 0 auto 64px; }
    .section-tag {
        display: inline-block;
        padding: 5px 14px;
        background: var(--matcha-pale);
        color: var(--matcha-deep);
        border-radius: 100px;
        font-size: .76rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        margin-bottom: 16px;
    }
    .section-title {
        font-family: var(--font-display);
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--ink);
        line-height: 1.2;
        letter-spacing: -.02em;
        margin-bottom: 14px;
    }
    .section-desc { color: var(--ink-soft); font-size: .95rem; line-height: 1.7; }

    .features-grid {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    .feature-card {
        background: var(--white);
        border-radius: var(--radius-md);
        padding: 36px 32px;
        border: 1px solid var(--border);
        transition: transform .25s, box-shadow .25s;
    }
    .feature-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .feature-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: var(--matcha-pale);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }
    .feature-title {
        font-family: var(--font-display);
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 10px;
    }
    .feature-desc { font-size: .88rem; color: var(--ink-soft); line-height: 1.7; }

    /* ── TEAM ── */
    .team { padding: 100px 40px; background: var(--white); }
    .team-grid {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 24px;
    }
    .team-card {
    text-align: center;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    background: var(--white);
    overflow: hidden;
    transition: transform .25s, box-shadow .25s;
    box-shadow: 0 0 20px rgba(74, 124, 89, 0.25);
}
.team-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 0 40px rgba(74, 124, 89, 0.6); 
}

    .team-photo-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1;
        overflow: hidden;
        background: transparent;
    }
    .team-photo-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        display: block;
        transition: transform .4s ease;
    }
    .team-card:hover .team-photo-wrap img { transform: scale(1.05); }

    .team-photo-wave {
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        line-height: 0;
        pointer-events: none;
    }
    .team-photo-wave svg { width: 100%; height: auto; display: block; }

    .team-avatar-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display);
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--white);
    }
    .team-info   { padding: 20px 20px 28px; }
    .team-name   { font-weight: 700; font-size: 1rem; color: var(--ink); margin-bottom: 5px; }
    .team-role   { font-size: .8rem; color: var(--ink-soft); line-height: 1.5; }
    .team-nim {
    font-size: .75rem;
    color: var(--matcha-mid);
    font-weight: 600;
    margin-top: 4px;
    letter-spacing: .03em;
}

    /* ── CTA ── */
    .cta-section {
        padding: 90px 40px;
        background: linear-gradient(135deg, var(--matcha-deep) 0%, var(--matcha) 100%);
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .cta-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    .cta-inner  { position: relative; }
    .cta-title  {
        font-family: var(--font-display);
        font-size: 2.4rem;
        font-weight: 700;
        color: var(--white);
        margin-bottom: 16px;
        letter-spacing: -.02em;
    }
    .cta-desc   { color: rgba(255,255,255,.75); font-size: 1rem; margin-bottom: 36px; }
    .btn-white  { background: var(--white); color: var(--matcha-deep); box-shadow: 0 4px 20px rgba(0,0,0,.15); }
    .btn-white:hover { background: var(--matcha-ghost); }
</style>
@endpush

{{--
    @keyframes dan @media HARUS di luar @push agar VS Code tidak error.
--}}
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .5; transform: scale(1.4); }
    }
    @keyframes floatY {
        0%, 100% { transform: translateY(0px); }
        50%       { transform: translateY(-8px); }
    }

    @media (max-width: 900px) {
        .hero-inner    { grid-template-columns: 1fr; gap: 48px; }
        .hero-visual   { order: -1; }
        .hero-wave-frame { width: 100%; margin-left: 0; }
        .hero-right-panel { display: none; }
        .stats-inner   { grid-template-columns: repeat(2, 1fr); gap: 24px; }
        .stat-item     { border: none; }
        .features-grid { grid-template-columns: 1fr; }
        .team-grid     { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 540px) {
        .team-grid { grid-template-columns: 1fr; }
    }
</style>

@section('content')

{{-- HERO --}}
<section class="hero">

    {{-- Panel kanan: blob organik SVG (lebih flowing, tidak kaku) --}}
    <div class="hero-right-panel">
        <svg viewBox="0 0 600 900" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="
                M 120,0
                C 220,-20 500,10 580,80
                C 650,150 620,280 610,400
                C 598,530 660,650 620,760
                C 580,860 440,920 300,910
                C 160,900 20,840 10,720
                C -5,590 60,480 50,360
                C 38,230 -10,120 20,50
                C 40,10 80,10 120,0 Z
            " fill="#d6e8db"/>
        </svg>
    </div>

    <div class="hero-inner">

        {{-- KIRI: Teks --}}
        <div class="hero-text">
            <div class="hero-eyebrow">
                <div class="pulse"></div>
                Sistem Informasi Geografis
            </div>
            <h1 class="hero-title">
                Temukan Hal<br>
                <span class="accent">Menarik</span> di Medan
            </h1>
            <p class="hero-desc">
                DanLens memetakan ratusan titik lokasi penting &mdash; kuliner, wisata,
                kesehatan, dan layanan publik &mdash; lengkap dengan informasi,
                rating, dan navigasi berbasis lokasi Anda.
            </p>
            <div class="hero-actions">
                <a href="{{ route('maps') }}" class="btn btn-primary">
                    Buka Peta Interaktif
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="#tentang" class="btn btn-outline">Pelajari Lebih Lanjut</a>
            </div>
        </div>

        {{-- KANAN: Gambar lebih besar dalam wave frame --}}
        <div class="hero-visual">
            <div class="hero-illus-wrap">
                <div class="hero-wave-frame">
                    <svg class="hero-wave-svg" viewBox="0 0 560 520" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <clipPath id="waveClip">
                                <path d="
                                    M 40,25
                                    C 100,-15 220,5 290,35
                                    C 370,70 460,12 530,45
                                    C 572,65 560,135 548,200
                                    C 534,278 578,355 548,430
                                    C 524,488 428,522 340,510
                                    C 242,497 144,532 68,500
                                    C 8,472 -12,402 6,325
                                    C 24,248 -8,162 14,96
                                    C 24,62 12,46 40,25 Z
                                "/>
                            </clipPath>
                        </defs>
                        <path d="
                            M 40,25
                            C 100,-15 220,5 290,35
                            C 370,70 460,12 530,45
                            C 572,65 560,135 548,200
                            C 534,278 578,355 548,430
                            C 524,488 428,522 340,510
                            C 242,497 144,532 68,500
                            C 8,472 -12,402 6,325
                            C 24,248 -8,162 14,96
                            C 24,62 12,46 40,25 Z
                        " fill="#d6e8db"/>
                        <image
                            href="{{ asset('img/10050032.jpg') }}"
                            x="0" y="0" width="560" height="520"
                            clip-path="url(#waveClip)"
                            preserveAspectRatio="xMidYMid slice"
                        />
                        <path d="
                            M 40,25
                            C 100,-15 220,5 290,35
                            C 370,70 460,12 530,45
                            C 572,65 560,135 548,200
                            C 534,278 578,355 548,430
                            C 524,488 428,522 340,510
                            C 242,497 144,532 68,500
                            C 8,472 -12,402 6,325
                            C 24,248 -8,162 14,96
                            C 24,62 12,46 40,25 Z
                        " fill="none" stroke="#a8c5b0" stroke-width="2"/>
                    </svg>

                    <div class="hero-float-badge hero-float-badge-1">
                        <div class="badge-icon">📍</div>
                        <div>
                            <div style="font-size:.7rem;color:var(--ink-soft);">Total Lokasi</div>
                            <div style="color:var(--matcha-deep);font-size:.92rem;">{{ $stats['total_tempat'] }} Tempat</div>
                        </div>
                    </div>

                    <div class="hero-float-badge hero-float-badge-2">
                        <div class="badge-icon">⭐</div>
                        <div>
                            <div style="font-size:.7rem;color:var(--ink-soft);">Kategori</div>
                            <div style="color:var(--matcha-deep);font-size:.92rem;">5 Jenis Tempat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="hero-wave">
        <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,40 C240,80 480,0 720,40 C960,80 1200,0 1440,40 L1440,80 L0,80 Z" fill="#f0f7f2"/>
        </svg>
    </div>
</section>

{{-- STATS --}}
<section class="stats-bar">
    <div class="stats-inner">
        <div class="stat-item">
            <div class="stat-number">{{ $stats['total_tempat'] }}</div>
            <div class="stat-label">Total Lokasi</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['total_kuliner'] }}</div>
            <div class="stat-label">Kuliner</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['total_wisata'] }}</div>
            <div class="stat-label">Wisata</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['total_kesehatan'] }}</div>
            <div class="stat-label">Kesehatan</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['total_kemasyarakatan'] }}</div>
            <div class="stat-label">Kemasyarakatan</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['total_transportasi'] }}</div>
            <div class="stat-label">Transportasi</div>
        </div>
    </div>
</section>

{{-- FEATURES --}}
<section class="features" id="tentang">
    <div class="section-header">
        <div class="section-tag">Fitur Unggulan</div>
        <h2 class="section-title">Lebih dari sekadar peta</h2>
        <p class="section-desc">
            Dirancang untuk memudahkan eksplorasi dengan teknologi pemetaan modern dan data lokal yang akurat.
        </p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🗺</div>
            <div class="feature-title">Interactive Mapping</div>
            <p class="feature-desc">
                Visualisasi lokasi berbasis Leaflet dengan tampilan marker yang informatif
                dan popup detail ketika diklik.
            </p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔍</div>
            <div class="feature-title">Smart Filtering</div>
            <p class="feature-desc">
                Saring berdasarkan kategori, rating minimum, dan jarak dari posisi
                Anda saat ini secara real-time.
            </p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📍</div>
            <div class="feature-title">User Location</div>
            <p class="feature-desc">
                Gunakan GPS perangkat Anda untuk menemukan tempat terdekat
                dengan radius pencarian yang dapat disesuaikan.
            </p>
        </div>
    </div>
</section>

{{-- TEAM --}}
<section class="team" id="tim">
    <div class="section-header">
        <div class="section-tag">Tim Kami</div>
        <h2 class="section-title">Orang-orang di balik DanLens</h2>
        <p class="section-desc">
            Dikerjakan oleh tim mahasiswa yang berdedikasi untuk menghadirkan
            sistem informasi geografis yang bermanfaat bagi masyarakat Kota Medan.
        </p>
    </div>

    <div class="team-grid">
        @foreach ($team as $member)
        <div class="team-card">
            <div class="team-photo-wrap">
                @if (!empty($member['foto']))
                    <img
                        src="{{ asset('img/' . $member['foto']) }}"
                        alt="Foto {{ $member['nama'] }}"
                        loading="lazy"
                    >
                @else
                    <div class="team-avatar-fallback" data-color="{{ $member['warna'] }}">
                        {{ $member['avatar'] }}
                    </div>
                @endif

                <div class="team-photo-wave">
                    <svg viewBox="0 0 220 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                        <path d="M0,0 C30,18 60,-6 90,14 C120,34 150,-8 180,12 C210,28 220,18 220,18 L220,24 L0,24 Z" fill="#ffffff"/>
                    </svg>
                </div>
            </div>

            <div class="team-info">
                <div class="team-name">{{ $member['nama'] }}</div>
                <div class="team-nim">{{ $member['nim'] ?? '' }}</div>
                <div class="team-role">{{ $member['peran'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- CTA --}}
<section class="cta-section">
    <div class="cta-inner">
        <h2 class="cta-title">Siap menemukan hal menarik di Medan?</h2>
        <p class="cta-desc">Buka peta interaktif dan temukan tempat terbaik di sekitar Anda.</p>
        <a href="{{ route('maps') }}" class="btn btn-white">
            Mulai Eksplorasi
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.team-avatar-fallback[data-color]').forEach(el => {
        el.style.background = el.dataset.color;
    });
</script>
@endpush