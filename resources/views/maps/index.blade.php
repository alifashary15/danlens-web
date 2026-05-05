@extends('layouts.app')

@section('title', 'Maps')

@push('styles')
<style>
    /* ─────────────────────────────────────────
       MAPS PAGE  —  full-height split layout
    ───────────────────────────────────────── */

    /* Remove footer gap for maps page */
    body.maps-page > footer { display: none; }

    .maps-wrapper {
        display: flex;
        height: calc(100vh - 64px);
        overflow: hidden;
    }

    /* ── SIDEBAR ── */
    .sidebar {
        width: 360px;
        min-width: 360px;
        background: var(--white);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 10;
    }

    .sidebar-header {
        padding: 24px 24px 0;
        flex-shrink: 0;
    }

    .sidebar-title {
        font-family: var(--font-display);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -.01em;
        margin-bottom: 4px;
    }

    .sidebar-subtitle {
        font-size: .8rem;
        color: var(--ink-soft);
        margin-bottom: 20px;
    }

    /* ── FILTER PANEL ── */
    .filter-panel {
        padding: 0 24px 20px;
        flex-shrink: 0;
        border-bottom: 1px solid var(--border);
    }

    .filter-group {
        margin-bottom: 16px;
    }
    .filter-group:last-child { margin-bottom: 0; }

    .filter-label {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--ink-soft);
        margin-bottom: 8px;
        display: block;
    }

    /* Kategori chips */
    .chip-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .chip {
        padding: 5px 13px;
        border-radius: 100px;
        border: 1.5px solid var(--border);
        font-size: .78rem;
        font-weight: 500;
        cursor: pointer;
        background: var(--white);
        color: var(--ink-mid);
        transition: all .18s;
        user-select: none;
    }
    .chip:hover { border-color: var(--matcha-light); background: var(--matcha-ghost); }
    .chip.active {
        background: var(--matcha-pale);
        border-color: var(--matcha);
        color: var(--matcha-deep);
        font-weight: 600;
    }
    .chip-all { border-color: var(--matcha); }
    .chip-all.active {
        background: var(--matcha);
        color: var(--white);
        border-color: var(--matcha);
    }

    /* Rating slider */
    .slider-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .slider-row input[type=range] {
        flex: 1;
        height: 4px;
        -webkit-appearance: none;
        appearance: none;
        background: var(--matcha-pale);
        border-radius: 100px;
        outline: none;
        cursor: pointer;
    }
    .slider-row input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px; height: 18px;
        border-radius: 50%;
        background: var(--matcha);
        border: 2px solid var(--white);
        box-shadow: 0 1px 4px rgba(74,124,89,.4);
        cursor: pointer;
    }
    .slider-val {
        font-size: .82rem;
        font-weight: 700;
        color: var(--matcha-deep);
        min-width: 28px;
        text-align: right;
    }

    /* Jarak toggle */
    .jarak-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .toggle-switch {
        position: relative;
        width: 40px; height: 22px;
        flex-shrink: 0;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-track {
        position: absolute;
        inset: 0;
        background: var(--border);
        border-radius: 100px;
        cursor: pointer;
        transition: background .2s;
    }
    .toggle-track::after {
        content: '';
        position: absolute;
        left: 3px; top: 3px;
        width: 16px; height: 16px;
        background: var(--white);
        border-radius: 50%;
        transition: transform .2s;
    }
    .toggle-switch input:checked ~ .toggle-track { background: var(--matcha); }
    .toggle-switch input:checked ~ .toggle-track::after { transform: translateX(18px); }
    .toggle-label { font-size: .82rem; color: var(--ink-mid); font-weight: 500; }

    .jarak-control {
        display: none;
        gap: 10px;
        align-items: center;
    }
    .jarak-control.visible { display: flex; }

    .loc-status {
        font-size: .76rem;
        color: var(--ink-soft);
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 8px;
    }
    .loc-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--matcha-light);
        flex-shrink: 0;
    }
    .loc-dot.active { background: #22c55e; }

    /* Reset button */
    .btn-reset {
        width: 100%;
        padding: 9px;
        background: transparent;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: .8rem;
        font-weight: 600;
        color: var(--ink-soft);
        cursor: pointer;
        transition: all .18s;
        margin-top: 4px;
    }
    .btn-reset:hover {
        border-color: var(--matcha-light);
        color: var(--matcha-deep);
        background: var(--matcha-ghost);
    }

    /* ── RESULTS LIST ── */
    .results-header {
        padding: 16px 24px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .results-count {
        font-size: .78rem;
        font-weight: 600;
        color: var(--ink-soft);
        letter-spacing: .02em;
    }
    .results-count span {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        color: var(--matcha-deep);
    }

    .results-list {
        flex: 1;
        overflow-y: auto;
        padding: 0 16px 16px;
        scrollbar-width: thin;
        scrollbar-color: var(--matcha-pale) transparent;
    }
    .results-list::-webkit-scrollbar { width: 4px; }
    .results-list::-webkit-scrollbar-track { background: transparent; }
    .results-list::-webkit-scrollbar-thumb { background: var(--matcha-pale); border-radius: 4px; }

    /* ── PLACE CARD (list) ── */
    .place-card {
        display: flex;
        gap: 12px;
        padding: 12px;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: background .18s, transform .18s;
        border: 1.5px solid transparent;
        margin-bottom: 6px;
    }
    .place-card:hover {
        background: var(--matcha-ghost);
        border-color: var(--matcha-pale);
    }
    .place-card.active {
        background: var(--matcha-ghost);
        border-color: var(--matcha-light);
    }

    .place-thumb {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        background: var(--matcha-pale);
    }
    .place-thumb-placeholder {
        width: 60px; height: 60px;
        border-radius: 10px;
        background: var(--matcha-pale);
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
    }

    .place-info { flex: 1; min-width: 0; }
    .place-name {
        font-weight: 600;
        font-size: .88rem;
        color: var(--ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 3px;
    }
    .place-meta {
        font-size: .76rem;
        color: var(--ink-soft);
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .place-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .place-rating {
        display: flex;
        align-items: center;
        gap: 3px;
        font-size: .76rem;
        font-weight: 700;
        color: var(--ink-mid);
    }
    .star {
        color: #f59e0b;
        font-size: .75rem;
    }
    .place-jarak {
        font-size: .72rem;
        color: var(--matcha-mid);
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--ink-soft);
    }
    .empty-state svg { margin-bottom: 12px; opacity: .4; }
    .empty-state p { font-size: .88rem; }

    /* Loading skeleton */
    .skeleton-card {
        display: flex;
        gap: 12px;
        padding: 12px;
        margin-bottom: 6px;
        border-radius: var(--radius-md);
        background: var(--matcha-ghost);
        animation: shimmer 1.5s infinite;
    }
    @keyframes shimmer {
        0%,100% { opacity:1; } 50% { opacity:.5; }
    }
    .skel-thumb { width:60px; height:60px; border-radius:10px; background:var(--matcha-pale); flex-shrink:0; }
    .skel-lines { flex:1; }
    .skel-line { height:10px; border-radius:4px; background:var(--matcha-pale); margin-bottom:7px; }
    .skel-line-sm { width:60%; }

    /* ── MAP ── */
    .map-container {
        flex: 1;
        position: relative;
        overflow: hidden;
    }

    #mainMap {
        width: 100%;
        height: 100%;
    }

    /* Override Leaflet popup */
    .leaflet-popup-content-wrapper {
        border-radius: var(--radius-md) !important;
        box-shadow: var(--shadow-lg) !important;
        border: 1px solid var(--border) !important;
        padding: 0 !important;
        overflow: hidden;
    }
    .leaflet-popup-content { margin: 0 !important; width: 280px !important; }
    .leaflet-popup-tip-container { display: none; }

    /* Custom popup content */
    .popup-wrap { font-family: var(--font-body); }
    .popup-img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        background: var(--matcha-pale);
        display: block;
    }
    .popup-img-placeholder {
        width:100%; height:140px;
        background: var(--matcha-pale);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem;
    }
    .popup-body { padding: 14px 16px 16px; }
    .popup-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 100px;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .03em;
        margin-bottom: 8px;
    }
    .popup-name {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 5px;
        line-height: 1.3;
    }
    .popup-jalan {
        font-size: .78rem;
        color: var(--ink-soft);
        margin-bottom: 10px;
        display: flex;
        align-items: flex-start;
        gap: 4px;
    }
    .popup-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .popup-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: .82rem;
        font-weight: 700;
        color: var(--ink);
    }
    .popup-jarak-pill {
        font-size: .72rem;
        font-weight: 600;
        background: var(--matcha-pale);
        color: var(--matcha-deep);
        padding: 3px 10px;
        border-radius: 100px;
    }
    .popup-kontak {
        font-size: .78rem;
        color: var(--ink-soft);
        margin-bottom: 10px;
    }
    .popup-detail {
        font-size: .78rem;
        color: var(--ink-soft);
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 12px;
    }
    .popup-actions {
        display: flex;
        gap: 8px;
    }
    .popup-btn {
        flex: 1;
        padding: 8px;
        border-radius: var(--radius-sm);
        font-size: .78rem;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        transition: all .18s;
        border: none;
        font-family: var(--font-body);
    }
    .popup-btn-primary {
        background: var(--matcha);
        color: var(--white);
    }
    .popup-btn-primary:hover { background: var(--matcha-deep); }
    .popup-btn-outline {
        background: var(--white);
        color: var(--ink-mid);
        border: 1.5px solid var(--border);
    }
    .popup-btn-outline:hover { background: var(--matcha-ghost); }

    /* Custom marker */
    .custom-marker {
        width: 36px; height: 36px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        display: flex; align-items: center; justify-content: center;
        border: 2.5px solid var(--white);
        box-shadow: 0 3px 10px rgba(0,0,0,.25);
        position: relative;
    }
    .custom-marker .inner {
        transform: rotate(45deg);
        font-size: 13px;
        line-height: 1;
    }

    /* Map controls overlay */
    .map-controls {
        position: absolute;
        top: 16px; right: 16px;
        z-index: 800;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .map-ctrl-btn {
        width: 40px; height: 40px;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: .85rem;
        box-shadow: var(--shadow-sm);
        transition: background .18s;
        color: var(--ink-mid);
    }
    .map-ctrl-btn:hover { background: var(--matcha-ghost); color: var(--matcha-deep); }

    /* Loc marker */
    .user-marker {
        width: 16px; height: 16px;
        background: #3b82f6;
        border-radius: 50%;
        border: 3px solid var(--white);
        box-shadow: 0 0 0 4px rgba(59,130,246,.25), 0 2px 6px rgba(0,0,0,.25);
    }

    /* Mobile: sidebar collapse */
    .sidebar-toggle {
        display: none;
    }

    @media (max-width: 768px) {
        .maps-wrapper { flex-direction: column; }
        .sidebar {
            width: 100%;
            min-width: 0;
            height: 50vh;
            border-right: none;
            border-bottom: 1px solid var(--border);
        }
    }
</style>
@endpush

@section('content')

<div class="maps-wrapper" id="mapsWrapper">

    {{-- ─── SIDEBAR ─── --}}
    <aside class="sidebar">

        <div class="sidebar-header">
            <div class="sidebar-title">Jelajahi Lokasi</div>
            <div class="sidebar-subtitle">Kota Medan &amp; Sekitarnya</div>
        </div>

        <div class="filter-panel">

            {{-- Kategori --}}
            <div class="filter-group">
                <span class="filter-label">Kategori</span>
                <div class="chip-group">
                    <div class="chip chip-all active" data-kategori="all">Semua</div>
                    @foreach ($kategoris as $kat)
                        <div class="chip" data-kategori="{{ $kat->id }}">{{ $kat->nama_kategori }}</div>
                    @endforeach
                </div>
            </div>

            {{-- Rating --}}
            <div class="filter-group">
                <span class="filter-label">Rating Minimum</span>
                <div class="slider-row">
                    <input type="range" id="sliderRating" min="0" max="5" step="0.5" value="0">
                    <div class="slider-val" id="ratingVal">Semua</div>
                </div>
            </div>

            {{-- Jarak --}}
            <div class="filter-group">
                <div class="jarak-toggle">
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggleJarak">
                        <span class="toggle-track"></span>
                    </label>
                    <span class="toggle-label">Filter berdasarkan jarak</span>
                </div>

                <div class="jarak-control" id="jarakControl">
                    <div class="slider-row" style="flex:1;">
                        <input type="range" id="sliderJarak" min="1" max="50" step="1" value="10">
                        <div class="slider-val" id="jarakVal">10 km</div>
                    </div>
                </div>

                <div class="loc-status" id="locStatus">
                    <div class="loc-dot" id="locDot"></div>
                    <span id="locText">Lokasi belum diaktifkan</span>
                </div>
            </div>

            <button class="btn-reset" id="btnReset">Reset semua filter</button>
        </div>

        {{-- Results --}}
        <div class="results-header">
            <div class="results-count">
                <span id="countNum">—</span> lokasi ditemukan
            </div>
        </div>

        <div class="results-list" id="resultsList">
            {{-- Skeleton loading --}}
            @for ($i = 0; $i < 5; $i++)
            <div class="skeleton-card">
                <div class="skel-thumb"></div>
                <div class="skel-lines">
                    <div class="skel-line"></div>
                    <div class="skel-line skel-line-sm"></div>
                </div>
            </div>
            @endfor
        </div>

    </aside>

    {{-- ─── MAP ─── --}}
    <div class="map-container">
        <div id="mainMap"></div>

        <div class="map-controls">
            <div class="map-ctrl-btn" id="btnMyLoc" title="Lokasi saya">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/><path stroke-linecap="round" d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
                    <circle cx="12" cy="12" r="7" stroke-dasharray="44" stroke-dashoffset="11"/>
                </svg>
            </div>
            <div class="map-ctrl-btn" id="btnFitAll" title="Lihat semua">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M4 8V4h4M16 4h4v4M4 16v4h4M16 20h4v-4"/>
                </svg>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {

    // ── CONFIG ──────────────────────────────────────────
    const MEDAN_CENTER = [3.585, 98.676];

    // ✅ FIX: Base URL Supabase — pastikan bucket "tempat_images" sudah PUBLIC
    const SUPABASE_STORAGE_URL = 'https://rnafixrgoucrplssoqtm.supabase.co/storage/v1/object/public/tempat_images/';

    const KATEGORI_CONFIG = {
        1: { icon: '🍜', color: '#f59e0b', badge: 'badge-kuliner',        label: 'Kuliner' },
        2: { icon: '🏖',  color: '#3b82f6', badge: 'badge-wisata',         label: 'Wisata' },
        3: { icon: '🏥', color: '#ec4899', badge: 'badge-kesehatan',      label: 'Kesehatan' },
        4: { icon: '🏛',  color: '#8b5cf6', badge: 'badge-kemasyarakatan', label: 'Kemasyarakatan' },
        5: { icon: '🚌', color: '#10b981', badge: 'badge-transportasi',   label: 'Transportasi' },
    };

    // ── STATE ────────────────────────────────────────────
    let allData      = [];
    let filteredData = [];
    let activeKat    = 'all';
    let ratingMin    = 0;
    let jarakAktif   = false;
    let jarakMax     = 10;
    let userLat      = null;
    let userLng      = null;
    let markers      = {};      // id → L.Marker
    let activeCard   = null;

    // ── MAP INIT ─────────────────────────────────────────
    const map = L.map('mainMap', {
        center: MEDAN_CENTER,
        zoom: 12,
        zoomControl: false,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    // ── MARKER FACTORY ───────────────────────────────────
    function makeMarkerIcon(kategoriId, small = false) {
        const cfg   = KATEGORI_CONFIG[kategoriId] || { icon: '📍', color: '#4a7c59' };
        const size  = small ? 28 : 36;
        const fsz   = small ? 10 : 13;
        return L.divIcon({
            className: '',
            html: `<div style="
                width:${size}px;height:${size}px;
                background:${cfg.color};
                border-radius:50% 50% 50% 0;
                transform:rotate(-45deg);
                border:2.5px solid #fff;
                box-shadow:0 3px 10px rgba(0,0,0,.28);
                display:flex;align-items:center;justify-content:center;
            "><span style="transform:rotate(45deg);font-size:${fsz}px;line-height:1;">${cfg.icon}</span></div>`,
            iconSize:   [size, size],
            iconAnchor: [size / 2, size],
            popupAnchor:[0, -(size + 4)],
        });
    }

    function makeUserIcon() {
        return L.divIcon({
            className: '',
            html: '<div class="user-marker"></div>',
            iconSize: [16, 16],
            iconAnchor: [8, 8],
        });
    }

    // ── ✅ FIX: Helper normalisasi URL gambar ─────────────
    // Kalau media_url sudah full URL → pakai langsung
    // Kalau cuma nama file → gabungkan dengan base URL Supabase
    function getImageUrl(mediaUrl) {
    if (!mediaUrl) return null;
    const result = (mediaUrl.startsWith('http://') || mediaUrl.startsWith('https://'))
        ? mediaUrl
        : SUPABASE_STORAGE_URL + mediaUrl;
    console.log('getImageUrl result:', result); // ← tambah ini
    return result;
}

    window.handleImgError = function(el, icon) {
        const placeholder = document.createElement('div');
        placeholder.className = el.className.includes('popup-img') ? 'popup-img-placeholder' : 'place-thumb-placeholder';
        placeholder.textContent = icon || '📍';
        el.parentNode.replaceChild(placeholder, el);
    };

    // ── POPUP BUILDER ────────────────────────────────────
    function buildPopup(t) {
        const cfg    = KATEGORI_CONFIG[t.kategori_id] || { badge: 'badge-default', label: t.kategori, icon: '📍' };
        const imgUrl = getImageUrl(t.media_url); 
        const icon   = cfg.icon ?? '📍';

        const imgEl  = imgUrl
            ? `<img src="${imgUrl}"
                    class="popup-img"
                    crossorigin="anonymous"
                    referrerpolicy="no-referrer"
                    data-icon="${icon}"
                    onerror="handleImgError(this, this.dataset.icon)">
               <div class="popup-img-placeholder" style="display:none;">${icon}</div>`
            : `<div class="popup-img-placeholder">${icon}</div>`;

        const jarakEl = t.jarak_km != null
            ? `<span class="popup-jarak-pill">${t.jarak_km} km dari Anda</span>` : '';

        const kontak = t.kontak ? `<div class="popup-kontak">☎ ${t.kontak}</div>` : '';

        const stars = '★'.repeat(Math.round(t.rating || 0)) + '☆'.repeat(5 - Math.round(t.rating || 0));

        return `
        <div class="popup-wrap">
            ${imgEl}
            <div class="popup-body">
                <span class="popup-badge ${cfg.badge}">${cfg.label}</span>
                <div class="popup-name">${t.nama_tempat}</div>
                <div class="popup-jalan">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-top:1px;flex-shrink:0"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                    ${t.jalan ?? ''}${t.kecamatan ? ', ' + t.kecamatan : ''}
                </div>
                <div class="popup-row">
                    <div class="popup-rating">
                        <span style="color:#f59e0b;">${stars}</span>
                        &nbsp;${(t.rating || 0).toFixed(1)}
                    </div>
                    ${jarakEl}
                </div>
                ${kontak}
                <div class="popup-detail">${t.detail ?? ''}</div>
                <div class="popup-actions">
                    <button class="popup-btn popup-btn-primary"
                        onclick="openMaps(${t.latitude}, ${t.longitude})">
                        Buka di Maps
                    </button>
                    <button class="popup-btn popup-btn-outline"
                        onclick="flyTo(${t.latitude}, ${t.longitude})">
                        Fokus
                    </button>
                </div>
            </div>
        </div>`;
    }

    // ── FETCH DATA ───────────────────────────────────────
    function fetchData() {
        const params = new URLSearchParams();
        if (activeKat !== 'all')    params.set('kategori', activeKat);
        if (ratingMin > 0)          params.set('rating_min', ratingMin);
        if (jarakAktif && userLat) {
            params.set('lat', userLat);
            params.set('lng', userLng);
            params.set('radius', jarakMax);
        }

        fetch(`/api/tempat?${params}`)
            .then(r => r.json())
            .then(data => {
                allData      = data;
                filteredData = data;
                renderMarkers();
                renderList();
            })
            .catch(err => console.error('Gagal fetch data tempat:', err));
    }

    // ── RENDER MARKERS ───────────────────────────────────
    function renderMarkers() {
        // Hapus marker lama yang tidak ada di data baru
        const newIds = new Set(filteredData.map(t => t.id));
        Object.entries(markers).forEach(([id, m]) => {
            if (!newIds.has(parseInt(id))) {
                map.removeLayer(m);
                delete markers[id];
            }
        });

        filteredData.forEach(t => {
            if (markers[t.id]) {
                markers[t.id].setPopupContent(buildPopup(t));
                return;
            }
            const marker = L.marker([t.latitude, t.longitude], {
                icon: makeMarkerIcon(t.kategori_id)
            })
            .bindPopup(buildPopup(t), { maxWidth: 280 })
            .addTo(map);

            marker.on('click', () => highlightCard(t.id));
            markers[t.id] = marker;
        });
    }

    // ── RENDER LIST ──────────────────────────────────────
    function renderList() {
        const list = document.getElementById('resultsList');
        document.getElementById('countNum').textContent = filteredData.length;

        if (!filteredData.length) {
            list.innerHTML = `
                <div class="empty-state">
                    <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                    <p>Tidak ada lokasi yang cocok.<br>Coba sesuaikan filter.</p>
                </div>`;
            return;
        }

        list.innerHTML = filteredData.map(t => {
            const cfg    = KATEGORI_CONFIG[t.kategori_id] || { icon: '📍', badge: 'badge-default' };
            console.log('media_url:', t.media_url, 'untuk:', t.nama_tempat);
            const imgUrl = getImageUrl(t.media_url);
            const icon   = cfg.icon ?? '📍';

            const img = imgUrl
                ? `<img src="${imgUrl}"
                        class="place-thumb"
                        crossorigin="anonymous"
                        referrerpolicy="no-referrer"
                        data-icon="${icon}"
                        onerror="handleImgError(this, this.dataset.icon)">`
                : `<div class="place-thumb-placeholder">${icon}</div>`;

            const dist  = t.jarak_km != null ? `<span class="place-jarak">${t.jarak_km} km</span>` : '';
            const stars = '★'.repeat(Math.round(t.rating || 0));

            return `
            <div class="place-card" data-id="${t.id}" onclick="selectPlace(${t.id})">
                ${img}
                <div class="place-info">
                    <div class="place-name">${t.nama_tempat}</div>
                    <div class="place-meta">${t.jalan ?? ''}${t.kecamatan ? ', ' + t.kecamatan : ''}</div>
                    <div class="place-footer">
                        <div class="place-rating">
                            <span class="star">${stars}</span>
                            ${(t.rating || 0).toFixed(1)}
                        </div>
                        ${dist}
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    // ── SELECT PLACE ─────────────────────────────────────
    window.selectPlace = function (id) {
        const t = filteredData.find(x => x.id === id);
        if (!t) return;

        // highlight card
        document.querySelectorAll('.place-card').forEach(c => c.classList.remove('active'));
        const card = document.querySelector(`.place-card[data-id="${id}"]`);
        if (card) card.classList.add('active');

        // pan & open popup
        map.setView([t.latitude, t.longitude], 16, { animate: true });
        setTimeout(() => {
            if (markers[id]) markers[id].openPopup();
        }, 350);
    };

    function highlightCard(id) {
        document.querySelectorAll('.place-card').forEach(c => c.classList.remove('active'));
        const card = document.querySelector(`.place-card[data-id="${id}"]`);
        if (card) {
            card.classList.add('active');
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    window.flyTo = function (lat, lng) {
        map.setView([lat, lng], 17, { animate: true });
    };

    window.openMaps = function (lat, lng) {
        window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
    };

    // ── FIT ALL ──────────────────────────────────────────
    document.getElementById('btnFitAll').addEventListener('click', () => {
        if (!filteredData.length) return;
        const group = filteredData.map(t => [t.latitude, t.longitude]);
        map.fitBounds(L.latLngBounds(group).pad(0.1), { animate: true });
    });

    // ── MY LOCATION ──────────────────────────────────────
    let userMarker = null;

    function setUserLoc(lat, lng) {
        userLat = lat; userLng = lng;
        document.getElementById('locDot').classList.add('active');
        document.getElementById('locText').textContent = `Lokasi aktif (${lat.toFixed(4)}, ${lng.toFixed(4)})`;

        if (userMarker) map.removeLayer(userMarker);
        userMarker = L.marker([lat, lng], { icon: makeUserIcon() })
            .bindTooltip('Lokasi Anda')
            .addTo(map);
    }

    document.getElementById('btnMyLoc').addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung geolokasi.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            pos => {
                setUserLoc(pos.coords.latitude, pos.coords.longitude);
                map.setView([pos.coords.latitude, pos.coords.longitude], 14, { animate: true });
                if (jarakAktif) fetchData();
            },
            () => alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.')
        );
    });

    // ── FILTER EVENTS ────────────────────────────────────

    // Kategori chips
    document.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', () => {
            document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeKat = chip.dataset.kategori;
            fetchData();
        });
    });

    // Rating slider
    const sliderRating = document.getElementById('sliderRating');
    const ratingValEl  = document.getElementById('ratingVal');
    sliderRating.addEventListener('input', () => {
        ratingMin = parseFloat(sliderRating.value);
        ratingValEl.textContent = ratingMin === 0 ? 'Semua' : `${ratingMin}+`;
        fetchData();
    });

    // Jarak toggle
    const toggleJarak  = document.getElementById('toggleJarak');
    const jarakControl = document.getElementById('jarakControl');
    toggleJarak.addEventListener('change', () => {
        jarakAktif = toggleJarak.checked;
        jarakControl.classList.toggle('visible', jarakAktif);

        if (jarakAktif && !userLat) {
            navigator.geolocation && navigator.geolocation.getCurrentPosition(
                pos => {
                    setUserLoc(pos.coords.latitude, pos.coords.longitude);
                    fetchData();
                },
                () => {
                    toggleJarak.checked = false;
                    jarakAktif = false;
                    jarakControl.classList.remove('visible');
                    alert('Aktifkan izin lokasi terlebih dahulu.');
                }
            );
        } else {
            fetchData();
        }
    });

    // Jarak slider
    const sliderJarak = document.getElementById('sliderJarak');
    const jarakValEl  = document.getElementById('jarakVal');
    sliderJarak.addEventListener('input', () => {
        jarakMax = parseInt(sliderJarak.value);
        jarakValEl.textContent = `${jarakMax} km`;
        if (jarakAktif) fetchData();
    });

    // Reset
    document.getElementById('btnReset').addEventListener('click', () => {
        activeKat  = 'all';
        ratingMin  = 0;
        jarakAktif = false;
        jarakMax   = 10;

        document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
        document.querySelector('.chip-all').classList.add('active');
        sliderRating.value   = 0;
        ratingValEl.textContent = 'Semua';
        toggleJarak.checked  = false;
        jarakControl.classList.remove('visible');
        sliderJarak.value    = 10;
        jarakValEl.textContent = '10 km';

        fetchData();
    });

    // ── INIT ─────────────────────────────────────────────
    fetchData();

})();
</script>
@endpush