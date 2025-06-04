<!-- CSS untuk cetak -->
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

<!-- Header cetak -->
<div id="print-header" class="d-none d-print-block text-center mb-4">
    <h4 class="mb-0">Laporan Kehadiran Kajian</h4>
    <h5 class="mb-0">RS PKU Muhammadiyah Boja</h5>
    <?php if (!empty($tanggal)): ?>
        <p class="mb-2">Tanggal Scan: <?= date('d M Y', strtotime($tanggal)) ?></p>
    <?php endif; ?>
</div>

<!-- Form filter -->
<div class="no-print mb-4">
    <form method="get" class="row g-2 align-items-end">
        <div class="col-md-4 col-sm-6">
            <label for="idkajian" class="form-label fw-bold">Kajian:</label>
            <select name="idkajian" id="idkajian" class="form-control">
                <option value="">- Semua Kajian -</option>
                <?php foreach ($dataKajian as $kajian): ?>
                    <option value="<?= $kajian['idkajian'] ?>" <?= $idkajian == $kajian['idkajian'] ? 'selected' : '' ?>>
                        <?= esc($kajian['namakajian']) ?> (<?= $kajian['tanggal'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 col-sm-6">
            <label for="tanggal" class="form-label fw-bold">Tanggal Scan:</label>
            <input type="date" name="tanggal" id="tanggal" value="<?= esc($tanggal) ?>" class="form-control">
        </div>
        <div class="col-md-2 col-sm-6">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Tabel -->
<div class="table-responsive">
    <table id="tabel" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Kajian</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Waktu Scan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dataKehadiran as $index => $row): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td><?= esc($row['namakajian']) ?></td>
                    <td><?= esc($row['nik']) ?></td>
                    <td><?= esc($row['nama']) ?></td>
                    <td class="text-center"><?= esc($row['waktu_scan']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Script DataTable -->
<script>
    $(document).ready(function() {
        $('#tabel').DataTable({
            pageLength: 50,
            responsive: true
        });
    });

    function printPage() {
        const table = $('#tabel').DataTable();
        table.page.len(-1).draw(); // tampilkan semua data
        setTimeout(() => {
            window.print();
            table.page.len(50).draw(); // kembali ke normal
        }, 500);
    }
</script>

<!-- Styling tambahan -->
<style>
    .fw-bold {
        font-weight: bold;
    }

    th, td {
        vertical-align: middle;
    }
</style>
