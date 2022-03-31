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
$conclusion = $model->rpt_conclusion($_REQUEST['adm']);
$manejo = $model->carga_medicina_manejo_pdf($_REQUEST['adm']);
$oftalmo = $model->rpt_oftalmo($_REQUEST['adm']);
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
//$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 10);
//$pdf->Ln(4);
$pdf->Cell(45, $h * 4, '', 0, 0, 'C', 0);
$pdf->Cell(90, $h * 4, 'EXAMEN OCUPACIONAL PARA MANEJO', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(45, $h * 2, $manejo->data[0]->m_med_manejo_tipo_equipo, 1, 1, 'C', 1);

$pdf->Cell(135, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(45, $h * 2, $manejo->data[0]->m_med_manejo_aptitud, 1, 1, 'C', 1);

//$pdf->Ln(8);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'EXAMEN MEDICO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'EMPRESA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(90, $h, $paciente->data[0]->emp_desc, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'APELLIDOS:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $paciente->data[0]->apellidos, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'NOMBRES:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $paciente->data[0]->nombre, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'EDAD:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $paciente->data[0]->edad . ' AÑOS', 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'DNI / ID:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->pac_ndoc, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'FECHA DE EXAMEN:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->fech_reg, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'PUESTO AL QUE POSTULA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(90, $h, $paciente->data[0]->puesto, 1, 1, 'C', 0); //////VALUE
//$h = 5;
$titulo = 6;

$pdf->Ln($salto);

//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 7, '





EVALUAION OFTALMOLOGICA', 1, 'C', 1, 0); //===>TITULO

$pdf->Cell(25, $h * 4, 'AGUDEZA VISUAL', 1, 0, 'C', 0); //===>TITULO
$pdf->Cell(17, $h, '', 1, 0, 'C', 0); //===>TITULO
$pdf->Cell(54, $h, 'SIN CORREGIR', 1, 0, 'C', 1);
$pdf->Cell(54, $h, 'CORREGIDA', 1, 1, 'C', 1);
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(17, $h, '', 1, 0, 'C', 0); //===>TITULO
$pdf->Cell(27, $h, 'VISION DE CERCA', 1, 0, 'C', 1); //===>TITULO
$pdf->Cell(27, $h, 'VISION DE LEJOS', 1, 0, 'C', 1); //===>TITULO
$pdf->Cell(27, $h, 'VISION DE CERCA', 1, 0, 'C', 1); //===>TITULO
$pdf->Cell(27, $h, 'VISION DE LEJOS', 1, 1, 'C', 1); //===>TITULO
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(17, $h, 'OJO DER', 1, 0, 'C', 1); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(27, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vcerca_od, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(27, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vlejos_od, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(27, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vcerca_od, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(27, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vlejos_od, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(17, $h, 'OJO DER', 1, 0, 'C', 1); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(27, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vcerca_oi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(27, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vlejos_oi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(27, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vcerca_oi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(27, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vlejos_oi, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'STEREO TEST (TEST MARIPOSA)', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $manejo->data[0]->m_med_manejo_mariposa, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'EVALUACION DE RECUPERACION', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $manejo->data[0]->m_med_manejo_recupera, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'EVAL. DE COLORES (VERDE, AMARILLO, ROJO)', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $manejo->data[0]->m_med_manejo_colores, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'EVALUACION PHORIA', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $manejo->data[0]->m_med_manejo_phoria, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'EVALUACION DE ENCANDILAMIENTO', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $manejo->data[0]->m_med_manejo_encandila, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'EVALUACION PERIMETRICA', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $manejo->data[0]->m_med_manejo_perimetrica, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA



$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 6, '





PSICOLOGIA', 1, 'C', 1, 0); //===>TITULO
$pdf->Cell(70, $h, 'BENDER', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_bender, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'BC 4 CUESTIONARIO DE ACTITUD FRENTE AL TRANSITO', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_bc4, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TOULOUSE', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_toulouse, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'HOJA DE ENTREVISTA FORMATO ESTABLECIDO', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_entrevista, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'EX. PSICO SENSOMETRICO COMPLETO', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_sensometrico, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TEST LABERINTO-ESCALA WESCHLER', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_weschler, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO PESADO', 1, 1, 'C', 1); //===>TITULO




$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 8, '







PSICOTECNICO', 1, 'C', 1, 0); //===>TITULO
$pdf->Cell(70, $h, 'TEST DE PUNTEADO ', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_test_puntea, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TEST DE PALANCA', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_test_palanca, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TEST REACTIMETRO', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_test_reactimetro, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TEST DOBLE LABERINTO ', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_test_laberinto, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TEST BIMANUAL', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_test_bimanual, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TEST DE ANTICIPACION', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_test_anticipa, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TEST DE REACCION MULTIPLE', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_test_reac_multi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TEST DE RESISTENCIA A LA MONOTOMIA', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $manejo->data[0]->m_med_manejo_test_monotimia, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'EQUIPO LIVIANO / PESADO', 1, 1, 'C', 1); //===>TITULO

$pdf->Ln($salto);
$pdf->Ln($salto);


$observ_total = $conclusion->total;

$conteo = array();
foreach ($conclusion->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->obs_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

foreach ($conclusion->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $h_text * ($conteo[$i] + 1) : $h);
    $text_tamaño = (($conteo[$i] != 0) ? $texto - 1 : ((strlen($row2->obs_desc) > 74) ? $texto - 1 : $texto));
    if ($i === 0) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, (($conteo[$i] != 0) ? $h_text : $h) * ($observ_total + $fila_total), 'RECOMENDACIONES', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(147, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 1);
    } else {
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(147, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 1);
    }
}





//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('TRAB_ALTURA_180_' . $_REQUEST['adm'] . '.PDF', 'I');
