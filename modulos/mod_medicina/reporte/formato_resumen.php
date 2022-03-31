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
$pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT, 2);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFillColor(194, 217, 241);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */
$paciente = $model->paciente($_REQUEST['adm']);
$grupo_fac = $model->rpt_lab_examen($_REQUEST['adm'], 22);
$anexo16 = $model->mod_medicina_anexo16($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');
$h = 5.5;
$titulo = 8;
$texto = 8;
$salto = 2;
$pdf->SetFont('helvetica', 'B', 7);

$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 10);

$pdf->Ln(1);
$pdf->Cell(180, $h, 'SERVICIO MEDICO DE MINERA LAS BAMBAS', 0, 1, 'C', 0);
$pdf->Cell(180, $h, 'DIVISION DE SALUD OCUPACIONAL', 0, 1, 'C', 0);
$pdf->Cell(180, $h, 'REG-05-E43', 0, 1, 'C', 0);
$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(180, $h * 2, 'INFORME MEDICO OCUPACIONAL', 0, 1, 'C', 0);

//$pdf->Ln();

$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'INFORMACION PERSONAL:', 1, 1, 'L', 1);
$pdf->Cell(180, $h * 7, '', 1, 0, 'C', 0);
$pdf->Ln(0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'TIPO DE EXAMEN:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, 'TIPO DE EXAMEN:', 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'PACIENTE:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->nom_ap, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'AREA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->adm_area, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'PUESTO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->adm_puesto, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'EMPRESA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->emp_desc, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, $paciente->data[0]->documento . ':', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->pac_ndoc, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'GRUPO SANGUINEO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $grupo_fac->data[0]->m_lab_exam_resultado, 0, 1, 'L', 0);


/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

