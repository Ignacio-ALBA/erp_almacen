<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Relación de Permisos en el Sistema";
    
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><?php echo $nombre_modulo; ?></li>
        <?php 
          if($breadcrumb){
            echo $breadcrumb;
          } else{
            echo '<li class="breadcrumb-item active">'.$PageSection.'</li>';
          }
        ?>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 
  $id = 'detalles_planeaciones_actividades';
  $ButtonAddLabel = "";
  $titulos = ['ID', 'Permiso','Tabla','Modulo','Estado'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,false,'ButtonsInRow');

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
