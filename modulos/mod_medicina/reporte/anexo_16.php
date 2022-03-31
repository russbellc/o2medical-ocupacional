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
$ubigeo = $model->ubigeo($_REQUEST['adm']);
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
$h = 3.5;
$titulo = 7;
$texto = 7;
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, 45, '', 'TLR', 0, 'L', 0);
$pdf->Ln(1);
$pdf->Cell(180, $h, 'REG-010-NOP-SAL-02/V01', 0, 1, 'R', 0);

$pdf->ImageSVG('images/logo_pdf.svg', 16, 10, '', '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(180, $h, 'FICHA MEDICA OCUPACIONAL', 0, 1, 'C', 0);
$pdf->Cell(180, $h, 'ANEXO 16', 0, 0, 'C', 0);

$pdf->Ln(0.1);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(130, $h, '', 0, 0, 'C', 0);
$pdf->Cell(50, $h, 'EXAMEN MEDICO', 0, 1, 'L', 0);
$pdf->Ln(1);
$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(1);

$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(130, $h, '', 0, 0, 'L', 0);
$pdf->Cell(35, $h, 'PRE-OCUPACIONAL', 0, 0, 'L', 0);
$pdf->Cell(10, $h, ($paciente->data[0]->tipo == 'PRE OCUPACIONAL') ? 'X' : '', 1, 1, 'C', 0);
$pdf->Ln(3);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(12, $h, 'Unidad:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(16.33, $h, '', 0, 0, 'L', 0); //Unidad
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(9, $h, 'Area:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(39.33, $h * 2, $paciente->data[0]->adm_act, 0, 'L', 0, 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(14, $h, 'Empresa:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(39.33, $h * 2, $paciente->data[0]->emp_desc, 0, 'L', 0, 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(35, $h, 'PERIODICO', 0, 0, 'L', 0);
$pdf->Cell(10, $h, ($paciente->data[0]->tipo == 'ANUAL') ? 'X' : '', 1, 1, 'C', 0);
$pdf->Ln(3);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(25, $h, 'N° Servicio/Contrato: ', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(15, $h, '', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'Contrata:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(26, $h, '', 0, 0, 'L', 0); //Contrata
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(22, $h, 'Subcontratista:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(27, $h, '', 0, 0, 'L', 0); //Subcontratista
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(35, $h, 'RETIRO', 0, 0, 'L', 0);
$pdf->Cell(10, $h, ($paciente->data[0]->tipo == 'RETIRO') ? 'X' : '', 1, 1, 'C', 0);
$pdf->Ln(3);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'Apellidos y Nombres:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(100, $h, $paciente->data[0]->nom_ap, 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(25, $h, 'Nº DE FICHA:', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(20, $h, $paciente->data[0]->adm, 0, 1, 'L', 0);
$pdf->Ln(1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, 'FECHA DEL EXAMEN:', 'TBL', 0, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(40, $h, $paciente->data[0]->fech_reg, 'TBR', 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(55, $h, 'MINERALES EXPLOTADOS O PROCESADOS:', 'TBL', 0, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(55, $h, '', 'TBR', 1, 'L', 0);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'Lugar de Nac.', 1, 0, 'C', 0);
$pdf->Cell(20, $h, 'Fecha de Nac.', 1, 0, 'C', 0);
$pdf->Cell(30, $h, 'Domicilio Habitual', 1, 0, 'C', 0);
$pdf->Cell(40, ($h) * 4, '', 1, 0, 'C', 0);
$pdf->Cell(70, ($h) * 4, '', 1, 0, 'C', 0);
$pdf->Ln(0.01);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(70, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, 'SUPERFICIE', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_suelo == '1') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'ALTURA DE LA LABOR', 0, 0, 'C', 0);
$pdf->Ln($h);

//['1', 'SUPERFICIE'], ["2", 'CONCENTRADORA'], ["3", 'SUBSUELO']

$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(20, ($h) * 3, $ubigeo->data[0]->ubigeo, 1, 'C', 0, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(20, ($h) * 3, $paciente->data[0]->fech_naci, 1, 'C', 0, 0);
$pdf->SetFont('helvetica', '', 5);
$pdf->MultiCell(30, ($h) * 3, $paciente->data[0]->ubica . '  Dirección: ' . $paciente->data[0]->direc, 1, 'C', 0, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, 'CONCENTRADORA', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_suelo == '2') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(35, $h, '', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(25, $h, '3501 a 4000 m', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_altura == '3') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);

$pdf->Ln($h);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(70, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, 'SUBSUELO', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_suelo == '3') ? 'X' : '', 'BLR', 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(25, $h, 'Hasta 3000 m', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_altura == '1') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(25, $h, '4001 a 4500 m', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_altura == '4') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);

$pdf->Ln($h);



$pdf->Cell(110, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(25, $h, '3001 a 3300 m', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_altura == '2') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(25, $h, 'más de 4501 m', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_altura == '5') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(5, $h, '', 0, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h, 'EDAD', 1, 0, 'C', 0);
$pdf->Cell(20, $h, 'SEXO', 1, 0, 'C', 0);
$pdf->Cell(30, $h, 'DOC. DE IDENTIDAD', 1, 0, 'C', 0);
$pdf->Cell(40, $h, 'ESTADO CIVIL', 1, 0, 'C', 0);
$pdf->Cell(70, $h, 'GRADO DE INSTRUCCION', 1, 1, 'C', 0);



$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(20, ($h) * 3, $paciente->data[0]->edad . ' AÑOS', 1, 0, 'C', 0);
$pdf->Cell(20, ($h) * 3, $paciente->data[0]->sexo, 1, 0, 'C', 0);
$pdf->Cell(30, ($h) * 2, 'DNI: ' . $paciente->data[0]->pac_ndoc, 1, 0, 'C', 0);
$pdf->Cell(40, ($h) * 3, '', 1, 0, 'C', 0);
$pdf->Cell(70, ($h) * 3, '', 1, 0, 'C', 0);
$pdf->Ln(0.1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(70, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(11, $h, 'Soltero', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($paciente->data[0]->ecivil == 'SOLTERO(A)') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, 'Conviviente', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($paciente->data[0]->ecivil == 'CONVIVIENTE') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(16.33, $h, 'Analfabeto', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($paciente->data[0]->ginstruccion == 'ANALFABETO') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(48.66, $h, '', 0, 0, 'C', 0);
$pdf->Ln($h);


$pdf->Cell(70, $h, '', 0, 0, 'C', 0);
$pdf->Cell(18, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, 'Viudo', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($paciente->data[0]->ecivil == 'VIUDO(A)') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(16.33, $h, 'Hasta 3° Prim.', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 6);
$pdf->Cell(5, $h, ($paciente->data[0]->ginstruccion == 'HASTA 3° DE PRIMARIA') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(16.33, $h, 'Secund. Incomp.', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 6);
$pdf->Cell(5, $h, ($paciente->data[0]->ginstruccion == 'SECUNDARIA INCOMPLETA') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(16.33, $h, 'Técnico', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 6);
$pdf->Cell(5, $h, ($paciente->data[0]->ginstruccion == 'TECNICA INCOMPLETA' || $paciente->data[0]->ginstruccion == 'TECNICA COMPLETA') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->Ln($h);


$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, 'Telf:' . $paciente->data[0]->pac_cel, 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(11, $h, 'Casado', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($paciente->data[0]->ecivil == 'CASADO(A)') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, 'Divorciado', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, ($paciente->data[0]->ecivil == 'DIVORCIADO(A)') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(16.33, $h, 'Más de 3° Prim', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 6);
$pdf->Cell(5, $h, ($paciente->data[0]->ginstruccion == 'MAS 3° DE PRIMARIA' || $paciente->data[0]->ginstruccion == 'PRIMARIA COMPLETA') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(16.33, $h, 'Secund. Comp.', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 6);
$pdf->Cell(5, $h, ($paciente->data[0]->ginstruccion == 'SECUNDARIA COMPLETA') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(16.33, $h, 'Universitario', 0, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 6);
$pdf->Cell(5, $h, ($paciente->data[0]->ginstruccion == 'UNIVERSITARIO INCOMPLETO' || $paciente->data[0]->ginstruccion == 'UNIVERSITARIO COMPLETO' || $paciente->data[0]->ginstruccion == 'POST-GRADO') ? 'X' : '', 'TLR', 0, 'C', 0);
$pdf->Cell(2, $h, '', 0, 1, 'C', 0);




$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'ANTECEDENTES OCUPACIONALES (VER ADJUNTO A HISTORIA OCUPACIONAL)', 1, 1, 'L', 1);
$pdf->Cell(180, $h, 'CARACTERISTICAS DEL PUESTO DE TRABAJO', 1, 1, 'L', 1);

$pdf->Cell(70, ($h) * 4, '', 1, 0, 'C', 0);
$pdf->Cell(40, ($h) * 4, '', 1, 0, 'C', 0);
$pdf->Cell(70, ($h) * 4, '', 1, 0, 'C', 0);
$pdf->Ln(0.1);





$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(23.33, $h, 'Ruido', 0, 0, 'C', 0);
$pdf->Cell(23.33, $h, 'Solventes', 0, 0, 'C', 0);
$pdf->Cell(23.33, $h, 'Posturas', 0, 0, 'C', 0);
$pdf->Cell(40, $h, 'OTROS:', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'Describir según corresponda', 'TLR', 0, 'C', 0);

$pdf->Ln($h);






$pdf->Cell(23.33, $h, 'Polvo', 0, 0, 'C', 0);
$pdf->Cell(23.33, $h, 'Metales pesados', 0, 0, 'C', 0);
$pdf->Cell(23.33, $h, 'Turnos', 0, 0, 'C', 0);
$pdf->MultiCell(40, ($h) * 3, '-', 0, 'L', 0, 0);
$pdf->Cell(70, $h, 'Puesto al que postula:' . $paciente->data[0]->adm_act, 'TLR', 0, 'L', 0);
$pdf->Ln($h);






$pdf->Cell(23.33, $h, 'Cancerígenos', 0, 0, 'C', 0);
$pdf->Cell(23.33, $h, 'Temperatura', 0, 0, 'C', 0);
$pdf->Cell(23.33, $h, 'Carga', 0, 0, 'C', 0);
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(50, $h, 'Puesto actual:', 1, 0, 'L', 0);
$pdf->Cell(20, $h, 'Tiempo:', 1, 0, 'L', 0);
$pdf->Ln($h);






$pdf->Cell(23.33, $h, 'Mutagénicos', 0, 0, 'C', 0);
$pdf->Cell(23.33, $h, 'Biológicos', 0, 0, 'C', 0);
$pdf->Cell(23.33, $h, 'Mov. Repet.', 0, 0, 'C', 0);
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'Reubicación:', 0, 0, 'L', 0);
$pdf->Cell(6.66, $h, '', 0, 0, 'L', 0);
$pdf->Cell(7, $h, 'SI (', 0, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, ')', 0, 0, 'L', 0);
$pdf->Cell(6.66, $h, '', 0, 0, 'L', 0);
$pdf->Cell(7, $h, 'NO (', 0, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, ')', 0, 0, 'L', 0);
$pdf->Cell(6.66, $h, '', 0, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(110, $h, 'ANTECEDENTES PERSONALES Y OCUPACIONALES (enfermedades y accidentes)', 'TLB', 0, 'L', 1);
$pdf->Cell(55, $h, 'SIN IMPORTANCIA PATOLOGICA ACTUAL:', 'TRB', 0, 'L', 1);
$pdf->Cell(5, $h, '', 1, 0, 'C', 0); //X
$pdf->Cell(10, $h, '', 'TRB', 1, 'L', 1);
$pdf->SetFont('helvetica', '', 7);

$pdf->Cell(10, $h, 'HTA', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_hta == '1') ? 'X' : ' ') . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(15, $h, 'H. Tg.', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_htg == '1') ? 'X' : ' ') . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(15, $h, 'Alergias', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_alergia == '1') ? 'X' : ' ' ) . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(10, $h, 'HBP', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_hbp == '1') ? 'X' : ' ') . ')', 'TRB', 0, 'C', 0);


$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(88, $h * 3, ((strlen($formato7c->data[0]->ficha7c_otros) >= 2) ? 'Otras Patologias: ' . $formato7c->data[0]->ficha7c_otros : '') . ' ' . ((strlen($formato7c->data[0]->ficha7c_quemaduras) >= 2) ? '
Quemaduras: ' . $formato7c->data[0]->ficha7c_quemaduras : '') . ' ' . ((strlen($formato7c->data[0]->ficha7c_qx) >= 2) ? '
Cirugias: ' . $formato7c->data[0]->ficha7c_qx : '') . ' ' . ((strlen($formato7c->data[0]->ficha7c_intoxica) >= 2) ? '
Intoxicaciones: ' . $formato7c->data[0]->ficha7c_intoxica : ''), 1, 'L', 0, 0);
$pdf->SetFont('helvetica', '', 7);
if ($paciente->data[0]->sexo == 'MASCULINO') {
    $pdf->Cell(22, $h, '', 1, 1, 'L', 0);
} else {
    $pdf->Cell(22, $h, 'FUR: ' . $formato7c->data[0]->ficha7c_fur, 1, 1, 'L', 0);
}





$pdf->SetFont('helvetica', '', 7);

$pdf->Cell(10, $h, 'DM', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_dn == '1') ? 'X' : ' ') . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(15, $h, 'H. Col.', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_hcol == '1') ? 'X' : ' ') . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(15, $h, 'Artropatia', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_artro == '1') ? 'X' : ' ') . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(10, $h, 'Migraña', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_migra == '1') ? 'X' : ' ' ) . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(88, $h, '', 0, 0, 'C', 0);
$pdf->Cell(22, $h, 'RC: ', 1, 1, 'L', 0);





$pdf->SetFont('helvetica', '', 7);

$pdf->Cell(10, $h, 'ASMA', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_asma == '1') ? 'X' : ' ') . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(15, $h, 'Prob. CV.', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_prob == '1') ? 'X' : ' ') . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(15, $h, 'Pt. Columna', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . (($formato7c->data[0]->ficha7c_ptcolum == '1') ? 'X' : ' ' ) . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(10, $h, 'Qx', 'TLB', 0, 'C', 0);
$pdf->Cell(5, $h, '(' . ((strlen($formato7c->data[0]->ficha7c_qx) >= 2) ? 'X' : ' ' ) . ')', 'TRB', 0, 'C', 0);

$pdf->Cell(88, $h, '', 0, 0, 'L', 0);
$pdf->Cell(22, $h, 'MAC: ', 1, 1, 'L', 0);






$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(60, $h, 'ANTECEDENTES FAMILIARES', 'TBL', 0, 'L', 1);
$pdf->Cell(60, $h, 'SIN IMPORTANCIA PATOLOGICA ACTUAL', 'TB', 0, 'R', 1);
$pdf->Cell(5, $h, '', 1, 0, 'C', 0); //X
$pdf->Cell(10, $h, '', 'TB', 0, 'L', 1);
$pdf->Cell(15, $h * 4, '', 1, 0, 'C', 0);
$pdf->Cell(30, $h, 'NUMERO DE HIJOS', 'TBR', 1, 'C', 1);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'Papá:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(120, $h, $formato7c->data[0]->ficha7c_padre, 1, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'SIN HIJOS', 0, 0, 'L', 0);
$pdf->Cell(15, $h, 'VIVOS', 1, 0, 'C', 0);
$pdf->Cell(15, $h, 'MUERTOS', 1, 1, 'C', 0);
$pdf->SetFont('helvetica', '', 7);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'Mamá:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(120, $h, $formato7c->data[0]->ficha7c_madre, 1, 0, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_hijov == 'Si') ? '' : 'X', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(15, $h * 2, $formato7c->data[0]->ficha7c_hijov_nro, 1, 0, 'C', 0);
$pdf->Cell(15, $h * 2, $formato7c->data[0]->ficha7c_hijof_nro, 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 1, 'L', 0);
$pdf->SetFont('helvetica', '', 7);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'Hermanos:', 1, 0, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(120, $h, $formato7c->data[0]->ficha7c_hermanos, 1, 1, 'L', 0);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(15, $h, 'HABITOS', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'Tabaco', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'Alcohol', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'Coca', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'TALLA:', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'PESO:', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'IMC', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'Funciones Respiratorias', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'Funciones Vitales', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', 7);


$pdf->Cell(15, $h, 'Nada', 1, 0, 'L', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_tabaco == '1') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_alcohol == '1') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_drogas == '1') ? 'X' : '', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(20, $h * 4, $triaje->data[0]->tri_talla . ' m.', 1, 0, 'C', 0);
$pdf->Cell(20, $h * 4, $triaje->data[0]->tri_peso . ' Kg.', 1, 0, 'C', 0);
$pdf->Cell(20, $h * 4, $triaje->data[0]->tri_img . ' Kg/m²', 1, 0, 'C', 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, 'FVC (L): ' . $ex_adicionales->data[0]->mmg_espiro_FVC, 0, 0, 'L', 0);
$pdf->Cell(30, $h, 'PA: ' . $triaje->data[0]->tri_pa . ' mmHg', 1, 1, 'L', 0);



$pdf->Cell(15, $h, 'Poco', 1, 0, 'L', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_tabaco == '2') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_alcohol == '2') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_drogas == '2') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, 'FEV1 (L): ' . $ex_adicionales->data[0]->mmg_espiro_FEV1, 0, 0, 'L', 0);
$pdf->Cell(30, $h, 'FC: ' . $triaje->data[0]->tri_fc . ' lat/min', 1, 1, 'L', 0);



$pdf->Cell(15, $h, 'Habitual', 1, 0, 'L', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_tabaco == '3') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_alcohol == '3') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_drogas == '3') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, 'FEV1/FVC: ' . $ex_adicionales->data[0]->mmg_espiro_FEV1_FVC, 0, 0, 'L', 0);
$pdf->Cell(30, $h, 'FR: ' . $triaje->data[0]->tri_fr . ' resp/min', 1, 1, 'L', 0);



$pdf->Cell(15, $h, 'Excesivo', 1, 0, 'L', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_tabaco == '4') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_alcohol == '4') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(15, $h, ($formato7c->data[0]->ficha7c_drogas == '4') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, 'FEF 25% – 75%: ' . $ex_adicionales->data[0]->mmg_espiro_FEF2575, 'B', 0, 'L', 0);
$pdf->Cell(30, $h, 'T°C: ' . $triaje->data[0]->tri_temp . ' °C', 1, 1, 'L', 0);



$pdf->Cell(120, $h, 'PERIMETRO TORAXICO:', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'CONCLUSION:', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'SO2: ' . $triaje->data[0]->tri_satura . '%', 1, 1, 'L', 0);


$pdf->Cell(50, $h, 'Máxima Inspiración: ' . $triaje->data[0]->tri_inspira . ' cm.', 1, 0, 'L', 0);
$pdf->Cell(130, $h, 'Expiración forzada: ' . $triaje->data[0]->tri_espira . ' cm.', 1, 1, 'L', 0);


$pdf->Cell(50, $h, 'PERIMETRO ABDOMINAL: ' . $triaje->data[0]->tri_abdom . ' cm.', 1, 0, 'L', 0);
$pdf->Cell(50, $h, 'PERIMETRO CADERA: ' . $triaje->data[0]->tri_cadera . ' cm.', 1, 0, 'L', 0); //tri_cadera
$pdf->Cell(80, $h, 'ICC: cm.', 1, 1, 'L', 0);




$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'EXAMEN FISICO REGIONAL', 1, 1, 'L', 1);

$pdf->Cell(180, $h, 'BOCA, AMIGDALAS, FARINGE, LARINGE: ' . (($formato7c->data[0]->ficha7c_boca == '1') ? 'NORMAL' : 'HALLAZGO'), 1, 1, 'L', 0);
$pdf->Cell(100, $h, 'CUELLO: ' . (($formato7c->data[0]->ficha7c_cuello == '1') ? 'NORMAL' : 'HALLAZGO'), 1, 0, 'L', 0);
$pdf->Cell(80, $h, 'NARIZ: ' . (($formato7c->data[0]->ficha7c_nariz == '1') ? 'NORMAL' : 'HALLAZGO'), 1, 1, 'L', 0);



$caries = $model->caries($_REQUEST['adm']);
$ausentes = $model->ausentes($_REQUEST['adm']);
$recomenda_odo = $model->recomenda_odo($_REQUEST['adm']);

$pdf->Cell(180, $h, 'DENTADURA', 1, 1, 'L', 1);
foreach ($recomenda_odo->data as $i => $row) {
    $pdf->SetFont('helvetica', '', 6.5);
    $pdf->MultiCell(120, $h * 2, $i + 1 . '.- ' . $row->reco_desc, 1, 'L', 0, 0);
}

$pdf->SetFont('helvetica', '', 7);

$pdf->Cell(0, $h, 'Piezas en mal estado: ' . $caries->data[0]->caries, 'TR', 1, 'L', 0);



$pdf->Cell(120, $h, '', 0, 0, 'L', 0);
$pdf->Cell(60, $h, 'Piezas que faltan: ' . $ausentes->data[0]->ausentes, 'BR', 1, 'L', 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h * 2, 'OFTALMOLOGIA', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'SIN CORREGIR', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'CORREGIDO', 1, 0, 'C', 1);
$pdf->Cell(0, $h, 'ENFERMEDADES OCULARES', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, '', 0, 0, 'C');
$pdf->Cell(15, $h, 'OI', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'OD', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'OI', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'OD', 1, 0, 'C', 1);
$pdf->Cell(0, $h, '', 1, 1, 'C'); //NINGUNO

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'VISION DE CERCA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_scerca_izq, 1, 0, 'C');
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_scerca_der, 1, 0, 'C');
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_ccerca_izq, 1, 0, 'C');
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_ccerca_der, 1, 0, 'C');
$pdf->Cell(0, $h, 'REFLEJOS PUPILARES: ', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'VISION DE LEJOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_slejos_izq, 1, 0, 'C');
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_slejos_der, 1, 0, 'C');
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_clejos_izq, 1, 0, 'C');
$pdf->Cell(15, $h, $oftalmologia->data[0]->ofta_clejos_der, 1, 0, 'C');
$pdf->Cell(0, $h, $oftalmologia->data[0]->ofta_refl, 1, 1, 'C');

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(40, $h, 'VISION DE COLORES', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(30, $h, $paciente->data[0]->ofta_colo, 1, 0, 'C');
$pdf->Cell(30, $h, 'TONOMETRIA:', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'OI: ' . $oftalmologia->data[0]->ofta_oj_izq . ' mmHg', 1, 0, 'C');
$pdf->Cell(25, $h, 'OD: ' . $oftalmologia->data[0]->ofta_oj_der . ' mmHg', 1, 0, 'C');
$pdf->Cell(0, $h, '', 1, 1, 'C');



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h * 2, 'TORAX', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(80, $h * 2, 'ECTOSCOPIA: ' . (($formato7c->data[0]->ficha7c_ectos == '1') ? 'CONSERVADA' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_ectos_desc, 1, 'L', 0, 0);
$pdf->MultiCell(80, $h * 2, 'CORAZON: ' . (($formato7c->data[0]->ficha7c_corazon == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_corazon_desc, 1, 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(20, $h * 2, 'MAMAS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(80, $h * 2, 'DERECHA: ' . (($formato7c->data[0]->ficha7c_mama == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_mama_de, 1, 'L', 0, 0);
$pdf->MultiCell(80, $h * 2, 'IZQUIERDA: '
        . (($formato7c->data[0]->ficha7c_mama == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_mama_iz, 1, 'L', 0, 1);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'Sistema Mio Osteo Articular', 1, 1, 'L', 1);


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

$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(180, $h, 'FUERZA MUSCULAR:' . $fuerza, 1, 1, 'L', 0);

$pdf->Cell(5, $h, '', 'LB', 0, 'L', 0);
$pdf->Cell(15, $h, 'Conservada', 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 1, 0, 'C', 0); //X
$pdf->Cell(5, $h, '', 'B', 0, 'L', 0);
$pdf->Cell(15, $h, 'Disminuida', 'B', 0, 'L', 0);
$pdf->Cell(5, $h, '', 1, 0, 'C', 0); //X
$pdf->Cell(5, $h, '', 'B', 0, 'L', 0);
$pdf->Cell(0, $h, 'Describir Observaciones:', 'BR', 1, 'L', 0);





$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(20, $h * 2, 'Miembros Superiores', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(80, $h * 2, 'DERECHA: '
        . (($formato7c->data[0]->ficha7c_misup_de == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_misup_de_desc, 1, 'L', 0, 0);
$pdf->MultiCell(80, $h * 2, 'IZQUIERDA: '
        . (($formato7c->data[0]->ficha7c_misup_iz == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_misup_iz_desc, 1, 'L', 0, 1);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(20, $h * 2, 'Miembros Inferiores', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(80, $h * 2, 'DERECHA: '
        . (($formato7c->data[0]->ficha7c_miinf_de == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_miinf_de_desc, 1, 'L', 0, 0);
$pdf->MultiCell(80, $h * 2, 'IZQUIERDA: '
        . (($formato7c->data[0]->ficha7c_miinf_iz == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_miinf_iz_desc, 1, 'L', 0, 1);



$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(90, $h, 'Reflejos Osteotendinosos: '
        . (($formato7c->data[0]->ficha7c_refle == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_refle_desc, 1, 0, 'L', 0);
$pdf->Cell(90, $h, 'Marcha: '
        . (($formato7c->data[0]->ficha7c_marcha == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_marcha_desc, 1, 1, 'L', 0);
$pdf->MultiCell(180, $h, 'Columna vertebral: '
        . (($formato7c->data[0]->ficha7c_column == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_column_desc, 1, 'L', 0, 1);






$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(20, $h * 3, 'ABDOMEN:', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(95, $h * 3, (($formato7c->data[0]->ficha7c_abdomen == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_abdomen_desc, 1, 'L', 0, 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(0, $h, 'Tacto Rectal', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', 7);

$pdf->Cell(115, $h, '', 0, 0, 'L', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(10, $h, 'No se hizo', 0, 0, 'R', 0);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_tacto == '1') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(10, $h, 'Normal', 0, 0, 'R', 0);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_tacto == '2') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 0, 'L', 0);
$pdf->Cell(10, $h, 'Anormal', 0, 0, 'R', 0);
$pdf->Cell(5, $h, ($formato7c->data[0]->ficha7c_tacto == '3') ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(0, $h, '', 'R', 1, 'L', 0);

$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(115, $h, '', 0, 0, 'L', 0);
$pdf->Cell(0, $h, 'OBSERVACIONES:' . $formato7c->data[0]->ficha7c_tacto_desc, 'BR', 1, 'L', 0);
$pdf->SetFont('helvetica', '', 6);



$pdf->MultiCell(70, $h, 'Anillos Inguinales:' . (($formato7c->data[0]->ficha7c_anill == '1') ? 'NORMAL' : 'HALLAZGO') . " - " . $formato7c->data[0]->ficha7c_anill_desc, 1, 'L', 0, 0);
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(50, $h, 'Hernias:' . (($formato7c->data[0]->ficha7c_hernia == '1') ? 'SI' : 'NO') . " - " . $formato7c->data[0]->ficha7c_hernia_desc, 1, 'L', 0, 0);
$pdf->MultiCell(60, $h, 'Várices:' . (($formato7c->data[0]->ficha7c_varic == '1') ? 'SE EVIDENCIA' : 'NO SE EVIDENCIA') . " - " . $formato7c->data[0]->ficha7c_varic_desc, 1, 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(20, $h * 1, 'Genitales', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(70, $h * 1, (($formato7c->data[0]->ficha7c_genit == '1') ? 'NORMAL' : 'HALLAZGO') . " - " . $formato7c->data[0]->ficha7c_gemit_desc, 1, 'L', 0, 0);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(20, $h * 1, 'Ganglios', 1, 'C', 1, 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(70, $h * 1, (($formato7c->data[0]->ficha7c_gangli == '1') ? 'NORMAL' : 'HALLAZGO') . " - " . $formato7c->data[0]->ficha7c_gangli_desc, 1, 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(180, $h, 'SNC:', 1, 'L', 1, 1);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(180, $h, '', 1, 'L', 0, 1);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(180, $h, 'Lenguaje, Atención, Memoria, Orientación, Inteligencia, Afectividad:' . (($formato7c->data[0]->ficha7c_lengua == '1') ? 'NORMAL' : 'HALLAZGO'), 1, 'L', 1, 1);
$pdf->SetFont('helvetica', '', 6);
$pdf->MultiCell(180, $h, $formato7c->data[0]->ficha7c_lengua_obs, 1, 'L', 0, 1);






// Añadir página
$pdf->AddPage('P', 'A4');
$pdf->SetFont('helvetica', 'B', 7);


$pdf->Cell(20, $h, 'PULMONES:', 1, 0, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(0, $h, (($formato7c->data[0]->ficha7c_pulmon == '1') ? 'NORMAL' : 'HALLAZGO') . ' - ' . $formato7c->data[0]->ficha7c_pulmon_desc, 1, 1, 'L', 0);


$pdf->Cell(0, $h, 'EVALUACIÓN RADIOLOGICA', 1, 1, 'L', 1);


$pdf->Cell(40, 31.3, $pdf->Image('images/pulmon.png', 18, '', 34, ''), 1, 0, 'L', 0);

$pdf->Cell(0, $h, 'LECTURA DE PLACA', 1, 1, 'C', 1);


$pdf->Cell(40, $h, '', 0, 0, 'L', 0);
$pdf->Cell(20, $h, 'Vértices', 1, 0, 'L', 1);
$pdf->Cell(0, $h, '', 1, 1, 'L', 0); //Libres

$pdf->Cell(40, $h, '', 0, 0, 'L', 0);
$pdf->Cell(20, $h, 'Hilios', 1, 0, 'L', 1);
$pdf->Cell(0, $h, '', 1, 1, 'L', 0); //No adenopatías

$pdf->Cell(40, $h, '', 0, 0, 'L', 0);
$pdf->Cell(20, $h, 'Senos', 1, 0, 'L', 1);
$pdf->Cell(0, $h, '', 1, 1, 'L', 0); //Libres

$pdf->Cell(40, $h, '', 0, 0, 'L', 0);
$pdf->Cell(20, $h, 'Mediastinos', 1, 0, 'L', 1);
$pdf->Cell(0, $h, '', 1, 1, 'L', 0); //No masas

$pdf->Cell(40, $h, '', 0, 0, 'L', 0);
$pdf->Cell(20, $h, 'Silueta Cardiaca', 1, 0, 'L', 1);
$pdf->Cell(0, $h, '', 1, 1, 'L', 0); //Dentro de límites normales

$pdf->Cell(40, $h, '', 0, 0, 'L', 0);
$pdf->MultiCell(0, $h * 2, 'CONCLUSIONES RADIOGRÁFICAS:', 1, 'L', 0, 1);



$pdf->Cell(40, $h, '', 0, 0, 'L', 0);
$pdf->Cell(35, $h, '0/0', 1, 0, 'C', 0);
$pdf->Cell(35, $h, '0/1', 1, 0, 'C', 0);
$pdf->Cell(17.5, $h, '1/1; 1/2', 1, 0, 'C', 0);
$pdf->Cell(17.5, $h, '2/1; 2/2; 2/3', 1, 0, 'C', 0);
$pdf->Cell(17.5, $h, '3/2;3/3;3/+', 1, 0, 'C', 0);
$pdf->Cell(17.5, $h, 'A; B; C', 1, 1, 'C', 0);

$pdf->Cell(20, $h, 'N° Rx:', 1, 0, 'L', 1);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_placa, 1, 0, 'L', 0);
$pdf->Cell(35, $h, 'CERO', 1, 0, 'C', 0);
$pdf->Cell(35, $h, '1/0', 1, 0, 'C', 0);
$pdf->Cell(17.5, $h, 'UNO', 1, 0, 'C', 0);
$pdf->Cell(17.5, $h, 'DOS ', 1, 0, 'C', 0);
$pdf->Cell(17.5, $h, 'TRES', 1, 0, 'C', 0);
$pdf->Cell(17.5, $h, 'CUATRO', 1, 1, 'C', 0);

$pdf->Cell(20, $h, 'Fecha:', 1, 0, 'L', 1);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_lector, 1, 0, 'L', 0);

$pdf->MultiCell(35, $h * 3, 'Sin Neumoconiosis "NORMAL"', 1, 'C', 0, 0);
$pdf->MultiCell(35, $h * 3, 'Imagen Radioografica deExposicion a Polvo "SOSPECHA"', 1, 'C', 0, 0);
$pdf->Cell(70, $h * 3, '"CON NEUMOCONIOSIS"', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 1, 'C', 0);



$pdf->Cell(20, $h, 'Calidad:', 1, 0, 'L', 1);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_calid, 1, 1, 'L', 0);
$pdf->Cell(20, $h, 'Simbolos:', 1, 0, 'L', 1);
$pdf->Cell(20, $h, $rayosx->data[0]->rayo_profu, 1, 1, 'L', 0);

$pdf->MultiCell(180, $h, 'CONCLUSIÓN LECTURA OIT:' . $rayosx->data[0]->rayo_inf_mostro, 1, 'L', 0, 1);

$pdf->Cell(30, $h, 'OÍDOS', 1, 0, 'L', 1);
$pdf->Cell(150 / 2, $h, 'Derecho', 1, 0, 'L', 1);
$pdf->Cell(150 / 2, $h, 'Izquierdo', 1, 1, 'L', 1);

$pdf->Cell(30, $h, 'Otoscopia', 1, 0, 'C', 0);
$pdf->Cell(150 / 2, $h, '', 1, 0, 'L', 0);
$pdf->Cell(150 / 2, $h, '', 1, 1, 'L', 0);

$pdf->Cell(30, $h * 8, '', 1, 0, 'C', 0);
$pdf->Cell(150, $h, 'AUDIOMETRIA : OIDO DERECHO', 1, 1, 'L', 1);

$pdf->Cell(30, $h, 'Hz ', 0, 0, 'R', 0);
$pdf->Cell(150 / 9, $h, '125', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '250', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '500', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '1000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '2000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '3000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '4000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '6000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '8000', 1, 1, 'C', 0);

$pdf->Cell(30, $h, 'Db(A) ', 0, 0, 'R', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_od_125, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_od_250, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_od_500, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_od_1000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_od_2000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_od_3000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_od_4000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_od_6000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_od_8000, 1, 1, 'C', 0);

$pdf->Cell(30, $h, 'Db(O) ', 0, 0, 'R', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_od_125, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_od_250, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_od_500, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_od_1000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_od_2000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_od_3000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_od_4000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_od_6000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_od_8000, 1, 1, 'C', 0);


$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(150, $h, 'AUDIOMETRIA : OIDO IZQUIERDO', 1, 1, 'L', 1);

$pdf->Cell(30, $h, 'Hz ', 0, 0, 'R', 0);
$pdf->Cell(150 / 9, $h, '125', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '250', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '500', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '1000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '2000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '3000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '4000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '6000', 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, '8000', 1, 1, 'C', 0);

$pdf->Cell(30, $h, 'Db(A) ', 0, 0, 'R', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_oi_125, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_oi_250, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_oi_500, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_oi_1000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_oi_2000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_oi_3000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_oi_4000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_oi_6000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_aerea->data[0]->audio_a_oi_8000, 1, 1, 'C', 0);

$pdf->Cell(30, $h, 'Db(O) ', 0, 0, 'R', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_oi_125, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_oi_250, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_oi_500, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_oi_1000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_oi_2000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_oi_3000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_oi_4000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_oi_6000, 1, 0, 'C', 0);
$pdf->Cell(150 / 9, $h, $audio_osea->data[0]->audio_o_oi_8000, 1, 1, 'C', 0);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(30, $h * 2, 'Diagnostico Audiométrico', 1, 'C', 1, 0);
$pdf->Cell(150 / 2, $h, 'OIDO DERECHO', 1, 0, 'C', 0);
$pdf->Cell(150 / 2, $h, 'OIDO IZQUIERDO', 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(150 / 2, $h, $audio_aerea->data[0]->audio_a_od_diag, 1, 0, 'C', 0);
$pdf->Cell(150 / 2, $h, $audio_aerea->data[0]->audio_a_oi_diag, 1, 1, 'C', 0);



$pdf->Cell(180, $h, 'Exámenes Auxiliares', 1, 1, 'C', 1);

$pdf->Cell(100, $h * 3, 'Grupo Sanguineo:', 1, 0, 'L', 0);
$pdf->Cell(40, $h * 3, 'Hemoglobina: ' . $lab_hemo->data[0]->lab3_hem_hglo_r . ' gr.%', 1, 0, 'C', 0);
$pdf->Cell(40, $h, 'Reacciones Serológicas a Lues', 1, 1, 'L', 0);
$pdf->SetFont('helvetica', '', 7);

$lab7 = $model->laboratorio($_REQUEST['adm'], '164');
$pdf->Cell(100, $h, $lab7->data[0]->lab1_desc1, 0, 0, 'C', 0);
/*
 * * 
  $pdf->Cell(22, $h, '', 0, 0, 'L', 0);
  $pdf->Cell(5, $h, 'O', 0, 0, 'R', 0);
  $pdf->Cell(5, $h, 'x', 1, 0, 'C', 0);
  $pdf->Cell(2, $h, '', 0, 0, 'L', 0);

  $pdf->Cell(5, $h, 'A', 0, 0, 'R', 0);
  $pdf->Cell(5, $h, 'x', 1, 0, 'C', 0);
  $pdf->Cell(2, $h, '', 0, 0, 'L', 0);

  $pdf->Cell(5, $h, 'B', 0, 0, 'R', 0);
  $pdf->Cell(5, $h, 'x', 1, 0, 'C', 0);
  $pdf->Cell(2, $h, '', 0, 0, 'L', 0);

  $pdf->Cell(5, $h, 'AB', 0, 0, 'R', 0);
  $pdf->Cell(5, $h, 'x', 1, 0, 'C', 0);
  $pdf->Cell(7, $h, '', 0, 0, 'L', 0);

  $pdf->SetFont('helvetica', 'B', 8);
  $pdf->Cell(5, $h, '+', 0, 0, 'C', 0);
  $pdf->SetFont('helvetica', '', 7);
  $pdf->Cell(5, $h, 'x', 1, 0, 'C', 0);
  $pdf->Cell(2, $h, '', 0, 0, 'L', 0);

  $pdf->SetFont('helvetica', 'B', 8);
  $pdf->Cell(5, $h, '-', 0, 0, 'C', 0);
  $pdf->SetFont('helvetica', '', 7);
  $pdf->Cell(5, $h, 'x', 1, 0, 'C', 0);
  $pdf->Cell(3, $h, '', 0, 0, 'L', 0);
 */

$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h * 2, 'Negativas (  )', 1, 0, 'C', 0);
$pdf->Cell(20, $h * 2, 'Positivo (  )', 1, 1, 'C', 0);



$pdf->Cell(20, $h * 2, 'SERIE BLANCA', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'Segmentados:', 'T', 0, 'R', 0);
$pdf->Cell(20, $h, $lab_hemo->data[0]->lab3_hem_neut_r . ' %', 'T', 0, 'L', 0);
$pdf->Cell(20, $h, 'Monocitos:', 'T', 0, 'R', 0);
$pdf->Cell(20, $h, $lab_hemo->data[0]->lab3_hem_mono_r . ' %', 'T', 0, 'L', 0);
$pdf->Cell(20, $h, 'Abastonados:', 'T', 0, 'R', 0);
$pdf->Cell(20, $h, $lab_hemo->data[0]->lab3_hem_abas_r . ' %', 'T', 0, 'L', 0);
$pdf->Cell(20, $h, 'Eosinófilos:', 'T', 0, 'R', 0);
$pdf->Cell(20, $h, $lab_hemo->data[0]->lab3_hem_eosi_r . ' %', 'TR', 1, 'L', 0);

$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'Linfocitos:', 'B', 0, 'R', 0);
$pdf->Cell(20, $h, $lab_hemo->data[0]->lab3_hem_linf_r . ' %', 'B', 0, 'L', 0);
$pdf->Cell(20, $h, 'Basófilos:', 'B', 0, 'R', 0);
$pdf->Cell(100, $h, $lab_hemo->data[0]->lab3_hem_baso_r . ' %', 'BR', 1, 'L', 0);

$pdf->Cell(30, $h, 'PERFIL LIPIDICO', 1, 0, 'L', 1);
$pdf->Cell(15, $h, 'Colesterol:', 'TB', 0, 'R', 0);
$pdf->Cell(15, $h, $lab1->data[0]->lab1_desc1 . ' mg/dl', 'TB', 0, 'L', 0);
$pdf->Cell(10, $h, 'HDL:', 'TB', 0, 'R', 0);
$pdf->Cell(15, $h, $lab2->data[0]->lab1_desc1 . ' mg/dl', 'TB', 0, 'L', 0);
$pdf->Cell(10, $h, 'LDL:', 'TB', 0, 'R', 0);
$pdf->Cell(15, $h, $lab3->data[0]->lab1_desc1 . ' mg/dl', 'TB', 0, 'L', 0);
$pdf->Cell(15, $h, 'Trigliceridos:', 'TB', 0, 'R', 0);
$pdf->Cell(15, $h, $lab4->data[0]->lab1_desc1 . ' mg/dl', 'TB', 0, 'L', 0);
$pdf->Cell(25, $h, 'Riesgo Coronario:', 'TB', 0, 'R', 0);
$pdf->Cell(15, $h, ' mg/dl', 'TBR', 1, 'L', 0);


$pdf->Cell(30, $h, 'EKG', 1, 0, 'L', 1);
$pdf->Cell(150, $h, $ex_adicionales->data[0]->elec_diag, 1, 1, 'L', 0);


$pdf->Cell(30, $h, 'PAP', 1, 0, 'L', 1);
$pdf->Cell(150, $h, '', 1, 1, 'L', 0);

$pdf->Cell(55, $h, 'Prueba de Esfuerzo (IMC > 35 / May-45 años)', 1, 0, 'L', 1);
$pdf->Cell(70, $h, (($ex_adicionales->data[0]->pes_conid == 4) ? 'APTO - NEGATIVO A ISQUEMIA' : (($ex_adicionales->data[0]->pes_conid == 5) ? 'NO APTO' : '')), 1, 0, 'L', 0);
$pdf->Cell(10, $h, 'PSA', 1, 0, 'L', 1);
$pdf->Cell(45, $h, '', 1, 1, 'L', 0);



$pdf->Cell(15, $h, 'Orina:', 1, 0, 'L', 1);
$pdf->Cell(70, $h, $validacion->data[0]->val_orina, 1, 0, 'R', 0);
$pdf->Cell(25, $h, 'Antígeno Australiano:', 1, 0, 'L', 1);
$pdf->Cell(70, $h, '', 1, 1, 'L', 0);



$pdf->Cell(21, $h, 'Glucosa:', 1, 0, 'R', 1);
$pdf->Cell(15, $h, $lab5->data[0]->lab1_desc1 . ' mg / dl', 1, 0, 'L', 0);
$pdf->Cell(21, $h, 'Ac. Urico:', 1, 0, 'R', 1);
$pdf->Cell(15, $h, ' mg/dl', 1, 0, 'L', 0);
$pdf->Cell(21, $h, 'Creatinina:', 1, 0, 'R', 1);
$pdf->Cell(15, $h, ' mg/dl', 1, 0, 'L', 0);
$pdf->Cell(21, $h, 'TGO:', 1, 0, 'R', 1);
$pdf->Cell(15, $h, ' U/L', 1, 0, 'L', 0);
$pdf->Cell(21, $h, 'TGP:', 1, 0, 'R', 1);
$pdf->Cell(15, $h, ' U/L', 1, 1, 'L', 0);


$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'Exámenes Auxiliares Complementarios:', 'TLR', 1, 'L', 1);
$pdf->SetFont('helvetica', '', 7);
$pdf->MultiCell(180, $h, '', 'BRL', 'L', 1, 1);


$pdf->Cell(90, $h, 'Informe de Psicología: ' . (($ex_adicionales->data[0]->psico_apto == 88) ? 'APTO' : (($ex_adicionales->data[0]->psico_apto == 89) ? 'NO APTO' : '')), 1, 0, 'L', 0);
$pdf->Cell(90, $h, 'Informe de Psicosensometria: ' . (($ex_adicionales->data[0]->senso_cond == 7) ? 'APTO' : (($ex_adicionales->data[0]->senso_cond == 8) ? 'NO APTO' : '')), 1, 1, 'L', 0);

$pdf->Cell(90, $h, '', 1, 0, 'R', 0);
$pdf->Cell(20, $h, '', 'BT', 0, 'R', 0);
$pdf->Cell(15, $h, 'APTO', 'BT', 0, 'R', 0);
$pdf->Cell(5, $h, ($ex_adicionales->data[0]->senso_cond == 7) ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(10, $h, '', 'BT', 0, 'R', 0);
$pdf->Cell(15, $h, 'NO APTO', 'BT', 0, 'R', 0);
$pdf->Cell(5, $h, ($ex_adicionales->data[0]->senso_cond == 8) ? 'X' : '', 1, 0, 'C', 0);
$pdf->Cell(20, $h, '', 'BRT', 1, 'R', 0);



$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'DIGNOSTICOS:(CIE10)', 1, 1, 'L', 1);
//$pdf->Cell(20, $h, 'CIE10', 1, 1, 'C', 1);
$pdf->SetFont('helvetica', '', 7);

foreach ($diagnostico->data as $i => $row) {
    $pdf->ln(1);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->MultiCell(180, $h, $i + 1 . '.- ' . $row->diag_desc, 'B', 'L', 0, 1);
}






$pdf->SetFont('helvetica', 'B', 7);
$pdf->Cell(180, $h, 'INTERCONSULTAS Y CONTROLES:', 'TLR', 1, 'L', 0);
$pdf->SetFont('helvetica', '', 7);
foreach ($interconsulta->data as $i => $row) {
    $pdf->ln(1);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->MultiCell(180, $h, $i + 1 . '.- ' . $row->inter, 'B', 'L', 0, 1);
}


$pdf->MultiCell(180, $h, 'Descripción de la NO APTITUD:' . $validacion->data[0]->val_no_aptitud, 1, 'L', 1, 1);


$pdf->Cell(35, $h * 8, '', 1, 0, 'R', 0);
$pdf->Cell(145, $h * 4, '', 1, 0, 'R', 0);
$pdf->Ln(0.01);

$pdf->Cell(35, $h, '', 0, 0, 'R', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(145, $h, 'Nombre y Apellidos del Técnologo Médico que realizo los exámenes de laboratorio - Colegiatura N°', 0, 1, 'L', 0);
$pdf->SetFont('helvetica', 'B', 7);


$pdf->Cell(35, $h, 'APTITUD', 0, 1, 'L', 0);
$pdf->SetFont('helvetica', '', 7);


$pdf->Cell(30, $h, '', 0, 1, 'L', 0);


$pdf->Cell(35, $h, '( ' . (($validacion->data[0]->val_aptitud == 'APTO') ? 'X' : '') . ' ) APTO', 0, 1, 'L', 0);


$pdf->Cell(35, $h, '( ' . (($validacion->data[0]->val_aptitud == 'NO APTO TEMPORAL') ? 'X' : '') . ' ) NO APTO TEMPORAL', 0, 0, 'L', 0);
$pdf->Cell(145, $h * 4, '', 1, 0, 'R', 0);
$pdf->Ln(0.01);

$pdf->Cell(35, $h, '', 0, 0, 'R', 0);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(145, $h, 'Nombre y Apellidos del Médico - Colegiatura N°', 0, 1, 'L', 0);
$pdf->SetFont('helvetica', '', 7);


$pdf->Cell(35, $h, '( ' . (($validacion->data[0]->val_aptitud == 'NO APTO') ? 'X' : '') . ' ) NO APTO DEFINITIVO', 0, 1, 'L', 0);


$pdf->Cell(30, $h, '', 0, 1, 'L', 0);
$pdf->Cell(30, $h, '', 0, 1, 'L', 0);




$pdf->Cell(60, $h, 'Tiempo de aptitud médica:' . $validacion->data[0]->val_tiempo . ' Dias.', 1, 0, 'L', 0);
$pdf->Cell(60, $h, 'Fecha de Inicio:' . $validacion->data[0]->val_fech_ini, 1, 0, 'L', 0);
$pdf->Cell(60, $h, 'Fecha de Termino:' . $validacion->data[0]->val_fech_fin, 1, 1, 'L', 0);



$pdf->Cell(110, $h, 'CONTACTOS EN', 1, 0, 'L', 0);
$pdf->Cell(40, $h * 5, '', 1, 0, 'L', 0);
$pdf->Cell(30, $h * 4, '', 1, 0, 'L', 0);
$pdf->Ln(0.01);



$pdf->Cell(110, $h, '', 0, 0, 'L', 0);
$pdf->Cell(40, $h, 'Apellidos y Nombres del Pcte:', 0, 1, 'L', 0);


$pdf->MultiCell(30, $h * 2, 'En caso de Accidente contactar a:', 1, 'L', 0, 0);
$pdf->Cell(80, $h * 2, $validacion->data[0]->val_emer_contac . ' - ' . $validacion->data[0]->val_emer_parente, 1, 0, 'L', 0);
$pdf->MultiCell(40, $h * 3, $paciente->data[0]->nom_ap, 0, 'C', 0, 0);
$pdf->Cell(5, $h * 2, '', 0, 1, 'L', 0);


$pdf->Cell(20, $h, 'Parentesco:', 1, 0, 'L', 0);
$pdf->Cell(20, $h, $validacion->data[0]->val_emer_parente, 1, 0, 'L', 0);
$pdf->Cell(15, $h, 'Telefono:', 1, 0, 'L', 0);
$pdf->Cell(20, $h, '', 1, 0, 'L', 0);
$pdf->Cell(15, $h, 'Celular:', 1, 0, 'L', 0);
$pdf->Cell(20, $h, $validacion->data[0]->val_emer_cell, 1, 1, 'L', 0);


$pdf->Cell(110, $h, 'Dirección:' . $validacion->data[0]->val_emer_direc, 1, 0, 'L', 0);
$pdf->Cell(40, $h, 'DNI:' . $paciente->data[0]->pac_ndoc, 1, 0, 'C', 0);
$pdf->Cell(30, $h, 'Huella digital del Pcte', 1, 0, 'C', 0);





$pdf->Output('Anexo_16_' . $_REQUEST['adm'] . '.PDF', 'I');
