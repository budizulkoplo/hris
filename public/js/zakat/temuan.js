function hitungZakatTemuan() {
    const nilaiBarangTemuan = parseFloat(document.getElementById('nilaiBarangTemuan').value || 0);
    const zakatTemuan = nilaiBarangTemuan * 0.2; // 20% dari nilai barang temuan

    document.getElementById('jumlahZakat').value = Math.round(zakatTemuan); // Tampilkan hasil

    const bayarZakatButton = document.getElementById("bayarZakatButton");
    bayarZakatButton.disabled = false;
}
