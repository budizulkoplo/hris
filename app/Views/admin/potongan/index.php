<form action="<?= base_url('admin/potongan') ?>" method="get" class="mb-4">
    <div class="col-md-4">
        <div class="input-group">
            <label for="bulanTahun" class="input-group-text">Pilih Bulan:</label>
            <input type="month" class="form-control" name="bulanTahun" id="bulanTahun" value="<?= esc($bulanTahun); ?>">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </div>
</form>

<?php if (empty($pegawai)): ?>
    <p>Data tidak tersedia.</p>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="potonganTable">
        <thead>
            <tr>
                <th>Pegawai PIN</th>
                <th>Nama</th>
                <th>Simpanan Wajib</th>
                <th>Simpanan Hari Raya</th>
                <th>Angsuran Koperasi</th>
                <th>Belanja TDM</th>
                <th>Arisan Darma Wanita</th>
                <th>Tabungan Darma Wanita</th>
                <th>Lain-lain</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pegawai as $p): 
            $row = array_filter($potongan, fn($r) => $r['pegawai_pin'] == $p['pegawai_pin']);
            $row = $row ? reset($row) : [];
        ?>
            <tr>
                <td><?= esc($p['pegawai_pin']); ?></td>
                <td><?= esc($p['pegawai_nama']); ?></td>
                <td contenteditable="true" data-field="simpanan_wajib" data-pin="<?= $p['pegawai_pin']; ?>"><?= $row['simpanan_wajib'] ?? ''; ?></td>
                <td contenteditable="true" data-field="simpanan_hari_raya" data-pin="<?= $p['pegawai_pin']; ?>"><?= $row['simpanan_hari_raya'] ?? ''; ?></td>
                <td contenteditable="true" data-field="angsuran_koperasi" data-pin="<?= $p['pegawai_pin']; ?>"><?= $row['angsuran_koperasi'] ?? ''; ?></td>
                <td contenteditable="true" data-field="belanja_koperasi_tdm" data-pin="<?= $p['pegawai_pin']; ?>"><?= $row['belanja_koperasi_tdm'] ?? ''; ?></td>
                <td contenteditable="true" data-field="arisan_darmawanita" data-pin="<?= $p['pegawai_pin']; ?>"><?= $row['arisan_darmawanita'] ?? ''; ?></td>
                <td contenteditable="true" data-field="tabungan_darmawanita" data-pin="<?= $p['pegawai_pin']; ?>"><?= $row['tabungan_darmawanita'] ?? ''; ?></td>
                <td contenteditable="true" data-field="lain_lain" data-pin="<?= $p['pegawai_pin']; ?>"><?= $row['lain_lain'] ?? ''; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
    $('#potonganTable td[contenteditable=true]').on('blur', function(){
        var pin = $(this).data('pin');
        var field = $(this).data('field');
        var value = $(this).text();
        var periode = $('#bulanTahun').val();

        $.post("<?= base_url('admin/potongan/save') ?>", {
            pegawai_pin: pin,
            periode: periode,
            field: field,
            value: value
        }, function(res){
            console.log(res);
        }, 'json');
    });
});
</script>
