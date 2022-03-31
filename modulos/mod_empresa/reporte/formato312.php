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
$paciente312 = $model->paciente312($_REQUEST['adm']);
$triaje312 = $model->triaje312($_REQUEST['adm']);
$oftalmo = $model->oftalmo($_REQUEST['adm']);
$ficha_312 = $model->ficha_312($_REQUEST['adm']);
$recomendaciones = $model->recomendaciones($_REQUEST['adm']);
$validacion = $model->validacion($_REQUEST['adm']);
$diagnostico = $model->diagnostico($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */





if ($paciente312->data[0]->emp_id == '67676767676') {
    $pdf->AddPage('P', 'A4');
    $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
//    $pdf->Image('images/logo.png', 5, 2, 45, '', 'PNG');
    $pdf->Ln(2);
    $h = 6;

    $pdf->SetFont('helvetica', 'B', 15);
    $pdf->Cell(0, 0, 'FICHA MÉDICA', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFillColor(194, 217, 241);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, 'N° de Ficha Médica', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(15, $h, $_REQUEST['adm'], 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'Fecha', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(35, $h, $paciente312->data[0]->fech_reg, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'Dia', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, $h, $paciente312->data[0]->fech_dia, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'Mes', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, $h, $paciente312->data[0]->fech_mes, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'Año', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, $h, $paciente312->data[0]->fech_año, 1, 1, 'C');


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(90, $h, 'Tipo de Evaluacion', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(90, $h, $paciente312->data[0]->tipo, 1, 1, 'C');

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, 'Lugar de Examen', 1, 0, 'C');
    $pdf->Cell(25, $h, 'Departamento', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, 'CUSCO', 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(20, $h, 'Provincia', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, 'CUSCO', 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(20, $h, 'Distrito', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(30, $h, 'WANCHAQ', 1, 1, 'C'); //


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'I. DATOS DE LA EMPRESA', 1, 1, 'L', 1);


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Razón Social', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(150, $h, $paciente312->data[0]->emp_desc, 1, 1, 'L');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'DIRECCIÓN', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(150, $h, $paciente312->data[0]->emp_dir, 1, 1, 'L');
// LIMA - LIMA - LA VICTORIA

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(20, $h, 'Ubicación', 1, 0, 'L');
    $pdf->Cell(30, $h, 'Departamento', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, $paciente312->data[0]->depa, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(25, $h, 'Provincia', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, $paciente312->data[0]->prov, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(25, $h, 'Distrito', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(30, $h, $paciente312->data[0]->dist, 1, 1, 'C');

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(50, $h, 'CARRERA PROFESIONAL', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(130, $h, $paciente312->data[0]->adm_act, 1, 1, 'C');


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'II. FILIACION DEL ESTUDIANTE', 1, 1, 'L', 1);



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(40, $h, 'Nombres y Apellidos', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(95, $h, $paciente312->data[0]->nom_ap, 1, 0, 'L');
    $pdf->Cell(45, 54, 'FOTO', 1, 0, 'C');

    if ($paciente312->data[0]->pac_foto == '1') {
        $foto = $model->foto($paciente312->data[0]->pac_id);
        $data = base64_decode($foto->data[0]->foto_desc);
        $pdf->Image('@' . $data, 150.5, 73, 44, 53);
    }

    $pdf->Ln(6);

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(40, $h, 'Fecha de Nacimiento' . $foto->data[0]->foto_desc, 1, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'DIA', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, $h, $paciente312->data[0]->naci_dia, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'MES', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(30, $h, $paciente312->data[0]->naci_mes, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'AÑO', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, $h, $paciente312->data[0]->naci_año, 1, 1, 'C');



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'Edad', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(120, $h, $paciente312->data[0]->edad . ' años', 1, 1, 'C');



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(115, $h, 'Documento de Identidad (Carnét de extrangeria, DNI o Pasaporte)', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, $h, $paciente312->data[0]->dni, 1, 1, 'C');



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Domicilio Fiscal', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(105, $h, $paciente312->data[0]->direc, 1, 1, 'C');




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Departamento', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(105, $h, $paciente312->data[0]->departamento, 1, 1, 'C');




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Provincia', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(105, $h, $paciente312->data[0]->provincia, 1, 1, 'C');



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Distrito', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(105, $h, $paciente312->data[0]->distrito, 1, 1, 'C');




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, 'Correo Electronico', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(50, $h, '', 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(20, $h, 'Celular', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(30, $h, $paciente312->data[0]->cel, 1, 1, 'C');


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(45, $h, 'Estado civil', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(45, $h, $paciente312->data[0]->ecivil, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(45, $h, 'Grado de instruccion', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(45, $h, $paciente312->data[0]->ginstruccion, 1, 1, 'C');


    $h = 5;


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'III. ANTECEDENTES PATOLÓGICOS PERSONALES', 1, 1, 'L', 1);


    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(35, $h, 'DIABETES', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_1, 1, 0, 'C');
    $pdf->Cell(35, $h, 'TBC', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_2, 1, 0, 'C');
    $pdf->Cell(35, $h, 'HEPATITIS.', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_3, 1, 0, 'C');
    $pdf->Cell(35, $h, 'ASMA', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_4, 1, 1, 'C');
////////////////////////////////////////////////////////////////////////////////
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(35, $h, 'HIPERTENSION ARTER.', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_5, 1, 0, 'C');
    $pdf->Cell(35, $h, 'ITS', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_6, 1, 0, 'C');
    $pdf->Cell(35, $h, 'TIFOIDEA', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_7, 1, 0, 'C');
    $pdf->Cell(35, $h, 'BRONQUITIS', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_8, 1, 1, 'C');
////////////////////////////////////////////////////////////////////////////////
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(35, $h, 'NEOPLASIA', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_9, 1, 0, 'C');
    $pdf->Cell(35, $h, 'CONVULSIONES', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_10, 1, 0, 'C');
    $pdf->Cell(35, $h, 'ALERGIAS', 1, 0, 'C');
    $pdf->Cell(55, $h, $ficha_312->data[0]->ficha_pato_11 . ' - ' . $ficha_312->data[0]->ficha_alergia, 1, 1, 'C');

    $pdf->MultiCell(180, 0, 'OTRAS PATOLOGIAS: ' . $ficha_312->data[0]->ficha_otros, 1, 'L', 0, 1);
    $pdf->MultiCell(180, 0, 'QUEMADURAS: ' . $ficha_312->data[0]->ficha_quemaduras, 1, 'L', 0, 1);
    $pdf->MultiCell(180, 0, 'CIRUGIAS: ' . $ficha_312->data[0]->ficha_qx, 1, 'L', 0, 1);
    $pdf->MultiCell(180, 0, 'INTOXICACIONES: ' . $ficha_312->data[0]->ficha_intoxica, 1, 'L', 0, 1);





    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'IV. ANTECEDENTES PATOLÓGICOS FAMILIARES', 1, 1, 'L', 1);

//    $pdf->SetFont('helvetica', '', 7);
//    $pdf->Cell(30, $h, 'ESTADO DEL PADRE', 1, 0, 'C');
//    $pdf->Cell(60, $h, $formato7c->data[0]->ficha_padre, 1, 0, 'L');
//    $pdf->Cell(30, $h, 'ESTADO DE LA MADRE', 1, 0, 'C');
//    $pdf->Cell(60, $h, $formato7c->data[0]->ficha_madre, 1, 1, 'L');
//
//
//    $pdf->Cell(25, $h, 'HERMANOS', 1, 0, 'C');
//    $pdf->Cell(65, $h, $formato7c->data[0]->ficha_hermanos, 1, 0, 'L');
//    $pdf->SetFont('helvetica', '', 7);
//    $pdf->Cell(25, $h, 'ESPOSO(A)', 1, 0, 'C');
//    $pdf->Cell(65, $h, $formato7c->data[0]->ficha_esposo, 1, 1, 'L');
//
//    $pdf->Cell(40, $h, 'TIENE HIJOS VIVOS?', 1, 0, 'C');
//    $pdf->Cell(20, $h, $formato7c->data[0]->ficha_hijov, 1, 0, 'L');
//    $pdf->Cell(10, $h, 'N°', 1, 0, 'C');
//    $pdf->Cell(20, $h, $formato7c->data[0]->ficha_hijov_nro, 1, 0, 'L');
//
//    $pdf->Cell(40, $h, 'TIENE HIJOS FALLECIDOS?', 1, 0, 'C');
//    $pdf->Cell(20, $h, $formato7c->data[0]->ficha_hijof, 1, 0, 'L');
//    $pdf->Cell(10, $h, 'N°', 1, 0, 'C');
//    $pdf->Cell(20, $h, $formato7c->data[0]->ficha_hijof_nro, 1, 1, 'L');





//    $pdf->SetFont('helvetica', '', 7);
//    $pdf->Cell(20, $h, 'ESTADO DEL PADRE', 1, 0, 'L');
//    $pdf->Cell(40, $h, $ficha_312->data[0]->ficha_padre, 1, 0, 'C');
//    $pdf->Cell(20, $h, 'ESTADO DE LA MADRE', 1, 0, 'C');
//    $pdf->Cell(40, $h, $ficha_312->data[0]->ficha_madre, 1, 0, 'L');
//    $pdf->Cell(25, $h, 'HERMANOS', 1, 0, 'C');
//    $pdf->Cell(35, $h, $ficha_312->data[0]->ficha_hermanos, 1, 1, 'L');
//
//
//    $pdf->SetFont('helvetica', '', 7);
//    $pdf->Cell(25, $h, 'ESPOSO(A)', 1, 0, 'C');
//    $pdf->Cell(25, $h, $ficha_312->data[0]->ficha_esposo, 1, 0, 'L');
//
//    $pdf->Cell(35, $h, 'HIJOS VIVOS', 1, 0, 'C');
//    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_hijov, 1, 0, 'L');
//    $pdf->Cell(10, $h, 'N°', 1, 0, 'C');
//    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_hijov_nro, 1, 0, 'L');
//
//    $pdf->Cell(35, $h, 'HIJOS FALLECIDOS', 1, 0, 'C');
//    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_hijof, 1, 0, 'C');
//    $pdf->Cell(10, $h, 'N°', 1, 0, 'C');
//    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_hijof_nro, 1, 1, 'L');


    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(180, $h, 'ABSENTISMO: Enfermedades y Accidentes (Asociados a trabajo o no)', 1, 1, 'L');


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(5, $h + 2, 'N°', 1, 0, 'C');
    $pdf->Cell(90, $h + 2, 'ENFERMEDAD, ACCIDENTE', 1, 0, 'C');
    $pdf->MultiCell(30, $h + 2, 'ASOCIADOS AL TRABAJO', 1, 'C', 0, 0);
    $pdf->Cell(15, $h + 2, 'AÑO', 1, 0, 'C');
    $pdf->Cell(40, $h + 2, 'DÍAS DE DESCANSO', 1, 1, 'C');
//ante_accidente, ante_trab,
//ante_ano, ante_descanso
    foreach ($ante_ocupa->data as $i => $row) {
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(5, $h, $i + 1, 1, 0, 'C');
        $pdf->Cell(90, $h, $row->ante_accidente, 1, 0, 'C');
        $pdf->Cell(30, $h, $row->ante_trab, 1, 0, 'C');
        $pdf->Cell(15, $h, $row->ante_ano, 1, 0, 'C');
        $pdf->Cell(40, $h, $row->ante_descanso, 1, 1, 'C');
    }

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'V. EVALUACIÓN MÉDICA', 1, 1, 'L', 1);

    $pdf->MultiCell(180, 0, 'ANAMNESIS:' . $ficha_312->data[0]->ficha_anamnesis, 1, 'L', 0, 1);


    $pdf->Cell(180, $h, 'EXAMEN CLÍNICO', 1, 1, 'L');

    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, 'TALLA(m)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_talla, 1, 0, 'C');
    $pdf->Cell(25, $h, 'PESO(Kg)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_peso, 1, 0, 'C');
    $pdf->Cell(20, $h, 'IMC Kg/m²', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_img, 1, 0, 'C');
    $pdf->Cell(50, $h, 'PERIMETRO ABDOMINAL (cm.)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_ptorax, 1, 1, 'C');

    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, 'F. RESP (X min)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_fr, 1, 0, 'C');
    $pdf->Cell(25, $h, 'F. CARD (X min)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_fc, 1, 0, 'C');
    $pdf->Cell(20, $h, 'PA', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_pa, 1, 0, 'C');
    $pdf->Cell(50, $h, 'TEMPERATURA (°C)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_temp, 1, 1, 'C');

//$med_ocupa

    $pdf->MultiCell(180, 0, 'ECTOSCOPÍA: ' . $ficha_312->data[0]->ficha_ectoscopia, 1, 'L', 0, 1);


    $pdf->Cell(30, $h, 'ESTADO MENTAL', 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(150, $h, $ficha_312->data[0]->ficha_est_mental, 1, 1, 'C');


    $pdf->AddPage('P', 'A4');

    $h = 6;
    $pdf->Ln(5);


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'EXAMEN FÍSICO', 1, 1, 'L');


    $pdf->Cell(35, $h, 'ÓRGANO O SISTEMA', 1, 0, 'C');
    $pdf->Cell(25, $h, 'SIN HALLAZGO', 1, 0, 'C');
    $pdf->Cell(120, $h, 'HALLAZGO', 1, 1, 'C');



    $text = '';
    $x = '';
    if (strlen($ficha_312->data[0]->ficha_piel) > 0) {
        $text = $ficha_312->data[0]->ficha_piel;
    } else {
        $x = 'X';
    }

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'PIEL', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);


    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_cabello) > 0) {
        $text = $ficha_312->data[0]->ficha_cabello;
    } else {
        $x = 'X';
    }

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'CABELLO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);

    $text = '';
    $x = '';

//    if (strlen($ficha_312->data[0]->ficha_oidos) > 0) {
//        $text = $ficha_312->data[0]->ficha_oidos;
//    } else {
//        $x = 'X';
//    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'OJOS Y ANEXOS', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, 'X', 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, '', 1, 'C', 0, 1);




    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_oidos) > 0) {
        $text = $ficha_312->data[0]->ficha_oidos;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'OIDOS', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);


    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_nariz) > 0) {
        $text = $ficha_312->data[0]->ficha_nariz;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'NARIZ', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);




    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_boca) > 0) {
        $text = $ficha_312->data[0]->ficha_boca;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'BOCA', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);



    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_faringe) > 0) {
        $text = $ficha_312->data[0]->ficha_faringe;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'FARINGE', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_cuello) > 0) {
        $text = $ficha_312->data[0]->ficha_cuello;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'CUELLO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_respiratorio) > 0) {
        $text = $ficha_312->data[0]->ficha_respiratorio;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO RESPIRATORIO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);







    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_cardiovascular) > 0) {
        $text = $ficha_312->data[0]->ficha_cardiovascular;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO CARDIOVASCULAR', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);


    $text = '';
    $x = '';
    if (strlen($ficha_312->data[0]->ficha_digestivo) > 0) {
        $text = $ficha_312->data[0]->ficha_digestivo;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO DIGESTIVO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';
    if (strlen($ficha_312->data[0]->ficha_genitou) > 0) {
        $text = $ficha_312->data[0]->ficha_genitou;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO GENITOURINARIO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);






    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_locomotor) > 0) {
        $text = $ficha_312->data[0]->ficha_locomotor;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO LOCOMOTOR', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);




    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_marcha) > 0) {
        $text = $ficha_312->data[0]->ficha_marcha;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'MARCHA', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_columna) > 0) {
        $text = $ficha_312->data[0]->ficha_columna;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'COLUMNA', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_mi_superi) > 0) {
        $text = $ficha_312->data[0]->ficha_mi_superi;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'MIEMBROS SUPERIORES', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);







    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_mi_inferi) > 0) {
        $text = $ficha_312->data[0]->ficha_mi_inferi;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'MIEMBROS INFERIORES', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_linfatico) > 0) {
        $text = $ficha_312->data[0]->ficha_linfatico;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'SISTEMA LINFATICO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);


    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_nervio) > 0) {
        $text = $ficha_312->data[0]->ficha_nervio;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'SISTEMA NERVIOSO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);



    $h = 5;


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'VI. CONCLUSIONES RADIOGRÁFICAS', 1, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_conclu_radiog . ' ' . $paciente312->data[0]->rayo, 1, 'L', 0, 1);





    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'VII. CONCLUSION PATOLÓGICAS DE LABORATORIO', 1, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 8); //ficha_recomendaciones
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_conclu_pato, 1, 'L', 0, 1);






    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'VIII. DIAGNOSTICO MÉDICO', 1, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_observ, 1, 'L', 0, 1);

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'IX. RECOMENDACIONES', 1, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 8); //ficha_recomendaciones
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_recomendaciones, 1, 'L', 0, 1);
    $pdf->Ln(2);
