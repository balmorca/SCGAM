<?php
    require 'Config/Conexdb.php';
    require 'Config/config.php';
    require 'clases/invfunciones.php';
    $db = new Database();
    $con = $db ->conectar();
    $errors = [];
// Obtener el nombre del archivo desde la URL
$id = $_GET['id'];

// Buscar el archivo en la base de datos
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

<?php 
    $archivo = htmlentities($result->archivo);
    $ruta_archivo = "archivos/manuales/" . $archivo;
    // Verificar que el archivo exista en el servidor
    if (file_exists($ruta_archivo)) {
        // Enviar el archivo al navegador
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="'. $archivo . '"');
        readfile($ruta_archivo);
    } else {
        echo "<script language='JavaScript'>
        alert('Documento no disponible, Por Favor, Contacte a Soporte. ');
        window.close();
        </script>";
    }
      ?>                                                      
<?php }} ?>
