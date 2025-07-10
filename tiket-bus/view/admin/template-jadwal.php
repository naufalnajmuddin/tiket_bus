<?php
require_once '../../config/database.php';
require_once '../../controller/BusController.php';

// Check if user is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$busController = new BusController($pdo);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_template':
                $ruteId = $_POST['rute_id'];
                $perusahaanId = $_POST['perusahaan_id'];
                $jamBerangkat = $_POST['jam_berangkat'];
                $harga = $_POST['harga'];
                $hariOperasi = implode(',', $_POST['hari_operasi'] ?? ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
                
                $sql = "INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) 
                        VALUES (:rute_id, :perusahaan_id, :jam_berangkat, :harga, :hari_operasi)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':rute_id' => $ruteId,
                    ':perusahaan_id' => $perusahaanId,
                    ':jam_berangkat' => $jamBerangkat,
                    ':harga' => $harga,
                    ':hari_operasi' => $hariOperasi
                ]);
                
                $message = "Template jadwal berhasil ditambahkan";
                break;
                
            case 'update_template':
                $templateId = $_POST['template_id'];
                $jamBerangkat = $_POST['jam_berangkat'];
                $harga = $_POST['harga'];
                $hariOperasi = implode(',', $_POST['hari_operasi'] ?? ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
                $status = $_POST['status'];
                
                $sql = "UPDATE template_jadwal 
                        SET jam_berangkat = :jam_berangkat, harga = :harga, hari_operasi = :hari_operasi, status = :status 
                        WHERE id = :template_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':jam_berangkat' => $jamBerangkat,
                    ':harga' => $harga,
                    ':hari_operasi' => $hariOperasi,
                    ':status' => $status,
                    ':template_id' => $templateId
                ]);
                
                $message = "Template jadwal berhasil diperbarui";
                break;
                
            case 'delete_template':
                $templateId = $_POST['template_id'];
                
                $sql = "DELETE FROM template_jadwal WHERE id = :template_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':template_id' => $templateId]);
                
                $message = "Template jadwal berhasil dihapus";
                break;
        }
    }
}

