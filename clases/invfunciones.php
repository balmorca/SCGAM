<?php
# Valida Si hay datos en el Formulario
function esNulo(array $parametros){
    foreach($parametros as $parametro){
        if(strlen(trim($parametro)) < 1) {
            return true;
        }
    }
    return false;
}
# Muestra los mensajes y errores en pantalla
function mostrarMensajes(array $errors) {
    if(count($errors) > 0) {
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
        foreach($errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '<ul>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}
#Validación De Login
function login($usuario, $password, $con) {
    $sql = $con->prepare("SELECT UserName, NombreCompleto, Password, permition FROM admin WHERE UserName = :usuario LIMIT 1");
    $sql->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $sql->execute();
    if ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        #Mejorar verificacion de seguridad de Password
        if ($password == $row['Password']) {
            $_SESSION['user_usertify'] = $row['UserName'];
            $_SESSION['user_nametify'] = $row['NombreCompleto'];
            $_SESSION['user_userpermittify'] = $row['permition'];
            $_SESSION['user_idtify'] = $row['id_userinterno'];
            header('Location: inicio.php');
            exit;
        } else {
            return 'USUARIO BLOQUEADO! <br> Contacte a Soporte para mayor informacion';
        }
    }
    return 'El usuario y/o contraseña son incorrectos.';
}

# Pdf Inventario
function PdfInventario($con) {
    include 'pdf.php';
    $jquery1 = $con->prepare("SELECT id, nombre, descripcion, cantidad, proveedor, serialid, fecha FROM productos");
    $jquery1->execute();
    $resultPdf = $jquery1->fetchAll(PDO::FETCH_ASSOC);
    $pdf = new PDF_Compras_Clientes();
    $pdf->AliasNbPages();
    // Nueva Pagina
    $pdf->AddPage();
    // Estilo de la celda
    //$pdf->SetFont('Arial', 'B', 15);
    // Posicion
    //$pdf->SetX(60);
    // Celdas
    //$pdf->Cell(100, 20, 'Hola Mundo', 1, 1, 'C');
    // Separar Celdas
    //$y = $pdf->GetY();
    //$pdf->SetY($y+10);
    // Celdas Ajustables automaticamente
    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->SetX(15);
    $pdf->Cell(40, 6, 'SERIAL', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, 'NOMBRE', 1, 0, 'C', 1);
    $pdf->Cell(50, 6, 'DESCRIPCION', 1, 0, 'C', 1);
    $pdf->Cell(20, 6, 'CANTIDAD', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, 'FECHA DE COMPRA', 1, 1, 'C', 1);
    $pdf->SetFont('Arial', '', 9);
    foreach($resultPdf as $row) {
        $pdf->SetX(15);
        $pdf->Cell(40, 7, $row['serialid'], 1, 0, 'C');
        $pdf->Cell(30, 7, $row['nombre'], 1, 0, 'C');
        $pdf->Cell(50, 7, $row['descripcion'], 1, 0, 'C');
        $pdf->Cell(20, 7, $row['cantidad'], 1, 0, 'C');
        $pdf->Cell(30, 7, $row['fecha'], 1, 1, 'C');
    }
    $pdf->Output();
}
# Valida si el nombre del Producto Existe
function productoExiste($nombre, $con) {
    $sql = $con->prepare("SELECT id FROM productos WHERE nombre LIKE ? LIMIT 1");
    $sql->execute([$nombre]);
    if($sql->fetchColumn() > 0) {
        return true;
    }
    return false;
}
# Valida si el Serial Existe
function serialExiste($serialid, $con) {
    $sql = $con->prepare("SELECT id FROM productos WHERE serialid LIKE ? LIMIT 1");
    $sql->execute([$serialid]);
    if($sql->fetchColumn() > 0) {
        return true;
    }
    return false;
}
# Ingreso De Mercancia
function EntradaMercancia(array $datos, $con) {
    $sql = $con->prepare("INSERT INTO tblactivos (tipo_activo, descrip, estado, etiqueta, serial, modelo, marca, ip_mac, ubicacion) VALUES
    (?,?,?,?,?,?,?,?,?)");
    if($sql->execute($datos)){
        return $con->lastInsertId();
    }
}
# Ingreso de Movimiento De Mercancia
function MovimientosMercancia(array $datos, $con) {
    $sql = $con->prepare("INSERT INTO movimientos (serialid, cantidad, movimiento, descripcion, proveedor, fecha) VALUES
    (?,?,?,?,?,?)");
    if($sql->execute($datos)){
        return $con->lastInsertId();
    }
    header('Location: Consig_Empresas.php');
    exit;
}
# Obtener Nombre de la Empresa Segun el Codigo del Producto (INVENTARIO)
function obtenerNombreEmpresa($codigo) {
    // Extraemos las primeras letras (puedes ajustar según sea necesario)
    $prefijo = substr($codigo, 0, 3);
    // Arreglo asociativo para mapear prefijos a empresas
    $empresas = [
        'ALM' => 'ALMARCA',
        'BAL' => 'BALMORCA',
        'HAR' => 'HARIPESCA',
        'INV' => 'INVERSIONES EL MORRO',
        // Agrega más pares clave-valor según tus códigos y empresas
    ];

    // Verificamos si el prefijo existe en el arreglo
    if (array_key_exists($prefijo, $empresas)) {
        return $empresas[$prefijo];
    } else {
        return "Empresa no encontrada";
    }
}
#############################################################################################################
#############################################----USUARIOS----################################################
#############################################################################################################
#Registrar Usuario
function RegistrarUsuario($nombres, $rutaTemporal, $tipoArchivo, $archivo, $nombreArchivoOriginal, $cedula, $indicador, $departamento, $fingreso, $cargo, $empresa_nomina, $con) {
    try {
    $sql = "INSERT INTO tblusuarios (nombres, cedula, indicador, departamento, fingreso, cargo, empresa_nomina) VALUES (:nombres, :cedula, :indicador, :departamento, :fingreso, :cargo, :empresa_nomina)";
    $query = $con->prepare($sql);
    $query->bindParam(':nombres', $nombres, PDO::PARAM_STR);
    $query->bindParam(':cedula', $cedula, PDO::PARAM_STR);
    $query->bindParam(':indicador', $indicador, PDO::PARAM_STR);
    $query->bindParam(':departamento', $departamento, PDO::PARAM_STR);
    $query->bindParam(':fingreso', $fingreso, PDO::PARAM_STR);
    $query->bindParam(':cargo', $cargo, PDO::PARAM_STR);
    $query->bindParam(':empresa_nomina', $empresa_nomina, PDO::PARAM_STR);
    $query->execute();
    // Sanitizar el nombre del archivo (elimina caracteres especiales)
    $nombreArchivoSeguro = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombreArchivoOriginal);
    // Crear una carpeta segura para el usuario (evita inyección de directorios)
    $carpetaBase = 'archivos/Doc_Empresas/';
    $idUsuario = $_SESSION['user_id']; // Asumiendo que tienes un ID de usuario
    $carpetaUsuario = "usuario_$idUsuario";
    $rutaDestino = $carpetaBase . $carpetaUsuario;
    // Crear la carpeta si no existe
    if (!is_dir($rutaDestino)) {
        mkdir($rutaDestino, 0755, true); // Crear la carpeta y sus subcarpetas si es necesario
    }
    // Generar una ruta única para el archivo
    $rutaCompleta = $rutaDestino . '/' . uniqid() . '_' . $nombreArchivoSeguro;
    // Mover el archivo
    if (move_uploaded_file($rutaTemporal, $rutaCompleta)) {
        // Éxito
        echo "Archivo subido correctamente.";
    } else {
        // Error al subir el archivo
        error_log("Error al subir el archivo: " . error_get_last()['message']);
        echo "Error al subir el archivo. Por favor, inténtalo más tarde.";
    }
    header('Location: Config_Usuarios.php');
    exit;

    } catch (PDOException $e) {
    // Manejar la excepción (loggear, mostrar mensaje, etc.)
    echo "Error al registrar Usuario: " . $e->getMessage();
    return false;
    }
}

#Editar Usuario
function EditarUsuarios($indicador, $editnombres, $editcedula, $editdepartamento, $editfingreso, $editcargo, $con) {
    try {
        // Preparar la consulta SQL
        $sql = "UPDATE tblusuarios SET nombres = :nombres, cedula = :cedula, departamento = :departamento, 
        fingreso = :fingreso, cargo = :cargo WHERE indicador = :indicador";
        // Preparar la sentencia
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':indicador', $indicador, PDO::PARAM_STR);
        $stmt->bindParam(':nombres', $editnombres, PDO::PARAM_STR);
        $stmt->bindParam(':cedula', $editcedula, PDO::PARAM_STR);
        $stmt->bindParam(':departamento', $editdepartamento, PDO::PARAM_STR);
        $stmt->bindParam(':fingreso', $editfingreso, PDO::PARAM_INT);
        $stmt->bindParam(':cargo', $editcargo, PDO::PARAM_STR);
        $stmt->execute();
        header('Location: Config_Usuarios.php');
        exit;
    } catch (PDOException $e) {
        // Mostrar un mensaje de error más específico
        echo "Error al editar el Usuario: " . $e->getMessage() . ". Por favor, contacta al administrador.";
        return ['error' => true, 'message' => $e->getMessage()];
    }
}

#Eliminar Usuario
function eliminar_usuario($id) {
    // Preparar y ejecutar la consulta DELETE
    $stmts = $con->prepare("DELETE * FROM tblusuario WHERE id = :id");
    $stmts->bind_param("i", $id);
    $stmts->execute();
    if ($stmts->affected_rows > 0) {
        print_r("Usuario Eliminado");
        $response = ['ok' => true];
    } else {
        $response = ['ok' => false, 'error' => 'Error al eliminar el usuario'];
    }
    $stmts->close();
    $con->close();
    return $response;
}

#Pdf Ingreso de Usuarios
function PdfUsuariosActas($id, $con) {
    include 'pdf-usuario.php';
    $jquery1 = $con->prepare("SELECT * FROM tblusuarios WHERE id=:id");
    $jquery1->bindParam(':id', $id, PDO::PARAM_STR);
    $jquery1->execute();
    $resultPdf = $jquery1->fetchAll(PDO::FETCH_ASSOC);
    //Iniciando un nuevo pdf
    $pdf = new PDF_Compras_Clientes();
    $pdf->AliasNbPages();
    foreach($resultPdf as $result) {
        $imagePath = 'images/' . strtoupper($result['empresa_nomina']) . '.png';
        if (!file_exists($imagePath)) {
            $imagePath = 'images/' . strtoupper($result['empresa_nomina']) . '.jpg';
            if (!file_exists($imagePath)) {
                echo "Imagen no encontrada";
                exit;
            }
        }
    // Nueva Pagina
    // $pdf->AddPage();
    // Estilo de la celda
    //$pdf->SetFont('Arial', 'B', 15);
    // Posicion
    //$pdf->SetX(60);
    // Celdas
    //$pdf->Cell(100, 20, 'Hola Mundo', 1, 1, 'C');
    // Separar Celdas
    //$y = $pdf->GetY();
    //$pdf->SetY($y+10);
    // Celdas Ajustables automaticamente
    //Establecer margenes del PDF
    //Informacion del PDF
    $pdf->SetCreator(utf8_decode('Asesoria GM'));
    $pdf->SetAuthor(utf8_decode('Victor Perez'));
    $pdf->SetTitle(utf8_decode('.: SCAGM 1.1 :.'));
    /** Eje de Coordenadas
     *          Y
     *          -
     *          - 
     *          -
     *  X ------------- X
     *          -
     *          -
     *          -
     *          Y
     * 
     * $pdf->SetXY(X, Y);
     */
    //Agregando la primera página
    $pdf->AddPage();
    $pdf->Image($imagePath, 5, 5, 30);
    $pdf->SetFont('helvetica','B',12); //Tipo de fuente y tamaño de letra
    $pdf->SetXY(155, 10);
    $pdf->Write(25, ''. date('d-M-Y'));
    //$app ='SCAGM 1.0';
    //$pdf->SetFont('helvetica','B',10); //Tipo de fuente y tamaño de letra
    //$pdf->SetXY(15, 20); //Margen en X y en Y
    //$pdf->SetTextColor(204,0,0);
    //$pdf->Write(0, 'Desarrollador: Victor Pérez');
    //$pdf->SetTextColor(0, 0, 0); //Color Negrita
    //$pdf->SetXY(15, 25);
    //$pdf->Write(0, 'Sistema: '. $app);
    $pdf->Ln(10); //Salto de Linea
    $pdf->Cell(40,26,'',0,0,'C');
    /*$pdf->SetDrawColor(50, 0, 0, 0);
    $pdf->SetFillColor(100, 0, 0, 0); */
    //$pdf->SetTextColor(34,68,136);
    //$pdf->SetTextColor(255,204,0); //Amarillo
    //$pdf->SetTextColor(34,68,136); //Azul
    //$pdf->SetTextColor(153,204,0); //Verde
    //$pdf->SetTextColor(204,0,0); //Marron
    //$pdf->SetTextColor(245,245,205); //Gris claro
    //$pdf->SetTextColor(100, 0, 0); //Color Carne
    $pdf->SetFont('helvetica','B', 13); 
    $pdf->Cell(60,5,'',0,0,'C');
    $pdf->SetFont('helvetica','B', 15); 
    $pdf->Ln(14); //Salto de Linea
    $pdf->Cell(0,6,utf8_decode('ACTA DE ENTREGA DE CARGO'),0,0,'C');
    //$pdf->Ln(2); //Salto de Linea
    //$pdf->SetTextColor(0, 0, 0); 
    //Armando la cabecera de la Tabla
    $pdf->SetFillColor(232,232,232);
    $pdf->SetFont('helvetica','B',12); //La B es para letras en Negritas
    $pdf->Ln(14); //Salto de Linea
    $pdf->Cell(100,6,utf8_decode('Persona Responsable'),0,0,'C');
    $pdf->Ln(8); //Salto de Linea
    $pdf->Cell(90,6,utf8_decode('Datos del Cargo '),0,0,'C');
    $pdf->Ln(5); //Salto de Linea
    $pdf->SetFont('helvetica','',12); //La B es para letras en Negritas
        $pdf->Cell(70,6,utf8_decode('Cargo '),0,0,'C');
        //$pdf->Ln(2); //Salto de Linea        
        $pdf->Cell(10,6,(' '.utf8_decode($result['cargo'])),0,0,'B'); 
        $pdf->Ln(5); //Salto de Linea
        $pdf->Cell(90,6,utf8_decode('Grado del Cargo '),0,0,'C'); 
        //$pdf->Ln(2); //Salto de Linea           
        $pdf->Cell(10,6,(''.utf8_decode($result['departamento'])),0,0,'B');                    
        $pdf->Ln(10); //Salto de Linea
        $pdf->SetFont('helvetica','B',12); //La B es para letras en Negritas        
        $pdf->Cell(127,6,utf8_decode('Nombres Apellidos del Trabajador'),0,0,'C');   
        $pdf->SetFont('helvetica','',12); //La B es para letras en Negritas             
        $pdf->Ln(10); //Salto de Linea           
        $pdf->Cell(127,6,(''.utf8_decode($result['nombres'])),0,0,'C');                    
        $pdf->Ln(10); //Salto de Linea
        $pdf->SetFont('helvetica','B',12); //La B es para letras en Negritas        
        $pdf->Cell(127,6,utf8_decode('Relacion de Equipos de Oficina'),0,0,'C');   
        $pdf->SetFont('helvetica','',12); //La B es para letras en Negritas             
        $pdf->Ln(10); //Salto de Linea       
        $pdf->Cell(175,4,(utf8_decode('1 Equipo de Computacion: ').$result['cedula']),0,0,'C');
        $pdf->Ln(5); //Salto de Linea
        $pdf->Cell(190,4,(utf8_decode('Monitor:   ').utf8_decode($result['departamento'])),0,0,'C');
        $pdf->Ln(5); //Salto de Linea
        $pdf->Cell(132,4,(utf8_decode('Ubicacion: ').utf8_decode($result['empresa_nomina'])),0,0,'C');
        // $pdf->Ln(10); //Salto de Linea
        //$cnt=$cnt+1;
        // Texto despues de la Descripción
        $pdf->SetFont('helvetica','',12); //La B es para letras en Negritas
        $pdf->Ln(14); //Salto de Linea
        $pdf->Cell(180,6,utf8_decode('Se hace contar que el equipo de computacion se encuentra en perfectas '),0,0,'C');
        $pdf->Ln(5); //Salto de Linea
        $pdf->Cell(182,6,utf8_decode('condiciones y en normal funcionamiento, donde se puede apreciar toda la '),0,0,'C');
        $pdf->Ln(5); //Salto de Linea
        $pdf->Cell(175,6,utf8_decode('documentacion y correos utilizados por la persona antes mencionada. '),0,0,'C');
        $pdf->Ln(20); //Salto de Linea
        $pdf->Cell(90,6,utf8_decode('Sin Mas que informar. - '),0,0,'C');
        // $pdf->Ln(5); //Salto de Linea
        $pdf->SetFont('helvetica','',12); //La B es para letras en Negritas
        // Texto despues de la Descripción
        $pdf->SetFont('helvetica','',12); //Tipo de fuente y tamaño de letra
        $pdf->SetXY(30, 200);
        $pdf->Write(0, '________________________');
        $pdf->SetXY(120, 200);
        $pdf->Write(0, '________________________');
        //$pdf->Ln(5); //Salto de Linea
        $pdf->SetXY(50, 205);
        $pdf->Write(0,utf8_decode('Victor Pérez '));
        $pdf->SetXY(135, 205);
        $pdf->Write(0,utf8_decode('Administrador '));
        //$pdf->Ln(5); //Salto de Linea
        $pdf->SetXY(33, 210);
        $pdf->Write(0,utf8_decode('Departamento de Sistemas'));
        $pdf->SetXY(130, 210);
        $pdf->Write(0,utf8_decode('Empresa Balmorca'));
        $pdf->SetXY(33, 215);
        $pdf->Write(0,utf8_decode('Empresa Del EQUIPO'));
        $pdf->SetXY(130, 215);
        $pdf->Write(0,utf8_decode('Empresa Balmorca'));
        $pdf->SetXY(70, 230);
        $pdf->Write(0, '________________________');
        $pdf->SetXY(90, 235);
        $pdf->Write(0,(''.utf8_decode($result['departamento'])));
        $pdf->SetXY(75, 240);
        $pdf->Write(0,utf8_decode('Departamento de Sistemas'));
        $pdf->SetXY(80, 245);
        $pdf->Write(0,utf8_decode('Empresa Balmorca'));
        $pdf->SetFont('helvetica','',12); //La B es para letras en Negritas
        //$pdf->AddPage(); //Agregar nueva Pagina
        $pdf->Output(utf8_decode('Activo-Fijo-Balmorca-').date('d-m-y').'.pdf', 'I'); 
        // Output funcion que recibe 2 parameros, el nombre del archivo, ver archivo o descargar,
        // La D es para Forzar una descarga
    }  
}

#Elevar Usuario
function ElevarUsuarios($indicador, $permition, $password, $usuario, $empresas_autoriz, $con) {
    try {
    $jquery1 = $con->prepare("SELECT * FROM tblusuarios WHERE indicador=:id_userinterno");
    $jquery1->bindParam(':id_userinterno', $indicador, PDO::PARAM_STR);
    $jquery1->execute();
    $DataUsuarios = $jquery1->fetchAll(PDO::FETCH_ASSOC);
    $sql = "INSERT INTO admin (NombreCompleto, UserName, Password, permition, empresas_autoriz, id_userinterno) VALUES (:indicador, :permition, :password, :empresas_autoriz)";
    $query = $con->prepare($sql);
    $query->bindParam(':id_userinterno', $indicador, PDO::PARAM_STR);
    $query->bindParam(':permition', $permition, PDO::PARAM_STR);
    $query->bindParam(':Password', $password, PDO::PARAM_STR);
    $query->bindParam(':NombreCompleto', $DataUsuarios["nombres"], PDO::PARAM_STR);
    $query->bindParam(':UserName', $usuario, PDO::PARAM_STR);
    $query->bindParam(':permition', $permition, PDO::PARAM_STR);
    $query->bindParam(':permition', $permition, PDO::PARAM_STR);
    $query->bindParam(':empresas_autoriz', $empresas_autoriz, PDO::PARAM_STR);
    $query->execute();
    header('Location: Config_Usuarios.php');
    exit;

    } catch (PDOException $e) {
    // Manejar la excepción (loggear, mostrar mensaje, etc.)
    echo "Error al registrar Usuario: " . $e->getMessage();
    return false;
    }
}
#############################################################################################################
#############################################----EMPRESAS----################################################
#############################################################################################################
#Registrar Empresa
function RegistrarEmpresa($NombreEmpresa, $NombreCorto, $rif, $ubicacion, $direccion, $menu_opcionesSeleccionadas, $con) {
    try {
        $sql = "INSERT INTO tblempresa (nombre, nombre_corto, rif, ubicacion, direccion, menu_opciones) VALUES (:nombre, :nombre_corto, :rif, :ubicacion, :direccion, :menu_opciones)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nombre', $NombreEmpresa, PDO::PARAM_STR);
        $stmt->bindParam(':nombre_corto', $NombreCorto, PDO::PARAM_STR);
        $stmt->bindParam(':rif', $rif, PDO::PARAM_STR);
        $stmt->bindParam(':ubicacion', $ubicacion, PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':menu_opciones', $menu_opcionesSeleccionadas, PDO::PARAM_STR);
        $stmt->execute();
        
        header('Location: Config_Empresas.php');
        exit;
    } catch (PDOException $e) {
        // Registrar el error en un log (si es necesario)
        error_log("Error al registrar la empresa: " . $e->getMessage());

        // Mostrar un mensaje de error más específico
        echo "Error al registrar la empresa: " . $e->getMessage() . ". Por favor, contacta al administrador.";
        return ['error' => true, 'message' => $e->getMessage()];
    }
}

#Editar Empresa
function EditarEmpresa($id, $datosEmpresa, $con) {
    try {
        // Preparar la consulta SQL
        $sql = "UPDATE tblempresa SET
                nombre = :nombre,
                nombre_corto = :nombre_corto,
                rif = :rif,
                ubicacion = :ubicacion,
                direccion = :direccion,
                telefono = :telefono,
                contacto = :contacto
                WHERE id = :id";
        // Preparar la sentencia
        $stmt = $con->prepare($sql);
        // Vincular los parámetros
        foreach ($datosEmpresa as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR); // Asumimos que todos los valores son strings
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT); // Especificar el tipo de dato para el ID
        // Ejecutar la consulta
        $stmt->execute();
        // Verificar si se actualizaron filas
        if ($stmt->rowCount() > 0) {
            return true; // Actualización exitosa
        } else {
            return false; // No se actualizaron filas
        }
    } catch (PDOException $e) {
        // Manejar el error
        // echo "Error al actualizar la empresa: " . $e->getMessage();
        return false;
    }
}

#Pdf Ficha Tecnica Empresas
function PdfEmpresas($id, $con) {
    include 'pdf-usuario.php';
    $jquery1 = $con->prepare("SELECT * FROM tblempresa WHERE id=:id");
    $jquery1->bindParam(':id', $id, PDO::PARAM_STR);
    $jquery1->execute();
    $resultPdf = $jquery1->fetchAll(PDO::FETCH_ASSOC);
    //Iniciando un nuevo pdf
    $pdf = new PDF_Empresas();
    $pdf->AliasNbPages();
    $pdf->SetCreator('Asesoria GM');
    $pdf->SetAuthor('Victor Perez');
    $pdf->SetTitle('.: SCAGM 1.1 :.');
    $pdf->AddPage();
   /*  $pdf->SetFont('helvetica','B',12); //Tipo de fuente y tamaño de letra
    $pdf->SetXY(140, 10);
    $pdf->Write(0, ''. date('d-m-Y'));
    $pdf->SetXY(163, 10);
    $pdf->Write(0, ''. date('h:i A'));
    $pdf->Ln(10); //Salto de Linea
    $pdf->Cell(40,26,'',0,0,'C'); */
    foreach($resultPdf as $result) {
        // Cabecera
        $imagePath = 'images/' . $result['nombre_corto'] . '.png';
        if (!file_exists($imagePath)) {
            $imagePath = 'images/' . $result['nombre_corto'] . '.jpg';
            if (!file_exists($imagePath)) {
                // Manejar el caso en el que no se encuentra la imagen
                echo "Imagen no encontrada";
                exit;
            }
        }
        $pdf->Image($imagePath, 5, 5, 30);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('Ficha Técnica de Empresa'), 0, 1, 'C');
        $pdf->Cell(0, 10, $result['nombre'], 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Fecha de Elaboracion: ') . date('d-m-Y'), 0, 1, 'R');
        $pdf->SetFont('Arial', '', 12);
        // Información general
        $pdf->Cell(50, 10, 'RIF:', 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 10, $result['rif'], 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Contacto:', 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 10, $result['contacto'], 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, utf8_decode('Dirección:'), 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->MultiCell(0, 5, $result['direccion']);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, utf8_decode('Teléfono:'), 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 10, $result['telefono'], 0, 1);
        $pdf->SetFont('Arial', '', 12);
        // Descripción de la empresa
        $pdf->Ln(10);
        $pdf->Cell(0, 10, utf8_decode('Descripción de la Empresa:'), 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 5, utf8_decode($result['descripcion'])); // Reemplaza con la descripción real
        $pdf->SetFont('Arial', '', 12);
        // Equipo de computación
        $pdf->Ln(10);
        $pdf->Cell(0, 10, utf8_decode('Equipo de Computación:'), 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 5, utf8_decode('El equipo de computación se encuentra en perfectas condiciones y en normal 
        funcionamiento. Toda la documentación y correos electrónicos relacionados con el uso del equipo se encuentran 
        disponibles y organizados.'));    
        // Firma y sello
        $pdf->Ln(20);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 10, '_________________________________', 0, 1, 'C');
        $pdf->Cell(0, 10, utf8_decode('Firma del Responsable'), 0, 1, 'C');
    } 
    // Generar el PDF
    $pdf->Output(utf8_decode('Ficha_Tecnica_') . utf8_decode($result['nombre_corto']) . '.pdf', 'I');
}

function usuariosperfil(){
    
    if(isset($_POST['submit'])) {
        $correlativo=$_POST['correlativo'];
        $apellidos_nonbre=$_POST['apellidos_nombre'];
        $rif=$_POST['rif'];
        $fnacimiento=$_POST['fnacimiento'];
        $edad=$_POST['edad'];
        $sexo=$_POST['sexo'];
        $lnacimiento=$_POST['lnacimiento'];
        $ecivil=$_POST['ecivil'];
        $direccion=$_POST['direccion'];
        $parroquia=$_POST['parroquia'];
        $thabitacion=$_POST['thabitacion'];
        $tmovil=$_POST['tmovil'];
        $email=$_POST['email'];
        $ginstruccion=$_POST['ginstruccion'];
        $titulo=$_POST['titulo'];
        $especialidad=$_POST['especialidad'];
        $motricidad=$_POST['motricidad'];
        $discapacidad=$_POST['discapacidad'];
        $automovil=$_POST['automovil'];
        $placa=$_POST['placa'];
        $propi_autor_vehi=$_POST['propi_autor_vehi'];
        $lentes=$_POST['lentes'];
        $sangre=$_POST['sangre'];
        $enfermedad=$_POST['enfermedad'];
        $eme_telef1=$_POST['eme_telef1'];
        $eme_telef2=$_POST['eme_telef2'];
        $eme_parentezco=$_POST['eme_parentezco'];
        $departamento=$_POST['departamento'];
        $cargo=$_POST['cargo'];
        $sueldobase=$_POST['sueldobase'];
        $ncuenta=$_POST['ncuenta'];
        $comocido_gru_paret=$_POST['comocido_gru_paret'];
        $legajo=$_POST['legajo'];
        $peso=$_POST['peso'];
        $pantalon=$_POST['pantalon'];
        $camisa=$_POST['camisa'];
        $estatura=$_POST['estatura'];
        $jinmediato=$_POST['jinmediato'];
        $empresa_old1=$_POST['empresa_old1'];
        $cargo_old1=$_POST['carogo_old1'];
        $duracion_old1=$_POST['duracion_old1'];
        $telefono_old1=$_POST['telefono_old1'];
        $sueldo_old1=$_POST['sueldo_old1'];
        $empresa_old2=$_POST['empresa_old2'];
        $cargo_old2=$_POST['carogo_old2'];
        $duracion_old2=$_POST['duracion_old2'];
        $telefono_old2=$_POST['telefono_old2'];
        $sueldo_old2=$_POST['sueldo_old2'];
        $ape_nom_cony=$_POST['ape_nom_cony'];
        $fnaci_cony=$_POST['fnaci_cony'];
        $rid_cony=$_POST['rif_cony'];
        $ocupa_cony=$_POST['ocupa_cony'];
        $gracidez_cony=$_POST['gravidez_cony'];
        $ape_nomb_pare1=$_POST['ape_nom_pare1'];
        $fnaci_pare1=$_POST['fnaci_pare1'];
        $rif_pare1=$_POST['rif_pere1'];
        $ocupa_pare1=$_POST['ocupa_pare1'];
        $discap_pare1=$_POST['discap_pare1'];
        $ape_nomb_pare2=$_POST['ape_nom_pare2'];
        $fnaci_pare2=$_POST['fnaci_pare2'];
        $rif_pare2=$_POST['rif_pere2'];
        $ocupa_pare2=$_POST['ocupa_pare2'];
        $discap_pare2=$_POST['discap_pare2'];
        $ape_nomb_pare3=$_POST['ape_nom_pare3'];
        $fnaci_pare3=$_POST['fnaci_pare3'];
        $rif_pare3=$_POST['rif_pere3'];
        $ocupa_pare3=$_POST['ocupa_pare3'];
        $discap_pare3=$_POST['discap_pare3'];
        $ape_nomb_pare4=$_POST['ape_nom_pare4'];
        $fnaci_pare4=$_POST['fnaci_pare4'];
        $rif_pare4=$_POST['rif_pere4'];
        $ocupa_pare4=$_POST['ocupa_pare4'];
        $discap_pare4=$_POST['discap_pare4'];
        $ape_nomb_pare5=$_POST['ape_nom_pare5'];
        $fnaci_pare5=$_POST['fnaci_pare5'];
        $rif_pare5=$_POST['rif_pere5'];
        $ocupa_pare5=$_POST['ocupa_pare5'];
        $discap_pare5=$_POST['discap_pare5'];
        $sql="INSERT INTO  tblempleados(correlativo,apellidos_nombre,rif,fnacimiento,edad,sexo,lnacimiento,ecivil,direccion,parroqui,thabitacion,tmovil,email,ginstruccion,titulo,especialidad,motricidad,discapacidad,automovil,placa,propi_autor_vehi,lentes,sangre,enfermedad,eme_telef1,eme_telef2,eme_parentezco,departamento,cargo,sueldobase,ncuenta,comocido_gru_paret,legajo,peso,calzado,pantalon,camisa,estatura,jinmediato,empresa_old1,carogo_old1,duracion_old1,telefono_old1,sueldo_old1,empresa_old2,carogo_old2,duracion_old2,telefono_old2,sueldo_old2,ape_nom_cony,fnaci_cony,rif_cony,ocupa_cony,gravidez_cony,ape_nom_pare1,fnaci_pare1,rif_pere1,ocupa_pare1,discap_pare1,ape_nom_pare2,fnaci_pare2,rif_pere2,ocupa_pare2,discap_pare2,ape_nom_pare3,fnaci_pare3,rif_pere3,ocupa_pare3,discap_pare3,ape_nom_pare4,fnaci_pare4,rif_pere4,ocupa_pare4,discap_pare4,ape_nom_pare5,fnaci_pare5,rif_pere5,ocupa_pare5,discap_pare5) VALUES(:correlativo,:apellidos_nombre,:rif,:fnacimiento,:edad,:sexo,:lnacimiento,:ecivil,:direccion,:parroqui,:thabitacion,:tmovil,:email,:ginstruccion,:titulo,:especialidad,:motricidad,:discapacidad,:automovil,:placa,:propi_autor_vehi,:lentes,:sangre,:enfermedad,:eme_telef1,:eme_telef2,:eme_parentezco,:departamento,:cargo,:sueldobase,:ncuenta,:comocido_gru_paret,:legajo,:peso,:calzado,:pantalon,:camisa,:estatura,:jinmediato,:empresa_old1,:carogo_old1,:duracion_old1,:telefono_old1,:sueldo_old1,:empresa_old2,:carogo_old2,:duracion_old2,:telefono_old2,:sueldo_old2,:ape_nom_cony,:fnaci_cony,:rif_cony,:ocupa_cony,:gravidez_cony,:ape_nom_pare1,:fnaci_pare1,:rif_pere1,:ocupa_pare1,:discap_pare1,:ape_nom_pare2,:fnaci_pare2,:rif_pere2,:ocupa_pare2,:discap_pare2,:ape_nom_pare3,:fnaci_pare3,:rif_pere3,:ocupa_pare3,:discap_pare3,:ape_nom_pare4,:fnaci_pare4,:rif_pere4,:ocupa_pare4,:discap_pare4,:ape_nom_pare5,:fnaci_pare5,:rif_pere5,:ocupa_pare5,:discap_pare5)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':correlativo',$correlativo,PDO::PARAM_STR);
        $query->bindParam(':apellidos_nonbre',$apellidos_nombre,PDO::PARAM_STR);
        $query->bindParam(':rif',$rif,PDO::PARAM_STR);
        $query->bindParam(':fnacimiento',$fnacimiento,PDO::PARAM_STR);
        $query->bindParam(':edad',$edad,PDO::PARAM_STR);
        $query->bindParam(':sexo',$sexo,PDO::PARAM_STR);
        $query->bindParam(':lnacimiento',$lnacimiento,PDO::PARAM_STR);
        $query->bindParam(':ecivil',$ecivil,PDO::PARAM_STR);
        $query->bindParam(':direccion',$direccion,PDO::PARAM_STR);
        $query->bindParam(':parroquia',$parroquia,PDO::PARAM_STR);
        $query->bindParam(':thabitacion',$thabitacion,PDO::PARAM_STR);
        $query->bindParam(':tmovil',$tmovil,PDO::PARAM_STR);
        $query->bindParam(':email',$email,PDO::PARAM_STR);
        $query->bindParam(':ginstruccion',$ginstruccion,PDO::PARAM_STR);
        $query->bindParam(':titulo',$titulo,PDO::PARAM_STR);
        $query->bindParam(':especialidad',$especialidad,PDO::PARAM_STR);
        $query->bindParam(':motricidad',$motricidad,PDO::PARAM_STR);
        $query->bindParam(':discapacidad',$discapacidad,PDO::PARAM_STR);
        $query->bindParam(':automovil',$automovil,PDO::PARAM_STR);
        $query->bindParam(':placa',$placa,PDO::PARAM_STR);
        $query->bindParam(':propi_autor_vehi',$propi_autor_vehi,PDO::PARAM_STR);
        $query->bindParam(':lentes',$lentes,PDO::PARAM_STR);
        $query->bindParam(':sangre',$sangre,PDO::PARAM_STR);
        $query->bindParam(':enfermedad',$enfermedad,PDO::PARAM_STR);
        $query->bindParam(':eme_telef1',$eme_telef1,PDO::PARAM_STR);
        $query->bindParam(':eme_telef2eme_telef2',$eme_telef2,PDO::PARAM_STR);
        $query->bindParam(':eme_parentezco',$eme_parentezco,PDO::PARAM_STR);
        $query->bindParam(':departamento',$departamento,PDO::PARAM_STR);
        $query->bindParam(':cargo',$cargo,PDO::PARAM_STR);
        $query->bindParam(':sueldobase',$sueldobase,PDO::PARAM_STR);
        $query->bindParam(':ncuenta',$ncuenta,PDO::PARAM_STR);
        $query->bindParam(':comocido_gru_paret',$comocido_gru_paret,PDO::PARAM_STR);
        $query->bindParam(':legajo',$legajo,PDO::PARAM_STR);
        $query->bindParam(':peso',$peso,PDO::PARAM_STR);
        $query->bindParam(':pantalon',$pantalon,PDO::PARAM_STR);
        $query->bindParam(':camisa',$camisa,PDO::PARAM_STR);
        $query->bindParam(':estatura',$estatura,PDO::PARAM_STR);
        $query->bindParam(':jinmediato',$jinmediato,PDO::PARAM_STR);
        $query->bindParam(':empresa_old1',$empresa_old1,PDO::PARAM_STR);
        $query->bindParam(':cargo_old1',$carogo_old1,PDO::PARAM_STR);
        $query->bindParam(':duracion_old1',$duracion_old1,PDO::PARAM_STR);
        $query->bindParam(':telefono_old1',$telefono_old1,PDO::PARAM_STR);
        $query->bindParam(':sueldo_old1',$sueldo_old1,PDO::PARAM_STR);
        $query->bindParam(':empresa_old2',$empresa_old2,PDO::PARAM_STR);
        $query->bindParam(':cargo_old2',$carogo_old2,PDO::PARAM_STR);
        $query->bindParam(':duracion_old2',$duracion_old2,PDO::PARAM_STR);
        $query->bindParam(':telefono_old2',$telefono_old2,PDO::PARAM_STR);
        $query->bindParam(':sueldo_old2',$sueldo_old2,PDO::PARAM_STR);
        $query->bindParam(':ape_nom_cony',$ape_nom_cony,PDO::PARAM_STR);
        $query->bindParam(':fnaci_cony',$fnaci_cony,PDO::PARAM_STR);
        $query->bindParam(':rid_cony',$rif_cony,PDO::PARAM_STR);
        $query->bindParam(':ocupa_cony',$ocupa_cony,PDO::PARAM_STR);
        $query->bindParam(':gracidez_cony',$gravidez_cony,PDO::PARAM_STR);
        $query->bindParam(':ape_nomb_pare1',$ape_nom_pare1,PDO::PARAM_STR);
        $query->bindParam(':fnaci_pare1',$fnaci_pare1,PDO::PARAM_STR);
        $query->bindParam(':rif_pare1',$rif_pere1,PDO::PARAM_STR);
        $query->bindParam(':ocupa_pare1',$ocupa_pare1,PDO::PARAM_STR);
        $query->bindParam(':discap_pare1',$discap_pare1,PDO::PARAM_STR);
        $query->bindParam(':ape_nomb_pare2',$ape_nom_pare2,PDO::PARAM_STR);
        $query->bindParam(':fnaci_pare22',$fnaci_pare,PDO::PARAM_STR);
        $query->bindParam(':rif_pare2',$rif_pere2,PDO::PARAM_STR);
        $query->bindParam(':ocupa_pare2',$ocupa_pare2,PDO::PARAM_STR);
        $query->bindParam(':discap_pare2',$discap_pare2,PDO::PARAM_STR);
        $query->bindParam(':ape_nomb_pare3',$ape_nom_pare3,PDO::PARAM_STR);
        $query->bindParam(':fnaci_pare3',$fnaci_pare3,PDO::PARAM_STR);
        $query->bindParam(':rif_pare3',$rif_pere3,PDO::PARAM_STR);
        $query->bindParam(':ocupa_pare3',$ocupa_pare3,PDO::PARAM_STR);
        $query->bindParam(':discap_pare3',$discap_pare3,PDO::PARAM_STR);
        $query->bindParam(':ape_nomb_pare4',$ape_nom_pare4,PDO::PARAM_STR);
        $query->bindParam(':fnaci_pare4',$fnaci_pare4,PDO::PARAM_STR);
        $query->bindParam(':rif_pare4',$rif_pere4,PDO::PARAM_STR);
        $query->bindParam(':ocupa_pare4',$ocupa_pare4,PDO::PARAM_STR);
        $query->bindParam(':discap_pare4',$discap_pare4,PDO::PARAM_STR);
        $query->bindParam(':ape_nomb_pare5',$ape_nom_pare5,PDO::PARAM_STR);
        $query->bindParam(':fnaci_pare5',$fnaci_pare5,PDO::PARAM_STR);
        $query->bindParam(':rif_pare5',$rif_pere5,PDO::PARAM_STR);
        $query->bindParam(':ocupa_pare5',$ocupa_pare5,PDO::PARAM_STR);
        $query->bindParam(':discap_pare5',$discap_pare5,PDO::PARAM_STR);
        $query->execute();
    }
}