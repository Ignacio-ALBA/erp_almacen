<?php
    ob_start();
    
    $consultaselect = "SELECT e.id_estados  , 
                          e.orden, 
                          e.estado, 
                          e.simbolo, 
                          CASE 
                              WHEN e.pordefecto = 1 THEN 'SÍ' 
                              ELSE 'NO' 
                          END AS pordefecto,
                          p.pais as kid_pais,  -- Ahora esta columna está después de pordefecto
                          e.fecha_creacion
                    FROM estados e
                    JOIN paises p ON e.kid_pais = p.id_pais 
                    WHERE e.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT pais,pordefecto FROM paises WHERE kid_estatus = 1 ORDER BY pordefecto DESC, orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $paises = $resultado->fetchAll(PDO::FETCH_ASSOC);
    // Transformar el array a un formato donde las claves son 'id_categoria' y los valores son 'categoria'
    $paises = array_map(fn($item) => [
      'valor' => $item['pais'],
      'pordefecto' => $item['pordefecto']
    ], $paises);
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Tiempo real";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Tiempo real</li>
        <li class="breadcrumb-item active"><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'tiempo_real';
  $ButtonAddLabel = "Nueva materia prima";
  $titulos = ['ID', 'Orden','Estado', 'Símbolo','Por Defecto','País','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Materia Prima',
      'Title3'=>'Ver Materia Prima',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'tiempo_real'
    ],
    [
      CreateInput(['type'=>'text','id'=>'estado','etiqueta'=>'Estado','required' => '']),
      CreateInput(['type'=>'text','id'=>'simbolo','etiqueta'=>'Símbolo','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreateSelect(['id'=>'kid_pais','etiqueta'=>'País','required' => ''],$paises),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean();
    include 'wrapper.php';
?>

<!-- Replace these lines -->
<link rel="stylesheet" href="/assets/plugins/leaflet/leaflet.css" />
<script src="/assets/plugins/leaflet/leaflet.js" nonce="<?php echo $_SESSION['nonce']; ?>"></script>
<script src="/assets/js/tiempo-real/mapa.js" nonce="<?php echo $_SESSION['nonce']; ?>"></script>

<style>
    #map {
        height: 500px;
        width: 100%;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .delivery-info {
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
</style>

<div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item">Tiempo real</li>
            <li class="breadcrumb-item active"><?php echo $PageSection; ?></li>
        </ol>
    </nav>
</div>

<!-- Add delivery summary cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Pedidos en Ruta</h5>
                <h2 class="card-text text-primary">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Rutas Activas</h5>
                <h2 class="card-text text-success">0</h2>
            </div>
        </div>
    </div>
</div>

<!-- Add map container -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Mapa de Entregas en Tiempo Real</h5>
        <div id="map"></div>
    </div>
</div>

<!-- Initialize the map -->
<script>
    // Initialize map centered in Mexico City
    const map = L.map('map').setView([19.4326, -99.1332], 5);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Example function to add a new delivery marker
    function addDeliveryMarker(lat, lng, info) {
        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(info);
    }

    // Example function to draw a route between two points
    function drawRoute(startLat, startLng, endLat, endLng) {
        const points = [
            [startLat, startLng],
            [endLat, endLng]
        ];
        L.polyline(points, {
            color: 'blue',
            weight: 3,
            opacity: 0.7
        }).addTo(map);
    }

    // Example: Add some test markers
    addDeliveryMarker(19.4326, -99.1332, 'Pedido #001 - En camino');
    addDeliveryMarker(20.6597, -103.3496, 'Pedido #002 - En ruta');

    // Example: Draw a test route
    drawRoute(19.4326, -99.1332, 20.6597, -103.3496);
</script>
<!-- Remove the inline script block at the bottom of the file -->
