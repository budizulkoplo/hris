<form action="<?= base_url('admin/masterpegawai/simpan') ?>" method="post">
    <?= csrf_field() ?>

    <div class="form-group row">
        <label class="col-3">NIP</label>
        <div class="col-9">
            <input type="text" name="pegawai_nip" class="form-control" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">NIK</label>
        <div class="col-9">
            <input type="text" name="nik" class="form-control" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">Nama Pegawai</label>
        <div class="col-9">
            <input type="text" name="pegawai_nama" class="form-control" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">Jabatan</label>
        <div class="col-9">
            <input type="text" name="jabatan" class="form-control">
        </div>
    </div>

    <!-- 🔹 Tambahan Field Bagian -->
    <div class="form-group row">
        <label class="col-3">Bagian</label>
        <div class="col-9">
            <input type="text" name="bagian" class="form-control">
        </div>
    </div>
    <!-- 🔹 Akhir Tambahan -->

    <div class="form-group row">
        <label class="col-3">No HP</label>
        <div class="col-9">
            <input type="text" name="nohp" class="form-control">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">Email</label>
        <div class="col-9">
            <input type="email" name="email" class="form-control">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-3">Alamat</label>
        <div class="col-9">
            <textarea name="alamat" class="form-control" rows="2"></textarea>
        </div>
    </div>

    <div class="form-group row mt-3">
        <div class="col-9 offset-3">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-save"></i> Simpan
            </button>
            <a href="<?= base_url('admin/masterpegawai') ?>" class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </div>
</form>
