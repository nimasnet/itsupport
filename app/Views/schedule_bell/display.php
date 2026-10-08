<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: #0b0f19;
            color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #3b82f6;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        .header-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .header-title h1 {
            font-size: 20px;
            font-weight: 800;
            color: #38bdf8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 12px;
            color: #94a3b8;
        }

        .header-clock {
            text-align: right;
        }
        .clock-time {
            font-size: 26px;
            font-weight: 800;
            color: #f59e0b;
            font-family: 'Courier New', Courier, monospace;
        }
        .clock-date {
            font-size: 12px;
            color: #94a3b8;
        }

        .banner-upcoming {
            background: linear-gradient(135deg, #1e1b4b, #311b92);
            border: 2px solid #6366f1;
            margin: 15px 30px;
            padding: 20px 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.25);
        }
        .banner-left { display: flex; align-items: center; gap: 16px; }
        .banner-icon {
            font-size: 36px;
            background: rgba(255,255,255,0.1);
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .upcoming-label { font-size: 12px; color: #a5b4fc; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        .upcoming-name { font-size: 24px; font-weight: 800; color: #fff; margin-top: 4px; }
        .upcoming-speaker { font-size: 13px; color: #cbd5e1; margin-top: 2px; }

        .banner-right { text-align: right; }
        .timer-label { font-size: 12px; color: #a5b4fc; text-transform: uppercase; font-weight: 700; }
        .timer-value { font-size: 34px; font-weight: 900; color: #10b981; font-family: monospace; text-shadow: 0 0 10px rgba(16,185,129,0.3); }

        .content-body {
            flex: 1;
            padding: 0 30px 20px 30px;
            overflow-y: auto;
        }

        table.tv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            background: #1e293b;
            border-radius: 10px;
            overflow: hidden;
        }
        table.tv-table th {
            background: #0f172a;
            color: #94a3b8;
            padding: 12px;
            font-size: 11px;
            text-transform: uppercase;
            text-align: center;
            border-bottom: 2px solid #334155;
        }
        table.tv-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #334155;
            text-align: center;
        }
        table.tv-table tr.ring-row {
            background: rgba(16, 185, 129, 0.15) !important;
        }
        .badge-spk {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .bg-in { background: #3b82f6; color: #fff; }
        .bg-out { background: #8b5cf6; color: #fff; }
        .bg-all { background: #10b981; color: #fff; }

        .back-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 12px;
            backdrop-filter: blur(5px);
            transition: all 0.2s;
        }
        .back-btn:hover { background: rgba(255,255,255,0.25); }
    </style>
</head>
<body>

    <header>
        <div class="header-title">
            <span style="font-size: 28px;">🔔</span>
            <div>
                <h1>Schedule Ring of Information Audio</h1>
                <div class="header-subtitle">PRODUCTION BUILDING AREA — KIOSK TV DISPLAY MODE</div>
            </div>
        </div>
        <div class="header-clock">
            <div class="clock-time" id="tvClock">00:00:00</div>
            <div class="clock-date" id="tvDate">---, -- ---- ----</div>
        </div>
    </header>

    <div class="banner-upcoming">
        <div class="banner-left">
            <div class="banner-icon">🔊</div>
            <div>
                <div class="upcoming-label">Bel Dering Berikutnya Hari Ini</div>
                <div class="upcoming-name" id="upName">Menganalisis Jadwal...</div>
                <div class="upcoming-speaker" id="upSpk">Speaker: ---</div>
            </div>
        </div>
        <div class="banner-right">
            <div class="timer-label">Hitung Mundur Dering</div>
            <div class="timer-value" id="upTimer">--:--:--</div>
        </div>
    </div>

    <div class="content-body">
        <table class="tv-table">
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="35%" style="text-align: left;">CONTEN / NAMA BEL</th>
                    <th width="15%">BUILDING</th>
                    <th width="15%">SPEAKER ZONE</th>
                    <th width="30%">WAKTU DERING HARI INI</th>
                </tr>
            </thead>
            <tbody id="tvTbody">
                <tr><td colspan="5" style="padding: 30px; color: #94a3b8;">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>

    <a href="<?= site_url('schedule-bell') ?>" class="back-btn">⬅ Kembali ke Admin Hub</a>

    <script>
        const schedules = <?= json_encode($schedules) ?>;
        const timeMap = {
            <?php foreach ($timeColumns as $idx => $tc): ?>
                '<?= $tc['key'] ?>': '<?= $tc['label'] ?>'<?= ($idx < count($timeColumns) - 1) ? ',' : '' ?>
            <?php endforeach; ?>
        };

        function renderTvTable() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            document.getElementById('tvClock').innerText = now.toTimeString().split(' ')[0];
            document.getElementById('tvDate').innerText = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

            const jsDay = now.getDay();
            const dayField = jsDay === 0 ? '' : 'day_' + jsDay;
            const currentSecs = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();

            let tbodyHtml = '';
            let upcoming = null;
            let minDiff = 86400;

            schedules.forEach(row => {
                const isTodayActive = dayField && row[dayField] == 1;
                
                const activeTimes = [];
                if (isTodayActive) {
                    Object.keys(timeMap).forEach(key => {
                        if (row[key] == 1) {
                            activeTimes.push(timeMap[key]);

                            const [h, m] = timeMap[key].split(':').map(Number);
                            const bellSecs = h * 3600 + m * 60;
                            const diff = bellSecs - currentSecs;

                            if (diff > 0 && diff < minDiff) {
                                minDiff = diff;
                                upcoming = {
                                    conten: row.conten,
                                    timeStr: timeMap[key],
                                    spk: (row.spk_in == 1 && row.spk_out == 1) ? 'Indoor & Outdoor' : (row.spk_in == 1 ? 'Indoor (IN)' : (row.spk_out == 1 ? 'Outdoor (OUT)' : 'None'))
                                };
                            }
                        }
                    });
                }

                let spkBadge = '<span class="badge-spk bg-in">IN</span>';
                if (row.spk_in == 1 && row.spk_out == 1) spkBadge = '<span class="badge-spk bg-all">IN & OUT</span>';
                else if (row.spk_out == 1) spkBadge = '<span class="badge-spk bg-out">OUT</span>';

                const timesText = activeTimes.length > 0 ? activeTimes.map(t => `<strong style="color:#10b981; margin:0 4px;">${t}</strong>`).join(', ') : '<span style="color:#64748b;">—</span>';

                tbodyHtml += `
                    <tr class="${activeTimes.length > 0 ? 'ring-row' : ''}">
                        <td>${row.no}</td>
                        <td style="text-align:left; font-weight:bold; color:#f8fafc;">${row.conten}</td>
                        <td style="color:#cbd5e1;">${row.building || '-'}</td>
                        <td>${spkBadge}</td>
                        <td>${timesText}</td>
                    </tr>
                `;
            });

            document.getElementById('tvTbody').innerHTML = tbodyHtml;

            const upNameEl = document.getElementById('upName');
            const upSpkEl = document.getElementById('upSpk');
            const upTimerEl = document.getElementById('upTimer');

            if (upcoming && minDiff < 86400) {
                upNameEl.innerText = `${upcoming.conten} (${upcoming.timeStr})`;
                upSpkEl.innerText = `Speaker: ${upcoming.spk}`;

                const hours = Math.floor(minDiff / 3600);
                const mins = Math.floor((minDiff % 3600) / 60);
                const secs = minDiff % 60;
                const pad = n => n.toString().padStart(2, '0');

                upTimerEl.innerText = `${pad(hours)}:${pad(mins)}:${pad(secs)}`;
            } else {
                upNameEl.innerText = 'Tidak ada bel berikutnya hari ini';
                upSpkEl.innerText = 'Speaker: ---';
                upTimerEl.innerText = '--:--:--';
            }
        }

        setInterval(renderTvTable, 1000);
        renderTvTable();
    </script>
</body>
</html>
