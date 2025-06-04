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

  <!-- Google Font: Source Sans Pro -->
  <link
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"
    rel="stylesheet" />
  <!-- Font Awesome -->
  <link
    href="<?= base_url() ?>/assets/admin/plugins/fontawesome-free/css/all.min.css"
    rel="stylesheet" />
  <!-- icheck bootstrap -->
  <link
    href="<?= base_url() ?>/assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css"
    rel="stylesheet" />
  <!-- Theme style -->
  <link
    href="<?= base_url() ?>/assets/admin/dist/css/adminlte.min.css"
    rel="stylesheet" />

  <!-- SWEETALERT -->
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <style>
    body {
      background-image: url('public/uploads/back.webp');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      min-height: 100vh;
    }

    .login-box {
      width: 90%;
      max-width: 400px;
      margin: 2rem auto;
      min-width: auto !important;
    }

    @media (min-width: 768px) {
      .login-box {
        min-width: 35% !important;
      }
    }

    .login-card-body {
      background-color: rgba(255, 255, 255, 0.85) !important;
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      border-radius: 10px !important;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .login-box .card {
      background: transparent !important;
      box-shadow: none !important;
    }

    /* Logo dan Judul */
    .login-logo .row {
      align-items: center;
    }

    .login-logo img {
      max-width: 100%;
      height: auto;
    }

    @media (max-width: 576px) {
      .login-logo h1 {
        font-size: 1.5rem;
      }

      .login-logo h5 {
        font-size: 1rem;
      }

      .login-logo .col-12 {
        margin-bottom: 1rem;
      }

      .icheck-primary input[type="checkbox"] {
        width: 20px;
        height: 20px;
      }

      .icheck-primary label {
        font-size: 1rem;
      }
    }
    @media (max-width: 768px) {
    body {
      background-image: none !important;
      background-color: #07b8b2;
    }
}

  </style>
  <link rel="manifest" href="<?= base_url('manifest.json') ?>">
  <meta name="theme-color" content="#007bff">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <div class="login-logo">
          <div class="row">
            <div class="col-12 col-md-3 text-center mb-3 mb-md-0">
              <img
                src="<?= base_url('assets/upload/image/'.$site['icon']) ?>"
                alt="Logo <?= esc($site['singkatan']) ?>"
                class="img-fluid" />
            </div>
            <div class="col-12 col-md-9 text-center text-md-left">
              <h1><?= esc($site['singkatan']) ?> Login</h1>
              <h5><?= esc($site['tagline']) ?></h5>
            </div>
          </div>
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
          <input
            type="text"
            name="username"
            class="form-control"
            placeholder="Username"
            autocomplete="off"
            required />
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input
            type="password"
            name="password"
            class="form-control"
            placeholder="Password"
            autocomplete="off"
            required />
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <input
          type="hidden"
          name="redirect"
          value="<?= isset($_GET['redirect']) ? esc($_GET['redirect']) : '' ?>" />

        <div class="row align-items-center">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember" />
              <label for="remember">Remember Me</label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">
              Sign In
            </button>
          </div>
        </div>

        <?= form_close() ?>
        <hr />
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="<?= base_url() ?>/assets/admin/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url() ?>/assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
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

  <script>
  // Daftarkan service worker
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
      .then(function () {
        console.log('✅ Service Worker registered');
      });
  }

  let deferredPrompt;

  // Simpan event sebelum install prompt ditampilkan
  window.addEventListener('beforeinstallprompt', (e) => {
    // Mencegah prompt default
    e.preventDefault();

    // Simpan event agar bisa dipanggil nanti
    deferredPrompt = e;

    // Tampilkan prompt setelah delay 2 detik
    setTimeout(() => {
      if (deferredPrompt) {
        deferredPrompt.prompt();

        // Tanggapi pilihan user
        deferredPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === 'accepted') {
            console.log('✅ User accepted install prompt');
          } else {
            console.log('❌ User dismissed install prompt');
          }
          deferredPrompt = null;
        });
      }
    }, 2000); // bisa sesuaikan delay
  });
</script>

</body>

</html>
