# 🎓 SIMPRAK - Sistem Pengumpulan Tugas Praktikum

Sistem manajemen praktikum berbasis web yang memudahkan mahasiswa dan asisten dalam mengelola praktikum, modul, dan pengumpulan laporan tugas.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Struktur Database](#-struktur-database)
- [Screenshot](#-screenshot)


## ✨ Fitur Utama

### 👨‍🎓 **Fitur Mahasiswa**

- 🔐 Login dan registrasi akun mahasiswa
- 📚 Melihat daftar praktikum yang tersedia
- 📝 Mendaftar ke praktikum yang diinginkan
- 📖 Melihat detail praktikum dan modul
- 📤 Upload laporan tugas per modul
- 📊 Melihat status pengumpulan dan nilai
- 🚪 Keluar dari praktikum (dengan konfirmasi)
- 📱 Interface responsif untuk mobile dan desktop

### 👨‍🏫 **Fitur Asisten**

- 🔐 Login dengan akun asisten
- 📚 Manajemen praktikum (CRUD)
- 📖 Manajemen modul praktikum
- 👥 Manajemen pengguna (mahasiswa dan asisten)
- 📄 Upload materi praktikum
- 📊 Melihat laporan yang masuk
- ✅ Penilaian laporan mahasiswa
- 📈 Dashboard dengan statistik


## 🛠 Teknologi yang Digunakan

- **Backend**: PHP Native
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6.0

## 🗄 Struktur Database

### Tabel Utama:

- `users` - Data pengguna (mahasiswa dan asisten)
- `mata_praktikum` - Data praktikum
- `modul` - Data modul per praktikum
- `pendaftaran_praktikum` - Relasi mahasiswa-praktikum
- `laporan` - Data laporan yang dikumpulkan mahasiswa

### Pengaturan Upload

- **Maksimal ukuran file**: 10MB
- **Format yang didukung**: PDF, DOC, DOCX
- **Folder upload**: `uploads/laporan/` dan `uploads/materi/`



## 📸 Screenshot

## Mahasiswa


![Dashboard Mahasiswa](screenshots/dashboard-mahasiswa.png)

## Asisten

### Mengelola Mata Praktikum

![Screenshot 2025-07-03 105237](https://github.com/user-attachments/assets/0b2beca0-c0e0-4aa1-813e-22f79a14c394)

### Mengelola Modul

![Screenshot 2025-07-03 105300](https://github.com/user-attachments/assets/0ed443c2-b850-4705-88b8-84d8d60e3929)

### Melihat Laporan Masuk

![Screenshot 2025-07-03 105323](https://github.com/user-attachments/assets/7e27c06d-b5a1-4266-afb6-207b2c5097e6)

### Mengelola Akun Pengguna

![Screenshot 2025-07-03 105342](https://github.com/user-attachments/assets/f22c2db7-9c8a-4700-bec2-2d1e8a4db15b)





