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

$altura = $model->carga_altura_pdf($_REQUEST['adm']);
// $psico_examen = $model->carga_psico_examen_pdf($_REQUEST['adm']);

//$observaciones = $model->observaciones_16a($_REQUEST['adm']);
//$anexo16 = $model->mod_medicina_anexo16($_REQUEST['adm']);
//$med_16a = $model->mod_medicina_16a($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');
//OPTIMA
// $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
//$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
// $pdf->ImageSVG('images/logo_pdf.svg', 10, 7, 46, '', $link = '', '', 'T');

//CLINICA O2
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');


if ($altura->data[0]->medico_firma == '1') {
  $pdf->Image('images/firma/'.$altura->data[0]->medico_cmp.'.jpg', 140, 235, 50, '', 'JPG');
}
// $pdf->Image('images/formato/contactos_o2.jpg', 148, 5, 50, '', 'JPEG');
$h = 4.5;
$titulo = 7;
$texto = 7;
$salto = 2;

$pdf->Ln(6);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(0, 0, 'FORMATO', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 0, 'EVALUACIÓN MEDICA PARA TRABAJOS', 0, 1, 'C');
$pdf->Cell(0, 0, 'EN ALTURA ESTRUCTURAL MAYOR A 1.8 METROS', 0, 1, 'C');
// $pdf->Ln(3);
$pdf->Ln(5);




$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, '', 1, 0, 'C', 0);
$pdf->Ln(0);
$pdf->Cell(90, $h, $paciente->data[0]->documento . ': ' . $paciente->data[0]->pac_ndoc, 0, 0, 'L', 0);
$pdf->Cell(90, $h, 'FECHA: ' . $paciente->data[0]->fech_reg, 0, 1, 'R', 0);

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
$pdf->Cell(180, $h, 'ANTECEDENTES LABORAL EN ALTURA MAYOR A 1.8 METROS:', 1, 1, 'L', 1);
$pdf->Cell(180, ($h * 3) + 1.4, '', 1, 0, 'L', 0);

$pdf->Ln(0);
$pdf->Ln($salto);

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, '¿EXPERIENCIA EN ALTURA ESTRUCTURAL ANTERIORMENTE?', 0, 0, 'L', 0);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_lab_experiencia, 1, 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 1, 'L', 0);

$pdf->Ln($salto);

$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, '¿HA TENIDO PROBLEMAS CON SU SALUD EN TRABAJOS DE ALTURA?', 0, 0, 'L', 0);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_lab_problem, 1, 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 1, 'L', 0);

$pdf->Ln($salto);
$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);

$pdf->Cell(45, $h, 'ANTECEDENTES PATOLOGICOS:', 1, 0, 'L', 1);
$pdf->Cell(10, $h, 'SI/NO', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'OBSERVACIONES', 1, 0, 'C', 1);
$pdf->Cell(50, $h, '', 1, 0, 'L', 1);
$pdf->Cell(10, $h, 'SI/NO', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'OBSERVACIONES', 1, 0, 'C', 1);
$pdf->Cell(5, $h, '', 1, 1, 'L', 1);


$pdf->Cell(180, $h * 9.5, '', 1, 0, 'L', 0);
$pdf->Ln(0);

