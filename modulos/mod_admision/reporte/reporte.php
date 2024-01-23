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

// set document information
// Información referente al PDF
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


$pdf->AddPage('P', 'A4');


//IMAGENES
$pdf->setJPEGQuality(100);
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');
$pdf->Image('images/formato/contactos_o2.jpg', 145, 5, 50, '', 'JPEG');
// $pdf->ImageSVG('images/logo_pdf.svg', 4, 6, '', '', $link = '', '', 'T');


$pdf->setVisibility('screen');
$pdf->SetAlpha(0.3);
$pdf->Image('images/formato/marca_agua_o2.jpg', 25, 70, 160, '', 'JPEG');
$pdf->SetAlpha(1);
$pdf->setVisibility('all');


// TITULO
$pdf->SetFont('helveticaB', 'B', 12);
$pdf->Cell(0, 0, 'HOJA DE RUTA - EMO: ' . $pac->data[0]->adm_id, 0, 1, 'C');
$pdf->Ln(8);

// VARIABLES
$h = 5.3;
$textTitulo = 7;
$texto = 7;

//CABECERA
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(145, $h, $pac->data[0]->emp_desc, 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0);



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
$cod = $pdf->zerofill($pac->data[0]->adm_id, 10);
$pdf->Cell(30, 30, $pdf->write2DBarcode($cod, 'QRCODE,Q', '', '', 30, 30, $styles), 0, 0, 'C');






$pdf->Ln(8);
$pdf->Cell(145, (4 * $h) + 1, '', 1);
$pdf->ln(1);

