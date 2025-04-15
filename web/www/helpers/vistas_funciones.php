<?php 
function validateSession() {
    session_start();
    if (!isset($_SESSION["s_usuario"]) || empty($_SESSION["s_usuario"])) {
        header("Location: /index.php");
        exit();
    }
}

function render404View() {
    $viewFile = '../errores/404.php'; 
    include($viewFile);
}

function renderView($viewName, $params = []) {
    global $conexion;
    $carpeta = rtrim(basename($_SERVER['SCRIPT_NAME'], '.php'), '/');

    if (isset($params['data_show'])) {
        extract($params['data_show']);
    }
    
    $viewFile = '../vistas/'.$carpeta .'/'. $viewName . ".php"; 
    //echo $viewFile;
    //debug($viewFile);
    if (file_exists($viewFile)) {
        include($viewFile);
    } else {
        render404View();
    }
}


function RenderMenuLateral($items, $baseId, $level, $route = '') {
    global $_SESSION;
    // Comienza el contenedor del menú
    $show_element = GetSection();
    foreach ($items as $item) {
        //debug($item);
        //debug(array_intersect($items['permiso'], $_SESSION["permisos"]));
        if(isset($item['permiso']) && !in_array("all",$_SESSION["permisos"]) && empty(array_intersect($items['permiso'], $_SESSION["permisos"]))){
            continue;
        }else if(isset($item['subitems'])){
            $count_parts = 0;
            foreach ($item['subitems'] as $subitem) {
                if(isset($subitem['permiso']) && !in_array( "all",$_SESSION["permisos"]) && empty(array_intersect($subitem['permiso'], $_SESSION["permisos"]))){
                    $count_parts++;
                }
            }
            if(isset($subitem['permiso']) && $count_parts >= count($item['subitems'])){continue;}

        }
        
        $is_collapsed = 'collapsed';
        $is_show = '';
        if(isset($item['route']) && $item['route'] == $show_element['scriptName']){
            $is_collapsed = '';
            $is_show = 'show';
        }else if(isset($item['href']) && $item['href'] == $show_element['scriptName']){
            $is_collapsed = '';
            $is_show = 'show';
        }

        if($level < 1){
            // Renderiza el elemento principal
            echo '<li class="nav-item">';
            echo '<a class="nav-link '.$is_collapsed.'" data-bs-target="#' . str_replace(' ', '_', $item['label']). $baseId . $level
            .(isset($item['href']) ? ' href="' . $item['route']. $item['href'] . '"' : ''). '" '.($is_collapsed == '' ? '' : 'toggle="collapse"').' data-bs-toggle="collapse"  aria-expanded="'.($is_collapsed == '' ? 'true' : 'false').'">';
            echo '<i class="' . (isset($item['icon']) ? $item['icon'] : 'bi bi-circle') . '"></i><span>' . $item['label'] . '</span>';
            if (!empty($item['subitems'])) {echo '<i class="bi bi-chevron-down ms-auto"></i>';}
            echo '</a>';
        }
        

        // Verifica si hay subelementos y los renderiza
        if (!empty($item['subitems'])) {
            
            echo '<ul class="collapse '. $is_show.'" id="' . str_replace(' ', '_', $item['label']). $baseId . $level . '">';
            // Llama a la función recursivamente para renderizar subelementos
            foreach ($item['subitems'] as $subitem) {
                
                if(isset($subitem['permiso']) && !in_array("all",$_SESSION["permisos"]) && empty(array_intersect($subitem['permiso'], $_SESSION["permisos"]))){
                    continue;
                }
                
                $is_collapsed = 'collapsed';
                $is_show = '';
                if(isset($subitem['route']) && $subitem['route'] == $show_element['scriptName']){
                    $matchingHrefs = array_column(array_filter($subitem['subitems'], function($subitemValue) use ($show_element) {
                        return isset($subitemValue['href']) && $subitemValue['href'] == $show_element['pathResult'];
                    }), 'href');
                    if($matchingHrefs && $matchingHrefs[0] == $show_element['pathResult']){
                        $is_collapsed = '';
                        $is_show = 'show';
                    }
                }
                // Renderiza solo el enlace del subelemento sin el <li>
                echo '<li class="nav-item">';
                echo '<a class="nav-link '.$is_collapsed.'"'
                . (isset($subitem['href']) ? ' href="' . $item['route']. $subitem['href'] . '"' : '')
                . (isset($subitem['subitems']) && !empty($subitem['subitems']) ? ' data-bs-target="#' . str_replace(' ', '_', $subitem['label']) . $baseId . $level . '" data-bs-toggle="collapse"' : '')
                . '>';
                echo '<i class="' . (isset($subitem['icon']) ? $subitem['icon'] : 'bi bi-dash-square') . '"></i><span>' . $subitem['label'] . '</span>';
                if (!empty($subitem['subitems'])) {echo '<i class="bi bi-chevron-down ms-auto"></i>';}
                echo '</a>';
                
                // Si hay más subelementos anidados, llama a RenderMenu de nuevo
                if (!empty($subitem['subitems'])) {
                    echo '<ul class="nav-content collapse '.$is_show.'" id="' .str_replace(' ', '_', $subitem['label']). $baseId . $level . '">';
                    RenderMenuLateral($subitem['subitems'], $baseId . $level, $level + 1,$item['route']);
                    echo '</ul>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }else{
            echo '<li class="nav-item">';
            echo '<a class="nav-link collapsed"'
            . (isset($item['href']) ? ' href="' . $route. $item['href'] . '"' : '')
            . (isset($item['subitems']) && !empty($item['subitems']) ? ' data-bs-target="#' . $item['label'] . $baseId . $level . '" data-bs-toggle="collapse"' : '')
            . '>';
            echo '<i class="' . (isset($item['icon']) ? $item['icon'] : 'bi bi-circle') . '"></i><span>' . $item['label'] . '</span>';
            echo '</a>';
            echo '</li>';
            
        }

        echo '</li><!-- End Forms Nav -->'; // Cierra el <li> del elemento principal
    }
}

function RenderMenu2($items,$name, $id, $depth = 0) {
    // Calcular margen izquierdo según la profundidad
    $marginLeft = $depth * 20; // Incremento de 20px por nivel de profundidad

    echo '<ul id="' .$name . $id . '" class="nav-content collapse" data-bs-parent="#sidebar-nav' . $id . '" style="margin-left: ' . $marginLeft . 'px;">';
    
    foreach ($items as $item) {
        $id += 1;
        echo '<li>';

        // Verificar si el href no está vacío
        if (!empty($item['href'])) {
            echo '<a href="' . $item['href'] . '">';
            echo '<i class="bi bi-circle"></i><span>' . $item['label'] . '</span>';
            echo '</a>';
        } else if (!empty($item['subitems'])) {
            echo '<a class="nav-link collapsed" data-bs-target="#'.$name . $id . '" data-bs-toggle="collapse" href="#" aria-expanded="true">';
            echo '<i class="bi bi-circle"></i><span>'. $item['label'].'</span>';
            echo '<div class="bi bi-chevron-down ms-auto"></div>';
            echo '</a>';
            RenderMenu2($item['subitems'],$name, $id, $depth + 1); // Llamada recursiva con profundidad incrementada
        } else {
            echo '<a class="nav-link' . $id . '" data-bs-target="#'.$name . $id . '" data-bs-toggle="collapse" href="#" aria-expanded="true">';
            echo '<i class="bi bi-circle"></i><span>' . $item['label'] . '</span>';
            echo '</a>';
        }

        echo '</li>';
        
    }
    echo '</ul>';
}

function processRequest()
{
    //validateSession();
    // Regenerar ID de sesión para prevenir fijación de sesión
    session_regenerate_id(true);

    // Validar la sesión del usuario
    

    // Obtener la URL actual
    $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
    $scriptName = $_SERVER['SCRIPT_NAME'] . '/';
    $position = strpos($currentUrl, $scriptName);
    $pathResult = '';
    $queryParams = [];

    // Procesar la ruta si existe
    if ($position !== false) {
        $pathResult = substr($currentUrl, $position + strlen($scriptName));
        $pathParts = explode('?', $pathResult, 2);
        $pathResult = $pathParts[0];

        if (isset($pathParts[1])) {
            parse_str($pathParts[1], $queryParams);
        }
    }

    // Sanitizar la entrada del pathResult
    $pathResult = filter_var($pathResult, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validar que los resultados no estén vacíos
    if ($pathResult === '' && empty($queryParams)) {
        return false;
    }

    return [
        'pathResult' => $pathResult,
        'queryParams' => $queryParams
    ];
}

function GetSection()
{
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
    $position = strpos($currentUrl, $scriptName);
    $pathResult = '';

    if ($position !== false) {
        $pathResult = substr($currentUrl, $position + strlen($scriptName));
        $pathResult = filter_var($pathResult, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    return [
        'pathResult' => $pathResult,
        'scriptName' => $scriptName
    ];
}
function GetTiemposEntregaForSelect() {
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();
    
    try {
        $consultaselect = "SELECT 
            id_tiempo_entrega as valor,
            tiempo_entrega as text 
            FROM tiempos_entrega 
            WHERE kid_estatus = 1 
            ORDER BY tiempo_entrega ASC";
        
        $resultado = $conexion->prepare($consultaselect);
        $resultado->execute();
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

function GetTiposPagoForSelect() {
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();
    
    try {
        $consultaselect = "SELECT 
            id_tipo_pago as valor,
            tipo_pago as text 
            FROM tipos_pago 
            WHERE kid_estatus = 1 
            ORDER BY tipo_pago ASC";
        
        $resultado = $conexion->prepare($consultaselect);
        $resultado->execute();
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}
function checkPerms($perms, $iscomponent = false) {
    $perms = is_array($perms) ? $perms : [$perms];
    if (in_array("all", $_SESSION["permisos"]) || !empty(array_intersect($perms, $_SESSION["permisos"]))) {
        return true;
    }
    return $iscomponent ? false : header("Location: /index.php");
}


?>