<div class="no-print mb-4">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-4 col-sm-6">
            <label class="form-label fw-bold">Kajian:</label>
            <select name="idkajian" class="form-control-sm select2">
                <option value="">- Semua Kajian -</option>
                <?php foreach ($dataKajian as $kajian): ?>
                    <option value="<?= $kajian['idkajian'] ?>" <?= $idkajian == $kajian['idkajian'] ? 'selected' : '' ?>>
                        <?= esc($kajian['namakajian']) ?>
                    </option>
                <?php endforeach; ?>
                <option value="api" <?= $idkajian == 'api' ? 'selected' : '' ?>>Ahad Pagi</option>
            </select>
        </div>

        <div class="col-md-3 col-sm-6">
            <label for="tanggal" class="form-label fw-bold">Tanggal Scan:</label>
            <input type="date" name="tanggal" id="tanggal" value="<?= esc($tanggal) ?>" class="form-control">
        </div>

        <div class="col-md-2 col-sm-6">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
    </form>
</div>
<?php if ($idkajian === 'api'): ?>
    <!-- ✅ TAMPILAN KHUSUS AGENDA AURAD PAGI -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            #print-header {
                display: block !important;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            table th, table td {
                border: 1px solid #000;
                padding: 6px;
                text-align: center;
            }
            tr {
                page-break-inside: avoid;
            }
        }
        th, td {
            vertical-align: middle;
        }
        .fw-bold {
            font-weight: bold;
        }
    </style>

    <div id="print-header" class="d-none d-print-block text-center mb-4">
        <h4 class="mb-0">Laporan Kehadiran Ahad Pagi</h4>
        <h5 class="mb-0">RS PKU Muhammadiyah Boja</h5>
        <?php if (!empty($periode)): ?>
            <p class="mb-2">Periode: <?= date('F Y', strtotime($periode . '-01')) ?></p>
        <?php endif; ?>
    </div>

    

    <div class="table-responsive">
        <table class="table table-bordered" id="example1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Foto</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                    <?php $no = 1; foreach ($dataKehadiran as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($row['nik']) ?></td>
                    <td><?= esc($row['nama_lengkap']) ?></td>
                    <td><?= esc($row['jabatan']) ?></td>
                    <td><?= esc($row['tgl_presensi']) ?></td>
                    <td><?= esc($row['jam_in']) ?></td>
                    <td>
                        <?php if (!empty($row['foto_in'])): ?>
                            <img src="https://kajian.pcmboja.com/storage/uploads/absensi/<?= $row['foto_in'] ?>" alt="Foto" width="100">

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($row['lokasi'])): 
                            $lok = explode(',', $row['lokasi']);
                            if (count($lok) == 2): 
                                $lat = trim($lok[0]);
                                $lng = trim($lok[1]);
                        ?>
                            <iframe 
                                width="200" 
                                height="150" 
                                frameborder="0" 
                                style="border:0" 
                                src="https://maps.google.com/maps?q=<?= $lat ?>,<?= $lng ?>&hl=es;z=14&output=embed">
                            </iframe>
                        <?php else: ?>
                            Tidak valid
                        <?php endif; ?>
                        <?php else: ?>
                            Tidak tersedia
                        <?php endif; ?>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#tabel').DataTable({
                pageLength: 50,
                responsive: true
            });
        });

        function printPage() {
            const table = $('#tabel').DataTable();
            table.page.len(-1).draw(); // tampilkan semua data
            setTimeout(() => {
                window.print();
                table.page.len(50).draw(); // kembali ke normal
            }, 500);
        }
    </script>

<?php else: ?>


<!-- Tabel -->
<div class="table-responsive">
    <table class="table table-bordered" id="example1">
        <thead>
            <tr>
                <th>No</th>
                <th>Kajian</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Waktu Scan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dataKehadiran as $index => $row): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td><?= esc($row['namakajian']) ?></td>
                    <td><?= esc($row['nik']) ?></td>
                    <td><?= esc($row['nama']) ?></td>
                    <td class="text-center"><?= esc($row['waktu_scan']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Script DataTable -->
<script>
    $(document).ready(function() {
        $('#tabel').DataTable({
            pageLength: 50,
            responsive: true
        });
    });

    function printPage() {
        const table = $('#tabel').DataTable();
        table.page.len(-1).draw(); // tampilkan semua data
        setTimeout(() => {
            window.print();
            table.page.len(50).draw(); // kembali ke normal
        }, 500);
    }
</script>

<!-- Styling tambahan -->
<style>
    .fw-bold {
        font-weight: bold;
    }

    th, td {
        vertical-align: middle;
    }
</style>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Pilih Kajian",
            allowClear: true,
            width: '100%' // pastikan lebar pas dengan form-control
        });
    });
</script>
