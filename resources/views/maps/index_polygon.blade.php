@extends('layouts.app')

@section('title', 'Maps — Polygon Kecamatan')

@push('styles')
<style>
    /* ─────────────────────────────────────────
       POLYGON PAGE  —  full-height split layout
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

    /* Kecamatan dropdown */
    .select-kecamatan {
        width: 100%;
        padding: 9px 14px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        font-family: var(--font-body);
        font-size: .85rem;
        color: var(--ink);
        background: var(--white);
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b8a78' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        cursor: pointer;
        transition: border-color .18s, box-shadow .18s;
        outline: none;
    }
    .select-kecamatan:focus {
        border-color: var(--matcha);
        box-shadow: 0 0 0 3px rgba(74,124,89,.12);
    }
    .select-kecamatan:hover { border-color: var(--matcha-light); }

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

    /* ── KECAMATAN LIST ── */
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

    .kec-list {
        flex: 1;
        overflow-y: auto;
        padding: 0 16px 16px;
        scrollbar-width: thin;
        scrollbar-color: var(--matcha-pale) transparent;
    }
    .kec-list::-webkit-scrollbar { width: 4px; }
    .kec-list::-webkit-scrollbar-track { background: transparent; }
    .kec-list::-webkit-scrollbar-thumb { background: var(--matcha-pale); border-radius: 4px; }

    /* ── KECAMATAN CARD ── */
    .kec-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: background .18s, border-color .18s;
        border: 1.5px solid transparent;
        margin-bottom: 5px;
    }
    .kec-card:hover {
        background: var(--matcha-ghost);
        border-color: var(--matcha-pale);
    }
    .kec-card.active {
        background: var(--matcha-ghost);
        border-color: var(--matcha-light);
    }

    .kec-color-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        flex-shrink: 0;
        border: 1.5px solid rgba(0,0,0,.1);
    }

    .kec-info { flex: 1; min-width: 0; }
    .kec-name {
        font-weight: 600;
        font-size: .875rem;
        color: var(--ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kec-sub {
        font-size: .74rem;
        color: var(--ink-soft);
        margin-top: 2px;
    }

    .kec-count-badge {
        font-size: .72rem;
        font-weight: 700;
        background: var(--matcha-pale);
        color: var(--matcha-deep);
        padding: 2px 9px;
        border-radius: 100px;
        flex-shrink: 0;
    }

    /* ── EMPTY STATE ── */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--ink-soft);
    }
    .empty-state svg { margin-bottom: 12px; opacity: .4; }
    .empty-state p { font-size: .88rem; }

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

    /* ── POLYGON INFO PANEL (overlay) ── */
    .polygon-info-panel {
        position: absolute;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        padding: 18px 22px;
        min-width: 260px;
        max-width: 360px;
        z-index: 800;
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s, transform .25s;
        font-family: var(--font-body);
    }
    .polygon-info-panel.visible {
        opacity: 1;
        pointer-events: auto;
        transform: translateX(-50%) translateY(0);
    }

    .panel-kec-name {
        font-family: var(--font-display);
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel-kec-dot {
        width: 12px; height: 12px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .panel-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 14px;
    }
    .panel-stat {
        text-align: center;
        background: var(--matcha-ghost);
        border-radius: var(--radius-sm);
        padding: 8px 4px;
    }
    .panel-stat-num {
        font-family: var(--font-display);
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--matcha-deep);
        line-height: 1;
    }
    .panel-stat-lbl {
        font-size: .67rem;
        color: var(--ink-soft);
        margin-top: 3px;
        letter-spacing: .02em;
    }

    .panel-close {
        position: absolute;
        top: 12px; right: 14px;
        width: 24px; height: 24px;
        display: flex; align-items: center; justify-content: center;
        border: none;
        background: var(--matcha-pale);
        border-radius: 50%;
        cursor: pointer;
        color: var(--ink-mid);
        font-size: .8rem;
        transition: background .18s;
    }
    .panel-close:hover { background: var(--matcha-light); color: var(--white); }

    .panel-btn-row {
        display: flex;
        gap: 8px;
    }
    .panel-btn {
        flex: 1;
        padding: 8px;
        border-radius: var(--radius-sm);
        font-size: .78rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        font-family: var(--font-body);
        transition: all .18s;
    }
    .panel-btn-primary {
        background: var(--matcha);
        color: var(--white);
    }
    .panel-btn-primary:hover { background: var(--matcha-deep); }
    .panel-btn-outline {
        background: var(--white);
        color: var(--ink-mid);
        border: 1.5px solid var(--border);
    }
    .panel-btn-outline:hover { background: var(--matcha-ghost); }

    /* Override Leaflet */
    .leaflet-popup-content-wrapper {
        border-radius: var(--radius-md) !important;
        box-shadow: var(--shadow-lg) !important;
        border: 1px solid var(--border) !important;
        padding: 0 !important;
        overflow: hidden;
    }
    .leaflet-popup-content { margin: 0 !important; width: auto !important; min-width: 200px; }
    .leaflet-popup-tip-container { display: none; }

    .poly-tooltip {
        font-family: var(--font-body);
        font-size: .82rem;
        font-weight: 600;
        background: var(--white) !important;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-sm) !important;
        box-shadow: var(--shadow-sm) !important;
        color: var(--ink) !important;
        padding: 5px 12px !important;
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

    /* Legend overlay */
    .map-legend {
        position: absolute;
        bottom: 16px;
        right: 16px;
        z-index: 800;
        background: rgba(255,255,255,.95);
        backdrop-filter: blur(8px);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 14px;
        box-shadow: var(--shadow-sm);
        font-size: .75rem;
        color: var(--ink-mid);
        max-width: 160px;
    }
    .legend-title {
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 8px;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 5px;
        font-size: .74rem;
    }
    .legend-swatch {
        width: 14px; height: 10px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    /* Skeleton */
    .skel-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        margin-bottom: 5px;
        border-radius: var(--radius-md);
        background: var(--matcha-ghost);
        animation: shimmer 1.5s infinite;
    }
    @keyframes shimmer { 0%,100% { opacity:1; } 50% { opacity:.5; } }
    .skel-dot { width:12px; height:12px; border-radius:3px; background:var(--matcha-pale); flex-shrink:0; }
    .skel-lines { flex:1; }
    .skel-line { height:9px; border-radius:4px; background:var(--matcha-pale); margin-bottom:6px; }
    .skel-sm { width:55%; }

    /* Mobile */
    @media (max-width: 768px) {
        .maps-wrapper { flex-direction: column; }
        .sidebar {
            width: 100%;
            min-width: 0;
            height: 45vh;
            border-right: none;
            border-bottom: 1px solid var(--border);
        }
        .polygon-info-panel {
            bottom: 12px;
            min-width: 240px;
            max-width: calc(100vw - 40px);
        }
    }
