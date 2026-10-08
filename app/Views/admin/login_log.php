<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="right-frame">
    <h3>Data Login Log</h3>
    <hr style="margin: 10px 0;">
    
    <div style="background: white; padding: 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto;">
        <table id="dataTable" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 10px; border: 1px solid #ddd; background-color: #f8f9fa;">No</th>
                    <th style="padding: 10px; border: 1px solid #ddd; background-color: #f8f9fa;">Username</th>
                    <th style="padding: 10px; border: 1px solid #ddd; background-color: #f8f9fa;">Waktu Login</th>
                    <th style="padding: 10px; border: 1px solid #ddd; background-color: #f8f9fa;">IP Address</th>
                    <th style="padding: 10px; border: 1px solid #ddd; background-color: #f8f9fa;">User Agent</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" style="padding: 10px; border: 1px solid #ddd; text-align: center;">Belum ada data log login.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($logs as $log): ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;"><?= $no++ ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($log['username']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;"><?= date('d-m-Y H:i:s', strtotime($log['login_time'])) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;"><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-size: 12px;"><?= htmlspecialchars($log['user_agent'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Load DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[ 2, "desc" ]], // Order by Waktu Login DESC by default
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        }
    });
});
</script>
<?= $this->endSection() ?>
