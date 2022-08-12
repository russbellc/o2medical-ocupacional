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
$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT, 2);
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
$anexo312 = $model->mod_medicina_312($_REQUEST['adm']);

// $triaje312 = $model->triaje312($_REQUEST['adm']);
// $oftalmo = $model->oftalmo($_REQUEST['adm']);
// $ficha_312 = $model->ficha_312($_REQUEST['adm']);
// $recomendaciones = $model->recomendaciones($_REQUEST['adm']);
// $validacion = $model->validacion($_REQUEST['adm']);
// $diagnostico = $model->diagnostico($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */






$pdf->AddPage('P', 'A4');
//OPTIMA
// $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');

//CLINICA O2
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');
// $pdf->Image('images/formato/contactos_o2.jpg', 148, 5, 50, '', 'JPEG');

$pdf->Ln(1);
$h = 4.5;
$titulo = 7;
$texto = 7;
$salto = 2;

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(0, 0, 'ANEXO N° 02', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 0, 'HISTORIA CLÍNICA MÉDICA OCUPACIONAL', 0, 1, 'C');
$pdf->Ln(3);
$pdf->SetFillColor(194, 217, 241);


$pdf->Cell(180, $h * 2, '', 1, 0, 'L');
$pdf->Ln(0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'N° de Ficha Médica:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(15, $h, $_REQUEST['adm'], 0, 0, 'L');
$pdf->Cell(15, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'Tipo de Evaluacion', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, $paciente->data[0]->tipo, 0, 0, 'L');
$pdf->Cell(20, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, 'Fecha:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(35, $h, $paciente->data[0]->fech_reg, 0, 1, 'C');


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'Lugar de Examen:', 0, 0, 'L');
$pdf->Cell(25, $h, 'Departamento:', 0, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, 'CUSCO', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'Provincia:', 0, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, 'CUSCO', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'Distrito:', 0, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(30, $h, 'SANTIAGO', 0, 1, 'L'); //


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'I. DATOS DE LA EMPRESA', 1, 1, 'L', 1);

$pdf->Cell(180, $h * 4, '', 1, 0, 'L');
$pdf->Ln(0);


$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'Razón Social:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(150, $h, $paciente->data[0]->emp_desc . ' RUC:' . $paciente->data[0]->emp_id, 0, 1, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h, 'Actividad Económica', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(150, $h, '-', 0, 1, 'L');

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'Lugar de Trabajo', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(150, $h, $paciente->data[0]->emp_direc, 0, 1, 'L');


// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(20, $h, 'Ubicación', 1, 0, 'L');
// $pdf->Cell(30, $h, 'Departamento', 1, 0, 'L');
// $pdf->SetFont('helvetica', '', $titulo);
// $pdf->Cell(25, $h, $paciente->data[0]->depa, 1, 0, 'C');
// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(25, $h, 'Provincia', 1, 0, 'L');
// $pdf->SetFont('helvetica', '', $titulo);
// $pdf->Cell(25, $h, $paciente->data[0]->prov, 1, 0, 'C');
// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(25, $h, 'Distrito', 1, 0, 'L');
// $pdf->SetFont('helvetica', '', $titulo);
// $pdf->Cell(30, $h, $paciente->data[0]->dist, 1, 1, 'C');

//adm_puesto,adm_area

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(33, $h, 'Puesto al que Postula:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(62, $h, $paciente->data[0]->adm_puesto, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'Area de trabajo:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(60, $h, $paciente->data[0]->adm_area, 0, 1, 'L');



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'II. FILIACION DEL TRABAJADOR', 1, 1, 'L', 1);


//-----------------------------------------------------------//
$pdf->Cell(180, $h * 10, '', 1, 0, 'L');
$pdf->Ln(0);

$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'Nombres y Apellidos:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(100, $h, $paciente->data[0]->nom_ap, 0, 0, 'L');
$pdf->Cell(45, $h * 10, 'FOTO', 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 1, 'L');

// if ($paciente->data[0]->pac_foto == '1') {
//     $foto = $model->foto($paciente->data[0]->pac_id);
//     $data = base64_decode($foto->data[0]->foto_desc);
//     $pdf->Image('@' . $data, 150.5, 79.2, 44, 53);
// }

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(28, $h, 'Fecha de Nacimiento:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(30, $h, $paciente->data[0]->fech_naci, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'Edad:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, $paciente->data[0]->edad . ' AÑOS', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'Sexo:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, $paciente->data[0]->sexo, 0, 1, 'L');

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'Nro de Documento de Identidad:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(30, $h, $paciente->data[0]->pac_ndoc, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(18, $h, 'Estado Civil:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(30, $h, $paciente->data[0]->ecivil, 0, 1, 'L');



$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'Domicilio Actual:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(110, $h, $paciente->data[0]->direc, 0, 1, 'L');



$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'Departamento:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(27, $h, $paciente->data[0]->depa_ubigeo, 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'Provincia:',  0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(26, $h, $paciente->data[0]->prov_ubigeo, 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'Distrito:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(27, $h, $paciente->data[0]->dist_ubigeo,  0, 1, 'L');;



$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(38, $h, 'Residensia en Lugar Trabajo:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(20, $h, $anexo312->data[0]->m_312_residencia, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(52, $h, 'Tiempo de residencia en lugar de trabajo:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(20, $h, $anexo312->data[0]->m_312_tiempo . ' Años', 0, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L');

$val_01 = '';
$val_02 = '';
$val_03 = '';
$val_04 = '';
$val_05 = '';
$val_06 = '';
switch ($anexo312->data[0]->m_312_seguro) {
    case 'ESSALUD':
        $val_01 = 'X';
        break;
    case 'EPS':
        $val_02 = 'X';
        break;
    case 'SCTR':
        $val_03 = 'X';
        break;
    case 'SIS':
        $val_04 = 'X';
        break;
    case 'OTRO':
        $val_05 = 'X';
        break;
    case 'NIEGA':
        $val_06 = 'X';
        break;

    default:
        # code...
        break;
}

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'ESSALUD:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, $val_01, 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'EPS:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, $val_02, 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'SCTR:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, $val_03, 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'SIS:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, $val_04, 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OTRO:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, $val_05, 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'NIEGA:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, $val_06, 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 1, 'L');



$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(28, $h, 'Grado de instruccion:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(45, $h, $paciente->data[0]->ginstruccion, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'Profecion:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(42, $h, $paciente->data[0]->pac_profe, 0, 1, 'L');


$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(28, $h, 'Correo Electronico:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(47, $h, $paciente->data[0]->pac_correo, 0, 0, 'C');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(13, $h, 'Celular:', 10, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(30, $h, $paciente->data[0]->pac_cel, 0, 1, 'C');


$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'N° Total de Hijos Vivos:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(40, $h, $anexo312->data[0]->m_312_nhijos, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'N° Dependientes:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(35, $h, $anexo312->data[0]->m_312_dependiente, 0, 1, 'L');



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'III. ANTECEDENTES OCUPACIONALES', 1, 1, 'L', 1);




$h = 4.5;
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h + 2, 'N°', 1, 0, 'C');
$pdf->Cell(30, $h + 2, 'EMPRESA', 1, 0, 'C');
$pdf->Cell(35, $h + 2, 'ÁREA DE TRABAJO', 1, 0, 'C');
$pdf->Cell(35, $h + 2, 'OCUPACIÓN', 1, 0, 'C');
$pdf->MultiCell(18, $h + 2, 'FECHA INICIO-FIN', 1, 'C', 0, 0);
$pdf->Cell(14, $h + 2, 'TIEMPO', 1, 0, 'C');
$pdf->MultiCell(30, $h + 2, 'EXPOSICION OCUPACIONAL', 1, 'C', 0, 0);
$pdf->Cell(13, $h + 2, 'EPP', 1, 1, 'C');

$h = 3.7;
$ante_ocupa = $model->mod_medicina_antece($_REQUEST['adm']);
foreach ($ante_ocupa->data as $i => $row) {
    $pdf->SetFont('helvetica', '', 5.5);
    $pdf->Cell(5, $h * 2, $i + 1, 1, 0, 'C');
    $pdf->MultiCell(30, $h * 2, $row->m_antec_empresa, 1, 'C', 0, 0);
    $pdf->MultiCell(35, $h * 2, $row->m_antec_empresa, 1, 'C', 0, 0);
    $pdf->MultiCell(35, $h * 2, $row->m_antec_proyec, 1, 'C', 0, 0);

    $pdf->MultiCell(18, $h * 2, $row->m_antec_fech_ini . ' ' . $row->m_antec_fech_fin, 1, 'C', 0, 0);
    $pdf->MultiCell(14, $h * 2, calculaIntervalo($row->m_antec_fech_ini, $row->m_antec_fech_fin), 1, 'C', 0, 0);
    $pdf->MultiCell(30, $h * 2, $row->m_antec_fisico . ' - ' . $row->m_antec_quinico . ' - ' . $row->m_antec_biologico . ' - ' . $row->m_antec_ergonom . ' - ' . $row->m_antec_otros  . ' - ' . $row->m_antec_obser, 1, 'C', 0, 0);
    $pdf->MultiCell(13, $h * 2, '', 1, 'C', 0, 1);
}

function calculaIntervalo($date1, $date2)
{
    $diff = abs(strtotime($date2) - strtotime($date1));
    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    // $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
    return $years . ' años y ' . $months . ' meses '; //. $days . ' días'
}
$h = 4.5;


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'IV. ANTECEDENTES PATOLÓGICOS PERSONALES', 1, 1, 'L', 1);




//-----------------------------------------------------------//
$pdf->Cell(180, $h * 14, '', 'LR', 0, 'L');
$pdf->Ln(2);

$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, 'IMA', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_ima == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'HTA', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_hta == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'ACV', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_acv == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'TBC', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_tbc == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'ETS', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_ets == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 1, 'L');

//-----------------------------------------------------------//
// $pdf->Ln(2);
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, 'VIH', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_vih == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'TEC', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_tec == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Alergias', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_alergias == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Asma', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_asma == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Bronquitis', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_bronquitis == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 1, 'L');

//-----------------------------------------------------------//
// $pdf->Ln(2);
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, 'Diabetes', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_diabetes == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Hepatitis AB', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_hepatitis == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Hernia', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_hernia == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Lumbalgia', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_lumbalgia == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Tifoidea', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_tifoidea == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 1, 'L');

//-----------------------------------------------------------//
// $pdf->Ln(2);
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, 'Neoplasias', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_neoplasias == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Quemaduras', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_quemaduras == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Discopatias', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_discopatias == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Convulciones', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_convulciones == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Gastritis', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_gastritis == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 1, 'L');

