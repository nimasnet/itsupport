<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Memuat library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

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
    
    .filter-group {
        display: flex;
        gap: 15px;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .filter-group select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        min-width: 200px;
        font-size: 14px;
    }
    
    .filter-group button {
        padding: 10px 20px;
        background-color: <?= $top_color ?>;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: opacity 0.2s;
    }
    
    .filter-group button:hover {
        opacity: 0.9;
    }
    
    .chart-wrapper {
        width: 100%;
        height: 450px;
        position: relative;
    }
</style>

<div class="right-frame">
    <h3>📊 CCTV Status Statistik (Sering Offline)</h3><br>
    
    <div class="card-container">
        <div class="filter-group">
            <label for="interval"><strong>Waktu Interval:</strong></label>
            <select id="interval">
                <option value="1">1 Hari (Sejak jam 08:00)</option>
                <option value="7">7 Hari Terakhir</option>
                <option value="15">15 Hari Terakhir</option>
                <option value="30">30 Hari Terakhir</option>
            </select>
            <button type="button" id="btnRefresh" onclick="loadChartData()">🔄 Manual Refresh (Ambil Data)</button>
            <span id="loading-indicator" style="display: none; color: #666; font-weight: bold;">Loading data...</span>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
            <p style="color: #555; margin: 0;">
                Menampilkan <strong>Top <span id="display-limit-text">10</span> CCTV (VLAN 48 & 57)</strong> yang paling sering mengalami status offline (DOWN) dalam interval waktu yang dipilih.
                <br>
                <small id="date-range-info" style="color: #888;"></small>
            </p>
            
            <div style="display: flex; align-items: center; gap: 10px;">
                <label for="limit" style="font-weight: bold; font-size: 14px; margin: 0;">Tampilkan Data:</label>
                <select id="limit" style="padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <div class="chart-wrapper">
            <canvas id="cctvStatsChart"></canvas>
        </div>
    </div>
</div>

<script>
    let statsChart = null;

    function initChart() {
        const ctx = document.getElementById('cctvStatsChart').getContext('2d');
        statsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Frekuensi Offline (Kali)',
                    data: [],
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: '#dc3545',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Terputus (DOWN)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Nama / IP CCTV'
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' kali terputus';
                            }
                        }
                    }
                }
            }
        });
    }

    function loadChartData() {
        const interval = $('#interval').val();
        const limit = $('#limit').val();
        
        $('#loading-indicator').show();
        $('#btnRefresh').prop('disabled', true);

        $.ajax({
            url: "<?= site_url('monitoring/cctv/get-stats') ?>",
            type: "POST",
            data: { interval: interval, limit: limit },
            dataType: "json",
            success: function(res) {
                $('#loading-indicator').hide();
                $('#btnRefresh').prop('disabled', false);
                
                if (res.status === 'success') {
                    // Update chart data
                    statsChart.data.labels = res.labels;
                    statsChart.data.datasets[0].data = res.data;
                    statsChart.update();
                    
                    // Update text info
                    $('#display-limit-text').text(res.limit);
                    $('#date-range-info').text('Data diambil mulai dari: ' + res.date_from);
                } else {
                    alert('Gagal mengambil data statistik.');
                }
            },
            error: function() {
                $('#loading-indicator').hide();
                $('#btnRefresh').prop('disabled', false);
                alert('Terjadi kesalahan jaringan.');
            }
        });
    }

    $(document).ready(function() {
        initChart();
        loadChartData();
        
        // Auto reload on interval change
        $('#interval, #limit').change(function() {
            loadChartData();
        });
    });
</script>

<?= $this->endSection() ?>
