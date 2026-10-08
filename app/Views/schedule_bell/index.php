<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Flatpickr for 24-hour time picker enforcement -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
    .schedule-wrapper {
        padding: 15px;
        background: #f4f6fa;
        min-height: calc(100vh - 60px);
        overflow: auto;
    }
    .schedule-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .schedule-card h2 {
        font-size: 14px;
        font-weight: bold;
        color: #222;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    /* Modernized toolbar & header styles */
    .toolbar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .toolbar-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .toolbar-title h2 {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.2px;
    }
    .toolbar-title-badge {
        background: #e0f2fe;
        color: #0369a1;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        border: 1px solid #bae6fd;
    }

    /* Action Button Uniform Styles */
    .btn-tb-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0 13px;
        height: 34px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        white-space: nowrap;
    }
    .btn-tb-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        opacity: 0.96;
    }
    .btn-tb-primary {
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff !important;
        height: 36px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);
    }
    .btn-tb-primary:hover {
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.4);
    }
    .btn-tb-purple { background: #8b5cf6; color: #fff !important; }
    .btn-tb-sky { background: #0284c7; color: #fff !important; }
    .btn-tb-indigo { background: #4f46e5; color: #fff !important; }
    .btn-tb-slate { background: #475569; color: #fff !important; }
    .btn-tb-amber { background: #d97706; color: #fff !important; }
    .btn-tb-blue { background: #2563eb; color: #fff !important; }
    .btn-tb-dark { background: #0f172a; border: 1px solid #334155; color: #38bdf8 !important; }
    .btn-tb-teal { background: #0d9488; color: #fff !important; }

    /* Control Inputs */
    .form-control-sm {
        height: 32px;
        padding: 4px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #0f172a;
        background: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .form-control-sm:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
    }

    .btn-add {
        background: #28a745;
        color: white;
        border: none;
        padding: 7px 18px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 13px;
        cursor: pointer;
    }
    .btn-add:hover { opacity: 0.88; }

    .btn-export {
        background: #007bff;
        color: white;
        border: none;
        padding: 7px 18px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .btn-export:hover { opacity: 0.88; }
    
    .btn-import {
        background: #ffc107;
        color: #222;
        border: none;
        padding: 7px 18px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 13px;
        cursor: pointer;
    }
    .btn-import:hover { opacity: 0.88; }

    .tbl-wrap {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 72vh;
    }

    table.sched {
        border-collapse: collapse;
        font-size: 11px;
        font-family: Arial, sans-serif;
        min-width: 2900px;
        table-layout: fixed;
    }

    table.sched th, table.sched td {
        border: 1px solid #b0b0b0;
        padding: 0;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        height: 24px;
    }

    /* Sticky columns */
    table.sched th.col-aksi,
    table.sched td.col-aksi {
        width: 105px;
        min-width: 105px;
        position: sticky;
        left: 0;
        z-index: 3;
        background: #dce3f0;
    }
    table.sched th.col-no,
    table.sched td.col-no {
        width: 40px;
        min-width: 40px;
        position: sticky;
        left: 105px;
        z-index: 3;
        background: #dce3f0;
    }
    table.sched th.col-conten,
    table.sched td.col-conten {
        width: 270px;
        min-width: 270px;
        text-align: left;
        padding: 0 6px;
        position: sticky;
        left: 145px;
        z-index: 3;
        background: #dce3f0;
    }
    table.sched td.col-conten { background: #fff; }
    table.sched td.col-aksi { background: #fff; }

    /* Header row 1 group labels */
    table.sched thead tr.grp-row th {
        background: #3a5a99;
        color: #fff;
        font-weight: bold;
        font-size: 11px;
        padding: 3px 4px;
        height: 22px;
    }
    table.sched thead tr.grp-row th.col-aksi,
    table.sched thead tr.grp-row th.col-no,
    table.sched thead tr.grp-row th.col-conten {
        background: #3a5a99;
        color: #fff;
        z-index: 4;
    }

    /* Header row 2 sub-labels */
    table.sched thead tr.sub-row th {
        background: #c6d0e8;
        color: #222;
        font-weight: bold;
        font-size: 10px;
        padding: 2px 2px;
        height: 20px;
    }
    table.sched thead tr.sub-row th.col-aksi,
    table.sched thead tr.sub-row th.col-no,
    table.sched thead tr.sub-row th.col-conten {
        background: #c6d0e8;
        z-index: 4;
    }
    table.sched thead tr.sub-row th.yellow { background: #f9d923; }

    /* Day columns */
    th.day-col, td.day-col { width: 22px; min-width: 22px; }
    /* Building */
    th.bld-col, td.bld-col { width: 90px; min-width: 90px; }
    /* Speaker */
    th.spk-col, td.spk-col { width: 28px; min-width: 28px; }
    /* Time cols */
    th.time-col, td.time-col { width: 42px; min-width: 42px; }

    /* Data rows */
    table.sched tbody tr:nth-child(even) td { background: #f7f9ff; }
    table.sched tbody tr:nth-child(even) td.col-conten, table.sched tbody tr:nth-child(even) td.col-aksi { background: #f0f4fb; }
    table.sched tbody tr:nth-child(odd) td.col-conten, table.sched tbody tr:nth-child(odd) td.col-aksi { background: #fff; }
    table.sched tbody tr:hover td { background: #e8f0fe !important; }

    /* Green filled cell (Upcoming / Standard Ring) */
    table.sched td.ring {
        background: #7ac740 !important;
    }

    /* Passed Ring Cell Highlight (Deep Emerald Green + Checkmark) */
    table.sched td.ring.cell-passed {
        background: #15803d !important;
        color: #ffffff !important;
        position: relative;
    }
    table.sched td.ring.cell-passed::after {
        content: "✓";
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        display: block;
        text-align: center;
        line-height: 1;
    }

    /* Yellow row highlight (only on non-ring cells so green rings stay green) */
    tr.row-yellow td:not(.ring) { background: #fffde7 !important; }
    tr.row-yellow td.col-conten, tr.row-yellow td.col-aksi, tr.row-yellow td.col-no { background: #fdf9c4 !important; }

    /* Passed & Completed Bell Styles (ONLY affects non-ring cells) */
    table.sched tr.row-passed td:not(.ring) {
        background: #f8fafc;
        color: #64748b;
    }
    table.sched tr.row-passed td.col-conten {
        background: #f1f5f9;
    }
    table.sched tr.row-passed td.col-no, table.sched tr.row-passed td.col-aksi {
        background: #f1f5f9;
    }

    table.sched thead { position: sticky; top: 0; z-index: 2; }
    
    .action-btn { padding: 3px 5px; font-size: 10px; cursor: pointer; border: none; border-radius: 3px; color: #fff; line-height: 1; }
    .btn-edit { background: #17a2b8; }
    .btn-audio { background: #28a745; }
    .btn-copy { background: #6f42c1; }
    .btn-del { background: #dc3545; }
    .btn-color { background: #f39c12; }

    /* Preset Buttons */
    .btn-preset {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-preset:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .btn-preset-danger {
        color: #dc3545;
        border-color: #fca5a5;
        background: #fff5f5;
    }
    .btn-preset-danger:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Inline editing styles */
    .cell-toggle {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .cell-toggle:hover {
        outline: 2px solid #28a745;
        outline-offset: -2px;
        filter: brightness(0.95);
    }
    .cell-editable {
        cursor: cell;
        position: relative;
    }
    .cell-editable:hover {
        outline: 1px dashed #007bff;
        outline-offset: -1px;
    }
    .cell-editing-input {
        width: 100%;
        height: 100%;
        border: 2px solid #007bff !important;
        padding: 2px 4px !important;
        font-size: 11px !important;
        box-sizing: border-box;
        outline: none;
        background: #fff;
        font-family: inherit;
    }
    .toast-notify {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: rgba(34, 34, 34, 0.9);
        color: #fff;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: bold;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: none;
        align-items: center;
        gap: 8px;
        transition: opacity 0.3s ease;
    }

    /* Cell Action Popover Balloon */
    .cell-popover {
        position: absolute;
        z-index: 10005;
        background: #0f172a;
        color: #f8fafc;
        border: 1px solid #334155;
        border-radius: 10px;
        padding: 12px 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.35);
        min-width: 240px;
        max-width: 320px;
        font-family: inherit;
        animation: popoverFadeIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes popoverFadeIn {
        from { opacity: 0; transform: scale(0.92) translateY(-4px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .popover-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #334155;
        padding-bottom: 6px;
        margin-bottom: 8px;
        font-size: 11px;
        font-weight: 700;
        color: #38bdf8;
    }
    .popover-close {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
    }
    .popover-close:hover { color: #fff; }

    .popover-actions-group {
        display: flex;
        gap: 6px;
        justify-content: space-between;
    }
    .btn-pop-action {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 7px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-pop-edit {
        background: #2563eb;
        color: #fff !important;
    }
    .btn-pop-edit:hover { background: #1d4ed8; }
    
    .btn-pop-delete {
        background: #dc2626;
        color: #fff !important;
    }
    .btn-pop-delete:hover { background: #b91c1c; }

    .btn-pop-cancel {
        background: #475569;
        color: #e2e8f0 !important;
    }
    .btn-pop-cancel:hover { background: #334155; }

    .submenu-divider {
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        margin: 8px 0 6px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .submenu-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 5px;
    }
    .btn-sub-action {
        background: #1e293b;
        color: #f1f5f9;
        border: 1px solid #334155;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-align: left;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-sub-action:hover {
        background: #334155;
        border-color: #38bdf8;
        color: #38bdf8;
    }
    .btn-pop-back {
        width: 100%;
        margin-top: 8px;
        background: transparent;
        border: 1px solid #475569;
        color: #cbd5e1;
        padding: 4px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-pop-back:hover { background: #334155; color: #fff; }

    /* Modal styles */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 9999; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(3px);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 3% auto;
        padding: 20px;
        border: 1px solid #cbd5e1;
        width: 90%;
        max-width: 800px;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    }
    .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    .close:hover { color: #000; }
    
    /* Modern Modal Upgrades */
    .btn-chip {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-chip:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .chip-reset {
        color: #ef4444;
        border-color: #fca5a5;
        background: #fff5f5;
    }
    .chip-reset:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    .time-pill-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 6px;
    }
    .time-pill {
        position: relative;
        display: inline-block;
        user-select: none;
    }
    .time-pill input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .time-pill-label {
        display: block;
        padding: 5px 0;
        text-align: center;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .time-pill:hover .time-pill-label {
        border-color: #3b82f6;
        color: #1e40af;
        background: #eff6ff;
    }
    .time-pill input:checked + .time-pill-label {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        border-color: #15803d;
        box-shadow: 0 2px 5px rgba(22, 163, 74, 0.3);
    }
    .time-pill-yellow input:checked + .time-pill-label {
        background: linear-gradient(135deg, #eab308, #ca8a04);
        color: #fff;
        border-color: #a16207;
        box-shadow: 0 2px 5px rgba(202, 138, 4, 0.3);
    }

    .day-pill-grid {
        display: flex;
        gap: 8px;
    }
    .day-pill {
        flex: 1;
        position: relative;
    }
    .day-pill input {
        position: absolute;
        opacity: 0;
        height: 0;
        width: 0;
    }
    .day-pill-label {
        display: block;
        padding: 7px 0;
        text-align: center;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .day-pill:hover .day-pill-label {
        border-color: #3b82f6;
        color: #1e40af;
    }
    .day-pill input:checked + .day-pill-label {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border-color: #1d4ed8;
        box-shadow: 0 2px 5px rgba(37, 99, 235, 0.3);
    }

    .spk-pill input:checked + .day-pill-label {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
        border-color: #6d28d9;
        box-shadow: 0 2px 5px rgba(124, 58, 237, 0.3);
    }

    .modal-preview-box {
        background: #0f172a;
        color: #f8fafc;
        border-radius: 8px;
        padding: 12px 16px;
        margin-top: 15px;
        border: 1px solid #334155;
    }
</style>

<div class="right-frame" style="background: #f4f6fa; padding: 20px; min-height: calc(100vh - 60px);">
    <!-- ANALYTICS WIDGET CARDS BAR -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 16px;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Bel Terdaftar</div>
                <div style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 2px;"><?= count($schedules) ?> Jadwal</div>
            </div>
            <div style="background: #eff6ff; color: #2563eb; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">🔔</div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Bel Aktif Hari Ini</div>
                <div id="activeTodayCount" style="font-size: 22px; font-weight: 800; color: #16a34a; margin-top: 2px;">-- Bel</div>
            </div>
            <div style="background: #f0fdf4; color: #16a34a; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">⚡</div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Coverage Speaker</div>
                <div id="spkCoverage" style="font-size: 14px; font-weight: 800; color: #7c3aed; margin-top: 4px;">IN & OUT Active</div>
            </div>
            <div style="background: #f3e8ff; color: #7c3aed; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">📢</div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Profil Shift Aktif</div>
                <div style="font-size: 14px; font-weight: 800; color: #0284c7; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                    <span id="activeProfileLabel">Mode Normal</span>
                </div>
            </div>
            <div style="background: #e0f2fe; color: #0284c7; width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">🌙</div>
        </div>
    </div>

    <!-- NEXT BELL LIVE COUNTDOWN BANNER -->
    <div id="nextBellBanner" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: #fff; padding: 14px 20px; border-radius: 10px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.12); flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.4); width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                🔔
            </div>
            <div>
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 600;">Status Bel Selanjutnya (Real-Time)</div>
                <div id="nextBellTitle" style="font-size: 15px; font-weight: 700; color: #38bdf8; margin-top: 2px;">Menganalisis jadwal...</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <!-- AUTO-BELL LIVE TOGGLE -->
            <label style="background: rgba(255,255,255,0.08); padding: 6px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.15); display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: bold; cursor: pointer; color: #e2e8f0;">
                <input type="checkbox" id="toggleAutoBell" onchange="toggleAutoBellMode(this)" style="cursor: pointer;">
                <span>🔊 Live Auto-Bell</span>
            </label>
            <div style="text-align: right;">
                <div style="font-size: 11px; color: #94a3b8;">Hitung Mundur Dering</div>
                <div id="nextBellTimer" style="font-size: 20px; font-weight: 800; color: #f59e0b; font-family: monospace;">--:--:--</div>
            </div>
            <div style="background: rgba(255,255,255,0.08); padding: 8px 14px; border-radius: 8px; text-align: center; border: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size: 10px; color: #cbd5e1; text-transform: uppercase;">Jam Sekarang</div>
                <div id="liveClock" style="font-size: 14px; font-weight: 700; color: #fff; font-family: monospace;">00:00:00</div>
            </div>
        </div>
    </div>

    <!-- CONTROL TOOLBAR: PRESET, SHIFT & SEARCH FILTER BAR (SINGLE ROW) -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 14px; margin-bottom: 16px; display: flex; flex-wrap: nowrap; justify-content: space-between; align-items: center; gap: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); overflow-x: auto;">
        <!-- PRESETS -->
        <div style="display: flex; align-items: center; gap: 4px; flex-shrink: 0;">
            <span style="font-size: 11px; font-weight: 700; color: #475569; white-space: nowrap;">⚡ Preset:</span>
            <button type="button" class="btn-preset" style="padding: 4px 8px; font-size: 11px;" onclick="runPreset('workdays')">Senin–Jumat</button>
            <button type="button" class="btn-preset" style="padding: 4px 8px; font-size: 11px;" onclick="runPreset('spk_in')">Indoor</button>
            <button type="button" class="btn-preset" style="padding: 4px 8px; font-size: 11px;" onclick="runPreset('spk_out')">Outdoor</button>
            <button type="button" class="btn-preset btn-preset-danger" style="padding: 4px 8px; font-size: 11px;" onclick="runPreset('clear_ring')">Reset</button>
        </div>
        
        <div style="height: 18px; width: 1px; background: #cbd5e1; flex-shrink: 0;"></div>

        <!-- SHIFT PROFILE -->
        <div style="display: flex; align-items: center; gap: 4px; flex-shrink: 0;">
            <span style="font-size: 11px; font-weight: 700; color: #475569; white-space: nowrap;">🌙 Profil Shift:</span>
            <select id="shiftProfileSelect" class="form-control-sm" onchange="switchShiftProfile(this.value)" style="font-weight: 600; width: 160px; font-size: 11px; padding: 2px 6px; height: 30px;">
                <option value="Mode Normal">🟢 Mode Normal</option>
                <option value="Mode Ramadan">🌙 Mode Ramadan</option>
                <option value="Mode Overtime">🛠️ Mode Overtime</option>
            </select>
            <button type="button" class="btn-preset" style="padding: 4px 8px; font-size: 11px;" onclick="saveCurrentProfile()" title="Simpan Jadwal Saat Ini Ke Profil Shift Baru">💾 Simpan</button>
        </div>

        <div style="height: 18px; width: 1px; background: #cbd5e1; flex-shrink: 0;"></div>

        <!-- RIGHT: LIVE FILTERS (COMPACT SINGLE ROW - 50% SHORTER DROPDOWNS) -->
        <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
            <input type="text" id="filterKeyword" class="form-control-sm" placeholder="🔍 Cari..." style="width: 140px; font-size: 11px; padding: 2px 8px; height: 30px;" onkeyup="filterScheduleTable()">
            <select id="filterDay" class="form-control-sm" onchange="filterScheduleTable()" style="width: 85px; font-size: 11px; padding: 2px 4px; height: 30px;">
                <option value="">Hari: Semua</option>
                <option value="1">Senin</option>
                <option value="2">Selasa</option>
                <option value="3">Rabu</option>
                <option value="4">Kamis</option>
                <option value="5">Jumat</option>
                <option value="6">Sabtu</option>
            </select>
            <select id="filterSpeaker" class="form-control-sm" onchange="filterScheduleTable()" style="width: 95px; font-size: 11px; padding: 2px 4px; height: 30px;">
                <option value="">Spk: Semua</option>
                <option value="in">IN (Indoor)</option>
                <option value="out">OUT (Outdoor)</option>
            </select>
        </div>
    </div>

    <!-- MAIN SCHEDULE CARD & TOOLBAR -->
    <div class="schedule-card">
        <div class="toolbar-header">
            <div class="toolbar-title">
                <h2>🔔 SCHEDULE RING OF INFORMATION AUDIO</h2>
                <span class="toolbar-title-badge">Production Building Area</span>
            </div>

            <!-- ORGANIZED ACTION BUTTON GROUPS -->
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <!-- GROUP 1: ALAT & FITUR AUDIO -->
                <div style="display: flex; gap: 6px; align-items: center;">
                    <button type="button" class="btn-tb-action btn-tb-purple" onclick="openAudioLibraryModal()" title="Buka Perpustakaan File Audio Custom">🎵 Kelola Audio</button>
                    <button type="button" class="btn-tb-action btn-tb-sky" onclick="openPeriodicWizardModal()" title="Buat Jadwal Dering Otomatis Berkala">⚡ Periodic Wizard</button>
                    <button type="button" class="btn-tb-action btn-tb-indigo" onclick="runManualReorder()" title="Urutkan Ulang Jadwal Berdasarkan Jam Dering">🔢 Auto-Urut Jam</button>
                    <button type="button" class="btn-tb-action btn-tb-indigo" onclick="openHolidayModal()" title="Atur Tanggal Libur Pabrik">🗓️ Hari Libur</button>
                    <button type="button" class="btn-tb-action btn-tb-slate" onclick="openLogModal()" title="Lihat Riwayat Perubahan">📜 Audit Log</button>
                </div>

                <div style="height: 22px; width: 1px; background: #cbd5e1; margin: 0 2px;"></div>

                <!-- GROUP 2: EXPORT / IMPORT & DISPLAY -->
                <div style="display: flex; gap: 6px; align-items: center;">
                    <button type="button" class="btn-tb-action btn-tb-amber" onclick="openImportModal()" title="Import File Jadwal dari Excel">📤 Import Excel</button>
                    <a href="<?= site_url('schedule-bell/export') ?>" class="btn-tb-action btn-tb-blue" title="Download File Excel Jadwal Saat Ini">📥 Export Excel</a>
                    <a href="<?= site_url('schedule-bell/display') ?>" target="_blank" class="btn-tb-action btn-tb-dark" title="Buka Mode TV Display Kiosk">📺 TV Display</a>
                    <a href="<?= site_url('schedule-bell/print') ?>" target="_blank" class="btn-tb-action btn-tb-teal" title="Cetak Dokumen A4 Landscape">🖨️ Cetak / PDF</a>
                </div>

                <div style="height: 22px; width: 1px; background: #cbd5e1; margin: 0 2px;"></div>

                <!-- GROUP 3: PRIMARY CTA -->
                <button type="button" class="btn-tb-action btn-tb-primary" onclick="openModal('add')">➕ Tambah Jadwal</button>
            </div>
        </div>

        <div class="tbl-wrap">
        <table class="sched" id="schedTable">
            <thead>
                <!-- GROUP ROW -->
                <tr class="grp-row">
                    <th class="col-aksi" rowspan="2">AKSI</th>
                    <th class="col-no" rowspan="2">NO</th>
                    <th class="col-conten" rowspan="2">CONTEN</th>
                    <th colspan="6">DAYS RING</th>
                    <th rowspan="2" class="bld-col">BUILDING</th>
                    <th colspan="2">SPEAKER</th>
                    <th colspan="<?= count($timeColumns) ?>">TIME RING</th>
                </tr>
                <!-- SUB LABEL ROW -->
                <tr class="sub-row">
                    <th class="day-col">S</th>
                    <th class="day-col">S</th>
                    <th class="day-col">R</th>
                    <th class="day-col">K</th>
                    <th class="day-col">J</th>
                    <th class="day-col">S</th>
                    <th class="spk-col">IN</th>
                    <th class="spk-col">OUT</th>
                    <?php foreach ($timeColumns as $tc): ?>
                        <th class="time-col"><?= $tc['label'] ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($schedules as $row): ?>
                <?php $rowClass = $row['highlight'] ? ' class="row-yellow"' : ''; ?>
                <tr id="row-<?= $row['id'] ?>"<?= $rowClass ?> data-day1="<?= $row['day_1'] ?>" data-day2="<?= $row['day_2'] ?>" data-day3="<?= $row['day_3'] ?>" data-day4="<?= $row['day_4'] ?>" data-day5="<?= $row['day_5'] ?>" data-day6="<?= $row['day_6'] ?>" data-spkin="<?= $row['spk_in'] ?>" data-spkout="<?= $row['spk_out'] ?>">
                    <td class="col-aksi">
                        <button class="action-btn btn-edit" onclick="openModal('edit', <?= htmlspecialchars(json_encode($row)) ?>)" title="Edit Modal">✏️</button>
                        <button class="action-btn btn-audio" onclick="playAudioBellPreview('<?= esc($row['conten']) ?>', '<?= esc($row['announcement_text'] ?? '') ?>', '<?= esc($row['audio_file'] ?? '') ?>')" title="Simulasi Suara Audio Bel">🔊</button>
                        <button class="action-btn" style="background:#0284c7;" onclick="playSpeechAnnouncement('<?= esc($row['announcement_text'] ?? $row['conten']) ?>')" title="Tes Pengumuman Suara (TTS)">🗣️</button>
                        <button class="action-btn btn-copy" onclick="duplicateSchedule(<?= $row['id'] ?>)" title="Duplikat Baris Ini">📋</button>
                        <button class="action-btn btn-color" onclick="toggleRowHighlight(<?= $row['id'] ?>)" title="Toggle Highlight (Kuning)">🎨</button>
                        <a href="<?= site_url('schedule-bell/delete/'.$row['id']) ?>" class="action-btn btn-del" onclick="return confirm('Hapus data ini?')" title="Hapus">🗑️</a>
                    </td>
                    <td class="col-no cell-editable" data-id="<?= $row['id'] ?>" data-field="no" title="Klik 2x untuk edit NO"><?= $row['no'] ?></td>
                    <td class="col-conten cell-editable" data-id="<?= $row['id'] ?>" data-field="conten" title="Klik 2x untuk edit CONTEN"><?= htmlspecialchars($row['conten']) ?></td>
                    
                    <td class="day-col cell-toggle<?= $row['day_1'] ? ' ring' : '' ?>" data-id="<?= $row['id'] ?>" data-field="day_1" title="Klik untuk toggle Senin"></td>
                    <td class="day-col cell-toggle<?= $row['day_2'] ? ' ring' : '' ?>" data-id="<?= $row['id'] ?>" data-field="day_2" title="Klik untuk toggle Selasa"></td>
                    <td class="day-col cell-toggle<?= $row['day_3'] ? ' ring' : '' ?>" data-id="<?= $row['id'] ?>" data-field="day_3" title="Klik untuk toggle Rabu"></td>
                    <td class="day-col cell-toggle<?= $row['day_4'] ? ' ring' : '' ?>" data-id="<?= $row['id'] ?>" data-field="day_4" title="Klik untuk toggle Kamis"></td>
                    <td class="day-col cell-toggle<?= $row['day_5'] ? ' ring' : '' ?>" data-id="<?= $row['id'] ?>" data-field="day_5" title="Klik untuk toggle Jumat"></td>
                    <td class="day-col cell-toggle<?= $row['day_6'] ? ' ring' : '' ?>" data-id="<?= $row['id'] ?>" data-field="day_6" title="Klik untuk toggle Sabtu"></td>
                    
                    <td class="bld-col cell-editable" data-id="<?= $row['id'] ?>" data-field="building" title="Klik 2x untuk edit BUILDING"><?= htmlspecialchars($row['building']) ?></td>
                    
                    <td class="spk-col cell-toggle<?= $row['spk_in'] ? ' ring' : '' ?>" data-id="<?= $row['id'] ?>" data-field="spk_in" title="Klik untuk toggle Speaker IN"></td>
                    <td class="spk-col cell-toggle<?= $row['spk_out'] ? ' ring' : '' ?>" data-id="<?= $row['id'] ?>" data-field="spk_out" title="Klik untuk toggle Speaker OUT"></td>
                    
                    <?php foreach ($timeColumns as $tc): ?>
                        <td class="time-col cell-toggle<?= !empty($row[$tc['key']]) ? ' ring' : '' ?>" data-id="<?= $row['id'] ?>" data-field="<?= $tc['key'] ?>" title="Klik untuk toggle Dering <?= $tc['label'] ?>"></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Floating Cell Popover Balloon -->
<div id="cellActionPopover" class="cell-popover" style="display: none;">
    <div class="popover-header">
        <span id="popoverCellTitle">📌 Detail Cell</span>
        <button type="button" class="popover-close" onclick="closeCellPopover()">&times;</button>
    </div>
    <div class="popover-body">
        <!-- Step 1: Main Action Balloon (Ubah, Hapus, Batal) -->
        <div id="popoverMainActions">
            <div style="font-size: 11px; color: #cbd5e1; margin-bottom: 8px;" id="popoverCellSubtitle">Pilih tindakan untuk cell ini:</div>
            <div class="popover-actions-group">
                <button type="button" class="btn-pop-action btn-pop-edit" onclick="showPopoverEditSubmenu()">
                    <span>✏️ Ubah</span>
                </button>
                <button type="button" class="btn-pop-action btn-pop-delete" onclick="handlePopoverDelete()">
                    <span>🗑️ Hapus</span>
                </button>
                <button type="button" class="btn-pop-action btn-pop-cancel" onclick="closeCellPopover()">
                    <span>✖️ Batal</span>
                </button>
            </div>
        </div>

        <!-- Step 2: Edit Submenu Options -->
        <div id="popoverEditSubmenu" style="display: none;">
            <div class="submenu-divider"><span>Pilih Opsi Edit:</span></div>
            <div class="submenu-grid">
                <button type="button" class="btn-sub-action" onclick="execPopoverToggle()">
                    <span>⚡ Toggle Status Dering (ON/OFF)</span>
                </button>
                <button type="button" class="btn-sub-action" onclick="execPopoverTextEdit()">
                    <span>📝 Edit Teks / Building Cell</span>
                </button>
                <button type="button" class="btn-sub-action" onclick="execPopoverAudioEdit()">
                    <span>🎵 Atur Sound Audio & TTS</span>
                </button>
                <button type="button" class="btn-sub-action" onclick="execPopoverFullForm()">
                    <span>⚙️ Edit Lengkap (Form Modal)</span>
                </button>
                <button type="button" class="btn-sub-action" onclick="execPopoverDuplicate()">
                    <span>📋 Duplikat Jadwal Baris Ini</span>
                </button>
                <button type="button" class="btn-sub-action" onclick="execPopoverHighlight()">
                    <span>🎨 Highlight Warna (Kuning)</span>
                </button>
            </div>
            <button type="button" class="btn-pop-back" onclick="showPopoverMainMenu()">&laquo; Kembali ke Menu Utama</button>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div id="importModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <span class="close" onclick="closeImportModal()">&times;</span>
        <h3 style="margin-bottom: 20px;">Import Data Excel</h3>
        <form method="POST" action="<?= site_url('schedule-bell/import') ?>" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label>Pilih File Excel (.xlsx, .xls):</label>
                <input type="file" name="excel_file" accept=".xlsx, .xls" required style="margin-top: 10px; width: 100%;">
            </div>
            <p style="font-size: 11px; color: #dc3545; margin-bottom: 15px;">
                *Peringatan: Mengunggah file Excel akan <b>menghapus seluruh jadwal saat ini</b> dan menggantinya dengan isi file yang baru.
            </p>
            <button type="submit" class="btn-add" style="width: 100%;">Upload & Import</button>
        </form>
    </div>
</div>

<!-- Modal Form -->
<div id="scheduleModal" class="modal">
    <div class="modal-content" style="max-width: 840px; padding: 0; border-radius: 14px; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.25);">
        
        <!-- Modal Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; background: linear-gradient(135deg, #0f172a, #1e293b); color: #fff;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; border: 1px solid rgba(56, 189, 248, 0.4);">
                    🔔
                </div>
                <div>
                    <h3 id="modalTitle" style="margin:0; font-size: 17px; font-weight: 800; color: #fff; letter-spacing: 0.3px;">Tambah Jadwal Bel Baru</h3>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Konfigurasi nama bel, hari operasional, zona speaker, dan matriks waktu dering</div>
                </div>
            </div>
            <span class="close" onclick="closeModal()" style="color: #94a3b8; font-size: 26px; cursor: pointer; line-height: 1; transition: color 0.15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#94a3b8'">&times;</span>
        </div>

        <form id="scheduleForm" method="POST" action="<?= site_url('schedule-bell/store') ?>" onsubmit="return validateAndSyncForm(event)" style="padding: 24px; max-height: 82vh; overflow-y: auto;">
            
            <!-- TEMPLATE PRESET TOOLBAR -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div style="font-size: 12px; font-weight: 700; color: #334155; display: flex; align-items: center; gap: 6px;">
                    ⚡ Templat Cepat (Auto-Fill Preset):
                </div>
                <select id="inp_template_preset" onchange="applyFormTemplate(this.value)" style="padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600; color: #0f172a; flex: 1; max-width: 380px; background: #fff; cursor: pointer;">
                    <option value="">-- Pilih Templat Otomatis --</option>
                    <option value="masuk_pagi">🌅 Bel Masuk Shift Pagi (07:00)</option>
                    <option value="apel_briefing">📢 Apel / Briefing Karyawan (07:05)</option>
                    <option value="istirahat_siang">🍲 Istirahat Siang (12:00)</option>
                    <option value="selesai_istirahat">⏰ Selesai Istirahat (12:30)</option>
                    <option value="pulang_kerja">🏁 Bel Pulang Kerja (16:00 / 12:30)</option>
                </select>
            </div>

            <!-- SECTION 1: INFORMASI UTAMA -->
            <input type="hidden" name="no" id="inp_no" value="1">
            <div style="margin-bottom: 20px;">
                <label style="font-weight: 700; font-size: 12px; color: #1e293b; display: block; margin-bottom: 5px;">Konten / Nama Bel:</label>
                <input type="text" name="conten" id="inp_conten" placeholder="Contoh: Bel Masuk Kerja Shift 1" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600;" oninput="updateLiveFormPreview()" list="contenSuggestions">
                <datalist id="contenSuggestions">
                    <option value="Bel Masuk Kerja Shift 1">
                    <option value="Apel Pagi / Safety Briefing">
                    <option value="Istirahat Siang / Sholat">
                    <option value="Selesai Istirahat Siang">
                    <option value="Bel Pulang Kerja Shift 1">
                    <option value="Pengumuman Penting Karyawan">
                </datalist>
            </div>

            <!-- SECTION 1B: BUILDING / AREA PRODUCTION CHECKLIST -->
            <div style="margin-bottom: 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label style="font-weight: 700; font-size: 12px; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                        🏢 Building / Area Production (Mode Checklist):
                    </label>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" class="btn-chip" onclick="quickToggleBuildings('factory')">Factory (F1–F6)</button>
                        <button type="button" class="btn-chip" onclick="quickToggleBuildings('all')">Semua Gedung</button>
                        <button type="button" class="btn-chip chip-reset" onclick="quickToggleBuildings('clear')">Reset Gedung</button>
                    </div>
                </div>

                <input type="hidden" name="building" id="inp_building" value="F1">

                <div class="day-pill-grid" style="flex-wrap: wrap; gap: 6px;">
                    <?php 
                    $bldList = [
                        'F1' => 'F1',
                        'F2' => 'F2',
                        'F3' => 'F3',
                        'F4' => 'F4',
                        'F5' => 'F5',
                        'F6' => 'F6',
                        'IH' => 'IH (In-House)',
                        'B1' => 'B1',
                        'B2' => 'B2',
                        'MO' => 'MO (Office)'
                    ];
                    foreach ($bldList as $code => $lbl):
                    ?>
                        <label class="day-pill spk-pill" style="min-width: 72px; flex: 1;">
                            <input type="checkbox" id="cb_bld_<?= $code ?>" value="<?= $code ?>" onchange="syncBuildingChecklistToInput()">
                            <span class="day-pill-label"><?= $lbl ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SECTION 1C: AUDIO SOUND INPUT (FILE UPLOAD & SOUND LIBRARY) -->
            <div style="margin-bottom: 20px; background: #fdf4ff; border: 1px solid #f0abfc; border-radius: 10px; padding: 14px 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="font-weight: 700; font-size: 12px; color: #86198f; display: flex; align-items: center; gap: 6px;">
                        🎵 Input File Audio Bel & Sound Library (MP3 / WAV Custom):
                    </label>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" class="btn-chip" onclick="openAudioLibraryModal()" style="background: #a855f7; color: #fff; border: none;">
                            📁 Kelola File Audio
                        </button>
                        <button type="button" class="btn-chip" onclick="testModalSelectedAudioPreview()" style="background: #9333ea; color: #fff; border: none;">
                            ▶️ Putar Preview Audio
                        </button>
                    </div>
                </div>

                <input type="hidden" name="audio_file" id="inp_audio_file" value="">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; align-items: flex-start;">
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: #701a75; display: block; margin-bottom: 4px;">Pilih Dari Sound Library:</label>
                        <select id="inp_audio_select" onchange="handleAudioSelectChange(this.value)" style="width: 100%; padding: 8px; border: 1px solid #d8b4fe; border-radius: 6px; font-size: 12px; font-weight: 600; color: #4c1d95; background: #fff; cursor: pointer;">
                            <option value="">🔔 Default Synthesizer Chime (Web Audio API)</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: #701a75; display: block; margin-bottom: 4px;">Atau Unggah File Audio Baru (.mp3, .wav):</label>
                        <input type="file" name="audio_file_upload" id="inp_audio_file_upload" accept="audio/mp3,audio/wav,audio/ogg,audio/mpeg,audio/m4a" style="width: 100%; font-size: 11px; padding: 5px; background: #fff; border: 1px solid #d8b4fe; border-radius: 6px;" onchange="previewUploadedAudioFile(this)">
                    </div>
                </div>
                <div id="audioFileNotice" style="font-size: 11px; color: #9333ea; margin-top: 6px; font-weight: 600;">
                    *Pilih audio tersimpan atau unggah file MP3/WAV kustom untuk bel ini (Maks. 10MB).
                </div>
            </div>

            <!-- SECTION 1D: TEXT-TO-SPEECH ANNOUNCEMENT -->
            <div style="margin-bottom: 20px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 14px 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="font-weight: 700; font-size: 12px; color: #0369a1; display: flex; align-items: center; gap: 6px;">
                        📢 Teks Pengumuman Suara Otomatis (TTS Voice Output):
                    </label>
                    <button type="button" class="btn-chip" onclick="testModalSpeechPreview()" style="background: #0284c7; color: #fff; border: none;">
                        🗣️ Tes Suara TTS
                    </button>
                </div>
                <textarea name="announcement_text" id="inp_announcement_text" rows="2" placeholder="Contoh: Perhatian, waktu istirahat siang telah tiba. Harap mematikan mesin dan mematuhi protokol K3." style="width: 100%; padding: 8px 12px; border: 1px solid #7dd3fc; border-radius: 6px; font-size: 12px; font-family: inherit; resize: vertical;" oninput="updateLiveFormPreview()"></textarea>
                <div style="font-size: 11px; color: #0284c7; margin-top: 4px;">
                    *Jika diisi, browser akan membacakan teks ini secara otomatis saat dering bel selesai diputar.
                </div>
            </div>

            <!-- SECTION 2: DAYS RING (HARI OPERASIONAL) -->
            <div style="margin-bottom: 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label style="font-weight: 700; font-size: 12px; color: #1e293b;">📅 Hari Operasional (Days Ring):</label>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" class="btn-chip" onclick="quickToggleDays('workdays')">Senin–Jumat</button>
                        <button type="button" class="btn-chip" onclick="quickToggleDays('all')">Senin–Sabtu</button>
                        <button type="button" class="btn-chip chip-reset" onclick="quickToggleDays('clear')">Reset Hari</button>
                    </div>
                </div>
                <div class="day-pill-grid">
                    <label class="day-pill">
                        <input type="checkbox" name="day_1" id="cb_day_1" value="1" onchange="updateLiveFormPreview()">
                        <span class="day-pill-label">Senin</span>
                    </label>
                    <label class="day-pill">
                        <input type="checkbox" name="day_2" id="cb_day_2" value="1" onchange="updateLiveFormPreview()">
                        <span class="day-pill-label">Selasa</span>
                    </label>
                    <label class="day-pill">
                        <input type="checkbox" name="day_3" id="cb_day_3" value="1" onchange="updateLiveFormPreview()">
                        <span class="day-pill-label">Rabu</span>
                    </label>
                    <label class="day-pill">
                        <input type="checkbox" name="day_4" id="cb_day_4" value="1" onchange="updateLiveFormPreview()">
                        <span class="day-pill-label">Kamis</span>
                    </label>
                    <label class="day-pill">
                        <input type="checkbox" name="day_5" id="cb_day_5" value="1" onchange="updateLiveFormPreview()">
                        <span class="day-pill-label">Jumat</span>
                    </label>
                    <label class="day-pill">
                        <input type="checkbox" name="day_6" id="cb_day_6" value="1" onchange="updateLiveFormPreview()">
                        <span class="day-pill-label">Sabtu</span>
                    </label>
                </div>
            </div>

            <!-- SECTION 3: SPEAKER ZONE & AUDIO TEST -->
            <div style="margin-bottom: 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1;">
                    <label style="font-weight: 700; font-size: 12px; color: #1e293b; display: block; margin-bottom: 8px;">📢 Speaker Zone Output:</label>
                    <div style="display: flex; gap: 12px;">
                        <label class="day-pill spk-pill" style="min-width: 140px;">
                            <input type="checkbox" name="spk_in" id="cb_spk_in" value="1" onchange="updateLiveFormPreview()">
                            <span class="day-pill-label">Indoor (IN)</span>
                        </label>
                        <label class="day-pill spk-pill" style="min-width: 140px;">
                            <input type="checkbox" name="spk_out" id="cb_spk_out" value="1" onchange="updateLiveFormPreview()">
                            <span class="day-pill-label">Outdoor (OUT)</span>
                        </label>
                    </div>
                </div>
                <div>
                    <button type="button" onclick="testModalAudioPreview()" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.25);">
                        <span>🔊</span> Uji Tes Suara Bel
                    </button>
                </div>
            </div>

            <!-- SECTION 4: TIME SETTING (PENGATURAN WAKTU DERING BEL) -->
            <div style="margin-bottom: 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
                    <div>
                        <label style="font-weight: 800; font-size: 13px; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                            ⏱️ Waktu Dering Bel (Setup Jam Pemutaran):
                        </label>
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                            Tentukan jam pemutaran bel ini. Tekan tombol untuk menambah pemutaran di lain waktu.
                        </div>
                    </div>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" class="btn-chip" onclick="addSpecificTimeSlot('')" style="background: #2563eb; color: #fff; border: none; font-weight: 700; padding: 6px 14px; border-radius: 6px; font-size: 11px; cursor: pointer;">
                            ➕ Tambah Pemutaran Di Lain Waktu
                        </button>
                    </div>
                </div>

                <!-- DYNAMIC TIME SLOTS CONTAINER -->
                <div id="specificTimeSlotsContainer" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 4px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px;">
                    <!-- Rendered dynamically via JS -->
                </div>
            </div>

            <!-- SECTION 5: HIGHLIGHT FLAG & LIVE ROW PREVIEW -->
            <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 10px; padding: 12px 16px;">
                <label style="font-size: 12px; font-weight: 700; color: #856404; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="highlight" id="cb_highlight" value="1" style="width: 16px; height: 16px; cursor: pointer;" onchange="updateLiveFormPreview()">
                    <span>🎨 Tandai Highlight Kuning (Baris Prioritas Tinggi)</span>
                </label>
                <div style="font-size: 11px; color: #856404; font-weight: 600;">Standard Visual Marker</div>
            </div>

            <!-- LIVE PREVIEW CONTAINER -->
            <div class="modal-preview-box">
                <div style="font-size: 10px; text-transform: uppercase; color: #94a3b8; font-weight: 700; margin-bottom: 6px;">
                    👁️ Pratinjau Tampilan Baris Tabel (Live Preview):
                </div>
                <div id="modalLivePreview" style="font-size: 12px; font-weight: 700; color: #38bdf8;">
                    #-- | Conten | Building | Days: -- | Speaker: -- | Times: --
                </div>
            </div>

            <!-- SUBMIT ACTION BUTTONS -->
            <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeModal()" style="padding: 10px 20px; border: 1px solid #cbd5e1; background: #fff; color: #475569; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" class="btn-add" style="padding: 10px 28px; font-size: 13px; border-radius: 8px; background: linear-gradient(135deg, #007bff, #0056b3); box-shadow: 0 4px 12px rgba(0,123,255,0.3);">
                    💾 Simpan Jadwal Bel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hari Libur -->
<div id="holidayModal" class="modal">
    <div class="modal-content" style="max-width: 520px;">
        <span class="close" onclick="closeHolidayModal()">&times;</span>
        <h3 style="margin-bottom: 12px; font-size: 16px; color: #1e293b;">🗓️ Pengelolaan Hari Libur (Holiday Exception)</h3>
        <p style="font-size: 12px; color: #64748b; margin-bottom: 16px;">
            Pada tanggal yang didaftarkan sebagai Hari Libur, bel dering otomatis akan diset ke status <b>Silent</b>.
        </p>

        <form id="holidayForm" onsubmit="submitToggleHoliday(event)" style="display: flex; gap: 8px; margin-bottom: 20px;">
            <input type="date" id="hol_date" required style="padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
            <input type="text" id="hol_note" placeholder="Keterangan (mis: Libur Nasional)" style="flex: 1; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
            <button type="submit" class="btn-add" style="padding: 7px 16px; font-size: 12px;">+ Tambah</button>
        </form>

        <div style="max-height: 250px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left; border-bottom: 1px solid #e2e8f0; color: #475569;">
                        <th style="padding: 10px 12px;">Tanggal</th>
                        <th style="padding: 10px 12px;">Keterangan</th>
                        <th style="padding: 10px 12px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="holidayTableBody">
                    <tr><td colspan="3" style="padding: 15px; text-align: center; color: #94a3b8;">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Audit Log -->
<div id="logModal" class="modal">
    <div class="modal-content" style="max-width: 650px;">
        <span class="close" onclick="closeLogModal()">&times;</span>
        <h3 style="margin-bottom: 12px; font-size: 16px; color: #1e293b;">📜 Riwayat Perubahan & Audit Log Schedule Bell</h3>
        <p style="font-size: 12px; color: #64748b; margin-bottom: 16px;">
            Menampilkan 200 aktivitas perubahan jadwal terbaru yang dilakukan oleh pengguna.
        </p>

        <div style="max-height: 350px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left; border-bottom: 1px solid #e2e8f0; color: #475569;">
                        <th style="padding: 8px 10px; width: 130px;">Waktu</th>
                        <th style="padding: 8px 10px; width: 80px;">User</th>
                        <th style="padding: 8px 10px; width: 90px;">Aksi</th>
                        <th style="padding: 8px 10px;">Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody id="logTableBody">
                    <tr><td colspan="4" style="padding: 15px; text-align: center; color: #94a3b8;">Memuat data log...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Periodic Bell Wizard -->
<div id="periodicWizardModal" class="modal">
    <div class="modal-content" style="max-width: 540px; border-radius: 12px; padding: 20px;">
        <span class="close" onclick="closePeriodicWizardModal()">&times;</span>
        <h3 style="margin-bottom: 6px; font-size: 16px; color: #0284c7; display: flex; align-items: center; gap: 8px;">
            ⚡ Generator Jam Periodik Wizard
        </h3>
        <p style="font-size: 12px; color: #64748b; margin-bottom: 18px;">
            Buat jadwal dering berulang otomatis berdasarkan rentang jam dan interval menit (contoh: Bel Pengumuman K3 setiap 1 jam).
        </p>

        <form id="periodicForm" onsubmit="submitPeriodicWizard(event)">
            <div style="margin-bottom: 14px;">
                <label style="font-size: 12px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 4px;">Nama Bel / Konten:</label>
                <input type="text" id="pw_conten" value="Pengumuman Safety & K3" required style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
            </div>
            <div style="margin-bottom: 14px;">
                <label style="font-size: 12px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;">🏢 Building / Area (Checklist Mode):</label>
                <input type="hidden" id="pw_building" value="F1">
                <div class="day-pill-grid" style="flex-wrap: wrap; gap: 4px;">
                    <?php 
                    $pwBldList = ['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'IH', 'B1', 'B2', 'MO'];
                    foreach ($pwBldList as $code):
                    ?>
                        <label class="day-pill spk-pill" style="min-width: 44px; flex: 1;">
                            <input type="checkbox" id="cb_pw_bld_<?= $code ?>" value="<?= $code ?>" onchange="syncPwBuildingChecklistToInput()">
                            <span class="day-pill-label" style="font-size: 10px; padding: 4px 0;"><?= $code ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 14px;">
                <div>
                    <label style="font-size: 11px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 4px;">Jam Mulai:</label>
                    <select id="pw_start_hour" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
                        <option value="6">06:00</option>
                        <option value="7">07:00</option>
                        <option value="8" selected>08:00</option>
                        <option value="9">09:00</option>
                        <option value="10">10:00</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 11px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 4px;">Jam Selesai:</label>
                    <select id="pw_end_hour" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
                        <option value="11">11:00</option>
                        <option value="12" selected>12:00</option>
                        <option value="16">16:00</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 11px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 4px;">Interval:</label>
                    <select id="pw_interval" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
                        <option value="30">30 Menit</option>
                        <option value="60" selected>1 Jam (60 Mnt)</option>
                        <option value="120">2 Jam (120 Mnt)</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 18px;">
                <label style="font-size: 11px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 4px;">Teks Suara Pengumuman (Optional TTS):</label>
                <input type="text" id="pw_announce" placeholder="Contoh: Utamakan keselamatan kerja dan gunakan APD." style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
            </div>
            <button type="submit" class="btn-add" style="width: 100%; padding: 10px; background: linear-gradient(135deg, #0284c7, #0369a1);">
                ⚡ Generate & Simpan Jadwal Periodik
            </button>
        </form>
    </div>
</div>

<!-- Modal Kelola Library Audio -->
<div id="audioLibraryModal" class="modal">
    <div class="modal-content" style="max-width: 680px; border-radius: 12px; padding: 20px;">
        <span class="close" onclick="closeAudioLibraryModal()">&times;</span>
        <h3 style="margin-bottom: 6px; font-size: 16px; color: #86198f; display: flex; align-items: center; gap: 8px;">
            🎵 Pengelolaan Library File Audio Bel (MP3 / WAV)
        </h3>
        <p style="font-size: 12px; color: #64748b; margin-bottom: 16px;">
            Unggah file nada bel kustom (.mp3, .wav, .ogg) untuk dipasang pada jadwal dering bel.
        </p>

        <form id="audioUploadForm" onsubmit="submitAudioUploadModal(event)" style="background: #fdf4ff; border: 1px solid #f0abfc; padding: 14px; border-radius: 10px; margin-bottom: 18px; display: flex; gap: 10px; align-items: center;">
            <input type="file" id="modal_audio_file" accept="audio/*" required style="flex: 1; font-size: 12px; padding: 6px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px;">
            <button type="submit" class="btn-add" style="background: linear-gradient(135deg, #a855f7, #9333ea); font-size: 12px; padding: 8px 18px; white-space: nowrap;">
                📤 Unggah Audio Baru
            </button>
        </form>

        <div style="max-height: 320px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left; border-bottom: 1px solid #e2e8f0; color: #475569;">
                        <th style="padding: 10px 12px;">Nama File Audio</th>
                        <th style="padding: 10px 12px; width: 80px;">Ukuran</th>
                        <th style="padding: 10px 12px; width: 130px;">Tanggal</th>
                        <th style="padding: 10px 12px; text-align: center; width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="audioFilesTableBody">
                    <tr><td colspan="4" style="padding: 15px; text-align: center; color: #94a3b8;">Memuat data audio...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let csrfTokenName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';
    const scheduleData = <?= json_encode($schedules) ?>;

    let autoBellEnabled = false;
    let holidaysList = [];
    let lastRingedSecondKey = '';

    // Fetch initial holidays
    fetchHolidaysList();

    function toggleAutoBellMode(cb) {
        autoBellEnabled = cb.checked;
        if (autoBellEnabled) {
            if ("Notification" in window && Notification.permission !== "granted") {
                Notification.requestPermission().then(perm => {
                    if (perm === "granted") {
                        showToast('🔔 Notifikasi Desktop diizinkan');
                    }
                });
            }
            showToast('🔊 Live Auto-Bell DIAKTIFKAN');
        } else {
            showToast('🔇 Live Auto-Bell DINONAKTIFKAN');
        }
    }

    function syncBuildingChecklistToInput() {
        const bldCodes = ['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'IH', 'B1', 'B2', 'MO'];
        const selected = [];
        bldCodes.forEach(code => {
            const cb = document.getElementById('cb_bld_' + code);
            if (cb && cb.checked) selected.push(code);
        });

        let result = '';
        if (selected.length === 0) {
            result = '';
        } else if (selected.length === bldCodes.length) {
            result = 'ALL Building';
        } else {
            const fCodes = selected.filter(c => c.startsWith('F')).map(c => c.replace('F', ''));
            const otherCodes = selected.filter(c => !c.startsWith('F'));
            
            if (fCodes.length > 1) {
                let fStr = 'F-' + fCodes.join(',');
                if (otherCodes.length > 0) {
                    fStr += ',' + otherCodes.join(',');
                }
                result = fStr;
            } else {
                result = selected.join(', ');
            }
        }

        const el = document.getElementById('inp_building');
        if (el) el.value = result;
        updateLiveFormPreview();
    }

    function setBuildingChecklistFromValue(val) {
        val = (val !== undefined && val !== null) ? val : '';
        const bldCodes = ['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'IH', 'B1', 'B2', 'MO'];
        
        bldCodes.forEach(code => {
            const cb = document.getElementById('cb_bld_' + code);
            if (cb) cb.checked = false;
        });

        if (!val) {
            syncBuildingChecklistToInput();
            return;
        }

        const upperVal = val.toUpperCase();
        if (upperVal === 'ALL BUILDING' || upperVal === 'ALL') {
            bldCodes.forEach(code => {
                const cb = document.getElementById('cb_bld_' + code);
                if (cb) cb.checked = true;
            });
        } else {
            bldCodes.forEach(code => {
                const cb = document.getElementById('cb_bld_' + code);
                if (!cb) return;

                if (code.startsWith('F')) {
                    const num = code.replace('F', '');
                    if (val.includes(code) || (val.includes('F-') && (val.includes(num) || val.includes(code)))) {
                        cb.checked = true;
                    }
                } else {
                    if (val.includes(code)) {
                        cb.checked = true;
                    }
                }
            });
        }

        syncBuildingChecklistToInput();
    }

    function quickToggleBuildings(type) {
        const bldCodes = ['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'IH', 'B1', 'B2', 'MO'];
        bldCodes.forEach(code => {
            const cb = document.getElementById('cb_bld_' + code);
            if (!cb) return;
            if (type === 'factory') {
                cb.checked = code.startsWith('F');
            } else if (type === 'all') {
                cb.checked = true;
            } else if (type === 'clear') {
                cb.checked = false;
            }
        });
        syncBuildingChecklistToInput();
    }

    function syncPwBuildingChecklistToInput() {
        const bldCodes = ['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'IH', 'B1', 'B2', 'MO'];
        const selected = [];
        bldCodes.forEach(code => {
            const cb = document.getElementById('cb_pw_bld_' + code);
            if (cb && cb.checked) selected.push(code);
        });

        let result = '';
        if (selected.length === 0) {
            result = 'F1';
        } else if (selected.length === bldCodes.length) {
            result = 'ALL Building';
        } else {
            const fCodes = selected.filter(c => c.startsWith('F')).map(c => c.replace('F', ''));
            const otherCodes = selected.filter(c => !c.startsWith('F'));
            if (fCodes.length > 1) {
                let fStr = 'F-' + fCodes.join(',');
                if (otherCodes.length > 0) fStr += ',' + otherCodes.join(',');
                result = fStr;
            } else {
                result = selected.join(', ');
            }
        }
        const el = document.getElementById('pw_building');
        if (el) el.value = result;
    }

    const modal = document.getElementById("scheduleModal");
    const form = document.getElementById("scheduleForm");
    
    function openModal(mode, data = null) {
        form.reset();
        document.getElementById("inp_template_preset").value = "";

        if (mode === 'add') {
            specificTimeSlots = [''];
            document.getElementById("modalTitle").innerText = "Tambah Jadwal Bel Baru";
            form.action = "<?= site_url('schedule-bell/store') ?>";
            
            // Auto-calculate Next No
            let maxNo = 0;
            if (scheduleData && scheduleData.length > 0) {
                scheduleData.forEach(s => {
                    const n = parseInt(s.no) || 0;
                    if (n > maxNo) maxNo = n;
                });
            }
            document.getElementById("inp_no").value = maxNo + 1;
            document.getElementById("inp_conten").value = "";
            setBuildingChecklistFromValue("");
            document.getElementById("inp_announcement_text").value = "";
            document.getElementById("inp_audio_file").value = "";
            document.getElementById("inp_audio_file_upload").value = "";
            fetchAudioFilesList("");

            // Default: days & speaker unselected until selected by user
            for(let i=1; i<=6; i++) {
                const cb = document.getElementById("cb_day_"+i);
                if (cb) cb.checked = false;
            }
            
            if (document.getElementById("cb_spk_in")) document.getElementById("cb_spk_in").checked = false;
            if (document.getElementById("cb_spk_out")) document.getElementById("cb_spk_out").checked = false;
            if (document.getElementById("cb_highlight")) document.getElementById("cb_highlight").checked = false;
        } else if (mode === 'edit') {
            document.getElementById("modalTitle").innerText = "Edit Jadwal Bel";

            // If data is passed as ID string or integer, resolve the data object from scheduleData
            if (typeof data !== 'object' && data !== null && typeof scheduleData !== 'undefined') {
                const found = scheduleData.find(s => s.id == data);
                if (found) data = found;
            }

            if (!data || typeof data !== 'object') return;

            form.action = "<?= site_url('schedule-bell/update/') ?>" + data.id;
            
            document.getElementById("inp_no").value = data.no || '';
            document.getElementById("inp_conten").value = (data.conten && data.conten !== 'undefined') ? data.conten : '';
            setBuildingChecklistFromValue(data.building || '');
            document.getElementById("inp_announcement_text").value = data.announcement_text || "";
            document.getElementById("inp_audio_file").value = data.audio_file || "";
            document.getElementById("inp_audio_file_upload").value = "";
            fetchAudioFilesList(data.audio_file || "");
            
            for(let i=1; i<=6; i++) {
                const cb = document.getElementById("cb_day_"+i);
                if (cb) cb.checked = (data['day_'+i] == 1);
            }
            
            if (document.getElementById("cb_spk_in")) document.getElementById("cb_spk_in").checked = (data.spk_in == 1);
            if (document.getElementById("cb_spk_out")) document.getElementById("cb_spk_out").checked = (data.spk_out == 1);
            if (document.getElementById("cb_highlight")) document.getElementById("cb_highlight").checked = (data.highlight == 1);

            const activeTimes = [];
            Object.keys(data).forEach(k => {
                if (k.indexOf('t_') === 0 && data[k] == 1) {
                    const timeStr = parseKeyToTimeString(k);
                    if (timeStr) {
                        const [h, m] = timeStr.split(':').map(Number);
                        activeTimes.push({ timeStr: timeStr, secs: h * 3600 + m * 60 });
                    }
                }
            });

            if (activeTimes.length > 0) {
                activeTimes.sort((a, b) => a.secs - b.secs);
                specificTimeSlots = activeTimes.map(t => t.timeStr);
            } else {
                specificTimeSlots = [''];
            }
        }

        renderSpecificTimeSlotsUI();
        syncMultiSpecificTimes();
        modal.style.display = "block";
    }

    function applyFormTemplate(key) {
        if (!key) return;

        // Reset times first
        quickToggleTimes('clear');

        if (key === 'masuk_pagi') {
            document.getElementById('inp_conten').value = 'Bel Masuk Kerja Shift 1';
            quickToggleDays('workdays');
            document.getElementById('cb_spk_in').checked = true;
            document.getElementById('cb_spk_out').checked = true;
            document.getElementById('cb_t_700').checked = true;
        } else if (key === 'apel_briefing') {
            document.getElementById('inp_conten').value = 'Apel Pagi / Safety Briefing';
            quickToggleDays('workdays');
            document.getElementById('cb_spk_in').checked = false;
            document.getElementById('cb_spk_out').checked = true;
            document.getElementById('cb_t_705').checked = true;
            document.getElementById('cb_highlight').checked = true;
        } else if (key === 'istirahat_siang') {
            document.getElementById('inp_conten').value = 'Istirahat Siang / Sholat';
            quickToggleDays('workdays');
            document.getElementById('cb_spk_in').checked = true;
            document.getElementById('cb_spk_out').checked = true;
            document.getElementById('cb_t_1200').checked = true;
        } else if (key === 'selesai_istirahat') {
            document.getElementById('inp_conten').value = 'Selesai Istirahat Siang';
            quickToggleDays('workdays');
            document.getElementById('cb_spk_in').checked = true;
            document.getElementById('cb_spk_out').checked = true;
            document.getElementById('cb_t_1230').checked = true;
        } else if (key === 'pulang_kerja') {
            document.getElementById('inp_conten').value = 'Bel Pulang Kerja Shift 1';
            quickToggleDays('workdays');
            document.getElementById('cb_spk_in').checked = true;
            document.getElementById('cb_spk_out').checked = true;
            document.getElementById('cb_t_1230').checked = true;
        }

        showToast('⚡ Templat ' + key + ' diterapkan!');
        updateLiveFormPreview();
    }

    function quickToggleDays(type) {
        for(let i=1; i<=6; i++) {
            const el = document.getElementById("cb_day_" + i);
            if (!el) continue;
            if (type === 'workdays') el.checked = (i <= 5);
            else if (type === 'all') el.checked = true;
            else if (type === 'clear') el.checked = false;
        }
        updateLiveFormPreview();
    }

    function quickToggleTimes(type) {
        const timeInputs = document.querySelectorAll('#scheduleModal .time-pill input[type="checkbox"]');
        timeInputs.forEach(input => {
            if (type === 'morning') {
                if (input.getAttribute('data-group') === 'morning') input.checked = true;
            } else if (type === 'midday') {
                if (input.getAttribute('data-group') === 'midday') input.checked = true;
            } else if (type === 'afternoon') {
                if (input.getAttribute('data-group') === 'afternoon') input.checked = true;
            } else if (type === 'clear') {
                input.checked = false;
            }
        });
        updateLiveFormPreview();
    }

    function testModalAudioPreview() {
        const contenName = document.getElementById('inp_conten').value || 'Tes Suara Bel';
        playAudioBellPreview(contenName);
    }

    function updateLiveFormPreview() {
        const previewEl = document.getElementById('modalLivePreview');
        if (!previewEl) return;

        const no = document.getElementById('inp_no').value || '--';
        const conten = document.getElementById('inp_conten').value || 'Nama Bel Kosong';
        const building = document.getElementById('inp_building').value || 'Tanpa Area';

        const dayNames = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const activeDays = [];
        for (let i = 1; i <= 6; i++) {
            if (document.getElementById('cb_day_' + i) && document.getElementById('cb_day_' + i).checked) {
                activeDays.push(dayNames[i - 1]);
            }
        }

        const spkIn = document.getElementById('cb_spk_in') && document.getElementById('cb_spk_in').checked;
        const spkOut = document.getElementById('cb_spk_out') && document.getElementById('cb_spk_out').checked;
        let spkText = 'Tidak Aktif';
        if (spkIn && spkOut) spkText = 'IN & OUT';
        else if (spkIn) spkText = 'Indoor (IN)';
        else if (spkOut) spkText = 'Outdoor (OUT)';

        const activeTimes = [];
        const timeInputs = document.querySelectorAll('#scheduleModal .time-pill input[type="checkbox"]');
        timeInputs.forEach(cb => {
            if (cb.checked) {
                const label = cb.nextElementSibling ? cb.nextElementSibling.innerText : '';
                if (label) activeTimes.push(label);
            }
        });

        const isHighlight = document.getElementById('cb_highlight') && document.getElementById('cb_highlight').checked;

        const highlightBadge = isHighlight ? ' <span style="background:#eab308; color:#fff; padding:1px 5px; border-radius:3px; font-size:10px;">HIGHLIGHT</span>' : '';
        const daysStr = activeDays.length > 0 ? activeDays.join(', ') : 'Belum Dipilih';
        const timesStr = activeTimes.length > 0 ? activeTimes.join(', ') : 'Belum Ada Dering';

        previewEl.innerHTML = `
            <span style="color:#94a3b8;">#${no}</span> &bull; 
            <span style="color:#38bdf8;">${conten}</span>${highlightBadge} &bull; 
            <span style="color:#cbd5e1;">${building}</span> | 
            <span style="color:#a7f3d0;">Hari: ${daysStr}</span> | 
            <span style="color:#fde047;">Speaker: ${spkText}</span> | 
            <span style="color:#67e8f9;">Dering: ${timesStr}</span>
        `;
    }

    function closeModal() {
        modal.style.display = "none";
    }

    const importModal = document.getElementById("importModal");
    function openImportModal() {
        importModal.style.display = "block";
    }
    function closeImportModal() {
        importModal.style.display = "none";
    }

    // --- HOLIDAY MODAL LOGIC ---
    function openHolidayModal() {
        fetchHolidaysList();
        document.getElementById("holidayModal").style.display = "block";
    }
    function closeHolidayModal() {
        document.getElementById("holidayModal").style.display = "none";
    }

    function fetchHolidaysList() {
        fetch('<?= site_url('schedule-bell/get-holidays') ?>')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    holidaysList = data.holidays || [];
                    renderHolidaysTable();
                }
            });
    }

    function renderHolidaysTable() {
        const tbody = document.getElementById('holidayTableBody');
        if (!tbody) return;

        if (holidaysList.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" style="padding: 15px; text-align: center; color: #94a3b8;">Belum ada hari libur terdaftar.</td></tr>';
            return;
        }

        let html = '';
        holidaysList.forEach(h => {
            html += `
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 12px; font-weight: bold; color: #1e293b;">${h.date}</td>
                    <td style="padding: 8px 12px; color: #475569;">${h.note}</td>
                    <td style="padding: 8px 12px; text-align: center;">
                        <button type="button" style="background:#dc3545; color:#fff; border:none; border-radius:4px; padding:3px 8px; font-size:11px; cursor:pointer;" onclick="deleteHoliday('${h.date}')">🗑️ Hapus</button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function submitToggleHoliday(e) {
        e.preventDefault();
        const date = document.getElementById('hol_date').value;
        const note = document.getElementById('hol_note').value || 'Hari Libur';

        const formData = new FormData();
        formData.append('date', date);
        formData.append('note', note);
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/toggle-holiday') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (res.csrf_hash) csrfHash = res.csrf_hash;
                holidaysList = res.holidays;
                renderHolidaysTable();
                showToast('⚡ ' + res.message);
                document.getElementById('hol_date').value = '';
                document.getElementById('hol_note').value = '';
            }
        });
    }

    function deleteHoliday(date) {
        const formData = new FormData();
        formData.append('date', date);
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/toggle-holiday') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (res.csrf_hash) csrfHash = res.csrf_hash;
                holidaysList = res.holidays;
                renderHolidaysTable();
                showToast('⚡ ' + res.message);
            }
        });
    }

    // --- AUDIT LOG MODAL LOGIC ---
    function openLogModal() {
        document.getElementById("logModal").style.display = "block";
        fetchLogsList();
    }
    function closeLogModal() {
        document.getElementById("logModal").style.display = "none";
    }

    function fetchLogsList() {
        const tbody = document.getElementById('logTableBody');
        tbody.innerHTML = '<tr><td colspan="4" style="padding: 15px; text-align: center; color: #94a3b8;">Memuat data log...</td></tr>';

        fetch('<?= site_url('schedule-bell/logs') ?>')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const logs = data.logs || [];
                    if (logs.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" style="padding: 15px; text-align: center; color: #94a3b8;">Belum ada riwayat aktivitas.</td></tr>';
                        return;
                    }

                    let html = '';
                    logs.forEach(l => {
                        let actionBadge = `<span style="background:#e2e8f0; color:#334155; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:10px;">${l.action}</span>`;
                        if (l.action === 'CREATE') actionBadge = `<span style="background:#dcfce7; color:#166534; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:10px;">CREATE</span>`;
                        else if (l.action === 'UPDATE' || l.action === 'INLINE_EDIT') actionBadge = `<span style="background:#e0f2fe; color:#0369a1; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:10px;">${l.action}</span>`;
                        else if (l.action === 'DELETE') actionBadge = `<span style="background:#fee2e2; color:#991b1b; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:10px;">DELETE</span>`;
                        else if (l.action === 'PRESET') actionBadge = `<span style="background:#fef3c7; color:#92400e; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:10px;">PRESET</span>`;
                        else if (l.action === 'HOLIDAY') actionBadge = `<span style="background:#f3e8ff; color:#6b21a8; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:10px;">HOLIDAY</span>`;

                        html += `
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 6px 10px; color: #64748b; font-family: monospace;">${l.timestamp}</td>
                                <td style="padding: 6px 10px; font-weight: bold; color: #1e293b;">${l.user}</td>
                                <td style="padding: 6px 10px;">${actionBadge}</td>
                                <td style="padding: 6px 10px; color: #334155;">${l.details}</td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                }
            });
    }

    // --- PERIODIC WIZARD MODAL LOGIC ---
    function openPeriodicWizardModal() {
        document.getElementById('periodicWizardModal').style.display = 'block';
    }
    function closePeriodicWizardModal() {
        document.getElementById('periodicWizardModal').style.display = 'none';
    }
    function submitPeriodicWizard(e) {
        e.preventDefault();
        const conten = document.getElementById('pw_conten').value;
        const building = document.getElementById('pw_building').value;
        const startHour = document.getElementById('pw_start_hour').value;
        const endHour = document.getElementById('pw_end_hour').value;
        const interval = document.getElementById('pw_interval').value;
        const announce = document.getElementById('pw_announce').value;

        const formData = new FormData();
        formData.append('conten', conten);
        formData.append('building', building);
        formData.append('start_hour', startHour);
        formData.append('end_hour', endHour);
        formData.append('interval_min', interval);
        formData.append('announcement_text', announce);
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/generate-periodic') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (res.csrf_hash) csrfHash = res.csrf_hash;
                showToast('⚡ ' + res.message);
                closePeriodicWizardModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('❌ ' + res.message, true);
            }
        });
    }

    // --- SHIFT PROFILE SWITCHER LOGIC ---
    function switchShiftProfile(profileName) {
        if (!confirm('Apakah Anda yakin ingin mengalihkan profil shift aktif ke "' + profileName + '"?')) return;

        const formData = new FormData();
        formData.append('profile_name', profileName);
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/switch-profile') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (res.csrf_hash) csrfHash = res.csrf_hash;
                showToast('🌙 ' + res.message);
                const labelEl = document.getElementById('activeProfileLabel');
                if (labelEl) labelEl.innerText = profileName;
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('❌ ' + res.message, true);
            }
        });
    }

    function saveCurrentProfile() {
        const profileName = prompt('Masukkan nama profil shift baru (misal: Shift Malam, Overtime Weekend):');
        if (!profileName || !profileName.trim()) return;

        const formData = new FormData();
        formData.append('profile_name', profileName.trim());
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/save-profile') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (res.csrf_hash) csrfHash = res.csrf_hash;
                showToast('💾 ' + res.message);
                
                const sel = document.getElementById('shiftProfileSelect');
                if (sel) {
                    const opt = document.createElement('option');
                    opt.value = profileName;
                    opt.innerText = '🌙 ' + profileName;
                    sel.appendChild(opt);
                    sel.value = profileName;
                }
            } else {
                showToast('❌ ' + res.message, true);
            }
        });
    }

    window.onclick = function(event) {
        if (event.target == importModal) closeImportModal();
        if (event.target == modal) closeModal();
        if (event.target == document.getElementById("holidayModal")) closeHolidayModal();
        if (event.target == document.getElementById("logModal")) closeLogModal();
        if (event.target == document.getElementById("periodicWizardModal")) closePeriodicWizardModal();
        if (event.target == document.getElementById("audioLibraryModal")) closeAudioLibraryModal();
    }

    // --- 1. AUDIO & TTS SPEECH PREVIEW SIMULATOR ---
    let currentActiveAudio = null;
    let availableAudioFiles = [];

    function fetchAudioFilesList(selectedPath = '') {
        fetch('<?= site_url('schedule-bell/get-audio-files') ?>')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    availableAudioFiles = data.files || [];
                    populateAudioSelectDropdown(selectedPath);
                    renderAudioFilesTable();
                }
            });
    }

    function populateAudioSelectDropdown(selectedPath = '') {
        const selectEl = document.getElementById('inp_audio_select');
        if (!selectEl) return;

        selectEl.innerHTML = '<option value="">🔔 Default Synthesizer Chime (Web Audio API)</option>';
        availableAudioFiles.forEach(f => {
            const opt = document.createElement('option');
            opt.value = f.path;
            opt.textContent = '🎵 ' + f.name + ' (' + f.size + ')';
            if (selectedPath && (selectedPath === f.path || selectedPath.endsWith(f.name))) {
                opt.selected = true;
            }
            selectEl.appendChild(opt);
        });

        if (selectedPath && selectEl.value !== selectedPath) {
            const opt = document.createElement('option');
            opt.value = selectedPath;
            opt.textContent = '🎵 ' + selectedPath.split('/').pop();
            opt.selected = true;
            selectEl.appendChild(opt);
        }
    }

    function handleAudioSelectChange(val) {
        document.getElementById('inp_audio_file').value = val;
    }

    function previewUploadedAudioFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const objectUrl = URL.createObjectURL(file);
            showToast('🎵 Pratinjau File: <b>' + file.name + '</b>');
            
            if (currentActiveAudio) {
                currentActiveAudio.pause();
            }
            const audio = new Audio(objectUrl);
            currentActiveAudio = audio;
            audio.play();
        }
    }

    function testModalSelectedAudioPreview() {
        const selectVal = document.getElementById('inp_audio_select').value;
        const uploadEl = document.getElementById('inp_audio_file_upload');
        const contenName = document.getElementById('inp_conten').value || 'Tes Audio Bel';
        const announceText = document.getElementById('inp_announcement_text').value || '';

        if (uploadEl.files && uploadEl.files[0]) {
            previewUploadedAudioFile(uploadEl);
        } else if (selectVal) {
            playAudioBellPreview(contenName, announceText, selectVal);
        } else {
            playSynthChimeFallback(contenName, announceText);
        }
    }

    function openAudioLibraryModal() {
        fetchAudioFilesList(document.getElementById('inp_audio_file').value);
        document.getElementById('audioLibraryModal').style.display = 'block';
    }

    function closeAudioLibraryModal() {
        document.getElementById('audioLibraryModal').style.display = 'none';
    }

    function renderAudioFilesTable() {
        const tbody = document.getElementById('audioFilesTableBody');
        if (!tbody) return;

        if (availableAudioFiles.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="padding: 15px; text-align: center; color: #94a3b8;">Belum ada file audio yang diunggah. Unggah file MP3/WAV pertama Anda di atas.</td></tr>';
            return;
        }

        let html = '';
        availableAudioFiles.forEach(f => {
            const safeName = f.name.replace(/'/g, "\\'");
            const safePath = f.path.replace(/'/g, "\\'");
            html += `
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px 12px; font-weight: 700; color: #1e293b;">🎵 ${f.name}</td>
                    <td style="padding: 10px 12px; color: #64748b;">${f.size}</td>
                    <td style="padding: 10px 12px; color: #64748b;">${f.mtime}</td>
                    <td style="padding: 10px 12px; text-align: center;">
                        <button type="button" class="action-btn btn-audio" onclick="playAudioBellPreview('${safeName}', '', '${safePath}')" title="Putar Audio">▶️</button>
                        <button type="button" class="action-btn btn-edit" onclick="selectAudioFileFromLibrary('${safePath}')" title="Gunakan Untuk Form">✅ Pilih</button>
                        <button type="button" class="action-btn btn-del" onclick="deleteAudioFileModal('${safeName}')" title="Hapus File">🗑️</button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function selectAudioFileFromLibrary(path) {
        document.getElementById('inp_audio_file').value = path;
        populateAudioSelectDropdown(path);
        closeAudioLibraryModal();
        showToast('✅ File Audio dipilih!');
    }

    function submitAudioUploadModal(e) {
        e.preventDefault();
        const fileInput = document.getElementById('modal_audio_file');
        if (!fileInput.files || !fileInput.files[0]) return;

        const formData = new FormData();
        formData.append('audio_file', fileInput.files[0]);
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/upload-audio') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.csrf_hash) csrfHash = data.csrf_hash;
                fileInput.value = '';
                showToast('✅ ' + data.message);
                fetchAudioFilesList(data.file_path);
            } else {
                showToast('❌ ' + data.message, true);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('❌ Gagal mengunggah file', true);
        });
    }

    function deleteAudioFileModal(filename) {
        if (!confirm("Apakah Anda yakin ingin menghapus file audio '" + filename + "'?")) return;

        const formData = new FormData();
        formData.append('filename', filename);
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/delete-audio') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.csrf_hash) csrfHash = data.csrf_hash;
                showToast('🗑️ ' + data.message);
                fetchAudioFilesList();
            } else {
                showToast('❌ ' + data.message, true);
            }
        });
    }

    fetchAudioFilesList();

    function playAudioBellPreview(contenName, announcementText = '', audioFilePath = '') {
        if (currentActiveAudio) {
            currentActiveAudio.pause();
            currentActiveAudio = null;
        }

        if (audioFilePath && audioFilePath.trim() !== '') {
            const baseUrl = '<?= base_url() ?>';
            const cleanPath = audioFilePath.replace(/^\/+/, '');
            const audioUrl = baseUrl + (baseUrl.endsWith('/') ? '' : '/') + cleanPath;
            
            showToast('🎵 Memutar Audio File: <b>' + cleanPath.split('/').pop() + '</b>');
            
            const audio = new Audio(audioUrl);
            currentActiveAudio = audio;

            audio.play().then(() => {
                audio.onended = () => {
                    if (announcementText && announcementText.trim() !== '') {
                        playSpeechAnnouncement(announcementText);
                    }
                };
            }).catch(err => {
                console.warn("Audio file playback failed, fallback to synth chime:", err);
                playSynthChimeFallback(contenName, announcementText);
            });
        } else {
            playSynthChimeFallback(contenName, announcementText);
        }
    }

    function playSynthChimeFallback(contenName, announcementText = '') {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            const ctx = new AudioContext();

            const notes = [523.25, 659.25, 783.99, 1046.50];
            const now = ctx.currentTime;

            notes.forEach((freq, idx) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, now + idx * 0.22);

                gain.gain.setValueAtTime(0.35, now + idx * 0.22);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + idx * 0.22 + 1.2);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + idx * 0.22);
                osc.stop(now + idx * 0.22 + 1.3);
            });

            showToast('🔊 Memutar bel: <b>' + contenName + '</b>');

            if (announcementText && announcementText.trim() !== '') {
                setTimeout(() => {
                    playSpeechAnnouncement(announcementText);
                }, 1300);
            }
        } catch(e) {
            console.error(e);
        }
    }

    function playSpeechAnnouncement(text) {
        if (!text || text.trim() === '') {
            showToast('ℹ️ Teks pengumuman suara belum diisi', true);
            return;
        }

        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.95;
            utterance.pitch = 1.0;
            window.speechSynthesis.speak(utterance);
            showToast('🗣️ Pengumuman Suara: <b>' + text + '</b>');
        } else {
            showToast('❌ Browser tidak mendukung Speech Synthesis', true);
        }
    }

    function testModalSpeechPreview() {
        const announceText = document.getElementById('inp_announcement_text').value || document.getElementById('inp_conten').value || 'Tes Pengumuman Suara Bel';
        playSpeechAnnouncement(announceText);
    }

    // --- 2. NEXT BELL LIVE COUNTDOWN BANNER & AUTO-BELL TRIGGER ---
    const timeMap = {
        <?php foreach ($timeColumns as $idx => $tc): ?>
            '<?= $tc['key'] ?>': '<?= $tc['label'] ?>'<?= ($idx < count($timeColumns) - 1) ? ',' : '' ?>
        <?php endforeach; ?>
    };

    function parseKeyToTimeString(key) {
        if (timeMap[key]) return timeMap[key] + ':00';
        let clean = key.replace('t_', '');
        if (clean.length === 3) clean = '0' + clean;
        if (clean.length === 4) {
            return clean.substring(0, 2) + ':' + clean.substring(2, 4) + ':00';
        }
        return null;
    }

    function updateLiveCountdown() {
        const now = new Date();
        const liveClockEl = document.getElementById('liveClock');
        if (liveClockEl) {
            liveClockEl.innerText = now.toTimeString().split(' ')[0];
        }

        // Format date YYYY-MM-DD
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const dayDate = String(now.getDate()).padStart(2, '0');
        const todayStr = `${year}-${month}-${dayDate}`;

        const isHolidayToday = holidaysList.find(h => h.date === todayStr);

        const titleEl = document.getElementById('nextBellTitle');
        const timerEl = document.getElementById('nextBellTimer');

        if (isHolidayToday) {
            titleEl.innerHTML = `<span style="color:#ef4444; font-weight:800;">🎉 HARI LIBUR (${isHolidayToday.note})</span> &bull; Bel Otomatis Silent`;
            timerEl.innerText = 'OFF (Libur)';
            return;
        }

        const jsDay = now.getDay();
        const dayField = jsDay === 0 ? '' : 'day_' + jsDay;
        const currentSecs = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();

        let upcoming = null;
        let minDiff = 86400;
        let activeCountToday = 0;
        let inCount = 0;
        let outCount = 0;

        scheduleData.forEach(row => {
            if (row.spk_in == 1) inCount++;
            if (row.spk_out == 1) outCount++;

            const tr = document.getElementById('row-' + row.id);
            let totalTimesToday = 0;
            let passedTimesToday = 0;

            if (dayField && row[dayField] == 1) {
                Object.keys(timeMap).forEach(key => {
                    if (row[key] == 1) {
                        activeCountToday++;
                        totalTimesToday++;
                        const [h, m] = timeMap[key].split(':').map(Number);
                        const bellSecs = h * 3600 + m * 60;
                        const diff = bellSecs - currentSecs;

                        // Visual indicator for passed bell time cell today
                        if (tr) {
                            const cell = tr.querySelector(`td[data-field="${key}"]`);
                            if (cell) {
                                if (currentSecs >= bellSecs + 59) {
                                    cell.classList.add('cell-passed');
                                    passedTimesToday++;
                                } else {
                                    cell.classList.remove('cell-passed');
                                }
                            }
                        }

                        // Trigger Auto-Bell when exact second arrives (diff === 0)
                        if (diff === 0 && autoBellEnabled) {
                            const ringKey = `${todayStr}_${row.id}_${key}`;
                            if (lastRingedSecondKey !== ringKey) {
                                lastRingedSecondKey = ringKey;
                                playAudioBellPreview(row.conten, row.announcement_text || '', row.audio_file || '');

                                if ("Notification" in window && Notification.permission === "granted") {
                                    new Notification("🔔 BEL DERING SEKARANG!", {
                                        body: `${row.conten} (${timeMap[key]})\nSpeaker: ${(row.spk_in == 1 && row.spk_out == 1) ? 'IN & OUT' : (row.spk_in == 1 ? 'IN' : 'OUT')}`
                                    });
                                }
                            }
                        }

                        if (diff > 0 && diff < minDiff) {
                            minDiff = diff;
                            upcoming = {
                                conten: row.conten,
                                timeStr: timeMap[key],
                                spk: (row.spk_in == 1 && row.spk_out == 1) ? 'Indoor & Outdoor' : (row.spk_in == 1 ? 'Indoor (IN)' : (row.spk_out == 1 ? 'Outdoor (OUT)' : 'None'))
                            };
                        }
                    }
                });

                // Row passed highlight when all active times for today have completed
                if (tr) {
                    if (totalTimesToday > 0 && passedTimesToday === totalTimesToday) {
                        tr.classList.add('row-passed');
                    } else {
                        tr.classList.remove('row-passed');
                    }
                }
            } else if (tr) {
                tr.classList.remove('row-passed');
                tr.querySelectorAll('.cell-passed').forEach(c => c.classList.remove('cell-passed'));
            }
        });

        const activeTodayEl = document.getElementById('activeTodayCount');
        if (activeTodayEl) activeTodayEl.innerText = `${activeCountToday} Dering`;

        const spkCoverageEl = document.getElementById('spkCoverage');
        if (spkCoverageEl) {
            spkCoverageEl.innerText = `IN: ${inCount} | OUT: ${outCount}`;
        }

        if (upcoming && minDiff < 86400) {
            titleEl.innerHTML = `<strong>${upcoming.conten}</strong> (${upcoming.timeStr}) &bull; <span style="font-size:12px; color:#94a3b8;">Speaker: ${upcoming.spk}</span>`;
            
            const hours = Math.floor(minDiff / 3600);
            const mins = Math.floor((minDiff % 3600) / 60);
            const secs = minDiff % 60;
            const pad = (n) => n.toString().padStart(2, '0');

            timerEl.innerText = `${pad(hours)}:${pad(mins)}:${pad(secs)}`;
        } else {
            titleEl.innerText = 'Tidak ada bel berikutnya hari ini';
            timerEl.innerText = '--:--:--';
        }
    }

    function addCustomTimeRing() {
        const timeVal = document.getElementById('inp_custom_time').value;
        if (!timeVal) {
            showToast('⚠️ Masukkan jam:menit:detik yang valid', true);
            return;
        }

        const parts = timeVal.split(':');
        const h = parseInt(parts[0], 10);
        const m = parseInt(parts[1], 10);

        let targetKey = null;
        let minDiff = 999999;
        const targetSecs = h * 3600 + m * 60;

        Object.keys(timeMap).forEach(key => {
            const [th, tm] = timeMap[key].split(':').map(Number);
            const secs = th * 3600 + tm * 60;
            const diff = Math.abs(secs - targetSecs);
            if (diff < minDiff) {
                minDiff = diff;
                targetKey = key;
            }
        });

        if (targetKey) {
            const cb = document.getElementById('cb_' + targetKey);
            if (cb) {
                cb.checked = true;
                const pillLabel = cb.nextElementSibling;
                if (pillLabel) {
                    pillLabel.style.transform = 'scale(1.1)';
                    pillLabel.style.boxShadow = '0 0 10px #0284c7';
                    setTimeout(() => { 
                        pillLabel.style.transform = 'scale(1)'; 
                        pillLabel.style.boxShadow = '';
                    }, 400);
                }
                updateLiveFormPreview();
                showToast(`⏱️ Waktu ${timeVal} dipilih pada matriks dering (${timeMap[targetKey]})!`);
                return;
            }
        }

        let customInput = document.getElementById('hidden_custom_time_input');
        if (!customInput) {
            customInput = document.createElement('input');
            customInput.type = 'hidden';
            customInput.name = 'custom_time_input';
            customInput.id = 'hidden_custom_time_input';
            document.getElementById('scheduleForm').appendChild(customInput);
        }
        customInput.value = timeVal;
        showToast(`⏱️ Waktu custom ${timeVal} ditambahkan ke jadwal!`);
    }

    function applyRepeatHoursInterval(startStr = null, endStr = null, intervalMins = null) {
        if (!startStr) startStr = document.getElementById('inp_repeat_start').value || '07:00';
        if (!endStr) endStr = document.getElementById('inp_repeat_end').value || '16:00';
        if (!intervalMins) intervalMins = parseInt(document.getElementById('inp_repeat_interval').value, 10) || 60;

        const [startH, startM] = startStr.split(':').map(Number);
        const [endH, endM] = endStr.split(':').map(Number);

        const startSecs = startH * 3600 + startM * 60;
        const endSecs = endH * 3600 + endM * 60;

        if (startSecs > endSecs) {
            showToast('⚠️ Waktu mulai harus lebih kecil dari waktu selesai!', true);
            return;
        }

        let checkedCount = 0;

        for (let secs = startSecs; secs <= endSecs; secs += (intervalMins * 60)) {
            let targetKey = null;
            let minDiff = 999999;
            Object.keys(timeMap).forEach(key => {
                const [th, tm] = timeMap[key].split(':').map(Number);
                const bellSecs = th * 3600 + tm * 60;
                const diff = Math.abs(bellSecs - secs);
                if (diff < minDiff && diff <= (intervalMins * 30)) {
                    minDiff = diff;
                    targetKey = key;
                }
            });

            if (targetKey) {
                const cb = document.getElementById('cb_' + targetKey);
                if (cb) {
                    cb.checked = true;
                    checkedCount++;
                }
            }
        }

        updateLiveFormPreview();
        showToast(`🔄 Berhasil mengaktifkan ${checkedCount} slot jam pengulang (${startStr} – ${endStr}, Setiap ${intervalMins} mnt)!`);
    }

    let specificTimeSlots = [''];

    function renderSpecificTimeSlotsUI() {
        const container = document.getElementById('specificTimeSlotsContainer');
        if (!container) return;

        container.innerHTML = '';
        if (specificTimeSlots.length === 0) {
            specificTimeSlots = [''];
        }

        specificTimeSlots.forEach((timeVal, idx) => {
            const slotDiv = document.createElement('div');
            slotDiv.style.cssText = 'display: flex; align-items: center; gap: 8px; background: #fff; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);';
            
            const slotLabel = specificTimeSlots.length > 1 
                ? `Jam Pemutaran ke-${idx + 1}:` 
                : `Jam Pemutaran:`;

            const removeBtn = specificTimeSlots.length > 1 
                ? `<button type="button" onclick="removeSpecificTimeSlot(${idx})" style="background: #fee2e2; border: 1px solid #fca5a5; color: #ef4444; border-radius: 6px; padding: 4px 8px; font-weight: bold; cursor: pointer; font-size: 11px; display: flex; align-items: center; gap: 3px;" title="Hapus Jam Ini"><span>&times;</span> Hapus</button>` 
                : '';

            slotDiv.innerHTML = `
                <span style="font-size: 12px; font-weight: 700; color: #1e40af;">⏱️ ${slotLabel}</span>
                <input type="text" class="multi-custom-time flatpickr-time" value="${timeVal}" placeholder="HH:MM:SS" onchange="specificTimeSlots[${idx}] = this.value; syncMultiSpecificTimes();" style="padding: 5px 10px; border: 1px solid #93c5fd; border-radius: 6px; font-size: 12px; font-weight: 800; color: #0f172a; background: #fff; width: 100px; text-align: center;">
                ${removeBtn}
            `;
            container.appendChild(slotDiv);
        });
        
        // Initialize Flatpickr on all newly created inputs
        flatpickr(".flatpickr-time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i:S",
            time_24hr: true,
            enableSeconds: true
        });
    }

    function syncMultiSpecificTimes() {
        const inputs = document.querySelectorAll('#specificTimeSlotsContainer .multi-custom-time');
        const selectedTimes = [];
        inputs.forEach(inp => {
            if (inp.value && inp.value.trim() !== '') {
                selectedTimes.push(inp.value.trim());
            }
        });

        let customInput = document.getElementById('hidden_custom_time_input');
        if (!customInput) {
            customInput = document.createElement('input');
            customInput.type = 'hidden';
            customInput.name = 'custom_time_input';
            customInput.id = 'hidden_custom_time_input';
            document.getElementById('scheduleForm').appendChild(customInput);
        }
        customInput.value = selectedTimes.join(',');

        updateLiveFormPreview();
        return selectedTimes.length;
    }

    function validateAndSyncForm(e) {
        const timeCount = syncMultiSpecificTimes();

        // 1. Validate Days Ring (at least 1 day selected)
        let daySelected = false;
        for (let i = 1; i <= 6; i++) {
            const cb = document.getElementById('cb_day_' + i);
            if (cb && cb.checked) {
                daySelected = true;
                break;
            }
        }
        if (!daySelected) {
            showToast('⚠️ Harap pilih minimal satu Hari Operasional (Senin–Sabtu)!', true);
            if (e) e.preventDefault();
            return false;
        }

        // 2. Validate Speaker Zone Output (at least 1 speaker selected)
        const spkIn = document.getElementById('cb_spk_in');
        const spkOut = document.getElementById('cb_spk_out');
        if ((!spkIn || !spkIn.checked) && (!spkOut || !spkOut.checked)) {
            showToast('⚠️ Harap pilih minimal satu Speaker Zone Output (Indoor / Outdoor)!', true);
            if (e) e.preventDefault();
            return false;
        }

        // 3. Validate Time Slots (at least 1 valid time entered)
        const customInput = document.getElementById('hidden_custom_time_input');
        if (!customInput || !customInput.value || customInput.value.trim() === '') {
            showToast('⚠️ Harap masukkan minimal satu Waktu Dering Bel yang valid!', true);
            if (e) e.preventDefault();
            return false;
        }

        return true;
    }

    function addSpecificTimeSlot(val = '') {
        specificTimeSlots.push(val);
        renderSpecificTimeSlotsUI();
        syncMultiSpecificTimes();
    }

    function removeSpecificTimeSlot(idx) {
        if (specificTimeSlots.length <= 1) {
            specificTimeSlots = [''];
        } else {
            specificTimeSlots.splice(idx, 1);
        }
        renderSpecificTimeSlotsUI();
        syncMultiSpecificTimes();
    }

    function runManualReorder() {
        if (!confirm('Urutkan ulang seluruh jadwal secara kronologis berdasarkan jam dering?')) return;

        const formData = new FormData();
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/reorder') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (res.csrf_hash) csrfHash = res.csrf_hash;
                showToast('🔢 ' + res.message);
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('❌ ' + res.message, true);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('❌ Gagal mengurutkan jadwal', true);
        });
    }

    setInterval(updateLiveCountdown, 1000);
    updateLiveCountdown();

    // --- 3. INTERACTIVE SEARCH & FILTER BAR ---
    function filterScheduleTable() {
        const keyword = document.getElementById('filterKeyword').value.toLowerCase();
        const selectedDay = document.getElementById('filterDay').value;
        const selectedSpk = document.getElementById('filterSpeaker').value;

        const rows = document.querySelectorAll('#schedTable tbody tr');
        rows.forEach(tr => {
            const conten = tr.querySelector('.col-conten') ? tr.querySelector('.col-conten').innerText.toLowerCase() : '';
            const bld = tr.querySelector('.bld-col') ? tr.querySelector('.bld-col').innerText.toLowerCase() : '';
            const no = tr.querySelector('.col-no') ? tr.querySelector('.col-no').innerText.toLowerCase() : '';

            const matchKeyword = conten.includes(keyword) || bld.includes(keyword) || no.includes(keyword);

            let matchDay = true;
            if (selectedDay) {
                matchDay = tr.getAttribute('data-day' + selectedDay) == '1';
            }

            let matchSpk = true;
            if (selectedSpk === 'in') {
                matchSpk = tr.getAttribute('data-spkin') == '1';
            } else if (selectedSpk === 'out') {
                matchSpk = tr.getAttribute('data-spkout') == '1';
            }

            if (matchKeyword && matchDay && matchSpk) {
                tr.style.display = '';
            } else {
                tr.style.display = 'none';
            }
        });
    }

    // --- 4. QUICK PRESETS TOOLBAR ---
    function runPreset(action) {
        let label = '';
        if (action === 'workdays') label = 'Terapkan Hari Kerja (Senin-Jumat) ke seluruh jadwal?';
        else if (action === 'spk_in') label = 'Set Speaker Indoor (IN) ke seluruh jadwal?';
        else if (action === 'spk_out') label = 'Set Speaker Outdoor (OUT) ke seluruh jadwal?';
        else if (action === 'clear_ring') label = '⚠️ Apakah Anda yakin ingin MERESET SEMUA WAKTU DERING?';

        if (!confirm(label)) return;

        const formData = new FormData();
        formData.append('action', action);
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/preset') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('⚡ ' + res.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('❌ ' + res.message, true);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('❌ Gagal menjalankan preset', true);
        });
    }

    // --- 5. DUPLICATE SCHEDULE ---
    function duplicateSchedule(id) {
        if (!confirm('Duplikat jadwal ini?')) return;

        const formData = new FormData();
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/duplicate/') ?>' + id, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('📋 ' + res.message);
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('❌ ' + res.message, true);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('❌ Gagal menduplikasi jadwal', true);
        });
    }

    // --- CELL POPOVER BALLOON INTERACTION SYSTEM ---
    let activeCellTarget = null;

    function openCellPopover(cell, event) {
        if (event) event.stopPropagation();
        activeCellTarget = cell;

        const id = cell.getAttribute('data-id');
        const field = cell.getAttribute('data-field');
        const tr = cell.closest('tr');
        if (!tr || !id) return;

        const contenName = tr.querySelector('.col-conten') ? tr.querySelector('.col-conten').innerText.trim() : 'Jadwal #' + id;
        const fieldLabel = getFieldDisplayLabel(field);
        const isRing = cell.classList.contains('ring');

        document.getElementById('popoverCellTitle').innerText = `📌 ${fieldLabel}`;
        document.getElementById('popoverCellSubtitle').innerHTML = `<b>${contenName}</b><br><span style="font-size:10px; color:#94a3b8;">Status cell: ${isRing ? '<span style="color:#4ade80; font-weight:bold;">ON (Aktif)</span>' : '<span style="color:#f87171; font-weight:bold;">OFF (Non-Aktif)</span>'}</span>`;

        showPopoverMainMenu();

        const popover = document.getElementById('cellActionPopover');
        popover.style.display = 'block';

        const rect = cell.getBoundingClientRect();
        const popRect = popover.getBoundingClientRect();

        let top = rect.bottom + window.scrollY + 6;
        let left = rect.left + window.scrollX - (popRect.width / 2) + (rect.width / 2);

        if (left < 10) left = 10;
        if (left + popRect.width > window.innerWidth - 20) {
            left = window.innerWidth - popRect.width - 20;
        }
        if (top + popRect.height > window.innerHeight + window.scrollY - 10) {
            top = rect.top + window.scrollY - popRect.height - 6;
        }

        popover.style.top = top + 'px';
        popover.style.left = left + 'px';
    }

    function closeCellPopover() {
        const popover = document.getElementById('cellActionPopover');
        if (popover) popover.style.display = 'none';
        activeCellTarget = null;
    }

    function showPopoverMainMenu() {
        document.getElementById('popoverMainActions').style.display = 'block';
        document.getElementById('popoverEditSubmenu').style.display = 'none';
    }

    function showPopoverEditSubmenu() {
        document.getElementById('popoverMainActions').style.display = 'none';
        document.getElementById('popoverEditSubmenu').style.display = 'block';
    }

    function getFieldDisplayLabel(field) {
        if (!field) return 'Cell Jadwal';
        if (field === 'conten') return 'Nama Conten Bel';
        if (field === 'building') return 'Building / Area';
        if (field === 'no') return 'Nomor Urut';
        if (field.startsWith('day_')) {
            const days = { day_1:'Hari Senin', day_2:'Hari Selasa', day_3:'Hari Rabu', day_4:'Hari Kamis', day_5:'Hari Jumat', day_6:'Hari Sabtu' };
            return days[field] || field;
        }
        if (field === 'spk_in') return 'Speaker Indoor (IN)';
        if (field === 'spk_out') return 'Speaker Outdoor (OUT)';
        if (timeMap[field]) return `Jam Dering ${timeMap[field]}`;
        return field;
    }

    // Balloon Action Handlers
    function execPopoverToggle() {
        if (!activeCellTarget) return;
        const cell = activeCellTarget;
        const id = cell.getAttribute('data-id');
        const field = cell.getAttribute('data-field');
        const isRing = cell.classList.contains('ring');
        const newValue = isRing ? 0 : 1;

        if (newValue === 1) cell.classList.add('ring');
        else cell.classList.remove('ring');

        saveInlineData(id, field, newValue, (success) => {
            if (success) {
                showToast('⚡ Status dering diperbarui ke ' + (newValue ? 'ON' : 'OFF'));
            } else {
                if (isRing) cell.classList.add('ring');
                else cell.classList.remove('ring');
                showToast('❌ Gagal mengubah status cell', true);
            }
        });
        closeCellPopover();
    }

    function handlePopoverDelete() {
        if (!activeCellTarget) return;
        const cell = activeCellTarget;
        const id = cell.getAttribute('data-id');
        const field = cell.getAttribute('data-field');

        if (cell.classList.contains('cell-toggle')) {
            cell.classList.remove('ring');
            saveInlineData(id, field, 0, (success) => {
                if (success) showToast('🗑️ Dering dibersihkan');
                else showToast('❌ Gagal menghapus dering', true);
            });
        } else if (confirm('Kosongkan isi cell ini?')) {
            cell.innerText = '';
            saveInlineData(id, field, '', (success) => {
                if (success) showToast('🗑️ Isi cell dibersihkan');
            });
        }
        closeCellPopover();
    }

    function execPopoverTextEdit() {
        if (!activeCellTarget) return;
        const cell = activeCellTarget;
        closeCellPopover();

        if (cell.classList.contains('cell-editable')) {
            triggerInlineCellInput(cell);
        } else {
            openModal('edit', cell.getAttribute('data-id'));
        }
    }

    function execPopoverAudioEdit() {
        if (!activeCellTarget) return;
        const id = activeCellTarget.getAttribute('data-id');
        closeCellPopover();
        openModal('edit', id);
    }

    function execPopoverFullForm() {
        if (!activeCellTarget) return;
        const id = activeCellTarget.getAttribute('data-id');
        closeCellPopover();
        openModal('edit', id);
    }

    function execPopoverDuplicate() {
        if (!activeCellTarget) return;
        const id = activeCellTarget.getAttribute('data-id');
        closeCellPopover();
        duplicateSchedule(id);
    }

    function execPopoverHighlight() {
        if (!activeCellTarget) return;
        const id = activeCellTarget.getAttribute('data-id');
        closeCellPopover();
        toggleRowHighlight(id);
    }

    // Attach Cell Click Listeners
    document.querySelectorAll('.cell-toggle').forEach(cell => {
        cell.addEventListener('click', function(e) {
            openCellPopover(this, e);
        });
    });

    document.querySelectorAll('.cell-editable').forEach(cell => {
        cell.addEventListener('click', function(e) {
            openCellPopover(this, e);
        });
        cell.addEventListener('dblclick', function(e) {
            e.stopPropagation();
            closeCellPopover();
            triggerInlineCellInput(this);
        });
    });

    function triggerInlineCellInput(cell) {
        if (cell.querySelector('input')) return;

        const id = cell.getAttribute('data-id');
        const field = cell.getAttribute('data-field');
        const currentText = cell.innerText.trim();

        const input = document.createElement('input');
        input.type = (field === 'no') ? 'number' : 'text';
        input.className = 'cell-editing-input';
        input.value = currentText;

        cell.innerHTML = '';
        cell.appendChild(input);
        input.focus();
        input.select();

        let isSaved = false;
        const finishEditing = (save) => {
            if (isSaved) return;
            isSaved = true;

            const newValue = input.value.trim();
            if (save && newValue !== currentText) {
                cell.innerText = newValue;
                saveInlineData(id, field, newValue, (success) => {
                    if (success) {
                        showToast('✅ Field "' + field + '" diperbarui');
                    } else {
                        cell.innerText = currentText;
                        showToast('❌ Gagal mengedit text', true);
                    }
                });
            } else {
                cell.innerText = currentText;
            }
        };

        input.addEventListener('blur', () => finishEditing(true));
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                finishEditing(true);
            } else if (e.key === 'Escape') {
                finishEditing(false);
            }
        });
    }

    // Close popover balloon when clicking anywhere outside
    document.addEventListener('click', function(e) {
        const popover = document.getElementById('cellActionPopover');
        if (popover && popover.style.display !== 'none') {
            if (!popover.contains(e.target) && !e.target.closest('.cell-toggle') && !e.target.closest('.cell-editable')) {
                closeCellPopover();
            }
        }
    });

    // 3. Row Highlight Toggle (Kuning)
    function toggleRowHighlight(id) {
        const tr = document.getElementById('row-' + id);
        if (!tr) return;

        const isHighlighted = tr.classList.contains('row-yellow');
        const newValue = isHighlighted ? 0 : 1;

        if (newValue === 1) {
            tr.classList.add('row-yellow');
        } else {
            tr.classList.remove('row-yellow');
        }

        saveInlineData(id, 'highlight', newValue, (success) => {
            if (success) {
                showToast('🎨 Highlight baris ' + (newValue ? 'aktif' : 'non-aktif'));
            } else {
                if (isHighlighted) tr.classList.add('row-yellow');
                else tr.classList.remove('row-yellow');
                showToast('❌ Gagal mengubah highlight', true);
            }
        });
    }

    // AJAX saver helper
    function saveInlineData(id, field, value, callback) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('field', field);
        formData.append('value', value);
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('schedule-bell/inline-update') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (res.csrf_hash) csrfHash = res.csrf_hash;
                callback(true);
            } else {
                callback(false);
            }
        })
        .catch(err => {
            console.error(err);
            callback(false);
        });
    }

    // Toast notification helper
    let toastTimeout;
    function showToast(msg, isError = false) {
        let toast = document.getElementById('toastNotify');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toastNotify';
            toast.className = 'toast-notify';
            document.body.appendChild(toast);
        }
        toast.style.background = isError ? 'rgba(220, 53, 69, 0.95)' : 'rgba(40, 167, 69, 0.95)';
        toast.innerHTML = msg;
        toast.style.display = 'flex';
        toast.style.opacity = '1';

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 1500);
    }
</script>
</div>
<?= $this->endSection() ?>
