<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEB SERVICE - NVR Viewer</title>
    <!-- Kita tidak menggunakan layout/main agar bisa full screen seperti NVR asli -->
    <style>
        body, html {
            margin: 0; padding: 0; height: 100%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #2b2b2b; color: #fff; overflow: hidden;
        }
        
        /* Top Navigation */
        .top-nav {
            background: linear-gradient(to bottom, #5a5a5a, #333333);
            height: 45px; display: flex; align-items: center; border-bottom: 1px solid #111;
        }
        .top-nav .logo {
            font-size: 20px; font-weight: bold; font-style: italic;
            padding: 0 15px; color: #f0f0f0; letter-spacing: 1px;
            width: 220px; box-sizing: border-box; text-shadow: 1px 1px 2px #000;
        }
        .nav-tabs {
            display: flex; height: 100%;
        }
        .nav-tab {
            padding: 0 25px; display: flex; align-items: center;
            border-right: 1px solid #222; border-left: 1px solid #666;
            cursor: pointer; font-size: 13px; font-weight: bold;
            color: #ccc; text-decoration: none;
            background: linear-gradient(to bottom, #4a4a4a, #3a3a3a);
        }
        .nav-tab.active {
            background: linear-gradient(to bottom, #d4d4d4, #b0b0b0); 
            color: #111; box-shadow: inset 0 2px 5px rgba(0,0,0,0.3);
            text-shadow: none;
        }
        .nav-tab:hover:not(.active) { background: linear-gradient(to bottom, #555, #444); color: #fff; }
        
        /* Main Container */
        .main-container { display: flex; height: calc(100% - 75px); }
        
        /* Left Sidebar (Channel List) */
        .left-sidebar {
            width: 250px; background: #383838; border-right: 1px solid #111;
            display: flex; flex-direction: column; box-shadow: 2px 0 5px rgba(0,0,0,0.5);
            z-index: 10;
        }
        .sidebar-header {
            background: linear-gradient(to right, #444, #333); padding: 8px 12px;
            font-size: 12px; font-weight: bold; border-bottom: 1px solid #222;
            color: #ddd; display: flex; justify-content: space-between;
        }
        .channel-list {
            flex: 1; overflow-y: auto; padding: 5px 0; background: #333;
        }
        .nvr-group { margin-bottom: 2px; }
        .nvr-title {
            padding: 8px 10px; font-size: 12px; color: #ccc; font-weight: bold;
            cursor: pointer; display: flex; align-items: center;
            background: #2a2a2a; border-bottom: 1px solid #222;
        }
        .nvr-title:hover { background: #3a3a3a; color: #fff; }
        .cam-item {
            padding: 6px 10px 6px 25px; font-size: 11px; color: #aaa;
            cursor: pointer; display: flex; align-items: center; 
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            border-bottom: 1px solid #2a2a2a;
        }
        .cam-item:hover { background: #444; color: #fff; }
        .cam-icon {
            width: 14px; height: 10px; background: #666; margin-right: 8px;
            display: inline-block; border-radius: 2px; position: relative;
        }
        .cam-icon::after {
            content: ''; position: absolute; right: -3px; top: 2px;
            border-top: 3px solid transparent; border-bottom: 3px solid transparent;
            border-left: 4px solid #666;
        }
        .cam-item.playing { color: #00ff00; font-weight: bold; }
        .cam-item.playing .cam-icon { background: #00ff00; }
        .cam-item.playing .cam-icon::after { border-left-color: #00ff00; }

        .left-bottom-buttons {
            padding: 10px; display: flex; flex-direction: column; gap: 5px;
            border-top: 1px solid #222; background: #2b2b2b;
        }
        .btn-gray {
            background: linear-gradient(to bottom, #eee, #ccc);
            border: 1px solid #888; border-radius: 2px; padding: 6px;
            font-size: 11px; cursor: pointer; color: #222; font-weight: bold;
            text-align: center; box-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }
        .btn-gray:hover { background: linear-gradient(to bottom, #fff, #ddd); }
        .btn-gray:active { background: #ccc; box-shadow: inset 0 1px 3px rgba(0,0,0,0.5); }

        /* Center Video Grid */
        .video-area {
            flex: 1; display: flex; flex-direction: column; background: #000;
            padding: 2px;
        }
        .video-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(2, 1fr);
            gap: 2px; flex: 1;
        }
        .video-cell {
            background: #111; border: 1px solid #333; position: relative;
            display: flex; justify-content: center; align-items: center;
            overflow: hidden;
        }
        .video-cell.active {
            border: 1px solid #00ff00; box-shadow: inset 0 0 5px #00ff00;
        }
        .cell-title {
            position: absolute; bottom: 5px; left: 10px; color: #00ff00;
            font-family: monospace; font-size: 14px; text-shadow: 1px 1px 2px #000;
            background: rgba(0,0,0,0.5); padding: 2px 5px; z-index: 5;
        }
        .cell-overlay-top {
            position: absolute; top: 0; right: 0; padding: 2px 5px;
            display: none; background: rgba(0,0,0,0.7); z-index: 10;
        }
        .video-cell:hover .cell-overlay-top { display: block; }
        .icon-btn { cursor: pointer; font-size: 12px; color: #ccc; margin-left: 8px; }
        .icon-btn:hover { color: #fff; }
        
        .empty-icon {
            width: 60px; height: 60px; border-radius: 50%; border: 3px solid #333;
            display: flex; justify-content: center; align-items: center; color: #333;
            font-size: 30px; background: #1a1a1a;
        }

        /* Right Sidebar (PTZ) */
        .right-sidebar {
            width: 260px; background: #383838; border-left: 1px solid #111;
            display: flex; flex-direction: column; box-shadow: -2px 0 5px rgba(0,0,0,0.5);
            z-index: 10;
        }
        .ptz-area {
            padding: 15px; display: flex; flex-direction: column; align-items: center;
        }
        .ptz-circle {
            position: relative; width: 140px; height: 140px; background: #222;
            border-radius: 50%; border: 2px solid #111; box-shadow: inset 0 5px 15px rgba(0,0,0,0.8);
            margin-bottom: 25px; margin-top: 10px;
        }
        .ptz-btn {
            position: absolute; width: 32px; height: 32px; background: linear-gradient(to bottom, #555, #333);
            border-radius: 50%; border: 1px solid #111; display: flex;
            justify-content: center; align-items: center; cursor: pointer;
            color: #ccc; font-size: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .ptz-btn:hover { background: linear-gradient(to bottom, #666, #444); color: #fff; }
        .ptz-btn:active { background: #222; box-shadow: inset 0 2px 4px rgba(0,0,0,0.8); }
        .ptz-up { top: 10px; left: 52px; }
        .ptz-down { bottom: 10px; left: 52px; }
        .ptz-left { top: 52px; left: 10px; }
        .ptz-right { top: 52px; right: 10px; }
        .ptz-center { 
            top: 47px; left: 47px; width: 42px; height: 42px; 
            background: linear-gradient(to bottom, #444, #222); 
        }
        .ptz-up-left { top: 20px; left: 20px; width: 24px; height: 24px; font-size: 10px;}
        .ptz-up-right { top: 20px; right: 20px; width: 24px; height: 24px; font-size: 10px;}
        .ptz-down-left { bottom: 20px; left: 20px; width: 24px; height: 24px; font-size: 10px;}
        .ptz-down-right { bottom: 20px; right: 20px; width: 24px; height: 24px; font-size: 10px;}
        
        .ptz-controls { width: 100%; font-size: 12px; color: #ccc; margin-bottom: 10px; }
        .ptz-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .ptz-row-btns { display: flex; gap: 5px; }
        .circle-btn {
            width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(to bottom, #555, #333);
            border: 1px solid #111; color: #ccc; display: flex; justify-content: center;
            align-items: center; cursor: pointer; font-weight: bold; box-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .circle-btn:hover { background: linear-gradient(to bottom, #666, #444); color: #fff; }
        .circle-btn:active { background: #222; box-shadow: inset 0 2px 4px rgba(0,0,0,0.8); }
        
        .ptz-tabs {
            display: flex; width: 100%; border-bottom: 1px solid #222; margin-top: 5px; margin-bottom: 15px;
        }
        .ptz-tab { flex: 1; text-align: center; padding: 6px; font-size: 11px; cursor: pointer; color: #aaa; background: #2b2b2b; }
        .ptz-tab.active { background: #383838; border-top: 2px solid #aaa; color: #fff; font-weight: bold; }
        
        .image-controls {
            padding: 0 15px 15px 15px;
        }
        .slider-row { display: flex; align-items: center; margin-bottom: 10px; gap: 10px; }
        .slider-icon { font-size: 14px; width: 15px; text-align: center; color: #aaa;}
        input[type=range] { flex: 1; accent-color: #888; }
        
        /* Bottom Control Bar */
        .bottom-bar {
            height: 30px; background: linear-gradient(to bottom, #4a4a4a, #2a2a2a); 
            border-top: 1px solid #111; display: flex; align-items: center; padding: 0 15px; gap: 15px;
        }
        .layout-btn {
            width: 22px; height: 16px; border: 1px solid #111; background: #222;
            cursor: pointer; display: flex; flex-wrap: wrap; padding: 1px; box-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }
        .layout-btn:hover { border-color: #888; }
        .layout-btn div { background: #666; border: 1px solid #222; box-sizing: border-box; }
        .layout-1 div { width: 100%; height: 100%; }
        .layout-4 div { width: 50%; height: 50%; }
        .layout-9 div { width: 33.3%; height: 33.3%; }
        .layout-16 div { width: 25%; height: 25%; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #222; }
        ::-webkit-scrollbar-thumb { background: #555; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #777; }
    </style>
</head>
<body>

    <div class="top-nav">
        <div class="logo"><i>WEB</i> SERVICE</div>
        <div class="nav-tabs">
            <div class="nav-tab active">Preview</div>
            <div class="nav-tab" onclick="alert('Playback rekaman hanya tersedia langsung di NVR fisik.')">Playback</div>
            <div class="nav-tab" onclick="alert('Menu Alarm belum dikonfigurasi.')">Alarm</div>
            <div class="nav-tab" onclick="alert('Akses Setting NVR dibatasi untuk alasan keamanan.')">SETTING</div>
            <div class="nav-tab" onclick="alert('Info Sistem NVR Proxy.')">INFO</div>
            <a href="<?= site_url('monitoring/cctv') ?>" class="nav-tab" style="margin-left: auto; border-left: 1px solid #222; border-right: none;">Keluar ke Dashboard</a>
        </div>
    </div>

    <div class="main-container">
        <!-- Left Sidebar -->
        <div class="left-sidebar">
            <div class="sidebar-header">
                <span>Channel</span>
                <span style="cursor:pointer;" title="Refresh" onclick="window.location.reload()">🔄</span>
            </div>
            <div class="channel-list">
                <?php 
                // Mapping Data NVR
                $nvrIds = []; $nvrTypes = [];
                foreach($nvrList as $nvr) {
                    $nvrIds[$nvr['nama_nvr']] = $nvr['id'];
                    $nvrTypes[$nvr['nama_nvr']] = $nvr['stream_type'] ?? 'mjpeg';
                }
                ?>
                
                <?php foreach($cctvByNvr as $nvrName => $channels): ?>
                <?php 
                    $nvrId = $nvrIds[$nvrName] ?? 0; 
                    $nvrType = $nvrTypes[$nvrName] ?? 'mjpeg';
                ?>
                <div class="nvr-group">
                    <div class="nvr-title" onclick="toggleNvr(this)">▼ <?= esc($nvrName) ?></div>
                    <div class="nvr-channels">
                    <?php foreach($channels as $c): ?>
                    <?php 
                        $streamData = htmlspecialchars(json_encode([
                            'id' => $c['id'],
                            'channel' => $c['channel'],
                            'nama' => $c['nama'],
                            'type' => $c['type'] ?? $nvrType,
                            'nvr_name' => $nvrName
                        ]), ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="cam-item" id="cam-item-<?= $c['id'] ?>" onclick="assignStream(this, '<?= $streamData ?>')">
                        <span class="cam-icon"></span>
                        CH<?= esc($c['channel']) ?> - <?= esc($c['nama']) ?>
                    </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="left-bottom-buttons">
                <div class="btn-gray" onclick="autoFillGrid()">Open All</div>
                <div class="btn-gray" onclick="alert('Fitur Audio 2 Arah (Start Talk) membutuhkan plugin asli.')">Start Talk ▾</div>
                <div class="btn-gray" onclick="alert('Recording dinonaktifkan di mode proxy.')">Instant Record</div>
                <div class="btn-gray" onclick="alert('Gunakan tab Playback.')">Local Play</div>
                <div class="btn-gray" onclick="changeGrid(4)">Multi Preview</div>
            </div>
        </div>

        <!-- Center Grid -->
        <div class="video-area">
            <div class="video-grid" id="video-grid">
                <!-- Cells generated via JS -->
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="right-sidebar">
            <div class="ptz-area">
                <div class="ptz-circle">
                    <div class="ptz-btn ptz-up" onclick="alert('PTZ Pan Up: Tidak didukung mode proxy.')">▲</div>
                    <div class="ptz-btn ptz-down" onclick="alert('PTZ Pan Down: Tidak didukung mode proxy.')">▼</div>
                    <div class="ptz-btn ptz-left" onclick="alert('PTZ Pan Left: Tidak didukung mode proxy.')">◀</div>
                    <div class="ptz-btn ptz-right" onclick="alert('PTZ Pan Right: Tidak didukung mode proxy.')">▶</div>
                    
                    <div class="ptz-btn ptz-up-left" onclick="alert('PTZ')">↖</div>
                    <div class="ptz-btn ptz-up-right" onclick="alert('PTZ')">↗</div>
                    <div class="ptz-btn ptz-down-left" onclick="alert('PTZ')">↙</div>
                    <div class="ptz-btn ptz-down-right" onclick="alert('PTZ')">↘</div>
                    
                    <div class="ptz-btn ptz-center" onclick="alert('Center')"></div>
                </div>
                
                <div class="ptz-controls">
                    <div class="ptz-row">
                        <span>Speed(1-8):</span>
                        <select style="width: 60px; background: #ddd; padding: 2px;"><option>5</option><option>8</option></select>
                    </div>
                    <div class="ptz-row">
                        <div class="ptz-row-btns"><div class="circle-btn" onclick="alert('Zoom Out')">-</div></div>
                        <span>Zoom</span>
                        <div class="ptz-row-btns"><div class="circle-btn" onclick="alert('Zoom In')">+</div></div>
                    </div>
                    <div class="ptz-row">
                        <div class="ptz-row-btns"><div class="circle-btn" onclick="alert('Focus Near')">-</div></div>
                        <span>Focus</span>
                        <div class="ptz-row-btns"><div class="circle-btn" onclick="alert('Focus Far')">+</div></div>
                    </div>
                    <div class="ptz-row">
                        <div class="ptz-row-btns"><div class="circle-btn" onclick="alert('Iris Close')">-</div></div>
                        <span>Iris</span>
                        <div class="ptz-row-btns"><div class="circle-btn" onclick="alert('Iris Open')">+</div></div>
                    </div>
                </div>
                
                <div class="ptz-tabs">
                    <div class="ptz-tab active">PTZ Setup</div>
                    <div class="ptz-tab">PTZ Menu</div>
                </div>
                
                <div style="width:100%; display:flex; gap:5px; margin-bottom: 8px;">
                    <select style="flex:1; padding:4px; background: #ddd;"><option>Scan</option><option>Preset</option></select>
                </div>
                <div style="display:flex; gap:8px; width:100%;">
                    <div class="btn-gray" style="flex:1;">Start</div>
                    <div class="btn-gray" style="flex:1;">Set</div>
                </div>
            </div>
            
            <div class="image-controls">
                <div class="ptz-tabs" style="margin-top:0;">
                    <div class="ptz-tab active">Image</div>
                    <div class="ptz-tab">Alarm Out</div>
                </div>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div class="slider-row"><span class="slider-icon">☀️</span><input type="range" min="1" max="100" value="50"></div>
                    <div class="slider-row"><span class="slider-icon">🌗</span><input type="range" min="1" max="100" value="50"></div>
                    <div class="slider-row"><span class="slider-icon">🎨</span><input type="range" min="1" max="100" value="50"></div>
                    <div class="slider-row"><span class="slider-icon">💡</span><input type="range" min="1" max="100" value="50"></div>
                </div>
                <div style="text-align:right; margin-top:15px;">
                    <div class="btn-gray" style="display:inline-block; padding:4px 15px;">Reset</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Bar -->
    <div class="bottom-bar">
        <div class="layout-btn layout-1" onclick="changeGrid(1)" title="1 Window"><div></div></div>
        <div class="layout-btn layout-4" onclick="changeGrid(4)" title="4 Windows"><div></div><div></div><div></div><div></div></div>
        <div class="layout-btn layout-9" onclick="changeGrid(9)" title="9 Windows"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
        <div class="layout-btn layout-16" onclick="changeGrid(16)" title="16 Windows"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
        
        <div style="flex:1"></div>
        <div style="color:#aaa; font-size:11px; margin-right:10px;">CPU: <span id="cpu-load">12%</span> | RAM: <span id="ram-load">35%</span> | Status: Connected</div>
    </div>

<script>
    let activeCellIndex = 0;
    let gridCount = 4;
    let gridConfig = [];

    function toggleNvr(element) {
        const list = element.nextElementSibling;
        if (list.style.display === 'none') {
            list.style.display = 'block';
            element.innerText = '▼ ' + element.innerText.substring(2);
        } else {
            list.style.display = 'none';
            element.innerText = '▶ ' + element.innerText.substring(2);
        }
    }

    function initGrid(count) {
        gridCount = count;
        const grid = document.getElementById('video-grid');
        grid.innerHTML = '';
        
        let cols = 1;
        if (count == 4) cols = 2;
        if (count == 9) cols = 3;
        if (count == 16) cols = 4;
        
        grid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
        grid.style.gridTemplateRows = `repeat(${cols}, 1fr)`;
        
        for (let i = 0; i < count; i++) {
            const cell = document.createElement('div');
            cell.className = 'video-cell' + (i === activeCellIndex ? ' active' : '');
            cell.id = `cell-${i}`;
            cell.onclick = () => setActiveCell(i);
            
            cell.innerHTML = `
                <div class="cell-title" id="title-${i}" style="display:none;"></div>
                <div class="cell-overlay-top">
                    <span class="icon-btn" onclick="clearCell(${i}); event.stopPropagation();" title="Tutup Layar">✖</span>
                </div>
                <div id="player-${i}" style="width:100%; height:100%; display:flex; justify-content:center; align-items:center;">
                    <div class="empty-icon">📷</div>
                </div>
            `;
            grid.appendChild(cell);
            
            if (gridConfig[i]) {
                renderPlayer(i, gridConfig[i]);
            }
        }
    }

    function setActiveCell(index) {
        document.querySelectorAll('.video-cell').forEach(c => c.classList.remove('active'));
        activeCellIndex = index;
        const cell = document.getElementById(`cell-${index}`);
        if(cell) cell.classList.add('active');
    }

    function changeGrid(count) {
        initGrid(count);
    }
    
    function clearCell(index) {
        const oldData = gridConfig[index];
        if (oldData) {
            const item = document.getElementById(`cam-item-${oldData.id}`);
            if (item) item.classList.remove('playing');
        }
        
        gridConfig[index] = null;
        document.getElementById(`player-${index}`).innerHTML = `<div class="empty-icon">📷</div>`;
        document.getElementById(`title-${index}`).style.display = 'none';
    }

    function assignStream(element, dataStr) {
        try {
            const data = JSON.parse(dataStr);
            
            // Hapus status playing lama di cell ini
            const oldData = gridConfig[activeCellIndex];
            if (oldData) {
                const oldItem = document.getElementById(`cam-item-${oldData.id}`);
                if (oldItem) oldItem.classList.remove('playing');
            }
            
            // Set playing status baru
            element.classList.add('playing');
            
            gridConfig[activeCellIndex] = data;
            renderPlayer(activeCellIndex, data);
            
            // Auto move to next cell (find empty or just next)
            let nextIndex = (activeCellIndex + 1) % gridCount;
            setActiveCell(nextIndex);
            
        } catch(e) {
            console.error("Error parsing stream data:", e);
        }
    }

    function autoFillGrid() {
        const items = document.querySelectorAll('.cam-item');
        let index = 0;
        
        // Bersihkan grid
        for(let i=0; i<gridCount; i++) clearCell(i);
        
        items.forEach(item => {
            if (index < gridCount) {
                setActiveCell(index);
                item.click();
                index++;
            }
        });
    }

    function renderPlayer(index, data) {
        const playerDiv = document.getElementById(`player-${index}`);
        const titleDiv = document.getElementById(`title-${index}`);
        
        titleDiv.innerText = `${data.nama}`;
        titleDiv.style.display = 'block';
        
        const proxyUrl = `<?= site_url('cctv/stream/') ?>${data.id}?t=${new Date().getTime()}`;
        
        if (data.type === 'mjpeg' || data.type === 'rtsp' || !data.type) {
            playerDiv.innerHTML = `<img src="${proxyUrl}" style="width:100%; height:100%; object-fit:contain;" alt="Koneksi Terputus / Stream Gagal">`;
        } else if (data.type === 'hls') {
            playerDiv.innerHTML = `<video src="${data.url}" controls autoplay muted style="width:100%; height:100%; object-fit:contain;"></video>`;
        }
    }

    window.onload = function() {
        initGrid(4);
        
        // Fake dynamic CPU/RAM
        setInterval(() => {
            document.getElementById('cpu-load').innerText = Math.floor(Math.random() * 15 + 5) + '%';
            document.getElementById('ram-load').innerText = Math.floor(Math.random() * 10 + 30) + '%';
        }, 5000);
    };
</script>

</body>
</html>
