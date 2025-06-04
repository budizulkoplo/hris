function hitungZakatHadiah() {
    const jenisHadiah = document.getElementById("jenisHadiah").value;
    const jumlahHadiah = parseFloat(document.getElementById("jumlahHadiah").value) || 0;
    const pajakHadiah = parseFloat(document.getElementById("pajakHadiah").value) || 0;

    // Menghitung total yang diterima setelah pajak
    const pajak = (pajakHadiah / 100) * jumlahHadiah;
    const totalDiterima = jumlahHadiah - pajak;

    // Menghitung zakat berdasarkan jenis hadiah
    let zakat = 0;
    if (jenisHadiah === "2.5Hibah") {
        zakat = (2.5 / 100) * totalDiterima; // Hibah Diduga
    } else {
        zakat = (parseFloat(jenisHadiah) / 100) * totalDiterima; // Hadiah Terkait Gaji, Komisi, atau Hibah Tak Terduga
    }

    // Update nilai ke form
    document.getElementById("totalDiterima").value = totalDiterima.toFixed(2);
    document.getElementById("jumlahZakat").value = zakat.toFixed(2);

    const bayarZakatButton = document.getElementById("bayarZakatButton");
    bayarZakatButton.disabled = false;
}
