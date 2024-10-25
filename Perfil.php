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
    #session_destroy();

    
    $sqls = $con->prepare("SELECT * FROM admin a INNER JOIN tblusuarios e WHERE a.id_userinterno = ?");
    $sqls->bindParam(1, $userId);
    $sqls->execute();
    $resultado = $sqls->fetchAll(PDO::FETCH_ASSOC);
    // Verificar si se ha enviado el formulario
    
    if (isset($_POST['imageuser']) && isset($_POST['action']) && $_POST['action'] === 'ActualizarPerfilImagen') {
        $userId = filter_var($_SESSION['user_idtify'], FILTER_SANITIZE_STRING);
        // Verificar si se ha seleccionado una imagen
        if (isset($_FILES['imageuser']) && $_FILES['imageuser']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = $_FILES['imageuser']['name'];
            $tipoArchivo = $_FILES['imageuser']['type'];
            $rutaTemporal = $_FILES['imageuser']['tmp_name'];
            // Validar tipo de archivo (imágenes solamente)
            if (strpos($tipoArchivo, 'image/') !== false) {
            // Validamos el tamaño del Archivo
            if ($_FILES['imageuser']['size'] > 2097152) { // 2MB
                echo 'El archivo es demasiado grande';
                exit;
            }
            // Carpeta donde se guardarán las imágenes
            $carpetaImagenes = 'archivos/Doc_Empleados/';
            // Variable de la Ruta Donde se Creará la carpeta del Perfil del Usuario
            $nombreCarpeta = $_SESSION['user_nametify'] . "/";
            // Generar un nombre único para la imagen
            $nombreUnico = "FOTO_PERFIL" . "." . pathinfo($_FILES['imageuser']['name'], PATHINFO_EXTENSION);
            // Ruta Donde se Creará la carpeta del Perfil del Usuario
            $rutaDestinos = $carpetaImagenes . $nombreCarpeta;
            if (!is_dir($rutaDestinos)) {
                if (mkdir($rutaDestinos)) {
                echo "Carpeta creada correctamente";
                } else {
                echo "No se pudo crear la carpeta";
                }
            }
            // Ruta completa donde se guardará la imagen
            $rutaDestino = $carpetaImagenes . $nombreCarpeta . $nombreUnico;
            // Mover la imagen
            if (file_exists($rutaDestino)) {
                if (unlink($rutaDestino)) {
                    if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
                        header('Location: perfil.php');
                        exit;
                    } else {
                        // Registrar el error en un log
                        error_log("Error al subir la imagen: " . error_get_last()['message']);
                        echo "Error al subir la imagen. Por favor, inténtalo más tarde.";
                    }
                } else {
                    echo "Error al eliminar la imagen";
                }
                echo "Imagen subida correctamente.";
            } else {
                // Registrar el error en un log
                error_log("Error al Comprobar la imagen: " . error_get_last()['message']);
                echo "Error al Comprobar la imagen. Por favor, inténtalo más tarde.";
            }
        } 
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
    <title>SysIntv</title>
    <link rel="icon" type="image/x-icon" href="assets/images/AsesoriaGM.ico">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


</head>

<body id="page-top">
    <style>
        .ultimo + div {
        visibility: hidden;
        }

        #imageuser-preview {
        width: 150px;
        height: 150px;
        border: 1px solid #ccc;
        border-radius: 100%;
        margin-bottom: 10px;
        margin: auto;
        overflow: hidden;
        }

        #imageuser-preview img {
        width: 100%;
        height: 100%;
        border: 0px solid #ccc;
        margin-bottom: 10px;
        object-fit: cover;
        overflow: hidden;
        }
    </style>
    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include "header.php"; ?>
  
        <div class="container">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#datos" type="button" role="tab" aria-controls="datos" aria-selected="true">Datos Personales</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Información Cargo</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Experiencia Laboral</button>
      </li>
    </ul>
    <div class="tab-content m-3" id="myTabContent">
        <div class="tab-pane fade show active" id="datos" role="tabpanel" aria-labelledby="datos-tab">
            <div class="container d-grid">
                <form class="row m-auto" action="Perfil.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="userId" value="<?php echo $_SESSION['user_idtify']; ?>">
                    <input type="hidden" name="imageuser" value="">
                    <input type="hidden" name="action" value="ActualizarPerfilImagen">
                    <div class="col-md-6 mb-3">
                        <h5 class="header blue lighter smaller">
                        <i class="ace-icon fa fa-filter smaller-90"></i>
                        Foto Empleado
                        </h5>
                        <div class="text-center">
                        <div id="imageuser-preview">
                            <?php
                            $carpetaImagenes = 'archivos/Doc_Empleados/' . $_SESSION['user_nametify'] . '/';
                            $nombreUnicojpg = "FOTO_PERFIL.jpg";
                            $rutaDestinos = $carpetaImagenes;
                            $rutaImagen = $rutaDestinos . $nombreUnicojpg;

                            if (file_exists($rutaImagen)) {
                                echo '<img src="' . $rutaImagen . '">';
                            } else {
                                echo '<img src="https://img.freepik.com/vector-gratis/gradiente-signo-foto_23-2149263898.jpg?t=st=1717041380~exp=1717044980~hmac=1f7f61d45bf882282931cd20391665f05183542e7100bb821564e930718b60c8&w=740">';
                            }
                            ?>
                        </div>
                        <input type="file" class="form-control form-control-sm mt-3" id="imageuser" name="imageuser" onchange="loadPreview(this)">
                        </div>
                    </div>
                        <!-- Fin Col-sm-12 -->
                        <div class="pull-right">
                            <button type="submit" class="btn btn-success">Actualizar</button>
                        </div>
                </form>
                        
                <!-- Inicio de id=datos -->
                <div class="tab-pane row in active" id="datos">
                    <div class="col-sm-12 row">
                            <h5 class="header blue lighter smaller">
                                <i class="ace-icon fa fa-filter smaller-90"></i>
                                Datos Personales
                            </h5>
                            <input type="hidden" name="config1" value="true">
                            <input type="hidden" name="cli" value="3031">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Apellidos y Nombre</label>
                                <div class="col-sm-7">
                                    <input type="text" value="PEREZ PEDRO" name="nombre" class="form-control">
                                </div>
                                <label class="col-sm-4 col-form-label">Rif</label>
                                <div class="col-sm-4">
                                    <input type="text" value="000000000" name="rif" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Fecha Nacimiento</label>
                                <div class="col-sm-2">
                                    <input type="date" value="2000-12-31" name="fnacimiento" class="form-control">
                                </div>
                                <label class="col-sm-2 col-form-label">Edad</label>
                                <div class="col-sm-2">
                                    <input type="text" value="31" name="edad" class="form-control">
                                </div>
                                <label class="col-sm-1 col-form-label">Sexo</label>
                                <div class="col-sm-2">
                                    <input type="text" value="F/M" name="sexo" class="form-control">
                                </div>
                                <label class="col-sm-2 col-form-label">L. Nacimiento</label>
                                <div class="col-sm-2">
                                    <input type="text" value="Ciudad" name="lnacimiento" class="form-control">
                                </div>
                                <label class="col-sm-2 col-form-label">Estado Civil</label>
                                <div class="col-sm-2">
                                    <input type="text" value="Soltero" name="ecivil" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Dirección</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" name="direccion">Av. Principal de Lechería</textarea>
                                </div>
                            </div>
                    </div>
                    <!-- Fin Col-sm-10 -->
                    <!-- Fin Col-sm-12 -->                    
                    <div class="col-sm-12 row">
                        <div class="form-group row">
                            <label for="fnacimiento" class="col-sm-2 col-form-label">Fecha Nacimiento</label>
                            <div class="col-sm-2">
                                <input type="date" value="2000-12-31" name="fnacimiento" id="fnacimiento" class="form-control">
                            </div>
                            <label for="edad" class="col-sm-1 col-form-label">Edad</label>
                            <div class="col-sm-1">
                                <input type="text" value="31" name="edad" id="edad" class="form-control">
                            </div>
                            <label for="sexo" class="col-sm-1 col-form-label">Sexo</label>
                            <div class="col-sm-1">
                                <input type="text" value="F/M" name="sexo" id="sexo" class="form-control">
                            </div>
                            <label for="lnacimiento" class="col-sm-2 col-form-label">L. Nacimiento</label>
                            <div class="col-sm-5">
                                <input type="text" value="Ciudad" name="lnacimiento" id="lnacimiento" class="form-control">
                            </div>
                            <label for="ecivil" class="col-sm-2 col-form-label">Estado Civil</label>
                            <div class="col-sm-2">
                                <input type="text" value="Soltero" name="ecivil" id="ecivil" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="correo" class="col-sm-2 col-form-label">Correo</label>
                            <div class="col-sm-4"> 

                                <input type="email" value="perdo.perez@xxx.com" name="correo" id="correo" class="form-control">
                            </div>
                            <label for="gintrucion" class="col-sm-2 col-form-label">Grado Instrucción</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="gintrucion" name="gintrucion">
                                    <option value="">Seleccionar...</option>
                                    <option value="Ing">Ing.</option>
                                    <option value="Tsu">Tsu.</option>
                                    <option value="Bachiller">Bachiller</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="titulo" class="col-sm-2 col-form-label">Título</label>
                            <div class="col-sm-4">
                                <input type="text" value="Titulo" name="titulo" id="titulo" class="form-control">
                            </div>
                            <label for="especialidad" class="col-sm-2 col-form-label">Especialidad</label>
                            <div class="col-sm-4">
                                <input type="text" value="Especialidad" name="especialidad" id="especialidad" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="motricidad" class="col-sm-2 col-form-label">Motricidad</label>
                            <div class="col-sm-2">
                                <select class="form-control" id="motricidad" name="motricidad">
                                    <option value="">Seleccionar...</option>
                                    <option value="Diestro">Diestro</option>
                                    <option value="Zurdo">Zurdo</option>
                                </select>
                            </div>
                            <label for="discapacidad" class="col-sm-2 col-form-label">Discapacidad</label>
                            <div class="col-sm-2">
                                <select class="form-control" id="discapacidad" name="discapacidad">
                                    <option value="">Seleccionar...</option>
                                    <option value="SI">Si</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 row">
                            <div class="form-group row">
                                <label for="automovil" class="col-sm-1 col-form-label">Automóvil</label>
                                <div class="col-sm-2">
                                <select class="form-control" id="automovil" name="automovil">
                                    <option value="">Seleccionar...</option>
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                </select>
                                </div>
                                <label for="placa" class="col-sm-1 col-form-label">Placa</label>
                                <div class="col-sm-2">
                                <input type="text" value="Placa" name="placa" id="placa" class="form-control">
                                </div>
                                <label for="propiedad" class="col-sm-2 col-form-label">Propiedad o Autorización</label>
                                <div class="col-sm-3">
                                <input type="text" value="Propietario?" name="propiedad" id="propiedad" class="form-control">
                                </div>
                                <label for="lentes" class="col-sm-2 col-form-label">Uso de Lentes</label>
                                <div class="col-sm-2">
                                <select class="form-control" id="lentes" name="lentes">
                                    <option value="">Seleccionar...</option>
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                </select>
                                </div>
                                <label for="sangre" class="col-sm-1 col-form-label">Sangre</label>
                                <div class="col-sm-2">
                                <select class="form-control" id="sangre" name="sangre">
                                    <option value="">Seleccionar...</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option> 

                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select> 

                                </div>
                            </div>                                                                                
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            
                <!-- Inicio id=laboral-->
                <div class="tab-pane" id="laboral">

                    <div class="col-sm-12">
                        <h4 class="header blue lighter smaller">
                            <i class="ace-icon fa fa-filter smaller-90"></i>
                            Slider <small>de la página principal y front de la applicación móvil</small>
                        </h4>
                        <div class="row">
                            <div class="col-sm-3">
                                <h5>Imagen #1</h5>
                                <form class="photo-upload-form">
                                    <input name="saveImage" type="hidden" value="true">
                                    <input name="imgName" type="hidden" value="slider1">
                                    <input name="imgFolder" type="hidden" value="sliders">
                                    <input name="s" type="hidden" value="1">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <input name="nfoto" type="file" class="photo-upload-input" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger hide" id="foto-error" role="alert"></div>
                                    <div class="row" style="margin:20px;">
                                        <div class="col-sm-12 photo-upload-img" style="text-align:center">
                                            <h4 style="color:grey">No existe imágen</h4>                                        </div>
                                    </div>
                                    <div class="row" style="margin:20px;">
                                        <div class="col-md-12">
                                            <input type="text" name="link_slider_1" placeholder="Link web (url al hacer click)" value="https://google.com" class="form-control slider-link" id="">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class=" col-sm-3">
                                <h5>Imagen #2</h5>
                                <form class="photo-upload-form">
                                    <input name="saveImage" type="hidden" value="true">
                                    <input name="imgName" type="hidden" value="slider2">
                                    <input name="imgFolder" type="hidden" value="sliders">
                                    <input name="s" type="hidden" value="1">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <input name="nfoto" type="file" class="photo-upload-input" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger hide" id="foto-error" role="alert"></div>
                                    <div class="row" style="margin:20px;">
                                        <div class="col-sm-12 photo-upload-img" style="text-align:center">
                                            <h4 style="color:grey">No existe imágen</h4>                                        </div>
                                    </div>
                                    <div class="row" style="margin:20px;">
                                        <div class="col-md-12">
                                            <input type="text" name="link_slider_2" placeholder="Link web (url al hacer click)" value="" class="form-control slider-link" id="">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-3">
                                <h5>Imagen #3</h5>
                                <form class="photo-upload-form">
                                    <input name="saveImage" type="hidden" value="true">
                                    <input name="imgName" type="hidden" value="slider3">
                                    <input name="imgFolder" type="hidden" value="sliders">
                                    <input name="s" type="hidden" value="1">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <input name="nfoto" type="file" class="photo-upload-input" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger hide" id="foto-error" role="alert"></div>
                                    <div class="row" style="margin:20px;">
                                        <div class="col-sm-12 photo-upload-img" style="text-align:center">
                                            <h4 style="color:grey">No existe imágen</h4>                                        </div>
                                    </div>
                                    <div class="row" style="margin:20px;">
                                        <div class="col-md-12">
                                            <input type="text" name="link_slider_3" placeholder="Link web (url al hacer click)" value="" class="form-control slider-link" id="">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-3">
                                <h5>Imagen #4</h5>
                                <form class="photo-upload-form">
                                    <input name="saveImage" type="hidden" value="true">
                                    <input name="imgName" type="hidden" value="slider4">
                                    <input name="imgFolder" type="hidden" value="sliders">
                                    <input name="s" type="hidden" value="1">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <input name="nfoto" type="file" class="photo-upload-input" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger hide" id="foto-error" role="alert"></div>
                                    <div class="row" style="margin:20px;">
                                        <div class="col-sm-12 photo-upload-img" style="text-align:center">
                                            <h4 style="color:grey">No existe imágen</h4>                                        </div>
                                    </div>
                                    <div class="row" style="margin:20px;">
                                        <div class="col-md-12">
                                            <input type="text" name="link_slider_4" placeholder="Link web (url al hacer click)" value="" class="form-control slider-link" id="">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="alert alert-info">Unicamente se admite una resolución de <b>900x400px</b> para las imágenes pequeñas y <b>1600x500px</b> para los sliders que cubran todo el espacio.</div>
                    </div>
                </div>
                <!-- Fin id=laboral -->
        </div>
        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            
                <!-- inicio id= conyuge-->
                <div class="tab-pane" id="conyuge">
                    <form class="form-horizontal form-data">
                        <input type="hidden" name="config4" value="true">
                        <input type="hidden" name="cli" value="3031">
                        <div class="col-sm-4">
                            <h4 class="header blue lighter smaller">
                                <i class="ace-icon fa fa-filter smaller-90"></i>
                                Colores de la web y aplicación móvil
                            </h4>
                            <div class="row" style="margin-top:20px;">
                                <label for="" class="col-sm-4">Color primario</label>
                                <div class="col-sm-8">
                                    <input class="jscolor {onFineChange:'updateColorApp(this)'} form-control" name="theme_color_primary" value="F9F9F9" style="font-size: 10px;">
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px;">
                                <label for="" class="col-sm-4">Color secundario</label>
                                <div class="col-sm-8">
                                    <input class="jscolor form-control" name="theme_color_secondary" value="D2E8FF" style="font-size: 10px;">
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px;">
                                <label for="" class="col-sm-4">Color de texto e iconos</label>
                                <div class="col-sm-8">
                                    <input class="jscolor form-control" name="theme_color_text" value="000000" style="font-size: 10px;">
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px;">
                                <label for="" class="col-sm-4">Status bar</label>
                                <div class="col-sm-2">
                                    <input type="text" name="theme_color_bar" value="light" class="form-control status-bar">
                                </div>
                            </div>
                            <div class="pull-right" style="margin-top:20px;">
                                <button type="submit" class="btn btn-success">Aplicar cambios</button>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <h4 class="header blue lighter smaller">
                                <i class="ace-icon fa fa-filter smaller-90"></i>
                                Ubitiendas
                            </h4>
                            <div class="row" id="appt">
                                <div class="col-md-8">Quiero que mi tienda aparezca en la app UbiTiendas</div>
                                <div class="col-md-4">
                                    <div style="width: fit-content;">
                                        <div class="material-switch pull-right">
                                            <input id="app_ubitiendas" name="app_ubitiendas" checked type="checkbox" />
                                            <label for="app_ubitiendas" class="label-primary"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h4 class="header blue lighter smaller">
                                <i class="ace-icon fa fa-filter smaller-90"></i>
                                Web
                            </h4>
                            <div class="row" id="publict">
                                <div class="col-md-8">Pagina disponible al público</div>
                                <div class="col-md-4">
                                    <div style="width: fit-content;">
                                        <div class="material-switch pull-right">
                                            <input id="public" name="public" checked type="checkbox" />
                                            <label for="public" class="label-primary"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>                
                <!-- Fin id= conyuge-->
        </div>
    </div>
</div>
        


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
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
     

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script>
      function loadPreview(input) {
        if (input.files && input.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;

            // Verificar si existe una imagen previa
            const previewDiv = document.getElementById('imageuser-preview');
            const existingImg = previewDiv.querySelector('img');

            if (existingImg) {
              // Si existe, reemplazarla
              previewDiv.replaceChild(img, existingImg);
            } else {
              // Si no existe, agregar la nueva imagen
              previewDiv.innerHTML = ''; // Limpiar cualquier vista previa existente
              previewDiv.appendChild(img);
            }
          };
          reader.readAsDataURL(input.files[0]);
        }
      }
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>