// Get all routes
$routes = $pdo->query("SELECT r.*, k1.nama as kota_asal, k2.nama as kota_tujuan 
                       FROM rute r 
                       JOIN kota k1 ON r.kota_asal_id = k1.id 
                       JOIN kota k2 ON r.kota_tujuan_id = k2.id 
                       WHERE r.status = 'aktif' 
                       ORDER BY k1.nama, k2.nama")->fetchAll();

// Get all companies
$companies = $pdo->query("SELECT * FROM perusahaan_otobus WHERE status = 'aktif' ORDER BY nama")->fetchAll();

// Get all template schedules
$templates = $pdo->query("SELECT t.*, r.id as rute_id, k1.nama as kota_asal, k2.nama as kota_tujuan, 
                                 po.nama as nama_perusahaan
                          FROM template_jadwal t
                          JOIN rute r ON t.rute_id = r.id
                          JOIN kota k1 ON r.kota_asal_id = k1.id
                          JOIN kota k2 ON r.kota_tujuan_id = k2.id
                          JOIN perusahaan_otobus po ON t.perusahaan_id = po.id
                          ORDER BY k1.nama, k2.nama, t.jam_berangkat")->fetchAll();

ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i>Kelola Template Jadwal
                    </h4>
                    <p class="card-text">Template jadwal digunakan untuk menghasilkan jadwal bus otomatis setiap hari.</p>
                </div>
                <div class="card-body">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Add Template Form -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-plus me-2"></i>Tambah Template Jadwal</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="add_template">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="rute_id" class="form-label">Rute</label>
                                                <select class="form-select" name="rute_id" required>
                                                    <option value="">Pilih Rute</option>
                                                    <?php foreach ($routes as $route): ?>
                                                        <option value="<?= $route['id'] ?>">
                                                            <?= htmlspecialchars($route['kota_asal']) ?> → <?= htmlspecialchars($route['kota_tujuan']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="perusahaan_id" class="form-label">Perusahaan</label>
                                                <select class="form-select" name="perusahaan_id" required>
                                                    <option value="">Pilih Perusahaan</option>
                                                    <?php foreach ($companies as $company): ?>
                                                        <option value="<?= $company['id'] ?>">
                                                            <?= htmlspecialchars($company['nama']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="jam_berangkat" class="form-label">Jam Berangkat</label>
                                                <input type="time" class="form-control" name="jam_berangkat" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="harga" class="form-label">Harga</label>
                                                <input type="number" class="form-control" name="harga" min="0" step="1000" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Hari Operasi</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hari_operasi[]" value="senin" checked>
                                                    <label class="form-check-label">Senin</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hari_operasi[]" value="selasa" checked>
                                                    <label class="form-check-label">Selasa</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hari_operasi[]" value="rabu" checked>
                                                    <label class="form-check-label">Rabu</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hari_operasi[]" value="kamis" checked>
                                                    <label class="form-check-label">Kamis</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hari_operasi[]" value="jumat" checked>
                                                    <label class="form-check-label">Jumat</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hari_operasi[]" value="sabtu" checked>
                                                    <label class="form-check-label">Sabtu</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hari_operasi[]" value="minggu" checked>
                                                    <label class="form-check-label">Minggu</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Tambah Template
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Template List -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-list me-2"></i>Daftar Template Jadwal</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Rute</th>
                                                    <th>Perusahaan</th>
                                                    <th>Jam Berangkat</th>
                                                    <th>Harga</th>
                                                    <th>Hari Operasi</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($templates as $template): ?>
                                                    <tr>
                                                        <td>
                                                            <?= htmlspecialchars($template['kota_asal']) ?> → 
                                                            <?= htmlspecialchars($template['kota_tujuan']) ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($template['nama_perusahaan']) ?></td>
                                                        <td><?= htmlspecialchars($template['jam_berangkat']) ?></td>
                                                        <td>Rp <?= number_format($template['harga'], 0, ',', '.') ?></td>
                                                        <td>
                                                            <?php 
                                                            $hariOperasi = explode(',', $template['hari_operasi']);
                                                            $hariLabels = [
                                                                'senin' => 'Sen', 'selasa' => 'Sel', 'rabu' => 'Rab',
                                                                'kamis' => 'Kam', 'jumat' => 'Jum', 'sabtu' => 'Sab', 'minggu' => 'Min'
                                                            ];
                                                            $displayHari = array_map(function($hari) use ($hariLabels) {
                                                                return $hariLabels[$hari] ?? $hari;
                                                            }, $hariOperasi);
                                                            echo implode(', ', $displayHari);
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?= $template['status'] === 'aktif' ? 'success' : 'danger' ?>">
                                                                <?= ucfirst($template['status']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-primary" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editModal<?= $template['id'] ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" 
                                                                    onclick="deleteTemplate(<?= $template['id'] ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    
                                                    <!-- Edit Modal -->
                                                    <div class="modal fade" id="editModal<?= $template['id'] ?>" tabindex="-1">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit Template Jadwal</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <form method="POST">
                                                                    <input type="hidden" name="action" value="update_template">
                                                                    <input type="hidden" name="template_id" value="<?= $template['id'] ?>">
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <label for="jam_berangkat_<?= $template['id'] ?>" class="form-label">Jam Berangkat</label>
                                                                                <input type="time" class="form-control" name="jam_berangkat" 
                                                                                       value="<?= $template['jam_berangkat'] ?>" required>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label for="harga_<?= $template['id'] ?>" class="form-label">Harga</label>
                                                                                <input type="number" class="form-control" name="harga" 
                                                                                       value="<?= $template['harga'] ?>" min="0" step="1000" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-3">
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Hari Operasi</label>
                                                                                <?php 
                                                                                $hariOperasi = explode(',', $template['hari_operasi']);
                                                                                $hariOptions = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
                                                                                $hariLabels = [
                                                                                    'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu',
                                                                                    'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'
                                                                                ];
                                                                                ?>
                                                                                <?php foreach ($hariOptions as $hari): ?>
                                                                                    <div class="form-check">
                                                                                        <input class="form-check-input" type="checkbox" name="hari_operasi[]" 
                                                                                               value="<?= $hari ?>" <?= in_array($hari, $hariOperasi) ? 'checked' : '' ?>>
                                                                                        <label class="form-check-label"><?= $hariLabels[$hari] ?></label>
                                                                                    </div>
                                                                                <?php endforeach; ?>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label for="status_<?= $template['id'] ?>" class="form-label">Status</label>
                                                                                <select class="form-select" name="status">
                                                                                    <option value="aktif" <?= $template['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                                                                    <option value="nonaktif" <?= $template['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteTemplate(templateId) {
    if (confirm('Apakah Anda yakin ingin menghapus template jadwal ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_template">
            <input type="hidden" name="template_id" value="${templateId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();
include '../master.php';
?> 