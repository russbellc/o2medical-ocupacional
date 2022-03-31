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

$psico_examen = $model->carga_psico_examen_pdf($_REQUEST['adm']);

//$observaciones = $model->observaciones_16a($_REQUEST['adm']);
//$anexo16 = $model->mod_medicina_anexo16($_REQUEST['adm']);
//$med_16a = $model->mod_medicina_16a($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');
$h = 4;
$titulo = 7;
$texto = 7;
$salto = 2;
$pdf->SetFont('helvetica', 'B', 7);
//$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 10, 7, 46, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 13);
$pdf->Ln(6);
$pdf->Cell(180, $h, 'EXAMEN DE PSICOLOGIA', 0, 1, 'C', 0);
//$pdf->Cell(180, $h, 'EVALUACION MEDICA PERFIL VISITA A 4000 m.s.n.m.', 0, 1, 'C', 0);
$pdf->Ln(7);


//$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(90, $h, $paciente->data[0]->documento . ': ' . $paciente->data[0]->pac_ndoc, 0, 0, 'L', 0);
$pdf->Cell(90, $h, 'FECHA: ' . $paciente->data[0]->fech_reg, 0, 1, 'R', 0);

$pdf->Ln($salto);
$pdf->Cell(180, ($h + 0.05) * 5, '', 1, 0, 'C', 0);
$pdf->Ln(0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'PACIENTE:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(77, $h, $paciente->data[0]->nom_ap, 0, 0, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'NRO DE HOJA DE RUTA:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(23, $h, $paciente->data[0]->adm, 0, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'GRADO DE INSTRUCCION:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $paciente->data[0]->ginstruccion, 0, 0, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'EDAD:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $paciente->data[0]->edad . ' AÑOS', 0, 0, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, $paciente->data[0]->documento . ':', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(23, $h, $paciente->data[0]->pac_ndoc, 0, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'EMPRESA:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(145, $h, $paciente->data[0]->emp_desc, 0, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'OCUPACION:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(77, $h, $paciente->data[0]->puesto, 0, 0, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(27, $h, 'TIPO DE EXAMEN:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(36, $h, $paciente->data[0]->tipo, 0, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'FECHA DE NACIMIENTO:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->fech_naci, 0, 0, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'ESTADO CIVIL:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->ecivil, 0, 0, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'LUGAR DE RECIDENCIA:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(45, $h, $paciente->data[0]->ubica_ubigeo, 0, 1, 'L', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'DATOS OCUPACIONALES:', 1, 1, 'L', 1);

$pdf->Cell(180, $h * 4, '', 1, 0, 'L', 0);
$pdf->Ln(0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'NOMBRE DE LA EMPRESA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(140, $h, $paciente->data[0]->emp_desc, 0, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'ACTIVIDAD DE LA EMPRESA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(140, $h, $psico_examen->data[0]->m_psico_exam_activ_empresa, 0, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'ÁREA DE TRABAJO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(140, $h, $psico_examen->data[0]->m_psico_exam_area_trabajo, 0, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'PUESTO DE TRABAJO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(80, $h, $psico_examen->data[0]->m_psico_exam_puesto, 0, 0, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'TIEMPO TOTAL LABORANDO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $psico_examen->data[0]->m_psico_exam_tiempo_labor, 0, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'PRINCIPALES RIESGOS:', 'LRT', 1, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h, $psico_examen->data[0]->m_psico_exam_princ_riesgos, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'MEDIDAS DE SEGURIDAD:', 'LRT', 1, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h, $psico_examen->data[0]->m_psico_exam_medi_seguridad, 'LRB', 'L', 0, 1);


$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'HISTORIA FAMILIAR:', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h * 2, $psico_examen->data[0]->m_psico_exam_histo_familiar, 1, 'L', 0, 1);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'ACCIDENTES Y ENFERMEDADES: (DURANTE EL TIEMPO DE TRABAJO)', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h * 2, $psico_examen->data[0]->m_psico_exam_accid_enfermedad, 1, 'L', 0, 1);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'HABITOS: (PASA TIEMPOS, CONSUMO DE TABACO, ALCOHOL Y/O DROGAS)', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h * 2, $psico_examen->data[0]->m_psico_exam_habitos, 1, 'L', 0, 1);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'OTRAS OBSERVACIONES:', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h * 2, $psico_examen->data[0]->m_psico_exam_otras_obs, 1, 'L', 0, 1);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'EXAMEN MENTAL:', 1, 1, 'L', 1);



$pdf->Cell(90, ($h + 0.05) * 11, '', 'LRT', 0, 'L', 0);
$pdf->Ln(0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(90, $h, 'OBSERVACION DE CONDUCTA:', 0, 0, 'L', 0);
$pdf->Cell(10, $h, 'PTJE', 1, 0, 'C', 0);
$pdf->Cell(80, $h, 'NOMBRE', 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- PRESENTACIÓN:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_presentacion, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_01, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'INVENTARIO MILLON DE ESTILOS DE PERSONALIDAD - MIPS', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- POSTURA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_postura, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_02, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'CUESTIONARIO DE PERSONALIDAD - EYSENCK', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- DISCURSO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, '', 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_03, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'ESCALA DE MOTIVACIONES PSICOSOCIALES - MPS', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, '- RITMO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_ritmo, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_04, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'LURIA - DNA DIAGNOSTICO NEUROPSICOLOGICO DE ADULTOS', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, '- TONO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_tono, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_05, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'ESCALA DE APRECIACION DEL ESTRES - EAE', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, '- ARTICULACIÓN:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_articulacion, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_06, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'INVENTARIO DE BURNOUT DE MASIACH', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- ORIENTACION:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, '', 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_07, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'CLIMA LABORAL', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, '- TIEMPO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_tiempo, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_08, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'BATERIA DE CONDUCTORES', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, '- ESPACIO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_espacio, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_09, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'WAIS', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, '- PERSONA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_persona, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_10, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'TEST BENTON', 1, 1, 'L', 0);

$pdf->Ln(0.05);
$pdf->Cell(90, ($h + 0.05) * 11, '', 1, 0, 'L', 0);
$pdf->Ln(0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(90, $h, 'PROCESOS COGNITIVOS:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_11, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'TEST BENDER', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- LUCIDO, ATENTO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_lucido_atent, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_12, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'INVENTARIO DE LA ANCIEDAD ZUNG', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- PENSAMIENTO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_pensamiento, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_13, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'INVENTARIO DE LA DEPRECION ZUNG', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- PERCEPCIÓN:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_persepcion, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, $psico_examen->data[0]->m_psico_exam_ptje_test_14, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, 'ESCALA DE MEMORIA DE WECHSLER', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- MEMORIA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_memoria, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(80, $h, '', 0, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- INTELIGENCIA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_inteligencia, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, '', 0, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- APETITO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_apetito, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, '', 0, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- SUEÑO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_sueno, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, '', 0, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- PERSONALIDAD:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_personalidad, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, '', 0, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- AFECTIVIDAD:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_afectividad, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, '', 0, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(30, $h, '- CONDUCTA SEXUAL:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $psico_examen->data[0]->m_psico_exam_conduc_sexual, 0, 0, 'L', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, '', 0, 1, 'L', 0);

$pdf->Ln($salto);
//$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(115, $h, 'DIAGNOSTICO FINAL:', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(90, $h, ' - AREA COGNITIVA:', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(115, $h * 2, $psico_examen->data[0]->m_psico_exam_area_cognitiva, 1, 'L', 0, 1);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(90, $h, ' - AREA EMOCIONAL:', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(115, $h * 2, $psico_examen->data[0]->m_psico_exam_area_emocional, 1, 'L', 0, 1);


//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('PSICOLOGIA_EXAMEN_' . $_REQUEST['adm'] . '.PDF', 'I');
