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
$formato7c = $model->rep_formato7c($_REQUEST['adm']);
$triaje = $model->triaje($_REQUEST['adm']);
$ex_adicionales = $model->ex_adicionales($_REQUEST['adm']);
$oftalmologia = $model->oftalmologia($_REQUEST['adm']);
$audio_aerea = $model->audio_aerea($_REQUEST['adm']);
$audio_osea = $model->audio_osea($_REQUEST['adm']);
$rayosx = $model->rayosx($_REQUEST['adm']);
$lab_hemo = $model->lab_hemo($_REQUEST['adm']);
$lab_rpr = $model->lab_rpr($_REQUEST['adm']);
$validacion = $model->validacion($_REQUEST['adm']);
$diagnostico = $model->diagnostico($_REQUEST['adm']);
$interconsulta = $model->interconsulta($_REQUEST['adm']);
$recomendaciones = $model->recomendaciones($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */

// Añadir página
$pdf->AddPage('P', 'A4');

$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
//$pdf->Image('images/logo.png', 8, 2, 45, '', 'PNG');
$pdf->Ln(2);
$h = 3.5;
$titulo = 7;
$texto = 7;

$pdf->SetFont('helvetica', 'B', 15);
$pdf->Cell(0, 0, 'EXAMEN MÉDICO OCUPACIONAL', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h + 1, 'I. DATOS GENERALES', 1, 1, 'L', 1);
$pdf->Cell(0, $h + 1, '', 1);
$pdf->ln(0);




$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(40, $h + 1, 'APELLIDOS Y NOMBRES', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(105, $h + 1, ': ' . $paciente->data[0]->nom_ap, 0, 0);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(17, $h + 1, 'Nro. FICHA:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(20, $h + 1, $paciente->data[0]->adm, 0, 1, 'C');


$pdf->Cell(0, 18, '', 1);
$pdf->ln(0.5);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'EMPRESA', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(113, $h, ':' . $paciente->data[0]->emp_desc, 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(22, $h, 'EXÁMEN MÉDICO', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, ': ' . $paciente->data[0]->tipo, 0, 1);




$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ACTIVIDAD A REALIZAR', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(98, $h, ':' . $paciente->data[0]->adm_act, 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'FECHA DE EXAMEN', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, ': ' . $paciente->data[0]->fech_reg, 0, 1);




$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(33, $h, $paciente->data[0]->documento, 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, ': ' . $paciente->data[0]->pac_ndoc, 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'FECHA DE NACIMIENTO', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, ': ' . $paciente->data[0]->fech_naci, 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(8, $h, 'EDAD', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, ': ' . $paciente->data[0]->edad . ' AÑOS', 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(8, $h, 'SEXO', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, ': ' . $paciente->data[0]->sexo, 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(13, $h, 'CELULAR', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(16, $h, ': ' . $paciente->data[0]->pac_cel, 0, 1);





$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(11, $h, 'CORREO', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(50, $h, ': ' . $paciente->data[0]->pac_correo, 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(33, $h, 'GRADO DE INSTRUCCION', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(45, $h, ': ' . $paciente->data[0]->ginstruccion, 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(18, $h, 'ESTADO CIVIL', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(22, $h, ': ' . $paciente->data[0]->ecivil, 0, 1);





$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'DOMICILIO ACTUAL', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(155, $h, ': ' . $paciente->data[0]->ubica . '  DIRECCION: ' . $paciente->data[0]->direc, 0, 1);












$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'II. ANTECEDENTES OCUPACIONALES', 1, 1, 'L', 1);
$pdf->Cell(0, $h + 0.5, '', 1);
$pdf->ln(0.5);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(28, $h, 'TRABAJO EN MINERIA', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, ': ' . (($formato7c->data[0]->ficha7c_mineria == '1') ? 'SI' : 'NO'), 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(37, $h, 'TRABAJA EN CONSTRUCCIÓN', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, ':' . (($formato7c->data[0]->ficha7c_construc == '1') ? 'SI' : 'NO'), 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'OPERACION EN ', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
if ($formato7c->data[0]->ficha7c_suelo == '1')
    $suelo = 'SUPERFICIE';
else if ($formato7c->data[0]->ficha7c_suelo == '2')
    $suelo = 'CONCENTRADORA';
else if ($formato7c->data[0]->ficha7c_suelo == '3')
    $suelo = 'SUBSUELO';
$pdf->Cell(29, $h, ': ' . $suelo, 0, 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(23, $h, 'ALTITUD LABORAL', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $texto);
if ($formato7c->data[0]->ficha7c_altura == '1')
    $altura = 'HASTA 3000 M';
else if ($formato7c->data[0]->ficha7c_altura == '2')
    $altura = '3001 A 3500 M';
else if ($formato7c->data[0]->ficha7c_altura == '3')
    $altura = '3501 A 4000 M';
else if ($formato7c->data[0]->ficha7c_altura == '4')
    $altura = '4001 A 4500 M';
else if ($formato7c->data[0]->ficha7c_altura == '5')
    $altura = 'MAS DE 4501 M';
$pdf->Cell(30, $h, ': ' . $altura, 0, 1);


$h = 4.5;
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h + 2, 'N°', 1, 0, 'C');
$pdf->MultiCell(18, $h + 2, 'FECHA INICIO-FIN', 1, 'C', 0, 0);
$pdf->Cell(42.1, $h + 2, 'EMPRESA', 1, 0, 'C');
$pdf->Cell(42.1, $h + 2, 'PROYECTO', 1, 0, 'C');
$pdf->Cell(42.1, $h + 2, 'CARGO', 1, 0, 'C');
$pdf->Cell(15, $h + 2, 'SUELO', 1, 0, 'C');
$pdf->Cell(15, $h + 2, 'ALTITUD', 1, 1, 'C');

$h = 2.7;
$ante_ocupa = $model->antece_7c($_REQUEST['adm']);
foreach ($ante_ocupa->data as $i => $row) {
    $pdf->SetFont('helvetica', '', 5.5);
    $pdf->Cell(5, $h * 2, $i + 1, 1, 0, 'C');
    $pdf->MultiCell(18, $h * 2, $row->antec_fech_ini . ' ' . $row->antec_fech_ini, 1, 'C', 0, 0);
    $pdf->MultiCell(42.1, $h * 2, $row->antec_empresa, 1, 'C', 0, 0);
    $pdf->MultiCell(42.1, $h * 2, $row->antec_proyec, 1, 'C', 0, 0);
    $pdf->MultiCell(42.1, $h * 2, $row->antec_cargo, 1, 'C', 0, 0);
    $pdf->MultiCell(15, $h * 2, $row->antec_suelo, 1, 'C', 0, 0);
    $pdf->MultiCell(15, $h * 2, $row->antec_alti, 1, 'C', 0, 1);
}

$h = 4;
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'III. ANTECEDENTES PATOLÓGICOS PERSONALES', 1, 1, 'L', 1);


$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(35, $h, 'HTA', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_hta == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->Cell(35, $h, 'HIPERTRIGLICERIDEMIA', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_htg == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->Cell(35, $h, 'MIGRAÑA', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_migra == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->Cell(35, $h, 'HBP', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_hbp == '1') ? 'SI' : 'NO'), 1, 1, 'C');
////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(35, $h, 'DD MM', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_dn == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->Cell(35, $h, 'HIPERCOLESTEROLEMIA', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_hcol == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->Cell(35, $h, 'ARTROPATIA', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_artro == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->Cell(35, $h, 'ASMA', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_asma == '1') ? 'SI' : 'NO'), 1, 1, 'C');
////////////////////////////////////////////////////////////////////////////////
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(35, $h, 'PROV. CARD. VASCULARES', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_prob == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->Cell(35, $h, 'Pt COLUMNA', 1, 0, 'C');
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_ptcolum == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->Cell(15, $h, 'ALERGIAS', 1, 0, 'C');
$pdf->Cell(75, $h, (($formato7c->data[0]->ficha7c_alergia == '1') ? 'SI' : 'NO') . ' - ' . $formato7c->data[0]->ficha7c_alergiadesc, 1, 1, 'C');

$pdf->MultiCell(180, 0, 'OTRAS PATOLOGIAS: ' . $formato7c->data[0]->ficha7c_otros, 1, 'L', 0, 1);
$pdf->MultiCell(180, 0, 'QUEMADURAS: ' . $formato7c->data[0]->ficha7c_quemaduras, 1, 'L', 0, 1);
$pdf->MultiCell(180, 0, 'CIRUGIAS: ' . $formato7c->data[0]->ficha7c_qx, 1, 'L', 0, 1);
$pdf->MultiCell(180, 0, 'INTOXICACIONES: ' . $formato7c->data[0]->ficha7c_intoxica, 1, 'L', 0, 1);

if ($formato7c->data[0]->ficha7c_tabaco == '1')
    $tabaco = 'NADA';
else if ($formato7c->data[0]->ficha7c_tabaco == '2')
    $tabaco = 'POCO';
else if ($formato7c->data[0]->ficha7c_tabaco == '3')
    $tabaco = 'HABITUAL';
else if ($formato7c->data[0]->ficha7c_tabaco == '4')
    $tabaco = 'EXCESIVO';


if ($formato7c->data[0]->ficha7c_alcohol == '1')
    $alcohol = 'NADA';
else if ($formato7c->data[0]->ficha7c_alcohol == '2')
    $alcohol = 'POCO';
else if ($formato7c->data[0]->ficha7c_alcohol == '3')
    $alcohol = 'HABITUAL';
else if ($formato7c->data[0]->ficha7c_alcohol == '4')
    $alcohol = 'EXCESIVO';


if ($formato7c->data[0]->ficha7c_drogas == '1')
    $drogas = 'NADA';
else if ($formato7c->data[0]->ficha7c_drogas == '2')
    $drogas = 'POCO';
else if ($formato7c->data[0]->ficha7c_drogas == '3')
    $drogas = 'HABITUAL';
else if ($formato7c->data[0]->ficha7c_drogas == '4')
    $drogas = 'EXCESIVO';

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(22.5, $h, 'TABACO', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(27.5, $h, $tabaco, 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(22.5, $h, 'ALCOHOL', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(27.5, $h, $alcohol, 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(22.5, $h, 'DROGAS', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(25, $h, $drogas, 1, 0);

if ($paciente->data[0]->sexo == 'MASCULINO') {
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(32.5, $h, '', 1, 1);
} ELSE {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(10, $h, 'F.U.R', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(22.5, $h, $formato7c->data[0]->ficha7c_fur, 1, 1);
}


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'IV. ANTECEDENTES PATOLÓGICOS FAMILIARES', 1, 1, 'L', 1);


$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, 'ESTADO DEL PADRE', 1, 0, 'C');
$pdf->Cell(60, $h, $formato7c->data[0]->ficha7c_padre, 1, 0, 'L');
$pdf->Cell(30, $h, 'ESTADO DE LA MADRE', 1, 0, 'C');
$pdf->Cell(60, $h, $formato7c->data[0]->ficha7c_madre, 1, 1, 'L');


$pdf->Cell(25, $h, 'HERMANOS', 1, 0, 'C');
$pdf->Cell(65, $h, $formato7c->data[0]->ficha7c_hermanos, 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(25, $h, 'ESPOSO(A)', 1, 0, 'C');
$pdf->Cell(65, $h, $formato7c->data[0]->ficha7c_esposo, 1, 1, 'L');

$pdf->Cell(40, $h, 'TIENE HIJOS VIVOS?', 1, 0, 'C');
$pdf->Cell(20, $h, $formato7c->data[0]->ficha7c_hijov, 1, 0, 'L');
$pdf->Cell(10, $h, 'N°', 1, 0, 'C');
$pdf->Cell(20, $h, $formato7c->data[0]->ficha7c_hijov_nro, 1, 0, 'L');

$pdf->Cell(40, $h, 'TIENE HIJOS FALLECIDOS?', 1, 0, 'C');
$pdf->Cell(20, $h, $formato7c->data[0]->ficha7c_hijof, 1, 0, 'L');
$pdf->Cell(10, $h, 'N°', 1, 0, 'C');
$pdf->Cell(20, $h, $formato7c->data[0]->ficha7c_hijof_nro, 1, 1, 'L');





$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'V. SIGNOS VITALES Y FUNCIONES VITALES', 1, 1, 'L', 1);


$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(16, $h * 2, 'TALLA', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(15, $h * 2, $triaje->data[0]->tri_talla . 'm', 1, 0);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(15, $h * 2, 'PESO', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(13, $h * 2, $triaje->data[0]->tri_peso . ' Kg', 1, 0);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(10, $h * 2, 'IMC', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(26, $h * 2, $triaje->data[0]->tri_img . ' Kg/m²', 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'CAPACIDAD VITAL', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(19, $h, $ex_adicionales->data[0]->esp_vital . ' cc', 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(22, $h, 'SATURACIÓN', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(19, $h, $triaje->data[0]->tri_satura . '%', 1, 0);
$pdf->ln($h);
$pdf->Cell(95, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'TEMPERARURA', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(17.5, $h, $triaje->data[0]->tri_temp . ' °C', 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(27, $h, 'PRESION ARTERIAL.', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15.5, $h, $triaje->data[0]->tri_pa, 1, 1);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(45, $h, 'FRECUENCIA CARDIACA', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(45, $h, $triaje->data[0]->tri_fc . ' x min.', 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(45, $h, 'FRECUENCIA RESPIRATORIA', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(45, $h, $triaje->data[0]->tri_fr . ' x min.', 1, 1);




$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'Perimetro Toraxico', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $triaje->data[0]->tri_ptorax . ' cm', 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'Maxima Inspiracion', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $triaje->data[0]->tri_inspira . ' cm', 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'Perimetro Abdominal', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $triaje->data[0]->tri_abdom . ' cm', 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'Expiracion Forzada', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $triaje->data[0]->tri_espira . ' cm', 1, 1);








$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'VI. EXAMEN FÍSICO REGIONAL', 1, 1, 'L', 1);

if ($ex_adicionales->data[0]->musc_mus_fuerz == '0')
    $fuerza = 'NINGUNA CONTRACCION';
else if ($ex_adicionales->data[0]->musc_mus_fuerz == '1')
    $fuerza = 'CONTRACCION DÉBIL';
else if ($ex_adicionales->data[0]->musc_mus_fuerz == '2')
    $fuerza = 'MOVIMIENTO ACTIVO SIN OPOSICION DE LA GRAVEDAD';
else if ($ex_adicionales->data[0]->musc_mus_fuerz == '3')
    $fuerza = 'MOVIMIENTO ACTIVO CONTRA LA FUERZA DE LA GRAVEDAD';
else if ($ex_adicionales->data[0]->musc_mus_fuerz == '4')
    $fuerza = 'MOVIMIENTO ACTIVO CONTRA LA FUERZA DE LA GRAVEDAD Y LA RESISTENCIA DEL EXAMINADOR';
else if ($ex_adicionales->data[0]->musc_mus_fuerz == '5')
    $fuerza = 'FUERZA NORMAL';

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'FUERZA MUSCULAR :', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(150, $h, $fuerza, 1, 1);




$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(18, $h, 'LOTEP :', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(18, $h, (($formato7c->data[0]->ficha7c_lotep == '1') ? 'SI' : 'NO'), 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(18, $h, 'ABEG :', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(18, $h, (($formato7c->data[0]->ficha7c_abeg == '1') ? 'SI' : 'NO'), 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(18, $h, 'ABEH :', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(18, $h, (($formato7c->data[0]->ficha7c_abeh == '1') ? 'SI' : 'NO'), 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(18, $h, 'ABEN :', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(18, $h, (($formato7c->data[0]->ficha7c_aben == '1') ? 'SI' : 'NO'), 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(21, $h, 'PUPILAS CIRLA', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, (($formato7c->data[0]->ficha7c_pupila == '1') ? 'SI' : 'NO'), 1, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'BOCA, AMIGDALAS, FARINGE, LARINGE: ' . (($formato7c->data[0]->ficha7c_boca == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_boca_obs, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'CUELLO: ' . (($formato7c->data[0]->ficha7c_cuello == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_cuello_obs, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'NARIZ: ' . (($formato7c->data[0]->ficha7c_nariz == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_nariz_obs, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'LENGUA, ATENCION, MEMORIA, ORIENTACION, INTELIGENCIA: ' . (($formato7c->data[0]->ficha7c_lengua == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_lengua_obs, 'LRB', 'L', 0, 1);



$caries = $model->caries($_REQUEST['adm']);
$extraer = $model->extraer($_REQUEST['adm']);
$pieza_caries = $model->pieza_caries($_REQUEST['adm']);
$pieza_extraer = $model->pieza_extraer($_REQUEST['adm']);
$recomenda_odo = $model->recomenda_odo($_REQUEST['adm']);
$trata_odo = $model->trata_odo($_REQUEST['adm']);
$piezae = '';
$piezaa = '';
foreach ($pieza_extraer->data as $x => $rep) {
    if ($rep->gramad_diag_raiz == 4) {
        if ($x < $rep) {
            $piezae = $rep->gramad_diente . '.' . $piezae;
        } else {
            $piezae = $rep->gramad_diente . '-' . $piezae;
        }
    } else if ($rep->gramad_diag_raiz == 3) {
        if ($x < $rep) {
            $piezaa = $rep->gramad_diente . '.' . $piezaa;
        } else {
            $piezaa = $rep->gramad_diente . '-' . $piezaa;
        }
    }
}
$piezac = '';
foreach ($pieza_caries->data as $i => $r) {
    if ($i < $r) {
        $piezac = $r->gramad_diente . '.' . $piezac;
    } else {
        $piezac = $r->gramad_diente . '-' . $piezac;
    }
}
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'VII. ODONTOLOGIA', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'N° TOTAL DE CARIES:', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, $caries->data[0]->caries, 1, 0, 'C');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(35, $h, 'N° TOTAL PARA EXTRAER:', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, $extraer->data[0]->extraer, 1, 0, 'C');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'PIEZAS CON CARIES:', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(65, $h, $piezac, 1, 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(35, $h, 'PIEZAS PARA EXTRAER:', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(55, $h, $piezae, 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'PIEZAS AUSENTES:', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(60, $h, $piezaa, 1, 1, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'RECOMENDACIÓNES Y OBSERVACIÓNES ODONTOLÓGICAS', 1, 1, 'C', 0);
//$pdf->ln(1);
$pdf->SetFont('helvetica', 'B', 7);

foreach ($recomenda_odo->data as $i => $row) {
    $pdf->ln(0.2);
    $pdf->SetFont('helvetica', '', 6.5);
    $pdf->MultiCell(180, 3, $i + 1 . '.- ' . $row->reco_desc, 'B', 'L', 0, 1);
}

$pdf->Ln(0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'TRATAMIENTOS ODONTOLÓGICO', 1, 1, 'C', 0);
//$pdf->ln(1);
$pdf->SetFont('helvetica', 'B', 7);

foreach ($trata_odo->data as $i => $row) {
    $pdf->ln(0.2);
    $pdf->SetFont('helvetica', '', 6.5);
    $pdf->MultiCell(180, 3, $i + 1 . '.- ' . $row->trata_desc, 'B', 'L', 0, 1);
}





$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'VIII. OTROS EXAMENES', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'OFTALMOLOGIA', 1, 0, 'C');
$pdf->Cell(30, $h, 'SIN CORREGIR', 1, 0);
$pdf->Cell(30, $h, 'CORREGIDO', 1, 0);
$pdf->Cell(0, $h, 'ENFERMEDADES OCULARES', 1, 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, '', 1, 0, 'C');
$pdf->Cell(15, $h, 'OI', 1, 0);
$pdf->Cell(15, $h, 'OD', 1, 0);
$pdf->Cell(15, $h, 'OI', 1, 0);
$pdf->Cell(15, $h, 'OD', 1, 0);
$pdf->Cell(0, $h, ' ', 1, 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'VISION DE LEJOS', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_slejos_izq, 1, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_slejos_der, 1, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_clejos_izq, 1, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_clejos_der, 1, 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(0, $h, 'TEST DE ISHIHARA: ' . $paciente->data[0]->ofta_colo, 1, 1); //


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'VISION DE CERCA', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_scerca_izq, 1, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_scerca_der, 1, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_ccerca_izq, 1, 0);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_ccerca_der, 1, 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'REFLEJOS PUPILARES', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(40, $h, $oftalmologia->data[0]->ofta_refl, 1, 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'DX OFTALMOLOGICA:', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(150, $h, $oftalmologia->data[0]->ofta_cie1, 1, 1);


if (strlen($ex_adicionales->data[0]->esp_diag) > 1) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(30, $h, 'ESPIROMETRIA:', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(150, $h, $ex_adicionales->data[0]->esp_diag, 1, 1);
}
if (strlen($ex_adicionales->data[0]->elec_diag) > 1) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(33, $h, 'ELECTROCARDIOGRAMA', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(147, $h, $ex_adicionales->data[0]->elec_diag, 1, 1);
}

if ($ex_adicionales->data[0]->pes_conid == 4) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(80, $h, 'PRUEBA DE ESFUERZO (IMC > 35 / MAYORES DE 50 AÑOS)', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(100, $h, ': APTO - NEGATIVO A ISQUEMIA', 1, 1);
} else if ($ex_adicionales->data[0]->pes_conid == 5) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(80, $h, 'PRUEBA DE ESFUERZO (IMC > 35 / MAYORES DE 50 AÑOS)', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(100, $h, ': NO APTO', 1, 1);
}


if ($ex_adicionales->data[0]->senso_cond == 7) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(45, $h, 'SENSOMETRICO', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(45, $h, ': APTO', 1, 0);
} else if ($ex_adicionales->data[0]->senso_cond == 8) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(45, $h, 'SENSOMETRICO', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(45, $h, ': NO APTO', 1, 0);
}


if ($ex_adicionales->data[0]->psico_apto == 88) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(45, $h, 'PSICOLOGICO', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(45, $h, ': APTO', 1, 0);
} else if ($ex_adicionales->data[0]->psico_apto == 89) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(45, $h, 'PSICOLOGICO', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(45, $h, ': NO APTO', 1, 0);
}













$pdf->AddPage('P', 'A4');
$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
//$pdf->Image('images/logo.png', 8, 2, 45, '', 'PNG');
$pdf->Ln(14);

$h = 3.5;
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'IX. VALORES AUDIOMETRICOS', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(0, $h, 'EXAMENES AUDIOMETRICO (Registrar la  cifra  de ubicacion en DB(A))', 1, 1, 'C', 0);
$pdf->Cell(20, $h * 3.08, 'VIA', 1, 0, 'C', 0);
$pdf->Cell(0, $h, 'Frecuencia en Hertz(Htz)', 1, 1, 'C');
$pdf->Cell(20, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80, $h, 'OIDO DERECHO', 1, 0, 'C', 0);
$pdf->Cell(80, $h, 'OIDO IZQUIERDO', 1, 1, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'L', 0);
$pdf->Cell(80 / 9, $h, '125', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '250', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '500', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '1000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '2000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '3000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '4000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '6000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '8000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '125', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '250', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '500', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '1000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '2000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '3000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '4000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '6000', 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, '8000', 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(10, $h, 'dB', 'TLR', 0, 'C', 0);
$pdf->Cell(10, $h, 'AREA', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_125, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_250, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_500, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_1000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_2000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_3000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_4000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_6000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_od_8000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_125, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_250, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_500, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_1000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_2000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_3000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_4000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_6000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_aerea->data[0]->audio_a_oi_8000, 1, 1, 'C', 0);
//, , , , , , , , , , , , , , , , , 
//, , , , , , , , , , , , , , , , , 

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(10, $h, '(A)', 'BLR', 0, 'C', 0);
$pdf->Cell(10, $h, 'OSEA', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_125, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_250, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_500, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_1000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_2000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_3000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_4000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_6000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_od_8000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_125, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_250, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_500, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_1000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_2000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_3000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_4000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_6000, 1, 0, 'C', 0);
$pdf->Cell(80 / 9, $h, $audio_osea->data[0]->audio_o_oi_8000, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'OIDO DERECHO', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(140, $h, $audio_aerea->data[0]->audio_a_od_diag, 1, 1, 'C', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'OIDO IZQUIERDO', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(140, $h, $audio_aerea->data[0]->audio_a_oi_diag, 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, '', 'LT', 0, 'L');
//$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(70, $h, 'DERECHO NORMAL', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_od == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(70, $h, 'IZQUIERDO NORMAL', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_oi == '1') ? 'SI' : 'NO'), 1, 1, 'C');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, '', 'L', 0, 'L');
$pdf->Cell(70, $h, 'TRIANGULO DE LUZ', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_od_tria == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(70, $h, 'TRIANGULO DE LUZ', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_oi_tria == '1') ? 'SI' : 'NO'), 1, 1, 'C');


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'TIMPANOS', 'L', 0, 'C');

$pdf->Cell(70, $h, 'PERFORACIONES', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_od_perfora == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(70, $h, 'PERFORACIONES', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_oi_perfora == '1') ? 'SI' : 'NO'), 1, 1, 'C');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, '', 'L', 0, 'L');
$pdf->Cell(70, $h, 'ABOMBAMIENTO', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_od_abomba == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(70, $h, 'ABOMBAMIENTO', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_oi_abomba == '1') ? 'SI' : 'NO'), 1, 1, 'C');


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, '', 'L', 0, 'L');
$pdf->Cell(70, $h, 'CERUMEN', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_od_cerum == '1') ? 'SI' : 'NO'), 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(70, $h, 'CERUMEN', 1, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(10, $h, (($formato7c->data[0]->ficha7c_oi_cerum == '1') ? 'SI' : 'NO'), 1, 1, 'C');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, '', 'OBSERVACION', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(80, '', $formato7c->data[0]->ficha7c_od_obs, 1, 'L', 0, 0);
$pdf->MultiCell(80, '', $formato7c->data[0]->ficha7c_oi_obs, 1, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'AUDICION', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(80, $h, $formato7c->data[0]->ficha7c_od_audic, 1, 'L', 0, 0);
$pdf->MultiCell(80, $h, $formato7c->data[0]->ficha7c_oi_audic, 1, 'L', 0, 1);









$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'X. EXAMEN DE TORAX Y MAMAS', 1, 1, 'L', 1);


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'EXTOSCOPIA: ' . (($formato7c->data[0]->ficha7c_ectos == '1') ? 'CONSERVADA' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_ectos_desc, 'LRB', 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'CORAZON: ' . (($formato7c->data[0]->ficha7c_corazon == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_corazon_desc, 'LRB', 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'PULMONES: ' . (($formato7c->data[0]->ficha7c_pulmon == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_pulmon_desc, 'LRB', 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'MAMA DERECHA: ' . (($formato7c->data[0]->ficha7c_mama == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_mama_de, 'LRB', 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'MAMA IZQUIERDA: ' . (($formato7c->data[0]->ficha7c_mama == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_mama_iz, 'LRB', 'L', 0, 1);





$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XI. SISTEMA MIO OSTEO ARTICULAR', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'MIENBRO SUPERIOR DERECHO: ' . (($formato7c->data[0]->ficha7c_misup_de == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_misup_de_desc, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'MIENBRO SUPERIOR IZQUIERDO: ' . (($formato7c->data[0]->ficha7c_misup_iz == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_misup_iz_desc, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'MIENBRO INFERIOR DERECHO: ' . (($formato7c->data[0]->ficha7c_miinf_de == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_miinf_de_desc, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'MIENBRO INFERIOR IZQUIERDO: ' . (($formato7c->data[0]->ficha7c_miinf_iz == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_miinf_iz_desc, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'REFLEJOS OSTEOTENDINOS: ' . (($formato7c->data[0]->ficha7c_refle == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_refle_desc, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'MARCHA: ' . (($formato7c->data[0]->ficha7c_marcha == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_marcha_desc, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'COLUMNA VERTEBRAL: ' . (($formato7c->data[0]->ficha7c_column == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_column_desc, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XII. ABDOMEN', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'ABDOMEN: ' . (($formato7c->data[0]->ficha7c_abdomen == '1') ? 'NORMAL' : 'HALLAZGO'), 'LRT', 1, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $formato7c->data[0]->ficha7c_abdomen_desc, 'LRB', 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(90, $h, 'DERECHO', 1, 0, 'C');
$pdf->Cell(90, $h, 'IZQUIERDO', 1, 1, 'C');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'PRU SUP', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, (($formato7c->data[0]->ficha7c_pru_sup_de == '1') ? 'SI' : 'NO'), 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'PRU MED', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, (($formato7c->data[0]->ficha7c_pru_med_de == '1') ? 'SI' : 'NO'), 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'PPL', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, (($formato7c->data[0]->ficha7c_ppl_de == '1') ? 'SI' : 'NO'), 1, 0, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'PRU SUP', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, (($formato7c->data[0]->ficha7c_pru_sup_iz == '1') ? 'SI' : 'NO'), 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'PRU MED', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, (($formato7c->data[0]->ficha7c_pru_med_iz == '1') ? 'SI' : 'NO'), 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'PPL', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, (($formato7c->data[0]->ficha7c_ppl_iz == '1') ? 'SI' : 'NO'), 1, 1, 'L');



$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XIII. EVALUACION RADIOGRAFICA', 1, 1, 'L', 1);

$pdf->Cell(0, 45, '', 'LRT');
$pdf->ln(0);
$pdf->Cell(140 / 4 + 40, 45, '', 'LRT');
$pdf->ln(0);
$pdf->Cell(140 / 2 + 40, 45, '', 'LRT');
$pdf->ln(0);
$pdf->Cell(40, 30, $pdf->Image('images/pulmon.png', 18, '', 34, ''), 0, 0, 'C');
$pdf->Cell(140 / 4, $h, '0/0 ', 1, 0, 'C');
$pdf->Cell(140 / 4, $h, '1/0 ', 1, 0, 'C');
$pdf->Cell(140 / 8, $h, '1/1,1/2', 1, 0, 'C');
$pdf->Cell(140 / 8, $h, '2/1,2/2,2/3 ', 1, 0, 'C');
$pdf->Cell(140 / 8, $h, '3/2,3/3,3/+', 1, 0, 'C');
$pdf->Cell(140 / 8, $h, 'A,B,C ', 1, 1, 'C');

$pdf->Cell(40, $h, ' ', 0, 0, 'L');
$pdf->Cell(140 / 4, $h, 'CERO ', 1, 0, 'C');
$pdf->Cell(140 / 4, $h, '1/0 ', 1, 0, 'C');
$pdf->Cell(140 / 8, $h, 'UNO', 1, 0, 'C');
$pdf->Cell(140 / 8, $h, 'DOS ', 1, 0, 'C');
$pdf->Cell(140 / 8, $h, 'TRES', 1, 0, 'C');
$pdf->Cell(140 / 8, $h, 'CUATRO', 1, 1, 'C');

$pdf->Cell(40, $h, ' ', 0, 0, 'L');
$pdf->MultiCell(140 / 4, 29.8 - $h * 2, 'Sin Neumoconiosis  "NORMAL"', 'LRT', 'C', 0, 0);
$pdf->MultiCell(140 / 4, 29.8 - $h * 2, 'Imagen Radioografica de Exposicion a Polvo "SOSPECHA"', 'LRT', 'C', 0, 0);
$pdf->Cell(0, 30 - $h * 2, '"CON NEUMOCONIOSIS"', 'LRT', 1, 'C');

$pdf->Cell(20, $h, 'N° RX', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_placa, 1, 1, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'FECHA', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_lector, 1, 1, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'CALIDAD', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_calid, 1, 1, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'SIMBOLO', 1, 0, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_profu, 1, 1, 'C');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(50, $h, 'EL ESTUDIO REALIZADO MOSTRO', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(130, $h, ': ' . $rayosx->data[0]->rayo_inf_mostro, 1, 1, 'L');



if ($formato7c->data[0]->ficha7c_tacto == '1')
    $tacto = 'NO SE HIZO';
else if ($formato7c->data[0]->ficha7c_tacto == '2')
    $tacto = 'NORMAL';
else if ($formato7c->data[0]->ficha7c_tacto == '3')
    $tacto = 'ANORMAL';
else if ($formato7c->data[0]->ficha7c_tacto == '4')
    $tacto = 'HALLAZGOS';


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XIV. TACTO RECTAL', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(180, $h, $tacto . " - " . $formato7c->data[0]->ficha7c_tacto_desc, 1, 1, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'ANILLOS INGUINALES', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7); //+
$pdf->Cell(150, $h, (($formato7c->data[0]->ficha7c_anill == '1') ? 'NORMAL' : 'HALLAZGO') . " - " . $formato7c->data[0]->ficha7c_anill_desc, 1, 1, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'HERNIAS', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(70, $h, (($formato7c->data[0]->ficha7c_hernia == '1') ? 'SI' : 'NO') . " - " . $formato7c->data[0]->ficha7c_hernia_desc, 1, 0, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'VARICES', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(70, $h, (($formato7c->data[0]->ficha7c_varic == '1') ? 'SE EVIDENCIA' : 'NO SE EVIDENCIA') . " - " . $formato7c->data[0]->ficha7c_varic_desc, 1, 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'GENITALES', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(70, $h, (($formato7c->data[0]->ficha7c_genit == '1') ? 'NORMAL' : 'HALLAZGO') . " - " . $formato7c->data[0]->ficha7c_gemit_desc, 1, 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'GANGLIOS ', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(70, $h, (($formato7c->data[0]->ficha7c_gangli == '1') ? 'NORMAL' : 'HALLAZGO') . " - " . $formato7c->data[0]->ficha7c_gangli_desc, 1, 1);


















$pdf->AddPage('P', 'A4');
$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
//$pdf->Image('images/logo.png', 8, 2, 45, '', 'PNG');
$pdf->Ln(14);


$h = 4.5;
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XV. RESULTADOS DE LABORATORIO', 1, 1, 'L', 1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'PERFIL LIPIDICO', 1, 1, 'L');


$lab1 = $model->laboratorio($_REQUEST['adm'], '87');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(23, $h, 'COLESTEROL', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, ': ' . $lab1->data[0]->lab1_desc1 . ' mg/dl', 1, 0, 'L');

$lab2 = $model->laboratorio($_REQUEST['adm'], '96');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'HDL', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(18, $h, ': ' . $lab2->data[0]->lab1_desc1 . ' mg/dl', 1, 0, 'L');

$lab3 = $model->laboratorio($_REQUEST['adm'], '97');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'LDL', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(18, $h, ': ' . $lab3->data[0]->lab1_desc1 . ' mg/dl', 1, 0, 'L');

$lab4 = $model->laboratorio($_REQUEST['adm'], '86');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(23, $h, 'TRIGLICERIDOS', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, ': ' . $lab4->data[0]->lab1_desc1 . ' mg/dl', 1, 0, 'L');

$lab5 = $model->laboratorio($_REQUEST['adm'], '91');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(23, $h, 'GLUCOSA', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, ': ' . $lab5->data[0]->lab1_desc1 . ' mg/dl', 1, 1, 'L');


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'SERIE BLANCA', 1, 1, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'MONOCITOS', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, ': ' . $lab_hemo->data[0]->lab3_hem_mono_r . '%', 1, 0, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'LINFOCITOS', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, ': ' . $lab_hemo->data[0]->lab3_hem_linf_r . '%', 1, 0, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'EOSINÓFILOS', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, ': ' . $lab_hemo->data[0]->lab3_hem_eosi_r . '%', 1, 1, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'ABASTONADOS', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, ': ' . $lab_hemo->data[0]->lab3_hem_abas_r . '%', 1, 0, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'BASÓFILOS', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, ': ' . $lab_hemo->data[0]->lab3_hem_baso_r . '%', 1, 0, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'SEGMENTADOS', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, ': ' . $lab_hemo->data[0]->lab3_hem_neut_r . '%', 1, 1, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'HEMOGLOBINA', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, ': ' . $lab_hemo->data[0]->lab3_hem_hglo_r . '', 1, 0, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'HEMATOCRITO', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, ': ' . $lab_hemo->data[0]->lab3_hem_htoc_r . '', 1, 0, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(50, $h, 'REACCIONES SERIALOGICAS LUES', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, $lab_rpr->data[0]->lab1_desc1, 1, 1, 'L');


if ($paciente->data[0]->sexo == 'MASCULINO') {
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(90, $h, '', 1, 0);
} ELSE {
    $lab6 = $model->laboratorio($_REQUEST['adm'], '119');
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(45, $h, 'SUB UNIDAD BETA', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(45, $h, $lab6->data[0]->lab1_desc1, 1, 0, 'L');
}


$lab7 = $model->laboratorio($_REQUEST['adm'], '164');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(45, $h, 'GRUPO Y FACTOR Rh', 1, 0, 'L');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(45, $h, $lab7->data[0]->lab1_desc1, 1, 1, 'L');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'ORINA : ' . $validacion->data[0]->val_orina, 1, 1, 'L');






$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XVI. DESCRIPCIÓN DE NO APTITUD', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $validacion->data[0]->val_no_aptitud, 1, 'L', 0, 1);




$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XVII. RESTRICCIONES', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $validacion->data[0]->val_rectric, 1, 'L', 0, 1);




$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XVIII. OBSERVACIONES', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, 0, $validacion->data[0]->val_obser, 1, 'L', 0, 1);




$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XIX. DIAGNOSTICOS, INTECONSULTA Y CONTROLES', 1, 1, 'L', 1);


$h = 3;
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h + 2, 'N°', 1, 0, 'C');
$pdf->Cell(95, $h + 2, 'DIAGNOSTICO', 1, 0, 'C');
$pdf->Cell(5, $h + 2, '', 0, 0, 'C');
$pdf->Cell(5, $h + 2, 'N°', 1, 0, 'C');
$pdf->Cell(70, $h + 2, 'INTECONSULTAS Y CONTROLES', 1, 1, 'C');


$total_diag = $diagnostico->total;
$total_inter = $interconsulta->total;
$h = 3.5;
if ($total_diag >= $total_inter) {
    for ($index = 0; $index < $total_diag; $index++) {
        $pdf->SetFont('helvetica', '', 6);
        $pdf->MultiCell(100, $h, $index + 1 . '.- ' . $diagnostico->data[$index]->diag_desc, 'B', 'L', 0, 0);

        if ($index < $total_inter) {
            $pdf->Cell(5, $h, '', 0, 0, 'C');
            $pdf->Cell(75, $h, $index + 1 . '.- ' . $interconsulta->data[$index]->inter, 'B', 0, 'L');
        } else {
            $pdf->Cell(90, $h + 2, '', 0, 0, 'C');
        }
        $pdf->ln($h);
    }
} else if ($total_inter > $total_diag) {
    for ($index = 0; $index < $total_inter; $index++) {
        $pdf->SetFont('helvetica', '', 6);

        if ($index < $total_diag) {
            $pdf->MultiCell(100, $h, $index + 1 . '.- ' . $diagnostico->data[$index]->diag_desc, 'B', 'L', 0, 0);
        } else {
            $pdf->Cell(100, $h, '', 0, 0, 'C');
        }
        $pdf->Cell(5, $h, '', 0, 0, 'C');
        $pdf->Cell(75, $h, $index + 1 . '.- ' . $interconsulta->data[$index]->inter, 'B', 0, 'L');

        $pdf->ln($h);
    }
}




$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XX. RECOMENDACIONES', 1, 1, 'L', 1);

$pdf->ln(1);
$pdf->SetFont('helvetica', 'B', 7);

foreach ($recomendaciones->data as $i => $row) {
    $pdf->ln(1);
    $pdf->SetFont('helvetica', '', 6);
    $pdf->MultiCell(180, $h, $i + 1 . '.- ' . $row->reco_desc, 'B', 'L', 0, 1);
}

$pdf->Ln(2.5);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XXI. APTITUD LABORAL', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', 7 + 10);
$pdf->MultiCell(0, $h, $validacion->data[0]->val_aptitud, 1, 'C', 0, 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'TIEMPO:', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, $validacion->data[0]->val_tiempo, 1, 0, 'C', 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'FECHA INICIO:', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, $validacion->data[0]->val_fech_ini, 1, 0, 'C', 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'FECHA TERMINO:', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, $h, $validacion->data[0]->val_fech_fin, 1, 1, 'C', 0);



$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(180, $h, 'XXII. EN CASOS DE ACCIDENTES:', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(25, $h, 'CONTACTAR A:', 1, 'C', 0, 0);

$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(60, $h, $validacion->data[0]->val_emer_contac, 1, 'L', 0, 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(20, $h, 'PARENTESCO:', 1, 'C', 0, 0);

$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(25, $h, $validacion->data[0]->val_emer_parente, 1, 'C', 0, 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(20, $h, 'CELULAR:', 1, 'C', 0, 0);

$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(30, $h, $validacion->data[0]->val_emer_cell, 1, 'C', 0, 1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(30, $h, 'MEDICACIÓN ACTUAL:', 1, 'C', 0, 0);

$pdf->SetFont('helvetica', $validacion->data[0]->val_emer_medic, 7);
$pdf->MultiCell(65, $h, '', 1, 'C', 0, 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(20, $h, 'DIRECCIÓN:', 1, 'C', 0, 0);

$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(65, $h, $validacion->data[0]->val_emer_direc, 1, 'C', 0, 1);

$pdf->Output('Formato_7c_' . $_REQUEST['adm'] . '.PDF', 'I');
