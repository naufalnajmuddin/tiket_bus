<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'].'/tiket-bus/config/database.php';
// Hapus AuthController dan proteksi admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get recent bookings
$stmt = $pdo->prepare("
    SELECT p.*, j.tanggal_berangkat, j.jam_berangkat, j.harga,
           k1.nama as kota_asal, k2.nama as kota_tujuan
    FROM pemesanan p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN rute r ON j.rute_id = r.id
    JOIN kota k1 ON r.kota_asal_id = k1.id
    JOIN kota k2 ON r.kota_tujuan_id = k2.id
    WHERE p.user_id = ?
    ORDER BY p.tanggal_pemesanan DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_bookings = $stmt->fetchAll();

// Ambil tiket yang sudah dibayar dari session
$tiket_dibayar = isset($_SESSION['tiket_dibayar']) ? $_SESSION['tiket_dibayar'] : [];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard User - Go.Ket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white fw-bold">Go.Ket</h4>
                        <p class="text-white-50 small">User Dashboard</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../cari-tiket.php">
                                <i class="fas fa-search me-2"></i>
                                Cari Tiket
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pemesanan.php">
                                <i class="fas fa-ticket-alt me-2"></i>
                                Pemesanan Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user me-2"></i>
                                Profile
                            </a>
                        </li>

                        <li class="nav-item mt-3">
                            <form action="../../controller/AuthController.php" method="POST" class="d-inline">
                                <input type="hidden" name="action" value="logout">
                                <button type="submit" class="nav-link border-0 bg-transparent text-white-50">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-user me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['nama']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Welcome Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="card-title mb-2">Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h4>
                                        <p class="card-text mb-0">Terakhir login: <?php echo date('d M Y H:i', $_SESSION['login_time']); ?></p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <i class="fas fa-bus fa-3x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-ticket-alt fa-2x text-primary mb-2"></i>
                                <h5 class="card-title">Total Pemesanan</h5>
                                <h3 class="text-primary"><?php echo count($recent_bookings); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">Pemesanan Aktif</h5>
                                <h3 class="text-warning"><?php echo count(array_filter($recent_bookings, function($b) { return $b['status'] === 'dibayar'; })); ?></h3>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Recent Bookings -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-ticket-alt me-2"></i>
                                    Pemesanan Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_bookings)): ?>
                                    <p class="text-muted text-center">Belum ada pemesanan</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Rute</th>
                                                    <th>Tanggal</th>
                                                    <th>Harga</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_bookings as $booking): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($booking['kode_pemesanan']); ?></td>
                                                        <td><?php echo htmlspecialchars($booking['kota_asal'] . ' → ' . $booking['kota_tujuan']); ?></td>
                                                        <td><?php echo date('d M Y', strtotime($booking['tanggal_berangkat'])); ?></td>
                                                        <td>Rp <?php echo number_format($booking['harga'], 0, ',', '.'); ?></td>
                                                        <td>
                                                            <?php
                                                            $status_class = '';
                                                            switch ($booking['status']) {
                                                                case 'pending': $status_class = 'warning'; break;
                                                                case 'dibayar': $status_class = 'success'; break;
                                                                case 'dibatalkan': $status_class = 'danger'; break;
                                                                case 'selesai': $status_class = 'info'; break;
                                                            }
                                                            ?>
                                                            <span class="badge bg-<?php echo $status_class; ?>">
                                                                <?php echo ucfirst($booking['status']); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tiket Sudah Dibayar -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Tiket Sudah Dibayar
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($tiket_dibayar)): ?>
                                    <p class="text-muted text-center">Belum ada tiket yang dibayar.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Bus</th>
                                                    <th>Rute</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam</th>
                                                    <th>Kursi</th>
                                                    <th>Total</th>
                                                    <th>Metode</th>
                                                    <th>Waktu Bayar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tiket_dibayar as $tiket): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($tiket['bus']) ?></td>
                                                        <td><?= htmlspecialchars($tiket['rute']) ?></td>
                                                        <td><?= htmlspecialchars($tiket['tanggal']) ?></td>
                                                        <td><?= htmlspecialchars($tiket['jam']) ?></td>
                                                        <td><?= htmlspecialchars($tiket['kursi']) ?></td>
                                                        <td>Rp <?= number_format($tiket['total'],0,',','.') ?></td>
                                                        <td><?= htmlspecialchars($tiket['metode']) ?></td>
                                                        <td><?= htmlspecialchars($tiket['waktu_bayar']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt me-2"></i>
                                    Aksi Cepat
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <a href="../cari-tiket.php" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-2"></i>
                                            Cari Tiket
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="pemesanan.php" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-list me-2"></i>
                                            Lihat Pemesanan
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="profile.php" class="btn btn-outline-secondary w-100">
                                            <i class="fas fa-user-edit me-2"></i>
                                            Edit Profile
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="login-history.php" class="btn btn-outline-info w-100">
                                            <i class="fas fa-history me-2"></i>
                                            Riwayat Login
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html> 