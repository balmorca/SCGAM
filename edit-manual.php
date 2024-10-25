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
if(isset($_POST['update']))
{
$tipo_manual=$_POST['tipo_manual'];
$descrip=$_POST['descrip'];
$fpublicacion=$_POST['fpublicacion'];
$responsable=$_POST['responsable'];
$archivo=$_POST['archivo'];
$cid=intval($_GET['id']);

$sql="update  tblmanual set 
						tipo_manual=:tipo_manual,descrip=:descrip,fpublicacion=:fpublicacion,responsable=:responsable,archivo=:archivo where id=:cid ";

$query = $dbh->prepare($sql);
$query->bindParam(':tipo_manual',$tipo_manual,PDO::PARAM_STR);
$query->bindParam(':descrip',$descrip,PDO::PARAM_STR);
$query->bindParam(':fpublicacion',$fpublicacion,PDO::PARAM_STR);
$query->bindParam(':responsable',$responsable,PDO::PARAM_STR);
$query->bindParam(':archivo',$archivo,PDO::PARAM_STR);
$query->bindParam(':cid',$cid,PDO::PARAM_INT);
$query->execute();
$msg="Datos Actualizados";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
	
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include "header2.php"; ?>
            <div class="main-page">
				<section class="section">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-8 col-md-offset-2">
								<div class="panel">
									<div class="panel-heading">
										<div class="panel-title">
											<h5>Actualizar Manual o Procedimiento</h5>
										</div>
									</div>
									<div class="panel-body">
									<?php if($msg){?>
									<div class="alert alert-success left-icon-alert" role="alert">
									<strong>Exitoso! </strong><?php echo htmlentities($msg); ?>
									</div><?php } 
									else if($error){?>
										<div class="alert alert-danger left-icon-alert" role="alert">
									<strong>Error!</strong> <?php echo htmlentities($error); ?>
								</div>
								<?php } ?>

										<form method="post">
									<?php 
									$cid=intval($_GET['id']);
									$sql = "SELECT * from tblmanual where id=:cid";
									$query = $con->prepare($sql);
									$query->bindParam(':cid',$cid,PDO::PARAM_STR);
									$query->execute();
									$results=$query->fetchAll(PDO::FETCH_OBJ);
									$cnt=1;
									if($query->rowCount() > 0)
									{
									foreach($results as $result)
									{   ?>

							<div class="form-group">
								<label for="success" class=" control-label"></label>
								<select name="tipo_manual" class="form-control" id="tipo_manual" required="required">
									<option value="">Seleccione Tipo de Manual o Procedimiento</option>
									<option value="Ejecucion - Individual">Ejecucion - Individual</option>
									<option value="Ejecucion - Colectivos">Ejecucion - Colectivos</option>
									<option value="Ejecucion - Departamentales">Ejecucion - Departamentales</option>
									<option value="Area - Administrativo">Area - Administrativo</option>
									<option value="Area - Contable">Area - Contable</option>
									<option value="Area - Finanzas">Area - Finanzas</option>
									<option value="Funcion - General">Funcion - General</option>
									<option value="Funcion - Especifico">Funcion - Especifico</option>  
								</select>
							</div>
							
							<div class="form-group">
								<label for="success" class="control-label">&nbsp;Descripción</label>
								<div class="">
									<input type="text" name="descrip" value="<?php echo htmlentities($result->descrip);?>" required="required" class="form-control" id="success">
									<span class="help-block"></span>
								</div>
							</div>

								<div class="col-md-4">
								<label for="success" class="control-label">&nbsp;Fecha Publicación</label>
								<div class="">
									<input type="date" name="fpublicacion" value="<?php echo htmlentities($result->fpublicacion);?>" class="form-control" required="required" id="success">
									<span class="help-block"></span>
								</div>
							</div>
							
							<div class="col-md-8">
								<label for="success" class="control-label">&nbsp;Responsable</label>
								<div class="">
									<input type="text" name="responsable" value="<?php echo htmlentities($result->responsable);?>" class="form-control" required="required" id="success">
									<span class="help-block"></span>
								</div>
							</div>

							<div class="form-group">
								<label for="success" class="form-label">Archivo en (WORD & PDF)</label>
								<div class="">
									<input type="text" name="archivo" value="<?php echo htmlentities($result->archivo);?>" class="form-control" id="success">
									<span class="help-block"></span>
								</div>
								<div class="">
									<input type="file" name="archivo" class="form-control" id="default" placeholder="Archivo a Cargar en PDF" required="required">
								</div>
							</div>					        
							<?php }} ?>
							<div class="form-group">
								<div class="">
									<button type="submit" name="update" class="btn btn-primary btn-labeled">Actualizar<span class="btn-label btn-label-right"><i class="fa fa-check"></i></span></button>
							</div>						
										</form>										  
									</div>
								</div>
							</div>
							<!-- /.col-md-8 col-md-offset-2 -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- /.section -->
			</div>
			<!-- /.main-page -->
			<!-- /.right-sidebar -->
		</div>
		<!-- /.content-container -->
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