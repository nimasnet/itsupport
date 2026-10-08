<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .right-frame {
        background: #f4f6fa;
        padding: 30px 25px;
        min-height: calc(100vh - 60px);
        overflow-y: auto;
    }
    .welcome-header {
        margin-bottom: 25px;
    }
    .welcome-header h3 {
        font-size: 20px;
        color: #2b3a4a;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .welcome-header p {
        font-size: 12px;
        color: #6c757d;
    }
    .grid-container {
        display: grid;
        grid-template-columns: repeat(3, 200px);
        gap: 15px;
        max-width: 630px;
        margin: 0;
    }
    .menu-card {
        background: #ffffff;
        border-radius: 8px;
        padding: 15px;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 3px 8px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 170px;
        position: relative;
        overflow: hidden;
    }
    .menu-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        transition: all 0.3s ease;
    }
    .card-user::before { background: linear-gradient(90deg, #0077b6, #00b4d8); }
    .card-switch::before { background: linear-gradient(90deg, #28a745, #34ce57); }
    .card-cekrool::before { background: linear-gradient(90deg, #fd7e14, #ff922b); }

    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    .menu-card:hover::before {
        height: 4px;
    }
    .card-icon {
        font-size: 24px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        transition: transform 0.3s ease;
    }
    .card-user .card-icon { background: rgba(0, 119, 182, 0.1); color: #0077b6; }
    .card-switch .card-icon { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .card-cekrool .card-icon { background: rgba(253, 126, 20, 0.1); color: #fd7e14; }

    .menu-card:hover .card-icon {
        transform: scale(1.08);
    }
    .card-body h4 {
        font-size: 15px;
        color: #2b3a4a;
        margin-bottom: 6px;
        font-weight: 600;
    }
    .card-body p {
        font-size: 11px;
        color: #6c757d;
        line-height: 1.4;
        margin-bottom: 12px;
    }
    .card-footer {
        display: flex;
        align-items: center;
        font-size: 11px;
        font-weight: bold;
        transition: color 0.2s ease;
    }
    .card-user .card-footer { color: #0077b6; }
    .card-switch .card-footer { color: #28a745; }
    .card-cekrool .card-footer { color: #fd7e14; }

    .card-footer span {
        margin-left: 4px;
        transition: transform 0.2s ease;
    }
    .menu-card:hover .card-footer span {
        transform: translateX(4px);
    }
</style>

<div class="right-frame">
    <div class="welcome-header">
        <h3>🌐 IP List Hub & Management</h3>
        <p>Silakan pilih modul administrasi di bawah untuk mengelola data infrastruktur jaringan.</p>
    </div>

    <div class="grid-container">
        <!-- User PC Card -->
        <a href="<?= site_url('ip-management/user-pc') ?>" class="menu-card card-user">
            <div>
                <div class="card-icon">💻</div>
                <div class="card-body">
                    <h4>User PC</h4>
                    <p>Kelola pendataan IP Address, MAC Address, tipe perangkat, pemilik, dan status perangkat user di jaringan local.</p>
                </div>
            </div>
            <div class="card-footer">
                Buka Modul <span>➔</span>
            </div>
        </a>

        <!-- Switch Network Card -->
        <a href="<?= site_url('ip-management/switch-network') ?>" class="menu-card card-switch">
            <div>
                <div class="card-icon">🔌</div>
                <div class="card-body">
                    <h4>Switch Network</h4>
                    <p>Pemantauan konfigurasi port, status koneksi perangkat Switch Network, dan alokasi VLAN jaringan secara real-time.</p>
                </div>
            </div>
            <div class="card-footer">
                Buka Modul <span>➔</span>
            </div>
        </a>

        <!-- Cekrool Card -->
        <a href="<?= site_url('ip-management/cekrool') ?>" class="menu-card card-cekrool">
            <div>
                <div class="card-icon">🔑</div>
                <div class="card-body">
                    <h4>Cekrool</h4>
                    <p>Validasi hak akses pengguna, status otorisasi peran (roles), dan audit aktivitas administrasi sistem IT Support.</p>
                </div>
            </div>
            <div class="card-footer">
                Buka Modul <span>➔</span>
            </div>
        </a>
    </div>
</div>

<?= $this->endSection() ?>
