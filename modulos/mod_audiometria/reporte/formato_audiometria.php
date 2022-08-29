<?php

class MYPDF extends TCPDF
{

  public $user;

  public function Header()
  {
  }

  function zerofill($entero, $largo)
  {
    $entero = (int) $entero;
    $largo = (int) $largo;
    $relleno = '';

    if (strlen($entero) < $largo) {
      $relleno = str_repeat(0, $largo - strlen($entero));
    }
    return $relleno . $entero;
  }

  public function Footer()
  {
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
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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
$audio = $model->mod_audio_audio_report($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');



//$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
// $pdf->ImageSVG('images/logo_pdf.svg', 10, 7, 46, '', $link = '', '', 'T');
//CLINICA O2
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');
// $pdf->Image('images/formato/contactos_o2.jpg', 148, 5, 50, '', 'JPEG');

if ($audio->data[0]->medico_firma == '1') {
  $pdf->Image('images/firma/'.$audio->data[0]->medico_cmp.'.jpg', 140, 235, 50, '', 'JPG');
}



$h = 4;
$titulo = 7;
$texto = 7;
$salto = 0;
$pdf->SetFont('helvetica', 'B', 13);
$pdf->Ln(6);
$pdf->Cell(180, $h, 'FICHA AUDIOMETRIA', 0, 1, 'C', 0);
//$pdf->Cell(180, $h, 'EVALUACION MEDICA PERFIL VISITA A 4000 m.s.n.m.', 0, 1, 'C', 0);
$pdf->Ln(4);


$pdf->SetFont('helvetica', 'B', $titulo);
//$pdf->Cell(80, $h, 'DATOS PERSONALES', 0, 1, 'L', 0);

$pdf->Cell(90, $h, '', 0, 0, 'L', 0);
$pdf->Cell(90, $h, 'DATOS GENERALES', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'EMPRESA:', 'LT', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(160, $h, $paciente->data[0]->emp_desc, 'RT', 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'PACIENTE:', 'L', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(90, $h, $paciente->data[0]->nom_ap, '', 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'NRO DE HOJA DE RUTA:', '', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, $h, $paciente->data[0]->adm, 'R', 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, $paciente->data[0]->documento . ':', 'L', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(80, $h, $paciente->data[0]->pac_ndoc, '', 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'FECHA DE EVALUACION:', '', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, $h, $paciente->data[0]->fech_reg_copleto, 'R', 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'SEXO:', 'LB', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->sexo, 'B', 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'EDAD:', 'B', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $paciente->data[0]->edad . ' AÑOS', 'B', 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'TELEFONO:', 'B', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, $h, $paciente->data[0]->pac_cel, 'RB', 1, 'LB', 0); //////VALUE

$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(180, $h, 'EXPOSICION LABORAL', 1, 1, 'L', 1);

$pdf->Cell(180, $h * 2, '', 1, 0, 'L', 0);

$pdf->Ln(0);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'EMPRESA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(65, $h, $paciente->data[0]->emp_desc, 0, 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'OCUPACION:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(65, $h, $audio->data[0]->m_a_audio_ocupacion, 0, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(10, $h, 'AÑOS:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->m_a_audio_anios, 0, 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'HORAS DE EXPOSICION:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->m_a_audio_horas_expo, 0, 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(60, $h, 'APRECIACION DEL RUIDO EN AREA LABORAL:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $audio->data[0]->m_a_audio_ruido_laboral, 0, 1, 'L', 0); //////VALUE


$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(75, $h, 'ANTECEDENTES RELACIONADOS', 1, 0, 'L', 1);
$pdf->Cell(12, $h, 'SI / NO', 1, 0, 'C', 1);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, 'SINTOMAS ACTUALES', 1, 0, 'L', 1);
$pdf->Cell(12, $h, 'SI / NO', 1, 1, 'C', 1);

//$pdf->Cell(180, $h * 2, '', 1, 0, 'L', 0);
//$pdf->Ln(0);



$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- CONSUMO DE TABACO', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_01, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, '- DISMINUCIÓN DE LA AUDICIÓN', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_sintoma_01, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- SERVICIO MILITAR', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_02, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, '- DOLOR DE OÍDOS', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_sintoma_02, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- HOBBIES CON EXPOSICIÓN A RUIDO: TIRO, DISCOTECAS', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_03, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, '- ZUMBIDOS, ACUFENOS', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_sintoma_03, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- EXPOSICIÓN LABORAL A QUÍMICOS', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_04, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, '- MAREOS, VÉRTIGO', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_sintoma_04, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- INFECCIÓN DE OÍDO: OMA, OTITIS CRÓNICA', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_05, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, '- INFECCIÓN DE OÍDO', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_sintoma_05, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- USO DE OTOTOXICOS', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_06, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, '- EXPOSICIÓN RESIENTE A RUIDOS (18HRS)', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_sintoma_06, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- TRAUMATISMO ENCÉFALO CRANEANO, MENINGITIS', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_07, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, '- OTRAS', 'LT', 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_sintoma_07, 'RT', 1, 'C', 0);


$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- TRAUMA ACÚSTICO', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_08, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(87, $h, 'DE EXISTIR SINTOMATOLOGÍA, TIEMPO DE ENFERMEDAD', 'LR', 1, 'L', 0);


$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- SARAMPIÓN', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_09, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'L', 0);
$pdf->Cell(87, $h, $audio->data[0]->m_a_audio_sintoma_07_desc, 'LRB', 1, 'C', 0);


$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, '- PAROTIDITIS', 1, 0, 'L', 0);
$pdf->Cell(12, $h, $audio->data[0]->m_a_audio_antece_10, 1, 1, 'C', 0);


$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(170, $h, 'ANTECEDENTES FAMILIARES: ALGUN FAMILIAR(PADRES,HERMANOS,TIOS O ABUELOS) QUE SUFRAN O HAYAN SUFRIDO DE SORDERA', 1, 0, 'L', 1);
$pdf->Cell(10, $h, $audio->data[0]->m_a_audio_antece_familiar, 1, 1, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(180, $h, $audio->data[0]->m_a_audio_antece_familiar_coment, 1, 1, 'L', 0);


$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(40, $h, 'USO DE EPP AUDITIVO', 1, 0, 'L', 1);
$pdf->Cell(35, $h, 'TAPONES:', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, $h, $audio->data[0]->m_a_audio_tapones, 1, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'OREJERAS:', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, $h, $audio->data[0]->m_a_audio_orejeras, 1, 1, 'L', 0);


$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(180, $h, 'EXAMEN DIRIGIDO', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(23, $h, 'NARIZ:', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $audio->data[0]->m_a_audio_nariz, 1, 0, 'C', 0);
$pdf->Cell(45, $h, $audio->data[0]->m_a_audio_nariz_esp, 1, 0, 'C', 0);

$pdf->Cell(4, $h, '', 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(23, $h, 'OROFARINGE:', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $audio->data[0]->m_a_audio_orofaringe, 1, 0, 'C', 0);
$pdf->Cell(45, $h, $audio->data[0]->m_a_audio_orofaringe_esp, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(23, $h, 'OIDO:', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $audio->data[0]->m_a_audio_oido, 1, 0, 'C', 0);
$pdf->Cell(45, $h, $audio->data[0]->m_a_audio_oido_esp, 1, 0, 'C', 0);

$pdf->Cell(4, $h, '', 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(23, $h, 'OTROS:', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $audio->data[0]->m_a_audio_otros, 1, 0, 'C', 0);
$pdf->Cell(45, $h, $audio->data[0]->m_a_audio_otros_esp, 1, 1, 'C', 0);

$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(87, $h, 'OIDO DERECHO', 1, 0, 'C', 1);
$pdf->Cell(6, $h, '', 0, 0, 'C', 0);
$pdf->Cell(87, $h, 'OIDO IZQUIERDO', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'TRIANGULO DE LUZ:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_triangulo_od, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'TRIANGULO DE LUZ:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_triangulo_oi, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'PERFORACIONES:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_perfora_od, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'PERFORACIONES:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_perfora_oi, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'PERMEABILIDAD:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_permeable_od, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'PERMEABILIDAD:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_permeable_oi, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'RETRACCION:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_retraccion_od, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'RETRACCION:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_retraccion_oi, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'ABOMBAMIENTO:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_abomba_od, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'ABOMBAMIENTO:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_abomba_oi, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'SERUMEN:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_serumen_od, 1, 0, 'C', 0);
$pdf->Cell(6, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'SERUMEN:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(57, $h, $audio->data[0]->m_a_audio_otos_serumen_oi, 1, 1, 'C', 0);


$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $texto);


$pdf->Cell(90, $h, 'OIDO DERECHO', 1, 0, 'C', 1);
$pdf->Cell(90, $h, 'OIDO IZQUIERDO', 1, 1, 'C', 1);

$pdf->Cell(18, $h, '', 1, 0, 'C', 0);
$pdf->Cell(8, $h, '125', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '250', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '500', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '1000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '2000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '3000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '4000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '6000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '8000', 1, 0, 'C', 1);
$pdf->Cell(18, $h, '', 1, 0, 'C', 0);
$pdf->Cell(8, $h, '125', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '250', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '500', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '1000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '2000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '3000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '4000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '6000', 1, 0, 'C', 1);
$pdf->Cell(8, $h, '8000', 1, 1, 'C', 1);


$pdf->Cell(18, $h, 'VIA AEREA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_125_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_250_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_500_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_1000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_2000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_3000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_4000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_6000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_8000_od, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(18, $h, 'VIA AEREA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_125_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_250_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_500_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_1000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_2000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_3000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_4000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_6000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_aereo_8000_oi, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(18, $h, 'VIA OSEA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_125_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_250_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_500_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_1000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_2000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_3000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_4000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_6000_od, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_8000_od, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(18, $h, 'VIA OSEA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_125_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_250_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_500_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_1000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_2000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_3000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_4000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_6000_oi, 1, 0, 'C', 0);
$pdf->Cell(8, $h, $audio->data[0]->m_a_audio_oseo_8000_oi, 1, 1, 'C', 0);


$pdf->Image("images/audio/audiograma_aereo" . $_GET['adm'] . ".png", 5, '', 115, '', '');
$pdf->Image("images/audio/audiograma_oseo" . $_GET['adm'] . ".png", 100, '', 115, '', '');


$pdf->Ln($h*14);
$pdf->SetFont('helvetica', 'B', $texto);
// $pdf->Cell(5, $h, '', 0, 0, 'C', 0);
// $pdf->Cell(82.5, $h, 'VIA AEREA', 0, 0, 'C', 1);
// $pdf->Cell(5, $h, '', 0, 0, 'C', 0);
// $pdf->Cell(82.5, $h, 'VIA OSEA', 0, 1, 'C', 1);


$pdf->Ln(2);
$pdf->Cell(180, $h, 'DIAGNOSTICO AEREA', 1, 1, 'C', 1);

$pdf->Cell(25, $h, 'OIDO DERECHO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(155, $h, $audio->data[0]->m_a_audio_diag_aereo_od, 1, 1, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'OIDO IZQUIERDO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(155, $h, $audio->data[0]->m_a_audio_diag_aereo_oi, 1, 1, 'L', 0);

// $pdf->Ln($salto);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(180, $h, 'DIAGNOSTICO OSEO', 1, 1, 'C', 1);

$pdf->Cell(25, $h, 'OIDO DERECHO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(155, $h, $audio->data[0]->m_a_audio_diag_osteo_od, 1, 1, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'OIDO IZQUIERDO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(155, $h, $audio->data[0]->m_a_audio_diag_osteo_oi, 1, 1, 'L', 0);



// $pdf->Ln($salto);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(120, $h, 'CLASIFICACION DE KCLOKHOFF MOD.', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(120, $h, $audio->data[0]->m_a_audio_kclokhoff, 1, 1, 'L', 0);


// $pdf->Ln($salto);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(120, $h, 'COMENTARIOS', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->MultiCell(120, $h, $audio->data[0]->m_a_audio_comentarios, 1, 'L', 0, 1);


//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('audiometria_' . $_REQUEST['adm'] . '.PDF', 'I');
