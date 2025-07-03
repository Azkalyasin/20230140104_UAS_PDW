<?php
session_start();
require_once 'config.php';

$page_title = 'Register';

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'asisten') {
        header('Location: asisten/dashboard.php');
    } else {
        header('Location: mahasiswa/dashboard.php');
    }
    exit();
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    
    // Validasi
    if (empty($nama) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek apakah email sudah terdaftar
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Hash password dan insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);
            
            if ($insert_stmt->execute()) {
                $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
                header('Location: login.php');
                exit();
            } else {
                $error = "Gagal mendaftar. Silakan coba lagi.";
            }
        }
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
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">SIMPRAK</h1>
            <p class="text-gray-600">Sistem Informasi Manajemen Praktikum</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Register</h2>
                <p class="text-gray-600">Daftar akun baru</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap
                    </label>
                    <input type="text" id="nama" name="nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Masukkan nama lengkap Anda"
                           value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Masukkan email Anda"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tag mr-2"></i>Role
                    </label>
                    <select id="role" name="role" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih role</option>
                        <option value="mahasiswa" <?php echo (isset($_POST['role']) && $_POST['role'] === 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                        <option value="asisten" <?php echo (isset($_POST['role']) && $_POST['role'] === 'asisten') ? 'selected' : ''; ?>>Asisten</option>
                    </select>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Minimal 6 karakter">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Konfirmasi Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ulangi password Anda">
                </div>
                
                <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>Register
                </button>
            </form>
            
            <div class="text-center mt-6">
                <p class="text-gray-600">
                    Sudah punya akun? 
                    <a href="login.php" class="text-blue-600 hover:text-blue-800 font-medium">
                        Login di sini
                    </a>
                </p>
            </div>
        </div>
        
        <div class="text-center">
            <a href="index.php" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>