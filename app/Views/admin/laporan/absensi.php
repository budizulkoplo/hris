
<!-- header laporan -->
<style>
@media print {
    .no-print {
        display: none !important;
    }

    #print-header {
        display: block !important;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    table th, table td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
    }

    tr {
        page-break-inside: avoid;
    }
}

</style>

<!-- header laporan -->
<div class="no-print">

<form action="<?= base_url('admin/laporan/absensi') ?>" method="get" class="mb-4">
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

<!-- Form tersembunyi untuk export -->
<form id="exportPayrollForm" action="<?= base_url('admin/laporan/exportpayroll') ?>" method="post" style="display: none;">
    <input type="hidden" name="bulanTahun" value="<?= $bulanTahun ?>">
</form>

<!-- Spinner -->
<div id="loadingSpinner" style="display: none; text-align: center;" class="mt-3">
    <div class="spinner-border text-warning" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p>Memproses export payroll...</p>
</div>

<?php if (empty($summaryPegawai)): ?>
    <div class="alert alert-warning text-center" role="alert">
        <strong>Data tidak tersedia untuk bulan yang dipilih</strong>
    </div>
<?php else: ?>
    <div class="summary-container mb-4">
        <div class="alert alert-info">
            <strong>Periode Laporan:</strong> <?= date('d F Y', strtotime($tanggalAwal)) ?> - <?= date('d F Y', strtotime($tanggalAkhir)) ?>
        </div>
    </div>
</div>
    <div class="table-responsive">
    <table id="tabel" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th>Bagian</th>
            <th>Nama Pegawai</th>
            <th class="text-center">Jml Absensi</th>
            <th class="text-center">Total Terlambat</th>
            <th class="text-center">Lembur</th>
            <th class="text-center">Konversi Lembur</th>
            <th class="text-center">Double Shift</th>
            <th class="text-center">Cuti</th>
            <th class="text-center">Tugas Luar</th>
            <th class="text-center">Total Hari Kerja</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($summaryPegawai as $pegawai): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= esc($pegawai['bagian']) ?></td>
                <td><?= esc($pegawai['pegawai_nama']) ?></td>
                <td class="text-center"><?= $pegawai['total_hari_kerja'] ?></td>
                <td class="text-center <?= $pegawai['total_terlambat'] > 0 ? 'text-danger fw-bold' : '' ?>">
                    <?= $pegawai['total_terlambat_formatted'] ?>
                </td>
                <td class="text-center <?= $pegawai['total_lembur'] > 0 ? 'text-success fw-bold' : '' ?>">
                    <?= $pegawai['real_lembur_bulan_ini'] ?>
                </td>
                <td class="text-center">
                    <?= floor($pegawai['konversilembur']) ?>
                </td>
                <td class="text-center"><?= $pegawai['doubleshift'] ?></td>
                <td class="text-center"><?= $pegawai['total_cuti'] ?></td>
                <td class="text-center"><?= $pegawai['total_tugas_luar'] ?></td>
                <td class="text-center">
                    <?= $pegawai['total_hari_kerja'] + $pegawai['total_tugas_luar'] + $pegawai['doubleshift']+$pegawai['total_cuti'] + floor($pegawai['konversilembur']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    </div>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $('#tabel').DataTable({
            pageLength: 100
        });
    });

    document.getElementById('exportExcel').addEventListener('click', function () {
        const bulanTahun = document.getElementById('bulanTahun').value;
        if (bulanTahun) {
            window.location.href = '<?= base_url('admin/laporan/rekapexport_excel?bulanTahun=') ?>' + encodeURIComponent(bulanTahun);
        } else {
            alert('Silakan pilih bulan terlebih dahulu.');
        }
    });

    function printPage() {
        // Disable paging sementara
        var table = $('#myTable').DataTable();
        table.page.len(-1).draw(); // -1 = semua data ditampilkan

        // Tunggu render selesai sebelum print
        setTimeout(function() {
            window.print();

            // Kembalikan paging seperti semula
            table.page.len(10).draw(); // ganti dengan pageLength awal kamu
        }, 500); // jeda sebentar supaya DataTables selesai render
    }

    function ExporttoPayroll() {
        // Tampilkan spinner
        document.getElementById('loadingSpinner').style.display = 'block';

        // Kirim form
        document.getElementById('exportPayrollForm').submit();
    }

</script>

<style>

    .fw-bold {
            font-weight: bold;
        }

    .summary-container {
        margin-bottom: 20px;
    }
    
    #absensiTable th {
        white-space: nowrap;
        vertical-align: middle;
    }
    
    #absensiTable td {
        vertical-align: middle;
    }
    
    
    
    .table-active {
        background-color: rgba(0, 0, 0, 0.05) !important;
    }
</style>