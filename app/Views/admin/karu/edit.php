<?= form_open(base_url('admin/karu/edit/' . $karu['idkaru'])); ?>
<?= csrf_field(); ?>

<h4>Edit Kepala Regu</h4>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<?php if (isset($validation)): ?>
    <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
<?php endif; ?>

<!-- Pilih Pegawai (PIN & Nama) -->
<div class="form-group row">
    <label class="col-3">Pilih Pegawai</label>
    <div class="col-9">
        <select name="pin" id="pegawai_edit" class="form-control-sm select2" required>
            <option value="">Pilih Pegawai</option>
            <?php foreach ($pegawai as $p) : ?>
                <option value="<?= esc($p['pegawai_pin']); ?>" 
                    data-nama="<?= esc($p['pegawai_nama']); ?>"
                    <?= ($karu['pin'] == $p['pegawai_pin']) ? 'selected' : ''; ?>>
                    <?= esc($p['pegawai_pin']); ?> - <?= esc($p['pegawai_nama']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Nama KARU (Autofill dari pegawai) -->
<div class="form-group row">
    <label class="col-3">Nama KARU</label>
    <div class="col-9">
        <input type="text" id="nama_karu_edit" name="nama" class="form-control" 
            placeholder="Nama KARU" required readonly 
            value="<?= esc($karu['nama']); ?>">
    </div>
</div>

<!-- Pilih Kelompok Kerja -->
<div class="form-group row">
    <label class="col-3">Kelompok Kerja</label>
    <div class="col-9">
        <select name="idkelompokkerja" class="form-control-sm select2" required>
            <option value="">Pilih Kelompok Kerja</option>
            <?php foreach ($kelompok as $k) : ?>
                <?php if (!in_array($k['idkelompokkerja'], $used_kelompok) || $k['idkelompokkerja'] == $karu['idkelompokkerja']) : ?>
                    <option value="<?= $k['idkelompokkerja']; ?>" <?= ($k['idkelompokkerja'] == $karu['idkelompokkerja']) ? 'selected' : ''; ?>>
                        <?= $k['namakelompok']; ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
<a href="<?= base_url('admin/karu'); ?>" class="btn btn-secondary">Kembali</a>

<?= form_close(); ?>
