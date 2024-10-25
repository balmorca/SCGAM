<?php
	

if(isset($_POST['submit']))
{	
	require 'phpqrcode/qrlib.php';

	$dir= 'temp/';
	
	echo $dir;

	if(file_exists($dir)){ 
        @mkdir($dir, 0700); 
    }else{ 
        echo "Ya existe ese directorio\n"; 
    } 

	echo "<script language='JavaScript'>alert('Archivo Generado');
                    </script>";
	
	$filename = $dir.'test.png';
	
	$tamanio=$_POST['tamanio'];
	$level=$_POST['ecc'];
	$frameSize = 1;
	$contenido=$_POST['url_url'];

	QRcode::png($contenido, $filename, $level, $tamanio, $frameSize);
	
	echo '<img src="'.$filename.'" />';
}
?>

<!DOCTYPE html>
<html lang="es">
	
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta property="og:type" content="website" />		
		<meta property="og:locale" content="es_ES" />
		<link rel="icon" href="images/favicon.png" sizes="32x32" />
		<title>QR Generator - CDP</title>
		
		<link rel="stylesheet" href="ccs/bootstrap.min.css">

<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-88406718-4');
</script>


	</head>

	<body>

		<div class="container">
			
			<div class="row">
				<div class="col">
					<h3 class="titulo"><b>Generador de códigos QR</b></h3>
				</div>
			</div>

			<!-- -->
<form class="" method="post">		

	<h5 class="titulo">Código QR para una direcci&oacute;n web</h5>
		<div class="alert alert-info" role="alert">
			Introduce una direcci&oacute;n web para generar un código QR.
		</div>
		<div class="form-row">
			<div class="form-group">
				<label for="url_url">URL:</label>
				<input type="url" class="form-control" id="url_url" name="url_url" />
			</div>
			<div class="form-group col-md-2">
				<a data-toggle="tooltip" title="Selecciona el tamaño del código QR"><i class="fas fa-info-circle"></i></a>
				<label for="campo">Tamaño:</label>
				
				<select class="form-control" id="tamanio" name="tamanio">
						<option value="1" >1</option>
						<option value="2" >2</option>
						<option value="3" >3</option>
						<option value="4" >4</option>
						<option value="5" >5</option>
						<option value="6" >6</option>
						<option value="7" selected>7</option>
						<option value="8" >8</option>
						<option value="9" >9</option>
						<option value="10" >10</option>
				</select>
			</div>
			
			<div class="form-group col-md-2">
				<a data-toggle="tooltip" title="Nivel ECC (capacidad de corrección de errores). Esto compensa la suciedad, daños o borrosidad del código de barras. Un alto nivel de ECC agrega más redundancia a costa de usar más espacio"><i class="fas fa-info-circle"></i></a>
				<label for="campo">Redundancia:</label>
				<select class="form-control" id="ecc" name="ecc">
					<option value="L">Baja</option>
					<option value="M" selected>Media</option>
					<option value="Q">Alta</option>
					<option value="H">Muy alta</option>
				</select>
			</div>
		</div>

							
		<button type="submit" id="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Generar código QR</button>
		<button type="button" id="descargar" class="btn btn-success"><i class="fas fa-download"></i> Descargar código QR</button>

</form>

					<div class="col-md-8 col-md-offset-2">
					<!--	<img src="temp/test.png" /> -->
					</div>
						
			<!-- -->

		</div>
	</body>	

</html>	