$diagnostico = $model->diagnostico($_REQUEST['adm']);
$audio = $model->rpt_audiometria($_REQUEST['adm']);
$oftalmo_diag = $model->rpt_oftalmo_diag($_REQUEST['adm']);
$ekg_conclu = $model->rpt_ekg_conclu($_REQUEST['adm']);
$osteo_aptitud = $model->rpt_osteo($_REQUEST['adm']);
$triaje = $model->rpt_triaje($_REQUEST['adm']);
/////////////////////////////////////////////////////////////////////////
$lim_texto = array(90, 180, 270, 360, 450, 540, 630, 720, 810, 900);
$h_text = 2.8;
$texto = 7.5;
$conteo = array();
foreach ($diagnostico->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->diag_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

$diag_total = $diagnostico->total;
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h * (8 + $diag_total + $fila_total), 'DIAGNOSTICOS', 1, 0, 'C', 1);
$pdf->Cell(27, $h * 2, 'AUDIOMETRIA', 1, 0, 'C', 1);
$pdf->Cell(8, $h, 'OD', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1.5);
$pdf->Cell(52, $h, $audio->data[0]->m_a_audio_diag_aereo_od, 1, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(8, $h, 'OI', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1.5);
$pdf->Cell(52, $h, $audio->data[0]->m_a_audio_diag_aereo_oi, 1, 1, 'L', 0);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'L', 0);
$pdf->Cell(27, $h, '', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto - 0.5);
$pdf->Cell(20, $h, 'CLASIFICACION', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 2);
$pdf->Cell(100, $h, $audio->data[0]->m_a_audio_kclokhoff, 1, 1, 'L', 0);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'L', 0);
$pdf->Cell(27, $h, 'OFTALMOLOGIA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->Cell(120, $h, $oftalmo_diag->data[0]->diag_concat, 1, 1, 'L', 0);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'L', 0);
$pdf->Cell(27, $h, 'ESPIROMETRIA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(120, $h, '', 1, 1, 'L', 0);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'L', 0);
$pdf->Cell(27, $h, 'CARDIOVASCULAR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1.5);
$pdf->Cell(120, $h, $ekg_conclu->data[0]->conclu_concat, 1, 1, 'L', 0);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'L', 0);
$pdf->Cell(27, $h, 'RESPIRATORIO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(120, $h, '', 1, 1, 'L', 0);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'L', 0);
$pdf->Cell(27, $h, 'OSTEO MUSCULAR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(120, $h, $osteo_aptitud->data[0]->m_osteo_aptitud, 1, 1, 'L', 0);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'L', 0);
$pdf->Cell(27, $h, 'NUTRICIONAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(120, $h, $triaje->data[0]->m_tri_triaje_nutricion_dx, 1, 1, 'L', 0);
////



foreach ($diagnostico->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $h_text * ($conteo[$i] + 1) : $h);
    $text_tamaño = (($conteo[$i] != 0) ? $texto - 1 : ((strlen($row2->diag_desc) > 74) ? $texto - 1 : $texto));
    if ($i === 0) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->Cell(27, (($conteo[$i] != 0) ? $h_text : $h) * ($diag_total + $fila_total), 'OTROS', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(120, $salteos, $i + 1 . '.- ' . $row2->diag_desc, 1, 'L', 0, 1);
    } else {
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->Cell(27, $salteos, '', 0, 0, 'C', 0);
        $pdf->SetFont('helvetica', '', $text_tamaño);
//        $pdf->Cell(120, $salteos, $i + 1 . '.- ' . $row2->diag_desc, 1, 1, 'L', 0);
        $pdf->MultiCell(120, $salteos, $i + 1 . '.- ' . $row2->diag_desc, 1, 'L', 0, 1);
    }
}

/////////////////////////////////////////////////////////////////////////

$observaciones = $model->observaciones($_REQUEST['adm']);
$restricciones = $model->restricciones($_REQUEST['adm']);
$interconsultas = $model->interconsultas($_REQUEST['adm']);

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$h_text = 3.5;
$texto = 8;
$conteo = array();
foreach ($observaciones->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->obs_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

foreach ($observaciones->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $conteo[$i] + 1 : 1);
    if ($i === 0) {

        $pdf->SetFont('helvetica', 'B', $titulo);
        $pdf->Cell(180, $h, 'OBSERVACIONES:', 1, 1, 'L', 1);

        $pdf->ln(2);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(155, $h_text * $salteos, $i + 1 . '.- ' . $row2->obs_desc, 'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->obs_plazo, 1, 1, 'C', 0);
    } else {
//        $pdf->SetFont('helvetica', 'B', $texto);
//        $pdf->Cell(33, $h_text * $salteos, '', 0, 0, 'L', 0);

        $pdf->ln(1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(155, $h_text * $salteos, $i + 1 . '.- ' . $row2->obs_desc, 'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->obs_plazo, 1, 1, 'C', 0);
    }
}

/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$h_text = 3.5;

$conteo = array();
foreach ($restricciones->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->restric_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

foreach ($restricciones->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $conteo[$i] + 1 : 1);
    if ($i === 0) {

        $pdf->SetFont('helvetica', 'B', $titulo);
        $pdf->Cell(180, $h, 'RESTRICCIONES:', 1, 1, 'L', 1);

        $pdf->ln(2);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(155, $h_text * $salteos, $i + 1 . '.- ' . $row2->restric_desc, 'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->restric_plazo, 1, 1, 'C', 0);
    } else {
//        $pdf->SetFont('helvetica', 'B', $texto);
//        $pdf->Cell(33, $h_text * $salteos, '', 0, 0, 'L', 0);

        $pdf->ln(1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(155, $h_text * $salteos, $i + 1 . '.- ' . $row2->restric_desc, 'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->restric_plazo, 1, 1, 'C', 0);
    }
}
/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$h_text = 3.5;

$conteo = array();
foreach ($interconsultas->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->inter_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

foreach ($interconsultas->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $conteo[$i] + 1 : 1);
    if ($i === 0) {

        $pdf->SetFont('helvetica', 'B', $titulo);
        $pdf->Cell(180, $h, 'INTERCONSULTAS:', 1, 1, 'L', 1);

        $pdf->ln(2);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(155, $h_text * $salteos, $i + 1 . '.- ' . $row2->inter_desc, 'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->inter_plazo, 1, 1, 'C', 0);
    } else {
//        $pdf->SetFont('helvetica', 'B', $texto);
//        $pdf->Cell(33, $h_text * $salteos, '', 0, 0, 'L', 0);

        $pdf->ln(1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(155, $h_text * $salteos, $i + 1 . '.- ' . $row2->inter_desc, 'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->inter_plazo, 1, 1, 'C', 0);
    }
}
/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////

$h_text = 3.5;

$recomendaciones = $model->recomendaciones($_REQUEST['adm']);
$recomen_total = $recomendaciones->total;

$conteo = array();
foreach ($recomendaciones->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->recom_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

foreach ($recomendaciones->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $conteo[$i] + 1 : 1);
    if ($i === 0) {

        $pdf->SetFont('helvetica', 'B', $titulo);
        $pdf->Cell(180, $h, 'RECOMENDACIONES:', 1, 1, 'L', 1);

        $pdf->ln(2);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(155, $h_text * $salteos, $i + 1 . '.- ' . $row2->recom_desc, 'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->recom_plazo, 1, 1, 'C', 0);
    } else {
//        $pdf->SetFont('helvetica', 'B', $texto);
//        $pdf->Cell(33, $h_text * $salteos, '', 0, 0, 'L', 0);

        $pdf->ln(1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(155, $h_text * $salteos, $i + 1 . '.- ' . $row2->recom_desc, 'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->recom_plazo, 1, 1, 'C', 0);
    }
}

$pdf->Ln($salto);
$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo + 3);
$pdf->Cell(180, $h * 2, $anexo16->data[0]->m_med_aptitud, 0, 1, 'C', 1);

$pdf->Ln($salto);
$pdf->Ln($salto);



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(100, $h, 'FECHA ACTUAL DE EMISION DEL INFORME:' . $paciente->data[0]->fech_reg, 0, 1, 'L', 0);


//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('HOJA_RESUMEN_' . $_REQUEST['adm'] . '.PDF', 'I');