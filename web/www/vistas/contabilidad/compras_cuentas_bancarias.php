<?php
  ob_start(); // Inicia la captura del buffer de salida
  $PageSection = "Compras con Cuenta Bancaria";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><?php echo $nombre_modulo; ?></li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 
  $id = 'compras_cuentas_bancarias';
  $ButtonAddLabel = "Nueva Compra";
  $titulos = ['ID','Cuenta Bancaria','Proyecto','Monto Total','Monto Neto','Fecha de Creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Compra',
      'Title3'=>'Ver Compra',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'contabilidad'
    ],
    [
      CreateSelect(['id'=>'kid_cuenta_bancaria','etiqueta'=>'Cuenta Bancaria','required' => ''],$cuentas_bancarias),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => ''],$proyectos),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required'=>'']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required'=>'']),
      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
