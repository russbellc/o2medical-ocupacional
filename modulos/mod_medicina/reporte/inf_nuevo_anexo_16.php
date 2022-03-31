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
$anexo16 = $model->mod_medicina_anexo16($_REQUEST['adm']);
$diagnostico = $model->diagnostico($_REQUEST['adm']);
$observaciones = $model->observaciones($_REQUEST['adm']);
$restricciones = $model->restricciones($_REQUEST['adm']);
$interconsultas = $model->interconsultas($_REQUEST['adm']);
$recomendaciones = $model->recomendaciones($_REQUEST['adm']);
$medico_auditor = $model->medico($anexo16->data[0]->m_med_medico_auditor);
$medico_ocupa = $model->medico($anexo16->data[0]->m_med_medico_ocupa);



$antecede = $model->rpt_antecedentes_v1($_REQUEST['adm']);
$triaje = $model->rpt_triaje($_REQUEST['adm']);
$oftalmo = $model->rpt_oftalmo($_REQUEST['adm']);
$oftalmo_diag = $model->rpt_oftalmo_diag($_REQUEST['adm']);
$audio = $model->rpt_audiometria($_REQUEST['adm']);
$osteo_conclu = $model->rpt_osteo_conclu($_REQUEST['adm']);
$osteo_aptitud = $model->rpt_osteo($_REQUEST['adm']);
$rayosx = $model->rpt_rayosx($_REQUEST['adm']);
$ekg_conclu = $model->rpt_ekg_conclu($_REQUEST['adm']);
$ekg_desc = $model->rpt_ekg_desc($_REQUEST['adm']);
$psico_informe = $model->rpt_psico_informe($_REQUEST['adm']);
$medicina_manejo = $model->rpt_medicina_manejo($_REQUEST['adm']);
$psicologia_altura = $model->rpt_psicologia_altura($_REQUEST['adm']);

$lab_hemograma = $model->rpt_lab_hemograma($_REQUEST['adm']);

