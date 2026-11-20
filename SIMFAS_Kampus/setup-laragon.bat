@echo off
setlocal
cd /d "%~dp0"
where composer >nul 2>nul
if errorlevel 1 (
  echo Composer belum terpasang. Install Composer terlebih dahulu, lalu jalankan file ini lagi.
  pause
  exit /b 1
)
set "DEST=%~dp0..\SIMFAS_Kampus_App"
if exist "%DEST%" (
  echo Folder SIMFAS_Kampus_App sudah ada. Ubah nama/hapus folder itu sebelum menjalankan setup.
  pause
  exit /b 1
)
echo Membuat proyek Laravel baru. Proses ini memerlukan internet...
composer create-project laravel/laravel "%DEST%"
if errorlevel 1 goto failed
for %%D in (app database resources routes tests) do xcopy "%~dp0%%D" "%DEST%\%%D\" /E /I /Y >nul
copy /Y "%~dp0README.md" "%DEST%\README_SIMFAS.md" >nul
 echo.
echo SIMFAS berhasil disiapkan di: %DEST%
echo Lanjutkan pengaturan database sesuai README_SIMFAS.md
pause
exit /b 0
:failed
echo Gagal membuat proyek Laravel. Periksa koneksi internet dan instalasi Composer.
pause
exit /b 1
