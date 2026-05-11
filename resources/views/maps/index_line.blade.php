@extends('layouts.app')

@section('title', 'Maps — Rute & Jarak')

@push('styles')
<style>
    /* ─────────────────────────────────────────
       LINE PAGE  —  full-height split layout
    ───────────────────────────────────────── */

    body.maps-page > footer { display: none; }

    .maps-wrapper {
        display: flex;
        height: calc(100vh - 64px);
        overflow: hidden;
    }

    /* ── SIDEBAR ── */
    .sidebar {
        width: 320px;
        min-width: 320px;
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
        margin-bottom: 14px;
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

    /* ── POINT SELECTOR ── */
    .point-selector {
        position: relative;
    }
    .point-selector-input {
        width: 100%;
        padding: 9px 36px 9px 14px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        font-family: var(--font-body);
        font-size: .85rem;
        color: var(--ink);
        background: var(--white);
        outline: none;
        transition: border-color .18s, box-shadow .18s;
        box-sizing: border-box;
    }
    .point-selector-input:focus {
        border-color: var(--matcha);
        box-shadow: 0 0 0 3px rgba(74,124,89,.12);
    }
    .point-selector-input::placeholder { color: var(--ink-soft); }

    .point-clear-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--ink-soft);
        display: none;
        font-size: .9rem;
        line-height: 1;
        padding: 2px 4px;
    }
    .point-clear-btn.visible { display: block; }
    .point-clear-btn:hover { color: var(--ink); }

    /* Dropdown autocomplete */
    .point-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0; right: 0;
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-md);
        z-index: 9999;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        scrollbar-width: thin;
        scrollbar-color: var(--matcha-pale) transparent;
    }
    .point-dropdown.open { display: block; }
    .point-dropdown::-webkit-scrollbar { width: 4px; }
    .point-dropdown::-webkit-scrollbar-thumb { background: var(--matcha-pale); border-radius: 4px; }

    .dropdown-item {
        padding: 9px 14px;
        font-size: .84rem;
        color: var(--ink);
        cursor: pointer;
        transition: background .14s;
        border-bottom: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .dropdown-item:last-child { border-bottom: none; }
    .dropdown-item:hover, .dropdown-item.focused { background: var(--matcha-ghost); }
    .dropdown-item-name { font-weight: 600; }
    .dropdown-item-sub  { font-size: .73rem; color: var(--ink-soft); }
    .dropdown-item-empty {
        padding: 12px 14px;
        font-size: .82rem;
        color: var(--ink-soft);
        text-align: center;
    }

    /* Point indicator row */
    .point-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }
    .point-badge {
        width: 26px; height: 26px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem;
        font-weight: 700;
        flex-shrink: 0;
        letter-spacing: 0;
    }
    .point-badge-a {
        background: var(--matcha);
        color: var(--white);
    }
    .point-badge-b {
        background: #e85d5d;
        color: var(--white);
    }
    .point-row-label {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ink-soft);
        margin-bottom: 6px;
    }

    /* Swap button */
    .btn-swap {
        width: 100%;
        padding: 8px;
        background: var(--matcha-ghost);
        border: 1.5px solid var(--matcha-pale);
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: .8rem;
        font-weight: 600;
        color: var(--matcha-deep);
        cursor: pointer;
        transition: all .18s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-bottom: 12px;
    }
    .btn-swap:hover { background: var(--matcha-pale); }

    /* Search button */
    .btn-search-route {
        width: 100%;
        padding: 10px;
        background: var(--matcha);
        border: none;
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: .875rem;
        font-weight: 700;
        color: var(--white);
        cursor: pointer;
        transition: background .18s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-search-route:hover { background: var(--matcha-deep); }
    .btn-search-route:disabled {
        background: var(--border);
        color: var(--ink-soft);
        cursor: not-allowed;
    }

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
        margin-top: 8px;
    }
    .btn-reset:hover {
        border-color: var(--matcha-light);
        color: var(--matcha-deep);
        background: var(--matcha-ghost);
    }

    /* ── HASIL RUTE ── */
    .results-area {
        flex: 1;
        overflow-y: auto;
        padding: 16px 24px;
        scrollbar-width: thin;
        scrollbar-color: var(--matcha-pale) transparent;
    }
    .results-area::-webkit-scrollbar { width: 4px; }
    .results-area::-webkit-scrollbar-thumb { background: var(--matcha-pale); border-radius: 4px; }

    /* Jarak card */
    .distance-card {
        background: var(--matcha-ghost);
        border: 1.5px solid var(--matcha-pale);
        border-radius: var(--radius-md);
        padding: 16px 18px;
        margin-bottom: 16px;
        display: none;
    }
    .distance-card.visible { display: block; }

    .distance-label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ink-soft);
        margin-bottom: 8px;
    }

    .distance-value {
        font-family: var(--font-display);
        font-size: 2rem;
        font-weight: 700;
        color: var(--matcha-deep);
        line-height: 1;
        margin-bottom: 4px;
    }
    .distance-value span {
        font-size: 1rem;
        font-weight: 600;
        color: var(--matcha);
    }

    .distance-from-to {
        font-size: .78rem;
        color: var(--ink-soft);
        margin-top: 8px;
        line-height: 1.5;
    }
    .distance-from-to strong { color: var(--ink-mid); }

    /* Info titik */
    .point-info-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px 16px;
        margin-bottom: 10px;
        display: none;
    }
    .point-info-card.visible { display: block; }

    .point-info-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .point-info-name {
        font-weight: 700;
        font-size: .9rem;
        color: var(--ink);
        flex: 1;
        min-width: 0;
    }
    .point-info-badge {
        font-size: .68rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 100px;
    }
    .point-info-badge-a { background: var(--matcha-pale); color: var(--matcha-deep); }
    .point-info-badge-b { background: #fde8e8; color: #c0392b; }

    .point-info-row {
        display: flex;
        gap: 6px;
        font-size: .78rem;
        color: var(--ink-soft);
        margin-bottom: 5px;
        align-items: flex-start;
    }
    .point-info-row:last-child { margin-bottom: 0; }
    .point-info-icon { flex-shrink: 0; margin-top: 1px; }

    /* Empty / placeholder state */
    .empty-state {
        text-align: center;
        padding: 40px 16px;
        color: var(--ink-soft);
    }
    .empty-state-icon {
        font-size: 2.4rem;
        margin-bottom: 12px;
        opacity: .5;
    }
    .empty-state-title {
        font-family: var(--font-display);
        font-size: .95rem;
        font-weight: 600;
        color: var(--ink-mid);
        margin-bottom: 6px;
    }
    .empty-state p { font-size: .82rem; line-height: 1.6; }

    /* Error state */
    .error-state {
        background: #fff5f5;
        border: 1px solid #f5c6c6;
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        font-size: .82rem;
        color: #c0392b;
        margin-bottom: 12px;
        display: none;
    }
    .error-state.visible { display: block; }

    /* Loading spinner */
    .loading-overlay {
        text-align: center;
        padding: 32px 16px;
        display: none;
    }
    .loading-overlay.visible { display: block; }
    .spinner {
        width: 28px; height: 28px;
        border: 3px solid var(--matcha-pale);
        border-top-color: var(--matcha);
        border-radius: 50%;
        animation: spin .7s linear infinite;
        margin: 0 auto 10px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .loading-text { font-size: .82rem; color: var(--ink-soft); }

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

    /* Map controls */
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

    /* Distance badge on map */
    .map-distance-badge {
        position: absolute;
        top: 16px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255,255,255,.96);
        backdrop-filter: blur(8px);
        border: 1.5px solid var(--matcha-pale);
        border-radius: 100px;
        padding: 7px 18px;
        font-family: var(--font-display);
        font-size: .9rem;
        font-weight: 700;
        color: var(--matcha-deep);
        box-shadow: var(--shadow-md);
        z-index: 800;
        display: none;
        white-space: nowrap;
        pointer-events: none;
    }
    .map-distance-badge.visible { display: block; }

    /* Override Leaflet popups */
    .leaflet-popup-content-wrapper {
        border-radius: var(--radius-md) !important;
        box-shadow: var(--shadow-lg) !important;
        border: 1px solid var(--border) !important;
        padding: 0 !important;
        overflow: hidden;
    }
    .leaflet-popup-content { margin: 0 !important; width: auto !important; min-width: 200px; }
    .leaflet-popup-tip-container { display: none; }

    .popup-inner {
        padding: 14px 16px;
        font-family: var(--font-body);
    }
    .popup-title {
        font-family: var(--font-display);
        font-size: .95rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 6px;
    }
    .popup-row {
        font-size: .78rem;
        color: var(--ink-soft);
        margin-bottom: 4px;
        display: flex;
        gap: 5px;
        align-items: flex-start;
    }

    /* Mobile */
    @media (max-width: 768px) {
        .maps-wrapper { flex-direction: column; }
        .sidebar {
            width: 100%;
            min-width: 0;
            height: 55vh;
            border-right: none;
            border-bottom: 1px solid var(--border);
        }
        .map-distance-badge { font-size: .8rem; padding: 6px 14px; }
    }
</style>
@endpush

@section('content')

<div class="maps-wrapper">

    {{-- ─── SIDEBAR ─── --}}
    <aside class="sidebar">

        <div class="sidebar-header">
            <div class="sidebar-title">Rute & Jarak</div>
            <div class="sidebar-subtitle">Hitung jarak antar dua lokasi di Kota Medan</div>
        </div>

        <div class="filter-panel">

            {{-- TITIK A --}}
            <div class="filter-group">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <div class="point-badge point-badge-a">A</div>
                    <span class="filter-label" style="margin:0;">Titik Awal</span>
                </div>
                <div class="point-selector" id="selectorA">
                    <input
                        type="text"
                        class="point-selector-input"
                        id="inputA"
                        placeholder="Cari tempat awal…"
                        autocomplete="off"
                    >
                    <button class="point-clear-btn" id="clearA" title="Hapus">✕</button>
                    <div class="point-dropdown" id="dropdownA"></div>
                </div>
            </div>

            {{-- SWAP --}}
            <button class="btn-swap" id="btnSwap" title="Tukar A dan B">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
                </svg>
                Tukar Titik
            </button>

            {{-- TITIK B --}}
            <div class="filter-group">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <div class="point-badge point-badge-b">B</div>
                    <span class="filter-label" style="margin:0;">Titik Tujuan</span>
                </div>
                <div class="point-selector" id="selectorB">
                    <input
                        type="text"
                        class="point-selector-input"
                        id="inputB"
                        placeholder="Cari tempat tujuan…"
                        autocomplete="off"
                    >
                    <button class="point-clear-btn" id="clearB" title="Hapus">✕</button>
                    <div class="point-dropdown" id="dropdownB"></div>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <button class="btn-search-route" id="btnHitungRute" disabled>
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Tampilkan Rute
            </button>

            <button class="btn-reset" id="btnReset">Reset semua</button>
        </div>

        {{-- ─── HASIL ─── --}}
        <div class="results-area" id="resultsArea">

            {{-- Loading --}}
            <div class="loading-overlay" id="loadingState">
                <div class="spinner"></div>
                <div class="loading-text">Menghitung rute…</div>
            </div>

            {{-- Error --}}
            <div class="error-state" id="errorState"></div>

            {{-- Jarak card --}}
            <div class="distance-card" id="distanceCard">
                <div class="distance-label">Jarak Garis Lurus</div>
                {{-- distanceValue diisi oleh JS, unit sudah termasuk di dalam formatJarak() --}}
                <div class="distance-value" id="distanceValue">—</div>
                <div class="distance-from-to" id="distanceFromTo"></div>
            </div>

            {{-- Info titik A --}}
            <div class="point-info-card" id="infoCardA">
                <div class="point-info-header">
                    <div class="point-badge point-badge-a" style="flex-shrink:0;">A</div>
                    <div class="point-info-name" id="infoNameA">—</div>
                    <div class="point-info-badge point-info-badge-a">Awal</div>
                </div>
                <div id="infoBodyA"></div>
            </div>

            {{-- Info titik B --}}
            <div class="point-info-card" id="infoCardB">
                <div class="point-info-header">
                    <div class="point-badge point-badge-b" style="flex-shrink:0;">B</div>
                    <div class="point-info-name" id="infoNameB">—</div>
                    <div class="point-info-badge point-info-badge-b">Tujuan</div>
                </div>
                <div id="infoBodyB"></div>
            </div>

            {{-- Placeholder saat belum ada pilihan --}}
            <div class="empty-state" id="emptyState">
                <div class="empty-state-icon">🗺</div>
                <div class="empty-state-title">Pilih dua lokasi</div>
                <p>Cari dan pilih Titik A (awal) dan Titik B (tujuan) untuk melihat rute dan jarak di peta.</p>
            </div>

        </div>

    </aside>

    {{-- ─── MAP ─── --}}
    <div class="map-container">
        <div id="mainMap"></div>

        {{-- Distance badge on map --}}
        <div class="map-distance-badge" id="mapDistanceBadge">
            📏 <span id="mapDistanceText">—</span>
        </div>

        {{-- Controls --}}
        <div class="map-controls">
            <div class="map-ctrl-btn" id="btnFitRoute" title="Sesuaikan tampilan rute">
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
    const DEBOUNCE_MS  = 280;

    // ── STATE ────────────────────────────────────────────
    let allTempat = [];   // cache semua tempat dari API
    let selectedA = null; // { id, nama_tempat, latitude, longitude, ... }
    let selectedB = null;
    let markerA   = null;
    let markerB   = null;
    let routeLine = null;

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

    // ── CUSTOM ICONS ─────────────────────────────────────
    function makeIcon(label, color) {
        return L.divIcon({
            className: '',
            html: `<div style="
                background: ${color};
                color: white;
                font-family: var(--font-body, sans-serif);
                font-size: 12px;
                font-weight: 800;
                width: 30px; height: 30px;
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 2px 8px rgba(0,0,0,.3);
                border: 2px solid white;
            "><span style="transform:rotate(45deg)">${label}</span></div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -34],
        });
    }

    const iconA = makeIcon('A', '#4a7c59');
    const iconB = makeIcon('B', '#e85d5d');

    // ── FETCH SEMUA TEMPAT (sekali, lalu di-cache) ───────
    function loadAllTempat() {
        fetch('/api/tempat')
            .then(r => r.json())
            .then(data => { allTempat = data; })
            .catch(err => console.error('Gagal load tempat:', err));
    }

    // ── SEARCH FILTER ────────────────────────────────────
    function searchTempat(query) {
        if (!query || query.trim().length < 2) return [];
        const q = query.trim().toLowerCase();
        return allTempat
            .filter(t =>
                t.nama_tempat.toLowerCase().includes(q) ||
                (t.kecamatan && t.kecamatan.toLowerCase().includes(q)) ||
                (t.jalan && t.jalan.toLowerCase().includes(q))
            )
            .slice(0, 8);
    }

    // ── RENDER DROPDOWN ──────────────────────────────────
    function renderDropdown(dropdownEl, results, onSelect) {
        dropdownEl.innerHTML = '';
        if (!results.length) {
            dropdownEl.innerHTML = '<div class="dropdown-item-empty">Tidak ada hasil ditemukan.</div>';
            dropdownEl.classList.add('open');
            return;
        }
        results.forEach(t => {
            const item = document.createElement('div');
            item.className = 'dropdown-item';
            item.innerHTML = `
                <div class="dropdown-item-name">${escHtml(t.nama_tempat)}</div>
                <div class="dropdown-item-sub">${escHtml(t.kategori || '')}${t.kecamatan ? ' · ' + escHtml(t.kecamatan) : ''}</div>
            `;
            item.addEventListener('mousedown', e => {
                e.preventDefault();
                onSelect(t);
            });
            dropdownEl.appendChild(item);
        });
        dropdownEl.classList.add('open');
    }

    function closeDropdown(dropdownEl) {
        dropdownEl.classList.remove('open');
    }

    // ── SETUP AUTOCOMPLETE ───────────────────────────────
    function setupAutocomplete(inputEl, dropdownEl, clearBtn, onSelect) {
        inputEl.addEventListener('input', () => {
            clearTimeout(inputEl._timer);
            const q = inputEl.value;
            clearBtn.classList.toggle('visible', q.length > 0);
            if (q.length < 2) { closeDropdown(dropdownEl); return; }
            inputEl._timer = setTimeout(() => {
                const results = searchTempat(q);
                renderDropdown(dropdownEl, results, item => {
                    inputEl.value = item.nama_tempat;
                    closeDropdown(dropdownEl);
                    clearBtn.classList.add('visible');
                    onSelect(item);
                });
            }, DEBOUNCE_MS);
        });

        inputEl.addEventListener('focus', () => {
            if (inputEl.value.length >= 2 && dropdownEl.children.length > 0) {
                dropdownEl.classList.add('open');
            }
        });

        inputEl.addEventListener('blur', () => {
            setTimeout(() => closeDropdown(dropdownEl), 180);
        });

        clearBtn.addEventListener('click', () => {
            inputEl.value = '';
            clearBtn.classList.remove('visible');
            closeDropdown(dropdownEl);
            onSelect(null);
        });
    }

    // ── SELECT HANDLERS ──────────────────────────────────
    setupAutocomplete(
        document.getElementById('inputA'),
        document.getElementById('dropdownA'),
        document.getElementById('clearA'),
        item => { selectedA = item; onSelectionChange(); }
    );

    setupAutocomplete(
        document.getElementById('inputB'),
        document.getElementById('dropdownB'),
        document.getElementById('clearB'),
        item => { selectedB = item; onSelectionChange(); }
    );

    function onSelectionChange() {
        const hasA = !!selectedA;
        const hasB = !!selectedB;

        // Update markers
        updateMarker('A', selectedA);
        updateMarker('B', selectedB);

        // Enable hitung button only when both selected
        document.getElementById('btnHitungRute').disabled = !(hasA && hasB);

        // Auto-calculate saat keduanya sudah dipilih
        if (hasA && hasB) {
            hitungRute();
        } else {
            // Clear route line kalau salah satu dihapus
            clearRouteLine();
            hideDistanceBadge();
            document.getElementById('emptyState').style.display = '';
        }
    }

    // ── MARKERS ──────────────────────────────────────────
    function updateMarker(which, tempat) {
        if (which === 'A') {
            if (markerA) { map.removeLayer(markerA); markerA = null; }
            if (tempat) {
                markerA = L.marker([tempat.latitude, tempat.longitude], { icon: iconA })
                    .addTo(map)
                    .bindPopup(buildPopup(tempat, 'A'));
            }
        } else {
            if (markerB) { map.removeLayer(markerB); markerB = null; }
            if (tempat) {
                markerB = L.marker([tempat.latitude, tempat.longitude], { icon: iconB })
                    .addTo(map)
                    .bindPopup(buildPopup(tempat, 'B'));
            }
        }
    }

    function buildPopup(t, label) {
        const color     = label === 'A' ? 'var(--matcha-pale)' : '#fde8e8';
        const textColor = label === 'A' ? 'var(--matcha-deep)' : '#c0392b';
        return `
            <div class="popup-inner">
                <div class="popup-title">${escHtml(t.nama_tempat)}</div>
                ${t.jalan     ? `<div class="popup-row"><span>📍</span><span>${escHtml(t.jalan)}</span></div>`     : ''}
                ${t.kecamatan ? `<div class="popup-row"><span>🏘</span><span>${escHtml(t.kecamatan)}</span></div>` : ''}
                ${t.rating    ? `<div class="popup-row"><span>⭐</span><span>${t.rating}</span></div>`             : ''}
                <div style="margin-top:8px;">
                    <span style="
                        display:inline-block;font-size:.7rem;font-weight:700;
                        padding:2px 10px;border-radius:100px;
                        background:${color};color:${textColor};
                    ">Titik ${label}</span>
                </div>
            </div>
        `;
    }

    // ── HITUNG RUTE ──────────────────────────────────────
    function hitungRute() {
        if (!selectedA || !selectedB) return;

        showLoading(true);
        clearRouteLine();

        const urlOSRM = `https://router.project-osrm.org/route/v1/driving/`
            + `${selectedA.longitude},${selectedA.latitude};`
            + `${selectedB.longitude},${selectedB.latitude}`
            + `?overview=full&geometries=geojson&steps=false`;

        fetch(urlOSRM)
            .then(r => r.json())
            .then(data => {
                if (data.code !== 'Ok' || !data.routes.length) throw new Error('Rute tidak ditemukan');

                const route     = data.routes[0];
                const jarakKm   = route.distance / 1000;          // meter → km
                const durasiMin = Math.round(route.duration / 60); // detik → menit
                const coords    = route.geometry.coordinates.map(c => [c[1], c[0]]); // [lng,lat] → [lat,lng]

                drawRoute(selectedA, selectedB, jarakKm, durasiMin, coords);
                showLoading(false);
            })
            .catch(() => {
                // Fallback: garis lurus Haversine
                const jarakKm = haversine(
                    selectedA.latitude, selectedA.longitude,
                    selectedB.latitude, selectedB.longitude
                );
                drawRoute(selectedA, selectedB, jarakKm, null, null);
                showLoading(false);

                const errEl = document.getElementById('errorState');
                errEl.textContent = '⚠ Rute jalan tidak tersedia, menampilkan garis lurus.';
                errEl.classList.add('visible');
                setTimeout(() => errEl.classList.remove('visible'), 4000);
            });
    }

    // ── HAVERSINE ────────────────────────────────────────
    function haversine(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = deg2rad(lat2 - lat1);
        const dLng = deg2rad(lng2 - lng1);
        const a = Math.sin(dLat/2)**2
                + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLng/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    function deg2rad(d) { return d * Math.PI / 180; }

    // ── FORMAT JARAK ─────────────────────────────────────
    /**
     * Mengembalikan string lengkap dengan satuan, misalnya "1.23 km" atau "850 m".
     * Dipakai untuk teks singkat (badge peta, dll).
     */
    function formatJarak(km) {
        if (km < 0.1) return `${(km * 1000).toFixed(0)} m`;
        return `${km.toFixed(2)} km`;
    }

    // ── DRAW ROUTE ───────────────────────────────────────
    function drawRoute(a, b, jarakKm, durasiMin, routeCoords) {
        // Koordinat: pakai jalur OSRM kalau ada, fallback garis lurus
        const latlngs = routeCoords || [
            [a.latitude, a.longitude],
            [b.latitude, b.longitude],
        ];
        const isRealRoute = !!routeCoords;

        // Garis rute
        routeLine = L.polyline(latlngs, {
            color:     '#4a7c59',
            weight:    isRealRoute ? 5 : 4,
            opacity:   .88,
            dashArray: isRealRoute ? null : '10 6',   // solid kalau rute nyata, dashed kalau garis lurus
            lineJoin:  'round',
            lineCap:   'round',
        }).addTo(map);

        // Fit map ke rute
        map.fitBounds(routeLine.getBounds().pad(0.25), { animate: true });

        // ── Teks jarak yang ditampilkan ──
        const jarakStr   = formatJarak(jarakKm);   // "1.23 km" atau "850 m"
        const labelJenis = isRealRoute ? 'Jarak Jalan' : 'Jarak Garis Lurus';
        const durasiHtml = durasiMin != null
            ? `<div style="font-size:.82rem;color:var(--matcha);font-weight:600;margin-top:4px;">⏱ ~${durasiMin} menit berkendara</div>`
            : `<div style="font-size:.75rem;color:var(--ink-soft);margin-top:4px;">estimasi jalan tidak tersedia</div>`;

        // Update sidebar distance card
        // BUG FIX: distanceValue sekarang diisi lengkap oleh formatJarak (sudah include satuan)
        document.getElementById('distanceCard').querySelector('.distance-label').textContent = labelJenis;
        document.getElementById('distanceValue').textContent = jarakStr;
        document.getElementById('distanceFromTo').innerHTML = `
            <strong>${escHtml(a.nama_tempat)}</strong><br>
            → <strong>${escHtml(b.nama_tempat)}</strong>
            ${durasiHtml}
        `;
        document.getElementById('distanceCard').classList.add('visible');

        // Update map badge
        document.getElementById('mapDistanceText').textContent =
            `${jarakStr}${durasiMin != null ? ' · ~' + durasiMin + ' mnt' : ''}`;
        document.getElementById('mapDistanceBadge').classList.add('visible');

        // Info cards
        renderInfoCard('A', a);
        renderInfoCard('B', b);

        // Sembunyikan empty state
        document.getElementById('emptyState').style.display = 'none';
    }

    function renderInfoCard(which, tempat) {
        const cardEl = document.getElementById(`infoCard${which}`);
        const nameEl = document.getElementById(`infoName${which}`);
        const bodyEl = document.getElementById(`infoBody${which}`);

        nameEl.textContent = tempat.nama_tempat;

        const rows = [
            tempat.jalan     ? { icon: '📍', text: tempat.jalan }              : null,
            tempat.kecamatan ? { icon: '🏘', text: tempat.kecamatan }          : null,
            tempat.kategori  ? { icon: '🏷', text: tempat.kategori }           : null,
            tempat.rating    ? { icon: '⭐', text: `Rating ${tempat.rating}` } : null,
            tempat.kontak    ? { icon: '📞', text: tempat.kontak }             : null,
        ].filter(Boolean);

        bodyEl.innerHTML = rows.map(r => `
            <div class="point-info-row">
                <span class="point-info-icon">${r.icon}</span>
                <span>${escHtml(r.text)}</span>
            </div>
        `).join('');

        cardEl.classList.add('visible');
    }

    // ── CLEAR / RESET ────────────────────────────────────
    function clearRouteLine() {
        if (routeLine) { map.removeLayer(routeLine); routeLine = null; }
        document.getElementById('distanceCard').classList.remove('visible');
        document.getElementById('infoCardA').classList.remove('visible');
        document.getElementById('infoCardB').classList.remove('visible');
    }

    function hideDistanceBadge() {
        document.getElementById('mapDistanceBadge').classList.remove('visible');
    }

    function showLoading(show) {
        document.getElementById('loadingState').classList.toggle('visible', show);
        // Sembunyikan empty state saat loading, tampilkan kembali saat selesai
        // (hanya kalau rute belum tersedia — diatur oleh drawRoute / onSelectionChange)
        if (show) {
            document.getElementById('emptyState').style.display = 'none';
        }
    }

    // ── BUTTONS ──────────────────────────────────────────
    document.getElementById('btnHitungRute').addEventListener('click', hitungRute);

    document.getElementById('btnSwap').addEventListener('click', () => {
        const tmpData = selectedA;
        const tmpVal  = document.getElementById('inputA').value;
        const visA    = document.getElementById('clearA').classList.contains('visible');

        selectedA = selectedB;
        selectedB = tmpData;

        document.getElementById('inputA').value = document.getElementById('inputB').value;
        document.getElementById('inputB').value = tmpVal;

        document.getElementById('clearA').classList.toggle('visible',
            document.getElementById('clearB').classList.contains('visible'));
        document.getElementById('clearB').classList.toggle('visible', visA);

        onSelectionChange();
    });

    document.getElementById('btnReset').addEventListener('click', () => {
        selectedA = null;
        selectedB = null;

        ['A','B'].forEach(w => {
            document.getElementById(`input${w}`).value = '';
            document.getElementById(`clear${w}`).classList.remove('visible');
            closeDropdown(document.getElementById(`dropdown${w}`));
        });

        if (markerA) { map.removeLayer(markerA); markerA = null; }
        if (markerB) { map.removeLayer(markerB); markerB = null; }
        clearRouteLine();
        hideDistanceBadge();
        document.getElementById('btnHitungRute').disabled = true;
        document.getElementById('emptyState').style.display = '';
        document.getElementById('errorState').classList.remove('visible');
        map.setView(MEDAN_CENTER, 12, { animate: true });
    });

    document.getElementById('btnFitRoute').addEventListener('click', () => {
        if (routeLine) {
            map.fitBounds(routeLine.getBounds().pad(0.25), { animate: true });
        } else if (markerA || markerB) {
            const pts = [];
            if (markerA) pts.push(markerA.getLatLng());
            if (markerB) pts.push(markerB.getLatLng());
            if (pts.length) map.fitBounds(L.latLngBounds(pts).pad(0.4), { animate: true });
        } else {
            map.setView(MEDAN_CENTER, 12, { animate: true });
        }
    });

    // ── UTIL ─────────────────────────────────────────────
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ── INIT ─────────────────────────────────────────────
    loadAllTempat();

})();
</script>
@endpush