<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="right-frame">
    <h3><?= isset($editData) ? "Edit Jaringan VLAN" : "Kelola Master Jaringan VLAN" ?></h3><br>
    
    <?php if ($page_perm !== 'R'): ?>
    <form action="<?= site_url('master/vlan/store') ?>" method="POST">
        <input type="hidden" name="id_vlan" value="<?= isset($editData) ? $editData['id'] : '' ?>">
        
        <div class="form-group">
            <label>Nama VLAN (Label)</label>
            <input type="text" name="nama_vlan" value="<?= isset($editData) ? esc($editData['nama_vlan']) : 'VLAN-' ?>" placeholder="Contoh: VLAN-60" required>
        </div>
        
        <div class="form-group">
            <label>Blok IP Network (Tanpa angka belakang)</label>
            <input type="text" name="network_ip" id="input_network" value="<?= isset($editData) ? esc($editData['network_ip']) : '' ?>" placeholder="Contoh: 192.168.60" required autocomplete="off">
            <small style="color:#666; display:block; margin-top:5px;">*Ketik 3 angka atau tekan <b>Spasi</b> untuk otomatis membuat titik.</small>
        </div>
        
        <button type="submit"><?= isset($editData) ? "Update Data VLAN" : "Tambahkan VLAN Baru" ?></button>
        
        <?php if (isset($editData)): ?>
            <a href="<?= site_url('master/vlan') ?>" style="margin-left: 10px; color: #dc3545; text-decoration: none; font-weight: bold;">Batal Edit</a>
        <?php endif; ?>
    </form>
    <?php else: ?>
    <div style="background:#fff3cd; color:#856404; padding:10px; border-radius:4px; border:1px solid #ffeeba;">
        Melihat dalam mode <b>Read-Only</b>. Anda tidak memiliki izin untuk menambah atau mengedit VLAN.
    </div>
    <?php endif; ?>

    <hr style="margin: 30px 0;">

    <h3>Daftar VLAN Terdaftar</h3>
    <table style="max-width: 600px;">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Nama VLAN</th>
                <th>Network IP Address</th>
                <th width="20%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($vlanList)): ?>
                <?php $no = 1; foreach($vlanList as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><b><?= esc($row['nama_vlan']) ?></b></td>
                        <td><?= esc($row['network_ip']) ?>.XXX</td>
                        <td>
                        <?php if ($page_perm !== 'R'): ?>
                            <a href="<?= site_url('master/vlan?edit_vlan='.$row['id']) ?>" class="btn-edit">Edit</a> | 
                            <a href="<?= site_url('master/vlan/delete/'.$row['id']) ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus VLAN ini?')">Hapus</a>
                        <?php else: ?>
                            <span style='color:#999; font-size:12px; font-style:italic;'>Akses Baca</span>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan='4' style='text-align:center;'>Belum ada data VLAN.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ipInput = document.getElementById('input_network');
    if (!ipInput) return;

    ipInput.addEventListener('keydown', function(e) {
        if (e.key === ' ' || e.code === 'Space') {
            e.preventDefault(); 
            let val = this.value;
            let dots = (val.match(/\./g) || []).length;
            if (val !== '' && !val.endsWith('.') && dots < 2) {
                this.value += '.';
            }
        }
    });

    ipInput.addEventListener('input', function(e) {
        let val = this.value.replace(/[^0-9.]/g, '');
        if (val.startsWith('.')) val = val.substring(1);
        val = val.replace(/\.+/g, '.');

        let blocks = val.split('.');
        if (blocks.length > 3) {
            blocks = blocks.slice(0, 3);
        }

        for (let i = 0; i < blocks.length; i++) {
            blocks[i] = blocks[i].substring(0, 3);
        }
        val = blocks.join('.');

        if (e.inputType !== 'deleteContentBackward') {
            let currentBlocks = val.split('.');
            if (currentBlocks.length < 3) {
                let lastBlock = currentBlocks[currentBlocks.length - 1];
                if (lastBlock.length === 3) {
                    val += '.';
                }
            }
        }
        this.value = val;
    });
});
</script>
<?= $this->endSection() ?>
