<?php

//require_once('../extras/fpdi/fpdf.php');
require_once('../extras/fpdi/fpdi.php');

//require'../extras/fpdi/fpdf.php';
//require'../extras/fpdi/fpdi.php';

class MYPDF extends FPDI {

    public $user;

    public function Header() {
        $this->setJPEGQuality(100);
//        $this->Image('images/macsa-firma.png', 15, 7, 50, '', 'PNG');
//        $this->Image('images/macsa-firma.png', 80, 7, 50, '', 'PNG');
//        $this->Image('images/logo.png', 10, 5, 50, '', 'PNG');
        $this->SetY(10);
        $this->SetFont('helvetica', 1, 9);
        $this->SetX(55);
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

// set document information
// Información referente al PDF
$pdf->SetCreator("MEDICOS ESPECIALISTAS CUSCO S.A.C.");
$pdf->SetAuthor('MIKAIL RUSSBELL BY.');
$pdf->SetTitle('CONSTANCIA DE SALUD OCUPACIONAL');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// Contenido de la cabecera
// Fuente de la cabecera y el pie de página
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Márgenes
$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT, 2);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Establecer la fuente
$pdf->SetFont('helvetica', 1, 16);

// Añadir página
$pdf->AddPage('P', 'A4');

//procesos de llamado al model
$paciente = $model->inf_paciente($_REQUEST['adm']);
$examenes = $model->examenes($paciente->data[0]->adm);
$validacion = $model->validacion($paciente->data[0]->adm);
$antecedentes = $model->antecedentes($paciente->data[0]->adm);
$triaje = $model->triaje($paciente->data[0]->adm);
$medicina = $model->medicina($paciente->data[0]->adm);
$oftalmologia = $model->oftalmologia($paciente->data[0]->adm);

$rayosx = $model->rayosx($paciente->data[0]->adm);
//inicio de el diseño de la pagina
$pdf->setJPEGQuality(100);

$pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
$pdf->SetFont('helvetica', 'BU', 15);
$pdf->Cell(0, 0, 'HOJA DE EXAMEN MEDICO OCUPACIONAL', 0, 1, 'C');
$pdf->Ln(5);
$f = 0;
$h = 6;
$w = 40;
$w2 = 50;
$w3 = 8;
$texh = 8;
$texh2 = 7;
$ali = 'L';
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w2, $h, 'DATOS GENERALES', 1, 1);
$pdf->Cell(0, 7 * $h, '', 1);
$pdf->ln(0);
$pdf->Cell($w, $h, 'EMPRESA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ':' . $paciente->data[0]->emp_desc, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);

$pdf->Cell($w, $h, 'ACTIVIDAD A REALIZAR ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(75, $h, ': ' . $paciente->data[0]->adm_act, $f, 0);
$cod = $pdf->zerofill($this->user->con_sedid, 4);
$cod.=$pdf->zerofill(': ' . $paciente->data[0]->adm, 7);
//$code = ;
$pdf->SetFont('helvetica', '', 'C');
$pdf->Cell(0, $h, $pdf->write1DBarcode($cod, 'C39', '', '', 60, 10, 0.4), $f, 1, 'R');

$pdf->SetFont('helvetica', 'B', $texh);

//
$pdf->Cell($w, $h, 'APELLIDOS ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(75, $h, ': ' . $paciente->data[0]->apellidos, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'NOMBRE ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(75, $h, ': ' . $paciente->data[0]->pac_nombres, $f, 0);
$pdf->SetFont('helvetica', 'B', $texh);

$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(5, $h, 'N° H.R.', $f, 0, $ali);
$pdf->Cell(52, $h, $pdf->zerofill($paciente->data[0]->adm, 10), $f, 0, 'R');
$pdf->ln($h);

$pdf->SetFont('helvetica', 'B', $texh);

$pdf->Cell($w, $h, 'TIPO DE FICHA', $f, 0, $ali);

$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $paciente->data[0]->tfi_desc, $f, 0);
//
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'CELULAR', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2); //,pac_fono,pac_cel
$pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_cel, $f, 1);
$pdf->SetFont('helvetica', 'B', $texh);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, $paciente->data[0]->tdoc_desc, $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_ndoc, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);

$pdf->Cell($w, $h, 'FECHA DE ADMISIÓN ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $paciente->data[0]->fecha, $f, 1);
//
$pdf->SetFont('helvetica', 'B', $texh);
//
$pdf->Cell($w, $h, 'SEXO', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell($w2, $h, ': ' . $paciente->data[0]->sexo, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'EDAD', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $paciente->data[0]->edad . ' Años.', $f, 1);


$w2 = 60;
$h = 9;
$pdf->Cell($w2, 3, "", 0, 1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w2, 5, "SERVICIOS", 1, 0, 'C');
$pdf->Cell($w2, 5, "FIRMA", 1, 0, 'C');
$pdf->Cell(8, 5, "", 0, 0, 'C');
$pdf->Cell(45, 55, 'FOTO', 1, 0, 'C');

if ($paciente->data[0]->pac_fono == '') {
    
} else {
//    $img = str_replace('data:image/png;base64,', '', $img);
//    $img = str_replace(' ', '+', $img);
//    $pdf->Cell(95, $h, $paciente->data[0]->pac_fono, 'B', 0, 'L');
    $data = base64_decode($paciente->data[0]->pac_fono);
    $pdf->Image('@' . $data, 144, 83, 43, '');
}

$pdf->Ln(5);
$j = 0;
foreach ($examenes->data as $i => $row) {
    if ($row->ar_id < 5) {
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell($w2, $h, $i + 1 . ".-" . $row->ex_desc, 1, 0);
        $pdf->Cell($w2, $h, "", 1, 0);
        $pdf->Cell(7, 5, "", 0, 0, 'C');
        $pdf->Ln(9);
    } elseif ($row->ar_id == 5) {
        $j+=1;
        $pdf->SetFont('helvetica', 'B', 8);
        $j == 1 ? $pdf->Cell($w2, 3, 'LABORATORIO', 1, 1) : "";
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell($w2, 3, $j . ".-" . $row->ex_desc, 1, 0);
        $pdf->Cell($w2, 3, "(  )", 1, 1, 'C');
    }
}

$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$pdf->setVisibility('all');
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////CERTIFICADO MEDICO//////////////////CERTIFICADO MEDICO///////////////////////////////////////////////////////////////////////
////////////////////////////////////////////CERTIFICADO MEDICO//////////////////CERTIFICADO MEDICO///////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$pdf->AddPage('P', 'A4');

$pdf->setJPEGQuality(100);
$pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
$pdf->SetFont('helvetica', 'B', 19);
$pdf->Cell(180, 15, 'CERTIFICADO DE APTITUD MÉDICO OCUPACIONAL', 0, 2, 'C');
$f = 0;
$h = 5;
$w = 30;
$w2 = 60;
$w3 = 8;
$texh = 8;
$texh2 = 7;
$ali = 'L';
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(35, $h, 'APELLIDOS:', 0, 0, 'L');
$pdf->Cell(95, $h, $paciente->data[0]->apellidos, 'B', 0, 'L');
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Cell(45, $h * 11, 'FOTO', 1, 0, 'C');

if ($paciente->data[0]->pac_fono == '') {
    
} else {
    $data = base64_decode($paciente->data[0]->pac_fono);
    $pdf->Image('@' . $data, 150, 40, 45, '');
}
$pdf->Ln(12);
$pdf->Cell(35, $h, 'NOMBRES:', 0, 0, 'L');
$pdf->Cell(95, $h, $paciente->data[0]->pac_nombres, 'B', 0, 'L');
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Ln(12);
$pdf->Cell(15, $h, 'EDAD:', 0, 0, 'L');
$pdf->Cell(24, $h, $paciente->data[0]->edad . ' AÑOS', 'B', 0, 'C');
$pdf->Cell(6, $h, '', 0, 0, 'C');
$pdf->Cell(15, $h, 'SEXO:', 0, 0, 'L');
$pdf->Cell(29, $h, $paciente->data[0]->sexo, 'B', 0, 'C');
$pdf->Cell(6, $h, '', 0, 0, 'C');
$pdf->Cell(10, $h, 'DNI:', 0, 0, 'L');
$pdf->Cell(25, $h, $paciente->data[0]->pac_ndoc, 'B', 0, 'C');
$pdf->Cell(2, $h, '', 0, 0, 'C');
$pdf->Ln(12); //GRUPO SANGUINEO Y FACTOR RH:
$pdf->Cell(85, $h, 'GRUPO SANGUINEO Y FACTOR RH:', 0, 0, 'L');
$pdf->Cell(45, $h, $validacion->data[0]->sangre, 'B', 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Ln(12);
$pdf->Cell(35, $h, 'ALERGIAS:', 0, 0, 'L');
$pdf->Cell(95, $h, $antecedentes->data[0]->alergias, 'B', 0, 'L');
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Ln(12);
$pdf->Cell(35, $h, 'EMPRESA:', 0, 0, 'L');
$pdf->Cell(145, $h, $paciente->data[0]->emp_desc, 'B', 0, 'L');
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Ln(12);
$pdf->Cell(50, $h, 'PUESTO DE TRABAJO:', 0, 0, 'L');
$pdf->Cell(130, $h, $paciente->data[0]->adm_act, 'B', 0, 'L');
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Ln(12);
$pdf->Cell(50, $h, 'TIPO DE EXAMEN:', 0, 0, 'L');
$pdf->Cell(130, $h, $paciente->data[0]->tfi_desc, 'B', 0, 'L');
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Ln(12);

$pdf->Cell(180, $h, 'RESTRICCIONES:', 1, 1, 'L');
$pdf->MultiCell(180, '', $validacion->data[0]->val_restri, 1, 'L', 0, 1);
$pdf->Ln(5);
$pdf->Cell(180, $h, 'OBSERVACIONES:', 1, 1, 'L');
$pdf->MultiCell(180, '', $validacion->data[0]->val_obser, 1, 'L', 0, 1);

$pdf->Ln(5);
$pdf->Cell(20, $h, '', 0, 0, 'C');
$pdf->Cell(140, $h, 'VIGENCIA DE APTITUD MEDICA', 1, 0, 'C');
$pdf->Cell(20, $h, '', 0, 1, 'C');
$pdf->Ln(5);
$pdf->Cell(50, $h, 'FECHA DE EXAMEN:', 0, 0, 'L');
$pdf->Cell(30, $h, $validacion->data[0]->ini, 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Cell(60, $h, 'FECHA DE CADUCIDAD:', 0, 0, 'L');
$pdf->Cell(30, $h, $validacion->data[0]->fin, 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'C');
$pdf->Ln(11);
$pdf->Cell(20, $h, '', 0, 0, 'C');
$pdf->Cell(140, $h, 'CONCLUSIÓN DE APTITUD MÉDICA', 1, 0, 'C');
$pdf->Cell(20, $h, '', 0, 1, 'C');
$pdf->Ln(3);
$pdf->Cell(20, $h, '', 0, 0, 'C');
$pdf->SetFont('helvetica', '', 14);
$pdf->Cell(140, $h, $validacion->data[0]->val_aptitu, 1, 0, 'C');
$pdf->Cell(20, $h, '', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 9);
$pdf->Ln(3);



//$pdf->Image('images/wiman.png', '', '', 55, '', 'PNG','C')
$pdf->Image('images/wiman.png', 35, 225, 55, '', 'PNG', 'C');
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(75, 30, '', '', 0, 'C');
$pdf->Cell(30, 30, '', '', 0, 'C');
$pdf->Cell(75, 30, '', 'B', 1, 'C');

$pdf->Cell(75, 1, '', 0, 0, 'C');
$pdf->Cell(30, 1, '', '', 0, 'C');
$pdf->Cell(75, 1, 'FIRMA Y SELLO DEL MEDICO QUE CERTIFICA', 0, 1, 'C');


//$pdf->Cell(45, 1, '', 0, 0, 'C');
//$pdf->Cell(90, 1, 'FIRMA Y SELLO DEL MEDICO QUE CERTIFICA', 0, 0, 'C');
//$pdf->Cell(45, 1, '', 0, 1, 'C');
//$pdf->Cell(45, $h, '', 0, 0, 'C');
////$pdf->Cell(90, $h, '', 0, 0, 'C');
//$pdf->Cell(45, $h, '', 0, 1, 'C');

$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$pdf->setVisibility('all');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////CERTIFICADO MEDICO//////////////////CERTIFICADO MEDICO///////////////////////////////////////////////////////////////////////
////////////////////////////////////////////CERTIFICADO MEDICO//////////////////CERTIFICADO MEDICO///////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$pdf->AddPage('P', 'A4');

$pdf->setJPEGQuality(100);
$pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
$pdf->SetFont('helvetica', 'B', 15);
$pdf->Cell(180, 15, 'REPORTE DE EXAMEN MEDICO OCUPACIONAL', 0, 2, 'C');
//$pdf->Ln(5);
$f = 0;
$h = 4;
$w = 30;
$w2 = 60;
$w3 = 8;
$texh = 8;
$texh2 = 7;
$ali = 'L';


$pdf->SetFont('helvetica', 'B', $texh);

$pdf->SetFillColor(194, 217, 241);
$pdf->Cell(90, $h, 'DATOS GENERALES', 0, 1, 'L', 1);
$pdf->Cell(0, 4 * $h, '', 1);
$pdf->ln(0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'NOMBRES ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(85, $h, ': ' . $paciente->data[0]->nombre, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'TIPO DE FICHA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 - 10, $h, ': ' . $paciente->data[0]->tfi_desc, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'DNI ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(85, $h, ': ' . $paciente->data[0]->pac_ndoc, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'NRO DE FICHA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 - 10, $h, ': ' . $paciente->data[0]->adm, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'EMPRESA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(85, $h, ': ' . $paciente->data[0]->emp_desc, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'FECHA EXAMEN ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 - 10, $h, ': ' . $paciente->data[0]->fecha, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'EDAD ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(85, $h, ': ' . $paciente->data[0]->edad . ' Años.', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'ACTIVIDAD ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 - 10, $h, ': ' . strtoupper($paciente->data[0]->adm_act), $f, 1);

$pdf->Ln(2);

$pdf->SetFont('helvetica', 'B', $texh);

$pdf->SetFillColor(194, 217, 241);
$pdf->Cell(90, $h, 'DATOS PERSONALES', 0, 1, 'L', 1);
$pdf->Cell(0, 3 * $h, '', 1);
$pdf->ln(0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(50, $h, 'FECHA Y LUGAR DE NACIMIENTO', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(40, $h, ': ' . $paciente->data[0]->fech_nac . ' ' . $paciente->data[0]->dep_desc, $f, 0); //

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'CELULAR', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(25, $h, ': ' . $paciente->data[0]->pac_cel, $f, 0); //

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'SEXO', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(35, $h, ': ' . $paciente->data[0]->sexo, $f, 1); //

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, 'DOMICILIO ACTUAL', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(160, $h, ': ' . $paciente->data[0]->actual . ' - ' . $paciente->data[0]->prov . ' - ' . $paciente->data[0]->dist . '    DIRECCIÓN:  ' . $paciente->data[0]->pac_domdir, $f, 1); //

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, 'ESTADO CIVIL', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(60, $h, ': ' . $paciente->data[0]->ec_desc, $f, 0); //

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'GRADO INSTRUCCION', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(50, $h, ': ' . $paciente->data[0]->gi_desc, $f, 1); //ec_desc, gi_desc,

$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'ANTECEDENTE OCUPACIONAL', 0, 1, 'L', 1);
$f = 1;

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w + 5, $h, 'Empresa ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 - 5, $h, ': ' . strtoupper($antecedentes->data[0]->empr), $f, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Area ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . strtoupper($antecedentes->data[0]->area), $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w + 5, $h, 'Exposición Ocupacional: ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 - 5, $h, ': ' . strtoupper($antecedentes->data[0]->expo), $f, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Ocupación ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . strtoupper($antecedentes->data[0]->ocupacion), $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w + 5, $h, 'Tipo de Trabajo ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 - 5, $h, ': ' . strtoupper($antecedentes->data[0]->antece_tocu), $f, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Tiempo ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2); //antece_tota
$pdf->Cell($w2, $h, ': ' . $antecedentes->data[0]->antece_tota, $f, 1);

$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', $texh);
$st = $pac->antece_impo == 1 ? "COM" : "SIN";
$pdf->Cell(90, $h, "ANTECENTES: $st IMPORTANCIA PATOLOGICA ", 0, 1, 'L', 1);

$w = 155 / 5;
$w2 = 25 / 5;
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'HTA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_hta, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'DM', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_dm, $f, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Prob CV ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_prob, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'HTg ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_htg, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'H.col ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_hcol, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Pt Columna ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_hta, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Migraña', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_dm, $f, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Artropatia', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_prob, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'HBP ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_htg, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Asma', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_hcol, $f, 1);

$f = 1;
//if (!empty($paciente->data[0]->alergias)) {
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Alergias ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->MultiCell(0, $h, $antecedentes->data[0]->alergias, $f, 'L', 0, 1);
//}
//if (!empty($paciente->data[0]->antece_alerg)) {
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Qx', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->MultiCell(0, $h, strtoupper($antecedentes->data[0]->qx), $f, 'L', 0, 1);
//}
$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "ANTECEDENTES FAMILIARES", 0, 1, 'L', 1);

$w = 40;
$w2 = 50;
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Estado del padre', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, strtoupper($antecedentes->data[0]->padre), $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Estado de la madre ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, strtoupper($antecedentes->data[0]->madre), $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Cuantos Hermanos?', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, strtoupper($antecedentes->data[0]->antece_herma), $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Tiene hijos?', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, strtoupper($antecedentes->data[0]->antece_hijos), $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Nro Hijos Vivos', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, strtoupper($antecedentes->data[0]->antece_vivos), $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Nro Hijos Fallecidos', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, strtoupper($antecedentes->data[0]->antece_muert), $f, 1);

$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "ENFERMEDADES Y ACCIDENTES", 0, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Enfermedad o accidente', $f, 0, $ali);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell($w2 + 40, $h, strtoupper($antecedentes->data[0]->accidentes), $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Asociado al Trabajo ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(10, $h, strtoupper($antecedentes->data[0]->asoccia), $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Año de accidente', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 + 40, $h, strtoupper($antecedentes->data[0]->años), $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Dias de descanso', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(10, $h, strtoupper($antecedentes->data[0]->descrip), $f, 1);

$w = 20;
$w2 = 40;
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Tabaco', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_tabaco, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Alcohol', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_alcoho, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Drogas', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $antecedentes->data[0]->antece_drogas, $f, 1);

$pdf->Ln(3);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(120, $h, 'SIGNOS VITALES', 0, 0, 'C', 1);

$pdf->Cell(0, $h, "FUNCIONES VITALES", 'L', 1, 'C', 1);
$w = 30;
$w2 = 30;
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'TALLA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_talla . 'm', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'CAP. VITAL', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->esp_vital . ' cc', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'PRE. ARTERIAL.', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_pa, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'PESO', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_peso . ' Kg', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'SATURACIÓN', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_satura . '%', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'FRE. CARDIACA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_fc . ' x min.', $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'IMC', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_img . ' Kg/m²', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'TEMPERARURA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_temp . ' °C', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'F. RESPIRATORIA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_fr . ' x min.', $f, 1);

$pdf->Ln(2);
$w = 45;
$w2 = 45;

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Perimetro Toraxico', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_ptorax . ' cms.', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Maxima Inspiracion', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_inspira . ' cms.', $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Perimetro Abdominal', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_abdom . ' cms.', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'Expiracion Forzada', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $triaje->data[0]->tri_espira . ' cms.', $f, 1);
$pdf->Ln(2);

$w = 60;
$w2 = 60;

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "MEDICINA OCUPACIONAL", 0, 1, 'L', 1);
$ali = 'C';
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(55, $h, 'BOCA, AMIGDALAS, FARINGE, LARINGE', $f, 0, $ali);
$pdf->Cell(75, $h, 'CUELLO', $f, 0, $ali);
$pdf->Cell(50, $h, 'NARIZ', $f, 1, $ali);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(55, $h * 3, $medicina->data[0]->med_boca_obs, $f, 'L', 0, 0);
$pdf->MultiCell(75, $h * 3, $medicina->data[0]->med_cuello_obs, $f, 'L', 0, 0);
$pdf->MultiCell(50, $h * 3, $medicina->data[0]->med_nariz_obs, $f, 'L', 0, 1);
$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "ODONTOLOGIA", 0, 1, 'L', 1);
$ali = 'L';
$w = 45;
$w2 = 45;
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'PZAS. MAL ESTADO(caries)', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(50, $h, $paciente->data[0]->caries, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'PZAS. PARA EXTRAER', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(50, $h, $paciente->data[0]->extraer, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'PZAS. QUE FALTAN', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(20, $h, $paciente->data[0]->ausente, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(25, $h, 'OBSERVACION', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(95, $h, $paciente->data[0]->odo_descrip, $f, 1);

$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "OFTALMOLOGIA", 0, 1, 'L', 1);


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, '', $f, 0, $ali);
$pdf->Cell(30, $h, 'SIN CORREGIR', $f, 0);
$pdf->Cell(30, $h, 'CORREGIDO', $f, 0);
$pdf->Cell(0, $h, 'ENFERMEDADES OCULARES', $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, '', $f, 0, $ali);
$pdf->Cell(15, $h, 'OI', $f, 0);
$pdf->Cell(15, $h, 'OD', $f, 0);
$pdf->Cell(15, $h, 'OI', $f, 0);
$pdf->Cell(15, $h, 'OD', $f, 0);
$pdf->Cell(0, $h, ' ', $f, 1);
/////////////////////////////////////////////////////
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_slejos_izq, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_slejos_der, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_clejos_izq, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_clejos_der, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_elejos_izq, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_elejos_der, $f, 1, 'C');
//        $pdf->SetFont('helvetica', 'B', $texh);
//        $pdf->Cell($w, $h, 'VISION DE CERCA', $f, 0, 'C');
//        $pdf->SetFont('helvetica', '', $texh);
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_scerca_izq, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_scerca_der, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_ccerca_izq, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_ccerca_der, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_ecerca_izq, $f, 0, 'C');
//        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_ecerca_der, $f, 1, 'C');
/////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'VISION DE LEJOS', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_slejos_izq, $f, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_slejos_der, $f, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_clejos_izq, $f, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_clejos_der, $f, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(0, $h, 'Dx: ' . $oftalmologia->data[0]->ofta_cie1, $f, 1); //


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'VISION DE CERCA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_scerca_izq, $f, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_scerca_der, $f, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_ccerca_izq, $f, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_ccerca_der, $f, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'REFLEJOS PUPILARES', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(40, $h, $oftalmologia->data[0]->ofta_refl, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'VISION DE COLORES', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(0, $h, $oftalmologia->data[0]->ofta_colo, $f, 1);


$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(140, $h, 'PRUEBA DE ESFUERZO (IMC >35 / MAYORES DE 50 AÑOS): ' . $paciente->data[0]->p_esfuerzo, 0, 1, 'L', 1); //
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'ORINA', $f, 0, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(50, $h, $paciente->data[0]->lab2_ori_resu, $f, 0, 'L');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'GLUCOSA', $f, 0, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(50, $h, $validacion->data[0]->gluco . ' mg/dl', $f, 1, 'L');

$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'EXAMENES AUXILIARES COMPLEMENTARIOS', 0, 1, 'L', 1);
$pdf->Cell(100, $h, 'ESPIROMETRIA ', $f, 0, 'L');
$pdf->Cell(40, $h, 'SENSOMETRICO', $f, 0, 'L');
$pdf->Cell(40, $h, 'PSICOLOGICO', $f, 1, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(100, $h, $validacion->data[0]->esp_diag, $f, 0, 'L');
$pdf->Cell(40, $h, $validacion->data[0]->senso, $f, 0, 'L');

if ($validacion->data[0]->psicologico == 'Si') {
    $psico = $validacion->data[0]->psicologico . ' APTO';
} else if ($validacion->data[0]->psicologico == 'No') {
    $psico = $validacion->data[0]->psicologico . ' APTO';
} else {
    $psico = '';
}

$pdf->Cell(40, $h, $psico, $f, 1, 'L');

$pdf->Ln();

$pdf->AddPage('P', 'A4');

$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "OIDOS", 0, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, '', 'LT', 0, $ali);
$pdf->Cell(70, $h, 'DERECHO NORMAL', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_od, $f, 0, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(70, $h, 'IZQUIERDO NORMAL', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_oi, $f, 1, 'C');

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, '', 'L', 0, $ali);
$pdf->Cell(70, $h, 'TRIANGULO DE LUZ', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_od_tri, $f, 0, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(70, $h, 'TRIANGULO DE LUZ', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_oi_tri, $f, 1, 'C');


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, 'TIMPANOS', 'L', 0, $ali);

