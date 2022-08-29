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

//$pdf->SetFillColor(0, 51, 153);
//$pdf->SetTextColor(245, 245, 245); //Blanco
//$pdf->SetTextColor(0, 0, 0); //negro

/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */
$paciente = $model->paciente($_REQUEST['adm']);
$ekg = $model->mod_cardio_ekg_report($_REQUEST['adm']);

$conclusion = $model->conclusion($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */

$pdf->SetFillColor(194, 217, 241);
// Añadir página
$pdf->AddPage('L', 'A4');

// $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
//$pdf->Image('images/logo.png', 8, 2, 45, '', 'PNG');


$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');




$pdf->Ln(5);
$h = 4.5;
$titulo = 7;
$texto = 8;
$salto = 2;

$pdf->SetFont('helvetica', 'B', 17);
$pdf->Cell(0, 0, 'INFORME ELECTROCARDIOGRAMA', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Ln(7);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(90, $h, 'DATOS GENERALES', 0, 1, 'L', 1);


$pdf->Cell(250, $h * 2, '', 1, 0, 'L', 0);
$pdf->Ln(0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'PACIENTE:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(80, $h, $paciente->data[0]->nom_ap, 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(10, $h, 'SEXO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->sexo, 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(10, $h, 'EDAD:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->edad . ' AÑOS', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(10, $h, 'DNI:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->pac_ndoc, 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'NRO DE HOJA DE RUTA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->adm, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(20, $h, 'EMPRESA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, $paciente->data[0]->emp_desc, 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(15, $h, 'PUESTO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(80, $h, $paciente->data[0]->puesto, 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'TIPO DE FICHA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(35, $h, $paciente->data[0]->tipo, 0, 1, 'L', 0);

$pdf->Ln($salto);

$pdf->Cell(250, $h * 2, '', 1, 0, 'L', 0);
$pdf->Ln(0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(40, $h, 'FRECUENCIA AURICULAR:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $ekg->data[0]->m_car_ekg_frec_auricular . '/min', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(45, $h, 'FRECUENCIA VENTRICULAR:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $ekg->data[0]->m_car_ekg_frec_auricular . '/min', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(15, $h, 'RITMO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $ekg->data[0]->m_car_ekg_ritmo, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'INTERVALO P-R:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(13, $h, $ekg->data[0]->m_car_ekg_intervalo_p_r, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(10, $h, 'QRS:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(13, $h, $ekg->data[0]->m_car_ekg_qrs, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(10, $h, 'Q-T:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $ekg->data[0]->m_car_ekg_q_t, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(8, $h, 'AP:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(13, $h, $ekg->data[0]->m_car_ekg_ap, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(12, $h, 'A, QRS:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(13, $h, $ekg->data[0]->m_car_ekg_a_qrs, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(8, $h, 'A.T:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(13, $h, $ekg->data[0]->m_car_ekg_at, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(15, $h, 'ONDAS P:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $ekg->data[0]->m_car_ekg_onda_p, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'COMPLEJO QRS:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $ekg->data[0]->m_car_ekg_complejos_qrs, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'SEGMENTE S-T:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $ekg->data[0]->m_car_ekg_segmento_s_t, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(18, $h, 'ONDAS T:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $ekg->data[0]->m_car_ekg_onda_t, 0, 1, 'L', 0);

$pdf->Cell(250, $h * 3, '', 1, 0, 'L', 0);
$pdf->Ln(0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(18, $h, 'QUINDINA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(65, $h, $ekg->data[0]->m_car_ekg_quindina, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(18, $h, 'SINTOMAS:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(65, $h, $ekg->data[0]->m_car_ekg_sintomas, 0, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(18, $h, 'HALLAZGOS:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(65, $h, $ekg->data[0]->m_car_ekg_otros_hallazgo, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'ANTECEDENTES:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $ekg->data[0]->m_car_ekg_antecedentes, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'DESCRIPCION:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $ekg->data[0]->m_car_ekg_descripcion, 0, 1, 'L', 0);


$pdf->Cell(250, $h * 1, '', 1, 0, 'L', 0);
$pdf->Ln(0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(25, $h, 'CONCLUSION:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(225, $h, $conclusion->data[0]->conclu2, 0, 1, 'L', 0);


$pdf->Output('formato_ekg_' . $_REQUEST['adm'] . '.PDF', 'I');