//
//    $pdf->Cell(135, 4, 'DIAGNOSTICO', 0, 0, 'L', 0);
//    $pdf->Cell(25, 4, 'CIE - 10', 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
//    $pdf->Cell(5, 4, 'D', 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, 'P', 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, 'R', 1, 1, 'C', 0);
//
//////////////////////////////////////////////////////////////////
//    $pdf->Cell(10, 4, '1.-', 0, 0, 'R', 0);
//    $pdf->Cell(120, 4, $ficha_312->data[0]->cie1, 'B', 0, 'L', 0);
//    $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
//    $pdf->Cell(25, 4, $ficha_312->data[0]->ficha_diag_cie1, 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
//    if ($ficha_312->data[0]->ficha_diag_st1 == 'D') {
//        $d = 'X';
//        $p = '';
//        $r = '';
//    } else if ($ficha_312->data[0]->ficha_diag_st1 == 'P') {
//        $d = '';
//        $p = 'X';
//        $r = '';
//    } else if ($ficha_312->data[0]->ficha_diag_st1 == 'R') {
//        $d = '';
//        $p = '';
//        $r = 'X';
//    }
//    $pdf->Cell(5, 4, $d, 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, $p, 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, $r, 1, 1, 'C', 0);
////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//    $pdf->Cell(10, 4, '2.-', 0, 0, 'R', 0);
//    $pdf->Cell(120, 4, $ficha_312->data[0]->cie2, 'B', 0, 'L', 0);
//    $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
//    $pdf->Cell(25, 4, $ficha_312->data[0]->ficha_diag_cie2, 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
//    if ($ficha_312->data[0]->ficha_diag_st2 == 'D') {
//        $d = 'X';
//        $p = '';
//        $r = '';
//    } else if ($ficha_312->data[0]->ficha_diag_st2 == 'P') {
//        $d = '';
//        $p = 'X';
//        $r = '';
//    } else if ($ficha_312->data[0]->ficha_diag_st2 == 'R') {
//        $d = '';
//        $p = '';
//        $r = 'X';
//    }
//    $pdf->Cell(5, 4, $d, 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, $p, 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, $r, 1, 1, 'C', 0);
////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//    $pdf->Cell(10, 4, '3.-', 0, 0, 'R', 0);
//    $pdf->Cell(120, 4, $ficha_312->data[0]->cie3, 'B', 0, 'L', 0);
//    $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
//    $pdf->Cell(25, 4, $ficha_312->data[0]->ficha_diag_cie3, 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
//    if ($ficha_312->data[0]->ficha_diag_st3 == 'D') {
//        $d = 'X';
//        $p = '';
//        $r = '';
//    } else if ($ficha_312->data[0]->ficha_diag_st3 == 'P') {
//        $d = '';
//        $p = 'X';
//        $r = '';
//    } else if ($ficha_312->data[0]->ficha_diag_st3 == 'R') {
//        $d = '';
//        $p = '';
//        $r = 'X';
//    }
//    $pdf->Cell(5, 4, $d, 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, $p, 1, 0, 'C', 0);
//    $pdf->Cell(5, 4, $r, 1, 1, 'C', 0);
//////////////////////////////////////////////////////////
    $pdf->Output('FICHA_MEDICA.PDF', 'I');
} else {

    $pdf->AddPage('P', 'A4');
    $pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
//    $pdf->Image('images/logo.png', 5, 2, 45, '', 'PNG');
    $pdf->Ln(2);
    $h = 6;

    $pdf->SetFont('helvetica', 'B', 15);
    $pdf->Cell(0, 0, 'Ficha Médico Ocupacional', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFillColor(194, 217, 241);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, 'N° de Ficha Médica', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(15, $h, $_REQUEST['adm'], 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'Fecha', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(35, $h, $paciente312->data[0]->fech_reg, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'Dia', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, $h, $paciente312->data[0]->fech_dia, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'Mes', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, $h, $paciente312->data[0]->fech_mes, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'Año', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, $h, $paciente312->data[0]->fech_año, 1, 1, 'C');


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(90, $h, 'Tipo de Evaluacion', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(90, $h, $paciente312->data[0]->tipo, 1, 1, 'C');

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, 'Lugar de Examen', 1, 0, 'C');
    $pdf->Cell(25, $h, 'Departamento', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, 'CUSCO', 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(20, $h, 'Provincia', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, 'CUSCO', 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(20, $h, 'Distrito', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(30, $h, 'WANCHAQ', 1, 1, 'C'); //


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'I. DATOS DE LA EMPRESA', 1, 1, 'L', 1);


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Razón Social', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(150, $h, $paciente312->data[0]->emp_desc . ' ' . $paciente312->data[0]->emp_id, 1, 1, 'L');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Actividad Económica', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(150, $h, '', 1, 1, 'L');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Lugar de Trabajo', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(150, $h, $paciente312->data[0]->emp_dir, 1, 1, 'L');
// LIMA - LIMA - LA VICTORIA


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(20, $h, 'Ubicación', 1, 0, 'L');
    $pdf->Cell(30, $h, 'Departamento', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, $paciente312->data[0]->depa, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(25, $h, 'Provincia', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, $paciente312->data[0]->prov, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(25, $h, 'Distrito', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(30, $h, $paciente312->data[0]->dist, 1, 1, 'C');

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(50, $h, 'Puesto al que Postula', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(130, $h, $paciente312->data[0]->adm_act, 1, 1, 'C');


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'II. FILIACION DEL TRABAJADOR', 1, 1, 'L', 1);



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(40, $h, 'Nombres y Apellidos', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(95, $h, $paciente312->data[0]->nom_ap, 1, 0, 'L');
    $pdf->Cell(45, 54, 'FOTO', 1, 0, 'C');

    if ($paciente312->data[0]->pac_foto == '1') {
        $foto = $model->foto($paciente312->data[0]->pac_id);
        $data = base64_decode($foto->data[0]->foto_desc);
        $pdf->Image('@' . $data, 150.5, 79.2, 44, 53);
    }

    $pdf->Ln(6);

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(40, $h, 'Fecha de Nacimiento', 1, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'DIA', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, $h, $paciente312->data[0]->naci_dia, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'MES', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(30, $h, $paciente312->data[0]->naci_mes, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'AÑO', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, $h, $paciente312->data[0]->naci_año, 1, 1, 'C');



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(15, $h, 'Edad', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(120, $h, $paciente312->data[0]->edad . ' años', 1, 1, 'C');



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(115, $h, 'Documento de Identidad (Carnét de extrangeria, DNI o Pasaporte)', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, $h, $paciente312->data[0]->dni, 1, 1, 'C');



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Domicilio Fiscal', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(105, $h, $paciente312->data[0]->direc, 1, 1, 'C');




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Departamento', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(105, $h, $paciente312->data[0]->departamento, 1, 1, 'C');




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Provincia', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(105, $h, $paciente312->data[0]->provincia, 1, 1, 'C');



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(30, $h, 'Distrito', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(105, $h, $paciente312->data[0]->distrito, 1, 1, 'C');




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, 'Correo Electronico', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(50, $h, $paciente312->data[0]->pac_correo, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(20, $h, 'Celular', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(30, $h, $paciente312->data[0]->cel, 1, 1, 'C');


//$pdf->Ln(1);


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(40, $h, 'Residensia en Lugar Trabajo', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(45, $h, $ficha_312->data[0]->ficha_residenci, 1, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(60, $h, 'Tiempo de residencia en lugar de trabajo', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(35, $h, $ficha_312->data[0]->ficha_tiempo, 1, 1, 'L');




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(17, $h, 'ESSALUD', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_essalud, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'EPS', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_eps, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'OTRO', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(47, $h, $ficha_312->data[0]->ficha_otro1, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'SCTR', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_sctr, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(10, $h, 'OTRO', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(46, $h, $ficha_312->data[0]->ficha_otro2, 1, 1, 'C');






    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(45, $h, 'Estado civil', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(45, $h, $paciente312->data[0]->ecivil, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(45, $h, 'Grado de instruccion', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(45, $h, $paciente312->data[0]->ginstruccion, 1, 1, 'C');



//antecedentes


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(45, $h, 'N° Total de Hijos Vivos', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(45, $h, $ficha_312->data[0]->ficha_nhijos, 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(45, $h, 'N° Dependientes', 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(45, $h, $ficha_312->data[0]->ficha_dependiente, 1, 1, 'C');








    $pdf->SetFont('helvetica', 'B', 8);
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
    $ante_ocupa = $model->ante_ocupa($_REQUEST['adm']);
    foreach ($ante_ocupa->data as $i => $row) {
        $pdf->SetFont('helvetica', '', 5.5);
        $pdf->Cell(5, $h * 2, $i + 1, 1, 0, 'C');
        $pdf->MultiCell(30, $h * 2, $row->ante_emp, 1, 'C', 0, 0);
        $pdf->MultiCell(35, $h * 2, $row->ante_area, 1, 'C', 0, 0);
        $pdf->MultiCell(35, $h * 2, $row->ante_ocupa, 1, 'C', 0, 0);
        $pdf->MultiCell(18, $h * 2, $row->ante_fech_ini . ' ' . $row->ante_fech_fin, 1, 'C', 0, 0);
        $pdf->MultiCell(14, $h * 2, $row->ante_tiempo, 1, 'C', 0, 0);
        $pdf->MultiCell(30, $h * 2, $row->ante_exposicion, 1, 'C', 0, 0);
        $pdf->MultiCell(13, $h * 2, $row->ante_epp, 1, 'C', 0, 1);
    }


    $h = 5;


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'IV. ANTECEDENTES PATOLÓGICOS PERSONALES', 1, 1, 'L', 1);


    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(35, $h, 'DIABETES', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_1, 1, 0, 'C');
    $pdf->Cell(35, $h, 'TBC', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_2, 1, 0, 'C');
    $pdf->Cell(35, $h, 'HEPATITIS.', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_3, 1, 0, 'C');
    $pdf->Cell(35, $h, 'ASMA', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_4, 1, 1, 'C');
////////////////////////////////////////////////////////////////////////////////
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(35, $h, 'HIPERTENSION ARTER.', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_5, 1, 0, 'C');
    $pdf->Cell(35, $h, 'ITS', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_6, 1, 0, 'C');
    $pdf->Cell(35, $h, 'TIFOIDEA', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_7, 1, 0, 'C');
    $pdf->Cell(35, $h, 'BRONQUITIS', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_8, 1, 1, 'C');
////////////////////////////////////////////////////////////////////////////////
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(35, $h, 'NEOPLASIA', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_9, 1, 0, 'C');
    $pdf->Cell(35, $h, 'CONVULSIONES', 1, 0, 'C');
    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_pato_10, 1, 0, 'C');
    $pdf->Cell(35, $h, 'ALERGIAS', 1, 0, 'C');
    $pdf->Cell(55, $h, $ficha_312->data[0]->ficha_pato_11 . ' - ' . $ficha_312->data[0]->ficha_alergia, 1, 1, 'C');

    $pdf->MultiCell(180, 0, 'OTRAS PATOLOGIAS: ' . $ficha_312->data[0]->ficha_otros, 1, 'L', 0, 1);
    $pdf->MultiCell(180, 0, 'QUEMADURAS: ' . $ficha_312->data[0]->ficha_quemaduras, 1, 'L', 0, 1);
    $pdf->MultiCell(180, 0, 'CIRUGIAS: ' . $ficha_312->data[0]->ficha_qx, 1, 'L', 0, 1);
    $pdf->MultiCell(180, 0, 'INTOXICACIONES: ' . $ficha_312->data[0]->ficha_intoxica, 1, 'L', 0, 1);


    $pdf->AddPage('P', 'A4');

    $h = 5;
    $pdf->Ln(5);

    $pdf->Cell(35, $h, 'HÁBITOS NOCIVOS', 1, 0, 'C');
    $pdf->Cell(55, $h, 'TIPO', 1, 0, 'C');
    $pdf->Cell(35, $h, 'CANTIDAD', 1, 0, 'C');
    $pdf->Cell(55, $h, 'FRECUENCIA', 1, 1, 'C');

    if ($ficha_312->data[0]->ficha_alcohol_fre == '1')
        $alcohol = 'NADA';
    else if ($ficha_312->data[0]->ficha_alcohol_fre == '2')
        $alcohol = 'POCO';
    else if ($ficha_312->data[0]->ficha_alcohol_fre == '3')
        $alcohol = 'HABITUAL';
    else if ($ficha_312->data[0]->ficha_alcohol_fre == '4')
        $alcohol = 'EXCESIVO';

    $pdf->Cell(35, $h, 'ALCOHOL', 1, 0, 'C');
    $pdf->Cell(55, $h, $ficha_312->data[0]->ficha_alcohol_tipo, 1, 0, 'C');
    $pdf->Cell(35, $h, $ficha_312->data[0]->ficha_alcohol_cantidad, 1, 0, 'C');
    $pdf->Cell(55, $h, $alcohol, 1, 1, 'C');



    if ($ficha_312->data[0]->ficha_tabaco_fre == '1')
        $tabaco = 'NADA';
    else if ($ficha_312->data[0]->ficha_tabaco_fre == '2')
        $tabaco = 'POCO';
    else if ($ficha_312->data[0]->ficha_tabaco_fre == '3')
        $tabaco = 'HABITUAL';
    else if ($ficha_312->data[0]->ficha_tabaco_fre == '4')
        $tabaco = 'EXCESIVO';
    $pdf->Cell(35, $h, 'TABACO', 1, 0, 'C');
    $pdf->Cell(55, $h, $ficha_312->data[0]->ficha_tabaco_tipo, 1, 0, 'C');
    $pdf->Cell(35, $h, $ficha_312->data[0]->ficha_tabaco_cantidad, 1, 0, 'C');
    $pdf->Cell(55, $h, $tabaco, 1, 1, 'C');


    if ($ficha_312->data[0]->ficha_drogas_fre == '1')
        $drogas = 'NADA';
    else if ($ficha_312->data[0]->ficha_drogas_fre == '2')
        $drogas = 'POCO';
    else if ($ficha_312->data[0]->ficha_drogas_fre == '3')
        $drogas = 'HABITUAL';
    else if ($ficha_312->data[0]->ficha_drogas_fre == '4')
        $drogas = 'EXCESIVO';
    $pdf->Cell(35, $h, 'DROGAS', 1, 0, 'C');
    $pdf->Cell(55, $h, $ficha_312->data[0]->ficha_drogas_tipo, 1, 0, 'C');
    $pdf->Cell(35, $h, $ficha_312->data[0]->ficha_drogas_cantidad, 1, 0, 'C');
    $pdf->Cell(55, $h, $drogas, 1, 1, 'C');

    $pdf->Cell(35, $h, 'MEDICAMENTOS', 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(145, $h, $ficha_312->data[0]->ficha_medicina, 1, 1, 'L');









    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'V. ANTECEDENTES PATOLÓGICOS FAMILIARES', 1, 1, 'L', 1);



    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(30, $h, 'ESTADO DEL PADRE', 1, 0, 'C');
    $pdf->Cell(60, $h, $ficha_312->data[0]->ficha_padre, 1, 0, 'L');
    $pdf->Cell(30, $h, 'ESTADO DE LA MADRE', 1, 0, 'C');
    $pdf->Cell(60, $h, $ficha_312->data[0]->ficha_madre, 1, 1, 'L');


    $pdf->Cell(25, $h, 'HERMANOS', 1, 0, 'C');
    $pdf->Cell(65, $h, $ficha_312->data[0]->ficha_hermanos, 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell(25, $h, 'ESPOSO(A)', 1, 0, 'C');
    $pdf->Cell(65, $h, $ficha_312->data[0]->ficha_esposo, 1, 1, 'L');

    $pdf->Cell(40, $h, 'TIENE HIJOS VIVOS?', 1, 0, 'C');
    $pdf->Cell(20, $h, $ficha_312->data[0]->ficha_hijov, 1, 0, 'L');
    $pdf->Cell(10, $h, 'N°', 1, 0, 'C');
    $pdf->Cell(20, $h, $ficha_312->data[0]->ficha_hijov_nro, 1, 0, 'L');

    $pdf->Cell(40, $h, 'TIENE HIJOS FALLECIDOS?', 1, 0, 'C');
    $pdf->Cell(20, $h, $ficha_312->data[0]->ficha_hijof, 1, 0, 'L');
    $pdf->Cell(10, $h, 'N°', 1, 0, 'C');
    $pdf->Cell(20, $h, $ficha_312->data[0]->ficha_hijof_nro, 1, 1, 'L');
//
//    $pdf->SetFont('helvetica', '', 7);
//    $pdf->Cell(20, $h, 'PADRE', 1, 0, 'C');
//    $pdf->Cell(40, $h, $ficha_312->data[0]->ficha_padre, 1, 0, 'C');
//    $pdf->Cell(20, $h, 'MADRE', 1, 0, 'C');
//    $pdf->Cell(40, $h, $ficha_312->data[0]->ficha_madre, 1, 0, 'C');
//    $pdf->Cell(25, $h, 'HERMANOS', 1, 0, 'C');
//    $pdf->Cell(35, $h, $ficha_312->data[0]->ficha_hermanos, 1, 1, 'C');
//
//
//    $pdf->SetFont('helvetica', '', 7);
//    $pdf->Cell(25, $h, 'ESPOSO(A)', 1, 0, 'C');
//    $pdf->Cell(25, $h, $ficha_312->data[0]->ficha_esposo, 1, 0, 'C');
//
//    $pdf->Cell(35, $h, 'HIJOS VIVOS', 1, 0, 'C');
//    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_hijov, 1, 0, 'C');
//    $pdf->Cell(10, $h, 'N°', 1, 0, 'C');
//    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_hijov_nro, 1, 0, 'C');
//
//    $pdf->Cell(35, $h, 'HIJOS FALLECIDOS', 1, 0, 'C');
//    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_hijof, 1, 0, 'C');
//    $pdf->Cell(10, $h, 'N°', 1, 0, 'C');
//    $pdf->Cell(10, $h, $ficha_312->data[0]->ficha_hijof_nro, 1, 1, 'C');


    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(180, $h, 'ABSENTISMO: Enfermedades y Accidentes (Asociados a trabajo o no)', 1, 1, 'L');







    $h = 5;
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(5, $h + 2, 'N°', 1, 0, 'C');
    $pdf->Cell(105, $h + 2, 'ENFERMEDAD O ACCIDENTE', 1, 0, 'C');
    $pdf->MultiCell(25, $h + 2, 'ASOCIADOS AL TRABAJO', 1, 'C', 0, 0);
    $pdf->Cell(15, $h + 2, 'AÑO', 1, 0, 'C');
    $pdf->Cell(30, $h + 2, 'DÍAS DE DESCANSO', 1, 1, 'C');
    $h = 4;
    foreach ($ante_ocupa->data as $i => $row) {
        $pdf->SetFont('helvetica', '', 6.5);
        $pdf->Cell(5, $h, $i + 1, 1, 0, 'C');
        $pdf->Cell(105, $h, $row->ante_accidente, 1, 0, 'C');
        $pdf->Cell(25, $h, $row->ante_trab, 1, 0, 'C');
        $pdf->Cell(15, $h, $row->ante_ano, 1, 0, 'C');
        $pdf->Cell(30, $h, $row->ante_descanso, 1, 1, 'C');
    }
    $h = 6;

//triaje



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'VI. EVALUACIÓN MÉDICA', 1, 1, 'L', 1);




    $pdf->MultiCell(180, 0, 'ANAMNESIS:' . $ficha_312->data[0]->ficha_anamnesis, 1, 'L', 0, 1);


    $pdf->Cell(180, $h, 'EXAMEN CLÍNICO', 1, 1, 'L');

    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, 'TALLA(m)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_talla, 1, 0, 'C');
    $pdf->Cell(25, $h, 'PESO(Kg)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_peso, 1, 0, 'C');
    $pdf->Cell(20, $h, 'IMC Kg/m²', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_img, 1, 0, 'C');
    $pdf->Cell(50, $h, 'PERIMETRO ABDOMINAL (cm.)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_ptorax, 1, 1, 'C');

    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, 'F. RESP (X min)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_fr, 1, 0, 'C');
    $pdf->Cell(25, $h, 'F. CARD (X min)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_fc, 1, 0, 'C');
    $pdf->Cell(20, $h, 'PA', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_pa, 1, 0, 'C');
    $pdf->Cell(50, $h, 'TEMPERATURA (°C)', 1, 0, 'C');
    $pdf->Cell(15, $h, $triaje312->data[0]->tri_temp, 1, 1, 'C');

//$med_ocupa

    $pdf->MultiCell(180, 0, 'ECTOSCOPÍA: ' . $ficha_312->data[0]->ficha_ectoscopia, 1, 'L', 0, 1);


    $pdf->Cell(30, $h, 'ESTADO MENTAL', 1, 0, 'C');
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Cell(150, $h, $ficha_312->data[0]->ficha_est_mental, 1, 1, 'C');




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'EXAMEN FÍSICO', 1, 1, 'L');


    $pdf->Cell(35, $h, 'ÓRGANO O SISTEMA', 1, 0, 'C');
    $pdf->Cell(25, $h, 'SIN HALLAZGO', 1, 0, 'C');
    $pdf->Cell(120, $h, 'HALLAZGO', 1, 1, 'C');



    $text = '';
    $x = '';
    if (strlen($ficha_312->data[0]->ficha_piel) > 0) {
        $text = $ficha_312->data[0]->ficha_piel;
    } else {
        $x = 'X';
    }

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'PIEL', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);


    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_cabello) > 0) {
        $text = $ficha_312->data[0]->ficha_cabello;
    } else {
        $x = 'X';
    }

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'CABELLO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_ojos) > 0) {
        $text = $ficha_312->data[0]->ficha_ojos;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'OJOS', 'LRT', 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, 0, $x, 'LRT', 'C', 0, 0);
    $pdf->MultiCell(120, 0, $text, 1, 'C', 0, 1);


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, 'ANEXOS', 'LR', 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, '', 'LR', 0, 'C');
    $pdf->Cell(25, $h, 'AGUDEZA VISUAL', 1, 0, 'C');
    $pdf->Cell(6.25, $h, 'OD', 1, 0, 'C');
    $pdf->Cell(10, $h, $oftalmo->data[0]->ofta_slejos_der, 1, 0, 'C');
    $pdf->Cell(6.25, $h, 'OI', 1, 0, 'C');
    $pdf->Cell(10, $h, $oftalmo->data[0]->ofta_slejos_izq, 1, 0, 'C');
    $pdf->Cell(30, $h, 'CON CORRECTORES', 1, 0, 'C');
    $pdf->Cell(6.25, $h, 'OD', 1, 0, 'C');
    $pdf->Cell(10, $h, $oftalmo->data[0]->ofta_clejos_der, 1, 0, 'C');
    $pdf->Cell(6.25, $h, 'OI', 1, 0, 'C');
    $pdf->Cell(10, $h, $oftalmo->data[0]->ofta_clejos_izq, 1, 1, 'C');


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, '', 'LR', 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, '', 'LR', 0, 'C');
    $pdf->Cell(30, $h, 'FONDOS DE OJO', 1, 0, 'C');
    $pdf->Cell(25, $h, $oftalmo->data[0]->ofta_fond, 1, 0, 'C');
    $pdf->Cell(35, $h, 'VISIÓN DE COLORES', 1, 0, 'C');
    $pdf->Cell(30, $h, $oftalmo->data[0]->ofta_visi, 1, 1, 'C');


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, '', 'LRB', 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(25, $h, '', 'LRB', 0, 'C');
    $pdf->Cell(45, $h, 'VISIÓN DIAGNOSTICO', 1, 0, 'C');
    $pdf->Cell(75, $h, $oftalmo->data[0]->ofta_cie1, 1, 1, 'C');




    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_oidos) > 0) {
        $text = $ficha_312->data[0]->ficha_oidos;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'OIDOS', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);


    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_nariz) > 0) {
        $text = $ficha_312->data[0]->ficha_nariz;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'NARIZ', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);




    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_boca) > 0) {
        $text = $ficha_312->data[0]->ficha_boca;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'BOCA', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);



    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_faringe) > 0) {
        $text = $ficha_312->data[0]->ficha_faringe;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'FARINGE', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_cuello) > 0) {
        $text = $ficha_312->data[0]->ficha_cuello;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'CUELLO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_respiratorio) > 0) {
        $text = $ficha_312->data[0]->ficha_respiratorio;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO RESPIRATORIO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);







    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_cardiovascular) > 0) {
        $text = $ficha_312->data[0]->ficha_cardiovascular;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO CARDIOVASCULAR', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);


    $text = '';
    $x = '';
    if (strlen($ficha_312->data[0]->ficha_digestivo) > 0) {
        $text = $ficha_312->data[0]->ficha_digestivo;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO DIGESTIVO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';
    if (strlen($ficha_312->data[0]->ficha_genitou) > 0) {
        $text = $ficha_312->data[0]->ficha_genitou;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO GENITOURINARIO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);



    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_locomotor) > 0) {
        $text = $ficha_312->data[0]->ficha_locomotor;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'APARATO LOCOMOTOR', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $pdf->AddPage('P', 'A4');

    $h = 5;
    $pdf->Ln(5);


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(35, $h, 'ÓRGANO O SISTEMA', 1, 0, 'C');
    $pdf->Cell(25, $h, 'SIN HALLAZGO', 1, 0, 'C');
    $pdf->Cell(120, $h, 'HALLAZGO', 1, 1, 'C');







    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_marcha) > 0) {
        $text = $ficha_312->data[0]->ficha_marcha;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'MARCHA', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_columna) > 0) {
        $text = $ficha_312->data[0]->ficha_columna;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'COLUMNA', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_mi_superi) > 0) {
        $text = $ficha_312->data[0]->ficha_mi_superi;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'MIEMBROS SUPERIORES', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);







    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_mi_inferi) > 0) {
        $text = $ficha_312->data[0]->ficha_mi_inferi;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'MIEMBROS INFERIORES', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);





    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_linfatico) > 0) {
        $text = $ficha_312->data[0]->ficha_linfatico;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'SISTEMA LINFATICO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);







    $text = '';
    $x = '';

    if (strlen($ficha_312->data[0]->ficha_nervio) > 0) {
        $text = $ficha_312->data[0]->ficha_nervio;
    } else {
        $x = 'X';
    }
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(35, $h + 2, 'SISTEMA NERVIOSO', 1, 'C', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(25, $h + 2, $x, 1, 'C', 0, 0);
    $pdf->MultiCell(120, $h + 2, $text, 1, 'C', 0, 1);



    $h = 5;

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'VII. CONCLUSION DE EVALUACIÓN PSICOLÓGICA', 1, 1, 'L', 1); //paciente
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_conclu_psicolo . ' ' . $paciente312->data[0]->psico_reco, 1, 'L', 0, 1);





    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'VIII. CONCLUSIONES RADIOGRÁFICAS', 1, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_conclu_radiog . ' ' . $paciente312->data[0]->rayo, 1, 'L', 0, 1);





    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'IX. CONCLUSION PATOLÓGICAS DE LABORATORIO', 1, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_conclu_pato, 1, 'L', 0, 1);





    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'X. CONCLUSION AUDIOMETRIA', 1, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_conclu_audio, 1, 'L', 0, 1);
