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

<div id="print-header" class="d-none d-print-block text-center mb-4">
    <h4 class="mb-0">Laporan Rekap Presensi Dokter</h4>
    <h5 class="mb-0">RS PKU Muhammadiyah Boja</h5>
    <p class="mb-2">Periode: <?= date('F Y', strtotime($periode . '-01')) ?></p>
</div>

<div class="no-print">
    <form action="<?= base_url('admin/laporan/absensidokter') ?>" method="get" class="mb-4">
        <label for="periode" class="form-label fw-bold">Pilih Periode (Bulan-Tahun):</label>
        <div class="input-group" style="max-width: 300px;">
            <input type="month" class="form-control" name="periode" id="periode" value="<?= esc($periode) ?>">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </form>
</div>

<?php if (empty($rekapDokter)): ?>
    <div class="alert alert-warning text-center" role="alert">
        <strong>Data absensi dokter tidak ditemukan untuk periode ini.</strong>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table id="dokterTable" class="table table-bordered table-striped" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">NIK</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th style="text-align: center;">Jumlah Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($rekapDokter as $dokter): ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td style="text-align: center;"><?= esc($dokter['nik']) ?></td>
                        <td><?= esc($dokter['nama_lengkap']) ?></td>
                        <td><?= esc($dokter['jabatan']) ?></td>
                        <td style="text-align: center; font-weight: bold;"><?= $dokter['jumlah_kehadiran'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold;">
                    <td colspan="4" style="text-align: right;">Total:</td>
                    <td style="text-align: center;"><?= $totalKehadiran ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('#dokterTable').DataTable({
                pageLength: 50,
                ordering: true,
                order: [[2, 'asc']],
                language: {
                    emptyTable: "Tidak ada data dokter"
                }
            });
        }
    });
</script>

<style>
    .fw-bold {
        font-weight: bold;
    }
    .table-bordered th, .table-bordered td {
        border: 1px solid #ddd !important;
    }
</style>
