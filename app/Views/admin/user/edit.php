<?= form_open(base_url('admin/user/edit/' . $user['id_user'])); ?>
<?= csrf_field(); ?>

<div class="form-group row">
    <label class="col-3">Nama Pengguna</label>
    <div class="col-9">
        <input type="text" name="nama" class="form-control" placeholder="Nama user" value="<?= esc($user['nama']) ?>" required>
    </div>
</div>

<div class="form-group row">
    <label class="col-3">Email</label>
    <div class="col-9">
        <input type="email" name="email" class="form-control" placeholder="Email" value="<?= esc($user['email']) ?>" required>
    </div>
</div>

<div class="form-group row">
    <label class="col-3">Username</label>
    <div class="col-9">
        <input type="text" name="username" class="form-control" placeholder="Username" value="<?= esc($user['username']) ?>" readonly>
    </div>
</div>

<div class="form-group row">
    <label class="col-3">Password</label>
    <div class="col-9">
        <input type="password" name="password" class="form-control" placeholder="Password baru">
        <small class="text-danger">Minimal 6 karakter dan maksimal 32 karakter. Biarkan kosong jika tidak ingin mengubah password.</small>
    </div>
</div>

<div class="form-group row">
    <label class="col-3">NIK</label>
    <div class="col-9">
        <input type="text" name="nik" class="form-control" placeholder="Nomor Induk Kependudukan" value="<?= esc($user['nik']) ?>" required>
    </div>
</div>

<div class="form-group row">
    <label class="col-3">Alamat</label>
    <div class="col-9">
        <textarea name="alamat" class="form-control" placeholder="Alamat pengguna" required><?= esc($user['alamat']) ?></textarea>
    </div>
</div>

<div class="form-group row">
    <label class="col-3">No HP</label>
    <div class="col-9">
        <input type="text" name="nohp" class="form-control" placeholder="Nomor Handphone" value="<?= esc($user['nohp']) ?>" required>
    </div>
</div>

<div class="form-group row">
    <label class="col-3">Keterangan</label>
    <div class="col-9">
        <textarea name="keterangan" class="form-control" placeholder="Keterangan tambahan"><?= esc($user['keterangan']) ?></textarea>
    </div>
</div>

<div class="form-group row">
    <label class="col-3">Level</label>
    <div class="col-9">
        <select name="akses_level" class="form-control">
            <?php foreach ($levels as $level): ?>
                <option value="<?= esc($level->name) ?>" <?= $user['akses_level'] === $level->name ? 'selected' : '' ?>>
                    <?= esc($level->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-3"></label>
    <div class="col-9">
        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
    </div>
</div>

<?= form_close(); ?>
