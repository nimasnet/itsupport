<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .btn-nama {
        display: inline-block;
        padding: 6px 15px;
        background-color: #fff;
        color: #0077b6;
        text-decoration: none;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        border: 2px solid #0077b6;
        transition: all 0.2s;
    }
    .btn-nama:hover { background-color: #0077b6; color: white; }
</style>

<div class="right-frame">
    <h3><?= isset($editData) ? "Edit Master Nama CCTV" : "Kelola Master Nama CCTV" ?></h3><br>
    
    <?php if ($page_perm !== 'R'): ?>
    <form action="<?= site_url('master/nama/store') ?>" method="POST">
        <input type="hidden" name="id_nama" value="<?= isset($editData) ? $editData['id'] : '' ?>">
        
        <div class="form-group">
            <label>Nama CCTV</label>
            <input type="text" name="nama_cctv" value="<?= isset($editData) ? esc($editData['nama_cctv']) : '' ?>" placeholder="Contoh: CCTV Lobby Utama" required>
        </div>
        
        <button type="submit"><?= isset($editData) ? "Update Nama CCTV" : "Tambahkan Nama Baru" ?></button>
        
        <?php if (isset($editData)): ?>
            <a href="<?= site_url('master/nama') ?>" style="margin-left: 10px; color: #dc3545; text-decoration: none; font-weight: bold;">Batal Edit</a>
        <?php endif; ?>
    </form>
    <?php else: ?>
    <div style="background:#fff3cd; color:#856404; padding:10px; border-radius:4px; border:1px solid #ffeeba;">
        Melihat dalam mode <b>Read-Only</b>. Anda tidak memiliki izin untuk menambah atau mengedit.
    </div>
    <?php endif; ?>

    <hr style="margin: 30px 0;">

    <h3>Cloud Tags Nama CCTV</h3>
    <div style="margin-top: 15px; margin-bottom: 30px;">
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <?php if (!empty($namaList)): ?>
                <?php foreach($namaList as $row): ?>
                    <span class="btn-nama"><?= esc($row['nama_cctv']) ?></span>
                <?php endforeach; ?>
            <?php else: ?>
                <span style="color:#888;">Belum ada data Nama CCTV.</span>
            <?php endif; ?>
        </div>
    </div>

    <h3>Tabel Nama CCTV Terdaftar</h3>
    <table style="max-width: 600px;">
        <thead>
            <tr>
                <th width="10%">No</th>
                <th>Nama CCTV</th>
                <th width="25%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($namaList)): ?>
                <?php $no = 1; foreach($namaList as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><b><?= esc($row['nama_cctv']) ?></b></td>
                        <td>
                        <?php if ($page_perm !== 'R'): ?>
                            <a href="<?= site_url('master/nama?edit_nama='.$row['id']) ?>" class="btn-edit">Edit</a> | 
                            <a href="<?= site_url('master/nama/delete/'.$row['id']) ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus nama CCTV ini?')">Hapus</a>
                        <?php else: ?>
                            <span style='color:#999; font-size:12px; font-style:italic;'>Akses Baca</span>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan='3' style='text-align:center;'>Belum ada data Nama CCTV.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
