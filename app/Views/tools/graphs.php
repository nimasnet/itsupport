<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Sertakan Library Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .right-frame {
        display: flex;
        flex-direction: column;
        background: #f4f6fa;
        padding: 20px;
        min-height: calc(100vh - 60px);
    }
    
    .card-container {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .target-form {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .target-form input[type="text"] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 250px;
        font-size: 15px;
        font-weight: bold;
    }
    
    .target-form button {
        padding: 10px 20px;
        background-color: #0077b6;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.2s;
    }
    
    .target-form button:hover {
        background-color: #005f8d;
    }
    
    .chart-wrapper {
        width: 100%;
        height: 350px;
        position: relative;
    }
    
    .log-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .log-table th, .log-table td {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }
    
    .log-table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: bold;
        position: sticky;
        top: 0;
    }
    
    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 12px;
        color: white;
    }
    
    .badge-up { background-color: #28a745; }
    .badge-down { background-color: #dc3545; }
    
    .empty-log {
        text-align: center;
        color: #777;
        font-style: italic;
        padding: 20px !important;
    }
</style>

<div class="right-frame">
    <h3>📈 Real-time Network Ping Graph</h3><br>
    
    <div class="card-container">
        <form method="GET" action="<?= site_url('tools/graphs') ?>" class="target-form">
            <label for="ip"><strong>IP Target Monitor:</strong></label>
            <input type="text" id="ip" name="ip" value="<?= esc($target_ip) ?>" placeholder="Contoh: 8.8.8.8" required>
            <button type="submit">Ubah Target IP</button>
            <span id="current-status-indicator" style="margin-left:auto; font-weight:bold; font-size:16px;">Memeriksa...</span>
        </form>
        
        <div class="chart-wrapper">
            <canvas id="pingChart"></canvas>
        </div>
    </div>
    
    <div class="card-container">
        <h4 style="margin-bottom:15px; color:#0077b6; border-bottom:2px solid #f2f2f2; padding-bottom:8px;">
            Log Aktivitas Jaringan Tersimpan
        </h4>
        <div style="max-height: 250px; overflow-y: auto; border: 1px solid #eee; border-radius: 4px;">
            <table class="log-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Waktu (Jam & Tanggal)</th>
                        <th width="20%">Status</th>
                        <th>Keterangan Tambahan</th>
                    </tr>
                </thead>
                <tbody id="logTableBody">
                    <tr id="emptyRow"><td colspan="4" class="empty-log">Log akan muncul secara otomatis ketika terjadi perubahan status (UP / DOWN).</td></tr>
                </tbody>
            </table>
        </div>
        <p style="font-size: 12px; color: #777; margin-top: 10px;">*Log perubahan status disimpan otomatis ke database melalui controller ping secara real-time.</p>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const targetIp = "<?= esc($target_ip) ?>";
        const pingUrl = "<?= site_url('monitoring/cekPing') ?>";
        let lastStatus = null;
        let logCounter = 1;
        
        // 1. Inisialisasi Chart.js
        const ctx = document.getElementById('pingChart').getContext('2d');
        const pingChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Latency (ms)',
                    data: [],
                    borderColor: '#0077b6',
                    backgroundColor: 'rgba(0, 119, 182, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#0077b6',
                    pointRadius: 3,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 0 },
                scales: {
                    x: { display: true, title: { display: true, text: 'Waktu' } },
                    y: { display: true, title: { display: true, text: 'Milisecond (ms)' }, min: 0, suggestedMax: 100 }
                },
                plugins: { legend: { display: false } }
            }
        });

        function getCurrentDateTimeStr() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const d = String(now.getDate()).padStart(2, '0');
            const mo = String(now.getMonth() + 1).padStart(2, '0');
            const y = now.getFullYear();
            return `${h}:${m}:${s} - ${d}/${mo}/${y}`;
        }
        
        function getShortTimeStr() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        function addLogEntry(status) {
            const tbody = document.getElementById('logTableBody');
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) emptyRow.remove();
            
            const tr = document.createElement('tr');
            const dateTimeStr = getCurrentDateTimeStr();
            
            let badgeClass = '';
            let statusText = '';
            let infoText = '';
            
            if (status === 'offline') {
                badgeClass = 'badge-down';
                statusText = 'DOWN';
                infoText = 'Koneksi ke ' + targetIp + ' terputus (Request Timed Out).';
            } else if (status === 'online') {
                badgeClass = 'badge-up';
                statusText = 'UP';
                infoText = 'Koneksi ke ' + targetIp + ' kembali tersambung normal.';
            }

            tr.innerHTML = `
                <td><span style="color:green;font-weight:bold;">Baru</span></td>
                <td><strong>${dateTimeStr}</strong></td>
                <td><span class="badge-status ${badgeClass}">${statusText}</span></td>
                <td>${infoText}</td>
            `;
            
            tbody.insertBefore(tr, tbody.firstChild);
        }

        function fetchPingData() {
            fetch(`${pingUrl}?ip=${targetIp}`)
                .then(response => response.json())
                .then(data => {
                    const currentStatus = data.status; // 'online' atau 'offline'
                    const latencyStr = data.response_time;
                    let latencyNum = 0;
                    
                    if (latencyStr && latencyStr !== 'Timeout' && latencyStr !== 'N/A') {
                        latencyNum = parseFloat(latencyStr.replace('ms', ''));
                    }
                    
                    const indicator = document.getElementById('current-status-indicator');
                    if (currentStatus === 'online') {
                        indicator.innerHTML = `<span style="color:#28a745;">&#9679; ONLINE (${latencyStr})</span>`;
                    } else {
                        indicator.innerHTML = `<span style="color:#dc3545;">&#9679; OFFLINE (RTO)</span>`;
                    }

                    if (lastStatus !== null && lastStatus !== currentStatus) {
                        addLogEntry(currentStatus);
                    }
                    lastStatus = currentStatus;

                    const timeLabel = getShortTimeStr();
                    
                    pingChart.data.labels.push(timeLabel);
                    pingChart.data.datasets[0].data.push(currentStatus === 'online' ? latencyNum : 0);

                    if (pingChart.data.labels.length > 20) {
                        pingChart.data.labels.shift();
                        pingChart.data.datasets[0].data.shift();
                    }
                    
                    if (currentStatus === 'offline') {
                        pingChart.data.datasets[0].borderColor = '#dc3545';
                        pingChart.data.datasets[0].backgroundColor = 'rgba(220, 53, 69, 0.1)';
                        pingChart.data.datasets[0].pointBackgroundColor = '#dc3545';
                    } else {
                        pingChart.data.datasets[0].borderColor = '#0077b6';
                        pingChart.data.datasets[0].backgroundColor = 'rgba(0, 119, 182, 0.1)';
                        pingChart.data.datasets[0].pointBackgroundColor = '#0077b6';
                    }

                    pingChart.update();
                })
                .catch(error => console.error('Error fetching ping data:', error));
        }

        fetchPingData();
        setInterval(fetchPingData, 5000);
    });
</script>

<?= $this->endSection() ?>
