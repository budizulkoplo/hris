# HRIS (Human Resource Information System)

Sistem HRIS ini dikembangkan untuk mengelola data pegawai, absensi, cuti, jadwal kerja, lembur, serta laporan-laporan SDM lainnya secara terpusat dan efisien.

## 🚀 Fitur Utama

- 📋 Manajemen Data Pegawai
- 🕑 Jadwal Kerja dan Shift Pegawai
- 📆 Kalender Absensi Bulanan (dengan shift & keterlambatan)
- 🧾 Rekapitulasi Absensi dan Keterlambatan
- 🛫 Pengajuan & Persetujuan Cuti / Izin / Tugas Luar
- ⏱️ Lembur (Input, Alasan, dan Validasi)
- 📄 Laporan Data dan Export
- 🔐 Login dan Hak Akses Berdasarkan Role

## 🛠️ Teknologi yang Digunakan

- **PHP 7.x**
- **CodeIgniter 4**
- **MySQL 5.6**
- **JavaScript + jQuery + DataTables**
- **Bootstrap** (untuk tampilan antarmuka)

## 🗂️ Struktur Direktori (Contoh)


## ⚙️ Instalasi

1. Clone repo ini:

   ```bash
   git clone https://github.com/username/hris.git

📌 Catatan
Data absensi ditarik dari mesin fingerprint dan diolah berdasarkan jadwal shift.

Kalender dan laporan cuti mengikuti periode: 26 bulan sebelumnya – 25 bulan berjalan.

Data lembur ditampilkan meskipun tidak ada data absen.

📄 Lisensi
Proyek ini dilindungi oleh lisensi pribadi / internal. Hubungi pengembang jika ingin kontribusi.

👨‍💻 Pengembang
Budizul
📧 buddyandreanto@gmail.com
GitHub: @budizulkoplo
