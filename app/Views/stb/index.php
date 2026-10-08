<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="right-frame">
    <h3>Data STB MESS</h3>
    
    <div class="header-action" style="margin: 15px 0 20px 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <form method="GET" action="<?= site_url('stb/mess') ?>" style="display: flex; gap: 5px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Cari data..." value="<?= esc($search) ?>" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-width: 250px;">
            
            <div style="display: flex; align-items: center; background: #f8f9fa; border: 1px solid #ddd; padding: 4px 10px; border-radius: 4px; gap: 8px; font-size: 13px;">
                <span style="color: #0077b6; font-weight: bold;">🔀 Urutkan:</span>
                <select name="sort_by" onchange="this.form.submit()" style="padding: 4px; border: 1px solid #ccc; border-radius: 3px;">
                    <option value="lokasi" <?= isset($sort_by) && $sort_by == 'lokasi' ? 'selected' : '' ?>>Lokasi</option>
                    <option value="kamar_no" <?= isset($sort_by) && $sort_by == 'kamar_no' ? 'selected' : '' ?>>Kamar No.</option>
                    <option value="nama_user" <?= isset($sort_by) && $sort_by == 'nama_user' ? 'selected' : '' ?>>Nama User</option>
                    <option value="ip_address" <?= isset($sort_by) && $sort_by == 'ip_address' ? 'selected' : '' ?>>IP Address</option>
                </select>
                <select name="sort_dir" onchange="this.form.submit()" style="padding: 4px; border: 1px solid #ccc; border-radius: 3px;">
                    <option value="ASC" <?= isset($sort_dir) && $sort_dir == 'ASC' ? 'selected' : '' ?>>▲ ASC</option>
                    <option value="DESC" <?= isset($sort_dir) && $sort_dir == 'DESC' ? 'selected' : '' ?>>▼ DESC</option>
                </select>
            </div>

            <button type="submit" style="background-color: #0077b6; padding: 7px 15px;">Cari / Refresh</button>
            <?php if (!empty($search)): ?>
            <a href="<?= site_url('stb/mess') ?>" style="text-decoration: none;"><button type="button" style="background-color: #6c757d; padding: 7px 15px;">Reset</button></a>
            <?php endif; ?>
        </form>

        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="<?= site_url('stb/exportExcel') ?>?search=<?= urlencode($search) ?>&sort_by=<?= esc($sort_by ?? 'lokasi') ?>&sort_dir=<?= esc($sort_dir ?? 'ASC') ?>" style="text-decoration: none;" onclick="return confirmExport(event, this);">
                <button type="button" style="background-color: #28a745;">📥 Export Excel</button>
            </a>
            <?php if ($page_perm !== 'R'): ?>
            <button type="button" style="background-color: #17a2b8;" onclick="openImportModalStb()">📥 Import Excel</button>
            <a href="<?= site_url('stb/kelola') ?>" style="text-decoration: none;">
                <button type="button" style="background-color: #0077b6;">+ Tambah Data STB</button>
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <table style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 1%; white-space: nowrap;">No</th>
                <th style="width: 1%; white-space: nowrap;">Kamar No.</th>
                <th style="width: 1%; white-space: nowrap;">Nama User / Device Name</th>
                <th style="width: 1%; white-space: nowrap;">Lokasi</th>
                <th style="width: 1%; white-space: nowrap;">IP Address</th>
                <th style="width: auto;">Keterangan</th>
                <th style="width: 1%; white-space: nowrap;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_data = count($stbList);
            
            if ($total_data > 0) {
                foreach($stbList as $row) {
                    echo "<tr>";
                    echo "
                        <td style='white-space: nowrap;'>".$no++."</td>
                        <td style='white-space: nowrap;'>".esc($row['kamar_no'])."</td>
                        <td style='white-space: nowrap;'>".esc($row['nama_user'])."</td>
                        <td style='white-space: nowrap;'>".esc($row['lokasi'])."</td>
                        <td style='white-space: nowrap;'><b>".esc($row['ip_address'])."</b></td>
                        <td>".esc($row['keterangan'])."</td>
                        <td style='white-space: nowrap;'>";
                    if ($page_perm !== 'R') {
                        echo "<a href='".site_url('stb/edit/'.$row['id'])."' class='btn-edit'>Edit</a> | 
                              <a href='".site_url('stb/delete/'.$row['id'])."' class='btn-hapus' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Hapus</a>";
                    } else {
                        echo "<span style='color:#999; font-size:12px; font-style:italic;'>Akses Baca</span>";
                    }
                    echo "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center; padding: 20px;'>Belum ada data STB.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

    <!-- Modal Import Excel -->
    <div id="importModalStb" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
        <div style="background-color:#fff; margin:5% auto; padding:20px; border-radius:8px; width:450px; position:relative; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <span style="position:absolute; right:15px; top:10px; font-size:24px; cursor:pointer;" onclick="closeImportModalStb()">&times;</span>
            <h3 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Import STB dari Excel</h3>
            
            <div style="margin-bottom:15px;">
                <a href="<?= site_url('stb/downloadTemplate') ?>" style="text-decoration:none;">
                    <button type="button" style="background:#28a745; color:white; border:none; padding:6px 12px; border-radius:4px; font-size:12px; cursor:pointer;">
                        ⬇️ Download Template Excel
                    </button>
                </a>
            </div>

            <form action="<?= site_url('stb/importExcel') ?>" method="POST" enctype="multipart/form-data">
                <div style="margin-bottom:15px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Pilih File Excel (.xls, .xlsx, .csv)</label>
                    <input type="file" name="file_excel" accept=".xls,.xlsx,.csv" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing: border-box;">
                </div>
                <div style="background-color:#e2e3e5; color:#383d41; padding:12px; border-radius:4px; font-size:12px; margin-bottom:15px; line-height:1.5;">
                    <b>Format Kolom Excel (Mulai Baris 2):</b><br>
                    A: Kamar No.<br>
                    B: Nama User<br>
                    C: Lokasi<br>
                    D: IP Address<br>
                    E: Keterangan
                </div>
                <div style="text-align:right;">
                    <button type="button" style="background:#6c757d; color:#fff; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; margin-right:5px;" onclick="closeImportModalStb()">Batal</button>
                    <button type="submit" style="background:#17a2b8; color:#fff; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; font-weight:bold;">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openImportModalStb() {
    document.getElementById('importModalStb').style.display = 'block';
}
function closeImportModalStb() {
    document.getElementById('importModalStb').style.display = 'none';
}

function confirmExport(event, el) {
    event.preventDefault();
    const total = <?= $total_data ?>;
    if (total === 0) {
        alert("Tidak ada data untuk diekspor.");
        return false;
    }
    
    if (confirm("Anda akan mengekspor " + total + " data. Lanjutkan?")) {
        window.location.href = el.href;
    }
}
</script>
<?= $this->endSection() ?>
