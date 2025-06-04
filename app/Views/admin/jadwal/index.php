<?= form_open(base_url('admin/jadwal/save')); ?>
<?= csrf_field(); ?>

<!-- Pilih Periode -->
<div class="form-group row">
    <label class="col-3">Pilih Bulan</label>
    <div class="col-9">
        <input type="month" name="periode" id="periode" class="form-control-sm" required>
    </div>
</div>

<!-- Pilih Karu -->
<div class="form-group row">
    <label class="col-3">Pilih Karu</label>
    <div class="col-9">
    <select name="idkaru" id="idkaru" class="form-control-sm select2" required>
    <option value="">Pilih Karu</option>
    <?php foreach ($karu_list as $karu) : 
        $selected = session()->get('idkaru') == $karu['idkaru'] ? 'selected' : '';
    ?>
        <option value="<?= esc($karu['idkaru']); ?>" <?= $selected; ?>>
            <?= esc($karu['nama']); ?> | <?= esc($karu['kelompok_nama']); ?>
        </option>
    <?php endforeach; ?>
</select>

    </div>
</div>
<!-- Daftar Pegawai -->
<div id="pegawaiContainer" class="mt-3"></div>
<?= form_close(); ?>
<p><br>
<b>Keterangan Shift:</b><br>
    P: Pagi<br>
    S: Siang<br>
    MD: Middle<br>
    M: Malam<br>
    O1: Office 1<br>
    O2: Office 2<br>
    PB: Pagi Bangsal<br>
</p>
<!-- CSS untuk Scrollable Table -->
<style>
.table-container {
    width: 100%;
    overflow-x: auto;
    max-height: 500px;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 5px;
}

table {
    width: auto;
    min-width: 100%;
    border-collapse: collapse;
}

thead th {
    position: sticky;
    top: 0;
    background: #f8f9fa;
    z-index: 2;
    text-align: center;
    white-space: nowrap;
    padding: 8px;
}

.fixed-column {
    position: sticky;
    left: 0;
    background: white;
    z-index: 3;
    white-space: nowrap;
    padding: 8px;
    font-weight: bold;
}

thead .fixed-column {
    z-index: 4;
}

.wide-select {
    min-width: 68px;
}
</style>

