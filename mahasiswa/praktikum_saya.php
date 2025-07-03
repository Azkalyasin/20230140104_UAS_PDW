<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil semua praktikum yang diikuti mahasiswa
$sql_praktikum = "SELECT pp.praktikum_id, mp.nama_mk, mp.kode_mk
    FROM pendaftaran_praktikum pp
    JOIN mata_praktikum mp ON pp.praktikum_id = mp.id
    WHERE pp.mahasiswa_id = ?
    ORDER BY mp.nama_mk ASC";
$stmt = $conn->prepare($sql_praktikum);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$praktikum_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praktikum Saya</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .sidebar-header {
            text-align: center;
            padding: 0 30px 30px;
            border-bottom: 2px solid #f7fafc;
            margin-bottom: 30px;
        }
        
        .sidebar-logo {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 15px;
        }
        
        .sidebar-title {
            color: #2d3748;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .sidebar-subtitle {
            color: #718096;
            font-size: 0.9rem;
        }
        
        .sidebar-menu {
            padding: 0 20px;
        }
        
        .menu-item {
            margin-bottom: 10px;
        }
        
        .menu-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #4a5568;
            text-decoration: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .menu-link:hover {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
            transform: translateX(5px);
        }
        
        .menu-link.active {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
        }
        
        .menu-icon {
            width: 20px;
            margin-right: 15px;
            font-size: 1.1rem;
        }
        
        .menu-text {
            font-size: 1rem;
        }
        
        .logout-item {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f7fafc;
        }
        
        .logout-link {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .logout-link:hover {
            background: linear-gradient(135deg, #ff5252, #d32f2f);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }
        
        .content-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .page-title {
            color: #4a5568;
            font-size: 2.5rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .page-subtitle {
            color: #718096;
            font-size: 1.1rem;
        }
        
        .praktikum-grid {
            display: grid;
            gap: 25px;
        }
        
        .praktikum-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .praktikum-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .praktikum-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f7fafc;
        }
        
        .praktikum-icon {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-right: 25px;
        }
        
        .praktikum-info h3 {
            color: #2d3748;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .praktikum-code {
            color: #718096;
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .modul-list {
            display: grid;
            gap: 20px;
        }
        
        .modul-item {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .modul-item:hover {
            background: linear-gradient(135deg, #edf2f7, #e2e8f0);
            transform: translateX(5px);
        }
        
        .modul-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modul-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2d3748;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-belum {
            background: linear-gradient(135deg, #fed7d7, #feb2b2);
            color: #c53030;
        }
        
        .status-menunggu {
            background: linear-gradient(135deg, #fef5e7, #fbd38d);
            color: #d69e2e;
        }
        
        .status-nilai {
            background: linear-gradient(135deg, #c6f6d5, #9ae6b4);
            color: #22543d;
        }
        
        .modul-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 12px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-upload {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }
        
        .btn-upload:hover {
            background: linear-gradient(135deg, #38a169, #2f855a);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
        }
        
        .btn-update {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: white;
        }
        
        .btn-update:hover {
            background: linear-gradient(135deg, #dd6b20, #c05621);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(237, 137, 54, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .empty-icon {
            font-size: 5rem;
            color: #a0aec0;
            margin-bottom: 25px;
        }
        
        .empty-title {
            font-size: 1.8rem;
            color: #4a5568;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .empty-desc {
            color: #718096;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .empty-action {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .empty-action:hover {
            background: linear-gradient(135deg, #3182ce, #2c5282);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(66, 153, 225, 0.3);
        }
        
        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                background: rgba(255, 255, 255, 0.95);
                border: none;
                border-radius: 10px;
                padding: 10px;
                font-size: 1.2rem;
                color: #4a5568;
                cursor: pointer;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }
            
            .content-header {
                padding: 20px;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .praktikum-card {
                padding: 20px;
            }
            
            .praktikum-header {
                flex-direction: column;
                text-align: center;
            }
            
            .praktikum-icon {
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .modul-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .modul-actions {
                justify-content: center;
            }
            
            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .page-title {
                font-size: 1.8rem;
            }
            
            .praktikum-card {
                padding: 15px;
            }
            
            .modul-item {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="sidebar-title">SIMPRAK</div>
                <div class="sidebar-subtitle">Mahasiswa Portal</div>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-item">
                    <a href="dashboard.php" class="menu-link">
                        <i class="fas fa-home menu-icon"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="praktikum_saya.php" class="menu-link active">
                        <i class="fas fa-book-open menu-icon"></i>
                        <span class="menu-text">Praktikum Saya</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="daftar_praktikum.php" class="menu-link">
                        <i class="fas fa-list-alt menu-icon"></i>
                        <span class="menu-text">Daftar Praktikum</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="detail_praktikum.php" class="menu-link">
                        <i class="fas fa-info-circle menu-icon"></i>
                        <span class="menu-text">Detail Praktikum</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="upload_laporan.php" class="menu-link">
                        <i class="fas fa-upload menu-icon"></i>
                        <span class="menu-text">Upload Laporan</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="keluar_praktikum.php" class="menu-link">
                        <i class="fas fa-sign-out-alt menu-icon"></i>
                        <span class="menu-text">Keluar Praktikum</span>
                    </a>
                </div>
                
                <div class="menu-item logout-item">
                    <a href="../logout.php" class="menu-link logout-link">
                        <i class="fas fa-power-off menu-icon"></i>
                        <span class="menu-text">Logout</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1 class="page-title"><i class="fas fa-book-open"></i> Praktikum Saya</h1>
                <p class="page-subtitle">Kelola praktikum yang sedang Anda ikuti</p>
            </div>

            <?php if ($praktikum_result && $praktikum_result->num_rows > 0): ?>
                <div class="praktikum-grid">
                    <?php while($praktikum = $praktikum_result->fetch_assoc()): ?>
                        <div class="praktikum-card">
                            <div class="praktikum-header">
                                <div class="praktikum-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="praktikum-info">
                                    <h3><?php echo htmlspecialchars($praktikum['nama_mk']); ?></h3>
                                    <div class="praktikum-code"><?php echo htmlspecialchars($praktikum['kode_mk']); ?></div>
                                </div>
                            </div>
                            
                            <?php
                            // Ambil semua modul untuk praktikum ini
                            $sql_modul = "SELECT m.id, m.judul_modul FROM modul m WHERE m.praktikum_id = ? ORDER BY m.id ASC";
                            $stmt_modul = $conn->prepare($sql_modul);
                            $stmt_modul->bind_param("i", $praktikum['praktikum_id']);
                            $stmt_modul->execute();
                            $modul_result = $stmt_modul->get_result();
                            if ($modul_result->num_rows > 0):
                            ?>
                                <div class="modul-list">
                                    <?php while($modul = $modul_result->fetch_assoc()): ?>
                                        <div class="modul-item">
                                            <div class="modul-header">
                                                <div class="modul-title"><?php echo htmlspecialchars($modul['judul_modul']); ?></div>
                                                <?php
                                                // Cek status laporan mahasiswa untuk modul ini
                                                $sql_laporan = "SELECT nilai FROM laporan WHERE mahasiswa_id = ? AND modul_id = ?";
                                                $stmt_laporan = $conn->prepare($sql_laporan);
                                                $stmt_laporan->bind_param("ii", $user_id, $modul['id']);
                                                $stmt_laporan->execute();
                                                $laporan_result = $stmt_laporan->get_result();
                                                if ($laporan = $laporan_result->fetch_assoc()) {
                                                    if ($laporan['nilai'] !== null) {
                                                        echo '<span class="status-badge status-nilai">Nilai: ' . htmlspecialchars($laporan['nilai']) . '</span>';
                                                    } else {
                                                        echo '<span class="status-badge status-menunggu">Menunggu Penilaian</span>';
                                                    }
                                                } else {
                                                    echo '<span class="status-badge status-belum">Belum Upload</span>';
                                                }
                                                ?>
                                            </div>
                                            <div class="modul-actions">
                                                <?php if ($laporan_result->num_rows > 0): ?>
                                                    <a href="upload_laporan.php?modul_id=<?php echo $modul['id']; ?>" class="action-btn btn-update">
                                                        <i class="fas fa-edit"></i> Update Laporan
                                                    </a>
                                                <?php else: ?>
                                                    <a href="upload_laporan.php?modul_id=<?php echo $modul['id']; ?>" class="action-btn btn-upload">
                                                        <i class="fas fa-upload"></i> Upload Laporan
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; padding: 30px; color: #718096; font-style: italic;">
                                    <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 15px; display: block; color: #a0aec0;"></i>
                                    Belum ada modul untuk praktikum ini.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="empty-title">Belum ada praktikum yang diikuti</div>
                    <div class="empty-desc">Silakan daftar ke praktikum yang tersedia untuk memulai pembelajaran</div>
                    <a href="daftar_praktikum.php" class="empty-action">
                        <i class="fas fa-plus"></i> Daftar Praktikum
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('open');
            }
        });
    </script>
</body>
</html> 