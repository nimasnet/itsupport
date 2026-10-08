# Walkthrough - Implementasi Switch Network Management

Saya telah berhasil mengimplementasikan modul **Switch Network Management** secara penuh, mulai dari pembuatan tabel database, model, routing, controller, hingga tampilan view dengan modal CRUD, bulk delete, pencarian, dan impor/ekspor Excel yang responsif.

## Perubahan yang Dilakukan

### 1. Database
- **Tabel Baru**: Membuat tabel `switch_network` di database `db_cctv` dengan kolom:
  - `id` (Primary Key, Auto Increment)
  - `lokasi` (Lokasi fisik switch)
  - `name_switch` (Nama perangkat switch)
  - `ip_address` (Alamat IP switch)
  - `type_switch` (Tipe/merek switch)
  - `notes` (Catatan deskripsi tambahan)
  - `created_at` & `updated_at` (Timestamp data)

### 2. Model Backend
- **[SwitchNetworkModel.php](file:///c:/xampp/htdocs/itsupport/app/Models/SwitchNetworkModel.php)**: Model CodeIgniter 4 baru untuk mengelola operasi database di tabel `switch_network`.

### 3. Controller
- **[IpManagementController.php](file:///c:/xampp/htdocs/itsupport/app/Controllers/IpManagementController.php)**:
  - Menginisialisasi `SwitchNetworkModel` di constructor.
  - Memperbarui method `switchNetwork()` untuk mengambil seluruh data dari model.
  - Mengimplementasikan method penanganan CRUD: `switchNetworkStore()`, `switchNetworkUpdate()`, `switchNetworkDelete()`, `switchNetworkBulkDelete()`.
  - Mengimplementasikan fungsionalitas Excel: `switchNetworkImportExcel()` dan `switchNetworkDownloadTemplate()`.

### 4. Routing
- **[Routes.php](file:///c:/xampp/htdocs/itsupport/app/Config/Routes.php)**: Menambahkan endpoint CRUD untuk modul Switch Network di bawah grup `ip-management`.

### 5. Frontend View
- **[switch_network.php](file:///c:/xampp/htdocs/itsupport/app/Views/ip_management/switch_network.php)**:
  - Membuat antarmuka tabel Switch Network dengan kolom: **No, Lokasi, Name Switch, IP Address, Type Switch, Notes, Aksi**.
  - Menambahkan tombol aksi CRUD: **Tambah Switch Baru, Import Excel, Download Template, Hapus Terpilih**, dan kolom pencarian real-time.
  - Menyediakan modal popup dinamis untuk proses Add, Edit, dan Import Excel.

---

## Verifikasi & Pengujian Sintaks
- Semua file PHP yang dimodifikasi telah dlinting (`php -l`) dan dinyatakan bebas dari kesalahan sintaks.
- Struktur routing dan interaksi database telah disesuaikan agar cocok dengan pola menu User PC.
