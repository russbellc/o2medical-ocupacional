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
$sexo = $paciente->data[0]->sexo;
$carga_examenes = $model->carga_examenes($_REQUEST['adm'], $sexo);
$hemograma = $model->carga_hemograma_pdf($_REQUEST['adm']);
$exa_orina = $model->carga_exa_orina_pdf($_REQUEST['adm']);
$p_lipido = $model->carga_p_lipido_pdf($_REQUEST['adm']);
$exa_drogas = $model->carga_exa_drogas_pdf($_REQUEST['adm']);
//$anexo16 = $model->mod_medicina_anexo16($_REQUEST['adm']);
//$med_16a = $model->mod_medicina_16a($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */

// 

$pdf->AddPage('P', 'A4');

//$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
// $pdf->ImageSVG('images/logo_pdf.svg', 10, 7, 46, '', $link = '', '', 'T');
//CLINICA O2
$pdf->Image('images/formato/logo_o2.jpg', 10, 5, 55, '', 'JPEG');
// 
$pdf->Image('images/firma/030663.jpg', 140, 235, 50, '', 'JPG');


$h = 3.5;
$titulo = 7;
$texto = 7;
$salto = 2;

$pdf->SetFont('helvetica', 'B', 13);
$pdf->Ln(6);
$pdf->Cell(180, $h, 'RESULTADOS DE LABORATORIO', 0, 1, 'C', 0);
//$pdf->Cell(180, $h, 'EVALUACION MEDICA PERFIL VISITA A 4000 m.s.n.m.', 0, 1, 'C', 0);
$pdf->Ln(7);


$pdf->SetFont('helvetica', 'B', $titulo);
//$pdf->Cell(80, $h, 'DATOS PERSONALES', 0, 1, 'L', 0);

$pdf->Cell(90, $h, 'DATOS GENERALES', 1, 1, 'L', 1);

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
$pdf->Cell(70, $h, 'EXAMENES', 1, 0, 'C', 0);
$pdf->Cell(45, $h, 'RESULTADOS', 1, 0, 'C', 0);
$pdf->Cell(25, $h, 'UNIDAD', 1, 0, 'C', 0);
$pdf->Cell(40, $h, 'RANGO DE REFERENCIA', 1, 1, 'C', 0);

$j = 1;


