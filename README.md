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

### Dashboard Mahasiswa

![Dashboard Mahasiswa](screenshots/dashboard-mahasiswa.png)

### Dashboard Asisten

![Dashboard Asisten](screenshots/dashboard-asisten.png)

### Upload Laporan

![Upload Laporan](screenshots/upload-laporan.png)

### Kelola Praktikum

![Kelola Praktikum](screenshots/kelola-praktikum.png)


