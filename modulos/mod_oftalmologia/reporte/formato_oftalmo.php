<?php

class MYPDF extends TCPDF {

    public $user;

    public function Header() {
    
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
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



-
// Añadir página
        $pdf->AddPage();
$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 47, '', $link = '', '', 'T');
$pac_rpt = $model->rpt_pac($_REQUEST['adm']);
$oftalmo = $model->rpt_oftalmo($_REQUEST['adm']);
$rpt_diag = $model->rpt_diag($_REQUEST['adm']);
$rpt_reco = $model->rpt_reco($_REQUEST['adm']);

$pdf->SetFont('helvetica', 'BU', 15);
$pdf->Cell(0, 0, 'EVALUACIÓN OFTALMOLÓGICA ', 0, 1, 'C');
$pdf->Ln(5);
$h = 5;


////$pdf->Cell($w3,$h, "DATOS PERSONALES",1,,'C',1);
$pdf->SetFont('helvetica', 'B', 7);
//
$pdf->SetFillColor(194, 217, 241);
$pdf->Cell(50, $h, 'DATOS GENERALES', 0, 0, 'L', 1);
$pdf->Cell(130, $h, 'N° HOJA DE RUTA: ' . $pac_rpt->data[0]->adm_id, 0, 1, 'R', 0);
$pdf->Cell(0, 4 * $h, '', 1);
$pdf->ln(0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'NOMBRES ', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(95, $h, ': ' . $pac_rpt->data[0]->pac_appat . ' ' . $pac_rpt->data[0]->pac_apmat . ' ' . $pac_rpt->data[0]->pac_nombres, 0, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(35, $h, 'DNI ', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(35, $h, ': ' . $pac_rpt->data[0]->pac_ndoc, 0, 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'SEXO ', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(95, $h, ': ' . $pac_rpt->data[0]->pac_sexo, 0, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(35, $h, 'TELEFONO ', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(35, $h, ': ' . $pac_rpt->data[0]->pac_cel, 0, 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'EMPRESA ', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(95, $h, ': ' . $pac_rpt->data[0]->emp_desc, 0, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(35, $h, 'FECHA DE REGISTRO', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(35, $h, ': ' . $pac_rpt->data[0]->adm_fechc, 0, 1);
//$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(17, $h, 'ACTIVIDAD ', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(93, $h, ': ' . $pac_rpt->data[0]->adm_act, 0, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(35, $h, 'EDAD ', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(35, $h, ': ' . $pac_rpt->data[0]->edad . ' Años', 0, 1);

$pdf->ln(2);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(80, $h, 'CORRECTORES OPTICOS AL MOMENTO DEL EXAMEN', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(100, $h, ':  ' . $oftalmo->data[0]->m_oft_oftalmo_correctores, 1, 1, 'L');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(50, $h, 'PATOLOGIA OFTALMOLOGICA ACTUAL', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(130, $h, ':  ' . $oftalmo->data[0]->m_oft_oftalmo_patologia, 1, 1, 'L');
$pdf->ln(2);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(115, $h, 'ANAMNESIS Y ANTECEDENTES', 1, 0, 'C', 1);
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Cell(60, $h, 'ANEXOS', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(115, $h * 2, $oftalmo->data[0]->m_oft_oftalmo_anamnesis, 1, 'L', 0, 0); //===================>VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(60, $h * 7, '', 1, 0, 'C');
$pdf->ImageSVG('images/oftalmo/anexo.svg', 137.5, 69, 55, '', $link = '', '', 'T');
$pdf->ln(12);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(115, $h, 'CAMPOS VISUALES', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(65, $h, '', 0, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'OD', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(100, $h, $oftalmo->data[0]->m_oft_oftalmo_campos_v_od, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(65, $h, '', 0, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'OI', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(100, $h, $oftalmo->data[0]->m_oft_oftalmo_campos_v_oi, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(65, $h, '', 0, 1, 'C', 0);
$pdf->ln(2);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(115, $h, 'TEST DE ISHIHARA', 1, 0, 'C', 1);
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(60, $h, $oftalmo->data[0]->m_oft_oftalmo_anexos, 'T', 1, 'C', 0); //===================>VALUE


$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(115, $h, $oftalmo->data[0]->m_oft_oftalmo_ishihara, 1, 0, 'C', 0); //===================>VALUE
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(65, $h, '', 0, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(115, $h, 'AGUDEZA VISUAL:', 1, 0, 'L', 1);
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Cell(60, $h, 'MOTILIDAD OCULAR', 1, 1, 'C', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->ImageSVG('images/oftalmo/motilidad.svg', 137.5, 109.5, 53, '', $link = '', '', 'T');


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, '', 'LRT', 0, 'C', 0);
$pdf->Cell(30, $h, 'SIN CORRECCIÓN', 1, 0, 'C', 0);
$pdf->Cell(30, $h, 'CON CORRECCIÓN', 1, 0, 'C', 0);
$pdf->Cell(30, $h, 'CON ESTENOPEICO', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(5, $h * 6, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h * 6, '', 1, 0, 'C', 0);
$pdf->ln(5);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, '', 'LRB', 0, 'C', 0);
$pdf->Cell(15, $h, 'OJO DER', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'OJO IZQ', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'OJO DER', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'OJO IZQ', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'OJO DER', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'OJO IZQ', 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'VISION DE LEJOS', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vlejos_od, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vlejos_oi, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vlejos_od, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vlejos_oi, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_esteno_vlejos_od, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_esteno_vlejos_oi, 1, 1, 'C', 0); //===================>VALUE


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'VISION DE CERCA', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vcerca_od, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vcerca_oi, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vcerca_od, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vcerca_oi, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_esteno_vcerca_od, 1, 0, 'C', 0); //===================>VALUE
$pdf->Cell(15, $h, $oftalmo->data[0]->m_oft_oftalmo_esteno_vcerca_oi, 1, 1, 'C', 0); //===================>VALUE


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'BINOCULAR', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_binocular, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_binocular, 1, 0, 'C', 0);
$pdf->Cell(30, $h, '', 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'CAMPIMETRIA', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(85, $h, $oftalmo->data[0]->m_oft_oftalmo_campimetria, 1, 0, 'L', 0); //===================>VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Cell(60, $h, $oftalmo->data[0]->m_oft_oftalmo_motilidad, 'T', 1, 'C', 0); //===================>VALUE
$pdf->ln(2);


//$pdf->Image("images/fotos/" . $_GET['adm'] . ".png", '', '', 43.8, '', '')

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'PRUEBA ESTEROPSIA - VISION DE PROFUNDIDAD - (TEST DE LA MOSCA - TEST DE CIRCULO)', 1, 1, 'C', 1);
$pdf->Cell(115, $h * 11, "-".$pdf->Image('images/oftalmo/estereopsis2.jpg', '', '', 100, '', ''), 1, 0, 'L', 0);
$pdf->Cell(65, $h, 'PRUEBA DE ESTEREOPSIS(%)', 1, 1, 'C', 0);
$pdf->SetFont('helvetica', '', 7);

$pdf->Cell(115, $h, '', 0, 0, 'L', 0);
$pdf->Cell(65, $h, $oftalmo->data[0]->m_oft_oftalmo_esteropsia . ' %', 1, 1, 'C', 0); //===================>VALUE

$pdf->Cell(115, $h, '', 0, 0, 'L', 0);
$pdf->Cell(65, $h * 9, '', 1, 1, 'C', 0); //===================>VALUE
$pdf->ln(2);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h * 2, 'TONOMETRIA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, 'OD', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '0', 7);
$pdf->Cell(50, $h, $oftalmo->data[0]->m_oft_oftalmo_tonometria_od, 1, 0, 'C', 0); //===================>VALUE
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(25, $h * 2, 'FONDO DE OJO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, 'OD', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '0', 7);
$pdf->Cell(50, $h, $oftalmo->data[0]->m_oft_oftalmo_fondo_od, 1, 1, 'C', 0); //===================>VALUE


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, '', 0, 0, 'C', 0);
$pdf->Cell(10, $h, 'OI', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '0', 7);
$pdf->Cell(50, $h, $oftalmo->data[0]->m_oft_oftalmo_tonometria_oi, 1, 0, 'C', 0); //===================>VALUE
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(25, $h, '', 0, 0, 'C', 0);
$pdf->Cell(10, $h, 'OD', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '0', 7);
$pdf->Cell(50, $h, $oftalmo->data[0]->m_oft_oftalmo_fondo_oi, 1, 1, 'C', 0); //===================>VALUE
$pdf->ln(2);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'DIAGNOSTICOS', 1, 1, 'L', 1);
//$h = 3.5;
$pdf->ln(1);


foreach ($rpt_diag->data as $i => $row) {
    $pdf->ln(1);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->MultiCell(180, $h - 1, $i + 1 . '.- ' . $row->diag_ofta_desc, 'B', 'L', 0, 1);
}
$pdf->ln(1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'RECOMENDACIONES Y OBSERVACIONES', 1, 1, 'L', 1);
//$h = 3.5;
$pdf->ln(1);

foreach ($rpt_reco->data as $i => $row) {
    $pdf->ln(1);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->MultiCell(180, $h - 1, $i + 1 . '.- ' . $row->reco_ofta_desc, 'B', 'L', 0, 1);
}





//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('oftalmo_' . $_REQUEST['adm'] . '.PDF', 'I');
