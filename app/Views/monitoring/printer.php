<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
    .card h3 { margin-bottom: 15px; color: #333; border-bottom: 2px solid #0077b6; padding-bottom: 8px; display: inline-block; }
    .table-monitoring { width: 100%; border-collapse: collapse; font-size: 14px; }
    .table-monitoring th, .table-monitoring td { padding: 10px; border: 1px solid #ddd; }
    .table-monitoring th { background-color: #f8f9fa; text-align: left; }
    .table-monitoring tbody tr:hover { background-color: #f1f5f9; }
    .status-online { color: #155724; background-color: #d4edda; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
    .status-offline { color: #721c24; background-color: #f8d7da; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
    .source-local { color: #004085; background-color: #cce5ff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
    .source-network { color: #383d41; background-color: #e2e3e5; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
</style>

<div class="right-frame">
    <h2 style="margin-bottom: 20px;">Printer Monitoring System</h2>
    
    <div class="card">
        <h3>💻 PC Klien Terhubung (Agent Aktif)</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Daftar PC yang telah mengirimkan sinyal aktif (heartbeat) dalam waktu dekat.</p>
        
        <table class="table-monitoring">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama PC / Hostname</th>
                    <th>IP Address</th>
                    <th>Terakhir Dilihat</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; if (!empty($clients)): ?>
                    <?php foreach($clients as $c): ?>
                        <?php
                        $last_seen = strtotime($c['last_seen']);
                        $now = time();
                        $diff = round(abs($now - $last_seen) / 60, 2);
                        $is_online = ($diff <= 5);
                        $status_label = $is_online ? '<span class="status-online">Online</span>' : '<span class="status-offline">Offline ('.$diff.' menit lalu)</span>';
                        ?>
                        <tr>
                            <td style='text-align: center;'><?= $no++ ?></td>
                            <td><strong><?= esc($c['pc_name']) ?></strong></td>
                            <td><?= esc($c['ip_address']) ?></td>
                            <td><?= date('d/m/Y H:i:s', $last_seen) ?></td>
                            <td style='text-align: center;'><?= $status_label ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan='5' style='text-align: center; color: #777;'>Belum ada agen PC yang terhubung.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>🖨️ Riwayat Proses Print Terakhir</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Menampilkan 100 log pencetakan terakhir dari seluruh jaringan.</p>
        
        <table class="table-monitoring">
            <thead>
                <tr>
                    <th>Waktu Print</th>
                    <th>Nama Printer</th>
                    <th>Dokumen</th>
                    <th>User</th>
                    <th>Host Pengirim (PC / IP)</th>
                    <th style="text-align: center;">Sumber</th>
                    <th style="text-align: center;">Halaman</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($history)): ?>
                    <?php foreach($history as $h): ?>
                        <?php
                        $waktu = strtotime($h['print_time']);
                        $sumber = esc($h['print_source']);
                        $sumber_class = (strtoupper($sumber) === 'LOCAL') ? 'source-local' : 'source-network';
                        $sumber_html = '<span class="'.$sumber_class.'">'.$sumber.'</span>';
                        
                        $status_job = esc($h['job_status']);
                        $client_info = "<strong>".esc($h['hostname'])."</strong><br><span style='font-size:12px; color:#555;'>".esc($h['client_ip'])."</span>";
                        if (strtoupper($sumber) === 'NETWORK') {
                            $client_info = "<div style='font-size:12px; margin-bottom:4px;'><span style='color:#777;'>Client IP:</span> <strong>".esc($h['hostname'])."</strong> (".esc($h['client_ip']).")</div>";
                            $client_info .= "<div style='font-size:12px;'><span style='color:#777;'>Server PC:</span> <strong>".esc($h['logger_pc'])."</strong></div>";
                        }
                        ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', $waktu) ?></td>
                            <td><strong><?= esc($h['printer_name']) ?></strong></td>
                            <td><?= esc($h['document_name']) ?></td>
                            <td><?= esc($h['user_name']) ?></td>
                            <td><?= $client_info ?></td>
                            <td style='text-align: center;'><?= $sumber_html ?></td>
                            <td style='text-align: center;'><strong><?= esc($h['pages']) ?></strong></td>
                            <td style='text-align: center;'><?= $status_job ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan='8' style='text-align: center; color: #777;'>Belum ada riwayat aktivitas print.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
