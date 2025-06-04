<?php 
$session = \Config\Services::session();
use App\Models\Dasbor_model;

$m_dasbor = new Dasbor_model();
$aksesLevel = $session->get('akses_level'); // Pastikan ini sesuai nama session-nya
?>

<div class="alert bg-light">
	<h5>Hai <em class="text-yellow"><?= $session->get('nama') ?>, </em>Berikut ringkasan data HRIS terkini..</h5>
</div>

<div class="row">
  <?php if ($aksesLevel != 'pegawai') : ?>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-lock"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Pengguna Website</span>
          <span class="info-box-number"><?= angka($m_dasbor->user()) ?></span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Pegawai</span>
          <span class="info-box-number"><?= angka($m_dasbor->pegawai()) ?></span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-user-times"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Pegawai Cuti</span>
          <span class="info-box-number"><?= angka($m_dasbor->cutiHariIni()) ?></span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-taxi"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Pegawai Tugas Luar</span>
          <span class="info-box-number"><?= angka($m_dasbor->tlHariIni()) ?></span>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Info box Kehadiran selalu ditampilkan -->
  <?php if ($aksesLevel == 'pegawai') : ?>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Kehadiran</span>
          <span class="info-box-number"><?= angka($m_dasbor->kehadiran()) ?></span>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
