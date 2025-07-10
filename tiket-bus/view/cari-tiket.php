<?php
require_once '../controller/BusController.php';

// Initialize controller
$busController = new BusController($pdo);

// Get all cities for dropdown
$cities = $busController->getAllCities();

// Handle search request
$searchResults = [];
$searchPerformed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $kotaAsal = $_POST['kota_asal'] ?? '';
    $kotaTujuan = $_POST['kota_tujuan'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    
    if (!empty($kotaAsal) && !empty($kotaTujuan) && !empty($tanggal)) {
        $searchResults = $busController->searchBusSchedules($kotaAsal, $kotaTujuan, $tanggal);
        $searchPerformed = true;
    }
}

ob_start();
?>
    <!-- Search Form -->
    <section class="mt-5 pt-4">
        <div class="container">
            <div class="search-form">
                <h3 class="text-white mb-4 text-center">
                    <i class="fas fa-search me-2"></i>Cari Tiket Bus
                </h3>
                
                <form id="searchForm" method="POST">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="kotaAsal" class="form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>Kota Asal
                            </label>
                            <select class="form-control" id="kotaAsal" name="kota_asal" required>
                                <option value="">Pilih Kota Asal</option>
                                <?php foreach (
                                    $cities as $city): ?>
                                    <option value="<?= htmlspecialchars($city['nama']) ?>" 
                                            <?= (isset($_POST['kota_asal']) && $_POST['kota_asal'] === $city['nama']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($city['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="kotaTujuan" class="form-label">
                                <i class="fas fa-map-marker me-2"></i>Kota Tujuan
                            </label>
                            <select class="form-control" id="kotaTujuan" name="kota_tujuan" required>
                                <option value="">Pilih Kota Tujuan</option>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?= htmlspecialchars($city['nama']) ?>" 
                                            <?= (isset($_POST['kota_tujuan']) && $_POST['kota_tujuan'] === $city['nama']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($city['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label for="tanggalBerangkat" class="form-label">
                                <i class="fas fa-calendar me-2"></i>Tanggal
                            </label>
                            <input type="date" class="form-control" id="tanggalBerangkat" name="tanggal" 
                                   value="<?= isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label for="jumlahPenumpang" class="form-label">
                                <i class="fas fa-users me-2"></i>Penumpang
                            </label>
                            <select class="form-control" id="jumlahPenumpang" name="jumlah_penumpang">
                                <option value="1">1 Orang</option>
                                <option value="2">2 Orang</option>
                                <option value="3">3 Orang</option>
                                <option value="4">4 Orang</option>
                                <option value="5">5 Orang</option>
                                <option value="6">6+ Orang</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" name="search" class="btn btn-search w-100">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section id="filterSection" class="d-none">
        <div class="container">
            <div class="filter-section">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filter & Urutkan
                        </h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <select class="form-select" id="sortBy" style="width: auto; display: inline-block;">
                            <option value="time">Urutkan: Waktu Keberangkatan</option>
                            <option value="price-low">Harga: Rendah ke Tinggi</option>
                            <option value="price-high">Harga: Tinggi ke Rendah</option>
                            <option value="duration">Durasi Tercepat</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Results -->
    <?php if ($searchPerformed): ?>
        <section id="searchResults">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-4">
                            <i class="fas fa-bus me-2"></i>Hasil Pencarian
                            <span class="badge bg-primary ms-2"><?= count($searchResults) ?></span>
                        </h4>
                        <p class="text-muted">
                            <i class="fas fa-route me-2"></i>
                            <?= htmlspecialchars($_POST['kota_asal']) ?> → <?= htmlspecialchars($_POST['kota_tujuan']) ?> 
                            pada <?= date('d F Y', strtotime($_POST['tanggal'])) ?>
                        </p>
                    </div>
                </div>
                
                <?php if (empty($searchResults)): ?>
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h4>Tidak ada tiket yang tersedia</h4>
                        <p>Maaf, tidak ada bus yang tersedia untuk rute dan tanggal yang Anda pilih.</p>
                        <p>Coba ubah tanggal keberangkatan atau pilih rute lain.</p>
                    </div>
                <?php else: ?>
                    <div id="busList">
                        <?php foreach ($searchResults as $bus): ?>
                            <div class="bus-card">
                                <div class="bus-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5 class="mb-1"><?= htmlspecialchars($bus['nama_po']) ?></h5>
                                            <span class="badge bg-primary"><?= htmlspecialchars($bus['jenis_bus']) ?></span>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <div class="price">Rp <?= number_format($bus['harga'], 0, ',', '.') ?></div>
                                            <small class="text-muted">per orang</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bus-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <div class="time-info">
                                                <i class="fas fa-clock text-primary me-2"></i>
                                                <?= date('H:i', strtotime($bus['jam_berangkat'])) ?> - 
                                                <?= date('H:i', strtotime($bus['jam_tiba'])) ?>
                                            </div>
                                            <div class="route-info">
                                                <i class="fas fa-route text-muted me-2"></i>
                                                <?= htmlspecialchars($bus['kota_asal']) ?> → <?= htmlspecialchars($bus['kota_tujuan']) ?>
                                            </div>
                                            <div class="text-muted">
                                                <i class="fas fa-hourglass-half me-2"></i>
                                                Durasi: <?= $busController->calculateDuration($bus['estimasi_waktu']) ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="seat-available">
                                                <i class="fas fa-chair me-2"></i><?= $bus['kursi_tersedia'] ?> kursi tersedia
                                            </div>
                                            <small class="text-muted">dari <?= $bus['kapasitas'] ?> total kursi</small>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <strong>Fasilitas:</strong>
                                            </div>
                                            <?php 
                                            $facilities = $busController->getFacilitiesArray($bus['fasilitas']);
                                            foreach ($facilities as $facility): 
                                            ?>
                                                <span class="facility-badge"><?= htmlspecialchars($facility) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="col-md-2 text-end">
                                            <a href="detail-tiket.php?id=<?= $bus['jadwal_id'] ?>" class="btn btn-detail">
                                                <i class="fas fa-eye me-2"></i>Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <script>
        // Set minimum date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('tanggalBerangkat');
            if (dateInput && !dateInput.value) {
                dateInput.min = today;
                dateInput.value = today;
            }
        });

        // Form validation
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            const kotaAsal = document.getElementById('kotaAsal').value;
            const kotaTujuan = document.getElementById('kotaTujuan').value;
            const tanggal = document.getElementById('tanggalBerangkat').value;
            
            if (!kotaAsal || !kotaTujuan || !tanggal) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang diperlukan!');
                return;
            }
            
            if (kotaAsal === kotaTujuan) {
                e.preventDefault();
                alert('Kota asal dan tujuan tidak boleh sama!');
                return;
            }
        });
    </script>
<?php
$content = ob_get_clean();
include 'master.php'; 