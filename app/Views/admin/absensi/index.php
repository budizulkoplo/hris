<style>
.blink {
  animation: blinker 1s linear infinite;
}
@keyframes blinker {
  50% { opacity: 0; }
}
</style>

<form action="<?= base_url('admin/absensi') ?>" method="get" class="mb-4">
    <div class="col-md-4">
        <div class="input-group">
            <label for="bulanTahun" class="input-group-text">Pilih Bulan:</label>
            <input type="month" class="form-control w-auto" name="bulanTahun" id="bulanTahun" value="<?= esc($bulanTahun); ?>">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </div>
</form>

<?php if (empty($dataAbsensi)): ?>
    <p>Data tidak tersedia.</p>
<?php else: ?>
    <div class="table-responsive">
        <table id="absensiTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th rowspan="2" class="text-center">Nama Pegawai</th>
                    <th rowspan="2" class="text-center">Kelompok</th>
                    <th colspan="<?= count(array_filter(array_keys($dataAbsensi[0]), 'is_numeric')); ?>" class="text-center">Tanggal</th>
                </tr>
                <tr>
                    <?php foreach (array_keys($dataAbsensi[0]) as $key): ?>
                        <?php if (is_numeric($key)): ?>
                            <th class="text-center"><?= $key; ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataAbsensi as $row): ?>
                    <tr>
                    <td nowrap><?= $row['pegawai_nama']; ?></td>

                        <td nowrap><?= esc($row['bagian']); ?></td>
                        <?php foreach (array_keys($row) as $key): ?>
                            <?php if (is_numeric($key)): ?>
                                <td class="text-center align-top" style="min-width: 130px;">
                                    <?= str_replace('<br>', '<br>', $row[$key]); ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<p class="mt-3">Keterangan:<br>
<span style='color:red;font-weight:bold'>Jam Merah</span>: Terlambat</p>
<span style='color:red;font-weight:bold'>Jadwal kedip merah</span>: Belum di input Jadwal</p>

<!-- DataTables CSS & JS -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

<script>
    $(document).ready(function () {
        var table = $('#absensiTable').DataTable({
            scrollX: true,
            pageLength: 50,
            fixedHeader: true,
            fixedColumns: {
                leftColumns: 1 // hanya freeze kolom "Kelompok"
            }
        });

        document.getElementById('exportExcel').addEventListener('click', function () {
            var bulanTahun = document.getElementById('bulanTahun').value;
            if (!bulanTahun) {
                alert('Pilih bulan terlebih dahulu!');
                return;
            }
            var url = "<?= base_url('admin/absensi/exportExcel') ?>?bulanTahun=" + bulanTahun;
            window.location.href = url;
        });
    });
</script>
