<?php
require_once 'config.php';

echo "=== Cek Tabel Laporan ===\n";
$result = $conn->query("SELECT COUNT(*) as total FROM laporan");
$row = $result->fetch_assoc();
echo "Total laporan: " . $row['total'] . "\n";

echo "=== Detail Laporan ===\n";
$result = $conn->query("SELECT l.*, m.judul_modul, u.nama as nama_mhs FROM laporan l JOIN modul m ON l.modul_id=m.id JOIN users u ON l.mahasiswa_id=u.id");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Mahasiswa: " . $row['nama_mhs'] . ", Modul: " . $row['judul_modul'] . ", File: " . $row['file_laporan'] . "\n";
}

echo "=== Cek Pendaftaran Praktikum ===\n";
$result = $conn->query("SELECT pp.*, u.nama, mp.nama_mk FROM pendaftaran_praktikum pp JOIN users u ON pp.mahasiswa_id=u.id JOIN mata_praktikum mp ON pp.praktikum_id=mp.id");
while($row = $result->fetch_assoc()) {
    echo "Mahasiswa: " . $row['nama'] . ", Praktikum: " . $row['nama_mk'] . "\n";
}

echo "=== Cek Modul ===\n";
$result = $conn->query("SELECT m.*, mp.nama_mk FROM modul m JOIN mata_praktikum mp ON m.praktikum_id=mp.id");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Praktikum: " . $row['nama_mk'] . ", Modul: " . $row['judul_modul'] . "\n";
}

echo "=== Cek Users ===\n";
$result = $conn->query("SELECT id, nama, email, role FROM users");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Nama: " . $row['nama'] . ", Email: " . $row['email'] . ", Role: " . $row['role'] . "\n";
}

echo "=== Cek Mata Praktikum ===\n";
$result = $conn->query("SELECT * FROM mata_praktikum");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Kode: " . $row['kode_mk'] . ", Nama: " . $row['nama_mk'] . "\n";
}

echo "=== Debug: Cek Laporan dengan Join Lengkap ===\n";
$sql = "SELECT l.*, m.judul_modul, mp.nama_mk, u.nama as nama_mhs 
        FROM laporan l 
        JOIN modul m ON l.modul_id=m.id 
        JOIN users u ON l.mahasiswa_id=u.id 
        JOIN mata_praktikum mp ON m.praktikum_id=mp.id";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    echo "Laporan ID: " . $row['id'] . ", Mahasiswa: " . $row['nama_mhs'] . ", Praktikum: " . $row['nama_mk'] . ", Modul: " . $row['judul_modul'] . ", File: " . $row['file_laporan'] . "\n";
}
?> 