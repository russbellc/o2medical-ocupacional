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

$psico_informe = $model->carga_psico_informe_pdf($_REQUEST['adm']);

//$observaciones = $model->observaciones_16a($_REQUEST['adm']);
//$anexo16 = $model->mod_medicina_anexo16($_REQUEST['adm']);
//$med_16a = $model->mod_medicina_16a($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');
$h = 5;
$titulo = 8;
$texto = 7.5;
$salto = 2;
$pdf->SetFont('helvetica', 'B', 7);
//$pdf->Cell(180, $h, 'REG-03-PRO-SAL-01-03', 0, 1, 'R', 0);

$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Ln(7);
$pdf->Cell(180, $h, 'INFORME PSICOLOGICO PRE-OCUPACIONAL', 0, 1, 'C', 0);
//$pdf->Cell(180, $h, 'EVALUACION MEDICA PERFIL VISITA A 4000 m.s.n.m.', 0, 1, 'C', 0);
$pdf->Ln(3);

//$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'FECHA: ' . $paciente->data[0]->fech_reg, 0, 1, 'R', 0);

$pdf->Ln($salto);
$pdf->Cell(180, 5.05 * 4, '', 1, 0, 'C', 0);
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

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'COMPETENCIAS PSICOLOGICAS', 1, 1, 'C', 1);

$pdf->Cell(60, $h * 3, 'COGNITIVAS', 1, 0, 'C', 0);
$pdf->Cell(60, $h, 'CAPACIDAD INTELECTUAL:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_capac_intelectual, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'ATENCION Y CONCENTRACION:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_aten_concentracion, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'ORIENTACION ESPACIAL:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_orient_espacial, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h * 4, 'AFECTIVAS', 1, 0, 'C', 0);
$pdf->Cell(60, $h, 'PERSONALIDAD HTP:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_pers_htp, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'PERSONALIDAD SALAMANCA:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_pers_salamanca, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'INTELIGENCIA EMOCIONAL:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_intel_emocional, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'CARACTEROLIGÍA:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_caracterologia, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h * 3, 'TEMORES', 1, 0, 'C', 0);
$pdf->Cell(60, $h, 'ALTURA:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_alturas, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'ESPACIOS CONFINADOS:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_esp_confinados, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'OTROS:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_otros, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h * 2, 'PSICOTECNICO', 1, 0, 'C', 0);
$pdf->Cell(60, $h, 'PRECISION, DESTREZA, REACCION:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_precis_destre_reac, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'ANTICIPACION, BIMANUAL, MONOTONIA:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_antici_bim_mono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'TIPO DE CONDUCTA', 1, 0, 'C', 0);
$pdf->Cell(60, $h, 'ACTITUD FRENTE AL TRANSITO:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $psico_informe->data[0]->m_psico_inf_actitud_f_trans, 1, 1, 'C', 0); //////VALUE



$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'FORTALEZAS', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h * 2, $psico_informe->data[0]->m_psico_inf_resultados, 1, 'L', 0, 1);


$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'DEBILIDADES', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h * 2, $psico_informe->data[0]->m_psico_inf_debilidades, 1, 'L', 0, 1);


$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'CONCLUSIONES', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h * 2, $psico_informe->data[0]->m_psico_inf_conclusiones, 1, 'L', 0, 1);


$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'RECOMENDACIONES', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(180, $h * 2, $psico_informe->data[0]->m_psico_inf_recomendaciones, 1, 'L', 0, 1);


$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'PSICÓLOGO EVALUADOR:', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, 'REYNOSO PANTIGOZO MARLENE AMELIA    C.Ps.P: 14888', 0, 1, 'L', 0);


$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'CONDICION DIAGNOSTICA PARA LABORAR', 1, 1, 'L', 1);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'PUESTA DE TRABAJO:', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $psico_informe->data[0]->m_psico_inf_puesto_trabajo, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'BRIGADISTA:', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $psico_informe->data[0]->m_psico_inf_brigadista, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'CONDUCCION DE EQUIPO LIVIANO:', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $psico_informe->data[0]->m_psico_inf_conduc_equip_liviano, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'CONDUCCION DE EQUIPO LIVIANO:', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $psico_informe->data[0]->m_psico_inf_conduc_equip_pesado, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'TRABAJO EN ALTURA A +1.80 mtrs.:', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $psico_informe->data[0]->m_psico_inf_trabajo_altura, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'TRABAJO EN ESPACIO CONFINADO:', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $psico_informe->data[0]->m_psico_inf_trab_esp_confinado, 1, 1, 'C', 0);




//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('Anexo_16_' . $_REQUEST['adm'] . '.PDF', 'I');
