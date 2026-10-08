<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="right-frame">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0;"><?= $edit_data ? 'Edit' : 'Tambah' ?> Data STB</h3>
        <a href="<?= site_url('stb/import?download_template=1') ?>" style="text-decoration: none;">
            <button type="button" style="background-color: #28a745; font-size: 13px; padding: 8px 15px; font-weight: bold; border-radius: 4px; border: none; color: white; cursor: pointer;">
                📥 Download Template Excel
            </button>
        </a>
    </div>
    
    <form method="POST" action="<?= site_url('stb/store') ?>">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Kamar No.</label>
            <select name="kamar_no" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <option value="">-- Pilih Kamar --</option>
                <option value="-" <?= ($edit_data && $edit_data['kamar_no'] == '-') ? 'selected' : '' ?>>-</option>
                <?php for($i=1; $i<=100; $i++): ?>
                    <option value="<?= $i ?>" <?= ($edit_data && $edit_data['kamar_no'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Nama User</label>
            <input type="text" name="nama_user" value="<?= $edit_data ? esc($edit_data['nama_user']) : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label>Lokasi</label>
            <select name="lokasi" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                <option value="">-- Pilih Lokasi --</option>
                <option value="Lantai 1" <?= ($edit_data && $edit_data['lokasi'] == 'Lantai 1') ? 'selected' : '' ?>>Lantai 1</option>
                <option value="Lantai 2" <?= ($edit_data && $edit_data['lokasi'] == 'Lantai 2') ? 'selected' : '' ?>>Lantai 2</option>
                <option value="Lantai 3" <?= ($edit_data && $edit_data['lokasi'] == 'Lantai 3') ? 'selected' : '' ?>>Lantai 3</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>IP Address</label>
            <div style="display: flex;">
                <span style="padding: 8px 12px; background: #eee; border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px; color: #555; box-sizing: border-box;">192.168.46.</span>
                <?php 
                    $last_ip = '';
                    if ($edit_data && strpos($edit_data['ip_address'], '192.168.46.') === 0) {
                        $last_ip = substr($edit_data['ip_address'], 11);
                    } else if ($edit_data) {
                        $last_ip = $edit_data['ip_address'];
                    }
                ?>
                <input type="text" id="ip_last" value="<?= esc($last_ip) ?>" required style="flex: 1; border-radius: 0 4px 4px 0; border: 1px solid #ccc; padding: 8px; box-sizing: border-box;" placeholder="xx (ketik - jika ingin dikosongkan)">
                <input type="hidden" name="ip_address" id="ip_full" value="<?= $edit_data ? esc($edit_data['ip_address']) : '192.168.46.' ?>">
            </div>
            <script>
                document.getElementById('ip_last').addEventListener('input', function() {
                    let val = this.value.trim();
                    if (val === '-') {
                        document.getElementById('ip_full').value = '-';
                    } else if(val !== '') {
                        document.getElementById('ip_full').value = '192.168.46.' + val;
                    } else {
                        document.getElementById('ip_full').value = '';
                    }
                });
            </script>
        </div>
        
        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" rows="3"><?= $edit_data ? esc($edit_data['keterangan']) : '' ?></textarea>
        </div>
        
        <button type="submit"><?= $edit_data ? 'Update' : 'Simpan' ?> Data</button>
        <a href="<?= site_url('stb/mess') ?>" style="margin-left: 10px; text-decoration: none;">
            <button type="button" style="background-color: #6c757d;">Batal</button>
        </a>
    </form>
</div>
<?= $this->endSection() ?>
