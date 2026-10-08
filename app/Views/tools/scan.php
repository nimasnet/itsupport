<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .right-frame {
        background: #f4f6fa;
        padding: 20px;
        min-height: calc(100vh - 60px);
    }
    .scanner-controls {
        background: #ffffff;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        align-items: center;
    }
    .scanner-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    .btn-action {
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        color: white;
        font-size: 13px;
        transition: background 0.2s;
    }
    .btn-scan { background-color: #f39c12; }
    .btn-scan:hover { background-color: #e67e22; }
    .btn-select { background-color: #0077b6; }
    .btn-auto-son { background-color: #17a2b8; }
    .btn-unselect { background-color: #6c757d; }
    .btn-add { background-color: #28a745; }
    
    .table-scroll-container {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow-y: auto;
        max-height: 60vh;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    #scanTable { width: 100%; border-collapse: collapse; }
    #scanTable th { 
        position: sticky; 
        top: 0; 
        background: #f8f9fa; 
        z-index: 1; 
        padding: 12px;
        border-bottom: 2px solid #ddd;
        text-align: left;
    }
    #scanTable td { padding: 10px; border-bottom: 1px solid #eee; }
    .scan-row:hover { background-color: #f1f8ff; }
    
    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
        color: white;
    }
    .bg-gray { background-color: #6c757d; }
    .bg-yellow { background-color: #ffc107; color: #333; }
    .bg-green { background-color: #28a745; }
    .bg-red { background-color: #dc3545; }
    .bg-blue { background-color: #0077b6; }

    .identity-cell {
        font-size: 12px;
        color: #333;
        font-weight: bold;
    }
    .identity-resolving {
        color: #aaa;
        font-style: italic;
        font-size: 11px;
    }
</style>

<div class="right-frame">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>🔍 Scanner Jaringan IP</h2>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background-color:#d4edda; color:#155724; padding:10px; border-radius:4px; margin-bottom:15px; border:1px solid #c3e6cb;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background-color:#f8d7da; color:#721c24; padding:10px; border-radius:4px; margin-bottom:15px; border:1px solid #f5c6cb;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="scanner-controls">
        <label style="font-weight: bold;">Pilih VLAN / Network Segmen:</label>
        <select id="vlan_selector" style="padding: 8px; width: 250px; border: 1px solid #ccc; border-radius: 4px;" onchange="generateRows()">
            <option value="">-- Pilih Jaringan --</option>
            <?php foreach ($vlan_list as $v): ?>
                <option value="<?= esc($v['network_ip']) ?>"><?= esc($v['nama_vlan']) ?> (<?= esc($v['network_ip']) ?>.x)</option>
            <?php endforeach; ?>
        </select>
        <span style="font-size: 13px; color: #666;">Akan men-scan IP 1 hingga 254 pada segmen terpilih.</span>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0;">
        <form action="<?= site_url('tools/scan') ?>" method="POST" id="scanForm">
            <?= csrf_field() ?>
            <!-- CSRF value disimpan untuk diakses JS -->
            <span id="csrf_name" style="display:none"><?= csrf_token() ?></span>
            <span id="csrf_value" style="display:none"><?= csrf_hash() ?></span>
            <div class="scanner-actions">
                <?php if ($page_perm !== 'R'): ?>
                <button type="button" class="btn-action btn-scan" onclick="startScan()">▶ MULAI SCAN</button>
                <button type="button" class="btn-action btn-select" onclick="selectAll()">☑ SELECT ALL</button>
                <button type="button" class="btn-action btn-auto-son" onclick="selectOnline()">☑ AUTO S-ON</button>
                <button type="button" class="btn-action btn-unselect" onclick="unselectAll()">☐ UNSELECT</button>
                <button type="button" class="btn-action btn-add" onclick="submitAddToList()">➕ ADD TO LIST</button>
                <?php else: ?>
                <div style="background:#fff3cd; color:#856404; padding:10px; border-radius:4px; width:100%;">
                    Mode <b>Read-Only</b>. Anda tidak dapat melakukan aksi ini.
                </div>
                <?php endif; ?>
            </div>

            <!-- Hidden inputs untuk identity (diisi oleh JS saat submit) -->
            <div id="identityInputs" style="display:none;"></div>

            <div class="table-scroll-container">
                <table id="scanTable">
                    <thead>
                        <tr>
                            <th width="6%" style="text-align:center;">Pilih</th>
                            <th width="4%">No</th>
                            <th width="18%">IP Address</th>
                            <th width="15%">Status</th>
                            <th width="28%">Identity (PC-Name)</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="scanBody">
                        <tr><td colspan="6" style="text-align:center; padding: 30px;">Pilih VLAN untuk memuat daftar IP.</td></tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
const registeredIps = <?= json_encode($registered_ips) ?>;
const pingUrl = "<?= site_url('tools/scan/ping') ?>";

// Store identities for all scanned IPs: { 'ip': 'PCNAME' }
const identityMap = {};

function generateRows() {
    const vlan = document.getElementById('vlan_selector').value;
    const tbody = document.getElementById('scanBody');
    tbody.innerHTML = '';
    
    if (vlan === "") {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 30px;">Pilih VLAN untuk memuat daftar IP.</td></tr>';
        return;
    }

    let html = '';
    for (let i = 1; i <= 254; i++) {
        let ip = vlan + '.' + i;
        let isRegistered = registeredIps.includes(ip);
        
        html += `
            <tr class="scan-row" data-ip="${ip}" data-status="unknown">
                <td style="text-align:center;">
                    <input type="checkbox" name="selected_ips[]" value="${ip}" class="ip-check" ${isRegistered ? 'disabled' : ''}>
                </td>
                <td>${i}</td>
                <td><b>${ip}</b></td>
                <td class="status-cell"><span class="badge bg-gray">Menunggu Scan</span></td>
                <td class="identity-cell" id="id-${ip.replace(/\./g,'-')}"><span class="identity-resolving">—</span></td>
                <td>${isRegistered ? '<span class="badge bg-blue">Terdaftar</span>' : 'Siap'}</td>
            </tr>
        `;
    }
    tbody.innerHTML = html;
}

function startScan() {
    const rows = document.querySelectorAll('.scan-row');
    if (rows.length === 0) {
        alert("Pilih VLAN terlebih dahulu!");
        return;
    }
    
    rows.forEach(row => {
        const ip = row.getAttribute('data-ip');
        const statusCell = row.querySelector('.status-cell');
        const identityCell = document.getElementById('id-' + ip.replace(/\./g, '-'));
        statusCell.innerHTML = '<span class="badge bg-yellow">Scanning...</span>';
        if (identityCell) identityCell.innerHTML = '<span class="identity-resolving">Resolving...</span>';

        fetch(`${pingUrl}?ip=${ip}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'online') {
                    statusCell.innerHTML = '<span class="badge bg-green">ONLINE</span>';
                    row.setAttribute('data-status', 'online');

                    // Show identity
                    const identity = data.identity || '';
                    if (identityCell) {
                        identityCell.innerHTML = identity
                            ? `<span style="color:#155724; font-weight:bold;">💻 ${identity}</span>`
                            : '<span class="identity-resolving">Tidak dikenali</span>';
                    }
                    identityMap[ip] = identity;
                } else {
                    statusCell.innerHTML = '<span class="badge bg-red">OFFLINE</span>';
                    row.setAttribute('data-status', 'offline');
                    if (identityCell) identityCell.innerHTML = '<span class="identity-resolving">—</span>';
                    identityMap[ip] = '';
                }
            })
            .catch(err => {
                statusCell.innerHTML = '<span class="badge bg-gray">Error</span>';
                if (identityCell) identityCell.innerHTML = '<span class="identity-resolving">Error</span>';
            });
    });
}

function selectAll() {
    document.querySelectorAll('.ip-check:not(:disabled)').forEach(cb => cb.checked = true);
}

function selectOnline() {
    document.querySelectorAll('.scan-row').forEach(row => {
        const cb = row.querySelector('.ip-check');
        if (cb && !cb.disabled && row.getAttribute('data-status') === 'online') {
            cb.checked = true;
        } else if (cb && !cb.disabled) {
            cb.checked = false;
        }
    });
}

function unselectAll() {
    document.querySelectorAll('.ip-check').forEach(cb => cb.checked = false);
}

function submitAddToList() {
    // Pastikan ada IP yang dipilih
    const checked = document.querySelectorAll('.ip-check:checked');
    if (checked.length === 0) {
        alert("Pilih minimal 1 IP yang ONLINE terlebih dahulu!");
        return;
    }

    if (!confirm('Tambahkan ' + checked.length + ' IP yang dipilih ke dalam daftar?')) {
        return;
    }

    // Buat form baru secara dinamis
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= site_url('tools/scan') ?>';
    form.style.display = 'none';

    // CSRF token - ambil dari span PHP yang dirender server
    const csrfName  = document.getElementById('csrf_name')  ? document.getElementById('csrf_name').innerText.trim()  : '';
    const csrfValue = document.getElementById('csrf_value') ? document.getElementById('csrf_value').innerText.trim() : '';
    if (csrfName && csrfValue) {
        const csrfInput = document.createElement('input');
        csrfInput.type  = 'hidden';
        csrfInput.name  = csrfName;
        csrfInput.value = csrfValue;
        form.appendChild(csrfInput);
    }

    // Field penanda: ini adalah request add_to_list
    const flagInput = document.createElement('input');
    flagInput.type  = 'hidden';
    flagInput.name  = 'add_to_list';
    flagInput.value = '1';
    form.appendChild(flagInput);

    // IP yang dicentang + identity
    checked.forEach(cb => {
        const ip = cb.value;

        const ipInput  = document.createElement('input');
        ipInput.type   = 'hidden';
        ipInput.name   = 'selected_ips[]';
        ipInput.value  = ip;
        form.appendChild(ipInput);

        const idInput  = document.createElement('input');
        idInput.type   = 'hidden';
        idInput.name   = 'ip_identities[' + ip + ']';
        idInput.value  = identityMap[ip] || '';
        form.appendChild(idInput);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>

<?= $this->endSection() ?>
