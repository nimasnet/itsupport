<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .admin-grid-top { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
    .admin-grid-bottom { width: 100%; }
    .card { background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .card h4 { margin-bottom: 15px; color: #0077b6; border-bottom: 2px solid #f2f2f2; padding-bottom: 8px; font-size: 16px; }
    .form-control { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
    .btn-block { width: 100%; padding: 10px; font-size: 14px; font-weight: bold; border: none; color: white; border-radius: 4px; cursor: pointer; }
    .checklist-container { background: #f8f9fa; padding: 12px; border-radius: 6px; border: 1px solid #eee; max-height: 350px; overflow-y: auto; }
    .checklist-container label { display: block; padding: 4px 0; font-weight: normal; cursor: pointer; }
    .table-scroll-container { max-height: 350px; overflow-y: auto; border: 1px solid #e0e0e0; border-radius: 4px; margin-top: 15px; }
    .user-table { width: 100%; border-collapse: collapse; }
    .user-table th { background-color: #f7f9fa; color: #333; position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.2); }
    .user-table th, .user-table td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #e0e0e0; }
    .user-table tr:hover { background-color: #fbfcfd; }
    .badge-role { padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    .role-admin { background-color: #e3f2fd; color: #0d47a1; }
    .role-operator { background-color: #e8f5e9; color: #1b5e20; }
    .role-user { background-color: #f5f5f5; color: #616161; }
    .btn-tab { padding: 8px 16px; background: #f0f2f5; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px; border: 1px solid #ccc; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-tab:hover { background: #d8dadf; }
    .btn-tab.active { background: <?= esc($settings['top_color'] ?? '#0077b6') ?>; color: white; border-color: <?= esc($settings['top_color'] ?? '#0077b6') ?>; }
</style>

<div class="right-frame">
    <div style="display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap;">
        <a href="<?= site_url('admin?view=admin') ?>" class="btn-tab <?= ($view === 'admin') ? 'active' : '' ?>">👤 Kelola Pengguna &amp; Hak Akses</a>
        <a href="<?= site_url('admin?view=main_edit') ?>" class="btn-tab <?= ($view === 'main_edit') ? 'active' : '' ?>">⚙️ Main Edit</a>
        <a href="<?= site_url('admin?view=ping_control') ?>" class="btn-tab <?= ($view === 'ping_control') ? 'active' : '' ?>">📡 Ping Control</a>
        <a href="<?= site_url('admin?view=default_homepage') ?>" class="btn-tab <?= ($view === 'default_homepage') ? 'active' : '' ?>">🏠 Default Homepages</a>
        <a href="<?= site_url('admin?view=schedule_bypass') ?>" class="btn-tab <?= ($view === 'schedule_bypass') ? 'active' : '' ?>">🔔 Schedule Bypass</a>
    </div>

    <?php if ($view === 'main_edit'): ?>
        <h3>Main Edit (Pengaturan Utama Tampilan)</h3><br>
        <div class="card" style="max-width: 500px;">
            <h4>Pengaturan Tampilan Utama</h4>
            <form method="POST" action="<?= site_url('admin/mainEdit') ?>">
                <?php
                $curr_top = $settings['top_color'] ?? '#0077b6';
                $curr_side = $settings['side_color'] ?? '#f0f2f5';
                ?>
                <div class="form-group">
                    <label>Warna Header Atas (Top Bar)</label>
                    <input type="color" name="top_color" value="<?= esc($curr_top) ?>" style="width: 100%; height: 45px; border-radius: 6px; border: 1.5px solid #ccc; cursor: pointer; padding: 2px;">
                </div>
                
                <div class="form-group" style="margin-top: 15px;">
                    <label>Warna Sidebar Kiri (Side Bar)</label>
                    <input type="color" name="side_color" value="<?= esc($curr_side) ?>" style="width: 100%; height: 45px; border-radius: 6px; border: 1.5px solid #ccc; cursor: pointer; padding: 2px;">
                </div>
                
                <?php $curr_excel = $settings['excel_header_color'] ?? '#4e73df'; ?>
                <div class="form-group" style="margin-top: 15px;">
                    <label>Warna Header Export Excel</label>
                    <input type="color" name="excel_header_color" value="<?= esc($curr_excel) ?>" style="width: 100%; height: 45px; border-radius: 6px; border: 1.5px solid #ccc; cursor: pointer; padding: 2px;">
                </div>
                
                <button type="submit" class="btn-block" style="background: <?= esc($curr_top) ?>; margin-top: 20px;">
                    💾 Simpan Pengaturan
                </button>
            </form>
        </div>
    <?php elseif ($view === 'default_homepage'): ?>
        <h3>Default Homepages (Halaman Awal)</h3><br>
        <div class="admin-grid-top">
            <div class="card">
                <h4>🏠 Atur Halaman Default per Role</h4>
                <p style="font-size:13px; color:#555; margin-bottom:20px; line-height:1.6;">
                    Tentukan halaman pertama yang akan dibuka oleh setiap tingkatan pengguna (role) saat berhasil login ke sistem.
                </p>
                <?php
                $hp_saved  = json_decode($settings['default_homepage'] ?? '{}', true) ?: [];
            $hp_rw    = $hp_saved['read-write'] ?? 'index.php';
            $hp_w     = $hp_saved['write']      ?? 'index.php';
            $hp_r     = $hp_saved['read']       ?? 'index.php';

            $homepage_options = [
                'index.php'          => '🏠 Dashboard CCTV',
                'monitoring.php'     => '📡 Monitoring IP (Live)',
                'monitoring_logs.php'=> '📋 Log Monitoring',
                'monitoring_device.php'=>'💻 Monitoring Status Device',
                'stb_mess.php'       => '📺 STB MESS',
                'kelola_stb.php'     => '⚙️ Kelola STB',
                'graphs.php'         => '📊 Graphs',
                'scan.php'           => '🔍 Scan Jaringan IP',
                'tambah.php'         => '➕ Tambah CCTV',
                'master_vlan.php'    => '🌐 Master Jaringan VLAN',
                'admin_management.php'=>'⚙️ Admin Management',
            ];

            $roles = [
                'read-write' => ['label' => 'Read-Write', 'color' => '#0d47a1', 'bg' => '#e3f2fd', 'val' => $hp_rw],
                'write'      => ['label' => 'Write Only', 'color' => '#1b5e20', 'bg' => '#e8f5e9', 'val' => $hp_w],
                'read'       => ['label' => 'Read Only',  'color' => '#555',    'bg' => '#f5f5f5', 'val' => $hp_r],
            ];
            ?>
            <form method="POST" action="<?= site_url('admin/defaultHomepage') ?>">
                <?php foreach ($roles as $role_key => $role_info): ?>
                <div class="form-group" style="background:<?= $role_info['bg'] ?>; border-radius:8px; padding:14px 16px; border:1px solid #ddd; margin-bottom:12px;">
                    <label style="color:<?= $role_info['color'] ?>; font-size:14px; margin-bottom:8px; display:block;">
                        <b><?= $role_info['label'] ?></b> — Halaman awal saat login
                    </label>
                    <select name="homepage_<?= $role_key ?>" class="form-control" style="margin-bottom:0; max-width:100%;">
                        <?php foreach ($homepage_options as $file => $desc): ?>
                            <option value="<?= $file ?>" <?= ($role_info['val'] === $file) ? 'selected' : '' ?>>
                                <?= $desc ?> (<?= $file ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>

                <button type="submit" class="btn-block" style="background:<?= esc($settings['top_color'] ?? '#0077b6') ?>; margin-top:10px;">
                    💾 Simpan Default Homepages Role
                </button>
            </form>
        </div>

        <div class="card">
            <h4>👤 Atur Halaman Default per User</h4>
            <p style="font-size:13px; color:#555; margin-bottom:20px; line-height:1.6;">
                Sesuaikan halaman pertama spesifik untuk setiap pengguna. Pengaturan ini akan mengabaikan pengaturan role.
            </p>
            <?php
            $user_hp_saved = json_decode($settings['user_homepages'] ?? '{}', true) ?: [];
            ?>
            <form method="POST" action="<?= site_url('admin/userDefaultHomepage') ?>">
                <div class="table-scroll-container" style="max-height: 400px; margin-top:0; border-radius:8px;">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Username (Role)</th>
                                <th>Halaman Awal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): 
                                $curr_hp = $user_hp_saved[$u['username']] ?? '';
                            ?>
                            <tr>
                                <td>
                                    <b><?= esc($u['username']) ?></b><br>
                                    <small style="color: #666;"><?= esc($u['role']) ?></small>
                                </td>
                                <td>
                                    <select name="homepage_user_<?= $u['id'] ?>" class="form-control" style="margin-bottom:0; padding: 6px; font-size:12px;">
                                        <option value="">-- Ikuti Default Role --</option>
                                        <?php foreach ($homepage_options as $file => $desc): ?>
                                            <option value="<?= $file ?>" <?= ($curr_hp === $file) ? 'selected' : '' ?>>
                                                <?= $desc ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn-block" style="background:<?= esc($settings['top_color'] ?? '#0077b6') ?>; margin-top:15px;">
                    💾 Simpan Default Homepages User
                </button>
            </form>
        </div>
    </div>
    <?php elseif ($view === 'schedule_bypass'): ?>
        <h3>Schedule Bell Bypass</h3><br>
        <div class="card" style="max-width: 600px;">
            <h4>Pengaturan Bypass Halaman Display Schedule Bell</h4>
            <p style="font-size:13px; color:#555; margin-bottom:15px; line-height:1.5;">
                Jika fitur Bypass diaktifkan, maka halaman <strong>Schedule Bell Display</strong> (<code>/schedule-bell/display</code>) dapat diakses langsung tanpa perlu melakukan login ke dalam sistem. Ini berguna jika halaman display akan ditampilkan di layar TV atau monitor publik secara mandiri.
            </p>
            <form method="POST" action="<?= site_url('admin/scheduleBypass') ?>">
                <?php $schedule_bypass = $settings['schedule_bypass'] ?? 0; ?>
                <div class="form-group" style="background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #ddd; margin-bottom: 10px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal; margin: 0;">
                        <input type="checkbox" name="schedule_bypass" value="1" <?= ($schedule_bypass == 1) ? 'checked' : '' ?> style="width:18px; height:18px;">
                        <div>
                            <strong style="color: #d32f2f; display: block; margin-bottom: 3px;">Aktifkan Bypass Login</strong>
                            <span style="font-size: 12px; color: #666;">Centang untuk mengizinkan akses tanpa login ke Schedule Bell Display.</span>
                        </div>
                    </label>
                </div>
                
                <button type="submit" class="btn-block" style="background: <?= esc($settings['top_color'] ?? '#0077b6') ?>; margin-top: 15px;">
                    💾 Simpan Pengaturan Bypass
                </button>
            </form>
        </div>
    <?php elseif ($view === 'ping_control'): ?>
        <h3>Ping Control System</h3><br>
        <div class="card" style="max-width: 600px;">
            <h4>Metode Pengumpulan Data Ping</h4>
            <p style="font-size:13px; color:#555; margin-bottom:15px; line-height:1.5;">
                Pilih metode utama bagaimana aplikasi ini memonitoring IP CCTV. Pilihan "Sistem PHP" adalah bawaan dimana browser client yang melakukan cek ping (sangat real-time, namun beban ada di client). Pilihan "Batch File Windows" berarti server yang melakukan ping di latar belakang, dan browser hanya membaca data terbaru dari database (lebih ringan untuk client).
            </p>
            <form method="POST" action="<?= site_url('admin/pingControl') ?>" onsubmit="return confirm('Anda yakin ingin merubah metode ping? Pastikan Anda mengikuti instruksi yang diberikan jika merubah ke mode Batch File.');">
                <?php $ping_method = $settings['ping_method'] ?? 'php'; ?>
                <div class="form-group" style="background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #ddd; margin-bottom: 10px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal; margin: 0;">
                        <input type="radio" name="ping_method" value="php" <?= ($ping_method === 'php') ? 'checked' : '' ?> style="width:18px; height:18px;">
                        <div>
                            <strong style="color: #0077b6; display: block; margin-bottom: 3px;">1. Ping via Sistem PHP (Bawaan)</strong>
                            <span style="font-size: 12px; color: #666;">Browser mengeksekusi request ping ke setiap IP.</span>
                        </div>
                    </label>
                </div>
                
                <div class="form-group" style="background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #ddd;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal; margin: 0;">
                        <input type="radio" name="ping_method" value="batch" <?= ($ping_method === 'batch') ? 'checked' : '' ?> style="width:18px; height:18px;">
                        <div>
                            <strong style="color: #28a745; display: block; margin-bottom: 3px;">2. Ping via Batch File Windows (.bat)</strong>
                            <span style="font-size: 12px; color: #666;">Server menjalankan script bat di latar belakang (Background Process). Browser hanya membaca status terbaru dari database.</span>
                        </div>
                    </label>
                </div>
                
                <button type="submit" class="btn-block" style="background: #0077b6; margin-top: 15px;">
                    💾 Simpan Metode Ping
                </button>
            </form>
            
            <?php if ($ping_method === 'batch'): ?>
            <div style="margin-top: 25px; padding-top: 15px; border-top: 2px dashed #eee;">
                <h4 style="color:#28a745; margin-bottom:8px;">Instruksi Eksekusi Batch File</h4>
                <p style="font-size:13px; color:#555; line-height:1.5;">
                    Untuk menggunakan metode ini, Anda harus memiliki script <b>run_ping.bat</b> yang berjalan di server Anda. Anda dapat men-generate file script secara otomatis ke direktori aplikasi dengan klik tombol di bawah ini.
                </p>
                <a href="<?= site_url('admin/generateBat') ?>" class="btn-block" style="background: #28a745; display: block; text-align: center; text-decoration: none; padding: 10px; margin-top: 10px;" onclick="return confirm('Generate script run_ping.bat dan daftar IP (ip_list.txt) sekarang? Ini akan menimpa file yang sudah ada.');">
                    📄 Generate Script Batch & List IP
                </a>
                <p style="font-size:12px; color:#888; margin-top: 10px; font-style:italic;">
                    * Setelah men-generate, cari file <b>run_ping.bat</b> di dalam folder public, klik dua kali untuk menjalankannya. Jangan tutup jendela hitam (Command Prompt) yang terbuka agar ping terus berjalan.
                </p>
            </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <h3>Admin Management System</h3><br>
        
        <div class="admin-grid-top">
            
            <div class="card">
                <h4><?= $editUser ? "Edit Pengguna / Hak Akses" : "Tambah User Baru" ?></h4>
                <form method="POST" action="<?= site_url('admin/storeUser') ?>">
                    <input type="hidden" name="id_user" value="<?= $editUser ? $editUser['id'] : '' ?>">
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?= $editUser ? esc($editUser['username']) : '' ?>" placeholder="Masukkan username" required autocomplete="off">
                    </div>
                    
                    <div class="form-group">
                        <label>Password <?= $editUser ? "<small style='color:blue;'>(Kosongkan jika tak ingin diubah)</small>" : "" ?></label>
                        <input type="password" name="password" class="form-control" placeholder="<?= $editUser ? 'Masukkan password baru jika diubah' : 'Masukkan password' ?>" <?= $editUser ? '' : 'required' ?>>
                    </div>
                    
                    <div class="form-group">
                        <label>Hak Akses Tingkatan (Role)</label>
                        <select name="role" class="form-control" required>
                            <?php $r = $editUser ? $editUser['role'] : ''; ?>
                            <option value="read" <?= ($r === 'read') ? 'selected' : '' ?>>Read Only</option>
                            <option value="write" <?= ($r === 'write') ? 'selected' : '' ?>>Write Only</option>
                            <option value="read-write" <?= ($r === 'read-write') ? 'selected' : '' ?>>Read-Write</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-block" style="background: <?= $editUser ? '#0077b6' : '#28a745' ?>;">
                        <?= $editUser ? "💾 Update Data Pengguna" : "➕ Simpan Pengguna Baru" ?>
                    </button>
    
                    <?php if ($editUser): ?>
                        <a href="<?= site_url('admin') ?>" style="display:block; text-align:center; margin-top:10px; color:#dc3545; font-weight:bold; text-decoration:none; font-size:14px;">❌ Batal Edit</a>
                    <?php endif; ?>
                </form>
            </div>
    
            <div class="card">
                <h4>Atur Hak Izin Halaman Web</h4>
                <form method="POST" action="<?= site_url('admin') ?>">
                    <div class="form-group">
                        <label>Pilih Pengguna (User)</label>
                        <select name="user_update" class="form-control" onchange="this.form.submit()" required>
                            <option value="">-- Pilih Pengguna untuk Dikonfigurasi --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= esc($u['username']) ?>" <?= (($user_update ?? '') == $u['username']) ? 'selected' : '' ?>>
                                    <?= esc($u['username']) ?> (<?= esc($u['role']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                
                <?php if(!empty($user_update)): ?>
                <form method="POST" action="<?= site_url('admin/updateAccess') ?>">
                    <input type="hidden" name="user_update" value="<?= esc($user_update) ?>">
                    
                    <div class="form-group">
                        <label>Daftar Halaman Aplikasi (Beri Centang):</label>
                        <div class="checklist-container" style="max-height: 500px;">
                            <?php
                            $actionable_pages = ['list_cctv.php', 'master_nama.php', 'master_nvr.php', 'cctv_by_nvr.php', 'master_vlan.php', 'setup_monitoring.php', 'admin_management.php', 'stb_mess.php', 'kelola_stb.php'];
                            
                            foreach($pageCategories as $category_name => $pages) {
                                echo '<div style="margin-bottom: 15px;">';
                                echo '<span style="font-weight: bold; font-size: 14px; color: #0077b6; display: block; border-bottom: 2px solid #ccc; padding-bottom: 5px; margin-bottom: 10px;">📁 '.esc($category_name).'</span>';
                                echo '<div style="padding-left: 10px; display: flex; flex-direction: column; gap: 8px;">';
                                
                                foreach($pages as $page => $label) {
                                    if (is_array($label)) {
                                        // It's a parent menu with sub-menus
                                        $parent_checked = in_array($page, $grantedPages) ? 'checked' : '';
                                        
                                        echo '<div style="background: #fff; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; display: flex; flex-direction: column; transition: background 0.2s;">';
                                        echo '<label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer; margin: 0; width: 100%;">';
                                        echo '<input type="checkbox" name="pages[]" value="'.$page.'" '.$parent_checked.' style="width: 18px; height: 18px; margin: 0; cursor: pointer;">';
                                        echo '<span style="font-size: 13.5px; color: #333; font-weight: bold;">'.$label['label'].'</span>';
                                        echo '</label>';
                                        
                                        if (isset($label['sub_menus'])) {
                                            echo '<div style="padding-left: 28px; display: flex; flex-direction: column; gap: 6px; margin-top: 8px; border-left: 2px solid #eee; margin-left: 8px;">';
                                            foreach ($label['sub_menus'] as $sub_page => $sub_label) {
                                                $sub_checked = in_array($sub_page, $grantedPages) ? 'checked' : '';
                                                echo '<label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer; margin: 0; width: 100%;">';
                                                echo '<input type="checkbox" name="pages[]" value="'.$sub_page.'" '.$sub_checked.' style="width: 16px; height: 16px; margin: 0; cursor: pointer;">';
                                                echo '<span style="font-size: 12.5px; color: #555; font-weight: 500;">↳ '.$sub_label.'</span>';
                                                echo '</label>';
                                            }
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                    } else {
                                        // Standard menu
                                        $checked = in_array($page, $grantedPages) ? 'checked' : '';
                                        
                                        echo '<div style="background: #fff; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; transition: background 0.2s;">';
                                        
                                        echo '<label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer; margin: 0; flex: 1;">';
                                        echo '<input type="checkbox" name="pages[]" value="'.$page.'" '.$checked.' style="width: 18px; height: 18px; margin: 0; cursor: pointer;">';
                                        echo '<span style="font-size: 13.5px; color: #333; font-weight: 500;">'.$label.'</span>';
                                        echo '</label>';
                                        
                                        echo '</div>';
                                    }
                                }
                                echo '</div></div>';
                            }
                            ?>
                            
                            <div style="margin-top: 20px; margin-bottom: 10px;">
                                <span style="font-weight: bold; font-size: 14px; color: #28a745; display: block; border-bottom: 2px solid #ccc; padding-bottom: 5px; margin-bottom: 10px;">
                                    🌐 Akses IP VLAN (Monitoring)
                                </span>
                                <div style="padding-left: 10px; display: flex; flex-direction: column; gap: 8px;">
                                    <?php 
                                    if(count($vlanList) > 0) {
                                        foreach($vlanList as $v) {
                                            $checked = in_array($v['id'], $grantedVlans) ? 'checked' : '';
                                            echo '<div style="background: #fff; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px;">';
                                            echo '<label style="display: flex; align-items: center; gap: 10px; font-weight: normal; cursor: pointer; margin: 0;">';
                                            echo '<input type="checkbox" name="vlans[]" value="'.$v['id'].'" '.$checked.' style="width: 18px; height: 18px; margin: 0; cursor: pointer;">';
                                            echo '<span style="font-size: 13.5px; color: #333; font-weight: 500;">'.htmlspecialchars($v['nama_vlan']).' ('.$v['network_ip'].'.x)</span>';
                                            echo '</label>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo '<p style="color:#999; font-size:13px; margin: 0;">Belum ada VLAN terdaftar.</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-block" style="background:#0077b6;">Update Izin Akses <?= htmlspecialchars($user_update) ?></button>
                </form>
                <?php else: ?>
                    <p style="color:#777; font-size:13px; font-style:italic; text-align:center; margin-top:40px;">
                        Silakan pilih pengguna di atas untuk melakukan konfigurasi pembatasan akses halaman.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    
        <div class="admin-grid-bottom" style="margin-top: 25px;">
            <div class="card">
                <h4>Daftar Pengguna Aplikasi Terdaftar</h4>
                
                <div class="table-scroll-container">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th width="8%">No</th>
                                <th>Nama Pengguna (Username)</th>
                                <th width="25%">Tingkatan Akses (Role)</th>
                                <th width="20%" style="text-align:center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if(count($users) > 0) {
                                foreach($users as $user_row) {
                                    $role_class = ($user_row['role'] === 'admin') ? 'role-admin' : (($user_row['role'] === 'operator') ? 'role-operator' : 'role-user');
                                    
                                    echo "<tr>
                                            <td>".$no++."</td>
                                            <td><b>".htmlspecialchars($user_row['username'])."</b></td>
                                            <td><span class='badge-role ".$role_class."'>".$user_row['role']."</span></td>
                                            <td style='text-align:center;'>
                                                <a href='".site_url('admin?edit_user_id='.$user_row['id'])."' class='btn-edit'>Edit Role</a> | 
                                                <a href='".site_url('admin/deleteUser/'.$user_row['id'])."' 
                                                   class='btn-hapus' 
                                                   onclick='return confirm(\"Apakah Anda yakin ingin menghapus user ".htmlspecialchars($user_row['username'])." dari sistem?\")'>
                                                   Hapus
                                                </a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center;'>Belum ada user yang didaftarkan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
