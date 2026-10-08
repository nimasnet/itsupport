<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .right-frame {
        background: #f4f6fa;
        padding: 20px;
        min-height: calc(100vh - 60px);
        overflow-y: auto; 
    }
    .card-container {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .table-responsive {
        max-height: 70vh;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 5px;
    }
    .log-table {
        width: 100%;
        border-collapse: collapse;
    }
    .log-table th, .log-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }
    .log-table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .log-table tr:hover {
        background-color: #f9fbfd;
    }
    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 12px;
        color: white;
        text-transform: uppercase;
        display: inline-block;
        text-align: center;
        min-width: 60px;
    }
    .badge-up { background-color: #28a745; }
    .badge-down { background-color: #dc3545; }
    .empty-log {
        text-align: center;
        color: #777;
        font-style: italic;
        padding: 30px !important;
    }
</style>

<div class="right-frame">
    <div style="margin-bottom: 20px;">
        <h3>💻 Monitoring Status Device (Riwayat Jaringan)</h3>
    </div>
    
    <div class="card-container">
        <h4 style="margin-bottom: 5px;">Log Perubahan Status Perangkat / IP</h4>
        <p style="color:#666; font-size:14px; margin-bottom:20px;">
            Daftar di bawah ini mencatat riwayat kapan suatu alamat IP terputus (DOWN) atau kembali terhubung (UP). 
            Menampilkan maksimal 500 riwayat terbaru.
        </p>
        
        <div class="table-responsive">
            <table class="log-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Waktu</th>
                        <th width="20%">Alamat IP</th>
                        <th width="15%">Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs) && is_array($logs)): ?>
                        <?php $no = 1; foreach ($logs as $log): ?>
                            <?php 
                                $badgeClass = ($log['status'] === 'UP') ? 'badge-up' : 'badge-down';
                                $tanggal = date('d/m/Y', strtotime($log['created_at']));
                                $waktu = date('H:i:s', strtotime($log['created_at']));
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= esc($tanggal) ?></strong></td>
                                <td><?= esc($waktu) ?></td>
                                <td><strong><?= htmlspecialchars($log['ip_address']) ?></strong></td>
                                <td><span class="badge-status <?= $badgeClass ?>"><?= esc($log['status']) ?></span></td>
                                <td><?= htmlspecialchars($log['info_text']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="empty-log">Belum ada riwayat status (log kosong). Pastikan Anda telah melakukan pemantauan jaringan di halaman Graphs.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
