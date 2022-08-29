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

$musculo = $model->carga_musculo_pdf($_REQUEST['adm']);
$anexo312 = $model->mod_medicina_312($_REQUEST['adm']);
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
// $pdf->Image('images/formato/contactos_o2.jpg', 148, 5, 50, '', 'JPEG');
$h = 4.9;
$titulo = 8;
$texto = 8;
$salto = 2;

$pdf->Ln(6);
$pdf->SetFont('helvetica', '', $titulo);
// $pdf->Cell(0, 0, 'FORMATO', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 0, 'CERTIFICADO DE APTITUD', 0, 1, 'C');
$pdf->Cell(0, 0, 'MÉDICO OCUPACIONAL', 0, 1, 'C');
// $pdf->Ln(3);
$pdf->Ln(5);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(90, $h, 'CODIGO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(90, $h, $paciente->data[0]->adm, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(90, $h, 'FECHA DE EXAMEN:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(90, $h, $paciente->data[0]->fech_reg, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(180, $h, 'CERTIFICA QUE EL Sr.(a):', 1, 1, 'C', 0);
$pdf->Cell(180, $h * 8, '', 1, 0, 'C', 0);
$pdf->Ln(2);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'NOMBRES Y APELLIDOS:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(140, $h, $paciente->data[0]->nom_ap, 0, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(45, $h, 'DOCUMENTOS DE IDENTIDAD:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, $h, $paciente->data[0]->pac_ndoc, 0, 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'EDAD:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $paciente->data[0]->edad . ' Años', 0, 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'GENERO:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $paciente->data[0]->sexo, 0, 1, 'L', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(65, $h, 'PUESTO DE TRABAJO (AL QUE POSTULA):', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(115, $h, $paciente->data[0]->puesto, 0, 1, 'L', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(65, $h, 'PUESTO ACTUAL O ÚLTIMA OCUPACIÓN:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(115, $h, '-', 0, 1, 'L', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, 'EMPRESA:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(155, $h, $paciente->data[0]->emp_desc, 0, 1, 'L', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'HISTORIA CLINICA:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(50, $h, $paciente->data[0]->pac_ndoc, 0, 0, 'L', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'TIPO DE EVALUACIÓN:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(50, $h, $paciente->data[0]->tipo, 0, 1, 'L', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(55, $h, 'GRUPO SANGUINEO - FACTOR RH:', 0, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(140, $h, $paciente->data[0]->sangre, 0, 1, 'L', 0); //////VALUE




$pdf->Ln($salto * 2.5);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(165, $h, 'CONCLUSIONES', 1, 0, 'L', 1);
$pdf->Cell(5, $h, 'P', 1, 0, 'C', 1);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->Cell(5, $h, 'R', 1, 1, 'C', 1);



$pdf->SetFont('helvetica', '', 6.5);

if (strlen($anexo312->data[0]->m_312_diag_cie1) > 1) {
  $pdf->Cell(5, $h, '', 'LB', 0, 'L', 0);
  $pdf->Cell(160, $h, $anexo312->data[0]->m_312_diag_cie1, 'B', 0, 'L', 0);
  $pdf->Cell(15, $h, $anexo312->data[0]->m_312_diag_st1, 1, 1, 'C', 0);
}

if (strlen($anexo312->data[0]->m_312_diag_cie2) > 1) {
  $pdf->Cell(5, $h, '', 'LB', 0, 'L', 0);
  $pdf->Cell(160, $h, $anexo312->data[0]->m_312_diag_cie2, 'B', 0, 'L', 0);
  $pdf->Cell(15, $h, $anexo312->data[0]->m_312_diag_st2, 1, 1, 'C', 0);
}

if (strlen($anexo312->data[0]->m_312_diag_cie3) > 1) {
  $pdf->Cell(5, $h, '', 'LB', 0, 'L', 0);
  $pdf->Cell(160, $h, $anexo312->data[0]->m_312_diag_cie3, 'B', 0, 'L', 0);
  $pdf->Cell(15, $h, $anexo312->data[0]->m_312_diag_st3, 1, 1, 'C', 0);
}

if (strlen($anexo312->data[0]->m_312_diag_cie4) > 1) {
  $pdf->Cell(5, $h, '', 'LB', 0, 'L', 0);
  $pdf->Cell(160, $h, $anexo312->data[0]->m_312_diag_cie4, 'B', 0, 'L', 0);
  $pdf->Cell(15, $h, $anexo312->data[0]->m_312_diag_st4, 1, 1, 'C', 0);
}

if (strlen($anexo312->data[0]->m_312_diag_cie5) > 1) {
  $pdf->Cell(5, $h, '', 'LB', 0, 'L', 0);
  $pdf->Cell(160, $h, $anexo312->data[0]->m_312_diag_cie5, 'B', 0, 'L', 0);
  $pdf->Cell(15, $h, $anexo312->data[0]->m_312_diag_st5, 1, 1, 'C', 0);
}

if (strlen($anexo312->data[0]->m_312_diag_cie6) > 1) {
  $pdf->Cell(5, $h, '', 'LB', 0, 'L', 0);
  $pdf->Cell(160, $h, $anexo312->data[0]->m_312_diag_cie6, 'B', 0, 'L', 0);
  $pdf->Cell(15, $h, $anexo312->data[0]->m_312_diag_st6, 1, 1, 'C', 0);
}

if (strlen($anexo312->data[0]->m_312_diag_cie7) > 1) {
  $pdf->Cell(5, $h, '', 'LB', 0, 'L', 0);
  $pdf->Cell(160, $h, $anexo312->data[0]->m_312_diag_cie7, 'B', 0, 'L', 0);
  $pdf->Cell(15, $h, $anexo312->data[0]->m_312_diag_st7, 1, 1, 'C', 0);
}

if (strlen($anexo312->data[0]->m_312_diag_cie8) > 1) {
  $pdf->Cell(5, $h, '', 'LB', 0, 'L', 0);
  $pdf->Cell(160, $h, $anexo312->data[0]->m_312_diag_cie8, 'B', 0, 'L', 0);
  $pdf->Cell(15, $h, $anexo312->data[0]->m_312_diag_st8, 1, 1, 'C', 0);
}


$pdf->Ln($salto);


switch ($anexo312->data[0]->m_312_aptitud) {
  case 'APTO':
    $pdf->ImageSVG('images/delete.svg', 82.5, 142, 5, '', $link = '', '', 'T');
    break;
  case 'APTO CON RESTRICCIONES':
    $pdf->ImageSVG('images/delete.svg', 82.5, 152, 5, '', $link = '', '', 'T');
    break;
  case 'OBSERVADO':
    $pdf->ImageSVG('images/delete.svg', 82.5, 161.5, 5, '', $link = '', '', 'T');
    break;
  case 'NO APTO':
    $pdf->ImageSVG('images/delete.svg', 82.5, 171.5, 5, '', $link = '', '', 'T');
    break;
}






$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'APTITUD EN RELACION AL PUESTO DE TRABAJO', 1, 1, 'C', 1);
$pdf->Cell(180, $h * 10, '', 'LRT', 0, 'C', 0);
$pdf->Ln(2);
$pdf->Ln($salto);

// $h=5;
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'APTO', 'LRT', 0, 'L', 0);
$pdf->Cell(10, $h, '', 'LRT', 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(90, $h, 'RESTRICCIONES', 1, 1, 'C', 0);

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'Para el puesto en el que trabaja o postula.', 'LRB', 0, 'L', 0);
$pdf->Cell(10, $h, '', 'LRB', 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 6.3);
$pdf->MultiCell(90, $h * 4,  $anexo312->data[0]->m_312_restricciones, 1, 'L', 0, 0);
$pdf->Ln(4.9);
// $pdf->Cell(90, $h, '-', 1, 1, 'C', 0);

//////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'APTO CON RESTRICCIÓN', 'LRT', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 'LRT', 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(90, $h, '', 0, 1, 'C', 0);

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'Para el puesto en el que trabaja o postula.', 'LRB', 0, 'L', 0);
$pdf->Cell(10, $h, '', 'LRB', 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(90, $h, '', 0, 1, 'C', 0);

//////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, 'OBSERVADO', 'LRT', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 'LRT', 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(90, $h, '', 0, 1, 'C', 0);

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'Para el puesto en el que trabaja o postula.', 'LRB', 0, 'L', 0);
$pdf->Cell(10, $h, '', 'LRB', 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(90, $h, 'OBSERVACIONES', 1, 1, 'C', 0);

//////////////////////////////////

$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'NO APTO', 'LRT', 0, 'L', 0);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 'LRT', 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);

$pdf->SetFont('helvetica', '', 6.3);
$pdf->MultiCell(90, $h * 2,  $anexo312->data[0]->m_312_restricciones, 1, 'L', 0, 0);
$pdf->Ln(4.9);

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'Para el puesto en el que trabaja o postula.', 'LRB', 0, 'L', 0);
$pdf->Cell(10, $h, '', 'LRB', 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(90, $h, '', 0, 1, 'C', 0);



$recomendaciones = $model->recomendaciones($_REQUEST['adm']);
$recomen_total = $recomendaciones->total;

$pdf->Ln($salto);
$pdf->Ln($salto);
// $pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'RECOMENDACIONES', 1, 1, 'L', 1);
$pdf->Cell(180, $h * 9, '', 'LRT', 0, 'C', 0);
$pdf->Ln(2);
$h = 3.5;
$pdf->SetFont('helvetica', 'B', 7);

// $pdf->Ln($salto);
$recom_text .= '';
foreach ($recomendaciones->data as $i => $row) {
  $recom_text .= $i + 1 . '.- ' . $row->recom_desc . "\n";
}
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(115, $h * 10, $recom_text, 0, 'L', 0, 1);


$pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////
$h = 4.5;

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(90, $h, '', 1, 0, 'L', 0);
$pdf->Cell(30, $h, 'MEDICO EVALUADOR', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(60, $h, $anexo312->data[0]->medico, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'FECHA DE EMISION', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, $anexo312->data[0]->m_312_fech_val, 1, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(35, $h, 'FECHA DE VENCIMIENTO', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(25, $h, $anexo312->data[0]->m_312_fech_vence, 1, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(60, $h, 'SELLO Y FIRMA DEL MEDICO QUE CERTIFICA', 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', 7);

if ($anexo312->data[0]->medico_firma == '1') {
  $pdf->Image('images/firma/'.$anexo312->data[0]->medico_cmp.'.jpg', 140, 169, 50, '', 'JPG');
}






//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('Certificado_medico_' . $_REQUEST['adm'] . '.PDF', 'I');