<script>
$(document).ready(function () {
    const today = new Date();
    const bulanIni = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2);
    $('#periode').val(bulanIni);

    const idkaru_session = "<?= session()->get('idkaru'); ?>"; // Ambil idkaru dari sesi

    if (idkaru_session) {
        $('#idkaru').val(idkaru_session).prop('disabled', true);
    }

    function loadPegawai() {
        const idkaru = $('#idkaru').val();
        const periode = $('#periode').val();

        if (idkaru && periode) {
            $.ajax({
                url: "<?= base_url('admin/jadwal/getPegawaiByKaru'); ?>",
                type: "POST",
                data: { idkaru: idkaru, periode: periode },
                dataType: "json",
                beforeSend: function () {
                    $('#pegawaiContainer').html('<p class="text-center">Memuat data...</p>');
                },
                success: function (response) {
                    let pegawaiHtml = '<h4 class="mt-3">Daftar Pegawai</h4>';
                    pegawaiHtml += '<div class="table-container">';
                    pegawaiHtml += '<table class="table table-bordered">';
                    pegawaiHtml += '<thead><tr><th class="fixed-column">Nama Pegawai</th>';

                    const date = new Date(periode + "-02");
                    const tahun = date.getFullYear();
                    const bulan = date.getMonth() + 1;

                    let bulanSebelumnya = bulan - 1;
                    let tahunSebelumnya = tahun;
                    if (bulanSebelumnya === 0) {
                        bulanSebelumnya = 12;
                        tahunSebelumnya -= 1;
                    }

                    const hariDalamBulanSebelumnya = new Date(tahunSebelumnya, bulanSebelumnya, 0).getDate();
                    const hariDalamBulan = new Date(tahun, bulan, 0).getDate();

                    const tanggalKerja = [];
                    for (let tgl = 26; tgl <= hariDalamBulanSebelumnya; tgl++) {
                        tanggalKerja.push({ tgl, bulan: bulanSebelumnya, tahun: tahunSebelumnya });
                    }
                    for (let tgl = 1; tgl <= 25; tgl++) {
                        tanggalKerja.push({ tgl, bulan, tahun });
                    }

                    tanggalKerja.forEach(function (tgl) {
                        pegawaiHtml += '<th>' + tgl.tgl + '</th>';
                    });

                    pegawaiHtml += '</tr></thead><tbody>';

                    if (response.pegawaiList.length > 0) {
                        response.pegawaiList.forEach(function (pegawai) {
                            pegawaiHtml += '<tr>';
                            pegawaiHtml += '<td class="fixed-column">' + pegawai.pegawai_nama + '</td>';

                            tanggalKerja.forEach(function (tgl) {
                                const tanggalFormat = tgl.tahun + '-' + ('0' + tgl.bulan).slice(-2) + '-' + ('0' + tgl.tgl).slice(-2);
                                const selectedShift = response.jadwalList?.[pegawai.pegawai_pin]?.[tanggalFormat] || '';

                                pegawaiHtml += '<td><select name="jadwal[' + pegawai.pegawai_pin + '][' + tanggalFormat + ']" class="form-control wide-select autosave" data-pegawai="' + pegawai.pegawai_pin + '" data-tgl="' + tanggalFormat + '">';
                                pegawaiHtml += '<option value="">-</option>';

                                response.shiftList.forEach(function (shift) {
                                    if (shift.bagian === pegawai.bagian) {
                                        const selected = shift.shift == selectedShift ? 'selected' : '';
                                        let shiftLabel = shift.shift;
                                        if (shiftLabel.toLowerCase() === 'pagi') shiftLabel = 'P';
                                        else if (shiftLabel.toLowerCase() === 'malam') shiftLabel = 'M';
                                        else if (shiftLabel.toLowerCase() === 'midle') shiftLabel = 'MD';
                                        else if (shiftLabel.toLowerCase() === 'siang') shiftLabel = 'S';
                                        else if (shiftLabel.toLowerCase() === 'office 1') shiftLabel = 'O1';
                                        else if (shiftLabel.toLowerCase() === 'office 2') shiftLabel = 'O2';
                                        else if (shiftLabel.toLowerCase() === 'pagi bangsal') shiftLabel = 'PB';
                                        pegawaiHtml += '<option value="' + shift.shift + '" ' + selected + '>' + shiftLabel + '</option>';
                                    }
                                });

                                pegawaiHtml += '</select></td>';
                            });

                            pegawaiHtml += '</tr>';
                        });
                    } else {
                        pegawaiHtml += '<tr><td colspan="' + (tanggalKerja.length + 1) + '" class="text-center">Tidak ada pegawai dalam kelompok ini.</td></tr>';
                    }

                    pegawaiHtml += '</tbody></table></div>';
                    $('#pegawaiContainer').html(pegawaiHtml);

                    // Autosave handler
                    $('.autosave').change(function () {
                        const pegawai_pin = $(this).data('pegawai');
                        const tgl = $(this).data('tgl');
                        const shift = $(this).val();

                        $.ajax({
                            url: "<?= base_url('admin/jadwal/autosave'); ?>",
                            type: "POST",
                            data: { pegawai_pin, tgl, shift },
                            success: function (response) {
                                console.log("Jadwal tersimpan:", response);
                            },
                            error: function (xhr, status, error) {
                                alert("Gagal menyimpan jadwal: " + error);
                            }
                        });
                    });
                },
                error: function () {
                    $('#pegawaiContainer').html('<p class="text-center text-danger">Gagal memuat data</p>');
                }
            });
        } else {
            $('#pegawaiContainer').html('');
        }
    }

    // Trigger ketika user ganti Karu atau Periode
    $('#idkaru, #periode').change(loadPegawai);

    // Kalau ada idkaru di session, langsung load saat awal
    if (idkaru_session) {
        loadPegawai();
    }
});
</script>


<script>
document.getElementById('exportExcel').addEventListener('click', function () {
    var idkaru = document.getElementById('idkaru').value;  // Ambil nilai ID Karu dari input
    var periode = document.getElementById('periode').value; // Ambil nilai Periode dari input

    if (!idkaru || !periode) {
        alert('Pilih Karu dan Periode terlebih dahulu!');
        return;
    }

    var url = "<?= base_url('admin/jadwal/exportCSV') ?>?idkaru=" + idkaru + "&periode=" + periode;
    window.location.href = url; // Redirect ke URL export
});


</script>