//audio_a_oi_diag, audio_a_od_diag




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'XI. CONCLUSION DE ESPIROMETRÍA', 1, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_conclu_espiro, 1, 'L', 0, 1);




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'XII. OTROS', 1, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(180, $h, $ficha_312->data[0]->ficha_conclu_otros, 1, 'L', 0, 1);



    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(145, $h, 'XIII. DIAGNOSTICO MÉDICO OCUPACIONAL', 1, 0, 'L', 1);
    $pdf->Cell(35, $h, '', 1, 1, 'C', 1);
    $pdf->Ln(2);

    $pdf->Cell(160, 4, 'DIAGNOSTICO (CIE-10)', 0, 0, 'L', 0);
//    $pdf->Cell(25, 4, 'CIE - 10', 1, 0, 'C', 0);
    $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
    $pdf->Cell(5, 4, 'D', 1, 0, 'C', 0);
    $pdf->Cell(5, 4, 'P', 1, 0, 'C', 0);
    $pdf->Cell(5, 4, 'R', 1, 1, 'C', 0);

////////////////////////////////////////////////////////////////
    if (strlen($ficha_312->data[0]->ficha_diag_cie1) > 1) {
        $pdf->Cell(10, 4, '1.-', 0, 0, 'R', 0);
        $pdf->Cell(150, 4, $ficha_312->data[0]->ficha_diag_cie1, 'B', 0, 'L', 0);
        $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
        $d = '';
        $p = '';
        $r = '';
        if ($ficha_312->data[0]->ficha_diag_st1 == 'D') {
            $d = 'X';
            $p = '';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st1 == 'P') {
            $d = '';
            $p = 'X';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st1 == 'R') {
            $d = '';
            $p = '';
            $r = 'X';
        }
        $pdf->Cell(5, 4, $d, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $p, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $r, 1, 1, 'C', 0);
    }
