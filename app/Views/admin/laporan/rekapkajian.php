<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <form action="" method="get">
        <div class="input-group">
            <input type="month" name="periode" value="<?= esc($bulanTahun) ?>" class="form-control">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </form>
</div>

<div id="print-header" class="d-none d-print-block text-center mb-4">
    <h4 class="mb-0">Rekap Kajian Pegawai</h4>
    <h5 class="mb-0">RS PKU Muhammadiyah Boja</h5>
    <p class="mb-2">Periode: <?= date('F Y', strtotime($bulanTahun.'-01')) ?></p>
</div>

<div class="table-responsive">
    <table class="table table-bordered" id="example1">
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Ahad Pagi</th>
                <th>Senin Pagi</th>
                <th>Kajian Bulan <?= date('F Y', strtotime($bulanTahun.'-01')) ?></th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
    <?php $no=1; foreach($rekap as $r): ?>
    <tr>
        <td class="text-center"><?= $no++ ?></td>
        <td><?= esc($r['nik']) ?></td>
        <td><?= esc($r['nama']) ?></td>
        <td class="text-center"><?= $r['ahad_pagi'] ?></td>
        <td class="text-center"><?= $r['senin_pagi'] ?></td>
        <td class="text-center"><?= $r['kajian_bulanan'] ?></td>
        <td class="text-center fw-bold"><?= $r['total'] ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>

    </table>
</div>

<script>
$(function(){
    $('#rekapTable').DataTable({ pageLength: 100 });
});
</script>
