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
$h = 4.5;
$titulo = 7;
$texto = 7;
$salto = 2;

$pdf->Ln(6);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(0, 0, 'FORMATO', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 0, 'EVALUACIÓN OSTEOMUSCULAR', 0, 1, 'C');
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
//IMAGENES
$pdf->Image('images/musculo/flexi_1.jpg', 42, 60, 25, '', 'JPG');
$pdf->Image('images/musculo/flexi_2.jpg', 70, 60, 25, '', 'JPG');
$pdf->Image('images/musculo/flexi_3.jpg', 99, 60, 25, '', 'JPG');
$pdf->Image('images/musculo/flexi_4.jpg', 127, 60, 25, '', 'JPG');

$pdf->Image('images/musculo/cadera_1.jpg', 42, 79, 25, '', 'JPG');
$pdf->Image('images/musculo/cadera_2.jpg', 70, 79, 25, '', 'JPG');
$pdf->Image('images/musculo/cadera_3.jpg', 99, 79, 25, '', 'JPG');
$pdf->Image('images/musculo/cadera_4.jpg', 127, 79, 25, '', 'JPG');

$pdf->Image('images/musculo/muslo_1.jpg', 42, 99, 25, '', 'JPG');
$pdf->Image('images/musculo/muslo_2.jpg', 70, 99, 25, '', 'JPG');
$pdf->Image('images/musculo/muslo_3.png', 99, 99, 25, '', 'PNG');
$pdf->Image('images/musculo/muslo_4.jpg', 127, 99, 25, '', 'JPG');

$pdf->Image('images/musculo/abdom_1.jpg', 42, 115, 25, '', 'JPG');
$pdf->Image('images/musculo/abdom_2.png', 70, 115, 25, '', 'PNG');
$pdf->Image('images/musculo/abdom_3.jpg', 99, 115, 25, '', 'JPG');
$pdf->Image('images/musculo/abdom_4.png', 127, 115, 25, '', 'PNG');


$pdf->Image('images/musculo/abduc_180_1.jpg', 39, 145, 31, '', 'JPG');
$pdf->Image('images/musculo/abduc_180_2.jpg', 67, 145, 31, '', 'JPG');
$pdf->Image('images/musculo/abduc_180_3.jpg', 95, 145, 31, '', 'JPG');

$pdf->Image('images/musculo/abduc_80_1.jpg', 39, 163, 31, '', 'JPG');
$pdf->Image('images/musculo/abduc_80_2.jpg', 67, 163, 31, '', 'JPG');
$pdf->Image('images/musculo/abduc_80_3.jpg', 95, 163, 31, '', 'JPG');

$pdf->Image('images/musculo/rota_exter_1.jpg', 39, 181, 31, '', 'JPG');
$pdf->Image('images/musculo/rota_exter_2.jpg', 67, 181, 31, '', 'JPG');
$pdf->Image('images/musculo/rota_exter_3.jpg', 95, 181, 31, '', 'JPG');

$pdf->Image('images/musculo/rota_inter_1.jpg', 39, 199, 31, '', 'JPG');
$pdf->Image('images/musculo/rota_inter_2.jpg', 67, 199, 31, '', 'JPG');
$pdf->Image('images/musculo/rota_inter_3.jpg', 95, 199, 31, '', 'JPG');

//28 es el incrementable verticalmente
//18 es el incrementable horizontalmente

switch ($musculo->data[0]->m_musc_flexi_ptos) {
  case '1':
    $px = 47;
    break;
  case '2':
    $px = 47 + (28 * 1);
    break;
  case '3':
    $px = 47 + (28 * 2);
    break;
  case '4':
    $px = 47 + (28 * 3);
    break;
}
$pdf->ImageSVG('images/delete.svg', $px, 60, 15, '', $link = '', '', 'T');

$px = '1';
switch ($musculo->data[0]->m_musc_cadera_ptos) {
  case '1':
    $px = 47;
    break;
  case '2':
    $px = 47 + (28 * 1);
    break;
  case '3':
    $px = 47 + (28 * 2);
    break;
  case '4':
    $px = 47 + (28 * 3);
    break;
}
$pdf->ImageSVG('images/delete.svg', $px, 77, 15, '', $link = '', '', 'T');


