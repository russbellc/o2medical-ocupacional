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

$observaciones = $model->observaciones_16a($_REQUEST['adm']);
$anexo16 = $model->mod_medicina_anexo16($_REQUEST['adm']);
$med_16a = $model->mod_medicina_16a($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');
$h = 4.3;
$titulo = 7;
$texto = 7;
$salto = 2;
$pdf->SetFont('helvetica', 'B', 7);
//$pdf->Cell(180, $h, 'REG-03-PRO-SAL-01-03', 0, 1, 'R', 0);

$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(3);
$pdf->Cell(180, $h, 'ANEXO 16A / MODIFICADO', 0, 1, 'C', 0);
$pdf->Cell(180, $h, 'EVALUACION MEDICA PERFIL VISITA A 4000 m.s.n.m.', 0, 1, 'C', 0);
$pdf->Ln(5);



//$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'DATOS PERSONALES', 0, 1, 'L', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'APELLIDOS:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $paciente->data[0]->apellidos, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'NOMBRES:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $paciente->data[0]->nombre, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'EDAD:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $paciente->data[0]->edad . ' AÑOS', 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, 'DOCUMENTO DE IDENTIDAD', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'FECHA DE NACIMIENTO', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'SEXO', 1, 0, 'C', 1);
$pdf->Cell(30, $h, 'NACIONALIDAD', 1, 0, 'C', 1);
$pdf->Cell(45, $h, 'CORREO ELECTRONICO', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $paciente->data[0]->documento . ' : ' . $paciente->data[0]->pac_ndoc, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(30, $h, $paciente->data[0]->fech_naci, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $paciente->data[0]->sexo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(30, $h, '', 1, 0, 'C', 0); //////VALUE
$pdf->Cell(45, $h, $paciente->data[0]->pac_correo, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(17, $h, 'LUGAR DE', 'LRT', 0, 'C', 1);
$pdf->Cell(60, $h, 'DIRECCION', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'DISTRITO', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'PROVINCIA', 1, 0, 'C', 1);
$pdf->Cell(25, $h, 'DEPARTAMENTO', 1, 0, 'C', 1);
$pdf->Cell(28, $h, 'TIEMPO DE RESIDENCIA', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(17, $h, 'RESIDENCIA', 'LRB', 0, 'C', 1);
$pdf->SetFont('helvetica', '', 6);
$pdf->Cell(60, $h, $paciente->data[0]->direc, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->dist_ubigeo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $paciente->data[0]->prov_ubigeo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $paciente->data[0]->depa_ubigeo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(28, $h, '', 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(17, $h, 'EMPRESA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->Cell(75, $h, $paciente->data[0]->emp_desc, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(33, $h, 'ACTIVIDAD A REALIZAR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto - 1);
$pdf->Cell(55, $h, $paciente->data[0]->puesto, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'PERSONA DE CONTACTO', 'LTR', 0, 'C', 1);
$pdf->Cell(70, $h, 'NOMBRE', 1, 0, 'C', 1);
$pdf->Cell(40, $h, 'PARENTESCO', 1, 0, 'C', 1);
$pdf->Cell(35, $h, 'TELF DE EMERGENCIA', 1, 1, 'C', 1);
$pdf->Cell(35, $h, 'EN CASO DE EMERGENCIA', 'LBR', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $anexo16->data[0]->m_med_contac_nom, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, $anexo16->data[0]->m_med_contac_parent, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(35, $h, $anexo16->data[0]->m_med_contac_telf, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'COMPAÑÍA DE SEGUROS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $med_16a->data[0]->anexo_16a_seguros, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'CLINICA / CENTRO MEDICO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(55, $h, $med_16a->data[0]->anexo_16a_clinica, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(45, $h, 'ANFITRION MINERA LAS BAMBAS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(75, $h, $med_16a->data[0]->anexo_16a_anfitrion, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'FECHA PROBABLE DE VISITA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $med_16a->data[0]->anexo_16a_fech_visita, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'ANTECEDENTES MEDICOS PERSONALES', 0, 1, 'L', 0);

$pdf->Ln($salto);


$h_cardio = 3.5;

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(180, $h, '  PREGUNTAS', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', $texto - 0.7);
//////////////////////////////////////////////////////////////////////////////////////////////////
//$pdf->Cell(8, $h_cardio * 2, '1.', 1, 0, 'C', 0);
//$pdf->Cell(162, $h_cardio * 2, 'ACTUALMENTE FUMA 01 CIGARRILLO A MAS AL DIA?', 1, 0, 'L', 0);
//$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op01, 1, 1, 'C', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '1.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA TENIDO ALGUN TIPO DE ATAQUE, CONVULSION, PERDIDA DE CONOCIMIENTO O EPILEPSIA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op02, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc02, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '2.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿UD. SUFRE O HA SUFRIDO DE PRESION ARTERIAL ALTA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op03, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc03, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '3.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA SUFRIDO ALGUN TIPO DE TRASTORNO MENTAL / PSIQUIATRICO?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op04, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO / DOSIS?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc04, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '4.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA SUFRIDO DE ALGUN TRASTORNO DE SUEÑO?. ¿HA REQUERIDO PASTILLAS PARA DORMIR?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op05, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc05, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '5.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA SUFRIDO DE BRONQUITIS, OTROS PROBLEMAS RESPITORIOS EN LOS ULTIMOS 06 MESES?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op06, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, CUANDO, POR CUANTO TIEMPO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc06, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '6.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ALGUNA HISTORIA DE DIABETES EN LA FAMILIA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op07, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿QUIEN?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc07, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '7.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ALGUNA HISTORIA DE ENFERMEDAD RENAL?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op08, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc08, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '8.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA ESTADO ANTES SOBRE LO 4000 m DE ALTURA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op09, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿DONDE, CUANDO, ALGUN PROBLEMA?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc09, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '9.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA SIDO OPERADO DE / POR ALGO?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op10, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CAUSA, CUANDO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc10, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '10.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ALGUNA HISTORIA DE ANEMIA?, ¿SE ENCUENTRA EMBARAZADA?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op11, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, TRATAMIENTO?. ¿SI?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc11, 'BR', 1, 'L', 0); //////VALUE
//$pdf->setVisibility('screen');
//$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', '', 'T', false, 300, '', false, false, 0, false, false, false);
////
//$pdf->AddPage('P', 'A4');
//$pdf->Ln($salto * 3);
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '11.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ALGUNA HISTORIA DE ENFERMEDAD DE COAGULACION O TROMBOSIS?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op12, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc12, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '12.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿UD. SUFRE DE DOLOR DE PECHO O FALTA DE AIRE AL ESFUERZO?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op13, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc13, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '13.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿UD. SUFRE DE PROBLEMAS CARDIACOS, ANGINA, USA MARCAPASOS?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op14, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc14, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '14.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿UD. SUFRE DE PROBLEMAS CARDIACOS, ANGINA, USA MARCAPASOS?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op15, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc15, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '15.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿SE LE HA DIAGNOSTICADO OBESIDAD MORBIDA (IMC>35 Kg/m2)', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op16, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUANDO, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc16, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '16.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿ESTA TOMANDO ALGUN MEDICAMENTO ACTUALMENTE?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op17, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, 'CUAL(ES), EN QUE DOSIS?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc17, 'BR', 1, 'L', 0); //////VALUE
//////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->Cell(8, $h_cardio * 2, '17.', 1, 0, 'C', 0);
$pdf->Cell(162, $h_cardio, '¿HA TENIDO ALGUN OTRO PROBEMA DE SALUD?', 'LTR', 0, 'L', 0);
$pdf->Cell(10, $h_cardio * 2, $anexo16->data[0]->m_med_cardio_op18, 1, 0, 'C', 0); //////VALUE
$pdf->Ln($h_cardio);
$pdf->Cell(8, $h_cardio * 2, '', 0, 0, 'C', 0);
$pdf->Cell(45, $h_cardio, '¿CUAL, TRATAMIENTO?', 'LB', 0, 'L', 0);
$pdf->Cell(117, $h_cardio, $anexo16->data[0]->m_med_cardio_desc18, 'BR', 1, 'L', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'EXAMEN MEDICO (a ser completado por el doctor)', 0, 1, 'L', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'PESO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'HEMOGLOBINA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'GLUCOSA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 1, 'C', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ESTATURA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'HEMATOCRITO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'HbA1C', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 1, 'C', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'IMC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'TRIGLICERIDOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'GRUPO Y FACTOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 1, 'C', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'PRESION ARTERIAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'COL. TOTAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'RIESGO CORONARIO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 1, 'C', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'PULSO (¿regular?)', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'HDL COLESTEROL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, '', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, '', 1, 1, 'C', 0);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'FREC. RESPIRATORIA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'LDL COLESTEROL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(35, $h, 'SUB UNIDAD B HGC SERICA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 1, 'C', 0); //////VALUE


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'SAT O2', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(25, $h, $paciente->data[0]->tipo, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);





$pdf->AddPage('P', 'A4');
$h = 4.3;
$titulo = 7;
$texto = 7;
$salto = 2;
$pdf->SetFont('helvetica', 'B', 7);
//$pdf->Cell(180, $h, 'REG-03-PRO-SAL-01-03', 0, 1, 'R', 0);

$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(3);
$pdf->Ln($salto * 7);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'EKG', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(150, $h, $paciente->data[0]->tipo, 1, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'PRUEBA DE ESFUERZO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(150, $h, $paciente->data[0]->tipo, 1, 1, 'L', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'POR LO QUE CERTIFICO  QUE EL/LA PACIENTE SE ENCUENTRA:', 0, 1, 'L', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(140, $h * 2, 'PARA ASCENDER A GRANDES ALTITUDES (mas de 4,000 m.s.n.m.)', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h * 2, $med_16a->data[0]->anexo_16a_aptitud, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, 'DATOS PERSONALES DEL MEDICO EVALUADOR', 0, 1, 'L', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'APELLIDOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(65, $h, $med_16a->data[0]->med_apellidos, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'CLINICA DONDE SE EVALUO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, 'CENTRO MEDICO OPTIMA', 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'NOMBRES', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(65, $h, $med_16a->data[0]->medico_nombre, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'TELEFONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, '', 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, 'CMP', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(65, $h, $med_16a->data[0]->medico_cmp, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(35, $h, 'FECHA DE EVALUACIÓN', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $med_16a->data[0]->anexo_16a_fech_evalua, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(65, $h, '', 0, 0, 'C', 0);
$pdf->Cell(10, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $texto);
$pdf->Cell(67, $h, 'VACUNA CONTRA LA INFLUENZA (adjunta constancia)', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(8, $h, $med_16a->data[0]->anexo_16a_vacuna, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(180, $h, 'La aptitud definitiva la dará el CM Antapaccay por el Auditor Médico de la empresa asignada', 0, 1, 'L', 0);

$pdf->Ln($salto);


$observ_total = $observaciones->total;

$conteo = array();
foreach ($observaciones->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->obs_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

foreach ($observaciones->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $h_text * ($conteo[$i] + 1) : $h);
    $text_tamaño = (($conteo[$i] != 0) ? $texto - 1 : ((strlen($row2->obs_desc) > 74) ? $texto - 1 : $texto));
    if ($i === 0) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, (($conteo[$i] != 0) ? $h_text : $h) * ($observ_total + $fila_total), 'OBSERVACIONES', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(122, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(10, $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $$text_tamaño);
        $pdf->Cell(15, $salteos, $row2->obs_plazo, 1, 1, 'C', 0);
    } else {
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(122, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(10, $salteos, 'PLAZO', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->Cell(15, $salteos, $row2->obs_plazo, 1, 1, 'C', 0);
    }
}

//
//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('Anexo_16_' . $_REQUEST['adm'] . '.PDF', 'I');
