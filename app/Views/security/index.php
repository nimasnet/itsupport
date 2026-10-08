<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .security-dashboard {
        background-color: #0f172a; /* Slate 900 */
        color: #f8fafc; /* Slate 50 */
        padding: 20px;
        border-radius: 12px;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .panel-card {
        background: rgba(30, 41, 59, 0.7); /* Slate 800 with opacity */
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .panel-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .status-dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }
    .status-online { background-color: #10b981; box-shadow: 0 0 8px #10b981; }
    .status-offline { background-color: #ef4444; box-shadow: 0 0 8px #ef4444; }
    
    .btn-arm { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: all 0.3s; }
    .btn-arm:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); box-shadow: 0 0 10px rgba(239, 68, 68, 0.5); }
    
    .btn-disarm { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: all 0.3s; }
    .btn-disarm:hover { background: linear-gradient(135deg, #059669, #047857); box-shadow: 0 0 10px rgba(16, 185, 129, 0.5); }
    
    .btn-action { background: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; }
    .btn-action:hover { background: #2563eb; }
    
    .event-log-container {
        background: #1e293b;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        height: 400px;
        overflow-y: auto;
        padding: 10px;
    }
    .event-item {
        padding: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 13px;
        animation: fadeIn 0.5s ease;
    }
    .event-item:last-child { border-bottom: none; }
    .event-time { color: #94a3b8; font-size: 11px; }
    .event-type { font-weight: bold; color: #38bdf8; }
    .event-desc { color: #cbd5e1; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Scrollbar */
    .event-log-container::-webkit-scrollbar { width: 8px; }
    .event-log-container::-webkit-scrollbar-track { background: #0f172a; }
    .event-log-container::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    .event-log-container::-webkit-scrollbar-thumb:hover { background: #475569; }

    /* Modal Form */
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); z-index: 1000;
        justify-content: center; align-items: center;
    }
    .modal-content {
        background: #1e293b; padding: 25px; border-radius: 12px; width: 450px;
        color: white; border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);
    }
    .modal-content input {
        width: 100%; padding: 10px; margin: 8px 0 15px; border-radius: 6px;
        border: 1px solid #475569; background: #0f172a; color: white;
    }
</style>

<div class="right-frame security-dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;">
        <h2 style="margin: 0; color: #f8fafc; font-weight: 700; letter-spacing: -0.5px;">🚨 Security Monitoring System</h2>
        <div>
            <?php if(get_permission('security_monitoring.php') !== 'R'): ?>
            <button onclick="openPanelModal()" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);">
                + Tambah Panel
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        
        <!-- Left Side: Panels -->
        <div>
            <h3 style="margin-top: 0; color: #cbd5e1; font-size: 16px; margin-bottom: 15px;">📍 Daftar Panel Terhubung</h3>
            
            <?php if(empty($panels)): ?>
                <div style="text-align: center; padding: 40px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                    <span style="font-size: 40px;">📭</span>
                    <p style="color: #94a3b8; margin-top: 10px;">Belum ada Panel Alarm yang dikonfigurasi.</p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                <?php foreach($panels as $p): ?>
                    <div class="panel-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h4 style="margin: 0; font-size: 18px; font-weight: 600;">
                                <span class="status-dot <?= $p['status'] === 'Online' ? 'status-online' : 'status-offline' ?>"></span>
                                <?= esc($p['name']) ?>
                            </h4>
                            <?php if(get_permission('security_monitoring.php') !== 'R'): ?>
                            <a href="<?= site_url('security/delete-panel/'.$p['id']) ?>" onclick="return confirm('Hapus panel ini?')" style="color: #ef4444; text-decoration: none; font-size: 12px;">Hapus</a>
                            <?php endif; ?>
                        </div>
                        
                        <div style="font-size: 13px; color: #cbd5e1; margin-bottom: 15px; background: rgba(0,0,0,0.2); padding: 8px; border-radius: 6px;">
                            <div><strong>IP:</strong> <?= esc($p['ip_address']) ?>:<?= esc($p['port']) ?></div>
                            <div><strong>Last Online:</strong> <?= $p['last_online'] ? date('d M Y H:i:s', strtotime($p['last_online'])) : 'Never' ?></div>
                        </div>

                        <?php if(get_permission('security_monitoring.php') !== 'R'): ?>
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button class="btn-arm" onclick="sendControl(<?= $p['id'] ?>, 'arm')" style="flex: 1;">🛡️ ARM</button>
                            <button class="btn-disarm" onclick="sendControl(<?= $p['id'] ?>, 'disarm')" style="flex: 1;">🔓 DISARM</button>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Side: Live Logs -->
        <div>
            <h3 style="margin-top: 0; color: #cbd5e1; font-size: 16px; margin-bottom: 15px; display: flex; justify-content: space-between;">
                <span>📡 Live CMS Events</span>
                <span style="font-size: 12px; color: #10b981; animation: pulse 2s infinite;">● Live</span>
            </h3>
            
            <div class="event-log-container" id="event-log-box">
                <div style="text-align: center; color: #64748b; padding-top: 20px;">Memuat data log...</div>
            </div>
            
            <div style="margin-top: 15px; background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3b82f6; padding: 15px; border-radius: 6px; font-size: 12px; color: #94a3b8;">
                <strong>💡 Info:</strong> Pastikan Anda telah mensetting <b>CMS IP</b> di panel alarm menunjuk ke IP server ini dengan Port <b>5000</b>. Dan pastikan *background service* `php spark alarm:server` sedang berjalan di server.
            </div>
        </div>

    </div>
</div>

<!-- Modal Form -->
<div class="modal-overlay" id="panelModal">
    <div class="modal-content">
        <h3 style="margin-top:0; border-bottom:1px solid #475569; padding-bottom:10px;">Tambah Panel Alarm</h3>
        <form id="panelForm" onsubmit="savePanel(event)">
            <input type="hidden" name="id" id="panel_id">
            
            <label>Nama Panel (Misal: Alarm Gudang)</label>
            <input type="text" name="name" id="panel_name" required>
            
            <div style="display: flex; gap: 10px;">
                <div style="flex: 3;">
                    <label>IP Address</label>
                    <input type="text" name="ip_address" id="panel_ip" required>
                </div>
                <div style="flex: 1;">
                    <label>HTTP Port</label>
                    <input type="number" name="port" id="panel_port" value="80">
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Username (Web)</label>
                    <input type="text" name="username" id="panel_user">
                </div>
                <div style="flex: 1;">
                    <label>Password (Web)</label>
                    <input type="password" name="password" id="panel_pass">
                </div>
            </div>

            <label>Endpoint URL - ARM (CGI/API)</label>
            <input type="text" name="endpoint_arm" id="panel_arm" placeholder="/cgi-bin/remote.cgi?action=arm">
            
            <label>Endpoint URL - DISARM (CGI/API)</label>
            <input type="text" name="endpoint_disarm" id="panel_disarm" placeholder="/cgi-bin/remote.cgi?action=disarm">

            <div style="text-align: right; margin-top: 10px;">
                <button type="button" onclick="closePanelModal()" style="background: transparent; border: 1px solid #64748b; color: white; padding: 8px 15px; border-radius: 6px; cursor: pointer; margin-right: 10px;">Batal</button>
                <button type="submit" style="background: #3b82f6; border: none; color: white; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPanelModal() {
    document.getElementById('panelForm').reset();
    document.getElementById('panel_id').value = '';
    document.getElementById('panelModal').style.display = 'flex';
}

function closePanelModal() {
    document.getElementById('panelModal').style.display = 'none';
}

function savePanel(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('panelForm'));
    
    fetch('<?= site_url('security/save-panel') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menyimpan.');
        }
    });
}

