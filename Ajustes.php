<?php
    require 'Config/Conexdb.php';
    require 'clases/invfunciones.php';
    require 'Config/config.php';
    $db = new Database();
    $con = $db ->conectar();
    #print_r($_SESSION);
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

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include "header.php"; ?>

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


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

</body>

</html>