$lipido = $model->rpt_lab_lipido($_REQUEST['adm']);
$glucosa = $model->rpt_lab_examen($_REQUEST['adm'], 23);
$creatinina = $model->rpt_lab_examen($_REQUEST['adm'], 38);
$acido_urico = $model->rpt_lab_examen($_REQUEST['adm'], 43);
$ggtp = $model->rpt_lab_examen($_REQUEST['adm'], 44);
$hbsag = $model->rpt_lab_examen($_REQUEST['adm'], 53);
$rpr = $model->rpt_lab_examen($_REQUEST['adm'], 54);
$hcg = $model->rpt_lab_examen($_REQUEST['adm'], 57);
$grupo_fac = $model->rpt_lab_examen($_REQUEST['adm'], 22);
$drogas = $model->rpt_lab_drogas($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');
$h = 4.3;
$titulo = 7;
$texto = 7;
$salto = 2;
$pdf->SetFont('helvetica', 'B', 7);
//$pdf->Cell(180, $h, 'REG-03-PRO-SAL-01-03', 0, 1, 'R', 0);

$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(3);
$pdf->Cell(180, $h, 'ANEXO 16 - MODIFICADO POR LAS BAMBAS', 0, 1, 'C', 0);
$pdf->Cell(180, $h, 'EXAMEN MÉDICO OCUPACIONAL', 0, 1, 'C', 0);
$pdf->Ln(5);



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'EXAMEN MÉDICO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(45, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(18, $h, 'EMPRESA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(92, $h, $paciente->data[0]->emp_desc, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'APELLIDOS:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(50, $h, $paciente->data[0]->apellidos, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(18, $h, 'NOMBRES:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(50, $h, $paciente->data[0]->nombre, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(4, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'F. DE EXAMEN:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(18, $h, $paciente->data[0]->fech_reg, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'PUESTO AL QUE POSTULA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(42, $h, $anexo16->data[0]->m_med_puesto_postula, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'AREA DE TRABAJO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(42, $h, $anexo16->data[0]->m_med_area, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(6, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'REUBICACION', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'PUESTO ACTUAL:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(42, $h, $anexo16->data[0]->m_med_puesto_actual, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(45, $h, 'TIEMPO EN SU PUESTO ACTUAL:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(27, $h, $anexo16->data[0]->m_med_tiempo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(6, $h, '', 0, 0, 'C', 0);
$pdf->Cell(25, $h * 2, $anexo16->data[0]->m_med_reubicacion, 1, 0, 'C', 0); //////VALUE

$pdf->Ln($h);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'EQUIPO QUE OPERA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(42, $h, $anexo16->data[0]->m_med_eq_opera, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(45, $h, 'FECHA DE INGRESO A EMPRESA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(27, $h, $anexo16->data[0]->m_med_fech_ingreso, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'DOCUMENTO DE IDENTIDAD', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $paciente->data[0]->documento . ' : ' . $paciente->data[0]->pac_ndoc, 1, 0, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'ESTADO CIVIL:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(45, $h, $paciente->data[0]->ecivil, 1, 1, 'C', 0); //////VALUE



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'EDAD:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->edad . ' AÑOS', 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'SEXO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $paciente->data[0]->sexo, 1, 0, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'GRADO DE INSTRUCCION:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(45, $h, $paciente->data[0]->ginstruccion, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(45, $h, 'TELEFONO O CELULAR:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(50, $h, $paciente->data[0]->pac_cel, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'E-MAIL:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(45, $h, $paciente->data[0]->pac_correo, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'LUGAR Y FECHA DE', 'LTR', 0, 'C', 1);
$pdf->Cell(50, $h, 'DEPARTAMENTO', 1, 0, 'C', 1);
$pdf->Cell(50, $h, 'PROVINCIA', 1, 0, 'C', 1);
$pdf->Cell(50, $h, 'FECHA DE NACIMIENTO', 1, 1, 'C', 1);
$pdf->Cell(30, $h, 'NACIMIENTO', 'LBR', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(50, $h, $paciente->data[0]->depa_naci, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(50, $h, $paciente->data[0]->prov_naci, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'DIA/MES/AÑO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->fech_naci, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h * 2, 'DOMICILIO HABITUAL', 1, 0, 'C', 1);
$pdf->Cell(50, $h, 'DIRECCION(Av/Calle/Jiron/Pje):', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->direc, 1, 1, 'C', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, 'DISTRITO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $paciente->data[0]->dist_ubigeo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(18, $h, 'PROVINCIA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $paciente->data[0]->prov_ubigeo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, 'DEPARTAMENTO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(32, $h, $paciente->data[0]->depa_ubigeo, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'PERSONA DE CONTACTO', 'LTR', 0, 'C', 1);
$pdf->Cell(70, $h, 'NOMBRE', 1, 0, 'C', 1);
$pdf->Cell(40, $h, 'PARENTESCO', 1, 0, 'C', 1);
$pdf->Cell(35, $h, 'TELF DE EMERGENCIA', 1, 1, 'C', 1);
$pdf->Cell(35, $h, 'EN CASO DE EMERGENCIA', 'LBR', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $anexo16->data[0]->m_med_contac_nom, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, $anexo16->data[0]->m_med_contac_parent, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(35, $h, $anexo16->data[0]->m_med_contac_telf, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'TIPO DE OPERACION:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $anexo16->data[0]->m_med_tip_opera, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'MINERALES EXPLOTADOS:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $anexo16->data[0]->m_med_minerales, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ALTURA DE LABOR:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $anexo16->data[0]->m_med_altura_lab, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */
$ergo_array = array();
for ($i = 1; $i <= 5; $i++) {
    $ergonomico = strval(m_med_rl_ergo . $i);
    (strlen($anexo16->data[0]->$ergonomico) == 0) ? null : array_push($ergo_array, $i);
}
$fisi_array = array();
for ($i = 1; $i <= 10; $i++) {
    $fisico = strval(m_med_rl_fisico . $i);
    (strlen($anexo16->data[0]->$fisico) == 0) ? null : array_push($fisi_array, $i);
}
$psico_array = array();
for ($i = 1; $i <= 4; $i++) {
    $psico = strval(m_med_rl_psico . $i);
    (strlen($anexo16->data[0]->$psico) == 0) ? null : array_push($psico_array, $i);
}
$quimico_array = array();
for ($i = 1; $i <= 7; $i++) {
    $quimico = strval(m_med_rl_quimi . $i);
    (strlen($anexo16->data[0]->$quimico) == 0) ? null : array_push($quimico_array, $i);
}
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 'RTL', 0, 'C', 1);
$pdf->Cell(20, $h, 'BIOLOGICOS', 1, 0, 'C', 1);
$pdf->Cell(35, $h, 'ERGONOMICOS', 1, 0, 'C', 1);
$pdf->Cell(45, $h, 'FISICOS', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'PSICOSOCIALES', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'QUIMICOS', 1, 1, 'C', 1);

$total_riesgos = max(array(count($ergo_array), count($fisi_array), count($psico_array), count($quimico_array)));

$pdf->Cell(30, $h * $total_riesgos, 'RIESGOS LABORALES', 'RBL', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1);

/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */

if ($total_riesgos == 0) {
    $pdf->Cell(20, $h, $anexo16->data[0]->m_med_rl_bio1, 1, 1, 'C', 0);
}

for ($i = 0; $i < $total_riesgos; $i++) {
    if ($i === 0) {
        $pdf->Cell(20, $h, $anexo16->data[0]->m_med_rl_bio1, 1, 0, 'C', 0);
    } else {
        $pdf->Cell(30, $h, '', 0, 0, 'C', 0);
        $pdf->Cell(20, $h, '', (($total_riesgos - 1) == $i) ? 'B' : 0, 0, 'C', 0);
    }
    $ergo = strval(m_med_rl_ergo . $ergo_array[$i]);
    $fisi = strval(m_med_rl_fisico . $fisi_array[$i]);
    $psico = strval(m_med_rl_psico . $psico_array[$i]);
    $quimico = strval(m_med_rl_quimi . $quimico_array[$i]);

    $pdf->Cell(35, $h, $anexo16->data[0]->$ergo, (count($ergo_array) <= $i) ? (($total_riesgos - 1) == $i) ? 'B' : 0 : 1, 0, 'C', 0);
    $pdf->Cell(45, $h, $anexo16->data[0]->$fisi, (count($fisi_array) <= $i) ? (($total_riesgos - 1) == $i) ? 'B' : 0 : 1, 0, 'C', 0);
    $pdf->Cell(25, $h, $anexo16->data[0]->$psico, (count($psico_array) <= $i) ? (($total_riesgos - 1) == $i) ? 'B' : 0 : 1, 0, 'C', 0);
    $pdf->Cell(25, $h, $anexo16->data[0]->$quimico, (count($quimico_array) <= $i) ? (($total_riesgos - 1) == $i) ? 'BR' : 'R' : 1, 1, 'C', 0);
}

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h * 6.9, 'MUJERES', 1, 0, 'C', 1);
$pdf->Cell(160, $h * 3, '', 1, 0, 'C', 0);

$pdf->Ln($h / 2);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(11, $h, 'FUR:', 0, 0, 'R', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_muj_fur, 1, 0, 'C', 0); //////VALUE
//$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'RC:', 0, 0, 'R', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(5, $h, $anexo16->data[0]->m_med_muj_rc, 1, 0, 'C', 0); //////VALUE
//$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(8, $h, 'G:', 0, 0, 'R', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(5, $h, $anexo16->data[0]->m_med_muj_g, 1, 0, 'C', 0); //////VALUE
//$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(8, $h, 'P:', 0, 0, 'R', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(5, $h, $anexo16->data[0]->m_med_muj_p, 1, 0, 'C', 0); //////VALUE
//$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'ULTIMO PAP:', 0, 0, 'R', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_muj_ult_pap, 1, 0, 'C', 0); //////VALUE
//$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(23, $h, 'RESULTADOS:', 0, 0, 'R', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_muj_resul, 1, 0, 'C', 0); //////VALUE
//$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(11, $h, 'MAC:', 0, 0, 'R', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(5, $h, $anexo16->data[0]->m_med_muj_mac, 1, 1, 'C', 0); //////VALUE
//$pdf->Cell(5, $h, '', 0, 0, 'C', 0);

$pdf->Ln($h / 2);
$pdf->Cell(20, $h, '', 0, 0, 0, 0); //TITULO
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'OBSERVACIONES:', 0, 0, 'R', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(135, $h, $anexo16->data[0]->m_med_muj_obs, 1, 1, 'C', 0); //////VALUE


$pdf->Ln($h / 2);


$pdf->Cell(20, $h * 4, '', 0, 0, 'C', 0);
$pdf->Cell(160, $h * 3.4, '', 1, 0, 'C', 0);

$pdf->Ln($h / 2);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'FORMULA', 'LTR', 0, 'C', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, 'G: a', 1, 0, 'C', 0);
$pdf->Cell(15, $h, '(Gravidez)', 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'a:', 0, 0, 'R', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(5, $h, $anexo16->data[0]->m_med_muj_a, 1, 0, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(22, $h, 'b:', 0, 0, 'R', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(5, $h, $anexo16->data[0]->m_med_muj_b, 1, 0, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(17, $h, 'c:', 0, 0, 'R', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(5, $h, $anexo16->data[0]->m_med_muj_c, 1, 0, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(13, $h, 'd:', 0, 0, 'R', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(5, $h, $anexo16->data[0]->m_med_muj_d, 1, 0, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(12, $h, 'e:', 0, 0, 'R', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(5, $h, $anexo16->data[0]->m_med_muj_e, 1, 1, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'OBSTETRICA', 'LBR', 0, 'C', 0); //TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, 'P: b c d e', 1, 0, 'C', 0); //////VALUE
$pdf->Cell(15, $h, '(Pariedad)', 1, 0, 'C', 0); //////VALUE
//

$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->MultiCell(32, $h, 'nº total embarzos, incluye abortos, molas hidatiformes y embarazos ectopicos', 0, 'C', 0, 0);
//
$pdf->MultiCell(22, $h, 'nº total de recien nacidos a termino', 0, 'C', 0, 0);
//
$pdf->MultiCell(22, $h, 'nº total de recien nacidos preaturos', 0, 'C', 0, 0);
//
$pdf->MultiCell(15, $h, 'nº total de obortos', 0, 'C', 0, 0);

$pdf->MultiCell(20, $h, 'nº total de hijos vivos actualmente', 0, 'C', 0, 1);
//                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            

$pdf->Ln($salto * 2.6);


$observ_total = $antecede->total;

$conteo = array();
foreach ($antecede->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->obs_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

foreach ($antecede->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $h_text * ($conteo[$i] + 1) : $h);
    $text_tamaño = (($conteo[$i] != 0) ? $texto - 1 : ((strlen($row2->obs_desc) > 74) ? $texto - 1 : $texto));
    if ($i === 0) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, (($conteo[$i] != 0) ? $h_text : $h) * ($observ_total + $fila_total), 'ANTECEDENTES LABOR.', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(147, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 1);
    } else {
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(147, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 1);
    }
}


$pdf->Ln($salto);

$h_cardio = 3.5;

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(180, $h, '  ANTECEDENTES CARDIOVASCULARES', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto - 0.7);
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '1.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio * 2, 'ACTUALMENTE FUMA 01 CIGARRILLO A MAS AL DIA?', 1, 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op01, 1, 1, 'C', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '2.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA TENIDO ALGUN TIPO DE ATAQUE, CONVULSION, PERDIDA DE CONOCIMIENTO O EPILEPSIA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op02, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc02, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '3.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿UD. SUFRE O HA SUFRIDO DE PRESION ARTERIAL ALTA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op03, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc03, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '4.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA SUFRIDO ALGUN TIPO DE TRASTORNO MENTAL / PSIQUIATRICO?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op04, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO / DOSIS?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc04, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '5.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA SUFRIDO DE ALGUN TRASTORNO DE SUEÑO?. ¿HA REQUERIDO PASTILLAS PARA DORMIR?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op05, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc05, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '6.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA SUFRIDO DE BRONQUITIS, OTROS PROBLEMAS RESPITORIOS EN LOS ULTIMOS 06 MESES?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op06, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, CUANDO, POR CUANTO TIEMPO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc06, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '7.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ALGUNA HISTORIA DE DIABETES EN LA FAMILIA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op07, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿QUIEN?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc07, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '8.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ALGUNA HISTORIA DE ENFERMEDAD RENAL?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op08, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc08, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '9.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA ESTADO ANTES SOBRE LO 4000 m DE ALTURA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op09, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿DONDE, CUANDO, ALGUN PROBLEMA?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc09, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '10.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA SIDO OPERADO DE / POR ALGO?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op10, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CAUSA, CUANDO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc10, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '11.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ALGUNA HISTORIA DE ANEMIA?, ¿SE ENCUENTRA EMBARAZADA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op11, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, TRATAMIENTO?. ¿SI?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc11, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '12.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ALGUNA HISTORIA DE ENFERMEDAD DE COAGULACION O TROMBOSIS?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op12, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc12, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '13.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿UD. SUFRE DE DOLOR DE PECHO O FALTA DE AIRE AL ESFUERZO?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op13, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc13, 'BR', 1, 'L', 0); //////VALUE

$pdf->setVisibility('screen');
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', '', 'T', false, 300, '', false, false, 0, false, false, false);
//
$pdf->AddPage('P', 'A4');
$pdf->Ln($salto * 3);
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '14.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿UD. SUFRE DE PROBLEMAS CARDIACOS, ANGINA, USA MARCAPASOS?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op14, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc14, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '15.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿UD. SUFRE DE PROBLEMAS CARDIACOS, ANGINA, USA MARCAPASOS?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op15, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc15, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '16.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿SE LE HA DIAGNOSTICADO OBESIDAD MORBIDA (IMC>35 Kg/m2)', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op16, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc16, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '17.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ESTA TOMANDO ALGUN MEDICAMENTO ACTUALMENTE?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op17, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, 'CUAL(ES), EN QUE DOSIS?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc17, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '18.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA TENIDO ALGUN OTRO PROBEMA DE SALUD?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op18, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc18, 'BR', 1, 'L', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'HABITOS ', 1, 0, 'C', 1);
///
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'TABACO ', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $anexo16->data[0]->m_med_tabaco, 1, 0, 'C', 0); //////VALUE
///
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'ALCOHOL ', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $anexo16->data[0]->m_med_alcohol, 1, 0, 'C', 0); //////VALUE
///
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'COCA ', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $anexo16->data[0]->m_med_coca, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(40, $h * 5, 'ANTECEDENTES FAMILIARES', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'PAPÁ', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(80, $h, $anexo16->data[0]->m_med_fam_papa, 1, 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(45, $h, 'NÚMERO DE HIJOS', 1, 1, 'C', 1);
//////
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, 'MAMÁ', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(80, $h, $anexo16->data[0]->m_med_fam_mama, 1, 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(15, $h, 'HIJOS', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'VIVOS', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'MUERTOS', 1, 1, 'C', 1);
//////
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, 'HERMANOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(80, $h, $anexo16->data[0]->m_med_fam_herma, 1, 0, 'L', 0); //////VALUE
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_fam_hijos, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_fam_h_vivos, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_fam_h_muertos, 1, 1, 'C', 0); //////VALUE
//////
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto - 1.5);
$pdf->Cell(125, $h, 'SU PADRE O HERMANO HA TENIDO UN CUADRO DE INFARTO DE MIOCARDIO (ATAQUE AL CORAZON) ANTES DE LOS 55 AÑOS.', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_fam_infarto55, 1, 1, 'C', 0); //////VALUE
//////
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto - 1.5);
$pdf->Cell(125, $h, 'SU MADRE O HERMANA HA TENIDO UN CUADRO DE INFARTO DE MIOCARDIO (ATAQUE AL CORAZON) ANTES DE LOS 65 AÑOS.', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_fam_infarto65, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'TALLA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $triaje->data[0]->m_tri_triaje_talla, 1, 0, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'PESO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $triaje->data[0]->m_tri_triaje_peso, 1, 0, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'IMC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $triaje->data[0]->m_tri_triaje_imc, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'PERIMETRO DE CINTURA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $triaje->data[0]->m_tri_triaje_perim_cintura, 1, 0, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'PERIMETRO DE CADERA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $triaje->data[0]->m_tri_triaje_perim_cadera, 1, 0, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'ICC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $triaje->data[0]->m_tri_triaje_icc, 1, 1, 'C', 0); //////VALUE
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'DX NUTRICION', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(145, $h, $triaje->data[0]->m_tri_triaje_nutricion_dx, 1, 1, 'L', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h * 2, 'FUNCIONES VITALES', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'PA SISTOLICA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $triaje->data[0]->m_tri_triaje_pa_sistolica, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'FC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $triaje->data[0]->m_tri_triaje_fc, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'T°C', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $triaje->data[0]->m_tri_triaje_temperatura, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(25, $h, 'PA DIASTOLICA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $triaje->data[0]->m_tri_triaje_pa_diastolica, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'FR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $triaje->data[0]->m_tri_triaje_fr, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'SO2%', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $triaje->data[0]->m_tri_triaje_saturacion, 1, 1, 'C', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'PERIMETRO TORAXICO', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'EN REPOSO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $triaje->data[0]->m_tri_triaje_perimt_toraxico, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'MAXIMA INSPIRACION', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $triaje->data[0]->m_tri_triaje_maxi_inspiracion, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'EXPIRACION FORZADA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $triaje->data[0]->m_tri_triaje_expira_forzada, 1, 1, 'C', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(40, $h, 'FUNCIONES RESPIRATORIAS', 'LTR', 0, 'C', 1);
$pdf->Cell(17, $h, 'FVC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(17, $h, '-', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(17, $h, 'FEV1', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(17, $h, '-', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(19, $h, 'FEV1/FVC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(17, $h, '-', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(19, $h, 'FEF 25-75%', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(17, $h, '-', 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(40, $h, 'Abs.%', 'LBR', 0, 'C', 1);
$pdf->Cell(25, $h, 'CONCLUSIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(115, $h, '-', 1, 1, 'C', 0);

$pdf->Ln($salto);

//////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h * 2, 'PIEL', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_piel_desc, 'TR', 1, 'L', 0); //////VALUE
//
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'DX:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_piel_dx, 'TBR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h * 2, 'CABEZA', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_cabeza_desc, 'TR', 1, 'L', 0); //////VALUE
//
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'DX:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_cabeza_dx, 'TBR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h * 2, 'CUELLO', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_cuello_desc, 'TR', 1, 'L', 0); //////VALUE
//
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'DX:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_cuello_dx, 'TBR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h * 2, 'NARIZ', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_nariz_desc, 'TR', 1, 'L', 0); //////VALUE
//
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'DX:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_nariz_dx, 'TBR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h * 2, 'BOCA, AMIGDALAS, FARINGE, LARINGE', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_boca_desc, 'TR', 1, 'L', 0); //////VALUE
//
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'DX:', 'TB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_boca_dx, 'TBR', 1, 'L', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'DENTADURA', 1, 0, 'C', 1);
$pdf->Cell(35, $h, 'PIEZAS EN MAL ESTADO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, '', 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'PIEZAS QUE FALTAN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, '', 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'DIAGNOSTICO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, '', 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(15, $h * 10, 'OJOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', 'B', $texto - 1);
$pdf->Cell(15, $h * 2, '', 1, 0, 'C', 0);
$pdf->Cell(40, $h, 'SIN CORREGIR', 1, 0, 'C', 1);
$pdf->Cell(40, $h, 'CORREGIDA', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'VISION DE', 'LTR', 0, 'C', 1);
$pdf->Cell(23, $h, 'REF PUPILARES', 'LTR', 0, 'C', 1);
$pdf->Cell(20, $h * 2, 'ESTEROPSIA', 1, 0, 'C', 1);
$pdf->Cell(7, $h * 2, '%', 1, 0, 'C', 1);
$pdf->Cell(5, $h, '', 0, 1, 'C', 0);
//
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'VISION DE CERCA', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'VISION DE LEJOS', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'VISION DE CERCA', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'VISION DE LEJOS', 1, 0, 'C', 1);

$pdf->Cell(20, $h, 'COLORES', 'LBR', 0, 'C', 1);
$pdf->Cell(23, $h, 'NORMALES', 'LBR', 0, 'C', 1);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(7, $h, '', 0, 1, 'C', 0);
//
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, 'OJO DER', 1, 0, 'C', 1);
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vcerca_od, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vlejos_od, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vcerca_od, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vlejos_od, 1, 0, 'C', 0); //////VALUE

$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_vision_color_od, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(23, $h, $oftalmo->data[0]->m_oft_oftalmo_ref_pupilar_od, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_esteropsia_od, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(7, $h * 2, $oftalmo->data[0]->m_oft_oftalmo_esteropsia, 1, 0, 'C', 0); //////VALUE    % PORCENTAJE
$pdf->Cell(5, $h, '', 0, 1, 'C', 0);
//
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, 'OJO IZQ', 1, 0, 'C', 1);
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vcerca_oi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_sincorrec_vlejos_oi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vcerca_oi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_concorrec_vlejos_oi, 1, 0, 'C', 0); //////VALUE

$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_vision_color_oi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(23, $h, $oftalmo->data[0]->m_oft_oftalmo_ref_pupilar_oi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $oftalmo->data[0]->m_oft_oftalmo_esteropsia_oi, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 1, 'C', 0);
//
$pdf->Ln($h / 2);
//
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h * 2, '', 'LRT', 0, 'C', 1);
$pdf->Cell(23, $h * 2, 'DISCROMATOPSIA', 1, 0, 'C', 1);
$pdf->Cell(15, $h * 2, $oftalmo->data[0]->m_oft_oftalmo_discromatopsia, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, 'TIPO', 1, 0, 'C', 1);
$pdf->Cell(87, $h, $oftalmo->data[0]->m_oft_oftalmo_tipo, 1, 1, 'C', 0); //////VALUE
//
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'ENFERMEDADES', 0, 0, 'C', 0);
$pdf->Cell(23, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'VERDE', 1, 0, 'C', 1);
$pdf->Cell(15.66, $h, $oftalmo->data[0]->m_oft_oftalmo_verde, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, 'AMARILLO', 1, 0, 'C', 1);
$pdf->Cell(15.66, $h, $oftalmo->data[0]->m_oft_oftalmo_amarillo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, 'ROJO', 1, 0, 'C', 1);
$pdf->Cell(15.66, $h, $oftalmo->data[0]->m_oft_oftalmo_rojo, 1, 1, 'C', 0); //////VALUE
//
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'OCULARES', 'LR', 0, 'C', 1);
$pdf->Cell(32, $h, 'AMETROPIA', 1, 0, 'C', 1);
$pdf->Cell(17, $h, $oftalmo->data[0]->m_oft_oftalmo_ametropia, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(32, $h, 'CONJUNTIVITIS', 1, 0, 'C', 1);
$pdf->Cell(16, $h, $oftalmo->data[0]->m_oft_oftalmo_conjuntivitis, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(32, $h, 'OJO ROJO', 1, 0, 'C', 1);
$pdf->Cell(16, $h, $oftalmo->data[0]->m_oft_oftalmo_ojo_rojo, 1, 1, 'C', 0); //////VALUE
//
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, '', 'LRB', 0, 'C', 1);
$pdf->Cell(32, $h, 'CATARATA', 1, 0, 'C', 1);
$pdf->Cell(17, $h, $oftalmo->data[0]->m_oft_oftalmo_catarata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(32, $h, 'NISTAGMOS', 1, 0, 'C', 1);
$pdf->Cell(16, $h, $oftalmo->data[0]->m_oft_oftalmo_nistagmos, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(32, $h, 'PTEREGION', 1, 0, 'C', 1);
$pdf->Cell(16, $h, $oftalmo->data[0]->m_oft_oftalmo_pterigion, 1, 1, 'C', 0); //////VALUE
//
$pdf->Ln($h / 2);
//
$pdf->Cell(15, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'DX', 1, 0, 'C', 1);
$pdf->Cell(145, $h, $oftalmo_diag->data[0]->diag_concat, 1, 1, 'L', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h * 2, 'OIDOS Y TIMPANOS', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(130, $h, $audio->data[0]->m_a_audio_otos_permeable_od . ' / ' . $audio->data[0]->m_a_audio_otos_perfora_od . ' / ' . $audio->data[0]->m_a_audio_otos_retraccion_od . ' / ' . $audio->data[0]->m_a_audio_otos_triangulo_od, 1, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(130, $h, $audio->data[0]->m_a_audio_otos_permeable_oi . ' / ' . $audio->data[0]->m_a_audio_otos_perfora_oi . ' / ' . $audio->data[0]->m_a_audio_otos_retraccion_oi . ' / ' . $audio->data[0]->m_a_audio_otos_triangulo_oi, 1, 1, 'L', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(23, $h * 7, '', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', 'B', $texto - 0.5);
$pdf->Cell(28, $h, '', 1, 0, 'C', 0);
$pdf->Cell(64.5, $h, 'OIDO DERECHO', 1, 0, 'C', 1);
$pdf->Cell(64.5, $h, 'OIDO IZQUIERDO', 1, 1, 'C', 1);
////
$pdf->Cell(23, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto - 0.5);
$pdf->Cell(10, $h * 3, 'VIA', 1, 0, 'C', 0);
$pdf->Cell(18, $h, 'FRECUENCIA', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '250', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '500', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '1000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '2000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '3000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '4000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '6000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '8000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '250', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '500', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '1000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '2000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '3000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '4000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '6000', 1, 0, 'C', 1);
$pdf->Cell(8.0625, $h, '8000', 1, 1, 'C', 1);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(23, $h, 'EXAMEN', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto - 0.5);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(18, $h, 'AEREA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 0.5);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_250_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_500_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_1000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_2000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_3000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_4000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_6000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_8000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_250_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_500_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_1000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_2000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_3000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_4000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_6000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_aereo_8000_oi, 1, 1, 'C', 0);
//$pdf->Cell(8.0625, $h, '0000', 1, 1, 'C', 0);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(23, $h, 'AUDIOMETRICO', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto - 0.5);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(18, $h, 'OSEA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 0.5);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_250_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_500_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_1000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_2000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_3000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_4000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_6000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_8000_od, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_250_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_500_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_1000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_2000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_3000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_4000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_6000_oi, 1, 0, 'C', 0);
$pdf->Cell(8.0625, $h, $audio->data[0]->m_a_audio_oseo_8000_oi, 1, 1, 'C', 0);
////
$pdf->Cell(23, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto - 0.5);
$pdf->Cell(28, $h, 'DIAGNOSTICO CLINICO', 1, 0, 'C', 1);

$diag_od = $audio->data[0]->m_a_audio_diag_aereo_od;
$text_zise1 = (strlen($diag_od) >= 47) ? 2.3 : 1;

$pdf->SetFont('helvetica', '', $texto - $text_zise1);
$pdf->Cell(64.5, $h, $diag_od, 1, 0, 'C', 0);


$diag_oi = $audio->data[0]->m_a_audio_diag_aereo_oi;
$text_zise2 = (strlen($diag_oi) >= 47) ? 2.3 : 1;


$pdf->SetFont('helvetica', '', $texto - $text_zise2);
$pdf->Cell(64.5, $h, $diag_oi, 1, 1, 'C', 0);
////
$pdf->Cell(23, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto - 0.5);
$pdf->Cell(37, $h, 'CLASF. DE KCLOKHOFF MOD.', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->Cell(120, $h, $audio->data[0]->m_a_audio_kclokhoff, 1, 1, 'L', 0);
////
$pdf->Cell(23, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto - 0.5);
$pdf->Cell(28, $h, 'COMENTARIOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->Cell(129, $h, $audio->data[0]->m_a_audio_comentarios, 1, 1, 'L', 0);


$pdf->setVisibility('screen');
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', '', 'T', false, 300, '', false, false, 0, false, false, false);
//
$pdf->AddPage('P', 'A4');
$pdf->Ln($salto * 3);

////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h * 2, 'TORAX', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->Cell(130, $h, $anexo16->data[0]->m_med_torax_desc, 1, 1, 'L', 0);
////
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'DX', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '0', $texto - 1);
$pdf->Cell(130, $h, $anexo16->data[0]->m_med_torax_dx, 1, 1, 'L', 0);
$pdf->Ln($salto);
////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h * 2, 'CORAZON', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->Cell(130, $h, $anexo16->data[0]->m_med_corazon_desc, 1, 1, 'L', 0);
////
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'DX', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '0', $texto - 1);
$pdf->Cell(130, $h, $anexo16->data[0]->m_med_corazon_dx, 1, 1, 'L', 0);
$pdf->Ln($salto);
////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h * 2, 'MAMAS', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DERECHA', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->Cell(130, $h, $anexo16->data[0]->m_med_mamas_derecho, 1, 1, 'L', 0);
////
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'IZQUIERDA', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '0', $texto - 1);
$pdf->Cell(130, $h, $anexo16->data[0]->m_med_mamas_izquier, 1, 1, 'L', 0);
$pdf->Ln($salto);
////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h * 2, 'PULMONES', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->Cell(130, $h, $anexo16->data[0]->m_med_pulmon_desc, 1, 1, 'L', 0);
////
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'DX', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '0', $texto - 1);
$pdf->Cell(130, $h, $anexo16->data[0]->m_med_pulmon_dx, 1, 1, 'L', 0);
$pdf->Ln($salto);
////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h * 2, 'OSTEO - MUSCULAR', 1, 0, 'C', 1);
$pdf->Cell(50, $h * 2, $osteo_aptitud->data[0]->m_osteo_aptitud, 1, 0, 'C', 0);
$pdf->Cell(20, $h * 2, 'DESCRIPCIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto); //36

$m_med_osteo_desc = $osteo_conclu->data[0]->conclu_concat;

if (strlen($m_med_osteo_desc) >= 50)
    $pdf->MultiCell(80, $h * 2, $m_med_osteo_desc, 1, 'C', 1, 0);
else
    $pdf->Cell(80, $h * 2, $m_med_osteo_desc, 1, 1, 'C', 0);

$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h * 4, 'ABDOMEN', 1, 0, 'C', 1);
$pdf->Cell(150, $h, $anexo16->data[0]->m_med_abdomen, 1, 1, 'C', 0);
//
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'DESCRIPCIÓN', 1, 0, 'C', 1);
$pdf->Cell(130, $h, $anexo16->data[0]->m_med_abdomen_desc, 1, 1, 'C', 0);
//
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(22.5, $h, 'PRU SUP DER', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_pru_sup_der, 1, 0, 'C', 0);
$pdf->Cell(22.5, $h, 'PRU SMED DER', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_pru_med_der, 1, 0, 'C', 0);
$pdf->Cell(22.5, $h, 'PRU INF DER', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_pru_inf_der, 1, 0, 'C', 0);
$pdf->Cell(22.5, $h, 'PPL DER', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_ppl_der, 1, 1, 'C', 0);
//
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(22.5, $h, 'PRU SUP IZQ', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_pru_sup_izq, 1, 0, 'C', 0);
$pdf->Cell(22.5, $h, 'PRU SMED IZQ', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_pru_med_izq, 1, 0, 'C', 0);
$pdf->Cell(22.5, $h, 'PRU INF IZQ', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_pru_inf_izq, 1, 0, 'C', 0);
$pdf->Cell(22.5, $h, 'PPL IZQ', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_ppl_izq, 1, 1, 'C', 0);

$pdf->Ln($salto);

$pdf->Cell(30, $h, 'TACTO RECTAL', 1, 0, 'C', 1);
$pdf->Cell(20, $h, $anexo16->data[0]->m_med_tacto, 1, 0, 'C', 0);
$pdf->Cell(20, $h, 'DESCRIPCION', 1, 0, 'C', 1);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_tacto_desc, 1, 1, 'C', 0);

$pdf->Ln($salto);

$pdf->Cell(45, $h, 'ANILLOS INGUINALES/CRURALES', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_anillos, 1, 0, 'C', 0);
$pdf->Cell(45, $h, 'HERNIAS', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_hernia, 1, 0, 'C', 0);
$pdf->Cell(45, $h, 'VARICES', 1, 0, 'C', 1);
$pdf->Cell(15, $h, $anexo16->data[0]->m_med_varices, 1, 1, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto - 1.5);
$pdf->Cell(15, $h, 'DESCRIPCIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1.5);
$pdf->Cell(45, $h, $anexo16->data[0]->m_med_anillos_desc, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto - 1.5);
$pdf->Cell(15, $h, 'DESCRIPCIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1.5);
$pdf->Cell(45, $h, $anexo16->data[0]->m_med_hernia_desc, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto - 1.5);
$pdf->Cell(15, $h, 'DESCRIPCIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1.5);
$pdf->Cell(45, $h, $anexo16->data[0]->m_med_varices_desc, 1, 1, 'C', 0);

$pdf->Ln($salto);

//////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h * 2, 'GENITALES (TESTICULOS)', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_genitales_desc, 1, 1, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'DX', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_genitales_dx, 1, 1, 'C', 0);

$pdf->Ln($salto);

//////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h * 2, 'GANGLIOS', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_ganglios_desc, 1, 1, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'DX', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_ganglios_dx, 1, 1, 'C', 0);

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h * 2, 'LENGUAJE, ATENCIÓN, ORIENTACIÓN', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DESCRIPCIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_lenguaje_desc, 1, 1, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'DX', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(110, $h, $anexo16->data[0]->m_med_lenguaje_dx, 1, 1, 'C', 0);

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h * 9, 'LECTURA DE LA PLACA', 1, 0, 'C', 1);
$pdf->Cell(38.75, $h, 'VERTICE', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(33.75, $h, $rayosx->data[0]->m_rx_rayosx_vertice, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(38.75, $h, 'MEDIASTINOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(33.75, $h, $rayosx->data[0]->m_rx_rayosx_mediastinos, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, 'CAMPOS PULMONARES', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1.2);
$pdf->Cell(42.5, $h, $rayosx->data[0]->m_rx_rayosx_camp_pulmo, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(38.75, $h, 'SILUETA CARDIOVASCULAR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(33.75, $h, $rayosx->data[0]->m_rx_rayosx_silueta_card, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, '', 0, 0, 'C', 0);
$pdf->Cell(38.75, $h, 'HILOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(33.75, $h, $rayosx->data[0]->m_rx_rayosx_hilos, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(15, $h, 'SENOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57.5, $h, $rayosx->data[0]->m_rx_rayosx_senos, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(60, $h, 'CONCLUSIONES RADIOGRAFICAS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(85, $h, $rayosx->data[0]->m_rx_rayosx_concluciones, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(60, $h, 'DESCRIBIR ANORMALIDADES ENCONTRADAS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(85, $h, $rayosx->data[0]->m_rx_rayosx_obs, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'DE TORAX', 0, 0, 'C', 0);
$pdf->Cell(38.75, $h, 'N° Rx', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(33.75, $h, $rayosx->data[0]->m_rx_rayosx_n_placa, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(38.75, $h, 'FECHA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(33.75, $h, $rayosx->data[0]->m_rx_rayosx_fech_lectura, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, '', 0, 0, 'C', 0);
$pdf->Cell(38.75, $h, 'CALIDAD', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(33.75, $h, $rayosx->data[0]->m_rx_rayosx_calidad, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(38.75, $h, 'SIMBOLOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(33.75, $h, $rayosx->data[0]->m_rx_rayosx_simbolo, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, '', 0, 0, 'C', 0);
$pdf->Cell(38.75, $h * 2, $rayosx->data[0]->m_rx_rayosx_profusion, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(33.75, $h * 2, '-', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(72.5, $h, 'CON NEUMOCONOSIS', 1, 1, 'C', 1);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(107.5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, '-', 1, 0, 'C', 0);
$pdf->Cell(52.5, $h, '-', 1, 1, 'C', 0);

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(90, $h, 'HEMOGRAMA', 1, 0, 'C', 1);
$pdf->Cell(90, $h, 'BIOQUIMICA', 1, 1, 'C', 1);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'HEMOGLOBINA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_hemoglobina, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'HEMATOCRITO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_hematocrito, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'GLUCOSA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $glucosa->data[0]->m_lab_exam_resultado, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'COL. TOTAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lipido->data[0]->m_lab_p_lipido_colesterol_total, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(90, $h, 'RECUENTRO CELULAR', 1, 0, 'C', 1);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, '', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, '', 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'HDL COLESTEROL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lipido->data[0]->m_lab_p_lipido_colesterol_hdl, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'LEUCOCITOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_leucocitos, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'PLAQUETAS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_plaquetas, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'CREATININA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $creatinina->data[0]->m_lab_exam_resultado, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'LDL COLESTEROL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lipido->data[0]->m_lab_p_lipido_colesterol_ldl, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(90, $h, 'FORMULA REFERENCIAL', 1, 0, 'C', 1);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'ACIDO URICO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $acido_urico->data[0]->m_lab_exam_resultado, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'VLDL COLESTEROL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lipido->data[0]->m_lab_p_lipido_colesterol_vldl, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'ABASTONADOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_abastonados, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'BASOFILOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_basofilos, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'GGTP', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $ggtp->data[0]->m_lab_exam_resultado, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'TRIGLICERIDOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lipido->data[0]->m_lab_p_lipido_trigliceridos, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'SEGMENTADOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_neutrofilos, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'LINFOCITOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_linfocitos, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, '', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, '', 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(45, $h, 'OTROS', 1, 1, 'C', 1);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'EOSINOFILOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_eosinofilos, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'MONOCITOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $lab_hemograma->data[0]->m_lab_hemo_monocitos, 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, '', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, '', 1, 0, 'C', 0);
////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'REC. RETICULOCITOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, '-', 1, 1, 'C', 0);

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(180, $h, 'INMUNOLOGIA', 1, 1, 'C', 1);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'HBSAG', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $hbsag->data[0]->m_lab_exam_resultado, 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'RPR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $rpr->data[0]->m_lab_exam_resultado, 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'B - HCG', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $hcg->data[0]->m_lab_exam_resultado, 1, 0, 'C', 0);
$pdf->Cell(45, $h, '', 1, 1, 'C', 0);

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(180, $h, 'BACILOSCOPIA', 1, 1, 'C', 1);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'BK EN ESPUTO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(155, $h, '', 1, 1, 'C', 0);

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(28, $h, 'GRUPO Y FACTOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $grupo_fac->data[0]->m_lab_exam_resultado, 1, 0, 'C', 0);
//
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(28, $h, 'ORINA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, '-', 1, 0, 'C', 0);
//
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(34, $h, 'TOXICOLOGICO-ALCOHOL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, '', 1, 1, 'C', 0);

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(180, $h, 'TOXICOLOGICO 10 PARAMETROS DE DROGAS (EN ORINA)', 1, 1, 'C', 1);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'COCAINA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_cocaina, 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'BARBITURICO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_barbiturico, 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'METAMFETAMINA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_methamphentamina, 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'MORPHINA 300', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_morphina, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'MARIHUANA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_marihuana, 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'ANPHETAMINA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_anphetamina, 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'MDMA (XTC)', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_mdma, 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'PHECYCLIDINE', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_phecyclidine, 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'BENZODIAZEPINA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_benzodiazepina, 1, 0, 'C', 0);
//
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'METADONA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $drogas->data[0]->m_lab_drogas_10_metadona, 1, 0, 'C', 0);
//
$pdf->Cell(90, $h, '', 1, 1, 'C', 0);
/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h * 2, 'EKG', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(155, $h, $ekg_conclu->data[0]->conclu_concat, 1, 1, 'L', 0);
///////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'DESCRIPCION', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(135, $h, $ekg_desc->data[0]->m_car_ekg_descripcion, 1, 1, 'L', 0);
/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'PRUEBA DE ESFUERZO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(145, $h, '-', 1, 1, 'C', 0);

////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////

$pdf->setVisibility('screen');
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', '', 'T', false, 300, '', false, false, 0, false, false, false);


$pdf->AddPage('P', 'A4');
$pdf->Ln($salto * 3);

////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h * 7, 'EXAMEN PSICOLOGICO', 1, 0, 'C', 1);
$pdf->Cell(80, $h, 'APTITUD PARA EL PUESTO DE TRABAJO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(67, $h, $psico_informe->data[0]->m_psico_inf_puesto_trabajo, 1, 1, 'C', 0);
/////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'C', 0);
$pdf->Cell(147, $h, 'APTTTUD SEGÚN PERFILES DE TRABAJO', 1, 1, 'C', 1);
//////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, 'BRIGADISTA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(67, $h, $psico_informe->data[0]->m_psico_inf_brigadista, 1, 1, 'C', 0);
//////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, 'CONDUCCION DE EQUIPO  LIVIANO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(67, $h, $psico_informe->data[0]->m_psico_inf_conduc_equip_liviano, 1, 1, 'C', 0);
//////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, 'CONDUCCION DE EQUIPO  PESADO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(67, $h, $psico_informe->data[0]->m_psico_inf_conduc_equip_pesado, 1, 1, 'C', 0);
//////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, 'TRABAJO EN ALTURA DE + DE 1.80 MTS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(67, $h, $psico_informe->data[0]->m_psico_inf_trabajo_altura, 1, 1, 'C', 0);
//////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, 'ESPACIO CONFINADO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(67, $h, $psico_informe->data[0]->m_psico_inf_trab_esp_confinado, 1, 1, 'C', 0);


/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$lim_texto = array(90, 180, 270, 360, 450, 540, 630, 720, 810, 900);
$h_text = 2.8;

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
$pdf->SetFont('helvetica', '', $texto - 0.5);
$pdf->Cell(100, $h, $audio->data[0]->m_a_audio_kclokhoff, 1, 1, 'L', 0);
////
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(33, $h, '', 0, 0, 'L', 0);
$pdf->Cell(27, $h, 'OFTALMOLOGIA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
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
$pdf->SetFont('helvetica', '', $texto);
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

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$observ_total = $observaciones->total;
$restric_total = $restricciones->total;
$inter_total = $interconsultas->total;
$recomen_total = $recomendaciones->total;


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
    $salteos = (($conteo[$i] != 0) ? $h_text * ($conteo[$i] + 1) : $h);
    $text_tamaño = (($conteo[$i] != 0) ? $texto - 1 : ((strlen($row2->obs_desc) > 74) ? $texto - 1 : $texto));
    if ($i === 0) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, (($conteo[$i] != 0) ? $h_text : $h) * ($observ_total + $fila_total), 'OBSERVACIONES', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(122, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(10, $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $$text_tamaño);
        $pdf->Cell(15, $salteos, $row2->obs_plazo, 1, 1, 'C', 0);
    } else {
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(122, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(10, $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->Cell(15, $salteos, $row2->obs_plazo, 1, 1, 'C', 0);
    }
}

/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////

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
    $salteos = (($conteo[$i] != 0) ? $h_text * ($conteo[$i] + 1) : $h);
    $text_tamaño = (($conteo[$i] != 0) ? $texto - 1 : ((strlen($row2->restric_desc) > 74) ? $texto - 1 : $texto));
    if ($i === 0) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, (($conteo[$i] != 0) ? $h_text : $h) * ($restric_total + $fila_total), 'RESTRICCIONES', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(122, $salteos, $i + 1 . '.- ' . $row2->restric_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(10, $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $$text_tamaño);
        $pdf->Cell(15, $salteos, $row2->restric_plazo, 1, 1, 'C', 0);
    } else {
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(122, $salteos, $i + 1 . '.- ' . $row2->restric_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(10, $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->Cell(15, $salteos, $row2->restric_plazo, 1, 1, 'C', 0);
    }
}

/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////

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
    $salteos = (($conteo[$i] != 0) ? $h_text * ($conteo[$i] + 1) : $h);
    $text_tamaño = (($conteo[$i] != 0) ? $texto - 1 : ((strlen($row2->inter_desc) > 74) ? $texto - 1 : $texto));
    if ($i === 0) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, (($conteo[$i] != 0) ? $h_text : $h) * ($inter_total + $fila_total), 'INTERCONSULTAS', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(122, $salteos, $i + 1 . '.- ' . $row2->inter_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(10, $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $$text_tamaño);
        $pdf->Cell(15, $salteos, $row2->inter_plazo, 1, 1, 'C', 0);
    } else {
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(122, $salteos, $i + 1 . '.- ' . $row2->inter_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(10, $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->Cell(15, $salteos, $row2->inter_plazo, 1, 1, 'C', 0);
    }
}

/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
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
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, $h_text * ($recomen_total + $fila_total + 0.08), 'RECOMENDACIONES', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(122, $h_text * $salteos, $i + 1 . '.- ' . $row2->recom_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->recom_plazo, 1, 1, 'C', 0);
    } else {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, $h_text * $salteos, '', 0, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->MultiCell(122, $h_text * $salteos, $i + 1 . '.- ' . $row2->recom_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texto - 1);
        $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $texto - 1);
        $pdf->Cell(15, $h_text * $salteos, $row2->recom_plazo, 1, 1, 'C', 0);
    }
}

/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->MultiCell(50, 3.5 * 2, 'PARA EL PUESTO AL QUE POSTULA', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, 3.5 * 2, $anexo16->data[0]->m_med_aptitud, 1, 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->MultiCell(50, 3.5 * 2, 'PARA MANEJO DE ' . $medicina_manejo->data[0]->m_med_manejo_tipo_equipo, 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, 3.5 * 2, $medicina_manejo->data[0]->m_med_manejo_aptitud, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->MultiCell(50, 3.5 * 2, 'PARA TRABAJOS EN ALTURA ESTRUCTURA (>1.8m)', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, 3.5 * 2, $psicologia_altura->data[0]->m_psico_altura_aptitud, 1, 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->MultiCell(50, 3.5 * 2, 'PARA MANIPULADOR DE ALIMENTOS', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, 3.5 * 2, '', 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->MultiCell(50, 3.5 * 2, 'PARA TRABAJO EN ESPACIOS CONFINDOS', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, 3.5 * 2, $psico_informe->data[0]->m_psico_inf_trab_esp_confinado, 1, 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->MultiCell(50, 3.5 * 2, 'PARA TRABAJOS DE MECANICO Y/O SOLDADOR', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, 3.5 * 2, '', 1, 1, 'C', 0);


/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'MEDICO OCUPCIONAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(85, $h, $medico_ocupa->data[0]->medico_nombres, 1, 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'CMP MED OCUPACIONAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $medico_ocupa->data[0]->medico_cmp, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'MEDICO AUDITOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(85, $h, $medico_auditor->data[0]->medico_nombres, 1, 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'CMP MED AUDITOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $medico_auditor->data[0]->medico_cmp, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'CENTRO MEDICO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(85, $h, 'CENTRO MEDICO OPTIMA', 1, 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'FECHA EVALUACIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $anexo16->data[0]->m_med_fech_val, 1, 1, 'C', 0);


//$medico_auditor = $model->medico($medico_auditor->data[0]->medico_nombres);
//$medico_ocupa = $model->medico($medico_ocupa->data[0]->m_med_medico_ocupa);
/////////////////////////////////////////////////////////////////////////

$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
//
//
//$pdf->SetFont('helvetica', 'B', $texh);
//$pdf->Cell(155, $h, '', 0, 0, 'L', 0);
////$pdf->Cell(145 / 2 - 3, $h, '', 0, 0, 'L', 0);
//$pdf->Cell(25, $h, 'Huella Digital', 0, 1, 'C', 1);
//
//$pdf->SetFont('helvetica', '', $texh);
////$pdf->Image('images/wiman.png', 35, 225, 55, '', 'PNG', 'C');
////$pdf->Image('images/firma.png', 68, 227, 50, '', 'PNG');
//$pdf->Cell(50, $h * 8, '', 1, 0, 'C', 0);
//$pdf->Cell(3, $h * 8, '', 0, 0, 'L', 0);
//$pdf->Cell(49, $h * 8, '', 1, 0, 'L', 0);
//$pdf->Cell(3, $h * 8, '', 0, 0, 'L', 0);
//$pdf->Cell(50, $h * 8, '', 1, 0, 'L', 0);
//$pdf->Cell(25, $h * 8, '', 1, 1, 'L', 0);
//
//
//$pdf->SetFont('helvetica', '', $texh);
//$pdf->Cell(50, $h, 'Sello y Firma del Medico', 0, 0, 'C', 0);
//$pdf->Cell(3, $h, '', 0, 0, 'L', 0);
//
//$pdf->SetFont('helvetica', '', $texh);
//$pdf->Cell(49, $h, 'Sello y Firma del Medico Auditor', 0, 0, 'C', 0);
//$pdf->Cell(3, $h, '', 0, 0, 'L', 0);
//
//$pdf->SetFont('helvetica', 'B', 6.5);
//$pdf->Cell(75, $h, $paciente->data[0]->apellidos . ', ' . $paciente->data[0]->nombre . '    DNI: ' . $paciente->data[0]->pac_ndoc, 0, 1, 'C', 0);
//
//$pdf->setVisibility('screen');
//$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', '', 'T', false, 300, '', false, false, 0, false, false, false);
//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('Anexo_16_' . $_REQUEST['adm'] . '.PDF', 'I');
