<?php
    require 'Config/Conexdb.php';
    require 'Config/config.php';
    require 'clases/invfunciones.php';
    $db = new Database();
    $con = $db ->conectar();
    $errors = [];
    // Si el Tipo de Usuario no es Administrador, Bloquea el Acceso
    // Obtén el id_interno enviado por POST
    $id_interno = $_SESSION['id_internotify'];
    // Consulta a la base de datos para obtener los datos de la empresa
    $sql = "SELECT * FROM tblempresa WHERE id_interno = :id_interno";
    $stmt = $con->prepare($sql);
    $stmt->execute(['id_interno' => $id_interno]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    $menuOpciones = json_decode($empresa['menu_opciones'], true);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include "header2.php"; ?>
            <!-- Modal Nuevo Producto -->
            
            <div class="main-page">
                        <!-- /.container-fluid -->
                        <section class="section">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="panel">
                                            <div class="panel-heading">
                                                <div class="panel-title">
                                                    <h5>Gestión de Manual y Procedimeito</h5>
                                                </div>
                                            </div>
                                        <?php if($msg){?>
                                        <div class="alert alert-success left-icon-alert" role="alert">
                                        <strong>Exitoso! </strong><?php echo htmlentities($msg); ?>
                                        </div><?php } 
                                        else if($error){?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                            <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                        </div>
                                        <?php } ?>
                                            <div class="panel-body p-20">

                                                <table id="TablaManuales" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th class="sort asc">Tipo Manual</th>
                                                            <th class="sort asc">Descripcion</th>
                                                            <th class="sort asc">Fecha Publicacion</th>
                                                            <th>Ver</th>                                                
                                                            <th>Modificar</th>
                                                            <th>Eliminar</th>
                                                        </tr>
                                                    </thead>
                                 
                                                    <tbody>
                                                        <?php $sql = "SELECT * from tblmanual";
                                                        $query = $con->prepare($sql);
                                                        $query->execute();
                                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                                        $cnt=1;
                                                        if($query->rowCount() > 0)
                                                        {
                                                        foreach($results as $result){   ?>
                                                            <tr>
                                                                <td><?php echo htmlentities($cnt);?></td>
                                                                    <td><?php echo htmlentities($result->tipo_manual);?></td>
                                                                    <td><?php echo htmlentities($result->descrip);?></td>
                                                                    <td><?php echo htmlentities($result->fpublicacion);?></td>
                                                                <td>
                                                                <a href="ver-manual.php?id=<?php echo htmlentities($result->id);?>" target="_blank" class="btn btn-info"><i class="fa fa-eye" title="Ver Record"></i> </a> 
                                                                </td>                                                                                                                   
                                                                <td>
                                                                <a href="edit-manual.php?id=<?php echo htmlentities($result->id);?>" class="btn btn-info"><i class="fa fa-edit" title="Editar Record"></i> </a> 
                                                                </td>
                                                                <td>
                                                                <a href="elim-manual.php?id=<?php echo htmlentities($result->id);?>" class="btn btn-danger"><i class="fa fa-trash" title="Eliminar Record"></i> </a> 
                                                                </td>
                                                            </tr>
                                                        <?php $cnt=$cnt+1;}} ?>
                                                    </tbody>
                                                </table>

                                         
                                                <!-- /.col-md-12 -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.col-md-6 -->

                                                               
                                                </div>
                                                <!-- /.col-md-12 -->
                                            </div>
                                        </div>
                                        <!-- /.panel -->
                                    </div>
                                    <!-- /.col-md-6 -->

                                </div>
                                <!-- /.row -->

                            </div>
                            <!-- /.container-fluid -->
                        </section>
                        <!-- /.section -->

                    </div>
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
    <script>$('#TablaManuales').DataTable();</script>
</body>

</html>