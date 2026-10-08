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
    
    /* Table styling for checklist cctv */
    .cctv-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 13px;
    }
    .cctv-table th, .cctv-table td {
        border: 1px solid #ddd;
        padding: 8px 10px;
        text-align: left;
    }
    .cctv-table th {
        background-color: #f2f2f2;
        font-weight: bold;
        color: #333;
    }
    .cctv-table tbody tr:hover {
        background-color: #f9f9f9;
    }
</style>

<div class="right-frame">
    <h3><?= esc($title) ?></h3>
    
    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form action="<?= site_url('checklist-cctv/store') ?>" method="post">
            <div class="form-grid">
                <!-- Kolom Kiri: Info Checklist -->
                <div>
                    <h4 style="margin: 0 0 15px 0; color:#0077b6; border-bottom: 2px solid #0077b6; padding-bottom: 5px;">Info Checklist</h4>
                    
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" id="field_tanggal" value="" title="Pilih tanggal checklist (Format: YYYY-MM-DD)" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Jam</label>
                        <input type="time" name="jam" id="field_jam" value="" title="Pilih jam checklist (Format: HH:MM)" required>
                    </div>
                    
                    <div class="form-group">
                        <label>PIC Check</label>
                        <input type="text" name="pic_check" value="<?= esc($pic_check) ?>" readonly style="background:#e9ecef; cursor:not-allowed;">
                    </div>
                    
                    <div class="form-group">
                        <label>NVR</label>
                        <select name="nvr" id="nvr" required>
                            <option value="">-- Pilih NVR --</option>
                            <?php foreach($nvrs as $n): ?>
                                <option value="<?= esc($n['nvr']) ?>"><?= esc($n['nvr']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Notes (Opsional)</label>
                        <textarea name="keterangan" rows="3" placeholder="Keterangan tambahan jika diperlukan..."></textarea>
                    </div>
                </div>
                
                <!-- Kolom Kanan: Daftar CCTV -->
                <div style="min-width:0; overflow:hidden;">
                    <h4 style="margin: 0 0 15px 0; color:#0077b6; border-bottom: 2px solid #0077b6; padding-bottom: 5px;">Daftar Status CCTV</h4>
                    
                    <div id="no_nvr_selected" style="padding: 30px; text-align: center; background: #fafafa; border: 1px dashed #ccc; border-radius: 4px; color: #666;">
                        Silakan pilih NVR terlebih dahulu untuk menampilkan daftar CCTV.
                    </div>

                    <div id="cctv_table_wrapper" style="display:none; max-height: 500px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px;">
                        <table class="cctv-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th style="width: 80px;">Channel</th>
                                    <th>Nama CCTV</th>
                                    <th style="width: 220px;">Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="cctv_list_tbody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div id="spelling_info" class="spelling-info" style="margin-top: 10px;"></div>
                </div>
            </div>
            
            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            
            <div>
                <button type="submit" class="btn-submit">Simpan Checklist</button>
                <a href="<?= site_url('checklist-cctv') ?>" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#nvr').change(function() {
        var nvr = $(this).val();
        var $tbody = $('#cctv_list_tbody');
        var $wrapper = $('#cctv_table_wrapper');
        var $noNvr = $('#no_nvr_selected');
        var $spellingInfo = $('#spelling_info');

        $tbody.empty();
        $wrapper.hide();
        $noNvr.show();
        $spellingInfo.hide();

        if (nvr) {
            $noNvr.text('Sedang memuat daftar CCTV...');
            $.ajax({
                url: '<?= site_url("checklist-cctv/getCctvsByNvr") ?>',
                type: 'POST',
                data: {nvr: nvr},
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.cctvs.length > 0) {
                        $noNvr.hide();
                        $wrapper.show();
                        
                        $.each(response.cctvs, function(index, cctv) {
                            var rowHtml = '<tr>' +
                                '<td style="text-align: center; vertical-align: middle;">' + (index + 1) + '</td>' +
                                '<td style="vertical-align: middle;">' +
                                    '<strong>' + cctv.channel + '</strong>' +
                                    '<input type="hidden" name="checklists[' + index + '][channel]" value="' + cctv.channel + '">' +
                                '</td>' +
                                '<td style="vertical-align: middle;">' +
                                    '<span>' + cctv.nama_cctv + '</span>' +
                                    '<input type="hidden" name="checklists[' + index + '][nama_cctv]" value="' + cctv.nama_cctv + '">' +
                                '</td>' +
                                '<td>' +
                                    '<select name="checklists[' + index + '][status]" required style="padding: 4px; font-size: 13px;">' +
                                        '<option value="">-- Pilih Status --</option>' +
                                        '<option value="Aktif & Record" selected>Aktif & Record</option>' +
                                        '<option value="Offline & Un\'Record">Offline & Un\'Record</option>' +
                                        '<option value="CCTV IT Pick">CCTV IT Pick</option>' +
                                        '<option value="Maintenance IT">Maintenance IT</option>' +
                                        '<option value="Time Un\'Realtime">Time Un\'Realtime</option>' +
                                        '<option value="Channel Blank.">Channel Blank.</option>' +
                                    '</select>' +
                                '</td>' +
                                '<td>' +
                                    '<input type="text" name="checklists[' + index + '][keterangan]" placeholder="Catatan..." style="padding: 4px; font-size: 13px;">' +
                                '</td>' +
                                '</tr>';
                            $tbody.append(rowHtml);
                        });
                    } else {
                        $noNvr.text('Tidak ada CCTV terdaftar di bawah NVR ini.');
                    }
                },
                error: function() {
                    $noNvr.hide();
                    $spellingInfo.text('Terjadi kesalahan koneksi saat memuat daftar CCTV.').show();
                }
            });
        }
    });
    // === AUTO-FILL DATE & TIME FROM DEVICE CLOCK ===
    function padZero(n) { return n < 10 ? '0' + n : n; }
    var now = new Date();
    var yyyy = now.getFullYear();
    var mm   = padZero(now.getMonth() + 1);
    var dd   = padZero(now.getDate());
    var hh   = padZero(now.getHours());
    var mi   = padZero(now.getMinutes());
    document.getElementById('field_tanggal').value = yyyy + '-' + mm + '-' + dd;
    document.getElementById('field_jam').value     = hh + ':' + mi;
});
</script>

<?= $this->endSection() ?>
