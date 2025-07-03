<?php
session_start();
require_once 'config.php';

$page_title = 'Katalog Praktikum';

// Query untuk mengambil semua mata praktikum
$sql = "SELECT mp.*, u.nama as nama_asisten,
        (SELECT COUNT(*) FROM pendaftaran_praktikum pp WHERE pp.praktikum_id = mp.id) as jumlah_mahasiswa,
        (SELECT COUNT(*) FROM modul m WHERE m.praktikum_id = mp.id) as jumlah_modul
        FROM mata_praktikum mp 
        LEFT JOIN users u ON mp.asisten_id = u.id 
        ORDER BY mp.nama_mk ASC";
$result = $conn->query($sql);

// Cek status pendaftaran untuk setiap praktikum (jika user login)
$user_registered = [];
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa') {
    $user_id = $_SESSION['user_id'];
    $check_sql = "SELECT praktikum_id FROM pendaftaran_praktikum WHERE mahasiswa_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    while ($row = $check_result->fetch_assoc()) {
        $user_registered[] = $row['praktikum_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SIMPRAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">SIMPRAK</h1>
                </div>
                <nav class="flex space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-gray-900">Beranda</a>
                    <a href="katalog.php" class="text-blue-600 font-medium">Katalog</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] === 'asisten'): ?>
                            <a href="asisten/dashboard.php" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                        <?php else: ?>
                            <a href="mahasiswa/dashboard.php" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                        <?php endif; ?>
                        <a href="logout.php" class="text-gray-600 hover:text-gray-900">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-600 hover:text-gray-900">Login</a>
                        <a href="register.php" class="text-gray-600 hover:text-gray-900">Register</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Katalog Mata Praktikum</h1>
            <p class="text-gray-600">Pilih mata praktikum yang ingin Anda ikuti</p>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Praktikum Grid -->
        <?php if ($result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo htmlspecialchars($row['kode_mk']); ?>
                                </span>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa'): ?>
                                    <?php if (in_array($row['id'], $user_registered)): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Terdaftar
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($row['nama_mk']); ?>
                            </h3>
                            
                            <p class="text-gray-600 text-sm mb-4">
                                <?php echo htmlspecialchars($row['deskripsi'] ?: 'Tidak ada deskripsi'); ?>
                            </p>
                            
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <i class="fas fa-user-tie mr-2"></i>
                                <span><?php echo htmlspecialchars($row['nama_asisten'] ?: 'Belum ditugaskan'); ?></span>
                            </div>
                            
                            <!-- Stats -->
                            <div class="flex justify-between items-center mb-4 text-sm">
                                <span class="text-gray-600">
                                    <i class="fas fa-users mr-1"></i><?php echo $row['jumlah_mahasiswa']; ?> Mahasiswa
                                </span>
                                <span class="text-gray-600">
                                    <i class="fas fa-book mr-1"></i><?php echo $row['jumlah_modul']; ?> Modul
                                </span>
                            </div>
                            
                            <div class="flex space-x-2">
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa'): ?>
                                    <?php if (in_array($row['id'], $user_registered)): ?>
                                        <a href="mahasiswa/detail_praktikum.php?id=<?php echo $row['id']; ?>" 
                                           class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-eye mr-2"></i>Lihat Detail
                                        </a>
                                    <?php else: ?>
                                        <a href="daftar_praktikum.php?id=<?php echo $row['id']; ?>" 
                                           class="flex-1 bg-green-600 text-white text-center px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-plus mr-2"></i>Daftar
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="login.php" 
                                       class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Daftar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">📚</div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Belum ada mata praktikum</h3>
                <p class="text-gray-600">Mata praktikum akan ditambahkan oleh administrator.</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-600">&copy; 2024 SIMPRAK - Sistem Informasi Manajemen Praktikum</p>
        </div>
    </footer>
</body>
</html> 