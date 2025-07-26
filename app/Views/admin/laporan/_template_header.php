<style>
@media print {
    .no-print { display: none !important; }
    #print-header { display: block !important; }
    table { border-collapse: collapse; width: 100%; }
    table th, table td { border: 1px solid #000; padding: 6px; text-align: center; }
    tr { page-break-inside: avoid; }
}
</style>

<div id="print-header" class="d-none d-print-block text-center mb-4">
    <h4 class="mb-0"><?= $title ?></h4>
    <h5 class="mb-0">RS PKU Muhammadiyah Boja</h5>
    <p class="mb-2">Periode: <?= date('F Y', strtotime($periode . '-01')) ?></p>
</div>

<div class="no-print">
    <form method="get" class="mb-4">
        <label for="periode" class="form-label fw-bold">Pilih Periode (Bulan-Tahun):</label>
        <div class="input-group" style="max-width: 300px;">
            <input type="month" class="form-control" name="periode" value="<?= esc($periode) ?>">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </form>
</div>
