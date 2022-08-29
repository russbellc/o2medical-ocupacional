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
                    ex_arid IN (7)
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
            $verifica = $this->sql("SELECT count(m_oftalmo_adm)total FROM mod_oftalmo where m_oftalmo_adm=$adm_id;");
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
                where ex_arid IN (7) and adm_id=$adm_id order by ex_arid;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_oftalmo_id id,m_oftalmo_st st
                , m_oftalmo_usu usu, m_oftalmo_fech_reg fech 
            FROM mod_oftalmo 
            where m_oftalmo_adm=$adm_id and m_oftalmo_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    //LOAD SAVE UPDATE LABORATORIO

    public function load_oftalmo_pred() {
        $adm = $_POST['adm'];
        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_oftalmo_pred where m_oft_pred_adm='$adm' and m_oft_pred_examen='$examen';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_oftalmo_pred() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':m_oftalmo_st'] = '1'; //ESTADO DEL MODULO PRINCIPAL

        $params[':adm'] = $adm;
        $params[':ex_id'] = $exa;

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_oft_pred_resultado'] = $_POST['m_oft_pred_resultado'];
        $params[':m_oft_pred_observaciones'] = $_POST['m_oft_pred_observaciones'];
        $params[':m_oft_pred_diagnostico'] = $_POST['m_oft_pred_diagnostico'];


        $q = "INSERT INTO mod_oftalmo_pred VALUES 
                (null,
                :adm,
                :ex_id,
                :m_oft_pred_resultado,
                :m_oft_pred_observaciones,
                :m_oft_pred_diagnostico);
                
                INSERT INTO mod_oftalmo VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_oftalmo_st,
                :ex_id);";

        $verifica = $this->sql("SELECT 
		m_oftalmo_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_oftalmo 
		inner join sys_usuario on usu_id=m_oftalmo_usu 
		where 
		m_oftalmo_adm='$adm' and m_oftalmo_examen='$exa';");
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

    public function update_oftalmo_pred() {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_oft_pred_resultado'] = $_POST['m_oft_pred_resultado'];
        $params[':m_oft_pred_observaciones'] = $_POST['m_oft_pred_observaciones'];
        $params[':m_oft_pred_diagnostico'] = $_POST['m_oft_pred_diagnostico'];

        $this->begin();
        $q = 'Update mod_oftalmo_pred set
                    m_oft_pred_resultado=:m_oft_pred_resultado,
                    m_oft_pred_observaciones=:m_oft_pred_observaciones,
                    m_oft_pred_diagnostico=:m_oft_pred_diagnostico
                where
                m_oft_pred_adm=:adm and m_oft_pred_examen=:ex_id;
                
                update mod_oftalmo set
                    m_oftalmo_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_oftalmo_adm=:adm and m_oftalmo_examen=:ex_id;';


        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    //LOAD SAVE UPDATE psico_examen

    public function load_oftalmo_oftalmo() {
        $adm = $_POST['adm'];
//        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_oftalmo_oftalmo where m_oft_oftalmo_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_oftalmo_oftalmo() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_oftalmo VALUES
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
        $params_2[':m_oft_oftalmo_correctores'] = $_POST['m_oft_oftalmo_correctores'];
        $params_2[':m_oft_oftalmo_anamnesis'] = $_POST['m_oft_oftalmo_anamnesis'];
        $params_2[':m_oft_oftalmo_patologia'] = $_POST['m_oft_oftalmo_patologia'];
        $params_2[':m_oft_oftalmo_campos_v_od'] = $_POST['m_oft_oftalmo_campos_v_od'];
        $params_2[':m_oft_oftalmo_campos_v_oi'] = $_POST['m_oft_oftalmo_campos_v_oi'];
        $params_2[':m_oft_oftalmo_tonometria_od'] = $_POST['m_oft_oftalmo_tonometria_od'];
        $params_2[':m_oft_oftalmo_tonometria_oi'] = $_POST['m_oft_oftalmo_tonometria_oi'];
        $params_2[':m_oft_oftalmo_ishihara'] = $_POST['m_oft_oftalmo_ishihara'];
        $params_2[':m_oft_oftalmo_anexos'] = $_POST['m_oft_oftalmo_anexos'];
        $params_2[':m_oft_oftalmo_campimetria'] = $_POST['m_oft_oftalmo_campimetria'];
        $params_2[':m_oft_oftalmo_motilidad'] = $_POST['m_oft_oftalmo_motilidad'];
        $params_2[':m_oft_oftalmo_fondo_od'] = $_POST['m_oft_oftalmo_fondo_od'];
        $params_2[':m_oft_oftalmo_fondo_oi'] = $_POST['m_oft_oftalmo_fondo_oi'];
        $params_2[':m_oft_oftalmo_sincorrec_vlejos_od'] = $_POST['m_oft_oftalmo_sincorrec_vlejos_od'];
        $params_2[':m_oft_oftalmo_sincorrec_vlejos_oi'] = $_POST['m_oft_oftalmo_sincorrec_vlejos_oi'];
        $params_2[':m_oft_oftalmo_sincorrec_vcerca_od'] = $_POST['m_oft_oftalmo_sincorrec_vcerca_od'];
        $params_2[':m_oft_oftalmo_sincorrec_vcerca_oi'] = $_POST['m_oft_oftalmo_sincorrec_vcerca_oi'];
        $params_2[':m_oft_oftalmo_sincorrec_binocular'] = $_POST['m_oft_oftalmo_sincorrec_binocular'];
        $params_2[':m_oft_oftalmo_concorrec_vlejos_od'] = $_POST['m_oft_oftalmo_concorrec_vlejos_od'];
        $params_2[':m_oft_oftalmo_concorrec_vlejos_oi'] = $_POST['m_oft_oftalmo_concorrec_vlejos_oi'];
        $params_2[':m_oft_oftalmo_concorrec_vcerca_od'] = $_POST['m_oft_oftalmo_concorrec_vcerca_od'];
        $params_2[':m_oft_oftalmo_concorrec_vcerca_oi'] = $_POST['m_oft_oftalmo_concorrec_vcerca_oi'];
        $params_2[':m_oft_oftalmo_concorrec_binocular'] = $_POST['m_oft_oftalmo_concorrec_binocular'];
        $params_2[':m_oft_oftalmo_esteno_vlejos_od'] = $_POST['m_oft_oftalmo_esteno_vlejos_od'];
        $params_2[':m_oft_oftalmo_esteno_vlejos_oi'] = $_POST['m_oft_oftalmo_esteno_vlejos_oi'];
        $params_2[':m_oft_oftalmo_esteno_vcerca_od'] = $_POST['m_oft_oftalmo_esteno_vcerca_od'];
        $params_2[':m_oft_oftalmo_esteno_vcerca_oi'] = $_POST['m_oft_oftalmo_esteno_vcerca_oi'];
        $params_2[':m_oft_oftalmo_vision_color_od'] = $_POST['m_oft_oftalmo_vision_color_od'];
        $params_2[':m_oft_oftalmo_vision_color_oi'] = $_POST['m_oft_oftalmo_vision_color_oi'];
        $params_2[':m_oft_oftalmo_ref_pupilar_od'] = $_POST['m_oft_oftalmo_ref_pupilar_od'];
        $params_2[':m_oft_oftalmo_ref_pupilar_oi'] = $_POST['m_oft_oftalmo_ref_pupilar_oi'];
        $params_2[':m_oft_oftalmo_esteropsia_od'] = $_POST['m_oft_oftalmo_esteropsia_od'];
        $params_2[':m_oft_oftalmo_esteropsia_oi'] = $_POST['m_oft_oftalmo_esteropsia_oi'];
        $params_2[':m_oft_oftalmo_esteropsia'] = $_POST['m_oft_oftalmo_esteropsia'];
        $params_2[':m_oft_oftalmo_discromatopsia'] = $_POST['m_oft_oftalmo_discromatopsia'];
        $params_2[':m_oft_oftalmo_tipo'] = $_POST['m_oft_oftalmo_tipo'];
        $params_2[':m_oft_oftalmo_verde'] = $_POST['m_oft_oftalmo_verde'];
        $params_2[':m_oft_oftalmo_amarillo'] = $_POST['m_oft_oftalmo_amarillo'];
        $params_2[':m_oft_oftalmo_rojo'] = $_POST['m_oft_oftalmo_rojo'];
        $params_2[':m_oft_oftalmo_ametropia'] = $_POST['m_oft_oftalmo_ametropia'];
        $params_2[':m_oft_oftalmo_conjuntivitis'] = $_POST['m_oft_oftalmo_conjuntivitis'];
        $params_2[':m_oft_oftalmo_ojo_rojo'] = $_POST['m_oft_oftalmo_ojo_rojo'];
        $params_2[':m_oft_oftalmo_catarata'] = $_POST['m_oft_oftalmo_catarata'];
        $params_2[':m_oft_oftalmo_nistagmos'] = $_POST['m_oft_oftalmo_nistagmos'];
        $params_2[':m_oft_oftalmo_pterigion'] = $_POST['m_oft_oftalmo_pterigion'];
        $params_2[':m_oft_oftalmo_ampliacion'] = $_POST['m_oft_oftalmo_ampliacion'];
        $params_2[':m_oft_oftalmo_medico'] = $_POST['m_oft_oftalmo_medico'];



        $q_2 = "INSERT INTO mod_oftalmo_oftalmo VALUES 
                (null,
                :adm,
                :m_oft_oftalmo_correctores,
                :m_oft_oftalmo_anamnesis,
                :m_oft_oftalmo_patologia,
                :m_oft_oftalmo_campos_v_od,
                :m_oft_oftalmo_campos_v_oi,
                :m_oft_oftalmo_tonometria_od,
                :m_oft_oftalmo_tonometria_oi,
                :m_oft_oftalmo_ishihara,
                :m_oft_oftalmo_anexos,
                :m_oft_oftalmo_campimetria,
                :m_oft_oftalmo_motilidad,
                :m_oft_oftalmo_fondo_od,
                :m_oft_oftalmo_fondo_oi,
                :m_oft_oftalmo_sincorrec_vlejos_od,
                :m_oft_oftalmo_sincorrec_vlejos_oi,
                :m_oft_oftalmo_sincorrec_vcerca_od,
                :m_oft_oftalmo_sincorrec_vcerca_oi,
                :m_oft_oftalmo_sincorrec_binocular,
                :m_oft_oftalmo_concorrec_vlejos_od,
                :m_oft_oftalmo_concorrec_vlejos_oi,
                :m_oft_oftalmo_concorrec_vcerca_od,
                :m_oft_oftalmo_concorrec_vcerca_oi,
                :m_oft_oftalmo_concorrec_binocular,
                :m_oft_oftalmo_esteno_vlejos_od,
                :m_oft_oftalmo_esteno_vlejos_oi,
                :m_oft_oftalmo_esteno_vcerca_od,
                :m_oft_oftalmo_esteno_vcerca_oi,
                :m_oft_oftalmo_vision_color_od,
                :m_oft_oftalmo_vision_color_oi,
                :m_oft_oftalmo_ref_pupilar_od,
                :m_oft_oftalmo_ref_pupilar_oi,
                :m_oft_oftalmo_esteropsia_od,
                :m_oft_oftalmo_esteropsia_oi,
                :m_oft_oftalmo_esteropsia,
                :m_oft_oftalmo_discromatopsia,
                :m_oft_oftalmo_tipo,
                :m_oft_oftalmo_verde,
                :m_oft_oftalmo_amarillo,
                :m_oft_oftalmo_rojo,
                :m_oft_oftalmo_ametropia,
                :m_oft_oftalmo_conjuntivitis,
                :m_oft_oftalmo_ojo_rojo,
                :m_oft_oftalmo_catarata,
                :m_oft_oftalmo_nistagmos,
                :m_oft_oftalmo_pterigion,
                :m_oft_oftalmo_ampliacion,
                :m_oft_oftalmo_medico
                );";

        $verifica = $this->sql("SELECT 
		m_oftalmo_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_oftalmo 
		inner join sys_usuario on usu_id=m_oftalmo_usu 
		where 
		m_oftalmo_adm='$adm' and m_oftalmo_examen='$exa';");
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

    public function update_oftalmo_oftalmo() {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_oftalmo set
                    m_oftalmo_usu=:usuario,
                    m_oftalmo_fech_update=now()
                where
                m_oftalmo_id=:id and m_oftalmo_adm=:adm and m_oftalmo_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_oft_oftalmo_correctores'] = $_POST['m_oft_oftalmo_correctores'];
        $params_2[':m_oft_oftalmo_anamnesis'] = $_POST['m_oft_oftalmo_anamnesis'];
        $params_2[':m_oft_oftalmo_patologia'] = $_POST['m_oft_oftalmo_patologia'];
        $params_2[':m_oft_oftalmo_campos_v_od'] = $_POST['m_oft_oftalmo_campos_v_od'];
        $params_2[':m_oft_oftalmo_campos_v_oi'] = $_POST['m_oft_oftalmo_campos_v_oi'];
        $params_2[':m_oft_oftalmo_tonometria_od'] = $_POST['m_oft_oftalmo_tonometria_od'];
        $params_2[':m_oft_oftalmo_tonometria_oi'] = $_POST['m_oft_oftalmo_tonometria_oi'];
        $params_2[':m_oft_oftalmo_ishihara'] = $_POST['m_oft_oftalmo_ishihara'];
        $params_2[':m_oft_oftalmo_anexos'] = $_POST['m_oft_oftalmo_anexos'];
        $params_2[':m_oft_oftalmo_campimetria'] = $_POST['m_oft_oftalmo_campimetria'];
        $params_2[':m_oft_oftalmo_motilidad'] = $_POST['m_oft_oftalmo_motilidad'];
        $params_2[':m_oft_oftalmo_fondo_od'] = $_POST['m_oft_oftalmo_fondo_od'];
        $params_2[':m_oft_oftalmo_fondo_oi'] = $_POST['m_oft_oftalmo_fondo_oi'];
        $params_2[':m_oft_oftalmo_sincorrec_vlejos_od'] = $_POST['m_oft_oftalmo_sincorrec_vlejos_od'];
        $params_2[':m_oft_oftalmo_sincorrec_vlejos_oi'] = $_POST['m_oft_oftalmo_sincorrec_vlejos_oi'];
        $params_2[':m_oft_oftalmo_sincorrec_vcerca_od'] = $_POST['m_oft_oftalmo_sincorrec_vcerca_od'];
        $params_2[':m_oft_oftalmo_sincorrec_vcerca_oi'] = $_POST['m_oft_oftalmo_sincorrec_vcerca_oi'];
        $params_2[':m_oft_oftalmo_sincorrec_binocular'] = $_POST['m_oft_oftalmo_sincorrec_binocular'];
        $params_2[':m_oft_oftalmo_concorrec_vlejos_od'] = $_POST['m_oft_oftalmo_concorrec_vlejos_od'];
        $params_2[':m_oft_oftalmo_concorrec_vlejos_oi'] = $_POST['m_oft_oftalmo_concorrec_vlejos_oi'];
        $params_2[':m_oft_oftalmo_concorrec_vcerca_od'] = $_POST['m_oft_oftalmo_concorrec_vcerca_od'];
        $params_2[':m_oft_oftalmo_concorrec_vcerca_oi'] = $_POST['m_oft_oftalmo_concorrec_vcerca_oi'];
        $params_2[':m_oft_oftalmo_concorrec_binocular'] = $_POST['m_oft_oftalmo_concorrec_binocular'];
        $params_2[':m_oft_oftalmo_esteno_vlejos_od'] = $_POST['m_oft_oftalmo_esteno_vlejos_od'];
        $params_2[':m_oft_oftalmo_esteno_vlejos_oi'] = $_POST['m_oft_oftalmo_esteno_vlejos_oi'];
        $params_2[':m_oft_oftalmo_esteno_vcerca_od'] = $_POST['m_oft_oftalmo_esteno_vcerca_od'];
        $params_2[':m_oft_oftalmo_esteno_vcerca_oi'] = $_POST['m_oft_oftalmo_esteno_vcerca_oi'];
        $params_2[':m_oft_oftalmo_vision_color_od'] = $_POST['m_oft_oftalmo_vision_color_od'];
        $params_2[':m_oft_oftalmo_vision_color_oi'] = $_POST['m_oft_oftalmo_vision_color_oi'];
        $params_2[':m_oft_oftalmo_ref_pupilar_od'] = $_POST['m_oft_oftalmo_ref_pupilar_od'];
        $params_2[':m_oft_oftalmo_ref_pupilar_oi'] = $_POST['m_oft_oftalmo_ref_pupilar_oi'];
        $params_2[':m_oft_oftalmo_esteropsia_od'] = $_POST['m_oft_oftalmo_esteropsia_od'];
        $params_2[':m_oft_oftalmo_esteropsia_oi'] = $_POST['m_oft_oftalmo_esteropsia_oi'];
        $params_2[':m_oft_oftalmo_esteropsia'] = $_POST['m_oft_oftalmo_esteropsia'];
        $params_2[':m_oft_oftalmo_discromatopsia'] = $_POST['m_oft_oftalmo_discromatopsia'];
        $params_2[':m_oft_oftalmo_tipo'] = $_POST['m_oft_oftalmo_tipo'];
        $params_2[':m_oft_oftalmo_verde'] = $_POST['m_oft_oftalmo_verde'];
        $params_2[':m_oft_oftalmo_amarillo'] = $_POST['m_oft_oftalmo_amarillo'];
        $params_2[':m_oft_oftalmo_rojo'] = $_POST['m_oft_oftalmo_rojo'];
        $params_2[':m_oft_oftalmo_ametropia'] = $_POST['m_oft_oftalmo_ametropia'];
        $params_2[':m_oft_oftalmo_conjuntivitis'] = $_POST['m_oft_oftalmo_conjuntivitis'];
        $params_2[':m_oft_oftalmo_ojo_rojo'] = $_POST['m_oft_oftalmo_ojo_rojo'];
        $params_2[':m_oft_oftalmo_catarata'] = $_POST['m_oft_oftalmo_catarata'];
        $params_2[':m_oft_oftalmo_nistagmos'] = $_POST['m_oft_oftalmo_nistagmos'];
        $params_2[':m_oft_oftalmo_pterigion'] = $_POST['m_oft_oftalmo_pterigion'];
        $params_2[':m_oft_oftalmo_ampliacion'] = $_POST['m_oft_oftalmo_ampliacion'];
        $params_2[':m_oft_oftalmo_medico'] = $_POST['m_oft_oftalmo_medico'];

        $q_2 = 'Update mod_oftalmo_oftalmo set                    
                    m_oft_oftalmo_correctores=:m_oft_oftalmo_correctores,
                    m_oft_oftalmo_anamnesis=:m_oft_oftalmo_anamnesis,
                    m_oft_oftalmo_patologia=:m_oft_oftalmo_patologia,
                    m_oft_oftalmo_campos_v_od=:m_oft_oftalmo_campos_v_od,
                    m_oft_oftalmo_campos_v_oi=:m_oft_oftalmo_campos_v_oi,
                    m_oft_oftalmo_tonometria_od=:m_oft_oftalmo_tonometria_od,
                    m_oft_oftalmo_tonometria_oi=:m_oft_oftalmo_tonometria_oi,
                    m_oft_oftalmo_ishihara=:m_oft_oftalmo_ishihara,
                    m_oft_oftalmo_anexos=:m_oft_oftalmo_anexos,
                    m_oft_oftalmo_campimetria=:m_oft_oftalmo_campimetria,
                    m_oft_oftalmo_motilidad=:m_oft_oftalmo_motilidad,
                    m_oft_oftalmo_fondo_od=:m_oft_oftalmo_fondo_od,
                    m_oft_oftalmo_fondo_oi=:m_oft_oftalmo_fondo_oi,
                    m_oft_oftalmo_sincorrec_vlejos_od=:m_oft_oftalmo_sincorrec_vlejos_od,
                    m_oft_oftalmo_sincorrec_vlejos_oi=:m_oft_oftalmo_sincorrec_vlejos_oi,
                    m_oft_oftalmo_sincorrec_vcerca_od=:m_oft_oftalmo_sincorrec_vcerca_od,
                    m_oft_oftalmo_sincorrec_vcerca_oi=:m_oft_oftalmo_sincorrec_vcerca_oi,
                    m_oft_oftalmo_sincorrec_binocular=:m_oft_oftalmo_sincorrec_binocular,
                    m_oft_oftalmo_concorrec_vlejos_od=:m_oft_oftalmo_concorrec_vlejos_od,
                    m_oft_oftalmo_concorrec_vlejos_oi=:m_oft_oftalmo_concorrec_vlejos_oi,
                    m_oft_oftalmo_concorrec_vcerca_od=:m_oft_oftalmo_concorrec_vcerca_od,
                    m_oft_oftalmo_concorrec_vcerca_oi=:m_oft_oftalmo_concorrec_vcerca_oi,
                    m_oft_oftalmo_concorrec_binocular=:m_oft_oftalmo_concorrec_binocular,
                    m_oft_oftalmo_esteno_vlejos_od=:m_oft_oftalmo_esteno_vlejos_od,
                    m_oft_oftalmo_esteno_vlejos_oi=:m_oft_oftalmo_esteno_vlejos_oi,
                    m_oft_oftalmo_esteno_vcerca_od=:m_oft_oftalmo_esteno_vcerca_od,
                    m_oft_oftalmo_esteno_vcerca_oi=:m_oft_oftalmo_esteno_vcerca_oi,
                    m_oft_oftalmo_vision_color_od=:m_oft_oftalmo_vision_color_od,
                    m_oft_oftalmo_vision_color_oi=:m_oft_oftalmo_vision_color_oi,
                    m_oft_oftalmo_ref_pupilar_od=:m_oft_oftalmo_ref_pupilar_od,
                    m_oft_oftalmo_ref_pupilar_oi=:m_oft_oftalmo_ref_pupilar_oi,
                    m_oft_oftalmo_esteropsia_od=:m_oft_oftalmo_esteropsia_od,
                    m_oft_oftalmo_esteropsia_oi=:m_oft_oftalmo_esteropsia_oi,
                    m_oft_oftalmo_esteropsia=:m_oft_oftalmo_esteropsia,
                    m_oft_oftalmo_discromatopsia=:m_oft_oftalmo_discromatopsia,
                    m_oft_oftalmo_tipo=:m_oft_oftalmo_tipo,
                    m_oft_oftalmo_verde=:m_oft_oftalmo_verde,
                    m_oft_oftalmo_amarillo=:m_oft_oftalmo_amarillo,
                    m_oft_oftalmo_rojo=:m_oft_oftalmo_rojo,
                    m_oft_oftalmo_ametropia=:m_oft_oftalmo_ametropia,
                    m_oft_oftalmo_conjuntivitis=:m_oft_oftalmo_conjuntivitis,
                    m_oft_oftalmo_ojo_rojo=:m_oft_oftalmo_ojo_rojo,
                    m_oft_oftalmo_catarata=:m_oft_oftalmo_catarata,
                    m_oft_oftalmo_nistagmos=:m_oft_oftalmo_nistagmos,
                    m_oft_oftalmo_pterigion=:m_oft_oftalmo_pterigion,
                    m_oft_oftalmo_ampliacion=:m_oft_oftalmo_ampliacion,
                    m_oft_oftalmo_medico=:m_oft_oftalmo_medico
                where
                m_oft_oftalmo_adm=:adm;';

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

    //LOAD SAVE UPDATE DIAGNOSTICO

    public function load_medico()
    {
        $sede = $this->user->con_sedid;
        return $this->sql("SELECT medico_id, concat(medico_apepat,' ',medico_apemat,', ',medico_nombre)as nombre
        FROM medico
        where medico_sede=$sede and medico_st=1 and medico_tipo = 'OFTALMOLOGIA';");
    }

    public function load_oftalmo_medico()
    {
        $adm = $_POST['adm'];
        $st = $_POST['st'];
        $usuario = $this->user->us_id;
        $medico = "";
        if ($st < '1') {
            $medico = ",(SELECT medico_id FROM medico where medico_usu='$usuario') m_oft_oftalmo_medico";
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


    public function list_diag() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT 
            diag_ofta_id, diag_ofta_adm, diag_ofta_desc
                FROM mod_oftalmo_diag
                where diag_ofta_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function save_diag() {
        $params = array();
        $params[':diag_ofta_adm'] = $_POST['diag_ofta_adm'];
        ($_POST['diag_ofta_tipo'] == 1) ? $params[':diag_ofta_desc'] = $_POST['diag_ofta_desc'] : $params[':diag_ofta_desc'] = $_POST['diag_ofta_cie'];

        $q = 'INSERT INTO mod_oftalmo_diag VALUES 
                (NULL,
                :diag_ofta_adm,
                UPPER(:diag_ofta_desc))';
        return $this->sql($q, $params);
    }

    public function update_diag() {
        $params = array();
        $params[':diag_ofta_id'] = $_POST['diag_ofta_id'];
        $params[':diag_ofta_adm'] = $_POST['diag_ofta_adm'];
        ($_POST['diag_ofta_tipo'] == 1) ? $params[':diag_ofta_desc'] = $_POST['diag_ofta_desc'] : $params[':diag_ofta_desc'] = $_POST['diag_ofta_cie'];

        $this->begin();
        $q = 'Update mod_oftalmo_diag set
                diag_ofta_desc=UPPER(:diag_ofta_desc)
                where
                diag_ofta_id=:diag_ofta_id and diag_ofta_adm=:diag_ofta_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $pac_id = $_POST['diag_ofta_id'];
            $this->commit();
            return array('success' => true, 'data' => $pac_id);
        } else {
            $this->rollback();
        }
    }

    public function load_diag() {
        $diag_adm = $_POST['diag_ofta_adm'];
        $diag_id = $_POST['diag_ofta_id'];
        $query = "SELECT
            diag_ofta_id, diag_ofta_adm, diag_ofta_desc
            FROM mod_oftalmo_diag
            where
            diag_ofta_id=$diag_id and
            diag_ofta_adm=$diag_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function st_busca_diag() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT diag_ofta_desc FROM mod_oftalmo_diag
                            where
                            diag_ofta_desc like '%$query%'
                            group by diag_ofta_desc");
        return $sql;
    }

    public function list_cie10() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT cie4_id, cie4_cie3id
                , concat(cie4_id,' - ',cie4_desc) cie4_desc
                FROM cie4
                where
                concat(cie4_id, cie4_cie3id, cie4_desc) like'%$query%'
                order by cie4_cie3id;");
        return $sql;
    }

    //LOAD SAVE UPDATE RECOMENDACION

    public function list_reco() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT reco_ofta_id, reco_ofta_adm, reco_ofta_desc
                FROM mod_oftalmo_reco
                where reco_ofta_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function save_reco() {
        $params = array();
        $params[':reco_ofta_adm'] = $_POST['reco_ofta_adm'];
        $params[':reco_ofta_desc'] = $_POST['reco_ofta_desc'];

        $q = 'INSERT INTO mod_oftalmo_reco VALUES 
                (NULL,
                :reco_ofta_adm,
                UPPER(:reco_ofta_desc))';
        return $this->sql($q, $params);
    }

    public function update_reco() {
        $params = array();
        $params[':reco_ofta_id'] = $_POST['reco_ofta_id'];
        $params[':reco_ofta_adm'] = $_POST['reco_ofta_adm'];
        $params[':reco_ofta_desc'] = $_POST['reco_ofta_desc'];

        $this->begin();
        $q = 'Update mod_oftalmo_reco set
                reco_ofta_desc=UPPER(:reco_ofta_desc)
                where
                reco_ofta_id=:reco_ofta_id and reco_ofta_adm=:reco_ofta_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $pac_id = $_POST['reco_ofta_adm'];
            $this->commit();
            return array('success' => true, 'data' => $pac_id);
        } else {
            $this->rollback();
        }
    }

    public function st_busca_reco() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT reco_ofta_desc FROM mod_oftalmo_reco
                            where
                            reco_ofta_desc like '%$query%'
                            group by reco_ofta_desc");
        return $sql;
    }

    public function load_reco() {
        $reco_id = $_POST['reco_ofta_id'];
        $reco_adm = $_POST['reco_ofta_adm'];
        $query = "SELECT
            reco_ofta_id, reco_ofta_adm, reco_ofta_desc
            FROM mod_oftalmo_reco
            where
            reco_ofta_id=$reco_id and
            reco_ofta_adm=$reco_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //LOAD SAVE UPDATE PDF

    public function paciente($adm) {
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

    public function rpt_pac($adm) {
        return $this->sql("SELECT
                    adm_id,pac_cel,if(pac_sexo='M','MASCULINO','FEMENINO') pac_sexo,pac_ndoc
                    ,pac_nombres,pac_apmat,pac_appat
                    ,emp_desc,concat(adm_puesto,' - ',adm_area)as adm_act,Date_format(adm_fech,'%d-%m-%Y') adm_fechc
                    ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
                    FROM admision
                    inner join paciente on adm_pac=pac_id
                    inner join pack on pk_id=adm_ruta
                    inner join empresa on emp_id=pk_emp
                    where adm_id=$adm");
    }

    public function rpt_oftalmo($adm) {
        return $this->sql("SELECT mod_oftalmo_oftalmo.* 
        , medico_cmp, medico_firma
                    FROM mod_oftalmo_oftalmo
            left join medico on medico_id=m_oft_oftalmo_medico
                    where m_oft_oftalmo_adm=$adm");
    }

    public function rpt_diag($adm) {
        $q = "SELECT upper(diag_ofta_desc) diag_ofta_desc FROM mod_oftalmo_diag where diag_ofta_adm=$adm";
        return $this->sql($q);
    }

    public function rpt_reco($adm) {
        $q = "SELECT upper(reco_ofta_desc) reco_ofta_desc FROM mod_oftalmo_reco where reco_ofta_adm=$adm";
        return $this->sql($q);
    }

}

//$sesion = new model(); 
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
?>