<?php

include 'helper/fechasEspaniol.php';

$fechasaEspaniol = new fechasEspaniol();

$fechaActual = $fechasaEspaniol->obtenerFechasActualEspaniol();

//para mostrar activado en el menu el que esta actualmente seleccionado
if (isset($this->menuselec)) {
  $vistaActiva = $this->menuselec;
  // nav-link active
} else {
  $vistaActiva['general'] = 'active';
}

if (isset($this->vistaactual)) {
  $vistaactual=$this->vistaactual; 
}else{
  $vistaactual="general";
}



$loginintranet = $_SESSION['datos_session']['login'];
$fecha_hoy = $_SESSION['datos_session']['fecha_hoy'];
$nombre_usuario = trim($_SESSION['datos_session']['nombre_usuario']);

?>

<input type="hidden" id='input_fecha_hoy' value='<?php echo $fecha_hoy; ?>' />
<input type="hidden" id='input_loginintranet' value='<?php echo $loginintranet; ?>' />
<input type="hidden" id='input_nombre_usuario' value='<?php echo $nombre_usuario; ?>' />
<input type="hidden" name="inputvistaactual" id="inputvistaactual" value="<?php echo $vistaactual; ?>" />

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">&nbsp;&nbsp;&nbsp;&nbsp;</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link"><?php echo $fechaActual;  ?></a>
        </li>
      </ul>

      <!-- SEARCH FORM -->
      <!--  <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
          <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>-->


    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="index3.html" class="brand-link">
        <img src="resources/lib/dist/img/logoibg.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">IBG</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="resources/lib/dist/img/user.png" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block"><?php echo $loginintranet ?></a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <li class="nav-item">
              <a href="index.php?controlador=general&tipo=general" class="nav-link  <?php echo $vistaActiva['general']; ?>">
                <i class="fas fa-home nav-icon"></i>
                <p>Manual</p>
              </a>
            </li>
            <li class="nav-item  ">
              <a href="index.php?controlador=generarcodigos&tipo=generarcodigos" class="nav-link  <?php echo $vistaActiva['generarcodigos']; ?>">
                <i class="nav-icon fas fa-th"></i>
                <p>
                  Generar Codigos
                </p>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark"><?php echo $vistaactual; ?></h1>
            </div><!-- /.col -->            
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->