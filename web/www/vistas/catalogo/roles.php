<?php
ob_start(); // Inicia la captura del buffer de salida

$permisos = [
  "Planeación"=> [
    "label" => "Catalogo",
    "onlysuperadmin" => false,
    "sub_permisos" => [
    ]
  ]
];

include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

//Real

$consulta = "SELECT id_tipo_usuario, tipo_usuario, id_estatus, CASE WHEN login = 1 THEN 'Activado' ELSE 'Desactivado' END AS estado_login FROM tipos_usuario";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);
?>

  <div class="pagetitle">
    <h1>Roles</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Catalogo</li>
        <li class="breadcrumb-item active">Roles</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

<?php 

  $id = 'roles';
  $ButtonAddLabel = "Nuevo Rol";
  $titulos = ['ID', 'Tipo Colaborador', 'Estado', 'En Login',];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=> $ButtonAddLabel,
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'/'
    ],
    [
      CreateInput(['type'=>'text','id'=>'marca','etiqueta'=>'Marca','required' => '']),
      CreateSelect(['type'=>'text','id'=>'id_estatus','etiqueta'=>'Estatus','required' => ''],[2=>'dos'])
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>