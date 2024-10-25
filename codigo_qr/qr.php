<!DOCTYPE html>
<html lang="es">
	
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta property="og:type" content="website" />
		<meta property="og:title" content="Generador de c&oacute;digos QR" />
		<meta property="og:description" content="Generador gratuito de códigos QR para texto, url, teléfonos, WIFI, SMS, GPS y mucho más." />
		<meta property="og:site_name" content="Generador de c&oacute;digos QR - CDP" />
		
		<meta property="og:locale" content="es_ES" />
		<link rel="icon" href="images/favicon.png" sizes="32x32" />
		<title>QR Generator - CDP</title>
		
		<link rel="stylesheet" href="ccs/bootstrap.min.css">
		<link rel="stylesheet" href="css/estilo.css">
		
		<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-88406718-4"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-88406718-4');
</script>


	</head>
	<script data-ad-client="ca-pub-6274765135713539" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<body>
	
		<div class="container">
			
			<div class="row">
				<div class="col">
					<h3 class="titulo"><b>Generador de códigos QR</b></h3>
				</div>
			</div>
			
			<br/>
			
			<div class="row">
				<div class="col">
					
					<form id="form_qr">
						<nav>
							<div class="nav nav-tabs" id="nav-tab" role="tablist">
								<a class="nav-link active" id="nav-texto-tab" data-toggle="tab" href="#nav-texto" role="tab" aria-controls="nav-texto" aria-selected="true"><i class="fas fa-file-alt"></i> Texto</a>
								<a class="nav-link" id="nav-url-tab" data-toggle="tab" href="#nav-url" role="tab" aria-controls="nav-url" aria-selected="false"><i class="fas fa-globe"></i> URL</a>
								<a class="nav-link" id="nav-telefono-tab" data-toggle="tab" href="#nav-telefono" role="tab" aria-controls="nav-telefono" aria-selected="false"><i class="fas fa-phone"></i> Tel&eacute;fono</a>
								<a class="nav-link" id="nav-vcard-tab" data-toggle="tab" href="#nav-vcard" role="tab" aria-controls="nav-vcard" aria-selected="false"><i class="fas fa-id-card"></i> Vcard</a>
								<a class="nav-link" id="nav-sms-tab" data-toggle="tab" href="#nav-sms" role="tab" aria-controls="nav-sms" aria-selected="false"><i class="fas fa-sms"></i> SMS</a>
								<a class="nav-link" id="nav-email-tab" data-toggle="tab" href="#nav-email" role="tab" aria-controls="nav-email" aria-selected="false"><i class="fas fa-envelope-square"></i> Email</a>
								<a class="nav-link" id="nav-gps-tab" data-toggle="tab" href="#nav-gps" role="tab" aria-controls="nav-gps" aria-selected="false"><i class="fas fa-map-marked-alt"></i> GPS</a>
								<a class="nav-link" id="nav-wifi-tab" data-toggle="tab" href="#nav-wifi" role="tab" aria-controls="nav-wifi" aria-selected="false"><i class="fas fa-wifi"></i> WIFI</a>
							</div>
						</nav>
						
						<div class="tab-content" id="nav-tabContent">
							<div class="tab-pane fade show active" id="nav-texto" role="tabpanel" aria-labelledby="nav-telefono-tab">
	<fieldset>
		<legend>
			<h5 class="titulo">Código QR para texto</h5>
		</legend>
		
		<div class="alert alert-info" role="alert">
			Introduce un texto para generar un código QR
		</div>
	</fieldset>

	<div class="form-group">
		<label for="txt_texto">Texto:</label>
		<textarea class="form-control" id="txt_texto" name="txt_texto" maxlength="1000" aria-describedby="textoHelp"></textarea>
		<small id="textoHelp" class="form-text text-muted">Máximo 1000 caracteres.</small>
	</div>
</div>
<div class="tab-pane fade" id="nav-url" role="tabpanel" aria-labelledby="nav-url-tab">

	<fieldset>
		<legend>
			<h5 class="titulo">Código QR para una direcci&oacute;n web</h5>
		</legend>
		
		<div class="alert alert-info" role="alert">
			Introduce una direcci&oacute;n web para generar un código QR.
		</div>
	</fieldset>

	<div class="form-group">
		<label for="url_url">URL:</label>
		<input type="url" class="form-control" id="url_url" name="url_url" />
	</div>
