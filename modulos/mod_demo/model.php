<?php

class model extends core {

    public function list_paciente() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $columna = isset($_POST['columna']) ? $_POST['columna'] : NULL;
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
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
                    ex_arid IN (6)
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

    public function list_formatos() {
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

    public function load_examenLab() {
        $adm = $_POST['adm'];
        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_psicologia_informe where m_psico_inf_adm='$adm' and m_lab_exam_examen='$examen';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_exaLab() {

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

    public function update_exaLab() {
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

    //LOAD SAVE UPDATE LABORATORIO

    public function load_psico_informe() {
        $adm = $_POST['adm'];
//        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_psicologia_informe where m_psico_inf_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_psico_informe() {

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

        $params[':m_psico_inf_capac_intelectual'] = $_POST['m_psico_inf_capac_intelectual'];
        $params[':m_psico_inf_aten_concentracion'] = $_POST['m_psico_inf_aten_concentracion'];
        $params[':m_psico_inf_orient_espacial'] = $_POST['m_psico_inf_orient_espacial'];
        $params[':m_psico_inf_pers_htp'] = $_POST['m_psico_inf_pers_htp'];
        $params[':m_psico_inf_pers_salamanca'] = $_POST['m_psico_inf_pers_salamanca'];
        $params[':m_psico_inf_intel_emocional'] = $_POST['m_psico_inf_intel_emocional'];
        $params[':m_psico_inf_caracterologia'] = $_POST['m_psico_inf_caracterologia'];
        $params[':m_psico_inf_alturas'] = $_POST['m_psico_inf_alturas'];
        $params[':m_psico_inf_esp_confinados'] = $_POST['m_psico_inf_esp_confinados'];
        $params[':m_psico_inf_otros'] = $_POST['m_psico_inf_otros'];
        $params[':m_psico_inf_precis_destre_reac'] = $_POST['m_psico_inf_precis_destre_reac'];
        $params[':m_psico_inf_antici_bim_mono'] = $_POST['m_psico_inf_antici_bim_mono'];
        $params[':m_psico_inf_actitud_f_trans'] = $_POST['m_psico_inf_actitud_f_trans'];
        $params[':m_psico_inf_resultados'] = $_POST['m_psico_inf_resultados'];
        $params[':m_psico_inf_debilidades'] = $_POST['m_psico_inf_debilidades'];
        $params[':m_psico_inf_conclusiones'] = $_POST['m_psico_inf_conclusiones'];
        $params[':m_psico_inf_recomendaciones'] = $_POST['m_psico_inf_recomendaciones'];
        $params[':m_psico_inf_puesto_trabajo'] = $_POST['m_psico_inf_puesto_trabajo'];
        $params[':m_psico_inf_brigadista'] = $_POST['m_psico_inf_brigadista'];
        $params[':m_psico_inf_conduc_equip_liviano'] = $_POST['m_psico_inf_conduc_equip_liviano'];
        $params[':m_psico_inf_conduc_equip_pesado'] = $_POST['m_psico_inf_conduc_equip_pesado'];
        $params[':m_psico_inf_trabajo_altura'] = $_POST['m_psico_inf_trabajo_altura'];
        $params[':m_psico_inf_trab_esp_confinado'] = $_POST['m_psico_inf_trab_esp_confinado'];



        $q = "INSERT INTO mod_psicologia_informe VALUES 
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
                :m_psico_inf_trab_esp_confinado);
                
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

    public function update_psico_informe() {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params[':m_psico_inf_capac_intelectual'] = $_POST['m_psico_inf_capac_intelectual'];
        $params[':m_psico_inf_aten_concentracion'] = $_POST['m_psico_inf_aten_concentracion'];
        $params[':m_psico_inf_orient_espacial'] = $_POST['m_psico_inf_orient_espacial'];
        $params[':m_psico_inf_pers_htp'] = $_POST['m_psico_inf_pers_htp'];
        $params[':m_psico_inf_pers_salamanca'] = $_POST['m_psico_inf_pers_salamanca'];
        $params[':m_psico_inf_intel_emocional'] = $_POST['m_psico_inf_intel_emocional'];
        $params[':m_psico_inf_caracterologia'] = $_POST['m_psico_inf_caracterologia'];
        $params[':m_psico_inf_alturas'] = $_POST['m_psico_inf_alturas'];
        $params[':m_psico_inf_esp_confinados'] = $_POST['m_psico_inf_esp_confinados'];
        $params[':m_psico_inf_otros'] = $_POST['m_psico_inf_otros'];
        $params[':m_psico_inf_precis_destre_reac'] = $_POST['m_psico_inf_precis_destre_reac'];
        $params[':m_psico_inf_antici_bim_mono'] = $_POST['m_psico_inf_antici_bim_mono'];
        $params[':m_psico_inf_actitud_f_trans'] = $_POST['m_psico_inf_actitud_f_trans'];
        $params[':m_psico_inf_resultados'] = $_POST['m_psico_inf_resultados'];
        $params[':m_psico_inf_debilidades'] = $_POST['m_psico_inf_debilidades'];
        $params[':m_psico_inf_conclusiones'] = $_POST['m_psico_inf_conclusiones'];
        $params[':m_psico_inf_recomendaciones'] = $_POST['m_psico_inf_recomendaciones'];
        $params[':m_psico_inf_puesto_trabajo'] = $_POST['m_psico_inf_puesto_trabajo'];
        $params[':m_psico_inf_brigadista'] = $_POST['m_psico_inf_brigadista'];
        $params[':m_psico_inf_conduc_equip_liviano'] = $_POST['m_psico_inf_conduc_equip_liviano'];
        $params[':m_psico_inf_conduc_equip_pesado'] = $_POST['m_psico_inf_conduc_equip_pesado'];
        $params[':m_psico_inf_trabajo_altura'] = $_POST['m_psico_inf_trabajo_altura'];
        $params[':m_psico_inf_trab_esp_confinado'] = $_POST['m_psico_inf_trab_esp_confinado'];

        $this->begin();
        $q = 'Update mod_psicologia_informe set                    
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
                m_psico_inf_trab_esp_confinado=:m_psico_inf_trab_esp_confinado
                where
                m_psico_inf_adm=:adm;
                
                Update mod_psicologia set
                    m_psicologia_usu=:usuario,
                    m_psicologia_fech_update=now()
                where
                m_psicologia_id=:id and m_psicologia_adm=:adm and m_psicologia_examen=:ex_id;';


        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

}

//$sesion = new model(); 
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
?>