//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
    if (strlen($ficha_312->data[0]->ficha_diag_cie2) > 1) {
        $pdf->Cell(10, 4, '2.-', 0, 0, 'R', 0);
        $pdf->Cell(150, 4, $ficha_312->data[0]->ficha_diag_cie2, 'B', 0, 'L', 0);
        $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
        $d = '';
        $p = '';
        $r = '';
        if ($ficha_312->data[0]->ficha_diag_st2 == 'D') {
            $d = 'X';
            $p = '';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st2 == 'P') {
            $d = '';
            $p = 'X';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st2 == 'R') {
            $d = '';
            $p = '';
            $r = 'X';
        }
        $pdf->Cell(5, 4, $d, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $p, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $r, 1, 1, 'C', 0);
    }
//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
    if (strlen($ficha_312->data[0]->ficha_diag_cie3) > 1) {
        $pdf->Cell(10, 4, '3.-', 0, 0, 'R', 0);
        $pdf->Cell(150, 4, $ficha_312->data[0]->ficha_diag_cie3, 'B', 0, 'L', 0);
        $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
        $d = '';
        $p = '';
        $r = '';
        if ($ficha_312->data[0]->ficha_diag_st3 == 'D') {
            $d = 'X';
            $p = '';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st3 == 'P') {
            $d = '';
            $p = 'X';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st3 == 'R') {
            $d = '';
            $p = '';
            $r = 'X';
        }
        $pdf->Cell(5, 4, $d, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $p, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $r, 1, 1, 'C', 0);
    }
