<?php

class MYPDF extends TCPDF {

    public function Header() {
//        $this->setJPEGQuality(100);
//        $this->Image('images/macsa-firma.png', 15, 7, 50, '', 'PNG');
////        $this->Image('images/macsa-firma.png', 80, 7, 50, '', 'PNG');
//        $this->Image('images/logo.png', 150, 5, 50, '', 'PNG');
//        $this->SetY(10);
//        $this->SetFont('helvetica', 'B', 10);
//        $this->SetX(55);
    }

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
//        $this->SetY(-15);
//        $this->SetFont('helvetica', 'I', 8);
//        $this->Cell(0, 10, 'Av. LOS INCAS 1412', 0, false, 'L', 0, '', 0, false, 'T', 'M');
//        $this->Cell(0, 10, 'Pagina - ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


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
$pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);
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


//$cod = $pdf->zerofill($this->user->con_sedid, 4);
//$cod.=$pdf->zerofill(': ' . $pac->data[0]->adm_id, 7);
////$code = ;
//$pdf->SetFont('helvetica', '', 'C');
//$pdf->Cell(0, $h, $pdf->write1DBarcode($cod, 'C39', '', '', 60, 10, 0.4), $f, 1, 'R');
$a = 216;
$b = 216;
for ($index = 0; $index < 5; $index++) {

    for ($index1 = 0; $index1 < 3; $index1++) {

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetX(6);
        $pdf->Cell(65, 11.3, $pdf->ImageSVG('images/mecsa.svg', '', '', 33, '', $link = '', '', 'T') . 'INVENTARIO 2016 ', 'LRT', 0, 'R');
        $pdf->Cell(65, 11.3, $pdf->ImageSVG('images/mecsa.svg', '', '', 33, '', $link = '', '', 'T') . 'INVENTARIO 2016 ', 'LRT', 0, 'R');
        $pdf->Cell(65, 11.3, $pdf->ImageSVG('images/mecsa.svg', '', '', 33, '', $link = '', '', 'T') . 'INVENTARIO 2016 ', 'LRT', 1, 'R');

        $pdf->SetX(6);
        $pdf->Cell(2, 12.3, '', 'L', 0, 'C');
        $pdf->Cell(63, 12.3, '   ' . $pdf->write1DBarcode('2016001' . str_pad($b++, 3, '0', STR_PAD_LEFT), 'C39', '', '', 61, 12, 0.6), 'R', 0, 'C');
        $pdf->Cell(2, 12.3, '', 'L', 0, 'C');
        $pdf->Cell(63, 12.3, '   ' . $pdf->write1DBarcode('2016001' . str_pad($b++, 3, '0', STR_PAD_LEFT), 'C39', '', '', 61, 12, 0.6), 'R', 0, 'C');
        $pdf->Cell(2, 12.3, '', 'L', 0, 'C');
        $pdf->Cell(63, 12.3, '   ' . $pdf->write1DBarcode('2016001' . str_pad($b++, 3, '0', STR_PAD_LEFT), 'C39', '', '', 61, 12, 0.6), 'R', 1, 'C');

        $pdf->SetX(6);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(65, 6.3, 'N°: 2016001' . str_pad($a++, 3, '0', STR_PAD_LEFT), 'LRB', 0, 'C');
        $pdf->Cell(65, 6.3, 'N°: 2016001' . str_pad($a++, 3, '0', STR_PAD_LEFT), 'LRB', 0, 'C');
        $pdf->Cell(65, 6.3, 'N°: 2016001' . str_pad($a++, 3, '0', STR_PAD_LEFT), 'LRB', 1, 'C');
    }
//    $pdf->Ln(37);
}



$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
$pdf->SetAlpha(0.9);
$pdf->setVisibility('all');
$pdf->Output('barra.pdf', 'I');
