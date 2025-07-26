<?php 
$session = \Config\Services::session();
use App\Models\Dasbor_model;

$m_dasbor = new Dasbor_model();
$aksesLevel = $session->get('akses_level'); // Pastikan ini sesuai nama session-nya
?>
<style>
  .btn-bevel {
  box-shadow: inset -2px -2px 5px rgba(255, 255, 255, 0.6),
              inset 2px 2px 5px rgba(0, 0, 0, 0.2),
              2px 2px 5px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease-in-out;
  }

  .btn-bevel:hover {
    box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.2),
                inset -2px -2px 5px rgba(255, 255, 255, 0.6),
                1px 1px 3px rgba(0, 0, 0, 0.2);
    transform: translateY(1px);
  }

</style>
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

  <?php if ($aksesLevel == 'pegawai') : ?>
  <div class="col-12 col-sm-6 col-md-3">
    <a href="<?= base_url('admin/kajian/formscan') ?>" class="text-white text-decoration-none">
      <div class="info-box mb-3 bg-success rounded btn-bevel">
        <span class="info-box-icon bg-warning elevation-1">
          <i class="fas fa-qrcode"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text fw-bold fs-6">KEHADIRAN</span>
          <span class="info-box-number fs-4">Scan QR</span>
        </div>
      </div>
    </a>
  </div>
    
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box mb-3">
      <span class="info-box-icon bg-info elevation-1"><i class="fas fa-check"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Kehadiran</span>
        <span class="info-box-number"><?= angka($m_dasbor->kehadiran()) ?></span>
      </div>
    </div>
  </div>
<?php endif; ?>

</div>