$pdf->Cell(70, $h, 'PERFORACIONES', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_od_perf, $f, 0, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(70, $h, 'PERFORACIONES', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_oi_perf, $f, 1, 'C');

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, '', 'L', 0, $ali);
$pdf->Cell(70, $h, 'ABOMBAMIENTO', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_od_abom, $f, 0, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(70, $h, 'ABOMBAMIENTO', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_oi_abom, $f, 1, 'C');


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, '', 'L', 0, $ali);
$pdf->Cell(70, $h, 'CERUMEN', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_od_ceru, $f, 0, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(70, $h, 'CERUMEN', $f, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(10, $h, $medicina->data[0]->med_oi_ceru, $f, 1, 'C');

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, '', 'OBSERVA.', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(80, '', $medicina->data[0]->med_od_obs, $f, 'L', 0, 0);
$pdf->MultiCell(80, '', $medicina->data[0]->med_oi_obs, $f, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, 'AUDICION', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(80, $h, $medicina->data[0]->med_od_aud, $f, 'L', 0, 0);
$pdf->MultiCell(80, $h, $medicina->data[0]->med_oi_aud, $f, 'L', 0, 1);
$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "TORAX", 0, 1, 'L', 1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->MultiCell(60, $h, 'ECTOSCOPIA', $f, 'L', 0, 0);
$pdf->MultiCell(60, $h, 'CORAZON', $f, 'L', 0, 0);
$pdf->MultiCell(60, $h, 'PULMONES', $f, 'L', 0, 1);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(60, '', $medicina->data[0]->med_ectos, $f, 'L', 0, 0);
$pdf->MultiCell(60, '', $medicina->data[0]->med_coraz, $f, 'L', 0, 0);
$pdf->MultiCell(60, '', $medicina->data[0]->med_pulmon, $f, 'L', 0, 1);
$pdf->Ln(0);
$pdf->SetFont('helvetica', 'B', '7');
$pdf->MultiCell(60, '', $medicina->data[0]->med_ectos_obs, $f, 'L', 0, 0);
$pdf->MultiCell(60, '', $medicina->data[0]->med_coraz_obs, $f, 'L', 0, 0);
$pdf->MultiCell(60, '', $medicina->data[0]->med_pulm_obs, $f, 'L', 0, 1);
$pdf->Ln(4);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "MAMAS", 0, 1, 'L', 1);
$pdf->MultiCell(90, $h, 'DERECHA', $f, 'L', 0, 0);
$pdf->MultiCell(90, $h, 'IZQUIERDA', $f, 'L', 0, 1);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(90, $h, $medicina->data[0]->med_mama_de, $f, 'L', 0, 0);
$pdf->MultiCell(90, $h, $medicina->data[0]->med_mama_iz, $f, 'L', 0, 1);
$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "SISTEMA MIO OSTEOARTICULAR", 0, 1, 'L', 1);
$pdf->MultiCell(40, $h, '', $f, 'L', 0, 0);
$pdf->MultiCell(70, $h, 'DERECHA', $f, 'L', 0, 0);
$pdf->MultiCell(70, $h, 'IZQUIERDA', $f, 'L', 0, 1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h * 4, 'SUPERIORES', $f, 'L', 0, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(70, $h * 4, $medicina->data[0]->med_super_de_obs, $f, 'L', 0, 0);
$pdf->MultiCell(70, $h * 4, $medicina->data[0]->med_super_iz_obs, $f, 'L', 0, 1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h * 4, 'INFERIORES', $f, 'L', 0, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(70, $h * 4, $medicina->data[0]->med_infer_de_obs, $f, 'L', 0, 0);
$pdf->MultiCell(70, $h * 4, $medicina->data[0]->med_infer_iz_obs, $f, 'L', 0, 1);


$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);

$pdf->MultiCell(60, $h, 'REFLEJOS OSTEOTENDINOS', $f, 'L', 0, 0);
$pdf->MultiCell(60, $h, 'MARCHA', $f, 'L', 0, 0);
$pdf->MultiCell(60, $h, 'COLUMNA VERTEBRAL', $f, 'L', 0, 1);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(60, $h * 2, $medicina->data[0]->med_refle_obs, $f, 'L', 0, 0);
$pdf->MultiCell(60, $h * 2, $medicina->data[0]->med_march_obs, $f, 'L', 0, 0);
$pdf->MultiCell(60, $h * 2, $medicina->data[0]->med_colum_obs, $f, 'L', 0, 1);

$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "ABDOMEN", 0, 1, 'L', 1);

$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(90, 4, $medicina->data[0]->med_abdom, $f, 'L', 0, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90 / 4, $h, "PRU Sup.", 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(90 / 4, $h, $medicina->data[0]->med_sup, 1, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90 / 4, $h, "PPLD", 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(90 / 4, $h, $medicina->data[0]->med_d, 1, 1, 'L', 0);

$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(90, $h, $medicina->data[0]->med_abdom_obs, $f, 'L', 0, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90 / 4, $h, "PRU Med.", 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(90 / 4, $h, $medicina->data[0]->med_med, 1, 0, 'L', 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90 / 4, $h, "PPLI", 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(90 / 4, $h, $medicina->data[0]->med_i, 1, 1, 'L', 0);
$pdf->Ln(4);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, "TACTO RECTAL", 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(0, $h, $medicina->data[0]->med_recta . " - " . $medicina->data[0]->med_recta_obs, $f, 'L', 0, 1);

$f = 1;
$w = (180 / 4) - 10;
$w2 = (180 / 4) + 10;

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'ANILLOS INGUINALES', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2); //+
$pdf->Cell($w2, $h, $medicina->data[0]->med_anill . " - " . $medicina->data[0]->med_anill_obs, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'HERNIAS', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $medicina->data[0]->med_herni . " - " . $medicina->data[0]->med_herni_obs, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'VARICES', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $medicina->data[0]->med_varic . " - " . $medicina->data[0]->med_varic_obs, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'GENITALES', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $medicina->data[0]->med_genit . " - " . $medicina->data[0]->med_genit_obs, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'GANGLIOS ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, $medicina->data[0]->med_gangl . " - " . $medicina->data[0]->med_gangl_obs, $f, 1);

$pdf->ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(120, $h, 'LENGUAJE, ATENCION, MEMORIA, ORIENTACION, INTELIGENCIA, AFECTIVIDAD', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(100, $h, $medicina->data[0]->med_neuro . " - " . $medicina->data[0]->med_neuro_obs, 1, 'L', 0, 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(80 / 4, $h, 'LOTEP', $f, 0);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(80 / 4, $h, $medicina->data[0]->med_neuro_lotep, $f, 0, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(80 / 3, $h, 'PUPILAS CIRLA', $f, 0);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(80 / 5, $h, $medicina->data[0]->med_neuro_pupil, $f, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'EVALUACION RADIOLOGICA', 0, 1, 'L', 1);
$pdf->Cell(0, 46, '', 1);
$pdf->ln(0);
$pdf->Cell(140 / 4 + 40, 30 + 4 * $h, '', 1);
$pdf->ln(0);
$pdf->Cell(140 / 2 + 40, 30 + 4 * $h, '', 1);
$pdf->ln(0);
$pdf->Cell(40, 30, $pdf->Image('images/pulmon.png', 18, '', 34, ''), $f, 0, 'C');
$pdf->Cell(140 / 4, $h, '0/0 ', $f, 0, 'C');
$pdf->Cell(140 / 4, $h, '1/0 ', $f, 0, 'C');
$pdf->Cell(140 / 8, $h, '1/1,1/2', $f, 0, 'C');
$pdf->Cell(140 / 8, $h, '2/1,2/2,2/3 ', $f, 0, 'C');
$pdf->Cell(140 / 8, $h, '3/2,3/3,3/+', $f, 0, 'C');
$pdf->Cell(140 / 8, $h, 'A,B,C ', $f, 1, 'C');

$pdf->Cell(40, $h, ' ', 0, 0, 'L');
$pdf->Cell(140 / 4, $h, 'CERO ', $f, 0, 'C');
$pdf->Cell(140 / 4, $h, '1/0 ', $f, 0, 'C');
$pdf->Cell(140 / 8, $h, 'UNO', $f, 0, 'C');
$pdf->Cell(140 / 8, $h, 'DOS ', $f, 0, 'C');
$pdf->Cell(140 / 8, $h, 'TRES', $f, 0, 'C');
$pdf->Cell(140 / 8, $h, 'CUATRO', $f, 1, 'C');

$pdf->Cell(40, $h, ' ', 0, 0, 'L');
$pdf->MultiCell(140 / 4, 30 - $h * 2, 'Sin Neumoconiosis  "NORMAL"', 'LRT', 'C', 0, 0);
$pdf->MultiCell(140 / 4, 30 - $h * 2, 'Imagen Radioografica de Exposicion a Polvo "SOSPECHA"', 'LRT', 'C', 0, 0);
$pdf->Cell(0, 30 - $h * 2, '"CON NEUMOCONIOSIS"', 'LRT', 1, 'C');

$pdf->Cell(20, $h, 'N° RX', $f, 0, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_placa, $f, 1, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, 'FECHA', $f, 0, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_lector, $f, 1, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, 'CALIDAD', $f, 0, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_calid, $f, 1, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, 'SIMBOLO', $f, 0, 'C');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_profu, $f, 1, 'C');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(50, $h, 'EL ESTUDIO REALIZADO MOSTRO', $f, 0, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(130, $h, ': ' . $rayosx->data[0]->rayo_inf_mostro, $f, 1, 'L');
$pdf->Ln(1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'EXAMENES AUXILIARES', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h * 2, 'GRUPO SANGUINEO', $f, 0, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h * 2, $validacion->data[0]->sangre, $f, 0, 'C');


foreach ($examenes->data as $i => $row) {//examenes2
    if ($row->ex_id == 124) {//----------->hemograma completo
        $lab_hemo = $model->lab_hemo($paciente->data[0]->adm);
    } else if ($row->ex_id == 126) {
        $lab_rpr = $model->lab_rpr($paciente->data[0]->adm, $row->ex_id);
    } else if ($row->ex_id == 86) {
        $lab_trigl = $model->lab_trigl($paciente->data[0]->adm, $row->ex_id);
    } else if ($row->ex_id == 97) {
        $lab_hdl = $model->lab_trigl($paciente->data[0]->adm, $row->ex_id);
    } else if ($row->ex_id == 96) {
        $lab_ldl = $model->lab_trigl($paciente->data[0]->adm, $row->ex_id);
    } else if ($row->ex_id == 87) {
        $lab_coles = $model->lab_trigl($paciente->data[0]->adm, $row->ex_id);
    } else if ($row->ex_id == 119) {
        $lab_pregnostico = $model->lab_trigl($paciente->data[0]->adm, $row->ex_id);
    }
}


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->MultiCell(40, $h * 2, 'HEMOGLOBINA  HEMATOCRITO', 1, 'L', 0, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->MultiCell(20, $h * 2, $lab_hemo->data[0]->lab3_hem_hglo_r . ' g/dl           ' . $lab_hemo->data[0]->lab3_hem_htoc_r . ' %', 1, 'C', 0, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->MultiCell(40, $h * 2, 'REACCIONES SEROLOGICAS A LUES', 1, 'L', 0, 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h * 2, $lab_rpr->data[0]->lab1_desc1, $f, 1, 'C');
$pdf->Ln(1);
$pdf->AddPage('P', 'A4');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'SERIE BLANCA', 0, 1, 'L', 1);

$pdf->Cell(180 / 7, $h, 'LEUCOC.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, 'EOSINNFIL.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, 'BASOFIL.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, 'LINFOCIT.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, 'MONOCIT.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, 'NEUTROF.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, 'BASTONES.', $f, 1, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(180 / 7, $h, $lab_hemo->data[0]->lab3_hem_leuc_r . ' ', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, $lab_hemo->data[0]->lab3_hem_eosi_r . ' %', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, $lab_hemo->data[0]->lab3_hem_baso_r . ' %.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, $lab_hemo->data[0]->lab3_hem_linf_r . ' %.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, $lab_hemo->data[0]->lab3_hem_mono_r . ' %.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, $lab_hemo->data[0]->lab3_hem_neut_r . ' %.', $f, 0, 'L');
$pdf->Cell(180 / 7, $h, $lab_hemo->data[0]->lab3_hem_abas_r . ' %.', $f, 1, 'L');
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(180 / 4, $h, 'COLESTEROL TOTAL.', $f, 0, 'L');
$pdf->Cell(180 / 4, $h, 'HDL.', $f, 0, 'L');
$pdf->Cell(180 / 4, $h, 'LDL', $f, 0, 'L');
$pdf->Cell(180 / 4, $h, 'TRIGLICERIDOS', $f, 1, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(180 / 4, $h, $lab_coles->data[0]->lab1_desc1 . ' mg/dl', $f, 0, 'L');
$pdf->Cell(180 / 4, $h, $lab_hdl->data[0]->lab1_desc1 . ' mg/dl', $f, 0, 'L');
$pdf->Cell(180 / 4, $h, $lab_ldl->data[0]->lab1_desc1 . ' mg/dl', $f, 0, 'L');
$pdf->Cell(180 / 4, $h, $lab_trigl->data[0]->lab1_desc1 . ' mg/dl', $f, 1, 'L');

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(10, $h, 'EKG', $f, 0, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(120, $h, strtoupper($validacion->data[0]->elect), $f, 0, 'L');

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, 'SUB UNIDAD BETA', $f, 0, 'L');
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h, $lab_pregnostico->data[0]->lab1_desc1, $f, 1, 'L');

$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'VALORES DE AUDIOMETRIA', 0, 1, 'L', 1);
$pdf->Cell(0, $h, 'EXAMENES AUDIOMETRICO (Registrar la  cifra  de ubicacion en DB(A))', 1, 1, 'C', 0);
$pdf->Cell(20, $h * 3, 'VIA', 1, 0, 'C', 0);
$pdf->Cell(0, $h, 'Frecuencia en Hertz(Htz)', $f, 1, 'C');
$pdf->Cell(20, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, 'OIDO DERECHO', $f, 0, 'C', 0);
$pdf->Cell(80, $h, 'OIDO IZQUIERDO', $f, 1, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80 / 9, $h, '125', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '250', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '500', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '1000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '2000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '3000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '4000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '6000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '8000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '125', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '250', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '500', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '1000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '2000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '3000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '4000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '6000', $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '8000', $f, 1, 'C', 0);

$audio_aerea = $model->audio_aerea($paciente->data[0]->adm);
$audio_osea = $model->audio_osea($paciente->data[0]->adm);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(10, $h, 'dB', 'TLR', 0, 'C', 0);
$pdf->Cell(10, $h, 'AREA', $f, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_125, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_250, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_500, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_1000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_2000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_3000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_4000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_6000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_8000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_125, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_250, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_500, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_1000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_2000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_3000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_4000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_6000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_8000, $f, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(10, $h, '(A)', 'BLR', 0, 'C', 0);
$pdf->Cell(10, $h, 'OSEA', $f, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_125, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_250, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_500, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_1000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_2000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_3000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_4000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_6000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_8000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_125, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_250, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_500, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_1000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_2000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_3000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_4000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_6000, $f, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_8000, $f, 1, 'C', 0);

$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'COMENTARIOS', 0, 1, 'L', 1);

$pdf->Cell(40, $h, 'OIDO DERECHO', $f, 0, 'C', 0); //audio_a_oi_diag,audio_a_od_diag
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(140, $h, $audio_aerea->data[0]->audio_a_od_diag, $f, 1, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'OIDO IZQUIERDO', $f, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(140, $h, $audio_aerea->data[0]->audio_a_oi_diag, $f, 1, 'C', 0);

$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'DESCRIPCION DE NO APTITUD', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texh - 1);
$pdf->MultiCell(0, $h, $validacion->data[0]->val_descri, 1, 'L', 0, 1); //val_interc
$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'DIAGNOSTICOS (Deben consignarse el CIE-10)', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texh - 1);
$pdf->MultiCell(0, $h, $validacion->data[0]->val_diagno, 1, 'L', 0, 1); //val_interc
$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'INTERCONSULTAS Y CONTROLES', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texh - 1);
$pdf->MultiCell(0, $h, $validacion->data[0]->val_interc, 1, 'L', 0, 1); //val_interc
$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'RECOMENDACIONES', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texh - 1);
$pdf->MultiCell(0, $h, $validacion->data[0]->val_recome, 1, 'L', 0, 1);

$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'RESTRICCIONES', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texh - 1);
$pdf->MultiCell(0, $h, $validacion->data[0]->val_restri, 1, 'L', 0, 1); //val_interc

$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'OBSERVACIONES', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texh - 1);
$pdf->MultiCell(0, $h, $validacion->data[0]->val_obser, 1, 'L', 0, 1); //val_interc
$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'APTITUD', 0, 1, 'L', 1);
$pdf->SetFont('helvetica', 'B', $texh + 10);
$pdf->MultiCell(0, $h, $validacion->data[0]->val_aptitu, 1, 'C', 0, 1);

$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(90, $h, 'APTITUD LABORAL', 0, 1, 'L', 1);

$pdf->Cell(40, $h, 'TIEMPO:', $f, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h, $validacion->data[0]->val_tiemp, $f, 0, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'FECHA INICIO:', $f, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h, $validacion->data[0]->ini, $f, 0, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(40, $h, 'FECHA TERMINO:', $f, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(20, $h, $validacion->data[0]->fin, $f, 1, 'C', 0);
//




$pdf->Ln(2);





$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(155, $h, '', 0, 0, 'L', 0);
//$pdf->Cell(145 / 2 - 3, $h, '', 0, 0, 'L', 0);
$pdf->Cell(25, $h, 'Huella Digital', 0, 1, 'C', 1);

$pdf->SetFont('helvetica', '', $texh);
//$pdf->Image('images/wiman.png', 35, 225, 55, '', 'PNG', 'C');
$pdf->Cell(50, $h * 8, '', 1, 0, 'C', 0);
$pdf->Cell(3, $h * 8, '', 0, 0, 'L', 0);
$pdf->Cell(49, $h * 8, $pdf->Image('images/wiman.png', '', '', 45, '', 'PNG'), 1, 0, 'L', 0);
$pdf->Cell(3, $h * 8, '', 0, 0, 'L', 0);
$pdf->Cell(50, $h * 8, '', 1, 0, 'L', 0);
//$pdf->Cell(4, $h * 8, '', 'B', 0, 'L', 0);
$pdf->Cell(25, $h * 8, '', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(50, $h, 'Sello y Firma del Medico', 0, 0, 'C', 0);
$pdf->Cell(3, $h, '', 0, 0, 'L', 0);

$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(49, $h, 'Sello y Firma del Medico Auditor', 0, 0, 'C', 0);
$pdf->Cell(3, $h, '', 0, 0, 'L', 0);

$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(75, $h, $paciente->data[0]->nombre, 0, 1, 'C', 0);
//$pdf->Cell(24, $h, '', 0, 0, 'L', 0);


$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(105, $h, '', 0, 0, 'C', 0);
//$pdf->Cell(10, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, 'DNI: ' . $paciente->data[0]->pac_ndoc, 0, 1, 'C', 0);
//$pdf->Cell(24, $h, '', 0, 0, 'L', 0);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////CERTIFICADO MEDICO//////////////////CERTIFICADO MEDICO///////////////////////////////////////////////////////////////////////
////////////////////////////////////////////CERTIFICADO MEDICO//////////////////CERTIFICADO MEDICO///////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$examenes2 = $model->examenes2($paciente->data[0]->adm);
$j = 0;
foreach ($examenes2->data as $i => $row) {//examenes2
    if ($row->ex_id == 23) {//----------->7D
        $pdf->AddPage('P', 'A4');
        $visita = $model->visita($paciente->data[0]->adm);
        $pdf->setJPEGQuality(100);
        $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->Cell(180, 6, 'EVALUACIÓN MÉDICA PARA ASCENSO A GRANDES ALTITUDES', 0, 1, 'C');
        $pdf->Ln(1);
        $pdf->Cell(180, 6, '(7 - D)', 0, 1, 'C');
        $pdf->Ln(1);

        $f = 0;
        $h = 6;
        $w = 30;
        $w2 = 60;
        $w3 = 8;
        $texh = 8;
        $texh2 = 7;
        $ali = 'L';

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell($w2, $h, 'DATOS GENERALES', 1, 1, '', 1);
        $pdf->Cell(0, 3 * $h, '', 1);
        $pdf->ln(0);

        $pdf->Cell(30, $h, 'Nombres ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell(60, $h, ': ' . $paciente->data[0]->nombre, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Documento ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_ndoc, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Fecha de registro', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->fecha, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Empresa ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->emp_desc, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Sexo', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->sexo, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Actividad a Relizar ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->adm_act, $f, 1);
        $pdf->Ln(2);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell($w, $h, 'Funciones Vitales ', 1, 1, $ali, 1);
        $pdf->Cell(0, 2 * $h, '', 1);
        $pdf->ln(0);

        $w = 23;
        $w2 = 22;
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Frec. Cardica', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $triaje->data[0]->tri_fc, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Peso', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $triaje->data[0]->tri_peso, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Talla', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $triaje->data[0]->tri_talla, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'I.M.C.', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $triaje->data[0]->tri_img, $f, 1);


        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Prec. Arterial  ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $triaje->data[0]->tri_pa, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Frecuencia  ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $triaje->data[0]->tri_fr, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Saturacion', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $triaje->data[0]->tri_satura, $f, 1);
        $pdf->ln(3);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell(90, $h, 'El Paciente a Precentado en los últimos 6 meses', 1, 1, $ali, 1);
        $pdf->Cell(0, 15 * $h, '', 1);
        $pdf->ln(0);
        $w = 90;
        $w2 = 90;

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Anemia, Policitemia, Anemia falciforme ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_anemi, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Cirugia mayor reciente o discapacidad fisica ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_cirug, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Desordenes de la coagulacion, trombosis ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_desor, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Diabetes mellitus, Hipertension arterial ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_diabe, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Dental: Caries multiples o activas, Absceso, Pulpitis ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_denta, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Epilepsia, Desmayos, Episodios de isquemia transitoria ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_epile, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Gestacion ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_gesta, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Obesidad (IMC > a 30kg/m2)', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_obesi, $f, 1);


        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Problemas cardiacos, Anginas, Uso de marcapaso ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_pro_card, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Problemas respiratorios o Edema pulmonar de altura ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_pro_resp, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Retinopatia, Glaucoma ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_retin, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Ulcera peptica o duodemal, Hemorroides sangrantes ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_ulcer, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Otro diagnostico o tratamiento medico Inportante ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_otros, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Infecciones recientes (Especialmente oidos, nariz, faringe) ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_infec, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Alergia (Se detalla abajo)', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $visita->data[0]->visita_alerg, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh2);
        $pdf->Cell(40, $h, 'Detalle de Alergias ', 1, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell(0, $h, ': ' . $visita->data[0]->visita_d_alergi, 1, 1);

        $pdf->SetFont('helvetica', 'B', $texh2);
        $pdf->Cell(40, $h, 'Uso de Medicamentos ', 1, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell(140, $h, ': ' . $visita->data[0]->visita_medic, 1, 1);

        $pdf->Ln(2);
        $pdf->SetFont('helvetica', 'B', $texh2);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell(60, $h, 'Laboratorio', 1, 1, $ali, 1);
        $pdf->Cell(0, 2 * $h, '', 1);
        $pdf->ln(0);
        $w = 30;
        $w2 = 30;

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Hemoglobina ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $rpt->data[0]->hemoglobina, $f, 0);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Glucosa ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $rpt->data[0]->glucosa, $f, 0);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Colesterol ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $rpt->data[0]->colesterol, $f, 1);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Trigliceridos ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $rpt->data[0]->trigliceridos, $f, 0);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w, $h, 'Grupo Sanguineo ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, ': ' . $rpt->data[0]->grupo_sang, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh2);
        $pdf->Cell(40, $h, 'Electrocardiograma', 1, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell(0, $h, ': ' . $rpt->data[0]->elec_diag, 1, 1);
        $pdf->SetFont('helvetica', 'B', $texh2);
        $pdf->Cell(40, $h, 'Observación', 1, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->MultiCell(0, $h, $visita->data[0]->visita_obsr, 1, 'L', 0, 1); //val_interc
        $pdf->Cell(180, $h, $visita->data[0]->VIS_APTO, 1, 1, 'C', 1); //VIS_APTO

        $pdf->Cell(45, 27, '', 0, 0, 'C');
        $pdf->Cell(90, 27, '', 'B', 0, 'C');
        $pdf->Cell(45, 27, '', 0, 1, 'C');
        $pdf->Cell(45, $h, '', 0, 0, 'C');
        $pdf->Cell(90, $h, 'FIRMA Y SELLO DEL MEDICO', 0, 0, 'C');
        $pdf->Cell(45, $h, '', 0, 1, 'C');
        $pdf->Cell(45, $h, '', 0, 0, 'C');
        $pdf->Cell(90, $h, '', 0, 0, 'C');
        $pdf->Cell(45, $h, '', 0, 1, 'C');
        $pdf->setVisibility('screen');
        $pdf->SetAlpha(0.1);
        $pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $pdf->setVisibility('all');
    } elseif ($row->ex_id == 22) {//----->TRAB ALTURA
        $pdf->AddPage('P', 'A4');
        //procesos de llamado al model
        //$paciente = $model->inf_paciente($_REQUEST['adm']);
        $trabajo_altura = $model->trabajo_altura($paciente->data[0]->adm);
        //inicio de el diseño de la pagina
        $pdf->setJPEGQuality(100);
        $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
        $pdf->SetFont('helvetica', 'B', 19);
//        $pdf->Cell(180, 15, $row->ex_desc, 0, 2, 'C');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(180, 9, 'EXAMEN PARA TRABAJOS SOBRE ALTURA FÍSICA > 1.8 MTS', 0, 2, 'C');
        $f = 0;
        $h = 4;
        $w = 30;
        $w2 = 60;
        $w3 = 8;
        $texh = 8;
        $texh2 = 7;
        $ali = 'L';
//$pdf->Ln(4);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell(180, $h, 'DATOS GENERALES', 1, 1, 'C', 1);

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Ln(1);
        $pdf->Cell(40, $h, 'APELLIDOS Y NOMBRES:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(140, $h, $paciente->data[0]->pac_nombres, 'B', 0, 'L');


        $pdf->Ln(4);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(50, $h, 'FECHA Y LUGA DE NACIMIENTO:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(96, $h, $paciente->data[0]->fech_nac . ' - DEPARTAMENTO: ' . $paciente->data[0]->dep_desc, 'B', 0, 'L');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(15, $h, 'EDAD:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(18, $h, $paciente->data[0]->edad, 'B', 0, 'L');

        $pdf->Ln(4);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(20, $h, 'DOMICILIO:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(160, $h, $paciente->data[0]->pac_domdir, 'B', 0, 'L');


        $pdf->Ln(4);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(20, $h, 'EMPRESA:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(160, $h, $paciente->data[0]->emp_desc, 'B', 0, 'L');

        $pdf->Ln(4);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'PUESTO DE TRABAJO:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(135, $h, $paciente->data[0]->adm_act, 'B', 0, 'L');

        $pdf->Ln(6);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell(180, $h, 'SIGNOS VITALES', 1, 1, 'C', 1);

        $pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(15, $h, 'PESO:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $triaje->data[0]->tri_peso . ' Kg', 'B', 0, 'C');
        $pdf->Cell(8, $h, '', 0, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(15, $h, 'TALLA:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $triaje->data[0]->tri_talla . ' m', 'B', 0, 'C');
        $pdf->Cell(7, $h, '', 0, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(10, $h, 'IMC:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(45, $h, $triaje->data[0]->tri_img . ' Kg/m2', 'B', 0, 'C');

        $pdf->Ln(4);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(8, $h, 'PA.:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(30, $h, $triaje->data[0]->tri_pa . ' mmHg', 'B', 0, 'C');
        $pdf->Cell(8, $h, '', 0, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(8, $h, 'FC:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(30, $h, $triaje->data[0]->tri_fc . ' x min', 'B', 0, 'C');
        $pdf->Cell(7, $h, '', 0, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(8, $h, 'FR:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(29, $h, $triaje->data[0]->tri_fr . ' x min', 'B', 0, 'C');
        $pdf->Cell(7, $h, '', 0, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(20, $h, '%SAT O2:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(25, $h, $triaje->data[0]->tri_satura . ' %', 'B', 0, 'C');
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->Ln(6);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell(180, $h, 'ANTECEDENTES', 1, 1, 'C', 1);

        $pdf->Ln(1);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, '', 1, 0, 'L', 1);
        $pdf->SetFont('helvetica', 1, 8);
        $pdf->Cell(20, $h, 'SI', 1, 0, 'C', 1);
        $pdf->Cell(20, $h, 'NO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, '', 1, 0, 'L', 1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(20, $h, 'SI', 1, 0, 'C', 1);
        $pdf->Cell(20, $h, 'NO', 1, 1, 'C', 1);

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Agorafobia', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_algora, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Diabetes No controlada', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_diabet, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Acrofobia', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_acrofo, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Insuficiencia Cardiaca', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_cardia, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Consumo de alcohol', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_alcoho, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Hipertensión Arterial  No Controlada', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_hipert, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Consumo de drogas', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_droga, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Alteraciones Cardiovasculares', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_altera, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Traumatismo Encéfalocraneano', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_encefa, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Ametropía de lejos', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_ametro, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Convulciones / Epilepsia', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_epilep, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Esteropsia Alterada', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_estero, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Vértigo / Mareos', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_mareos, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Asma Bronquial / Patron Obstructivo', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_asma, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Síncope', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_sincop, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Hipoacusia Severa', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_hipoac, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Mioclonías', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_mioclo, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Entrenamiento en primeros axilios', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_auxilo, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Acatisia', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_acatis, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Entrenamiento para trabajo en altura', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_altura, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(45, $h, 'Cefalea / Migraña', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_migran, 1, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(55, $h, 'Enfermedades Psiquiatricas', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, $trabajo_altura->data[0]->altu_psiqui, 1, 1, 'C');

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(30, $h, 'OBSERVACIONES:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(150, $h, $trabajo_altura->data[0]->altu_obs01, 'B', 0, 'L');
        $pdf->Ln(6);

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell(180, $h, 'EXAMEN MÉDICO ESPECÍFICO', 1, 0, 'C', 1);
        $pdf->Ln(6);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, '', 1, 0, 'L', 1);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, 'NORMAL', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(40, $h, 'ANORMAL', 1, 1, 'C', 1);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Tímpanos', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_timpan, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Audición', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_audici, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Sustentación en un pie por 15 segundos', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_pie, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Caminar libre sobre recta de 3 mts (Sin desvio)', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_cami01, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Caminar libre ojos vendados - 3 mts (Sin desvio)', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_cami02, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Caminar libre ojos vendados punta - talon 3 mts (Sin devio)', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_cami03, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Limitacion en fuerza o movilidad de extremidades', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_fuerza, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Diadocoquinesia directa', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_direct, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Adiadocoquinesia cruzada', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_cruzad, 1, 1, 'C');

//$pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(100, $h, 'Nistagmus', 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(80, $h, $trabajo_altura->data[0]->altu_nistag, 1, 1, 'C');

        $pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(30, $h, 'OBSERVACIONES:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(150, $h, $trabajo_altura->data[0]->altu_obs02, 'B', 0, 'L');
        $pdf->Ln(6);


        if ($trabajo_altura->data[0]->cod_desc == 'Si') {
            $pdf->Ln(1);
            $pdf->Cell(20, $h, '', 0, 0, 'C');
            $pdf->Cell(140, $h, 'VIGENCIA DE APTITUDMEDICA', 1, 0, 'C');
            $pdf->Cell(20, $h, '', 0, 1, 'C');
            $pdf->Ln(1);
            $pdf->Cell(30, $h, 'FECHA DE EXAMEN:', 0, 0, 'L');
            $pdf->Cell(50, $h, $validacion->data[0]->ini, 1, 0, 'C');
            $pdf->Cell(5, $h, '', 0, 0, 'C');
            $pdf->Cell(40, $h, 'FECHA DE CADUCIDAD:', 0, 0, 'L');
            $pdf->Cell(50, $h, $validacion->data[0]->fin, 1, 0, 'C');
            $pdf->Cell(5, $h, '', 0, 0, 'C');
        } else {
            
        }
        $pdf->Ln(8);
        $pdf->Cell(20, $h, '', 0, 0, 'C');
        $pdf->Cell(140, $h, 'CONCLUSIÓN DE APTITUD MÉDICA', 1, 0, 'C');
        $pdf->Cell(20, $h, '', 0, 1, 'C');
        $pdf->Ln(3);
        $pdf->Cell(20, $h, '', 0, 0, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(140, $h, $trabajo_altura->data[0]->cod_desc . ' Apto', 1, 0, 'C');
        $pdf->Cell(20, $h, '', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Ln(3);
        $pdf->Cell(45, 30, '', 0, 0, 'C');
        $pdf->Cell(90, 30, '', 'B', 0, 'C');
        $pdf->Cell(45, 30, '', 0, 1, 'C');
        $pdf->Cell(45, $h, '', 0, 0, 'C');
//$pdf->Cell(90, $h, 'Dr. Jessica Maruska Medina Sotomayor CMP: 56947', 0, 0, 'C');
        $pdf->Cell(90, $h, 'FIRMA Y SELLO DEL MEDICO QUE CERTIFICA', 0, 0, 'C');
        $pdf->Cell(45, $h, '', 0, 1, 'C');
        $pdf->Cell(45, $h, '', 0, 0, 'C');
        $pdf->Cell(90, $h, '', 0, 0, 'C');
        $pdf->Cell(45, $h, '', 0, 1, 'C');
    } elseif ($row->ex_id == 24) {//----->MUSCULOESQUELETICO
        $pdf->AddPage('P', 'A4');
        //procesos de llamado al model
        //$paciente = $model->inf_paciente($_REQUEST['adm']);
        $musculo = $model->musculo($paciente->data[0]->adm);
        //inicio de el diseño de la pagina
        $pdf->setJPEGQuality(100);
        $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
        $pdf->SetFont('helvetica', 'BU', 15);
        $pdf->Cell(0, 0, 'REPORTE DE EVALUACION MUSCULOESQUELETICA ', 0, 1, 'C');
        $pdf->Ln(5);
        $f = 0;
        $h = 5;
        $w = 40;
        $w2 = 50;
        $w3 = 8;
        $texh = 8;
        $texh2 = 7;
        $ali = 'L';
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell($w2, $h, 'DATOS GENERALES', 0, 1, 'L', 1);
        $pdf->Cell(0, 5 * $h, '', 1);
        $pdf->ln(0);
        $pdf->Cell($w, $h, 'NOMBRES ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->nombre, $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'DNI ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_ndoc, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'SEXO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->sexo, $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'CELULAR ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_cel, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'EMPRESA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->emp_desc, $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'FECHA DE REGISTRO', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->fecha, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'EDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->edad . ' Años', $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'ACTIVIDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->adm_act, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'PESO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $triaje->data[0]->tri_peso, $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'ESTATURA', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $triaje->data[0]->tri_talla, $f, 1);
        $f = 1;
        $w = 160;
        $w2 = 20;
        $w3 = 60;
        $w4 = 0;
        $pdf->ln(5);
        $pdf->Cell($w4, $h, 'EVALUACION MUSCULOESQUELETICA', 0, 1, 'L', 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->ln(0);

        $demo[] = array('titulo' => '¿Carga Objetos con frecuencia? (levantamiento manual de carga)', 'pre' => '¿Cuantos kilos suele cargar?', 'rpt' => !empty($pac->data[0]->musc_01) ? $musculo->data[0]->musc_01 : 'NO');
        $demo[] = array('titulo' => '¿Realiza labores agachado?', 'pre' => '¿Por cuanto tiempo?', 'rpt' => !empty($musculo->data[0]->musc_02) ? $musculo->data[0]->musc_02 : 'NO');
        $demo[] = array('titulo' => '¿Realiza movimentos repetitivos durante el trabajo?', 'pre' => '¿Por cuanto tiempo? ', 'rpt' => !empty($musculo->data[0]->musc_03) ? $musculo->data[0]->musc_03 : 'NO');
        $demo[] = array('titulo' => '¿Trabaja de pie?', 'pre' => '¿Por cuanto tiempo permanece de pie? ', 'rpt' => !empty($musculo->data[0]->musc_04) ? $musculo->data[0]->musc_04 : 'NO');
        $demo[] = array('titulo' => '¿Trabaja regularmente en una oficina?', 'pre' => '¿Cuanto tiempo permanece sentado?', 'rpt' => !empty($musculo->data[0]->musc_05) ? $musculo->data[0]->musc_05 : 'NO');
        $demo[] = array('titulo' => '¿Siente fatiga al trabajar?', 'pre' => 'no se', 'rpt' => !empty($musculo->data[0]->musc_06) ? $musculo->data[0]->musc_06 : 'NO');
        $demo[] = array('titulo' => '¿Presenta dolor de cabeza al trabajar?', 'pre' => 'Especifique:', 'rpt' => !empty($musculo->data[0]->musc_07) ? $musculo->data[0]->musc_07 : 'NO');
        $demo[] = array('titulo' => '¿Ha presentado dolores musculares o articulares?', 'pre' => 'Especifique:', 'rpt' => !empty($musculo->data[0]->musc_08) ? $musculo->data[0]->musc_08 : 'NO');
        $demo[] = array('titulo' => '¿Estos dolores aparecen durante el turno de trabajo?', 'pre' => 'nada', 'rpt' => !empty($musculo->data[0]->musc_09) ? $musculo->data[0]->musc_09 : 'NO');
        $demo[] = array('titulo' => '¿Sufre regularmente de dolores lumbares?', 'pre' => '¿Con que frecuencia? ', 'rpt' => !empty($musculo->data[0]->musc_10) ? $musculo->data[0]->musc_10 : 'NO');
        $demo[] = array('titulo' => '¿Ha sido hospitalizado por dolor lumbar?', 'pre' => 'Explique:', 'rpt' => !empty($musculo->data[0]->musc_11) ? $musculo->data[0]->musc_11 : 'NO');
        $demo[] = array('titulo' => '¿Ha presentado recientemente alguna enfermedad?	', 'pre' => '	¿Cual?	', 'rpt' => !empty($musculo->data[0]->musc_12) ? $musculo->data[0]->musc_12 : 'NO');
        $demo[] = array('titulo' => '¿Presenta alguna enfermedad cronica?', 'pre' => '¿Cual?', 'rpt' => !empty($musculo->data[0]->musc_13) ? $musculo->data[0]->musc_13 : 'NO');
        $demo[] = array('titulo' => '¿Recibe alguna medicación?', 'pre' => '¿Cual?', 'rpt' => !empty($musculo->data[0]->musc_14) ? $musculo->data[0]->musc_14 : 'NO');
        $demo[] = array('titulo' => '¿Realiza ejercicios fisicos regulares? (mayor igual 3 veces por semana)', 'pre' => 'nada', 'rpt' => !empty($musculo->data[0]->musc_15) ? $musculo->data[0]->musc_15 : 'NO');
        $pdf->SetFillColor(225, 225, 225);
        foreach ($demo as $y => $value) {
            $pdf->SetFont('helvetica', 'B', $texh);
            $pdf->Cell($w, $h, $value['titulo'], $f, 0, $ali, 1);
            $pdf->SetFont('helvetica', '', $texh2);
            $pdf->Cell($w2, $h, $value['rpt'] != 'NO' ? 'SI' : 'NO', $f, 1, 'C', 1);
            if ($value['rpt'] != 'NO' && $value['rpt'] != 1) {
                $pdf->SetFont('helvetica', 'B', $texh);
                $pdf->Cell($w3, $h, $value['pre'], $f, 0, $ali);
                $pdf->SetFont('helvetica', '', $texh2);
                $pdf->Cell($w4, $h, $value['rpt'], $f, 1);
            }
        }
        $pdf->Ln(5);
        $w = 60;
        $w2 = 0;
        $f = 1;
        $pdf->SetFillColor(194, 217, 241);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w4, $h, 'Articulaciones (Pelvis, Columna Vertebral y Extremidades)', 0, 1, 'L', 1);
        $pdf->ln(0);


        $pdf->SetFillColor(225, 225, 225);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Inspección (Deformidades, Desviaciones)', $f, 0, $ali, 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $musculo->data[0]->musc_art_inspe, $f, 1, $ali, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Palpación (Dolor, Fluctuación)', $f, 0, $ali, 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $musculo->data[0]->musc_art_palpa, $f, 1, $ali, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Rango de movilidad', $f, 0, $ali, 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $musculo->data[0]->musc_art_rango, $f, 1, $ali, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Lasegue, Patrick', $f, 0, $ali, 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $musculo->data[0]->musc_art_patri, $f, 1, $ali, 1);

        $pdf->SetFillColor(194, 217, 241);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w4, $h, 'Musculos (Lumbares y de Extremidades)', 0, 1, 'L', 1);
        $pdf->ln(0);

        $pdf->SetFillColor(225, 225, 225);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Inspección (Hipotrofia, Atrofia):', $f, 0, $ali, 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $musculo->data[0]->musc_mus_inspe, $f, 1, $ali, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Palpación (Dolor, Contracturas):', $f, 0, $ali, 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $musculo->data[0]->musc_mus_palpa, $f, 1, $ali, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Tono Muscular:', $f, 0, $ali, 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $musculo->data[0]->musc_mus_tono, $f, 1, $ali, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Fuerza Muscular (0-5):', $f, 0, $ali, 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $musculo->data[0]->musc_mus_fuerz, $f, 1, $ali, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'DIAGNOSTICO:', $f, 0, $ali, 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $musculo->data[0]->musc_hallaz, $f, 1, $ali, 1);

        $pdf->setVisibility('screen');
        $pdf->SetAlpha(0.1);
        $pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
        $pdf->SetAlpha(0.9);
        $pdf->setVisibility('all');
        $pdf->AddPage();
        $pdf->SetFillColor(194, 217, 241);
        $pdf->SetFont('helvetica', 'BU', 15);
        $pdf->Cell($w4, $h, 'EVALUACION MUSCULOESQUELETICA', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w4, $h, 'Aptitud Espalda', 0, 1, 'L', 1);
        $pdf->ln(0);

        $w = 30;
        $f = 1;
        $pdf->SetFillColor(225, 225, 225);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, '', $f, 0, $ali);
        $pdf->Cell($w + 5, $h, 'Excelente: 1', $f, 0, $ali);
        $pdf->Cell($w + 5, $h, 'Promedio: 2', $f, 0, $ali);
        $pdf->Cell($w + 5, $h, 'Regular: 3', $f, 0, $ali);
        $pdf->Cell($w + 5, $h, 'Pobre: 4', $f, 0, $ali);
        $pdf->Cell($w - 20, $h, 'Puntos', $f, 1, $ali, 1);
        $h = 22;
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Flexibilidad/Fuerza:', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/1a.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/1b.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/1c.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/1d.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w - 20, $h, $musculo->data[0]->musc_flex, $f, 1, 'C', 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'CADERA:', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/2a.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/2b.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/2c.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/2d.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w - 20, $h, $musculo->data[0]->musc_cade, $f, 1, 'C', 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'MUSLO:', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/3a.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/3b.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/3c.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/3d.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w - 20, $h, $musculo->data[0]->musc_musl, $f, 1, 'C', 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'ABDOMEN LATERAL:', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/4a.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/4b.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/4c.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/4d.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w - 20, $h, $musculo->data[0]->musc_abdo, $f, 1, 'C', 1);
        $h = 5;

        $pt_1 = $musculo->data[0]->musc_abdo;
        $pt_2 = $musculo->data[0]->musc_cade;
        $pt_3 = $musculo->data[0]->musc_flex;
        $pt_4 = $musculo->data[0]->musc_musl;
        $sumas = $pt_1 + $pt_2 + $pt_3 + $pt_4;
        $h = 5;
        $pdf->Cell(170, 5, 'TOTAL', $f, 0, 'C', 1);
        $pdf->MultiCell(10, 5, $sumas, 1, 'C', 0, 1);

        $pdf->Cell($w, $h * 2, 'OBSERVACIONES', $f, 0, $ali, 1);
        $pdf->MultiCell(0, $h * 2, $musculo->data[0]->musc_esp_obs, 1, 'C', 0, 0);
        $pdf->Ln(15);

        $pdf->Cell($w4, $h, 'Rangos Articulares', 0, 1, 'L', 1);
        $pdf->ln(0);

        $w = 30;
        $f = 1;

        $pdf->SetFillColor(225, 225, 225);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, '', $f, 0, $ali);
        $pdf->Cell($w + 5, $h, 'Optimo: 1', $f, 0, $ali);
        $pdf->Cell($w + 5, $h, 'Limitado: 2', $f, 0, $ali);
        $pdf->Cell($w + 5, $h, 'Muy Limitado: 3', $f, 0, $ali);
        $pdf->Cell($w - 5, $h, 'Puntos', $f, 0, $ali);
        $pdf->Cell($w - 10, $h, 'Dolor/Resisten.', $f, 1, $ali, 1);
        $h = 22;
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->MultiCell($w, $h, "\n\nAbduccion de hombro\n (Normal 0º - 180º):", 1, 'C', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/5a.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/5b.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/5c.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w - 5, $h, $musculo->data[0]->musc_1_pt, $f, 0, 'C');
        $pdf->Cell($w - 10, $h, $musculo->data[0]->musc_1_if == 10 ? 'SI' : 'NO', $f, 1, 'C', 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->MultiCell($w, $h, "\n\nAbduccion de hombro\n(Normal 0º - 80º):", 1, 'C', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/6a.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/6b.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/6c.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w - 5, $h, $musculo->data[0]->musc_2_pt, $f, 0, 'C');
        $pdf->Cell($w - 10, $h, $musculo->data[0]->musc_2_if == 10 ? 'SI' : 'NO', $f, 1, 'C', 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->MultiCell($w, $h, "\n\nRotación externa\n(Normal 0º - 90º):", 1, 'C', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/7a.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/7b.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/7c.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w - 5, $h, $musculo->data[0]->musc_3_pt, $f, 0, 'C');
        $pdf->Cell($w - 10, $h, $musculo->data[0]->musc_3_if == 10 ? 'SI' : 'NO', $f, 1, 'C', 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->MultiCell($w, $h, "\n\nRotación externa de\nhombro interna:", 1, 'C', 0, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/8a.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/8b.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w + 5, $h, $pdf->Image('../modulos/mod_musculo/images/8c.png', '', '', $w, $h - 5), $f, 0, $ali);
        $pdf->Cell($w - 5, $h, $musculo->data[0]->musc_4_pt, $f, 0, 'C');
        $pdf->Cell($w - 10, $h, $musculo->data[0]->musc_4_if == 10 ? 'SI' : 'NO', $f, 1, 'C', 1);
        $pt_1 = $musculo->data[0]->musc_1_pt;
        $pt_2 = $musculo->data[0]->musc_2_pt;
        $pt_3 = $musculo->data[0]->musc_3_pt;
        $pt_4 = $musculo->data[0]->musc_4_pt;
        $suma = $pt_1 + $pt_2 + $pt_3 + $pt_4;
        $h = 5;
        $pdf->Cell(135, 5, 'TOTAL', $f, 0, 'C', 1);
        $pdf->MultiCell(25, 5, $suma, 1, 'C', 0, 0);

        $pdf->MultiCell(20, 5, '', 1, 'C', 0, 1);
        $h = 10;
        $pdf->Cell($w, $h, 'OBSERVACIONES', $f, 0, 'C', 1);
        $pdf->MultiCell(0, $h, $musculo->data[0]->musc_art_obs, 1, 'C', 0, 0);

        $pdf->setVisibility('screen');
        $pdf->SetAlpha(0.1);
        $pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
        $pdf->SetAlpha(0.9);
        $pdf->setVisibility('all');
    } elseif ($row->ex_id == 9) {//------->OFTALMOLOGIA
        $pdf->AddPage('P', 'A4');
        //procesos de llamado al model
        //$paciente = $model->inf_paciente($_REQUEST['adm']);
        //inicio de el diseño de la pagina
        $pdf->setJPEGQuality(100);
        $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
        $pdf->SetFont('helvetica', 'BU', 15);
        $pdf->Cell(0, 0, 'REPORTE DE EVALUACION OFTALMOLOGICA ', 0, 1, 'C');
        $pdf->Ln(5);
        $f = 0;
        $h = 6;
        $w = 40;
        $w2 = 50;
        $w3 = 8;
        $texh = 8;
        $texh2 = 7;
        $ali = 'L';
        $pdf->SetFont('helvetica', 'B', $texh + 2);
//
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell($w2, $h, 'DATOS GENERALES', 0, 1, 'L', 1);
        $pdf->Cell(0, 4 * $h, '', 1);
        $pdf->ln(0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'NOMBRES ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->nombre, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'DNI ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_ndoc, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'SEXO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->sexo, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'CELULAR ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_cel, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'EMPRESA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->emp_desc, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'FECHA DE REGISTRO', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->fecha, $f, 1);
//$pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'EDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->edad . ' Años', $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'ACTIVIDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->adm_act, $f, 1);

        $f = 1;
        $w = 60;
        $pdf->ln(5);
        $pdf->SetFont('helvetica', 'B', $texh + 2);
        $pdf->Cell($w2 + 10, $h, 'EVALUACION OFTALMOLOGICA', 0, 1, 'L', 1);
        $pdf->ln(0);

        $w2 = 0;
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(0, $h, 'ANTECEDENTES OFTALMICOS', $f, 1, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'USO DE CORRECTORES', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $oftalmologia->data[0]->ofta_usa, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'EXPOSICION A COMPUTADORAS', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $oftalmologia->data[0]->ofta_comp, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'OTRO', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $oftalmologia->data[0]->ofta_otro, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'VISION DE COLORES', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $oftalmologia->data[0]->ofta_colo, $f, 1);


        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'FONDO DE OJO', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $oftalmologia->data[0]->ofta_fond, $f, 1);


        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'CAMPIMETRIA POR CONFRONTACION', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $oftalmologia->data[0]->ofta_camp, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'PRESION INTRAOCULAR', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $oftalmologia->data[0]->ofta_anex, $f, 1);
        $w = 60;
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'TOMOMETRIA', $f, 0, 'C');
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell(0, $h, $oftalmologia->data[0]->ofta_tono, $f, 1, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(90, $h, 'OJO IZQUIERDO', $f, 0, 'C');
        $pdf->Cell(90, $h, 'OJO DERECHO', $f, 1, 'C');
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell(90, $h, $oftalmologia->data[0]->ofta_oj_izq, $f, 0, 'C');
        $pdf->Cell(90, $h, $oftalmologia->data[0]->ofta_oj_der, $f, 1, 'C');
        $pdf->Ln(5);


        $w = 90;
        $pdf->SetFont('helvetica', 'B', $texh + 2);
        $pdf->Cell(0, $h, 'MOVILIDAD OCULAR', $f, 1, 'C');
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w / 2, $h, 'EXTRINSECA:', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w / 2, $h, $oftalmologia->data[0]->ofta_extr, $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh2);
        $pdf->Cell($w / 2, $h, 'INTRINSECA', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w / 2, $h, $oftalmologia->data[0]->ofta_intr, $f, 1);

        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', $texh + 2);
        $pdf->Cell(0, $h, 'AGUDEZA VISUAL', $f, 1, 'C');
        $w = 180 / 7;

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h * 2, '', $f, 0, 'C');
        $pdf->Cell($w * 2, $h, 'SIN CORRECION', $f, 0, 'C');
        $pdf->Cell($w * 2, $h, 'CON CORRECION', $f, 0, 'C');
        $pdf->Cell($w * 2, $h, 'CON ESTENOPEICO', $f, 1, 'C');

        $pdf->Cell($w, $h, '', 0, 0, 'C');
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'Ojo Izquierdo', $f, 0, 'C');
        $pdf->Cell($w, $h, 'Ojo Derecho', $f, 0, 'C');
        $pdf->Cell($w, $h, 'Ojo Izquierdo', $f, 0, 'C');
        $pdf->Cell($w, $h, 'Ojo Derecho ', $f, 0, 'C');
        $pdf->Cell($w, $h, 'Ojo Izquierdo', $f, 0, 'C');
        $pdf->Cell($w, $h, 'Ojo Derecho', $f, 1, 'C');

        $pdf->Cell($w, $h, 'VISION DE LEJOS', $f, 0, 'C');
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_slejos_izq, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_slejos_der, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_clejos_izq, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_clejos_der, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_elejos_izq, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_elejos_der, $f, 1, 'C');
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'VISION DE CERCA', $f, 0, 'C');
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_scerca_izq, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_scerca_der, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_ccerca_izq, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_ccerca_der, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_ecerca_izq, $f, 0, 'C');
        $pdf->Cell($w, $h, $oftalmologia->data[0]->ofta_ecerca_der, $f, 1, 'C');

        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(90, $h, 'DIAGNOSTICO', 0, 1, 'L', 1);
        $f = 0;
        $pdf->Cell(10, $h, '1.-', $f, 0, 'C');
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell(0, $h, $oftalmologia->data[0]->ofta_cie1, $f, 1, 'L');

        $pdf->Cell(10, $h, '2.-', $f, 0, 'C');
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell(0, $h, $oftalmologia->data[0]->c2, $f, 1, 'L');

        $pdf->Cell(10, $h, '3.-', $f, 0, 'C');
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->Cell(0, $h, $oftalmologia->data[0]->c3, $f, 1, 'L');
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(90, $h, 'TRATAMIENTO Y RECOMENDACIONES', 0, 1, 'L', 1);
        $pdf->SetFont('helvetica', '', $texh);
        $pdf->MultiCell(0, $h * 2, $oftalmologia->data[0]->ofta_recm, 0, 'L', 0, 0);
    } elseif ($row->ex_id == 15) {//------>PSICOLOGIA
        $pdf->AddPage('P', 'A4');
        //procesos de llamado al model
        //$paciente = $model->inf_paciente($_REQUEST['adm']);
        $psicologia = $model->psicologia($paciente->data[0]->adm);
        //inicio de el diseño de la pagina
        $pdf->setJPEGQuality(100);
        $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');

        $pdf->SetFont('helvetica', 'BU', 15);
        $pdf->Cell(0, 0, 'RESULTADO PSICOLOGICO', 0, 1, 'C');
        $pdf->Ln(5);
        $f = 0;
        $h = 6;
        $w = 40;
        $w2 = 50;
        $w3 = 8;
        $texh = 9;
        $texh2 = 8;
        $ali = 'L';


////$pdf->Cell($w3,$h, "DATOS PERSONALES",1,,'C',1);
        $pdf->SetFont('helvetica', 'B', $texh);
//
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell($w2, $h, 'DATOS GENERALES', 0, 1, 'L', 1);
        $pdf->Cell(0, 4 * $h, '', 1);
        $pdf->ln(0);


        $pdf->Cell($w - 15, $h, 'NOMBRES ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 15, $h, ': ' . $paciente->data[0]->nombre, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'DNI ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_ndoc, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w - 15, $h, 'SEXO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 15, $h, ': ' . $paciente->data[0]->sexo, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'CELULAR ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_cel, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w - 15, $h, 'EMPRESA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 15, $h, ': ' . $paciente->data[0]->emp_desc, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'FECHA DE REGISTRO', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->fecha, $f, 1);
//$pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w - 15, $h, 'EDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 15, $h, ': ' . $paciente->data[0]->edad . ' Años', $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'ACTIVIDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->adm_act, $f, 1);
        $pdf->Ln(5);

        $f = 1;
        $w = 60;
        $w2 = 0;
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, 'OBSERVACIONES Y CONDUCTAS', 0, 1, 'L', 1);
//$pdf->Cell(0, 0 * $h, '', 1);
        $pdf->ln(0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'PRESENTACION ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_pres, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'POSTURA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_post, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h * 3, 'DISCURSO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'RITMO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_ritm, $f, 1);

        $pdf->Cell($w, $h, ' ', 0, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'TONO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_tono, $f, 1);

        $pdf->Cell($w, $h, ' ', 0, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'ARTICULACION ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_arti, $f, 1);

//============================
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h * 3, 'ORIENTACION ', $f, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'TIEMPO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_tiem, $f, 1);

        $pdf->Cell($w, $h, ' ', 0, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'ESPACIO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_espa, $f, 1);

        $pdf->Cell($w, $h, ' ', 0, 0, $ali);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'PERSONA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_pers, $f, 1);

        $pdf->ln(5);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, 'RESULTADOS DE LA EVALUACION', 0, 1, 'L', 1);
        $pdf->ln(0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'NIVEL INTELECTUAL ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_inte, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'COORDINACION VISOMOTRIZ ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_viso, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'NIVEL MEMORIA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_memo, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'PERSONALIDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_prso, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'AFECTIVIDAD', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_afec, $f, 1);

        $pdf->ln(5);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, 'CONCLUSIONES', 0, 1, 'L', 1);
        $pdf->ln(0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'AREA COGNITIVA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_cogn, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'AREA EMOCIONAL', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_emoc, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'AREA DE ORGANICIDAD', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_orga, $f, 1);

        if ($psicologia->data[0]->psico_acro !== 'DESACTIVADO') {
            $pdf->SetFont('helvetica', 'B', $texh);
            $pdf->Cell($w, $h, 'ACROFOBIA(TEMOR A LAS ALTURAS)', $f, 0, $ali);
            $pdf->SetFont('helvetica', '', $texh2);
            $pdf->Cell($w2, $h, $psicologia->data[0]->psico_acro, $f, 1);
        }
        if ($psicologia->data[0]->psico_ries !== 'DESACTIVADO') {
            $pdf->SetFont('helvetica', 'B', $texh);
            $pdf->Cell($w + 10, $h, 'RIESGOS PSICOSOCIALES EN EL TRABAJO', $f, 0, $ali);
            $pdf->SetFont('helvetica', '', $texh2);
            $pdf->Cell($w2 - 10, $h, $psicologia->data[0]->psico_ries, $f, 1);
        }
        if ($psicologia->data[0]->psico_somno !== 'DESACTIVADO') {
            $pdf->SetFont('helvetica', 'B', $texh);
            $pdf->Cell($w + 10, $h, 'SOMNOLENCIA', $f, 0, $ali);
            $pdf->SetFont('helvetica', '', $texh2);
            $pdf->Cell($w2 - 10, $h, $psicologia->data[0]->psico_somno, $f, 1);
        }
        if ($psicologia->data[0]->psico_estr !== 'DESACTIVADO') {
            $pdf->SetFont('helvetica', 'B', $texh);
            $pdf->Cell($w + 10, $h, 'ESTRÉS', $f, 0, $ali);
            $pdf->SetFont('helvetica', '', $texh2);
            $pdf->Cell($w2 - 10, $h, $psicologia->data[0]->psico_estr, $f, 1);
        }//psico_somno, psico_estr
        $pdf->ln(5);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, 'SUGERENCIAS', 0, 1, 'L', 1);
        $pdf->ln(0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'TERAPIA DE APOYO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_tera, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, $psicologia->data[0]->psico_apto, 0, 1, 'C', 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h * 2, 'SUGERENCIAS', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
//$pdf->Cell($w2, $h, $psicologia->data[0]->psico_reco, $f, 1);
        $pdf->MultiCell(0, $h * 2, $psicologia->data[0]->psico_reco, 1, 'L', 0, 0);
    } elseif ($row->ex_id == 14) {//------>ODONTOLOGIA
        $pdf->AddPage('P', 'A4');
        //procesos de llamado al model
        //$paciente = $model->inf_paciente($_REQUEST['adm']);
        $arriba = $model->diente_arriba(); //diag_arriba
        $diag_arriba = $model->diag_arriba($paciente->data[0]->adm);

        $abajo = $model->diente_abajo();
        $diag_abajo = $model->diag_abajo($paciente->data[0]->adm);
        $odonto = $model->odonto($paciente->data[0]->adm);
        //inicio de el diseño de la pagina
        $pdf->setJPEGQuality(100);
        $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
        $pdf->SetFont('helvetica', 'BU', 15);
        $pdf->Cell(0, 0, 'REPORTE DE RESULTADOS ODONTOLOGICOS', 0, 1, 'C');
        $pdf->Ln(5);
        $f = 0;
        $h = 5;
        $w = 40;
        $w2 = 50;
        $w3 = 8;
        $texh = 8;
        $texh2 = 7;
        $ali = 'L';

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell($w2, $h, 'DATOS GENERALES', 0, 1, 'L', 1);
        $pdf->Cell(0, 4 * $h, '', 1);
        $pdf->ln(0);
        $pdf->Cell($w - 25, $h, 'NONBRE ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 25, $h, ': ' . $paciente->data[0]->nombre, $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'DNI ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->pac_ndoc, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w - 25, $h, 'SEXO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 25, $h, ': ' . $paciente->data[0]->sexo, $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'TIPO DE FICHA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->tfi_desc, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w - 25, $h, 'EMPRESA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 25, $h, ': ' . $paciente->data[0]->emp_desc, $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'FECHA DE REGISTRO ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->fecha, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w - 25, $h, 'EDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 25, $h, ': ' . $paciente->data[0]->edad, $f, 0);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'ACTIVIDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->adm_act, $f, 1);
        $pdf->Ln(10);


        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(180, $h, 'ODONTOGRAMA', 0, 1, 'C', 1);
        $pdf->ln(5);

        $h = '37';
        $marco = '';
        $a = 6;

        foreach ($arriba->data as $i => $row) {
            $a = $a + 11;
            $pdf->Cell('11.25', $h, $pdf->Image('images/dientes/' . $row->pieza_nro . '.png', $a, 79, 10, '', 'PNG'), $marco, 0, 'C');
            foreach ($diag_arriba->data as $i => $rows) {
                if ($row->pieza_nro == $rows->dient_diente) {
                    $pdf->Image('images/dientes/a' . $rows->dient_diag . '.png', $a, 79, 10, '', 'PNG');
                    //dient_1, dient_2, dient_3, dient_4, dient_5
                    $pdf->Image('images/dientes/1_' . $rows->dient_1 . '.png', $a, 115, 10, '', 'PNG');
                    $pdf->Image('images/dientes/2_' . $rows->dient_2 . '.png', $a, 115, 10, '', 'PNG');
                    $pdf->Image('images/dientes/3_' . $rows->dient_3 . '.png', $a, 115, 10, '', 'PNG');
                    $pdf->Image('images/dientes/4_' . $rows->dient_4 . '.png', $a, 115, 10, '', 'PNG');
                    $pdf->Image('images/dientes/5_' . $rows->dient_5 . '.png', $a, 115, 10, '', 'PNG');
                } else {
                    $pdf->Image('images/dientes/a.png', $a, 79, 10, '', 'PNG');
                }
            }
            $pdf->Image('images/dientes/00000.png', $a, 115, 10, '', 'PNG');
            $pdf->Cell(5, 5, '', '', 0, 'C');
        }

        $a = 6;
        $pdf->Cell(180, 50, '', 0, 1, 'C', 1);
        foreach ($abajo->data as $i => $row) {
            $a = $a + 11;
            $pdf->Cell('11.25', $h, $pdf->Image('images/dientes/' . $row->pieza_nro . '.png', $a, 132, 10, '', 'PNG'), $marco, 0, 'C');
            foreach ($diag_abajo->data as $i => $rows) {
                if ($row->pieza_nro == $rows->dient_diente) {
                    $pdf->Image('images/dientes/b' . $rows->dient_diag . '.png', $a, 132, 10, '', 'PNG');
                    //dient_1, dient_2, dient_3, dient_4, dient_5
                    $pdf->Image('images/dientes/1_' . $rows->dient_1 . '.png', $a, 169, 10, '', 'PNG');
                    $pdf->Image('images/dientes/2_' . $rows->dient_2 . '.png', $a, 169, 10, '', 'PNG');
                    $pdf->Image('images/dientes/3_' . $rows->dient_3 . '.png', $a, 169, 10, '', 'PNG');
                    $pdf->Image('images/dientes/4_' . $rows->dient_4 . '.png', $a, 169, 10, '', 'PNG');
                    $pdf->Image('images/dientes/5_' . $rows->dient_5 . '.png', $a, 169, 10, '', 'PNG');
                } else {
                    $pdf->Image('images/dientes/b.png', $a, 132, 10, '', 'PNG');
                }
            }
            $pdf->Image('images/dientes/00000.png', $a, 169, 10, '', 'PNG');
            $pdf->Cell(5, 5, '', '', 0, 'C');
        }
        $pdf->Cell(180, 50, '', 0, 1, 'C', 1);

        $f = 0;
        $h = 5;
        $w = 40;
        $w2 = 50;
        $w3 = 8;
        $texh = 8;
        $texh2 = 7;
        $ali = 'L';
//
        $f = 1;
        $w = 60;
        $w2 = 0;

        $pdf->ln(5);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, 'DIAGNOSTICO', 0, 1, 'C', 1);
        $pdf->ln(0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, 'Caries Dental', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell(30, $h, $odonto->data[0]->caries, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, 'Pzas Ausentes', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell(30, $h, $odonto->data[0]->ausente, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, 'Pzas para Extraer', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell(30, $h, $odonto->data[0]->extraer, $f, 1);


        $pdf->ln(5);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, 'TRATAMIENTO', 0, 1, 'C', 1);
        $texh2 = 10;
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->MultiCell(0, 0, 'RESTAURAR ' . $odonto->data[0]->caries . ' PIEZAS, ' . ' EXTRAER ' . $odonto->data[0]->extraer . ' PIEZAS. ' . strtoupper($odonto->data[0]->odo_desc), 1, 'L', 0, 1); //
        $pdf->ln(5); //odo_desc, odo_obsr,
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, 'OBSERVACIONES', 0, 1, 'C', 1);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->MultiCell(0, 0, strtoupper($odonto->data[0]->odo_obsr), 1, 'L', 0, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->ln(0);
    } elseif ($row->ex_id == 35) {//------>AUDIOMETRIA   pdf_aud
        if ($validacion->data[0]->pdf_aud == '1') {
            $pdf->AddPage('P', 'A4');
            $pdf->setJPEGQuality(100);
//            $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
            $pages = $pdf->setSourceFile('audiometria/' . $paciente->data[0]->adm . '.pdf');
            $page = $pdf->ImportPage(1);
            $pdf->useTemplate($page, 0, 0);
        } else {
            
        }
    } elseif ($row->ex_id == 40) {//------>ESPIROMETRIA  pdf_esp
        if ($paciente->data[0]->pdf_esp == '1') {
            $pdf->AddPage('P', 'A4');
            $pdf->setJPEGQuality(100);
//            $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
            $pages = $pdf->setSourceFile('espirometria/' . $paciente->data[0]->adm . '.pdf');
            $page = $pdf->ImportPage(1);
            $pdf->useTemplate($page, 0, 0);
        } else {
            
        }
    } elseif ($row->ex_id == 57) {//------>RAYOS X
        $pdf->AddPage('P', 'A4');
        //procesos de llamado al model
        //$paciente = $model->inf_paciente($_REQUEST['adm']);
        //inicio de el diseño de la pagina
        $pdf->setJPEGQuality(100);
        $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');

        $pdf->SetFont('helvetica', 'BU', 13);
        $pdf->Cell(0, 0, 'FORMULARIO DE INFORME RADIOGRAFICO CON METODOLOGIA OIT', 0, 1, 'C');
        $pdf->Ln(3);
        $f = 0;
        $h = 4;
        $w = 40;
        $w2 = 50;
        $w3 = 8;
        $texh = 8;
        $texh2 = 7;
        $ali = 'L';
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->SetFillColor(194, 217, 241);
        $pdf->Cell($w2, $h, 'DATOS GENERALES', 0, 1, 'L', 1);
        $pdf->Cell(0, 4 * $h, '', 1);
        $pdf->ln(0);


        $pdf->Cell($w - 20, $h, 'N° DE PLACA', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 20, $h, ': ' . $rayosx->data[0]->rayo_placa, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'LECTOR', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ', $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w - 25, $h, 'NOMBRES', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 25, $h, ': ' . $paciente->data[0]->nombre, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'TIPO DE FICHA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $paciente->data[0]->tfi_desc, $f, 1);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'FECHA DE LECTURA', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $rayosx->data[0]->rayo_lector, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w, $h, 'FECHA DE RADIOGRAFIA', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2, $h, ': ' . $rayosx->data[0]->rayo_fech, $f, 1);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w - 25, $h, 'EDAD ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 - 15, $h, ': ' . $paciente->data[0]->edad, $f, 0);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(20, $h, 'EMPRESA ', $f, 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell($w2 + 60, $h, ': ' . $paciente->data[0]->emp_desc, $f, 1);
        $f = 0;
        $h = 5;
        $w = 40;
        $w2 = 50;
        $w3 = 8;
        $texh = 8;
        $texh2 = 8;
        $ali = 'L';
        $color = 0;

        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell($w2, $h, 'I. CALIDAD RADIOGRÁFICA', 0, 1, 'L', 1);
        $pdf->ln(0);
        $f = 1;

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, '', 'LTR', 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_calid == 'Buena' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '1', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Buena', $f, 0, 'L', $color);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, '', 'LTR', 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_causa == 'Sobre Exposición' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '1', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Sobre Exposición', $f, 0, 'L', $color);
        $color = ($rayosx->data[0]->rayo_causa == 'Escapulas' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '5', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Escapulas', $f, 1, 'L', $color);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, 'Calidad', 'LR', 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_calid == 'Aceptable' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '2', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Aceptable', $f, 0, 'L', $color);
        $color = 0;
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, 'Causas', 'LR', 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_causa == 'Sub Exposicion' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '2', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Sub Exposicion', $f, 0, 'L', $color);
        $color = ($rayosx->data[0]->rayo_causa == 'Artefacto' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '6', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Artefacto', $f, 1, 'L', $color);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, 'Radiografica', 'LR', 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_calid == 'Baja Calidad' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '3', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Baja Calidad', $f, 0, 'L', $color);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, '', 'LR', 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_causa == 'Posicion Centrado' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '3', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Posición Centrado', $f, 0, 'L', $color);
        $color = ($rayosx->data[0]->rayo_causa == 'Otros' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '7', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Otros', $f, 1, 'L', $color);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, '', 'LBR', 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_calid == 'Inaceptable' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '4', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Inaceptable', $f, 0, 'L', $color);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(30, $h, '', 'LBR', 0, $ali);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_causa == 'Inspiracion Insuficiente' ? 1 : 0); //--------------------------->
        $pdf->Cell(5, $h, '4', $f, 0, 'C', $color);
        $pdf->Cell(35, $h, 'Inspiracion Insuficiente', $f, 0, 'L', $color);
        $pdf->Cell(5, $h, '', $f, 0, 'C', 0);
        $pdf->Cell(35, $h, '', $f, 1, 'L', 0);
        $color = 0;
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->MultiCell(30, 8, 'Comentarios sobre defectos tecnicos', 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->MultiCell(150, 8, $rayosx->data[0]->rayo_comen, 1, 'L', 0, 1);

        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(180, $h, 'II. ANORMALIDADES PARENQUIMATOSAS (si NO hay anormalidades parenquimatosas pase a   III. ANORMALIDADES PLEURALES)', 0, 1, 'L', 1);
        $pdf->ln(1);
        $f = 1;

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->MultiCell(45, 18, '2.1. Zonas afectadas (marque TODAS las zonas afectadas).', 1, 'L', 0, 0);
        $pdf->MultiCell(45, 18, '2.2. Profusion (opacidades pequeñas)(escalas de 12 puntos)(consulte las radiografias estandar - marque las sub categoria de profusión)', 1, 'L', 0, 0);
        $pdf->MultiCell(45, 18, '2.3. Forma y tamaño :(Consulte las radiografias estandar: se requiere dos simbolos; marque un primario y un secundario)', 1, 'L', 0, 0);
        $pdf->MultiCell(45, 18, '2.4. Opacidades (marque 0 si no hay o marque A, B o C)', 1, 'L', 0, 1);

        $pdf->Cell(9, $h, '', $f, 0, 'L', $color);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(12.1, $h, 'Superior', $f, 0, 'C', $color);
        $pdf->Cell(11.99, $h, 'Medio', $f, 0, 'C', $color);
        $pdf->Cell(11.99, $h, 'Inferior', $f, 0, 'C', $color);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_profu == '0/-' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '0/-', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_profu == '0/0' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '0/0', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_profu == '0/1' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '0/1', $f, 0, 'C', $color);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(22.5, $h, 'Primaria', $f, 0, 'C', $color);
        $pdf->Cell(22.5, $h, 'Secundaria', $f, 0, 'C', $color);
        $pdf->SetFont('helvetica', '', $texh2);
        $color = ($rayosx->data[0]->rayo_opac == '0' ? 1 : 0); //--------------------------->
        $pdf->Cell(45, $h, '0', $f, 1, 'C', $color);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(9, $h, 'Der.', $f, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell(45 - 9, $h, $rayosx->data[0]->rayo_afec_der02, $f, 0, 'C');

        $color = ($rayosx->data[0]->rayo_profu == '1/0' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '1/0', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_profu == '1/1' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '1/1', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_profu == '1/2' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '1/2', $f, 0, 'C', $color);

        $color = ($rayosx->data[0]->rayo_form_prim02 == 'p' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 'p', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_form_prim02 == 's' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 's', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_form_secu02 == 'p' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 'p', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_form_secu02 == 's' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 's', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_opac == 'A' ? 1 : 0); //--------------------------->
        $pdf->Cell(45, $h, 'A', $f, 1, 'C', $color);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(9, $h, 'Izq.', $f, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $texh2);

        $pdf->Cell(45 - 9, $h, $rayosx->data[0]->rayo_afec_izq02, $f, 0, 'C');

        $color = ($rayosx->data[0]->rayo_profu == '2/1' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '2/1', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_profu == '2/2' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '2/2', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_profu == '2/3' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '2/3', $f, 0, 'C', $color);

        $color = ($rayosx->data[0]->rayo_form_prim02 == 'q' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 'q', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_form_prim02 == 't' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 't', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_form_secu02 == 'q' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 'q', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_form_secu02 == 't' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 't', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_opac == 'B' ? 1 : 0); //--------------------------->
        $pdf->Cell(45, $h, 'B', $f, 1, 'C', $color);

        $pdf->Cell(45, $h, '', $f, 0, 'C', $color);

        $color = ($rayosx->data[0]->rayo_profu == '3/2' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '3/2', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_profu == '3/3' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '3/3', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_profu == '3/+' ? 1 : 0); //--------------------------->
        $pdf->Cell(15, $h, '3/+', $f, 0, 'C', $color);

        $color = ($rayosx->data[0]->rayo_form_prim02 == 'r' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 'r', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_form_prim02 == 'u' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 'u', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_form_secu02 == 'r' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 'r', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_form_secu02 == 'u' ? 1 : 0); //--------------------------->
        $pdf->Cell(11.25, $h, 'u', $f, 0, 'C', $color);
        $color = ($rayosx->data[0]->rayo_opac == 'C' ? 1 : 0); //--------------------------->
        $pdf->Cell(45, $h, 'C', $f, 1, 'C', $color);
        $color = 0;

        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(140, $h, 'III. ANORMALIDADES PLEURALES (si NO hay anormalidades parenquimatosas pase a IV. SIMBOLOS)', 0, 0, 'L', 1);
        $pdf->Cell(20, $h, 'SI', 1, 0, 'C', 0);
        $pdf->Cell(20, $h, 'NO', 1, 1, 'C', 1);
        $pdf->ln(0);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell(180, $h, '3.1. Placas pleurales ( 0 = Ninguna, D = Hemitorax Derecho, I = Hemitorax Izquierdo)', $f, 1, 'L', $color);
        $pdf->Cell(43, 8, 'Sitio', 'LTR', 0, 'L', $color);
        $pdf->Cell(17, 8, 'Calificación', 'LTR', 0, 'L', $color);
        $pdf->MultiCell(60, 8, 'Extención (pared toraxica; combinada para placas de perfil y de frente)', 1, 'L', 0, 0);
        $pdf->Cell(60, 8, 'Ancho (opcional)(ancho minimo exigido: 3mm)', $f, 1, 'L', $color);

        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(43, $h, '', 'LR', 0, 'L', $color);
        $pdf->Cell(17, $h, '', 'LR', 0, 'L', $color);
        $pdf->Cell(5, $h, '1', $f, 0, 'L', $color);
        $pdf->Cell(55, $h, '<1/4 de la pared lateral del torax', $f, 0, 'L', $color);
        $pdf->Cell(5, $h, 'a', $f, 0, 'L', $color);
        $pdf->Cell(55, $h, 'De 3 a 5 mm', $f, 1, 'L', $color);

        $pdf->Cell(43, $h, '', 'LR', 0, 'L', $color);
        $pdf->Cell(17, $h, '', 'LR', 0, 'L', $color);
        $pdf->Cell(5, $h, '2', $f, 0, 'L', $color);
        $pdf->Cell(55, $h, 'entre 1/4 y 1/2 de la pared lateral del torax', $f, 0, 'L', $color);
        $pdf->Cell(5, $h, 'b', $f, 0, 'L', $color);
        $pdf->Cell(55, $h, 'De 5 a 10 mm', $f, 1, 'L', $color);

        $pdf->Cell(43, $h, '', 'LRB', 0, 'L', $color);
        $pdf->Cell(17, $h, '', 'LRB', 0, 'L', $color);
        $pdf->Cell(5, $h, '3', $f, 0, 'L', $color);
        $pdf->Cell(55, $h, '>1/2 de la pared lateral del torax', $f, 0, 'L', $color);
        $pdf->Cell(5, $h, 'c', $f, 0, 'L', $color);
        $pdf->Cell(55, $h, 'Mayor a 10 mm', $f, 1, 'L', $color);

        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->Cell(28, $h, 'Pared toraxica perfil', $f, 0, 'L', $color);
        $pdf->Cell(5, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(5, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(5, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(5.66, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(5.66, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(5.66, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(15, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(15, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(15, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(15, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(30, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(30, $h, 'I', $f, 1, 'C', $color);

        $pdf->Cell(28, $h, 'De frente', $f, 0, 'L', $color);
        $pdf->Cell(5, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(5, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(5, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(5.66, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(5.66, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(5.66, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(8, $h, '1', $f, 0, 'C', $color);
        $pdf->Cell(14, $h, '2', $f, 0, 'C', $color);
        $pdf->Cell(8, $h, '3', $f, 0, 'C', $color);
        $pdf->Cell(8, $h, '1', $f, 0, 'C', $color);
        $pdf->Cell(14, $h, '2', $f, 0, 'C', $color);
        $pdf->Cell(8, $h, '3', $f, 0, 'C', $color);
        $pdf->Cell(8, $h, 'a', $f, 0, 'C', $color);
        $pdf->Cell(14, $h, 'b', $f, 0, 'C', $color);
        $pdf->Cell(8, $h, 'c', $f, 0, 'C', $color);
        $pdf->Cell(8, $h, 'a', $f, 0, 'C', $color);
        $pdf->Cell(14, $h, 'b', $f, 0, 'C', $color);
        $pdf->Cell(8, $h, 'c', $f, 1, 'C', $color);

        $pdf->Cell(28, $h, 'Diafragma', $f, 0, 'L', $color);
        $pdf->Cell(5, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(5, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(5, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(5.66, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(5.66, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(5.66, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(60, $h, '', $f, 0, 'C', $color);
        $pdf->Cell(60, $h, '', $f, 1, 'C', $color);

        $pdf->Cell(180, $h, '3.2. Engrosamiento Difuso de la Pleura ( 0 = Ninguna, D = Hemitorax Derecho, I = Hemitorax Izquierdo)', $f, 1, 'L', $color);
        $pdf->Cell(45, $h, 'PARED TORACICA', $f, 0, 'C', $color);
        $pdf->Cell(45, $h, 'CALCIFICACIÓN', $f, 0, 'C', $color);
        $pdf->Cell(10, $h, '', 'LTR', 0, 'C', $color);
        $pdf->Cell(40, $h, 'EXTESIÓN', $f, 0, 'C', $color);
        $pdf->Cell(40, $h, 'ANCHO', $f, 1, 'C', $color);

        $pdf->Cell(30, $h, 'DE PERFIL', $f, 0, 'L', $color);
        $pdf->Cell(5, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(5, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(5, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(15, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(15, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(15, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(10, $h, '', 'LR', 0, 'C', $color);
        $pdf->Cell(10, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(10, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(10, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(10, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(20, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(20, $h, 'I', $f, 1, 'C', $color);

        $pdf->Cell(30, $h, 'DE FRENTE', $f, 0, 'L', $color);
        $pdf->Cell(5, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(5, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(5, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(15, $h, '0', $f, 0, 'C', 1);
        $pdf->Cell(15, $h, 'D', $f, 0, 'C', $color);
        $pdf->Cell(15, $h, 'I', $f, 0, 'C', $color);
        $pdf->Cell(10, $h, '', 'LRB', 0, 'C', $color);
        $pdf->Cell(6.66, $h, '1', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, '2', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, '3', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, '1', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, '2', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, '3', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, 'a', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, 'b', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, 'c', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, 'a', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, 'b', $f, 0, 'C', $color);
        $pdf->Cell(6.66, $h, 'c', $f, 1, 'C', $color);

        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->Cell(180, $h, 'IV. SIMBOLOS (Rodee con un circulo la respuesta adecuada; si rodea od escriba a continuacion un COMENTARIO)', 0, 1, 'L', 1);
        $pdf->ln(0);

        $pdf->Cell(12, $h, 'aa', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'at', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'ax', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'bu', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'ca', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'cg', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'cn', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'co', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'cp', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'cv', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'di', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'ef', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'em', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'es', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'od', 'LTR', 1, 'C', $color);

        $pdf->Cell(12, $h, 'fr', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'hi', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'ho', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'id', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'ih', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'kl', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'me', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'pa', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'pb', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'pi', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'px', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'ra', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'rp', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, 'tb', $f, 0, 'C', $color);
        $pdf->Cell(12, $h, '', 'LBR', 1, 'C', $color);

        $pdf->SetFont('helvetica', 'B', $texh);
        $pdf->MultiCell(30, 8, 'COMENTARIOS', 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', $texh2);
        $pdf->MultiCell(150, 8, $rayosx->data[0]->rayo_inf_mostro, 1, 'L', 0, 1);
    } else {
        
    }
}


$lab = $model->lab($paciente->data[0]->adm);
$lab2 = $model->lab2($paciente->data[0]->adm);
if ($lab->data[0]->ord >= 1) {
    $pdf->AddPage('P', 'A4');
    $pdf->setJPEGQuality(100);
    $pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
    $pdf->SetFont('helvetica', 'BU', 15);
    $pdf->Cell(0, 0, 'RESULTADOS DE LABORATORIO ', 0, 1, 'C');

    $f = 0;
    $h = 3;
    $w = 40;
    $w2 = 50;
    $w5 = 40;
    $w4 = 30;
    $w3 = 8;
    $texh = 8;
    $texh2 = 7;
    $ali = 'L';
    $w6 = 40;
    $w7 = 70;

////$pdf->Cell($w3,$h, "DATOS PERSONALES",1,,'C',1);
    $pdf->SetFont('helvetica', 'B', $texh);
//
    $pdf->SetFillColor(194, 217, 241);
    $pdf->Cell($w7, $h, 'DATOS GENERALES', 1, 1, 'L', 1);
// $pdf->Cell(100, $h, $examen->ex_desc, $f, 1, $ali, 1);
    $pdf->Cell(0, 13, '', 1);
    $pdf->ln(0);

//adm_id, adm_pacid, adm_pkid, adm_tfiid, adm_usid, adm_fechc, adm_sedid, adm_act, pac_id, pac_tdocid, pac_ndoc, pac_nombres, pac_appat, pac_apmat,
// pac_sexo, pac_fono, pac_cel, pac_ecid, pac_proid, pac_giid, pac_nacdisid, pac_fecha, pac_nacfec, pac_domdisid, pac_domdir, pac_sedid, pk_id,
//  pk_goid, pk_empid, pk_desc, pk_precio, pk_fechc, emp_id, emp_desc, emp_acro, emp_mail, emp_cel, emp_fono, emp_rl, emp_dir, emp_disid, emp_st, 
//  emp_fechc

    $pdf->Cell($w6, $h, 'APELLIDOS Y NOMBRES', $f, 0, $ali);
    $pdf->SetFont('helvetica', '', $texh2);
    $pdf->Cell($w7, $h, ': ' . $paciente->data[0]->nombre, $f, 0);

    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell($w5, $h, 'DNI ', $f, 0, $ali);
    $pdf->SetFont('helvetica', '', $texh2);
    $pdf->Cell($w4, $h, ': ' . $paciente->data[0]->pac_ndoc, $f, 1);

    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell($w6, $h, 'SEXO ', $f, 0, $ali);
    $pdf->SetFont('helvetica', '', $texh2);
    $pdf->Cell($w7, $h, ': ' . $paciente->data[0]->sexo, $f, 0);

    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell($w5, $h, 'TELEFONO ', $f, 0, $ali);
    $pdf->SetFont('helvetica', '', $texh2);
    $pdf->Cell($w4, $h, ': ' . $paciente->data[0]->pac_cel, $f, 1);

    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell($w6, $h, 'EMPRESA ', $f, 0, $ali);
    $pdf->SetFont('helvetica', '', $texh2);
    $pdf->Cell($w7, $h, ': ' . $paciente->data[0]->emp_desc, $f, 0);

    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell($w5, $h, 'FECHA DE ADMISIÓN ', $f, 0, $ali);
    $pdf->SetFont('helvetica', '', $texh2);
    $pdf->Cell($w4, $h, ': ' . $paciente->data[0]->fecha, $f, 1);

    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell($w6, $h, 'EDAD ', $f, 0, $ali);
    $pdf->SetFont('helvetica', '', $texh2);
    $pdf->Cell($w7, $h, ': ' . $paciente->data[0]->edad . ' Años', $f, 0);

    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell($w5, $h, 'NRO DE HISTORIA', $f, 0, $ali);
    $pdf->SetFont('helvetica', '', $texh2);
    $pdf->Cell($w4, $h, ': ' . $paciente->data[0]->adm, $f, 1);

    $pdf->Ln(2);
    $f = 0;
    $h = 4;
    $w = 40;
    $w2 = 50;
    $w5 = 40;
    $w4 = 30;
    $w3 = 8;
    $texh = 8;
    $texh2 = 7;
    $ali = 'L';
    $w6 = 40;
    $w7 = 70;





    $f = 1;
    $h = 3.5;

    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell(70, $h, 'EXAMENES', 1, 0, 'C', 0);
    $pdf->Cell(60, $h, 'RESULTADOS', 1, 0, 'C', 0);
    $pdf->Cell(50, $h, 'VALORES NORMALES', 1, 1, 'C', 0);
    foreach ($lab2->data[0]->examen->data as $e => $examen) {

//    $pdf->SetFont('helvetica', 'B', $texh);
//    $pdf->Cell(100, $h, $examen->ex_desc, $f, 1, $ali, 1);
        foreach ($examen->resul->data as $y => $resul) {
            $pdf->SetFont('helvetica', '', $texh2);
            if ($examen->ex_id == 131 || $examen->ex_id == 124 || $examen->ex_id == 161 || $examen->ex_id == 114 || $examen->ex_id == 163 || $examen->ex_id == 112 || $examen->ex_id == 107 || $examen->ex_id == 129) {
                if ($examen->ex_id == 131) {
//                $pdf->SetFillColor(255, 255, 127);
                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(180, $h, $examen->ex_desc, $f, 1, $ali, 1);

                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(70, $h, 'EXAMENES', $f, 0, 'C', 0);
                    $pdf->Cell(60, $h, 'RESULTADOS', 1, 0, 'C', 0);
                    $pdf->Cell(50, $h, 'VALORES NORMALES', 1, 1, 'C', 0);

                    $pdf->SetFont('helvetica', '', $texh2);
                    $pdf->Cell(70, $h, 'Color', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_color, $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1);

                    $pdf->Cell(70, $h, 'Aspecto', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_aspe, $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1);

                    $pdf->Cell(70, $h, 'PH', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_ph, $f, 0, 'C');
                    $pdf->Cell(50, $h, ' 0-15', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Densidad', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_dens, $f, 0, 'C');
                    $pdf->Cell(50, $h, ' 5.000 - 6.000', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Glucosa', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_gluc, $f, 0, 'C');
                    $pdf->Cell(50, $h, ' 70 - 105', $f, 1, 'C');


                    $pdf->Cell(70, $h, 'Urobilinogeno', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_uro . ' UE/dl', $f, 0, 'C');
                    $pdf->Cell(50, $h, '0.00 - 1.00', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Proteinas', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_prot . ' mg/dl', $f, 0, 'C');
                    $pdf->Cell(50, $h, '0 - 15', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Nitritos', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_nitr, $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1);

                    $pdf->Cell(70, $h, 'Bilirrubina', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_bili, $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1);

                    $pdf->Cell(70, $h, 'Hemoglobina', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_hemo . ' Hematies/uL', $f, 0, 'C');
                    $pdf->Cell(50, $h, '0 - 9', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Esterasa Leucocitaria', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_leuc . ' Leucocitos/uL', $f, 0, 'C');
                    $pdf->Cell(50, $h, '0 - 14', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Cuerpos Cetónico', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_ceto . ' mg/dl', $f, 0, 'C');
                    $pdf->Cell(50, $h, '0 - 14', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Leucocitos', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_leuc . ' x campo', $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Hematies', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_hema . ' x campo', $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Cristales', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_cris, $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1, 'C');


                    $pdf->Cell(70, $h, 'Gérmenes', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_germ, $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Células Epiteliales', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_epit, $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1);

                    $pdf->Cell(70, $h, 'Otros', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab2_ori_otro, $f, 0, 'C');
                    $pdf->Cell(50, $h, '', $f, 1);

                    $pdf->Cell(70, $h, 'Resultado', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab2_ori_resu, $f, 1);
//                $pdf->Cell($w2, $h, '', $f, 1);
                } else if ($examen->ex_id == 124) {
                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(180, $h, $examen->ex_desc, $f, 1, $ali, 1);

                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(70, $h, 'EXAMENES', $f, 0, 'C', 0);
                    $pdf->Cell(60, $h, 'RESULTADOS', 1, 0, 'C', 0);
                    $pdf->Cell(50, $h, 'VALORES NORMALES', 1, 1, 'C', 0);
                    $pdf->SetFont('helvetica', '', $texh2);

                    $pdf->Cell(70, $h, 'Hemoglobina', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_hglo_r . ' g/ml', $f, 0, 'C');
                    $pdf->Cell(50, $h, '11.5 - 18.0', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Hematocrito ', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_htoc_r . ' %', $f, 0, 'C');
                    $pdf->Cell(50, $h, '42 - 52', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Hematies ', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_htie_r, $f, 0, 'C');
                    $pdf->Cell(50, $h, '4.50-5.50*10^6', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Plaquetas ', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_plaq_r, $f, 0, 'C');
                    $pdf->Cell(50, $h, '150000 - 400000', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Leucocitos Totales', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_leuc_r, $f, 0, 'C');
                    $pdf->Cell(50, $h, '4500 - 11000', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Monocitos ', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_mono_r . ' %', $f, 0, 'C');
                    $pdf->Cell(50, $h, '3 - 8', $f, 1, 'C');


                    $pdf->Cell(70, $h, 'Linfocitos ', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_linf_r . ' %', $f, 0, 'C');
                    $pdf->Cell(50, $h, '20 - 40', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Eosinófilos ', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_eosi_r . ' %', $f, 0, 'C');
                    $pdf->Cell(50, $h, '0.5 - 4', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Abastonados ', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_abas_r . ' %', $f, 0, 'C');
                    $pdf->Cell(50, $h, '1 - 5', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Basófilos ', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_baso_r . ' %', $f, 0, 'C');
                    $pdf->Cell(50, $h, '0 - 0.5', $f, 1, 'C');


                    $pdf->Cell(70, $h, 'Neutrófilos Segmentados ', $f, 0);
                    $pdf->Cell(60, $h, $resul->lab3_hem_neut_r . ' %', $f, 0, 'C');
                    $pdf->Cell(50, $h, '55 - 65', $f, 1, 'C');

                    $pdf->Cell(70, $h, 'Observaciones ', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab3_hem_obs, $f, 1);
//                $pdf->Cell($w2, $h, '', $f, 1);
                } else if ($examen->ex_id == 163) {
                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(180, $h, $examen->ex_desc, $f, 1, $ali, 1);

                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(70, $h, 'EXAMENES', $f, 0, 'C', 0);
                    $pdf->Cell(110, $h, 'RESULTADOS', 1, 1, 'C', 0);
                    $pdf->SetFont('helvetica', '', $texh2);

                    $pdf->Cell(70, $h, 'GRAM', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab4_gram, $f, 1, 'C');

                    $pdf->Cell(70, $h, 'CULTIVO', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab4_cult, $f, 1, 'C');

                    $pdf->Cell(70, $h, 'OBSERVACIONES', $f, 0, 'C');
                    $pdf->Cell(110, $h, $resul->lab4_obse, $f, 1, 'C');

                    $pdf->Cell(70, $h, 'ANTIBIOGRAMA', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab4_anti, $f, 1, 'C');
                } else if ($examen->ex_id == 112) {
                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(180, $h, $examen->ex_desc, $f, 1, $ali, 1);

                    $pdf->SetFont('helvetica', 'B', $texh);
//                $pdf->Cell(70, $h, 'EXAMENES', $f, 0, 'C', 0);
//                $pdf->Cell(110, $h, 'RESULTADOS', 1, 1, 'C', 0);
                    $pdf->SetFont('helvetica', '', $texh2);

                    $pdf->Cell(70, $h, 'GRAM', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab6_gram, $f, 1, 'C');

                    $pdf->Cell(70, $h, 'CULTIVO', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab6_cult, $f, 1, 'C');

                    $pdf->Cell(70, $h, 'OBSERVACIONES', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab6_obse, $f, 1, 'C');

                    $pdf->Cell(70, $h, 'ANTIBIOGRAMA', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab6_anti, $f, 1, 'C');
                } else if ($examen->ex_id == 107) {
                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(180, $h, $examen->ex_desc, $f, 1, $ali, 1);

                    $pdf->SetFont('helvetica', 'B', $texh);
//                $pdf->Cell(70, $h, 'EXAMENES', $f, 0, 'C', 0);
//                $pdf->Cell(110, $h, 'RESULTADOS', 1, 1, 'C', 0);
                    $pdf->SetFont('helvetica', '', $texh2);

                    $pdf->Cell(70, $h, 'GRAM', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab7_gram, $f, 1, 'C');

                    $pdf->Cell(70, $h, 'CULTIVO', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab7_cult, $f, 1, 'C');

                    $pdf->Cell(70, $h, 'OBSERVACIONES', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab7_obse, $f, 1, 'C');

                    $pdf->Cell(70, $h, 'ANTIBIOGRAMA', $f, 0);
                    $pdf->Cell(110, $h, $resul->lab7_anti, $f, 1, 'C');
                } else if ($examen->ex_id == 161) {

//                $pdf->AddPage('P', 'A4');
//                $pdf->Ln(15);
                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(180, $h, $examen->ex_desc, $f, 1, $ali, 1);

                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(35, $h, 'EXAMENES', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, '1RA MUESTRA', 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, '2DA MUESTRA', 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, '3RA MUESTRA', 1, 1, 'C', 0);
                    $pdf->SetFont('helvetica', '', $texh2);
                    $pdf->Cell(35, $h, 'FECHA', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->fech_1_lab5, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->fech_2_lab5, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->fech_3_lab5, 1, 1, 'C', 0);

                    $pdf->Cell(35, $h, 'COLOR', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_1_color, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_2_color, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_3_color, 1, 1, 'C', 0);

                    $pdf->Cell(35, $h, 'ASPECTO', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_1_aspec, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_2_aspec, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_3_aspec, 1, 1, 'C', 0);

                    $pdf->Cell(35, $h, 'PARASITO', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_1_paras, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_2_paras, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_3_paras, 1, 1, 'C', 0);

                    $pdf->Cell(35, $h, 'OTROS', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_1_otros, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_2_otros, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab5_3_otros, 1, 1, 'C', 0);
                } else if ($examen->ex_id == 114) {

//                $pdf->AddPage('P', 'A4');
//                $pdf->Ln(15);
                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(180, $h, $examen->ex_desc, $f, 1, $ali, 1);

                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(35, $h, 'EXAMENES', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, '1RA MUESTRA', 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, '2DA MUESTRA', 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, '3RA MUESTRA', 1, 1, 'C', 0);
                    $pdf->SetFont('helvetica', '', $texh2);
                    $pdf->Cell(35, $h, 'FECHA', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->fech_1_lab8, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->fech_2_lab8, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->fech_3_lab8, 1, 1, 'C', 0);

//                $pdf->Cell(35, $h, 'COLOR', $f, 0, 'C', 0);
//                $pdf->Cell(48.33, $h, $resul->lab8_1_color, 1, 0, 'C', 0);
//                $pdf->Cell(48.33, $h, $resul->lab8_2_color, 1, 0, 'C', 0);
//                $pdf->Cell(48.33, $h, $resul->lab8_3_color, 1, 1, 'C', 0);
//                $pdf->Cell(35, $h, 'ASPECTO', $f, 0, 'C', 0);
//                $pdf->Cell(48.33, $h, $resul->lab8_1_aspec, 1, 0, 'C', 0);
//                $pdf->Cell(48.33, $h, $resul->lab8_2_aspec, 1, 0, 'C', 0);
//                $pdf->Cell(48.33, $h, $resul->lab8_3_aspec, 1, 1, 'C', 0);

                    $pdf->Cell(35, $h, 'BAAR', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab8_1_paras, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab8_2_paras, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab8_3_paras, 1, 1, 'C', 0);

                    $pdf->Cell(35, $h, 'OTROS', $f, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab8_1_otros, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab8_2_otros, 1, 0, 'C', 0);
                    $pdf->Cell(48.33, $h, $resul->lab8_3_otros, 1, 1, 'C', 0);
                } else if ($examen->ex_id == 129) {
                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(180, $h, $examen->ex_desc, $f, 1, $ali, 1);

                    $pdf->SetFont('helvetica', 'B', $texh);
                    $pdf->Cell(70, $h, 'RESULTADO', $f, 0, 'C', 0);
                    $pdf->Cell(110, $h, 'VALORES NORMALES', 1, 1, 'C', 0);
                    $pdf->SetFont('helvetica', '', $texh2);

                    $pdf->Cell(70, $h, '', 'LR', 0);
                    $pdf->Cell(110, $h, 'Exposición no significante: < 9.9', $f, 1);

                    $pdf->Cell(70, $h, '', 'LR', 0);
                    $pdf->Cell(110, $h, 'Minimizar exposición: 10.0 - 29.9', $f, 1);

                    $pdf->Cell(70, $h, $resul->lab1_desc1 . ' ' . $resul->ex_unidad, 'LR', 0, 'C');
                    $pdf->Cell(110, $h, 'Retirar paciente de la exposición al plomo: 30.0 - 49.9', $f, 1);

                    $pdf->Cell(70, $h, '', 'LR', 0);
                    $pdf->Cell(110, $h, 'Retirar de exposición al plomo + Evaluación médica + Considerar terapia: 50.0 - 79.9', $f, 1);

                    $pdf->Cell(70, $h, '', 'LR', 0);
                    $pdf->Cell(110, $h, 'Iniciar terapia de quelacion: 80.0 o más', $f, 1);
                }
            } else {

                $pdf->SetFont('helvetica', 'B', $texh);
                $pdf->Cell(70, $h, $examen->ex_desc, $f, 0, 'L', 1);
                $pdf->SetFont('helvetica', '', $texh2);
                $pdf->Cell(60, $h, $resul->lab1_desc1 . ' ' . $resul->ex_unidad, 1, 0, 'C', 0);
                $pdf->Cell(50, $h, $resul->ex_valores . ' ' . $resul->lab1_desc2, 1, 1, 'C', 0);
            }
        }
    }
} else {
    
}


//
//$pdf->AddPage('P', 'A4');
//
////procesos de llamado al model
////$paciente = $model->inf_paciente($_REQUEST['adm']);
////$examenes = $model->examenes($paciente->data[0]->adm);
//
////inicio de el diseño de la pagina
//$pdf->setJPEGQuality(100);
//$pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');



$pdf->Output('AUDITORIA-' . $paciente->data[0]->adm . '.PDF', 'I');
