<?php

require_once('../extras/fpdi/fpdi.php');

class MYPDF extends FPDI
{

    public function Header()
    {
        //        $this->setJPEGQuality(100);
        //        $this->Image('images/macsa-firma.png', 15, 7, 50, '', 'PNG');
        ////        $this->Image('images/macsa-firma.png', 80, 7, 50, '', 'PNG');
        //        $this->Image('images/logo.png', 150, 5, 50, '', 'PNG');
        $this->SetY(10);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetX(55);
    }

    function zerofill($entero, $largo)
    {
        $entero = (int) $entero;
        $largo = (int) $largo;
        $relleno = '';

        if (strlen($entero) < $largo) {
            $relleno = str_repeat(0, $largo - strlen($entero));
        }
        return $relleno . $entero;
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        // $this->Cell(0, 10, 'Av. LOS INCAS 1412', 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'Pagina - ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator("");
$pdf->SetAuthor('');
$pdf->SetTitle('');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// Contenido de la cabecera
// Fuente de la cabecera y el pie de página
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Márgenes
$pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Establecer la fuente  municip
$model = new model();
// $pdf->user = $model->user;
// $municip = $model->municip();

$pdf->SetFont('helvetica', 'B', 16);
$adm = $_REQUEST['adm'];
$PageCount = $pdf->setSourceFile('adjuntos/' . $adm . '.pdf');
for ($i = 1; $i <= ($PageCount); $i++) {
    $pdf->AddPage();
    $import_page = $pdf->ImportPage($i);
    $pdf->usetemplate($import_page);
}
$pdf->Output('Escaner_' . $adm . ' .PDF', 'I');
