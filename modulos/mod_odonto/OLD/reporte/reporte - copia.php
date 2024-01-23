<?php

class MYPDF extends TCPDF {

    public $user;

    public function Header() {
        $this->setJPEGQuality(100);
//        $this->Image('images/bechtel0.png', 20, 10, 20, '', 'PNG');
        $this->Image('images/macsa-firma.png', 80, 11, 50, '', 'PNG');
//        $this->Image('images/bambas.png', 170, 11, 23, '', 'PNG');
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
$pdf->SetMargins(PDF_MARGIN_LEFT, 27, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Establecer la fuente
$pdf->SetFont('helvetica', 'B', 16);

// Añadir página
$pdf->AddPage('P', 'A4');
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


$pdf->Cell($w - 25, $h, 'MONBRE ', $f, 0, $ali);
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
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->adm_act.' ->'.$pac->data[0]->pz22, $f, 1);
$pdf->Ln(5);


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(180, $h, 'ODONTOGRAMA', 0, 1, 'C', 1);
//$pdf->Cell(0, 0 * $h, '', 1);
$pdf->ln(5);

$h = '37';
$marco = '';
//$pdf->Image('images/macsa-firma.png', 80, 11, 50, '', 'PNG');
$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/18.png', 16, 79, 10, '', 'PNG'), $marco, 0, 'C');
$pdf->Image('images/dientes/a' . $pac->data[0]->pz18 . '.png', 16, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/17.png', 27, 79, 10, '', 'PNG'), $marco, 0, 'C');
$pdf->Image('images/dientes/a' . $pac->data[0]->pz17 . '.png', 27, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/16.png', 38, 79, 10, '', 'PNG'), $marco, 0, 'C');
$pdf->Image('images/dientes/a' . $pac->data[0]->pz16 . '.png', 38, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/15.png', 49, 79, 10, '', 'PNG'), $marco, 0, 'C');
$pdf->Image('images/dientes/a' . $pac->data[0]->pz15 . '.png', 49, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/14.png', 60, 79, 10, '', 'PNG'), $marco, 0, 'C');
$pdf->Image('images/dientes/a' . $pac->data[0]->pz14 . '.png', 60, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/13.png', 72, 79, 10, '', 'PNG'), $marco, 0, 'C');
$pdf->Image('images/dientes/a' . $pac->data[0]->pz13 . '.png', 72, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/12.png', 83, 79, 10, '', 'PNG'), $marco, 0, 'C');
$pdf->Image('images/dientes/a' . $pac->data[0]->pz12 . '.png', 83, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/11.png', 94, 79, 10, '', 'PNG'), 'R', 0, 'C');
$pdf->Image('images/dientes/a' . $pac->data[0]->pz11 . '.png', 94, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/21.png', 106, 79, 10, '', 'PNG'), 'L', 0, 'C');
$pdf->Image('images/dientes/a' . $pac->data[0]->pz21 . '.png', 106, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h, $pdf->Image('images/dientes/22.png', 117, 79, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/a' . $pac->data[0]->pz22 . '.png', 117, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/23.png', 128, 79, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/a' . $pac->data[0]->pz23 . '.png', 128, 79, 10, '', 'PNG');

$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/24.png', 139, 79, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/a' . $pac->data[0]->pz24 . '.png', 139, 79, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/25.png', 150, 79, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/a' . $pac->data[0]->pz25 . '.png', 150, 79, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/26.png', 162, 79, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/a' . $pac->data[0]->pz26 . '.png', 162, 79, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/27.png', 173, 79, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/a' . $pac->data[0]->pz27 . '.png', 173, 79, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/28.png', 184, 79, 10, '', 'PNG'), $marco, 1, 'C');
//$pdf->Image('images/dientes/a' . $pac->data[0]->pz28 . '.png', 184, 79, 10, '', 'PNG');

$h = '11.25';
//
//
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', 'R', 0, 'C');
$pdf->Cell('11.25', $h,  '', 'L', 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 1, 'C');
//
$pdf->Image('images/dientes/00000.png', 15, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 27, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 38, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 49, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 60, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 71, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 82, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 93, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 105, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 116, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 128, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 139, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 150, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 162, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 173, 115, 11, '', 'PNG');
$pdf->Image('images/dientes/00000.png', 184, 115, 11, '', 'PNG');


$pdf->Ln(5);


$h = '37';

////$pdf->Image('images/macsa-firma.png', 80, 11, 50, '', 'PNG');
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/48.png', 16, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz48 . '.png', 16, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/47.png', 27, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz47 . '.png', 27, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/46.png', 38, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz46 . '.png', 38, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/45.png', 49, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz45 . '.png', 49, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/44.png', 60, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz44 . '.png', 60, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/43.png', 72, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz43 . '.png', 72, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/42.png', 83, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz42 . '.png', 83, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/41.png', 94, 132, 10, '', 'PNG'), 'R', 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz41 . '.png', 94, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/31.png', 106, 132, 10, '', 'PNG'), 'L', 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz31 . '.png', 106, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/32.png', 117, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz32 . '.png', 117, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/33.png', 128, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz33 . '.png', 128, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/34.png', 139, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz34 . '.png', 139, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/35.png', 150, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz35 . '.png', 150, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/36.png', 162, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz36 . '.png', 162, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/37.png', 173, 132, 10, '', 'PNG'), $marco, 0, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz37 . '.png', 173, 132, 10, '', 'PNG');
//
$pdf->Cell('11.25', $h,  $pdf->Image('images/dientes/38.png', 184, 132, 10, '', 'PNG'), $marco, 1, 'C');
//$pdf->Image('images/dientes/b' . $pac->data[0]->pz38 . '.png', 184, 132, 10, '', 'PNG');
//
//$h = '11.25';
//
//
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', 'R', 0, 'C');
$pdf->Cell('11.25', $h,  '', 'L', 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 0, 'C');
$pdf->Cell('11.25', $h,  '', $marco, 1, 'C');
//
//
//$pdf->Image('images/dientes/00000.png', 15, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 27, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 38, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 49, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 60, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 71, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 82, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 93, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 105, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 116, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 128, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 139, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 150, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 162, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 173, 169, 11, '', 'PNG');
//$pdf->Image('images/dientes/00000.png', 184, 169, 11, '', 'PNG');


$f = 0;
$h = 5;
$w = 40;
$w2 = 50;
$w3 = 8;
$texh = 8;
$texh2 = 7;
$ali = 'L';
//
$f = 1;
$w = 60;
$w2 = 0;

$pdf->ln(5);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w2, $h, 'REFERENCIAS', 0, 1, 'L', 1);
$pdf->ln(0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, 'Caries Dental', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(30, $h, $pac->data[0]->caries, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, 'Pzas Ausentes', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(30, $h, $pac->data[0]->ausente, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, 'Pzas para Extraer', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(30, $h, $pac->data[0]->extraer, $f, 1);


$pdf->ln(5);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w2, $h, 'RECOMENDACIONES', 0, 1, 'C', 1);

$pdf->SetFont('helvetica', '', $texh2);
$pdf->MultiCell(0, 0, $pac->data[0]->odo_recomendacion, 1, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w2, $h, 'CONCLUSIONES', 0, 1, 'C', 1);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->MultiCell(0,0, $pac->data[0]->odo_conclusion, 1, 'L', 0, 1);
$pdf->ln(0);

$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
$pdf->SetAlpha(0.9);
$pdf->setVisibility('all');
$pdf->Output('Pscologia.pdf', 'I');
