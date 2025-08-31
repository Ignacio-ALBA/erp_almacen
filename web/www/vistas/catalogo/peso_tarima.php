<?php
ob_start(); // Inicia la captura del buffer de salida

$consultaselect = "SELECT c.id, 
                          c.descripcion, 
                          CONCAT(c.valor, ' kg') AS valor,
                          c.orden,
                          CASE WHEN c.defecto = 1 THEN 'Activado' ELSE 'Desactivado' END AS defecto
                   FROM pesos_tarimas c
                   WHERE c.kid_estatus = 1";


$resultado = $conexion->prepare($consultaselect);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

$PageSection = "Peso de Tarimas";
?>

<div class="pagetitle">
  <h1><?php echo $PageSection; ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item">Catálogo</li>
      <li class="breadcrumb-item active"><?php echo $PageSection; ?></li>
    </ol>
  </nav>
</div>

<?php
$id = 'pesos_tarimas';
$ButtonAddLabel = "Nueva Tarima";
$titulos = ['ID', 'Nombre', 'Peso', 'Orden', 'Por defecto'];

CreateTable($id, $ButtonAddLabel, $titulos, $data, true, []);
CreateModalForm(
  [
    'id' => $id,
    'Title' => $ButtonAddLabel,
    'Title2' => 'Editar Tarima',
    'Title3' => 'Ver Tarima',
    'ModalType' => 'modal-dialog-centered',
    'method' => 'POST',
    'action' => 'bd/crudSummit.php',
    'bloque' => 'catalogo'
  ],
  [
    CreateInput(['type'=>'text','id'=>'descripcion','etiqueta'=>'Nombre','required' => '']),
    CreateInput(['type'=>'number','id'=>'valor','etiqueta'=>'Peso de tarima','required' => '']),
    CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
    CreatSwitchCheck(['id'=>'defecto','etiqueta'=>'Por defecto'])
    
  ]
);

$wrapper_dashboard = ob_get_clean();
include 'wrapper.php';
?>

<!-- Aquí metemos el script de acciones -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("formpesos_tarimas");

    if (form) {
      form.addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append("modalCRUD", "pesos_tarimas");
        formData.append("opcion", 1);
        formData.append("formDataJson", JSON.stringify({
    descripcion: document.getElementById("descripcion").value,
    valor: document.getElementById("valor").value,
    orden: document.getElementById("orden").value,
    defecto: document.getElementById("defecto").checked ? 1 : 0
}));
        formData.append("AlertDataSimilar", "false");

        fetch("bd/crudSummit.php", {
            method: "POST",
            body: formData
          })
          .then(resp => resp.json())
          .then(data => console.log("✅ Respuesta:", data))
          .catch(err => console.error("❌ Error:", err));

      });
    }
  });

  document.addEventListener("click", function(e) {
  const btn = e.target.closest("button");
  if (!btn) return;

  const modalCRUD = btn.getAttribute("modalCRUD");
  const row = btn.closest("tr");
  const idValue = row ? row.querySelector("td").innerText : null; // la primera columna (ID)

  // --- Ver ---
  if (btn.classList.contains("ModalDataView")) {
    fetch("bd/crudEndpoint.php", {
      method: "POST",
      body: new URLSearchParams({
        modalCRUD: modalCRUD,
        firstColumnValue: idValue
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.status === "success") {
        // llenar modal en modo solo lectura
        document.getElementById("descripcion").value = data.data.descripcion;
        document.getElementById("valor").value = data.data.valor;
        document.getElementById("orden").value = data.data.orden;
        document.getElementById("defecto").checked = data.data.defecto == 1;

        // deshabilitar inputs para ver
        ["descripcion","valor","orden","defecto"].forEach(id => {
          document.getElementById(id).setAttribute("disabled","disabled");
        });

        document.getElementById("modalTitle1pesos_tarimas").style.display = "none";
        document.getElementById("modalTitle2pesos_tarimas").style.display = "none";
        document.getElementById("modalTitle3pesos_tarimas").style.display = "block";

        const modalEl = document.getElementById("modalCRUDpesos_tarimas");
        new bootstrap.Modal(modalEl).show();
      }
    });
  }

  // --- Editar ---
  if (btn.classList.contains("ModalDataEdit")) {
    fetch("bd/crudEndpoint.php", {
      method: "POST",
      body: new URLSearchParams({
        modalCRUD: modalCRUD,
        firstColumnValue: idValue
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.status === "success") {
        // llenar modal en modo edición
        document.getElementById("descripcion").value = data.data.descripcion;
        document.getElementById("valor").value = data.data.valor;
        document.getElementById("orden").value = data.data.orden;
        document.getElementById("defecto").checked = data.data.defecto == 1;

        // habilitar inputs
        ["descripcion","valor","orden","defecto"].forEach(id => {
          document.getElementById(id).removeAttribute("disabled");
        });

        document.getElementById("modalTitle1pesos_tarimas").style.display = "none";
        document.getElementById("modalTitle2pesos_tarimas").style.display = "block";
        document.getElementById("modalTitle3pesos_tarimas").style.display = "none";

        const modalEl = document.getElementById("modalCRUDpesos_tarimas");
        new bootstrap.Modal(modalEl).show();
      }
    });
  }

  // --- Eliminar ---
  if (btn.classList.contains("ModalDataDelete")) {
    if (confirm("¿Seguro que deseas eliminar esta tarima?")) {
      const formData = new FormData();
      formData.append("modalCRUD","pesos_tarimas");
      formData.append("opcion",3);
      formData.append("firstColumnValue", idValue);

      fetch("bd/crudSummit.php", { method:"POST", body:formData })
        .then(r=>r.json())
        .then(data=>{
          if(data.status==="success"){
            alert("Eliminado correctamente");
            $('#tabla_pesos_tarimas').DataTable().ajax.reload();
          } else {
            alert("Error al eliminar: " + data.message);
          }
        });
    }
  }
});
</script>