</style>
@endpush

@section('content')

<div class="maps-wrapper">

    {{-- ─── SIDEBAR ─── --}}
    <aside class="sidebar">

        <div class="sidebar-header">
            <div class="sidebar-title">Polygon Kecamatan</div>
            <div class="sidebar-subtitle">Visualisasi Wilayah Kota Medan</div>
        </div>

        <div class="filter-panel">
            <div class="filter-group">
                <span class="filter-label">Filter Kecamatan</span>
                <select class="select-kecamatan" id="selectKecamatan">
                    <option value="all">— Tampilkan Semua —</option>
                    @foreach ($kecamatans as $kec)
                        <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn-reset" id="btnReset">Reset filter</button>
        </div>

        {{-- Count --}}
        <div class="results-header">
            <div class="results-count">
                <span id="countNum">—</span> kecamatan ditampilkan
            </div>
        </div>

        {{-- List kecamatan --}}
        <div class="kec-list" id="kecList">
            @for ($i = 0; $i < 8; $i++)
            <div class="skel-card">
                <div class="skel-dot"></div>
                <div class="skel-lines">
                    <div class="skel-line"></div>
                    <div class="skel-line skel-sm"></div>
                </div>
            </div>
            @endfor
        </div>

    </aside>

    {{-- ─── MAP ─── --}}
    <div class="map-container">
        <div id="mainMap"></div>

        {{-- Controls --}}
        <div class="map-controls">
            <div class="map-ctrl-btn" id="btnFitAll" title="Lihat semua kecamatan">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M4 8V4h4M16 4h4v4M4 16v4h4M16 20h4v-4"/>
                </svg>
            </div>
            <div class="map-ctrl-btn" id="btnToggleLabels" title="Toggle label">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M7 7h10M7 12h6M7 17h4"/>
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                </svg>
            </div>
        </div>

        {{-- Info panel (klik polygon) --}}
        <div class="polygon-info-panel" id="polyInfoPanel">
            <button class="panel-close" id="panelClose" title="Tutup">✕</button>
            <div class="panel-kec-name" id="panelName">
                <div class="panel-kec-dot" id="panelDot"></div>
                <span id="panelNameText">—</span>
            </div>
            <div class="panel-stats" id="panelStats"></div>
            <div class="panel-btn-row">
                <button class="panel-btn panel-btn-primary" id="panelBtnZoom">Perbesar Wilayah</button>
                <button class="panel-btn panel-btn-outline" id="panelBtnMapsPoint">Lihat Titik Lokasi</button>
            </div>
        </div>

        {{-- Legend --}}
        <div class="map-legend" id="mapLegend">
            <div class="legend-title">Kecamatan</div>
            <div id="legendItems"></div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {

    // ── CONFIG ──────────────────────────────────────────
    const MEDAN_CENTER = [3.585, 98.676];

    // Warna per kecamatan — palet matcha-based multi-tone
    const PALETTE = [
        '#4a7c59','#3b6e8a','#7c4a6e','#8a6e3b','#3b8a6e',
        '#6e3b8a','#8a3b4a','#4a6e8a','#6e8a3b','#8a4a3b',
        '#3b4a8a','#6e4a3b','#3b8a4a','#8a7c3b','#4a8a7c',
        '#7c3b6e','#3b6e4a','#6e7c3b','#4a3b8a','#8a6e7c',
        '#3b7c6e',
    ];

    // ── STATE ────────────────────────────────────────────
    let allKecamatans   = [];   // raw data dari server
    let filteredKecs    = [];   // setelah filter dropdown
    let polygonLayers   = {};   // kec_id → L.GeoJSON layer
    let labelMarkers    = {};   // kec_id → L.Marker label
    let selectedKecId   = null; // polygon yang sedang aktif/di-klik
    let showLabels      = true;
    let currentFilter   = 'all';
    let activePanelKec  = null; // data kecamatan yg tampil di panel

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

    // ── FETCH DATA KECAMATAN (polygon + stats tempat) ──
    function fetchPolygons() {
        fetch('/api/kecamatan/polygon')
            .then(r => r.json())
            .then(data => {
                allKecamatans = data;
                applyFilter();
            })
            .catch(err => {
                console.error('Gagal fetch polygon:', err);
                document.getElementById('kecList').innerHTML = `
                    <div class="empty-state">
                        <p>Gagal memuat data.<br>Periksa koneksi atau server.</p>
                    </div>`;
            });
    }

    // ── APPLY FILTER DROPDOWN ───────────────────────────
    function applyFilter() {
        if (currentFilter === 'all') {
            filteredKecs = allKecamatans;
        } else {
            filteredKecs = allKecamatans.filter(k => k.id == currentFilter);
        }
        renderPolygons();
        renderSidebarList();
        document.getElementById('countNum').textContent = filteredKecs.length;
    }

    // ── COLOR HELPER ────────────────────────────────────
    function colorFor(id) {
        return PALETTE[(id - 1) % PALETTE.length];
    }

    // ── RENDER POLYGONS ──────────────────────────────────
    function renderPolygons() {
        // Hapus semua layer lama
        Object.values(polygonLayers).forEach(l => map.removeLayer(l));
        Object.values(labelMarkers).forEach(m => map.removeLayer(m));
        polygonLayers = {};
        labelMarkers  = {};

        filteredKecs.forEach(kec => {
            if (!kec.geojson) return;

            let geojsonData;
            try {
                geojsonData = typeof kec.geojson === 'string'
                    ? JSON.parse(kec.geojson)
                    : kec.geojson;
            } catch (e) {
                console.warn('GeoJSON parse error for', kec.nama_kecamatan, e);
                return;
            }

            const color   = colorFor(kec.id);
            const isActive = selectedKecId === kec.id;

            const layer = L.geoJSON(geojsonData, {
                style: {
                    color:       color,
                    weight:      isActive ? 3 : 2,
                    opacity:     1,
                    fillColor:   color,
                    fillOpacity: isActive ? 0.35 : 0.15,
                },
                onEachFeature: (feature, featureLayer) => {
                    featureLayer.on({
                        mouseover: () => hoverPolygon(featureLayer, kec, color),
                        mouseout:  () => resetPolygon(featureLayer, kec, color),
                        click:     () => selectPolygon(kec, color),
                    });
                },
            }).addTo(map);

            polygonLayers[kec.id] = layer;

            // Label di tengah polygon (menggunakan centroid)
            if (showLabels) {
                addLabel(kec, geojsonData, color);
            }
        });

        updateLegend();

        // Fit bounds kalau filter aktif
        if (filteredKecs.length > 0 && currentFilter !== 'all') {
            const bounds = L.latLngBounds([]);
            filteredKecs.forEach(kec => {
                if (polygonLayers[kec.id]) {
                    bounds.extend(polygonLayers[kec.id].getBounds());
                }
            });
            if (bounds.isValid()) {
                map.fitBounds(bounds.pad(0.15), { animate: true });
            }
        }
    }

    // ── LABEL CENTROID ───────────────────────────────────
    function addLabel(kec, geojsonData, color) {
        try {
            const tempLayer = L.geoJSON(geojsonData);
            const center    = tempLayer.getBounds().getCenter();

            const label = L.marker(center, {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="
                        background: rgba(255,255,255,.88);
                        border: 1.5px solid ${color};
                        border-radius: 100px;
                        padding: 3px 9px;
                        font-family: var(--font-body, sans-serif);
                        font-size: 11px;
                        font-weight: 700;
                        color: ${color};
                        white-space: nowrap;
                        pointer-events: none;
                        box-shadow: 0 1px 4px rgba(0,0,0,.15);
                    ">${kec.nama_kecamatan}</div>`,
                    iconAnchor: [0, 0],
                }),
                interactive: false,
                zIndexOffset: -100,
            }).addTo(map);

            labelMarkers[kec.id] = label;
        } catch (e) {
            // label gagal — skip saja
        }
    }

    // ── HOVER ────────────────────────────────────────────
    function hoverPolygon(layer, kec, color) {
        if (selectedKecId === kec.id) return;
        layer.setStyle({ fillOpacity: 0.3, weight: 2.5 });
    }

    function resetPolygon(layer, kec, color) {
        if (selectedKecId === kec.id) return;
        layer.setStyle({ fillOpacity: 0.15, weight: 2 });
    }

    // ── SELECT POLYGON ───────────────────────────────────
    function selectPolygon(kec, color) {
        // Reset style polygon sebelumnya
        if (selectedKecId && polygonLayers[selectedKecId]) {
            polygonLayers[selectedKecId].setStyle({ fillOpacity: 0.15, weight: 2 });
        }

        selectedKecId = kec.id;
        if (polygonLayers[kec.id]) {
            polygonLayers[kec.id].setStyle({ fillOpacity: 0.35, weight: 3 });
        }

        showInfoPanel(kec, color);
        highlightSidebarCard(kec.id);
    }

    // ── INFO PANEL ───────────────────────────────────────
    function showInfoPanel(kec, color) {
        activePanelKec = kec;

        document.getElementById('panelNameText').textContent = kec.nama_kecamatan;
        document.getElementById('panelDot').style.background = color;
        document.getElementById('panelDot').style.borderColor = color;

        // Stats grid
        const stats = kec.stats || {};
        const statItems = [
            { num: stats.total       ?? 0, lbl: 'Total Lokasi' },
            { num: stats.kuliner     ?? 0, lbl: 'Kuliner' },
            { num: stats.wisata      ?? 0, lbl: 'Wisata' },
            { num: stats.kesehatan   ?? 0, lbl: 'Kesehatan' },
            { num: stats.kemasyarakatan ?? 0, lbl: 'Kemasyarakatan' },
            { num: stats.transportasi ?? 0, lbl: 'Transportasi' },
        ];

        document.getElementById('panelStats').innerHTML = statItems.map(s => `
            <div class="panel-stat">
                <div class="panel-stat-num">${s.num}</div>
                <div class="panel-stat-lbl">${s.lbl}</div>
            </div>
        `).join('');

        document.getElementById('polyInfoPanel').classList.add('visible');
    }

    function hideInfoPanel() {
        document.getElementById('polyInfoPanel').classList.remove('visible');
        if (selectedKecId && polygonLayers[selectedKecId]) {
            polygonLayers[selectedKecId].setStyle({ fillOpacity: 0.15, weight: 2 });
        }
        selectedKecId   = null;
        activePanelKec  = null;
        document.querySelectorAll('.kec-card').forEach(c => c.classList.remove('active'));
    }

    // ── SIDEBAR LIST ─────────────────────────────────────
    function renderSidebarList() {
        const list = document.getElementById('kecList');

        if (!filteredKecs.length) {
            list.innerHTML = `
                <div class="empty-state">
                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M9 20H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v6"/><path d="M16 12h.01M12 12h.01M8 12h.01"/><path stroke-linecap="round" d="M19 19l-2-2m0 0a3 3 0 10-4.243-4.243A3 3 0 0017 17z"/></svg>
                    <p>Tidak ada kecamatan ditemukan.</p>
                </div>`;
            return;
        }

        list.innerHTML = filteredKecs.map(kec => {
            const color = colorFor(kec.id);
            const total = kec.stats?.total ?? 0;
            return `
            <div class="kec-card" data-id="${kec.id}" onclick="clickSidebarCard(${kec.id})">
                <div class="kec-color-dot" style="background:${color}; border-color:${color};"></div>
                <div class="kec-info">
                    <div class="kec-name">${kec.nama_kecamatan}</div>
                    <div class="kec-sub">${total} lokasi terdaftar</div>
                </div>
                <div class="kec-count-badge">${total}</div>
            </div>`;
        }).join('');
    }

    function highlightSidebarCard(id) {
        document.querySelectorAll('.kec-card').forEach(c => c.classList.remove('active'));
        const card = document.querySelector(`.kec-card[data-id="${id}"]`);
        if (card) {
            card.classList.add('active');
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    window.clickSidebarCard = function (id) {
        const kec = filteredKecs.find(k => k.id == id);
        if (!kec) return;

        const color = colorFor(kec.id);
        selectPolygon(kec, color);

        // Zoom ke polygon
        if (polygonLayers[id]) {
            map.fitBounds(polygonLayers[id].getBounds().pad(0.15), { animate: true });
        }
    };

    // ── LEGEND ───────────────────────────────────────────
    function updateLegend() {
        const legend = document.getElementById('mapLegend');
        const items  = document.getElementById('legendItems');

        // Sembunyikan legend kalau terlalu banyak (tampilkan semua kalau ≤ 10)
        const shown = filteredKecs.length <= 10 ? filteredKecs : filteredKecs.slice(0, 10);

        items.innerHTML = shown.map(kec => `
            <div class="legend-item">
                <div class="legend-swatch" style="background:${colorFor(kec.id)};"></div>
                <span>${kec.nama_kecamatan}</span>
            </div>
        `).join('');

        if (filteredKecs.length > 10) {
            items.innerHTML += `<div style="font-size:.7rem;color:var(--ink-soft);margin-top:4px;">+${filteredKecs.length - 10} lainnya…</div>`;
        }

        legend.style.display = filteredKecs.length === 0 ? 'none' : '';
    }

    // ── MAP CONTROLS ─────────────────────────────────────
    document.getElementById('btnFitAll').addEventListener('click', () => {
        if (!filteredKecs.length) return;
        const bounds = L.latLngBounds([]);
        filteredKecs.forEach(kec => {
            if (polygonLayers[kec.id]) bounds.extend(polygonLayers[kec.id].getBounds());
        });
        if (bounds.isValid()) map.fitBounds(bounds.pad(0.05), { animate: true });
    });

    document.getElementById('btnToggleLabels').addEventListener('click', () => {
        showLabels = !showLabels;
        Object.values(labelMarkers).forEach(m => {
            showLabels ? map.addLayer(m) : map.removeLayer(m);
        });
        document.getElementById('btnToggleLabels').style.color = showLabels
            ? 'var(--matcha-deep)' : 'var(--ink-soft)';
    });

    // ── PANEL BUTTONS ────────────────────────────────────
    document.getElementById('panelClose').addEventListener('click', hideInfoPanel);

    document.getElementById('panelBtnZoom').addEventListener('click', () => {
        if (!activePanelKec) return;
        const layer = polygonLayers[activePanelKec.id];
        if (layer) map.fitBounds(layer.getBounds().pad(0.1), { animate: true });
    });

    document.getElementById('panelBtnMapsPoint').addEventListener('click', () => {
        if (!activePanelKec) return;
        // Redirect ke maps-point dengan filter kecamatan
        window.location.href = `/maps?kecamatan=${activePanelKec.id}`;
    });

    // ── FILTER DROPDOWN ──────────────────────────────────
    document.getElementById('selectKecamatan').addEventListener('change', function () {
        currentFilter = this.value;
        selectedKecId = null;
        hideInfoPanel();
        applyFilter();
    });

    // ── RESET ────────────────────────────────────────────
    document.getElementById('btnReset').addEventListener('click', () => {
        currentFilter = 'all';
        document.getElementById('selectKecamatan').value = 'all';
        selectedKecId = null;
        hideInfoPanel();
        applyFilter();
        map.setView(MEDAN_CENTER, 12, { animate: true });
    });

    // ── INIT ─────────────────────────────────────────────
    fetchPolygons();

})();
</script>
@endpush