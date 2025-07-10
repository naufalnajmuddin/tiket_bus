<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
require_once $_SERVER['DOCUMENT_ROOT'].'/tiket-bus/config/database.php';

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE role = 'user'");
$total_users = $stmt->fetch()['total_users'];

$stmt = $pdo->query("SELECT COUNT(*) as total_bookings FROM pemesanan");
$total_bookings = $stmt->fetch()['total_bookings'];

$stmt = $pdo->query("SELECT COUNT(*) as total_revenue FROM pemesanan WHERE status = 'dibayar'");
$total_revenue = $stmt->fetch()['total_revenue'];

$stmt = $pdo->query("SELECT COUNT(*) as total_buses FROM bus WHERE status = 'aktif'");
$total_buses = $stmt->fetch()['total_buses'];



// Get recent bookings
$stmt = $pdo->query("
    SELECT p.*, u.nama as user_nama, j.tanggal_berangkat, j.jam_berangkat,
           k1.nama as kota_asal, k2.nama as kota_tujuan
    FROM pemesanan p
    JOIN users u ON p.user_id = u.id
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN rute r ON j.rute_id = r.id
    JOIN kota k1 ON r.kota_asal_id = k1.id
    JOIN kota k2 ON r.kota_tujuan_id = k2.id
    ORDER BY p.tanggal_pemesanan DESC
    LIMIT 10
");
$recent_bookings = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Go.Ket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
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
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border: none;
            border-radius: 10px;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
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
                        <p class="text-white-50 small">Admin Dashboard</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>
                                Kelola User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="buses.php">
                                <i class="fas fa-bus me-2"></i>
                                Kelola Bus
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="routes.php">
                                <i class="fas fa-route me-2"></i>
                                Kelola Rute
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="schedules.php">
                                <i class="fas fa-calendar me-2"></i>
                                Kelola Jadwal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="template-jadwal.php">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Template Jadwal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="bookings.php">
                                <i class="fas fa-ticket-alt me-2"></i>
                                Kelola Pemesanan
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i>
                                Laporan
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
                    <h1 class="h2">Dashboard Admin</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-user-shield me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['nama']); ?> (Admin)
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
                                        <p class="card-text mb-0">Panel Administrasi Go.Ket - Terakhir login: <?php echo date('d M Y H:i', $_SESSION['login_time']); ?></p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <i class="fas fa-user-shield fa-3x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h5 class="card-title">Total Users</h5>
                                <h3 class="text-primary"><?php echo $total_users; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-ticket-alt fa-2x text-success mb-2"></i>
                                <h5 class="card-title">Total Pemesanan</h5>
                                <h3 class="text-success"><?php echo $total_bookings; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">Pendapatan</h5>
                                <h3 class="text-warning"><?php echo $total_revenue; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-bus fa-2x text-info mb-2"></i>
                                <h5 class="card-title">Bus Aktif</h5>
                                <h3 class="text-info"><?php echo $total_buses; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-ticket-alt me-2"></i>
                                    Aktivitas Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Rute</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_bookings as $booking): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($booking['user_nama']); ?></td>
                                                    <td><?php echo htmlspecialchars($booking['kota_asal'] . ' → ' . $booking['kota_tujuan']); ?></td>
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
                                                    <td><?php echo date('d/m', strtotime($booking['tanggal_berangkat'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
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
                                    <div class="col-md-2 mb-2">
                                        <a href="users.php" class="btn btn-primary w-100">
                                            <i class="fas fa-users me-2"></i>
                                            Kelola User
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <a href="buses.php" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-bus me-2"></i>
                                            Kelola Bus
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <a href="routes.php" class="btn btn-outline-success w-100">
                                            <i class="fas fa-route me-2"></i>
                                            Kelola Rute
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <a href="schedules.php" class="btn btn-outline-warning w-100">
                                            <i class="fas fa-calendar me-2"></i>
                                            Kelola Jadwal
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <a href="bookings.php" class="btn btn-outline-info w-100">
                                            <i class="fas fa-ticket-alt me-2"></i>
                                            Kelola Pemesanan
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