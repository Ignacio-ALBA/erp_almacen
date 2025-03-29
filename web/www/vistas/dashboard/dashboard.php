<?php
ob_start(); // Inicia la captura del buffer de salida
$PageSection = "Dashboard";
?>

<div class="pagetitle">
  <h1><?php echo $PageSection; ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Inicio</a></li>
      <li class="breadcrumb-item active"><?php echo $PageSection; ?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">

    <!-- Left side columns -->
    <div class="col-lg-8">
      <div class="row">

        <!-- Sales Card -->
        <div class="col-xxl-4 col-md-6">
          <div class="card info-card sales-card">
            <!--
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>-->

            <div class="card-body">
              <h5 class="card-title">Proyectos Activos<span>| Este mes</span></h5>

              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="ri ri-folder-add-fill"></i>
                </div>
                <div class="ps-3">
                  <h6><?php echo $numero_proyectos ?></h6>
                  <!--<span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">incremento</span>-->

                </div>
              </div>
            </div>

          </div>
        </div><!-- End Sales Card -->

        <!-- Revenue Card -->
        <div class="col-xxl-4 col-md-6">
          <div class="card info-card revenue-card">

            <!--<div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>-->

            <div class="card-body">
              <h5 class="card-title">Ganancia <span>|Este mes</span></h5>

              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="ps-3">
                  <h6>$3,264</h6>
                  <span class="text-success small pt-1 fw-bold">15%</span> <span class="text-muted small pt-2 ps-1">incremento</span>

                </div>
              </div>
            </div>

          </div>
        </div><!-- End Revenue Card -->
        

        <!-- Customers Card -->
        <div class="col-xxl-4 col-xl-12">

          <div class="card info-card customers-card">

            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">Clientes Activos<span>|Este año</span></h5>

              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                  <h6><?php echo $clientes_activos ?></h6>
                  <!--<span class="text-danger small pt-1 fw-bold">20%</span> <span class="text-muted small pt-2 ps-1">decremento</span>-->

                </div>
              </div>

            </div>
          </div>

        </div><!-- End Customers Card -->

        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Cuentas con mas gastos <span>| En este mes</span></h5>

              <!-- Progress Bars with Striped Backgrounds-->
              <span>Manuel Badillo Moreno:</span>
              <div style="display: flex; justify-content:space-between; align-items:center;">
                <div class="progress col-10">
                  <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">$500</div>
                </div>
                <div class="col-2"> $ 2000</div>
              </div>
              <span>Sandra Ponce:</span>
              <div style="display: flex; justify-content:space-between; align-items:center;">
                <div class="progress col-10">
                  <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 19.04%" aria-valuenow="19.04" aria-valuemin="0" aria-valuemax="100">$800</div>
                </div>
                <div class="col-2"> $ 4200</div>
              </div>
              <span>Aleida Badillo:</span>
              <div style="display: flex; justify-content:space-between; align-items:center;">
                <div class="progress col-10">
                  <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 3%" aria-valuenow="3" aria-valuemin="0" aria-valuemax="100">$600</div>
                </div>
                <div class="col-2"> $ 20000</div>
              </div>

            </div>
          </div>
        </div>

        <!-- Proveedores -->
        <div class="col-12">
          <div class="card overflow-auto">

            <div class="card-body">
              <h5 class="card-title">Proveedores</h5>

              <?php 
              $id = 'proveedores';
              $titulos = ['ID','Proveedores','Calificación'];
              $botones_acciones = ['<button class="ModalDataView btn btn-primary primary" modalCRUD="proveedores"><i class="bi bi-eye"></i> Ver</button>'];
              CreateTableNotSection($id, false, $titulos, $data_proveedores, false, $botones_acciones);

              CreateModalForm(
                [
                  'id'=> $id, 
                  'Title'=>'',
                  'Title2'=>'Editar Proveedor',
                  'Title3'=>'Ver Proveedor',
                  'ModalType'=>'modal-dialog-scrollable', 
                  'method'=>'POST',
                  'action'=>'bd/crudSummit.php',
                  'bloque'=>'compras'
                ],
                [
                  CreateInput(['type'=>'text','maxlength'=>'16','id'=>'codigo','etiqueta'=>'Código','required' => '']),
                  CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
                  CreateInput(['type'=>'text','maxlength'=>'100','id'=>'proveedor','etiqueta'=>'Categoría','required' => '']),
                  CreateInput(['type'=>'text','maxlength'=>'100','id'=>'razon_social','etiqueta'=>'Razón Social','required' => '']),
                  CreateInput(['type'=>'text','maxlength'=>'100','id'=>'nombre_comercial','etiqueta'=>'Nombre Comercial','required' => '']),
                  CreateInput(['type'=>'text','maxlength'=>'13','id'=>'rfc','etiqueta'=>'RFC','class'=>'ValidateRFC','required' => '']),
                  CreateInput(['type'=>'text','maxlength'=>'50','id'=>'nombre','etiqueta'=>'Nombre','required' => '']),
                  CreateInput(['type'=>'text','maxlength'=>'50','id'=>'apellido_paterno','etiqueta'=>'Apellido Paterno','required' => '']),
                  CreateInput(['type'=>'text','maxlength'=>'50','id'=>'apellido_materno','etiqueta'=>'Apellido Materno','required' => '']),
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
                  CreateSelect(['id'=>'calificacion','etiqueta'=>'Calificación'],[['valor'=>6,'pordefecto'=>1],['valor'=>5,'pordefecto'=>1],['valor'=>4,'pordefecto'=>0],['valor'=>3,'pordefecto'=>0],['valor'=>2,'pordefecto'=>0],['valor'=>1,'pordefecto'=>0]]),
                  //CreateSelect(['id'=>'pais','etiqueta'=>'País','readonly' => '','disabled' => ''],$paises),
                  CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
                  
                ]);
              ?>

            </div>
          </div>
        </div><!-- End Recent Sales -->

          <!-- Proveedores -->
          <div class="col-12">
          <div class="card overflow-auto">

            <div class="card-body">
              <h5 class="card-title">Comentarios de Proveedores</h5>

              <?php 

              $botones_acciones = ['<button class="ModalDataView btn btn-primary primary" modalCRUD="comentarios_proveedores"><i class="bi bi-eye"></i> Ver</button>'];
              $id = 'comentarios_proveedores';
              $ButtonAddLabel = "Nuevo Comentario";
              $titulos = ['ID','Proveedor','Comentario', 'Tipo de Comentario','Fecha de creación'];
              CreateTable($id, $ButtonAddLabel, $titulos, $data_query_proveedor_comentarios,false,  $botones_acciones);
              CreateModalForm(
                [
                  'id'=> $id, 
                  'Title'=>$ButtonAddLabel,
                  'Title2'=>'Editar Comentario',
                  'Title3'=>'Ver Comentario',
                  'ModalType'=>'modal-dialog-scrollable', 
                  'method'=>'POST',
                  'action'=>'bd/crudSummit.php',
                  'bloque'=>'compras'
                ],
                [
                  CreateSelect(['id'=>'kid_proveedor','etiqueta'=>'Proveedor','disabled' => '','class'=>'OnEditReadOnly'],$proveedores),
                  CreateTextArea(['type'=>'text', 'maxlength'=>'300','id'=>'comentario_proveedor','etiqueta'=>'Comentario','required' => '']),
                  CreateSelect(['id'=>'kid_tipo_comentario','etiqueta'=>'Tipo de Comentario','disabled' => ''],$tipo_comentario)
            
                  //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
                  
                ]);
              ?>

            </div>
          </div>
        </div><!-- End Recent Sales -->

        <!-- Cotizaciones -->
        <div class="col-12">
          <div class="card overflow-auto">

            <div class="card-body">
              <h5 class="card-title">Cotizaciones</h5>

              <?php 
                $id = 'cotizaciones_compras';
                $botones_acciones = ['<button class="ModalDataView btn btn-primary primary" modalCRUD="cotizaciones_compras"><i class="bi bi-eye"></i> Ver</button>'];
                $titulos = ['ID', 'Cotización','Proveedor','Estado'];
                CreateTableNotSection($id, false, $titulos, $data_cotizaciones,false,$botones_acciones);
                CreateModalForm(
                  [
                    'id'=> $id, 
                    'Title'=>'',
                    'Title2'=>'Editar Cotización',
                    'Title3'=>'Ver Cotización',
                    'ModalType'=>'modal-dialog-scrollable', 
                    'method'=>'POST',
                    'action'=>'bd/crudSummit.php',
                    'bloque'=>'compras'
                  ],
                  [
                    CreateInput(['type'=>'text','id'=>'cotizacion_compras','etiqueta'=>'Cotización','required' => '']),
                    CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','class'=>'OnEditReadOnly DataGET Data-GETArticulosProyecto'],$proyectos),
                    CreateSelect(['id'=>'kid_proveedor','etiqueta'=>'Proveedor','required' => ''],$proveedores),
                    CreateSelect(['id'=>'kid_estatus','etiqueta'=>'Estado','div_style'=>'display:none;','class'=>'OnlyInEdit'],$estatus)
                  ])
              ?>

            </div>
          </div>
        </div>

        <!-- Compras -->
        <div class="col-12">
          <div class="card overflow-auto">

            <div class="card-body">
              <h5 class="card-title">Compras</h5>

              <?php 
              $botones_acciones = ['<button class="ModalDataView btn btn-primary primary" modalCRUD="ordenes_compras"><i class="bi bi-eye"></i> Ver</button>'];
                $id = 'ordenes_compras';
                $titulos = ['ID', 'Orden de Compra','Código Externo','Proveedor'];
                CreateTableNotSection($id, false, $titulos, $data_compras,false,$botones_acciones);
                CreateModalForm(
                  [
                    'id'=> $id, 
                    'Title'=>'',
                    'Title2'=>'Editar Orden de Compra',
                    'Title3'=>'Ver Orden de Compra',
                    'ModalType'=>'modal-dialog-scrollable', 
                    'method'=>'POST',
                    'action'=>'bd/crudSummit.php',
                    'bloque'=>'compras'
                  ],
                  [
                    CreateInput(['type'=>'text','maxlength'=>'100','id'=>'orden_compras','etiqueta'=>'Orden de Compras','required' => '']),
                    CreateInput(['type'=>'text','maxlength'=>'80','id'=>'codigo_externo','etiqueta'=>'Código Externo','required' => '']),
                    CreateInput(['type'=>'text','id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','readonly' => '','class'=>'OnEditReadOnly']),
                    CreateInput(['type'=>'text','id'=>'kid_proveedor','etiqueta'=>'Proveedor','required' => '','readonly' => '','class'=>'OnEditReadOnly']),
                    CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '']),
                    CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly' => '']),
                    CreateSelect(['id'=>'kid_estatus','etiqueta'=>'Estado','div_style'=>'display:none;','class'=>'OnlyInEdit'],$estatus),
                    //CreateButton(['id'=>'button_aceptar_recepcion','etiqueta'=>'Confirmar Recepción','modalCRUD'=>'ordenes_compras','op'=>1,'class'=>'OnlyInEdit btn btn-primary primary GhangeEstatus'])
                  ]);
              ?>

            </div>
          </div>
        </div>


        <!-- Compras -->
        <div class="col-12">
          <div class="card overflow-auto">

            <div class="card-body">
              <h5 class="card-title">Recepciones</h5>

              <?php 
              $botones_acciones = ['<button class="ModalDataView btn btn-primary primary" modalCRUD="recepciones_compras"><i class="bi bi-eye"></i> Ver</button>'];
              $id = 'recepciones_compras';
              $ButtonAddLabel = "Nuevo recepción";
              $titulos = ['ID', 'Recepción','Proveedor','Almacén'];
              CreateTableNotSection($id, false, $titulos, $data_recepciones, false, $botones_acciones);
              CreateModalForm(
                [
                  'id'=> $id, 
                  'Title'=>'',
                  'Title2'=>'Editar recepción',
                  'Title3'=>'Ver Lista',
                  'ModalType'=>'modal-dialog-scrollable', 
                  'method'=>'POST',
                  'action'=>'bd/crudSummit.php',
                  'bloque'=>'compras'
                ],
                [
                  CreateInput(['type'=>'text','maxlength'=>'100','id'=>'recepcion_compras','etiqueta'=>'Recepción','required' => '']),
                  CreateInput(['type'=>'text','maxlength'=>'100','id'=>'codigo_externo','etiqueta'=>'Código Externo','required' => '']),
                  CreateSelect(['type'=>'text','id'=>'kid_proyecto','etiqueta'=>'Proyecto','readonly' => ''],$proyectos),
                  CreateInput(['type'=>'text','id'=>'kid_proveedor','etiqueta'=>'Proveedor','readonly' => '']),
                  CreateInput(['type'=>'text','id'=>'kid_orden_compras','etiqueta'=>'Orden de Compra','readonly' => '']),
                  CreateSelect(['type'=>'text','id'=>'kid_almacen','etiqueta'=>'Almacén','readonly' => ''],$almacenes),
                  CreateSelect(['id'=>'kid_recibe','etiqueta'=>'Recibió','required' => ''],$colaboradores),
                  CreateSelect(['id'=>'kid_reclama','etiqueta'=>'Reclamo','required' => ''],$colaboradores),
                  CreateSelect(['id'=>'kid_regresa','etiqueta'=>'Regreso ','required' => ''],$colaboradores),
                  CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total']),
                  CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto']),
                ]);
              ?>

            </div>
          </div>
        </div>

        <!-- Top Selling -->
        <div class="col-12">
          <div class="card top-selling overflow-auto">

            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filtro</h6>
                </li>

                <li><a class="dropdown-item" href="#">Hoy</a></li>
                <li><a class="dropdown-item" href="#">Este mes</a></li>
                <li><a class="dropdown-item" href="#">Este año</a></li>
              </ul>
            </div>

          </div>
        </div><!-- End Top Selling -->

      </div>
    </div><!-- End Left side columns -->

    <div class="col-lg-4">

          <!-- Recent Activity -->
          <div class="card">

            <div class="card-body">
              <h5 class="card-title">Actividades <span>| Hoy</span></h5>

              <div class="activity">

              <?php 

              if(count($data_actividades_dia)<1){
                echo '<p class="text-center">No hay actividades para hoy</p>';
              }else{
                foreach($data_actividades_dia as $data){

                  $class = 'warning';
                  if($data['kid_estatus'] == 10){$class = 'success';}else if($data['kid_estatus'] == 8){$class = 'danger';}else if($data['kid_estatus'] == 8){$class = 'muted';}
                  //debug($data);
                  echo '
                    <div class="activity-item d-flex">
                      <div class="activite-label">'.$data['horas_totales']-$data['horas_totales_reales'].' hrs</div>
                      <i class="bi bi-circle-fill activity-badge text-'.$class.' align-self-start"></i>
                      <div class="activity-content">
                        '.$data['actividad'].'
                      </div>
                  </div>
                  ';
                }
              }
              
              ?>
              </div>

            </div>
          </div><!-- End Recent Activity -->


          <!-- Recent Persosal con mas de 80 % de completado -->
          <div class="card">

            <div class="card-body">
              <h5 class="card-title">Personal con actividades completadas <span>| mas de 80%</span></h5>

              <div class="activity">

              <?php 

              if(count($data_personal80)<1){
                echo '<p class="text-center">No hay personal mas de de 80% de actividades finalizadas</p>';
              }else{
                foreach($data_personal80 as $data){

                  //debug($data);
                  echo '
                    <div class="activity-item d-flex">
                      <div class="activite-label">'.$data['porcentaje_finalizado'].' %</div>
                      <i class="bi bi-circle-fill activity-badge text-success align-self-start"></i>
                      <div class="activity-content">
                        '.$data['nombre_completo'].'
                      </div>
                  </div>
                  ';
                }
              }
              
              ?>
              </div>

            </div>
          </div><!-- End Recent Activity -->

          <!-- Accesos ditrectos -->
          <div class="card">
            

            <div class="card-body pb-0">
              <h5 class="card-title">Acesos Directos</h5>

              <?php 
                echo '<div class="btn-group" role="group" aria-label="Basic example" style="margin-bottom: 10px; width:100%;">'
                .CreateButton(['id'=>'btactividades','etiqueta'=>'Actividades', 'class'=>'btn btn-primary'],false)
                .CreateButton(['id'=>'btclientes','etiqueta'=>'Clientes', 'class'=>'btn btn-outline-primary'],false)
                .'</div>';

                echo '<div class="btn-group" role="group" aria-label="Basic example" style="margin-bottom: 10px; width:100%;">'
                .CreateButton(['id'=>'btcomentariosproveedores','etiqueta'=>'Comentarios de Proveedores', 'class'=>'btn btn-primary'],false)
                .CreateButton(['id'=>'btproveedores','etiqueta'=>'Proveedores', 'class'=>'btn btn-outline-primary'],false)
                .'</div> ';

                echo '<div class="btn-group" role="group" aria-label="Basic example" style="margin-bottom: 10px; width:100%;">'
                .CreateButton(['id'=>'btproyectos','etiqueta'=>'Proyectos', 'class'=>'btn btn-primary'],false)
                .CreateButton(['id'=>'btcuentas','etiqueta'=>'Cuentas', 'class'=>'btn btn-outline-primary'],false)
                .'</div>';
              ?>
              
            </div>
          </div><!-- End Budget Report -->

          <!-- News & Updates Traffic -->
          <div class="card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filtro</h6>
                </li>

                <li><a class="dropdown-item" href="#">Hoy</a></li>
                <li><a class="dropdown-item" href="#">Este mes</a></li>
                <li><a class="dropdown-item" href="#">Este año</a></li>
              </ul>
            </div>

            <div class="card-body pb-0">
              <h5 class="card-title">Noticias &amp; Actualizaciones <span>| Hoy</span></h5>

              <div class="news">
                <div class="post-item clearfix">
                  <img src="assets/img/news-1.jpg" alt="">
                  <h4><a href="#">Kick-off de proyecto 5</a></h4>
                  <p>Iniciando proyecto en Puebla</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-2.jpg" alt="">
                  <h4><a href="#">Rentando generador</a></h4>
                  <p>Envío de generador a estación en Tlaxcala</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-3.jpg" alt="">
                  <h4><a href="#">Mantenimiento en Amozoc</a></h4>
                  <p>En nave industrial 1</p>
                </div>

                <!-- <div class="post-item clearfix">
                  <img src="assets/img/news-4.jpg" alt="">
                  <h4><a href="#">Laborum corporis quo dara net para</a></h4>
                  <p>Qui enim quia optio. Eligendi aut asperiores enim repellendusvel rerum cuder...</p>
                </div>

                <div class="post-item clearfix">
                  <img src="assets/img/news-5.jpg" alt="">
                  <h4><a href="#">Et dolores corrupti quae illo quod dolor</a></h4>
                  <p>Odit ut eveniet modi reiciendis. Atque cupiditate libero beatae dignissimos eius...</p>
                </div> -->

              </div><!-- End sidebar recent posts-->

            </div>
          </div><!-- End News & Updates -->

        </div>
  </div>
</section>



<?php
    $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

    include 'wrapper.php'; // Incluye el wrapper
?>