$px = '1';
switch ($musculo->data[0]->m_musc_muslo_ptos) {
  case '1':
    $px = 47;
    break;
  case '2':
    $px = 47 + (28 * 1);
    break;
  case '3':
    $px = 47 + (28 * 2);
    break;
  case '4':
    $px = 47 + (28 * 3);
    break;
}
$pdf->ImageSVG('images/delete.svg', $px, 95, 15, '', $link = '', '', 'T');


$px = '1';
switch ($musculo->data[0]->m_musc_abdom_ptos) {
  case '1':
    $px = 47;
    break;
  case '2':
    $px = 47 + (28 * 1);
    break;
  case '3':
    $px = 47 + (28 * 2);
    break;
  case '4':
    $px = 47 + (28 * 3);
    break;
}
$pdf->ImageSVG('images/delete.svg', $px, 113, 15, '', $link = '', '', 'T');

//28 es el incrementable verticalmente
//18 es el incrementable horizontalmente
$px = '1';
switch ($musculo->data[0]->m_musc_abduc_180_ptos) {
  case '1':
    $px = 47;
    break;
  case '2':
    $px = 47 + (28 * 1);
    break;
  case '3':
    $px = 47 + (28 * 2);
    break;
}
$pdf->ImageSVG('images/delete.svg', $px, 142, 15, '', $link = '', '', 'T');


$px = '1';
switch ($musculo->data[0]->m_musc_abduc_80_ptos) {
  case '1':
    $px = 47;
    break;
  case '2':
    $px = 47 + (28 * 1);
    break;
  case '3':
    $px = 47 + (28 * 2);
    break;
}
$pdf->ImageSVG('images/delete.svg', $px, 160, 15, '', $link = '', '', 'T');


$px = '1';
switch ($musculo->data[0]->m_musc_rota_exter_ptos) {
  case '1':
    $px = 47;
    break;
  case '2':
    $px = 47 + (28 * 1);
    break;
  case '3':
    $px = 47 + (28 * 2);
    break;
}
$pdf->ImageSVG('images/delete.svg', $px, 178, 15, '', $link = '', '', 'T');


