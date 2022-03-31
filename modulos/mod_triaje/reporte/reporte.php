<?php

class MYPDF extends TCPDF {

    public $user;

    public function Header() {
        $this->setJPEGQuality(100);
//        $this->Image('images/macsa-firma.png', 15, 7, 50, '', 'PNG');
//        $this->Image('images/macsa-firma.png', 80, 7, 50, '', 'PNG');
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
$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
$pac = $model->rpt($_REQUEST['adm']);

$pdf->SetFont('helvetica', 'BU', 15);
$pdf->Cell(0, 0, 'REPORTE DE EVALUACION OFTALMOLOGICA ', 0, 1, 'C');
$pdf->Ln(5);
$f = 0;
$h = 6;
$w = 40;
$w2 = 50;
$w3 = 8;
$texh = 8;
$texh2 = 7;
$ali = 'L';


////$pdf->Cell($w3,$h, "DATOS PERSONALES",1,,'C',1);
$pdf->SetFont('helvetica', 'B', $texh + 2);
//
$pdf->SetFillColor(194, 217, 241);
$pdf->Cell($w2, $h, 'DATOS GENERALES', 0, 1, 'L', 1);
$pdf->Cell(0, 4 * $h, '', 1);
$pdf->ln(0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'NOMBRES ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->pac_appat . ' ' . $pac->data[0]->pac_apmat . ' ' . $pac->data[0]->pac_nombres, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'DNI ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->pac_ndoc, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'SEXO ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->pac_sexo, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'TELEFONO ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->pac_cel, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'EMPRESA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->emp_desc, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'FECHA DE REGISTRO', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->adm_fech, $f, 1);
//$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'EDAD ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->edad . ' Años', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'ACTIVIDAD ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->adm_act, $f, 1);

$f = 1;
$w = 60;
$pdf->ln(5);
$pdf->SetFont('helvetica', 'B', $texh + 2);
$pdf->Cell($w2 + 10, $h, 'EVALUACION OFTALMOLOGICA', 0, 1, 'L', 1);
$pdf->ln(0);

$w2 = 0;
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(0, $h, 'ANTECEDENTES OFTALMICOS', $f, 1, $ali);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'USO DE CORRECTORES', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $pac->data[0]->ofta_usa, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'EXPOSICION A COMPUTADORAS', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $pac->data[0]->ofta_comp, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'OTRO', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $pac->data[0]->ofta_otro, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'TEST DE ISHIHARA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $pac->data[0]->ofta_colo, $f, 1);


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'FONDO DE OJO', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $pac->data[0]->ofta_fond, $f, 1);


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'CAMPIMETRIA POR CONFRONTACION', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $pac->data[0]->ofta_camp, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'PRESION INTRAOCULAR', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $pac->data[0]->ofta_anex, $f, 1);
$w = 60;
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'TOMOMETRIA', $f, 0, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(0, $h, $pac->data[0]->ofta_tono, $f, 1, $ali);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Test de Estereopsis', $f, 0, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(0, $h, $pac->data[0]->ofta_este . '%', $f, 1, $ali);


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'OJO IZQUIERDO', $f, 0, 'C');
$pdf->Cell(90, $h, 'OJO DERECHO', $f, 1, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(90, $h, $pac->data[0]->ofta_oj_izq, $f, 0, 'C');
$pdf->Cell(90, $h, $pac->data[0]->ofta_oj_der, $f, 1, 'C');
$pdf->Ln(5);


$w = 90;
$pdf->SetFont('helvetica', 'B', $texh + 2);
$pdf->Cell(0, $h, 'MOVILIDAD OCULAR', $f, 1, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w / 2, $h, 'EXTRINSECA:', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w / 2, $h, $pac->data[0]->ofta_extr, $f, 0);
$pdf->SetFont('helvetica', 'B', $texh2);
$pdf->Cell($w / 2, $h, 'INTRINSECAF', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w / 2, $h, $pac->data[0]->ofta_intr, $f, 1);

$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', $texh + 2);
$pdf->Cell(0, $h, 'AGUDEZA VISUAL', $f, 1, 'C');
$w = 180 / 7;

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h * 2, '', $f, 0, 'C');
$pdf->Cell($w * 2, $h, 'SIN CORRECION', $f, 0, 'C');
$pdf->Cell($w * 2, $h, 'CON CORRECION', $f, 0, 'C');
$pdf->Cell($w * 2, $h, 'CON ESTENOPEICO', $f, 1, 'C');

$pdf->Cell($w, $h, '', 0, 0, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Ojo Izquierdo', $f, 0, 'C');
$pdf->Cell($w, $h, 'Ojo Derecho', $f, 0, 'C');
$pdf->Cell($w, $h, 'Ojo Izquierdo', $f, 0, 'C');
$pdf->Cell($w, $h, 'Ojo Derecho ', $f, 0, 'C');
$pdf->Cell($w, $h, 'Ojo Izquierdo', $f, 0, 'C');
$pdf->Cell($w, $h, 'Ojo Derecho', $f, 1, 'C');

$pdf->Cell($w, $h, 'VISION DE LEJOS', $f, 0, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell($w, $h, $pac->data[0]->ofta_slejos_izq, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_slejos_der, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_clejos_izq, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_clejos_der, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_elejos_izq, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_elejos_der, $f, 1, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'VISION DE CERCA', $f, 0, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell($w, $h, $pac->data[0]->ofta_scerca_izq, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_scerca_der, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_ccerca_izq, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_ccerca_der, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_ecerca_izq, $f, 0, 'C');
$pdf->Cell($w, $h, $pac->data[0]->ofta_ecerca_der, $f, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'DIAGNOSTICO', 0, 1, 'L', 1);
$f = 0;
$pdf->Cell(10, $h, '1.-', $f, 0, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(0, $h, $pac->data[0]->ofta_cie1, $f, 1, 'L');

$pdf->Cell(10, $h, '2.-', $f, 0, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(0, $h, $pac->data[0]->c2, $f, 1, 'L');

$pdf->Cell(10, $h, '3.-', $f, 0, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(0, $h, $pac->data[0]->c3, $f, 1, 'L');
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'TRATAMIENTO Y RECOMENDACIONES', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(0, $h * 2, $pac->data[0]->ofta_recm, 0, 'L', 0, 0);


//ofta_id, ofta_adm, ofta_sede, ofta_usu, ofta_fech, ofta_st, ofta_usa, ofta_pico, ofta_desl, ofta_sensa, ofta_otro, ofta_visi, ofta_cefa, ofta_quem,
// ofta_comp, ofta_anex, ofta_polo, ofta_extr, ofta_intr, ofta_colo, ofta_camp, ofta_cie1, ofta_cie2, ofta_cie3, ofta_recm, ofta_slejos_izq, 
// ofta_slejos_der, ofta_scerca_izq, ofta_scerca_der, ofta_clejos_izq, ofta_clejos_der, ofta_ccerca_izq, ofta_ccerca_der, ofta_elejos_izq, 
// ofta_elejos_der, ofta_ecerca_izq, ofta_ecerca_der, ofta_refl, ofta_movi, ofta_fond, ofta_fon_obs, ofta_tono, ofta_oj_izq, ofta_oj_der, ofta_obs
$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
$pdf->SetAlpha(0.9);
$pdf->setVisibility('all');
$pdf->Output('Pscologia', 'I');
