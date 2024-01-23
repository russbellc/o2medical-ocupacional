<?php

class MYPDF extends TCPDF {

//
    public function Header() {
        
    }

//
    function zerofill($entero, $largo) {
        $entero = (int) $entero;
        $largo = (int) $largo;
        $relleno = '';

        if (strlen($entero) < $largo) {
            $relleno = str_repeat(0, $largo - strlen($entero));
        }
        return $relleno . $entero;
    }

    public function Footer() {
        
    }

}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetMargins(PDF_MARGIN_LEFT - 5, PDF_MARGIN_TOP - 23, PDF_MARGIN_RIGHT);

// set document information
// Información referente al PDF
$pdf->SetCreator("");
$pdf->SetAuthor('');
$pdf->SetTitle('');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// Contenido de la cabecera
// Fuente de la cabecera y el pie de página
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Márgenes
//$pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Establecer la fuente
$pdf->SetFont('helvetica', 'B', 16);

// Añadir página
$pdf->AddPage();

$model = new model();
$inicio = $_REQUEST['fini'];
$final = $_REQUEST['ffinal'];
$empresas = $_REQUEST['empresa'];

$pdf->setJPEGQuality(100);
//$pdf->Image('images/macsa-firma.png', 15, 7, 50, '', 'PNG');
$pdf->Image('images/logo.png', 155, 3, 40, '', 'PNG');
$diario = $model->report_diario("$inicio", "$final", "$empresas");

$pdf->SetFont('helvetica', 'BU', 15);
$pdf->Cell(0, 0, 'REPORTE ESPIROMETRIA: ' . $diario->total . ' Pacientes', 0, 1, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 0, $inicio . ' AL ' . $final, 0, 1, 'L');
$pdf->ln(1);
$h = 2;

$pdf->SetFillColor(194, 217, 241);

//$pdf->ln(5);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(5, $h, 'N°', 1, 0, 'C', 1);
$pdf->Cell(7, $h, 'H.C', 1, 0, 'C', 1);
$pdf->Cell(60, $h, 'EMPRESA', 1, 0, 'C', 1);
$pdf->Cell(13, $h, 'FECHA', 1, 0, 'C', 1);
$pdf->Cell(50, $h, 'NOMBRES', 1, 0, 'C', 1);
$pdf->Cell(5, $h, 'SX', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'FICHA', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'RUTA', 1, 1, 'C', 1);


foreach ($diario->data as $i => $row) {
    $pdf->SetFont('helvetica', 'B', 6);
    $pdf->Cell(5, $h, $i + 1, 1, 0, 'C', 0);
    $pdf->Cell(7, $h, $row->NRO, 1, 0, 'C', 0);
    $pdf->Cell(60, $h, $row->EMPRESA, 1, 0, 'L', 0);
    $pdf->Cell(13, $h, $row->FECHA, 1, 0, 'C', 0);
    $pdf->Cell(50, $h, $row->NOMBRES, 1, 0, 'L', 0);
    $pdf->Cell(5, $h, $row->SEXO, 1, 0, 'C', 0);
    $pdf->Cell(25, $h, $row->FICHA, 1, 0, 'C', 0);
    $pdf->Cell(25, $h, substr($row->RUTA, 0, 17), 1, 1, 'C', 0);
}


$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
$pdf->SetAlpha(0.9);
$pdf->setVisibility('all');
$pdf->Output('reporte.pdf', 'I');
