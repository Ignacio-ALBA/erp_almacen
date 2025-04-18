<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Tipos de Colaboradores";
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

  $id = 'tipos_usuario';
  $ButtonAddLabel = "Nuevo Tipo de Colaborador";
  $titulos = ['ID', 'Tipo','Descripción','Por Defecto','En Login','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, true,$botones_acciones);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Tipo de Adicional',
      'Title3'=>'Ver Tipo de Adicional',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'talento_humano'
    ],
    [
      CreateInput(['type'=>'text','id'=>'tipo_usuario','etiqueta'=>'Tipo de Colaborador','required' => '']),
      CreateInput(['type'=>'text','id'=>'descripcion','etiqueta'=>'Descripción','required' => '']),
      CreatSwitchCheck(['id'=>'login','etiqueta'=>'Permitido en el Inicio de Sesión']),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
    ]);

    $accordion_padre = [];
    foreach($lista_permisos as $modulos){
      $accordions_hijos = [] ;
      foreach($modulos['tablas'] as $tabla){
        //debug($tabla);
        $accordions_hijos[] = ['title'=>'Tabla '.$tabla['tabla'],
        'body'=>CreateList(
          [
            'input'=>'',
            'type'=>'checkbox'
          ],
          $tabla['permisos']
        )];
        debug($tabla);
      }
      

      $accordion_padre[] = [
        'title'=>'Modulo '.$modulos['nombre'],
        'body'=>CreateAccordion(
          [
            'id'=>str_replace(' ', '', $modulos['nombre']),
            'class'=>'accordion-flush']
            ,$accordions_hijos
            )];
    }


    $id = 'asignar_permisos';
    CreateModalForm(
      [
        'id'=>$id, 
        'Title'=>'',
        'Title2'=>'Asignar Permisos',
        'Title3'=>'',
        'ModalType'=>'modal-dialog-centered', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'talento_humano'
      ],
      [
        CreateAccordion(['id'=> $id],$accordion_padre)
      ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
