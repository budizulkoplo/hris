<?php 
use App\Models\Konfigurasi_model;

$session = \Config\Services::session();
$konfigurasi  = new Konfigurasi_model;
$site         = $konfigurasi->listing();
$username=$session->get('nama');
?>
<style type="text/css" media="screen">
  .nav-item a:hover {
    color: yellow !important;
  }
</style>
<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url('admin/dasbor') ?>" class="brand-link">
      <img src="<?php echo base_url('assets/upload/image/'.$site['icon']) ?>" alt="<?php echo $site['namaweb'] ?>" class="brand-image img-circle elevation-3" style="opacity: .8; background-color: white; border-radius: 50%; padding: 5px;">
      <span class="brand-text font-weight-light"><?php echo $site['namaweb'] ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php if($session->get('gambar')=="") { echo base_url('/assets/admin/dist/img/user.png'); }else{ echo base_url('assets/upload/image/'.$session->get('gambar')); } ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="<?php echo base_url('admin/akun') ?>" class="d-block"><?php echo $session->get('nama') ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <?php 
        // Memanggil fungsi load_menus untuk mendapatkan item menu
        $menus = load_menuadmin(); 
        ?>
        <?php if ($menus): ?>
            <?php foreach ($menus as $menu): ?>
                <li class="nav-item">
                    <!-- Jika ada submenu, tidak menggunakan href -->
                    <a href="<?= (isset($menu['submenu']) && !empty($menu['submenu'])) ? '#' : base_url($menu['link']) ?>" class="nav-link">
                        <i class="<?= $menu['icon'] ?>"></i>
                        <p>
                            <?= $menu['namamenu'] ?>
                            <?php if (!empty($menu['submenu'])): ?>
                                <i class="right fas fa-angle-left"></i> <!-- Icon untuk dropdown -->
                            <?php endif; ?>
                        </p>
                    </a>
                    <?php if (isset($menu['submenu']) && !empty($menu['submenu'])): ?>
                        <ul class="nav nav-treeview">
                            <?php foreach ($menu['submenu'] as $submenu): ?>
                                <li class="nav-item">
                                    <a href="<?= base_url($submenu['link']) ?>" class="nav-link">
                                        <i class="<?= $submenu['icon'] ?>"></i>
                                        <p><?= $submenu['namamenu'] ?></p>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="nav-item">
                <p>No menus available</p>
            </li>
        <?php endif; ?>
    </ul>
</nav>


    </div>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
    </aside>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?php echo $title ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo base_url('admin/dasbor') ?>">Dashboard</a></li>
              <li class="breadcrumb-item active"><?php echo $title ?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex align-items-center">
                  <h3 class="card-title mb-0"><?= $title ?></h3>
                  <div class="menu ml-auto">
                    <?php if (isset($payrollstatus) && $payrollstatus === 'payroll'): ?>
                        <button onclick="ExporttoPayroll()" class="btn btn-warning">
                            <i class="fas fa-save"></i> Export to Payroll
                        </button>
                    <?php endif; ?>
                    &nbsp;
                    <?php if (isset($excelstatus) && $excelstatus === 'excel'): ?>
                      <button id="exportExcel" class="btn btn-success ">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    <?php endif; ?>
                    &nbsp;
                    <?php if (isset($printstatus) && $printstatus === 'print'): ?>
                        <button onclick="printPage()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    <?php endif; ?>
                    </div>
              </div>
              <div class="card-body" style="min-height: 500px;">


<?php 
$validation = \Config\Services::validation();
    $errors = $validation->getErrors();
    if(!empty($errors))
    {
        echo '<span class="text-danger">'.$validation->listErrors().'</span>';
    }
?>

<?php if (session('msg')) : ?>
     <div class="alert alert-info alert-dismissible">
         <?= session('msg') ?>
         <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
     </div>
 <?php endif ?>