</div>

<div class="tab-pane fade" id="nav-telefono" role="tabpanel" aria-labelledby="nav-telefono-tab">
	<fieldset>
		<legend>
			<h5 class="titulo">Código QR para telefono</h5>
		</legend>
		
		<div class="alert alert-info" role="alert">
			Introduce un n&uacute;mero tel&eacute;fonico para generar un código QR.
		</div>
	</fieldset>

	<div class="form-group">
		<label for="tel_numero">Telefono:</label>
		<input type="tel" class="form-control" id="tel_numero" name="tel_numero" maxlength="15" />
	</div>
</div>

<div class="tab-pane fade" id="nav-sms" role="tabpanel" aria-labelledby="nav-sms-tab">
	<fieldset>
		<legend>
			<h5 class="titulo">Código QR para SMS</h5>
		</legend>
		
		<div class="alert alert-info" role="alert">
			Introduce un n&uacute;mero tel&eacute;fonico y un mensaje para generar un código QR.
		</div>
	</fieldset>
	<div class="form-group">
		<label for="sms_numero">N&uacute;mero tel&eacute;fonico:</label>
		<input type="tel" class="form-control" id="sms_numero" name="sms_numero" />
	</div>
	<div class="form-group">
		<label for="sms_mensaje">Mensaje:</label>
		<textarea class="form-control"  id="sms_mensaje" name="sms_mensaje" maxlength="160" aria-describedby="smsMensajeHelp"></textarea>
		<small id="smsMensajeHelp" class="form-text text-muted">Máximo 160 caracteres.</small>
	</div>
</div>

<div class="tab-pane fade" id="nav-vcard" role="tabpanel" aria-labelledby="nav-vcard-tab">
	<fieldset>
		<legend>
			<h5 class="titulo">Código QR para una Vcard</h5>
		</legend>
		
		<div class="alert alert-info" role="alert">
			Introduce los datos de contacto para generar un código QR.
		</div>
	</fieldset>
	
	<div class="form-row">
		<div class="form-group col-md-12">
			<label for="vc_nombre">Nombre:</label>
			<input type="text" class="form-control" id="vc_nombre" name="vc_nombre" />
		</div>
	</div>
	
	<div class="form-row">
		<div class="form-group col-md-6">
			<label for="vc_empresa">Compa&ntilde;a:</label>
			<input type="text" class="form-control" id="vc_empresa" name="vc_empresa" />
		</div>
		<div class="form-group col-md-6">
			<label for="vc_cargo">Cargo:</label>
			<input type="text" class="form-control" id="vc_cargo" name="vc_cargo" />
		</div>
	</div>
	
	<div class="form-row">
		<div class="form-group col-md-12">
			<label for="vc_web">Web:</label>
			<input type="url" class="form-control" id="vc_web" name="vc_web" />
		</div>
	</div>
	
	<div class="form-row">
		<div class="form-group col-md-12">
			<label for="vc_direccion">Direcci&oacute;n:</label>
			<input type="text" class="form-control" id="vc_direccion" name="vc_direccion" />
		</div>
	</div>
	
	<div class="form-row">
		<div class="form-group col-md-6">
			<label for="vc_telefono">N&uacute;mero tel&eacute;fonico:</label>
			<input type="tel" class="form-control" id="vc_telefono" name="vc_telefono" />
		</div>
		<div class="form-group col-md-6">
			<label for="vc_email">Correo electr&oacute;nico:</label>
			<input type="email" class="form-control" id="vc_email" name="vc_email" />
		</div>
	</div>								
</div>

<div class="tab-pane fade" id="nav-email" role="tabpanel" aria-labelledby="nav-email-tab">
	<fieldset>
		<legend>
			<h5 class="titulo">Código QR para enviar correo electr&oacute;nico</h5>
		</legend>
		
		<div class="alert alert-info" role="alert">
			Introduce una direcci&oacute;n de correo electr&oacute;nico, el asunto y el mensaje para generar un código QR.
		</div>
	</fieldset>
	
	<div class="form-group">
		<label for="email_email">Correo electr&oacute;nico:</label>
		<input type="email" class="form-control" id="email_email" name="email_email" />
	</div>
	
	<div class="form-group">
		<label for="email_asunto">Asunto:</label>
		<input type="text" class="form-control" id="email_asunto" name="email_asunto" />
	</div>
	
	<div class="form-group">
		<label for="email_mensjae">Mensaje:</label>
		<textarea class="form-control" id="email_mensjae" name="email_mensjae" maxlength="200" aria-describedby="emailMensajeHelp"></textarea>
		<small id="emailMensajeHelp" class="form-text text-muted">Máximo 200 caracteres.</small>
	</div>
