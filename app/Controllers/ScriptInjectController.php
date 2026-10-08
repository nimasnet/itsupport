                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <?php

namespace App\Controllers;

class ScriptInjectController extends BaseController
{
    // Folder penyimpanan file yang diupload
    protected string $uploadPath = FCPATH . '../app/Storage/ScriptInject/';

    public function __construct()
    {
        // Pastikan folder storage ada
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    public function index()
    {
        if (!has_access('script_inject.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $data['title']     = 'Script Inject Manager';
        $data['top_color'] = '#f39c12';
        $data['files']     = $this->getFileList();
        $data['flash_msg'] = session()->getFlashdata('message');
        $data['flash_err'] = session()->getFlashdata('error');

        return view('tools/script_inject', $data);
    }

    public function upload()
    {
        if (!has_access('script_inject.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $file = $this->request->getFile('script_file');

        if (!$file || !$file->isValid()) {
            session()->setFlashdata('error', 'Tidak ada file yang dipilih atau file tidak valid.');
            return redirect()->to(site_url('tools/script-inject'));
        }

        // Allowed extensions
        $allowedExts = ['bat', 'ps1', 'sh', 'py', 'js', 'vbs', 'cmd', 'reg', 'inf', 'ini', 'conf', 'cfg', 'txt', 'sql', 'xml', 'json', 'yaml', 'yml', 'php', 'html', 'htm', 'css'];
        $ext = strtolower($file->getClientExtension());

        if (!in_array($ext, $allowedExts)) {
            session()->setFlashdata('error', "Ekstensi .$ext tidak diizinkan. Diizinkan: " . implode(', ', $allowedExts));
            return redirect()->to(site_url('tools/script-inject'));
        }

        // Max size 20MB
        if ($file->getSize() > 20 * 1024 * 1024) {
            session()->setFlashdata('error', 'Ukuran file maksimal 20 MB.');
            return redirect()->to(site_url('tools/script-inject'));
        }

        $newName = $file->getClientName();

        // Jika nama sudah ada, beri suffix timestamp
        if (file_exists($this->uploadPath . $newName)) {
            $newName = pathinfo($newName, PATHINFO_FILENAME)
                . '_' . date('Ymd_His')
                . '.' . $ext;
        }

        $file->move($this->uploadPath, $newName);

        session()->setFlashdata('message', "File <b>$newName</b> berhasil diupload.");
        return redirect()->to(site_url('tools/script-inject'));
    }

    public function download(string $filename)
    {
        if (!has_access('script_inject.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $filename = basename($filename); // sanitize path traversal
        $filepath = $this->uploadPath . $filename;

        if (!file_exists($filepath)) {
            session()->setFlashdata('error', 'File tidak ditemukan.');
            return redirect()->to(site_url('tools/script-inject'));
        }

        return $this->response
            ->setHeader('Content-Description', 'File Transfer')
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Content-Length', filesize($filepath))
            ->setHeader('Cache-Control', 'no-cache')
            ->setBody(file_get_contents($filepath));
    }

    public function delete(string $filename)
    {
        if (!has_access('script_inject.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $filename = basename($filename);
        $filepath = $this->uploadPath . $filename;

        if (file_exists($filepath)) {
            unlink($filepath);
            session()->setFlashdata('message', "File <b>$filename</b> berhasil dihapus.");
        } else {
            session()->setFlashdata('error', 'File tidak ditemukan.');
        }

        return redirect()->to(site_url('tools/script-inject'));
    }

    public function bulkDelete()
    {
        if (!has_access('script_inject.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $files = $this->request->getPost('selected_files');
        if (empty($files) || !is_array($files)) {
            session()->setFlashdata('error', 'Pilih minimal 1 file untuk dihapus.');
            return redirect()->to(site_url('tools/script-inject'));
        }

        $deleted = 0;
        foreach ($files as $f) {
            $filepath = $this->uploadPath . basename($f);
            if (file_exists($filepath)) {
                unlink($filepath);
                $deleted++;
            }
        }

        session()->setFlashdata('message', "$deleted file berhasil dihapus.");
        return redirect()->to(site_url('tools/script-inject'));
    }

    // ============================================================
    // HELPER
    // ============================================================
    private function getFileList(): array
    {
        $files = [];
        if (!is_dir($this->uploadPath)) {
            return $files;
        }

        foreach (glob($this->uploadPath . '*') as $filepath) {
            if (is_file($filepath)) {
                $files[] = [
                    'name'     => basename($filepath),
                    'size'     => $this->formatSize(filesize($filepath)),
                    'size_raw' => filesize($filepath),
                    'ext'      => strtolower(pathinfo($filepath, PATHINFO_EXTENSION)),
                    'mtime'    => filemtime($filepath),
                    'mtime_fmt' => date('d M Y H:i:s', filemtime($filepath)),
                ];
            }
        }

        // Sort by newest first
        usort($files, fn($a, $b) => $b['mtime'] - $a['mtime']);

        return $files;
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2)    . ' KB';
        return $bytes . ' B';
    }
}
