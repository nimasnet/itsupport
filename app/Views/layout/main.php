<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'IT Support Dashboard' ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
        
        .top-frame { 
            background-color: <?= $top_color ?? '#0077b6' ?>; 
            color: white; 
            padding: 0 20px; 
            height: 60px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 50;
        }
        
        .top-right-menu { display: flex; align-items: center; gap: 15px; font-size: 14px; }
        .btn-signout { background-color: #dc3545; color: white; text-decoration: none; padding: 6px 15px; border-radius: 4px; font-weight: bold; border: 1px solid #bd2130; transition: background-color 0.2s; }
        .btn-signout:hover { background-color: #c82333; }

        .main-container { display: flex; flex: 1; overflow: hidden; }
        .left-frame { background-color: <?= $side_color ?? '#f0f2f5' ?>; width: 15%; padding: 20px; border-right: 1px solid #ccc; overflow-y: auto; }
        
        .left-frame ul { list-style: none; }
        .left-frame li { margin-bottom: 10px; }
        .left-frame a { text-decoration: none; color: #333; display: block; padding: 8px; background: #e4e6e9; border-radius: 4px; transition: background 0.2s;}
        .left-frame a:hover { background: #d8dadf; }
        
        .right-frame { background-color: #ffffff; width: 85%; padding: 20px; overflow-y: auto; position: relative; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input[type="text"], input[type="password"], textarea { width: 100%; max-width: 400px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; background-color: <?= $top_color ?? '#0077b6' ?>; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { opacity: 0.9; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-edit { color: #0077b6; text-decoration: none; font-weight: bold; }
        .btn-hapus { color: #dc3545; text-decoration: none; font-weight: bold; }

        details summary::-webkit-details-marker { display: none; }
        details summary { list-style: none; outline: none; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="top-frame">
        <h2>Sistem Pendataan IT Support</h2>
        <div class="top-right-menu">
            <span>Halo, <b><?= htmlspecialchars($current_user) ?></b> (<?= $current_role ?>)</span>
            <a href="<?= site_url('logout') ?>" class="btn-signout" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">Sign Out</a>
        </div>
    </div>

    <div class="main-container">
        <div class="left-frame">
            <h3>Menu Utama</h3><br>
            <ul>
                <?php if(has_access('index.php')): ?>
                <li><a href="<?= site_url('/') ?>">Dashboard PRTG</a></li>
                <?php endif; ?>
                
                <?php if(has_any_access(['list_cctv.php', 'tambah.php', 'master_nama.php', 'monitoring.php', 'setup_monitoring.php', 'monitoring_logs.php', 'hardisk_log.php', 'checklist_status.php', 'it_respon_actions.php'])): ?>
                <li style="margin-top: 10px;">
                    <details id="menu-cctv" style="outline: none;">
                        <summary style="cursor: pointer; padding: 8px 10px; background: #d8dadf; border-radius: 4px; font-weight: bold; margin-bottom: 5px; list-style: none; display: flex; justify-content: space-between; align-items: center; user-select: none;">
                            <span>📁 Pengelolaan CCTV</span>
                            <span style="font-size: 9px; color: #555;">▼</span>
                        </summary>
                        <ul style="list-style: none; padding-left: 8px; display: flex; flex-direction: column; gap: 5px; margin-top: 5px;">
                            <?php if(has_access('list_cctv.php')): ?>
                            <li><a href="<?= site_url('cctv') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">List CCTV</a></li>
                            <?php endif; ?>
                            <?php if(has_access('tambah.php')): ?>
                            <li><a href="<?= site_url('cctv/create') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Tambah CCTV</a></li>
                            <?php endif; ?>
                            <?php if(has_access('master_nama.php')): ?>
                            <li><a href="<?= site_url('master/nama') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Master Nama CCTV</a></li>
                            <?php endif; ?>
                            <?php if(has_access('master_nvr.php')): ?>
                            <li><a href="<?= site_url('master/nvr') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Master NVR</a></li>
                            <?php endif; ?>
                            <?php if(has_access('cctv_by_nvr.php')): ?>
                            <li><a href="<?= site_url('cctv/nvr') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">CCTV BY NVR</a></li>
                            <?php endif; ?>
                            <?php if(has_access('hardisk_log.php')): ?>
                            <li><a href="<?= site_url('cctv/hardisk-log') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Hardisk Log Replacement</a></li>
                            <?php endif; ?>
                            <?php if(has_access('hardisk_manage.php')): ?>
                            <li><a href="<?= site_url('cctv/hardisk-manage') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Hardisk Manage</a></li>
                            <?php endif; ?>
                            <?php if(has_access('checklist_status.php')): ?>
                            <li><a href="<?= site_url('checklist-cctv') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Checklist Status</a></li>
                            <?php endif; ?>
                            <?php if(has_access('it_respon_actions.php')): ?>
                            <li><a href="<?= site_url('cctv/it-respon-actions') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">IT Respon Actions</a></li>
                            <?php endif; ?>
                            <?php if(has_any_access(['monitoring.php', 'setup_monitoring.php', 'monitoring_logs.php', 'cctv_stats.php'])): ?>
                            <li>
                                <?php if(has_access('monitoring.php')): ?>
                                <a href="<?= site_url('monitoring/cctv') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>; font-weight: bold;">Monitoring IP (Live)</a>
                                <?php endif; ?>
                                <?php if(has_any_access(['setup_monitoring.php', 'monitoring_logs.php', 'cctv_stats.php'])): ?>
                                <ul style="list-style: none; padding-left: 10px; display: flex; flex-direction: column; gap: 4px; margin-top: 4px;">
                                    <?php if(has_access('setup_monitoring.php')): ?>
                                    <li><a href="<?= site_url('monitoring/cctv/setup') ?>" style="background: #fafafa; font-size: 12px; padding: 5px 8px;">⚙️ Setup Monitoring</a></li>
                                    <?php endif; ?>
                                    <?php if(has_access('monitoring_logs.php')): ?>
                                    <li><a href="<?= site_url('monitoring/cctv/logs') ?>" style="background: #fafafa; font-size: 12px; padding: 5px 8px;">📋 Log Monitoring</a></li>
                                    <?php endif; ?>
                                    <?php if(has_access('cctv_stats.php')): ?>
                                    <li><a href="<?= site_url('monitoring/cctv/stats-chart') ?>" style="background: #fafafa; font-size: 12px; padding: 5px 8px;">📊 CCTV Status Statistik</a></li>
                                    <?php endif; ?>
                                </ul>
                                <?php endif; ?>
                            </li>
                            <?php endif; ?>
                            <?php if(has_access('cctv_viewer.php')): ?>
                            <li><a href="<?= site_url('cctv/viewer') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>; font-weight: bold; color: #17a2b8;">🎥 CCTV Viewer (Live)</a></li>
                            <?php endif; ?>
                            <?php if(has_access('nvr_viewer.php')): ?>
                            <li>
                                <a href="<?= site_url('cctv/nvr-viewer') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>; font-weight: bold; color: #007bff;">📺 NVR Viewer (Live)</a>
                                <?php if(has_access('nvr_config.php')): ?>
                                <ul style="list-style: none; padding-left: 10px; margin-top: 2px;">
                                    <li><a href="<?= site_url('cctv/nvr-config') ?>" style="background: #fafafa; font-size: 12px; padding: 5px 8px; border-left: 2px solid #ccc;">⚙️ NVR Config</a></li>
                                </ul>
                                <?php endif; ?>
                            </li>
                            <?php endif; ?>
                            <?php if(has_access('security_monitoring.php')): ?>
                            <li><a href="<?= site_url('security') ?>" style="background: #0f172a; font-size: 13px; border-left: 3px solid #ef4444; font-weight: bold; color: #f8fafc;">🚨 Security Monitoring</a></li>
                            <?php endif; ?>
                        </ul>
                    </details>
                </li>
                <?php endif; ?>
                
                <?php if(has_access('master_vlan.php')): ?>
                <li style="margin-top: 10px;">
                    <details id="menu-network" style="outline: none;">
                        <summary style="cursor: pointer; padding: 8px 10px; background: #d8dadf; border-radius: 4px; font-weight: bold; margin-bottom: 5px; list-style: none; display: flex; justify-content: space-between; align-items: center; user-select: none;">
                            <span>📁 Pengelolaan Jaringan</span>
                            <span style="font-size: 9px; color: #555;">▼</span>
                        </summary>
                        <ul style="list-style: none; padding-left: 8px; display: flex; flex-direction: column; gap: 5px; margin-top: 5px;">
                            <li><a href="<?= site_url('master/vlan') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Master Jaringan VLAN</a></li>
                        </ul>
                    </details>
                </li>
                <?php endif; ?>
                
                <?php if(has_access('ip_management.php')): ?>
                <li><a href="<?= site_url('ip-management') ?>">IP List Management</a></li>
                <?php endif; ?>
                
                <?php if(has_any_access(['scan.php', 'graphs.php', 'printer_monitoring.php', 'script_inject.php'])): ?>
                <li style="margin-top: 10px;">
                    <details id="menu-tools" style="outline: none;">
                        <summary style="cursor: pointer; padding: 8px 10px; background: #d8dadf; border-radius: 4px; font-weight: bold; margin-bottom: 5px; list-style: none; display: flex; justify-content: space-between; align-items: center; user-select: none;">
                            <span>📁 Tools</span>
                            <span style="font-size: 9px; color: #555;">▼</span>
                        </summary>
                        <ul style="list-style: none; padding-left: 8px; display: flex; flex-direction: column; gap: 5px; margin-top: 5px;">
                            <?php if(has_access('scan.php')): ?>
                            <li><a href="<?= site_url('tools/scan') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Scan Jaringan IP</a></li>
                            <?php endif; ?>
                            <?php if(has_access('graphs.php')): ?>
                            <li><a href="<?= site_url('tools/graphs') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Graphs</a></li>
                            <?php endif; ?>
                            <?php if(has_access('printer_monitoring.php')): ?>
                            <li><a href="<?= site_url('monitoring/printer') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Printer Monitoring</a></li>
                            <?php endif; ?>
                            <?php if(has_access('script_inject.php')): ?>
                            <li><a href="<?= site_url('tools/script-inject') ?>" style="background: #1a1a2e; color: #e2b96f; font-size: 13px; border-left: 3px solid #e2b96f; font-weight: bold;">💉 Script Inject</a></li>
                            <?php endif; ?>
                        </ul>
                    </details>
                </li>
                <?php endif; ?>
                
                <?php if(has_access('monitoring_device.php')): ?>
                <li><a href="<?= site_url('monitoring/device') ?>">Monitoring Status Device</a></li>
                <?php endif; ?>
                
                <?php if(has_any_access(['ups_checklist.php', 'ups_manage_list.php', 'wemos.php', 'mikrotik.php', 'grafanity.php'])): ?>
                <li style="margin-top: 10px;">
                    <details id="menu-monitoring-support" style="outline: none;">
                        <summary style="cursor: pointer; padding: 8px 10px; background: #d8dadf; border-radius: 4px; font-weight: bold; margin-bottom: 5px; list-style: none; display: flex; justify-content: space-between; align-items: center; user-select: none;">
                            <span>📁 Monitoring Support</span>
                            <span style="font-size: 9px; color: #555;">▼</span>
                        </summary>
                        <ul style="list-style: none; padding-left: 8px; display: flex; flex-direction: column; gap: 5px; margin-top: 5px;">
                            <?php if(has_access('ups_checklist.php')): ?>
                            <li><a href="<?= site_url('monitoring-support/ups-checklist') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?? '#0077b6' ?>;">UPS Checklist</a></li>
                            <?php endif; ?>
                            <?php if(has_access('ups_manage_list.php')): ?>
                            <li><a href="<?= site_url('monitoring-support/ups-manage-list') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?? '#0077b6' ?>;">UPS Manage List</a></li>
                            <?php endif; ?>
                            <?php if(has_access('wemos.php')): ?>
                            <li><a href="<?= site_url('wemos') ?>" style="background: #0b0b1e; color: #00d4ff; font-weight:bold; font-size: 13px; border-left: 3px solid #7c6fff;">⚡ Wemos Dashboard</a></li>
                            <?php endif; ?>
                            <?php if(has_access('mikrotik.php')): ?>
                            <li><a href="/mikrotik/" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid #e05e5e; font-weight: bold;">📡 MikroTik Tool</a></li>
                            <?php endif; ?>
                            <?php if(has_access('grafanity.php')): ?>
                            <li><a href="/grafanity/" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid #f39c12; font-weight: bold;">📊 Grafanity NMS</a></li>
                            <?php endif; ?>
                        </ul>
                    </details>
                </li>
                <?php endif; ?>
                
                <?php if(has_any_access(['admin_management.php', 'login_log.php'])): ?>
                <li style="margin-top: 10px;">
                    <details id="menu-admin" style="outline: none;">
                        <summary style="cursor: pointer; padding: 8px 10px; background: #d8dadf; border-radius: 4px; font-weight: bold; margin-bottom: 5px; list-style: none; display: flex; justify-content: space-between; align-items: center; user-select: none;">
                            <span>📁 Admin Setup</span>
                            <span style="font-size: 9px; color: #555;">▼</span>
                        </summary>
                        <ul style="list-style: none; padding-left: 8px; display: flex; flex-direction: column; gap: 5px; margin-top: 5px;">
                            <?php if(has_access('admin_management.php')): ?>
                            <li><a href="<?= site_url('admin') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Admin Management</a></li>
                            <?php endif; ?>
                            <?php if(has_access('login_log.php')): ?>
                            <li><a href="<?= site_url('admin/login-log') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>;">Loggin Log</a></li>
                            <?php endif; ?>
                        </ul>
                    </details>
                </li>
                <?php endif; ?>
                
                <?php if(has_any_access(['stb_mess.php', 'kelola_stb.php'])): ?>
                <li style="margin-top: 10px;">
                    <details id="menu-device" style="outline: none;">
                        <summary style="cursor: pointer; padding: 8px 10px; background: #d8dadf; border-radius: 4px; font-weight: bold; margin-bottom: 5px; list-style: none; display: flex; justify-content: space-between; align-items: center; user-select: none;">
                            <span>📁 Device List</span>
                            <span style="font-size: 9px; color: #555;">▼</span>
                        </summary>
                        <ul style="list-style: none; padding-left: 8px; display: flex; flex-direction: column; gap: 5px; margin-top: 5px;">
                            <?php if(has_access('stb_mess.php')): ?>
                            <li><a href="<?= site_url('stb/mess') ?>" style="background: #f0f2f5; font-size: 13px; border-left: 3px solid <?= $top_color ?>; font-weight: bold;">STB MESS</a></li>
                            <?php endif; ?>
                            <?php if(has_access('kelola_stb.php')): ?>
                            <li><a href="<?= site_url('stb/kelola') ?>" style="background: #fafafa; font-size: 12px; padding: 5px 8px;">⚙️ Kelola STB</a></li>
                            <?php endif; ?>
                        </ul>
                    </details>
                </li>
                <?php endif; ?>
                
                <?php if(has_access('schedule_bell.php')): ?>
                <li style="margin-top: 10px;">
                    <a href="<?= site_url('schedule-bell') ?>" style="background: #d8dadf; font-weight: bold;">🔔 Schedule Bell</a>
                </li>
                <?php endif; ?>
                
                <?php if(isset($custom_menus) && count($custom_menus) > 0): ?>
                    <br><li><hr style='border-top: 1px solid #ccc; margin: 10px 0;'></li>
                    <li><small style='color:#666; font-weight:bold;'>MENU TAMBAHAN</small></li>
                    <?php foreach($custom_menus as $m): ?>
                        <?php if(has_access($m['url_menu'])): ?>
                            <li><a href="<?= site_url('custom/' . $m['id']) ?>"><?= htmlspecialchars($m['nama_menu']) ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
             </ul>
        </div>
        
        <?= $this->renderSection('content') ?>
        
    </div>
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const details = document.querySelectorAll('details');
        const currentPath = window.location.pathname;

        details.forEach(detail => {
            const links = detail.querySelectorAll('a');
            links.forEach(link => {
                if (link.getAttribute('href') === currentPath || link.href === window.location.href) {
                    detail.open = true;
                    link.style.background = '<?= $top_color ?>';
                    link.style.color = 'white';
                    link.style.fontWeight = 'bold';
                }
            });
            
            detail.addEventListener('toggle', function() {
                localStorage.setItem(detail.id + '_open', detail.open);
            });
            
            const savedState = localStorage.getItem(detail.id + '_open');
            if (savedState !== null) {
                detail.open = (savedState === 'true');
            }
        });
        
        // SweetAlert2 Flashdata Toast Notification
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        <?php if(session()->getFlashdata('success')): ?>
        Toast.fire({ icon: 'success', title: '<?= addslashes(session()->getFlashdata('success')) ?>' });
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('message')): ?>
        Toast.fire({ icon: 'success', title: '<?= addslashes(session()->getFlashdata('message')) ?>' });
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
        Toast.fire({ icon: 'error', title: '<?= addslashes(session()->getFlashdata('error')) ?>' });
        <?php endif; ?>

        // SweetAlert2 Delete Confirmation for links with class btn-hapus
        document.querySelectorAll('.btn-hapus').forEach(item => {
            // Remove inline onclick if it exists so it doesn't conflict
            item.removeAttribute('onclick');
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        // SweetAlert2 Form Confirmation Interception
        document.querySelectorAll('form[onsubmit]').forEach(form => {
            const onsubmitStr = form.getAttribute('onsubmit');
            if (onsubmitStr && onsubmitStr.includes('return confirm(')) {
                const match = onsubmitStr.match(/return confirm\(['"](.*?)['"]\)/);
                if (match) {
                    const confirmMessage = match[1];
                    form.removeAttribute('onsubmit');
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: confirmMessage,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Lanjutkan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                }
            }
        });

        // SweetAlert2 Link Confirmation Interception
        document.querySelectorAll('a[onclick]').forEach(link => {
            const onclickStr = link.getAttribute('onclick');
            if (onclickStr && onclickStr.includes('return confirm(')) {
                const match = onclickStr.match(/return confirm\(['"](.*?)['"]\)/);
                if (match) {
                    const confirmMessage = match[1];
                    link.removeAttribute('onclick');
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: confirmMessage,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Lanjutkan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = url;
                            }
                        });
                    });
                }
            }
        });
    });
    </script>
</body>
</html>
