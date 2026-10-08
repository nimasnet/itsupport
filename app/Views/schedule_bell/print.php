<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; background: #fff; margin: 0; padding: 10px; }
        .print-header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .print-header h2 { font-size: 14px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .print-header p { font-size: 11px; margin: 4px 0 0 0; color: #444; }

        table.print-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; }
        table.print-table th, table.print-table td { border: 1px solid #000; padding: 3px 2px; text-align: center; vertical-align: middle; }
        table.print-table th { background: #e0e0e0; font-weight: bold; }
        table.print-table td.ring { background: #7ac740 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        table.print-table tr.row-yellow td { background: #fffde7 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        .footer-sign { margin-top: 30px; display: flex; justify-content: space-between; padding: 0 40px; }
        .sign-box { text-align: center; width: 150px; }
        .sign-space { height: 50px; }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; background: #eef2ff; padding: 10px; border-radius: 6px; border: 1px solid #c7d2fe; display: flex; justify-content: space-between; align-align: center;">
        <span style="font-size: 12px; font-weight: bold; color: #1e40af;">📄 Pratinjau Cetak Dokumentasi Schedule Bell (Format A4 Landscape)</span>
        <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 6px 16px; border-radius: 4px; font-weight: bold; cursor: pointer;">🖨️ Cetak Dokumen Sekarang</button>
    </div>

    <div class="print-header">
        <h2>SCHEDULE RING OF INFORMATION AUDIO — PRODUCTION BUILDING AREA</h2>
        <p>Dokumentasi Resmi IT Support — Tanggal Cetak: <?= date('d F Y H:i') ?></p>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th rowspan="2" width="30">NO</th>
                <th rowspan="2" style="text-align: left; padding-left: 5px;">CONTEN</th>
                <th colspan="6">DAYS RING</th>
                <th rowspan="2" width="60">BUILDING</th>
                <th colspan="2">SPEAKER</th>
                <th colspan="<?= count($timeColumns) ?>">TIME RING</th>
            </tr>
            <tr>
                <th width="14">S</th><th width="14">S</th><th width="14">R</th><th width="14">K</th><th width="14">J</th><th width="14">S</th>
                <th width="18">IN</th><th width="18">OUT</th>
                <?php foreach ($timeColumns as $tc): ?>
                    <th width="18"><?= $tc['label'] ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedules as $row): ?>
            <tr<?= $row['highlight'] ? ' class="row-yellow"' : '' ?>>
                <td><?= $row['no'] ?></td>
                <td style="text-align: left; padding-left: 5px; font-weight: bold;"><?= esc($row['conten']) ?></td>
                <td class="<?= $row['day_1'] ? 'ring' : '' ?>"></td>
                <td class="<?= $row['day_2'] ? 'ring' : '' ?>"></td>
                <td class="<?= $row['day_3'] ? 'ring' : '' ?>"></td>
                <td class="<?= $row['day_4'] ? 'ring' : '' ?>"></td>
                <td class="<?= $row['day_5'] ? 'ring' : '' ?>"></td>
                <td class="<?= $row['day_6'] ? 'ring' : '' ?>"></td>
                <td><?= esc($row['building']) ?></td>
                <td class="<?= $row['spk_in'] ? 'ring' : '' ?>"></td>
                <td class="<?= $row['spk_out'] ? 'ring' : '' ?>"></td>
                <?php foreach ($timeColumns as $tc): ?>
                    <td class="<?= !empty($row[$tc['key']]) ? 'ring' : '' ?>"></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Dibuat Oleh,</p>
            <div class="sign-space"></div>
            <p><strong>IT Support Staff</strong></p>
        </div>
        <div class="sign-box">
            <p>Disetujui Oleh,</p>
            <div class="sign-space"></div>
            <p><strong>IT Supervisor / Manager</strong></p>
        </div>
    </div>

</body>
</html>
