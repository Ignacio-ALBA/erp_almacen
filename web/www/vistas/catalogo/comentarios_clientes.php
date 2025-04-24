<?php
    ob_start(); // Inicia la captura del buffer de salida

    $consultaselect = "SELECT cc.id_comentario_cliente, 
                            c.nombre AS kid_cliente, 
                            cc.comentario_cliente,
                            tc.tipo_comentario AS kid_tipo_comentario,
                            cc.fecha_creacion
                      FROM 
                          comentarios_clientes cc
                      LEFT JOIN 
                          clientes c ON cc.kid_cliente = c.id_cliente
                      LEFT JOIN 
                          tipos_comentarios tc ON cc.kid_tipo_comentario = tc.id_tipo_comentario 
                      WHERE 
                          cc.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);


    $tipo_comentario = GetTiposComentariosListForSelect();


    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Comentarios de Clientes";
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

  $id = 'comentarios_clientes';
  $ButtonAddLabel = "Nuevo Comentario";
  $titulos = ['ID','Cliente','Comentario de cliente', 'Tipo de Comentario','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
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
      CreateSelect(['id'=>'kid_cliente','etiqueta'=>'Cliente','disabled' => ''],$clientes),
      CreateInput(['type'=>'text','id'=>'comentario_cliente','etiqueta'=>'Comentario','required' => '']),
      CreateSelect(['id'=>'kid_tipo_comentario','etiqueta'=>'Tipo de comentario','disabled' => ''],$tipo_comentario)

      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
