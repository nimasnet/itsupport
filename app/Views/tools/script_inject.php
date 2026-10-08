<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
  /* ======= Script Inject Manager - Scoped Styles ======= */
  .si-page { font-family: 'Segoe UI', sans-serif; }

  /* Header bar */
  .si-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 24px;
  }
  .si-header h3 {
    font-size: 1.4rem;
    color: #1e1e2e;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .si-badge {
    background: linear-gradient(135deg, #e2b96f, #f39c12);
    color: #1a1a1a;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
  }

  /* Upload Zone */
  .upload-zone {
    background: linear-gradient(135deg, #1e1e2e 0%, #252542 100%);
    border: 2px dashed rgba(226, 185, 111, 0.4);
    border-radius: 16px;
    padding: 36px 24px;
    text-align: center;
    transition: border-color .25s, background .25s;
    cursor: pointer;
    position: relative;
    margin-bottom: 24px;
  }
  .upload-zone.drag-over {
    border-color: #e2b96f;
    background: linear-gradient(135deg, #252542, #2e2e55);
  }
  .upload-zone .uz-icon { font-size: 2.8rem; margin-bottom: 10px; }
  .upload-zone .uz-title {
    color: #e2b96f;
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 6px;
  }
  .upload-zone .uz-sub { color: #888; font-size: 0.82rem; }
  .upload-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }
  .btn-upload {
    background: linear-gradient(135deg, #e2b96f, #f39c12);
    color: #1a1a1a;
    border: none;
    padding: 10px 28px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
    margin-top: 14px;
    transition: opacity .2s, transform .15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .btn-upload:hover { opacity: .88; transform: translateY(-1px); }

  /* Stats strip */
  .si-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
  }
  .si-stat-card {
    background: linear-gradient(135deg, #1e1e2e, #252542);
    border: 1px solid rgba(226, 185, 111, 0.15);
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
  }
  .si-stat-card .stat-label { color: #888; font-size: .75rem; text-transform: uppercase; letter-spacing: 1px; }
  .si-stat-card .stat-value { color: #e2b96f; font-size: 1.8rem; font-weight: 700; }

  /* Toolbar */
  .si-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 14px;
  }
  .si-search {
    background: #f4f4f4;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 8px 14px 8px 36px;
    font-size: .9rem;
    outline: none;
    width: 240px;
    transition: border .2s, box-shadow .2s;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.242 1.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: 10px center;
  }
  .si-search:focus { border-color: #e2b96f; box-shadow: 0 0 0 3px rgba(226,185,111,.12); }

  .btn-danger-sm {
    background: #dc3545;
    color: white;
    border: none;
    padding: 7px 16px;
    border-radius: 6px;
    font-size: .83rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: opacity .2s;
  }
  .btn-danger-sm:hover { opacity: .85; }

  /* File table */
  .si-table-wrap {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
  }
  .si-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .88rem;
  }
  .si-table thead tr {
    background: linear-gradient(90deg, #1e1e2e, #2a2a45);
    color: #e2b96f;
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  .si-table th { padding: 13px 16px; text-align: left; }
  .si-table td { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; color: #333; }
  .si-table tbody tr:hover { background: #fafaf5; }
  .si-table tbody tr:last-child td { border-bottom: none; }

  /* File type badge */
  .ext-badge {
    display: inline-block;
    padding: 2px 9px;
    border-radius: 12px;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
  }
  .ext-bat, .ext-cmd  { background: #fff3cd; color: #856404; }
  .ext-ps1            { background: #cfe2ff; color: #084298; }
  .ext-py             { background: #d1e7dd; color: #0a3622; }
  .ext-sh             { background: #f8d7da; color: #842029; }
  .ext-sql            { background: #e2d9f3; color: #432874; }
  .ext-json, .ext-yaml, .ext-yml { background: #fcd4d4; color: #7b1818; }
  .ext-txt, .ext-ini, .ext-conf, .ext-cfg { background: #e9ecef; color: #495057; }
  .ext-php            { background: #d5c5f3; color: #3e1f91; }
  .ext-other          { background: #e2e8f0; color: #475569; }

  /* Action buttons */
  .btn-dl {
    background: #198754;
    color: white;
    text-decoration: none;
    padding: 5px 12px;
    border-radius: 5px;
    font-size: .8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: opacity .2s;
  }
  .btn-dl:hover { opacity: .82; color: white; }
  .btn-del {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 5px;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none;
    transition: opacity .2s;
  }
  .btn-del:hover { opacity: .82; color: white; }

  /* Empty state */
  .si-empty {
    text-align: center;
    padding: 60px 20px;
    color: #aaa;
  }
  .si-empty .em-icon { font-size: 3rem; margin-bottom: 12px; }
  .si-empty p { font-size: .9rem; }

  /* Alert */
  .si-alert {
    padding: 12px 18px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: .88rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .si-alert.success { background: #d1e7dd; color: #0a3622; border-left: 4px solid #198754; }
  .si-alert.error   { background: #f8d7da; color: #842029; border-left: 4px solid #dc3545; }

  .cb-all { cursor: pointer; transform: scale(1.2); }

  /* Progress bar for upload */
  #upload-progress-wrap { display: none; margin-top: 12px; }
  #upload-progress-wrap .prog-label { font-size: .8rem; color: #e2b96f; margin-bottom: 4px; }
  #upload-progress-bar-outer { background: rgba(255,255,255,.1); border-radius: 8px; height: 6px; }
  #upload-progress-bar { height: 6px; background: linear-gradient(90deg, #e2b96f, #f39c12); border-radius: 8px; width: 0%; transition: width .3s; }
</style>

<div class="right-frame si-page">

  <!-- Header -->
  <div class="si-header">
    <h3>
      💉 Script Inject Manager
      <span class="si-badge">File Manager</span>
    </h3>
    <a href="<?= site_url('tools/script-inject') ?>" style="color:#999; font-size:.82rem; text-decoration:none;">🔄 Refresh</a>
  </div>

  <!-- Flash Messages -->
  <?php if (!empty($flash_msg)): ?>
    <div class="si-alert success">✅ <?= $flash_msg ?></div>
  <?php endif; ?>
  <?php if (!empty($flash_err)): ?>
    <div class="si-alert error">❌ <?= $flash_err ?></div>
  <?php endif; ?>

  <!-- Upload Zone -->
  <div class="upload-zone" id="upload-zone">
    <div class="uz-icon">📂</div>
    <div class="uz-title">Drag & Drop file ke sini, atau klik untuk memilih</div>
    <div class="uz-sub">
      Diizinkan: .bat, .ps1, .sh, .py, .js, .vbs, .cmd, .reg, .ini, .conf, .cfg, .txt, .sql, .xml, .json, .yaml, .php, .html &nbsp;|&nbsp; Maks: 20 MB
    </div>
    <form id="upload-form" action="<?= site_url('tools/script-inject/upload') ?>" method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="file" id="file-input" name="script_file" accept=".bat,.ps1,.sh,.py,.js,.vbs,.cmd,.reg,.inf,.ini,.conf,.cfg,.txt,.sql,.xml,.json,.yaml,.yml,.php,.html,.htm,.css">
      <button type="submit" class="btn-upload" id="btn-upload" style="pointer-events:none; position:relative; z-index:5;">
        <span>⬆</span> Upload File
      </button>
    </form>
    <div id="upload-progress-wrap">
      <div class="prog-label" id="prog-label">Mengupload...</div>
      <div id="upload-progress-bar-outer">
        <div id="upload-progress-bar"></div>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <?php
    $totalFiles = count($files);
    $totalSize  = array_sum(array_column($files, 'size_raw'));
    function fmtBytes($bytes) {
      if ($bytes >= 1048576) return round($bytes/1048576, 2) . ' MB';
      if ($bytes >= 1024)    return round($bytes/1024, 2)    . ' KB';
      return $bytes . ' B';
    }
    $extGroups = [];
    foreach ($files as $f) { $extGroups[$f['ext']] = ($extGroups[$f['ext']] ?? 0) + 1; }
    arsort($extGroups);
    $topExt = key($extGroups) ?? '-';
  ?>
  <div class="si-stats">
    <div class="si-stat-card">
      <span class="stat-label">Total File</span>
      <span class="stat-value"><?= $totalFiles ?></span>
    </div>
    <div class="si-stat-card">
      <span class="stat-label">Total Ukuran</span>
      <span class="stat-value" style="font-size:1.4rem;"><?= fmtBytes($totalSize) ?></span>
    </div>
    <div class="si-stat-card">
      <span class="stat-label">Tipe Terbanyak</span>
      <span class="stat-value" style="font-size:1.4rem;">.<?= strtoupper($topExt) ?></span>
    </div>
  </div>

  <!-- Table -->
  <form id="bulk-form" action="<?= site_url('tools/script-inject/bulk-delete') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="si-toolbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <input type="text" class="si-search" id="si-search" placeholder="Cari nama file..." oninput="filterTable(this.value)">
        <span id="row-counter" style="color:#999;font-size:.82rem;"><?= $totalFiles ?> file</span>
      </div>
      <button type="button" class="btn-danger-sm" onclick="bulkDeleteConfirm()">
        🗑️ Hapus Terpilih (<span id="sel-count">0</span>)
      </button>
    </div>

    <div class="si-table-wrap">
      <?php if (empty($files)): ?>
        <div class="si-empty">
          <div class="em-icon">📭</div>
          <p>Belum ada file yang diupload.</p>
          <p style="margin-top:6px;color:#ccc;font-size:.82rem;">Upload file menggunakan kotak di atas.</p>
        </div>
      <?php else: ?>
        <table class="si-table" id="si-table">
          <thead>
            <tr>
              <th><input type="checkbox" class="cb-all" id="cb-all" title="Pilih Semua"></th>
              <th style="width:35%;">Nama File</th>
              <th>Tipe</th>
              <th>Ukuran</th>
              <th>Link Download</th>
              <th>Tanggal Upload</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="si-tbody">
            <?php foreach ($files as $f): ?>
              <?php
                $extClass = in_array($f['ext'], ['bat','cmd','ps1','py','sh','sql','php','json','yaml','yml','txt','ini','conf','cfg'])
                  ? 'ext-' . $f['ext']
                  : 'ext-other';
                $icon = match($f['ext']) {
                  'bat', 'cmd' => '🖥️',
                  'ps1'        => '⚙️',
                  'py'         => '🐍',
                  'sh'         => '🐚',
                  'sql'        => '🗄️',
                  'json', 'yaml', 'yml' => '📋',
                  'php'        => '🐘',
                  'txt', 'ini', 'conf', 'cfg' => '📄',
                  default      => '📁',
                };
              ?>
              <tr class="si-row">
                <td><input type="checkbox" name="selected_files[]" value="<?= esc($f['name']) ?>" class="cb-file" onchange="updateCount()"></td>
                <td>
                  <span style="font-weight:600; color:#1e1e2e;"><?= $icon ?> <?= esc($f['name']) ?></span>
                </td>
                <td><span class="ext-badge <?= $extClass ?>"><?= strtoupper($f['ext']) ?></span></td>
                <td style="color:#666;"><?= $f['size'] ?></td>
                <td>
                  <?php $dlUrl = site_url('tools/script-inject/download/' . urlencode($f['name'])); ?>
                  <div style="display:flex; align-items:center; gap:6px;">
                    <input type="text" id="link-<?= md5($f['name']) ?>" value="<?= $dlUrl ?>" readonly
                      style="font-size:.75rem; background:#f4f4f4; border:1px solid #ddd; border-radius:5px; padding:4px 8px; width:200px; color:#555; cursor:pointer;"
                      onclick="this.select()" title="Klik untuk seleksi">
                    <button type="button"
                      onclick="copyLink('<?= md5($f['name']) ?>', this)"
                      style="background:#0d6efd; color:white; border:none; padding:4px 9px; border-radius:5px; font-size:.75rem; cursor:pointer; white-space:nowrap; transition:opacity .2s;"
                      title="Salin link">
                      📋 Salin
                    </button>
                  </div>
                </td>
                <td style="color:#888; font-size:.82rem;"><?= $f['mtime_fmt'] ?></td>
                <td style="display:flex; gap:6px; align-items:center;">
                  <a href="<?= site_url('tools/script-inject/download/' . urlencode($f['name'])) ?>" class="btn-dl" title="Download">
                    ⬇ Download
                  </a>
                  <a href="#" onclick="confirmDelete('<?= esc($f['name'], 'js') ?>')" class="btn-del" title="Hapus">
                    🗑 Hapus
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </form>

</div>

<!-- Hidden delete form -->
<form id="delete-form" method="GET" style="display:none;">
  <input type="hidden" id="delete-filename" name="filename">
</form>

<script>
// ---- Upload with filename display ----
const fileInput = document.getElementById('file-input');
const btnUpload = document.getElementById('btn-upload');
const uploadZone = document.getElementById('upload-zone');

fileInput.addEventListener('change', function() {
  if (this.files.length > 0) {
    const fname = this.files[0].name;
    btnUpload.style.pointerEvents = 'auto';
    btnUpload.innerHTML = `<span>⬆</span> Upload: ${fname}`;
  }
});

// Drag & Drop
uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('drag-over'); });
uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag-over'));
uploadZone.addEventListener('drop', e => {
  e.preventDefault();
  uploadZone.classList.remove('drag-over');
  const dt = e.dataTransfer;
  fileInput.files = dt.files;
  fileInput.dispatchEvent(new Event('change'));
});

// Submit form → show progress
document.getElementById('upload-form').addEventListener('submit', function(e) {
  if (!fileInput.files.length) { e.preventDefault(); return; }
  const wrap = document.getElementById('upload-progress-wrap');
  const bar  = document.getElementById('upload-progress-bar');
  const lbl  = document.getElementById('prog-label');
  wrap.style.display = 'block';
  btnUpload.disabled = true;
  let pct = 0;
  const iv = setInterval(() => {
    if (pct < 90) { pct += Math.random() * 15; bar.style.width = Math.min(pct, 90) + '%'; }
  }, 180);
  // Will navigate away after submit so no need to clearInterval
});

// ---- Checkbox select all ----
document.getElementById('cb-all')?.addEventListener('change', function() {
  document.querySelectorAll('.cb-file').forEach(cb => cb.checked = this.checked);
  updateCount();
});

function updateCount() {
  const n = document.querySelectorAll('.cb-file:checked').length;
  document.getElementById('sel-count').textContent = n;
  const cbAll = document.getElementById('cb-all');
  if (cbAll) {
    const total = document.querySelectorAll('.cb-file').length;
    cbAll.indeterminate = n > 0 && n < total;
    cbAll.checked = n === total && total > 0;
  }
}

// ---- Search ----
function filterTable(q) {
  q = q.toLowerCase();
  const rows = document.querySelectorAll('#si-tbody .si-row');
  let visible = 0;
  rows.forEach(row => {
    const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
    const show = name.includes(q);
    row.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  const counter = document.getElementById('row-counter');
  if (counter) counter.textContent = visible + ' file';
}

// ---- Delete confirm ----
function confirmDelete(filename) {
  if (!confirm(`Hapus file:\n"${filename}"\n\nTindakan ini tidak dapat dibatalkan!`)) return;
  window.location.href = '<?= site_url('tools/script-inject/delete/') ?>' + encodeURIComponent(filename);
}

// ---- Bulk Delete ----
function bulkDeleteConfirm() {
  const checked = document.querySelectorAll('.cb-file:checked');
  if (checked.length === 0) { alert('Pilih minimal 1 file terlebih dahulu.'); return; }
  if (!confirm(`Hapus ${checked.length} file yang terpilih?\n\nTindakan ini tidak dapat dibatalkan!`)) return;
  document.getElementById('bulk-form').submit();
}

// ---- Copy Link ----
function copyLink(id, btn) {
  const input = document.getElementById('link-' + id);
  if (!input) return;
  navigator.clipboard.writeText(input.value).then(() => {
    const orig = btn.innerHTML;
    btn.innerHTML = '✅ Tersalin!';
    btn.style.background = '#198754';
    setTimeout(() => {
      btn.innerHTML = orig;
      btn.style.background = '#0d6efd';
    }, 1800);
  }).catch(() => {
    // fallback for older browsers
    input.select();
    document.execCommand('copy');
    btn.innerHTML = '✅ Tersalin!';
    btn.style.background = '#198754';
    setTimeout(() => {
      btn.innerHTML = '📋 Salin';
      btn.style.background = '#0d6efd';
    }, 1800);
  });
}
</script>

<?= $this->endSection() ?>
