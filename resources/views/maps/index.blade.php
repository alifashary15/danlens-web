@extends('layouts.app')

@section('title', 'Maps')

@push('styles')
<style>
    /* ─────────────────────────────────────────
       MAPS GABUNGAN  —  Point + Polygon + Line
    ───────────────────────────────────────── */

    body.maps-page > footer { display: none; }

    .maps-wrapper {
        display: flex;
        height: calc(100vh - 64px);
        overflow: hidden;
    }

    /* ── SIDEBAR ── */
    .sidebar {
        width: 340px;
        min-width: 340px;
        background: var(--white);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 10;
    }

    .sidebar-header {
        padding: 20px 24px 0;
        flex-shrink: 0;
    }
    .sidebar-title {
        font-family: var(--font-display);
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -.01em;
        margin-bottom: 2px;
    }
    .sidebar-subtitle {
        font-size: .78rem;
        color: var(--ink-soft);
        margin-bottom: 14px;
    }

    /* ── SEARCH BAR ── */
    .sidebar-search {
        padding: 0 24px 14px;
        flex-shrink: 0;
    }
    .search-wrap {
        position: relative;
    }
    .search-icon {
        position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
        font-size: .85rem; pointer-events: none; color: var(--ink-soft);
    }
    .search-input {
        width: 100%; padding: 8px 32px 8px 32px;
        border-radius: var(--radius-sm); border: 1.5px solid var(--border);
        font-family: var(--font-body); font-size: .83rem; color: var(--ink);
        background: var(--white); outline: none; box-sizing: border-box;
        transition: border-color .18s, box-shadow .18s;
    }
    .search-input:focus { border-color: var(--matcha); box-shadow: 0 0 0 3px rgba(74,124,89,.12); }
    .search-input::placeholder { color: var(--ink-soft); }
    .search-clear {
        position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: var(--ink-soft); font-size: .8rem; padding: 2px; display: none;
    }
    .search-clear.visible { display: block; }
    .search-clear:hover { color: var(--ink); }

    .search-dropdown {
        position: absolute; top: calc(100% + 3px); left: 0; right: 0;
        background: var(--white); border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); box-shadow: var(--shadow-md);
        z-index: 9999; max-height: 220px; overflow-y: auto; display: none;
        scrollbar-width: thin;
    }
    .search-dropdown.open { display: block; }

    /* ── FILTER PANEL (default view) ── */
    .filter-panel {
        padding: 14px 24px 16px;
        flex-shrink: 0;
        border-bottom: 1px solid var(--border);
    }
    .filter-group { margin-bottom: 13px; }
    .filter-group:last-child { margin-bottom: 0; }
    .filter-label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--ink-soft);
        margin-bottom: 7px;
        display: block;
    }

    /* Chips */
    .chip-group { display: flex; flex-wrap: wrap; gap: 5px; }
    .chip {
        padding: 4px 11px;
        border-radius: 100px;
        border: 1.5px solid var(--border);
        font-size: .76rem;
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
    .chip-all.active { background: var(--matcha); color: var(--white); }

    /* Slider */
    .slider-row { display: flex; align-items: center; gap: 10px; }
    .slider-row input[type=range] {
        flex: 1; height: 4px;
        -webkit-appearance: none; appearance: none;
        background: var(--matcha-pale);
        border-radius: 100px; outline: none; cursor: pointer;
    }
    .slider-row input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 17px; height: 17px; border-radius: 50%;
        background: var(--matcha);
        border: 2px solid var(--white);
        box-shadow: 0 1px 4px rgba(74,124,89,.4); cursor: pointer;
    }
    .slider-val {
        font-size: .8rem; font-weight: 700;
        color: var(--matcha-deep); min-width: 36px; text-align: right;
    }

    /* Toggle switch */
    .toggle-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
    .toggle-switch {
        position: relative; width: 40px; height: 22px; flex-shrink: 0;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-track {
        position: absolute; inset: 0;
        background: var(--border); border-radius: 100px; cursor: pointer; transition: background .2s;
    }
    .toggle-track::after {
        content: ''; position: absolute;
        left: 3px; top: 3px; width: 16px; height: 16px;
        background: var(--white); border-radius: 50%; transition: transform .2s;
    }
    .toggle-switch input:checked ~ .toggle-track { background: var(--matcha); }
    .toggle-switch input:checked ~ .toggle-track::after { transform: translateX(18px); }
    .toggle-label { font-size: .82rem; color: var(--ink-mid); font-weight: 500; }

    .jarak-control { display: none; }
    .jarak-control.visible { display: block; }

    .loc-status {
        font-size: .74rem; color: var(--ink-soft);
        display: flex; align-items: center; gap: 5px; margin-top: 6px;
    }
    .loc-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--matcha-light); flex-shrink: 0; }
    .loc-dot.active { background: #22c55e; }

    /* ── KECAMATAN CHECKBOX (dalam filter panel) ── */
    .kec-checkbox-list {
        display: flex; flex-direction: column; gap: 3px;
        max-height: 150px; overflow-y: auto;
        scrollbar-width: thin; scrollbar-color: var(--matcha-pale) transparent;
        padding-right: 4px;
    }
    .kec-checkbox-list::-webkit-scrollbar { width: 3px; }
    .kec-checkbox-list::-webkit-scrollbar-thumb { background: var(--matcha-pale); border-radius: 3px; }

    .kec-checkbox-item {
        display: flex; align-items: center; gap: 8px;
        padding: 5px 8px; border-radius: var(--radius-sm);
        cursor: pointer; transition: background .14s;
    }
    .kec-checkbox-item:hover { background: var(--matcha-ghost); }
    .kec-checkbox-item input[type=checkbox] {
        accent-color: var(--matcha);
        width: 14px; height: 14px; flex-shrink: 0; cursor: pointer;
    }
    .kec-checkbox-dot {
        width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0;
    }
    .kec-checkbox-label { font-size: .8rem; color: var(--ink-mid); font-weight: 500; flex: 1; }

    .kec-all-btn {
        font-size: .73rem; font-weight: 600;
        color: var(--matcha); cursor: pointer; padding: 3px 0; margin-bottom: 5px;
        display: inline-block;
        background: none; border: none; font-family: var(--font-body);
    }
    .kec-all-btn:hover { color: var(--matcha-deep); }

    /* Reset */
    .btn-reset {
        width: 100%; padding: 8px;
        background: transparent; border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); font-family: var(--font-body);
        font-size: .78rem; font-weight: 600; color: var(--ink-soft);
        cursor: pointer; transition: all .18s; margin-top: 4px;
    }
    .btn-reset:hover { border-color: var(--matcha-light); color: var(--matcha-deep); background: var(--matcha-ghost); }

    /* ── RESULTS LIST (Point) ── */
    .results-header {
        padding: 12px 24px 8px; display: flex;
        align-items: center; justify-content: space-between; flex-shrink: 0;
    }
    .results-count { font-size: .76rem; font-weight: 600; color: var(--ink-soft); }
    .results-count span { font-family: var(--font-display); font-size: .95rem; font-weight: 700; color: var(--matcha-deep); }

    .results-list {
        flex: 1; overflow-y: auto; padding: 0 14px 14px;
        scrollbar-width: thin; scrollbar-color: var(--matcha-pale) transparent;
    }
    .results-list::-webkit-scrollbar { width: 4px; }
    .results-list::-webkit-scrollbar-thumb { background: var(--matcha-pale); border-radius: 4px; }

    .place-card {
        display: flex; gap: 11px; padding: 11px;
        border-radius: var(--radius-md); cursor: pointer;
        transition: background .18s; border: 1.5px solid transparent; margin-bottom: 5px;
    }
    .place-card:hover { background: var(--matcha-ghost); border-color: var(--matcha-pale); }
    .place-card.active { background: var(--matcha-ghost); border-color: var(--matcha-light); }

    .place-thumb {
        width: 56px; height: 56px; border-radius: 9px;
        object-fit: cover; flex-shrink: 0; background: var(--matcha-pale);
    }
    .place-thumb-placeholder {
        width: 56px; height: 56px; border-radius: 9px;
        background: var(--matcha-pale); flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }
    .place-info { flex: 1; min-width: 0; }
    .place-name {
        font-weight: 600; font-size: .86rem; color: var(--ink);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;
    }
    .place-meta { font-size: .74rem; color: var(--ink-soft); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .place-footer { display: flex; align-items: center; justify-content: space-between; }
    .place-rating { display: flex; align-items: center; gap: 3px; font-size: .74rem; font-weight: 700; color: var(--ink-mid); }
    .star { color: #f59e0b; }
    .place-jarak { font-size: .7rem; color: var(--matcha-mid); font-weight: 600; }

    /* ── DIRECTION SIDEBAR ── */
    .direction-sidebar {
        display: none;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }
    .direction-sidebar.active { display: flex; }

    .direction-header {
        padding: 14px 20px 12px;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
        background: var(--matcha-ghost);
    }
    .direction-back-btn {
        display: flex; align-items: center; gap: 7px;
        background: none; border: none; cursor: pointer;
        font-family: var(--font-body); font-size: .82rem; font-weight: 600;
        color: var(--matcha-deep); padding: 0; margin-bottom: 12px;
        transition: color .18s;
    }
    .direction-back-btn:hover { color: var(--ink); }

    .direction-dest-label {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: var(--ink-soft); margin-bottom: 4px;
    }
    .direction-dest-name {
        font-family: var(--font-display); font-size: .95rem;
        font-weight: 700; color: var(--ink);
    }

    .direction-inputs {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }

    .dir-input-row {
        display: flex; align-items: center; gap: 10px; margin-bottom: 10px;
    }
    .dir-point-badge {
        width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 800; color: white;
    }
    .dir-point-badge-a { background: var(--matcha); }
    .dir-point-badge-b { background: #e85d5d; }

    .dir-input-wrap { flex: 1; position: relative; }
    .dir-input {
        width: 100%; padding: 8px 30px 8px 11px;
        border-radius: var(--radius-sm); border: 1.5px solid var(--border);
        font-family: var(--font-body); font-size: .83rem; color: var(--ink);
        background: var(--white); outline: none;
        transition: border-color .18s, box-shadow .18s;
    }
    .dir-input:focus { border-color: var(--matcha); box-shadow: 0 0 0 3px rgba(74,124,89,.12); }
    .dir-input::placeholder { color: var(--ink-soft); }
    .dir-input:read-only { background: var(--matcha-ghost); color: var(--matcha-deep); font-weight: 600; border-color: var(--matcha-light); }

    .dir-clear-btn {
        position: absolute; right: 7px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: var(--ink-soft); display: none; font-size: .8rem; padding: 2px;
    }
    .dir-clear-btn.visible { display: block; }
    .dir-clear-btn:hover { color: var(--ink); }

    .dir-dropdown {
        position: absolute; top: calc(100% + 3px); left: 0; right: 0;
        background: var(--white); border: 1.5px solid var(--border);
        border-radius: var(--radius-sm); box-shadow: var(--shadow-md);
        z-index: 9999; max-height: 200px; overflow-y: auto; display: none;
        scrollbar-width: thin;
    }
    .dir-dropdown.open { display: block; }

    .btn-swap-dir {
        background: none; border: none; cursor: pointer;
        color: var(--matcha); padding: 4px; border-radius: 50%;
        transition: background .18s; flex-shrink: 0;
    }
    .btn-swap-dir:hover { background: var(--matcha-pale); }

    .btn-hitung-dir {
        width: 100%; padding: 9px; background: var(--matcha); border: none;
        border-radius: var(--radius-sm); font-family: var(--font-body);
        font-size: .85rem; font-weight: 700; color: var(--white);
        cursor: pointer; transition: background .18s; margin-top: 4px;
        display: flex; align-items: center; justify-content: center; gap: 5px;
    }
    .btn-hitung-dir:hover { background: var(--matcha-deep); }
    .btn-hitung-dir:disabled { background: var(--border); color: var(--ink-soft); cursor: not-allowed; }

    .direction-results {
        flex: 1; overflow-y: auto; padding: 14px 20px;
        scrollbar-width: thin; scrollbar-color: var(--matcha-pale) transparent;
    }
    .direction-results::-webkit-scrollbar { width: 4px; }
    .direction-results::-webkit-scrollbar-thumb { background: var(--matcha-pale); border-radius: 4px; }

    /* Hasil rute */
    .distance-card {
        background: var(--matcha-ghost); border: 1.5px solid var(--matcha-pale);
        border-radius: var(--radius-md); padding: 14px 16px; margin-bottom: 14px; display: none;
    }
    .distance-card.visible { display: block; }
    .distance-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--ink-soft); margin-bottom: 6px; }
    .distance-value { font-family: var(--font-display); font-size: 1.9rem; font-weight: 700; color: var(--matcha-deep); line-height: 1; margin-bottom: 4px; }
    .distance-from-to { font-size: .76rem; color: var(--ink-soft); margin-top: 7px; line-height: 1.5; }
    .distance-from-to strong { color: var(--ink-mid); }

    .point-info-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius-md); padding: 12px 14px; margin-bottom: 9px; display: none;
    }
    .point-info-card.visible { display: block; }
    .point-info-header { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .point-info-name { font-weight: 700; font-size: .88rem; color: var(--ink); flex: 1; min-width: 0; }
    .point-info-row { display: flex; gap: 5px; font-size: .76rem; color: var(--ink-soft); margin-bottom: 4px; align-items: flex-start; }

    .empty-state-dir {
        text-align: center; padding: 28px 16px; color: var(--ink-soft);
    }
    .empty-state-dir-icon { font-size: 2rem; margin-bottom: 10px; opacity: .5; }
    .empty-state-dir-title { font-family: var(--font-display); font-size: .9rem; font-weight: 600; color: var(--ink-mid); margin-bottom: 5px; }
    .empty-state-dir p { font-size: .8rem; line-height: 1.6; }

    .error-state {
        background: #fff5f5; border: 1px solid #f5c6c6;
        border-radius: var(--radius-sm); padding: 10px 12px;
        font-size: .8rem; color: #c0392b; margin-bottom: 10px; display: none;
    }
    .error-state.visible { display: block; }

    .loading-overlay { text-align: center; padding: 28px 16px; display: none; }
    .loading-overlay.visible { display: block; }
    .spinner { width: 26px; height: 26px; border: 3px solid var(--matcha-pale); border-top-color: var(--matcha); border-radius: 50%; animation: spin .7s linear infinite; margin: 0 auto 8px; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .loading-text { font-size: .8rem; color: var(--ink-soft); }

    /* ── DROPDOWN ITEMS ── */
    .dropdown-item {
        padding: 8px 12px; font-size: .82rem; color: var(--ink);
        cursor: pointer; transition: background .13s;
        border-bottom: 1px solid var(--border);
    }
    .dropdown-item:last-child { border-bottom: none; }
    .dropdown-item:hover { background: var(--matcha-ghost); }
    .dropdown-item-name { font-weight: 600; }
    .dropdown-item-sub { font-size: .71rem; color: var(--ink-soft); margin-top: 1px; }
    .dropdown-item-empty { padding: 12px; font-size: .8rem; color: var(--ink-soft); text-align: center; }

    /* ── EMPTY / SKELETON ── */
    .empty-state { text-align: center; padding: 40px 24px; color: var(--ink-soft); }
    .empty-state svg { margin-bottom: 10px; opacity: .4; }
    .empty-state p { font-size: .86rem; }

    .skeleton-card { display: flex; gap: 11px; padding: 11px; margin-bottom: 5px; border-radius: var(--radius-md); background: var(--matcha-ghost); animation: shimmer 1.5s infinite; }
    @keyframes shimmer { 0%,100% { opacity:1; } 50% { opacity:.5; } }
    .skel-thumb { width:56px; height:56px; border-radius:9px; background:var(--matcha-pale); flex-shrink:0; }
    .skel-lines { flex:1; }
    .skel-line { height:9px; border-radius:4px; background:var(--matcha-pale); margin-bottom:6px; }
    .skel-sm { width:60%; }

    /* ── MAP ── */
    .map-container { flex: 1; position: relative; overflow: hidden; }
    #mainMap { width: 100%; height: 100%; }

    /* Leaflet popup overrides */
    .leaflet-popup-content-wrapper { border-radius: var(--radius-md) !important; box-shadow: var(--shadow-lg) !important; border: 1px solid var(--border) !important; padding: 0 !important; overflow: hidden; }
    .leaflet-popup-content { margin: 0 !important; width: 270px !important; }
    .leaflet-popup-tip-container { display: none; }

    .popup-wrap { font-family: var(--font-body); }
    .popup-img { width:100%; height:130px; object-fit:cover; background:var(--matcha-pale); display:block; }
    .popup-img-placeholder { width:100%; height:130px; background:var(--matcha-pale); display:flex; align-items:center; justify-content:center; font-size:2.2rem; }
    .popup-body { padding: 12px 14px 14px; }
    .popup-badge { display:inline-block; padding:2px 9px; border-radius:100px; font-size:.68rem; font-weight:700; letter-spacing:.03em; margin-bottom:7px; }
    .popup-name { font-family:var(--font-display); font-size:.95rem; font-weight:600; color:var(--ink); margin-bottom:4px; line-height:1.3; }
    .popup-jalan { font-size:.75rem; color:var(--ink-soft); margin-bottom:8px; }
    .popup-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
    .popup-rating { display:flex; align-items:center; gap:3px; font-size:.8rem; font-weight:700; color:var(--ink); }
    .popup-jarak-pill { font-size:.7rem; font-weight:600; background:var(--matcha-pale); color:var(--matcha-deep); padding:2px 9px; border-radius:100px; }
    .popup-actions { display:flex; gap:7px; }
    .popup-btn { flex:1; padding:7px; border-radius:var(--radius-sm); font-size:.76rem; font-weight:600; text-align:center; cursor:pointer; transition:all .18s; border:none; font-family:var(--font-body); }
    .popup-btn-primary { background:var(--matcha); color:var(--white); }
    .popup-btn-primary:hover { background:var(--matcha-deep); }
    .popup-btn-outline { background:var(--white); color:var(--ink-mid); border:1.5px solid var(--border); }
    .popup-btn-outline:hover { background:var(--matcha-ghost); }
    .popup-btn-direction { background: #3b82f6; color: white; }
    .popup-btn-direction:hover { background: #1d4ed8; }

    /* Line popup */
    .popup-inner { padding: 13px 15px; font-family: var(--font-body); }
    .popup-title { font-family: var(--font-display); font-size: .92rem; font-weight: 700; color: var(--ink); margin-bottom: 5px; }
    .popup-row-line { font-size: .76rem; color: var(--ink-soft); margin-bottom: 3px; display: flex; gap: 5px; }

    /* Polygon tooltip */
    .poly-tooltip {
        font-family: var(--font-body); font-size: .8rem; font-weight: 600;
        background: var(--white) !important; border: 1px solid var(--border) !important;
        border-radius: var(--radius-sm) !important; box-shadow: var(--shadow-sm) !important;
        color: var(--ink) !important; padding: 4px 10px !important;
    }

    /* Polygon info panel */
    .polygon-info-panel {
        position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%) translateY(20px);
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);
        padding: 16px 20px; min-width: 240px; max-width: 340px;
        z-index: 800; opacity: 0; pointer-events: none;
        transition: opacity .25s, transform .25s; font-family: var(--font-body);
    }
    .polygon-info-panel.visible { opacity: 1; pointer-events: auto; transform: translateX(-50%) translateY(0); }

    .panel-kec-name { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--ink); margin-bottom: 10px; display: flex; align-items: center; gap: 7px; }
    .panel-kec-dot { width: 11px; height: 11px; border-radius: 2px; flex-shrink: 0; }
    .panel-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-bottom: 12px; }
    .panel-stat { text-align: center; background: var(--matcha-ghost); border-radius: var(--radius-sm); padding: 7px 3px; }
    .panel-stat-num { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--matcha-deep); line-height: 1; }
    .panel-stat-lbl { font-size: .64rem; color: var(--ink-soft); margin-top: 2px; }
    .panel-close { position: absolute; top: 11px; right: 12px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; border: none; background: var(--matcha-pale); border-radius: 50%; cursor: pointer; color: var(--ink-mid); font-size: .75rem; transition: background .18s; }
    .panel-close:hover { background: var(--matcha-light); color: var(--white); }
    .panel-btn-row { display: flex; gap: 7px; }
    .panel-btn { flex: 1; padding: 7px; border-radius: var(--radius-sm); font-size: .76rem; font-weight: 600; border: none; cursor: pointer; font-family: var(--font-body); transition: all .18s; }
    .panel-btn-primary { background: var(--matcha); color: var(--white); }
    .panel-btn-primary:hover { background: var(--matcha-deep); }
    .panel-btn-outline { background: var(--white); color: var(--ink-mid); border: 1.5px solid var(--border); }
    .panel-btn-outline:hover { background: var(--matcha-ghost); }

    /* Map controls */
    .map-controls { position: absolute; top: 16px; right: 16px; z-index: 800; display: flex; flex-direction: column; gap: 8px; }
    .map-ctrl-btn { width: 38px; height: 38px; background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: .82rem; box-shadow: var(--shadow-sm); transition: background .18s; color: var(--ink-mid); }
    .map-ctrl-btn:hover { background: var(--matcha-ghost); color: var(--matcha-deep); }

    /* Distance badge on map */
    .map-distance-badge {
        position: absolute; top: 16px; left: 50%; transform: translateX(-50%);
        background: rgba(255,255,255,.96); backdrop-filter: blur(8px);
        border: 1.5px solid var(--matcha-pale); border-radius: 100px;
        padding: 6px 16px; font-family: var(--font-display); font-size: .88rem;
        font-weight: 700; color: var(--matcha-deep); box-shadow: var(--shadow-md);
        z-index: 800; display: none; white-space: nowrap; pointer-events: none;
    }
    .map-distance-badge.visible { display: block; }

    /* Legend */
    .map-legend {
        position: absolute; bottom: 14px; right: 14px; z-index: 800;
        background: rgba(255,255,255,.95); backdrop-filter: blur(8px);
        border: 1px solid var(--border); border-radius: var(--radius-md);
        padding: 10px 13px; box-shadow: var(--shadow-sm);
        font-size: .73rem; color: var(--ink-mid); max-width: 150px;
    }
    .legend-title { font-weight: 700; color: var(--ink); margin-bottom: 7px; font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; }
    .legend-item { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; font-size: .72rem; }
    .legend-swatch { width: 13px; height: 9px; border-radius: 2px; flex-shrink: 0; }

    /* User marker */
    .user-marker { width: 15px; height: 15px; background: #3b82f6; border-radius: 50%; border: 3px solid var(--white); box-shadow: 0 0 0 4px rgba(59,130,246,.22), 0 2px 6px rgba(0,0,0,.22); }

    /* Mobile */
    @media (max-width: 768px) {
        .maps-wrapper { flex-direction: column; }
        .sidebar { width: 100%; min-width: 0; height: 52vh; border-right: none; border-bottom: 1px solid var(--border); }
        .polygon-info-panel { min-width: 220px; max-width: calc(100vw - 32px); }
        .map-legend { display: none; }
    }
</style>
@endpush

@section('content')

<div class="maps-wrapper">

    {{-- ─── SIDEBAR ─── --}}
    <aside class="sidebar">

        {{-- ═══════════════════════════════════
             DEFAULT VIEW: Filter + Daftar Lokasi
        ════════════════════════════════════ --}}
        <div id="defaultSidebar" style="display:flex;flex-direction:column;height:100%;overflow:hidden;">

            <div class="sidebar-header">
                <div class="sidebar-title">Jelajahi Medan</div>
                <div class="sidebar-subtitle">Titik lokasi &amp; wilayah kecamatan</div>
            </div>

            <div class="sidebar-search">
                <div class="search-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-input" id="mainSearchInput" placeholder="Cari nama, alamat, kecamatan...">
                    <button class="search-clear" id="mainSearchClear">&#x2715;</button>
                    <div class="search-dropdown" id="mainSearchDropdown"></div>
                </div>
            </div>

            <div class="filter-panel" style="overflow-y:auto;max-height:calc(100% - 160px);flex-shrink:0;">

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
                    <div class="toggle-row">
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggleJarak">
                            <span class="toggle-track"></span>
                        </label>
                        <span class="toggle-label">Filter berdasarkan jarak</span>
                    </div>
                    <div class="jarak-control" id="jarakControl">
                        <div class="slider-row">
                            <input type="range" id="sliderJarak" min="1" max="50" step="1" value="10">
                            <div class="slider-val" id="jarakVal">10 km</div>
                        </div>
                    </div>
                    <div class="loc-status" id="locStatus">
                        <div class="loc-dot" id="locDot"></div>
                        <span id="locText">Lokasi belum diaktifkan</span>
                    </div>
                </div>

                {{-- Kecamatan --}}
                <div class="filter-group">
                    <span class="filter-label">Tampilkan Kecamatan</span>
                    <button class="kec-all-btn" id="btnKecAll">Pilih semua / Hapus semua</button>
                    <div class="kec-checkbox-list" id="kecCheckboxList">
                        @foreach ($kecamatans as $kec)
                        <label class="kec-checkbox-item">
                            <input type="checkbox" class="kec-checkbox" value="{{ $kec->id }}" checked>
                            <div class="kec-checkbox-dot" data-hue="{{ ($kec->id * 37) % 360 }}"></div>
                            <span class="kec-checkbox-label">{{ $kec->nama_kecamatan }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button class="btn-reset" id="btnReset">↺ Reset semua filter</button>
            </div>

            <div class="results-header">
                <div class="results-count"><span id="countNum">—</span> lokasi ditemukan</div>
            </div>

            <div class="results-list" id="resultsList">
                @for ($i = 0; $i < 5; $i++)
                <div class="skeleton-card">
                    <div class="skel-thumb"></div>
                    <div class="skel-lines">
                        <div class="skel-line"></div>
                        <div class="skel-line skel-sm"></div>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        {{-- ═══════════════════════════════════
             DIRECTION VIEW
        ════════════════════════════════════ --}}
        <div class="direction-sidebar" id="directionSidebar">

            <div class="direction-header">
                <button class="direction-back-btn" id="btnBackFromDir">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke peta
                </button>
                <div class="direction-dest-label">Tujuan</div>
                <div class="direction-dest-name" id="dirDestName">—</div>
            </div>

            <div class="direction-inputs">
                {{-- Titik A --}}
                <div class="dir-input-row">
                    <div class="dir-point-badge dir-point-badge-a">A</div>
                    <div class="dir-input-wrap">
                        <input type="text" class="dir-input" id="dirInputA" placeholder="Lokasi saya / cari titik awal…" autocomplete="off">
                        <button class="dir-clear-btn" id="dirClearA">✕</button>
                        <div class="dir-dropdown" id="dirDropdownA"></div>
                    </div>
                    <button class="btn-swap-dir" id="btnUseMyLoc" title="Gunakan lokasi saya">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/><path stroke-linecap="round" d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
                        </svg>
                    </button>
                </div>

                {{-- Titik B (readonly = tujuan yg diklik) --}}
                <div class="dir-input-row">
                    <div class="dir-point-badge dir-point-badge-b">B</div>
                    <div class="dir-input-wrap">
                        <input type="text" class="dir-input" id="dirInputB" readonly>
                    </div>
                </div>

                <button class="btn-hitung-dir" id="btnHitungDir" disabled>
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Tampilkan Rute
                </button>
            </div>

            <div class="direction-results" id="directionResults">
                <div class="loading-overlay" id="dirLoadingState">
                    <div class="spinner"></div>
                    <div class="loading-text">Menghitung rute…</div>
                </div>
                <div class="error-state" id="dirErrorState"></div>
                <div class="distance-card" id="dirDistanceCard">
                    <div class="distance-label" id="dirDistanceLabel">Jarak Jalan</div>
                    <div class="distance-value" id="dirDistanceValue">—</div>
                    <div class="distance-from-to" id="dirDistanceFromTo"></div>
                </div>
                <div class="point-info-card" id="dirInfoCardA">
                    <div class="point-info-header">
                        <div class="dir-point-badge dir-point-badge-a" style="flex-shrink:0;width:22px;height:22px;font-size:.65rem;">A</div>
                        <div class="point-info-name" id="dirInfoNameA">—</div>
                    </div>
                    <div id="dirInfoBodyA"></div>
                </div>
                <div class="point-info-card" id="dirInfoCardB">
                    <div class="point-info-header">
                        <div class="dir-point-badge dir-point-badge-b" style="flex-shrink:0;width:22px;height:22px;font-size:.65rem;">B</div>
                        <div class="point-info-name" id="dirInfoNameB">—</div>
                    </div>
                    <div id="dirInfoBodyB"></div>
                </div>
                <div class="empty-state-dir" id="emptyStateDir">
                    <div class="empty-state-dir-icon">🗺</div>
                    <div class="empty-state-dir-title">Pilih titik awal</div>
                    <p>Cari atau gunakan lokasi Anda sebagai titik awal, lalu hitung rute ke tujuan.</p>
                </div>
            </div>
        </div>

    </aside>

    {{-- ─── MAP ─── --}}
    <div class="map-container">
        <div id="mainMap"></div>

        {{-- Distance badge --}}
        <div class="map-distance-badge" id="mapDistanceBadge">
            📏 <span id="mapDistanceText">—</span>
        </div>

        {{-- Polygon info panel --}}
        <div class="polygon-info-panel" id="polyInfoPanel">
            <button class="panel-close" id="panelClose">✕</button>
            <div class="panel-kec-name">
                <div class="panel-kec-dot" id="panelDot"></div>
                <span id="panelNameText">—</span>
            </div>
            <div class="panel-stats" id="panelStats"></div>
            <div class="panel-btn-row">
                <button class="panel-btn panel-btn-primary" id="panelBtnZoom">Perbesar</button>
            </div>
        </div>

        {{-- Map controls --}}
        <div class="map-controls">
            <div class="map-ctrl-btn" id="btnMyLoc" title="Lokasi saya">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/><path stroke-linecap="round" d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
                    <circle cx="12" cy="12" r="7" stroke-dasharray="44" stroke-dashoffset="11"/>
                </svg>
            </div>
            <div class="map-ctrl-btn" id="btnFitAll" title="Fit semua">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M4 8V4h4M16 4h4v4M4 16v4h4M16 20h4v-4"/>
                </svg>
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
'use strict';

// ════════════════════════════════════════════════════
//  CONFIG
// ════════════════════════════════════════════════════
const MEDAN_CENTER = [3.585, 98.676];
const SUPABASE_URL = 'https://rnafixrgoucrplssoqtm.supabase.co/storage/v1/object/public/tempat_images/';
const DEBOUNCE_MS  = 280;

const KATEGORI_CONFIG = {
    1: { icon: '🍜', color: '#f59e0b', badge: 'badge-kuliner',        label: 'Kuliner' },
    2: { icon: '🏖',  color: '#3b82f6', badge: 'badge-wisata',         label: 'Wisata' },
    3: { icon: '🏥', color: '#ec4899', badge: 'badge-kesehatan',      label: 'Kesehatan' },
    4: { icon: '🏛',  color: '#8b5cf6', badge: 'badge-kemasyarakatan', label: 'Kemasyarakatan' },
    5: { icon: '🚌', color: '#10b981', badge: 'badge-transportasi',   label: 'Transportasi' },
};

const PALETTE = [
    '#4a7c59','#3b6e8a','#7c4a6e','#8a6e3b','#3b8a6e',
    '#6e3b8a','#8a3b4a','#4a6e8a','#6e8a3b','#8a4a3b',
    '#3b4a8a','#6e4a3b','#3b8a4a','#8a7c3b','#4a8a7c',
    '#7c3b6e','#3b6e4a','#6e7c3b','#4a3b8a',
];
function colorFor(id) { return PALETTE[(id - 1) % PALETTE.length]; }

// ════════════════════════════════════════════════════
//  STATE
// ════════════════════════════════════════════════════
// Point + filter
let allData       = [], filteredData = [];
let activeKat     = 'all', ratingMin = 0;
let jarakAktif    = false, jarakMax  = 10;
let userLat       = null, userLng    = null;
let pointMarkers  = {};
let userMarker    = null;
let rangeCircle   = null;

// Polygon
let allKecamatans = [], filteredKecs = [];
let polygonLayers = {}, labelMarkers = {};
let selectedKecs  = new Set();
let selectedKecId = null;
let activePanelKec = null;
let allKecIds     = [];

// Direction mode
let directionMode  = false;
let dirDestTempat  = null;  // tempat B (tujuan, diklik dari popup)
let dirSelectedA   = null;  // titik A
let allTempat      = [];
let dirMarkerA     = null, dirMarkerB = null;
let dirRouteLine   = null;

// ════════════════════════════════════════════════════
//  MAP INIT
// ════════════════════════════════════════════════════
const map = L.map('mainMap', { center: MEDAN_CENTER, zoom: 12, zoomControl: false });
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

// ════════════════════════════════════════════════════
//  UTILS
// ════════════════════════════════════════════════════
function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function getImageUrl(url) {
    if (!url) return null;
    return (url.startsWith('http://') || url.startsWith('https://')) ? url : SUPABASE_URL + url;
}
function haversine(lat1, lng1, lat2, lng2) {
    const R = 6371, dLat = deg2rad(lat2-lat1), dLng = deg2rad(lng2-lng1);
    const a = Math.sin(dLat/2)**2 + Math.cos(deg2rad(lat1))*Math.cos(deg2rad(lat2))*Math.sin(dLng/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}
function deg2rad(d) { return d * Math.PI / 180; }
function formatJarak(km) {
    if (km < 0.1) return `${(km*1000).toFixed(0)} m`;
    return `${km.toFixed(2)} km`;
}
window.handleImgError = function(el, icon) {
    const ph = document.createElement('div');
    ph.className = 'popup-img-placeholder';
    ph.textContent = icon || '📍';
    el.parentNode.replaceChild(ph, el);
};

// ════════════════════════════════════════════════════
//  MARKER ICONS
// ════════════════════════════════════════════════════
function makeMarkerIcon(kategoriId, dimmed) {
    const cfg  = KATEGORI_CONFIG[kategoriId] || { icon: '📍', color: '#4a7c59' };
    const size = 34;
    const opacity = dimmed ? '.3' : '1';
    return L.divIcon({
        className: '',
        html: `<div style="width:${size}px;height:${size}px;background:${cfg.color};border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2.5px solid #fff;box-shadow:0 3px 10px rgba(0,0,0,.28);display:flex;align-items:center;justify-content:center;opacity:${opacity};"><span style="transform:rotate(45deg);font-size:12px;line-height:1;">${cfg.icon}</span></div>`,
        iconSize: [size,size], iconAnchor: [size/2,size], popupAnchor: [0,-(size+3)],
    });
}
function makeUserIcon() {
    return L.divIcon({ className: '', html: '<div class="user-marker"></div>', iconSize: [15,15], iconAnchor: [7,7] });
}
function makeLineIcon(label, color) {
    return L.divIcon({
        className: '',
        html: `<div style="background:${color};color:white;font-family:var(--font-body,sans-serif);font-size:11px;font-weight:800;width:28px;height:28px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);border:2px solid white;"><span style="transform:rotate(45deg)">${label}</span></div>`,
        iconSize: [28,28], iconAnchor: [14,28], popupAnchor: [0,-32],
    });
}

// ════════════════════════════════════════════════════
//  FETCH + RENDER POINTS
// ════════════════════════════════════════════════════
function fetchData() {
    const params = new URLSearchParams();
    if (activeKat !== 'all') params.set('kategori', activeKat);
    if (ratingMin > 0)       params.set('rating_min', ratingMin);
    if (jarakAktif && userLat !== null) {
        params.set('lat', userLat);
        params.set('lng', userLng);
        params.set('radius', jarakMax);
    }
    fetch(`/api/tempat?${params}`)
        .then(r => r.json())
        .then(data => {
            allData = filteredData = data;
            allTempat = data; // share dengan direction search
            renderMarkers();
            renderList();
        })
        .catch(err => console.error('Gagal fetch tempat:', err));
}

function renderMarkers() {
    const newIds = new Set(filteredData.map(t => t.id));

    // Hapus marker yg tidak ada lagi
    Object.entries(pointMarkers).forEach(([id, m]) => {
        if (!newIds.has(parseInt(id))) { map.removeLayer(m); delete pointMarkers[id]; }
    });

    filteredData.forEach(t => {
        const dimmed = directionMode && dirDestTempat && t.id !== dirDestTempat.id;
        const icon   = makeMarkerIcon(t.kategori_id, dimmed);

        if (pointMarkers[t.id]) {
            pointMarkers[t.id].setPopupContent(buildPopup(t));
            pointMarkers[t.id].setIcon(icon);
            return;
        }
        const m = L.marker([t.latitude, t.longitude], { icon })
            .bindPopup(buildPopup(t), { maxWidth: 270 })
            .addTo(map);
        m.on('click', () => { if (!directionMode) highlightCard(t.id); });
        pointMarkers[t.id] = m;
    });

    // Update range circle
    updateRangeCircle();
}

function updateRangeCircle() {
    if (rangeCircle) { map.removeLayer(rangeCircle); rangeCircle = null; }
    if (jarakAktif && userLat !== null) {
        rangeCircle = L.circle([userLat, userLng], {
            radius: jarakMax * 1000,
            color: '#4a7c59',
            weight: 1.5,
            opacity: 0.6,
            fillColor: '#4a7c59',
            fillOpacity: 0.07,
            dashArray: '6 4',
        }).addTo(map);
    }
}

function buildPopup(t) {
    const cfg    = KATEGORI_CONFIG[t.kategori_id] || { badge: 'badge-default', label: t.kategori || '—', icon: '📍' };
    const imgUrl = getImageUrl(t.media_url);
    const icon   = cfg.icon;
    const imgEl  = imgUrl
        ? `<img src="${imgUrl}" class="popup-img" crossorigin="anonymous" referrerpolicy="no-referrer" data-icon="${icon}" onerror="handleImgError(this, this.dataset.icon)">`
        : `<div class="popup-img-placeholder">${icon}</div>`;
    const stars  = '★'.repeat(Math.round(t.rating||0)) + '☆'.repeat(5-Math.round(t.rating||0));
    const jarakEl = t.jarak_km != null ? `<span class="popup-jarak-pill">${t.jarak_km} km</span>` : '';
    return `
    <div class="popup-wrap">${imgEl}
        <div class="popup-body">
            <span class="popup-badge ${cfg.badge}">${cfg.label}</span>
            <div class="popup-name">${escHtml(t.nama_tempat)}</div>
            <div class="popup-jalan">📍 ${escHtml(t.jalan||'')}${t.kecamatan ? ', '+escHtml(t.kecamatan) : ''}</div>
            <div class="popup-row">
                <div class="popup-rating"><span style="color:#f59e0b">${stars}</span> ${(t.rating||0).toFixed(1)}</div>
                ${jarakEl}
            </div>
            ${t.kontak ? `<div style="font-size:.74rem;color:var(--ink-soft);margin-bottom:8px;">☎ ${escHtml(t.kontak)}</div>` : ''}
            <div class="popup-actions">
                <button class="popup-btn popup-btn-primary" onclick="openMaps(${t.latitude},${t.longitude})">Buka Maps</button>
                <button class="popup-btn popup-btn-direction" onclick="enterDirectionMode(${t.id})">🧭 Direction</button>
            </div>
        </div>
    </div>`;
}

function renderList() {
    const list = document.getElementById('resultsList');
    document.getElementById('countNum').textContent = filteredData.length;
    if (!filteredData.length) {
        list.innerHTML = `<div class="empty-state"><p>Tidak ada lokasi yang cocok.<br>Coba sesuaikan filter.</p></div>`;
        return;
    }
    list.innerHTML = filteredData.map(t => {
        const cfg    = KATEGORI_CONFIG[t.kategori_id] || { icon: '📍', badge: 'badge-default' };
        const imgUrl = getImageUrl(t.media_url);
        const img    = imgUrl
            ? `<img src="${imgUrl}" class="place-thumb" crossorigin="anonymous" referrerpolicy="no-referrer" data-icon="${cfg.icon}" onerror="handleImgError(this,this.dataset.icon)">`
            : `<div class="place-thumb-placeholder">${cfg.icon}</div>`;
        const dist  = t.jarak_km != null ? `<span class="place-jarak">${t.jarak_km} km</span>` : '';
        const stars = '★'.repeat(Math.round(t.rating||0));
        return `
        <div class="place-card" data-id="${t.id}" onclick="selectPlace(${t.id})">
            ${img}
            <div class="place-info">
                <div class="place-name">${escHtml(t.nama_tempat)}</div>
                <div class="place-meta">${escHtml(t.jalan||'')}${t.kecamatan ? ', '+escHtml(t.kecamatan) : ''}</div>
                <div class="place-footer">
                    <div class="place-rating"><span class="star">${stars}</span> ${(t.rating||0).toFixed(1)}</div>
                    ${dist}
                </div>
            </div>
        </div>`;
    }).join('');
}

window.selectPlace = function(id) {
    const t = filteredData.find(x => x.id === id);
    if (!t) return;
    document.querySelectorAll('.place-card').forEach(c => c.classList.remove('active'));
    const card = document.querySelector(`.place-card[data-id="${id}"]`);
    if (card) card.classList.add('active');
    map.setView([t.latitude, t.longitude], 16, { animate: true });
    setTimeout(() => { if (pointMarkers[id]) pointMarkers[id].openPopup(); }, 350);
};

function highlightCard(id) {
    document.querySelectorAll('.place-card').forEach(c => c.classList.remove('active'));
    const card = document.querySelector(`.place-card[data-id="${id}"]`);
    if (card) { card.classList.add('active'); card.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
}

window.flyTo    = (lat, lng) => map.setView([lat, lng], 17, { animate: true });
window.openMaps = (lat, lng) => window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');

// ════════════════════════════════════════════════════
//  USER LOCATION
// ════════════════════════════════════════════════════
function setUserLoc(lat, lng) {
    userLat = lat; userLng = lng;
    document.getElementById('locDot').classList.add('active');
    document.getElementById('locText').textContent = `Lokasi aktif (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
    if (userMarker) map.removeLayer(userMarker);
    userMarker = L.marker([lat, lng], { icon: makeUserIcon() }).bindTooltip('Lokasi Anda').addTo(map);
    updateRangeCircle();
}

// ════════════════════════════════════════════════════
//  FILTER EVENTS
// ════════════════════════════════════════════════════
document.querySelectorAll('.chip').forEach(chip => {
    chip.addEventListener('click', () => {
        document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        activeKat = chip.dataset.kategori;
        fetchData();
    });
});

const sliderRating = document.getElementById('sliderRating');
const ratingValEl  = document.getElementById('ratingVal');
sliderRating.addEventListener('input', () => {
    ratingMin = parseFloat(sliderRating.value);
    ratingValEl.textContent = ratingMin === 0 ? 'Semua' : `${ratingMin}+`;
    fetchData();
});

const toggleJarak  = document.getElementById('toggleJarak');
const jarakControl = document.getElementById('jarakControl');

toggleJarak.addEventListener('change', () => {
    jarakAktif = toggleJarak.checked;
    jarakControl.classList.toggle('visible', jarakAktif);

    if (jarakAktif && userLat === null) {
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
        updateRangeCircle();
        fetchData();
    }
});

const sliderJarak = document.getElementById('sliderJarak');
const jarakValEl  = document.getElementById('jarakVal');

// Gunakan 'input' untuk live update circle, debounce fetchData
let jarakFetchTimer = null;
sliderJarak.addEventListener('input', () => {
    jarakMax = parseInt(sliderJarak.value);
    jarakValEl.textContent = `${jarakMax} km`;
    updateRangeCircle(); // update circle langsung
    if (jarakAktif && userLat !== null) {
        clearTimeout(jarakFetchTimer);
        jarakFetchTimer = setTimeout(() => fetchData(), 400);
    }
});

document.getElementById('btnReset').addEventListener('click', () => {
    activeKat = 'all'; ratingMin = 0; jarakAktif = false; jarakMax = 10;
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    document.querySelector('.chip-all').classList.add('active');
    sliderRating.value = 0; ratingValEl.textContent = 'Semua';
    toggleJarak.checked = false; jarakControl.classList.remove('visible');
    sliderJarak.value = 10; jarakValEl.textContent = '10 km';
    updateRangeCircle();
    // Reset kecamatan
    allKecIds.forEach(id => selectedKecs.add(id));
    document.querySelectorAll('.kec-checkbox').forEach(cb => { cb.checked = true; });
    applyPolygonFilter();
    fetchData();
});

// ════════════════════════════════════════════════════
//  MAP CTRL BUTTONS
// ════════════════════════════════════════════════════
document.getElementById('btnMyLoc').addEventListener('click', () => {
    if (!navigator.geolocation) { alert('Browser tidak mendukung geolokasi.'); return; }
    navigator.geolocation.getCurrentPosition(
        pos => {
            setUserLoc(pos.coords.latitude, pos.coords.longitude);
            map.setView([pos.coords.latitude, pos.coords.longitude], 14, { animate: true });
            if (jarakAktif) fetchData();
        },
        () => alert('Gagal mendapatkan lokasi.')
    );
});

document.getElementById('btnFitAll').addEventListener('click', () => {
    if (filteredData.length) {
        map.fitBounds(L.latLngBounds(filteredData.map(t => [t.latitude, t.longitude])).pad(0.1));
    } else {
        map.setView(MEDAN_CENTER, 12, { animate: true });
    }
});

// ════════════════════════════════════════════════════
//  POLYGON
// ════════════════════════════════════════════════════
function fetchPolygons() {
    fetch('/api/kecamatan/polygon')
        .then(r => r.json())
        .then(data => {
            allKecamatans = data;
            allKecIds     = data.map(k => k.id);
            selectedKecs  = new Set(allKecIds);
            applyPolygonFilter();
        })
        .catch(err => console.error('Gagal fetch polygon:', err));
}

function applyPolygonFilter() {
    filteredKecs = allKecamatans.filter(k => selectedKecs.has(k.id));
    renderPolygons();
    updateLegend();
}

function renderPolygons() {
    Object.values(polygonLayers).forEach(l => map.removeLayer(l));
    Object.values(labelMarkers).forEach(m => map.removeLayer(m));
    polygonLayers = {}; labelMarkers = {};

    filteredKecs.forEach(kec => {
        if (!kec.geojson) return;
        let geom;
        try { geom = typeof kec.geojson === 'string' ? JSON.parse(kec.geojson) : kec.geojson; }
        catch(e) { return; }

        const color    = colorFor(kec.id);
        const isActive = selectedKecId === kec.id;

        const layer = L.geoJSON(geom, {
            style: { color, weight: isActive ? 3 : 2, opacity: 1, fillColor: color, fillOpacity: isActive ? 0.35 : 0.15 },
            onEachFeature: (feat, fl) => {
                fl.on({
                    mouseover: () => { if (selectedKecId !== kec.id) fl.setStyle({ fillOpacity: 0.28, weight: 2.5 }); },
                    mouseout:  () => { if (selectedKecId !== kec.id) fl.setStyle({ fillOpacity: 0.15, weight: 2 }); },
                    click:     () => selectPolygon(kec, color),
                });
            },
        });

        layer.bindTooltip(`<b>${kec.nama_kecamatan}</b><br>${kec.stats?.total||0} lokasi`, {
            className: 'poly-tooltip', sticky: true, direction: 'top',
        });

        layer.addTo(map);
        polygonLayers[kec.id] = layer;
        addLabel(kec, geom, color);
    });
}

function addLabel(kec, geom, color) {
    try {
        const center = L.geoJSON(geom).getBounds().getCenter();
        const label  = L.marker(center, {
            icon: L.divIcon({
                className: '',
                html: `<div style="background:rgba(255,255,255,.88);border:1.5px solid ${color};border-radius:100px;padding:2px 8px;font-family:var(--font-body,sans-serif);font-size:10px;font-weight:700;color:${color};white-space:nowrap;pointer-events:none;box-shadow:0 1px 4px rgba(0,0,0,.12);">${kec.nama_kecamatan}</div>`,
                iconAnchor: [0,0],
            }),
            interactive: false, zIndexOffset: -100,
        }).addTo(map);
        labelMarkers[kec.id] = label;
    } catch(e) {}
}

function selectPolygon(kec, color) {
    if (selectedKecId && polygonLayers[selectedKecId])
        polygonLayers[selectedKecId].setStyle({ fillOpacity: 0.15, weight: 2 });
    selectedKecId = kec.id;
    if (polygonLayers[kec.id]) polygonLayers[kec.id].setStyle({ fillOpacity: 0.35, weight: 3 });
    showInfoPanel(kec, color);
}

function showInfoPanel(kec, color) {
    activePanelKec = kec;
    document.getElementById('panelNameText').textContent = kec.nama_kecamatan;
    document.getElementById('panelDot').style.background = color;
    const s = kec.stats || {};
    document.getElementById('panelStats').innerHTML = [
        { num: s.total||0,           lbl: 'Total' },
        { num: s.kuliner||0,         lbl: 'Kuliner' },
        { num: s.wisata||0,          lbl: 'Wisata' },
        { num: s.kesehatan||0,       lbl: 'Kesehatan' },
        { num: s.kemasyarakatan||0,  lbl: 'Kemasyarakatan' },
        { num: s.transportasi||0,    lbl: 'Transportasi' },
    ].map(i => `<div class="panel-stat"><div class="panel-stat-num">${i.num}</div><div class="panel-stat-lbl">${i.lbl}</div></div>`).join('');
    document.getElementById('polyInfoPanel').classList.add('visible');
}

function hideInfoPanel() {
    document.getElementById('polyInfoPanel').classList.remove('visible');
    if (selectedKecId && polygonLayers[selectedKecId])
        polygonLayers[selectedKecId].setStyle({ fillOpacity: 0.15, weight: 2 });
    selectedKecId = null; activePanelKec = null;
}

function updateLegend() {
    const legend = document.getElementById('mapLegend');
    const items  = document.getElementById('legendItems');
    const shown  = filteredKecs.length <= 8 ? filteredKecs : filteredKecs.slice(0, 8);
    items.innerHTML = shown.map(kec => `
        <div class="legend-item">
            <div class="legend-swatch" style="background:${colorFor(kec.id)};"></div>
            <span>${escHtml(kec.nama_kecamatan)}</span>
        </div>`).join('');
    if (filteredKecs.length > 8)
        items.innerHTML += `<div style="font-size:.68rem;color:var(--ink-soft);margin-top:3px;">+${filteredKecs.length-8} lainnya…</div>`;
    legend.style.display = filteredKecs.length > 0 ? '' : 'none';
}

document.getElementById('panelClose').addEventListener('click', hideInfoPanel);
document.getElementById('panelBtnZoom').addEventListener('click', () => {
    if (activePanelKec && polygonLayers[activePanelKec.id])
        map.fitBounds(polygonLayers[activePanelKec.id].getBounds().pad(0.1), { animate: true });
});

// Apply dot colors
document.querySelectorAll('.kec-checkbox-dot[data-hue]').forEach(dot => {
    dot.style.background = `hsl(${dot.dataset.hue}, 55%, 45%)`;
});

// Kecamatan checkbox
document.querySelectorAll('.kec-checkbox').forEach(cb => {
    cb.addEventListener('change', () => {
        const id = parseInt(cb.value);
        if (cb.checked) selectedKecs.add(id); else selectedKecs.delete(id);
        applyPolygonFilter();
    });
});

document.getElementById('btnKecAll').addEventListener('click', () => {
    const allChecked = selectedKecs.size === allKecIds.length;
    if (allChecked) {
        selectedKecs.clear();
        document.querySelectorAll('.kec-checkbox').forEach(cb => { cb.checked = false; });
    } else {
        allKecIds.forEach(id => selectedKecs.add(id));
        document.querySelectorAll('.kec-checkbox').forEach(cb => { cb.checked = true; });
    }
    applyPolygonFilter();
});

// ════════════════════════════════════════════════════
//  DIRECTION MODE
// ════════════════════════════════════════════════════
window.enterDirectionMode = function(tempatId) {
    const t = allData.find(x => x.id === tempatId) || allTempat.find(x => x.id === tempatId);
    if (!t) return;

    map.closePopup();
    directionMode = true;
    dirDestTempat = t;
    dirSelectedA  = null;

    // Sembunyikan semua marker kecuali tujuan: hapus dari peta, jangan hanya di-dim
    Object.entries(pointMarkers).forEach(([id, m]) => {
        const isB = parseInt(id) === t.id;
        if (isB) {
            m.setIcon(makeLineIcon('B', '#e85d5d'));
        } else {
            map.removeLayer(m);
        }
    });

    // Tampilkan direction sidebar
    document.getElementById('defaultSidebar').style.display = 'none';
    document.getElementById('directionSidebar').classList.add('active');

    // Set nama tujuan
    document.getElementById('dirDestName').textContent = t.nama_tempat;
    document.getElementById('dirInputB').value = t.nama_tempat;

    // Reset state direction
    dirSelectedA = null;
    document.getElementById('dirInputA').value = '';
    document.getElementById('dirClearA').classList.remove('visible');
    document.getElementById('btnHitungDir').disabled = true;
    document.getElementById('dirDistanceCard').classList.remove('visible');
    document.getElementById('dirInfoCardA').classList.remove('visible');
    document.getElementById('dirInfoCardB').classList.remove('visible');
    document.getElementById('emptyStateDir').style.display = '';
    document.getElementById('mapDistanceBadge').classList.remove('visible');
    if (dirRouteLine) { map.removeLayer(dirRouteLine); dirRouteLine = null; }
    if (dirMarkerA)   { map.removeLayer(dirMarkerA); dirMarkerA = null; }

    // Focus ke marker B
    map.setView([t.latitude, t.longitude], 15, { animate: true });
};

function exitDirectionMode() {
    directionMode = false;
    dirDestTempat = null;
    dirSelectedA  = null;

    // Restore semua marker: tambahkan kembali ke peta dan reset icon
    Object.entries(pointMarkers).forEach(([id, m]) => {
        const t = filteredData.find(x => x.id === parseInt(id));
        if (t) {
            m.setIcon(makeMarkerIcon(t.kategori_id, false));
            if (!map.hasLayer(m)) m.addTo(map);
        }
    });

    // Bersihkan direction layers
    if (dirRouteLine) { map.removeLayer(dirRouteLine); dirRouteLine = null; }
    if (dirMarkerA)   { map.removeLayer(dirMarkerA); dirMarkerA = null; }
    if (dirMarkerB)   { map.removeLayer(dirMarkerB); dirMarkerB = null; }

    document.getElementById('mapDistanceBadge').classList.remove('visible');
    document.getElementById('directionSidebar').classList.remove('active');
    document.getElementById('defaultSidebar').style.display = 'flex';
}

document.getElementById('btnBackFromDir').addEventListener('click', exitDirectionMode);

// Gunakan lokasi saya sebagai titik A
document.getElementById('btnUseMyLoc').addEventListener('click', () => {
    if (userLat !== null) {
        dirSelectedA = { isMyLoc: true, latitude: userLat, longitude: userLng, nama_tempat: 'Lokasi Saya' };
        document.getElementById('dirInputA').value = 'Lokasi Saya';
        document.getElementById('dirClearA').classList.add('visible');
        onDirSelectionChange();
    } else {
        navigator.geolocation && navigator.geolocation.getCurrentPosition(
            pos => {
                setUserLoc(pos.coords.latitude, pos.coords.longitude);
                dirSelectedA = { isMyLoc: true, latitude: pos.coords.latitude, longitude: pos.coords.longitude, nama_tempat: 'Lokasi Saya' };
                document.getElementById('dirInputA').value = 'Lokasi Saya';
                document.getElementById('dirClearA').classList.add('visible');
                onDirSelectionChange();
            },
            () => alert('Gagal mendapatkan lokasi. Aktifkan izin lokasi browser.')
        );
    }
});

// Autocomplete titik A
function searchTempat(query) {
    if (!query || query.trim().length < 2) return [];
    const q = query.trim().toLowerCase();
    return allTempat.filter(t =>
        t.nama_tempat.toLowerCase().includes(q) ||
        (t.kecamatan && t.kecamatan.toLowerCase().includes(q)) ||
        (t.jalan && t.jalan.toLowerCase().includes(q))
    ).slice(0, 8);
}

function renderDropdown(dropdownEl, results, onSelect) {
    dropdownEl.innerHTML = '';
    if (!results.length) {
        dropdownEl.innerHTML = '<div class="dropdown-item-empty">Tidak ada hasil.</div>';
        dropdownEl.classList.add('open'); return;
    }
    results.forEach(t => {
        const item = document.createElement('div');
        item.className = 'dropdown-item';
        item.innerHTML = `<div class="dropdown-item-name">${escHtml(t.nama_tempat)}</div>
            <div class="dropdown-item-sub">${escHtml(t.kategori||'')}${t.kecamatan ? ' · '+escHtml(t.kecamatan) : ''}</div>`;
        item.addEventListener('mousedown', e => { e.preventDefault(); onSelect(t); });
        dropdownEl.appendChild(item);
    });
    dropdownEl.classList.add('open');
}
function closeDropdown(el) { el.classList.remove('open'); }

const dirInputA   = document.getElementById('dirInputA');
const dirDropA    = document.getElementById('dirDropdownA');
const dirClearA   = document.getElementById('dirClearA');

dirInputA.addEventListener('input', () => {
    clearTimeout(dirInputA._timer);
    const q = dirInputA.value;
    dirClearA.classList.toggle('visible', q.length > 0);
    if (q.length < 2) { closeDropdown(dirDropA); return; }
    dirInputA._timer = setTimeout(() => renderDropdown(dirDropA, searchTempat(q), item => {
        dirInputA.value = item.nama_tempat;
        closeDropdown(dirDropA);
        dirClearA.classList.add('visible');
        dirSelectedA = item;
        onDirSelectionChange();
    }), DEBOUNCE_MS);
});
dirInputA.addEventListener('focus', () => { if (dirInputA.value.length >= 2) dirDropA.classList.add('open'); });
dirInputA.addEventListener('blur',  () => setTimeout(() => closeDropdown(dirDropA), 180));
dirClearA.addEventListener('click', () => {
    dirInputA.value = ''; dirClearA.classList.remove('visible');
    closeDropdown(dirDropA); dirSelectedA = null; onDirSelectionChange();
});

function onDirSelectionChange() {
    document.getElementById('btnHitungDir').disabled = !dirSelectedA;
    if (dirMarkerA) { map.removeLayer(dirMarkerA); dirMarkerA = null; }
    if (dirSelectedA) {
        dirMarkerA = L.marker([dirSelectedA.latitude, dirSelectedA.longitude], { icon: makeLineIcon('A', '#4a7c59') })
            .bindPopup(`<div class="popup-inner"><div class="popup-title">${escHtml(dirSelectedA.nama_tempat)}</div></div>`)
            .addTo(map);
    }
    // Reset hasil rute
    if (dirRouteLine) { map.removeLayer(dirRouteLine); dirRouteLine = null; }
    document.getElementById('dirDistanceCard').classList.remove('visible');
    document.getElementById('dirInfoCardA').classList.remove('visible');
    document.getElementById('dirInfoCardB').classList.remove('visible');
    document.getElementById('emptyStateDir').style.display = dirSelectedA ? 'none' : '';
    document.getElementById('mapDistanceBadge').classList.remove('visible');
}

document.getElementById('btnHitungDir').addEventListener('click', hitungRute);

function hitungRute() {
    if (!dirSelectedA || !dirDestTempat) return;
    document.getElementById('dirLoadingState').classList.add('visible');
    document.getElementById('emptyStateDir').style.display = 'none';
    if (dirRouteLine) { map.removeLayer(dirRouteLine); dirRouteLine = null; }

    const urlOSRM = `https://router.project-osrm.org/route/v1/driving/`
        + `${dirSelectedA.longitude},${dirSelectedA.latitude};${dirDestTempat.longitude},${dirDestTempat.latitude}`
        + `?overview=full&geometries=geojson&steps=false`;

    fetch(urlOSRM)
        .then(r => r.json())
        .then(data => {
            if (data.code !== 'Ok' || !data.routes.length) throw new Error();
            const route     = data.routes[0];
            const jarakKm   = route.distance / 1000;
            const durasiMin = Math.round(route.duration / 60);
            const coords    = route.geometry.coordinates.map(c => [c[1], c[0]]);
            drawRoute(jarakKm, durasiMin, coords);
            document.getElementById('dirLoadingState').classList.remove('visible');
        })
        .catch(() => {
            const jarak = haversine(dirSelectedA.latitude, dirSelectedA.longitude, dirDestTempat.latitude, dirDestTempat.longitude);
            drawRoute(jarak, null, null);
            document.getElementById('dirLoadingState').classList.remove('visible');
            const errEl = document.getElementById('dirErrorState');
            errEl.textContent = '⚠ Rute jalan tidak tersedia, menampilkan garis lurus.';
            errEl.classList.add('visible');
            setTimeout(() => errEl.classList.remove('visible'), 4000);
        });
}

function drawRoute(jarakKm, durasiMin, routeCoords) {
    const latlngs     = routeCoords || [[dirSelectedA.latitude, dirSelectedA.longitude],[dirDestTempat.latitude, dirDestTempat.longitude]];
    const isRealRoute = !!routeCoords;

    dirRouteLine = L.polyline(latlngs, {
        color: '#e85d5d', weight: isRealRoute ? 5 : 4, opacity: .88,
        dashArray: isRealRoute ? null : '10 6', lineJoin: 'round', lineCap: 'round',
    }).addTo(map);

    map.fitBounds(dirRouteLine.getBounds().pad(0.25), { animate: true });

    const jarakStr   = formatJarak(jarakKm);
    const labelJenis = isRealRoute ? 'Jarak Jalan' : 'Jarak Garis Lurus';
    const durasiHtml = durasiMin != null
        ? `<div style="font-size:.8rem;color:var(--matcha);font-weight:600;margin-top:4px;">⏱ ~${durasiMin} menit berkendara</div>`
        : `<div style="font-size:.72rem;color:var(--ink-soft);margin-top:4px;">estimasi jalan tidak tersedia</div>`;

    document.getElementById('dirDistanceLabel').textContent = labelJenis;
    document.getElementById('dirDistanceValue').textContent = jarakStr;
    document.getElementById('dirDistanceFromTo').innerHTML  =
        `<strong>${escHtml(dirSelectedA.nama_tempat)}</strong><br>→ <strong>${escHtml(dirDestTempat.nama_tempat)}</strong>${durasiHtml}`;
    document.getElementById('dirDistanceCard').classList.add('visible');

    document.getElementById('mapDistanceText').textContent = `${jarakStr}${durasiMin != null ? ' · ~'+durasiMin+' mnt' : ''}`;
    document.getElementById('mapDistanceBadge').classList.add('visible');

    // Info cards
    renderDirInfoCard('A', dirSelectedA);
    renderDirInfoCard('B', dirDestTempat);
    document.getElementById('emptyStateDir').style.display = 'none';
}

function renderDirInfoCard(which, tempat) {
    document.getElementById(`dirInfoName${which}`).textContent = tempat.nama_tempat;
    document.getElementById(`dirInfoBody${which}`).innerHTML = [
        tempat.jalan     ? { icon:'📍', text: tempat.jalan }              : null,
        tempat.kecamatan ? { icon:'🏘', text: tempat.kecamatan }          : null,
        tempat.kategori  ? { icon:'🏷', text: tempat.kategori }           : null,
        tempat.rating    ? { icon:'⭐', text: `Rating ${tempat.rating}` } : null,
        tempat.kontak    ? { icon:'📞', text: tempat.kontak }             : null,
    ].filter(Boolean).map(r =>
        `<div class="point-info-row"><span>${r.icon}</span><span>${escHtml(r.text)}</span></div>`
    ).join('');
    document.getElementById(`dirInfoCard${which}`).classList.add('visible');
}

// ════════════════════════════════════════════════════
//  MAIN SEARCH BAR
// ════════════════════════════════════════════════════
const mainSearchInput    = document.getElementById('mainSearchInput');
const mainSearchClear    = document.getElementById('mainSearchClear');
const mainSearchDropdown = document.getElementById('mainSearchDropdown');

mainSearchInput.addEventListener('input', () => {
    const q = mainSearchInput.value.trim();
    mainSearchClear.classList.toggle('visible', q.length > 0);
    if (q.length < 2) { mainSearchDropdown.classList.remove('open'); return; }
    clearTimeout(mainSearchInput._timer);
    mainSearchInput._timer = setTimeout(() => {
        const results = searchTempat(q);
        mainSearchDropdown.innerHTML = '';
        if (!results.length) {
            mainSearchDropdown.innerHTML = '<div class="dropdown-item-empty">Tidak ada hasil.</div>';
        } else {
            results.forEach(t => {
                const item = document.createElement('div');
                item.className = 'dropdown-item';
                item.innerHTML = `<div class="dropdown-item-name">${escHtml(t.nama_tempat)}</div>
                    <div class="dropdown-item-sub">${escHtml(t.kategori||'')}${t.kecamatan ? ' · '+escHtml(t.kecamatan) : ''}</div>`;
                item.addEventListener('mousedown', e => {
                    e.preventDefault();
                    mainSearchInput.value = t.nama_tempat;
                    mainSearchClear.classList.add('visible');
                    mainSearchDropdown.classList.remove('open');
                    map.setView([t.latitude, t.longitude], 16, { animate: true });
                    setTimeout(() => { if (pointMarkers[t.id]) pointMarkers[t.id].openPopup(); }, 350);
                    highlightCard(t.id);
                });
                mainSearchDropdown.appendChild(item);
            });
        }
        mainSearchDropdown.classList.add('open');
    }, DEBOUNCE_MS);
});

mainSearchInput.addEventListener('blur', () => setTimeout(() => mainSearchDropdown.classList.remove('open'), 180));
mainSearchInput.addEventListener('focus', () => { if (mainSearchInput.value.trim().length >= 2) mainSearchDropdown.classList.add('open'); });

mainSearchClear.addEventListener('click', () => {
    mainSearchInput.value = '';
    mainSearchClear.classList.remove('visible');
    mainSearchDropdown.classList.remove('open');
    mainSearchInput.focus();
});

// ════════════════════════════════════════════════════
//  INIT
// ════════════════════════════════════════════════════
fetchData();
fetchPolygons();

})();
</script>
@endpush