<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
  /* Scoped styles for Wemos Dashboard */
  .wemos-container {
    font-family: 'Segoe UI', sans-serif;
    background: #0b0b1e;
    color: #e0e0ff;
    padding: 16px;
    border-radius: 8px;
  }
  .wemos-container h1 {
    text-align: center;
    font-size: 1.6rem;
    padding: 18px 0 22px;
    background: linear-gradient(90deg, #7c6fff, #00d4ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .wemos-container .subtitle {
    text-align: center;
    font-size: .75rem;
    color: #555;
    margin-top: -18px;
    margin-bottom: 22px;
    letter-spacing: 2px;
    text-transform: uppercase;
  }
  .wemos-container .grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 14px;
    max-width: 1100px;
    margin: 0 auto;
  }
  .wemos-container .card {
    background: rgba(255, 255, 255, .04);
    border: 1px solid rgba(255, 255, 255, .08);
    border-radius: 18px;
    padding: 20px;
    transition: transform .2s, box-shadow .2s;
  }
  .wemos-container .card:hover {
    transform: translateY(-2px);
  }
  .wemos-container .card-title {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #7c6fff;
    margin-bottom: 14px;
  }
  .wemos-container .clock-time {
    font-size: 3.5rem;
    font-weight: 700;
    text-align: center;
    color: #fff;
    letter-spacing: 5px;
    text-shadow: 0 0 30px rgba(124, 111, 255, .7);
    font-variant-numeric: tabular-nums;
  }
  .wemos-container .clock-date {
    text-align: center;
    color: #888;
    margin-top: 8px;
    font-size: .9rem;
  }
  .wemos-container .sec-wrap {
    margin-top: 14px;
  }
  .wemos-container .sec-label {
    font-size: .7rem;
    color: #555;
    margin-bottom: 4px;
    text-align: right;
  }
  .wemos-container .progress-bar {
    width: 100%;
    height: 5px;
    background: rgba(255, 255, 255, .08);
    border-radius: 3px;
    overflow: hidden;
  }
  .wemos-container .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #7c6fff, #00d4ff);
    border-radius: 3px;
    transition: width .3s ease;
  }
  .wemos-container .metric {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 9px 0;
    border-bottom: 1px solid rgba(255, 255, 255, .05);
  }
  .wemos-container .metric:last-child {
    border-bottom: none;
  }
  .wemos-container .metric-label {
    color: #777;
    font-size: .83rem;
  }
  .wemos-container .metric-value {
    color: #f0f0ff;
    font-weight: 600;
    font-size: .9rem;
  }
  .wemos-container .signal-bars {
    display: flex;
    align-items: flex-end;
    gap: 3px;
    height: 18px;
    margin-left: 8px;
  }
  .wemos-container .bar {
    width: 5px;
    border-radius: 2px;
    background: rgba(255, 255, 255, .12);
    transition: background .4s;
  }
  .wemos-container .bar.on {
    background: linear-gradient(0deg, #7c6fff, #00d4ff);
  }
  .wemos-container .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #00e676;
    display: inline-block;
    margin-right: 6px;
    animation: pulse 2s infinite;
  }
  @keyframes pulse { 0%, 100% { opacity: 1 } 50% { opacity: .4 } }
  .wemos-container .badge {
    display: inline-block;
    background: rgba(124, 111, 255, .15);
    border: 1px solid rgba(124, 111, 255, .3);
    color: #a598ff;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: .72rem;
  }
  .wemos-container .door-status {
    font-size: 2rem;
    font-weight: 800;
    text-align: center;
    padding: 16px 10px;
    border-radius: 14px;
    margin: 8px 0;
    letter-spacing: 2px;
    transition: all .4s ease;
  }
  .wemos-container .door-open {
    color: #ff5252;
    background: rgba(255, 82, 82, .1);
    border: 1px solid rgba(255, 82, 82, .3);
    box-shadow: 0 0 20px rgba(255, 82, 82, .2);
    animation: glow-red 1.5s infinite;
  }
  .wemos-container .door-closed {
    color: #00e676;
    background: rgba(0, 230, 118, .1);
    border: 1px solid rgba(0, 230, 118, .3);
    box-shadow: 0 0 10px rgba(0, 230, 118, .1);
  }
  @keyframes glow-red { 0%, 100% { box-shadow: 0 0 10px rgba(255, 82, 82, .2) } 50% { box-shadow: 0 0 25px rgba(255, 82, 82, .5) } }
  .wemos-container .btn {
    background: #7c6fff;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: all .3s;
  }
  .wemos-container .btn:hover { background: #5a4bdf; }
  .wemos-container .btn-off { background: #ff5252; }
  .wemos-container .btn-off:hover { background: #df4444; }
  .wemos-container .btn-on { background: #00e676; color: #0b0b1e; }
  .wemos-container .btn-on:hover { background: #00c765; }
  .wemos-container .input-num {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 4px;
    border-radius: 4px;
    width: 50px;
    text-align: center;
    font-family: inherit;
  }
  .wemos-container .footer {
    text-align: center;
    margin-top: 20px;
    color: #888;
    font-size: .75rem;
    padding-bottom: 10px;
  }
  .wemos-container .conn-indicator {
    float: right;
    font-size: .7rem;
    display: flex;
    align-items: center;
    gap: 5px;
    background: rgba(0, 0, 0, .5);
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, .08);
    margin-bottom: -30px;
    position: relative;
    z-index: 10;
  }
  .wemos-container .log-list {
    list-style: none;
    font-size: 0.85rem;
    color: #ccc;
    margin-top: 10px;
  }
  .wemos-container .log-list li {
    padding: 8px 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: center;
  }
  .wemos-container .log-list li::before {
    content: "\2022";
    color: #00d4ff;
    font-weight: bold;
    display: inline-block;
    width: 1em;
    margin-left: -1em;
  }
  .wemos-container .log-list li:last-child {
    border-bottom: none;
  }
  .wemos-container .stat-box {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    padding: 10px;
  }
  .wemos-container .stat-num {
    font-size: 1.8rem;
    font-weight: bold;
  }
  .wemos-container .toggle-switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
    vertical-align: middle;
  }
  .wemos-container .toggle-switch input { opacity: 0; width: 0; height: 0; }
  .wemos-container .toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255, 255, 255, .1);
    border-radius: 24px;
    transition: .3s;
    border: 1px solid rgba(255, 255, 255, .15);
  }
  .wemos-container .toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px; width: 18px;
    left: 2px; bottom: 2px;
    background: #fff;
    border-radius: 50%;
    transition: .3s;
    box-shadow: 0 1px 4px rgba(0, 0, 0, .3);
  }
  .wemos-container input:checked + .toggle-slider {
    background: linear-gradient(90deg, #7c6fff, #00d4ff);
    border-color: transparent;
  }
  .wemos-container input:checked + .toggle-slider:before {
    transform: translateX(22px);
  }
  .wemos-container .sched-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 600;
  }
