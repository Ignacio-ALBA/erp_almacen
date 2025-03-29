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
  $id = 'detalles_reportes_cb';
  $ButtonAddLabel = "Nueva Observación";
  $titulos = ['ID','ID Reporte','Descripción','Fecha de Transacción','No. Referencia','Tipo Reporte','Total','Neto','Fecha de Creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,false,'ButtonsInRow');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Observación',
      'Title3'=>'Ver Observación',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'contabilidad'
    ],
    [
      //CreateSelect(['id'=>'kid_reporte_cuenta_bancaria','etiqueta'=>'Cuenta Bancaria','required' => ''],$reportes_cuentas_bancarias),
      CreateTextArea(['type'=>'text','id'=>'observacion','etiqueta'=>'Tipo de Reporte','required' => '']),
    ]);
    CreateModalForm(
      [
        'id'=> 'observaciones_reportes_cb', 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Observación',
        'Title3'=>'Ver Observación',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'contabilidad'
      ],
      [
        CreateInput(['id'=>'kid_reporte_cuenta_bancaria','etiqueta'=>'Cuenta Bancaria','required' => '','readonly' => '']),
        CreateTextArea(['type'=>'text','id'=>'observacion','etiqueta'=>'Tipo de Reporte','required' => '']),
      ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