//////////////////////////////////////////////////////////
    $pdf->Ln(2);


    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, 4, 'OTROS DIAGNOSTICO', 0, 1, 'L', 0);

////////////////////////////////////////////////////////////////
    if (strlen($ficha_312->data[0]->ficha_diag_cie4) > 1) {
        $pdf->Cell(10, 4, '1.-', 0, 0, 'R', 0);
        $pdf->Cell(150, 4, $ficha_312->data[0]->ficha_diag_cie4, 'B', 0, 'L', 0);
        $pdf->Cell(5, 4, '', 0, 0, 'C', 0);

        $d = '';
        $p = '';
        $r = '';
        if ($ficha_312->data[0]->ficha_diag_st4 == 'D') {
            $d = 'X';
            $p = '';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st4 == 'P') {
            $d = '';
            $p = 'X';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st4 == 'R') {
            $d = '';
            $p = '';
            $r = 'X';
        }
        $pdf->Cell(5, 4, $d, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $p, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $r, 1, 1, 'C', 0);
    }
//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
    if (strlen($ficha_312->data[0]->ficha_diag_cie5) > 1) {
        $pdf->Cell(10, 4, '2.-', 0, 0, 'R', 0);
        $pdf->Cell(150, 4, $ficha_312->data[0]->ficha_diag_cie5, 'B', 0, 'L', 0);
        $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
        $d = '';
        $p = '';
        $r = '';
        if ($ficha_312->data[0]->ficha_diag_st5 == 'D') {
            $d = 'X';
            $p = '';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st5 == 'P') {
            $d = '';
            $p = 'X';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st5 == 'R') {
            $d = '';
            $p = '';
            $r = 'X';
        }
        $pdf->Cell(5, 4, $d, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $p, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $r, 1, 1, 'C', 0);
    }
