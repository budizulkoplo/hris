<?php 
use App\Models\Konfigurasi_model;
$konfigurasi  = new Konfigurasi_model;
$site         = $konfigurasi->listing();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />

  <title><?= esc($title) ?></title>
  <meta content="<?= esc(strip_tags($description)) ?>" name="description" />
  <meta content="<?= esc($keywords) ?>" name="keywords" />
  
  <!-- Favicons -->
  <link href="<?= base_url('assets/upload/image/'.$site['icon']) ?>" rel="icon" />
  <link href="<?= base_url('assets/upload/image/'.$site['icon']) ?>" rel="apple-touch-icon" />

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=fallback" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="<?= base_url() ?>/assets/admin/plugins/fontawesome-free/css/all.min.css" rel="stylesheet" />
  <!-- Bootstrap + AdminLTE -->
  <link href="<?= base_url() ?>/assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet" />
  <link href="<?= base_url() ?>/assets/admin/dist/css/adminlte.min.css" rel="stylesheet" />

  <!-- SWEETALERT -->
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <style>
    body {
      background: #07b8b2;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .login-box {
      width: 100%;
      max-width: 360px;
      margin: auto;
    }

    .login-card-body {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      padding: 2rem;
    }

    .login-logo img {
      max-width: 150px;
      margin-bottom: 1rem;
    }

    .login-logo h1 {
      font-size: 1.5rem;
      font-weight: 600;
      margin: 0;
    }

    .login-logo h5 {
      font-size: 0.9rem;
      color: #666;
      margin-top: 0.25rem;
    }

    .btn-login {
      width: 100%;
      border-radius: 8px;
      font-weight: 600;
      padding: 0.6rem;
    }
  </style>

  <link rel="manifest" href="<?= base_url('manifest.json') ?>">
  <meta name="theme-color" content="#07b8b2">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="card">
      <div class="card-body login-card-body">
        <div class="login-logo text-center">
          <img src="<?= base_url('assets/upload/image/'.$site['icon']) ?>" alt="Logo <?= esc($site['singkatan']) ?>">
          <h1>LOGIN</h1>
          <h5><?= esc($site['tagline']) ?></h5>
        </div>
        <hr />
        <p class="login-box-msg">Masukkan username dan password</p>
        
        <?php if(\Config\Services::validation()->getErrors()): ?>
        <div class="text-danger mb-3">
          <?= \Config\Services::validation()->listErrors() ?>
        </div>
        <?php endif; ?>

        <?= form_open(base_url('login')) ?>
        <?= csrf_field() ?>

        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" autocomplete="off" required />
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" autocomplete="off" required />
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>

        <input type="hidden" name="redirect" value="<?= isset($_GET['redirect']) ? esc($_GET['redirect']) : '' ?>" />

        <button type="submit" class="btn btn-primary btn-login">Sign In</button>

        <?= form_close() ?>
      </div>
    </div>
  </div>

  <!-- jQuery & Bootstrap -->
  <script src="<?= base_url() ?>/assets/admin/plugins/jquery/jquery.min.js"></script>
  <script src="<?= base_url() ?>/assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url() ?>/assets/admin/dist/js/adminlte.min.js"></script>

  <script>
    <?php if(session()->getFlashdata('sukses')): ?>
      swal("Berhasil", "<?= session()->getFlashdata('sukses') ?>", "success");
    <?php endif; ?>

    <?php if(isset($_GET['logout'])): ?>
      swal("Berhasil", "Anda berhasil logout.", "success");
    <?php endif; ?>

    <?php if(isset($_GET['login'])): ?>
      swal("Oops...", "Anda belum login.", "warning");
    <?php endif; ?>

    <?php if(session()->getFlashdata('warning')): ?>
      swal("Mohon maaf", "<?= session()->getFlashdata('warning') ?>", "warning");
    <?php endif; ?>
  </script>
</body>
</html>
