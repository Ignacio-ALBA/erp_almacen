<?php
  ob_start(); // Inicia la captura del buffer de salida
  $PageSection = "Cuentas Bancarias";
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
  $id = 'cuentas_bancarias';
  $ButtonAddLabel = "Nueva Cuenta";
  $titulos = ['ID','Cuenta Bancaria','Banco','No. Cuenta','CABLE','No. Tarjeta','Tipo de Cuenta','Saldo','Deuda','Fecha de Creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true,'ButtonsInRow');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Cuenta',
      'Title3'=>'Ver Cuenta',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'contabilidad'
    ],
    [
      CreateInput(['type'=>'text','id'=>'cuenta_bancaria','etiqueta'=>'Cuenta Bancaria','required' => '']),
      CreateSelect(['id'=>'kid_banco','etiqueta'=>'Banco','required' => ''],$bancos),
      CreateInput(['type'=>'number','maxlength'=>'12','id'=>'numero_cuenta_bancaria','etiqueta'=>'Número de Cuenta', 'class'=>'ValidateAccountNumber']),
      CreateInput(['type'=>'number','maxlength'=>'18','id'=>'cable','etiqueta'=>'CABLE', 'class'=>'ValidateCLABE']),
      CreateInput(['type'=>'number','maxlength'=>'19','id'=>'tarjeta','etiqueta'=>'Número de Tarjeta', 'class'=>'ValidateCardNumber']),
      CreateSelect(['id'=>'kid_tipo_cuenta_bancaria','etiqueta'=>'Tipo de Cuenta','required' => ''],$tipos_cuentas_bancarias),
      CreateInput(['type'=>'number','id'=>'saldo','etiqueta'=>'Saldo','required' => '']),
      CreateInput(['type'=>'number','id'=>'deuda','etiqueta'=>'Deuda','required' => '']),
      CreatSwitchCheck(['id'=>'cuenta_maestra','etiqueta'=>'Cuenta Maestra']),
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
