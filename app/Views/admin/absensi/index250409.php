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
                    <th rowspan="2" class="text-center">Kelompok</th>
                    <th rowspan="2" class="text-center">Nama Pegawai</th>
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
                        <td><?= esc($row['bagian']); ?></td>
                        <td nowrap><?= esc($row['pegawai_nama']); ?></td>

                        <?php foreach (array_keys($row) as $key): ?>
                            <?php if (is_numeric($key)): ?>
                                <td class="text-center">
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
Keterangan:<br>
C: Cuti <br>
TL: Tugas Luar <br>
<span style='color:red;font-weight:bold'>Jam Merah</span>: Terlambat

<script>
    $(document).ready(function() {
        $('#absensiTable').DataTable({
            "scrollX": true,
            "pageLength": 50
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
