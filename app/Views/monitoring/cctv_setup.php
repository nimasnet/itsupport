<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="right-frame" style="background: #f4f6fa; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>⚙️ Setup Monitoring IP</h2>
        <?php if (!empty($setup['last_run'])): ?>
            <span style="background: #e8f5e9; padding: 5px 15px; border-radius: 20px; font-size: 13px; color: #28a745; border: 1px solid #c8e6c9;">
                Terakhir Dijalankan: <b><?= date('d-m-Y H:i:s', strtotime($setup['last_run'])) ?></b>
            </span>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style='background-color:#d4edda; color:#155724; padding:10px; border-radius:4px; margin-bottom:15px;'>
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style='background-color:#f8d7da; color:#721c24; padding:10px; border-radius:4px; margin-bottom:15px;'>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 800px;">
        <form method="POST" action="<?= site_url('monitoring/cctv/setup/save') ?>">
            <div class="form-group">
                <label for="interval_minutes" style="font-weight: bold; display: block; margin-bottom: 5px;">Interval Akumulasi Ping (Menit)</label>
                <p style="font-size: 13px; color: #666; margin-bottom: 10px;">Berapa menit sekali sistem akan mem-ping semua IP dan menyimpannya ke database log.</p>
                <input type="number" name="interval_minutes" id="interval_minutes" value="<?= esc($setup['interval_minutes']) ?>" min="1" required style="width: 150px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin-top: 25px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Pilih VLAN yang akan di-monitoring otomatis:</label>
                <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Centang VLAN di bawah ini. Hanya IP dari VLAN yang dicentang yang akan disimpan log pingnya.</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; background: #f8f9fa; padding: 15px; border: 1px solid #e0e0e0; border-radius: 6px;">
                    <?php if (count($vlan_list) == 0): ?>
                        <p style="color: #999; grid-column: 1 / -1;">Tidak ada VLAN. Tambahkan di menu Master Jaringan VLAN.</p>
                    <?php else: ?>
                        <?php foreach ($vlan_list as $v): ?>
                            <?php $isChecked = in_array($v['id'], $selected_vlans) ? 'checked' : ''; ?>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                <input type="checkbox" name="vlan_ids[]" value="<?= esc($v['id']) ?>" <?= $isChecked ?> style="width: 18px; height: 18px;">
                                <span style="line-height: 1.2;">
                                    <?= htmlspecialchars($v['nama_vlan']) ?> <br>
                                    <small style="color:#888;">(<?= htmlspecialchars($v['network_ip']) ?>.x)</small>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 10px; flex-wrap: wrap;">
                <?php if ($page_perm !== 'R'): ?>
                <button type="submit" style="background-color: #28a745; color: white; border: none; border-radius: 5px; font-size: 15px; padding: 10px 25px; cursor: pointer; font-weight: bold; transition: background 0.2s;">
                    💾 Simpan Konfigurasi
                </button>
                <button type="button" onclick="runManualPing()" id="btnManualPing" style="background-color: #0077b6; color: white; border: none; border-radius: 5px; font-size: 15px; padding: 10px 25px; cursor: pointer; font-weight: bold; transition: background 0.2s;">
                    🚀 Jalankan Ping Sekarang
                </button>
                <a href="<?= site_url('monitoring/cctv/logs') ?>" style="background-color: #6c757d; color: white; border: none; border-radius: 5px; font-size: 15px; padding: 10px 25px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; font-weight: bold; transition: background 0.2s;">
                    📋 Lihat Log
                </a>
                <?php else: ?>
                <div style="background:#fff3cd; color:#856404; padding:10px; border-radius:4px; border:1px solid #ffeeba; width: 100%;">
                    Melihat dalam mode <b>Read-Only</b>. Anda tidak memiliki izin untuk menyimpan konfigurasi atau menjalankan ping.
                </div>
                <a href="<?= site_url('monitoring/cctv/logs') ?>" style="background-color: #6c757d; color: white; border: none; border-radius: 5px; font-size: 15px; padding: 10px 25px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; font-weight: bold;">
                    📋 Lihat Log
                </a>
                <?php endif; ?>
            </div>
            
            <div id="pingStatusMsg" style="margin-top: 20px; display: none; padding: 15px; border-radius: 6px; border-left: 5px solid; font-size: 14px; line-height: 1.5;"></div>
        </form>
    </div>
</div>

<script>
function runManualPing() {
    const btn = document.getElementById('btnManualPing');
    const msgBox = document.getElementById('pingStatusMsg');
    
    btn.disabled = true;
    btn.innerHTML = "⏳ Mengeksekusi Ping...";
    msgBox.style.display = 'block';
    msgBox.style.backgroundColor = '#fff3cd';
    msgBox.style.color = '#856404';
    msgBox.style.borderColor = '#ffeeba';
    msgBox.innerText = "Sedang melakukan ping ke semua IP terdaftar di VLAN terpilih. Harap tunggu, ini mungkin memakan waktu beberapa saat...";
    
    fetch('<?= site_url('monitoring/cctv/setup/run') ?>?manual=1')
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = "🚀 Jalankan Ping Sekarang";
        if (data.status === 'success') {
            msgBox.style.backgroundColor = '#d4edda';
            msgBox.style.color = '#155724';
            msgBox.style.borderColor = '#c3e6cb';
            msgBox.innerHTML = `<strong>✅ Berhasil! Ping selesai dilakukan.</strong><br> Total IP di-ping: ${data.total_pinged}<br> Total Online: ${data.total_online}<br> Total Offline: ${data.total_offline}<br> Waktu eksekusi: ${data.execution_time} detik.`;
            setTimeout(() => location.reload(), 3000);
        } else {
            msgBox.style.backgroundColor = '#f8d7da';
            msgBox.style.color = '#721c24';
            msgBox.style.borderColor = '#f5c6cb';
            msgBox.innerHTML = "<strong>❌ Terjadi kesalahan:</strong> " + (data.message || "Unknown error");
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = "🚀 Jalankan Ping Sekarang";
        msgBox.style.backgroundColor = '#f8d7da';
        msgBox.style.color = '#721c24';
        msgBox.style.borderColor = '#f5c6cb';
        msgBox.innerHTML = "<strong>❌ Terjadi kesalahan server/koneksi saat memanggil API.</strong>";
    });
}
</script>

<?= $this->endSection() ?>
