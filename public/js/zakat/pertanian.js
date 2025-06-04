function hitungZakatPertanian() {
    const hasilPanen = parseFloat(document.getElementById('hasilPanen').value) || 0;
    const biayaProduksi = parseFloat(document.getElementById('biayaProduksi').value) || 0;
    const jenisIrigasi = parseFloat(document.getElementById('jenisIrigasi').value);

    const hasilBersih = hasilPanen - biayaProduksi;
    const zakatPertanian = hasilBersih * (jenisIrigasi / 100);

    document.getElementById('jumlahZakat').value = zakatPertanian.toFixed(2);

    const bayarZakatButton = document.getElementById("bayarZakatButton");
    bayarZakatButton.disabled = false;
}
