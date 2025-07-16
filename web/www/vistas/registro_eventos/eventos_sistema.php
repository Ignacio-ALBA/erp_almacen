<?php
ob_start(); // Inicia la captura del buffer de salida

$consultaselect = "
                    SELECT id, c.email, l.fecha_creacion, modulo, evento
                    FROM log  l 
                    inner join colaboradores c on l.usuario_id = c.id_colaborador 
                    order by id desc";

$resultado = $conexion->prepare($consultaselect);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

$PageSection = "Eventos de Sistema";
?>


<div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item">Registro de eventos</li>
            <li class="breadcrumb-item active"><?php echo $PageSection; ?></li>
        </ol>
    </nav>
</div><!-- End Page Title -->
<?php

$id = 'eventos_sistema';
$ButtonAddLabel = "Nuevo registro";
$titulos = ['ID', 'Email', 'Fecha Evento', 'Modulo', 'Descripcion'];
CreateTable($id, $ButtonAddLabel, $titulos, $data, true, []);
CreateModalForm(
    [
        'id' => $id,
        'Title' => $ButtonAddLabel,
        'Title2' => 'Editar registro',
        'Title3' => 'Ver registro',
        'ModalType' => 'modal-dialog-centered',
        'method' => 'POST',
        'action' => 'bd/crudSummit.php',
        'bloque' => 'catalogo'
    ],
    [
        CreateInput(['type' => 'text', 'id' => 'email', 'etiqueta' => 'Email', 'required' => '']),
        CreateInput(['type' => 'text', 'id' => 'evento', 'etiqueta' => 'Evento', 'required' => '']),
        CreateInput(['type' => 'datetime-local', 'id' => 'fecha_creacion', 'etiqueta' => 'Fecha de creación', 'required' => ''])
    ]);
$wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content
include 'wrapper.php'; // Incluye el wrapper
?>
