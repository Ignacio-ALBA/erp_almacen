<?php
  ob_start(); // Inicia la captura del buffer de salida
  $PageSection = "Facturas Clientes";
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
  $id = 'facturas_clientes';
  $ButtonAddLabel = "Nueva Factura";
  $titulos = ['ID','Cliente','Proyecto','Cuenta Bancaria','Monto Total','Monto Neto','Factura PDF','Factura XML','Fecha de Facturación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Factura',
      'Title3'=>'Ver Factura',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'contabilidad'
    ],
    [
      CreateSelect(['id'=>'kid_cliente','etiqueta'=>'Cliente','required' => ''],$clientes),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => ''],$proyectos),
      CreateSelect(['id'=>'kid_cuenta_bancaria','etiqueta'=>'Cuenta Bancaria','required' => ''],$cuentas_bancarias),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '']),
      CreateInput(['type'=>'date','id'=>'fecha_factura','etiqueta'=>'Fecha de la Factura','required' => '']),
      CreateInput(['type'=>'file','accept'=>'application/pdf','id'=>'archivo_pdf','etiqueta'=>'Factura en PDF']),
      CreateInput(['type'=>'file','accept'=>'application/xml','id'=>'archivo_xml','etiqueta'=>'Factura en XML']),

     
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
