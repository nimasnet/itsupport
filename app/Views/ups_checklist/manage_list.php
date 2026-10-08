<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    .ups-page { font-family: 'Inter', Arial, sans-serif; }
    
    .ups-header {
        display: flex; align-items: center; gap: 14px; margin-bottom: 24px;
    }
    .ups-header-icon {
        width: 50px; height: 50px;
        background: linear-gradient(135deg, #4f46e5, #8b5cf6);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
        box-shadow: 0 4px 15px rgba(79,70,229,0.35);
    }
    .ups-header h2 { margin: 0; font-size: 22px; font-weight: 700; color: #1e293b; }
    .ups-header p { margin: 2px 0 0; font-size: 13px; color: #64748b; }

    .ups-layout { display: flex; gap: 24px; align-items: flex-start; }
    .form-section { flex: 0 0 350px; min-width: 350px; }
    .table-section { flex: 1; min-width: 0; }

    .ups-card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 2px 24px rgba(30,64,175,0.08);
        border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 28px;
    }
    .ups-card-header {
        background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
        padding: 18px 24px; display: flex; align-items: center; gap: 10px;
    }
    .ups-card-header h3 { margin: 0; color: #fff; font-size: 15px; font-weight: 600; }
    .ups-card-header span { font-size: 18px; }
    .ups-card-body { padding: 24px; }

    .form-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .form-field label { font-size: 13px; font-weight: 600; color: #374151; }
    .ups-input {
        padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px;
        font-size: 14px; color: #1e293b; transition: all 0.2s;
    }
    .ups-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99,102,241,0.1); }

    .btn-submit {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff; border: none; padding: 12px 24px; border-radius: 8px;
        font-weight: 600; font-size: 14px; cursor: pointer; width: 100%;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-submit:hover {
        transform: translateY(-1px); box-shadow: 0 6px 15px rgba(99,102,241,0.3);
    }

    .ups-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .ups-table thead tr { background: linear-gradient(135deg, #4f46e5, #6366f1); }
    .ups-table th { background: transparent; color: #fff; padding: 11px 14px; font-weight: 600; text-align: left; border-bottom: none; }
    .ups-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; color: #374151; }
    .ups-table tbody tr:hover { background: #f8fafc; }

    .btn-delete {
        background: #fee2e2; color: #dc2626; border: none; padding: 6px 12px;
        border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;
    }
    .btn-delete:hover { background: #fca5a5; color: #991b1b; }

    .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    @media(max-width: 1024px) {
        .ups-layout { flex-direction: column; }
        .form-section, .table-section { flex: auto; width: 100%; }
    }
</style>

<div class="ups-page right-frame">

    <div class="ups-header">
        <div class="ups-header-icon">🗺️</div>
        <div>
            <h2>UPS Manage List</h2>
            <p>Kelola daftar lokasi UPS (Database Location)</p>
        </div>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">✅ <?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">❌ <?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="ups-layout">
        <!-- FORM SECTION -->
        <div class="ups-card form-section">
            <div class="ups-card-header">
                <span>➕</span>
                <h3>Tambah Lokasi UPS</h3>
            </div>
            <div class="ups-card-body">
                <form method="POST" action="<?= site_url('monitoring-support/ups-manage-list/store') ?>">
                    <?= csrf_field() ?>
                    <div class="form-field">
                        <label>Nama Lokasi UPS</label>
                        <input type="text" name="location_name" class="ups-input" placeholder="Contoh: Server Room Lantai 2" required autocomplete="off">
                    </div>
                    <button type="submit" class="btn-submit">
                        💾 Simpan Lokasi
                    </button>
                </form>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <div class="ups-card table-section">
            <div class="ups-card-header">
                <span>📋</span>
                <h3>Daftar Lokasi UPS Terdaftar</h3>
            </div>
            <div class="ups-card-body" style="padding: 0;">
                <?php if(!empty($locations)): ?>
                    <div style="overflow-x:auto;">
                        <table class="ups-table">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Lokasi</th>
                                    <th width="150">Tanggal Dibuat</th>
                                    <th width="80" style="text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($locations as $i => $loc): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td style="font-weight: 500;"><?= esc($loc['location_name']) ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($loc['created_at'])) ?></td>
                                    <td style="text-align:center;">
                                        <a href="<?= site_url('monitoring-support/ups-manage-list/delete/'.$loc['id']) ?>" 
                                           class="btn-delete" 
                                           onclick="return confirm('Yakin ingin menghapus lokasi ini?');">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="padding: 40px; text-align: center; color: #94a3b8;">
                        <div style="font-size: 40px; margin-bottom: 10px;">🗺️</div>
                        <p>Belum ada data lokasi UPS.<br><span style="font-size: 12px;">Gunakan form di sebelah kiri untuk menambah lokasi.</span></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