function sendControl(id, action) {
    if(!confirm(`Anda yakin ingin mengirim perintah ${action.toUpperCase()} ke panel ini?`)) return;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', action);

    fetch('<?= site_url('security/remote-control') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Sukses: ' + data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan koneksi.');
    });
}

let lastLogCount = 0;
function fetchLiveEvents() {
    fetch('<?= site_url('security/get-events') ?>')
    .then(res => res.json())
    .then(data => {
        if (data.length !== lastLogCount && data.length > 0) {
            const container = document.getElementById('event-log-box');
            container.innerHTML = '';
            
            data.forEach(evt => {
                let badgeColor = '#94a3b8';
                if(evt.event_type.includes('SIA')) badgeColor = '#eab308';
                if(evt.event_type.includes('Contact')) badgeColor = '#ec4899';
                
                let timeObj = new Date(evt.event_time);
                let timeStr = timeObj.toLocaleTimeString('id-ID', { hour12: false });
                
                container.innerHTML += `
                    <div class="event-item">
                        <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                            <span style="background: ${badgeColor}; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold;">${evt.event_type}</span>
                            <span class="event-time">${timeStr}</span>
                        </div>
                        <div class="event-desc"><strong>${evt.panel_name}</strong> - ${evt.description}</div>
                        ${evt.zone ? `<div style="font-size: 11px; color: #64748b; margin-top:3px;">Zone: ${evt.zone}</div>` : ''}
                    </div>
                `;
            });
            lastLogCount = data.length;
        } else if (data.length === 0 && lastLogCount === 0) {
            document.getElementById('event-log-box').innerHTML = '<div style="text-align: center; color: #64748b; padding-top: 20px;">Belum ada sinyal CMS masuk.</div>';
        }
    });
}

// Fetch logs every 3 seconds
setInterval(fetchLiveEvents, 3000);
fetchLiveEvents();

</script>

<?= $this->endSection() ?>
