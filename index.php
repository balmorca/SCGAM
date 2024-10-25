<?php
    require 'Config/config.php';
    require 'Config/Conexdb.php';
    require 'clases/invfunciones.php';
    $db = new Database();
    $con = $db->conectar();

    $errors = [];

    if (!empty($_POST)) {
        $usuario = trim($_POST['UserName']);
        $password = trim($_POST['Password']);

        if (esNulo([$password, $usuario])) {
            $errors[] = "Debe llenar todos los campos";
        } else {
            $result = login($usuario, $password, $con);
            if ($result !== true) {
                $errors[] = $result;
            }
        }
    }

   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>.: Inicio de Sesión :.</title>
      <link rel="icon" type="image/x-icon" href="assets/images/AsesoriaGM.ico">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   </head>
   <body>
      <div class="p-5 d-grid" style="background-image: url(assets/images/fondo.jpg); background-color: #ffffff; background-size: cover;
        width: 100vw; height: 100vh; background-position: center; background-repeat: no-repeat; background-size: cover;">
                <div class="position-absolute m-5 top-20 end-0 p-5 border rounded" style="background: #EFEFFB;">
                    <div>
                        <div class="text-center"><br>
                            <a href="#"><img style="height: 70px" src="assets/images/logo-footer.png"></a>
                            <br>
                            <h5 style="color: #000000;"> <strong>[ SCAGM 1.0 ]</strong></h5>
                        </div>
                    </div>
                    <div class="panel-body">
                    <?php mostrarMensajes($errors); ?>
                        <form action="index.php" method="post">
                            <div class="form-group">
                                <label for="UserName" class="control-label" style="color: #000000;" >Indicador</label>
                                <input type="text" name="UserName" class="form-control" id="UserName" placeholder="Indicador" required>
                            </div>
                            <div class="form-group">
                                <label for="Password" class="control-label" style="color: #000000;" >Clave</label>
                                <input type="password" name="Password" class="form-control" id="Password" placeholder="Clave" required>
                            </div><br>
                            <div class="form-group mt-20">
                                <button type="submit" class="btn btn-primary">Ingresar</button>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
        </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


   </body>
</html>