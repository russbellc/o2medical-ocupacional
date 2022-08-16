<?php

class model extends core
{

    public function list_paciente()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $usuario = $this->user->us_id;
        $fecha = date('Y-m-d');
        $columna = isset($_POST['columna']) ? $_POST['columna'] : NULL;
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
        ($usuario == "psico_juan") ? $user = " and adm_fech BETWEEN '2021-11-24 00:00:00' AND '$fecha 23:59:59'" :  $user = "";
        $sql1 = ("SELECT
                    adm_id as adm,adm_foto
                    ,tfi_desc,emp_desc, pac_ndoc
                    ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
                    ,concat(adm_puesto,' - ',adm_area)as puesto,tfi_desc
                    ,concat(pac_appat,' ',pac_apmat,' ',pac_nombres)as nombre
                    ,concat(pac_nombres)as nom
                    ,concat(pac_appat,' ',pac_apmat)as ape
                    ,pac_sexo, adm_fech fecha
                    ,count(adm_id) nro_examenes
                    #,if((aud_pdf)=1,1,0) pdf
                    FROM admision
                    inner join paciente on adm_pac=pac_id
                    inner join pack on adm_ruta=pk_id
                    left join empresa on pk_emp=emp_id
                    left join tficha on adm_tficha=tfi_id
                    inner join dpack on dpk_pkid=pk_id
                    inner join examen on ex_id=dpk_exid
                    where
                    ex_arid IN (6)  $user
                    ");
        $empresa = $this->user->empresas;
        ($this->user->acceso == 1) ? '' : $sql1 .= "and emp_id IN ($empresa) ";
        if (!is_null($columna) && !is_null($query)) {
            if ($columna == "1") {
                $sql1 .= "and adm_id=$query";
            } else if ($columna == "2") {
                $sql1 .= "and pac_ndoc=$query";
            } else if ($columna == "3") {
                $sql1 .= "and concat(pac_appat,' ',pac_apmat,' ',pac_nombres) like '%$query%'";
            } else if ($columna == "4" && $this->user->acceso == 1) {
                $sql1 .= "and emp_desc like '%$query%' or emp_id like '%$query%'";
            } else if ($columna == "5") {
                $sql1 .= "and tfi_desc LIKE '%$query%'";
            }
        }
        $sql1 .= " group by adm_id order by adm_id DESC;";
        $sql = $this->sql($sql1);
        foreach ($sql->data as $i => $value) {
            $adm_id = $value->adm;
            $nro_examenes = $value->nro_examenes;
            $verifica = $this->sql("SELECT count(m_psicologia_adm)total FROM mod_psicologia where m_psicologia_adm=$adm_id;");
            $total = $verifica->data[0]->total;
            $value->st = ($nro_examenes == $total) ? '1' : '0';
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_formatos()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm_id = isset($_POST['adm']) ? $_POST['adm'] : NULL;
        $q = "SELECT adm_id as adm, ex_desc,pk_id,ex_id,pac_sexo
                FROM admision
                inner join pack on adm_ruta=pk_id
                inner join dpack on dpk_pkid=pk_id
                inner join examen on ex_id=dpk_exid
                inner join paciente on pac_id=adm_pac
                where ex_arid IN (6) and adm_id=$adm_id order by ex_arid;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_psicologia_id id,m_psicologia_st st
                , m_psicologia_usu usu, m_psicologia_fech_reg fech 
            FROM mod_psicologia 
            where m_psicologia_adm=$adm_id and m_psicologia_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    //LOAD SAVE UPDATE LABORATORIO

    public function load_examenLab()
    {
        $adm = $_POST['adm'];
        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_psicologia_informe where m_psico_inf_adm='$adm' and m_lab_exam_examen='$examen';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_exaLab()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':m_psicologia_st'] = '1'; //ESTADO DEL MODULO PRINCIPAL

        $params[':adm'] = $adm;
        $params[':ex_id'] = $exa;

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_lab_exam_resultado'] = $_POST['m_lab_exam_resultado'];
        $params[':m_lab_exam_observaciones'] = $_POST['m_lab_exam_observaciones'];
        $params[':m_lab_exam_diagnostico'] = $_POST['m_lab_exam_diagnostico'];


        $q = "INSERT INTO mod_psicologia_informe VALUES 
                (null,
                :adm,
                :ex_id,
                :m_lab_exam_resultado,
                :m_lab_exam_observaciones,
                :m_lab_exam_diagnostico);
                
                INSERT INTO mod_psicologia VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_psicologia_st,
                :ex_id);";

        $verifica = $this->sql("SELECT 
		m_psicologia_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_psicologia 
		inner join sys_usuario on usu_id=m_psicologia_usu 
		where 
		m_psicologia_adm='$adm' and m_psicologia_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql = $this->sql($q, $params);
            if ($sql->success) {
                $this->commit();
                return $sql;
            } else {
                $this->rollback();
                return array('success' => false);
            }
        }
    }

