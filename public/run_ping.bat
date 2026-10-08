@echo off
:loop
echo Memulai Ping ke seluruh IP CCTV...
for /F "tokens=*" %%A in (ip_list.txt) do (
    ping -n 1 -w 1000 %%A > nul
    if errorlevel 1 (
        curl -s "http://localhost/itsupport/public/api_save_ping.php?ip=%%A^&status=DOWN" > nul
    ) else (
        curl -s "http://localhost/itsupport/public/api_save_ping.php?ip=%%A^&status=UP" > nul
    )
)
echo Selesai. Menunggu 5 detik...
timeout /t 5 > nul
goto loop
