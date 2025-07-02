#!/bin/bash

# Jangan masukan komentar dalam kode

PROJECT_ROOT_DIR="/home/bayu/projek_herbalife_ci4"
WEB_USER="www-data"    
PROJECT_OWNER="www-data" # Owner untuk writable, sesuai diskusi terakhir

if [[ $EUID -ne 0 ]]; then
   echo "Script ini harus dijalankan sebagai root atau dengan sudo."
   exit 1
fi

if [ ! -d "$PROJECT_ROOT_DIR" ]; then
    echo "Error: Direktori proyek '$PROJECT_ROOT_DIR' tidak ditemukan."
    exit 1
fi

echo "--- Mengatur Izin Direktori Induk agar Nginx dapat Mengakses ---"

# 1. Pastikan /home/sorabi/ memiliki izin eksekusi (dan baca) untuk othersewewwr
# Ini penting agar www-data bisa masuk ke direktori home sorabi
echo "Mengatur izin untuk /home/bayu/..."
sudo chmod o+rx /home/bayu/
# chmod 755 akan memberi rwx untuk owner, r-x untuk group dan others.
# Ini aman karena tidak memberi izin tulis ke others.

# 2. Pastikan direktori root proyek itu sendiri memiliki izin yang sama
echo "Mengatur izin untuk $PROJECT_ROOT_DIR..."
sudo chmod o+rx "$PROJECT_ROOT_DIR"
# Atau bisa juga: sudo chmod 755 "$PROJECT_ROOT_DIR"

# Setelah ini, kita menjalankan kembali bagian permission proyek itu sendiri
echo "--- Menjalankan kembali pengaturan izin untuk proyek CodeIgniter 4 ---"

cd "$PROJECT_ROOT_DIR" || { echo "Gagal masuk ke direktori proyek."; exit 1; }

echo "Mengatur kepemilikan direktori 'writable' ke $PROJECT_OWNER:$WEB_USER..."
sudo chown -R "$PROJECT_OWNER":"$WEB_USER" writable

echo "Memberikan izin tulis untuk user dan grup pada 'writable' (ug+rwx), dan baca/eksekusi untuk 'others' (775/777)..."
sudo chmod -R ug+rwx writable
sudo chmod -R o+rx writable

echo "Mengatur setgid bit pada 'writable' (g+s)..."
sudo chmod -R g+s writable

echo "Mengatur izin default untuk file (664) dan direktori (775) lainnya pada seluruh proyek..."
sudo find . -type f -exec chmod 664 {} \;
sudo find . -type d -exec chmod 775 {} \;

echo "Memastikan file .env ada..."
if [ ! -f ".env" ]; then
    echo "File .env tidak ditemukan. Menyalin dari 'env'..."
    cp env .env
    echo "PENTING: Harap edit file .env untuk mengkonfigurasi database, baseURL, dll.!"
fi

echo "Pengaturan permission untuk deployment CodeIgniter 4 selesai."
echo "Pastikan konfigurasi web server (Nginx/Apache) sudah menunjuk ke '$PROJECT_ROOT_DIR/public'."
