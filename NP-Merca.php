<?php
    require 'Config/Conexdb.php';
    require 'Config/config.php';
    require 'clases/invfunciones.php';
    $db = new Database();
    $con = $db ->conectar();
    $errors = [];
    $NameUser = $_SESSION["user_nametify"];
    // Preparando a consulta con bindparam para evitar inserción de SQL
    $sql = "SELECT a.empresas_autoriz, e.nombre_corto, e.id_interno, a.NombreCompleto AS user_name
            FROM admin a
            INNER JOIN tblempresa e ON JSON_SEARCH(a.empresas_autoriz, 'one', e.nombre_corto) IS NOT NULL
            WHERE a.NombreCompleto = :user_name";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':user_name', $NameUser, PDO::PARAM_STR);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $errors = [];
    if(!empty($_POST)){
        $ip_mac = trim($_POST['ip_mac']);
        $etiqueta = trim($_POST['etiqueta']);
        $serial = trim($_POST['serial']);
        $modelo = trim($_POST['modelo']);
        $marca = trim($_POST['marca']);
        $ubicacion = trim($_POST['ubicacion']);
        $estado = trim($_POST['estado']);
        $descrip = trim($_POST['descrip']);
        $tipo_activo = trim($_POST['tipo_activo']);
        if(esNulo([$tipo_activo, $descrip, $estado, $etiqueta, $serial, $modelo, $marca, $ip_mac, $ubicacion])){
        $errors[] = "Debe llenar todos los campos";
        }
        if(count($errors) == 0) {
            $id = EntradaMercancia([$tipo_activo, $descrip, $estado, $etiqueta, $serial, $modelo, $marca, $ip_mac, $ubicacion], $con);
            header('Location: NP-Merca.php');
            exit;
        } else {
        $errors[] = "Error al Ingresar Nueva Mercancía";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>SCGAM</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include "header.php"; ?>
            <!-- Modal Nuevo Producto -->
            <div class="modal fade" id="NuevoProducto" tabindex="-1" aria-labelledby="NuevoProductoLabel" data-bs-backdrop="static" aria-hidden="true">
                <div class="modal-dialog modal-lg"">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5 h3 mb-0 text-gray-800" id="NuevoProductoLabel">Nuevo Movimiento De Mercancia</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div id="layoutSidenav_content">
                                    <div class="container-fluid px-1 mt-1">
                                        <div class="card mb-1">
                                            <div class="card-body">
                                                <?php mostrarMensajes($errors); ?>
                                                <form action="NP-Merca.php" method="POST" autocomplete="off">
                                                    <div class="form-group">
                                                        <label for="date" class=" control-label">Seleccione Tipo de Activo:</label>
                                                        <select name="tipo_activo" class="form-control" id="default" required="required">
                                                            <option value="" selected disabled>Seleccione Tipo de Activo</option>
                                                            <option value="Telecomunicaciones">Telecomunicaciones </option>
                                                            <option value="Automatizacion">Automatización</option>
                                                            <option value="Produccion">Producción</option>
                                                            <option value="Logistica">Lógistica</option>


                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="default" class="control-label">Descripción:</label>
                                                        <input type="text" name="descrip" class="form-control" id="default" placeholder="Ingrese los datos" required="required">
                                                    </div>                                 
                                                    <div class="form-group">
                                                        <label for="date" class=" control-label">Estatus del Equipo:</label>
                                                        <select name="estado" class="form-control" id="default" required="required">
                                                            <option value="" selected disabled>Seleccione Estatus del Equipo </option>
                                                            <option value="Nuevo">Nuevo </option>
                                                            <option value="Asignado">Asignado</option>
                                                            <option value="En Deposito">En Deposito</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="default" class="control-label">Etiqueta:</label>
                                                        <input type="text" name="etiqueta" class="form-control" id="default" placeholder="Ingrese los datos" required="required">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="default" class="control-label">Serial:</label>
                                                        <input type="text" name="serial" class="form-control" id="default" placeholder="Ingrese los datos" required="required">
                                                    </div>
                                                        <div class="form-group">
                                                        <label for="default" class="control-label">Modelo:</label>
                                                        <input type="text" name="modelo" class="form-control" id="default" placeholder="Ingrese los datos" required="required">
                                                    </div>
                                                        <div class="form-group">
                                                        <label for="default" class="control-label">Marca:</label>
                                                        <input type="text" name="marca" class="form-control" id="default" placeholder="Ingrese los datos" required="required">
                                                    </div>
                                                        <div class="form-group">
                                                        <label for="default" class="control-label">Dirección IP O MAC:</label>
                                                        <input type="text" name="ip_mac" class="form-control" id="default" placeholder="Ingrese los datos" required="required">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="date" class=" control-label">Seleccione Ubicación:</label>
                                                        <select name="ubicacion" class="form-control" id="default" required="required">
                                                            <option value="" selected disabled>Seleccione Ubicación</option>
                                                            <option value="ASIGNADO">Asignado</option>
                                                            <option value="ALMARCA">Almarca</option>
                                                            <option value="ASESORIA GM">Asesoria GM</option>
                                                            <option value="BALMORCA">Balmorca</option>
                                                            <option value="GADO-1">Gado 1</option>
                                                            <option value="INVCOALMAR">Inv. Coalmar</option>
                                                            <option value="HARISPESCA">Haripesca</option>
                                                        </select>
                                                    </div>
                                                    <div class="modal-footer form-group">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                        <button type="submit" name="submit" class="btn btn-primary">Guardar</button>
                                                        <button type="reset" name="reset" class="btn btn-danger">Limpiar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin Del Modal Nuevo Producto -->
        <section class="section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">

                        <div class="panel">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h5>Inventario</h5>
                                </div>
                                <div class="d-grid gap-2 d-md-block m-3">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#NuevoProducto" data-bs-whatever="@mdo">Nuevo Producto</button>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#NuevoAsignacion" data-bs-whatever="@mdo">Asignación</button>
                                </div>
                            </div>
                            <div class="panel-body p-20">

                                <table id="TablaMercancia" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Codigo</th>
                                            <th>Empresa</th>
                                            <th>Descripcion</th>
                                            <th>Tipo De Activo</th>
                                            <th>Marca</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM tblbalmorca
                                                        UNION ALL
                                                        SELECT * FROM tblalmarca
                                                        UNION ALL
                                                        SELECT * FROM tblharipesca
                                                        UNION ALL
                                                        SELECT * FROM tblinversiones
                                                        UNION ALL
                                                        SELECT * FROM tblasesoriagm
                                                        UNION ALL
                                                        SELECT * FROM tblgado;";
                                        $query = $con->prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt=1;
                                        if($query->rowCount() > 0) { foreach($results as $result) { ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt);?></td>
                                            <td><?php echo htmlentities($result->codigo_activo);?></td>
                                            <td><?php echo htmlentities($nombreEmpresa = obtenerNombreEmpresa(htmlentities($result->codigo_activo)));?></td>
                                            <td><?php echo htmlentities($result->descripcion);?></td>
                                            <td><?php echo htmlentities($result->tipo_activo);?></td>
                                            <td><?php echo htmlentities($result->marca);?></td>
                                        </tr>
                                        <?php $cnt=$cnt+1;}} ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.section -->
    </div>
    <!-- End of Content Wrapper -->

    <!-- Footer -->
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>Copyright &copy; Victor Fernandes 2021</span>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <script>$('#TablaMercancia').DataTable();</script>
</body>

</html>