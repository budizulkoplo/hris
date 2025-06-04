<p>
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-default">
        <i class="fa fa-plus"></i> Tambah Baru
    </button>
</p>

<?= form_open_multipart(base_url('admin/user')); ?>
<?= csrf_field(); ?>
<div class="modal fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Baru</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- Nama Pengguna -->
                <div class="form-group row">
                    <label class="col-3">Nama Pengguna</label>
                    <div class="col-9">
                        <input type="text" name="nama" class="form-control" placeholder="Nama user" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group row">
                    <label class="col-3">Email</label>
                    <div class="col-9">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>

                <!-- Username -->
                <div class="form-group row">
                    <label class="col-3">Username</label>
                    <div class="col-9">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group row">
                    <label class="col-3">Password</label>
                    <div class="col-9">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <small class="text-danger">Minimal 5 karakter dan maksimal 32 karakter</small>
                    </div>
                </div>

                <!-- NIK -->
                <div class="form-group row">
                    <label class="col-3">NIK</label>
                    <div class="col-9">
                        <input type="text" name="nik" class="form-control" placeholder="Nomor Induk Kependudukan" required>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="form-group row">
                    <label class="col-3">Alamat</label>
                    <div class="col-9">
                        <input type="text" name="alamat" class="form-control" placeholder="Alamat" required>
                    </div>
                </div>

                <!-- No HP -->
                <div class="form-group row">
                    <label class="col-3">No HP</label>
                    <div class="col-9">
                        <input type="text" name="nohp" class="form-control" placeholder="Nomor HP" required>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="form-group row">
                    <label class="col-3">Keterangan</label>
                    <div class="col-9">
                        <textarea name="keterangan" class="form-control" placeholder="Keterangan"></textarea>
                    </div>
                </div>

                <!-- Level -->
                <div class="form-group row">
                    <label class="col-3">Level</label>
                    <div class="col-9">
                        <select name="akses_level" class="form-control">
                            <?php foreach ($levels as $level): ?>
                                <option value="<?= esc($level->name) ?>">
                                    <?= esc($level->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Upload Gambar -->
                <div class="form-group row">
                    <label class="col-3">Foto Profil</label>
                    <div class="col-9">
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= form_close(); ?>