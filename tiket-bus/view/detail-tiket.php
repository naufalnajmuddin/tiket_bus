<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);

require_once '../config/database.php';
require_once '../controller/BusController.php';
$busController = new BusController($pdo);

$jadwalId = $_GET['id'] ?? null;
$busData = null;
if ($jadwalId) {
    $busData = $busController->getBusScheduleById($jadwalId);
    if (!$busData) {
        echo "<script>alert('Data bus tidak ditemukan!');window.location.href='cari-tiket.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Data bus tidak ditemukan!');window.location.href='cari-tiket.php';</script>";
    exit;
}
ob_start();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Tiket - Go.Ket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <!-- Bus Detail -->
    <section class="mt-5 pt-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Bus Information -->
                    <div class="bus-detail-card">
                        <div class="bus-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-2">
                                        <i class="fas fa-bus me-2"></i>
                                        <span id="busName"><?= htmlspecialchars($busData['nama_po']) ?></span>
                                    </h3>
                                    <p class="mb-0">
                                        <i class="fas fa-tag me-2"></i>
                                        <span id="busType"><?= htmlspecialchars($busData['jenis_bus']) ?></span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h4 class="mb-0">Rp <span id="busPrice"><?= number_format($busData['harga'], 0, ',', '.') ?></span></h4>
                                    <small>per kursi</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-route me-2"></i>Rute Perjalanan</h5>
                                    <p class="mb-2">
                                        <strong>Dari:</strong> <span id="origin"><?= htmlspecialchars($busData['kota_asal']) ?></span><br>
                                        <strong>Ke:</strong> <span id="destination"><?= htmlspecialchars($busData['kota_tujuan']) ?></span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Tanggal:</strong> <span id="date"><?= date('d F Y', strtotime($busData['tanggal_berangkat'])) ?></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-clock me-2"></i>Jadwal</h5>
                                    <p class="mb-2">
                                        <strong>Berangkat:</strong> <span id="departure"><?= date('H:i', strtotime($busData['jam_berangkat'])) ?></span><br>
                                        <strong>Tiba:</strong> <span id="arrival"><?= date('H:i', strtotime($busData['jam_tiba'])) ?></span>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Durasi:</strong> <span id="duration"><?= $busController->calculateDuration($busData['estimasi_waktu']) ?></span>
                                    </p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-12">
                                    <h5><i class="fas fa-cogs me-2"></i>Fasilitas</h5>
                                    <div id="facilities">
                                        <?php foreach ($busController->getFacilitiesArray($busData['fasilitas']) as $facility): ?>
                                            <span class="badge bg-primary me-2"><?= htmlspecialchars($facility) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seat Map -->
                    <div class="seat-map">
                        <h4 class="text-center mb-4">
                            <i class="fas fa-chair me-2"></i>Pilih Kursi Anda
                        </h4>
                        <!-- TODO: Implement seat map from database (kursi table) -->
                        <div class="text-center mb-4">
                            <div class="seat driver">D</div>
                            <div class="seat driver">R</div>
                            <div class="seat driver">I</div>
                            <div class="seat driver">V</div>
                            <div class="seat driver">E</div>
                            <div class="seat driver">R</div>
                        </div>
                        <div class="text-center" id="seatMap">
                            <!-- Seat map will be generated here -->
                        </div>
                        <div class="text-center mt-4">
                            <h6>Keterangan:</h6>
                            <div class="d-flex justify-content-center flex-wrap">
                                <div class="legend-item">
                                    <div class="seat legend available"></div>
                                    <span class="legend-text">Tersedia</span>
                                </div>
                                <div class="legend-item">
                                    <div class="seat legend selected"></div>
                                    <span class="legend-text">Dipilih</span>
                                </div>
                                <div class="legend-item">
                                    <div class="seat legend occupied"></div>
                                    <span class="legend-text">Terisi</span>
                                </div>
                                <div class="legend-item">
                                    <div class="seat legend driver"></div>
                                    <span class="legend-text">Driver</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Selected Seats -->
                    <div id="selectedSeatsSection" class="selected-seats d-none">
                        <h5><i class="fas fa-check-circle me-2"></i>Kursi yang Dipilih</h5>
                        <div id="selectedSeatsList">
                            <!-- Selected seats will be shown here -->
                        </div>
                    </div>
                    
                    <!-- Price Summary -->
                    <div class="price-summary">
                        <h5><i class="fas fa-calculator me-2"></i>Ringkasan Harga</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Harga per kursi:</span>
                            <span>Rp <span id="pricePerSeat"><?= number_format($busData['harga'], 0, ',', '.') ?></span></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah kursi:</span>
                            <span id="seatCount">0</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong>Rp <span id="totalPrice">0</span></strong>
                        </div>
                        
                        <button class="btn btn-book w-100" id="bookButton" disabled>
                            <i class="fas fa-credit-card me-2"></i>Lanjutkan Pemesanan
                        </button>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Anda akan diarahkan ke halaman login jika belum masuk
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <footer class="bg-dark mt-5">
        <div class="col-12" style="padding: 10px">
            <div style="text-align: center;">
                <a href="" class="text-light mr-1" title="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="" class="text-light mr-1" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="" class="text-light mr-1" title="Whatsapp"><i class="fab fa-whatsapp"></i></a>
            </div>

            <p class="text-center text-secondary mt-2">Copyright &copy; <i class="text-light">
                    Go.Ket
                </i> 2025</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>

    <script>
        // Data bus dari PHP
        const busData = <?php echo json_encode($busData); ?>;
        let selectedSeats = [];
        let currentBusId = <?= json_encode($jadwalId) ?>;

        // TODO: Ambil seatConfig dari database (kursi table) sesuai jadwalId
        // Untuk sementara, gunakan seatConfig statis
        const seatConfig = {
            rows: Math.ceil(busData.kapasitas / 5),
            seatsPerRow: 5,
            occupiedSeats: [], // Nanti diisi dari database
            driverSeats: []
        };

        document.addEventListener('DOMContentLoaded', function() {
            generateSeatMap();
        });

        function generateSeatMap() {
            const seatMap = document.getElementById('seatMap');
            seatMap.innerHTML = '';
            for (let row = 1; row <= seatConfig.rows; row++) {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'mb-2';
                for (let seat = 1; seat <= seatConfig.seatsPerRow; seat++) {
                    const seatNumber = (row - 1) * seatConfig.seatsPerRow + seat;
                    if (seatNumber > busData.kapasitas) break;
                    const seatDiv = document.createElement('div');
                    seatDiv.className = 'seat available';
                    seatDiv.textContent = seatNumber;
                    seatDiv.setAttribute('data-seat', seatNumber);
                    // Check if seat is occupied
                    if (seatConfig.occupiedSeats.includes(seatNumber)) {
                        seatDiv.className = 'seat occupied';
                        seatDiv.textContent = 'X';
                    } else {
                        seatDiv.addEventListener('click', () => toggleSeat(seatNumber));
                    }
                    rowDiv.appendChild(seatDiv);
                }
                seatMap.appendChild(rowDiv);
            }
        }

        function toggleSeat(seatNumber) {
            const seatElement = document.querySelector(`[data-seat="${seatNumber}"]`);
            const index = selectedSeats.indexOf(seatNumber);
            if (index > -1) {
                selectedSeats.splice(index, 1);
                seatElement.className = 'seat available';
            } else {
                selectedSeats.push(seatNumber);
                seatElement.className = 'seat selected';
            }
            updateSelectedSeats();
            updatePriceSummary();
        }

        function updateSelectedSeats() {
            const section = document.getElementById('selectedSeatsSection');
            const list = document.getElementById('selectedSeatsList');
            if (selectedSeats.length > 0) {
                section.classList.remove('d-none');
                list.innerHTML = selectedSeats.map(seat => 
                    `<span class="seat-number">Kursi ${seat}</span>`
                ).join('');
            } else {
                section.classList.add('d-none');
            }
        }

        function updatePriceSummary() {
            const seatCount = selectedSeats.length;
            const totalPrice = seatCount * busData.harga;
            document.getElementById('seatCount').textContent = seatCount;
            document.getElementById('totalPrice').textContent = totalPrice.toLocaleString();
            const bookButton = document.getElementById('bookButton');
            if (seatCount > 0) {
                bookButton.disabled = false;
            } else {
                bookButton.disabled = true;
            }
        }

        // Handle booking button click
        document.getElementById('bookButton').addEventListener('click', function() {
            if (selectedSeats.length === 0) {
                alert('Silakan pilih kursi terlebih dahulu!');
                return;
            }
            const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
            if (!isLoggedIn) {
                alert('Silakan login terlebih dahulu untuk melanjutkan pemesanan!');
                window.location.href = 'login.php';
                return;
            }
            // Redirect to booking page with selected data
            window.location.href = `pemesanan.php?id=${currentBusId}&seats=${selectedSeats.join(',')}`;
        });
    </script>
<?php
$content = ob_get_clean();
include 'master.php';
?>
</body>

</html> 