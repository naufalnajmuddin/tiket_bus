<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulasi: ambil data pesanan dari GET/POST atau session
$order = [
    'bus' => $_GET['bus'] ?? 'Sinar Jaya',
    'rute' => $_GET['rute'] ?? 'Jakarta - Bandung',
    'tanggal' => $_GET['tanggal'] ?? date('Y-m-d'),
    'jam' => $_GET['jam'] ?? '08:00',
    'kursi' => $_GET['kursi'] ?? '1,2',
    'total' => $_GET['total'] ?? 300000,
];

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bayar'])) {
    // Simulasi: simpan tiket ke session user
    if (!isset($_SESSION['tiket_dibayar'])) {
        $_SESSION['tiket_dibayar'] = [];
    }
    $_SESSION['tiket_dibayar'][] = [
        'bus' => $order['bus'],
        'rute' => $order['rute'],
        'tanggal' => $order['tanggal'],
        'jam' => $order['jam'],
        'kursi' => $order['kursi'],
        'total' => $order['total'],
        'metode' => $_POST['metode'],
        'nama' => $_POST['nama'],
        'email' => $_POST['email'],
        'waktu_bayar' => date('Y-m-d H:i:s'),
    ];
    $success = true;
    // Notifikasi berhasil dan redirect ke dashboard user
    $_SESSION['success'] = 'Pembayaran berhasil! Tiket Anda sudah tersimpan di dashboard.';
    header('Location: user/dashboard.php');
    exit();
}

ob_start();
?>
<style>
.payment-card {
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.payment-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0;
}

.passenger-item {
    border-left: 4px solid #667eea;
    background: #f8f9fa;
}

.payment-summary {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

.payment-summary h5 {
    color: #667eea;
    margin-bottom: 15px;
}

.btn-pay {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-pay:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
}
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card payment-card">
                <div class="card-header payment-header">
                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Pembayaran Tiket</h4>
                </div>
                <div class="card-body">
                    <div class="payment-summary">
                        <h5><i class="fas fa-info-circle me-2"></i>Ringkasan Pesanan</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-transparent"><strong>Bus:</strong> <?= htmlspecialchars($order['bus']) ?></li>
                            <li class="list-group-item bg-transparent"><strong>Rute:</strong> <?= htmlspecialchars($order['rute']) ?></li>
                            <li class="list-group-item bg-transparent"><strong>Tanggal:</strong> <?= htmlspecialchars($order['tanggal']) ?>, <?= htmlspecialchars($order['jam']) ?></li>
                            <li class="list-group-item bg-transparent"><strong>Kursi:</strong> <?= htmlspecialchars($order['kursi']) ?></li>
                            <li class="list-group-item bg-transparent"><strong>Total Bayar:</strong> Rp <?= number_format($order['total'],0,',','.') ?></li>
                        </ul>
                    </div>
                    
                    <!-- Passenger Information Section -->
                    <div id="passengerInfo" class="mb-4" style="display: none;">
                        <div class="payment-summary">
                            <h5><i class="fas fa-users me-2"></i>Data Penumpang</h5>
                            <div id="passengerList" class="list-group list-group-flush">
                                <!-- Passenger data will be populated here -->
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="metode" class="form-label">Metode Pembayaran</label>
                            <select class="form-select" id="metode" name="metode" required>
                                <option value="">Pilih Metode</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="E-Wallet">E-Wallet</option>
                                <option value="Kartu Kredit">Kartu Kredit</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Pemesan</label>
                            <input type="text" class="form-control" id="nama" name="nama" required value="<?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>">
                        </div>
                        <button type="submit" name="bayar" class="btn btn-pay w-100">
                            <i class="fas fa-check-circle me-2"></i>Bayar Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'master.php'; 
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Retrieve passenger data from session storage
    const passengerData = sessionStorage.getItem('passengerData');
    
    if (passengerData) {
        try {
            const passengers = JSON.parse(passengerData);
            displayPassengerInfo(passengers);
        } catch (error) {
            console.error('Error parsing passenger data:', error);
        }
    }
});

function displayPassengerInfo(passengers) {
    const passengerInfo = document.getElementById('passengerInfo');
    const passengerList = document.getElementById('passengerList');
    
    if (passengers && passengers.length > 0) {
        passengerInfo.style.display = 'block';
        
        passengerList.innerHTML = passengers.map((passenger, index) => `
            <div class="list-group-item passenger-item bg-transparent">
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-primary">Penumpang ${index + 1} - Kursi ${passenger.seat}</strong><br>
                        <small class="text-muted">${passenger.name}</small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-id-card me-1"></i>KTP: ${passenger.ktp}<br>
                            <i class="fas fa-phone me-1"></i>Telp: ${passenger.phone}<br>
                            <i class="fas fa-user me-1"></i>${passenger.gender === 'L' ? 'Laki-laki' : 'Perempuan'}, ${passenger.age} tahun
                        </small>
                    </div>
                </div>
            </div>
        `).join('');
    }
}
</script>