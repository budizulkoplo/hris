<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#laporanTable').DataTable({
            pageLength: 50,
            order: [[0, 'asc']],
            language: { emptyTable: "Tidak ada data ditemukan" }
        });
    }
});
</script>

<style>
    .fw-bold { font-weight: bold; }
    .table-bordered th, .table-bordered td {
        border: 1px solid #ddd !important;
    }
</style>
