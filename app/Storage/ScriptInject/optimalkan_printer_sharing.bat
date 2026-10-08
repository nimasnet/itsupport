@echo off
:: Memeriksa hak akses Administrator
openfiles >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Harap jalankan file batch ini sebagai Administrator!
    echo Klik kanan file ini lalu pilih "Run as administrator".
    pause
    exit /b
)

echo =======================================================
echo    OPTIMALISASI LIMIT PRINTER SHARING WINDOWS
echo =======================================================
echo.

:: Opsi 1: Menurunkan waktu autodisconnect via CMD ke 1 menit
echo [1/2] Mengatur waktu pemutusan otomatis (Autodisconnect) menjadi 1 menit...
net config server /autodisconnect:1
if %errorlevel% equ 0 (
    echo [SUKSES] Autodisconnect berhasil diatur ke 1 menit.
) else (
    echo [GAGAL] Gagal mengatur net config server.
)
echo.

:: Opsi 2: Mengubah batas waktu idle session via Registry (GPO equivalent)
echo [2/2] Mengatur limit idle session via Registry...
reg add "HKLM\SYSTEM\CurrentControlSet\Services\LanmanServer\Parameters" /v "autodisconnect" /t REG_DWORD /d 1 /f >nul
if %errorlevel% equ 0 (
    echo [SUKSES] Batas waktu idle di Registry berhasil diperbarui.
) else (
    echo [GAGAL] Gagal memperbarui nilai Registry.
)
echo.

echo =======================================================
echo Proses selesai! Perubahan akan aktif setelah service direstart.
echo Menghentikan dan memulai ulang service Server...
echo =======================================================
net stop server /y
net start server
echo.
echo [SELESAI] Semua konfigurasi telah diterapkan.
pause
