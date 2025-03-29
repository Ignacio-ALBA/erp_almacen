<?php
  ob_start(); // Inicia la captura del buffer de salida
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
  $id = 'detalles_cuentas_bancarias';
  $ButtonAddLabel = "Nuevo Detalle de Cuenta";
  $titulos = ['ID','Detalle Cuenta','Cuenta Bancaria','Proyecto','Monto Asignado','Monto Disponible','Monto Adeudado','Monto Gastado','Fecha de Creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,$AllowADDButton);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Detalle de Cuenta',
      'Title3'=>'Ver Detalle de Cuenta',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'contabilidad'
    ],
    [
      CreateInput(['type'=>'text','id'=>'detalle_cuenta_bancaria','etiqueta'=>'Detalle de Cuenta Bancaria','readonly' => '']),
      CreateSelect(['id'=>'kid_cuenta_bancaria','etiqueta'=>'Cuenta Bancaria','required' => ''],$cuentas_bancarias),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => ''],$proyectos),
      CreateInput(['type'=>'number','id'=>'monto_asignado','etiqueta'=>'Monto Asignado','class'=>'SUM-1','required'=>'']),
      CreateInput(['type'=>'number','id'=>'monto_gastado','etiqueta'=>'Monto Gastado','class'=>'RES-1','required'=>'']),
      CreateInput(['type'=>'number','id'=>'monto_adeudado','etiqueta'=>'Monto Adeudado','required'=>'']),
      CreateInput(['type'=>'number','id'=>'monto_disponible','etiqueta'=>'Monto Disponible','readonly' => '','class'=>'RESULT-1','required'=>'']),
      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
