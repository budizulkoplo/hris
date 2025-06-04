<?= form_open(base_url('admin/karu/tambah')); ?>
<?= csrf_field(); ?>

<p>
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-tambah-karu">
        <i class="fa fa-plus"></i> Tambah KARU
    </button>
</p>

<div class="modal fade" id="modal-tambah-karu">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah KARU</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Pilih Pegawai (PIN & Nama) -->
                <div class="form-group row">
                    <label class="col-3">Pilih Pegawai</label>
                    <div class="col-9">
                        <select name="pin" id="pegawai" class="form-control-sm select2" required>
                            <option value="">Pilih Pegawai</option>
                            <?php foreach ($pegawai as $p) : ?>
                                <option value="<?= esc($p['pegawai_pin']); ?>" 
                                    data-nama="<?= esc($p['pegawai_nama']); ?>"
                                    <?= old('pin') == $p['pegawai_pin'] ? 'selected' : ''; ?>>
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
                        <input type="text" id="nama_karu" name="nama_karu" class="form-control" 
                            placeholder="Nama KARU" required readonly 
                            value="<?= old('nama_karu'); ?>">
                    </div>
                </div>

                <!-- Pilih Kelompok Kerja -->
                <div class="form-group row">
                    <label class="col-3">Kelompok Kerja</label>
                    <div class="col-9">
                        <select name="idkelompokkerja" class="form-control-sm select2" required>
                            <option value="">Pilih Kelompok Kerja</option>
                            <?php foreach ($kelompok as $k) : ?>
                                <?php if (!in_array($k['idkelompokkerja'], $used_kelompok)) : ?>
                                    <option value="<?= $k['idkelompokkerja']; ?>" <?= old('idkelompokkerja') == $k['idkelompokkerja'] ? 'selected' : ''; ?>>
                                        <?= $k['namakelompok']; ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>

        </div>
    </div>
</div>

<?= form_close(); ?>

<script>
    $(document).ready(function () {
        // Inisialisasi Select2 jika digunakan
        $('.select2').select2();

        function updateNamaKaru() {
            var selectedOption = $('#pegawai').find(':selected');
            var nama = selectedOption.data('nama') || '';

            console.log("👤 Pegawai dipilih:", selectedOption.val());
            console.log("📝 Nama KARU:", nama);

            $('#nama_karu').val(nama);
        }

        // Event listener saat dropdown pegawai berubah
        $(document).on('change', '#pegawai', function () {
            updateNamaKaru();
        });

        // Saat modal dibuka, cek apakah pegawai sudah dipilih
        $('#modal-tambah-karu').on('shown.bs.modal', function () {
            console.log("✅ Modal dibuka!");

            if ($('#pegawai').val()) {
                updateNamaKaru();
            }
        });
    });
</script>

