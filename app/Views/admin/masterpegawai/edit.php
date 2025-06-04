<form action="<?= base_url('admin/masterpegawai/update/' . $pegawai['pegawai_id']) ?>" method="post">
    <?= csrf_field() ?>

    <div class="form-group row">
        <label class="col-3">NIP</label>
        <div class="col-9">
            <input type="text" name="pegawai_nip" class="form-control" value="<?= esc($pegawai['pegawai_nip']) ?>">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">NIK</label>
        <div class="col-9">
            <input type="text" name="nik" class="form-control" value="<?= esc($pegawai['nik']) ?>">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">Nama Pegawai</label>
        <div class="col-9">
            <input type="text" name="pegawai_nama" class="form-control" value="<?= esc($pegawai['pegawai_nama']) ?>" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">Jabatan</label>
        <div class="col-9">
            <input type="text" name="jabatan" class="form-control" value="<?= esc($pegawai['jabatan']) ?>">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">No HP</label>
        <div class="col-9">
            <input type="text" name="nohp" class="form-control" value="<?= esc($pegawai['nohp']) ?>">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">Email</label>
        <div class="col-9">
            <input type="email" name="email" class="form-control" value="<?= esc($pegawai['email']) ?>">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">Alamat</label>
        <div class="col-9">
            <textarea name="alamat" class="form-control"><?= esc($pegawai['alamat']) ?></textarea>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-9 offset-3">
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
            <a href="<?= base_url('admin/masterpegawai') ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
