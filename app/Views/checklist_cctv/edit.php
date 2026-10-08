<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .btn-submit {
        background-color: <?= $top_color ?>;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }
    .btn-cancel {
        background-color: #6c757d;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        margin-left: 10px;
    }
    .spelling-info { font-size: 12px; color: #dc3545; display: none; margin-top: 5px; font-weight: bold; }
    
    .form-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 25px;
        align-items: start;
    }
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="right-frame">
    <h3><?= esc($title) ?></h3>
    
    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form action="<?= site_url('checklist-cctv/update/'.$checklist['id']) ?>" method="post">
            <div class="form-grid">
                <!-- Kolom Kiri: Info Checklist -->
                <div>
                    <h4 style="margin: 0 0 15px 0; color:#0077b6; border-bottom: 2px solid #0077b6; padding-bottom: 5px;">Info Checklist</h4>
                    
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" value="<?= esc($checklist['tanggal']) ?>" title="Pilih tanggal checklist (Format: YYYY-MM-DD)" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Jam</label>
                        <input type="time" name="jam" value="<?= esc($checklist['jam']) ?>" title="Pilih jam checklist (Format: HH:MM)" required>
                    </div>
                    
                    <div class="form-group">
                        <label>PIC Check</label>
                        <input type="text" name="pic_check" value="<?= esc($checklist['pic_check']) ?>" readonly style="background:#e9ecef; cursor:not-allowed;">
                    </div>
                    
                    <div class="form-group">
                        <label>Notes (Opsional)</label>
                        <textarea name="keterangan" rows="4" placeholder="Keterangan tambahan jika diperlukan..."><?= esc($checklist['keterangan']) ?></textarea>
                    </div>
                </div>
                
                <!-- Kolom Kanan: Detail CCTV -->
                <div>
                    <h4 style="margin: 0 0 15px 0; color:#0077b6; border-bottom: 2px solid #0077b6; padding-bottom: 5px;">Detail CCTV</h4>
                    
                    <div class="form-group">
                        <label>NVR</label>
                        <select name="nvr" id="nvr" required>
                            <option value="">-- Pilih NVR --</option>
                            <?php foreach($nvrs as $n): ?>
                                <option value="<?= esc($n['nvr']) ?>" <?= ($checklist['nvr'] == $n['nvr']) ? 'selected' : '' ?>><?= esc($n['nvr']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Channel</label>
                        <select name="channel" id="channel" required>
                            <option value="">-- Pilih Channel --</option>
                            <?php foreach($channels as $c): ?>
                                <option value="<?= esc($c['channel']) ?>" <?= ($checklist['channel'] == $c['channel']) ? 'selected' : '' ?>><?= esc($c['channel']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama CCTV</label>
                        <input type="text" name="nama_cctv" id="nama_cctv" value="<?= esc($checklist['nama_cctv']) ?>" required readonly style="background:#e9ecef;">
                        <div id="spelling_info" class="spelling-info">Data CCTV tidak ditemukan! Silakan periksa kembali NVR dan Channel, atau isi manual.</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="">-- Pilih Status --</option>
                            <?php 
                            $statuses = ['Aktif & Record', "Offline & Un'Record", 'CCTV IT Pick', 'Maintenance IT', "Time Un'Realtime", 'Channel Blank.'];
                            foreach($statuses as $st): 
                            ?>
                            <option value="<?= esc($st) ?>" <?= ($checklist['status'] == $st) ? 'selected' : '' ?>><?= esc($st) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            
            <div>
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="<?= site_url('checklist-cctv') ?>" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Populate CCTV list initially with database rows returned by edit method
    var cctvList = <?= json_encode($cctvs) ?>;

    $('#nvr').change(function() {
        var nvr = $(this).val();
        var $channel = $('#channel');
        var $namaCctv = $('#nama_cctv');
        var $spellingInfo = $('#spelling_info');

        $channel.html('<option value="">-- Pilih Channel --</option>');
        $channel.prop('disabled', true).css({'background': '#e9ecef', 'cursor': 'not-allowed'});
        $namaCctv.val('').prop('readonly', true).css('background', '#e9ecef');
        $spellingInfo.hide();
        cctvList = [];

        if (nvr) {
            $.ajax({
                url: '<?= site_url("checklist-cctv/getCctvsByNvr") ?>',
                type: 'POST',
                data: {nvr: nvr},
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.cctvs.length > 0) {
                        cctvList = response.cctvs;
                        $channel.prop('disabled', false).css({'background': '', 'cursor': ''});
                        $.each(cctvList, function(index, cctv) {
                            $channel.append('<option value="' + cctv.channel + '">' + cctv.channel + '</option>');
                        });
                    } else {
                        $spellingInfo.text('Tidak ada channel CCTV untuk NVR ini!').show();
                    }
                },
                error: function() {
                    $spellingInfo.text('Terjadi kesalahan koneksi saat memuat channel.').show();
                }
            });
        }
    });

    $('#channel').change(function() {
        var channelVal = $(this).val();
        var $namaCctv = $('#nama_cctv');
        var $spellingInfo = $('#spelling_info');
        
        $namaCctv.val('');
        $spellingInfo.hide();

        if (channelVal && cctvList.length > 0) {
            var found = cctvList.find(function(cctv) {
                return String(cctv.channel) === String(channelVal);
            });
            if (found) {
                $namaCctv.val(found.nama_cctv);
            } else {
                $spellingInfo.text('Nama CCTV tidak ditemukan untuk channel ini!').show();
            }
        }
    });
});
</script>

<?= $this->endSection() ?>
