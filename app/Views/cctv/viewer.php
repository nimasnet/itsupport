<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="right-frame">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>🎥 Live CCTV Viewer</h3>
        <div>
            <label style="font-weight:bold; margin-right: 10px;">Layout Grid:</label>
            <select id="grid-selector" onchange="changeGrid()" style="padding: 5px; border-radius: 4px;">
                <option value="1">1 Layar (1x1)</option>
                <option value="4" selected>4 Layar (2x2)</option>
                <option value="9">9 Layar (3x3)</option>
                <option value="16">16 Layar (4x4)</option>
            </select>
        </div>
    </div>
    
    <div id="video-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; height: 75vh;">
        <!-- Cells will be generated here by JS -->
    </div>
</div>

<!-- Modal Pilih CCTV -->
<div id="cctvSelectModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div style="background-color:#fff; margin:5% auto; padding:20px; border-radius:8px; width:450px; position:relative; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <span style="position:absolute; right:15px; top:10px; font-size:24px; cursor:pointer;" onclick="closeCctvModal()">&times;</span>
        <h3 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Pilih CCTV untuk Layar <span id="target-screen-id"></span></h3>
        
        <div style="margin-bottom:15px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Cari/Pilih CCTV:</label>
            <select id="cctv-dropdown" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                <option value="">-- Hapus / Kosongkan Layar --</option>
                <?php foreach($cctvList as $c): ?>
                    <?php 
                        $streamData = json_encode([
                            'id' => $c['id'],
                            'url' => $c['stream_url'],
                            'type' => $c['stream_type'] ?? 'mjpeg',
                            'user' => $c['stream_user'],
                            'pass' => $c['stream_pass'],
                            'nama' => $c['nama_cctv'],
                            'posisi' => $c['posisi']
                        ]);
                    ?>
                    <option value="<?= esc($streamData) ?>">
                        <?= esc($c['nama_cctv']) ?> - <?= esc($c['posisi']) ?> (<?= esc($c['ip_address']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="text-align:right;">
            <button type="button" style="background:#6c757d; color:#fff; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; margin-right:5px;" onclick="closeCctvModal()">Batal</button>
            <button type="button" style="background:#17a2b8; color:#fff; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; font-weight:bold;" onclick="applyCctvToScreen()">Terapkan</button>
        </div>
    </div>
</div>

<script>
    let activeCellIndex = null;
    let gridConfig = [];

    function initGrid(count) {
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
            cell.className = 'cctv-cell';
            cell.style.backgroundColor = '#000';
            cell.style.borderRadius = '4px';
            cell.style.position = 'relative';
            cell.style.display = 'flex';
            cell.style.justifyContent = 'center';
            cell.style.alignItems = 'center';
            cell.style.overflow = 'hidden';
            cell.style.border = '1px solid #333';
            
            cell.innerHTML = `
                <div id="player-${i}" style="width:100%; height:100%; display:flex; justify-content:center; align-items:center;">
                    <span style="color:#666; font-size:40px; cursor:pointer;" onclick="openCctvModal(${i})">➕</span>
                </div>
                <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.6); color:#fff; padding:5px 10px; font-size:12px; display:flex; justify-content:space-between; align-items:center;">
                    <span id="title-${i}">Layar ${i+1} - Kosong</span>
                    <button onclick="openCctvModal(${i})" style="background:#444; color:#fff; border:none; padding:2px 8px; border-radius:3px; cursor:pointer; font-size:10px;">Ubah</button>
                </div>
            `;
            grid.appendChild(cell);
            
            if (gridConfig[i]) {
                renderPlayer(i, gridConfig[i]);
            }
        }
    }

    function changeGrid() {
        const sel = document.getElementById('grid-selector').value;
        initGrid(parseInt(sel));
    }

    function openCctvModal(index) {
        activeCellIndex = index;
        document.getElementById('target-screen-id').innerText = (index + 1);
        document.getElementById('cctvSelectModal').style.display = 'block';
    }

    function closeCctvModal() {
        document.getElementById('cctvSelectModal').style.display = 'none';
        activeCellIndex = null;
    }

    function applyCctvToScreen() {
        if (activeCellIndex === null) return;
        
        const val = document.getElementById('cctv-dropdown').value;
        if (!val) {
            // Kosongkan
            gridConfig[activeCellIndex] = null;
            document.getElementById(`player-${activeCellIndex}`).innerHTML = `<span style="color:#666; font-size:40px; cursor:pointer;" onclick="openCctvModal(${activeCellIndex})">➕</span>`;
            document.getElementById(`title-${activeCellIndex}`).innerText = `Layar ${activeCellIndex+1} - Kosong`;
        } else {
            const data = JSON.parse(val);
            gridConfig[activeCellIndex] = data;
            renderPlayer(activeCellIndex, data);
        }
        
        closeCctvModal();
    }

    function renderPlayer(index, data) {
        const playerDiv = document.getElementById(`player-${index}`);
        const titleDiv = document.getElementById(`title-${index}`);
        
        titleDiv.innerText = `${data.nama} - ${data.posisi}`;
        
        // Kita menggunakan PHP Proxy Endpoint untuk MJPEG dan RTSP (jika RTSP diset tapi sebenarnya adalah Hikvision)
        const proxyUrl = `<?= site_url('cctv/stream/') ?>${data.id}?t=${new Date().getTime()}`;
        
        if (data.type === 'mjpeg' || data.type === 'rtsp') {
            playerDiv.innerHTML = `<img src="${proxyUrl}" style="width:100%; height:100%; object-fit:contain;" alt="Memuat Stream CCTV..." onerror="this.onerror=null; this.alt='Stream Gagal Dimuat. Pastikan Sub-Stream diset ke MJPEG di NVR.';">`;
        } else if (data.type === 'hls') {
            playerDiv.innerHTML = `<video src="${data.url}" controls autoplay muted style="width:100%; height:100%; object-fit:contain;"></video>`;
        } else {
            playerDiv.innerHTML = `<div style="color:#fff;">Format Tidak Dikenal</div>`;
        }
    }

    // Init onload
    window.onload = function() {
        initGrid(4);
    };
</script>

<?= $this->endSection() ?>