//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
    if (strlen($ficha_312->data[0]->ficha_diag_cie6) > 1) {
        $pdf->Cell(10, 4, '3.-', 0, 0, 'R', 0);
        $pdf->Cell(150, 4, $ficha_312->data[0]->ficha_diag_cie6, 'B', 0, 'L', 0);
        $pdf->Cell(5, 4, '', 0, 0, 'C', 0);
        $d = '';
        $p = '';
        $r = '';
        if ($ficha_312->data[0]->ficha_diag_st6 == 'D') {
            $d = 'X';
            $p = '';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st6 == 'P') {
            $d = '';
            $p = 'X';
            $r = '';
        } else if ($ficha_312->data[0]->ficha_diag_st6 == 'R') {
            $d = '';
            $p = '';
            $r = 'X';
        }
        $pdf->Cell(5, 4, $d, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $p, 1, 0, 'C', 0);
        $pdf->Cell(5, 4, $r, 1, 1, 'C', 0);
    }
//////////////////////////////////////////////////////////



    $h = 3.5;
    $pdf->SetFont('helvetica', 'B', 7);

    foreach ($diagnostico->data as $i2 => $row2) {
        $pdf->ln(1);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->MultiCell(180, $h, $i2 + 1 . '.- ' . $row2->diag_desc, 'B', 'L', 0, 1);
    }

    $h = 6;

    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->MultiCell(180, 4, 'APTITUD MÉDICA', 1, 'C', 0, 1);

    $pdf->SetFont('helvetica', 'B', 13);
    $pdf->MultiCell(180, 4, $validacion->data[0]->val_aptitud, 1, 'C', 0, 1);




    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(180, $h, 'XIII. RECOMENDACIONES', 1, 1, 'L', 1);
    $h = 3.5;
    $pdf->ln(1);
    $pdf->SetFont('helvetica', 'B', 7);

    foreach ($recomendaciones->data as $i => $row) {
        $pdf->ln(1);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->MultiCell(180, $h, $i + 1 . '.- ' . $row->reco_desc, 'B', 'L', 0, 1);
    }






    $pdf->Output('Reporte_CLARO.PDF', 'I');
}