</style>

<div class="right-frame">
  <h3>Monitoring Wemos (Data Langsung & Log Terpusat)</h3>
  <hr style="margin: 20px 0;">

  <div class="wemos-container">
    <div class="conn-indicator">
      <span class="dot" id="cdot"></span><span id="cstat">Live</span>
    </div>
    
    <h1>&#9889; NodeMCU Dashboard</h1>
    <p class="subtitle">ESP8266 &nbsp;&bull;&nbsp; WIB Real-time</p>
    
    <div class="grid">
      <!-- Clock -->
      <div class="card">
        <div class="card-title">&#128336; Jam Real-time (WIB)</div>
        <div class="clock-time" id="clock">--:--:--</div>
        <div class="clock-date" id="date">Memuat...</div>
        <div class="sec-wrap">
          <div class="sec-label" id="sec-label">0 detik</div>
          <div class="progress-bar"><div class="progress-fill" id="sec-bar" style="width:0%"></div></div>
        </div>
      </div>

      <!-- Door Sensor & Relay -->
      <div class="card" id="door-card">
        <div class="card-title">&#128682; Pintu & Relay</div>
        <div class="door-status door-closed" id="door-status">&#128274; TERTUTUP</div>
        
        <div class="metric" style="margin-top:10px;">
          <span class="metric-label">Status Relay (D1)</span>
          <span class="metric-value"><button id="relay-btn" class="btn btn-off" onclick="toggleRelay()">OFF</button></span>
        </div>
        <div class="metric">
          <span class="metric-label">Auto ON Delay</span>
          <span class="metric-value">
            <input type="number" id="delay-input" class="input-num" value="15"> dtk
            <button class="btn" onclick="saveDelay()" style="padding:4px 8px;font-size:0.75rem;">Set</button>
          </span>
        </div>
        <div class="metric">
          <span class="metric-label">Pintu Terakhir Berubah</span>
          <span class="metric-value" id="door-time">--:--:--</span>
        </div>
      </div>
      
      <!-- Schedule Card -->
      <div class="card">
        <div class="card-title">&#9200; Jadwal Relay Otomatis</div>
        <div class="metric">
          <span class="metric-label">Aktifkan Jadwal</span>
          <span class="metric-value">
            <label class="toggle-switch">
              <input type="checkbox" id="sched-enabled" onchange="schedEditing=true">
              <span class="toggle-slider"></span>
            </label>
          </span>
        </div>
        <div class="metric">
          <span class="metric-label">Jam Mulai</span>
          <span class="metric-value">
            <input type="number" id="sched-sh" class="input-num" min="0" max="23" value="8" oninput="schedEditing=true"> :
            <input type="number" id="sched-sm" class="input-num" min="0" max="59" value="0" oninput="schedEditing=true">
          </span>
        </div>
        <div class="metric">
          <span class="metric-label">Jam Selesai</span>
          <span class="metric-value">
            <input type="number" id="sched-eh" class="input-num" min="0" max="23" value="17" oninput="schedEditing=true"> :
            <input type="number" id="sched-em" class="input-num" min="0" max="59" value="0" oninput="schedEditing=true">
          </span>
        </div>
        <div class="metric">
          <span class="metric-label">Status Sekarang</span>
          <span class="metric-value" id="sched-status"><span style="color:#888">Memuat...</span></span>
        </div>
        <div style="margin-top:14px;text-align:right;">
          <button class="btn" onclick="saveSchedule()" id="btn-save-sched">&#128190; Simpan Jadwal</button>
        </div>
      </div>

      <!-- Log & Stats (Span 2 columns if space allows) -->
      <div class="card" style="grid-column: 1 / -1;">
        <div class="card-title">&#128202; Log Database Terpusat (IT Support)</div>
        <div style="display:flex; justify-content:space-between; margin-bottom:20px; gap:10px;">
          <div class="stat-box">
            <span class="metric-label" style="margin-bottom:5px">Harian</span>
            <span class="stat-num" id="stat-daily" style="color:#00d4ff;">0</span>
          </div>
          <div class="stat-box">
            <span class="metric-label" style="margin-bottom:5px">Mingguan</span>
            <span class="stat-num" id="stat-weekly" style="color:#7c6fff;">0</span>
          </div>
          <div class="stat-box">
            <span class="metric-label" style="margin-bottom:5px">Bulanan</span>
            <span class="stat-num" id="stat-monthly" style="color:#a598ff;">0</span>
          </div>
        </div>
        <div class="card-title" style="margin-top:10px;">&#128220; 15 Catatan Terakhir dari Database</div>
        <ul id="log-list" class="log-list">
          <li style="color:#777; font-style:italic">Memuat data log dari server...</li>
        </ul>
      </div>

      <!-- WiFi -->
      <div class="card">
        <div class="card-title">&#128246; WiFi Info</div>
        <div class="metric">
          <span class="metric-label">Status</span>
          <span class="metric-value"><span class="dot"></span>Connected</span>
        </div>
        <div class="metric">
          <span class="metric-label">SSID</span>
          <span class="metric-value" id="ssid">...</span>
        </div>
        <div class="metric">
          <span class="metric-label">IP Address</span>
          <span class="metric-value" id="ip">...</span>
        </div>
        <div class="metric">
          <span class="metric-label">Signal (RSSI)</span>
          <span class="metric-value" id="rssi-val">...</span>
          <div class="signal-bars">
            <div class="bar" id="b1" style="height:5px"></div>
            <div class="bar" id="b2" style="height:9px"></div>
            <div class="bar" id="b3" style="height:13px"></div>
            <div class="bar" id="b4" style="height:18px"></div>
          </div>
        </div>
      </div>

      <!-- System -->
      <div class="card">
        <div class="card-title">&#128187; System Info</div>
        <div class="metric">
          <span class="metric-label">Chip</span>
          <span class="metric-value"><span class="badge">ESP8266EX</span></span>
        </div>
        <div class="metric">
          <span class="metric-label">CPU</span>
          <span class="metric-value" id="cpu">...</span>
        </div>
        <div class="metric">
          <span class="metric-label">Free Heap</span>
          <span class="metric-value" id="heap">...</span>
        </div>
        <div class="metric">
          <span class="metric-label">Uptime</span>
          <span class="metric-value" id="uptime">...</span>
        </div>
      </div>
    </div>
    <div class="footer" id="lastupdate">Menghubungkan...</div>
  </div>
