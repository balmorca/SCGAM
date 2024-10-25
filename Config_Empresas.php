<?php
    require 'Config/Conexdb.php';
    require 'clases/invfunciones.php';
    require 'Config/config.php';
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
    if (isset($_GET['pdf'])) {
        $id = $_GET['id'];
        if (PdfEmpresas($id, $con)) {
            true;
        } else {
            echo "Error al generar la consulta en PDF.";
        }
    } 
    // Eliminar Tabla Empresas
    if (isset($_POST['id']) && isset($_POST['action']) && $_POST['action'] === 'eliminar') {
        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        // Validar que el ID sea un número entero positivo y exista en la base de datos
        if ($id > 0) {
            $sql = "SELECT COUNT(*) FROM tblempresa WHERE id = :id";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                $sql = "DELETE FROM tblempresa WHERE id = :id";
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    header('Location: Config_Empresas.php');
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
    // Conexión para el Registro De Empresa
    if (!empty($_POST) && isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK && $_POST["action"] === "RegistrarEmpresa") {
        // Sanitize and validate user input (consider using prepared statements)
        $NombreEmpresa = trim($_POST['nombre']);
        $NombreCorto = trim($_POST['nombre_corto']);
        $rif = trim($_POST['rif']);
        $ubicacion = trim($_POST['ubicacion']);
        $direccion = trim($_POST['direccion']);
        $nombreArchivoOriginal = $_FILES['archivo']['name'];
        $tipoArchivo = $_FILES['archivo']['type'];
        $rutaTemporal = $_FILES['archivo']['tmp_name'];
        // Validar tipo de archivo y tamaño (ajusta según tus necesidades)
        if ($_FILES['archivo']['size'] > 2097152) {
            echo "El archivo es demasiado grande.";
            exit;
        }
        // Convert empresas to JSON (consider using a secure method like json_encode())
        $menu_opcionesSeleccionadas = isset($_POST['menu_opciones']) ? json_encode($_POST['menu_opciones']) : '[]';
        if (esNulo([$NombreEmpresa, $NombreCorto, $rif, $ubicacion, $direccion])) {
            $errors[] = "Debe llenar todos los campos";
            } else {
            $result = RegistrarEmpresa($NombreEmpresa, $rutaTemporal, $tipoArchivo, $archivo, $nombreArchivoOriginal, $NombreCorto, $rif, $ubicacion, $direccion, $menu_opcionesSeleccionadas, $con);
            if ($result !== true) {
                $errors[] = $result;
            }
        }
    }
    // Conexión para Editar la Empresa
    if (isset($_POST['id']) && isset($_POST['action']) && $_POST['action'] === "EditarEmpresa") {
        // Sanitize and validate user input (consider using prepared statements)$id = trim($_POST['id']);
        $id = trim($_POST['id']);
        $NombreEmpresa = trim($_POST['nombre']);
        $NombreCorto = trim($_POST['nombre_corto']);
        $rif = trim($_POST['rif']);
        $ubicacion = trim($_POST['ubicacion']);
        $direccion = trim($_POST['direccion']);
        $telefono = trim($_POST['telefono']);
        $contacto = trim($_POST['contacto']);
        $datosEmpresa = [
            'nombre' => $NombreEmpresa,
            'nombre_corto' => $NombreCorto,
            'rif' => $rif,
            'ubicacion' => $ubicacion,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'contacto' => $contacto
        ];
        $result = EditarEmpresa($id, $datosEmpresa, $con);
        if ($result !== true) {
            echo "Error al editar la empresa: ";
            false;
        } else {
            header('Location: Config_Empresas.php');
            exit;
            // echo "Empresa editada correctamente";
            true;
        }
    }
    // Verificar si se ha enviado el formulario
    /* if (isset($_POST['archivo'])) {
        $id = $_GET['id']; // Get userId from the form
        // Verificar si se ha seleccionado una imagen
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = $_FILES['archivo']['name'];
        $tipoArchivo = $_FILES['archivo']['type'];
        $rutaTemporal = $_FILES['archivo']['tmp_name'];
        // Validar tipo de archivo (imágenes solamente)
        if (strpos($tipoArchivo, 'image/') !== false) {
            // Carpeta donde se guardarán las imágenes
            $carpetaImagenes = 'archivos/';
            // Variable de la Ruta Donde se Creará la carpeta del Perfil del Usuario
            $nombreCarpeta = $resultados["nombre_corto"] . "/";
            // Generar un nombre único para la imagen
            $nombreUnico = uniqid() . '-' . $nombreArchivo;
            // Ruta Donde se Creará la carpeta del Perfil del Usuario
            $rutaDestinos = $carpetaImagenes . $nombreCarpeta;
            if (!is_dir($rutaDestinos)) {
                if (mkdir($rutaDestinos)) {
                    echo "Carpeta creada correctamente";
                } else {
                    echo "No se pudo crear la carpeta";
                }
            }
        } else {
        echo "No se ha seleccionado ninguna imagen";
        }
    }
} */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>SysIntv</title>
    <link rel="icon" type="image/x-icon" href="assets/images/AsesoriaGM.ico">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include "header.php"; ?>
        <section class="section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">

                        <div class="panel">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h5>Información de Empresas</h5>
                                </div>
                                <div class="d-grid gap-2 d-md-block m-3">
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#registrarEmpresa" data-bs-whatever="@mdo">
                                        <button class="btn btn-primary" type="button">Registrar Empresa</button>
                                    </a> 
                                </div>
                            </div>
                            <div class="panel-body p-20">

                                <table id="TablaEmpresas" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Rif</th>
                                            <th>Nombre Corto</th>
                                            <th>Contacto</th>
                                            <th>Ubicacion</th>
                                            <th>Ver</th>                               
                                            <th>Modificar</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sql = "SELECT * from tblempresa";
                                        $query = $con->prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt=1;
                                        if($query->rowCount() > 0) { foreach($results as $result) { ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt);?></td>
                                            <td><?php echo htmlentities($result->rif);?></td>
                                            <td><?php echo htmlentities($result->nombre_corto);?></td>
                                            <td><?php echo htmlentities($result->contacto);?></td>
                                            <td><?php echo htmlentities($result->ubicacion);?></td>
                                            <td>
                                                <a type="button" class="btn btn-info" target="_blank" title="Pdf" href="Config_Empresas.php?id=<?php echo htmlentities($result->id);?>&pdf">
                                                    <i class="fa fa-eye" title="Ver Record"></i>
                                                </a>
                                            </td>                                                           
                                            <td>
                                            <a type="button" class="btn btn-info" data-bs-toggle="modal"  data-bs-target="#EditarEmpresa<?php echo $result->id; ?>"><i class="fa fa-edit" title="Edit Record"></i></a> 
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#EliminarUsuario<?php echo $result->id; ?>"><i class="fa fa-trash" title="Eliminar Record"></i></button>
                                            </td>
                                            <!-- Modal para Confirmar Eliminacion de Usuario -->
                                            <form action="Config_Empresas.php" method="post">
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
                                                                ¿Estás seguro de que deseas eliminar a la Emrpesa "<b><?php echo htmlentities($result->nombre_corto);?></b>"? <br> <h5 class="text-center mt-3"> (Esta acción es irreversible!.) </h5>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button  type="submit" name="submit" class="btn btn-danger" data-id="<?php echo $result->id;?>">Eliminar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <!-- Modal Registar Nueva Empresa -->
                                            <div class="modal fade" id="registrarEmpresa" tabindex="-1" aria-labelledby="registrarEmpresaLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="registrarEmpresaLabel">Registrar Empresa</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="Config_Empresas.php" method="post" class="m-3">
                                                                <input type="hidden" name="action" value="RegistrarEmpresa">
                                                                <div class="form-group col-12">
                                                                    <label for="nombre" class="control-label">Nombre De Empresa</label>
                                                                    <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ingrese los datos" required="required">
                                                                </div>
                                                                <div class="form-group row col-12">
                                                                    <div class="col-6">
                                                                        <label for="nombre_corto" class="control-label">Nombre Corto</label>
                                                                        <input type="text" name="nombre_corto" class="form-control" id="nombre_corto" placeholder="Ingrese los datos" required="required">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="ubicacion" class="control-label">Ubicación</label>
                                                                        <input type="text" name="ubicacion" class="form-control" id="ubicacion" placeholder="Ingrese los datos" required="required">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row col-12">
                                                                    <div class="col-6">
                                                                        <label for="rif" class="control-label">Rif</label>
                                                                        <input type="text" name="rif" class="form-control" id="rif" placeholder="Ingrese los datos" value="J" required="required">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="telefono" class="control-label">Teléfono</label>
                                                                        <input type="text" name="telefono" class="form-control" id="telefono" placeholder="Ingrese los datos" required="required">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="ubicacion" class="control-label">Ubicación</label>
                                                                    <input type="text" name="ubicacion" class="form-control" id="ubicacion" placeholder="Ingrese los datos" required="required">
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="direccion" class="control-label">Dirección</label>
                                                                    <input type="text" name="direccion" class="form-control" id="direccion" placeholder="Ingrese los datos" required="required">
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="contacto" class="control-label">Contacto</label>
                                                                    <input type="text" name="contacto" class="form-control" id="contacto" placeholder="Ingrese los datos" required="required">
                                                                </div>
                                                                <style>
                                                                    #preview {
                                                                        display: flex;
                                                                        flex-wrap: wrap;
                                                                        justify-content: center;
                                                                    }
                                                                    .preview-image {
                                                                        max-width: 200px; /* Ajusta el ancho máximo según tus necesidades */
                                                                        height: auto;
                                                                        margin: 10px;
                                                                        border: 1px solid #ccc; 
                                                                    }
                                                                </style>
                                                                <div class="form-group mb-3 col-12">
                                                                    <label class="control-label" for="archivo">Subir Documentación de la Empresa:</label>
                                                                    <input type="file" class="form-control" name="archivo" id="archivo" multiple>
                                                                    <div id="preview"></div>
                                                                </div>
                                                                <script>
                                                                    const input = document.getElementById('archivo');
                                                                    const preview = document.getElementById('preview');
                                                                    input.addEventListener('change', () => {
                                                                        preview.innerHTML = '';
                                                                        for (let i = 0; i < input.files.length; i++) {
                                                                            const file = input.files[i];
                                                                            // Verificar si el archivo es una imagen
                                                                            if (/image/.test(file.type)) {
                                                                                const reader = new FileReader();
                                                                                reader.onload = (e) => {
                                                                                    const img = document.createElement('img');
                                                                                    img.src = e.target.result;
                                                                                    img.classList.add('preview-image');
                                                                            // Agregar una clase para personalizar el estilo
                                                                                    preview.appendChild(img);
                                                                                };
                                                                                reader.readAsDataURL(file);
                                                                            } else {
                                                                                const li = document.createElement('li');
                                                                                li.textContent = file.name;
                                                                                preview.appendChild(li);
                                                                            }
                                                                        }
                                                                    });
                                                                </script>
                                                                <br>
                                                                <button type="submit" name="submit" class="btn btn-primary">Guardar</button>
                                                                <button type="reset" name="reset" class="btn btn-danger">Limpiar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal Editar Usuario -->
                                            <div class="modal fade" id="EditarEmpresa<?php echo $result->id; ?>" tabindex="-1" aria-labelledby="EditarEmpresaLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="EditarEmpresaLabel">Editar Empresa</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="Config_Empresas.php" method="post" class="m-3">
                                                                <input type="hidden" name="action" value="EditarEmpresa">
                                                                <input type="hidden" name="id" value="<?php echo htmlentities($result->id); ?>">
                                                                <div class="form-group col-12">
                                                                    <label for="nombre" class="control-label">Nombre De Empresa</label>
                                                                    <input type="text" name="nombre" class="form-control" id="nombre" value="<?php echo htmlentities($result->nombre); ?>" required="required">
                                                                </div>
                                                                <div class="form-group row col-12">
                                                                    <div class="col-6">
                                                                        <label for="nombre_corto" class="control-label">Nombre Corto</label>
                                                                        <input type="text" name="nombre_corto" class="form-control" id="nombre_corto" value="<?php echo htmlentities($result->nombre_corto); ?>" required="required">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="ubicacion" class="control-label">Ubicación</label>
                                                                        <input type="text" name="ubicacion" class="form-control" id="ubicacion" value="<?php echo htmlentities($result->ubicacion); ?>" required="required">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row col-12">
                                                                    <div class="col-6">
                                                                        <label for="rif" class="control-label">Rif</label>
                                                                        <input type="text" name="rif" class="form-control" id="rif" value="<?php echo htmlentities($result->rif); ?>" required="required">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="telefono" class="control-label">Teléfono</label>
                                                                        <input type="text" name="telefono" class="form-control" id="telefono" value="<?php echo htmlentities($result->telefono); ?>" required="required">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="contacto" class="control-label">Contacto</label>
                                                                    <input type="text" name="contacto" class="form-control" id="contacto" value="<?php echo htmlentities($result->contacto); ?>" required="required">
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="direccion" class="control-label">Dirección</label>
                                                                    <input type="text" name="direccion" class="form-control" id="direccion" value="<?php echo htmlentities($result->direccion); ?>" required="required">
                                                                </div>
                                                                <hr>
                                                                <button type="submit" name="submit" class="btn btn-primary">Guardar</button>
                                                                <button type="reset" name="reset" class="btn btn-danger">Limpiar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
    <script>$('#TablaEmpresas').DataTable();</script>

</body>

</html>