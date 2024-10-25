<?php 
    require 'reportes/fpdf.php';



    class PDF_Compras_Clientes extends FPDF {
    /* function Header() {
        $this->Image('images/balmorca.png', 5, 5, 30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(30);
        $this->Cell(120, 10, 'Reporte De Compras Del Cliente');
        $this->Ln(20);
    } */
    /* function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C' );
    } */
    }



    class PDF_Empresas extends FPDF {
    /* function Header() {
        $this->Image('images/balmorca.png', 5, 5, 30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(30);
        $this->Cell(120, 10, 'Reporte De Empresas');
        $this->Ln(20);
    } */
    /* function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C' );
    } */
    }
?>