</div>

<script>
  const wemosBaseUrl = 'http://<?= esc($wemos_ip) ?>';
  const myApiLogsUrl = '<?= site_url('wemos/apiGetLogs') ?>';

  function pz(n){return n<10?'0'+n:n}
  function fmtUp(s){var h=Math.floor(s/3600),m=Math.floor((s%3600)/60),sc=s%60;return pz(h)+':'+pz(m)+':'+pz(sc)}
  function setSig(rssi){
    var n=rssi>-55?4:rssi>-65?3:rssi>-75?2:rssi>-85?1:0;
    for(var i=1;i<=4;i++)document.getElementById('b'+i).className='bar'+(i<=n?' on':'');
  }

  function toggleRelay(){
    var btn = document.getElementById('relay-btn');
    var newState = btn.textContent === 'ON' ? 0 : 1;
    fetch(wemosBaseUrl + '/api/relay?state=' + newState).then(()=>{ fetchData(); });
  }
  function saveDelay(){
    var val = document.getElementById('delay-input').value;
    fetch(wemosBaseUrl + '/api/settings?delay=' + val);
    alert('Delay tersimpan: ' + val + ' detik');
  }
  function saveSchedule(){
    var en = document.getElementById('sched-enabled').checked ? 1 : 0;
    var sh = document.getElementById('sched-sh').value;
    var sm = document.getElementById('sched-sm').value;
    var eh = document.getElementById('sched-eh').value;
    var em = document.getElementById('sched-em').value;
    if(parseInt(sh)<0||parseInt(sh)>23||parseInt(eh)<0||parseInt(eh)>23||parseInt(sm)<0||parseInt(sm)>59||parseInt(em)<0||parseInt(em)>59){
      alert('Input jam/menit tidak valid!'); return;
    }
    var btn=document.getElementById('btn-save-sched');
    btn.textContent='Menyimpan...';
    fetch(wemosBaseUrl + '/api/schedule?enabled='+en+'&startHour='+sh+'&startMin='+sm+'&endHour='+eh+'&endMin='+em)
      .then(()=>{ btn.textContent='\u2705 Tersimpan!'; setTimeout(()=>{btn.innerHTML='&#128190; Simpan Jadwal'; schedEditing=false;},1500); })
      .catch(()=>{ btn.textContent='Gagal!'; setTimeout(()=>{btn.innerHTML='&#128190; Simpan Jadwal';},1500); });
  }

  var ok=true;
  var schedEditing=false;
  
  // Data polling ke Wemos langsung
  function fetchData(){
    fetch(wemosBaseUrl + '/api/data')
      .then(r=>r.json())
      .then(d=>{
        if(!ok){ok=true;document.getElementById('cdot').style.background='#00e676';document.getElementById('cstat').textContent='Live'}
        document.getElementById('clock').textContent=d.time;
        document.getElementById('date').textContent=d.date;
        document.getElementById('sec-bar').style.width=(d.sec/59*100)+'%';
        document.getElementById('sec-label').textContent=d.sec+' detik';
        
        var isOpen=d.door===1;
        var ds=document.getElementById('door-status');
        ds.textContent=isOpen?'\uD83D\uDD13 TERBUKA':'\uD83D\uDD12 TERTUTUP';
        ds.className='door-status '+(isOpen?'door-open':'door-closed');
        document.getElementById('door-time').textContent=d.door_time;
        
        var rBtn = document.getElementById('relay-btn');
        rBtn.textContent = d.relay ? 'ON' : 'OFF';
        rBtn.className = d.relay ? 'btn btn-on' : 'btn btn-off';
        
        if(document.activeElement !== document.getElementById('delay-input')){
          document.getElementById('delay-input').value = d.delay;
        }
        document.getElementById('ssid').textContent=d.ssid;
        document.getElementById('ip').textContent=d.ip;
        document.getElementById('rssi-val').textContent=d.rssi+' dBm';
        setSig(d.rssi);
        
        document.getElementById('cpu').textContent=d.cpu+' MHz';
        document.getElementById('heap').textContent=Math.round(d.heap/1024)+' KB / 80 KB';
        document.getElementById('uptime').textContent=fmtUp(d.uptime);
        document.getElementById('lastupdate').textContent='Diperbarui: '+d.time+' WIB';
        // Update UI Jadwal
        if(!schedEditing){
          document.getElementById('sched-enabled').checked=d.sched_en===1;
          document.getElementById('sched-sh').value=d.sched_sh;
          document.getElementById('sched-sm').value=d.sched_sm;
          document.getElementById('sched-eh').value=d.sched_eh;
          document.getElementById('sched-em').value=d.sched_em;
        }
        var si=document.getElementById('sched-status');
        if(!d.sched_en){si.innerHTML='<span class="sched-badge" style="background:rgba(255,255,255,.07);color:#888">Nonaktif &mdash; Relay 24 Jam</span>';}
        else if(d.in_sched){si.innerHTML='<span class="sched-badge" style="background:rgba(0,230,118,.12);color:#00e676;border:1px solid rgba(0,230,118,.3)">&#9989; Dalam Jadwal</span>';}
        else{si.innerHTML='<span class="sched-badge" style="background:rgba(255,82,82,.1);color:#ff5252;border:1px solid rgba(255,82,82,.3)">&#9208; Di Luar Jadwal</span>';}
      })
      .catch(()=>{
        ok=false;
        document.getElementById('cdot').style.background='#ff5252';
        document.getElementById('cstat').textContent='Offline';
      });
  }
  
  // Data polling ke Database IT Support Server
  function fetchLogs(){
    fetch(myApiLogsUrl)
      .then(r=>r.json())
      .then(d=>{
        document.getElementById('stat-daily').textContent = d.daily;
        document.getElementById('stat-weekly').textContent = d.weekly;
        document.getElementById('stat-monthly').textContent = d.monthly;
        
        var lst = document.getElementById('log-list');
        if(d.logs.length === 0){
          lst.innerHTML = '<li style="color:#777; font-style:italic">Belum ada catatan...</li>';
        } else {
          var html = '';
          d.logs.forEach(function(l){ html += '<li>' + l + '</li>'; });
          lst.innerHTML = html;
        }
      }).catch(()=>{});
  }

  fetchData();
  fetchLogs();
  setInterval(fetchData, 1000);
  setInterval(fetchLogs, 5000); 
</script>

<?= $this->endSection() ?>
