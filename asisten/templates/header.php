<?php
require_once '../config.php';

// Cek apakah user sudah login dan role-nya asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
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
    <div id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-blue-800 text-white transform md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="flex items-center justify-between p-4 border-b border-blue-700">
            <h1 class="text-xl font-bold">SIMPRAK</h1>
            <button id="closeSidebar" class="md:hidden text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- User Info -->
        <div class="p-4 border-b border-blue-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <p class="font-semibold"><?= htmlspecialchars($user_data['nama']) ?></p>
                    <p class="text-sm text-blue-200"><?= htmlspecialchars($user_data['email']) ?></p>
                    <p class="text-xs text-blue-300">Asisten</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition-colors <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-700' : '' ?>">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="kelola_praktikum.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition-colors <?= basename($_SERVER['PHP_SELF']) == 'kelola_praktikum.php' ? 'bg-blue-700' : '' ?>">
                        <i class="fas fa-book w-5"></i>
                        <span>Kelola Praktikum</span>
                    </a>
                </li>
                <li>
                    <a href="kelola_modul.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition-colors <?= basename($_SERVER['PHP_SELF']) == 'kelola_modul.php' ? 'bg-blue-700' : '' ?>">
                        <i class="fas fa-file-alt w-5"></i>
                        <span>Kelola Modul</span>
                    </a>
                </li>
                <li>
                    <a href="laporan_masuk.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition-colors <?= basename($_SERVER['PHP_SELF']) == 'laporan_masuk.php' ? 'bg-blue-700' : '' ?>">
                        <i class="fas fa-inbox w-5"></i>
                        <span>Laporan Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="kelola_pengguna.php" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-700 transition-colors <?= basename($_SERVER['PHP_SELF']) == 'kelola_pengguna.php' ? 'bg-blue-700' : '' ?>">
                        <i class="fas fa-users w-5"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Logout -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-700">
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
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="p-6">
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