foreach ($carga_examenes->data as $i => $row) {
    if ($row->ex_id == 21) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(180, $h, $row->ex_desc, 1, 1, 'L', 1);

        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(170, $h, 'MUESTRA DE SANGRE:', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- HEMOGLOBINA', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_hemoglobina, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_hemoglobina, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_hemoglobina, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- HEMATOCRITO', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_hematocrito, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_hematocrito, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_hematocrito, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- HEMATIES', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_hematies, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_hematies, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_hematies, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- PLAQUETAS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_plaquetas, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_plaquetas, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_plaquetas, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- LEUCOCITOS TOTALES', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_leucocitos, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_leucocitos, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_leucocitos, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(170, $h, 'FORMULA LEUCOCITARIA:', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- MONOCITOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_monocitos, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_monocitos, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_monocitos, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- LINFOCITOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_linfocitos, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_linfocitos, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_linfocitos, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- EOSINAFILOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_eosinofilos, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_eosinofilos, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_eosinofilos, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- ABASTONADOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_abastonados, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_abastonados, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_abastonados, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- BASOFILOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_basofilos, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_basofilos, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_basofilos, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- NEUTROFILOS SEGMENTADOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $hemograma->data[0]->m_lab_hemo_neutrofilos, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $hemograma->data[0]->m_hemo_unid_neutrofilos, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $hemograma->data[0]->m_hemo_rango_neutrofilos, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(10, $h, '', 'LB', 0, 'L', 0);
        $pdf->Cell(60, $h, 'OBSERVACIONES', 'B', 0, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(110, $h, $hemograma->data[0]->m_lab_hemo_obs, 'RB', 1, 'L', 0);
    } else if ($row->ex_id == 37) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(180, $h, $row->ex_desc, 1, 1, 'L', 1);

        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(170, $h, 'MACROSCOPICO:', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- COLOR', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_color, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- ASPECTO', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_aspecto, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(170, $h, 'EXAMEN QUÍMICO:', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- PH', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_ph, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, '', 0, 0, 'C', 0);
        $pdf->Cell(40, $h, '4.5 - 8.0', 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- DENSIDAD', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_densidad, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, '', 0, 0, 'C', 0);
        $pdf->Cell(40, $h, '1.002 - 1.035', 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- GLUCOSA EN ORINA', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_glucosa, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- UROBILINÓGENO', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_urobilino, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- PROTEINAS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_proteinas, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- NITRITOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_nitritos, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- BILIRRUBINA', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_bilirrubina, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- HEMOGLOBINA', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_hemoglobina, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- ACIDO ASCORBICO', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_acido_ascorbi, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- ESTERASA LEUCOSITARIA', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_esterasa_leuco, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- CUERPOS CETÓNICOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_cuerpo_certoni, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(170, $h, 'SEDIMENTO URINARIO:', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- LEUCOCITOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_leucocitos . ' x campo', 0, 0, 'C', 0);
        $pdf->Cell(25, $h, '', 0, 0, 'C', 0);
        $pdf->Cell(40, $h, '< 10 CELULAS x CAMPO', 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- HEMATIES', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_hematies . ' x campo', 0, 0, 'C', 0);
        $pdf->Cell(25, $h, '', 0, 0, 'C', 0);
        $pdf->Cell(40, $h, '1 - 5  x CAMPO', 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- CRISTALES', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_cristales, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- GÉRMENES', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_germenes, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- CÉLULAS EPITELIALES', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_cel_epitelia, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- CILINDROS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_cilindros, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(20, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(50, $h, '- OTROS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $exa_orina->data[0]->m_lab_orina_otros, 0, 0, 'C', 0);
        $pdf->Cell(65, $h, '', 'R', 1, 'L', 0);

        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(10, $h, '', 'LB', 0, 'L', 0);
        $pdf->Cell(60, $h, 'OBSERVACIONES', 'B', 0, 'L', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(110, $h, $exa_orina->data[0]->m_lab_orina_observaciones, 'RB', 1, 'L', 0);
    } else if ($row->ex_id == 56) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(180, $h, 'PERFIL LIPÍDICO', 1, 1, 'L', 1);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(60, $h, 'COLESTEROL HDL', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $p_lipido->data[0]->m_lab_p_lipido_colesterol_hdl, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $p_lipido->data[0]->m_p_lipido_unid_colesterol_hdl, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $p_lipido->data[0]->m_p_lipido_refe_colesterol_hdl, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(60, $h, 'COLESTEROL LDL', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $p_lipido->data[0]->m_lab_p_lipido_colesterol_ldl, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $p_lipido->data[0]->m_p_lipido_unid_colesterol_ldl, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $p_lipido->data[0]->m_p_lipido_refe_colesterol_ldl, 'R', 1, 'C', 0);
        /*
          $pdf->SetFont('helvetica', '', $texto);
          $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
          $pdf->Cell(60, $h, 'COLESTEROL VLDL', 0, 0, 'L', 0);
          $pdf->Cell(45, $h, $p_lipido->data[0]->m_lab_p_lipido_colesterol_vldl, 0, 0, 'C', 0);
          $pdf->Cell(25, $h, $p_lipido->data[0]->m_p_lipido_unid_colesterol_vldl, 0, 0, 'C', 0);
          $pdf->Cell(40, $h, $p_lipido->data[0]->m_p_lipido_refe_colesterol_vldl, 'R', 1, 'C', 0); */

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(60, $h, 'COLESTEROL TOTAL', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $p_lipido->data[0]->m_lab_p_lipido_colesterol_total, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $p_lipido->data[0]->m_p_lipido_unid_colesterol_total, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $p_lipido->data[0]->m_p_lipido_refe_colesterol_total, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(60, $h, 'TRIGLICERIDOS', 0, 0, 'L', 0);
        $pdf->Cell(45, $h, $p_lipido->data[0]->m_lab_p_lipido_trigliceridos, 0, 0, 'C', 0);
        $pdf->Cell(25, $h, $p_lipido->data[0]->m_p_lipido_unid_trigliceridos, 0, 0, 'C', 0);
        $pdf->Cell(40, $h, $p_lipido->data[0]->m_p_lipido_refe_trigliceridos, 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'LB', 0, 'L', 0);
        $pdf->Cell(60, $h, 'RIESGO CORONARIO', 'B', 0, 'LB', 0);
        $pdf->Cell(45, $h, $p_lipido->data[0]->m_lab_p_lipido_riesg_coronario, 'B', 0, 'C', 0);
        $pdf->Cell(25, $h, '', 'B', 0, 'C', 0);
        $pdf->Cell(40, $h, '< 5', 'RB', 1, 'C', 0);
    } else if ($row->ex_id == 48 || $row->ex_id == 62) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(180, $h, 'TOXICOLOGICO 10 PARAMETROS DE DROGAS EN ORINA', 1, 1, 'L', 1);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(40, $h, 'COCAINA:', 0, 0, 'L', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_cocaina, 0, 0, 'L', 0);
        $pdf->Cell(40, $h, 'MARIHUANA:', 0, 0, 'L', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_marihuana, 0, 0, 'L', 0);
        $pdf->Cell(10, $h, '', 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(40, $h, 'BENZODIAZEPINA:', 0, 0, 'L', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_benzodiazepina, 0, 0, 'L', 0);
        $pdf->Cell(40, $h, 'BARBITURICO:', 0, 0, 'L', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_barbiturico, 0, 0, 'L', 0);
        $pdf->Cell(10, $h, '', 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(40, $h, 'ANPHETAMINA:', 0, 0, 'L', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_anphetamina, 0, 0, 'L', 0);
        $pdf->Cell(40, $h, 'METADONA:', 0, 0, 'L', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_metadona, 0, 0, 'L', 0);
        $pdf->Cell(10, $h, '', 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'L', 0, 'L', 0);
        $pdf->Cell(40, $h, 'METHAMPHENTAMINA 1000:', 0, 0, 'L', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_methamphentamina, 0, 0, 'L', 0);
        $pdf->Cell(40, $h, 'MDMA (XTC):', 0, 0, 'L', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_mdma, 0, 0, 'L', 0);
        $pdf->Cell(10, $h, '', 'R', 1, 'C', 0);

        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(10, $h, '', 'LB', 0, 'L', 0);
        $pdf->Cell(40, $h, 'MORPHINA 300:', 'B', 0, 'LB', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_morphina, 'B', 0, 'L', 0);
        $pdf->Cell(40, $h, 'PHECYCLIDINE:', 'B', 0, 'LB', 0);
        $pdf->Cell(40, $h, $exa_drogas->data[0]->m_lab_drogas_10_phecyclidine, 'B', 0, 'L', 0);
        $pdf->Cell(10, $h, '', 'RB', 1, 'C', 0);
    } else {
        $load_examenes = $model->load_examenes($_REQUEST['adm'], $row->ex_id);
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(70, $h, $row->ex_desc, 1, 0, 'L', 1);
        $pdf->SetFont('helvetica', '', $texto);
        $pdf->Cell(45, $h, $load_examenes->data[0]->m_lab_exam_resultado, 1, 0, 'C', 0);
        $pdf->Cell(25, $h, $load_examenes->data[0]->labc_uni, 1, 0, 'C', 0);
        $pdf->Cell(40, $h, $load_examenes->data[0]->labc_valor, 1, 1, 'C', 0);
    }
}

//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('laboratorio_' . $_REQUEST['adm'] . '.PDF', 'I');