$pdf->SetFont('helvetica', '', $titulo);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Convulciones, epilepsia', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_conv_epilep, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_conv_epilep_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Cefales, migrañas', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_migra, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_migra_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
///////////////////////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Mareos, vertigos', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_mareo, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_mareo_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Tec moderado - severo', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_tec, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_tec_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
///////////////////////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Insomnio', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_insomnio, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_insomnio_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Enfermedades psiquiatricas', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_enf_psiq, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_enf_psiq_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
///////////////////////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Enfermedades Cardiovasculares', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_enf_cardio, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_enf_cardio_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Enfermedades Oculares', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_enf_ocular, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_enf_ocular_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
///////////////////////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Hipoacusia severa', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_hipoacusia, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_hipoacusia_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Diabetes no controlada', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_diabetes, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_diabetes_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
///////////////////////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'HTA no controlada', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_hta, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_hta_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Acrofobia/Agorofobia', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_acrof, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_acrof_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
///////////////////////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Asma bronquial n/c', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_asma, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_asma_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Epoc', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_epoc, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_epoc_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
///////////////////////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Medicamentos psicotropicos', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_med_psico, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_med_psico_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Consumo de drogas', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_cons_droga, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_cons_droga_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
///////////////////////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Alcoholismo', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_alcohol, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_alcohol_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Otros declarados', 0, 0, 'L', 0);
$pdf->Cell(10, $h, $altura->data[0]->m_altura_ante_pato_otros, 1, 0, 'C', 0);
$pdf->Cell(30, $h, $altura->data[0]->m_altura_ante_pato_otros_desc, 1, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
///////////////////////////////////////////////////


$pdf->Ln($salto);
$pdf->Ln($salto);



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'INFORMACION OFTALMOLOGICA', 1, 1, 'L', 1);
$pdf->Cell(180, $h * 4, '', 1, 0, 'L', 0);
$pdf->Ln(0);
$pdf->Ln($salto);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, 'AGUDEZA VISUAL', 1, 0, 'C', 0);
$pdf->Cell(30, $h, 'SIN CORRECTORES', 1, 0, 'C', 0);
$pdf->Cell(30, $h, 'CON CORRECTORES', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'TEST DE ESTEREOPSIS: '.$altura->data[0]->m_oft_oftalmo_esteropsia.'%', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(35, $h, 'TEST DE ISHIHARA', 1, 1, 'C', 0);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h * 2, 'DE LEJOS', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'OD', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'OI', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'OD', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'OI', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(20, $h, 'OD', 1, 0, 'C', 0);
$pdf->Cell(20, $h, 'OI', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(35, $h * 2, $altura->data[0]->m_oft_oftalmo_ishihara, 1, 0, 'C', 0);

$pdf->Ln($h);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, $altura->data[0]->m_oft_oftalmo_sincorrec_vlejos_od, 1, 0, 'C', 0);
$pdf->Cell(15, $h, $altura->data[0]->m_oft_oftalmo_sincorrec_vlejos_oi, 1, 0, 'C', 0);
$pdf->Cell(15, $h, $altura->data[0]->m_oft_oftalmo_concorrec_vlejos_od, 1, 0, 'C', 0);
$pdf->Cell(15, $h, $altura->data[0]->m_oft_oftalmo_concorrec_vlejos_oi, 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(20, $h, $altura->data[0]->m_oft_oftalmo_esteropsia_od, 1, 0, 'C', 0);
$pdf->Cell(20, $h, $altura->data[0]->m_oft_oftalmo_esteropsia_oi, 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);





$pdf->Ln($salto);
$pdf->Ln($salto);



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'INFORMACION LABORATORIO', 1, 1, 'L', 1);
$pdf->Cell(180, $h * 2, '', 1, 0, 'L', 0);
$pdf->Ln(0);
$pdf->Ln($salto);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(35, $h, 'GLUCOSA', 1, 0, 'C', 0);
$pdf->Cell(15, $h, $altura->data[0]->lab_glucosa, 1, 0, 'C', 0);

$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(35, $h, 'COLESTEROL', 1, 0, 'C', 0);
$pdf->Cell(15, $h, $altura->data[0]->m_lab_p_lipido_colesterol_total, 1, 0, 'C', 0);

$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(35, $h, 'TRIGLICERIDOS', 1, 0, 'C', 0);
$pdf->Cell(15, $h, $altura->data[0]->m_lab_p_lipido_trigliceridos, 1, 1, 'C', 0);





$pdf->Ln($salto);
$pdf->Ln($salto);



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'EXAMEN FISICO', 1, 1, 'L', 1);
$pdf->Cell(180, $h * 9, '', 1, 0, 'L', 0);
$pdf->Ln(0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, 'Vision estereoscópica', 0, 0, 'L', 0);
$pdf->Cell(20, $h, $altura->data[0]->m_altura_exa_fis_estereos, 1, 1, 'C', 0);


$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, 'Sustentación en un pie por 15 Seg', 0, 0, 'L', 0);
$pdf->Cell(20, $h, $altura->data[0]->m_altura_exa_fis_sustenta, 1, 1, 'C', 0);


$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, 'Caminar libre sobre recta 3m (Sin desvio)', 0, 0, 'L', 0);
$pdf->Cell(20, $h, $altura->data[0]->m_altura_exa_fis_sobre_3m, 1, 1, 'C', 0);


$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, 'Caminar libre ojos vendados 3m (Sin desvio)', 0, 0, 'L', 0);
$pdf->Cell(20, $h, $altura->data[0]->m_altura_exa_fis_ojo_3m, 1, 1, 'C', 0);


$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, 'Caminar libre ojos vendados punta-talon 3m (Sin desvio)', 0, 0, 'L', 0);
$pdf->Cell(20, $h, $altura->data[0]->m_altura_exa_fis_punta_talon, 1, 1, 'C', 0);


$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, 'Limitación en fuerza o movilidad de extremidades', 0, 0, 'L', 0);
$pdf->Cell(20, $h, $altura->data[0]->m_altura_exa_fis_lim_fuerza, 1, 1, 'C', 0);


$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, 'Diadococinesis', 0, 0, 'L', 0);
$pdf->Cell(20, $h, $altura->data[0]->m_altura_exa_fis_diadococinesis, 1, 1, 'C', 0);


$pdf->Ln($salto);

$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->MultiCell(160, $h, 'OBSERVACIONES: ' . $altura->data[0]->m_altura_exa_fis_obs, 0, 'L', 0, 1);



$pdf->Ln(2);


$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(180, $h, 'APTITUD MÉDICA', 1, 'C', 0, 1);

$pdf->SetFont('helvetica', 'B', 13);
$pdf->MultiCell(180, $h, $altura->data[0]->m_altura_aptitud, 1, 'C', 0, 1);


$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'CONCLUSIONES: ', 1, 0, 'L', 1);
$pdf->Cell(140, $h, $altura->data[0]->m_altura_conclu, 1, 1, 'C', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', '', $titulo);
$pdf->MultiCell(180, $h, 'RECOMENDACIONES:
'.$altura->data[0]->m_altura_recom, 1, 'L', 0, 1);



// $pdf->Cell(20, $h, 'OBSERVACIONES', 1, 0, 'L', 0);



//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('PSICOLOGIA_EXAMEN_' . $_REQUEST['adm'] . '.PDF', 'I');