//-----------------------------------------------------------//
// $pdf->Ln(2);
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, 'Ulceras', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_ulceras == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Enf. Psiquiatrico', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_enf_psiquia == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Enf. Cardio', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_enf_cardio == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Enf. Ocular', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_enf_ocular == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Enf. Reumatismo', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_enf_reuma == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 1, 'L');

//-----------------------------------------------------------//
// $pdf->Ln(2);
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, 'Enf. Pulmon', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_enf_pulmon == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Alt piel', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_alt_piel == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Tendinitis', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_tendinitis == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Fractura', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_fractura == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Anemia', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_anemia == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 1, 'L');

//-----------------------------------------------------------//
// $pdf->Ln(2);
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(25, $h, 'Obesidad', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_obesidad == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Dislipidemia', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_dislipidem == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Intoxicación', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_intoxica == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Cirugia', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_cirugia == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 0, 'L');

$pdf->Cell(25, $h, 'Otros', 0, 0, 'R');
$pdf->Cell(5, $h, (($anexo312->data[0]->m_312_pato_otros == 1) ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(5, $h, '', 0, 1, 'L');

$pdf->Ln(2);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->MultiCell(170, $h, 'ALERGIAS:  ' . (($anexo312->data[0]->m_312_pato_alergias == 1) ? $anexo312->data[0]->m_312_pato_alergias_desc : 'NIEGA'), 'B', 'L', 0, 1);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->MultiCell(170, $h, 'CIRUGIAS:  ' . (($anexo312->data[0]->m_312_pato_cirugia == 1) ? $anexo312->data[0]->m_312_pato_alergias_desc : 'NIEGA'), 'B', 'L', 0, 1);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->MultiCell(170, $h, 'OBSERVACIONES:  ' . $anexo312->data[0]->m_312_pato_observaciones, 'B', 'L', 0, 1);

if ($anexo312->data[0]->m_312_pato_tbc == 1) {

    //-----------------------------------------------------------//
    $pdf->Ln(2);
    $pdf->Cell(5, $h, '', 0, 0, 'L');

    $pdf->SetFont('helvetica', 'B', $titulo);
    $pdf->Cell(40, $h, 'SOLO SI TUBO TUBERCULOSIS:', 0, 0, 'L');

    $pdf->Cell(5, $h, '', 0, 0, 'L');

    $pdf->Cell(45, $h, 'FECHA QUE TUVO TUBERCULOSIS:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', $titulo);
    $pdf->Cell(15, $h, $anexo312->data[0]->m_312_pato_tbc_fecha, 0, 1, 'C');
    $pdf->Cell(5, $h, '', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', $titulo);
    $pdf->MultiCell(170, $h, '¿COMPLETO SU TRATAMIENTO?:  ' . $anexo312->data[0]->m_312_pato_tbc_tratamiento, 'B', 'L', 0, 1);
}

// $pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'V. ABSENTISMO (Asociado a trabajo o no)', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h + 2, 'N°', 1, 0, 'C');
$pdf->Cell(90, $h + 2, 'ENFERMEDAD O ACCIDENTE', 1, 0, 'C');
$pdf->Cell(40, $h + 2, 'ASOCIADOS AL TRABAJO', 1, 0, 'C');
$pdf->Cell(15, $h + 2, 'AÑO', 1, 0, 'C');
$pdf->Cell(30, $h + 2, 'DÍAS DE DESCANSO', 1, 1, 'C');

// foreach ($ante_ocupa->data as $i => $row) {
//     $pdf->SetFont('helvetica', '', 6.5);
//     $pdf->Cell(5, $h, $i + 1, 1, 0, 'C');
//     $pdf->Cell(105, $h, $row->ante_accidente, 1, 0, 'C');
//     $pdf->Cell(25, $h, $row->ante_trab, 1, 0, 'C');
//     $pdf->Cell(15, $h, $row->ante_ano, 1, 0, 'C');
//     $pdf->Cell(30, $h, $row->ante_descanso, 1, 1, 'C');
// }


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'VI. HÁBITOS NOCIVOS', 1, 1, 'L', 1);

//-----------------------------------------------------------//
$pdf->Cell(180, $h * 6, '', 'LBR', 0, 'L');
$pdf->Ln(2);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(35, $h, 'HÁBITOS NOCIVOS', 1, 0, 'C');
$pdf->Cell(40, $h, 'TIPO', 1, 0, 'C');
$pdf->Cell(55, $h, 'CANTIDAD', 1, 0, 'C');
$pdf->Cell(40, $h, 'FRECUENCIA', 1, 1, 'C');


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(35, $h, 'ALCOHOL', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(40, $h, $anexo312->data[0]->m_312_alcohol_tipo, 1, 0, 'C');
$pdf->Cell(55, $h, $anexo312->data[0]->m_312_alcohol_cantidad, 1, 0, 'C');
$pdf->Cell(40, $h, $anexo312->data[0]->m_312_alcohol_fre, 1, 1, 'C');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(35, $h, 'TABACO', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(40, $h, $anexo312->data[0]->m_312_tabaco_tipo, 1, 0, 'C');
$pdf->Cell(55, $h, $anexo312->data[0]->m_312_tabaco_cantidad, 1, 0, 'C');
$pdf->Cell(40, $h, $anexo312->data[0]->m_312_tabaco_fre, 1, 1, 'C');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(35, $h, 'DROGAS', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(40, $h, $anexo312->data[0]->m_312_drogas_tipo, 1, 0, 'C');
$pdf->Cell(55, $h, $anexo312->data[0]->m_312_drogas_cantidad, 1, 0, 'C');
$pdf->Cell(40, $h, $anexo312->data[0]->m_312_drogas_fre, 1, 1, 'C');

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(35, $h, 'MEDICAMENTOS', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(135, $h, $anexo312->data[0]->m_312_medicamentos, 1, 1, 'L');










$pdf->AddPage('P', 'A4');
//OPTIMA
// $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');

//CLINICA O2
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');

$pdf->Ln(9);
$pdf->Cell(180, $h, '', 1, 0, 'L');
$pdf->Ln(0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'PACIENTE:', 0, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(125, $h, $paciente->data[0]->apellidos . ', ' . $paciente->data[0]->nombre, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'N° DE FICHA MÉDICA:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, $_REQUEST['adm'], 0, 1, 'L');



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'VII. ANTECEDENTES PATOLÓGICOS FAMILIARES', 1, 1, 'L', 1);

//-----------------------------------------------------------//
$pdf->Cell(180, $h * 5, '', 'LBR', 0, 'L');
$pdf->Ln(2);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ESTADO DEL PADRE', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(140, $h, $anexo312->data[0]->m_312_padre, 1, 1, 'L');

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ESTADO DE LA MADRE', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(140, $h, $anexo312->data[0]->m_312_madre, 1, 1, 'L');

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ESTADO DEL CONYUGE', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(140, $h, $anexo312->data[0]->m_312_conyuge, 1, 1, 'L');

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'N° DE HIJOS VIVOS', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(55, $h, $anexo312->data[0]->m_312_conyuge, 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'N° DE HIJOS FALLECIDOS', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(50, $h, $anexo312->data[0]->m_312_conyuge, 1, 1, 'L');
$pdf->Ln(2);




$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'VIII. EVALUACIÓN MÉDICA', 1, 1, 'L', 1);

//-----------------------------------------------------------//
$pdf->Cell(180, $h * 46, '', 'LR', 0, 'L');
$pdf->Ln(1);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ANAMNESIS:', 0, 1, 'L');
$pdf->SetFont('helvetica', '', $titulo);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->MultiCell(170, $h, $anexo312->data[0]->m_312_anamnesis, 0, 'L', 0, 1);
// $pdf->Ln(2);



$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(170, $h, 'EXAMEN CLÍNICO', 1, 1, 'L');

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'TALLA(m)', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(15, $h, $triaje312->data[0]->tri_talla, 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'PESO(Kg)', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(15, $h, $triaje312->data[0]->tri_peso, 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'IMC Kg/m²', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(15, $h, $triaje312->data[0]->tri_img, 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'PERIMETRO ABDOMINAL (cm.)', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(30, $h, $triaje312->data[0]->tri_ptorax, 1, 1, 'L');

$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'F. RESP (X min)', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(15, $h, $triaje312->data[0]->tri_fr, 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'F. CARD (X min)', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(15, $h, $triaje312->data[0]->tri_fc, 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'PA', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(15, $h, $triaje312->data[0]->tri_pa, 1, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'TEMPERATURA (°C)', 1, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(15, $h, $triaje312->data[0]->tri_temp, 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', $titulo);
if ($paciente->data[0]->sexo == 'FEMENINO') {
    $pdf->Cell(10, $h, 'FUR:', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', $titulo);
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_pa, 1, 1, 'L');
} else {
    $pdf->Cell(25, $h, '', 1, 1, 'C');
}


$pdf->Ln(1);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ECTOSCOPÍA:', 0, 1, 'L');
$pdf->SetFont('helvetica', '', $titulo);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->MultiCell(170, $h, $anexo312->data[0]->m_312_ectoscopia, 'B', 'L', 0, 1);

$pdf->Ln(1);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ESTADO MENTAL:', 0, 1, 'L');
$pdf->SetFont('helvetica', '', $titulo);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->MultiCell(170, $h, $anexo312->data[0]->m_312_est_mental, 'B', 'L', 0, 1);


$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(170, $h, 'EXAMEN FISICO', 1, 1, 'L');

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(25, $h * 4, 'OJOS Y ANEXOS', 1, 0, 'C');
$pdf->Cell(25, $h * 2, 'AGUDEZA VISUAL', 1, 0, 'C');
$pdf->Cell(30, $h, 'SIN CORREGIR', 1, 0, 'C');
$pdf->Cell(30, $h, 'CORREGIDA', 1, 0, 'C');
$pdf->Cell(35, $h, 'VISION DE PROFUNDIDAD', 1, 0, 'C');
$pdf->Cell(25, $h, 'NORMAL', 1, 1, 'L');                //==================>VALUE

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(25, $h, '', 0, 0, 'C');
$pdf->Cell(25, $h, '', 0, 0, 'C');
$pdf->Cell(15, $h, 'OD', 1, 0, 'C');
$pdf->Cell(15, $h, 'OI', 1, 0, 'C');
$pdf->Cell(15, $h, 'OD', 1, 0, 'C');
$pdf->Cell(15, $h, 'OI', 1, 0, 'C');
$pdf->Cell(35, $h, 'VISION DE COLORES', 1, 0, 'C');
$pdf->Cell(25, $h, 'NORMAL', 1, 1, 'L');                //==================>VALUE

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(25, $h, '', 0, 0, 'C');
$pdf->Cell(25, $h, 'VISION DE LEJOS', 1, 0, 'C');
$pdf->Cell(15, $h, 'OD', 1, 0, 'C');                    //==================>VALUE
$pdf->Cell(15, $h, 'OI', 1, 0, 'C');                    //==================>VALUE
$pdf->Cell(15, $h, 'OD', 1, 0, 'C');                    //==================>VALUE
$pdf->Cell(15, $h, 'OI', 1, 0, 'C');                    //==================>VALUE
$pdf->Cell(35, $h, 'FONDO DE OJO', 1, 0, 'C');
$pdf->Cell(25, $h, 'NORMAL', 1, 1, 'L');                //==================>VALUE

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(25, $h, '', 0, 0, 'C');
$pdf->Cell(25, $h, 'VISION DE CERCA', 1, 0, 'C');
$pdf->Cell(15, $h, 'OD', 1, 0, 'C');                    //==================>VALUE
$pdf->Cell(15, $h, 'OI', 1, 0, 'C');                    //==================>VALUE
$pdf->Cell(15, $h, 'OD', 1, 0, 'C');                    //==================>VALUE
$pdf->Cell(15, $h, 'OI', 1, 0, 'C');                    //==================>VALUE
$pdf->Cell(35, $h, 'EXTERNO', 1, 0, 'C');
$pdf->Cell(25, $h, 'NORMAL', 1, 1, 'L');                //==================>VALUE


$pdf->Ln(3);
$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(170, $h, 'EXAMEN FISICO', 1, 1, 'L');

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h, '', 1, 0, 'C');
$pdf->Cell(25, $h, 'SIN HALLAZGOS', 1, 0, 'C');
$pdf->Cell(115, $h, 'DESCRIPCIÓN DE ALTERACIONES', 1, 1, 'C');

//-----------------------------------------------------------//
$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 26, '', 'LR', 0, 'C');
$pdf->Cell(25, $h * 26, '', 'LR', 0, 'C');
$pdf->Cell(115, $h * 26, '', 'LR', 0, 'C');
$pdf->Ln(0);
//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_piel == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'PIEL', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');                //==================>VALUE
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_piel : '', 'TRL', 'L', 0, 1); //==================>VALUE



//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_cabeza == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'CABEZA', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_cabeza : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_oidos == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'OIDOS', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_oidos : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_nariz == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'NARIZ', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_nariz : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_boca == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'BOCA', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_boca : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_faringe == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'FARINGE', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_faringe : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_cuello == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'CUELLO', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_cuello : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_respiratorio == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'AP. RESPIRATORIO', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_respiratorio : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_cardiovascular == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'AP. CARDIOVASCULAR', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_cardiovascular : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_digestivo == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'AP. DIGESTIVO', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_digestivo : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_genitou == 'DIFERIDO') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'AP. GENITOURINARIO', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_genitou : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_locomotor == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'AP. LOCOMOTOR', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_locomotor : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_marcha == 'CONSERVADO') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'MARCHA', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_marcha : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_columna == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'COLUMNA', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_columna : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_mi_superi == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'MIEMBRO SUPERIOS', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_mi_superi : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_mi_inferi == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'MIEMBRO INFERIOR', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_mi_inferi : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_linfatico == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'SIS. LIMFATICO', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_linfatico : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_nervio == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'SIS. NERVIOSO', 'TRL', 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 'T', 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_nervio : '', 'TRL', 'L', 0, 1);

//VARIABLES
$valx = '';
if ($anexo312->data[0]->m_312_osteomuscular == 'NORMAL') {
    $valx = 'X';
} else {
    $valx = '';
}

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->Cell(30, $h * 1.39, 'SIS. OSTEOMUSCULAR', 1, 0, 'C');
$pdf->Cell(25, $h * 1.39, $valx, 1, 0, 'C');
$pdf->MultiCell(115, $h * 1.39, ($valx == '') ? $anexo312->data[0]->m_312_osteomuscular : '', 1, 'L', 0, 1);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->MultiCell(170, $h, 'OBSERVACIONES:  ' . $anexo312->data[0]->m_312_ef_observaciones, 1, 'L', 0, 1);











$pdf->AddPage('P', 'A4');
//OPTIMA
// $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');

//CLINICA O2
$pdf->Image('images/formato/logo_o2.jpg', 15, 5, 55, '', 'JPEG');

$pdf->Ln(9);
$pdf->Cell(180, $h, '', 1, 0, 'L');
$pdf->Ln(0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'PACIENTE:', 0, 0, 'C');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(125, $h, $paciente->data[0]->apellidos . ', ' . $paciente->data[0]->nombre, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'N° DE FICHA MÉDICA:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, $_REQUEST['adm'], 0, 1, 'L');



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'IX. CONCLUSION DE EVALUACIÓN PSICOLÓGICA', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, $h, '       ' . $anexo312->data[0]->m_312_conclu_psico, 1, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'X. CONCLUSIONES RADIOGRÁFICAS', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, $h, '       ' . $anexo312->data[0]->m_312_conclu_rx, 1, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'XI. CONCLUSIONES PATOLÓGICAS DE LABORATORIO', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, $h, '       ' . $anexo312->data[0]->m_312_conclu_lab, 1, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'XII. CONCLUSIONES AUDIOMETRIA', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, $h, '       ' . $anexo312->data[0]->m_312_conclu_audio, 1, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'XIII. CONCLUSIONES ESPIROMETRÍA', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, $h, '       ' . $anexo312->data[0]->m_312_conclu_espiro, 1, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'XIV. OTRAS CONCLUSIONES', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, $h, '       ' . $anexo312->data[0]->m_312_conclu_otros, 1, 'L', 0, 1);

//-----------------------------------------------------------//


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'XV. DIAGNOSTICO MÉDICO OCUPACIONAL', 1, 1, 'L', 1);
$pdf->Cell(180, $h * 12, '', 'LBR', 0, 'L');
$pdf->Ln(2);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(150, $h, 'DIAGNOSTICO (CIE-10)', 0, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'P', 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'R', 1, 1, 'C', 0);

//VARIALBES DIAGNOSTICO
$d = '';
$p = '';
$r = '';
$diag = '';
if (strlen($anexo312->data[0]->m_312_diag_cie1) > 1) {
    switch ($anexo312->data[0]->m_312_diag_st1) {
        case 'P':
            $p = 'X';
            break;
        case 'D':
            $d = 'X';
            break;
        case 'R':
            $r = 'X';
            break;
    }
    $diag = $anexo312->data[0]->m_312_diag_cie1;
}
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, '1.-', 0, 0, 'L', 0);
$pdf->Cell(140, $h, $diag, 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, $p, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $d, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $r, 1, 1, 'C', 0);

//VARIALBES DIAGNOSTICO
$d = '';
$p = '';
$r = '';
$diag = '';
if (strlen($anexo312->data[0]->m_312_diag_cie2) > 1) {
    switch ($anexo312->data[0]->m_312_diag_st2) {
        case 'P':
            $p = 'X';
            break;
        case 'D':
            $d = 'X';
            break;
        case 'R':
            $r = 'X';
            break;
    }
    $diag = $anexo312->data[0]->m_312_diag_cie2;
}
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, '2.-', 0, 0, 'L', 0);
$pdf->Cell(140, $h, $diag, 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, $p, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $d, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $r, 1, 1, 'C', 0);

//VARIALBES DIAGNOSTICO
$d = '';
$p = '';
$r = '';
$diag = '';
if (strlen($anexo312->data[0]->m_312_diag_cie3) > 1) {
    switch ($anexo312->data[0]->m_312_diag_st3) {
        case 'P':
            $p = 'X';
            break;
        case 'D':
            $d = 'X';
            break;
        case 'R':
            $r = 'X';
            break;
    }
    $diag = $anexo312->data[0]->m_312_diag_cie3;
}
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, '3.-', 0, 0, 'L', 0);
$pdf->Cell(140, $h, $diag, 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, $p, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $d, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $r, 1, 1, 'C', 0);

//VARIALBES DIAGNOSTICO
$d = '';
$p = '';
$r = '';
$diag = '';
if (strlen($anexo312->data[0]->m_312_diag_cie4) > 1) {
    switch ($anexo312->data[0]->m_312_diag_st4) {
        case 'P':
            $p = 'X';
            break;
        case 'D':
            $d = 'X';
            break;
        case 'R':
            $r = 'X';
            break;
    }
    $diag = $anexo312->data[0]->m_312_diag_cie4;
}
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, '4.-', 0, 0, 'L', 0);
$pdf->Cell(140, $h, $diag, 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, $p, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $d, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $r, 1, 1, 'C', 0);


$pdf->Ln(3);

$pdf->Cell(5, $h, '', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(150, $h, 'OTROS DIAGNOSTICO (CIE-10)', 0, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'P', 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'R', 1, 1, 'C', 0);

//VARIALBES DIAGNOSTICO
$d = '';
$p = '';
$r = '';
$diag = '';
if (strlen($anexo312->data[0]->m_312_diag_cie5) > 1) {
    switch ($anexo312->data[0]->m_312_diag_st5) {
        case 'P':
            $p = 'X';
            break;
        case 'D':
            $d = 'X';
            break;
        case 'R':
            $r = 'X';
            break;
    }
    $diag = $anexo312->data[0]->m_312_diag_cie5;
}
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, '5.-', 0, 0, 'L', 0);
$pdf->Cell(140, $h, $diag, 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, $p, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $d, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $r, 1, 1, 'C', 0);

//VARIALBES DIAGNOSTICO
$d = '';
$p = '';
$r = '';
$diag = '';
if (strlen($anexo312->data[0]->m_312_diag_cie6) > 1) {
    switch ($anexo312->data[0]->m_312_diag_st6) {
        case 'P':
            $p = 'X';
            break;
        case 'D':
            $d = 'X';
            break;
        case 'R':
            $r = 'X';
            break;
    }
    $diag = $anexo312->data[0]->m_312_diag_cie6;
}
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, '6.-', 0, 0, 'L', 0);
$pdf->Cell(140, $h, $diag, 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, $p, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $d, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $r, 1, 1, 'C', 0);

//VARIALBES DIAGNOSTICO
$d = '';
$p = '';
$r = '';
$diag = '';
if (strlen($anexo312->data[0]->m_312_diag_cie7) > 1) {
    switch ($anexo312->data[0]->m_312_diag_st7) {
        case 'P':
            $p = 'X';
            break;
        case 'D':
            $d = 'X';
            break;
        case 'R':
            $r = 'X';
            break;
    }
    $diag = $anexo312->data[0]->m_312_diag_cie7;
}
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, '7.-', 0, 0, 'L', 0);
$pdf->Cell(140, $h, $diag, 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, $p, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $d, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $r, 1, 1, 'C', 0);

//VARIALBES DIAGNOSTICO
$d = '';
$p = '';
$r = '';
$diag = '';
if (strlen($anexo312->data[0]->m_312_diag_cie8) > 1) {
    switch ($anexo312->data[0]->m_312_diag_st8) {
        case 'P':
            $p = 'X';
            break;
        case 'D':
            $d = 'X';
            break;
        case 'R':
            $r = 'X';
            break;
    }
    $diag = $anexo312->data[0]->m_312_diag_cie8;
}
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, '8.-', 0, 0, 'L', 0);
$pdf->Cell(140, $h, $diag, 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, $p, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $d, 1, 0, 'C', 0);
$pdf->Cell(5, $h, $r, 1, 1, 'C', 0);

$pdf->ln(4);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(180, $h, 'APTITUD MÉDICA', 1, 'C', 0, 1);

$pdf->SetFont('helvetica', 'B', 13);
$pdf->MultiCell(180, $h, $anexo312->data[0]->m_312_aptitud, 1, 'C', 0, 1);



$recomendaciones = $model->recomendaciones($_REQUEST['adm']);
$recomen_total = $recomendaciones->total;

// $pdf->Ln($salto);

/////////////////////////////////////////////////////////////////////////


// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(180, $h, 'XVI. RECOMENDACIONES', 1, 1, 'L', 1);
// $pdf->Cell(180, $h * 12, '', 'LBR', 0, 'L');
// $pdf->Ln(2);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'XVI. RECOMENDACIONES', 1, 1, 'L', 1);
$h = 3.5;
$pdf->SetFont('helvetica', 'B', 7);

foreach ($recomendaciones->data as $i => $row) {
    $pdf->SetFont('helvetica', '', 6);
    $pdf->MultiCell(180, $h, '      ' . $i + 1 . '.- ' . $row->recom_desc, 1, 'L', 0, 1);
}

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'XVII. RESTRICCIONES', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, $h, '      ' . $anexo312->data[0]->m_312_restricciones, 1, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'XVIII. OBSERVACIONES', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, $h, '      ' . $anexo312->data[0]->m_312_observaciones, 1, 'L', 0, 1);
$pdf->SetFont('helvetica', 'B', $titulo);


$busca_medico = $model->busca_medico($anexo312->data[0]->m_312_medico_ocupa);
$pdf->Cell(180, $h, 'MÉDICO EVALUADOR', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $titulo);
$pdf->Cell(180, $h, '' , 1, 0, 'L', 0);

$pdf->ln(0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(80, $h, 'Dr.(a) ' . $busca_medico->data[0]->nombres, 0, 0, 'L', 0);
$pdf->Cell(90, $h, 'CMP ' . $busca_medico->data[0]->medico_cmp, 0, 1, 'L', 0);

// $conteo = array();
// foreach ($recomendaciones->data as $i => $value) {
//     $saltos_t = 0;
//     foreach ($lim_texto as $a => $val) {
//         ($val < strlen($value->recom_desc)) ? $saltos_t = $a + 1 : null;
//     }
//     array_push($conteo, $saltos_t);
// }
// $fila_total = array_sum($conteo);

// foreach ($recomendaciones->data as $i => $row2) {
//     $salteos = (($conteo[$i] != 0) ? $conteo[$i] + 1 : 1);
//     if ($i === 0) {
//         $pdf->SetFont('helvetica', 'B', $texto);
//         $pdf->Cell(33, $h_text * ($recomen_total + $fila_total + 0.08), 'RECOMENDACIONES', 1, 0, 'C', 1);
//         $pdf->SetFont('helvetica', '', $texto - 1);
//         $pdf->MultiCell(122, $h_text * $salteos, $i + 1 . '.- ' . $row2->recom_desc, 1, 'L', 0, 0);
//         $pdf->SetFont('helvetica', 'B', $texto - 1);
//         $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
//         $pdf->SetFont('helvetica', '', $texto - 1);
//         $pdf->Cell(15, $h_text * $salteos, $row2->recom_plazo, 1, 1, 'C', 0);
//     } else {
//         $pdf->SetFont('helvetica', 'B', $texto);
//         $pdf->Cell(33, $h_text * $salteos, '', 0, 0, 'L', 0);
//         $pdf->SetFont('helvetica', '', $texto - 1);
//         $pdf->MultiCell(122, $h_text * $salteos, $i + 1 . '.- ' . $row2->recom_desc, 1, 'L', 0, 0);
//         $pdf->SetFont('helvetica', 'B', $texto - 1);
//         $pdf->Cell(10, $h_text * $salteos, 'PLAZO', 1, 0, 'C', 1);
//         $pdf->SetFont('helvetica', '', $texto - 1);
//         $pdf->Cell(15, $h_text * $salteos, $row2->recom_plazo, 1, 1, 'C', 0);
//     }
// }

// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(180, $h, 'VI. EVALUACIÓN MÉDICA', 1, 1, 'L', 1);




// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(180, $h, 'XIV. RECOMENDACIONES', 1, 1, 'L', 1);
// $h = 3.5;
// $pdf->ln(1);
// $pdf->SetFont('helvetica', 'B', 7);

// foreach ($recomendaciones->data as $i => $row) {
//     $pdf->ln(1);
//     $pdf->SetFont('helvetica', '', 6);
//     $pdf->MultiCell(180, $h, $i + 1 . '.- ' . $row->reco_desc, 'B', 'L', 0, 1);
// }



// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(180, $h, 'XV. RESTRICCIONES', 1, 1, 'L', 1);
// $pdf->SetFont('helvetica', '', $titulo);
// $pdf->MultiCell(180, $h, $validacion->data[0]->val_rectric, 1, 'L', 0, 1);



// $pdf->SetFont('helvetica', 'B', $titulo);
// $pdf->Cell(180, $h, 'XVI. OBSERVACIONES', 1, 1, 'L', 1);
// $pdf->SetFont('helvetica', '', $titulo);
// $pdf->MultiCell(180, $h, $validacion->data[0]->val_obser, 1, 'L', 0, 1);





$pdf->Output('Anexo_312_' . $_REQUEST['adm'] . '.PDF', 'I');
