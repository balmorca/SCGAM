<?php
    require 'Config/Conexdb.php';
    require 'clases/invfunciones.php';
    require 'Config/config.php';
    $db = new Database();
    $con = $db->conectar();
    $errors = [];
    $NameUser = $_SESSION["user_nametify"];

    // Preparando a consulta con bindparam para evitar inserción de SQL
    $sql = "SELECT a.empresas_autoriz, e.nombre_corto, e.id_interno, a.NombreCompleto AS user_name 
        FROM admin a INNER JOIN tblempresa e ON JSON_SEARCH(a.empresas_autoriz, 'one', e.nombre_corto) IS NOT NULL WHERE a.NombreCompleto = :user_name";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':user_name', $NameUser, PDO::PARAM_STR);
        $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Conexión para el Registro De Usuario
    if (!empty($_POST) && isset($_POST['action']) && $_POST['action'] === 'RegisterUser') {
        // Sanitize and validate user input (consider using prepared statements)
        $nombres = trim($_POST['nombres']);
        $cedula = trim($_POST['cedula']);
        $indicador = trim($_POST['indicador']);
        $departamento = trim($_POST['departamento']);
        $fingreso = trim($_POST['fingreso']);
        $cargo = trim($_POST['cargo']);
        $empresa_nomina = trim($_POST['empresa_nomina']);
        if (esNulo([$nombres, $cedula, $indicador, $departamento, $fingreso, $cargo])) {
        $errors[] = "Debe llenar todos los campos";
        } else {
            $result = RegistrarUsuario($nombres, $cedula, $indicador, $departamento, $fingreso, $cargo, $empresa_nomina, $con);
            if ($result !== true) {
                $errors[] = $result;
            }
        }
    }

    // Eliminar Usuario
    if (isset($_POST['id']) && isset($_POST['action']) && $_POST['action'] === 'eliminar') {
        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        // Validar que el ID sea un número entero positivo y exista en la base de datos
        if ($id > 0) {
            $sql = "SELECT COUNT(*) FROM tblusuarios WHERE id = :id";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                $sql = "DELETE FROM tblusuarios WHERE id = :id";
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    header('Location: Config_Usuarios.php');
                    exit;
                } else {
                    // Registrar el error y mostrar un mensaje al usuario
                    error_log("Error al eliminar la empresa: " . $stmt->errorInfo()[2]);
                    echo 'Ha ocurrido un error al eliminar la empresa.';
                }
            } else {
                echo 'La empresa no existe.';
            }
        } else {
            echo 'ID de empresa inválido.';
        }
    }

    // Inicia Consulta de Pdf de Ingreso de Usuarios
    if (isset($_GET['pdf'])) {
        $id = $_GET['id']; // Retrieve the user ID from the query parameter
        if (PdfUsuariosActas($id, $con)) {
            // PDF generation was successful
            true;
        } else {
            // Handle PDF generation errors (e.g., display an error message)
            false; 
            echo "Error generating PDF.";
        }
    }
    
    // Conexión para Editar el Usuario
    if (isset($_POST['id']) && isset($_POST['action']) && $_POST['action'] === "EditarUsuarios") {
        // Sanitize and validate user input (consider using prepared statements)$id = trim($_POST['id']);
        $indicador = trim($_POST['id']);
        $editnombres = trim($_POST['editnombre']);
        $editcedula = trim($_POST['editcedula']);
        $editdepartamento = trim($_POST['editdepartamento']);
        $editfingreso = trim($_POST['editfingreso']);
        $editcargo = trim($_POST['editcargo']);
        $result = EditarUsuarios($indicador, $editnombres, $editcedula, $editdepartamento, $editfingreso, $editcargo, $con);
        if ($result !== true) {
            echo "Error al editar la usuario: ";
            false;
        } else {
            header('Location: Config_Usuarios.php');
            exit;
            // echo "Empresa editada correctamente";
            true;
        }
    }
    
    // Conexión para Elevar el Usuario ---------------------- Falta por arreglar
    if (isset($_POST['indicador']) && isset($_POST['action']) && $_POST['action'] === "ElevarUsuarios") {
        $indicador = trim($_POST['indicador']);
        $permition = trim($_POST['permition']);
        $password = trim($_POST['password']);
        $repassword = trim($_POST['repassword']);
        $usuario = trim($_POST['usuario']);
        $empresas_autoriz = isset($_POST['empresas_autoriz']) ? $_POST['empresas_autoriz'] : [];
        // Validate input data (e.g., check if permition and empresas_autoriz are valid)
        if($password === $repassword) {
            $result = ElevarUsuarios($indicador, $permition, $password, $usuario, $empresas_autoriz, $con);
        } else {
            echo "Las Contraseñas no Coinciden!";
            exit;
        }
        if ($result !== true) {
            echo "Error al editar la usuario: ";
            false;
        } else {
            header('Location: Config_Usuarios.php');
            exit;
            true;
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
    <title>SCAGM</title>
    <link rel="icon" type="image/x-icon" href="assets/images/AsesoriaGM.ico">
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
        <section class="section mx-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h5>Ver información de Usuarios</h5>
                                </div>
                                <div class="d-grid gap-2 d-md-block m-3">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registrarUsuario" data-bs-whatever="@mdo">Registrar Usuario</button>

                                </div>
                            </div>
                            <div class="panel-body p-20">
                                <table id="TablaUsuarios" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre y Apellido</th>
                                            <th>Cedula</th>
                                            <th>Legajo</th>
                                            <th>Empresa</th>
                                            <th>Ver</th>                               
                                            <th>Modificar</th>
                                            <th>Permisos</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <?php $sql = "SELECT * from tblusuarios";
                                        $query = $con->prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt=1;
                                        if($query->rowCount() > 0)
                                        {
                                        foreach($results as $result){ ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt);?></td>
                                            <td><?php echo htmlentities($result->nombres);?></td>
                                            <td><?php echo htmlentities($result->cedula);?></td>
                                            <td><?php echo htmlentities($result->indicador);?></td>
                                            <td><?php echo htmlentities($result->empresa_nomina);?></td>
                                            <td>
                                                <a type="button" class="btn btn-info" title="Pdf" target="_blank" href="Config_Usuarios.php?id=<?php echo htmlentities($result->id);?>&pdf">
                                                    <i class="fa fa-eye" title="Ver Record"></i>
                                                </a>
                                            </td>                                                           
                                            <td>
                                                <a type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#EditarUsuario<?php echo $result->id; ?>" data-bs-whatever="@mdo" class="btn btn-info"><i class="fa fa-edit" title="Edit Record"></i></a>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ElevarUsuario<?php echo $result->id; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-person-gear" viewBox="0 0 16 16">
                                                        <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m.256 7a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1zm3.63-4.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
                                                    </svg>
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#EliminarUsuario<?php echo $result->id; ?>"><i class="fa fa-trash" title="Eliminar Record"></i></button>
                                            </td>
                                            
                                            <!-- Modal Registar Usuarios -->
                                            <div class="modal fade" id="registrarUsuario" tabindex="-1" aria-labelledby="registrarUsuarioLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="registrarUsuarioLabel">Registrar Usuarios</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="Config_Usuarios.php" method="post" class="m-3">
                                                            <input type="hidden" name="action" value="RegisterUser">
                                                                <div class="form-group col-12">
                                                                    <label for="nombres" class="control-label" autocomplete="off">Nombre Completo</label>
                                                                    <input type="text" name="nombres" class="form-control" id="nombres" placeholder="Ingrese los datos" required="required">
                                                                </div>
                                                                <div class="form-group row col-12">
                                                                    <div class="col-6">
                                                                        <label for="cedula" class="control-label" autocomplete="off">Cedula</label>
                                                                        <input type="text" name="cedula" class="form-control" id="cedula" placeholder="Ingrese los datos" Value="V" required="required">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="indicador" class="control-label" autocomplete="off">Legajo</label>
                                                                        <input type="text" name="indicador" class="form-control" id="indicador" placeholder="Ingrese los datos" required="required">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row col-12">
                                                                    <div class="col-6">
                                                                        <label for="cargo" class="control-label" autocomplete="off">Cargo</label>
                                                                        <input type="text" name="cargo" class="form-control" id="cargo" placeholder="Ingrese los datos" required="required">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="fingreso" class="control-label" >Ingreso</label>
                                                                        <input type="date" name="fingreso" class="form-control" id="fingreso" required="required">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="departamento" class="control-label" autocomplete="off">Departamento</label>
                                                                    <input type="text" name="departamento" class="form-control" id="departamento" placeholder="Ingrese los datos" required="required">
                                                                </div>
                                                                <div class="form-check">
                                                                    <label for="empresa_nomina">Selecciona la empresa Nomina:</label>
                                                                    <div>
                                                                        <input type="radio" name="empresa_nomina" value="BALMORCA"> BALMORCA <br>
                                                                        <input type="radio" name="empresa_nomina" value="INVERSIONES COALMARCA"> INVERSIONES COALMARCA <br>
                                                                        <input type="radio" name="empresa_nomina" value="HARIPESCA"> HARIPESCA <br>
                                                                        <input type="radio" name="empresa_nomina" value="ASESORIA EL MORRO"> ASESORIA EL MORRO <br>
                                                                        <input type="radio" name="empresa_nomina" value="GADO"> GADO <br>
                                                                        <input type="radio" name="empresa_nomina" value="ALMARCA"> ALMARCA
                                                                    </div>
                                                                    <br>
                                                                </div>
                                                                <br>
                                                                <button type="submit" name="submit" class="btn btn-primary">Guardar</button>
                                                                <button type="reset" name="reset" class="btn btn-danger">Limpiar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal para Confirmar Eliminacion de Usuario -->
                                            <form action="Config_Usuarios.php" method="post">
                                                <input type="hidden" name="action" value="eliminar">
                                                <input type="hidden" name="id" value="<?php echo $result->id;?>">
                                                <div class="modal fade" id="EliminarUsuario<?php echo $result->id; ?>" tabindex="-1" aria-labelledby="EliminarUsuarioLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="EliminarUsuarioLabel">Confirmación de Eliminación</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body"> 
                                                                ¿Estás seguro de que deseas eliminar al usuario " <br> <b><?php echo htmlentities($result->nombres);?></b>"? <br> <h5 class="text-center mt-3"> (Esta acción es irreversible!.) </h5>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button> 
                                                                <button  type="submit" name="submit" class="btn btn-danger" data-id="<?php echo $result->id;?>">Eliminar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <!-- Modal Elevar Usuarios -->
                                            <div class="modal fade" id="ElevarUsuario<?php echo $result->id; ?>" tabindex="-1" aria-labelledby="ElevarUsuarioLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="ElevarUsuarioLabel">Elevar Usuario</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="Config_Usuarios.php" method="post" class="m-3">
                                                                <input type="hidden" name="action" value="ElevarUsuarios">
                                                                <input type="hidden" name="indicador" value="<?php echo $result->indicador;?>">
                                                                <div class="form-group">
                                                                    <div class="form-group row col-12">
                                                                        <div class="form-group col-6">
                                                                            <label for="indicador" class="control-label">Legajo: </label>
                                                                            <label for="indicador" disabled class="control-label"> <b><?php echo htmlentities($result->indicador);?> </b></label>
                                                                        </div>
                                                                        <div class="form-group col-6">
                                                                            <label for="Negocio" class="control-label">Nomina: </label>
                                                                            <label for="Negocio" disabled class="control-label"> <b><?php echo htmlentities($result->empresa_nomina);?> </b></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row col-12">
                                                                        <div class="form-group col-8">
                                                                            <label for="nombres" class="control-label">Nombre Completo: </label>
                                                                            <input for="nombres" class="form-control" disabled value="<?php echo htmlentities($result->nombres);?>">
                                                                        </div>
                                                                        <div class="form-group col-4">
                                                                            <label for="cedula" class="control-label">Cedula: </label>
                                                                            <input for="cedula" class="form-control" disabled value="<?php echo htmlentities($result->cedula);?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row col-12">
                                                                        <div class="form-group col-6">
                                                                            <label for="cargo" class="control-label">Cargo: </label>
                                                                            <input for="cargo" class="form-control" disabled value="<?php echo htmlentities($result->cargo);?>">
                                                                        </div>
                                                                        <div class="form-group col-6">
                                                                            <label for="departamento" class="control-label">Departamento: </label>
                                                                            <input for="departamento" class="form-control" disabled value="<?php echo htmlentities($result->departamento);?>">
                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="form-group col-12">
                                                                        <label for="Usuario" class="control-label" autocomplete="off">Usuario:</label>
                                                                        <input type="email" name="Usuario" class="form-control" id="Usuario" placeholder="Ingrese los datos" required="required">
                                                                    </div>
                                                                    <div class="form-group row col-12">
                                                                        <div class="form-group col-6">
                                                                            <label for="password" class="control-label" autocomplete="off">Password:</label>
                                                                            <input type="text" name="password" class="form-control" id="password" placeholder="Ingrese los datos" required="required">
                                                                        </div>
                                                                        <div class="form-group col-6">
                                                                            <label for="repassword" class="control-label" autocomplete="off">Repassword:</label>
                                                                            <input type="text" name="repassword" class="form-control" id="repassword" placeholder="Ingrese los datos" required="required">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-check">
                                                                    <label for="permition">Selecciona el Nivel del Usuario:</label>
                                                                    <div>
                                                                        <input type="radio" name="permition" value="OPERADOR"> OPERADOR <br>
                                                                        <input type="radio" name="permition" value="ADMINISTRADOR"> ADMINISTRADOR <br>
                                                                        <input type="radio" name="permition" value="SUPERMASTER"> SUPER MASTER
                                                                    </div>
                                                                </div>
                                                                <div class="form-check">
                                                                    <label for="empresas_autoriz">Selecciona las Empresas Autorizadas:</label>
                                                                    <div>
                                                                        <input type="checkbox" name="empresas_autoriz[]" value="BALMORCA"> BALMORCA <br>
                                                                        <input type="checkbox" name="empresas_autoriz[]" value="INVERSIONES COALMARCA"> INVERSIONES COALMARCA <br>
                                                                        <input type="checkbox" name="empresas_autoriz[]" value="HARIPESCA"> HARIPESCA <br>
                                                                        <input type="checkbox" name="empresas_autoriz[]" value="ASESORIA EL MORRO"> ASESORIA EL MORRO <br>
                                                                        <input type="checkbox" name="empresas_autoriz[]" value="GADO"> GADO <br>
                                                                        <input type="checkbox" name="empresas_autoriz[]" value="ALMARCA"> ALMARCA
                                                                    </div>
                                                                    <br>
                                                                </div>
                                                                <button type="submit" name="submit" class="btn btn-primary">Guardar</button>
                                                                <button type="reset" name="reset" class="btn btn-danger">Limpiar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Fin Del Modal Elevar Usuarios -->
                                            <!-- Modal Editar Usuario -->
                                            <div class="modal fade" id="EditarUsuario<?php echo $result->id; ?>" tabindex="-1" aria-labelledby="EditarUsuarioLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="EditarUsuarioLabel">Editar Usuario</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="Config_Usuarios.php" method="post" class="m-3">
                                                                <input type="hidden" name="action" value="EditarUsuarios">
                                                                <input type="hidden" name="id" value="<?php echo htmlentities($result->indicador); ?>">
                                                                <div class="col-6">
                                                                    <label for="editindicador" class="control-label">Legajo:</label>
                                                                    <label for="editindicador" id="editindicador" class="control-label"><b><?php echo htmlentities($result->indicador); ?></b></label>
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="editnombre" class="control-label">Nombre Completo:</label>
                                                                    <input type="text" name="editnombre" class="form-control" id="editnombre" value="<?php echo htmlentities($result->nombres); ?>" required="required">
                                                                </div>
                                                                <div class="form-group row col-12">
                                                                    <div class="col-6">
                                                                        <label for="editcedula" class="control-label">Cedula:</label>
                                                                        <input type="text" name="editcedula" class="form-control" id="editcedula" value="<?php echo htmlentities($result->cedula); ?>" required="required">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="editfingreso" class="control-label">Fecha Ingreso:</label>
                                                                        <input type="date" name="editfingreso" class="form-control" value="<?php echo htmlentities($result->fingreso); ?>" id="editfingreso" required="required">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row col-12">
                                                                    <div class="col-6">
                                                                        <label for="editcargo" class="control-label">Cargo:</label>
                                                                        <input type="text" name="editcargo" class="form-control" value="<?php echo htmlentities($result->cargo); ?>" id="editcargo" required="required">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="editdepartamento" class="control-label">Departamento:</label>
                                                                        <input type="text" name="editdepartamento" class="form-control" id="editdepartamento" value="<?php if($result->departamento === ""){ echo "No Asignado"; } else { echo htmlentities($result->departamento);} ?>" required="required">
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <button type="submit" name="submit" class="btn btn-primary">Guardar</button>
                                                                <button type="reset" name="reset" class="btn btn-danger">Restaurar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                        <?php $cnt=$cnt+1; } } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>     
                </div>
            </div>
        </section>
        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Victor Fernandes 2024</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->
    </div>
    <!-- End of Page Wrapper -->

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
    <script>$('#TablaUsuarios').DataTable();</script>

</body>

</html>