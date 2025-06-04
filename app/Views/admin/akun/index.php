<?php $isPegawai = ($aksesLevel === 'pegawai'); ?>

<div class="row">
    <div class="col-3">
        <img src="<?= ($user['gambar'] ?? '') === '' ? icon() : base_url('assets/upload/image/' . $user['gambar']); ?>" class="img img-thumbnail">
    </div>
    <div class="col-9">
        <form action="<?= base_url('admin/akun') ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">

            <!-- Nama Pengguna -->
            <div class="form-group row">
                <label class="col-3">Nama Pengguna</label>
                <div class="col-9">
                    <input type="text" name="nama" class="form-control" placeholder="Nama pengguna" value="<?= $user['nama'] ?>" required>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group row">
                <label class="col-3">Email</label>
                <div class="col-9">
                    <input type="email" name="email" class="form-control" placeholder="Email pengguna" value="<?= $user['email'] ?>" required>
                </div>
            </div>

            <!-- Username -->
            <div class="form-group row">
                <label class="col-3">Username</label>
                <div class="col-9">
                    <input type="text" name="username" class="form-control" placeholder="Username" value="<?= $user['username'] ?>" <?= $isPegawai ? 'readonly' : '' ?>>
                </div>
            </div>

            <!-- NIK -->
            <div class="form-group row">
                <label class="col-3">NIK</label>
                <div class="col-9">
                    <input type="text" name="nik" class="form-control" placeholder="NIK" value="<?= $user['nik'] ?>" <?= $isPegawai ? 'readonly' : '' ?>>
                </div>
            </div>

            <!-- Alamat -->
            <div class="form-group row">
                <label class="col-3">Alamat</label>
                <div class="col-9">
                    <textarea name="alamat" class="form-control" placeholder="Alamat lengkap" rows="3" required><?= $user['alamat'] ?></textarea>
                </div>
            </div>

            <!-- Nomor HP -->
            <div class="form-group row">
                <label class="col-3">Nomor HP</label>
                <div class="col-9">
                    <input type="text" name="nohp" class="form-control" placeholder="Nomor HP" value="<?= $user['nohp'] ?>" required>
                </div>
            </div>

            <!-- Keterangan (disembunyikan jika pegawai) -->
            <?php if (!$isPegawai): ?>
            <div class="form-group row">
                <label class="col-3">Keterangan</label>
                <div class="col-9">
                    <textarea name="keterangan" class="form-control" placeholder="Keterangan tambahan" rows="3"><?= $user['keterangan'] ?></textarea>
                </div>
            </div>
            <?php endif; ?>

            <!-- Password -->
            <div class="form-group row">
                <label class="col-3">Password</label>
                <div class="col-9">
                    <input type="text" name="password" class="form-control" placeholder="Password baru">
                    <small class="text-danger">Minimal 6 karakter dan maksimal 32 karakter atau biarkan kosong jika tidak ingin mengubah password</small>
                </div>
            </div>

            <!-- Upload Foto Profil (disembunyikan jika pegawai) -->
            <?php if (!$isPegawai): ?>
            <div class="form-group row">
                <label class="col-3">Upload Foto Profil</label>
                <div class="col-9">
                    <input type="file" name="gambar" class="form-control">
                </div>
            </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <div class="form-group row">
                <label class="col-3"></label>
                <div class="col-9">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="password"]').value;

    if (password.length > 0 && password.length < 6) {
        alert('Password minimal 6 karakter.');
        e.preventDefault(); // cegah form disubmit
    }
});
</script>
