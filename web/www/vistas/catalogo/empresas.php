<?php
    ob_start(); // Inicia la captura del buffer de salida
    
    $consultaselect = "SELECT e.id_empresa, 
                          e.empresa, 
                          e.razon_social, 
                          e.rfc, 
                          CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS kid_propietario,
                          CONCAT(u2.nombre, ' ', u2.apellido_paterno, ' ', u2.apellido_materno) AS kid_representante_legal,
                          CONCAT(u3.nombre, ' ', u3.apellido_paterno, ' ', u3.apellido_materno) AS kid_representante_tecnico,
                          CONCAT(u4.nombre, ' ', u4.apellido_paterno, ' ', u4.apellido_materno) AS kid_representante_administrativo,
                          e.fecha_creacion
                      FROM 
                          empresas e
                      LEFT JOIN 
                          colaboradores u ON e.kid_propietario = u.id_colaborador 
                      LEFT JOIN 
                          colaboradores u2 ON e.kid_representante_legal = u2.id_colaborador 
                      LEFT JOIN 
                          colaboradores u3 ON e.kid_representante_tecnico = u3.id_colaborador 
                      LEFT JOIN 
                          colaboradores u4 ON e.kid_representante_administrativo = u4.id_colaborador
                      WHERE 
                          e.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT email, nombre,apellido_paterno, apellido_materno  FROM colaboradores WHERE kid_estatus = 1";

    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $colaboradores = $resultado->fetchAll(PDO::FETCH_ASSOC);
    // Transformar el array a un formato donde las claves son 'id_categoria' y los valores son 'categoria'

    $colaboradores = array_map(fn($item) => [
      'valor'=> $item['email'],
      'text' => trim(implode(' ', array_filter([$item['nombre'], $item['apellido_paterno'], $item['apellido_materno']]))),
      'pordefecto' => 0
    ], $colaboradores);
    
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Empresas";
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

  $id = 'empresas';
  $ButtonAddLabel = "Nueva Empresa";
  $titulos = ['ID', 'Empresa','Razon Social','RFC', 'Propietario','Representante Legal','Representante Técnico','Representante Administrativo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Empresa',
      'Title3'=>'Ver Empresa',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'empresa','etiqueta'=>'Empresa','required' => '']),
      CreateInput(['type'=>'text','id'=>'razon_social','etiqueta'=>'Razon Social']),
      CreateInput(['type'=>'text','id'=>'rfc','etiqueta'=>'RFC','class'=>'ValidateRFC']),
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
