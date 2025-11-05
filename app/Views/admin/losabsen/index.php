<div class="card p-4">
    <div class="row">
    <!-- Kiri: Form Input -->
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="mb-4">Input Absen Manual (Lupa Absen)</h5>
            <form action="<?= base_url('admin/losabsen/simpan') ?>" method="post" id="formAbsenManual">
                <!-- Pilih Pegawai -->
                <div class="mb-3">
                    <label for="pin">Nama Pegawai</label>
                    <select name="pin" id="pin" class="form-control-sm select2" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php foreach ($pegawai as $row): ?>
                            <option value="<?= esc($row['pegawai_pin']) ?>">
                                <?= esc($row['pegawai_nama']) ?> - <?= esc($row['pegawai_pin']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tanggal & Jam Absen -->
                <div class="mb-3">
                    <label for="scan_date">Tanggal & Jam Absen</label>
                    <input type="datetime-local" name="scan_date" id="scan_date" class="form-control" required>
                </div>

                <!-- Mode Absen -->
                <div class="mb-3">
                    <label for="inoutmode">Tipe Absen</label>
                    <select name="inoutmode" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="1">Masuk Kerja</option>
                        <option value="2">Pulang Kerja</option>
                        <option value="5">Lembur Masuk</option>
                        <option value="6">Lembur Pulang</option>
                    </select>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('admin/masterpegawai') ?>" class="btn btn-secondary">Kembali</a>
            </form>
            
        </div>
    </div>

    <!-- Kanan: Log Absen -->
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="mb-3">Log Absen Pegawai</h5>
            <div class="mb-3">
                <label for="tanggal">Tanggal</label>
                <input type="date" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div id="logAbsenTable" class="table-responsive" style="max-height: 400px; overflow-y:auto;">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Shift</th>
                            <th>Check</th>
                        </tr>
                    </thead>
                    <tbody id="logAbsenBody">
                        <tr><td colspan="5" class="text-center">Pilih pegawai & tanggal</td></tr>
                    </tbody>
                </table>
                <p>*ditampilkan log H+1 untuk menampilkan jika ada data shift malam</p>
            </div>
        </div>
    </div>
</div>


    <hr>
<h5 class="mt-4">Riwayat Absen Manual</h5>
<form method="get" class="mb-4">
    <div class="row g-2">
        <div class="col-md-4 col-sm-6">
            <label for="bulanTahun" class="form-label fw-bold">Pilih Bulan:</label>
            <div class="input-group">
                <input type="month" class="form-control" name="bulanTahun" id="bulanTahun"
                    value="<?= esc($bulanTahun ?? date('Y-m')); ?>">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </div>
    </div>
</form>

<table id="riwayat" class="table table-sm table-bordered table-hover mt-2">
    <thead class="table-light">
    <tr>
        <th>#</th>
        <th>Waktu Absen</th>
        <th>Nama Pegawai</th>
        <th>Mode</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
    <?php if (empty($absen_manual)) : ?>
        <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
    <?php else : ?>
        <?php $no = 1; foreach ($absen_manual as $row): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('Y-m-d H:i:s', strtotime($row['scan_date'])) ?></td>
                <td>
                    <?= esc($map_nama[$row['pin']] ?? 'Tidak ditemukan') ?> 
                    <small class="text-muted">(<?= esc($row['pin']) ?>)</small>
                </td>
                <td>
                    <?php
                    switch ($row['inoutmode']) {
                        case 1: echo "Masuk Kerja"; break;
                        case 2: echo "Pulang Kerja"; break;
                        case 5: echo "Lembur Masuk"; break;
                        case 6: echo "Lembur Pulang"; break;
                        default: echo $row['inoutmode'];
                    }
                    ?>
                </td>
                <td>
                    <a href="<?= base_url('admin/losabsen/hapus/' . $row['att_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus data ini?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>

</table>

</div>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "-- Pilih Pegawai --",
            allowClear: true,
            width: '100%'
        });

        $('#riwayat').DataTable({
            pageLength: 100
        });
    });
    
</script>
<script>
$(document).ready(function () {
    function loadLogAbsen() {
        const pin = $('#pin').val();
        const tanggal = $('#tanggal').val();

        if (pin && tanggal) {
            $('#logAbsenBody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

            $.getJSON("<?= base_url('admin/losabsen/getLogAbsen') ?>", { pin, tanggal }, function (response) {
                if (response.success && response.data.length > 0) {
                    let html = '';
                    response.data.forEach((row, index) => {
                        const rowClass = !row.sn ? 'table-danger' : '';
                        let actionBtn = '';

                        // Jika shift = '-' tampilkan tombol hapus
                        if (row.shift === '-') {
                            actionBtn = `
                                <button class="btn btn-sm btn-danger btnHapusLog" 
                                    data-pin="${row.pin}" 
                                    data-scan_date="${row.scan_date}">
                                    Hapus
                                </button>`;
                        }

                        html += `
                            <tr class="${rowClass}">
                                <td>${index + 1}</td>
                                <td>${row.scan_date}</td>
                                <td>${row.statusabsen}</td>
                                <td>${row.shift}</td>
                                <td>${actionBtn}</td>
                            </tr>`;
                    });

                    $('#logAbsenBody').html(html);

                    // Event handler tombol hapus
                    $('.btnHapusLog').on('click', function () {
                        const pin = $(this).data('pin');
                        const scan_date = $(this).data('scan_date');

                        if (confirm(`Yakin hapus log?\nPIN: ${pin}\nTanggal: ${scan_date}`)) {
                            $.post("<?= base_url('admin/losabsen/deleteLog') ?>", 
                                { pin: pin, scan_date: scan_date }, 
                                function (res) {
                                    if (res.success) {
                                        alert('Data berhasil dihapus');
                                        loadLogAbsen(); // reload ulang
                                    } else {
                                        alert('Gagal hapus data');
                                    }
                                }, 
                            'json');
                        }
                    });

                } else {
                    $('#logAbsenBody').html('<tr><td colspan="5" class="text-center">Tidak ada log absensi.</td></tr>');
                }
            }).fail(function () {
                $('#logAbsenBody').html('<tr><td colspan="5" class="text-center text-danger">Gagal mengambil data.</td></tr>');
            });
        } else {
            $('#logAbsenBody').html('<tr><td colspan="5" class="text-center">Pilih pegawai & tanggal</td></tr>');
        }
    }

    $('#pin, #tanggal').change(loadLogAbsen);
});
</script>



