<?php
    ob_start(); // Inicia la captura del buffer de salida
    

    $consultaselect = "SELECT s.id_sucursal , 
                          s.sucursal, 
                          s.razon_social, 
                          s.rfc, 
                          CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS kid_propietario,
                          CONCAT(u2.nombre, ' ', u2.apellido_paterno, ' ', u2.apellido_materno) AS kid_representante_legal,
                          CONCAT(u3.nombre, ' ', u3.apellido_paterno, ' ', u3.apellido_materno) AS kid_representante_tecnico,
                          CONCAT(u4.nombre, ' ', u4.apellido_paterno, ' ', u4.apellido_materno) AS kid_representante_administrativo,
                          s.fecha_creacion
                      FROM 
                          sucursales s
                      LEFT JOIN 
                          colaboradores u ON s.kid_propietario = u.id_colaborador 
                      LEFT JOIN 
                          colaboradores u2 ON s.kid_representante_legal = u2.id_colaborador 
                      LEFT JOIN 
                          colaboradores u3 ON s.kid_representante_tecnico = u3.id_colaborador 
                      LEFT JOIN 
                          colaboradores u4 ON s.kid_representante_administrativo = u4.id_colaborador
                      WHERE 
                          s.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT email, nombre,apellido_paterno, apellido_materno  FROM colaboradores WHERE kid_estatus = 1";

    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $colaboradores = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT regimen, descripcion  FROM regimenes_fiscales WHERE kid_estatus = 1 ORDER BY pordefecto DESC, orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $regimenes = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT pais FROM paises WHERE kid_estatus = 1 ORDER BY pordefecto DESC, orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $paises = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT e.estado,p.simbolo ,e.pordefecto  FROM estados e JOIN paises p ON e.kid_pais = p.id_pais WHERE e.kid_estatus = 1 ORDER BY e.pordefecto DESC, e.orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $estados = $resultado->fetchAll(PDO::FETCH_ASSOC);
    // Transformar el array a un formato donde las claves son 'id_categoria' y los valores son 'categoria'

    $colaboradores = array_map(fn($item) => [
      'valor'=> $item['email'],
      'text' => trim(implode(' ', array_filter([$item['nombre'], $item['apellido_paterno'], $item['apellido_materno']]))),
      'pordefecto' => 0
    ], $colaboradores);


    $regimenes = array_map(fn($item) => [
      'valor'=> $item['regimen'],
      'text' => trim(implode('-', array_filter([$item['regimen'], $item['descripcion']]))),
      'pordefecto' => 0
    ], $regimenes);

    $paises = array_map(fn($item) => [
      'valor'=> $item['pais'],
      'pordefecto' => 0
    ], $paises);

    $estados = array_map(fn($item) => [
      'valor'=> $item['estado'],
      'text' => trim(implode('-', array_filter([$item['estado'], $item['simbolo']]))),
      'pordefecto' => $item['pordefecto'],
    ], $estados);
    
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Sucursales";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Catálogo</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'sucursales';
  $ButtonAddLabel = "Nueva Sucursal";
  $titulos = ['ID', 'Sucursal','Razon Social','RFC', 'Propietario','Representante Legal','Representante Técnico','Representante Administrativo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Sucursal',
      'Title3'=>'Ver Sucursal',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'sucursal','etiqueta'=>'Sucursal','required' => '']),
      CreateInput(['type'=>'text','id'=>'razon_social','etiqueta'=>'Razon Social']),
      CreateInput(['type'=>'text','id'=>'rfc','etiqueta'=>'RFC','class'=>'ValidateRFC']),
      CreateSelect(['id'=>'regimen_capital','etiqueta'=>'Régimen Fiscal'],$regimenes),
      CreateInput(['type'=>'text','id'=>'nombre_comercial','etiqueta'=>'Nombre Comercial']),
      CreateInput(['type'=>'date','id'=>'fecha_inicio_operaciones','etiqueta'=>'Fecha Inicio de Operaciones']),
      //CreateSelect(['id'=>'estatus_padron','etiqueta'=>'Estado del Padron']),
      CreateSelect(['id'=>'nombre_pais','etiqueta'=>'País'],$paises),
      CreateSelect(['id'=>'kid_estado','etiqueta'=>'Estado', 'class'=>'DataGET Data-GETMunicipios','bloque'=>'catalogo'],$estados),
      CreateSelect(['id'=>'nombre_municipio','etiqueta'=>'Municipio', 'class'=>'DataGET kid_estado'],[]),
      CreateInput(['type'=>'number','id'=>'cp','etiqueta'=>'CP', 'class'=>'DataGET Data-GETColonia','bloque'=>'catalogo']),
      CreateInput(['type'=>'text','id'=>'nombre_colonia','etiqueta'=>'Colonia', 'class'=>'cp']),
      CreateInput(['type'=>'text','id'=>'nombre_localidad','etiqueta'=>'Localidad']),
      CreateInput(['type'=>'text','id'=>'nombre_vialidad','etiqueta'=>'Vialidad']),
      CreateInput(['type'=>'number','id'=>'numero_exterior','etiqueta'=>'Número Exterior']),
      CreateInput(['type'=>'number','id'=>'numero_interior','etiqueta'=>'Número Interior']),
      CreateInput(['type'=>'number','id'=>'longitud','etiqueta'=>'Longitud']),
      CreateInput(['type'=>'number','id'=>'latitud','etiqueta'=>'Latitud']),
      //CreateInput(['type'=>'text','id'=>'maps','etiqueta'=>'Mapas']),
      CreateInput(['type'=>'text','id'=>'referencias','etiqueta'=>'Referencias']),
      CreateInput(['type'=>'number','id'=>'telefono_contacto','etiqueta'=>'Teléfono de Contacto','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'number','id'=>'celular_contacto','etiqueta'=>'Celular de Contacto','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'email','id'=>'email_contacto','etiqueta'=>'Correo de Contacto','class'=>'ValidateCorreo']),
      CreateSelect(['id'=>'kid_propietario','etiqueta'=>'Propietario'],$colaboradores),
      //Representante Legal
      CreateSelect(['id'=>'kid_representante_legal','etiqueta'=>'Representante Legal','class'=>'VerificarCambioMostrar'],$colaboradores),
      CreateInput(['div_style'=>'display:none;','type'=>'number','id'=>'telefono_representante_legal','etiqueta'=>'Teléfono Representante Legal','class'=>'ValidateTelefono', 'div_clases'=>' kid_representante_legal']),
      CreateInput(['div_style'=>'display:none;','type'=>'number','id'=>'celular_representante_legal','etiqueta'=>'Celular Representante Legal','class'=>'ValidateTelefono', 'div_clases'=>' kid_representante_legal']),
      CreateInput(['div_style'=>'display:none;','type'=>'email','id'=>'email_representante_legal','etiqueta'=>'Correo Representante Legal','class'=>'ValidateCorreo', 'div_clases'=>' kid_representante_legal']),
      //Representante Técnico
      CreateSelect(['id'=>'kid_representante_tecnico','etiqueta'=>'Representante Técnico','class'=>'VerificarCambioMostrar'],$colaboradores),
      CreateInput(['div_style'=>'display:none;','type'=>'number','id'=>'telefono_representante_tecnico','etiqueta'=>'Teléfono Representante Técnico','class'=>'ValidateTelefono', 'div_clases'=>' kid_representante_tecnico']),
      CreateInput(['div_style'=>'display:none;','type'=>'number','id'=>'celular_representante_tecnico','etiqueta'=>'Celular Representante Técnico','class'=>'ValidateTelefono', 'div_clases'=>' kid_representante_tecnico']),
      CreateInput(['div_style'=>'display:none;','type'=>'email','id'=>'email_representante_tecnico','etiqueta'=>'Correo Representante Técnico','class'=>'ValidateCorreo', 'div_clases'=>' kid_representante_tecnico']),
      //Representante Administrativo
      CreateSelect(['id'=>'kid_representante_administrativo','etiqueta'=>'Representante Administrativo','class'=>'VerificarCambioMostrar'],$colaboradores),
      CreateInput(['div_style'=>'display:none;','type'=>'number','id'=>'telefono_representante_administrativo','etiqueta'=>'Teléfono Representante Administrativo','class'=>'ValidateTelefono', 'div_clases'=>' kid_representante_administrativo']),
      CreateInput(['div_style'=>'display:none;','type'=>'number','id'=>'celular_representante_administrativo','etiqueta'=>'Celular Representante Administrativo','class'=>'ValidateTelefono', 'div_clases'=>' kid_representante_administrativo']),
      CreateInput(['div_style'=>'display:none;','type'=>'email','id'=>'email_representante_administrativo','etiqueta'=>'Correo Representante Administrativo','class'=>'ValidateCorreo', 'div_clases'=>' kid_representante_administrativo']),
      //CreateSelect(['id'=>'pais','etiqueta'=>'País','readonly' => '','disabled' => ''],$paises),
      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
