<?php
session_start();

if(isset($_SESSION["s_usuario"])){
    header("Location: rutas/dashboard.php/dashboard");
}

//include_once 'bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

//$consulta = "SELECT * FROM tipos_usuario WHERE activo=1 AND login=1";
$consulta = "SELECT * FROM tipos_usuario WHERE kid_estatus=1 AND login=1";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$tipos_usuario=$resultado->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>ALBA ERP</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

<main>
  <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel" style="position:absolute; top:0; left:0; z-index:0;">
    <div class="carousel-inner">
    <?php
      $imagenes = glob('assets/img/imagenes_login/*.{jpg,png}', GLOB_BRACE);
      // Variable para controlar la clase 'active'
      $active = 'active';
      
      // Iterar sobre las imágenes y crear los items del carousel
      foreach ($imagenes as $imagen) {
          echo '<div class="carousel-item ' . $active . '" style="width:100vw; height:100vh;">';
          echo '<img src="' . $imagen . '" class="d-block w-100" alt="..." width="100%" height="100%" style="filter: blur(1rem);">';
          echo '</div>';
          // Solo la primera imagen debe tener la clase 'active'
          $active = '';
      }
    ?>
    </div>
  </div>

    <div class="container" style="position:relative; z-index:1;">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center" style="background-color: #F6F9FF; padding:20px; border-radius:10px;">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logoi.JPG" alt="">
                  <img src="assets/img/logot2.JPG" alt="">
                  <!-- <span class="d-none d-lg-block">NiceAdmin</span> -->
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Ingresa a tu cuenta</h5>
                    <p class="text-center small">Ingresa usuario y contraseña</p>
                  </div>

                  <form class="row g-3 needs-validation" id="formLogin" action="" method="post">

                    <div class="col-12">
                      <div class="input-group has-validation">
                        <input class="form-control" type="text" id="usuario" name="usuario" placeholder="Usuario" require>
                        <span class="focus-efecto"></span>
                        <div class="invalid-feedback">Ingresa tu usuario.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <input class="form-control" type="password" id="password" name="password" placeholder="Contraseña" require>
                      <span class="focus-efecto"></span>
                      <div class="invalid-feedback">Ingresa tu contraseña</div>
                    </div>

                    <div class="col-12">
                        <select id="id_tipoUsuario" name="id_tipoUsuario" class="form-control" required>
                          <option value="" disabled selected required>Seleccione un tipo</option>
                          <?php foreach ($tipos_usuario as $tipo): ?>
                            <option value="<?php echo $tipo["id_tipo_usuario"]; ?>"><?php echo $tipo["tipo_usuario"]; ?></option>
                          <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Recuérdame</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">¿No tienes cuenta? <a href="pages-register.html">Crear una cuenta</a></p>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                Diseñado por <a href="">ALBA-DTI S. de R.L. de C.V.</a>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="jquery/jquery-3.3.1.min.js"></script>    
  <script src="popper/popper.min.js"></script>    
  <script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>   

  <!-- Template Main JS File -->
</body>

<script src="assets/js/main.js"></script>

<script>
    $('#formLogin').submit(function(e){
        e.preventDefault();
        var usuario = $.trim($("#usuario").val());    
        var password =$.trim($("#password").val());    
        var id_tipoUsuario = $.trim($('#id_tipoUsuario').val());
        

        if(id_tipoUsuario == ""){
          console.log("id_tipoUsuario"); // Para verificar el valor
          console.log(id_tipoUsuario); // Para verificar el valor
          return false;
        }
        
        if(usuario.length == "" || password == ""){
          Swal.fire({
              type:'warning',
              title:'Debe ingresar un usuario y/o password',
          });
        return false; 
        }else{
            $.ajax({
            url:"bd/login.php",
            type:"POST",
            dataType: "json",
            data: {usuario:usuario, password:password, id_tipoUsuario:id_tipoUsuario}, 
            success:function(data){             
                if(data != null){
                    Swal.fire({
                        type:'success',
                        title:'¡Conexión exitosa!',
                        confirmButtonColor:'#3085d6',
                        confirmButtonText:'Ingresar'
                    }).then((result) => {
                        if(result.value){
                            //window.location.href = "vistas/pag_inicio.php";
                            /*if(id_tipoUsuario==3){
                                window.location.href = "dashboard/dashboard_admin.php";
                            }else if(id_tipoUsuario==7){
                                window.location.href = "dashboard/dashboard_plan.php";
                            }else{
                                window.location.href = "dashboard/dashboard_default.php";
                            }*/
                            window.location.href = "rutas/dashboard.php/dashboard";
                        }
                    })
                }else{
                  Swal.fire({
                        type:'error',
                        title:'Colaborador, tipo y/o password incorrecta',
                    });

                    
                }
            }    
            });
        }     
    });
</script>
</html>