////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', 'B', $textTitulo);
$pdf->Cell(30, $h, 'TIPO DE EVALUACION', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(80, $h, ': ' . $pac->data[0]->tfi_desc, 0, 0);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(40, $h, 'N° H.R.' . $pdf->zerofill($pac->data[0]->adm_id, 10), 0, 0, 'L');


$pdf->Cell(17, $h, '', 0, 1);


////////////////////////////////////////////////////////////////////

$pdf->SetFont('helvetica', 'B', $textTitulo);
$pdf->Cell(30, $h, 'APELLIDOS Y NOMBRES', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(81, $h, ': ' . $pac->data[0]->pac_appat . ' ' . $pac->data[0]->pac_apmat . ', ' . $pac->data[0]->pac_nombres, 0, 0);
$pdf->SetFont('helvetica', 'B', $textTitulo);
$pdf->Cell(6, $h, 'SEXO', 0, 0, 'C');

$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(19, $h, ': ' . (($pac->data[0]->pac_sexo == 'M') ? 'MASCULINO' : 'FEMENINO'), 0, 1);
////////////////////////////////////////////////////////////////////

$pdf->SetFont('helvetica', 'B', $textTitulo);
$pdf->Cell(33, $h, ($pac->data[0]->tdoc_desc == 'DNI') ? 'NÚMERO DE DNI' : $pac->data[0]->tdoc_desc, 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, ': ' . $pac->data[0]->pac_ndoc, 0, 0);

$pdf->SetFont('helvetica', 'B', $textTitulo);
$pdf->Cell(5, $h, 'EDAD', 0, 0, 'C');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, ': ' . $pac->data[0]->edad . ' Años', 0, 0);

$pdf->SetFont('helvetica', 'B', $textTitulo);
$pdf->Cell(24, $h, 'FECHA DE ADMISIÓN', 0, 0, 'C');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(27, $h, ': ' . $pac->data[0]->adm_fech, 0, 1);


////////////////////////////////////////////////////////////////////

$pdf->SetFont('helvetica', 'B', $textTitulo);
$pdf->Cell(32, $h, 'AREA - PUESTO LABORAL', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(93, $h, ': ' . $pac->data[0]->puesto, 0, 1);


$pdf->ln(2);


$pdf->SetFont('helvetica', 'B', $textTitulo);
$pdf->Cell(80, $h, "EXAMENES", 1, 0, 'C');
$pdf->Cell(50, $h, "PRUEBA REALIZADA POR", 1, 0, 'C');
$pdf->Cell(50, $h, "OBSERVACIONES", 1, 1, 'C');


$pdf->SetFont('helvetica', '', $texto);


$sexo = $pac->data[0]->pac_sexo;
$area = $model->area($_GET['adm'], $sexo); //psico_paquete
$ar = 0;

foreach ($area->data as $i => $row) {
    if ($row->ar_id != $ar) {
        $ar = $row->ar_id;
        $j = 1;
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell(80, 3, $row->ar_desc, 'T', 0);
        $pdf->Cell(50, 3, "", 'LTR', 0);
        $pdf->Cell(50, 3, "", 'LTR', 0);
        $pdf->Cell(17, 5, "", 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(5, 3, "", 1, 0);
        $pdf->Cell(75, 3, $j++ . ".-" . $row->ex_desc, 0, 0);
        $pdf->Cell(50, 3, "", 'LR', 0);
        $pdf->Cell(50, 3, "", 'LR', 0);
        $pdf->Cell(17, 5, "", 0, 1, 'C');
    } else {
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(5, 3, "", 1, 0);
        $pdf->Cell(75, 3, $j++ . ".-" . $row->ex_desc, 0, 0);
        $pdf->Cell(50, 3, "", 'LR', 0);
        $pdf->Cell(50, 3, "", 'LR', 0);
        $pdf->Cell(17, 5, "", 0, 1, 'C');
    }
}

$pdf->Cell(180, $h, "", 'T', 1, 'C');
$pdf->ln(2);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, 5, '** OJO: AL CONCLUIR ENTREGAR ESTA HOJA EN TECNOLOGIA DE LA INFORMACIÓN', 0, 0, 'L');




$pdf->AddPage('P', 'A4');
$pdf->setJPEGQuality(100);
//$pdf->Image('images/logo.png', 15, 5, 50, '', 'PNG');
//optima
// $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');

//clinica O2
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');


$pdf->setVisibility('screen');
$pdf->SetAlpha(0.3);
$pdf->Image('images/formato/marca_agua_o2.jpg', 25, 70, 160, '', 'JPEG');
$pdf->SetAlpha(1);
$pdf->setVisibility('all');


$pdf->SetFont('helvetica', 'B', 15);

$pdf->Ln(10);
$pdf->Cell(0, 0, 'CONSENTIMIENTO INFORMADO', 0, 1, 'C');
$pdf->Cell(0, 0, 'PARA LA REALIZACION DEL EXAMEN MEDICO OCUPACIONAL', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 13);

$pdf->MultiCell(180, 1, "Yo, {$pac->data[0]->pac_appat} {$pac->data[0]->pac_apmat} {$pac->data[0]->pac_nombres}  identificado(a) con {$pac->data[0]->tdoc_desc} N° {$pac->data[0]->pac_ndoc}; trabajador de la Empresa {$pac->data[0]->emp_desc}, con domicilio en {$pac->data[0]->pac_domdir} Distrito de {$pac->data[0]->dis_desc} Provincia de {$pac->data[0]->prov_desc}  Departamento de {$pac->data[0]->dep_desc}; en pleno uso de mis facultades, libre y voluntariamente, DECLARO QUE HE SIDO DEBIDAMENTE INFORMADO, acerca del EXAMEN MEDICO OCUPACIONAL: {$pac->data[0]->tfi_desc} que se me realizara en O2 MEDICAL NETWORK E.I.R.L. , por lo que ACEPTO la realización del mismo (tanto exámenes médicos, procedimientos de ayuda diagnostica ocupacional, así como exámenes auxiliares de laboratorio e imagenologicos relacionados al riesgo de exposición).

  Asimismo declaro que MIS RESPUESTAS dadas durante el EXAMEN MEDICO OCUPACIONAL son VERDADERAS; consciente de que al ocultar o falsear la información proporcionada, puedo causar daño a mi salud o que puedan interpretarse erróneamente mis probables diagnósticos,  por lo que ASUMO LA RESPONSABILIDAD DE LAS MISMAS.

  Autorizo a O2 MEDICAL NETWORK E.I.R.L. en proporcionar la información concerniente a mi estado de salud como resultado del EXAMEN MEDICO OCUPACIONAL,  contenido en mi ficha médica, al RESPONSABLE MEDICO DEL AREA DE SALUD OCUPACIONAL de mi empresa.

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
$pdf->Cell(0, 0, 'Cusco ' . $pac->data[0]->adm_fech, 0, 1, 'R'); //FECHA

$pdf->Ln(8);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, 1, "* • El presente consentimiento se ampara en lo dispuesto en el Art.5 (2do párrafo) Art.13, Art.15 (inciso c), f), g) y h)), Art.25, Art.27 t Art.29 (3er párrafo) de la Ley General de Salud 29842 del Estado Peruano.
  ", 0);



// $pdf->AddPage('P', 'A4');
// $pdf->setJPEGQuality(100);
// // $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');


// //clinica O2
// $pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');



// $pdf->Ln(10);
// $pdf->SetFont('helvetica', 'B', 15);
// $pdf->Cell(0, 0, 'AUTORIZACION DE LIBERACION DE', 0, 1, 'C');
// $pdf->Cell(0, 0, 'INFORMACION MEDICA', 0, 1, 'C');
// $pdf->Ln(10);


// $pdf->SetFont('helvetica', '', 13);
// $pdf->Cell(10, 0, '', 0, 0, 'C');
// $pdf->MultiCell(160, 1, "Yo, {$pac->data[0]->pac_appat} {$pac->data[0]->pac_apmat} {$pac->data[0]->pac_nombres} ,trabajador de la empresa {$pac->data[0]->emp_desc} identificado(a) con {$pac->data[0]->tdoc_desc} N° {$pac->data[0]->pac_ndoc}; con domicilio en {$pac->data[0]->pac_domdir} Distrito de {$pac->data[0]->dis_desc} Provincia de {$pac->data[0]->prov_desc}  Departamento de {$pac->data[0]->dep_desc}.

// Declaro que, en forma libre y espontánea, autorizo a O2 MEDICAL NETWORK E.I.R.L. a proporcionar información con respecto a mi estado de salud, resultados auxiliares, mis resultados de evaluación medico ocupacionales, contenidas en mi Historia clínica, al responsable del Área de Salud Ocupacional De La Compañía Minera las Bambas S.A.

// La presente autorización se ampara en lo dispuesto en ley general de salud N° 26842.
//   ", 0);


// $pdf->Ln(15);

// $h = 4;
// $pdf->SetFillColor(194, 217, 241);
// $pdf->SetFont('helvetica', 'B', $texh);
// $pdf->Cell(95, $h, '', 0, 0, 'L', 0);
// $pdf->Cell(50, $h, 'FIRMA', 0, 0, 'C', 1);
// $pdf->Cell(25, $h, 'Huella Digital', 0, 1, 'C', 1);

// $pdf->Cell(95, $h * 8, '', 0, 0, 'C', 0);
// $pdf->Cell(50, $h * 8, '', 1, 0, 'L', 0);
// $pdf->Cell(25, $h * 8, '', 1, 1, 'L', 0);

// $pdf->Cell(95, $h, '', 0, 0, 'L', 0);
// $pdf->Cell(75, $h, 'FECHA: ' . $pac->data[0]->adm_fech, 0, 1, 'C', 1);






$pdf->AddPage('P', 'A4');
$pdf->setJPEGQuality(100);
// $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');

//clinica O2
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');


$pdf->setVisibility('screen');
$pdf->SetAlpha(0.3);
$pdf->Image('images/formato/marca_agua_o2.jpg', 25, 70, 160, '', 'JPEG');
$pdf->SetAlpha(1);
$pdf->setVisibility('all');


$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 15);
$pdf->Cell(0, 0, 'DECLARACION JURADA', 0, 1, 'C');
//$pdf->Cell(0, 0, 'INFORMACION MEDICA', 0, 1, 'C');
$pdf->Ln(10);


$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(10, 0, '', 0, 0, 'C');
$pdf->MultiCell(160, 1, "Yo, {$pac->data[0]->pac_appat} {$pac->data[0]->pac_apmat} {$pac->data[0]->pac_nombres} ,trabajador de la empresa {$pac->data[0]->emp_desc} identificado(a) con {$pac->data[0]->tdoc_desc} N° {$pac->data[0]->pac_ndoc}; con domicilio en {$pac->data[0]->pac_domdir} Distrito de {$pac->data[0]->dis_desc} Provincia de {$pac->data[0]->prov_desc}  Departamento de {$pac->data[0]->dep_desc}, por el presente acepto que se me realice el examen médico:
    
    - {$pac->data[0]->tfi_desc}
    
Declaro que mis respuestas dadas durante el examen son verdaderas y estoy consciente que al ocultar o falsear información me puede causar daño. Asumo la responsabilidad de ello. 
  ", 0);


$pdf->Ln(15);

$pdf->Cell(0, 5, '', 0, 1, 'C');
$pdf->Cell(90, 0, '', 0, 0, 'C');
$pdf->Cell(55, 25, '', 1, 0, 'C');
$pdf->Cell(35, 25, '', 1, 1, 'C');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(90, 0, '', 0, 0, 'C');
$pdf->Cell(55, 0, 'FIRMA', 1, 0, 'C');
$pdf->Cell(35, 0, 'HUELLA', 1, 1, 'C');


$pdf->Ln(6);
$pdf->Cell(0, 0, 'Cusco ' . $pac->data[0]->adm_fech, 0, 1, 'R'); //FECHA






















$pdf->AddPage('P', 'A4');
$pdf->setJPEGQuality(100);
// $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');

//clinica O2
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');


$pdf->setVisibility('screen');
$pdf->SetAlpha(0.3);
$pdf->Image('images/formato/marca_agua_o2.jpg', 25, 70, 160, '', 'JPEG');
$pdf->SetAlpha(1);
$pdf->setVisibility('all');


// $pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 15);
$pdf->Cell(0, 0, 'Declaración Jurada', 0, 1, 'C');
$pdf->Cell(0, 0, 'Ficha de sintomatología COVID-19', 0, 1, 'C');
$pdf->Ln(5);



$h = 4.5;
$pdf->SetFont('helvetica', '', 13);
$pdf->MultiCell(180, $h, 'He recibido explicación del objetivo de esta evaluación y me comprometo a responder con la verdad.', 0, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(25, $h, 'Empresa:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(130, $h, $pac->data[0]->emp_desc, 0, 1, 'L');


$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(50, $h, 'Apellidos y Nombres:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(130, $h, $pac->data[0]->pac_appat .' '. $pac->data[0]->pac_apmat.', '.$pac->data[0]->pac_nombres, 0, 1, 'L');


$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(10, $h, 'DNI:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(45, $h, $pac->data[0]->pac_ndoc, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(40, $h, 'Número (celular):', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(25, $h, $pac->data[0]->pac_cel, 0, 1, 'L');


$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(38, $h, 'Área de Trabajo:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(100, $h, $pac->data[0]->puesto, 0, 1, 'L');


$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(23, $h, 'Dirección:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(90, $h, $pac->data[0]->pac_domdir, 0, 1, 'L');




$pdf->Ln(3);
// $pdf->Ln(5);

$pdf->SetFont('helvetica', '', 13);
$pdf->MultiCell(180, $h, 'En los últimos 14 días calendario ha tenido alguno de los síntomas siguientes, marque entre paréntesis y subraye cuál:', 0, 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '', 0, 0, 'L'); 
$pdf->Cell(15, $h, 'SI', 1, 0, 'C');
$pdf->Cell(15, $h, 'NO', 1, 1, 'C');
$pdf->SetFont('helvetica', '', 13);


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '1. Sensación de alza térmica o fiebre', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '2. Tos, dolor de garganta, estornudos o dificultad para respirar', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '3. Expectoración o flema amarilla o verdosa', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '4. Pérdida de gusto o del olfato', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '5. Contacto con persona(s) con un caso confirmado de COVID-19', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '6. Está tomando alguna medicación (detallar cuál o cuáles):', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');

// $pdf->Ln(15);

$pdf->Ln(3);

$pdf->SetFont('helvetica', '', 13);
$pdf->MultiCell(180, $h, 'Indique otras condiciones acerca de su salud:', 0, 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '', 0, 0, 'L'); 
$pdf->Cell(15, $h, 'SI', 1, 0, 'C');
$pdf->Cell(15, $h, 'NO', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 13);

$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '1. Obesidad (IMC > 40) (IMC (peso/talla²)', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '2. Gestación', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '3. Enfermedad cardiovascular', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '4. Edad Mayor a 65 años', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '5. Hipertensión arterial', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '6. Cáncer', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '7. Diabetes mellitus', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '8. Asma', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '9. Enfermedad pulmonar crónica', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '10. Insuficiencia renal crónica', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L'); 
$pdf->Cell(140, $h, '11. Enfermedad o tratamiento inmunosupresor', 0, 0, 'L'); 
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(15, $h, '', 1, 0, 'L');
$pdf->Cell(15, $h, '', 1, 1, 'L');




$pdf->Ln(3);
$pdf->SetFont('helvetica', '', 13);
$pdf->MultiCell(180, $h, 'Todos los datos expresados en esta ficha constituyen declaración jurada de mi parte.', 0, 'L', 0, 1);
$pdf->MultiCell(180, $h, 'He sido informado que de omitir o declarar información falsa puedo perjudicar la salud de mis compañeros de trabajo y la mía propia, asumiendo las responsabilidades que correspondan.', 0, 'L', 0, 1);




// $pdf->Cell(0, 5, '', 0, 1, 'C');
$pdf->Cell(90, 0, '', 0, 0, 'C');
$pdf->Cell(55, 25, '', 1, 0, 'C');
$pdf->Cell(35, 25, '', 1, 1, 'C');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(90, 0, '', 0, 0, 'C');
$pdf->Cell(55, 0, 'FIRMA', 1, 0, 'C');
$pdf->Cell(35, 0, 'HUELLA', 1, 1, 'C');


$pdf->Ln(3);
$pdf->Cell(0, 0, 'Cusco ' . $pac->data[0]->adm_fech, 0, 1, 'R'); //FECHA






















if ($rpr_prueba == 1) {



    $pdf->AddPage('P', 'A4');
    $pdf->setJPEGQuality(100);
    // $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');

    //clinica O2
    $pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');


$pdf->setVisibility('screen');
$pdf->SetAlpha(0.3);
$pdf->Image('images/formato/marca_agua_o2.jpg', 25, 70, 160, '', 'JPEG');
$pdf->SetAlpha(1);
$pdf->setVisibility('all');


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

    $pdf->Cell(0, 5, '', 0, 1, 'C');
    $pdf->Cell(90, 0, '', 0, 0, 'C');
    $pdf->Cell(55, 25, '', 1, 0, 'C');
    $pdf->Cell(35, 25, '', 1, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(90, 0, '', 0, 0, 'C');
    $pdf->Cell(55, 0, 'FIRMA', 1, 0, 'C');
    $pdf->Cell(35, 0, 'HUELLA', 1, 1, 'C');


    $pdf->Ln(6);
    $pdf->Cell(0, 0, 'Cusco ' . $pac->data[0]->adm_fech, 0, 1, 'R'); //FECHA
}



if ($hcg == 1 && $pac->data[0]->pac_sexo == 'F') {



    $pdf->AddPage('P', 'A4');
    $pdf->setJPEGQuality(100);
    // $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');

    //clinica O2
    $pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');


$pdf->setVisibility('screen');
$pdf->SetAlpha(0.3);
$pdf->Image('images/formato/marca_agua_o2.jpg', 25, 70, 160, '', 'JPEG');
$pdf->SetAlpha(1);
$pdf->setVisibility('all');


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

    $pdf->Cell(0, 5, '', 0, 1, 'C');
    $pdf->Cell(90, 0, '', 0, 0, 'C');
    $pdf->Cell(55, 25, '', 1, 0, 'C');
    $pdf->Cell(35, 25, '', 1, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(90, 0, '', 0, 0, 'C');
    $pdf->Cell(55, 0, 'FIRMA', 1, 0, 'C');
    $pdf->Cell(35, 0, 'HUELLA', 1, 1, 'C');


    $pdf->Ln(6);
    $pdf->Cell(0, 0, 'Cusco ' . $pac->data[0]->adm_fech, 0, 1, 'R'); //FECHA
}



if ($pac->data[0]->pac_sexo == 'F') {



    $pdf->AddPage('P', 'A4');
    $pdf->setJPEGQuality(100);
    // $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, 50, '', $link = '', '', 'T');

    //clinica O2
    $pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');


$pdf->setVisibility('screen');
$pdf->SetAlpha(0.3);
$pdf->Image('images/formato/marca_agua_o2.jpg', 25, 70, 160, '', 'JPEG');
$pdf->SetAlpha(1);
$pdf->setVisibility('all');


    $pdf->Ln(30);
    $pdf->SetFont('helvetica', 'B', 15);
    $pdf->Cell(0, 0, 'DECLARACIÓN JURADO DE NO ESTAR EMBARAZADA', 0, 1, 'C');
    // $pdf->Cell(0, 0, 'LA PRUEBA (Beta-HCG)', 0, 1, 'C');
    $pdf->Ln(25);


    $pdf->SetFont('helvetica', '', 13);
    $pdf->Cell(10, 0, '', 0, 0, 'C');
    $pdf->MultiCell(160, 1, "Yo, {$pac->data[0]->pac_appat} {$pac->data[0]->pac_apmat} {$pac->data[0]->pac_nombres} ,identificado(a) con {$pac->data[0]->tdoc_desc} N° {$pac->data[0]->pac_ndoc} con {$pac->data[0]->edad} años de edad, trabajador de la empresa {$pac->data[0]->emp_desc}; del área {$pac->data[0]->puesto}.


Declaro NO estar embarazada ni tener sospecha de estarlo, por lo que accedio a toma de la Radiografía solicitada según protocolo.
Autorizo al personal de PREVENCIONES OCUPACIONALES DE SALUD para que lleve a cabo la radiografía correspondiente exceptuándolos de cualquier responsabilidad.

", 0);



    $pdf->Ln(25);

    $pdf->Cell(0, 5, '', 0, 1, 'C');
    $pdf->Cell(90, 0, '', 0, 0, 'C');
    $pdf->Cell(55, 25, '', 1, 0, 'C');
    $pdf->Cell(35, 25, '', 1, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(90, 0, '', 0, 0, 'C');
    $pdf->Cell(55, 0, 'FIRMA', 1, 0, 'C');
    $pdf->Cell(35, 0, 'HUELLA', 1, 1, 'C');


    $pdf->Ln(6);
    $pdf->Cell(0, 0, 'Cusco ' . $pac->data[0]->adm_fech, 0, 1, 'R'); //FECHA
}





$pdf->Output('HOJA DE RUTA.pdf', 'I');
