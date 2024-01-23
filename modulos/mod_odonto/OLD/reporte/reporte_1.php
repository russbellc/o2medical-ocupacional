<?php

class MYPDF extends TCPDF {

    public $user;

    public function Header() {
        $this->setJPEGQuality(100);
//        $this->Image('images/macsa-firma.png', 15, 7, 50, '', 'PNG');
//        $this->Image('images/macsa-firma.png', 80, 7, 50, '', 'PNG');
        $this->Image('images/logo.png', 10, 5, 50, '', 'PNG');
        $this->SetY(10);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetX(55);
//        $this->Line(0, 30, 240, 30, array('width' => 0.4));
//        $this->Ln(7);
//        $this->SetFont('helvetica', 'U', 10);
//        $this->Cell(0, 0, '', 0, 1);
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

    function calculaedad($fechanacimiento) {
        list($ano, $mes, $dia) = explode("-", $fechanacimiento);
        $ano_diferencia = date("Y") - $ano;
        $mes_diferencia = date("m") - $mes;
        $dia_diferencia = date("d") - $dia;
        if ($dia_diferencia < 0 || $mes_diferencia < 0)
            $ano_diferencia--;
        return $ano_diferencia;
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, $this->user->sed_desc, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'Pagina - ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$model = new model();
$pdf->user = $model->user;

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
$arriba = $model->diente_arriba(); //diag_arriba
$diag_arriba = $model->diag_arriba($_REQUEST['adm']);

$abajo = $model->diente_abajo();
$diag_abajo = $model->diag_abajo($_REQUEST['adm']);

$pac = $model->rpt($_REQUEST['adm']);

$pdf->SetFont('helvetica', 'BU', 15);
$pdf->Cell(0, 0, 'REPORTE DE RESULTADOS ODONTOLOGICOS', 0, 1, 'C');
$pdf->Ln(5);
$f = 0;
$h = 5;
$w = 40;
$w2 = 50;
$w3 = 8;
$texh = 8;
$texh2 = 7;
$ali = 'L';


////$pdf->Cell($w3,$h, "DATOS PERSONALES",1,,'C',1);
$pdf->SetFont('helvetica', 'B', $texh);
//
$pdf->SetFillColor(194, 217, 241);
$pdf->Cell($w2, $h, 'DATOS GENERALES', 0, 1, 'L', 1);
$pdf->Cell(0, 4 * $h, '', 1);
$pdf->ln(0);


$pdf->Cell($w - 25, $h, 'NONBRE ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 + 25, $h, ': ' . $pac->data[0]->nombres, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'DNI ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->pac_ndoc, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w - 25, $h, 'SEXO ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 + 25, $h, ': ' . $pac->data[0]->pac_sexo, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'TIPO DE FICHA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->tfi_desc, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w - 25, $h, 'EMPRESA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 + 25, $h, ': ' . $pac->data[0]->emp_desc, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'FECHA DE REGISTRO ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->adm_fechc, $f, 1);
//$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w - 25, $h, 'EDAD ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 + 25, $h, ': ' . $pac->data[0]->edad, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'ACTIVIDAD ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->adm_act, $f, 1);
$pdf->Ln(5);



$p = -1;
for ($index = 0; $index < 16; $index++) {
    $p = $p + 11.8;
    $pdf->ImageSVG("http://localhost/mecsa/extras/svg.php", $p, 93, 50, 50);
}
$p = 34.4;
for ($index = 0; $index < 10; $index++) {
    $p = $p + 11.8;
    $pdf->ImageSVG("http://localhost/mecsa/extras/svg.php", $p, 108, 50, 50);
}
$p = 34.4;
for ($index = 0; $index < 10; $index++) {
    $p = $p + 11.8;
    $pdf->ImageSVG("http://localhost/mecsa/extras/svg.php", $p, 123, 50, 50);
}
$p = -1;
for ($index = 0; $index < 16; $index++) {
    $p = $p + 11.8;
    $pdf->ImageSVG("http://localhost/mecsa/extras/svg.php", $p, 138, 50, 50);
}


$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
$pdf->SetAlpha(0.9);
$pdf->setVisibility('all');
$pdf->Output('Pscologia.pdf', 'I');
