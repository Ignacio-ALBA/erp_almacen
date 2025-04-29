<?php
include 'navmenulist.php';
// Configuración de cabeceras de seguridad
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
//header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com http://localhost; img-src 'self' data:;");
// Generar un nonce aleatorio
$nonce = base64_encode(random_bytes(16));

// Configurar la política de seguridad de contenido
//header("Content-Security-Policy: script-src 'self' 'nonce-$nonce';");
//header("Content-Security-Policy: script-src 'self' 'nonce-$nonce' https://cdn.datatables.net https://code.jquery.com https://cdnjs.cloudflare.com 'unsafe-eval';");
header("Content-Security-Policy: script-src 'self' 'nonce-$nonce' https://cdn.datatables.net https://code.jquery.com https://cdnjs.cloudflare.com 'unsafe-eval';");
header("Content-Security-Policy: script-src 'self' 'nonce-$nonce' https://cdn.datatables.net https://code.jquery.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net 'unsafe-eval';");



$SERVERURL = getServerUrl();
$BaseUrl = getBaseUrl();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $PageSection; ?> - ALBA ERP</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?php echo $SERVERURL ?>/assets/img/favicon.png" rel="icon">
  <link href="<?php echo $SERVERURL ?>/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo $SERVERURL ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $SERVERURL ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $SERVERURL ?>/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?php echo $SERVERURL ?>/assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="<?php echo $SERVERURL ?>/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="<?php echo $SERVERURL ?>/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?php echo $SERVERURL ?>/assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo $SERVERURL ?>/assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  <style>
/* Estilos para alinear la barra de búsqueda y la paginación a la derecha */
.dataTables_wrapper .dataTables_filter {
float: right; /* Alinear la barra de búsqueda a la derecha */
}
.dataTables_wrapper .dataTables_paginate {
float: right; /* Alinear los botones de paginación a la derecha */
}
.dataTables_wrapper .dataTables_length {
float: left; /* Alinear el selector de longitud a la izquierda */
}
.readonly-input {
background-color: #f0f0f0; /* Color gris claro */
color: #666; /* Color de texto gris */
border: 1px solid #ccc; /* Borde gris */
padding: 8px; /* Espaciado interno */
border-radius: 4px; /* Bordes redondeados */
}

<?php  
global $conexion;

if ($conexion !== null) {
$consulta = "SELECT clase, color_hex FROM colores_interfaz";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$colores = $resultado->fetchAll(PDO::FETCH_ASSOC);
$colores = array_column($colores, 'color_hex', 'clase');

$conexion = null;

$color_hex = $colores['primary'];

// Quitar el símbolo '#' si está presente
if (strpos($color_hex, '#') === 0) {
$color_hex = substr($color_hex, 1);
}

// Convertir de hexadecimal a RGB
$r = hexdec(substr($color_hex, 0, 2));
$g = hexdec(substr($color_hex, 2, 2));
$b = hexdec(substr($color_hex, 4, 2));

// Crear el color RGBA con 25% de opacidad
$rgba_primary = "rgba($r, $g, $b, 0.25)";

foreach($colores as $key => $color){
echo'.btn-outline-'.$key.' {
--bs-btn-color: '.$color.';
--bs-btn-border-color: '.$color.';
--bs-btn-hover-bg: '.$color.';
--bs-btn-hover-border-color: '.$color.';
--bs-btn-focus-shadow-rgb: '. hexToRgb($color) .';
--bs-btn-active-bg: '.$color.';
--bs-btn-active-border-color: '.$color.';
--bs-btn-disabled-color: '.$color.';
--bs-btn-disabled-border-color: '.$color.';
}'. "\n"; ;
}
/*
foreach($colores as $key => $color){
echo'.btn-'.$key.' {
--bs-btn-bg: ' . $color . ';
--bs-btn-border-color: ' . $color . ';
--bs-btn-active-bg: ' . $color . ';
--bs-btn-active-border-color: ' . $color . ';
--bs-btn-hover-bg:  rgba(' . hexToRgb($color) . ', 0.8);
--bs-btn-hover-border-color:  rgba(' . hexToRgb($color) . ', 0.8);
--bs-btn-disabled-bg:  rgba(' . hexToRgb($color) . ', 0.5);
--bs-btn-disabled-border-color:  rgba(' . hexToRgb($color) . ', 0.5);
}'. "\n"; ;
}*/

