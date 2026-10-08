<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .right-frame {
        padding: 20px;
        background-color: #f4f7f6;
        min-height: calc(100vh - 60px);
    }
    
    .prtg-title {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .header-right-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 3px;
    }
    .digital-clock {
        font-size: 18px;
        font-weight: 700;
        font-family: 'Courier New', Courier, monospace;
        color: #0077b6;
        letter-spacing: 1px;
        background: linear-gradient(135deg, #e8f4ff 0%, #cce8ff 100%);
        padding: 4px 11px;
        border-radius: 6px;
        border: 1px solid #b3d9f7;
        box-shadow: 0 1px 6px rgba(0,119,182,0.12);
        min-width: 96px;
        text-align: center;
    }
    .refresh-countdown {
        font-size: 10px;
        color: #555;
        background: #f5f7fa;
        border: 1px solid #dde;
        border-radius: 5px;
        padding: 2px 8px;
        text-align: center;
        min-width: 96px;
    }
    .refresh-countdown span.cd-number {
        font-weight: bold;
        color: #0077b6;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .summary-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        border-bottom: 4px solid #ccc;
        position: relative;
        overflow: hidden;
    }
    
    .summary-card.total { border-color: #3498db; }
    .summary-card.up { border-color: #2ecc71; }
    .summary-card.down { border-color: #e74c3c; }
    .summary-card.unknown { border-color: #f1c40f; }

    .card-title {
        font-size: 14px;
        color: #7f8c8d;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .card-value {
        font-size: 32px;
        font-weight: bold;
        color: #2c3e50;
    }

    .dashboard-main {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 20px;
        margin-bottom: 25px;
    }

    .panel {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .panel-title {
        font-size: 16px;
        font-weight: 700;
        color: #34495e;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #ecf0f1;
    }

    .dash-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .dash-table th, .dash-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ecf0f1;
        font-size: 13px;
    }
    
    .dash-table th {
        background-color: #f8f9fa;
        color: #7f8c8d;
        font-weight: 600;
    }
    
    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
        color: #fff;
    }
    
    .badge-up { background-color: #2ecc71; }
    .badge-down { background-color: #e74c3c; }
    .badge-unknown { background-color: #f1c40f; }

    .vlan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }
    
    .vlan-card {
        background: #fff;
        border: 1px solid #e0e6ed;
        border-radius: 6px;
        padding: 15px;
        display: flex;
        flex-direction: column;
    }
    
    .vlan-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .vlan-name {
        font-weight: bold;
        color: #2c3e50;
        font-size: 15px;
    }
    
    .vlan-ip {
        font-size: 12px;
        color: #7f8c8d;
    }

    .vlan-stats-bar {
        display: flex;
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
        margin-top: 10px;
        background-color: #ecf0f1;
    }
    
    .bar-up { background-color: #2ecc71; }
    .bar-down { background-color: #e74c3c; }
    .bar-unknown { background-color: #f1c40f; }

    .vlan-legend {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        margin-top: 5px;
        color: #7f8c8d;
    }

    @media (max-width: 1024px) {
        .dashboard-main {
            grid-template-columns: 1fr;
        }
    }

    /* Recheck Balloon Panel */
    #recheck-overlay {
        display: none;
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 350px;
        max-width: calc(100vw - 40px);
        background: #fff;
        z-index: 10000;
        flex-direction: column;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
        border: 1px solid #e0e6ed;
        border-left: 5px solid #3498db;
        color: #333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow: hidden;
        animation: slideUpBalloon 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes slideUpBalloon {
        from { transform: translateY(150%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    #recheck-overlay .loader-box {
        padding: 20px;
        position: relative;
    }
    .blinking-dot {
        position: absolute;
        top: 24px;
        right: 20px;
        width: 12px;
        height: 12px;
        background-color: #2ecc71;
        border-radius: 50%;
        animation: blinkDot 1s infinite;
    }
    @keyframes blinkDot {
        0%, 100% { opacity: 1; box-shadow: 0 0 8px #2ecc71; }
        50% { opacity: 0.3; box-shadow: none; }
    }
    #recheck-overlay h2 {
        font-size: 16px;
        margin-top: 0;
        margin-bottom: 5px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    #recheck-overlay .progress-container {
        width: 100%;
        height: 12px;
        background: #ecf0f1;
        border-radius: 6px;
        overflow: hidden;
        margin: 15px 0 10px 0;
    }
    #recheck-overlay .progress-bar {
        width: 0%;
        height: 100%;
        background: linear-gradient(90deg, #3498db, #2ecc71);
        transition: width 0.3s ease;
    }
    #recheck-overlay .progress-text {
        font-size: 13px;
        font-weight: bold;
        color: #555;
        text-align: right;
    }
</style>

<!-- Recheck Overlay -->
<div id="recheck-overlay">
    <div class="loader-box">
        <div class="blinking-dot" title="Sedang berjalan..."></div>
        <h2>🔄 Proses Recheck</h2>
        <p id="recheck-status-text" style="color: #7f8c8d; font-size: 13px; margin:0;">Mengambil data IP...</p>
        <div class="progress-container">
            <div id="recheck-progress-bar" class="progress-bar"></div>
        </div>
        <div id="recheck-progress-text" class="progress-text">0% (0 / 0)</div>
    </div>
</div>

<div class="right-frame">
    <div class="prtg-title">
        <div><span style="font-size:28px;">📊</span> Network Monitoring Dashboard</div>
        <div class="header-right-info">
            <div class="digital-clock" id="digitalClock">00:00:00</div>
            <div class="refresh-countdown" id="refreshCountdown">
                Refresh dalam <span class="cd-number" id="cdNumber"><?= $interval_seconds; ?></span> detik
            </div>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="summary-grid">
        <div class="summary-card total">
            <div class="card-title">Total Devices</div>
            <div class="card-value"><?= $total_devices; ?></div>
        </div>
        <div class="summary-card up">
            <div class="card-title">Online (UP)</div>
            <div class="card-value" style="color: #2ecc71;"><?= $up_count; ?></div>
        </div>
        <div class="summary-card down">
            <div class="card-title">Offline (DOWN)</div>
            <div class="card-value" style="color: #e74c3c;"><?= $down_count; ?></div>
        </div>
        <div class="summary-card unknown">
            <div class="card-title">Unknown / Pending</div>
            <div class="card-value" style="color: #f1c40f;"><?= $unknown_count; ?></div>
        </div>
    </div>

    <!-- MAIN DASHBOARD -->
    <div class="dashboard-main">
        <!-- Chart Panel -->
        <div class="panel">
            <div class="panel-title">Sensor Status Overview</div>
            <div style="position: relative; height:250px; display:flex; justify-content:center;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Offline Devices Panel -->
        <div class="panel">
            <div class="panel-title">⚠️ Devices Currently Down (Top 10)</div>
            <?php if(count($offline_details) > 0): ?>
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Channel</th>
                            <th>IP Address</th>
                            <th>Nama Device</th>
                            <th>Posisi</th>
                            <th>NVR</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($offline_details as $dev): ?>
                        <tr>
                            <td style="text-align:center; font-weight:bold;"><?= $dev['channel'] ? $dev['channel'] : '-'; ?></td>
                            <td>
                                <strong>
                                    <a href="javascript:void(0);" onclick="openPingGraph('<?= esc($dev['ip_address']) ?>')" style="color: #0077b6; text-decoration: underline;" title="Klik untuk Buka Ping Test">
                                        <?= esc($dev['ip_address']); ?>
                                    </a>
                                </strong>
                            </td>
                            <td><?= $dev['nama_cctv']; ?></td>
                            <td><?= $dev['posisi']; ?></td>
                            <td><?= $dev['nvr']; ?></td>
                            <td><span class="badge badge-down">DOWN</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div style="text-align:center; padding: 30px; color:#7f8c8d;">
                <span style="font-size:30px;">✅</span><br>
                Semua device dalam keadaan normal (UP).
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- VLAN STATUS -->
    <div class="panel" style="margin-bottom: 25px;">
        <div class="panel-title">Group / VLAN Status</div>
        <div class="vlan-grid">
            <?php foreach($vlan_stats as $v): 
                $pct_up = ($v['total'] > 0) ? ($v['up'] / $v['total']) * 100 : 0;
                $pct_down = ($v['total'] > 0) ? ($v['down'] / $v['total']) * 100 : 0;
                $pct_unknown = ($v['total'] > 0) ? ($v['unknown'] / $v['total']) * 100 : 0;
            ?>
            <div class="vlan-card">
                <div class="vlan-header">
                    <div class="vlan-name"><?= $v['nama_vlan']; ?></div>
                    <div class="vlan-ip"><?= $v['network_ip']; ?>.x</div>
                </div>
                <div style="font-size:24px; font-weight:bold; color:#2c3e50;">
                    <?= $v['up']; ?> <span style="font-size:12px; color:#7f8c8d; font-weight:normal;">/ <?= $v['total']; ?> UP</span>
                </div>
                <div class="vlan-stats-bar">
                    <div class="bar-up" style="width: <?= $pct_up; ?>%"></div>
                    <div class="bar-down" style="width: <?= $pct_down; ?>%"></div>
                    <div class="bar-unknown" style="width: <?= $pct_unknown; ?>%"></div>
                </div>
                <div class="vlan-legend">
                    <span><span style="color:#2ecc71;">●</span> <?= $v['up']; ?> Up</span>
                    <span><span style="color:#e74c3c;">●</span> <?= $v['down']; ?> Down</span>
                    <span><span style="color:#f1c40f;">●</span> <?= $v['unknown']; ?> Unk</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- RECENT LOGS -->
    <div class="panel">
        <div class="panel-title">Recent Alarms / Logs</div>
        <div style="overflow-x:auto;">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>IP Address</th>
                        <th>Nama Device</th>
                        <th>Event / Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($recent_logs) > 0): ?>
                        <?php foreach($recent_logs as $log): 
                            $badge_class = ($log['status'] == 'UP') ? 'badge-up' : 'badge-down';
                        ?>
                        <tr>
                            <td><?= date('d-m-Y H:i:s', strtotime($log['created_at'])); ?></td>
                            <td><strong><?= $log['ip_address']; ?></strong></td>
                            <td><?= $log['nama_cctv'] ? $log['nama_cctv'] : '-'; ?></td>
                            <td><span class="badge <?= $badge_class; ?>"><?= $log['status']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center;">Belum ada log data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('statusChart').getContext('2d');
    
    const upCount = <?= $up_count; ?>;
    const downCount = <?= $down_count; ?>;
    const unknownCount = <?= $unknown_count; ?>;
    
    if(upCount === 0 && downCount === 0 && unknownCount === 0) {
        document.getElementById('statusChart').style.display = 'none';
        return;
    }

    const data = {
        labels: ['Online (UP)', 'Offline (DOWN)', 'Unknown'],
        datasets: [{
            data: [upCount, downCount, unknownCount],
            backgroundColor: [
                '#2ecc71',
                '#e74c3c',
                '#f1c40f'
            ],
            hoverOffset: 4,
            borderWidth: 0
        }]
    };

    const config = {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            }
        }
    };

    new Chart(ctx, config);
    
    const intervalSec = <?= $interval_seconds; ?>;
    const cdNumberEl = document.getElementById('cdNumber');
    
    let targetTime = sessionStorage.getItem('dashboard_target_refresh');
    let now = Date.now();
    
    if (!targetTime || parseInt(targetTime) <= now) {
        targetTime = now + (intervalSec * 1000);
        sessionStorage.setItem('dashboard_target_refresh', targetTime);
    } else {
        targetTime = parseInt(targetTime);
        if (targetTime > now + (intervalSec * 1000)) {
            targetTime = now + (intervalSec * 1000);
            sessionStorage.setItem('dashboard_target_refresh', targetTime);
        }
    }
    
    let countdownTimerId = null;

    function updateCountdown() {
        let currentNow = Date.now();
        let diffSec = Math.ceil((targetTime - currentNow) / 1000);
        
        if (diffSec <= 0) {
            diffSec = 0;
            if (cdNumberEl) cdNumberEl.textContent = diffSec;
            
            sessionStorage.removeItem('dashboard_target_refresh');
            if (countdownTimerId) clearInterval(countdownTimerId);
            
            let rcEl = document.getElementById('refreshCountdown');
            if (rcEl) {
                rcEl.innerHTML = "Mengecek Status... <span style='font-size:12px;'>⏳</span>";
            }
            
            // Show Overlay
            const overlay = document.getElementById('recheck-overlay');
            const statusText = document.getElementById('recheck-status-text');
            const progressBar = document.getElementById('recheck-progress-bar');
            const progressText = document.getElementById('recheck-progress-text');
            
            overlay.style.display = 'flex';
            
            fetch('<?= site_url('monitoring/getSetupIps') ?>')
            .then(res => res.json())
            .then(async data => {
                if(data.status === 'success' && data.ips && data.ips.length > 0) {
                    const ips = data.ips;
                    const total = ips.length;
                    let completed = 0;
                    
                    statusText.textContent = `Memproses ping ke ${total} perangkat CCTV...`;
                    
                    // Batched sequential ping (batch size: 3 for faster processing without freezing)
                    const batchSize = 3;
                    for (let i = 0; i < total; i += batchSize) {
                        const batch = ips.slice(i, i + batchSize);
                        const promises = batch.map(ip => {
                            return fetch('<?= site_url('monitoring/cekPing') ?>?ip=' + ip)
                                .then(r => r.json())
                                .catch(e => ({})); // Ignore individual errors to continue
                        });
                        
                        await Promise.all(promises);
                        
                        completed += batch.length;
                        const percent = Math.round((completed / total) * 100);
                        progressBar.style.width = percent + '%';
                        progressText.textContent = `${percent}% (${completed} / ${total})`;
                    }
                    
                    statusText.textContent = 'Menyimpan konfigurasi...';
                    await fetch('<?= site_url('monitoring/updateLastRun') ?>');
                    
                    window.location.reload();
                } else {
                    // Fallback to original run if error fetching IPs
                    fetch('<?= site_url('monitoring/cctv/setup/run') ?>?manual=1')
                        .then(() => window.location.reload())
                        .catch(() => window.location.reload());
                }
            })
            .catch(error => {
                window.location.reload();
            });
            
        } else {
            if (cdNumberEl) cdNumberEl.textContent = diffSec;
        }
    }
    
    updateCountdown();
    countdownTimerId = setInterval(updateCountdown, 1000);

    const clockEl = document.getElementById('digitalClock');
    function updateClock() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        if (clockEl) clockEl.textContent = hh + ':' + mm + ':' + ss;
    }
    updateClock();
    setInterval(updateClock, 1000);
    updateClock();

});

function openPingGraph(ip) {
    const url = '<?= site_url('tools/graphs') ?>?ip=' + ip;
    const width = 800;
    const height = 600;
    const left = (window.screen.width / 2) - (width / 2);
    const top = (window.screen.height / 2) - (height / 2);
    window.open(url, 'pingGraphPopup', `width=${width},height=${height},top=${top},left=${left},scrollbars=yes,resizable=yes`);
}
</script>

<?= $this->endSection(); ?>
