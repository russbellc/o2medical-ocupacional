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
$grupo_fac = $model->rpt_lab_examen($_REQUEST['adm'], 22);
$anexo16 = $model->mod_medicina_anexo16($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');
$h = 5.5;
$titulo = 8;
$texto = 8;
$salto = 2;
$pdf->SetFont('helvetica', 'B', 7);

$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 10);

$pdf->Ln(1);
$pdf->Cell(180, $h, 'SERVICIO MEDICO DE MINERA LAS BAMBAS', 0, 1, 'C', 0);
$pdf->Cell(180, $h, 'DIVISION DE SALUD OCUPACIONAL', 0, 1, 'C', 0);
$pdf->Cell(180, $h, 'REG-05-E43', 0, 1, 'C', 0);
$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(180, $h * 2, 'INFORME MEDICO OCUPACIONAL', 0, 1, 'C', 0);

//$pdf->Ln();

$pdf->Ln($salto);


$pdf->Cell(180, $h * 7, '', 1, 0, 'C', 0);
$pdf->Ln(0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'TIPO DE EXAMEN:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, 'TIPO DE EXAMEN:', 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'PACIENTE:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->nom_ap, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'AREA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->adm_area, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'PUESTO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->adm_puesto, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'EMPRESA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->emp_desc, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, $paciente->data[0]->documento . ':', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $paciente->data[0]->pac_ndoc, 0, 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'GRUPO SANGUINEO:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(100, $h, $grupo_fac->data[0]->m_lab_exam_resultado, 0, 1, 'L', 0);


$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'DIAGNOSTICOS OCUPACIONALES:', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(180, $h * 9, '', 1, 0, 'C', 0);
$pdf->Ln(0);
$pdf->Cell(90, $h * 9, '', 0, 0, 'L', 0);
$pdf->Cell(90, $h * 9, '', 0, 1, 'L', 0);

$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'DIAGNOSTICOS CLINICOS:', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(180, $h * 9, '', 1, 0, 'C', 0);
$pdf->Ln(0);
$pdf->Cell(90, $h * 9, '', 0, 0, 'L', 0);
$pdf->Cell(90, $h * 9, '', 0, 1, 'L', 0);


$pdf->Ln($salto);
$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo + 3);
$pdf->Cell(180, $h * 2, $anexo16->data[0]->m_med_aptitud, 0, 1, 'C', 1);

$pdf->Ln($salto);
$pdf->Ln($salto);



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(100, $h, 'FECHA ACTUAL DE EMISION DEL INFORME:' . $paciente->data[0]->fech_reg, 0, 1, 'L', 0);


$pdf->Ln($salto);
$pdf->Ln($salto);
$pdf->Ln($salto);
$pdf->Ln($salto);
$pdf->Ln($salto);
$pdf->Ln($salto);
$pdf->Ln($salto);
$pdf->Ln($salto);
$pdf->Ln($salto);
$pdf->Ln($salto);
$pdf->Ln($salto);

$pdf->Ln($salto);






//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('TRAB_ALTURA_180_' . $_REQUEST['adm'] . '.PDF', 'I');