    public function update_exaLab()
    {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_lab_exam_resultado'] = $_POST['m_lab_exam_resultado'];
        $params[':m_lab_exam_observaciones'] = $_POST['m_lab_exam_observaciones'];
        $params[':m_lab_exam_diagnostico'] = $_POST['m_lab_exam_diagnostico'];

        $this->begin();
        $q = 'Update mod_psicologia_informe set
                    m_lab_exam_resultado=:m_lab_exam_resultado,
                    m_lab_exam_observaciones=:m_lab_exam_observaciones,
                    m_lab_exam_diagnostico=:m_lab_exam_diagnostico
                where
                m_psico_inf_adm=:adm and m_lab_exam_examen=:ex_id;
                
                update mod_psicologia set
                    m_psicologia_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_psicologia_adm=:adm and m_psicologia_examen=:ex_id;';


        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    //LOAD SAVE UPDATE psico_informe

    public function load_psico_informe()
    {
        $adm = $_POST['adm'];
        //        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_psicologia_informe where m_psico_inf_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_psico_informe()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_psicologia VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                1,
                :ex_id);";

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_psico_inf_capac_intelectual'] = $_POST['m_psico_inf_capac_intelectual'];
        $params_2[':m_psico_inf_aten_concentracion'] = $_POST['m_psico_inf_aten_concentracion'];
        $params_2[':m_psico_inf_orient_espacial'] = $_POST['m_psico_inf_orient_espacial'];
        $params_2[':m_psico_inf_pers_htp'] = $_POST['m_psico_inf_pers_htp'];
        $params_2[':m_psico_inf_pers_salamanca'] = $_POST['m_psico_inf_pers_salamanca'];
        $params_2[':m_psico_inf_intel_emocional'] = $_POST['m_psico_inf_intel_emocional'];
        $params_2[':m_psico_inf_caracterologia'] = $_POST['m_psico_inf_caracterologia'];
        $params_2[':m_psico_inf_alturas'] = $_POST['m_psico_inf_alturas'];
        $params_2[':m_psico_inf_esp_confinados'] = $_POST['m_psico_inf_esp_confinados'];
        $params_2[':m_psico_inf_otros'] = $_POST['m_psico_inf_otros'];
        $params_2[':m_psico_inf_precis_destre_reac'] = $_POST['m_psico_inf_precis_destre_reac'];
        $params_2[':m_psico_inf_antici_bim_mono'] = $_POST['m_psico_inf_antici_bim_mono'];
        $params_2[':m_psico_inf_actitud_f_trans'] = $_POST['m_psico_inf_actitud_f_trans'];
        $params_2[':m_psico_inf_resultados'] = $_POST['m_psico_inf_resultados'];
        $params_2[':m_psico_inf_debilidades'] = $_POST['m_psico_inf_debilidades'];
        $params_2[':m_psico_inf_conclusiones'] = $_POST['m_psico_inf_conclusiones'];
        $params_2[':m_psico_inf_recomendaciones'] = $_POST['m_psico_inf_recomendaciones'];
        $params_2[':m_psico_inf_puesto_trabajo'] = $_POST['m_psico_inf_puesto_trabajo'];
        $params_2[':m_psico_inf_brigadista'] = $_POST['m_psico_inf_brigadista'];
        $params_2[':m_psico_inf_conduc_equip_liviano'] = $_POST['m_psico_inf_conduc_equip_liviano'];
        $params_2[':m_psico_inf_conduc_equip_pesado'] = $_POST['m_psico_inf_conduc_equip_pesado'];
        $params_2[':m_psico_inf_trabajo_altura'] = $_POST['m_psico_inf_trabajo_altura'];
        $params_2[':m_psico_inf_trab_esp_confinado'] = $_POST['m_psico_inf_trab_esp_confinado'];

        $params_2[':m_psico_inf_grieger'] = $_POST['m_psico_inf_grieger'];
        $params_2[':m_psico_inf_htp'] = $_POST['m_psico_inf_htp'];
        $params_2[':m_psico_inf_raven'] = $_POST['m_psico_inf_raven'];
        $params_2[':m_psico_inf_laberinto'] = $_POST['m_psico_inf_laberinto'];
        $params_2[':m_psico_inf_bender'] = $_POST['m_psico_inf_bender'];
        $params_2[':m_psico_inf_bc4'] = $_POST['m_psico_inf_bc4'];
        $params_2[':m_psico_inf_precision'] = $_POST['m_psico_inf_precision'];
        $params_2[':m_psico_inf_destreza'] = $_POST['m_psico_inf_destreza'];
        $params_2[':m_psico_inf_reaccion'] = $_POST['m_psico_inf_reaccion'];
        $params_2[':m_psico_inf_toulous'] = $_POST['m_psico_inf_toulous'];


        $q_2 = "INSERT INTO mod_psicologia_informe VALUES 
                (null,
                :adm,
                :m_psico_inf_capac_intelectual,
                :m_psico_inf_aten_concentracion,
                :m_psico_inf_orient_espacial,
                :m_psico_inf_pers_htp,
                :m_psico_inf_pers_salamanca,
                :m_psico_inf_intel_emocional,
                :m_psico_inf_caracterologia,
                :m_psico_inf_alturas,
                :m_psico_inf_esp_confinados,
                :m_psico_inf_otros,
                :m_psico_inf_precis_destre_reac,
                :m_psico_inf_antici_bim_mono,
                :m_psico_inf_actitud_f_trans,
                :m_psico_inf_resultados,
                :m_psico_inf_debilidades,
                :m_psico_inf_conclusiones,
                :m_psico_inf_recomendaciones,
                :m_psico_inf_puesto_trabajo,
                :m_psico_inf_brigadista,
                :m_psico_inf_conduc_equip_liviano,
                :m_psico_inf_conduc_equip_pesado,
                :m_psico_inf_trabajo_altura,
                :m_psico_inf_trab_esp_confinado,
                :m_psico_inf_grieger,
                :m_psico_inf_htp,
                :m_psico_inf_raven,
                :m_psico_inf_laberinto,
                :m_psico_inf_bender,
                :m_psico_inf_bc4,
                :m_psico_inf_precision,
                :m_psico_inf_destreza,
                :m_psico_inf_reaccion,
                :m_psico_inf_toulous);";

        $verifica = $this->sql("SELECT 
		m_psicologia_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_psicologia 
		inner join sys_usuario on usu_id=m_psicologia_usu 
		where 
		m_psicologia_adm='$adm' and m_psicologia_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    $this->commit();
                    return $sql_2;
                } else {
                    $this->rollback();
                    return array('success' => false, 'error' => 'Problemas con el registro.');
                }
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        }
    }

    public function update_psico_informe()
    {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_psicologia set
                    m_psicologia_usu=:usuario,
                    m_psicologia_fech_update=now()
                where
                m_psicologia_id=:id and m_psicologia_adm=:adm and m_psicologia_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_psico_inf_capac_intelectual'] = $_POST['m_psico_inf_capac_intelectual'];
        $params_2[':m_psico_inf_aten_concentracion'] = $_POST['m_psico_inf_aten_concentracion'];
        $params_2[':m_psico_inf_orient_espacial'] = $_POST['m_psico_inf_orient_espacial'];
        $params_2[':m_psico_inf_pers_htp'] = $_POST['m_psico_inf_pers_htp'];
        $params_2[':m_psico_inf_pers_salamanca'] = $_POST['m_psico_inf_pers_salamanca'];
        $params_2[':m_psico_inf_intel_emocional'] = $_POST['m_psico_inf_intel_emocional'];
        $params_2[':m_psico_inf_caracterologia'] = $_POST['m_psico_inf_caracterologia'];
        $params_2[':m_psico_inf_alturas'] = $_POST['m_psico_inf_alturas'];
        $params_2[':m_psico_inf_esp_confinados'] = $_POST['m_psico_inf_esp_confinados'];
        $params_2[':m_psico_inf_otros'] = $_POST['m_psico_inf_otros'];
        $params_2[':m_psico_inf_precis_destre_reac'] = $_POST['m_psico_inf_precis_destre_reac'];
        $params_2[':m_psico_inf_antici_bim_mono'] = $_POST['m_psico_inf_antici_bim_mono'];
        $params_2[':m_psico_inf_actitud_f_trans'] = $_POST['m_psico_inf_actitud_f_trans'];
        $params_2[':m_psico_inf_resultados'] = $_POST['m_psico_inf_resultados'];
        $params_2[':m_psico_inf_debilidades'] = $_POST['m_psico_inf_debilidades'];
        $params_2[':m_psico_inf_conclusiones'] = $_POST['m_psico_inf_conclusiones'];
        $params_2[':m_psico_inf_recomendaciones'] = $_POST['m_psico_inf_recomendaciones'];
        $params_2[':m_psico_inf_puesto_trabajo'] = $_POST['m_psico_inf_puesto_trabajo'];
        $params_2[':m_psico_inf_brigadista'] = $_POST['m_psico_inf_brigadista'];
        $params_2[':m_psico_inf_conduc_equip_liviano'] = $_POST['m_psico_inf_conduc_equip_liviano'];
        $params_2[':m_psico_inf_conduc_equip_pesado'] = $_POST['m_psico_inf_conduc_equip_pesado'];
        $params_2[':m_psico_inf_trabajo_altura'] = $_POST['m_psico_inf_trabajo_altura'];
        $params_2[':m_psico_inf_trab_esp_confinado'] = $_POST['m_psico_inf_trab_esp_confinado'];

        $params_2[':m_psico_inf_grieger'] = $_POST['m_psico_inf_grieger'];
        $params_2[':m_psico_inf_htp'] = $_POST['m_psico_inf_htp'];
        $params_2[':m_psico_inf_raven'] = $_POST['m_psico_inf_raven'];
        $params_2[':m_psico_inf_laberinto'] = $_POST['m_psico_inf_laberinto'];
        $params_2[':m_psico_inf_bender'] = $_POST['m_psico_inf_bender'];
        $params_2[':m_psico_inf_bc4'] = $_POST['m_psico_inf_bc4'];
        $params_2[':m_psico_inf_precision'] = $_POST['m_psico_inf_precision'];
        $params_2[':m_psico_inf_destreza'] = $_POST['m_psico_inf_destreza'];
        $params_2[':m_psico_inf_reaccion'] = $_POST['m_psico_inf_reaccion'];
        $params_2[':m_psico_inf_toulous'] = $_POST['m_psico_inf_toulous'];


        $q_2 = 'Update mod_psicologia_informe set                    
                m_psico_inf_capac_intelectual=:m_psico_inf_capac_intelectual,
                m_psico_inf_aten_concentracion=:m_psico_inf_aten_concentracion,
                m_psico_inf_orient_espacial=:m_psico_inf_orient_espacial,
                m_psico_inf_pers_htp=:m_psico_inf_pers_htp,
                m_psico_inf_pers_salamanca=:m_psico_inf_pers_salamanca,
                m_psico_inf_intel_emocional=:m_psico_inf_intel_emocional,
                m_psico_inf_caracterologia=:m_psico_inf_caracterologia,
                m_psico_inf_alturas=:m_psico_inf_alturas,
                m_psico_inf_esp_confinados=:m_psico_inf_esp_confinados,
                m_psico_inf_otros=:m_psico_inf_otros,
                m_psico_inf_precis_destre_reac=:m_psico_inf_precis_destre_reac,
                m_psico_inf_antici_bim_mono=:m_psico_inf_antici_bim_mono,
                m_psico_inf_actitud_f_trans=:m_psico_inf_actitud_f_trans,
                m_psico_inf_resultados=:m_psico_inf_resultados,
                m_psico_inf_debilidades=:m_psico_inf_debilidades,
                m_psico_inf_conclusiones=:m_psico_inf_conclusiones,
                m_psico_inf_recomendaciones=:m_psico_inf_recomendaciones,
                m_psico_inf_puesto_trabajo=:m_psico_inf_puesto_trabajo,
                m_psico_inf_brigadista=:m_psico_inf_brigadista,
                m_psico_inf_conduc_equip_liviano=:m_psico_inf_conduc_equip_liviano,
                m_psico_inf_conduc_equip_pesado=:m_psico_inf_conduc_equip_pesado,
                m_psico_inf_trabajo_altura=:m_psico_inf_trabajo_altura,
                m_psico_inf_trab_esp_confinado=:m_psico_inf_trab_esp_confinado,
                m_psico_inf_grieger=:m_psico_inf_grieger,
                m_psico_inf_htp=:m_psico_inf_htp,
                m_psico_inf_raven=:m_psico_inf_raven,
                m_psico_inf_laberinto=:m_psico_inf_laberinto,
                m_psico_inf_bender=:m_psico_inf_bender,
                m_psico_inf_bc4=:m_psico_inf_bc4,
                m_psico_inf_precision=:m_psico_inf_precision,
                m_psico_inf_destreza=:m_psico_inf_destreza,
                m_psico_inf_reaccion=:m_psico_inf_reaccion,
                m_psico_inf_toulous=:m_psico_inf_toulous
                where
                m_psico_inf_adm=:adm;';


        $sql_2 = $this->sql($q_2, $params_2);
        if ($sql_2->success) {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success && $sql_1->total == 1) {
                $this->commit();
                return $sql_1;
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }

    //LOAD SAVE UPDATE psico_examen

    public function load_psico_examen()
    {
        $adm = $_POST['adm'];
        //        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_psicologia_examen where m_psico_exam_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_psico_examen()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_psicologia VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                1,
                :ex_id);";

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_psico_exam_activ_empresa'] = $_POST['m_psico_exam_activ_empresa'];
        $params_2[':m_psico_exam_area_trabajo'] = $_POST['m_psico_exam_area_trabajo'];
        $params_2[':m_psico_exam_tiempo_labor'] = $_POST['m_psico_exam_tiempo_labor'];
        $params_2[':m_psico_exam_puesto'] = $_POST['m_psico_exam_puesto'];
        $params_2[':m_psico_exam_princ_riesgos'] = $_POST['m_psico_exam_princ_riesgos'];
        $params_2[':m_psico_exam_medi_seguridad'] = $_POST['m_psico_exam_medi_seguridad'];
        $params_2[':m_psico_exam_histo_familiar'] = $_POST['m_psico_exam_histo_familiar'];
        $params_2[':m_psico_exam_accid_enfermedad'] = $_POST['m_psico_exam_accid_enfermedad'];
        $params_2[':m_psico_exam_habitos'] = $_POST['m_psico_exam_habitos'];
        $params_2[':m_psico_exam_otras_obs'] = $_POST['m_psico_exam_otras_obs'];
        $params_2[':m_psico_exam_presentacion'] = $_POST['m_psico_exam_presentacion'];
        $params_2[':m_psico_exam_postura'] = $_POST['m_psico_exam_postura'];
        $params_2[':m_psico_exam_ritmo'] = $_POST['m_psico_exam_ritmo'];
        $params_2[':m_psico_exam_tono'] = $_POST['m_psico_exam_tono'];
        $params_2[':m_psico_exam_articulacion'] = $_POST['m_psico_exam_articulacion'];
        $params_2[':m_psico_exam_tiempo'] = $_POST['m_psico_exam_tiempo'];
        $params_2[':m_psico_exam_espacio'] = $_POST['m_psico_exam_espacio'];
        $params_2[':m_psico_exam_persona'] = $_POST['m_psico_exam_persona'];
        $params_2[':m_psico_exam_lucido_atent'] = $_POST['m_psico_exam_lucido_atent'];
        $params_2[':m_psico_exam_pensamiento'] = $_POST['m_psico_exam_pensamiento'];
        $params_2[':m_psico_exam_persepcion'] = $_POST['m_psico_exam_persepcion'];
        $params_2[':m_psico_exam_memoria'] = $_POST['m_psico_exam_memoria'];
        $params_2[':m_psico_exam_inteligencia'] = $_POST['m_psico_exam_inteligencia'];
        $params_2[':m_psico_exam_apetito'] = $_POST['m_psico_exam_apetito'];
        $params_2[':m_psico_exam_sueno'] = $_POST['m_psico_exam_sueno'];
        $params_2[':m_psico_exam_personalidad'] = $_POST['m_psico_exam_personalidad'];
        $params_2[':m_psico_exam_afectividad'] = $_POST['m_psico_exam_afectividad'];
        $params_2[':m_psico_exam_conduc_sexual'] = $_POST['m_psico_exam_conduc_sexual'];
        $params_2[':m_psico_exam_area_cognitiva'] = $_POST['m_psico_exam_area_cognitiva'];
        $params_2[':m_psico_exam_area_emocional'] = $_POST['m_psico_exam_area_emocional'];
        $params_2[':m_psico_exam_ptje_test_01'] = $_POST['m_psico_exam_ptje_test_01'];
        $params_2[':m_psico_exam_ptje_test_02'] = $_POST['m_psico_exam_ptje_test_02'];
        $params_2[':m_psico_exam_ptje_test_03'] = $_POST['m_psico_exam_ptje_test_03'];
        $params_2[':m_psico_exam_ptje_test_04'] = $_POST['m_psico_exam_ptje_test_04'];
        $params_2[':m_psico_exam_ptje_test_05'] = $_POST['m_psico_exam_ptje_test_05'];
        $params_2[':m_psico_exam_ptje_test_06'] = $_POST['m_psico_exam_ptje_test_06'];
        $params_2[':m_psico_exam_ptje_test_07'] = $_POST['m_psico_exam_ptje_test_07'];
        $params_2[':m_psico_exam_ptje_test_08'] = $_POST['m_psico_exam_ptje_test_08'];
        $params_2[':m_psico_exam_ptje_test_09'] = $_POST['m_psico_exam_ptje_test_09'];
        $params_2[':m_psico_exam_ptje_test_10'] = $_POST['m_psico_exam_ptje_test_10'];
        $params_2[':m_psico_exam_ptje_test_11'] = $_POST['m_psico_exam_ptje_test_11'];
        $params_2[':m_psico_exam_ptje_test_12'] = $_POST['m_psico_exam_ptje_test_12'];
        $params_2[':m_psico_exam_ptje_test_13'] = $_POST['m_psico_exam_ptje_test_13'];
        $params_2[':m_psico_exam_ptje_test_14'] = $_POST['m_psico_exam_ptje_test_14'];

        $params_2[':m_psico_exam_motivo_eva'] = $_POST['m_psico_exam_motivo_eva'];
        $params_2[':m_psico_exam_operacion'] = $_POST['m_psico_exam_operacion'];
        // $params_2[':m_psico_exam_ante01_fech_ini'] = $_POST['m_psico_exam_ante01_fech_ini'];

        
        $timestamp1 = strtotime($_POST['m_psico_exam_ante01_fech_ini']);
        $m_psico_exam_ante01_fech_ini = ((strlen($_POST['m_psico_exam_ante01_empresa']) > 0) ? date('Y-m-d', $timestamp1) : null);
        $params_2[':m_psico_exam_ante01_fech_ini'] = $m_psico_exam_ante01_fech_ini;


        $params_2[':m_psico_exam_ante01_empresa'] = $_POST['m_psico_exam_ante01_empresa'];
        $params_2[':m_psico_exam_ante01_act_emp'] = $_POST['m_psico_exam_ante01_act_emp'];
        $params_2[':m_psico_exam_ante01_puesto'] = $_POST['m_psico_exam_ante01_puesto'];
        $params_2[':m_psico_exam_ante01_opera'] = $_POST['m_psico_exam_ante01_opera'];
        $params_2[':m_psico_exam_ante01_causa'] = $_POST['m_psico_exam_ante01_causa'];
        // $params_2[':m_psico_exam_ante02_fech_ini'] = $_POST['m_psico_exam_ante02_fech_ini'];

        
        $timestamp2 = strtotime($_POST['m_psico_exam_ante02_fech_ini']);
        $m_psico_exam_ante02_fech_ini = ((strlen($_POST['m_psico_exam_ante02_empresa']) > 0) ? date('Y-m-d', $timestamp2) : null);
        $params_2[':m_psico_exam_ante02_fech_ini'] = $m_psico_exam_ante02_fech_ini;


        $params_2[':m_psico_exam_ante02_empresa'] = $_POST['m_psico_exam_ante02_empresa'];
        $params_2[':m_psico_exam_ante02_act_emp'] = $_POST['m_psico_exam_ante02_act_emp'];
        $params_2[':m_psico_exam_ante02_puesto'] = $_POST['m_psico_exam_ante02_puesto'];
        $params_2[':m_psico_exam_ante02_opera'] = $_POST['m_psico_exam_ante02_opera'];
        $params_2[':m_psico_exam_ante02_causa'] = $_POST['m_psico_exam_ante02_causa'];
        // $params_2[':m_psico_exam_ante03_fech_ini'] = $_POST['m_psico_exam_ante03_fech_ini'];

        
        $timestamp3 = strtotime($_POST['m_psico_exam_ante03_fech_ini']);
        $m_psico_exam_ante03_fech_ini = ((strlen($_POST['m_psico_exam_ante03_empresa']) > 0) ? date('Y-m-d', $timestamp3) : null);
        $params_2[':m_psico_exam_ante03_fech_ini'] = $m_psico_exam_ante03_fech_ini;


        $params_2[':m_psico_exam_ante03_empresa'] = $_POST['m_psico_exam_ante03_empresa'];
        $params_2[':m_psico_exam_ante03_act_emp'] = $_POST['m_psico_exam_ante03_act_emp'];
        $params_2[':m_psico_exam_ante03_puesto'] = $_POST['m_psico_exam_ante03_puesto'];
        $params_2[':m_psico_exam_ante03_opera'] = $_POST['m_psico_exam_ante03_opera'];
        $params_2[':m_psico_exam_ante03_causa'] = $_POST['m_psico_exam_ante03_causa'];
        $params_2[':m_psico_exam_niv_intelectual'] = $_POST['m_psico_exam_niv_intelectual'];
        $params_2[':m_psico_exam_co_visomotriz'] = $_POST['m_psico_exam_co_visomotriz'];
        $params_2[':m_psico_exam_niv_memoria'] = $_POST['m_psico_exam_niv_memoria'];
        $params_2[':m_psico_exam_persona_desc'] = $_POST['m_psico_exam_persona_desc'];
        $params_2[':m_psico_exam_afectivi_desc'] = $_POST['m_psico_exam_afectivi_desc'];
        $params_2[':m_psico_exam_test_maslash'] = $_POST['m_psico_exam_test_maslash'];
        $params_2[':m_psico_exam_test_intelig'] = $_POST['m_psico_exam_test_intelig'];
        $params_2[':m_psico_exam_test_fatiga'] = $_POST['m_psico_exam_test_fatiga'];
        $params_2[':m_psico_exam_test_somnolencia'] = $_POST['m_psico_exam_test_somnolencia'];
        $params_2[':m_psico_exam_test_ansiedad'] = $_POST['m_psico_exam_test_ansiedad'];
        $params_2[':m_psico_exam_test_depresion'] = $_POST['m_psico_exam_test_depresion'];
        $params_2[':m_psico_exam_test_acrofobia'] = $_POST['m_psico_exam_test_acrofobia'];
        $params_2[':m_psico_exam_test_estres'] = $_POST['m_psico_exam_test_estres'];
        $params_2[':m_psico_exam_aptitud'] = $_POST['m_psico_exam_aptitud'];
        $params_2[':m_psico_exam_aptitud_desc'] = $_POST['m_psico_exam_aptitud_desc'];
        $params_2[':m_psico_exam_medico'] = $_POST['m_psico_exam_medico'];


        $q_2 = "INSERT INTO mod_psicologia_examen VALUES 
                (null,
                :adm,
                :m_psico_exam_activ_empresa,
                :m_psico_exam_area_trabajo,
                :m_psico_exam_tiempo_labor,
                :m_psico_exam_puesto,
                :m_psico_exam_princ_riesgos,
                :m_psico_exam_medi_seguridad,
                :m_psico_exam_histo_familiar,
                :m_psico_exam_accid_enfermedad,
                :m_psico_exam_habitos,
                :m_psico_exam_otras_obs,
                :m_psico_exam_presentacion,
                :m_psico_exam_postura,
                :m_psico_exam_ritmo,
                :m_psico_exam_tono,
                :m_psico_exam_articulacion,
                :m_psico_exam_tiempo,
                :m_psico_exam_espacio,
                :m_psico_exam_persona,
                :m_psico_exam_lucido_atent,
                :m_psico_exam_pensamiento,
                :m_psico_exam_persepcion,
                :m_psico_exam_memoria,
                :m_psico_exam_inteligencia,
                :m_psico_exam_apetito,
                :m_psico_exam_sueno,
                :m_psico_exam_personalidad,
                :m_psico_exam_afectividad,
                :m_psico_exam_conduc_sexual,
                :m_psico_exam_area_cognitiva,
                :m_psico_exam_area_emocional,
                :m_psico_exam_ptje_test_01,
                :m_psico_exam_ptje_test_02,
                :m_psico_exam_ptje_test_03,
                :m_psico_exam_ptje_test_04,
                :m_psico_exam_ptje_test_05,
                :m_psico_exam_ptje_test_06,
                :m_psico_exam_ptje_test_07,
                :m_psico_exam_ptje_test_08,
                :m_psico_exam_ptje_test_09,
                :m_psico_exam_ptje_test_10,
                :m_psico_exam_ptje_test_11,
                :m_psico_exam_ptje_test_12,
                :m_psico_exam_ptje_test_13,
                :m_psico_exam_ptje_test_14,
                :m_psico_exam_motivo_eva,
                :m_psico_exam_operacion,
                :m_psico_exam_ante01_fech_ini,
                :m_psico_exam_ante01_empresa,
                :m_psico_exam_ante01_act_emp,
                :m_psico_exam_ante01_puesto,
                :m_psico_exam_ante01_opera,
                :m_psico_exam_ante01_causa,
                :m_psico_exam_ante02_fech_ini,
                :m_psico_exam_ante02_empresa,
                :m_psico_exam_ante02_act_emp,
                :m_psico_exam_ante02_puesto,
                :m_psico_exam_ante02_opera,
                :m_psico_exam_ante02_causa,
                :m_psico_exam_ante03_fech_ini,
                :m_psico_exam_ante03_empresa,
                :m_psico_exam_ante03_act_emp,
                :m_psico_exam_ante03_puesto,
                :m_psico_exam_ante03_opera,
                :m_psico_exam_ante03_causa,
                :m_psico_exam_niv_intelectual,
                :m_psico_exam_co_visomotriz,
                :m_psico_exam_niv_memoria,
                :m_psico_exam_persona_desc,
                :m_psico_exam_afectivi_desc,
                :m_psico_exam_test_maslash,
                :m_psico_exam_test_intelig,
                :m_psico_exam_test_fatiga,
                :m_psico_exam_test_somnolencia,
                :m_psico_exam_test_ansiedad,
                :m_psico_exam_test_depresion,
                :m_psico_exam_test_acrofobia,
                :m_psico_exam_test_estres,
                :m_psico_exam_aptitud,
                :m_psico_exam_aptitud_desc,
                :m_psico_exam_medico);";

        $verifica = $this->sql("SELECT 
		m_psicologia_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_psicologia 
		inner join sys_usuario on usu_id=m_psicologia_usu 
		where 
		m_psicologia_adm='$adm' and m_psicologia_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    $this->commit();
                    return $sql_2;
                } else {
                    $this->rollback();
                    return array('success' => false, 'error' => 'Problemas con el registro.');
                }
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        }
    }

    public function update_psico_examen()
    {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_psicologia set
                    m_psicologia_usu=:usuario,
                    m_psicologia_fech_update=now()
                where
                m_psicologia_id=:id and m_psicologia_adm=:adm and m_psicologia_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_psico_exam_activ_empresa'] = $_POST['m_psico_exam_activ_empresa'];
        $params_2[':m_psico_exam_area_trabajo'] = $_POST['m_psico_exam_area_trabajo'];
        $params_2[':m_psico_exam_tiempo_labor'] = $_POST['m_psico_exam_tiempo_labor'];
        $params_2[':m_psico_exam_puesto'] = $_POST['m_psico_exam_puesto'];
        $params_2[':m_psico_exam_princ_riesgos'] = $_POST['m_psico_exam_princ_riesgos'];
        $params_2[':m_psico_exam_medi_seguridad'] = $_POST['m_psico_exam_medi_seguridad'];
        $params_2[':m_psico_exam_histo_familiar'] = $_POST['m_psico_exam_histo_familiar'];
        $params_2[':m_psico_exam_accid_enfermedad'] = $_POST['m_psico_exam_accid_enfermedad'];
        $params_2[':m_psico_exam_habitos'] = $_POST['m_psico_exam_habitos'];
        $params_2[':m_psico_exam_otras_obs'] = $_POST['m_psico_exam_otras_obs'];
        $params_2[':m_psico_exam_presentacion'] = $_POST['m_psico_exam_presentacion'];
        $params_2[':m_psico_exam_postura'] = $_POST['m_psico_exam_postura'];
        $params_2[':m_psico_exam_ritmo'] = $_POST['m_psico_exam_ritmo'];
        $params_2[':m_psico_exam_tono'] = $_POST['m_psico_exam_tono'];
        $params_2[':m_psico_exam_articulacion'] = $_POST['m_psico_exam_articulacion'];
        $params_2[':m_psico_exam_tiempo'] = $_POST['m_psico_exam_tiempo'];
        $params_2[':m_psico_exam_espacio'] = $_POST['m_psico_exam_espacio'];
        $params_2[':m_psico_exam_persona'] = $_POST['m_psico_exam_persona'];
        $params_2[':m_psico_exam_lucido_atent'] = $_POST['m_psico_exam_lucido_atent'];
        $params_2[':m_psico_exam_pensamiento'] = $_POST['m_psico_exam_pensamiento'];
        $params_2[':m_psico_exam_persepcion'] = $_POST['m_psico_exam_persepcion'];
        $params_2[':m_psico_exam_memoria'] = $_POST['m_psico_exam_memoria'];
        $params_2[':m_psico_exam_inteligencia'] = $_POST['m_psico_exam_inteligencia'];
        $params_2[':m_psico_exam_apetito'] = $_POST['m_psico_exam_apetito'];
        $params_2[':m_psico_exam_sueno'] = $_POST['m_psico_exam_sueno'];
        $params_2[':m_psico_exam_personalidad'] = $_POST['m_psico_exam_personalidad'];
        $params_2[':m_psico_exam_afectividad'] = $_POST['m_psico_exam_afectividad'];
        $params_2[':m_psico_exam_conduc_sexual'] = $_POST['m_psico_exam_conduc_sexual'];
        $params_2[':m_psico_exam_area_cognitiva'] = $_POST['m_psico_exam_area_cognitiva'];
        $params_2[':m_psico_exam_area_emocional'] = $_POST['m_psico_exam_area_emocional'];
        $params_2[':m_psico_exam_ptje_test_01'] = $_POST['m_psico_exam_ptje_test_01'];
        $params_2[':m_psico_exam_ptje_test_02'] = $_POST['m_psico_exam_ptje_test_02'];
        $params_2[':m_psico_exam_ptje_test_03'] = $_POST['m_psico_exam_ptje_test_03'];
        $params_2[':m_psico_exam_ptje_test_04'] = $_POST['m_psico_exam_ptje_test_04'];
        $params_2[':m_psico_exam_ptje_test_05'] = $_POST['m_psico_exam_ptje_test_05'];
        $params_2[':m_psico_exam_ptje_test_06'] = $_POST['m_psico_exam_ptje_test_06'];
        $params_2[':m_psico_exam_ptje_test_07'] = $_POST['m_psico_exam_ptje_test_07'];
        $params_2[':m_psico_exam_ptje_test_08'] = $_POST['m_psico_exam_ptje_test_08'];
        $params_2[':m_psico_exam_ptje_test_09'] = $_POST['m_psico_exam_ptje_test_09'];
        $params_2[':m_psico_exam_ptje_test_10'] = $_POST['m_psico_exam_ptje_test_10'];
        $params_2[':m_psico_exam_ptje_test_11'] = $_POST['m_psico_exam_ptje_test_11'];
        $params_2[':m_psico_exam_ptje_test_12'] = $_POST['m_psico_exam_ptje_test_12'];
        $params_2[':m_psico_exam_ptje_test_13'] = $_POST['m_psico_exam_ptje_test_13'];
        $params_2[':m_psico_exam_ptje_test_14'] = $_POST['m_psico_exam_ptje_test_14'];

        $params_2[':m_psico_exam_motivo_eva'] = $_POST['m_psico_exam_motivo_eva'];
        $params_2[':m_psico_exam_operacion'] = $_POST['m_psico_exam_operacion'];
        // $params_2[':m_psico_exam_ante01_fech_ini'] = $_POST['m_psico_exam_ante01_fech_ini'];

        
        $timestamp1 = strtotime($_POST['m_psico_exam_ante01_fech_ini']);
        $m_psico_exam_ante01_fech_ini = ((strlen($_POST['m_psico_exam_ante01_empresa']) > 0) ? date('Y-m-d', $timestamp1) : null);
        $params_2[':m_psico_exam_ante01_fech_ini'] = $m_psico_exam_ante01_fech_ini;


        $params_2[':m_psico_exam_ante01_empresa'] = $_POST['m_psico_exam_ante01_empresa'];
        $params_2[':m_psico_exam_ante01_act_emp'] = $_POST['m_psico_exam_ante01_act_emp'];
        $params_2[':m_psico_exam_ante01_puesto'] = $_POST['m_psico_exam_ante01_puesto'];
        $params_2[':m_psico_exam_ante01_opera'] = $_POST['m_psico_exam_ante01_opera'];
        $params_2[':m_psico_exam_ante01_causa'] = $_POST['m_psico_exam_ante01_causa'];
        // $params_2[':m_psico_exam_ante02_fech_ini'] = $_POST['m_psico_exam_ante02_fech_ini'];

        
        $timestamp2 = strtotime($_POST['m_psico_exam_ante02_fech_ini']);
        $m_psico_exam_ante02_fech_ini = ((strlen($_POST['m_psico_exam_ante02_empresa']) > 0) ? date('Y-m-d', $timestamp2) : null);
        $params_2[':m_psico_exam_ante02_fech_ini'] = $m_psico_exam_ante02_fech_ini;


        $params_2[':m_psico_exam_ante02_empresa'] = $_POST['m_psico_exam_ante02_empresa'];
        $params_2[':m_psico_exam_ante02_act_emp'] = $_POST['m_psico_exam_ante02_act_emp'];
        $params_2[':m_psico_exam_ante02_puesto'] = $_POST['m_psico_exam_ante02_puesto'];
        $params_2[':m_psico_exam_ante02_opera'] = $_POST['m_psico_exam_ante02_opera'];
        $params_2[':m_psico_exam_ante02_causa'] = $_POST['m_psico_exam_ante02_causa'];
        // $params_2[':m_psico_exam_ante03_fech_ini'] = $_POST['m_psico_exam_ante03_fech_ini'];

        
        $timestamp3 = strtotime($_POST['m_psico_exam_ante03_fech_ini']);
        $m_psico_exam_ante03_fech_ini = ((strlen($_POST['m_psico_exam_ante03_empresa']) > 0) ? date('Y-m-d', $timestamp3) : null);
        $params_2[':m_psico_exam_ante03_fech_ini'] = $m_psico_exam_ante03_fech_ini;


        $params_2[':m_psico_exam_ante03_empresa'] = $_POST['m_psico_exam_ante03_empresa'];
        $params_2[':m_psico_exam_ante03_act_emp'] = $_POST['m_psico_exam_ante03_act_emp'];
        $params_2[':m_psico_exam_ante03_puesto'] = $_POST['m_psico_exam_ante03_puesto'];
        $params_2[':m_psico_exam_ante03_opera'] = $_POST['m_psico_exam_ante03_opera'];
        $params_2[':m_psico_exam_ante03_causa'] = $_POST['m_psico_exam_ante03_causa'];
        $params_2[':m_psico_exam_niv_intelectual'] = $_POST['m_psico_exam_niv_intelectual'];
        $params_2[':m_psico_exam_co_visomotriz'] = $_POST['m_psico_exam_co_visomotriz'];
        $params_2[':m_psico_exam_niv_memoria'] = $_POST['m_psico_exam_niv_memoria'];
        $params_2[':m_psico_exam_persona_desc'] = $_POST['m_psico_exam_persona_desc'];
        $params_2[':m_psico_exam_afectivi_desc'] = $_POST['m_psico_exam_afectivi_desc'];
        $params_2[':m_psico_exam_test_maslash'] = $_POST['m_psico_exam_test_maslash'];
        $params_2[':m_psico_exam_test_intelig'] = $_POST['m_psico_exam_test_intelig'];
        $params_2[':m_psico_exam_test_fatiga'] = $_POST['m_psico_exam_test_fatiga'];
        $params_2[':m_psico_exam_test_somnolencia'] = $_POST['m_psico_exam_test_somnolencia'];
        $params_2[':m_psico_exam_test_ansiedad'] = $_POST['m_psico_exam_test_ansiedad'];
        $params_2[':m_psico_exam_test_depresion'] = $_POST['m_psico_exam_test_depresion'];
        $params_2[':m_psico_exam_test_acrofobia'] = $_POST['m_psico_exam_test_acrofobia'];
        $params_2[':m_psico_exam_test_estres'] = $_POST['m_psico_exam_test_estres'];
        $params_2[':m_psico_exam_aptitud'] = $_POST['m_psico_exam_aptitud'];
        $params_2[':m_psico_exam_aptitud_desc'] = $_POST['m_psico_exam_aptitud_desc'];
        $params_2[':m_psico_exam_medico'] = $_POST['m_psico_exam_medico'];

        $q_2 = 'Update mod_psicologia_examen set
                    m_psico_exam_activ_empresa=:m_psico_exam_activ_empresa,
                    m_psico_exam_area_trabajo=:m_psico_exam_area_trabajo,
                    m_psico_exam_tiempo_labor=:m_psico_exam_tiempo_labor,
                    m_psico_exam_puesto=:m_psico_exam_puesto,
                    m_psico_exam_princ_riesgos=:m_psico_exam_princ_riesgos,
                    m_psico_exam_medi_seguridad=:m_psico_exam_medi_seguridad,
                    m_psico_exam_histo_familiar=:m_psico_exam_histo_familiar,
                    m_psico_exam_accid_enfermedad=:m_psico_exam_accid_enfermedad,
                    m_psico_exam_habitos=:m_psico_exam_habitos,
                    m_psico_exam_otras_obs=:m_psico_exam_otras_obs,
                    m_psico_exam_presentacion=:m_psico_exam_presentacion,
                    m_psico_exam_postura=:m_psico_exam_postura,
                    m_psico_exam_ritmo=:m_psico_exam_ritmo,
                    m_psico_exam_tono=:m_psico_exam_tono,
                    m_psico_exam_articulacion=:m_psico_exam_articulacion,
                    m_psico_exam_tiempo=:m_psico_exam_tiempo,
                    m_psico_exam_espacio=:m_psico_exam_espacio,
                    m_psico_exam_persona=:m_psico_exam_persona,
                    m_psico_exam_lucido_atent=:m_psico_exam_lucido_atent,
                    m_psico_exam_pensamiento=:m_psico_exam_pensamiento,
                    m_psico_exam_persepcion=:m_psico_exam_persepcion,
                    m_psico_exam_memoria=:m_psico_exam_memoria,
                    m_psico_exam_inteligencia=:m_psico_exam_inteligencia,
                    m_psico_exam_apetito=:m_psico_exam_apetito,
                    m_psico_exam_sueno=:m_psico_exam_sueno,
                    m_psico_exam_personalidad=:m_psico_exam_personalidad,
                    m_psico_exam_afectividad=:m_psico_exam_afectividad,
                    m_psico_exam_conduc_sexual=:m_psico_exam_conduc_sexual,
                    m_psico_exam_area_cognitiva=:m_psico_exam_area_cognitiva,
                    m_psico_exam_area_emocional=:m_psico_exam_area_emocional,
                    m_psico_exam_ptje_test_01=:m_psico_exam_ptje_test_01,
                    m_psico_exam_ptje_test_02=:m_psico_exam_ptje_test_02,
                    m_psico_exam_ptje_test_03=:m_psico_exam_ptje_test_03,
                    m_psico_exam_ptje_test_04=:m_psico_exam_ptje_test_04,
                    m_psico_exam_ptje_test_05=:m_psico_exam_ptje_test_05,
                    m_psico_exam_ptje_test_06=:m_psico_exam_ptje_test_06,
                    m_psico_exam_ptje_test_07=:m_psico_exam_ptje_test_07,
                    m_psico_exam_ptje_test_08=:m_psico_exam_ptje_test_08,
                    m_psico_exam_ptje_test_09=:m_psico_exam_ptje_test_09,
                    m_psico_exam_ptje_test_10=:m_psico_exam_ptje_test_10,
                    m_psico_exam_ptje_test_11=:m_psico_exam_ptje_test_11,
                    m_psico_exam_ptje_test_12=:m_psico_exam_ptje_test_12,
                    m_psico_exam_ptje_test_13=:m_psico_exam_ptje_test_13,
                    m_psico_exam_ptje_test_14=:m_psico_exam_ptje_test_14,
                    m_psico_exam_motivo_eva=:m_psico_exam_motivo_eva,
                    m_psico_exam_operacion=:m_psico_exam_operacion,
                    m_psico_exam_ante01_fech_ini=:m_psico_exam_ante01_fech_ini,
                    m_psico_exam_ante01_empresa=:m_psico_exam_ante01_empresa,
                    m_psico_exam_ante01_act_emp=:m_psico_exam_ante01_act_emp,
                    m_psico_exam_ante01_puesto=:m_psico_exam_ante01_puesto,
                    m_psico_exam_ante01_opera=:m_psico_exam_ante01_opera,
                    m_psico_exam_ante01_causa=:m_psico_exam_ante01_causa,
                    m_psico_exam_ante02_fech_ini=:m_psico_exam_ante02_fech_ini,
                    m_psico_exam_ante02_empresa=:m_psico_exam_ante02_empresa,
                    m_psico_exam_ante02_act_emp=:m_psico_exam_ante02_act_emp,
                    m_psico_exam_ante02_puesto=:m_psico_exam_ante02_puesto,
                    m_psico_exam_ante02_opera=:m_psico_exam_ante02_opera,
                    m_psico_exam_ante02_causa=:m_psico_exam_ante02_causa,
                    m_psico_exam_ante03_fech_ini=:m_psico_exam_ante03_fech_ini,
                    m_psico_exam_ante03_empresa=:m_psico_exam_ante03_empresa,
                    m_psico_exam_ante03_act_emp=:m_psico_exam_ante03_act_emp,
                    m_psico_exam_ante03_puesto=:m_psico_exam_ante03_puesto,
                    m_psico_exam_ante03_opera=:m_psico_exam_ante03_opera,
                    m_psico_exam_ante03_causa=:m_psico_exam_ante03_causa,
                    m_psico_exam_niv_intelectual=:m_psico_exam_niv_intelectual,
                    m_psico_exam_co_visomotriz=:m_psico_exam_co_visomotriz,
                    m_psico_exam_niv_memoria=:m_psico_exam_niv_memoria,
                    m_psico_exam_persona_desc=:m_psico_exam_persona_desc,
                    m_psico_exam_afectivi_desc=:m_psico_exam_afectivi_desc,
                    m_psico_exam_test_maslash=:m_psico_exam_test_maslash,
                    m_psico_exam_test_intelig=:m_psico_exam_test_intelig,
                    m_psico_exam_test_fatiga=:m_psico_exam_test_fatiga,
                    m_psico_exam_test_somnolencia=:m_psico_exam_test_somnolencia,
                    m_psico_exam_test_ansiedad=:m_psico_exam_test_ansiedad,
                    m_psico_exam_test_depresion=:m_psico_exam_test_depresion,
                    m_psico_exam_test_acrofobia=:m_psico_exam_test_acrofobia,
                    m_psico_exam_test_estres=:m_psico_exam_test_estres,
                    m_psico_exam_aptitud=:m_psico_exam_aptitud,
                    m_psico_exam_aptitud_desc=:m_psico_exam_aptitud_desc,
                    m_psico_exam_medico=:m_psico_exam_medico
                where
                m_psico_exam_adm=:adm;';

        $sql_2 = $this->sql($q_2, $params_2);
        if ($sql_2->success) {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success && $sql_1->total == 1) {
                $this->commit();
                return $sql_1;
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }

    //LOAD SAVE UPDATE psico_ALTURA

    public function load_psicologia_altura()
    {
        $adm = $_POST['adm'];
        //        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_psicologia_altura where m_psico_altura_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_psicologia_altura()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_psicologia VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                1,
                :ex_id);";

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_psico_altura_tec_mod_grave'] = $_POST['m_psico_altura_tec_mod_grave'];
        $params_2[':m_psico_altura_tec_mod_grave_desc'] = $_POST['m_psico_altura_tec_mod_grave_desc'];
        $params_2[':m_psico_altura_convulsiones'] = $_POST['m_psico_altura_convulsiones'];
        $params_2[':m_psico_altura_convulsiones_desc'] = $_POST['m_psico_altura_convulsiones_desc'];
        $params_2[':m_psico_altura_mareo'] = $_POST['m_psico_altura_mareo'];
        $params_2[':m_psico_altura_mareo_desc'] = $_POST['m_psico_altura_mareo_desc'];
        $params_2[':m_psico_altura_problem_audicion'] = $_POST['m_psico_altura_problem_audicion'];
        $params_2[':m_psico_altura_problem_audicion_desc'] = $_POST['m_psico_altura_problem_audicion_desc'];
        $params_2[':m_psico_altura_problem_equilib'] = $_POST['m_psico_altura_problem_equilib'];
        $params_2[':m_psico_altura_problem_equilib_desc'] = $_POST['m_psico_altura_problem_equilib_desc'];
        $params_2[':m_psico_altura_acrofobia'] = $_POST['m_psico_altura_acrofobia'];
        $params_2[':m_psico_altura_acrofobia_desc'] = $_POST['m_psico_altura_acrofobia_desc'];
        $params_2[':m_psico_altura_agorafobia'] = $_POST['m_psico_altura_agorafobia'];
        $params_2[':m_psico_altura_agorafobia_desc'] = $_POST['m_psico_altura_agorafobia_desc'];
        $params_2[':m_psico_altura_alcohol_tipo'] = $_POST['m_psico_altura_alcohol_tipo'];
        $params_2[':m_psico_altura_alcohol_cant'] = $_POST['m_psico_altura_alcohol_cant'];
        $params_2[':m_psico_altura_alcohol_frecu'] = $_POST['m_psico_altura_alcohol_frecu'];
        $params_2[':m_psico_altura_tabaco_tipo'] = $_POST['m_psico_altura_tabaco_tipo'];
        $params_2[':m_psico_altura_tabaco_cant'] = $_POST['m_psico_altura_tabaco_cant'];
        $params_2[':m_psico_altura_tabaco_frecu'] = $_POST['m_psico_altura_tabaco_frecu'];
        $params_2[':m_psico_altura_cafe_tipo'] = $_POST['m_psico_altura_cafe_tipo'];
        $params_2[':m_psico_altura_cafe_cant'] = $_POST['m_psico_altura_cafe_cant'];
        $params_2[':m_psico_altura_cafe_frecu'] = $_POST['m_psico_altura_cafe_frecu'];
        $params_2[':m_psico_altura_droga_tipo'] = $_POST['m_psico_altura_droga_tipo'];
        $params_2[':m_psico_altura_droga_cant'] = $_POST['m_psico_altura_droga_cant'];
        $params_2[':m_psico_altura_droga_frecu'] = $_POST['m_psico_altura_droga_frecu'];
        $params_2[':m_psico_altura_preg_resp_01'] = $_POST['m_psico_altura_preg_resp_01'];
        $params_2[':m_psico_altura_preg_ptje_01'] = $_POST['m_psico_altura_preg_ptje_01'];
        $params_2[':m_psico_altura_preg_resp_02'] = $_POST['m_psico_altura_preg_resp_02'];
        $params_2[':m_psico_altura_preg_ptje_02'] = $_POST['m_psico_altura_preg_ptje_02'];
        $params_2[':m_psico_altura_preg_resp_03'] = $_POST['m_psico_altura_preg_resp_03'];
        $params_2[':m_psico_altura_preg_ptje_03'] = $_POST['m_psico_altura_preg_ptje_03'];
        $params_2[':m_psico_altura_preg_resp_04'] = $_POST['m_psico_altura_preg_resp_04'];
        $params_2[':m_psico_altura_preg_ptje_04'] = $_POST['m_psico_altura_preg_ptje_04'];
        $params_2[':m_psico_altura_preg_resp_05'] = $_POST['m_psico_altura_preg_resp_05'];
        $params_2[':m_psico_altura_preg_ptje_05'] = $_POST['m_psico_altura_preg_ptje_05'];
        $params_2[':m_psico_altura_preg_resp_06'] = $_POST['m_psico_altura_preg_resp_06'];
        $params_2[':m_psico_altura_preg_ptje_06'] = $_POST['m_psico_altura_preg_ptje_06'];
        $params_2[':m_psico_altura_preg_resp_07'] = $_POST['m_psico_altura_preg_resp_07'];
        $params_2[':m_psico_altura_preg_ptje_07'] = $_POST['m_psico_altura_preg_ptje_07'];
        $params_2[':m_psico_altura_preg_resp_08'] = $_POST['m_psico_altura_preg_resp_08'];
        $params_2[':m_psico_altura_preg_ptje_08'] = $_POST['m_psico_altura_preg_ptje_08'];
        $params_2[':m_psico_altura_preg_resp_09'] = $_POST['m_psico_altura_preg_resp_09'];
        $params_2[':m_psico_altura_preg_ptje_09'] = $_POST['m_psico_altura_preg_ptje_09'];
        $params_2[':m_psico_altura_preg_resp_10'] = $_POST['m_psico_altura_preg_resp_10'];
        $params_2[':m_psico_altura_preg_ptje_10'] = $_POST['m_psico_altura_preg_ptje_10'];
        $params_2[':m_psico_altura_entrena_altura'] = $_POST['m_psico_altura_entrena_altura'];
        $params_2[':m_psico_altura_entrena_auxilio'] = $_POST['m_psico_altura_entrena_auxilio'];
        $params_2[':m_psico_altura_equilibrio_01'] = $_POST['m_psico_altura_equilibrio_01'];
        $params_2[':m_psico_altura_equilibrio_02'] = $_POST['m_psico_altura_equilibrio_02'];
        $params_2[':m_psico_altura_equilibrio_03'] = $_POST['m_psico_altura_equilibrio_03'];
        $params_2[':m_psico_altura_equilibrio_04'] = $_POST['m_psico_altura_equilibrio_04'];
        $params_2[':m_psico_altura_equilibrio_05'] = $_POST['m_psico_altura_equilibrio_05'];
        $params_2[':m_psico_altura_equilibrio_06'] = $_POST['m_psico_altura_equilibrio_06'];
        $params_2[':m_psico_altura_equilibrio_07'] = $_POST['m_psico_altura_equilibrio_07'];
        $params_2[':m_psico_altura_equilibrio_08'] = $_POST['m_psico_altura_equilibrio_08'];
        $params_2[':m_psico_altura_equilibrio_09'] = $_POST['m_psico_altura_equilibrio_09'];
        $params_2[':m_psico_altura_nistagmus_esponta'] = $_POST['m_psico_altura_nistagmus_esponta'];
        $params_2[':m_psico_altura_nistagmus_provoca'] = $_POST['m_psico_altura_nistagmus_provoca'];
        $params_2[':m_psico_altura_pie_plano'] = $_POST['m_psico_altura_pie_plano'];
        $params_2[':m_psico_altura_usa_plantillas'] = $_POST['m_psico_altura_usa_plantillas'];
        $params_2[':m_psico_altura_toulouse'] = $_POST['m_psico_altura_toulouse'];
        $params_2[':m_psico_altura_bc_2'] = $_POST['m_psico_altura_bc_2'];
        $params_2[':m_psico_altura_h_entre_form_est'] = $_POST['m_psico_altura_h_entre_form_est'];
        $params_2[':m_psico_altura_temores'] = $_POST['m_psico_altura_temores'];
        $params_2[':m_psico_altura_aptitud'] = $_POST['m_psico_altura_aptitud'];

        $q_2 = "INSERT INTO mod_psicologia_altura VALUES 
                (null,
                :adm,
                :m_psico_altura_tec_mod_grave,
                :m_psico_altura_tec_mod_grave_desc,
                :m_psico_altura_convulsiones,
                :m_psico_altura_convulsiones_desc,
                :m_psico_altura_mareo,
                :m_psico_altura_mareo_desc,
                :m_psico_altura_problem_audicion,
                :m_psico_altura_problem_audicion_desc,
                :m_psico_altura_problem_equilib,
                :m_psico_altura_problem_equilib_desc,
                :m_psico_altura_acrofobia,
                :m_psico_altura_acrofobia_desc,
                :m_psico_altura_agorafobia,
                :m_psico_altura_agorafobia_desc,
                :m_psico_altura_alcohol_tipo,
                :m_psico_altura_alcohol_cant,
                :m_psico_altura_alcohol_frecu,
                :m_psico_altura_tabaco_tipo,
                :m_psico_altura_tabaco_cant,
                :m_psico_altura_tabaco_frecu,
                :m_psico_altura_cafe_tipo,
                :m_psico_altura_cafe_cant,
                :m_psico_altura_cafe_frecu,
                :m_psico_altura_droga_tipo,
                :m_psico_altura_droga_cant,
                :m_psico_altura_droga_frecu,
                :m_psico_altura_preg_resp_01,
                :m_psico_altura_preg_ptje_01,
                :m_psico_altura_preg_resp_02,
                :m_psico_altura_preg_ptje_02,
                :m_psico_altura_preg_resp_03,
                :m_psico_altura_preg_ptje_03,
                :m_psico_altura_preg_resp_04,
                :m_psico_altura_preg_ptje_04,
                :m_psico_altura_preg_resp_05,
                :m_psico_altura_preg_ptje_05,
                :m_psico_altura_preg_resp_06,
                :m_psico_altura_preg_ptje_06,
                :m_psico_altura_preg_resp_07,
                :m_psico_altura_preg_ptje_07,
                :m_psico_altura_preg_resp_08,
                :m_psico_altura_preg_ptje_08,
                :m_psico_altura_preg_resp_09,
                :m_psico_altura_preg_ptje_09,
                :m_psico_altura_preg_resp_10,
                :m_psico_altura_preg_ptje_10,
                :m_psico_altura_entrena_altura,
                :m_psico_altura_entrena_auxilio,
                :m_psico_altura_equilibrio_01,
                :m_psico_altura_equilibrio_02,
                :m_psico_altura_equilibrio_03,
                :m_psico_altura_equilibrio_04,
                :m_psico_altura_equilibrio_05,
                :m_psico_altura_equilibrio_06,
                :m_psico_altura_equilibrio_07,
                :m_psico_altura_equilibrio_08,
                :m_psico_altura_equilibrio_09,
                :m_psico_altura_nistagmus_esponta,
                :m_psico_altura_nistagmus_provoca,
                :m_psico_altura_pie_plano,
                :m_psico_altura_usa_plantillas,                
                :m_psico_altura_toulouse,
                :m_psico_altura_bc_2,
                :m_psico_altura_h_entre_form_est,
                :m_psico_altura_temores,                
                :m_psico_altura_aptitud);";

        $verifica = $this->sql("SELECT 
		m_psicologia_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_psicologia 
		inner join sys_usuario on usu_id=m_psicologia_usu 
		where 
		m_psicologia_adm='$adm' and m_psicologia_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    $this->commit();
                    return $sql_2;
                } else {
                    $this->rollback();
                    return array('success' => false, 'error' => 'Problemas con el registro.');
                }
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        }
    }

    public function update_psicologia_altura()
    {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_psicologia set
                    m_psicologia_usu=:usuario,
                    m_psicologia_fech_update=now()
                where
                m_psicologia_id=:id and m_psicologia_adm=:adm and m_psicologia_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_psico_altura_tec_mod_grave'] = $_POST['m_psico_altura_tec_mod_grave'];
        $params_2[':m_psico_altura_tec_mod_grave_desc'] = $_POST['m_psico_altura_tec_mod_grave_desc'];
        $params_2[':m_psico_altura_convulsiones'] = $_POST['m_psico_altura_convulsiones'];
        $params_2[':m_psico_altura_convulsiones_desc'] = $_POST['m_psico_altura_convulsiones_desc'];
        $params_2[':m_psico_altura_mareo'] = $_POST['m_psico_altura_mareo'];
        $params_2[':m_psico_altura_mareo_desc'] = $_POST['m_psico_altura_mareo_desc'];
        $params_2[':m_psico_altura_problem_audicion'] = $_POST['m_psico_altura_problem_audicion'];
        $params_2[':m_psico_altura_problem_audicion_desc'] = $_POST['m_psico_altura_problem_audicion_desc'];
        $params_2[':m_psico_altura_problem_equilib'] = $_POST['m_psico_altura_problem_equilib'];
        $params_2[':m_psico_altura_problem_equilib_desc'] = $_POST['m_psico_altura_problem_equilib_desc'];
        $params_2[':m_psico_altura_acrofobia'] = $_POST['m_psico_altura_acrofobia'];
        $params_2[':m_psico_altura_acrofobia_desc'] = $_POST['m_psico_altura_acrofobia_desc'];
        $params_2[':m_psico_altura_agorafobia'] = $_POST['m_psico_altura_agorafobia'];
        $params_2[':m_psico_altura_agorafobia_desc'] = $_POST['m_psico_altura_agorafobia_desc'];
        $params_2[':m_psico_altura_alcohol_tipo'] = $_POST['m_psico_altura_alcohol_tipo'];
        $params_2[':m_psico_altura_alcohol_cant'] = $_POST['m_psico_altura_alcohol_cant'];
        $params_2[':m_psico_altura_alcohol_frecu'] = $_POST['m_psico_altura_alcohol_frecu'];
        $params_2[':m_psico_altura_tabaco_tipo'] = $_POST['m_psico_altura_tabaco_tipo'];
        $params_2[':m_psico_altura_tabaco_cant'] = $_POST['m_psico_altura_tabaco_cant'];
        $params_2[':m_psico_altura_tabaco_frecu'] = $_POST['m_psico_altura_tabaco_frecu'];
        $params_2[':m_psico_altura_cafe_tipo'] = $_POST['m_psico_altura_cafe_tipo'];
        $params_2[':m_psico_altura_cafe_cant'] = $_POST['m_psico_altura_cafe_cant'];
        $params_2[':m_psico_altura_cafe_frecu'] = $_POST['m_psico_altura_cafe_frecu'];
        $params_2[':m_psico_altura_droga_tipo'] = $_POST['m_psico_altura_droga_tipo'];
        $params_2[':m_psico_altura_droga_cant'] = $_POST['m_psico_altura_droga_cant'];
        $params_2[':m_psico_altura_droga_frecu'] = $_POST['m_psico_altura_droga_frecu'];
        $params_2[':m_psico_altura_preg_resp_01'] = $_POST['m_psico_altura_preg_resp_01'];
        $params_2[':m_psico_altura_preg_ptje_01'] = $_POST['m_psico_altura_preg_ptje_01'];
        $params_2[':m_psico_altura_preg_resp_02'] = $_POST['m_psico_altura_preg_resp_02'];
        $params_2[':m_psico_altura_preg_ptje_02'] = $_POST['m_psico_altura_preg_ptje_02'];
        $params_2[':m_psico_altura_preg_resp_03'] = $_POST['m_psico_altura_preg_resp_03'];
        $params_2[':m_psico_altura_preg_ptje_03'] = $_POST['m_psico_altura_preg_ptje_03'];
        $params_2[':m_psico_altura_preg_resp_04'] = $_POST['m_psico_altura_preg_resp_04'];
        $params_2[':m_psico_altura_preg_ptje_04'] = $_POST['m_psico_altura_preg_ptje_04'];
        $params_2[':m_psico_altura_preg_resp_05'] = $_POST['m_psico_altura_preg_resp_05'];
        $params_2[':m_psico_altura_preg_ptje_05'] = $_POST['m_psico_altura_preg_ptje_05'];
        $params_2[':m_psico_altura_preg_resp_06'] = $_POST['m_psico_altura_preg_resp_06'];
        $params_2[':m_psico_altura_preg_ptje_06'] = $_POST['m_psico_altura_preg_ptje_06'];
        $params_2[':m_psico_altura_preg_resp_07'] = $_POST['m_psico_altura_preg_resp_07'];
        $params_2[':m_psico_altura_preg_ptje_07'] = $_POST['m_psico_altura_preg_ptje_07'];
        $params_2[':m_psico_altura_preg_resp_08'] = $_POST['m_psico_altura_preg_resp_08'];
        $params_2[':m_psico_altura_preg_ptje_08'] = $_POST['m_psico_altura_preg_ptje_08'];
        $params_2[':m_psico_altura_preg_resp_09'] = $_POST['m_psico_altura_preg_resp_09'];
        $params_2[':m_psico_altura_preg_ptje_09'] = $_POST['m_psico_altura_preg_ptje_09'];
        $params_2[':m_psico_altura_preg_resp_10'] = $_POST['m_psico_altura_preg_resp_10'];
        $params_2[':m_psico_altura_preg_ptje_10'] = $_POST['m_psico_altura_preg_ptje_10'];
        $params_2[':m_psico_altura_entrena_altura'] = $_POST['m_psico_altura_entrena_altura'];
        $params_2[':m_psico_altura_entrena_auxilio'] = $_POST['m_psico_altura_entrena_auxilio'];
        $params_2[':m_psico_altura_equilibrio_01'] = $_POST['m_psico_altura_equilibrio_01'];
        $params_2[':m_psico_altura_equilibrio_02'] = $_POST['m_psico_altura_equilibrio_02'];
        $params_2[':m_psico_altura_equilibrio_03'] = $_POST['m_psico_altura_equilibrio_03'];
        $params_2[':m_psico_altura_equilibrio_04'] = $_POST['m_psico_altura_equilibrio_04'];
        $params_2[':m_psico_altura_equilibrio_05'] = $_POST['m_psico_altura_equilibrio_05'];
        $params_2[':m_psico_altura_equilibrio_06'] = $_POST['m_psico_altura_equilibrio_06'];
        $params_2[':m_psico_altura_equilibrio_07'] = $_POST['m_psico_altura_equilibrio_07'];
        $params_2[':m_psico_altura_equilibrio_08'] = $_POST['m_psico_altura_equilibrio_08'];
        $params_2[':m_psico_altura_equilibrio_09'] = $_POST['m_psico_altura_equilibrio_09'];
        $params_2[':m_psico_altura_nistagmus_esponta'] = $_POST['m_psico_altura_nistagmus_esponta'];
        $params_2[':m_psico_altura_nistagmus_provoca'] = $_POST['m_psico_altura_nistagmus_provoca'];
        $params_2[':m_psico_altura_pie_plano'] = $_POST['m_psico_altura_pie_plano'];
        $params_2[':m_psico_altura_usa_plantillas'] = $_POST['m_psico_altura_usa_plantillas'];

        $params_2[':m_psico_altura_toulouse'] = $_POST['m_psico_altura_toulouse'];
        $params_2[':m_psico_altura_bc_2'] = $_POST['m_psico_altura_bc_2'];
        $params_2[':m_psico_altura_h_entre_form_est'] = $_POST['m_psico_altura_h_entre_form_est'];
        $params_2[':m_psico_altura_temores'] = $_POST['m_psico_altura_temores'];

        $params_2[':m_psico_altura_aptitud'] = $_POST['m_psico_altura_aptitud'];

        $q_2 = 'Update mod_psicologia_altura set
                    m_psico_altura_tec_mod_grave=:m_psico_altura_tec_mod_grave,
                    m_psico_altura_tec_mod_grave_desc=:m_psico_altura_tec_mod_grave_desc,
                    m_psico_altura_convulsiones=:m_psico_altura_convulsiones,
                    m_psico_altura_convulsiones_desc=:m_psico_altura_convulsiones_desc,
                    m_psico_altura_mareo=:m_psico_altura_mareo,
                    m_psico_altura_mareo_desc=:m_psico_altura_mareo_desc,
                    m_psico_altura_problem_audicion=:m_psico_altura_problem_audicion,
                    m_psico_altura_problem_audicion_desc=:m_psico_altura_problem_audicion_desc,
                    m_psico_altura_problem_equilib=:m_psico_altura_problem_equilib,
                    m_psico_altura_problem_equilib_desc=:m_psico_altura_problem_equilib_desc,
                    m_psico_altura_acrofobia=:m_psico_altura_acrofobia,
                    m_psico_altura_acrofobia_desc=:m_psico_altura_acrofobia_desc,
                    m_psico_altura_agorafobia=:m_psico_altura_agorafobia,
                    m_psico_altura_agorafobia_desc=:m_psico_altura_agorafobia_desc,
                    m_psico_altura_alcohol_tipo=:m_psico_altura_alcohol_tipo,
                    m_psico_altura_alcohol_cant=:m_psico_altura_alcohol_cant,
                    m_psico_altura_alcohol_frecu=:m_psico_altura_alcohol_frecu,
                    m_psico_altura_tabaco_tipo=:m_psico_altura_tabaco_tipo,
                    m_psico_altura_tabaco_cant=:m_psico_altura_tabaco_cant,
                    m_psico_altura_tabaco_frecu=:m_psico_altura_tabaco_frecu,
                    m_psico_altura_cafe_tipo=:m_psico_altura_cafe_tipo,
                    m_psico_altura_cafe_cant=:m_psico_altura_cafe_cant,
                    m_psico_altura_cafe_frecu=:m_psico_altura_cafe_frecu,
                    m_psico_altura_droga_tipo=:m_psico_altura_droga_tipo,
                    m_psico_altura_droga_cant=:m_psico_altura_droga_cant,
                    m_psico_altura_droga_frecu=:m_psico_altura_droga_frecu,
                    m_psico_altura_preg_resp_01=:m_psico_altura_preg_resp_01,
                    m_psico_altura_preg_ptje_01=:m_psico_altura_preg_ptje_01,
                    m_psico_altura_preg_resp_02=:m_psico_altura_preg_resp_02,
                    m_psico_altura_preg_ptje_02=:m_psico_altura_preg_ptje_02,
                    m_psico_altura_preg_resp_03=:m_psico_altura_preg_resp_03,
                    m_psico_altura_preg_ptje_03=:m_psico_altura_preg_ptje_03,
                    m_psico_altura_preg_resp_04=:m_psico_altura_preg_resp_04,
                    m_psico_altura_preg_ptje_04=:m_psico_altura_preg_ptje_04,
                    m_psico_altura_preg_resp_05=:m_psico_altura_preg_resp_05,
                    m_psico_altura_preg_ptje_05=:m_psico_altura_preg_ptje_05,
                    m_psico_altura_preg_resp_06=:m_psico_altura_preg_resp_06,
                    m_psico_altura_preg_ptje_06=:m_psico_altura_preg_ptje_06,
                    m_psico_altura_preg_resp_07=:m_psico_altura_preg_resp_07,
                    m_psico_altura_preg_ptje_07=:m_psico_altura_preg_ptje_07,
                    m_psico_altura_preg_resp_08=:m_psico_altura_preg_resp_08,
                    m_psico_altura_preg_ptje_08=:m_psico_altura_preg_ptje_08,
                    m_psico_altura_preg_resp_09=:m_psico_altura_preg_resp_09,
                    m_psico_altura_preg_ptje_09=:m_psico_altura_preg_ptje_09,
                    m_psico_altura_preg_resp_10=:m_psico_altura_preg_resp_10,
                    m_psico_altura_preg_ptje_10=:m_psico_altura_preg_ptje_10,
                    m_psico_altura_entrena_altura=:m_psico_altura_entrena_altura,
                    m_psico_altura_entrena_auxilio=:m_psico_altura_entrena_auxilio,
                    m_psico_altura_equilibrio_01=:m_psico_altura_equilibrio_01,
                    m_psico_altura_equilibrio_02=:m_psico_altura_equilibrio_02,
                    m_psico_altura_equilibrio_03=:m_psico_altura_equilibrio_03,
                    m_psico_altura_equilibrio_04=:m_psico_altura_equilibrio_04,
                    m_psico_altura_equilibrio_05=:m_psico_altura_equilibrio_05,
                    m_psico_altura_equilibrio_06=:m_psico_altura_equilibrio_06,
                    m_psico_altura_equilibrio_07=:m_psico_altura_equilibrio_07,
                    m_psico_altura_equilibrio_08=:m_psico_altura_equilibrio_08,
                    m_psico_altura_equilibrio_09=:m_psico_altura_equilibrio_09,
                    m_psico_altura_nistagmus_esponta=:m_psico_altura_nistagmus_esponta,
                    m_psico_altura_nistagmus_provoca=:m_psico_altura_nistagmus_provoca,
                    m_psico_altura_pie_plano=:m_psico_altura_pie_plano,
                    m_psico_altura_usa_plantillas=:m_psico_altura_usa_plantillas,                    
                    m_psico_altura_toulouse=:m_psico_altura_toulouse,
                    m_psico_altura_bc_2=:m_psico_altura_bc_2,
                    m_psico_altura_h_entre_form_est=:m_psico_altura_h_entre_form_est,
                    m_psico_altura_temores=:m_psico_altura_temores,                    
                    m_psico_altura_aptitud=:m_psico_altura_aptitud
                where
                m_psico_altura_adm=:adm;';

        $sql_2 = $this->sql($q_2, $params_2);
        if ($sql_2->success) {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success && $sql_1->total == 1) {
                $this->commit();
                return $sql_1;
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }

    //LOAD SAVE UPDATE CONCLUSIONES


    public function list_conclu_altu_psico()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT conclu_altu_psico_id, conclu_altu_psico_adm, conclu_altu_psico_desc
                FROM mod_psicologia_altura_conclu
                where conclu_altu_psico_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function save_conclu_altu_psico()
    {
        $params = array();
        $params[':conclu_altu_psico_adm'] = $_POST['conclu_altu_psico_adm'];
        $params[':conclu_altu_psico_desc'] = $_POST['conclu_altu_psico_desc'];

        $q = 'INSERT INTO mod_psicologia_altura_conclu VALUES 
                (NULL,
                :conclu_altu_psico_adm,
                UPPER(:conclu_altu_psico_desc))';
        return $this->sql($q, $params);
    }

    public function update_conclu_altu_psico()
    {
        $params = array();
        $params[':conclu_altu_psico_id'] = $_POST['conclu_altu_psico_id'];
        $params[':conclu_altu_psico_adm'] = $_POST['conclu_altu_psico_adm'];
        $params[':conclu_altu_psico_desc'] = $_POST['conclu_altu_psico_desc'];

        $this->begin();
        $q = 'Update mod_psicologia_altura_conclu set
                conclu_altu_psico_desc=UPPER(:conclu_altu_psico_desc)
                where
                conclu_altu_psico_id=:conclu_altu_psico_id and conclu_altu_psico_adm=:conclu_altu_psico_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $pac_id = $_POST['conclu_altu_psico_adm'];
            $this->commit();
            return array('success' => true, 'data' => $pac_id);
        } else {
            $this->rollback();
        }
    }

    public function st_busca_conclu_altu_psico()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT conclu_altu_psico_desc FROM mod_psicologia_altura_conclu
                            where
                            conclu_altu_psico_desc like '%$query%'
                            group by conclu_altu_psico_desc");
        return $sql;
    }

    public function load_conclu_altu_psico()
    {
        $reco_id = $_POST['conclu_altu_psico_id'];
        $reco_adm = $_POST['conclu_altu_psico_adm'];
        $query = "SELECT
            conclu_altu_psico_id, conclu_altu_psico_adm, conclu_altu_psico_desc
            FROM mod_psicologia_altura_conclu
            where
            conclu_altu_psico_id=$reco_id and
            conclu_altu_psico_adm=$reco_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //LOAD SAVE UPDATE psico_examen

    public function list_conclu_confi_psico()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT conclu_confi_psico_id, conclu_confi_psico_adm, conclu_confi_psico_desc
                FROM mod_psicologia_confina_conclu
                where conclu_confi_psico_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function save_conclu_confi_psico()
    {
        $params = array();
        $params[':conclu_confi_psico_adm'] = $_POST['conclu_confi_psico_adm'];
        $params[':conclu_confi_psico_desc'] = $_POST['conclu_confi_psico_desc'];

        $q = 'INSERT INTO mod_psicologia_confina_conclu VALUES 
                (NULL,
                :conclu_confi_psico_adm,
                UPPER(:conclu_confi_psico_desc))';
        return $this->sql($q, $params);
    }

    public function update_conclu_confi_psico()
    {
        $params = array();
        $params[':conclu_confi_psico_id'] = $_POST['conclu_confi_psico_id'];
        $params[':conclu_confi_psico_adm'] = $_POST['conclu_confi_psico_adm'];
        $params[':conclu_confi_psico_desc'] = $_POST['conclu_confi_psico_desc'];

        $this->begin();
        $q = 'Update mod_psicologia_confina_conclu set
                conclu_confi_psico_desc=UPPER(:conclu_confi_psico_desc)
                where
                conclu_confi_psico_id=:conclu_confi_psico_id and conclu_confi_psico_adm=:conclu_confi_psico_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $pac_id = $_POST['conclu_confi_psico_adm'];
            $this->commit();
            return array('success' => true, 'data' => $pac_id);
        } else {
            $this->rollback();
        }
    }

    public function st_busca_conclu_confi_psico()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT conclu_confi_psico_desc FROM mod_psicologia_confina_conclu
                            where
                            conclu_confi_psico_desc like '%$query%'
                            group by conclu_confi_psico_desc");
        return $sql;
    }

    public function load_conclu_confi_psico()
    {
        $reco_id = $_POST['conclu_confi_psico_id'];
        $reco_adm = $_POST['conclu_confi_psico_adm'];
        $query = "SELECT
            conclu_confi_psico_id, conclu_confi_psico_adm, conclu_confi_psico_desc
            FROM mod_psicologia_confina_conclu
            where
            conclu_confi_psico_id=$reco_id and
            conclu_confi_psico_adm=$reco_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //LOAD SAVE UPDATE ESPACIOS CONFINADOS

    public function load_psicologia_confinados()
    {
        $adm = $_POST['adm'];
        //        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_psicologia_confinados where m_psico_confinados_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_psicologia_confinados()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_psicologia VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                1,
                :ex_id);";

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_psico_confinados_preg01'] = $_POST['m_psico_confinados_preg01'];
        $params_2[':m_psico_confinados_preg02'] = $_POST['m_psico_confinados_preg02'];
        $params_2[':m_psico_confinados_preg03'] = $_POST['m_psico_confinados_preg03'];
        $params_2[':m_psico_confinados_preg04'] = $_POST['m_psico_confinados_preg04'];
        $params_2[':m_psico_confinados_preg05'] = $_POST['m_psico_confinados_preg05'];
        $params_2[':m_psico_confinados_preg06'] = $_POST['m_psico_confinados_preg06'];
        $params_2[':m_psico_confinados_preg07'] = $_POST['m_psico_confinados_preg07'];
        $params_2[':m_psico_confinados_preg08'] = $_POST['m_psico_confinados_preg08'];
        $params_2[':m_psico_confinados_preg09'] = $_POST['m_psico_confinados_preg09'];
        $params_2[':m_psico_confinados_preg10'] = $_POST['m_psico_confinados_preg10'];
        $params_2[':m_psico_confinados_preg11'] = $_POST['m_psico_confinados_preg11'];
        $params_2[':m_psico_confinados_preg12'] = $_POST['m_psico_confinados_preg12'];
        $params_2[':m_psico_confinados_preg13'] = $_POST['m_psico_confinados_preg13'];
        $params_2[':m_psico_confinados_preg14'] = $_POST['m_psico_confinados_preg14'];
        $params_2[':m_psico_confinados_entrena_confina'] = $_POST['m_psico_confinados_entrena_confina'];
        $params_2[':m_psico_confinados_prim_auxilios'] = $_POST['m_psico_confinados_prim_auxilios'];
        $params_2[':m_psico_confinados_fobia_claustro'] = $_POST['m_psico_confinados_fobia_claustro'];
        $params_2[':m_psico_confinados_bat7'] = $_POST['m_psico_confinados_bat7'];
        $params_2[':m_psico_confinados_formato'] = $_POST['m_psico_confinados_formato'];
        $params_2[':m_psico_confinados_cuest_temores'] = $_POST['m_psico_confinados_cuest_temores'];
        $params_2[':m_psico_confinados_aptitud'] = $_POST['m_psico_confinados_aptitud'];

        $q_2 = "INSERT INTO mod_psicologia_confinados VALUES 
                (null,
                :adm,
                :m_psico_confinados_preg01,
				:m_psico_confinados_preg02,
				:m_psico_confinados_preg03,
				:m_psico_confinados_preg04,
				:m_psico_confinados_preg05,
				:m_psico_confinados_preg06,
				:m_psico_confinados_preg07,
				:m_psico_confinados_preg08,
				:m_psico_confinados_preg09,
				:m_psico_confinados_preg10,
				:m_psico_confinados_preg11,
				:m_psico_confinados_preg12,
				:m_psico_confinados_preg13,
				:m_psico_confinados_preg14,
				:m_psico_confinados_entrena_confina,
				:m_psico_confinados_prim_auxilios,
				:m_psico_confinados_fobia_claustro,
				:m_psico_confinados_bat7,
				:m_psico_confinados_formato,
				:m_psico_confinados_cuest_temores,
				:m_psico_confinados_aptitud);";

        $verifica = $this->sql("SELECT 
		m_psicologia_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_psicologia 
		inner join sys_usuario on usu_id=m_psicologia_usu 
		where 
		m_psicologia_adm='$adm' and m_psicologia_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    $this->commit();
                    return $sql_2;
                } else {
                    $this->rollback();
                    return array('success' => false, 'error' => 'Problemas con el registro.');
                }
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        }
    }


    public function update_psicologia_confinados()
    {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_psicologia set
                    m_psicologia_usu=:usuario,
                    m_psicologia_fech_update=now()
                where
                m_psicologia_id=:id and m_psicologia_adm=:adm and m_psicologia_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_psico_confinados_preg01'] = $_POST['m_psico_confinados_preg01'];
        $params_2[':m_psico_confinados_preg02'] = $_POST['m_psico_confinados_preg02'];
        $params_2[':m_psico_confinados_preg03'] = $_POST['m_psico_confinados_preg03'];
        $params_2[':m_psico_confinados_preg04'] = $_POST['m_psico_confinados_preg04'];
        $params_2[':m_psico_confinados_preg05'] = $_POST['m_psico_confinados_preg05'];
        $params_2[':m_psico_confinados_preg06'] = $_POST['m_psico_confinados_preg06'];
        $params_2[':m_psico_confinados_preg07'] = $_POST['m_psico_confinados_preg07'];
        $params_2[':m_psico_confinados_preg08'] = $_POST['m_psico_confinados_preg08'];
        $params_2[':m_psico_confinados_preg09'] = $_POST['m_psico_confinados_preg09'];
        $params_2[':m_psico_confinados_preg10'] = $_POST['m_psico_confinados_preg10'];
        $params_2[':m_psico_confinados_preg11'] = $_POST['m_psico_confinados_preg11'];
        $params_2[':m_psico_confinados_preg12'] = $_POST['m_psico_confinados_preg12'];
        $params_2[':m_psico_confinados_preg13'] = $_POST['m_psico_confinados_preg13'];
        $params_2[':m_psico_confinados_preg14'] = $_POST['m_psico_confinados_preg14'];
        $params_2[':m_psico_confinados_entrena_confina'] = $_POST['m_psico_confinados_entrena_confina'];
        $params_2[':m_psico_confinados_prim_auxilios'] = $_POST['m_psico_confinados_prim_auxilios'];
        $params_2[':m_psico_confinados_fobia_claustro'] = $_POST['m_psico_confinados_fobia_claustro'];
        $params_2[':m_psico_confinados_bat7'] = $_POST['m_psico_confinados_bat7'];
        $params_2[':m_psico_confinados_formato'] = $_POST['m_psico_confinados_formato'];
        $params_2[':m_psico_confinados_cuest_temores'] = $_POST['m_psico_confinados_cuest_temores'];
        $params_2[':m_psico_confinados_aptitud'] = $_POST['m_psico_confinados_aptitud'];

        $q_2 = 'Update mod_psicologia_confinados set
				m_psico_confinados_preg01=:m_psico_confinados_preg01,
				m_psico_confinados_preg02=:m_psico_confinados_preg02,
				m_psico_confinados_preg03=:m_psico_confinados_preg03,
				m_psico_confinados_preg04=:m_psico_confinados_preg04,
				m_psico_confinados_preg05=:m_psico_confinados_preg05,
				m_psico_confinados_preg06=:m_psico_confinados_preg06,
				m_psico_confinados_preg07=:m_psico_confinados_preg07,
				m_psico_confinados_preg08=:m_psico_confinados_preg08,
				m_psico_confinados_preg09=:m_psico_confinados_preg09,
				m_psico_confinados_preg10=:m_psico_confinados_preg10,
				m_psico_confinados_preg11=:m_psico_confinados_preg11,
				m_psico_confinados_preg12=:m_psico_confinados_preg12,
				m_psico_confinados_preg13=:m_psico_confinados_preg13,
				m_psico_confinados_preg14=:m_psico_confinados_preg14,
				m_psico_confinados_entrena_confina=:m_psico_confinados_entrena_confina,
				m_psico_confinados_prim_auxilios=:m_psico_confinados_prim_auxilios,
				m_psico_confinados_fobia_claustro=:m_psico_confinados_fobia_claustro,
				m_psico_confinados_bat7=:m_psico_confinados_bat7,
				m_psico_confinados_formato=:m_psico_confinados_formato,
				m_psico_confinados_cuest_temores=:m_psico_confinados_cuest_temores,
				m_psico_confinados_aptitud=:m_psico_confinados_aptitud
                where
                m_psico_confinados_adm=:adm;';

        $sql_2 = $this->sql($q_2, $params_2);
        if ($sql_2->success) {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success && $sql_1->total == 1) {
                $this->commit();
                return $sql_1;
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }


    //LOAD SAVE UPDATE ESPACIOS CONFINADOS


    public function paciente($adm)
    {
        $sql = $this->sql("SELECT
            adm_id as adm
            ,concat(pac_nombres,', ',pac_appat,' ',pac_apmat) nom_ap
            ,concat(pac_nombres) nombre,concat(pac_appat,' ',pac_apmat) apellidos
            , emp_desc,concat(adm_puesto,' - ',adm_area)as puesto
            ,Date_format(adm_fech,'%d-%m-%Y %h:%i %p') fech_reg_copleto
            ,Date_format(adm_fech,'%d-%m-%Y') fech_reg
            ,if(tdoc_id=0,'NÚMERO DE DNI',tdoc_desc) documento,pac_ndoc
            ,Date_format(pac_fech_nac,'%d-%m-%Y') fech_naci
            ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
            ,if(pac_sexo='M','MASCULINO','FEMENINO') sexo,tfi_desc tipo
            ,pac_cel,pac_correo, ec_desc ecivil, gi_desc ginstruccion,pac_domdir direc
            ,concat(m.dep_desc,'-' ,l.prov_desc,'-' ,k.dis_desc) ubica_nacimiento
            ,m.dep_desc depa_naci,l.prov_desc prov_naci,k.dis_desc dist_naci
            ,concat(m_ubigeo.dep_desc,'-' ,l_ubigeo.prov_desc,'-' ,k_ubigeo.dis_desc) ubica_ubigeo
            ,m_ubigeo.dep_desc depa_ubigeo,l_ubigeo.prov_desc prov_ubigeo,k_ubigeo.dis_desc dist_ubigeo
            FROM admision
            inner join paciente on pac_id=adm_pac
            inner join pack on pk_id=adm_ruta
            inner join empresa on emp_id=pk_emp
            inner join tficha on tfi_id=adm_tficha
            inner join ecivil on ec_id=pac_ecid
            inner join ginstruccion on gi_id=pac_giid
            inner join tdocumento on tdoc_id=pac_tdocid

            left join distrito k on k.dis_id=pac_domdisid
            left join provincia l on l.prov_id=k.dis_provid
            left join departamento m on m.dep_id=l.prov_depid

            left join distrito k_ubigeo on k_ubigeo.dis_id=pac_ubigeo
            left join provincia l_ubigeo on l_ubigeo.prov_id=k_ubigeo.dis_provid
            left join departamento m_ubigeo on m_ubigeo.dep_id=l_ubigeo.prov_depid
            where
            adm_id=$adm");
        return $sql;
    }

    public function carga_psico_informe_pdf($adm)
    {
        $query = "SELECT * FROM mod_psicologia_informe
            where m_psico_inf_adm='$adm';";
        return $this->sql($query);
    }

    public function carga_psico_examen_pdf($adm)
    {
        $query = "SELECT *
            ,Date_format(m_psico_exam_ante01_fech_ini,'%m-%Y') m_psico_exam_ante01_fech_ini
            ,Date_format(m_psico_exam_ante02_fech_ini,'%m-%Y') m_psico_exam_ante02_fech_ini
            ,Date_format(m_psico_exam_ante03_fech_ini,'%m-%Y') m_psico_exam_ante03_fech_ini
            FROM mod_psicologia_examen
            where m_psico_exam_adm='$adm';";
        return $this->sql($query);
    }

    public function recomendaciones($adm)
    {
        $q = "SELECT upper(m_psico_recom_desc) m_psico_recom_desc, m_psico_recom_plazo FROM mod_psicologia_recomenda where m_psico_recom_adm=$adm";
        return $this->sql($q);
    }

    public function carga_psicologia_altura_pdf($adm)
    {
        $query = "SELECT * FROM mod_psicologia_altura
            where m_psico_altura_adm='$adm';";
        return $this->sql($query);
    }

    public function carga_psicologia_confinados_pdf($adm)
    {
        $query = "SELECT * FROM mod_psicologia_confinados
            where m_psico_confinados_adm='$adm';";
        return $this->sql($query);
    }

    public function rpt_conclusion($adm)
    {
        $q = "SELECT upper(conclu_altu_psico_desc) obs_desc
                FROM mod_psicologia_altura_conclu where conclu_altu_psico_adm=$adm";
        return $this->sql($q);
    }

    public function esp_confin_conclusion($adm)
    {
        $q = "SELECT upper(conclu_confi_psico_desc) obs_desc
                FROM mod_psicologia_confina_conclu where conclu_confi_psico_adm=$adm";
        return $this->sql($q);
    }
    public function medicina_manejo_pdf($adm)
    {
        $q = "SELECT m_med_manejo_test_puntea as punteado,
                m_med_manejo_test_palanca as palanca,
                m_med_manejo_test_reactimetro as reactimetro
                FROM mod_medicina_manejo where m_med_manejo_adm =$adm";
        return $this->sql($q);
    }

    public function load_medico()
    {
        $sede = $this->user->con_sedid;
        return $this->sql("SELECT medico_id, concat(medico_apepat,' ',medico_apemat,', ',medico_nombre)as nombre
        FROM medico
        where medico_sede=$sede and medico_st=1 and medico_auditor='NO' AND medico_tipo = 'PSICOLOGIA';");
    }

    public function llena_psicologia()
    {
        $adm = $_POST['adm'];
        $st = $_POST['st'];
        $usuario = $this->user->us_id;
        $medico = "";
        if ($st < '1') {
            $medico = ",(SELECT medico_id FROM medico where medico_usu='$usuario' AND medico_tipo = 'PSICOLOGIA') m_psico_exam_medico";
        }
        $query = "SELECT
            adm_id
            $medico
            FROM admision
            where adm_id=$adm
            group by adm_id order by adm_id;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //mod_psicologia_recomenda
    public function list_recom()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT m_psico_recom_id, m_psico_recom_adm, m_psico_recom_desc, m_psico_recom_plazo
                FROM mod_psicologia_recomenda
                where m_psico_recom_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_busca_recom()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_psico_recom_desc FROM mod_psicologia_recomenda
                            where
                            m_psico_recom_desc like '%$query%'
                            group by m_psico_recom_desc");
        return $sql;
    }

    public function save_recom()
    {
        $params = array();
        $params[':m_psico_recom_adm'] = $_POST['m_psico_recom_adm'];
        $params[':m_psico_recom_desc'] = $_POST['m_psico_recom_desc'];
        $params[':m_psico_recom_plazo'] = $_POST['m_psico_recom_plazo'];

        $q = 'INSERT INTO mod_psicologia_recomenda VALUES 
                (NULL,
                :m_psico_recom_adm,
                UPPER(:m_psico_recom_desc),
                :m_psico_recom_plazo)';
        return $this->sql($q, $params);
    }

    public function update_recom()
    {
        $params = array();
        $params[':m_psico_recom_id'] = $_POST['m_psico_recom_id'];
        $params[':m_psico_recom_adm'] = $_POST['m_psico_recom_adm'];
        $params[':m_psico_recom_desc'] = $_POST['m_psico_recom_desc'];
        $params[':m_psico_recom_plazo'] = $_POST['m_psico_recom_plazo'];

        $this->begin();
        $q = 'Update mod_psicologia_recomenda set
                m_psico_recom_desc=UPPER(:m_psico_recom_desc),
				m_psico_recom_plazo=:m_psico_recom_plazo
                where
                m_psico_recom_id=:m_psico_recom_id and m_psico_recom_adm=:m_psico_recom_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $m_psico_recom_id = $_POST['m_psico_recom_id'];
            $this->commit();
            return array('success' => true, 'data' => $m_psico_recom_id);
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function load_recom()
    {
        $m_psico_recom_adm = $_POST['m_psico_recom_adm'];
        $m_psico_recom_id = $_POST['m_psico_recom_id'];
        $query = "SELECT
            m_psico_recom_id, m_psico_recom_adm, m_psico_recom_desc, m_psico_recom_plazo
            FROM mod_psicologia_recomenda
            where
            m_psico_recom_id=$m_psico_recom_id and
            m_psico_recom_adm=$m_psico_recom_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }
}

//$sesion = new model(); 
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
