<?= $this->extend('admin/layout/wrapper') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan Kinerja Pegawai Tahun <?= $tahun ?></h3>
                    <div class="card-tools">
                        <form method="get" class="form-inline">
                            <select name="tahun" class="form-control mr-2" onchange="this.form.submit()">
                                <?php for ($i = date('Y'); $i >= 2020; $i--): ?>
                                    <option value="<?= $i ?>" <?= $i == $tahun ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Debug Info -->
                    <div class="alert alert-info">
                        <h5>Debug Information:</h5>
                        <p>Trend Keterlambatan: <?= count($trendKeterlambatan) ?> records</p>
                        <p>Top Keterlambatan: <?= count($topKeterlambatan) ?> records</p>
                        <p>Trend Lembur: <?= count($trendLembur) ?> records</p>
                        <p>Top Lembur: <?= count($topLembur) ?> records</p>
                    </div>

                    <!-- Tren Keterlambatan -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4>Tren Keterlambatan Per Bulan</h4>
                            <?php if (!empty($trendKeterlambatan)): ?>
                                <canvas id="chartKeterlambatan" height="100"></canvas>
                                
                                <!-- Tabel Data untuk Verifikasi -->
                                <div class="mt-3">
                                    <h6>Data Tren Keterlambatan:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Bulan</th>
                                                    <th>Jumlah Pegawai</th>
                                                    <th>Total Detik</th>
                                                    <th>Rata-rata</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($trendKeterlambatan as $trend): ?>
                                                    <tr>
                                                        <td><?= $trend['nama_bulan'] ?></td>
                                                        <td><?= $trend['jumlah_pegawai'] ?></td>
                                                        <td><?= $trend['total_detik_terlambat'] ?></td>
                                                        <td><?= $trend['rata_rata_terlambat'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">Tidak ada data tren keterlambatan untuk tahun <?= $tahun ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Top 10 Keterlambatan -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4>Top 10 Karyawan Terlambat Terbanyak</h4>
                            <?php if (!empty($topKeterlambatan)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama</th>
                                                <th>Bagian</th>
                                                <th>Jumlah Terlambat</th>
                                                <th>Total Waktu</th>
                                                <th>Rata-rata</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; ?>
                                            <?php foreach ($topKeterlambatan as $pegawai): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $pegawai['pegawai_nama'] ?></td>
                                                    <td><?= $pegawai['bagian'] ?></td>
                                                    <td><?= $pegawai['jumlah_terlambat'] ?>x</td>
                                                    <td><?= $pegawai['total_terlambat_formatted'] ?></td>
                                                    <td><?= $pegawai['rata_terlambat_formatted'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">Tidak ada data top keterlambatan untuk tahun <?= $tahun ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tren Lembur -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4>Tren Lembur Per Bulan</h4>
                            <?php if (!empty($trendLembur)): ?>
                                <canvas id="chartLembur" height="100"></canvas>
                            <?php else: ?>
                                <div class="alert alert-warning">Tidak ada data tren lembur untuk tahun <?= $tahun ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Data Lainnya (sementara dihide dulu) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Data Lainnya</h5>
                                </div>
                                <div class="card-body">

                                         </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Tren Keterlambatan
<?php if (!empty($trendKeterlambatan)): ?>
const ctxKeterlambatan = document.getElementById('chartKeterlambatan').getContext('2d');
new Chart(ctxKeterlambatan, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($trendKeterlambatan, 'nama_bulan')) ?>,
        datasets: [{
            label: 'Jumlah Pegawai Terlambat',
            data: <?= json_encode(array_column($trendKeterlambatan, 'jumlah_pegawai')) ?>,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            yAxisID: 'y',
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Jumlah Pegawai'
                }
            }
        }
    }
});
<?php endif; ?>

// Chart Tren Lembur
<?php if (!empty($trendLembur)): ?>
const ctxLembur = document.getElementById('chartLembur').getContext('2d');
new Chart(ctxLembur, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($trendLembur, 'nama_bulan')) ?>,
        datasets: [{
            label: 'Total Jam Lembur',
            data: <?= json_encode(array_column($trendLembur, 'total_jam_lembur')) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Total Jam Lembur'
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<?= $this->endSection() ?>