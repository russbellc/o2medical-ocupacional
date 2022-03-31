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
$osteo_conclusion = $model->osteo_conclusion($_REQUEST['adm']);

$osteo_musc = $model->mod_medicina_osteo_musc($_REQUEST['adm']);
$triaje = $model->rpt_triaje($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');
$h = 4.3;
$titulo = 7;
$texto = 7;
$salto = 2;
$pdf->SetFont('helvetica', 'B', 7);

$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
//$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');
//$pdf->SetFont('helvetica', 'B', 10);
//$pdf->Ln(4);
//$pdf->Cell(180, $h, 'EXAMEN OCUPACIONAL OSTEO MUSCULAR', 0, 1, 'C', 0);
//$pdf->Ln(8);

$pdf->SetFont('helvetica', 'B', 10);
//$pdf->Ln(4);
$pdf->Cell(40, $h * 4, '', 0, 0, 'C', 0);
$pdf->Cell(100, $h * 4, 'EXAMEN OCUPACIONAL OSTEO MUSCULAR', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(40, $h * 4, $osteo_musc->data[0]->m_osteo_aptitud, 1, 1, 'C', 1);
//$pdf->Ln(8);

$pdf->Ln($salto);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'EXAMEN MEDICO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'EMPRESA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(90, $h, $paciente->data[0]->emp_desc, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'APELLIDOS:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $paciente->data[0]->apellidos, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'NOMBRES:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $paciente->data[0]->nombre, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'EDAD:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $paciente->data[0]->edad . ' AÑOS', 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'DNI / ID:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->pac_ndoc, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'FECHA DE EXAMEN:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->fech_reg, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'PUESTO AL QUE POSTULA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(90, $h, $paciente->data[0]->puesto, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'RLT', 0, 'C', 1);
$pdf->Cell(30, $h, 'TRAUMÁTICOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(125, $h, $osteo_musc->data[0]->m_osteo_trauma, 1, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'ANTECEDENTES', 'RL', 0, 'C', 1);
$pdf->Cell(30, $h, 'DEGENERATIVOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(125, $h, $osteo_musc->data[0]->m_osteo_degenera, 1, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'MÚSCULO', 'RL', 0, 'C', 1);
$pdf->Cell(30, $h, 'CONGÉNITOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(125, $h, $osteo_musc->data[0]->m_osteo_congeni, 1, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'ESQUELETICOS', 'RL', 0, 'C', 1);
$pdf->Cell(30, $h, 'QUIRÚRGICOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(125, $h, $osteo_musc->data[0]->m_osteo_quirur, 1, 1, 'L', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'RLB', 0, 'C', 1);
$pdf->Cell(30, $h, 'TRATAMIENTO ACTUAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(125, $h, $osteo_musc->data[0]->m_osteo_trata, 1, 1, 'L', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'VALORACION FISICA', 1, 0, 'C', 1);
$pdf->Cell(15, $h, 'TALLA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $triaje->data[0]->m_tri_triaje_talla, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'PESO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $triaje->data[0]->m_tri_triaje_peso, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'IMC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $triaje->data[0]->m_tri_triaje_imc, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(40, $h, $triaje->data[0]->m_tri_triaje_nutricion_dx, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo + 1);
$pdf->Cell(180, $h, 'AMNANESIS', 0, 1, 'L', 0);

$pdf->Ln($salto);

////////////TITULOS
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'LT', 0, 'C', 1);
$pdf->Cell(25, $h, '', 'TR', 0, 'C', 1);
$pdf->Cell(30, $h, 'DURACION DE LAS', 'RLT', 0, 'C', 1);
$pdf->Cell(25, $h, 'TIEMPO DE', 'RLT', 0, 'C', 1); //TIEMPO DE INICIO DE MOLESTIAS 
$pdf->Cell(25, $h, 'DURACIÓN DE', 'RLT', 0, 'C', 1); //DURACIÓN DE CADA EPISODIO  DE DOLOR 
$pdf->Cell(25, $h, 'RECIBIÓ', 'RLT', 0, 'C', 1); //RECIBIÓ TRATAMIENTO MÉDICO
$pdf->Cell(25, $h, 'DÍAS', 'RLT', 1, 'C', 1); //DÍAS DE TRATAMIENTO


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(25, $h, '', 'R', 0, 'C', 1);
$pdf->Cell(30, $h, ' MOLESTIAS EN LOS', 'RL', 0, 'C', 1);
$pdf->Cell(25, $h, 'INICIO', 'RL', 0, 'C', 1);
$pdf->Cell(25, $h, 'CADA EPISODIO', 'RL', 0, 'C', 1);
$pdf->Cell(25, $h, 'TRATAMIENTO', 'RL', 0, 'C', 1);
$pdf->Cell(25, $h, 'DE', 'RL', 1, 'C', 1);


$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(25, $h, '', 'R', 0, 'C', 1);
$pdf->Cell(30, $h, 'ULTIMOS 3 MESES', 'RLB', 0, 'C', 1);
$pdf->Cell(25, $h, 'DE MOLESTIAS', 'RLB', 0, 'C', 1);
$pdf->Cell(25, $h, 'DE DOLOR', 'RLB', 0, 'C', 1);
$pdf->Cell(25, $h, 'MÉDICO', 'RLB', 0, 'C', 1);
$pdf->Cell(25, $h, 'TRATAMIENTO', 'RLB', 1, 'C', 1);


////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(25, $h, 'CUELLO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_cuello_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_cuello_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_cuello_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_cuello_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_cuello_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(25, $h, 'ESPALDA ALTA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_espalda_a_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_espalda_a_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_espalda_a_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_espalda_a_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_espalda_a_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(25, $h, 'ESPALDA BAJA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_espalda_b_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_espalda_b_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_espalda_b_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_espalda_b_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_espalda_b_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(20, $h * 2, 'HOMBROS', 1, 0, 'C', 1);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_hombro_d_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_hombro_d_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_hombro_d_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_hombro_d_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_hombro_d_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_hombro_i_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_hombro_i_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_hombro_i_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_hombro_i_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_hombro_i_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'EXPLORACION DE', 'L', 0, 'C', 1);
$pdf->Cell(20, $h, 'CODOS /', 'LRT', 0, 'C', 1); //CODOS / ANTEBRAZOS
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_codo_d_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_codo_d_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_codo_d_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_codo_d_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_codo_d_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'SINTOMATOLOGÍA', 'L', 0, 'C', 1); //EXPLORACION DE SINTOMATOLOGÍA DOLOROSA
$pdf->Cell(20, $h, 'ANTEBRAZOS', 'LRB', 0, 'C', 1); //CODOS / ANTEBRAZOS
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_codo_i_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_codo_i_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_codo_i_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_codo_i_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_codo_i_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'DOLOROSA', 'L', 0, 'C', 1);
$pdf->Cell(20, $h, 'MUÑECA /', 'LRT', 0, 'C', 1); //MUÑECA / MANO
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_mano_d_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_mano_d_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_mano_d_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_mano_d_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_mano_d_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(20, $h, 'MANO', 'LRB', 0, 'C', 1);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_mano_i_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_mano_i_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_mano_i_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_mano_i_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_mano_i_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(20, $h, 'CADERAS /', 'LRT', 0, 'C', 1); //CADERAS / MUSLOS
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_muslo_d_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_muslo_d_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_muslo_d_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_muslo_d_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_muslo_d_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(20, $h, 'MUSLOS', 'LRB', 0, 'C', 1);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_muslo_i_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_muslo_i_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_muslo_i_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_muslo_i_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_muslo_i_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(20, $h * 2, 'RODILLAS', 1, 0, 'C', 1);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_rodilla_d_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_rodilla_d_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_rodilla_d_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_rodilla_d_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_rodilla_d_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(20, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_rodilla_i_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_rodilla_i_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_rodilla_i_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_rodilla_i_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_rodilla_i_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'L', 0, 'C', 1);
$pdf->Cell(20, $h, 'TOBILLOS /', 'LRT', 0, 'C', 1); //TOBILLOS / PIES
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_pies_d_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_pies_d_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_pies_d_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_pies_d_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_pies_d_dias_trata, 1, 1, 'C', 0); //////VALUE
////////////VALUES
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '', 'LB', 0, 'C', 1);
$pdf->Cell(20, $h, 'PIES', 'LRB', 0, 'C', 1);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_pies_i_dura_3meses, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_pies_i_time_ini, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_pies_i_dura_dolor, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_pies_i_recib_trata, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(25, $h, $osteo_musc->data[0]->m_osteo_pies_i_dias_trata, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'OBSERVACIONES', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(150, $h, $osteo_musc->data[0]->m_osteo_anames_obs, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo + 1);
$pdf->Cell(180, $h, 'EXAMEN FISICO', 0, 1, 'L', 0);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 'LTR', 0, 'C', 1);
$pdf->Cell(43, $h, 'DESVIACION DEL EJE', 'LRT', 0, 'C', 1);
$pdf->Cell(30, $h, 'LORDOSIS CERVICAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(22, $h, $osteo_musc->data[0]->m_osteo_lordo_cervic, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'DESVIACIONES', 'LRT', 0, 'C', 1);
$pdf->Cell(30, $h, 'HALLAZGOS', 1, 1, 'C', 1);

$pdf->Cell(30, $h, '', 'LR', 0, 'C', 1);
$pdf->Cell(43, $h, 'ANTERO - POSTERIOR', 'LR', 0, 'C', 1);
$pdf->Cell(30, $h, 'CIFOSIS DORSAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(22, $h, $osteo_musc->data[0]->m_osteo_cifosis, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, 'LATERALES', 'LR', 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $osteo_musc->data[0]->m_osteo_desvia_lat_halla, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'COLUMNA', 'LR', 0, 'C', 1);
$pdf->Cell(43, $h, 'EVALUACIÓN EN BIPEDESTACIÓN', 'LRB', 0, 'C', 1);
$pdf->Cell(30, $h, 'LORDOSIS LUMBAR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(22, $h, $osteo_musc->data[0]->m_osteo_lordo_lumbar, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(25, $h, '(TEST DE ADAMS)', 'LRB', 0, 'C', 1);
$pdf->Cell(17, $h, 'ESCOLIOSIS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(13, $h, $osteo_musc->data[0]->m_osteo_desvia_lat_escolio, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(30, 0, '', 'LR', 0, 'C', 1);
$pdf->Cell(150, 0, '', 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'VERTEBRAL', 'LR', 0, 'C', 1);
$pdf->Cell(20, $h, 'PALPACIÓN', 'LRT', 0, 'C', 1);
$pdf->Cell(50, $h, 'APÓFISIS ESPINOSAS DOLOROSAS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_apofisis, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS.', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $osteo_musc->data[0]->m_osteo_apofisis_obs, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 'LR', 0, 'C', 1);
$pdf->Cell(20, $h, 'DE', 'LR', 0, 'C', 1);
$pdf->Cell(50, $h, 'CONTRACTURA MUSCULAR CERVICAL', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_contra_musc_cervic, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS.', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $osteo_musc->data[0]->m_osteo_contra_musc_cervic_obs, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 'LRB', 0, 'C', 1);
$pdf->Cell(20, $h, 'COLUMNA', 'LRB', 0, 'C', 1);
$pdf->Cell(50, $h, 'CONTRACTURA MUSCULAR LUMBAR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_contra_musc_lumbar, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS.', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(60, $h, $osteo_musc->data[0]->m_osteo_contra_musc_lumbar_obs, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 'LRT', 0, 'C', 1);
$pdf->Cell(30, $h * 4, 'CUELLO', 1, 0, 'C', 1);
$pdf->Cell(40, $h * 2, 'FLEXIÓN (0-45°)', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h * 2, $osteo_musc->data[0]->m_osteo_cuello_flex, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h * 2, 'FLEXIÓN LATERAL (0-45°)', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cuello_flex_lat_d, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'CUELLO Y', 'LR', 0, 'C', 1);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cuello_flex_lat_i, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'TRONCO', 'LR', 0, 'C', 1);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(40, $h * 2, 'EXTENSIÓN (0-45°)', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h * 2, $osteo_musc->data[0]->m_osteo_cuello_ext, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h * 2, 'ROTACIÓN (0-60°)', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cuello_ext_rot_d, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '(EVALUAR LOS', 'LR', 0, 'C', 1); //CUELLO Y TRONCO (EVALUAR LOS RANGOS  DE  MOVILIDAD ACTIVA Y PASIVA)
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cuello_ext_rot_i, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(30, 0, '', 'LR', 0, 'C', 1);
$pdf->Cell(150, 0, '', 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'DE  MOVILIDAD', 'LR', 0, 'C', 1);
$pdf->Cell(30, $h * 4, 'TRONCO', 1, 0, 'C', 1);
$pdf->Cell(40, $h * 2, 'FLEXIÓN (0-80°,10CM)', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h * 2, $osteo_musc->data[0]->m_osteo_tronco_flex, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h * 2, 'FLEXIÓN LATERAL (0-35°)', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tronco_flex_lat_d, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'ACTIVA Y', 'LR', 0, 'C', 1);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tronco_flex_lat_i, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'PASIVA)', 'LR', 0, 'C', 1);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(40, $h * 2, 'EXTENSIÓN (0-20-30°)', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h * 2, $osteo_musc->data[0]->m_osteo_tronco_ext, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h * 2, 'ROTACIÓN  (0-45°)', 1, 0, 'C', 1);
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tronco_ext_rot_d, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 'LR', 0, 'C', 1);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(50, $h, '', 0, 0, 'C', 0);
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, '', 0, 0, 'C', 0);
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tronco_ext_rot_i, 1, 1, 'C', 0); //////VALUE

$pdf->Cell(30, 0, '', 'LR', 0, 'C', 1);
$pdf->Cell(150, 0, '', 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 'LR', 0, 'C', 1);
$pdf->Cell(150, $h, 'HIPERMOVILIDAD/ACORTAMIENTOS, FUERZA MUSCULAR ', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 'LRB', 0, 'C', 1);
$pdf->Cell(25, $h, 'COMENTARIOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(125, $h, $osteo_musc->data[0]->m_osteo_hiper_acor_f_coment, 1, 1, 'C', 0); //////VALUE


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
//
//$pdf->Ln($salto);
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 42, '





















EXTREMIDADES SUPERIORES (EVALUAR LOS RANGOS DE  MOVILIDAD ACTIVA Y PASIVA)', 1, 'C', 1, 0); //===>TITULO
$pdf->Cell(30, $h * 20, 'HOMBROS', 1, 0, 'C', 1);
$pdf->MultiCell(30, $h * 5, '


FLEXIÓN (0-180°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_flex_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '

ADUCCIÓN HORIZONTAL (0-135°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_adu_h_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_flex_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_adu_h_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_flex_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_adu_h_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_flex_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_adu_h_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_flex_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_adu_h_color, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 5, '


EXTENSIÓN (0-60°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_ext_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '

ROTACIÓN INTERNA (0-70°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_in_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_ext_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_in_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_ext_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_in_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_ext_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_in_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_ext_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_in_color, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 5, '


ABDUCCIÓN (0-180°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abduc_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '

ROTACIÓN EXTERNA (0-90°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_ex_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abduc_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_ex_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abduc_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_ex_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abduc_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_ex_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abduc_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_rot_ex_color, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 5, '

ABDUCCIÓN HORIZONTAL (0-45°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abd_h_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h * 5, '', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 1, 'C', 0);
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abd_h_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 1, 'C', 0); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abd_h_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 1, 'C', 0); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abd_h_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 1, 'C', 0); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_hombro_abd_h_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 1, 'C', 0);






//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(30, $h * 10, 'CODO Y ANTEBRAZO', 1, 0, 'C', 1);
$pdf->MultiCell(30, $h * 5, '


FLEXIÓN (0-150°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_flex_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '


SUPINACIÓN (0-80°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_supina_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_flex_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_supina_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_flex_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_supina_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_flex_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_supina_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_flex_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_supina_color, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 5, '


EXTENSIÓN (0-180°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_ext_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '


PRONACIÓN (0-80°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_prona_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_ext_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_prona_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_ext_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_prona_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_ext_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_prona_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_ext_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_codo_prona_color, 1, 1, 'C', 0);






//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(30, $h * 10, 'MUÑECA', 1, 0, 'C', 1);
$pdf->MultiCell(30, $h * 5, '


FLEXIÓN (0-80°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_flex_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '

DESVIACIÓN CUBITAL (0-30°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_cubi_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_flex_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_cubi_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_flex_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_cubi_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_flex_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_cubi_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_flex_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_cubi_color, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 5, '


EXTENSIÓN (0-70°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_ext_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '

DESVIACIÓN RADIAL (0-20°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_radi_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_ext_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_radi_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_ext_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_radi_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_ext_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_radi_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_ext_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_muneca_des_radi_color, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(150, $h, 'ACORTAMIENTOS/FLACIDECES, FUERZA MUSCULAR, SENSIBILIDAD', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(25, $h, 'COMENTARIOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(125, $h, $osteo_musc->data[0]->m_osteo_sup_acor_fu_sen_coment, 1, 1, 'C', 0); //////VALUE




$pdf->Ln($salto);
$pdf->Ln($salto);

//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 15, '







EXTREMIDADES INFERIORES (EVALUAR LOS RANGOS DE  MOVILIDAD ACTIVA Y PASIVA)', 1, 'C', 1, 0); //===>TITULO
$pdf->Cell(30, $h * 15, 'CADERA', 1, 0, 'C', 1);
$pdf->MultiCell(30, $h * 5, '


FLEXIÓN (0-120°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_flex_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '


ADUCCIÓN  (0-30°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_aduc_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_flex_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_aduc_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_flex_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_aduc_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_flex_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_aduc_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_flex_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_aduc_color, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 5, '


EXTENSIÓN (0-30°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_ext_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '

ROTACIÓN INTERNA (0-45°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_int_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_ext_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_int_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_ext_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_int_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_ext_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_int_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_ext_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_int_color, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 5, '


ABDUCCIÓN (0-45°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_abduc_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '

 ROTACIÓN EXTERNA (0-45°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_ext_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_abduc_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_ext_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_abduc_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_ext_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_abduc_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_ext_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_abduc_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_cader_rota_ext_color, 1, 1, 'C', 0); //////VALUE


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



//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 22, '











EXTREMIDADES INFERIORES (EVALUAR LOS RANGOS DE  MOVILIDAD ACTIVA Y PASIVA)', 1, 'C', 1, 0); //===>TITULO
$pdf->Cell(30, $h * 10, 'RODILLA', 1, 0, 'C', 1);
$pdf->MultiCell(30, $h * 5, '


FLEXIÓN (0-135°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_flex_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '


ROTACIÓN TIBIAL ', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_rota_tibi_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_flex_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_rota_tibi_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_flex_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_rota_tibi_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_flex_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_rota_tibi_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_flex_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_rota_tibi_color, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 5, '


EXTENSIÓN (0-180°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_ext_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h * 5, '', 1, 0, 'C', 0);
$pdf->Cell(5, $h, '', 0, 1, 'C', 0);
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_ext_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 1, 'C', 0); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_ext_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 1, 'C', 0); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_ext_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 1, 'C', 0); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_rodill_ext_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 1, 'C', 0);






//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(30, $h * 10, 'TOBILLO', 1, 0, 'C', 1);
$pdf->MultiCell(30, $h * 5, '


DORSIFLEXIÓN (0-20°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_dorsi_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '


INVERSIÓN (0-35°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_inver_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_dorsi_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_inver_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_dorsi_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_inver_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_dorsi_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_inver_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_dorsi_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_inver_color, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(60, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 5, '

FLEXIÓN PLANTAR (0-50°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_flex_plan_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 5, '


EVERSIÓN (0-15°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h, 'DERECHO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_ever_der, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_flex_plan_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'IZQUIERDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_ever_izq, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_flex_plan_fuerza, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'FUERZA', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_ever_fuerza, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_flex_plan_tono, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'TONO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_ever_tono, 1, 1, 'C', 0); //////VALUE

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_flex_plan_color, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(20, $h, 'COLOR', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $osteo_musc->data[0]->m_osteo_tobill_ever_color, 1, 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(150, $h, 'ACORTAMIENTOS/FLACIDECES, FUERZA MUSCULAR, SENSIBILIDAD', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(25, $h, 'COMENTARIOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(125, $h, $osteo_musc->data[0]->m_osteo_inf_acor_fu_sen_coment, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);
$h = 6.4;
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 14, '













EXTREMIDADES SUPERIORES', 1, 'C', 1, 0); //===>TITULO
$pdf->MultiCell(30, $h * 2, 'TEST DE JOBE ( ELEVAR BRAZOS CONTRA RESISTENCIA - PULGAR ABAJO)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/1.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_test_jobe_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_test_jobe_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_test_jobe_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_test_jobe_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
MANIOBRA DE APLEY ( TEST DEL RASCADO)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/2.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_apley_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_apley_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_apley_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_apley_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
PALPACIÓN DE EPICÓNDILO LATERAL', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/3.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_palpa_epi_lat_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_palpa_epi_lat_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_palpa_epi_lat_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_palpa_epi_lat_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
PALPACIÓN DE EPICÓNDILO MEDIAL', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/4.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_palpa_epi_med_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_palpa_epi_med_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_palpa_epi_med_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_palpa_epi_med_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
TEST DE PHALEN (PALMAS 90°)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/5.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_test_phalen_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_test_phalen_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_test_phalen_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_test_phalen_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
TEST DE TINEL (PERCUTIR MEDIANO)', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/6.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_test_tinel_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_test_tinel_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_test_tinel_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_test_tinel_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
TEST DE 
FINKELSTEIN', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/7.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_test_finkelstein_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_test_finkelstein_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_test_finkelstein_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_test_finkelstein_izq_obs, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 6, '




EXTREMIDADES INFERIORES', 1, 'C', 1, 0); //===>TITULO
$pdf->MultiCell(30, $h * 2, '
MANIOBRA DE LASEGUE', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/8.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_lasegue_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_lasegue_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_lasegue_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_lasegue_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
MANIOBRA DE 
BRADGARD', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, '', 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_bradga_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_bradga_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_bradga_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_bradga_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
MANIOBRA DE 
THOMAS', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, '', 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_thomas_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_thomas_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_thomas_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_thomas_izq_obs, 1, 1, 'C', 0); //////VALUE


$pdf->AddPage('P', 'A4');
$pdf->SetFont('helvetica', 'B', 7);
//$pdf->Cell(180, $h, 'REG-03-PRO-SAL-01-03', 0, 1, 'R', 0);

$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(3);
$pdf->Ln($salto * 7);

$pdf->Ln($salto);

//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 6, '




EXTREMIDADES INFERIORES', 1, 'C', 1, 0); //===>TITULO
$pdf->MultiCell(30, $h * 2, '
MANIOBRA DE FABERE
PATRICK', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/9.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_fabere_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_fabere_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_fabere_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_fabere_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
MANIOBRA DE VARO 
Y VALGO DOLOROSA', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, '', 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_varo_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_varo_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_varo_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_varo_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
MANIOBRA DE CAJON 
ANTERIOR DEL TOBILLO', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, '', 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_cajon_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_cajon_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_mani_cajon_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_mani_cajon_izq_obs, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo + 1);
$pdf->Cell(180, 4.3, 'EXAMEN NEUROLOGICO', 0, 1, 'L', 0);

$pdf->Ln($salto);

//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 8, '





VALORACION DE REFLEJOS', 1, 'C', 1, 0); //===>TITULO
$pdf->MultiCell(30, $h * 2, '
REFLEJO
BICIPITAL', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/10.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_refle_bicipi_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_refle_bicipi_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_refle_bicipi_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_refle_bicipi_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
REFLEJO
TRICIPITAL', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/11.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_refle_trici_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_refle_trici_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_refle_trici_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_refle_trici_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
REFLEJO PATELAR
O ROTULIANO', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/12.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_refle_patelar_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_refle_patelar_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_refle_patelar_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_refle_patelar_izq_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->MultiCell(30, $h * 2, '
REFLEJO
AQUILIANO', 1, 'C', 0, 0); //===>TITULO
$pdf->Cell(20, $h * 2, $pdf->Image('images/osteoMuscular/13.jpg', '', '', 20, '', 'JPG'), 1, 0, 'C', 0);
$pdf->Cell(5, $h, 'D', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_refle_aquilia_der, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_refle_aquilia_der_obs, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(80, $h, '', 0, 0, 'C', 0);
$pdf->Cell(5, $h, 'I', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $osteo_musc->data[0]->m_osteo_refle_aquilia_izq, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'OBS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $osteo_musc->data[0]->m_osteo_refle_aquilia_izq_obs, 1, 1, 'C', 0); //////VALUE


$h = 4.3;

$pdf->Ln($salto);
$pdf->Ln($salto);

$observ_total = $osteo_conclusion->total;

$conteo = array();
foreach ($osteo_conclusion->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->obs_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

foreach ($osteo_conclusion->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $h_text * ($conteo[$i] + 1) : $h);
    $text_tamaño = (($conteo[$i] != 0) ? $texto - 1 : ((strlen($row2->obs_desc) > 74) ? $texto - 1 : $texto));
    if ($i === 0) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, (($conteo[$i] != 0) ? $h_text : $h) * ($observ_total + $fila_total), 'RECOMENDACIONES', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(147, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 1);
    } else {
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(147, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 1);
    }
}

//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('Anexo_16_' . $_REQUEST['adm'] . '.PDF', 'I');