$px = '1';
switch ($musculo->data[0]->m_musc_rota_inter_ptos) {
  case '1':
    $px = 47;
    break;
  case '2':
    $px = 47 + (28 * 1);
    break;
  case '3':
    $px = 47 + (28 * 2);
    break;
}
$pdf->ImageSVG('images/delete.svg', $px, 196, 15, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'Aptitud de Espalda', 1, 0, 'C', 1);
$pdf->Cell(28, $h, 'Excelente: 1', 1, 0, 'C', 1);
$pdf->Cell(28, $h, 'Promedio: 2', 1, 0, 'C', 1);
$pdf->Cell(28, $h, 'Regular: 3', 1, 0, 'C', 1);
$pdf->Cell(28, $h, 'Pobre: 4', 1, 0, 'C', 1);
$pdf->Cell(10, $h, 'Ptos', 1, 0, 'C', 1);
$pdf->Cell(33, $h, 'Observaciones', 1, 1, 'C', 1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(25, $h * 4, '
FLEXIBILIDAD FUERZA ABDOMEN
', 1, 'C', 0, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(10, $h * 4, $musculo->data[0]->m_musc_flexi_ptos, 1, 0, 'C', 0);

(($musculo->data[0]->m_musc_flexi_obs == 'NO') ? $pdf->Cell(33, $h * 4, $musculo->data[0]->m_musc_flexi_obs, 1, 1, 'C', 0) : $pdf->MultiCell(33, $h * 4, $musculo->data[0]->m_musc_flexi_obs, 1, 'C', 0, 1));


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h * 4, 'CADERA', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(10, $h * 4, $musculo->data[0]->m_musc_cadera_ptos, 1, 0, 'C', 0);

(($musculo->data[0]->m_musc_cadera_obs == 'NO') ? $pdf->Cell(33, $h * 4, $musculo->data[0]->m_musc_cadera_obs, 1, 1, 'C', 0) : $pdf->MultiCell(33, $h * 4, $musculo->data[0]->m_musc_cadera_obs, 1, 'C', 0, 1));


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h * 4, 'MUSLO', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(10, $h * 4, $musculo->data[0]->m_musc_muslo_ptos, 1, 0, 'C', 0);

(($musculo->data[0]->m_musc_muslo_obs == 'NO') ? $pdf->Cell(33, $h * 4, $musculo->data[0]->m_musc_muslo_obs, 1, 1, 'C', 0) : $pdf->MultiCell(33, $h * 4, $musculo->data[0]->m_musc_muslo_obs, 1, 'C', 0, 1));


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(25, $h * 4, '
ABDOMEN ALTERAL
', 1, 'C', 0, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(10, $h * 4, $musculo->data[0]->m_musc_abdom_ptos, 1, 0, 'C', 0);

(($musculo->data[0]->m_musc_abdom_obs == 'NO') ? $pdf->Cell(33, $h * 4, $musculo->data[0]->m_musc_abdom_obs, 1, 1, 'C', 0) : $pdf->MultiCell(33, $h * 4, $musculo->data[0]->m_musc_abdom_obs, 1, 'C', 0, 1));

$suma01 = $musculo->data[0]->m_musc_flexi_ptos + $musculo->data[0]->m_musc_cadera_ptos + $musculo->data[0]->m_musc_muslo_ptos + $musculo->data[0]->m_musc_abdom_ptos;


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, '', 0, 0, 'C', 0);
$pdf->Cell(28, $h, '', 0, 0, 'C', 0);
$pdf->Cell(28, $h, '', 0, 0, 'C', 0);
$pdf->Cell(28, $h, '', 0, 0, 'C', 0);
$pdf->Cell(28, $h, 'TOTAL', 1, 0, 'R', 0);
$pdf->Cell(10, $h, $suma01, 1, 0, 'C', 0);
$pdf->Cell(33, $h, '', 1, 1, '', 0);



$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'Rangos Articulares', 1, 0, 'C', 1);
$pdf->Cell(28, $h, 'Optimo: 1', 1, 0, 'C', 1);
$pdf->Cell(28, $h, 'Limitado: 2', 1, 0, 'C', 1);
$pdf->Cell(28, $h, 'Muy Limitado: 3', 1, 0, 'C', 1);
$pdf->Cell(10, $h, 'Ptos', 1, 0, 'C', 1);
$pdf->Cell(61, $h, 'Dolor Contra Resistenciaa SI / NO', 1, 1, 'C', 1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(25, $h * 4, '
Abduccion de hombro (Normal 0° - 180°)
', 1, 'C', 0, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(10, $h * 4, $musculo->data[0]->m_musc_abduc_180_ptos, 1, 0, 'C', 0);
$pdf->Cell(61, $h * 4, $musculo->data[0]->m_musc_abduc_180_dolor, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(25, $h * 4, '
Aduccion de hombro (Normal 0° - 80°)
', 1, 'C', 0, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(10, $h * 4, $musculo->data[0]->m_musc_abduc_80_ptos, 1, 0, 'C', 0);
$pdf->Cell(61, $h * 4, $musculo->data[0]->m_musc_abduc_80_dolor, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(25, $h * 4, '
Rotacion Externa (Normal 0° - 90°)
', 1, 'C', 0, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(10, $h * 4, $musculo->data[0]->m_musc_rota_exter_ptos, 1, 0, 'C', 0);
$pdf->Cell(61, $h * 4, $musculo->data[0]->m_musc_rota_exter_dolor, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(25, $h * 4, '
Rotacion Interna de hombro
', 1, 'C', 0, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(28, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(10, $h * 4, $musculo->data[0]->m_musc_rota_inter_ptos, 1, 0, 'C', 0);
$pdf->Cell(61, $h * 4, $musculo->data[0]->m_musc_rota_inter_dolor, 1, 1, 'C', 0);

$suma02 = $musculo->data[0]->m_musc_abduc_180_ptos + $musculo->data[0]->m_musc_abduc_80_ptos + $musculo->data[0]->m_musc_rota_exter_ptos + $musculo->data[0]->m_musc_rota_inter_ptos;

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, '', 0, 0, 'C', 0);
$pdf->Cell(28, $h, '', 0, 0, 'C', 0);
$pdf->Cell(28, $h, '', 0, 0, 'C', 0);
$pdf->Cell(28, $h, 'TOTAL', 1, 0, 'R', 0);
$pdf->Cell(10, $h, $suma02, 1, 0, 'C', 0);
$pdf->Cell(61, $h, '', 1, 1, '', 0);


$pdf->Ln($salto);

$pdf->Cell(90, $h, 'OBSERVACIONES', 1, 1, 'L', 1);
$pdf->MultiCell(180, $h, $musculo->data[0]->m_musc_ra_obs, 1, 'L', 0, 1);


$pdf->Ln($salto);

$pdf->Cell(180, $h, 'Segun la EVALUACION DE CAPACIDAD FISICA, el Médico que suscribe CERTIFICA que el trabajador:', 1, 1, 'L', 1);


$cert01 = '';
$cert02 = '';
switch ($musculo->data[0]->m_musc_aptitud) {
  case 'No tiene limitaciones funcionales':
    $cert01 = 'X';
    $cert02 = '';
    break;
  case 'Tiene limitaciones funcionales':
    $cert01 = '';
    $cert02 = 'X';
    break;
}

$pdf->Cell(180, $h * 4, '', 'LRT', 0, 'L', 0);
$pdf->Ln(3);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(5, $h, $cert01, 1, 0, 'C', 0);
$pdf->Cell(90, $h, 'No tiene limitaciones funcionales', 0, 1, 'L', 0);

$pdf->Ln(3);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(5, $h, $cert02, 1, 0, 'C', 0);
$pdf->Cell(90, $h, 'Tiene limitaciones funcionales', 0, 1, 'L', 0);


if ($musculo->data[0]->medico_firma == '1') {
  $pdf->Image('images/firma/'.$musculo->data[0]->medico_cmp.'.jpg', 140, 235, 50, '', 'JPG');
}



























$pdf->AddPage('P', 'A4');
//OPTIMA
// $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
//$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
// $pdf->ImageSVG('images/logo_pdf.svg', 10, 7, 46, '', $link = '', '', 'T');

//CLINICA O2
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');
// $pdf->Image('images/formato/contactos_o2.jpg', 148, 5, 50, '', 'JPEG');
$h = 4.5;
$titulo = 7;
$texto = 7;
$salto = 2;

$pdf->Ln(6);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(0, 0, 'FORMATO', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 0, 'EVALUACIÓN OSTEOMUSCULAR', 0, 1, 'C');
$pdf->Ln(1);
// $pdf->Ln(3);




$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(155, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, 'Nro HR: ' . ($_REQUEST['adm']), 1, 1, 'C', 0);


$pdf->Cell(180, $h, 'COLUMNA VERTEBRAL', 1, 1, 'C', 1);

$pdf->Cell(36, ($h * 3) + 1, 'EVALUACION ESTATICA', 1, 0, 'C', 1);
$pdf->Cell(18 * 3, ($h * 3) + 1, 'DESVIACIONES DEL EJE LATERAL', 1, 0, 'C', 1);
$pdf->Cell(18 * 3, ($h * 3) + 1, 'DESVIA. DEL EJE ANTERO POSTERIOR', 1, 0, 'C', 1);
$pdf->Cell(18 * 2, $h, 'PALPACIONES', 1, 1, 'C', 1);

$pdf->Cell(36, ($h * 2) + 1, '', 0, 0, 'C', 0);
$pdf->Cell(18 * 3, ($h * 2) + 1, '', 0, 0, 'C', 0);
$pdf->Cell(18 * 3, ($h * 2) + 1, '', 0, 0, 'C', 0);
$pdf->MultiCell(18, ($h * 2) + 1, 'Apófisis espinosa dolorosa', 1, 'C', 1, 0);
$pdf->MultiCell(18, ($h * 2) + 1, 'Contractura muscular', 1, 'C', 1, 1);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'COLUMNA VERTEBRAL', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18 * 3, $h, $musculo->data[0]->m_musc_col_cevical_desvia_lateral, 1, 0, 'C', 0);
$pdf->Cell(18 * 3, $h, $musculo->data[0]->m_musc_col_cevical_desvia_antero, 1, 0, 'C', 0);
$pdf->MultiCell(18, $h, $musculo->data[0]->m_musc_col_cevical_palpa_apofisis, 1, 'C', 0, 0);
$pdf->MultiCell(18, $h, $musculo->data[0]->m_musc_col_cevical_palpa_contractura, 1, 'C', 0, 1);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'COLUMNA DORSAL', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18 * 3, $h, $musculo->data[0]->m_musc_col_dorsal_desvia_lateral, 1, 0, 'C', 0);
$pdf->Cell(18 * 3, $h, $musculo->data[0]->m_musc_col_dorsal_desvia_antero, 1, 0, 'C', 0);
$pdf->MultiCell(18, $h, $musculo->data[0]->m_musc_col_dorsal_palpa_apofisis, 1, 'C', 0, 0);
$pdf->MultiCell(18, $h, $musculo->data[0]->m_musc_col_dorsal_palpa_contractura, 1, 'C', 0, 1);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'COLUMNA LUMBAR', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18 * 3, $h, $musculo->data[0]->m_musc_col_lumbar_desvia_lateral, 1, 0, 'C', 0);
$pdf->Cell(18 * 3, $h, $musculo->data[0]->m_musc_col_lumbar_desvia_antero, 1, 0, 'C', 0);
$pdf->MultiCell(18, $h, $musculo->data[0]->m_musc_col_lumbar_palpa_apofisis, 1, 'C', 0, 0);
$pdf->MultiCell(18, $h, $musculo->data[0]->m_musc_col_lumbar_palpa_contractura, 1, 'C', 0, 1);




$pdf->Ln($salto);




$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, ($h * 2) + 3, 'EVALUACION DINAMICA', 1, 0, 'C', 1);
$pdf->Cell(144, $h, 'MOVILIDAD - DOLOR', 1, 1, 'C', 1);

$pdf->Cell(36, $h, '', 0, 0, 'L', 0);
$pdf->Cell(18, $h + 3, 'FLEXION', 1, 0, 'C', 1);
$pdf->Cell(18, $h + 3, 'EXTENCION', 1, 0, 'C', 1);
$pdf->MultiCell(18, $h + 3, 'LATERALIZ. IZQUIERDA', 1, 'C', 1, 0);
$pdf->MultiCell(18, $h + 3, 'LATERALIZ. DERECHA', 1, 'C', 1, 0);
$pdf->MultiCell(18, $h + 3, 'ROTACION IZQUIERDA', 1, 'C', 1, 0);
$pdf->MultiCell(18, $h + 3, 'ROTACION DERECHA', 1, 'C', 1, 0);
$pdf->Cell(18, $h + 3, 'IRRITACION', 1, 0, 'C', 1);
$pdf->MultiCell(18, $h + 3, 'ALT. MASA MUSCULAR', 1, 'C', 1, 1);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'COLUMNA VERTEBRAL', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_cevical_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_cevical_exten, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_cevical_lat_izq, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_cevical_lat_der, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_cevical_rota_izq, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_cevical_rota_der, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_cevical_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_cevical_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'COLUMNA DORSAL', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_dorsal_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_dorsal_exten, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_dorsal_lat_izq, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_dorsal_lat_der, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_dorsal_rota_izq, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_dorsal_rota_der, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_dorsal_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_dorsal_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'COLUMNA LUMBAR', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_lumbar_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_lumbar_exten, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_lumbar_lat_izq, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_lumbar_lat_der, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_lumbar_rota_izq, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_lumbar_rota_der, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_lumbar_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_col_lumbar_alt_masa, 1, 1, 'C', 0);




$pdf->Ln($salto);




$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(36, ($h * 2) + 3, '
EVALUACION DINAMICA
DE ARTICULACIONES', 1, 'C', 1, 0);
$pdf->Cell(144, $h, 'MOVILIDAD - DOLOR', 1, 1, 'C', 1);

$pdf->Cell(36, $h, '', 0, 0, 'L', 0);
$pdf->Cell(18, $h + 3, 'ABDUCCION', 1, 0, 'C', 1);
$pdf->Cell(18, $h + 3, 'ADUCCION', 1, 0, 'C', 1);
$pdf->Cell(18, $h + 3, 'FLEXION', 1, 0, 'C', 1);
$pdf->Cell(18, $h + 3, 'EXTENCION', 1, 0, 'C', 1);
$pdf->MultiCell(18, $h + 3, 'ROTACION INTERNA', 1, 'C', 1, 0);
$pdf->MultiCell(18, $h + 3, 'ROTACION EXTERNA', 1, 'C', 1, 0);
$pdf->Cell(18, $h + 3, 'IRRITACION', 1, 0, 'C', 1);
$pdf->MultiCell(18, $h + 3, 'ALT. MASA MUSCULAR', 1, 'C', 1, 1);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'HOMBRO - DERECHO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_der_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_der_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_der_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_der_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_der_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_der_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_der_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_der_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'HOMBRO - IZQUIERDO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_izq_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_izq_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_izq_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_izq_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_izq_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_izq_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_izq_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_hombro_izq_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'CODO - DERECHO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_der_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_der_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_der_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_der_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_der_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_der_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_der_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_der_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'CODO - IZQUIERDO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_izq_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_izq_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_izq_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_izq_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_izq_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_izq_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_izq_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_codo_izq_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'MUÑECA - DERECHO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_der_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_der_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_der_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_der_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_der_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_der_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_der_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_der_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'MUÑECA - IZQUIERDO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_izq_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_izq_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_izq_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_izq_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_izq_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_izq_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_izq_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_muneca_izq_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'MANOS Y MUÑECA - DERE.', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_der_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_der_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_der_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_der_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_der_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_der_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_der_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_der_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'MANOS Y MUÑECA - IZQ.', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_izq_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_izq_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_izq_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_izq_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_izq_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_izq_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_izq_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_mano_izq_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'CADERA - DERECHO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_der_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_der_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_der_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_der_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_der_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_der_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_der_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_der_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'CADERA - IZQUIERDO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_izq_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_izq_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_izq_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_izq_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_izq_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_izq_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_izq_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_cadera_izq_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'RODILLA - DERECHO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_der_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_der_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_der_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_der_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_der_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_der_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_der_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_der_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'RODILLA - IZQUIERDO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_izq_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_izq_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_izq_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_izq_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_izq_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_izq_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_izq_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_rodilla_izq_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'TOBILLO - DERECHO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_der_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_der_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_der_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_der_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_der_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_der_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_der_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_der_alt_masa, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'TOBILLO - IZQUIERDO', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_izq_abduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_izq_aduccion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_izq_flexion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_izq_extencion, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_izq_rota_exter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_izq_rota_inter, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_izq_irradia, 1, 0, 'C', 0);
$pdf->Cell(18, $h, $musculo->data[0]->m_musc_tobillo_izq_alt_masa, 1, 1, 'C', 0);



$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(100, $h, 'PUNTUACION DE REFERENCIA (SIGNOS Y SINTOMAS)', 1, 0, 'C', 1);
$pdf->Cell(80, $h, 'DESCRIPCION DE HALLAZGOS', 1, 1, 'C', 1);


$pdf->SetFont('helvetica', '', $titulo);
$cert01 = '';
$cert02 = '';
$cert03 = '';
$cert04 = '';
$cert05 = '';
switch ($musculo->data[0]->m_musc_colum_punto_ref) {
  case 'Grado 0: Ausencia de signos y sintomas':
    $cert01 = 'X';
    break;
  case 'Grado 1: Contractura y/o dolor a la movilizacion':
    $cert02 = 'X';
    break;
  case 'Grado 2: Grado1 mas dolor a la palpacion y/o persuacion':
    $cert03 = 'X';
    break;
  case 'Grado 3: Grado 2 mas limitación funcional evidente clinicamente':
    $cert04 = 'X';
    break;
  case 'Grado 4: Dolor en reposo':
    $cert05 = 'X';
    break;
}

$pdf->Cell(180, $h * 8, '', 1, 0, 'L', 0);

$pdf->Ln(3);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(5, $h, $cert01, 1, 0, 'C', 0);
$pdf->Cell(90, $h, 'Grado 0: Ausencia de signos y sintomas', 0, 0, 'L', 0);
$pdf->MultiCell(80, $h * 7, $musculo->data[0]->m_musc_colum_desc, 'L', 'C', 0, 0);
$pdf->Ln(4.5);

$pdf->Ln(2);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(5, $h, $cert02, 1, 0, 'C', 0);
$pdf->Cell(90, $h, 'Grado 1: Contractura y/o dolor a la movilizacion', 0, 1, 'L', 0);

$pdf->Ln(2);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(5, $h, $cert03, 1, 0, 'C', 0);
$pdf->Cell(90, $h, 'Grado 2: Grado1 mas dolor a la palpacion y/o persuacion', 0, 1, 'L', 0);

$pdf->Ln(2);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(5, $h, $cert04, 1, 0, 'C', 0);
$pdf->Cell(90, $h, 'Grado 3: Grado 2 mas limitación funcional evidente clinicamente', 0, 1, 'L', 0);

$pdf->Ln(2);

$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(5, $h, $cert05, 1, 0, 'C', 0);
$pdf->Cell(90, $h, 'Grado 4: Dolor en reposo', 0, 1, 'L', 0);



$pdf->Ln($salto * 2.2);


$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(180, $h, 'VALORACIÓN', 1, 'C', 1, 1);

$pdf->SetFont('helvetica', 'B', 13);
$pdf->MultiCell(180, $h, $musculo->data[0]->m_musc_colum_aptitud, 1, 'C', 0, 1);


// $pdf->Ln($salto);

// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(100, $h, 'DIAGNOSTICOS', 1, 1, 'C', 1);

// $pdf->SetFont('helvetica', '', $titulo);
// ((strlen($musculo->data[0]->m_musc_diag_01) > 0) ? $pdf->Cell(180, $h, $musculo->data[0]->m_musc_diag_01, 1, 1, 'L', 0) : '');
// ((strlen($musculo->data[0]->m_musc_diag_02) > 0) ? $pdf->Cell(180, $h, $musculo->data[0]->m_musc_diag_02, 1, 1, 'L', 0) : '');
// ((strlen($musculo->data[0]->m_musc_diag_03) > 0) ? $pdf->Cell(180, $h, $musculo->data[0]->m_musc_diag_03, 1, 1, 'L', 0) : '');


// $pdf->Ln($salto);

// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(100, $h, 'CONCLUSIONES', 1, 1, 'C', 1);

// $pdf->SetFont('helvetica', '', $titulo);
// ((strlen($musculo->data[0]->m_musc_conclu_01) > 0) ? $pdf->MultiCell(180, $h, $musculo->data[0]->m_musc_conclu_01, 1, 'L', 0, 1) : '');
// ((strlen($musculo->data[0]->m_musc_conclu_02) > 0) ? $pdf->MultiCell(180, $h, $musculo->data[0]->m_musc_conclu_02, 1, 'L', 0, 1) : '');
// ((strlen($musculo->data[0]->m_musc_conclu_03) > 0) ? $pdf->MultiCell(180, $h, $musculo->data[0]->m_musc_conclu_03, 1, 'L', 0, 1) : '');


$pdf->Ln($salto);


if ($musculo->data[0]->medico_firma == '1') {
  $pdf->Image('images/firma/'.$musculo->data[0]->medico_cmp.'.jpg', 140, 235, 50, '', 'JPG');
}

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(100, $h, 'RECOMENDACIONES' , 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', $titulo);
((strlen($musculo->data[0]->m_musc_recom_01) > 0) ? $pdf->MultiCell(180, $h, $musculo->data[0]->m_musc_recom_01, 1, 'L', 0, 1) : '');
((strlen($musculo->data[0]->m_musc_recom_02) > 0) ? $pdf->MultiCell(180, $h, $musculo->data[0]->m_musc_recom_02, 1, 'L', 0, 1) : '');
((strlen($musculo->data[0]->m_musc_recom_03) > 0) ? $pdf->MultiCell(180, $h, $musculo->data[0]->m_musc_recom_03, 1, 'L', 0, 1) : '');

$pdf->Ln($salto);

// $pdf->Cell(100, $h, '', 0, 0, 'C', 0);
// if ($musculo->data[0]->medico_firma == '1') {
//   $pdf->Cell(80, $h, $pdf->Image('images/firma/' . $musculo->data[0]->medico_cmp . '.jpg', '', '', 50, '', 'JPG'), 0, 0, 'C', 0);
// }


// $pdf->Cell(20, $h, 'OBSERVACIONES', 1, 0, 'L', 0);




//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('musculo_esqueletico_' . $_REQUEST['adm'] . '.PDF', 'I');
