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
                    ex_arid IN (1)
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
            $verifica = $this->sql("SELECT count(m_triaje_adm)total FROM mod_triaje where m_triaje_adm=$adm_id;");
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
                where ex_arid IN (1) and adm_id=$adm_id order by ex_arid;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_triaje_id id,m_triaje_st st
                , m_triaje_usu usu, m_triaje_fech_reg fech 
            FROM mod_triaje 
            where m_triaje_adm=$adm_id and m_triaje_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    //LOAD SAVE UPDATE mod_triaje_pred

    public function load_triaje_pred() {
        $adm = $_POST['adm'];
        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_triaje_pred where m_tri_pred_adm='$adm' and m_tri_pred_examen='$examen';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_triaje_pred() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':m_triaje_st'] = '1'; //ESTADO DEL MODULO PRINCIPAL

        $params[':adm'] = $adm;
        $params[':ex_id'] = $exa;

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_tri_pred_resultado'] = $_POST['m_tri_pred_resultado'];
        $params[':m_tri_pred_observaciones'] = $_POST['m_tri_pred_observaciones'];
        $params[':m_tri_pred_diagnostico'] = $_POST['m_tri_pred_diagnostico'];


        $q = "INSERT INTO mod_triaje_pred VALUES 
                (null,
                :adm,
                :ex_id,
                :m_tri_pred_resultado,
                :m_tri_pred_observaciones,
                :m_tri_pred_diagnostico);
                
                INSERT INTO mod_triaje VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_triaje_st,
                :ex_id);";

        $verifica = $this->sql("SELECT 
		m_triaje_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_triaje 
		inner join sys_usuario on usu_id=m_triaje_usu 
		where 
		m_triaje_adm='$adm' and m_triaje_examen='$exa';");
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

    public function update_triaje_pred() {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_tri_pred_resultado'] = $_POST['m_tri_pred_resultado'];
        $params[':m_tri_pred_observaciones'] = $_POST['m_tri_pred_observaciones'];
        $params[':m_tri_pred_diagnostico'] = $_POST['m_tri_pred_diagnostico'];

        $this->begin();
        $q = 'Update mod_triaje_pred set
                    m_tri_pred_resultado=:m_tri_pred_resultado,
                    m_tri_pred_observaciones=:m_tri_pred_observaciones,
                    m_tri_pred_diagnostico=:m_tri_pred_diagnostico
                where
                m_psico_inf_adm=:adm and m_tri_pred_examen=:ex_id;
                
                update mod_triaje set
                    m_triaje_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_triaje_adm=:adm and m_triaje_examen=:ex_id;';


        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    //LOAD SAVE UPDATE mod_triaje_triaje

    public function load_triaje_triaje() {
        $adm = $_POST['adm'];
//        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_triaje_triaje where m_tri_triaje_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_triaje_triaje() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_triaje VALUES
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
        $params_2[':m_tri_triaje_talla'] = $_POST['m_tri_triaje_talla'];
        $params_2[':m_tri_triaje_peso'] = $_POST['m_tri_triaje_peso'];
        $params_2[':m_tri_triaje_imc'] = $_POST['m_tri_triaje_imc'];
        $params_2[':m_tri_triaje_perim_cintura'] = $_POST['m_tri_triaje_perim_cintura'];
        $params_2[':m_tri_triaje_perim_cadera'] = $_POST['m_tri_triaje_perim_cadera'];
        $params_2[':m_tri_triaje_icc'] = $_POST['m_tri_triaje_icc'];
        $params_2[':m_tri_triaje_nutricion_dx'] = $_POST['m_tri_triaje_nutricion_dx'];
        $params_2[':m_tri_triaje_pa_sistolica'] = $_POST['m_tri_triaje_pa_sistolica'];
        $params_2[':m_tri_triaje_pa_diastolica'] = $_POST['m_tri_triaje_pa_diastolica'];
        $params_2[':m_tri_triaje_fc'] = $_POST['m_tri_triaje_fc'];
        $params_2[':m_tri_triaje_fr'] = $_POST['m_tri_triaje_fr'];
        $params_2[':m_tri_triaje_temperatura'] = $_POST['m_tri_triaje_temperatura'];
        $params_2[':m_tri_triaje_saturacion'] = $_POST['m_tri_triaje_saturacion'];
        $params_2[':m_tri_triaje_perimt_toraxico'] = $_POST['m_tri_triaje_perimt_toraxico'];
        $params_2[':m_tri_triaje_maxi_inspiracion'] = $_POST['m_tri_triaje_maxi_inspiracion'];
        $params_2[':m_tri_triaje_expira_forzada'] = $_POST['m_tri_triaje_expira_forzada'];
        $params_2[':m_tri_triaje_perimt_abdominal'] = $_POST['m_tri_triaje_perimt_abdominal'];

        $timestamp = strtotime($_POST['m_tri_triaje_fur']);
        $m_tri_triaje_fur = ((strlen($_POST['m_tri_triaje_fur']) > 0) ? date('Y-m-d', $timestamp) : null);
        $params_2[':m_tri_triaje_fur'] = $m_tri_triaje_fur;



        $q_2 = "INSERT INTO mod_triaje_triaje VALUES 
                (null,
                :adm,
                :m_tri_triaje_talla,
                :m_tri_triaje_peso,
                :m_tri_triaje_imc,
                :m_tri_triaje_perim_cintura,
                :m_tri_triaje_perim_cadera,
                :m_tri_triaje_icc,
                :m_tri_triaje_nutricion_dx,
                :m_tri_triaje_pa_sistolica,
                :m_tri_triaje_pa_diastolica,
                :m_tri_triaje_fc,
                :m_tri_triaje_fr,
                :m_tri_triaje_temperatura,
                :m_tri_triaje_saturacion,
                :m_tri_triaje_perimt_toraxico,
                :m_tri_triaje_maxi_inspiracion,
                :m_tri_triaje_expira_forzada,
                :m_tri_triaje_perimt_abdominal,
                :m_tri_triaje_fur);";

        $verifica = $this->sql("SELECT 
		m_triaje_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_triaje 
		inner join sys_usuario on usu_id=m_triaje_usu 
		where 
		m_triaje_adm='$adm' and m_triaje_examen='$exa';");
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

    public function update_triaje_triaje() {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_triaje set
                    m_triaje_usu=:usuario,
                    m_triaje_fech_update=now()
                where
                m_triaje_id=:id and m_triaje_adm=:adm and m_triaje_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];

        $params_2[':m_tri_triaje_talla'] = $_POST['m_tri_triaje_talla'];
        $params_2[':m_tri_triaje_peso'] = $_POST['m_tri_triaje_peso'];
        $params_2[':m_tri_triaje_imc'] = $_POST['m_tri_triaje_imc'];
        $params_2[':m_tri_triaje_perim_cintura'] = $_POST['m_tri_triaje_perim_cintura'];
        $params_2[':m_tri_triaje_perim_cadera'] = $_POST['m_tri_triaje_perim_cadera'];
        $params_2[':m_tri_triaje_icc'] = $_POST['m_tri_triaje_icc'];
        $params_2[':m_tri_triaje_nutricion_dx'] = $_POST['m_tri_triaje_nutricion_dx'];
        $params_2[':m_tri_triaje_pa_sistolica'] = $_POST['m_tri_triaje_pa_sistolica'];
        $params_2[':m_tri_triaje_pa_diastolica'] = $_POST['m_tri_triaje_pa_diastolica'];
        $params_2[':m_tri_triaje_fc'] = $_POST['m_tri_triaje_fc'];
        $params_2[':m_tri_triaje_fr'] = $_POST['m_tri_triaje_fr'];
        $params_2[':m_tri_triaje_temperatura'] = $_POST['m_tri_triaje_temperatura'];
        $params_2[':m_tri_triaje_saturacion'] = $_POST['m_tri_triaje_saturacion'];
        $params_2[':m_tri_triaje_perimt_toraxico'] = $_POST['m_tri_triaje_perimt_toraxico'];
        $params_2[':m_tri_triaje_maxi_inspiracion'] = $_POST['m_tri_triaje_maxi_inspiracion'];
        $params_2[':m_tri_triaje_expira_forzada'] = $_POST['m_tri_triaje_expira_forzada'];
        $params_2[':m_tri_triaje_perimt_abdominal'] = $_POST['m_tri_triaje_perimt_abdominal'];

        $timestamp = strtotime($_POST['m_tri_triaje_fur']);
        $m_tri_triaje_fur = ((strlen($_POST['m_tri_triaje_fur']) > 0) ? date('Y-m-d', $timestamp) : null);
        $params_2[':m_tri_triaje_fur'] = $m_tri_triaje_fur;

        $q_2 = 'Update mod_triaje_triaje set
                    m_tri_triaje_talla=:m_tri_triaje_talla,
                    m_tri_triaje_peso=:m_tri_triaje_peso,
                    m_tri_triaje_imc=:m_tri_triaje_imc,
                    m_tri_triaje_perim_cintura=:m_tri_triaje_perim_cintura,
                    m_tri_triaje_perim_cadera=:m_tri_triaje_perim_cadera,
                    m_tri_triaje_icc=:m_tri_triaje_icc,
                    m_tri_triaje_nutricion_dx=:m_tri_triaje_nutricion_dx,
                    m_tri_triaje_pa_sistolica=:m_tri_triaje_pa_sistolica,
                    m_tri_triaje_pa_diastolica=:m_tri_triaje_pa_diastolica,
                    m_tri_triaje_fc=:m_tri_triaje_fc,
                    m_tri_triaje_fr=:m_tri_triaje_fr,
                    m_tri_triaje_temperatura=:m_tri_triaje_temperatura,
                    m_tri_triaje_saturacion=:m_tri_triaje_saturacion,
                    m_tri_triaje_perimt_toraxico=:m_tri_triaje_perimt_toraxico,
                    m_tri_triaje_maxi_inspiracion=:m_tri_triaje_maxi_inspiracion,
                    m_tri_triaje_expira_forzada=:m_tri_triaje_expira_forzada,
                    m_tri_triaje_perimt_abdominal=:m_tri_triaje_perimt_abdominal,
                    m_tri_triaje_fur=:m_tri_triaje_fur
                where
                m_tri_triaje_adm=:adm;';

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

}

//$sesion = new model(); 
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
?>