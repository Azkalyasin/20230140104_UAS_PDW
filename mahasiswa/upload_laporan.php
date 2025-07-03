<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil semua modul dari praktikum yang diikuti mahasiswa
$sql = "SELECT m.id, m.judul_modul, m.deskripsi_modul, mp.nama_mk, mp.kode_mk,
        (SELECT COUNT(*) FROM laporan l WHERE l.modul_id = m.id AND l.mahasiswa_id = ?) as sudah_upload,
        (SELECT l.nilai FROM laporan l WHERE l.modul_id = m.id AND l.mahasiswa_id = ? LIMIT 1) as nilai
        FROM modul m
        JOIN mata_praktikum mp ON m.praktikum_id = mp.id
        JOIN pendaftaran_praktikum pp ON mp.id = pp.praktikum_id
        WHERE pp.mahasiswa_id = ?
        ORDER BY mp.nama_mk ASC, m.id ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modul_id']) && isset($_FILES['laporan'])) {
    $modul_id = $_POST['modul_id'];
    $file = $_FILES['laporan'];
    
    // Validasi file
    $allowed_types = ['pdf', 'doc', 'docx'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        echo "<script>alert('Format file tidak didukung. Gunakan PDF, DOC, atau DOCX.');</script>";
    } elseif ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
        echo "<script>alert('Ukuran file terlalu besar. Maksimal 10MB.');</script>";
    } else {
        // Generate unique filename
        $filename = time() . '_' . $user_id . '_' . $modul_id . '.' . $file_extension;
        $upload_path = '../uploads/laporan/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Check if already uploaded
            $check_sql = "SELECT id FROM laporan WHERE mahasiswa_id = ? AND modul_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $user_id, $modul_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Update existing laporan
                $update_sql = "UPDATE laporan SET file_laporan = ?, tanggal_upload = NOW() WHERE mahasiswa_id = ? AND modul_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("sii", $filename, $user_id, $modul_id);
                
                if ($update_stmt->execute()) {
                    echo "<script>alert('Laporan berhasil diperbarui!'); window.location.reload();</script>";
                } else {
                    echo "<script>alert('Gagal memperbarui laporan. Silakan coba lagi.');</script>";
                }
            } else {
                // Insert new laporan
                $insert_sql = "INSERT INTO laporan (mahasiswa_id, modul_id, file_laporan, tanggal_upload) VALUES (?, ?, ?, NOW())";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iis", $user_id, $modul_id, $filename);
                
                if ($insert_stmt->execute()) {
                    echo "<script>alert('Laporan berhasil diupload!'); window.location.reload();</script>";
                } else {
                    echo "<script>alert('Gagal upload laporan. Silakan coba lagi.');</script>";
                }
            }
        } else {
            echo "<script>alert('Gagal upload file. Silakan coba lagi.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Laporan</title>
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
        
        .upload-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .section-title {
            color: #2d3748;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .section-icon {
            margin-right: 12px;
            color: #4299e1;
        }
        
        .modul-selector {
            margin-bottom: 25px;
        }
        
        .select-label {
            display: block;
            color: #4a5568;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .modul-select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
        }
        
        .modul-select:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }
        
        .modul-info {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }
        
        .modul-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .modul-icon {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 15px;
        }
        
        .modul-title {
            color: #2d3748;
            font-size: 1.3rem;
            font-weight: 700;
        }
        
        .modul-description {
            color: #718096;
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-uploaded {
            background: linear-gradient(135deg, #c6f6d5, #9ae6b4);
            color: #22543d;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #fef5e7, #fbd38d);
            color: #d69e2e;
        }
        
        .status-graded {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
        }
        
        .upload-form {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border-radius: 15px;
            padding: 25px;
            border: 2px dashed #cbd5e0;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .upload-form:hover {
            border-color: #4299e1;
            background: linear-gradient(135deg, #edf2f7, #e2e8f0);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #4299e1;
            margin-bottom: 15px;
        }
        
        .upload-text {
            color: #4a5568;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        
        .file-input {
            display: none;
        }
        
        .file-label {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .file-label:hover {
            background: linear-gradient(135deg, #3182ce, #2c5282);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(66, 153, 225, 0.3);
        }
        
        .selected-file {
            margin-top: 15px;
            padding: 10px;
            background: white;
            border-radius: 10px;
            color: #4a5568;
            font-size: 0.9rem;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .submit-btn:hover {
            background: linear-gradient(135deg, #38a169, #2f855a);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
        }
        
        .submit-btn:disabled {
            background: linear-gradient(135deg, #a0aec0, #718096);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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
            
            .upload-section {
                padding: 20px;
            }
            
            .modul-header {
                flex-direction: column;
                text-align: center;
            }
            
            .modul-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .upload-form {
                padding: 20px;
            }
            
            .upload-icon {
                font-size: 2.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .page-title {
                font-size: 1.8rem;
            }
            
            .upload-section {
                padding: 15px;
            }
            
            .upload-form {
                padding: 15px;
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
                    <a href="praktikum_saya.php" class="menu-link">
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
                    <a href="upload_laporan.php" class="menu-link active">
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
                <h1 class="page-title"><i class="fas fa-upload"></i> Upload Laporan</h1>
                <p class="page-subtitle">Upload laporan praktikum Anda</p>
            </div>

            <?php if ($result && $result->num_rows > 0): ?>
                <div class="upload-section">
                    <h2 class="section-title">
                        <i class="fas fa-file-upload section-icon"></i>
                        Pilih Modul untuk Upload
                    </h2>
                    
                    <div class="modul-selector">
                        <label for="modul-select" class="select-label">Pilih Modul:</label>
                        <select id="modul-select" class="modul-select" onchange="showModulInfo()">
                            <option value="">-- Pilih Modul --</option>
                            <?php 
                            $current_praktikum = '';
                            while($row = $result->fetch_assoc()): 
                                if ($current_praktikum != $row['nama_mk']):
                                    if ($current_praktikum != '') echo '</optgroup>';
                                    echo '<optgroup label="' . htmlspecialchars($row['nama_mk']) . '">';
                                    $current_praktikum = $row['nama_mk'];
                                endif;
                            ?>
                                <option value="<?php echo $row['id']; ?>" 
                                        data-title="<?php echo htmlspecialchars($row['judul_modul']); ?>"
                                        data-description="<?php echo htmlspecialchars($row['deskripsi_modul'] ?: 'Deskripsi modul belum tersedia'); ?>"
                                        data-praktikum="<?php echo htmlspecialchars($row['nama_mk']); ?>"
                                        data-uploaded="<?php echo $row['sudah_upload']; ?>"
                                        data-nilai="<?php echo $row['nilai']; ?>">
                                    <?php echo htmlspecialchars($row['judul_modul']); ?>
                                    <?php if ($row['sudah_upload'] > 0): ?>
                                        - <?php echo $row['nilai'] !== null ? 'Nilai: ' . $row['nilai'] : 'Menunggu Penilaian'; ?>
                                    <?php endif; ?>
                                </option>
                            <?php 
                                if ($current_praktikum != '') echo '</optgroup>';
                            endwhile; 
                            ?>
                        </select>
                    </div>
                    
                    <div id="modul-info" class="modul-info" style="display: none;">
                        <div class="modul-header">
                            <div class="modul-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div>
                                <div class="modul-title" id="modul-title"></div>
                                <div id="modul-status"></div>
                            </div>
                        </div>
                        <div class="modul-description" id="modul-description"></div>
                    </div>
                    
                    <div id="upload-form" class="upload-form" style="display: none;">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">Pilih file laporan untuk diupload</div>
                        
                        <form method="post" enctype="multipart/form-data" id="laporan-form">
                            <input type="hidden" name="modul_id" id="selected-modul-id">
                            <input type="file" name="laporan" id="file-input" class="file-input" accept=".pdf,.doc,.docx" onchange="updateFileName()">
                            <label for="file-input" class="file-label">
                                <i class="fas fa-folder-open"></i> Pilih File
                            </label>
                            
                            <div id="selected-file" class="selected-file" style="display: none;"></div>
                            
                            <button type="submit" class="submit-btn" id="submit-btn" disabled>
                                <i class="fas fa-upload"></i> Upload Laporan
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="empty-title">Belum ada praktikum yang diikuti</div>
                    <div class="empty-desc">Silakan daftar ke praktikum yang tersedia untuk dapat upload laporan</div>
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

        function showModulInfo() {
            const select = document.getElementById('modul-select');
            const modulInfo = document.getElementById('modul-info');
            const uploadForm = document.getElementById('upload-form');
            const selectedOption = select.options[select.selectedIndex];
            
            if (select.value) {
                const title = selectedOption.getAttribute('data-title');
                const description = selectedOption.getAttribute('data-description');
                const praktikum = selectedOption.getAttribute('data-praktikum');
                const uploaded = selectedOption.getAttribute('data-uploaded');
                const nilai = selectedOption.getAttribute('data-nilai');
                
                document.getElementById('modul-title').textContent = title;
                document.getElementById('modul-description').textContent = description;
                document.getElementById('selected-modul-id').value = select.value;
                
                let statusHtml = '';
                if (uploaded == '1') {
                    if (nilai !== 'null') {
                        statusHtml = '<span class="status-badge status-graded"><i class="fas fa-star"></i> Nilai: ' + nilai + '</span>';
                    } else {
                        statusHtml = '<span class="status-badge status-pending"><i class="fas fa-clock"></i> Menunggu Penilaian</span>';
                    }
                } else {
                    statusHtml = '<span class="status-badge status-uploaded"><i class="fas fa-upload"></i> Belum Upload</span>';
                }
                
                document.getElementById('modul-status').innerHTML = statusHtml;
                
                modulInfo.style.display = 'block';
                uploadForm.style.display = 'block';
            } else {
                modulInfo.style.display = 'none';
                uploadForm.style.display = 'none';
            }
        }
        
        function updateFileName() {
            const fileInput = document.getElementById('file-input');
            const selectedFile = document.getElementById('selected-file');
            const submitBtn = document.getElementById('submit-btn');
            
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                selectedFile.textContent = 'File terpilih: ' + file.name;
                selectedFile.style.display = 'block';
                submitBtn.disabled = false;
            } else {
                selectedFile.style.display = 'none';
                submitBtn.disabled = true;
            }
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