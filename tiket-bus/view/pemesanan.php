<?php
ob_start();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemesanan Tiket - Go.Ket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <!-- Booking Header -->
    <section class="booking-header mt-5 pt-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-credit-card me-2"></i>Pemesanan Tiket
                    </h2>
                    <p class="mb-0">Lengkapi data penumpang untuk melanjutkan pemesanan</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: 75%"></div>
                    </div>
                    <small class="text-white">Langkah 3 dari 4</small>
                </div>
            </div>
        </div>
    </section>

    <!-- Step Indicator -->
    <section class="mt-4">
        <div class="container">
            <div class="step-indicator">
                <div class="step completed">
                    <div class="step-number">1</div>
                    <div class="step-text">Cari Tiket</div>
                </div>
                <div class="step completed">
                    <div class="step-number">2</div>
                    <div class="step-text">Pilih Kursi</div>
                </div>
                <div class="step active">
                    <div class="step-number">3</div>
                    <div class="step-text">Data Penumpang</div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-text">Pembayaran</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Form -->
    <section class="mt-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Trip Information -->
                    <div class="booking-card">
                        <div class="booking-header-card">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informasi Perjalanan
                            </h5>
                        </div>
                        <div class="passenger-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Bus:</strong> <span id="busName">Sinar Jaya</span></p>
                                    <p><strong>Rute:</strong> <span id="route">Jakarta - Bandung</span></p>
                                    <p><strong>Tanggal:</strong> <span id="date">25 Januari 2025</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Berangkat:</strong> <span id="departure">08:00</span></p>
                                    <p><strong>Tiba:</strong> <span id="arrival">14:00</span></p>
                                    <p><strong>Kursi:</strong> <span id="selectedSeats">1, 2, 3</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger Data -->
                    <div class="booking-card">
                        <div class="booking-header-card">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Data Penumpang
                            </h5>
                        </div>
                        <div class="passenger-form">
                            <form id="passengerForm">
                                <div id="passengerForms">
                                    <!-- Passenger forms will be generated here -->
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-pay">
                                        <i class="fas fa-credit-card me-2"></i>Lanjutkan ke Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Price Summary -->
                    <div class="summary-card">
                        <h5 class="mb-4">
                            <i class="fas fa-calculator me-2"></i>Ringkasan Pembayaran
                        </h5>
                        
                        <div class="price-item">
                            <span>Harga per kursi:</span>
                            <span>Rp <span id="pricePerSeat">150,000</span></span>
                        </div>
                        
                        <div class="price-item">
                            <span>Jumlah kursi:</span>
                            <span id="seatCount">3</span>
                        </div>
                        
                        <div class="price-item">
                            <span>Biaya admin:</span>
                            <span>Rp 5,000</span>
                        </div>
                        
                        <div class="price-item">
                            <span>Biaya asuransi:</span>
                            <span>Rp 10,000</span>
                        </div>
                        
                        <hr>
                        
                        <div class="total-price">
                            <div class="price-item">
                                <span>Total:</span>
                                <span>Rp <span id="totalPrice">465,000</span></span>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Kursi yang Dipilih:</h6>
                            <div id="seatBadges">
                                <!-- Seat badges will be shown here -->
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-light rounded">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Pembayaran dapat dilakukan melalui transfer bank, e-wallet, atau kartu kredit
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
        let selectedSeats = [];
        let busData = {};
        let pricePerSeat = 150000;

        document.addEventListener('DOMContentLoaded', function() {
            // Get data from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const busId = urlParams.get('busId');
            const seatsParam = urlParams.get('seats');
            
            if (seatsParam) {
                selectedSeats = seatsParam.split(',').map(seat => parseInt(seat));
            }
            
            // Load bus data (in real app, this would come from database)
            loadBusData(busId);
            
            // Generate passenger forms
            generatePassengerForms();
            
            // Update summary
            updateSummary();
        });

        function loadBusData(busId) {
            // Sample bus data
            busData = {
                id: busId,
                po: "Sinar Jaya",
                busType: "Executive",
                departure: "08:00",
                arrival: "14:00",
                route: "Jakarta - Bandung",
                date: "25 Januari 2025"
            };
            
            // Update display
            document.getElementById('busName').textContent = busData.po;
            document.getElementById('route').textContent = busData.route;
            document.getElementById('date').textContent = busData.date;
            document.getElementById('departure').textContent = busData.departure;
            document.getElementById('arrival').textContent = busData.arrival;
            document.getElementById('selectedSeats').textContent = selectedSeats.join(', ');
        }

        function generatePassengerForms() {
            const container = document.getElementById('passengerForms');
            container.innerHTML = '';
            
            selectedSeats.forEach((seat, index) => {
                const formHtml = `
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-user me-2"></i>Penumpang ${index + 1} - Kursi ${seat}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" name="passenger_${index}_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. KTP *</label>
                                    <input type="text" class="form-control" name="passenger_${index}_ktp" required maxlength="16">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. Telepon *</label>
                                    <input type="tel" class="form-control" name="passenger_${index}_phone" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="passenger_${index}_email">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Kelamin *</label>
                                    <select class="form-control" name="passenger_${index}_gender" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Usia *</label>
                                    <input type="number" class="form-control" name="passenger_${index}_age" required min="1" max="120">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                container.innerHTML += formHtml;
            });
        }

        function updateSummary() {
            const seatCount = selectedSeats.length;
            const adminFee = 5000;
            const insuranceFee = 10000;
            const subtotal = seatCount * pricePerSeat;
            const total = subtotal + adminFee + insuranceFee;
            
            document.getElementById('pricePerSeat').textContent = pricePerSeat.toLocaleString();
            document.getElementById('seatCount').textContent = seatCount;
            document.getElementById('totalPrice').textContent = total.toLocaleString();
            
            // Update seat badges
            const seatBadges = document.getElementById('seatBadges');
            seatBadges.innerHTML = selectedSeats.map(seat => 
                `<span class="seat-badge">Kursi ${seat}</span>`
            ).join('');
        }

        // Handle form submission
        document.getElementById('passengerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            const formData = new FormData(this);
            let isValid = true;
            let passengerData = [];
            
            for (let i = 0; i < selectedSeats.length; i++) {
                const passenger = {
                    seat: selectedSeats[i],
                    name: formData.get(`passenger_${i}_name`),
                    ktp: formData.get(`passenger_${i}_ktp`),
                    phone: formData.get(`passenger_${i}_phone`),
                    email: formData.get(`passenger_${i}_email`),
                    gender: formData.get(`passenger_${i}_gender`),
                    age: formData.get(`passenger_${i}_age`)
                };
                
                // Validate required fields
                if (!passenger.name || !passenger.ktp || !passenger.phone || !passenger.gender || !passenger.age) {
                    isValid = false;
                    break;
                }
                
                // Validate KTP length
                if (passenger.ktp.length !== 16) {
                    alert(`No. KTP Penumpang ${i + 1} harus 16 digit!`);
                    isValid = false;
                    break;
                }
                
                passengerData.push(passenger);
            }
            
            if (!isValid) {
                alert('Mohon lengkapi semua data yang diperlukan!');
                return;
            }
            
            // Store passenger data in session storage for payment page
            sessionStorage.setItem('passengerData', JSON.stringify(passengerData));
            
            // Calculate total price
            const seatCount = selectedSeats.length;
            const adminFee = 5000;
            const insuranceFee = 10000;
            const subtotal = seatCount * pricePerSeat;
            const total = subtotal + adminFee + insuranceFee;
            
            // Redirect to payment page with order data
            const paymentUrl = `pembayaran.php?bus=${encodeURIComponent(busData.po)}&rute=${encodeURIComponent(busData.route)}&tanggal=${encodeURIComponent(busData.date)}&jam=${encodeURIComponent(busData.departure)}&kursi=${selectedSeats.join(',')}&total=${total}`;
            window.location.href = paymentUrl;
        });
    </script>
<?php
$content = ob_get_clean();
include 'master.php';
?>
</body>

</html> 