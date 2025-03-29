<?php
    ob_start();
?>
<div class="pagetitle">
    <h1>Cambiar Contraseña</h1>
    <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Dashboard</li>
        <li class="breadcrumb-item active" >Cambiar Contraseña</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <br>
                    <p>De 8 a 15 caracteres con al menos una letra en minúscula, una letra en mayúscula, un dígito y un caracter especial</p>        
                    <form id="changepass" method="post" action="/vistas/dashboard/bd/cambiarpassword.php" onsubmit="return verificarPasswords()" class="needs-validation" novalidate>
                        <?php 
                        echo CreateInput(['type'=>'email','id'=>'usuario','etiqueta'=>'Colaborador','value'=>$_SESSION["s_usuario"],'class'=>'ValidateCorre','disabled' => '']);
                        echo CreateInput(['type'=>'password','id'=>'password1','name'=>"password1",'etiqueta'=>'Contraseña','class'=>'ValidatePWS ValidateSamePWSs','required' => '']);
                        echo'<br>';
                        echo CreateInput(['type'=>'password','id'=>'password2','name'=>"password2",'etiqueta'=>'Vuelve a escribir la Contraseña','class'=>'ValidatePWS ValidateSamePWSs','required' => '']);
                        echo'<br>';
                        ?>
                        <button type="submit" id="login" class="btn btn-primary">Cambiar contraseña</button>
                    </form>
                </div>
            </div> 
        </div> 
    </div>       
</section>
<?php
  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>