echo '
.pagination {
--bs-pagination-focus-color: ' . $colores['primary'] . ';
--bs-pagination-focus-box-shadow:' . $rgba_primary . ';
--bs-pagination-color:' . $colores['primary'] . ';
--bs-pagination-hover-color:' . $colores['primary'] . ';
--bs-pagination-hover-border-color:' . $colores['primary'] . ';
--bs-pagination-active-bg: ' . $colores['primary'] . ';
--bs-pagination-active-border-color: ' . $colores['primary'] . ';
}
.table-buttons {
    min-height: 62px; /* Ocupa todo el ancho */
    display: flex; /* Usar flexbox para alinear los botones */
    justify-content: end; /* Espacio entre los botones */
    align-items:end;
    padding: 0px 10px;
}
';
}
?> 
  </style>
  <?php 
    if (!empty($params['list_styles'])) {
      foreach ($params['list_styles'] as $style => $variables) {
          // Extraer las variables en el contexto actual
          
          extract($variables); // Esto crea variables a partir de los nombres en el array
          include $style.'.php'; // Incluir el archivo PHP
      }
    }
    
  ?>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="/" class="logo d-flex align-items-center">
        <img src="<?php echo $SERVERURL ?>/assets/img/logoi.JPG" alt="">
        <img src="<?php echo $SERVERURL ?>/assets/img/logot2.JPG" alt="">
        <!-- <span class="d-none d-lg-block">Proselect</span> -->
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <!--
    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div>--><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">4</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              You have 4 new notifications
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-x-circle text-danger"></i>
              <div>
                <h4>Atque rerum nesciunt</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>1 hr. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-check-circle text-success"></i>
              <div>
                <h4>Sit rerum fuga</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>2 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-info-circle text-primary"></i>
              <div>
                <h4>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>4 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="dropdown-footer">
              <a href="#">Show all notifications</a>
            </li>

          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-chat-left-text"></i>
            <span class="badge bg-success badge-number">3</span>
          </a><!-- End Messages Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
            <li class="dropdown-header">
              You have 3 new messages
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="<?php echo $SERVERURL ?>/assets/img/messages-1.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Maria Hudson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>4 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="<?php echo $SERVERURL ?>/../assets/img/messages-2.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Anna Nelson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>6 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="<?php echo $SERVERURL ?>/assets/img/messages-3.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>David Muldon</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>8 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="dropdown-footer">
              <a href="#">Show all messages</a>
            </li>

          </ul><!-- End Messages Dropdown Items -->

        </li><!-- End Messages Nav -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo $SERVERURL ?>/assets/img/perfilManuel.jpeg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION["s_nombre"]; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION["s_nombre"]; ?></h6>
              <span><?php echo $_SESSION["s_tipo_usuario"] ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>Mi Perfil</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="/rutas/dashboard.php/cambiarpass">
                <i class="bi bi-key"></i>
                <span>Cambiar Contraseña</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-gear"></i>
                <span>Configuración</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>Ayuda</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo $SERVERURL ?>/bd/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar Sesión</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="/">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->
      <?php RenderMenuLateral($navItems, 'nav', 0); ?>

      <!--<li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Almacén</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">

          <li>
            <a href="tables-general.html">
              <i class="bi bi-circle"></i><span>Inventario</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Herramientas</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Maquinaria</span>
            </a>
          </li>
        </ul>
      </li>
      <!-- 
      <li class="nav-heading">Páginas Adicionales (Demostración)</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.html">
          <i class="bi bi-person"></i>
          <span>Perfil</span>
        </a>
      </li>-->
<!--
      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-faq.html">
          <i class="bi bi-question-circle"></i>
          <span>F.A.Q</span>
        </a>
      </li> -->
<!--
      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-contact.html">
          <i class="bi bi-envelope"></i>
          <span>Contactos</span>
        </a>
      </li> 
      -->

      <!--
      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-register.html">
          <i class="bi bi-card-list"></i>
          <span>Registrar</span>
        </a>
      </li>
      -->
 
      <!--
      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-login.html">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Ingresar</span>
        </a>
      </li>
       -->
       <!--
      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-error-404.html">
          <i class="bi bi-dash-circle"></i>
          <span>Error 404</span>
        </a>
      </li>
       -->
<!--
      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-blank.html">
          <i class="bi bi-file-earmark"></i>
          <span>Nada</span>
        </a>
      </li>
      -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    
    <?php echo $wrapper_dashboard; ?>
  </main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
  <div class="copyright">
    &copy; Copyright <strong><span>ALBA-DTI S. de R.L. de C.V.</span></strong>. Todos los derechos reservados
  </div>
  <div class="credits">
    <!-- All the links in the footer should remain intact. -->
    <!-- You can delete the links only if you purchased the pro version. -->
    <!-- Licensing information: https://bootstrapmade.com/license/ -->
    <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
    Diseñado por <a href="https:/www.alba-dti.com/">www.alba-dti.com</a>
  </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/jquery/jquery-3.3.1.min.js"></script> <!-- Cargar jQuery primero -->
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/vendor/apexcharts/apexcharts.min.js"></script>
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/vendor/chart.js/chart.umd.js"></script>
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/vendor/echarts/echarts.min.js"></script>
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/vendor/quill/quill.js"></script>
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/vendor/simple-datatables/simple-datatables.js"></script> <!-- DataTables -->
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/vendor/datatables/datatables.min.js"></script>  
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/vendor/tinymce/tinymce.min.js"></script>
<script nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/vendor/php-email-form/validate.js"></script>
<script nonce="<?php echo $nonce; ?>" src="https://cdn.datatables.net/searchpanes/1.3.1/js/dataTables.searchPanes.min.js"></script>

<!-- Otras dependencias de DataTables -->
<script nonce="<?php echo $nonce; ?>" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script nonce="<?php echo $nonce; ?>" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script nonce="<?php echo $nonce; ?>" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script nonce="<?php echo $nonce; ?>" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script nonce="<?php echo $nonce; ?>" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>

<!-- Template Main JS File -->
<script  nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/js/main.js"></script>
<script  nonce="<?php echo $nonce; ?>" src="<?php echo $SERVERURL ?>/assets/js/fullcalendar/index.global.min.js"></script>
<!--<script src="<?php //echo $SERVERURL ?>/vistas/formularios.js"></script>-->
<?php 
  if(!empty($params['list_js_scripts'])) {
    foreach ($params['list_js_scripts'] as $script => $variables) {
        // Verificar si el archivo existe
        $scriptPath = __DIR__ .'/'. $script . '.php';
        //debug($scriptPath);
        if (file_exists($scriptPath)) {
            // Extraer las variables en el contexto actual
            extract($variables);
            include $scriptPath; // Incluir el archivo PHP
        } else {
            // Loguear el error o notificar
            debug("El archivo $scriptPath no existe o no es accesible.");
        }
    }
  }


?>
</body>

</html>