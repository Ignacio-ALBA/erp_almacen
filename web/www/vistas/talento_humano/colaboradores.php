<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Colaboradores ";
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

  $id = 'colaboradores';
  $ButtonAddLabel = "Nuevo Colaborador";
  $titulos = ['ID','Nombre','Modalidad','Email','Tipo de Colaborador','Inicio de Sesión','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones,'StaticButtons');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Colaborador',
      'Title3'=>'Ver Colaborador',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'talento_humano'
    ],
    [
      CreateInput(['type'=>'text','maxlength'=>'17','id'=>'codigo','etiqueta'=>'Código','required' => '']),
      CreateInput(['type'=>'email','id'=>'email','etiqueta'=>'Correo Electrónico','class'=>'ValidateCorreo OnEditReadOnly','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'27','id'=>'nombre','etiqueta'=>'Nombre','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'27','id'=>'apellido_paterno','etiqueta'=>'Apellido Paterno','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'27','id'=>'apellido_materno','etiqueta'=>'Apellido Materno','required' => '']),
      CreatSwitchCheck(['id'=>'login','etiqueta'=>'Colaborador del sistema','class'=>'VerificarCambioMostrar']),
      CreateSelect(['id'=>'kid_tipo_usuario','etiqueta'=>'Tipo de Colaborador','required' => ''],$tipos_usuario),
      CreateSelect(['id'=>'kid_internos_externos','etiqueta'=>'Modalidad de Trabajo','required' => ''],$internos_externos),
      CreateSelect(['id'=>'kid_tipo_cantidad','etiqueta'=>'Tipo de Costo','required' => ''],$tipo_cantidad),
      CreateInput(['type'=>'number','id'=>'cantidad_periodo','etiqueta'=>'Cantidad','required' => '']),
      CreateInput(['type'=>'password','id'=>'password1','etiqueta'=>'Contraseña','class'=>'ValidatePWS ValidateSamePWSs','div_clases'=>' login','required' => '']),
      CreateInput(['type'=>'password','id'=>'password2','etiqueta'=>'Reingrese la Contraseña','class'=>'ValidatePWS ValidateSamePWSs','div_clases'=>' login','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'razon_social','etiqueta'=>'Razón Social']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'nombre_comercial','etiqueta'=>'Nombre Comercial']),
      CreateInput(['type'=>'text','maxlength'=>'13','id'=>'rfc','etiqueta'=>'RFC','class'=>'ValidateRFC']),
      CreateInput(['type'=>'text','maxlength'=>'20','id'=>'curp','etiqueta'=>'CURP','class'=>'ValidateCURP']),
      CreateInput(['type'=>'text','maxlength'=>'11','id'=>'numero_imss','etiqueta'=>'Número IMSS']),
      CreateSelect(['id'=>'nombre_pais','etiqueta'=>'País'],$paises),
      CreateSelect(['id'=>'kid_estado','etiqueta'=>'Estado', 'class'=>'DataGET Data-GETMunicipios','bloque'=>'catalogo'],$estados),
      CreateSelect(['id'=>'nombre_municipio','etiqueta'=>'Municipio', 'class'=>'kid_estado'],[]),
      CreateInput(['type'=>'number','id'=>'cp','etiqueta'=>'CP', 'class'=>'DataGET Data-GETColonia','bloque'=>'catalogo']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'nombre_colonia','etiqueta'=>'Colonia', 'class'=>'cp']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'nombre_localidad','etiqueta'=>'Localidad']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'nombre_vialidad','etiqueta'=>'Vialidad']),
      CreateInput(['type'=>'number','id'=>'numero_exterior','etiqueta'=>'Número Exterior']),
      CreateInput(['type'=>'number','id'=>'numero_interior','etiqueta'=>'Número Interior']),
      CreateInput(['type'=>'number','id'=>'telefono1','etiqueta'=>'Teléfono 1','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'number','id'=>'telefono2','etiqueta'=>'Teléfono 2','class'=>'ValidateTelefono']),
      CreateSelect(['id'=>'kid_tipo_contrato','etiqueta'=>'Tipo de Contrato'],$tipo_contrato),
      CreateInput(['type'=>'date','id'=>'fecha_nacimiento','etiqueta'=>'Fecha de Nacimiento']),
      CreateInput(['type'=>'number','id'=>'edad','etiqueta'=>'Edad']),
      CreateSelect(['id'=>'kid_estado_civil','etiqueta'=>'Estado Civil'],$estado_civil),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'nombre_emergencia','etiqueta'=>'Nombre de Contacto de Emergencia']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'parentesco','etiqueta'=>'Parentesco']),
      CreateInput(['type'=>'text','maxlength'=>'11','id'=>'telefono_emergencia','etiqueta'=>'Teléfono de Emergencia','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'email','id'=>'correo_emergencia','etiqueta'=>'Correo de Emergencia','class'=>'ValidateCorreo']),
      CreateInput(['type'=>'date','id'=>'fecha_ingreso','etiqueta'=>'Fecha de Ingreso']),
      CreateInput(['type'=>'date','id'=>'fecha_firma','etiqueta'=>'Fecha de Firma']),
      CreateInput(['type'=>'number','id'=>'sueldo','etiqueta'=>'Sueldo']),
      CreateInput(['type'=>'text','id'=>'sueldo_texto','etiqueta'=>'Sueldo en Texto']),
      CreateInput(['type'=>'text','maxlength'=>'60','id'=>'puesto','etiqueta'=>'Puesto']),
      //CreateSelect(['id'=>'pais','etiqueta'=>'País','readonly' => '','disabled' => ''],$paises),
      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);


    CreateModalForm(
      [
        'id'=> 'colaboradores-changepwd', 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Cambiar Contraseña',
        'Title3'=>'Ver Colaborador',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'talento_humano'
      ],
      [
        CreateInput(['type'=>'email','id'=>'email','etiqueta'=>'Correo Electrónico','class'=>'ValidateCorreo','required' => '','readonly' => '']),
        CreateInput(['type'=>'password','id'=>'password1','etiqueta'=>'Contraseña','class'=>'ValidatePWS ValidateSamePWSs','required' => '']),
        CreateInput(['type'=>'password','id'=>'password2','etiqueta'=>'Reingrese la Contraseña','class'=>'ValidatePWS ValidateSamePWSs','required' => '']),
      ]);
  

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
