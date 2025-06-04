function calculateZakatHibah() {
    const totalHibah = parseFloat(document.getElementById('totalHibah').value) || 0;
    const nisab = parseFloat(document.getElementById('nisabHibah').value);

    if (totalHibah >= nisab) {
        const zakat = totalHibah * 0.025; // 2.5% zakat
        document.getElementById('resultHibah').innerHTML = `<p class="text-success">Zakat yang harus dibayarkan adalah Rp ${zakat.toFixed(2)}</p>`;
    } else {
        document.getElementById('resultHibah').innerHTML = `<p class="text-danger">Nilai hibah Anda belum mencapai nisab. Tidak wajib zakat.</p>`;
    }
}