</div>

<div class="tab-pane fade" id="nav-gps" role="tabpanel" aria-labelledby="nav-gps-tab">
	<fieldset>
		<legend>
			<h5 class="titulo">Código QR para una geolocalizaci&oacute;n</h5>
		</legend>
		
		<div class="alert alert-info" role="alert">
			Permite acceder a tu ubicaci&oacute;n o introduce una latitud y una longitd para generar un código QR.
		</div>
	</fieldset>
	
	<div class="form-row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="gps_latitud">Latitud:</label>
				<input type="text" class="form-control" id="gps_latitud" name="gps_latitud" />
			</div>
			<div class="form-group">
				<label for="gps_longitud">Longitud:</label>
				<input type="text" class="form-control" id="gps_longitud" name="gps_longitud" />
			</div>
		</div>
		<div class="col-md-6 embed-responsive" id="map">
			<div id="mensaje" style="color:black"></div>
			<div id="errores" style="color:red"></div>
		</div>
	</div>	
</div>

<div class="tab-pane fade" id="nav-wifi" role="tabpanel" aria-labelledby="nav-wifi-tab">
	<fieldset>
		<legend>
			<h5 class="titulo">Código QR para WIFI</h5>
		</legend>
		
		<div class="alert alert-info" role="alert">
			Introduce el nombre de la red (SSID), la contrase&ntilde;a de la red y el tipo de encriptaci&oacute;n de seguridad de la red para generar un código QR.
		</div>
	</fieldset>

	<div class="form-group">
		<label for="wifi_ssid">Nombre de red (SSID):</label>
		<input type="text" class="form-control" id="wifi_ssid" name="wifi_ssid" />
	</div>
	
	<div class="form-row">
		<div class="form-group col-md-6">
			<label for="wifi_password">Contrase&ntilde;a:</label>
			<input type="text" class="form-control" id="wifi_password" name="wifi_password" />
		</div>
		
		<div class="form-group col-md-6">
			<label for="wifi_seguridad">Tipo de seguridad:</label>
			<select class="form-control" id="wifi_seguridad" name="wifi_seguridad">
				<option value="WEP">WEP</option>
				<option value="WPA">WPA</option>
				<option value="">Sin contrase&ntilde;a</option>
			</select>
		</div>	
	</div>	
</div>	

</div>
						
						<div class="form-row">
							<div class="form-group col-md-6">
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
							<div class="form-group col-md-6">
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
						
						<input type="hidden" id="tab-activo" name="tab-activo" value="nav-texto-tab" />
						
						<button type="button" id="generar" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Generar código QR</button>
						<!--<button type="button" id="descargar" class="btn btn-success"><i class="fas fa-download"></i> Descargar código QR</button>-->
						
					</form>
					
					<div class="row qr">
						<img id="img-qr" />
						<a id="link-img" download="qr-code.png" hidden></a>
					</div>
				</div>
			</div>
			
			<div class="row">
			    <div class="col"></div>
			    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<!-- Contenido Pie -->
				<ins class="adsbygoogle"
				style="display:block"
				data-ad-client="ca-pub-6274765135713539"
				data-ad-slot="5421310400"
				data-ad-format="auto"
				data-full-width-responsive="true"></ins>
				<script>
					(adsbygoogle = window.adsbygoogle || []).push({});
				</script>
			</div>
		</div>
		
		<script src="https://use.fontawesome.com/releases/v5.13.1/js/all.js" data-auto-replace-svg="nest"></script>
		<script src="js/jquery-3.5.1.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap.bundle.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIm6sL7CVJU8A94dY6KVMkZOxNJSdkelg">
		</script>
		<script src="js/functions.js"></script>

	</body>
	
</html>