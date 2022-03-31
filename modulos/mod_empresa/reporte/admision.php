<?php

class MYPDF extends TCPDF {

    public $user;

    public function Header() {
//        $this->setJPEGQuality(100);
//        $this->Image('images/seguro.png', 12, 5, 65, '', 'PNG');
////        $this->Image('images/macsa-firma.png', 80, 7, 50, '', 'PNG');
//        $this->SetFont('helvetica', 'B', 9);
//        $this->Ln(6);
//        $this->Cell(0, 2, 'ANEXO N° 02', 0, 1, 'C');
//        $this->Cell(0, 2, 'FICHA DE ATENCIÓN PARA ESCOLAR CON TAMIZAJE DE AGUDEZA VISUAL', 0, 1, 'C');
////        $this->Cell(0, 2, 'CENTRO DENTAL SAO PAULO Y SERVICIOS DE SALUD', 0, 1, 'R');
//        $this->SetY(10);
//        $this->SetFont('helvetica', 'B', 9);
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
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, '', 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, $this->user->sed_desc, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'Pagina - ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$model = new model();
$pdf->user = $model->user;

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
$pdf->SetMargins(PDF_MARGIN_LEFT, 7, PDF_MARGIN_RIGHT, 2);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

/*//////////////////////////////////////////////////////
-----------------variables declaradas------------------
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\*/
//$pac = $model->reporte($_REQUEST['adm']);
/*//////////////////////////////////////////////////////
-----------------variables declaradas------------------
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\*/

// Añadir página
$pdf->AddPage('P', 'A4');




$pdf->Output('Formato_7c_.PDF', 'I');
