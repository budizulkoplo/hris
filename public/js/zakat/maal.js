function hitungZakatMaal() {
    const hargaEmas = parseFloat(document.getElementById('hargaEmas').value);
    const jumlahNisab = hargaEmas * 85;
    document.getElementById('jumlahNisab').value = jumlahNisab;

    const uangTunai = parseFloat(document.getElementById('uangTunai').value) || 0;
    const saham = parseFloat(document.getElementById('saham').value) || 0;
    const realEstate = parseFloat(document.getElementById('realEstate').value) || 0;
    const emasPerak = parseFloat(document.getElementById('emasPerak').value) || 0;
    const mobil = parseFloat(document.getElementById('mobil').value) || 0;

    const jumlahHarta = uangTunai + saham + realEstate + emasPerak + mobil;
    document.getElementById('jumlahHarta').value = jumlahHarta;

    const hutang = parseFloat(document.getElementById('hutang').value) || 0;
    const hartaKenaZakat = jumlahHarta - hutang;
    document.getElementById('hartaKenaZakat').value = hartaKenaZakat;

    const statusNisab = document.getElementById('statusNisab');
    const jumlahZakat = document.getElementById('jumlahZakat');

    if (hartaKenaZakat >= jumlahNisab) {
        statusNisab.textContent = 'Sudah Mencapai Nisabs';
        statusNisab.classList.remove('text-danger');
        statusNisab.classList.add('text-success');

        jumlahZakat.value = hartaKenaZakat * 0.025;
    } else {
        statusNisab.textContent = 'Belum Mencapai Nisab';
        statusNisab.classList.remove('text-success');
        statusNisab.classList.add('text-danger');

        jumlahZakat.value = 0;
    }

    const bayarZakatButton = document.getElementById("bayarZakatButton");
    bayarZakatButton.disabled = false;
    
}










