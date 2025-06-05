<?php
ob_start();

$PageSection = "Reporte de Detalles de Recepciones de Materia Prima";
?>

<div class="pagetitle">
  <h1><?php echo $PageSection; ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item">Materia Prima</li>
      <li class="breadcrumb-item active"><?php echo $PageSection; ?></li>
    </ol>
  </nav>
</div>

<!-- Formulario de selección de fechas -->
<div class="card mb-4">
  <div class="card-header"><strong>Filtrar por rango de fechas</strong></div>
  <div class="card-body">
    <form method="get" action="">
      <input type="hidden" name="pathResult" value="reporte_detalles_recepciones_mp">
      <div class="row">
        <div class="col-md-4">
          <label for="fecha_inicio" class="form-label">Fecha inicio</label>
          <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio"
            value="<?php echo isset($fecha_inicio) ? date('Y-m-d\TH:i', strtotime($fecha_inicio)) : date('Y-m-d\T00:00'); ?>">
        </div>
        <div class="col-md-4">
          <label for="fecha_fin" class="form-label">Fecha fin</label>
          <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin"
            value="<?php echo isset($fecha_fin) ? date('Y-m-d\TH:i', strtotime($fecha_fin)) : date('Y-m-d\T23:59'); ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?php echo $error; ?></div>
<?php else: ?>
  <div class="card mb-4">
    <div class="card-header"><strong>Resumen</strong></div>
    <div class="card-body">
      <ul>
        <li><strong>Total de Detalles:</strong> <?php echo $reporte['total_detalles'] ?? 0; ?></li>
        <li><strong>Total Tarimas:</strong> <?php echo $reporte['total_tarimas'] ?? 0; ?></li>
        <li><strong>Total Parets:</strong> <?php echo $reporte['total_parets'] ?? 0; ?></li>
        <li><strong>Peso Real Total:</strong> <?php echo $reporte['total_peso_real'] ?? 0; ?></li>
        <li><strong>Peso Estimado Total:</strong> <?php echo $reporte['total_peso_estimado'] ?? 0; ?></li>
        <li><strong>Diferencia de Peso Total:</strong> <?php echo $reporte['total_diferencia_peso'] ?? 0; ?></li>
      </ul>
    </div>
  </div>

  <?php
    $id = 'detalles_recepciones_mp';
    $ButtonAddLabel = "Nuevo Detalle";
    $titulos = [
      'ID', 'Artículo', 'Tarimas', 'Parets', 'Peso Real', 'Peso Estimado', 'Diferencia Peso',
      'Costo Unitario Total', 'Costo Unitario Neto', 'Monto Total', 'Monto Neto', 'Fecha de creación'
    ];
    CreateTable($id, $ButtonAddLabel, $titulos, $data, false, $botones_acciones);
  ?>
<?php endif; ?>

<?php
$wrapper_dashboard = ob_get_clean();
include 'wrapper.php';
?>



