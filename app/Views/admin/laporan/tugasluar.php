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
    <h4 class="mb-0">Laporan Tugas Luar Pegawai</h4>
    <h5 class="mb-0">RS PKU Muhammadiyah Boja</h5>
    <p class="mb-2">Periode: <?= date('F Y', strtotime($periode . '-01')) ?></p>
</div>

<div class="no-print">
    <form action="<?= base_url('admin/laporan/tugasluar') ?>" method="get" class="mb-4">
        <label for="periode" class="form-label fw-bold">Pilih Periode (Bulan-Tahun):</label>
        <div class="input-group" style="max-width: 300px;">
            <input type="month" class="form-control" name="periode" id="periode" value="<?= esc($periode) ?>">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </form>
</div>

<?php if (empty($tugasluar)): ?>
    <div class="alert alert-warning text-center" role="alert">
        <strong>Data tugas luar tidak ditemukan untuk periode ini.</strong>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table id="tugasluarTable" class="table table-bordered table-striped">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Alasan</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($tugasluar as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($row['pegawai_nama']) ?></td>
                        <td><?= esc($row['jabatan']) ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tgltugasluar'])) ?></td>
                        <td><?= esc($row['waktu']) ?></td>
                        <td><?= esc($row['alasan']) ?></td>
                        <td><?= esc($row['lokasi']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tugasluarTable').DataTable({
            pageLength: 50,
            order: [[5, 'asc']],
            language: {
                emptyTable: "Tidak ada data tugas luar"
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
