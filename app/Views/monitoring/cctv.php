<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .right-frame { display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; position: relative; }
    .header-action { display: flex; justify-content: space-between; align-items: center; margin: 15px 0 20px 0; }
    .vlan-buttons { display: flex; flex-wrap: wrap; gap: 15px; }
    .btn-wrapper { position: relative; display: inline-block; }
    .btn-vlan { display: inline-block; padding: 8px 20px; background-color: #f0f2f5; color: #333; text-decoration: none; border-radius: 4px; font-weight: bold; border: 1px solid #ccc; transition: background-color 0.2s; }
    .btn-vlan:hover { background-color: #d8dadf; }
    .btn-vlan.active { background-color: #0077b6; color: white; border-color: #005f8d; }
    .btn-blink { animation: btnDangerBlink 1s linear infinite !important; border-color: #bd2130 !important; color: white !important; }
    @keyframes btnDangerBlink { 0%, 49% { background-color: #dc3545; } 50%, 100% { background-color: #8b0000; } }

    .btn-tooltip { visibility: hidden; position: absolute; bottom: 135%; left: 50%; transform: translateX(-50%); background-color: rgba(33, 37, 41, 0.98); color: #fff; padding: 12px 15px; border-radius: 8px; font-size: 13px; line-height: 1.6; white-space: nowrap; opacity: 0; transition: opacity 0.3s; z-index: 200; box-shadow: 0 4px 10px rgba(0,0,0,0.4); pointer-events: none; }
    .btn-tooltip::after { content: ""; position: absolute; top: 100%; left: 50%; margin-left: -6px; border-width: 6px; border-style: solid; border-color: rgba(33, 37, 41, 0.98) transparent transparent transparent; }
    .btn-wrapper:hover .btn-tooltip { visibility: visible; opacity: 1; }

    .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(45px, 1fr)); gap: 8px; margin-top: 15px; flex: 1; align-content: start; padding-bottom: 50px; }
    .ip-box { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 0; font-size: 14px; font-weight: bold; border-radius: 6px; border: 2px solid transparent; position: relative; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s; line-height: 1.1; }
    .ip-box .vlan-label { font-size: 12px; font-weight: bold; line-height: 1.2; text-align: center; }
    .ip-box .ch-label { font-size: 10px; font-weight: normal; opacity: 0.85; line-height: 1.1; text-align: center; }
    .ip-box:hover { transform: scale(1.1); z-index: 50; }

    .status-unregistered { background-color: #f0f0f0; color: #a0a0a0; border-color: #ddd; cursor: not-allowed; }
    .status-loading { background-color: #f39c12; color: white; border-color: #e67e22; }
    .status-online { background-color: #28a745; color: white; border-color: #1e7e34; }
    .status-offline { animation: blinkBackground 1s linear infinite; }
    @keyframes blinkBackground { 0%, 49% { background-color: #dc3545; border-color: #bd2130; color: white; } 50%, 100% { background-color: transparent; border-color: transparent; color: transparent; } }

    .blink-circle { width: 12px; height: 12px; background-color: #28a745; border-radius: 50%; margin-right: 8px; box-shadow: 0 0 8px #28a745; transition: transform 0.2s, opacity 0.2s; }
    .blink-circle.blink-active { opacity: 0.2; transform: scale(0.5); }

    .ip-box .tooltiptext { visibility: hidden; width: max-content; min-width: 200px; background-color: rgba(33, 37, 41, 0.98); color: #fff; text-align: left; border-radius: 8px; padding: 12px; position: absolute; z-index: 100; opacity: 0; transition: opacity 0.3s; font-size: 13px; font-weight: normal; line-height: 1.6; pointer-events: none; box-shadow: 0 4px 10px rgba(0,0,0,0.5); bottom: 125%; left: 50%; transform: translateX(-50%); }
    .ip-box .tooltiptext::after { content: ""; position: absolute; top: 100%; left: 50%; margin-left: -6px; border-width: 6px; border-style: solid; border-color: rgba(33, 37, 41, 0.98) transparent transparent transparent; }
    .ip-box:hover .tooltiptext { visibility: visible; opacity: 1; }

    .legend { display: flex; gap: 15px; font-size: 13px; margin-bottom: 15px; }
    .legend div { display: flex; align-items: center; gap: 5px; }
    .legend-box { width: 15px; height: 15px; border-radius: 3px; }

    .live-update-btn { display: flex; align-items: center; font-size: 13px; font-weight: bold; color: #2d7a3a; background: #e8f5e9; padding: 6px 14px; border-radius: 20px; border: 1px solid #c8e6c9; cursor: pointer; transition: background 0.2s, box-shadow 0.2s; user-select: none; }
    .live-update-btn:hover { background: #d0ecd4; box-shadow: 0 2px 8px rgba(40,167,69,0.2); }
    .live-update-btn .gear-icon { margin-left: 7px; font-size: 14px; opacity: 0.7; transition: transform 0.4s; }
    .live-update-btn:hover .gear-icon { transform: rotate(60deg); }

    /* Modal Styling */
    .interval-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(3px); }
    .interval-modal-overlay.show { display: flex; }
    .interval-modal { background: #fff; border-radius: 14px; box-shadow: 0 8px 40px rgba(0,0,0,0.25); padding: 32px 36px 28px 36px; min-width: 340px; max-width: 95vw; animation: modalIn 0.22s cubic-bezier(.4,1.4,.6,1) both; position: relative; }
    @keyframes modalIn { from { opacity: 0; transform: scale(0.85) translateY(30px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    .interval-modal h4 { margin: 0 0 6px 0; font-size: 17px; color: #1a1a2e; display: flex; align-items: center; gap: 8px; }
    .interval-modal .subtitle { font-size: 13px; color: #888; margin-bottom: 22px; }
    .interval-presets { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px; }
    .preset-btn { padding: 10px 0; border-radius: 8px; border: 2px solid #e0e0e0; background: #f8f8f8; font-size: 14px; font-weight: bold; color: #444; cursor: pointer; transition: all 0.15s; text-align: center; }
    .preset-btn:hover { border-color: #0077b6; background: #e8f4ff; color: #0077b6; }
    .preset-btn.selected { border-color: #0077b6; background: #0077b6; color: #fff; box-shadow: 0 2px 8px rgba(0,119,182,0.25); }
    .custom-interval-row { display: flex; align-items: center; gap: 10px; margin-bottom: 22px; background: #f5f7fa; border-radius: 8px; padding: 12px 14px; border: 1px solid #e5e7eb; }
    .custom-interval-row label { font-size: 13px; color: #555; white-space: nowrap; }
    .custom-interval-row input[type=number] { width: 80px; padding: 7px 10px; border-radius: 6px; border: 1.5px solid #ccd; font-size: 15px; font-weight: bold; text-align: center; outline: none; }
    .custom-interval-row input[type=number]:focus { border-color: #0077b6; }
    .custom-interval-row span { font-size: 13px; color: #888; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; }
    .btn-modal-cancel { padding: 9px 22px; border-radius: 7px; border: 1px solid #ddd; background: #f5f5f5; color: #555; font-size: 14px; cursor: pointer; }
    .btn-modal-cancel:hover { background: #e8e8e8; }
    .btn-modal-apply { padding: 9px 22px; border-radius: 7px; border: none; background: #0077b6; color: #fff; font-size: 14px; font-weight: bold; cursor: pointer; }
    .btn-modal-apply:hover { background: #005f8d; box-shadow: 0 4px 14px rgba(0,119,182,0.3); }
    .modal-close-x { position: absolute; top: 14px; right: 16px; background: none; border: none; font-size: 20px; color: #aaa; cursor: pointer; line-height: 1; padding: 2px 6px; border-radius: 4px; }
    .modal-close-x:hover { color: #e53e3e; background: #fff0f0; }
</style>

<div class="interval-modal-overlay" id="intervalModalOverlay">
    <div class="interval-modal">
        <button class="modal-close-x" id="btnCloseModal">&times;</button>
        <h4>&#9881; Pengaturan Live Update</h4>
        <p class="subtitle">Pilih seberapa sering halaman melakukan ping otomatis ke semua perangkat.</p>
        <div class="interval-presets" id="presetButtons">
            <button class="preset-btn" data-val="3">3 detik</button>
            <button class="preset-btn" data-val="5">5 detik</button>
            <button class="preset-btn" data-val="10">10 detik</button>
            <button class="preset-btn" data-val="15">15 detik</button>
            <button class="preset-btn" data-val="30">30 detik</button>
            <button class="preset-btn" data-val="60">1 menit</button>
            <button class="preset-btn" data-val="120">2 menit</button>
            <button class="preset-btn" data-val="300">5 menit</button>
        </div>
        <div class="custom-interval-row">
            <label>&#9998; Custom:</label>
            <input type="number" id="customIntervalInput" min="1" max="3600" placeholder="10">
            <span>detik (1–3600)</span>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-cancel" id="btnCancelModal">Batal</button>
            <button class="btn-modal-apply" id="btnApplyInterval">&#10003; Terapkan</button>
        </div>
    </div>
</div>

<div class="right-frame" id="main-frame">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Monitoring Jaringan CCTV (<?= esc($activeVlanName ?: 'Pilih VLAN') ?> - <?= esc($activeNetwork) ?>.XXX)</h3>
        <div class="live-update-btn" id="liveUpdateBtn">
            <div class="blink-circle"></div>
            <span id="liveUpdateLabel">Live Update (5s)</span>
            <span class="gear-icon">&#9881;</span>
        </div>
    </div>
    
    <div class="header-action">
        <div class="vlan-buttons">
            <?php foreach ($vlanList as $v): ?>
                <?php
                $net = $v['network_ip'];
                $isActive = ($activeNetwork == $net) ? 'active' : '';
                $total_konfigurasi = isset($allCctvIps[$net]) ? count($allCctvIps[$net]) : 0;
                $safe_net_id = str_replace('.', '-', $net);
                ?>
                <div class="btn-wrapper">
                    <a href="<?= site_url('monitoring/cctv?vlan='.$net) ?>" class="btn-vlan <?= $isActive ?>" id="btn-vlan-<?= $safe_net_id ?>">
                        <?= esc($v['nama_vlan']) ?>
                    </a>
                    <div class="btn-tooltip" id="tooltip-<?= $safe_net_id ?>">
                        <strong style="color:#4dabf7; display:block; border-bottom:1px solid #555; padding-bottom:5px; margin-bottom:5px;">Statistik Jaringan</strong>
                        Total Terdaftar: <b><?= $total_konfigurasi ?> IP</b><br>
                        Status Online: <b class="cnt-on" style="color:#2ecc71;">0</b><br>
                        Status Offline: <b class="cnt-off" style="color:#e74c3c;">0</b>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($activeVlanName == 'VLAN-48'): ?>
    <div class="nvr-action" style="margin-bottom: 20px;">
        <div class="nvr-buttons" style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php
            $isAllActive = ($activeNvr == '') ? 'active' : '';
            echo '<a href="'.site_url('monitoring/cctv?vlan='.$activeNetwork).'" class="btn-vlan '.$isAllActive.'" style="'.($isAllActive?'':'background-color:#e2e8f0;').'">Semua NVR</a>';
            foreach($nvrList as $nvr) {
                $nvr_name = esc($nvr['nama_nvr']);
                $isNvrActive = ($activeNvr == $nvr['nama_nvr']) ? 'active' : '';
                echo '<a href="'.site_url('monitoring/cctv?vlan='.$activeNetwork.'&nvr='.urlencode($nvr['nama_nvr'])).'" class="btn-vlan '.$isNvrActive.'" style="'.($isNvrActive?'':'background-color:#e2e8f0;').'">'.$nvr_name.'</a>';
            }
            ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="legend">
        <div><div class="legend-box" style="background: #28a745;"></div> Online Normal</div>
        <div><div class="legend-box" style="background: #dc3545;"></div> Offline / RTO</div>
        <div><div class="legend-box" style="background: #f39c12;"></div> Memeriksa Ping...</div>
        <div><div class="legend-box" style="background: #f0f0f0; border: 1px solid #ddd;"></div> Belum Terdaftar</div>
    </div>
    
    <div class="grid-container">
        <?php
        if ($activeNetwork != '') {
            $ip_base = $activeNetwork . ".";
            for ($i = 1; $i <= 254; $i++) {
                $ip = $ip_base . $i;
                if (isset($cctvList[$ip])) {
                    $cctv = $cctvList[$ip];
                    if ($activeNvr != '' && $cctv['nvr'] != $activeNvr) continue;
                    
                    $tooltip_html = "
                        <div style='border-bottom: 1px solid #555; padding-bottom: 5px; margin-bottom: 5px;'>
                            <strong style='color:#4dabf7; font-size:15px;'>Detail CCTV</strong>
                        </div>
                        <b>IP Address:</b> $ip<br>
                        <b>Channel:</b> ".esc($cctv['channel'])."<br>
                        <b>NVR:</b> ".esc($cctv['nvr'])."<br>
                        <b>Nama CCTV:</b> ".esc($cctv['nama_cctv'])."<br>
                        <b>Posisi:</b> ".esc($cctv['posisi'])."<br>
                        <div style='margin-top: 5px; padding-top: 5px; border-top: 1px solid #555;'>
                            <b>Status:</b> <span class='ping-status' style='color:#f1c40f;'>Checking...</span>
                        </div>
                    ";
                    
                    $net_parts = explode('.', $activeNetwork);
                    $vlan_label = end($net_parts) . '.' . $i;
                    echo "<div class='ip-box cctv-row status-loading' data-ip='$ip'>
                            <span class='vlan-label'>$vlan_label</span>
                            <span class='ch-label'>Ch: ".esc($cctv['channel'])."</span>
                            <div class='tooltiptext'>$tooltip_html</div>
                          </div>";
                } else {
                    if ($activeNvr != '') continue;
                    echo "<div class='ip-box status-unregistered'>$i
                            <div class='tooltiptext'>
                                <strong style='color:#aaa; font-size:14px;'>$ip</strong><br>
                                <em>IP Kosong / Belum ada perangkat terdaftar.</em>
                            </div>
                          </div>";
                }
            }
        }
        ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const allCctvIps = <?= json_encode($allCctvIps) ?>;
        const PING_METHOD = "<?= $pingMethod ?>";
        let isFirstScan = true;
        const pingState = {}; 
        
        for (const [network, ips] of Object.entries(allCctvIps)) {
            ips.forEach(ip => { pingState[ip] = { history: [], displayedState: null }; });
        }

        function determineNewState(ip, currentResult, isInitial) {
            pingState[ip].history.push(currentResult);
            if (pingState[ip].history.length > 3) pingState[ip].history.shift();
            
            let newState = pingState[ip].displayedState;
            if (isInitial || newState === null) {
                newState = currentResult;
            } else {
                if (pingState[ip].history.length === 3 &&
                    pingState[ip].history[0] === pingState[ip].history[1] &&
                    pingState[ip].history[1] === pingState[ip].history[2]) {
                    newState = pingState[ip].history[0];
                }
            }
            pingState[ip].displayedState = newState;
            return newState;
        }

        function doPingAll() {
            const indicator = document.querySelector('.blink-circle');
            if (indicator) {
                indicator.classList.add('blink-active');
                setTimeout(() => indicator.classList.remove('blink-active'), 300);
            }

            for (const [network, ips] of Object.entries(allCctvIps)) {
                let onlineCount = 0;
                let offlineCount = 0;
                const safeNetId = network.replace(/\./g, '-');
                const btnEl = document.getElementById('btn-vlan-' + safeNetId);
                const tooltipEl = document.getElementById('tooltip-' + safeNetId);
                
                if (btnEl) btnEl.classList.remove('btn-blink');

                ips.forEach(ip => {
                    const currentIsFirstScan = isFirstScan;
                    const gridBox = document.querySelector(`.cctv-row[data-ip="${ip}"]`);
                    
                    if (gridBox && currentIsFirstScan) {
                        gridBox.classList.remove('status-online', 'status-offline');
                        gridBox.classList.add('status-loading');
                        const statusText = gridBox.querySelector('.ping-status');
                        if(statusText) statusText.innerHTML = "<span style='color:#f39c12;'>Checking...</span>";
                    }

                    fetch('<?= site_url('monitoring/cekPing') ?>?ip=' + ip + '&t=' + new Date().getTime())
                    .then(response => response.json())
                    .then(data => {
                        const currentResult = (data.status === 'online') ? 'online' : 'offline';
                        const newState = determineNewState(ip, currentResult, currentIsFirstScan);

                        if (gridBox) {
                            gridBox.classList.remove('status-loading', 'status-online', 'status-offline');
                            const statusText = gridBox.querySelector('.ping-status');
                            if(newState === 'online') {
                                gridBox.classList.add('status-online');
                                if(statusText) statusText.innerHTML = "<span style='color:#2ecc71;'>Online (Terhubung)</span>";
                            } else {
                                gridBox.classList.add('status-offline');
                                if(statusText) statusText.innerHTML = "<span style='color:#e74c3c;'>Offline (Terputus)</span>";
                            }
                        }

                        if(newState === 'online') onlineCount++;
                        else offlineCount++;

                        if (tooltipEl) {
                            tooltipEl.querySelector('.cnt-on').innerText = onlineCount;
                            tooltipEl.querySelector('.cnt-off').innerText = offlineCount;
                        }

                        if (offlineCount > 0 && btnEl) btnEl.classList.add('btn-blink');
                    })
                    .catch(error => {
                        const newState = determineNewState(ip, 'offline', currentIsFirstScan);
                        if(newState === 'online') onlineCount++;
                        else offlineCount++;

                        if (tooltipEl) {
                            tooltipEl.querySelector('.cnt-on').innerText = onlineCount;
                            tooltipEl.querySelector('.cnt-off').innerText = offlineCount;
                        }
                        if (offlineCount > 0 && btnEl) btnEl.classList.add('btn-blink');
                        
                        if (gridBox) {
                            gridBox.classList.remove('status-loading', 'status-online', 'status-offline');
                            gridBox.classList.add('status-offline');
                            const statusText = gridBox.querySelector('.ping-status');
                            if(statusText) statusText.innerHTML = "Error Request";
                        }
                    });
                });
            }
            isFirstScan = false;
        }

        function doPingBatch() {
            const indicator = document.querySelector('.blink-circle');
            if (indicator) {
                indicator.classList.add('blink-active');
                setTimeout(() => indicator.classList.remove('blink-active'), 300);
            }

            fetch('<?= site_url('monitoring/cekStatusDb') ?>?t=' + new Date().getTime())
            .then(response => response.json())
            .then(allData => {
                for (const [network, ips] of Object.entries(allCctvIps)) {
                    let onlineCount = 0;
                    let offlineCount = 0;
                    const safeNetId = network.replace(/\./g, '-');
                    const btnEl = document.getElementById('btn-vlan-' + safeNetId);
                    const tooltipEl = document.getElementById('tooltip-' + safeNetId);
                    
                    if (btnEl) btnEl.classList.remove('btn-blink');

                    ips.forEach(ip => {
                        const currentIsFirstScan = isFirstScan;
                        const gridBox = document.querySelector(`.cctv-row[data-ip="${ip}"]`);
                        
                        if (gridBox && currentIsFirstScan) {
                            gridBox.classList.remove('status-online', 'status-offline');
                            gridBox.classList.add('status-loading');
                            const statusText = gridBox.querySelector('.ping-status');
                            if(statusText) statusText.innerHTML = "<span style='color:#f39c12;'>Checking...</span>";
                        }

                        let currentResult = 'offline';
                        if (allData[ip] && allData[ip].status === 'online') {
                            currentResult = 'online';
                        }
                        
                        const newState = currentResult;
                        pingState[ip].displayedState = newState;

                        if (gridBox) {
                            gridBox.classList.remove('status-loading', 'status-online', 'status-offline');
                            const statusText = gridBox.querySelector('.ping-status');
                            
                            if(newState === 'online') {
                                gridBox.classList.add('status-online');
                                if(statusText) statusText.innerHTML = "<span style='color:#2ecc71;'>Online (Terhubung)</span>";
                            } else {
                                gridBox.classList.add('status-offline');
                                if(statusText) statusText.innerHTML = "<span style='color:#e74c3c;'>Offline (Terputus)</span>";
                            }
                        }

                        if(newState === 'online') onlineCount++;
                        else offlineCount++;

                        if (tooltipEl) {
                            tooltipEl.querySelector('.cnt-on').innerText = onlineCount;
                            tooltipEl.querySelector('.cnt-off').innerText = offlineCount;
                        }

                        if (offlineCount > 0 && btnEl) btnEl.classList.add('btn-blink');
                    });
                }
                isFirstScan = false;
            })
            .catch(error => { console.error("Error fetching DB status:", error); });
        }
        
        function triggerPing() {
            if (PING_METHOD === 'batch') doPingBatch();
            else doPingAll();
        }

        let savedInterval = localStorage.getItem('monitoring_rescan_interval');
        let currentIntervalSec = savedInterval ? parseInt(savedInterval) : 5;
        if (isNaN(currentIntervalSec) || currentIntervalSec < 1 || currentIntervalSec > 3600) currentIntervalSec = 5;
        let currentIntervalMs = currentIntervalSec * 1000;
        let pingIntervalId = null;

        function startPingInterval(ms) {
            if (pingIntervalId) clearInterval(pingIntervalId);
            pingIntervalId = setInterval(triggerPing, ms);
        }

        triggerPing();
        startPingInterval(currentIntervalMs);

        // Modal Logic
        const overlay = document.getElementById('intervalModalOverlay');
        const btnOpen = document.getElementById('liveUpdateBtn');
        const btnClose = document.getElementById('btnCloseModal');
        const btnCancel = document.getElementById('btnCancelModal');
        const btnApply = document.getElementById('btnApplyInterval');
        const customInput = document.getElementById('customIntervalInput');
        const presetBtns = document.querySelectorAll('#presetButtons .preset-btn');
        const liveLabel = document.getElementById('liveUpdateLabel');

        function updateLiveLabelText(sec) {
            let labelText;
            if (sec < 60) labelText = 'Live Update (' + sec + 's)';
            else if (sec < 3600) labelText = 'Live Update (' + (sec/60).toFixed(sec%60===0?0:1) + ' mnt)';
            else labelText = 'Live Update (1 jam)';
            liveLabel.textContent = labelText;
        }

        function markPresetActive(sec) {
            presetBtns.forEach(btn => btn.classList.remove('selected'));
            let found = false;
            presetBtns.forEach(btn => {
                if (parseInt(btn.dataset.val) === sec) {
                    btn.classList.add('selected');
                    found = true;
                }
            });
            if (!found) customInput.value = sec;
            else customInput.value = '';
        }

        function openModal() {
            markPresetActive(currentIntervalSec);
            overlay.classList.add('show');
        }

        function closeModal() {
            overlay.classList.remove('show');
        }

        btnOpen.addEventListener('click', openModal);
        btnClose.addEventListener('click', closeModal);
        btnCancel.addEventListener('click', closeModal);

        presetBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                presetBtns.forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                customInput.value = '';
            });
        });

        customInput.addEventListener('input', function() {
            presetBtns.forEach(b => b.classList.remove('selected'));
        });

        btnApply.addEventListener('click', function() {
            let newSec = 0;
            if (customInput.value) {
                newSec = parseInt(customInput.value);
                if (isNaN(newSec) || newSec < 1) newSec = 1;
                if (newSec > 3600) newSec = 3600;
            } else {
                const selectedBtn = document.querySelector('#presetButtons .preset-btn.selected');
                if (selectedBtn) newSec = parseInt(selectedBtn.dataset.val);
                else newSec = 5;
            }

            currentIntervalSec = newSec;
            localStorage.setItem('monitoring_rescan_interval', currentIntervalSec);
            currentIntervalMs = currentIntervalSec * 1000;
            
            updateLiveLabelText(currentIntervalSec);
            startPingInterval(currentIntervalMs);
            closeModal();
        });

        updateLiveLabelText(currentIntervalSec);
    });
</script>
<?= $this->endSection() ?>
