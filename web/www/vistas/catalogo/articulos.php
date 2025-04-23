<?php
  ob_start(); // Inicia la captura del buffer de salida
  $consultaselect = "SELECT a.id_articulo  , 
                        a.codigo_interno, 
                        a.articulo, 
                        c.categoria as kid_categoria,
                        p.presentacion as kid_presentacion,
                        d.dimension as kid_dimension,
                        a.fecha_creacion
                  FROM articulos a
                  LEFT JOIN categorias c ON a.kid_categoria = c.id_categoria
                  LEFT JOIN presentaciones p ON a.kid_presentacion = p.id_presentacion 
                  LEFT JOIN dimensiones d ON a.kid_dimension = d.id_dimension  -- Suponiendo que tienes una relación con la tabla dimensiones
                  WHERE a.kid_estatus = 1";

  $resultado = $conexion->prepare($consultaselect);
  $resultado->execute();
  $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

  $marcas = GetMarcasListForSelect();
  $categorias = GetCategoriasListForSelect();
  $subcategorias  = GetSubcategoriasListForSelect();
  $formatos = GetFormatosListForSelect();
  $presentaciones = GetPresentacionesListForSelect();
  $dimensiones = GetDimensionesListForSelect(); 
  $PageSection = "Artículos";
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

  $id = 'articulos';
  $ButtonAddLabel = "Nuevo Artículo";
  $titulos = ['ID', 'Código Interno','Artículo', 'Marca','Categoría','Subcategoria','Cantidad','Formato','Presentación','Dimensión','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Artículo',1
      'Title3'=>'Ver Artículo',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'codigo_interno','etiqueta'=>'Código interno','required' => '']),
      //CreateInput(['type'=>'text','id'=>'codigo_externo','etiqueta'=>'Código externo','required' => '']),
      CreateInput(['type'=>'text','id'=>'articulo','etiqueta'=>'Artículo','required' => '']),
      CreateSelect(['id'=>'kid_marca','etiqueta'=>'Marca'],$marcas),
      CreateInput(['type'=>'text','id'=>'no_serie','etiqueta'=>'Número de Serie','required' => '']),
      CreateInput(['type'=>'text','id'=>'modelo','etiqueta'=>'Modelo','required' => '']),
     // CreateButton(['type'=>'button','id'=>'new_kid_marca','etiqueta'=>'Nueva Marca', 'modalCRUD'=>'marcas', 'class'=>'btn btn-secondary DataAdd']),
     /* CreateModalinModal(
        [
          'id'=> "marcas", 
          'Title'=>'Nueva Marca',
          'Title2'=>'Editar Marca',
          'method'=>'POST',
          'action'=>'bd/crudSummit.php',
          'bloque'=>'catalogo'
        ],
        [
          CreateInput(['type'=>'text','id'=>'marca','etiqueta'=>'Marca','required' => '']),
          CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
          CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
        ]),*/
      CreateSelect(['id'=>'kid_categoria','etiqueta'=>'Categoria','required' => ''],$categorias),
      CreateSelect(['id'=>'kid_subcategoria','etiqueta'=>'Subcategoria','required' => ''],$subcategorias),
      CreateSelect(['id'=>'kid_formato','etiqueta'=>'Formato','required' => ''],$formatos),
      CreateInput(['type'=>'text','id'=>'cantidad_formato','etiqueta'=>'Cantidad de Formato','required' => '']),
      CreateSelect(['id'=>'kid_presentacion','etiqueta'=>'Presentación','required' => ''],$presentaciones),
      CreateSelect(['id'=>'kid_dimension','etiqueta'=>'Dimensión','required' => ''],$dimensiones)
    ]);

    

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
