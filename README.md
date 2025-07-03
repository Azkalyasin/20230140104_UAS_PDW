# 🎓 SIMPRAK - Sistem Pengumpulan Tugas Praktikum

Sistem manajemen praktikum berbasis web yang memudahkan mahasiswa dan asisten dalam mengelola praktikum, modul, dan pengumpulan laporan tugas.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Struktur Database](#-struktur-database)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Cara Penggunaan](#-cara-penggunaan)
- [Struktur File](#-struktur-file)
- [Screenshot](#-screenshot)
- [Kontribusi](#-kontribusi)
- [Lisensi](#-lisensi)

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

### 🎨 **Fitur UI/UX**

- 🎨 Desain modern dengan glassmorphism
- 📱 Fully responsive design
- 🌈 Gradient colors yang menarik
- ⚡ Animasi dan transisi yang smooth
- 🔍 User-friendly interface
- 📊 Status badges yang informatif

## 🛠 Teknologi yang Digunakan

- **Backend**: PHP Native
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6.0
- **Design**: Glassmorphism, Gradient UI
- **Responsive**: Mobile-first approach

## 🗄 Struktur Database

### Tabel Utama:

- `users` - Data pengguna (mahasiswa dan asisten)
- `mata_praktikum` - Data praktikum
- `modul` - Data modul per praktikum
- `pendaftaran_praktikum` - Relasi mahasiswa-praktikum
- `laporan` - Data laporan yang dikumpulkan mahasiswa

## 🚀 Instalasi

### Prasyarat

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)
- Composer (opsional)

### Langkah Instalasi

1. **Clone Repository**

   ```bash
   git clone https://github.com/username/simprak.git
   cd simprak
   ```

2. **Setup Database**

   ```bash
   # Import database
   mysql -u username -p database_name < database.sql
   ```

3. **Konfigurasi Database**

   ```bash
   # Edit file config.php
   nano config.php
   ```

4. **Setup Folder Uploads**

   ```bash
   # Buat folder uploads
   mkdir uploads/laporan
   mkdir uploads/materi
   chmod 755 uploads/laporan
   chmod 755 uploads/materi
   ```

5. **Akses Aplikasi**
   ```
   http://localhost/simprak/
   ```

## ⚙️ Konfigurasi

### File `config.php`

```php
<?php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'simprak_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

### Pengaturan Upload

- **Maksimal ukuran file**: 10MB
- **Format yang didukung**: PDF, DOC, DOCX
- **Folder upload**: `uploads/laporan/` dan `uploads/materi/`

## 📖 Cara Penggunaan

### 👨‍🎓 **Untuk Mahasiswa**

1. **Registrasi/Login**

   - Akses halaman login
   - Daftar akun baru atau login dengan akun yang ada

2. **Daftar Praktikum**

   - Lihat daftar praktikum yang tersedia
   - Klik "Daftar Praktikum" untuk mendaftar

3. **Upload Laporan**

   - Pilih modul dari dropdown
   - Upload file laporan (PDF/DOC/DOCX)
   - Lihat status pengumpulan dan nilai

4. **Kelola Praktikum**
   - Lihat detail praktikum yang diikuti
   - Keluar dari praktikum jika diperlukan

### 👨‍🏫 **Untuk Asisten**

1. **Login Asisten**

   - Login dengan akun asisten yang sudah dibuat

2. **Kelola Praktikum**

   - Tambah/edit/hapus praktikum
   - Kelola modul per praktikum
   - Upload materi praktikum

3. **Kelola Pengguna**

   - Tambah/edit/hapus mahasiswa dan asisten
   - Reset password pengguna

4. **Penilaian Laporan**
   - Lihat laporan yang masuk
   - Berikan nilai dan feedback

## 📁 Struktur File

```
SistemPengumpulanTugas/
├── asisten/                    # Halaman asisten
│   ├── dashboard.php
│   ├── kelola_praktikum.php
│   ├── kelola_modul.php
│   ├── kelola_pengguna.php
│   ├── laporan_masuk.php
│   └── templates/
│       ├── header.php
│       └── footer.php
├── mahasiswa/                  # Halaman mahasiswa
│   ├── dashboard.php
│   ├── praktikum_saya.php
│   ├── daftar_praktikum.php
│   ├── detail_praktikum.php
│   ├── upload_laporan.php
│   ├── keluar_praktikum.php
│   └── templates/
│       ├── header_mahasiswa.php
│       └── footer_mahasiswa.php
├── uploads/                    # Folder upload
│   ├── laporan/
│   └── materi/
├── config.php                  # Konfigurasi database
├── database.sql               # Struktur database
├── index.php                  # Halaman utama
├── login.php                  # Halaman login
├── register.php               # Halaman registrasi
├── logout.php                 # Logout
└── README.md                  # Dokumentasi
```

## 📸 Screenshot

### Dashboard Mahasiswa

![Dashboard Mahasiswa](screenshots/dashboard-mahasiswa.png)

### Dashboard Asisten

![Dashboard Asisten](screenshots/dashboard-asisten.png)

### Upload Laporan

![Upload Laporan](screenshots/upload-laporan.png)

### Kelola Praktikum

![Kelola Praktikum](screenshots/kelola-praktikum.png)

## 🔧 Fitur Teknis

### Keamanan

- ✅ Session management
- ✅ SQL injection prevention
- ✅ File upload validation
- ✅ Role-based access control
- ✅ Password hashing

### Performa

- ✅ Optimized database queries
- ✅ Efficient file handling
- ✅ Responsive image loading
- ✅ Caching strategies

### Kompatibilitas

- ✅ Cross-browser compatibility
- ✅ Mobile responsive
- ✅ Progressive enhancement
- ✅ Accessibility features

## 🤝 Kontribusi

Kontribusi sangat diterima! Berikut cara berkontribusi:

1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

### Guidelines

- Gunakan coding standards yang konsisten
- Tambahkan komentar untuk kode yang kompleks
- Test fitur baru sebelum submit
- Update dokumentasi jika diperlukan

## 🐛 Troubleshooting

### Masalah Umum

**1. Koneksi Database Error**

```
Error: Connection failed
```

**Solusi**: Periksa konfigurasi database di `config.php`

**2. Upload File Gagal**

```
Error: File upload failed
```

**Solusi**: Periksa permission folder `uploads/` dan ukuran file

**3. Session Error**

```
Error: Session not working
```

**Solusi**: Pastikan `session_start()` dipanggil di awal file

**4. Page Not Found**

```
Error: 404 Not Found
```

**Solusi**: Periksa konfigurasi web server dan URL rewriting

## 📝 Changelog

### v1.0.0 (2024-01-XX)

- ✅ Initial release
- ✅ Fitur login dan registrasi
- ✅ Dashboard mahasiswa dan asisten
- ✅ Manajemen praktikum dan modul
- ✅ Upload dan penilaian laporan
- ✅ Interface responsif

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

## 👥 Tim Pengembang

- **Lead Developer**: [Nama Anda]
- **UI/UX Designer**: [Nama Designer]
- **Database Admin**: [Nama DBA]

## 📞 Kontak

- **Email**: your.email@example.com
- **GitHub**: [@username](https://github.com/username)
- **Website**: [https://yourwebsite.com](https://yourwebsite.com)

## 🙏 Ucapan Terima Kasih

Terima kasih kepada semua kontributor dan pengguna yang telah membantu mengembangkan SIMPRAK menjadi lebih baik.

---

**SIMPRAK** - Membuat manajemen praktikum lebih mudah dan efisien! 🎓✨
