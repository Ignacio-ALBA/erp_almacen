<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Talento Humano";
?>

  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Talento Humano</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'talento_humano';
  $ButtonAddLabel = "Nuevo TH";
  $titulos = ['ID','Código','Proveedores','Calificación','Razón Social','RFC','Correo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar TH',
      'Title3'=>'Ver TH',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'talento_humano'
    ],
    [
        CreateInput(['type'=>'text','maxlength'=>'30','id'=>'codigo','etiqueta'=>'Código','required' => '']),
        CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
        CreateInput(['type'=>'text','maxlength'=>'27','id'=>'nombre','etiqueta'=>'Nombre','required' => '']),
        CreateInput(['type'=>'text','maxlength'=>'27','id'=>'apellido_paterno','etiqueta'=>'Apellido Paterno','required' => '']),
        CreateInput(['type'=>'text','maxlength'=>'27','id'=>'apellido_materno','etiqueta'=>'Apellido Materno','required' => '']),
        CreateInput(['type'=>'text','maxlength'=>'100','id'=>'razon_social','etiqueta'=>'Razón Social','required' => '']),
        CreateInput(['type'=>'text','maxlength'=>'100','id'=>'nombre_comercial','etiqueta'=>'Nombre Comercial','required' => '']),
        CreateInput(['type'=>'text','maxlength'=>'13','id'=>'rfc','etiqueta'=>'RFC','class'=>'ValidateRFC','required' => '']),
        CreateInput(['type'=>'number','min'=>'1'.str_repeat('0',9), 'max'=>str_repeat('9', 10),'id'=>'telefono1','etiqueta'=>'Teléfono 1','class'=>'ValidateTelefono']),
        CreateInput(['type'=>'number','min'=>'1'.str_repeat('0',9), 'max'=>str_repeat('9', 10),'id'=>'telefono2','etiqueta'=>'Teléfono 2','class'=>'ValidateTelefono']),
        CreateInput(['type'=>'number','min'=>'1'.str_repeat('0',9), 'max'=>str_repeat('9', 10),'id'=>'celular1','etiqueta'=>'Celular 1','class'=>'ValidateTelefono']),
        CreateInput(['type'=>'number','min'=>'1'.str_repeat('0',9), 'max'=>str_repeat('9', 10),'id'=>'celular2','etiqueta'=>'Celular 2','class'=>'ValidateTelefono']),
        CreateInput(['type'=>'email','id'=>'email1','etiqueta'=>'Correo 1','class'=>'ValidateCorreo']),
        CreateInput(['type'=>'email','id'=>'email2','etiqueta'=>'Correo 2','class'=>'ValidateCorreo']),
        CreateSelect(['id'=>'nombre_pais','etiqueta'=>'País'],$paises),
        CreateSelect(['id'=>'kid_estado','etiqueta'=>'Estado', 'class'=>'DataGET Data-GETMunicipios','bloque'=>'catalogo'],$estados),
        CreateSelect(['id'=>'nombre_municipio','etiqueta'=>'Municipio', 'class'=>'kid_estado'],[]),
        CreateInput(['type'=>'number','maxlength'=>'6','id'=>'cp','etiqueta'=>'CP', 'class'=>'DataGET Data-GETColonia','bloque'=>'catalogo']),
        CreateInput(['type'=>'text','maxlength'=>'200','id'=>'nombre_colonia','etiqueta'=>'Colonia', 'class'=>'cp']),
        CreateInput(['type'=>'text','maxlength'=>'200','id'=>'nombre_localidad','etiqueta'=>'Localidad']),
        CreateInput(['type'=>'text','maxlength'=>'200','id'=>'nombre_vialidad','etiqueta'=>'Vialidad']),
        CreateInput(['type'=>'number','maxlength'=>'5','id'=>'numero_exterior','etiqueta'=>'Número Exterior']),
        CreateInput(['type'=>'number','maxlength'=>'5','id'=>'numero_interior','etiqueta'=>'Número Interior']),
        CreatSwitchCheck(['id'=>'acceso','etiqueta'=>'Acceso'])
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
