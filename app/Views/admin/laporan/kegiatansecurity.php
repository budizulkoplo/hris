<div class="container mt-4">
    <form method="get" class="mb-4">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Tanggal</label>
            <div class="col-sm-4">
                <input type="date" name="tanggal" class="form-control" value="<?= esc($tanggal) ?>">
            </div>
            <div class="col-sm-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <?php if (!empty($kegiatanPerPegawai)): ?>
        <?php foreach ($kegiatanPerPegawai as $pegawai): ?>
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <strong><?= esc($pegawai['pegawai_nama']) ?></strong> 
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Jam</th>
                                <th>Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pegawai['kegiatan'] as $k): ?>
                                <tr>
                                    <td><?= esc($k['jam']) ?></td>
                                    <td><?= esc($k['kegiatan']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        <div class="alert alert-warning">Tidak ada data kegiatan pada tanggal ini.</div>
    <?php endif; ?>
</div>
