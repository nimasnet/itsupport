<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$query_params = [];
if ($filter_ip)     $query_params[] = 'ip='     . urlencode($filter_ip);
if ($filter_status) $query_params[] = 'status=' . urlencode($filter_status);
if ($filter_date)   $query_params[] = 'date='   . urlencode($filter_date);
$base_query = implode('&', $query_params);
?>
<style>
    /* ============ MONITORING LOGS STYLES ============ */
    .log-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .log-header h2 {
        font-size: 22px;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .log-header h2 span.icon {
        background: linear-gradient(135deg, #0077b6, #00b4d8);
        color: white;
        width: 38px; height: 38px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }

    /* Summary Cards */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .summary-card {
        border-radius: 10px;
        padding: 18px 20px;
        color: white;
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        display: flex; flex-direction: column; gap: 6px;
    }
    .summary-card .card-label { font-size: 12px; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.5px; }
    .summary-card .card-value { font-size: 30px; font-weight: 800; line-height: 1; }
    .summary-card .card-sub   { font-size: 12px; opacity: 0.8; }
    .card-total  { background: linear-gradient(135deg, #4361ee, #3a0ca3); }
    .card-up     { background: linear-gradient(135deg, #06d6a0, #028a60); }
    .card-down   { background: linear-gradient(135deg, #ef233c, #8b0000); }
    .card-session{ background: linear-gradient(135deg, #f77f00, #c55a11); }

    /* Recent sessions */
    .recent-runs {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .recent-runs h4 { font-size: 13px; color: #666; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    .session-pills { display: flex; flex-wrap: wrap; gap: 8px; }
    .session-pill {
        background: white;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex; align-items: center; gap: 6px;
        text-decoration: none; color: #333;
    }
    .session-pill:hover { border-color: #0077b6; color: #0077b6; box-shadow: 0 2px 6px rgba(0,119,182,0.2); }
    .pill-up   { color: #06d6a0; font-weight: bold; }
    .pill-down { color: #ef233c; font-weight: bold; }

    /* Filter Bar */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label { font-size: 12px; font-weight: bold; color: #555; text-transform: uppercase; }
    .filter-group select,
    .filter-group input { 
        padding: 7px 12px; border: 1px solid #ccc; border-radius: 5px; 
        font-size: 14px; background: white; min-width: 160px;
    }
    .btn-filter { 
        padding: 8px 20px; background: #0077b6; color: white; 
        border: none; border-radius: 5px; cursor: pointer; font-size: 14px;
        align-self: flex-end;
    }
    .btn-filter:hover { background: #005f8d; }
    .btn-reset { 
        padding: 8px 15px; background: #6c757d; color: white; 
        border: none; border-radius: 5px; cursor: pointer; font-size: 14px;
        align-self: flex-end; text-decoration: none; display: inline-block;
    }
    .btn-reset:hover { background: #545b62; }
    .btn-export {
        padding: 8px 15px; background: #217346; color: white;
        border: none; border-radius: 5px; cursor: pointer; font-size: 14px;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        align-self: flex-end; font-weight: bold;
        box-shadow: 0 2px 6px rgba(33,115,70,0.25);
        transition: background 0.15s, box-shadow 0.15s;
    }
    .btn-export:hover { background: #185c37; box-shadow: 0 4px 12px rgba(33,115,70,0.35); }

    /* Table */
    .log-table-wrap {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .log-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .log-table thead th {
        background: linear-gradient(135deg, #0077b6, #005f8d);
        color: white;
        padding: 12px 14px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border: none;
    }
    .log-table tbody tr { transition: background 0.15s; }
    .log-table tbody tr:nth-child(even) { background: #fafafa; }
    .log-table tbody tr:hover { background: #e8f4fd; }
    .log-table td { 
        padding: 10px 14px; 
        border-bottom: 1px solid #f0f0f0; 
        vertical-align: middle; 
        border-left: none; border-right: none;
    }
    .log-table tbody tr:last-child td { border-bottom: none; }

    .badge-up {
        background: #d4f5e9; color: #028a60;
        padding: 3px 12px; border-radius: 20px;
        font-weight: bold; font-size: 12px;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .badge-down {
        background: #fde8e8; color: #c0392b;
        padding: 3px 12px; border-radius: 20px;
        font-weight: bold; font-size: 12px;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .badge-up::before   { content: "●"; font-size: 8px; }
    .badge-down::before { content: "●"; font-size: 8px; }

    .response-time { font-family: monospace; font-size: 12px; color: #555; }
    .response-timeout { color: #c0392b; font-style: italic; font-size: 12px; }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        flex-wrap: wrap;
        gap: 10px;
    }
    .pagination-info { font-size: 13px; color: #555; }
    .pagination-links { display: flex; gap: 5px; }
    .page-btn {
        padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px;
        text-decoration: none; color: #333; font-size: 13px;
        background: white; transition: all 0.2s;
    }
    .page-btn:hover  { border-color: #0077b6; color: #0077b6; }
    .page-btn.active { background: #0077b6; color: white; border-color: #0077b6; font-weight: bold; }
    .page-btn.disabled { color: #ccc; cursor: not-allowed; pointer-events: none; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    .empty-state .empty-icon { font-size: 60px; margin-bottom: 15px; }
    .empty-state h3 { color: #555; margin-bottom: 8px; }

    .no-data { color: #bbb; font-size: 12px; font-style: italic; }

    /* ===== Modal Export Excel ===== */
    .export-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9998;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .export-modal-overlay.show { display: flex; }
    .export-modal {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 50px rgba(0,0,0,0.28);
        padding: 30px 32px 26px 32px;
        min-width: 440px;
        max-width: 560px;
        width: 92vw;
        animation: exportModalIn 0.22s cubic-bezier(.4,1.4,.6,1) both;
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
    }
    @keyframes exportModalIn {
        from { opacity:0; transform: scale(0.87) translateY(28px); }
        to   { opacity:1; transform: scale(1) translateY(0); }
    }
    .export-modal-header {
        display: flex; align-items: center; gap: 13px; margin-bottom: 5px;
    }
    .export-modal-header .ex-icon {
        width: 44px; height: 44px; border-radius: 10px; flex-shrink: 0;
        background: linear-gradient(135deg, #217346, #185c37);
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
    }
    .export-modal-header h4 { margin:0; font-size: 18px; color: #1a1a2e; }
    .export-modal-sub { font-size: 13px; color: #888; margin-bottom: 22px; margin-left: 57px; }
    .export-section-label {
        font-size: 11px; font-weight: 800; color: #217346;
        text-transform: uppercase; letter-spacing: 0.8px;
        margin: 18px 0 8px 0;
        display: flex; align-items: center; gap: 6px;
    }
    .export-section-label::after {
        content: ''; flex: 1; height: 1px; background: #e5e7eb;
    }
    .export-datetime-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
        margin-bottom: 4px;
    }
    .export-dt-group { display: flex; flex-direction: column; gap: 4px; }
    .export-dt-group label { font-size: 12px; font-weight: bold; color: #555; }
    .export-dt-group input[type=datetime-local] {
        padding: 8px 10px; border: 1.5px solid #d1d5db;
        border-radius: 7px; font-size: 13px; width: 100%;
        box-sizing: border-box; outline: none;
        transition: border-color 0.2s;
    }
    .export-dt-group input:focus { border-color: #217346; }
    /* VLAN export list */
    .export-vlan-all-bar {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 12px; background: #f0faf4;
        border-radius: 7px; margin-bottom: 8px;
        font-size: 13px; font-weight: bold; color: #215732;
        cursor: pointer; border: 1px solid #c6e8d1; user-select: none;
    }
    .export-vlan-all-bar:hover { background: #ddf2e7; }
    .export-vlan-all-bar input[type=checkbox] { width:16px; height:16px; accent-color:#217346; cursor:pointer; }
    .export-vlan-list {
        max-height: 200px; overflow-y: auto;
        border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 8px;
    }
    .export-vlan-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 14px; border-bottom: 1px solid #f0f0f0;
        cursor: pointer; transition: background 0.12s;
    }
    .export-vlan-item:last-child { border-bottom: none; }
    .export-vlan-item:hover { background: #f0faf4; }
    .export-vlan-item input[type=checkbox] { width:16px; height:16px; accent-color:#217346; cursor:pointer; flex-shrink:0; }
    .export-vlan-item .ev-name { font-weight:bold; font-size:13px; color:#1a1a2e; flex:1; }
    .export-vlan-item .ev-net  { font-size:11px; color:#888; font-family:monospace; }
    .export-vlan-item .ev-badge {
        background: #e8f5e9; color: #217346;
        padding: 2px 9px; border-radius: 10px; font-size: 11px; font-weight:bold;
    }
    /* Save location info box */
    .save-location-box {
        background: #f8f9fa; border: 1px solid #e0e6ed;
        border-radius: 8px; padding: 12px 14px;
        margin-bottom: 4px; font-size: 13px; color: #555;
        display: flex; align-items: center; gap: 10px;
    }
    .save-location-box .sl-icon { font-size: 20px; flex-shrink:0; }
    .save-location-box p { margin:0; line-height: 1.5; }
    .save-location-box strong { color: #1a1a2e; }
    /* Export modal footer */
    .export-modal-footer {
        display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px;
    }
    .btn-export-cancel {
        padding: 9px 22px; border-radius: 7px;
        border: 1px solid #ddd; background: #f5f5f5;
        color: #555; font-size: 14px; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-export-cancel:hover { background: #e8e8e8; }
    .btn-do-export {
        padding: 9px 22px; border-radius: 7px; border: none;
        background: #217346; color: #fff; font-size: 14px; font-weight: bold;
        cursor: pointer; display: flex; align-items: center; gap: 7px;
        box-shadow: 0 2px 8px rgba(33,115,70,0.3);
        transition: background 0.15s, box-shadow 0.15s;
    }
    .btn-do-export:hover { background: #185c37; box-shadow: 0 4px 14px rgba(33,115,70,0.4); }
    .btn-do-export:disabled { background: #90c4a8; cursor:not-allowed; box-shadow:none; }

    /* ===== Popup Sukses Export ===== */
    .export-success-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 10001; align-items: center; justify-content: center;
    }
    .export-success-overlay.show { display: flex; }
    .export-success-modal {
        background: #fff; border-radius: 14px;
        box-shadow: 0 10px 50px rgba(0,0,0,0.3);
        padding: 36px 38px 30px 38px;
        max-width: 400px; width: 90vw;
        text-align: center;
        animation: exportModalIn 0.25s cubic-bezier(.4,1.4,.6,1) both;
    }
    .success-checkmark {
        width: 68px; height: 68px; border-radius: 50%;
        background: linear-gradient(135deg, #06d6a0, #028a60);
        display: flex; align-items: center; justify-content: center;
        font-size: 34px; margin: 0 auto 18px auto;
        box-shadow: 0 4px 20px rgba(6,214,160,0.35);
        animation: popIn 0.4s cubic-bezier(.4,1.6,.6,1) both;
    }
    @keyframes popIn { from{opacity:0;transform:scale(0.5)} to{opacity:1;transform:scale(1)} }
    .export-success-modal h4 { font-size: 19px; color: #1a1a2e; margin-bottom: 8px; }
    .export-success-modal p  { font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 6px; }
    .export-filename {
        background: #f0faf4; border: 1px solid #c6e8d1;
        border-radius: 7px; padding: 8px 14px;
        font-family: monospace; font-size: 13px; color: #217346;
        font-weight: bold; margin: 12px 0 22px 0;
        word-break: break-all;
    }
    .btn-close-success {
        padding: 10px 30px; border-radius: 7px; border: none;
        background: #217346; color: white; font-size: 14px;
        font-weight: bold; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-close-success:hover { background: #185c37; }

    /* ===== Modal Reset Log ===== */
    .reset-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .reset-modal-overlay.show { display: flex; }

    .reset-modal {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 50px rgba(0,0,0,0.3);
        padding: 30px 32px 26px 32px;
        min-width: 380px;
        max-width: 520px;
        width: 90vw;
        animation: resetModalIn 0.22s cubic-bezier(.4,1.4,.6,1) both;
        position: relative;
    }
    @keyframes resetModalIn {
        from { opacity: 0; transform: scale(0.85) translateY(30px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .reset-modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
    }
    .reset-modal-header .modal-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        background: linear-gradient(135deg, #ef233c, #8b0000);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .reset-modal-header h4 {
        margin: 0;
        font-size: 18px;
        color: #1a1a2e;
    }
    .reset-modal-subtitle {
        font-size: 13px;
        color: #888;
        margin-bottom: 20px;
        margin-left: 54px;
    }
    .modal-close-btn {
        position: absolute;
        top: 14px; right: 16px;
        background: none; border: none;
        font-size: 20px; color: #aaa;
        cursor: pointer; padding: 2px 6px;
        border-radius: 4px;
        transition: color 0.15s, background 0.15s;
    }
    .modal-close-btn:hover { color: #e53e3e; background: #fff0f0; }

    /* Select All bar */
    .select-all-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: #f0f4ff;
        border-radius: 7px;
        margin-bottom: 10px;
        font-size: 13px;
        font-weight: bold;
        color: #3a3a6e;
        cursor: pointer;
        border: 1px solid #d0d8f5;
        user-select: none;
    }
    .select-all-bar:hover { background: #e2e9ff; }
    .select-all-bar input[type=checkbox] { width: 16px; height: 16px; cursor: pointer; accent-color: #0077b6; }

    /* VLAN list */
    .vlan-check-list {
        max-height: 280px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .vlan-check-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.12s;
    }
    .vlan-check-item:last-child { border-bottom: none; }
    .vlan-check-item:hover { background: #f5f7ff; }
    .vlan-check-item input[type=checkbox] { width: 17px; height: 17px; accent-color: #ef233c; cursor: pointer; flex-shrink: 0; }
    .vlan-check-item .vlan-name { font-weight: bold; font-size: 14px; color: #1a1a2e; flex: 1; }
    .vlan-check-item .vlan-net  { font-size: 12px; color: #888; font-family: monospace; }
    .vlan-check-item .vlan-badge {
        background: #fde8e8; color: #c0392b;
        padding: 2px 10px; border-radius: 12px;
        font-size: 11px; font-weight: bold;
        white-space: nowrap;
    }
    .vlan-check-item .vlan-badge.empty {
        background: #f0f0f0; color: #aaa;
    }

    /* Modal footer buttons */
    .reset-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-cancel-modal {
        padding: 9px 22px; border-radius: 7px;
        border: 1px solid #ddd; background: #f5f5f5;
        color: #555; font-size: 14px; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-cancel-modal:hover { background: #e8e8e8; }
    .btn-delete-log {
        padding: 9px 22px; border-radius: 7px;
        border: none; background: #ef233c;
        color: #fff; font-size: 14px; font-weight: bold;
        cursor: pointer;
        transition: background 0.15s, box-shadow 0.15s;
        box-shadow: 0 2px 8px rgba(239,35,60,0.25);
        display: flex; align-items: center; gap: 6px;
    }
    .btn-delete-log:hover { background: #c0392b; box-shadow: 0 4px 14px rgba(239,35,60,0.35); }
    .btn-delete-log:disabled { background: #f0a0a8; cursor: not-allowed; box-shadow: none; }

    /* Confirm overlay */
    .confirm-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }
    .confirm-modal-overlay.show { display: flex; }
    .confirm-modal {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 50px rgba(0,0,0,0.35);
        padding: 30px 32px;
        max-width: 420px;
        width: 90vw;
        animation: resetModalIn 0.2s ease both;
        text-align: center;
    }
    .confirm-modal .confirm-icon { font-size: 48px; margin-bottom: 14px; }
    .confirm-modal h4 { font-size: 18px; color: #1a1a2e; margin-bottom: 8px; }
    .confirm-modal p  { font-size: 14px; color: #666; margin-bottom: 22px; line-height: 1.6; }
    .confirm-modal .confirm-vlan-list {
        background: #fff5f5;
        border: 1px solid #fdd;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 22px;
        text-align: left;
        font-size: 13px;
        color: #c0392b;
        font-weight: bold;
        max-height: 150px;
        overflow-y: auto;
    }
    .confirm-modal .confirm-buttons { display: flex; gap: 10px; justify-content: center; }
    .btn-confirm-yes {
        padding: 10px 28px; border-radius: 7px; border: none;
        background: #ef233c; color: white; font-size: 14px; font-weight: bold;
        cursor: pointer; transition: background 0.15s;
    }
    .btn-confirm-yes:hover { background: #c0392b; }
    .btn-confirm-no {
        padding: 10px 28px; border-radius: 7px;
        border: 1px solid #ddd; background: #f5f5f5;
        color: #555; font-size: 14px; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-confirm-no:hover { background: #e8e8e8; }

    /* Tombol Reset Log di header */
    .btn-reset-log {
        padding: 8px 16px; background: #ef233c; color: white;
        border: none; border-radius: 5px; cursor: pointer;
        font-size: 13px; font-weight: bold;
        display: inline-flex; align-items: center; gap: 6px;
        transition: background 0.15s, box-shadow 0.15s;
        box-shadow: 0 2px 6px rgba(239,35,60,0.2);
    }
    .btn-reset-log:hover { background: #c0392b; box-shadow: 0 4px 12px rgba(239,35,60,0.3); }
</style>

<!-- ===== Modal Export Excel ===== -->
<div class="export-modal-overlay" id="exportModalOverlay">
    <div class="export-modal">
        <button class="modal-close-btn" id="btnCloseExport" title="Tutup">&times;</button>
        <div class="export-modal-header">
            <div class="ex-icon">📊</div>
            <h4>Export Log ke Excel</h4>
        </div>
        <p class="export-modal-sub">Atur rentang waktu dan pilih VLAN yang akan diekspor.</p>

        <!-- Rentang Tanggal & Waktu -->
        <div class="export-section-label">📅 Rentang Waktu</div>
        <div class="export-datetime-row">
            <div class="export-dt-group">
                <label for="exportDateStart">Dari (Start)</label>
                <input type="datetime-local" id="exportDateStart">
            </div>
            <div class="export-dt-group">
                <label for="exportDateEnd">Sampai (End)</label>
                <input type="datetime-local" id="exportDateEnd">
            </div>
        </div>
        <p style="font-size:12px; color:#aaa; margin: 4px 0 0 0;">Kosongkan jika ingin ekspor semua tanggal.</p>

        <!-- Pilih VLAN -->
        <div class="export-section-label">🌐 Pilih VLAN</div>
        <label class="export-vlan-all-bar">
            <input type="checkbox" id="exChkAll"> Pilih Semua VLAN
        </label>
        <div class="export-vlan-list">
            <?php foreach ($vlan_reset_list as $vr): ?>
            <label class="export-vlan-item">
                <input type="checkbox" class="ex-vlan-cb" value="<?= htmlspecialchars($vr['network_ip']) ?>" data-name="<?= htmlspecialchars($vr['nama_vlan']) ?>" checked>
                <span class="ev-name"><?= htmlspecialchars($vr['nama_vlan']) ?></span>
                <span class="ev-net"><?= htmlspecialchars($vr['network_ip']) ?>.xxx</span>
                <span class="ev-badge"><?= number_format($vr['jumlah_log']) ?> log</span>
            </label>
            <?php endforeach; ?>
            <?php if (empty($vlan_reset_list)): ?>
            <div style="padding:20px; text-align:center; color:#aaa; font-size:13px;">Belum ada VLAN terdaftar.</div>
            <?php endif; ?>
        </div>

        <!-- Lokasi Simpan -->
        <div class="export-section-label">💾 Lokasi Simpan</div>
        <div class="save-location-box">
            <span class="sl-icon">📂</span>
            <p>File akan diunduh melalui browser Anda. <strong>Pilih folder tujuan</strong> pada dialog "Save As" yang muncul setelah klik Export. Nama file akan dibuat otomatis berdasarkan tanggal & waktu export.</p>
        </div>

        <div class="export-modal-footer">
            <button class="btn-export-cancel" id="btnCancelExport">Batal</button>
            <button class="btn-do-export" id="btnDoExport">📊 Export Sekarang</button>
        </div>
    </div>
</div>

<!-- ===== Popup Sukses Export ===== -->
<div class="export-success-overlay" id="exportSuccessOverlay">
    <div class="export-success-modal">
        <div class="success-checkmark">✓</div>
        <h4>Export Berhasil!</h4>
        <p>File Excel telah berhasil dibuat dan sedang diunduh.</p>
        <div class="export-filename" id="exportedFilename"></div>
        <p style="font-size:12px; color:#aaa;">Cek folder <strong>Downloads</strong> atau folder yang Anda pilih di dialog simpan browser.</p>
        <button class="btn-close-success" id="btnCloseSuccess">✓ Selesai</button>
    </div>
</div>

<!-- ===== Modal Pilih VLAN untuk Reset ===== -->
<div class="reset-modal-overlay" id="resetModalOverlay">
    <div class="reset-modal">
        <button class="modal-close-btn" id="btnCloseReset" title="Tutup">&times;</button>
        <div class="reset-modal-header">
            <div class="modal-icon">🗑️</div>
            <h4>Reset Log Monitoring</h4>
        </div>
        <p class="reset-modal-subtitle">Pilih VLAN yang log-nya ingin dihapus dari database.</p>

        <!-- Select All -->
        <label class="select-all-bar">
            <input type="checkbox" id="chkSelectAll"> Pilih Semua VLAN
        </label>

        <!-- Daftar VLAN -->
        <div class="vlan-check-list" id="vlanCheckList">
            <?php foreach ($vlan_reset_list as $vr): ?>
            <label class="vlan-check-item">
                <input type="checkbox" class="vlan-checkbox" value="<?= htmlspecialchars($vr['network_ip']) ?>" data-name="<?= htmlspecialchars($vr['nama_vlan']) ?>">
                <span class="vlan-name"><?= htmlspecialchars($vr['nama_vlan']) ?></span>
                <span class="vlan-net"><?= htmlspecialchars($vr['network_ip']) ?>.xxx</span>
                <span class="vlan-badge <?= $vr['jumlah_log'] == 0 ? 'empty' : '' ?>">
                    <?= number_format($vr['jumlah_log']) ?> log
                </span>
            </label>
            <?php endforeach; ?>
            <?php if (empty($vlan_reset_list)): ?>
            <div style="padding: 30px; text-align: center; color: #aaa; font-size: 13px;">Belum ada VLAN yang terdaftar.</div>
            <?php endif; ?>
        </div>

        <div class="reset-modal-footer">
            <button class="btn-cancel-modal" id="btnCancelReset">Batal</button>
            <button class="btn-delete-log" id="btnProceedDelete" disabled>
                🗑️ Hapus Log Dipilih
            </button>
        </div>
    </div>
</div>

<!-- ===== Modal Konfirmasi Hapus ===== -->
<div class="confirm-modal-overlay" id="confirmModalOverlay">
    <div class="confirm-modal">
        <div class="confirm-icon">⚠️</div>
        <h4>Konfirmasi Hapus Log</h4>
        <p>Anda akan menghapus <strong>seluruh log</strong> dari VLAN berikut secara permanen. Aksi ini <strong>tidak dapat dibatalkan</strong>.</p>
        <div class="confirm-vlan-list" id="confirmVlanList"></div>
        <div class="confirm-buttons">
            <button class="btn-confirm-no" id="btnConfirmNo">← Kembali</button>
            <button class="btn-confirm-yes" id="btnConfirmYes">Ya, Hapus Sekarang</button>
        </div>
    </div>
</div>

<div class="right-frame" style="background: #f4f6fa; padding: 20px;">
    <!-- HEADER -->
    <div class="log-header">
        <h2>
            <span class="icon">📋</span>
            Log Hasil Monitoring Ping
        </h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="<?= site_url('monitoring/cctv/setup') ?>" style="padding: 8px 16px; background: #0077b6; color: white; border-radius: 5px; text-decoration: none; font-size: 13px;">
                ⚙️ Setup Monitoring
            </a>
            <button class="btn-export" id="btnOpenExport">
                📊 Export Excel
            </button>
            <button class="btn-reset-log" id="btnOpenReset">
                🗑️ Reset Log
            </button>
        </div>
    </div>

    <!-- SUMMARY CARDS (Hari Ini) -->
    <div class="summary-cards">
        <div class="summary-card card-total">
            <span class="card-label">Total Log Hari Ini</span>
            <span class="card-value" id="val-total-today"><?= number_format($summary['total'] ?? 0) ?></span>
            <span class="card-sub">Dari seluruh VLAN</span>
        </div>
        <div class="summary-card card-up">
            <span class="card-label">Status UP</span>
            <span class="card-value" id="val-up-today"><?= number_format($summary['up_count'] ?? 0) ?></span>
            <span class="card-sub">IP merespon ping</span>
        </div>
        <div class="summary-card card-down">
            <span class="card-label">Status DOWN</span>
            <span class="card-value" id="val-down-today"><?= number_format($summary['down_count'] ?? 0) ?></span>
            <span class="card-sub">IP tidak merespon</span>
        </div>
        <div class="summary-card card-session">
            <span class="card-label">Total Semua Log</span>
            <span class="card-value" id="val-total-filtered"><?= number_format($total_rows) ?></span>
            <span class="card-sub">Sesuai filter aktif</span>
        </div>
    </div>

    <!-- RECENT SESSIONS -->
    <?php if (count($recent_sessions) > 0): ?>
    <div class="recent-runs">
        <h4>🕐 Sesi Ping Terakhir (Klik untuk Filter)</h4>
        <div class="session-pills" id="recentSessionPills">
            <?php foreach ($recent_sessions as $sess): ?>
            <a href="<?= site_url('monitoring/cctv/logs') ?>?date=<?= date('Y-m-d', strtotime($sess['created_at'])) ?>" class="session-pill">
                🔄 <?= date('d/m H:i', strtotime($sess['created_at'])) ?>
                &nbsp;|&nbsp;
                <span class="pill-up">▲ <?= $sess['up'] ?></span>
                <span class="pill-down">▼ <?= $sess['down'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- FILTER BAR -->
    <form method="GET" action="<?= site_url('monitoring/cctv/logs') ?>">
        <div class="filter-bar">
            <div class="filter-group">
                <label>IP Address</label>
                <input type="text" name="ip" placeholder="Cari IP..." value="<?= htmlspecialchars($filter_ip) ?>">
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="UP"   <?= ($filter_status == 'UP') ? 'selected' : '' ?>>UP (Online)</option>
                    <option value="DOWN" <?= ($filter_status == 'DOWN') ? 'selected' : '' ?>>DOWN (Offline)</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Tanggal</label>
                <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>">
            </div>
            <button type="submit" class="btn-filter">🔍 Cari</button>
            <a href="<?= site_url('monitoring/cctv/logs') ?>" class="btn-reset">↩ Reset</a>
        </div>
    </form>

    <!-- TABLE -->
    <?php if (count($logs) > 0): ?>
    <div class="log-table-wrap">
        <table class="log-table">
            <thead>
                <tr>
                    <th style="width: 45px;">#</th>
                    <th>IP Address</th>
                    <th>Nama CCTV</th>
                    <th>Posisi / Lokasi</th>
                    <th>Channel / NVR</th>
                    <th>Status</th>
                    <th>Response Time</th>
                    <th>Keterangan</th>
                    <th>Waktu Ping</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $row_num = (($page - 1) * 100) + 1;
                foreach ($logs as $log):
                    $is_up = $log['status'] === 'UP';
                    $row_highlight = !$is_up ? 'background: #fff5f5;' : '';
                ?>
                <tr style="<?= $row_highlight ?>">
                    <td style="color: #aaa; font-size: 11px;"><?= $row_num++ ?></td>
                    <td>
                        <strong style="font-family: monospace; font-size: 13px; color: #1a1a2e;">
                            <?= htmlspecialchars($log['ip_address']) ?>
                        </strong>
                    </td>
                    <td>
                        <?php if ($log['nama_cctv']): ?>
                            <?= htmlspecialchars($log['nama_cctv']) ?>
                        <?php else: ?>
                            <span class="no-data">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($log['posisi']): ?>
                            <span style="font-size: 12px;"><?= htmlspecialchars($log['posisi']) ?></span>
                        <?php else: ?>
                            <span class="no-data">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($log['channel'] || $log['nvr']): ?>
                            <span style="font-size: 12px;">
                                <?= htmlspecialchars($log['channel'] ?? '—') ?> / <?= htmlspecialchars($log['nvr'] ?? '—') ?>
                            </span>
                        <?php else: ?>
                            <span class="no-data">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($is_up): ?>
                            <span class="badge-up">UP</span>
                        <?php else: ?>
                            <span class="badge-down">DOWN</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($log['response_time'] && $log['response_time'] !== 'Timeout'): ?>
                            <span class="response-time"><?= htmlspecialchars($log['response_time']) ?></span>
                        <?php else: ?>
                            <span class="response-timeout">Timeout</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size: 12px; color: #666; max-width: 200px; word-break: break-word;">
                        <?= htmlspecialchars($log['info_text'] ?? '—') ?>
                    </td>
                    <td style="font-size: 12px; white-space: nowrap; color: #555;">
                        <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="pagination">
        <span class="pagination-info">
            Menampilkan <b><?= min((($page - 1) * 100) + 1, $total_rows) ?> - <?= min($page * 100, $total_rows) ?></b>
            dari <b><?= number_format($total_rows) ?></b> data
        </span>
        <div class="pagination-links">
            <?php if ($page > 1): ?>
                <a href="<?= site_url('monitoring/cctv/logs') ?>?<?= $base_query ?>&page=1" class="page-btn">« Pertama</a>
                <a href="<?= site_url('monitoring/cctv/logs') ?>?<?= $base_query ?>&page=<?= $page - 1 ?>" class="page-btn">‹ Prev</a>
            <?php endif; ?>

            <?php
            $start_pg = max(1, $page - 2);
            $end_pg   = min($total_pages, $page + 2);
            for ($pg = $start_pg; $pg <= $end_pg; $pg++):
                $active_cls = ($pg == $page) ? 'active' : '';
            ?>
                <a href="<?= site_url('monitoring/cctv/logs') ?>?<?= $base_query ?>&page=<?= $pg ?>" class="page-btn <?= $active_cls ?>">
                    <?= $pg ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="<?= site_url('monitoring/cctv/logs') ?>?<?= $base_query ?>&page=<?= $page + 1 ?>" class="page-btn">Next ›</a>
                <a href="<?= site_url('monitoring/cctv/logs') ?>?<?= $base_query ?>&page=<?= $total_pages ?>" class="page-btn">Terakhir »</a>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    <!-- EMPTY STATE -->
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>Belum Ada Data Log</h3>
        <p>Tidak ditemukan data log dengan filter yang dipilih.</p>
        <p style="margin-top: 10px;">
            <?php if ($filter_ip || $filter_status || $filter_date): ?>
                <a href="<?= site_url('monitoring/cctv/logs') ?>" class="btn-reset" style="display:inline-block; margin-top:10px;">↩ Hapus Filter</a>
            <?php else: ?>
                Jalankan ping dari halaman 
                <a href="<?= site_url('monitoring/cctv/setup') ?>" style="color: #0077b6; font-weight: bold;">Setup Monitoring</a>
                untuk mulai mencatat log.
            <?php endif; ?>
        </p>
    </div>
    <?php endif; ?>
</div>

<script>
(function() {
    // ---- Elemen ----
    const overlay       = document.getElementById('resetModalOverlay');
    const confirmOverlay= document.getElementById('confirmModalOverlay');
    const btnOpen       = document.getElementById('btnOpenReset');
    const btnClose      = document.getElementById('btnCloseReset');
    const btnCancel     = document.getElementById('btnCancelReset');
    const btnProceed    = document.getElementById('btnProceedDelete');
    const chkAll        = document.getElementById('chkSelectAll');
    const checkboxes    = document.querySelectorAll('.vlan-checkbox');
    const confirmList   = document.getElementById('confirmVlanList');
    const btnConfirmYes = document.getElementById('btnConfirmYes');
    const btnConfirmNo  = document.getElementById('btnConfirmNo');

    // ---- Buka / Tutup Modal Reset ----
    function openReset()  { overlay.classList.add('show'); }
    function closeReset() { overlay.classList.remove('show'); }
    function openConfirm()  { confirmOverlay.classList.add('show'); }
    function closeConfirm() { confirmOverlay.classList.remove('show'); }

    btnOpen.addEventListener('click', openReset);
    btnClose.addEventListener('click', closeReset);
    btnCancel.addEventListener('click', closeReset);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeReset(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeReset(); closeConfirm(); } });

    // ---- Select All Logic ----
    chkAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateProceedBtn();
    });
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const all = [...checkboxes].every(c => c.checked);
            const any = [...checkboxes].some(c => c.checked);
            chkAll.checked = all;
            chkAll.indeterminate = any && !all;
            updateProceedBtn();
        });
    });

    function updateProceedBtn() {
        const anyChecked = [...checkboxes].some(c => c.checked);
        btnProceed.disabled = !anyChecked;
    }

    // ---- Klik Hapus → Buka Konfirmasi ----
    btnProceed.addEventListener('click', function() {
        const selected = [...checkboxes].filter(c => c.checked);
        // Bangun daftar nama VLAN di popup konfirmasi
        confirmList.innerHTML = selected.map(c =>
            `<div>• ${c.dataset.name} <span style="font-weight:normal; color:#888;">(${c.value}.xxx)</span></div>`
        ).join('');
        openConfirm();
    });

    btnConfirmNo.addEventListener('click', closeConfirm);
    confirmOverlay.addEventListener('click', e => { if (e.target === confirmOverlay) closeConfirm(); });

    // ---- Konfirmasi Ya → Kirim AJAX ----
    btnConfirmYes.addEventListener('click', function() {
        const selected = [...checkboxes].filter(c => c.checked);
        const vlans    = selected.map(c => c.value);

        btnConfirmYes.disabled = true;
        btnConfirmYes.textContent = '⏳ Menghapus...';

        const formData = new FormData();
        vlans.forEach(v => formData.append('vlans[]', v));

        fetch('<?= site_url('monitoring/cctv/logs/reset') ?>', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                closeConfirm();
                closeReset();
                // Reset checkbox state
                checkboxes.forEach(cb => cb.checked = false);
                chkAll.checked = false;
                updateProceedBtn();
                // Tampilkan notifikasi & reload
                showToast(`✅ Berhasil menghapus ${data.deleted.toLocaleString()} log!`, 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showToast('❌ Gagal: ' + (data.message || 'Terjadi kesalahan.'), 'error');
            }
        })
        .catch(() => showToast('❌ Gagal terhubung ke server.', 'error'))
        .finally(() => {
            btnConfirmYes.disabled = false;
            btnConfirmYes.textContent = 'Ya, Hapus Sekarang';
        });
    });

    // ---- Toast Notifikasi ----
    function showToast(msg, type) {
        const t = document.createElement('div');
        t.textContent = msg;
        t.style.cssText = `
            position: fixed; bottom: 30px; right: 30px; z-index: 99999;
            padding: 14px 22px; border-radius: 10px;
            font-size: 14px; font-weight: bold;
            box-shadow: 0 6px 24px rgba(0,0,0,0.2);
            animation: fadeInUp 0.3s ease;
            color: white;
            background: ${type === 'success' ? 'linear-gradient(135deg,#06d6a0,#028a60)' : 'linear-gradient(135deg,#ef233c,#8b0000)'};
        `;
        const style = document.createElement('style');
        style.textContent = '@keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}';
        document.head.appendChild(style);
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
    }
    // ===== Export Excel Modal Logic =====
    (function() {
        const exOverlay   = document.getElementById('exportModalOverlay');
        const exSuccess   = document.getElementById('exportSuccessOverlay');
        const btnOpen     = document.getElementById('btnOpenExport');
        const btnClose    = document.getElementById('btnCloseExport');
        const btnCancel   = document.getElementById('btnCancelExport');
        const btnDoExport = document.getElementById('btnDoExport');
        const exChkAll    = document.getElementById('exChkAll');
        const exCbs       = document.querySelectorAll('.ex-vlan-cb');
        const btnCloseSuc = document.getElementById('btnCloseSuccess');
        const filenameEl  = document.getElementById('exportedFilename');

        // Set default datetime values (today 00:00 to now)
        const now = new Date();
        const pad = n => String(n).padStart(2,'0');
        const todayStr = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
        const nowStr   = `${todayStr}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
        const startStr = `${todayStr}T00:00`;
        document.getElementById('exportDateStart').value = startStr;
        document.getElementById('exportDateEnd').value   = nowStr;

        function openExport()  { exOverlay.classList.add('show'); }
        function closeExport() { exOverlay.classList.remove('show'); }
        function openSuccess(fname) {
            filenameEl.textContent = fname;
            exSuccess.classList.add('show');
        }
        function closeSuccess() { exSuccess.classList.remove('show'); }

        btnOpen.addEventListener('click', openExport);
        btnClose.addEventListener('click', closeExport);
        btnCancel.addEventListener('click', closeExport);
        btnCloseSuc.addEventListener('click', closeSuccess);
        exOverlay.addEventListener('click', e => { if (e.target === exOverlay) closeExport(); });
        exSuccess.addEventListener('click', e => { if (e.target === exSuccess) closeSuccess(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeExport(); closeSuccess(); } });

        // Select All VLAN logic
        exChkAll.checked = true; // default all selected
        exChkAll.addEventListener('change', function() {
            exCbs.forEach(cb => cb.checked = this.checked);
        });
        exCbs.forEach(cb => {
            cb.addEventListener('change', function() {
                const all = [...exCbs].every(c => c.checked);
                const any = [...exCbs].some(c => c.checked);
                exChkAll.checked = all;
                exChkAll.indeterminate = any && !all;
            });
        });

        // Do Export
        btnDoExport.addEventListener('click', function() {
            const selectedVlans = [...exCbs].filter(c => c.checked).map(c => c.value);
            if (selectedVlans.length === 0) {
                alert('Pilih minimal satu VLAN untuk diekspor.');
                return;
            }

            const dateStart = document.getElementById('exportDateStart').value;
            const dateEnd   = document.getElementById('exportDateEnd').value;

            // Build URL params
            const params = new URLSearchParams();
            if (dateStart) params.append('ex_start', dateStart);
            if (dateEnd)   params.append('ex_end',   dateEnd);
            selectedVlans.forEach(v => params.append('ex_vlans[]', v));

            // Generate filename for display
            const ts = new Date();
            const fname = `monitoring_log_${ts.getFullYear()}${pad(ts.getMonth()+1)}${pad(ts.getDate())}_${pad(ts.getHours())}${pad(ts.getMinutes())}${pad(ts.getSeconds())}.xls`;

            btnDoExport.disabled = true;
            btnDoExport.innerHTML = '⏳ Menyiapkan...';

            // Trigger download via hidden iframe
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = '<?= site_url('monitoring/cctv/logs/export') ?>?' + params.toString();
            document.body.appendChild(iframe);

            // After short delay, assume download started
            setTimeout(() => {
                closeExport();
                openSuccess(fname);
                btnDoExport.disabled = false;
                btnDoExport.innerHTML = '📊 Export Sekarang';
                iframe.remove();
            }, 1800);
        });
    })();

    // ===== Auto Refresh Stats Logic =====
    (function() {
        const totalTodayEl = document.getElementById('val-total-today');
        const upTodayEl = document.getElementById('val-up-today');
        const downTodayEl = document.getElementById('val-down-today');
        const totalFilteredEl = document.getElementById('val-total-filtered');
        const pillsContainer = document.getElementById('recentSessionPills');

        function fetchUpdatedStats() {
            const params = new URLSearchParams(window.location.search);
            fetch('<?= site_url('monitoring/cctv/logs/stats') ?>?' + params.toString())
            .then(r => r.json())
            .then(data => {
                if (totalTodayEl) totalTodayEl.textContent = Number(data.total_today).toLocaleString('id-ID');
                if (upTodayEl) upTodayEl.textContent = Number(data.up_today).toLocaleString('id-ID');
                if (downTodayEl) downTodayEl.textContent = Number(data.down_today).toLocaleString('id-ID');
                if (totalFilteredEl) totalFilteredEl.textContent = Number(data.total_filtered).toLocaleString('id-ID');
                
                // Update session pills if container exists
                if (pillsContainer && data.recent_sessions) {
                    let html = '';
                    data.recent_sessions.forEach(sess => {
                        html += `
                            <a href="<?= site_url('monitoring/cctv/logs') ?>?date=${sess.date_filter}" class="session-pill">
                                🔄 ${sess.date_formatted}
                                &nbsp;|&nbsp;
                                <span class="pill-up">▲ ${sess.up}</span>
                                <span class="pill-down">▼ ${sess.down}</span>
                            </a>
                        `;
                    });
                    pillsContainer.innerHTML = html;
                }
            })
            .catch(err => console.warn('Error fetching updated stats:', err));
        }

        // Jalankan polling setiap 5 detik
        setInterval(fetchUpdatedStats, 5000);
    })();
})();
</script>

<?= $this->endSection() ?>
