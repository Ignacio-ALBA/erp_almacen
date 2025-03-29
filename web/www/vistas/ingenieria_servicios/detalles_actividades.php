<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Contenido de Actividades";
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
  
  $id = 'detalles_actividades';
  $ButtonAddLabel = "Nuevo Contenido de actividad";
  $titulos = ['ID', 'Nombre Tarea','No. Actividad','Proyecto','Personal','Estado','Fecha de Inicio','Fecha de Fin','Días Totales', 'Horas Totales', 'Progreso'];

  $forminputs = [];
  if ($permiso == 3){
    $botones_acciones = 'ButtonsInRow';
  }
  CreateTable($id, $ButtonAddLabel, $titulos, $data,false, 'ButtonsInRow');

  if ($permiso == 1){
    $forminputs = [
      CreateSelect(['id'=>'kid_personal_asignado','etiqueta'=>'Personal en Actividad','required' => '',],[]/*personal*/),
    ];

    CreateModalForm(
      [
        'id'=> $id.'-setpersonal', 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Contenido de actividad',
        'Title3'=>'VerContenido de actividad',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'ingenieria_servicios'
      ],
      $forminputs
      );

    $forminputs = [
      CreateInput(['type'=>'text','maxlength'=>'30','id'=>'actividad','etiqueta'=>'Actividad','required' => '']),
      CreateInput(['type'=>'number','id'=>'cantidad_actividades','etiqueta'=>'Número de Actividades','required' => '']),
      CreateTextArea(['maxlength'=>'300','id'=>'comentario','etiqueta'=>'Comentario',]),
      CreatSwitchCheck(['id'=>'dias_festivos','etiqueta'=>'Se Trabajara en Días Festivos','class'=>'OnHolidaysAllow']),
      CreatSwitchCheck(['id'=>'dia_sabado','etiqueta'=>'Se Trabajara en Dias Sábados','class'=>'OnSaturdayAllow']),
      CreatSwitchCheck(['id'=>'dia_domingo','etiqueta'=>'Se Trabajara en Dias Domingos','class'=>'OnSundayAllow']),
      CreateInput(['type'=>'date','id'=>'fecha_inicial','etiqueta'=>'Fecha Inicial Programada','required' => '', 'class'=>'DateStartCal-1']),
      CreateInput(['type'=>'date','id'=>'fecha_final','etiqueta'=>'Fecha Final Programada','required' => '', 'class'=>'DateEndCal-1']),
      CreateInput(['type'=>'number','id'=>'dias_totales','etiqueta'=>'Días Totales Planeadas','required' => '', 'readonly' => '','class'=>'DateResultCal-1']),
      CreateInput(['type'=>'number','id'=>'horas_totales','etiqueta'=>'Horas Totales Planeadas','required' => '']),
      CreateInput(['type'=>'number','id'=>'grupo_paralelo','etiqueta'=>'Grupo en Paralelo','required' => '']),
      CreateInput(['type'=>'number','id'=>'grupo_seriado','etiqueta'=>'Grupo en Serie','required' => '']),
      CreateInput(['type'=>'number','id'=>'nivel_profundidad','etiqueta'=>'Nivel de Profundidad','required' => ''])
      //CreateSelect(['id'=>'pais','etiqueta'=>'País','readonly' => '','disabled' => ''],$paises),
    ];
  }else if($permiso == 2){
    $forminputs = [
      CreateSelect(['id'=>'kid_personal_asignado','etiqueta'=>'Personal en Actividad','required' => '',],$personal),
    ];
  }
  if($permiso != 2){
    CreateModalForm(
      [
        'id'=> "pausar_reanudar_detalles_actividades", 
        'Title'=>"Pausar Actividad",
        'Title2'=>'Reanudar Actividad',
        'Title3'=>'Ver Contenido de actividad',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'ingenieria_servicios'
      ],
      [
        CreateTextArea(['type'=>'text','maxlength'=>'300','id'=>'justificacion','etiqueta'=>'Justificación']),
        CreateInput(['type'=>'number','id'=>'latitud','etiqueta'=>'Latitud','readonly' => '', 'class'=>'GETLatitud']),
        CreateInput(['type'=>'number','id'=>'longitud','etiqueta'=>'Longitud','readonly' => '', 'class'=>'GETLongitud']),
      ]
      );

      $buttons =[
        CreateButton(['id'=>'limpiarfoto','type'=>'button','etiqueta'=>'<i class="bi bi-trash"></i> Eliminar Foto','class'=>'DeleteFotoDiv btn btn-danger danger'],false),
        CreateButton(['id'=>'fotosbt','type'=>'button','etiqueta'=>'<i class="bi bi-camera"></i> Tomar Foto','class'=>'TomarFotosBT btn btn-info info'],false),
        CreateButton(['id'=>'addfoto','type'=>'button','etiqueta'=>'<i class="bi bi-plus"></i> Agregar Foto','class'=>'AddFotoDiv btn btn-success success'],false)
    ];

      $tab = [
        [
        'titulo'=>'Subir Foto',
        'contenido'=>[
          CreateCardIMG(['id'=>'imgview', 'style'=>'width:100%; height:100%; border-radius: 5px;', 'class'=>'IMGViewerContainer']),
          CreateInput(['type'=>'file','id'=>'selectfile','accept'=>'image/*','etiqueta'=>'Evidencia Fotográfica','class'=>'IMGInputUpdateContainer']),
          GenerateCustomsButtons($buttons,''),
          //CreateInput(['type'=>'text','id'=>'imgfiles','etiqueta'=>'','style'=>'display:none;','div_style'=>'display:none;', 'class'=>'FotoInput','required'=>''])
        ]
        ],
        [
          'titulo'=>'Ver Contenido',
          'contenido'=>[
            CreateCarousel(['title'=>'Fotos Subidas','id'=>'view_fotos', 'class'=>'ViewFotos']),
            CreateTextArea(['maxlength'=>'200','id'=>'comentario','etiqueta'=>'Comentario','required' => ''])
            ]
        ]
      ];
      
      CreateModalForm(
        [
          'id'=> "finalizar_detalles_actividades", 
          'Title'=>"Finalizar Actividad",
          'Title2'=>'Reanudar Actividad',
          'Title3'=>'VerContenido de actividad',
          'ModalType'=>'modal-dialog-scrollable', 
          'method'=>'POST',
          'action'=>'bd/crudSummit.php',
          'bloque'=>'ingenieria_servicios',
          'modalCRUD'=>'finalizar_detalles_actividades'
        ],
        [ CreateCardTab($tab,['style'=>'padding:0;', 'div_style'=>'box-shadow: 0px 0 30px rgba(0,0,0,0); background-color: rgba(0, 0, 0,0);'])]
        );

        $tab = [
          [
          'titulo'=>'Subir Foto',
          'contenido'=>[
            CreateCardIMG(['id'=>'imgview', 'style'=>'width:100%; height:100%; border-radius: 5px;', 'class'=>'IMGViewerContainer']),
            CreateInput(['type'=>'file','id'=>'selectfile','accept'=>'image/*','etiqueta'=>'Evidencia Fotográfica','class'=>'IMGInputUpdateContainer']),
            GenerateCustomsButtons($buttons,''),
            //CreateInput(['type'=>'text','id'=>'imgfiles','etiqueta'=>'','style'=>'display:none;','div_style'=>'display:none;', 'class'=>'FotoInput','required'=>''])
          ]
          ],
          [
            'titulo'=>'Ver Contenido',
            'contenido'=>[
              CreateCarousel(['title'=>'Fotos Subidas','id'=>'view_fotos', 'class'=>'ViewFotos']),
              CreateTextArea(['maxlength'=>'200','id'=>'comentario','etiqueta'=>'Comentario','required' => ''])
              ]
          ]
        ];

        CreateModalForm(
          [
            'id'=> "subir_evidencias", 
            'Title'=>"Subir Evidencias de  Actividad",
            'Title2'=>'Reanudar Actividad',
            'Title3'=>'VerContenido de actividad',
            'ModalType'=>'modal-dialog-scrollable', 
            'method'=>'POST',
            'action'=>'bd/crudSummit.php',
            'bloque'=>'ingenieria_servicios',
            'modalCRUD'=>'subir_evidencias'
          ],
          [ CreateCardTab($tab,['style'=>'padding:0;', 'div_style'=>'box-shadow: 0px 0 30px rgba(0,0,0,0); background-color: rgba(0, 0, 0,0);'])]
          );

    $forminputs = [
      CreateInput(['type'=>'text','maxlength'=>'30','id'=>'actividad','etiqueta'=>'Actividad','disabled' => '']),
      CreateInput(['type'=>'date','id'=>'fecha_inicial','etiqueta'=>'Fecha Inicial Programada','disabled' => '']),
      CreateInput(['type'=>'date','id'=>'fecha_final','etiqueta'=>'Fecha Final Programada','disabled' => '']),
      CreateInput(['type'=>'number','id'=>'dias_totales','etiqueta'=>'Días Totales Planeadas','disabled' => '']),
      CreateInput(['type'=>'number','id'=>'horas_totales','etiqueta'=>'Horas Totales Planeadas','disabled' => '']),
      CreateInput(['type'=>'date','id'=>'fecha_inicial_real','etiqueta'=>'Fecha Inicial Real','disabled' => '']),
      CreateInput(['type'=>'date','id'=>'fecha_final_real','etiqueta'=>'Fecha Final Real','disabled' => '']),
      CreateInput(['type'=>'number','id'=>'dias_totales_reales','etiqueta'=>'Días Totales Reales','disabled' => '']),
      CreateInput(['type'=>'number','id'=>'horas_totales_reales','etiqueta'=>'Horas Totales Reales','disabled' => ''])
      //CreateSelect(['id'=>'pais','etiqueta'=>'País','readonly' => '','disabled' => ''],$paises),
    ];
  }

  if($forminputs){
    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Contenido de actividad',
        'Title3'=>'VerContenido de actividad',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'ingenieria_servicios'
      ],
      $forminputs
      );
  }


  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
