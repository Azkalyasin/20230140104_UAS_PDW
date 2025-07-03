<?php
require_once '../config.php';

// Cek apakah user sudah login dan role-nya mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../login.php');
    exit();
}

// Ambil data user yang sedang login
$user_id = $_SESSION['user_id'];
$user_query = "SELECT nama, email FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Hitung jumlah praktikum yang diikuti
$praktikum_count_query = "SELECT COUNT(*) as total FROM pendaftaran_praktikum WHERE mahasiswa_id = ?";
$stmt = $conn->prepare($praktikum_count_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$praktikum_count_result = $stmt->get_result();
$praktikum_count = $praktikum_count_result->fetch_assoc()['total'];

// Hitung jumlah laporan yang sudah dikumpulkan
$laporan_count_query = "SELECT COUNT(*) as total FROM laporan WHERE mahasiswa_id = ?";
$stmt = $conn->prepare($laporan_count_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$laporan_count_result = $stmt->get_result();
$laporan_count = $laporan_count_result->fetch_assoc()['total'];

if (!$praktikum_count_result) {
    echo "<div style='color:red'>SQL Error: " . $conn->error . "</div>";
}
echo "<div style='color:blue'>Jumlah Praktikum: " . ($praktikum_count_result ? $praktikum_count_result->num_rows : 0) . "</div>";

echo "<div style='color:blue'>Stats: " . print_r($stats, true) . "</div>";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - SIMPRAK' : 'SIMPRAK - Sistem Pengumpulan Tugas Praktikum' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-green-800 text-white transform md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="flex items-center justify-between p-4 border-b border-green-700">
            <h1 class="text-xl font-bold">SIMPRAK</h1>
            <button id="closeSidebar" class="md:hidden text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- User Info -->
        <div class="p-4 border-b border-green-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-graduate text-white"></i>
                </div>
                <div>
                    <p class="font-semibold"><?= htmlspecialchars($user_data['nama']) ?></p>
                    <p class="text-sm text-green-200"><?= htmlspecialchars($user_data['email']) ?></p>
                    <p class="text-xs text-green-300">Mahasiswa</p>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-green-200">Praktikum:</span>
                    <span class="font-semibold"><?= $praktikum_count ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-green-200">Laporan:</span>
                    <span class="font-semibold"><?= $laporan_count ?></span>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-green-700 transition-colors <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-green-700' : '' ?>">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="praktikum_saya.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-green-700 transition-colors <?= basename($_SERVER['PHP_SELF']) == 'praktikum_saya.php' ? 'bg-green-700' : '' ?>">
                        <i class="fas fa-book w-5"></i>
                        <span>Praktikum Saya</span>
                    </a>
                </li>
                <li>
                    <a href="daftar_praktikum.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-green-700 transition-colors <?= basename($_SERVER['PHP_SELF']) == 'daftar_praktikum.php' ? 'bg-green-700' : '' ?>">
                        <i class="fas fa-plus-circle w-5"></i>
                        <span>Daftar Praktikum</span>
                    </a>
                </li>
                <li>
                    <a href="../katalog.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-green-700 transition-colors <?= basename($_SERVER['PHP_SELF']) == 'katalog.php' ? 'bg-green-700' : '' ?>">
                        <i class="fas fa-search w-5"></i>
                        <span>Katalog Praktikum</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Logout -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-green-700">
            <a href="../logout.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-red-600 transition-colors text-red-300 hover:text-white">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center space-x-4">
                    <button id="openSidebar" class="md:hidden text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800"><?= isset($page_title) ? $page_title : 'Dashboard' ?></h2>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-clock mr-1"></i>
                        <span id="current-time"></span>
                    </div>
                    <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-graduate text-white text-sm"></i>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['warning'])): ?>
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?= htmlspecialchars($_SESSION['warning']) ?>
                </div>
            </div>
            <?php unset($_SESSION['warning']); ?>
        <?php endif; ?>
        <main class="p-6">
            <div style="background:yellow;color:black;padding:10px;">DEBUG: Masuk ke konten utama!</div>
            <table border="1" cellpadding="8" style="margin-top:20px;">
                <tr>
                    <th>Statistik</th>
                    <th>Jumlah</th>
                </tr>
                <tr>
                    <td>Praktikum Diikuti</td>
                    <td><?php echo isset($stats['total_praktikum']) ? $stats['total_praktikum'] : 0; ?></td>
                </tr>
                <tr>
                    <td>Laporan Dikumpulkan</td>
                    <td><?php echo isset($stats['total_laporan']) ? $stats['total_laporan'] : 0; ?></td>
                </tr>
                <tr>
                    <td>Menunggu Penilaian</td>
                    <td><?php echo isset($stats['menunggu_nilai']) ? $stats['menunggu_nilai'] : 0; ?></td>
                </tr>
            </table>
        </main>
    </div>
</body>
</html>