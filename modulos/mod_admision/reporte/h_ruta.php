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

// set document information
// Información referente al PDF
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
$pdf->SetMargins(PDF_MARGIN_LEFT, 13, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Establecer la fuente
$pdf->SetFont('helvetica', 'B', 25);

// Añadir página
$pac = $model->report($_GET['adm']);
$pdf->AddPage();



$pdf->setJPEGQuality(100);
//$pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
// $pdf->ImageSVG('images/logo_pdf.svg', 4, 6, '', '', $link = '', '', 'T');
$pdf->SetFont('helvetica', 'BU', 12);
$pdf->Cell(0, 0, 'HOJA DE EXAMEN MEDICO OCUPACIONAL', 0, 1, 'C');
$pdf->Ln(10);
$f = 0;
$h = 5.3;
$w = 40;
$w2 = 50;
$w3 = 8;
$texh = 8;
$texh2 = 7;
$ali = 'L';
$id = isset($_GET['adm']) ? $_GET['adm'] : null;


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w2, $h, 'DATOS GENERALES', 1, 1);
$pdf->Cell(0, 6 * $h, '', 1);
$pdf->ln(0);

$pdf->Cell(25, $h, 'EMPRESA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(155, $h, ':' . $pac->data[0]->emp_desc, $f, 1);


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(25, $h, 'NOMBRES', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(75, $h, ': ' . $pac->data[0]->pac_nombres, 0, 0);


$cod = $pdf->zerofill($pac->data[0]->adm_id, 10);
$pdf->SetFont('helvetica', '', 'C');
//$pdf->write1DBarcode($cod, 'C39', '', '', 81, 10, 0.4)
$pdf->Cell(0, $h, $pdf->write1DBarcode($cod, 'C39', '', '', '', 10, 0.4, $style), $f, 1, 'R');
$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => true,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(25, $h, 'APELLIDOS ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(90, $h, ': ' . $pac->data[0]->pac_appat . ' ' . $pac->data[0]->pac_apmat, $f, 1);


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(25, $h, 'TIPO DE FICHA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(88, $h, ': ' . $pac->data[0]->tfi_desc, $f, 0);


$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(5, $h, 'N° H.R.', $f, 0, $ali);
$pdf->Cell(48, $h, $pdf->zerofill($pac->data[0]->adm_id, 10), $f, 0, 'R');
$pdf->ln($h);


$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(25, $h, 'PUESTO', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(75, $h, ': ' . $pac->data[0]->puesto, $f, 0);



$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'FECHA DE ADMISIÓN ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->adm_fech, $f, 1);



$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(25, $h, $pac->data[0]->tdoc_desc, $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(45, $h, ': ' . $pac->data[0]->pac_ndoc, $f, 0);



$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'CELULAR', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(25, $h, ': ' . $pac->data[0]->pac_cel, $f, 0);



$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(10, $h, 'SEXO', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh);
$pdf->Cell(30, $h, ': ' . (($pac->data[0]->pac_sexo == 'M') ? 'MASCULINO' : 'FEMENINO'), $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(10, $h, 'EDAD', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(20, $h, ': ' . $pac->data[0]->edad . ' Años.', $f, 1);


$w2 = 60;
$h = 9;
$pdf->Cell($w2, 3, "", 0, 1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(87, 5, "SERVICIOS", 0, 0, 'C');
$pdf->Cell(35, 5, "FIRMA", 0, 0, 'C');
$pdf->Cell(13, 5, "", 0, 0, 'C');
$pdf->Cell(45, 55, (($pac->data[0]->adm_foto == '1') ? $pdf->Image("images/fotos/" . $_GET['adm'] . ".png", '', '', 43.8, '', '') : 'FOTO'), 1, 0, 'C');

if ($pac->data[0]->adm_foto == '1') {
    //$pdf->Image("images/fotos/" . $_GET['adm'] . ".png", 150.6, 68.5, 43.8, '','','PNG');
	//$pdf->Image('images/osteoMuscular/10.jpg', '', '', 20, '', 'JPG');
    //$pdf->Image("images/fotos/1346.png", 150.6, 68.5, 43.8, '','','PNG');
}
$sexo = $pac->data[0]->pac_sexo;
$area = $model->area($_GET['adm'], $sexo); //psico_paquete
$ar = 0;
$pdf->Ln(7);
$rpr_prueba = 0;
$hcg = 0;
foreach ($area->data as $i => $row) {
    if ($row->ar_id != $ar) {
        $ar = $row->ar_id;
        $j = 1;
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell(5, 3, "", 1, 0);
        $pdf->Cell(82, 3, $row->ar_desc, 0, 0);
        $pdf->Cell(35, 3, "", 0, 0);
        $pdf->Cell(7, 5, "", 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(5, 3, "", 0, 0);
        $pdf->Cell(5, 3, "", 1, 0);
        $pdf->Cell(82, 3, $j++ . ".-" . $row->ex_desc, 0, 0);
        $pdf->Cell(40, 3, "", 0, 0);
        $pdf->Cell(7, 5, "", 0, 1, 'C');
    } else {
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(5, 3, "", 0, 0);
        $pdf->Cell(5, 3, "", 1, 0);
        $pdf->Cell(70, 3, $j++ . ".-" . $row->ex_desc, 0, 0);
        $pdf->Cell(40, 3, "", 0, 0);
        $pdf->Cell(7, 5, "", 0, 1, 'C');
    }
    $rpr_prueba += ($row->ex_id == '54') ? 1 : 0;
    $hcg += ($row->ex_id == '57') ? 1 : 0;
}




/*
  $psico_paquete = $model->psico_paquete($pac->data[0]->adm_id); //psico_paquete

  $j = 0;
  foreach ($area->data as $i => $row) {
  if ($row->ar_id < 5) {
  if ($row->ex_desc == 'PSICOLOGIA') {
  $string = "";
  foreach ($psico_paquete->data as $i => $rows) {
  $string .= $rows->examen . " - ";
  }
  $pdf->SetFont('helvetica', '', 7);
  $pdf->Cell($w2, $h, $i + 1 . ".-" . $row->ex_desc . ' (' . $string . ')', 1, 0);
  $pdf->Cell($w2, $h, "", 1, 0);
  $pdf->Cell(7, 5, "", 0, 0, 'C');
  $pdf->Ln(9);
  } else {
  $pdf->SetFont('helvetica', '', 7);
  $pdf->Cell($w2, $h, $i + 1 . ".-" . $row->ex_desc, 1, 0);
  $pdf->Cell($w2, $h, "", 1, 0);
  $pdf->Cell(7, 5, "", 0, 0, 'C');
  $pdf->Ln(9);
  }
  } elseif ($row->ar_id == 5) {
  $j += 1;
  $pdf->SetFont('helvetica', 'B', 8);
  $j == 1 ? $pdf->Cell($w2, 3, 'LABORATORIO', 1, 1) : "";
  $pdf->SetFont('helvetica', '', 7);
  $pdf->Cell($w2, 3, $j . ".-" . $row->ex_desc, 1, 0);
  $pdf->Cell($w2, 3, "(  )", 1, 1, 'C');
  }
  }

 */
$pdf->ln(1);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, 5, '** OJO: AL CONCLUIR ENTREGAR ESTA HOJA EN ADMISIÓN', 0, 0, 'L');
//$pdf->setVisibility('screen');
////$pdf->SetAlpha(0.1);
//$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', '', 'T', false, 300, '', false, false, 0, false, false, false);
////$pdf->setVisibility('all');

$pdf->setVisibility('screen');
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', '', 'T', false, 300, '', false, false, 0, false, false, false);

$pdf->AddPage();
$pdf->setJPEGQuality(100);
//$pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
$pdf->SetFont('helvetica', 'B', 15);

$pdf->Ln(10);
$pdf->Cell(0, 0, 'CONSENTIMIENTO INFORMADO', 0, 1, 'C');
$pdf->Cell(0, 0, 'PARA LA REALIZACION DEL EXAMEN MEDICO OCUPACIONAL', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 13);

$pdf->MultiCell(180, 1, "Yo, {$pac->data[0]->pac_appat} {$pac->data[0]->pac_apmat} {$pac->data[0]->pac_nombres}  identificado(a) con {$pac->data[0]->tdoc_desc} N° {$pac->data[0]->pac_ndoc}; trabajador de la Empresa {$pac->data[0]->emp_desc}, con domicilio en {$pac->data[0]->pac_domdir} Distrito de {$pac->data[0]->dis_desc } Provincia de {$pac->data[0]->prov_desc}  Departamento de {$pac->data[0]->dep_desc}; en pleno uso de mis facultades, libre y voluntariamente, DECLARO QUE HE SIDO DEBIDAMENTE INFORMADO, acerca del EXAMEN MEDICO OCUPACIONAL: {$pac->data[0]->tfi_desc} que se me realizara en OPTIMA S.A.C. , por lo que ACEPTO la realización del mismo (tanto exámenes médicos/odontológicos, procedimientos de ayuda diagnostica ocupacional, así como exámenes auxiliares de laboratorio e imagenologicos relacionados al riesgo de exposición).

  Asimismo declaro que MIS RESPUESTAS dadas durante el EXAMEN MEDICO OCUPACIONAL son VERDADERAS; consciente de que al ocultar o falsear la información proporcionada, puedo causar daño a mi salud o que puedan interpretarse erróneamente mis probables diagnósticos,  por lo que ASUMO LA RESPONSABILIDAD DE LAS MISMAS.

  Autorizo a Optima S.A.C en proporcionar la información concerniente a mi estado de salud como resultado del EXAMEN MEDICO OCUPACIONAL,  contenido en mi ficha médica, al RESPONSABLE MEDICO DEL AREA DE SALUD OCUPACIONAL de mi empresa.

  Entiendo que este documento puede ser REVOCADO por mi persona  en cualquier momento, del examen.

  Después de haber leído el presente documento lo firmo y coloco mi huella digital en señal de conformidad.
  ", 0);
$pdf->Cell(0, 5, '', 0, 1, 'C');
$pdf->Cell(90, 0, '', 0, 0, 'C');
$pdf->Cell(55, 25, '', 1, 0, 'C');
$pdf->Cell(35, 25, '', 1, 1, 'C');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(90, 0, '', 0, 0, 'C');
$pdf->Cell(55, 0, 'FIRMA', 1, 0, 'C');
$pdf->Cell(35, 0, 'HUELLA', 1, 1, 'C');


$pdf->Ln(6);
$pdf->Cell(0, 0, 'Cusco ' . $pac->data[0]->adm_fechc, 0, 1, 'R'); //FECHA

$pdf->Ln(8);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, 1, "* • El presente consentimiento se ampara en lo dispuesto en el Art.5 (2do párrafo) Art.13, Art.15 (inciso c), f), g) y h)), Art.25, Art.27 t Art.29 (3er párrafo) de la Ley General de Salud 29842 del Estado Peruano.
  ", 0);


$pdf->AddPage();
$pdf->setJPEGQuality(100);
$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');


$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 15);
$pdf->Cell(0, 0, 'AUTORIZACION DE LIBERACION DE', 0, 1, 'C');
$pdf->Cell(0, 0, 'INFORMACION MEDICA', 0, 1, 'C');
$pdf->Ln(10);


$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(10, 0, '', 0, 0, 'C');
$pdf->MultiCell(160, 1, "Yo, {$pac->data[0]->pac_appat} {$pac->data[0]->pac_apmat} {$pac->data[0]->pac_nombres} ,trabajador de la empresa {$pac->data[0]->emp_desc} identificado(a) con {$pac->data[0]->tdoc_desc} N° {$pac->data[0]->pac_ndoc}; con domicilio en {$pac->data[0]->pac_domdir} Distrito de {$pac->data[0]->dis_desc } Provincia de {$pac->data[0]->prov_desc}  Departamento de {$pac->data[0]->dep_desc}.
    
Declaro que, en forma libre y espontánea, autorizo a el Centro Medico Optima S.R.L. a proporcionar información con respecto a mi estado de salud, resultados auxiliares, mis resultados de evaluación medico ocupacionales, contenidas en mi Historia clínica, al responsable del Área de Salud Ocupacional De La Compañía Minera las Bambas S.A.

La presente autorización se ampara en lo dispuesto en ley general de salud N° 26842.
  ", 0);


$pdf->Ln(15);

$h = 4;
$pdf->SetFillColor(194, 217, 241);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(95, $h, '', 0, 0, 'L', 0);
$pdf->Cell(50, $h, 'FIRMA', 0, 0, 'C', 1);
$pdf->Cell(25, $h, 'Huella Digital', 0, 1, 'C', 1);

$pdf->Cell(95, $h * 8, '', 0, 0, 'C', 0);
$pdf->Cell(50, $h * 8, '', 1, 0, 'L', 0);
$pdf->Cell(25, $h * 8, '', 1, 1, 'L', 0);

$pdf->Cell(95, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, 'FECHA: ' . $pac->data[0]->adm_fech, 0, 1, 'C', 1);





$pdf->AddPage();
$pdf->setJPEGQuality(100);
$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');


$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 15);
$pdf->Cell(0, 0, 'DECLARACION JURADA', 0, 1, 'C');
//$pdf->Cell(0, 0, 'INFORMACION MEDICA', 0, 1, 'C');
$pdf->Ln(10);


$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(10, 0, '', 0, 0, 'C');
$pdf->MultiCell(160, 1, "Yo, {$pac->data[0]->pac_appat} {$pac->data[0]->pac_apmat} {$pac->data[0]->pac_nombres} ,trabajador de la empresa {$pac->data[0]->emp_desc} identificado(a) con {$pac->data[0]->tdoc_desc} N° {$pac->data[0]->pac_ndoc}; con domicilio en {$pac->data[0]->pac_domdir} Distrito de {$pac->data[0]->dis_desc } Provincia de {$pac->data[0]->prov_desc}  Departamento de {$pac->data[0]->dep_desc}, por el presente acepto que se me realice el examen médico:
    
    - {$pac->data[0]->tfi_desc}
    
Declaro que mis respuestas dadas durante el examen son verdaderas y estoy consciente que al ocultar o falsear información me puede causar daño. Asumo la responsabilidad de ello. 
  ", 0);


$pdf->Ln(15);

$h = 4;
$pdf->SetFillColor(194, 217, 241);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(95, $h, '', 0, 0, 'L', 0);
$pdf->Cell(50, $h, 'FIRMA', 0, 0, 'C', 1);
$pdf->Cell(25, $h, 'Huella Digital', 0, 1, 'C', 1);

$pdf->Cell(95, $h * 8, '', 0, 0, 'C', 0);
$pdf->Cell(50, $h * 8, '', 1, 0, 'L', 0);
$pdf->Cell(25, $h * 8, '', 1, 1, 'L', 0);

$pdf->Cell(95, $h, '', 0, 0, 'L', 0);
$pdf->Cell(75, $h, 'FECHA: ' . $pac->data[0]->adm_fech, 0, 1, 'C', 1);







if ($rpr_prueba == 1) {


    $pdf->AddPage();
    $pdf->setJPEGQuality(100);
    $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');


    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 15);
    $pdf->Cell(0, 0, 'CONSENTIMIENTO PARA REALIZAR ', 0, 1, 'C');
    $pdf->Cell(0, 0, 'LA PRUEBA (VDRL/RPR)', 0, 1, 'C');
    $pdf->Ln(10);


    $pdf->SetFont('helvetica', '', 13);
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->MultiCell(160, 1, "Yo, {$pac->data[0]->pac_appat} {$pac->data[0]->pac_apmat} {$pac->data[0]->pac_nombres} ,identificado(a) con {$pac->data[0]->tdoc_desc} N° {$pac->data[0]->pac_ndoc} con {$pac->data[0]->edad} años de edad, trabajador de la empresa {$pac->data[0]->emp_desc}; del área {$pac->data[0]->puesto}.
    
Se me ha explicado la razón de la prueba y sus fines, en tal sentido por medio de la presente autorizo y doy consentimiento para que se me realice dicho examen. Para mejor constancia firmo la presente.

    (   ) SI, Doy mi consentimiento a la prueba VDRL/RPR
    
    (   ) NO, Doy mi consentimiento a la prueba VDRL/RPR
    

", 0);


    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, 'Porque: ', 'B', 1, 'L');
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, '', 'B', 1, 'L');
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, '', 'B', 1, 'L');
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, '', 'B', 1, 'L');
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, '', 'B', 1, 'L');
    $pdf->Ln(15);

    $h = 4;
    $pdf->SetFillColor(194, 217, 241);
    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell(95, $h, '', 0, 0, 'L', 0);
    $pdf->Cell(50, $h, 'FIRMA', 0, 0, 'C', 1);
    $pdf->Cell(25, $h, 'Huella Digital', 0, 1, 'C', 1);

    $pdf->Cell(95, $h * 8, '', 0, 0, 'C', 0);
    $pdf->Cell(50, $h * 8, '', 1, 0, 'L', 0);
    $pdf->Cell(25, $h * 8, '', 1, 1, 'L', 0);

    $pdf->Cell(95, $h, '', 0, 0, 'L', 0);
    $pdf->Cell(75, $h, 'FECHA: ' . $pac->data[0]->adm_fech, 0, 1, 'C', 1);
}



if ($hcg == 1 && $pac->data[0]->pac_sexo == 'F') {


    $pdf->AddPage();
    $pdf->setJPEGQuality(100);
    $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');


    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 15);
    $pdf->Cell(0, 0, 'CONSENTIMIENTO PARA REALIZAR', 0, 1, 'C');
    $pdf->Cell(0, 0, 'LA PRUEBA (Beta-HCG)', 0, 1, 'C');
    $pdf->Ln(10);


    $pdf->SetFont('helvetica', '', 13);
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->MultiCell(160, 1, "Yo, {$pac->data[0]->pac_appat} {$pac->data[0]->pac_apmat} {$pac->data[0]->pac_nombres} ,identificado(a) con {$pac->data[0]->tdoc_desc} N° {$pac->data[0]->pac_ndoc} con {$pac->data[0]->edad} años de edad, trabajador de la empresa {$pac->data[0]->emp_desc}; del área {$pac->data[0]->puesto}.
    
Se me ha explicado la razón de la prueba y sus fines, en tal sentido por medio de la presente autorizo y doy consentimiento para que se me realice dicho examen. Para mejor constancia firmo la presente.

    (   ) SI, Doy mi consentimiento a la prueba Beta-HCG
    
    (   ) NO, Doy mi consentimiento a la prueba Beta-HCG
    

", 0);


    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, 'Porque: ', 'B', 1, 'L');
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, '', 'B', 1, 'L');
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, '', 'B', 1, 'L');
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, '', 'B', 1, 'L');
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->Cell(160, 0, '', 'B', 1, 'L');
    $pdf->Ln(15);

    $h = 4;
    $pdf->SetFillColor(194, 217, 241);
    $pdf->SetFont('helvetica', 'B', $texh);
    $pdf->Cell(95, $h, '', 0, 0, 'L', 0);
    $pdf->Cell(50, $h, 'FIRMA', 0, 0, 'C', 1);
    $pdf->Cell(25, $h, 'Huella Digital', 0, 1, 'C', 1);

    $pdf->Cell(95, $h * 8, '', 0, 0, 'C', 0);
    $pdf->Cell(50, $h * 8, '', 1, 0, 'L', 0);
    $pdf->Cell(25, $h * 8, '', 1, 1, 'L', 0);

    $pdf->Cell(95, $h, '', 0, 0, 'L', 0);
    $pdf->Cell(75, $h, 'FECHA: ' . $pac->data[0]->adm_fech, 0, 1, 'C', 1);
}





$pdf->setVisibility('screen');
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', '', 'T', false, 300, '', false, false, 0, false, false, false);


$pdf->Output('HOJA DE RUTA.pdf', 'I');
?>