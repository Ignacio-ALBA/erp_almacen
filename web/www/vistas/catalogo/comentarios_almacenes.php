<?php
    ob_start(); // Inicia la captura del buffer de salida

    $consultaselect = "SELECT ca.id_comentario_almacen, 
                            a.almacen AS kid_almacen, 
                            ar.articulo,
                            ca.comentario_almacen,
                            tc.tipo_comentario AS kid_tipo_comentario,
                            ca.fecha_creacion
                      FROM 
                          comentarios_almacenes ca
                      LEFT JOIN 
                          almacenes a ON ca.kid_almacen = a.id_almacen
                      LEFT JOIN 
                          detalles_almacenes da ON ca.kid_detalle_almacen = da.id_detalle_almacen
                      LEFT JOIN 
                          articulos ar ON da.kid_articulo = ar.id_articulo
                      LEFT JOIN 
                          tipos_comentarios tc ON ca.kid_tipo_comentario = tc.id_tipo_comentario 
                      WHERE 
                          ca.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);


    $tipo_comentario = GetTiposComentariosListForSelect();


    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Comentarios de Almacén";
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

  $id = 'comentarios_almacenes';
  $ButtonAddLabel = "Nuevo Comentario";
  $titulos = ['ID','Almacén','Detalle de Almacén','Comentario', 'Tipo de Comentario','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,false, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Comentario',
      'Title3'=>'Ver Comentario',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['id'=>'kid_almacen','etiqueta'=>'Almacén','disabled' => '']),
      CreateInput(['id'=>'kid_detalle_almacen','etiqueta'=>'Detalle Almacén','disabled' => '']),
      CreateInput(['type'=>'text','id'=>'comentario_almacen','etiqueta'=>'Comentario','required' => '']),
      CreateSelect(['id'=>'kid_tipo_comentario','etiqueta'=>'Detalle Almacén','disabled' => ''],$tipo_comentario)

      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
