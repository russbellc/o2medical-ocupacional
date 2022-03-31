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
        $this->SetY(-45);
        $this->SetFont('helvetica', 'B', 7);
        $this->Cell(80, 30, '', 0, 0, 'L', 0);
        $this->Cell(30, 35, '', 1, 0, 'L', 0);
        $this->Cell(10, 30, '', 0, 0, 'L', 0);
        $this->MultiCell(80, 30, '
            
1.- Físicos: Ruido, Vibración, Radiación, Calor, etc.
2.- Químicos: Polvo, Metales pesados (plomo y cadmio), etc
3.- Biológicos
4.- Ergonómicos', 0, 'L', 0, 0);
        $this->Cell(80, 30, '', 0, 1, 'L', 0);




        $this->Cell(7, 5, '', 0, 0, 'L', 0);
        $this->MultiCell(70, 5, 'FIRMA, DNI Y HUELLA DIGITAL DEL TRABAJADOR
        DNI:', 'T', 'C', 0, 0);
        $this->Cell(115, 5, '', 0, 0, 'L', 0);
        $this->MultiCell(70, 5, 'FIRMA Y SELLO DEL MÉDICO', 'T', 'C', 0, 0);
        $this->Cell(7, 5, '', 0, 1, 'L', 0);
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

$pdf->SetFillColor(0, 51, 153);
$pdf->SetTextColor(245, 245, 245); //Blanco
$pdf->SetTextColor(0, 0, 0); //negro

/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */
$paciente = $model->paciente($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */

// Añadir página
$pdf->AddPage('L', 'A4');

$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
//$pdf->Image('images/logo.png', 8, 2, 45, '', 'PNG');
$pdf->Ln(2);
$h = 3.5;
$titulo = 7;
$texto = 7;

$pdf->SetFont('helvetica', 'B', 17);
$pdf->Cell(0, 0, 'REGISTRO DE ANTECEDENTES OCUPACIONALES', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 0, 'REG - 04 - E43', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(40, $h + 1, 'APELLIDOS Y NOMBRES:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(140, $h + 1, $paciente->data[0]->nom_ap, 'B', 0, 'L', 0);
$pdf->Cell(10, $h + 1, '', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(10, $h + 1, 'EDAD:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(30, $h + 1, $paciente->data[0]->edad . ' AÑOS', 'B', 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(20, $h + 1, 'EMPRESA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(210, $h + 1, $paciente->data[0]->emp_desc, 'B', 1, 'L', 0);




$pdf->Ln(6);
$pdf->SetTextColor(245, 245, 245); //Blanco
$pdf->SetLineStyle(array('color' => array(245, 245, 245)));
$pdf->SetFont('helvetica', 'B', 5);

$pdf->Cell(30, $h + 1, 'FECHA', 1, 0, 'C', 1);
$pdf->Cell(30, ($h + 1) * 2, 'CARGO O PUESTO DE LABOR', 1, 0, 'C', 1);
$pdf->MultiCell(20, ($h + 1) * 2, '
EMPRESA O CONTRATA', 1, 'C', 1, 0);
$pdf->MultiCell(26, ($h + 1) * 2, '
PROYECTO O EMPRESA DE DESTINO', 1, 'C', 1, 0); //
$pdf->MultiCell(20, ($h + 1) * 2, '
ALTITUD
(msnm)', 1, 'C', 1, 0);
$pdf->Cell(95, $h + 1, 'RIESGOS', 1, 0, 'C', 1);
$pdf->Cell(45, $h + 1, 'EXAMEN DE RETIRO', 1, 1, 'C', 1);


$pdf->Cell(15, $h + 1, 'INICIO', 1, 0, 'C', 1);
$pdf->Cell(15, $h + 1, 'FIN', 1, 0, 'C', 1);
$pdf->Cell(96, $h + 1, '', 0, 0, 'C', 0);

$pdf->SetLineStyle(array('color' => array(0, 0, 0)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(245, 245, 245);
$pdf->Cell(20, $h + 1, 'OBSERVACIONES', 1, 0, 'C', 1);
$pdf->SetFillColor(192, 192, 192);
$pdf->Cell(15, $h + 1, 'FÍSICOS', 1, 0, 'C', 1);
$pdf->SetFillColor(245, 245, 245);
$pdf->Cell(15, $h + 1, 'QUÍMICOS', 1, 0, 'C', 1);
$pdf->SetFillColor(192, 192, 192);
$pdf->Cell(15, $h + 1, 'BIOLÓGICOS', 1, 0, 'C', 1);
$pdf->SetFillColor(245, 245, 245);
$pdf->Cell(15, $h + 1, 'ERGONÓMICOS', 1, 0, 'C', 1);
$pdf->SetFillColor(192, 192, 192);
$pdf->Cell(15, $h + 1, 'OTROS', 1, 0, 'C', 1);

$pdf->SetFillColor(0, 51, 153);
$pdf->SetTextColor(245, 245, 245);
$pdf->SetLineStyle(array('color' => array(245, 245, 245)));


$pdf->Cell(10, $h + 1, 'Fecha', 1, 0, 'C', 1);
$pdf->Cell(15, $h + 1, 'Centro Médico', 1, 0, 'C', 1);
$pdf->Cell(20, $h + 1, 'Describir Hallazgos', 1, 1, 'C', 1);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(245, 245, 245);
$pdf->SetLineStyle(array('color' => array(0, 0, 0)));












$ante_ocupa = $model->antece_7c($_REQUEST['adm']);
foreach ($ante_ocupa->data as $i => $row) {

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
////////////////////////////////INICIO////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

    $pdf->SetFont('helvetica', '', 5);
    $pdf->Cell(15, ($h + 1) * 6, $row->m_antec_fech_ini, 1, 0, 'C', 0);
    $pdf->Cell(15, ($h + 1) * 6, $row->m_antec_fech_fin, 1, 0, 'C', 0);
    $pdf->MultiCell(30, ($h + 1) * 3, '

' . $row->m_antec_cargo, 1, 'C', 0, 0);
    $pdf->MultiCell(20, ($h + 1) * 6, '
    



' . $row->m_antec_empresa, 1, 'C', 0, 0);
    $pdf->MultiCell(26, ($h + 1) * 6, '
    



' . $row->m_antec_proyec, 1, 'C', 0, 0);
////////////////if///////////////////////////
    ($row->m_antec_alti == '< 3000') ? $pdf->SetTextColor(245, 245, 245) : '';
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 5);
    $pdf->Cell(20, ($h + 1) * 1.5, '< 3000', 1, 0, 'C', ($row->m_antec_alti == '< 3000') ? 1 : 0);
    $pdf->SetFont('helvetica', '', 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(245, 245, 245);
//////////////end if//////////////////////////

    $pdf->Cell(20, ($h + 1) * 1.5, 'AGENTE', 1, 0, 'C', 0);

    $pdf->SetFillColor(192, 192, 192);
    $pdf->Cell(15, ($h + 1) * 1.5, ($row->m_antec_fisico == 1) ? 'X' : '', 1, 0, 'C', 1);
    $pdf->Cell(15, ($h + 1) * 1.5, ($row->m_antec_quinico == 1) ? 'X' : '', 1, 0, 'C', 0);
    $pdf->Cell(15, ($h + 1) * 1.5, ($row->m_antec_biologico == 1) ? 'X' : '', 1, 0, 'C', 1);
    $pdf->Cell(15, ($h + 1) * 1.5, ($row->m_antec_ergonom == 1) ? 'X' : '', 1, 0, 'C', 0);
    $pdf->Cell(15, ($h + 1) * 1.5, ($row->m_antec_otros == 1) ? 'X' : '', 1, 0, 'C', 1);


    $pdf->Cell(10, ($h + 1) * 6, $row->m_antec_retiro_date, 1, 0, 'C', 0);
    $pdf->MultiCell(15, ($h + 1) * 6, '
    



' . $row->m_antec_retiro_cmedico, 1, 'C', 0, 0);
    $pdf->MultiCell(20, ($h + 1) * 6, '
    



' . $row->m_antec_retiro_desc, 1, 'C', 0, 0);
    $pdf->Cell(5, ($h + 1) * 1.5, '', 0, 1, 'C', 0);


//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
    $pdf->MultiCell(106, ($h + 1) * 1.5, '', 0, 'C', 0, 0);
////////////////if///////////////////////////
    ($row->m_antec_alti == '3001 - 4000') ? $pdf->SetTextColor(245, 245, 245) : '';
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 5);
    $pdf->Cell(20, ($h + 1) * 1.5, '3001 - 4000', 1, 0, 'C', ($row->m_antec_alti == '3001 - 4000') ? 1 : 0);
    $pdf->SetFont('helvetica', '', 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(245, 245, 245);
//////////////end if//////////////////////////
    $pdf->Cell(20, ($h + 1) * 1.5, 'Hrs. De EXP.', 1, 0, 'C', 0);

    $pdf->SetFillColor(192, 192, 192);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_fisico_hora, 1, 0, 'C', 1);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_quinico_hora, 1, 0, 'C', 0);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_biologico_hora, 1, 0, 'C', 1);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_ergonom_hora, 1, 0, 'C', 0);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_otros_hora, 1, 1, 'C', 1);
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
    $pdf->MultiCell(30, ($h + 1) * 1.5, '', 0, 'C', 0, 0);
//////////////end if//////////////////////////
    $pdf->SetFont('helvetica', 'B', 5);
    ($row->m_antec_suelo == 'SUPERFICIE') ? $pdf->SetTextColor(245, 245, 245) : '';
    $pdf->SetFillColor(0, 0, 0);
    $pdf->Cell(15, ($h + 1) * 3, 'SUPERFICIE', 1, 0, 'C', ($row->m_antec_suelo == 'SUPERFICIE') ? 1 : 0);
    $pdf->SetFont('helvetica', '', 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(245, 245, 245);
//////////////end if//////////////////////////
//////////////end if//////////////////////////
    $pdf->SetFont('helvetica', 'B', 5);
    ($row->m_antec_suelo == 'SUBTERRANEO') ? $pdf->SetTextColor(245, 245, 245) : '';
    $pdf->SetFillColor(0, 0, 0);
    $pdf->Cell(15, ($h + 1) * 3, 'SUBTERRANEO', 1, 0, 'C', ($row->m_antec_suelo == 'SUBTERRANEO') ? 1 : 0);
    $pdf->SetFont('helvetica', '', 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(245, 245, 245);
//////////////end if//////////////////////////
    $pdf->MultiCell(46, ($h + 1) * 1.5, '', 0, 'C', 0, 0);
////////////////if///////////////////////////
    ($row->m_antec_alti == '4001 - 4500') ? $pdf->SetTextColor(245, 245, 245) : '';
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 5);
    $pdf->Cell(20, ($h + 1) * 1.5, '4001 - 4500', 1, 0, 'C', ($row->m_antec_alti == '4001 - 4500') ? 1 : 0);
    $pdf->SetFont('helvetica', '', 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(245, 245, 245);
//////////////end if//////////////////////////
    $pdf->Cell(20, ($h + 1) * 1.5, '% USO DE EPP', 1, 0, 'C', 0);

    $pdf->SetFillColor(192, 192, 192);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_fisico_uso, 1, 0, 'C', 1);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_quinico_uso, 1, 0, 'C', 0);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_biologico_uso, 1, 0, 'C', 1);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_ergonom_uso, 1, 0, 'C', 0);
    $pdf->Cell(15, ($h + 1) * 1.5, $row->m_antec_otros_uso, 1, 1, 'C', 1);
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
    $pdf->MultiCell(106, ($h + 1) * 1.5, '', 0, 'C', 0, 0);
////////////////if///////////////////////////
    ($row->m_antec_alti == '> 4500') ? $pdf->SetTextColor(245, 245, 245) : '';
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 5);
    $pdf->Cell(20, ($h + 1) * 1.5, '> 4500', 1, 0, 'C', ($row->m_antec_alti == '> 4500') ? 1 : 0);
    $pdf->SetFont('helvetica', '', 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(245, 245, 245);
//////////////end if//////////////////////////

    $pdf->Cell(20, ($h + 1) * 1.5, 'ESPECIFICAR:', 1, 0, 'C', 0);
    $pdf->Cell(75, ($h + 1) * 1.5, $row->m_antec_obser, 1, 1, 'L', 0);
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
////////////////////////////////FIN///////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
}








$pdf->Output('Formato_AntecedentesO_' . $_REQUEST['adm'] . '.PDF', 'I');
