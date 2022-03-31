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
                    ex_arid IN (2)
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
            $verifica = $this->sql("SELECT count(m_rayosx_adm)total FROM mod_rayosx where m_rayosx_adm=$adm_id;");
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
                where ex_arid IN (2) and adm_id=$adm_id order by ex_arid;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_rayosx_id id,m_rayosx_st st
                , m_rayosx_usu usu, m_rayosx_fech_reg fech 
            FROM mod_rayosx 
            where m_rayosx_adm=$adm_id and m_rayosx_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    //LOAD SAVE UPDATE LABORATORIO

    public function load_rayosx_pred() {
        $adm = $_POST['adm'];
        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_rayosx_pred where m_rx_pred_adm='$adm' and m_rx_pred_examen='$examen';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_rayosx_pred() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':m_rayosx_st'] = '1'; //ESTADO DEL MODULO PRINCIPAL

        $params[':adm'] = $adm;
        $params[':ex_id'] = $exa;

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_rx_pred_resultado'] = $_POST['m_rx_pred_resultado'];
        $params[':m_rx_pred_observaciones'] = $_POST['m_rx_pred_observaciones'];
        $params[':m_rx_pred_diagnostico'] = $_POST['m_rx_pred_diagnostico'];


        $q = "INSERT INTO mod_rayosx_pred VALUES 
                (null,
                :adm,
                :ex_id,
                :m_rx_pred_resultado,
                :m_rx_pred_observaciones,
                :m_rx_pred_diagnostico);
                
                INSERT INTO mod_rayosx VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_rayosx_st,
                :ex_id);";

        $verifica = $this->sql("SELECT 
		m_rayosx_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_rayosx 
		inner join sys_usuario on usu_id=m_rayosx_usu 
		where 
		m_rayosx_adm='$adm' and m_rayosx_examen='$exa';");
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

    public function update_rayosx_pred() {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_rx_pred_resultado'] = $_POST['m_rx_pred_resultado'];
        $params[':m_rx_pred_observaciones'] = $_POST['m_rx_pred_observaciones'];
        $params[':m_rx_pred_diagnostico'] = $_POST['m_rx_pred_diagnostico'];

        $this->begin();
        $q = 'Update mod_rayosx_pred set
                    m_rx_pred_resultado=:m_rx_pred_resultado,
                    m_rx_pred_observaciones=:m_rx_pred_observaciones,
                    m_rx_pred_diagnostico=:m_rx_pred_diagnostico
                where
                m_rx_pred_adm=:adm and m_rx_pred_examen=:ex_id;
                
                update mod_rayosx set
                    m_rayosx_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_rayosx_adm=:adm and m_rayosx_examen=:ex_id;';


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

    public function load_rayosx_rayosx() {
        $adm = $_POST['adm'];
//        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_rayosx_rayosx where m_rx_rayosx_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_rayosx_rayosx() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $adm;
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $exa;

        $q_1 = "INSERT INTO mod_rayosx VALUES
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
        $params_2[':adm'] = $adm;
        $params_2[':m_rx_rayosx_n_placa'] = $_POST['m_rx_rayosx_n_placa'];
        $params_2[':m_rx_rayosx_lector'] = $_POST['m_rx_rayosx_lector'];
        $params_2[':m_rx_rayosx_fech_lectura'] = $_POST['m_rx_rayosx_fech_lectura'];
        $params_2[':m_rx_rayosx_calidad'] = $_POST['m_rx_rayosx_calidad'];
        $params_2[':m_rx_rayosx_causas'] = $_POST['m_rx_rayosx_causas'];
        $params_2[':m_rx_rayosx_coment_tec'] = $_POST['m_rx_rayosx_coment_tec'];
        $params_2[':m_rx_rayosx_zona_a_sup_der'] = $_POST['m_rx_rayosx_zona_a_sup_der'];
        $params_2[':m_rx_rayosx_zona_a_sup_izq'] = $_POST['m_rx_rayosx_zona_a_sup_izq'];
        $params_2[':m_rx_rayosx_zona_a_med_der'] = $_POST['m_rx_rayosx_zona_a_med_der'];
        $params_2[':m_rx_rayosx_zona_a_med_izq'] = $_POST['m_rx_rayosx_zona_a_med_izq'];
        $params_2[':m_rx_rayosx_zona_a_inf_der'] = $_POST['m_rx_rayosx_zona_a_inf_der'];
        $params_2[':m_rx_rayosx_zona_a_inf_izq'] = $_POST['m_rx_rayosx_zona_a_inf_izq'];
        $params_2[':m_rx_rayosx_profusion'] = $_POST['m_rx_rayosx_profusion'];
        $params_2[':m_rx_rayosx_forma_tama_pri'] = $_POST['m_rx_rayosx_forma_tama_pri'];
        $params_2[':m_rx_rayosx_forma_tama_sec'] = $_POST['m_rx_rayosx_forma_tama_sec'];
        $params_2[':m_rx_rayosx_opacidad'] = $_POST['m_rx_rayosx_opacidad'];
        $params_2[':m_rx_rayosx_anormal_pleural'] = $_POST['m_rx_rayosx_anormal_pleural'];
        $params_2[':m_rx_rayosx_sitio_pared'] = $_POST['m_rx_rayosx_sitio_pared'];
        $params_2[':m_rx_rayosx_sitio_pared_calci'] = $_POST['m_rx_rayosx_sitio_pared_calci'];
        $params_2[':m_rx_rayosx_sitio_frente'] = $_POST['m_rx_rayosx_sitio_frente'];
        $params_2[':m_rx_rayosx_sitio_frente_calci'] = $_POST['m_rx_rayosx_sitio_frente_calci'];
        $params_2[':m_rx_rayosx_sitio_diagra'] = $_POST['m_rx_rayosx_sitio_diagra'];
        $params_2[':m_rx_rayosx_sitio_diagra_calci'] = $_POST['m_rx_rayosx_sitio_diagra_calci'];
        $params_2[':m_rx_rayosx_sitio_otros'] = $_POST['m_rx_rayosx_sitio_otros'];
        $params_2[':m_rx_rayosx_sitio_otros_calci'] = $_POST['m_rx_rayosx_sitio_otros_calci'];
        $params_2[':m_rx_rayosx_sitio_oblite_calci'] = $_POST['m_rx_rayosx_sitio_oblite_calci'];
        $params_2[':m_rx_rayosx_exten_0D'] = $_POST['m_rx_rayosx_exten_0D'];
        $params_2[':m_rx_rayosx_exten_0D_123'] = $_POST['m_rx_rayosx_exten_0D_123'];
        $params_2[':m_rx_rayosx_exten_0I'] = $_POST['m_rx_rayosx_exten_0I'];
        $params_2[':m_rx_rayosx_exten_0I_123'] = $_POST['m_rx_rayosx_exten_0I_123'];
        $params_2[':m_rx_rayosx_ancho_D_abc'] = $_POST['m_rx_rayosx_ancho_D_abc'];
        $params_2[':m_rx_rayosx_ancho_I_abc'] = $_POST['m_rx_rayosx_ancho_I_abc'];
        $params_2[':m_rx_rayosx_pared_perfil'] = $_POST['m_rx_rayosx_pared_perfil'];
        $params_2[':m_rx_rayosx_pared_frente'] = $_POST['m_rx_rayosx_pared_frente'];
        $params_2[':m_rx_rayosx_calci_perfil'] = $_POST['m_rx_rayosx_calci_perfil'];
        $params_2[':m_rx_rayosx_calci_frente'] = $_POST['m_rx_rayosx_calci_frente'];
        $params_2[':m_rx_rayosx_engro_exten_0D'] = $_POST['m_rx_rayosx_engro_exten_0D'];
        $params_2[':m_rx_rayosx_engro_exten_0D_123'] = $_POST['m_rx_rayosx_engro_exten_0D_123'];
        $params_2[':m_rx_rayosx_engro_exten_0I'] = $_POST['m_rx_rayosx_engro_exten_0I'];
        $params_2[':m_rx_rayosx_engro_exten_0I_123'] = $_POST['m_rx_rayosx_engro_exten_0I_123'];
        $params_2[':m_rx_rayosx_engro_ancho_D_abc'] = $_POST['m_rx_rayosx_engro_ancho_D_abc'];
        $params_2[':m_rx_rayosx_engro_ancho_I_abc'] = $_POST['m_rx_rayosx_engro_ancho_I_abc'];
        $params_2[':m_rx_rayosx_simbolo'] = $_POST['m_rx_rayosx_simbolo'];
        $params_2[':m_rx_rayosx_aa'] = $_POST['m_rx_rayosx_aa'];
        $params_2[':m_rx_rayosx_at'] = $_POST['m_rx_rayosx_at'];
        $params_2[':m_rx_rayosx_ax'] = $_POST['m_rx_rayosx_ax'];
        $params_2[':m_rx_rayosx_bu'] = $_POST['m_rx_rayosx_bu'];
        $params_2[':m_rx_rayosx_ca'] = $_POST['m_rx_rayosx_ca'];
        $params_2[':m_rx_rayosx_cg'] = $_POST['m_rx_rayosx_cg'];
        $params_2[':m_rx_rayosx_cn'] = $_POST['m_rx_rayosx_cn'];
        $params_2[':m_rx_rayosx_co'] = $_POST['m_rx_rayosx_co'];
        $params_2[':m_rx_rayosx_cp'] = $_POST['m_rx_rayosx_cp'];
        $params_2[':m_rx_rayosx_cv'] = $_POST['m_rx_rayosx_cv'];
        $params_2[':m_rx_rayosx_di'] = $_POST['m_rx_rayosx_di'];
        $params_2[':m_rx_rayosx_ef'] = $_POST['m_rx_rayosx_ef'];
        $params_2[':m_rx_rayosx_em'] = $_POST['m_rx_rayosx_em'];
        $params_2[':m_rx_rayosx_es'] = $_POST['m_rx_rayosx_es'];
        $params_2[':m_rx_rayosx_od'] = $_POST['m_rx_rayosx_od'];
        $params_2[':m_rx_rayosx_fr'] = $_POST['m_rx_rayosx_fr'];
        $params_2[':m_rx_rayosx_hi'] = $_POST['m_rx_rayosx_hi'];
        $params_2[':m_rx_rayosx_ho'] = $_POST['m_rx_rayosx_ho'];
        $params_2[':m_rx_rayosx_ids'] = $_POST['m_rx_rayosx_ids'];
        $params_2[':m_rx_rayosx_ih'] = $_POST['m_rx_rayosx_ih'];
        $params_2[':m_rx_rayosx_kl'] = $_POST['m_rx_rayosx_kl'];
        $params_2[':m_rx_rayosx_me'] = $_POST['m_rx_rayosx_me'];
        $params_2[':m_rx_rayosx_pa'] = $_POST['m_rx_rayosx_pa'];
        $params_2[':m_rx_rayosx_pb'] = $_POST['m_rx_rayosx_pb'];
        $params_2[':m_rx_rayosx_pi'] = $_POST['m_rx_rayosx_pi'];
        $params_2[':m_rx_rayosx_px'] = $_POST['m_rx_rayosx_px'];
        $params_2[':m_rx_rayosx_ra'] = $_POST['m_rx_rayosx_ra'];
        $params_2[':m_rx_rayosx_rp'] = $_POST['m_rx_rayosx_rp'];
        $params_2[':m_rx_rayosx_tb'] = $_POST['m_rx_rayosx_tb'];
        $params_2[':m_rx_rayosx_coment'] = $_POST['m_rx_rayosx_coment'];
        $params_2[':m_rx_rayosx_obs'] = $_POST['m_rx_rayosx_obs'];
        $params_2[':m_rx_rayosx_concluciones'] = $_POST['m_rx_rayosx_concluciones'];
        $params_2[':m_rx_rayosx_vertice'] = $_POST['m_rx_rayosx_vertice'];
        $params_2[':m_rx_rayosx_mediastinos'] = $_POST['m_rx_rayosx_mediastinos'];
        $params_2[':m_rx_rayosx_camp_pulmo'] = $_POST['m_rx_rayosx_camp_pulmo'];
        $params_2[':m_rx_rayosx_silueta_card'] = $_POST['m_rx_rayosx_silueta_card'];
        $params_2[':m_rx_rayosx_hilos'] = $_POST['m_rx_rayosx_hilos'];
        $params_2[':m_rx_rayosx_senos'] = $_POST['m_rx_rayosx_senos'];


        $q_2 = "INSERT INTO mod_rayosx_rayosx VALUES 
                (null,
                :adm,
                :m_rx_rayosx_n_placa,
                :m_rx_rayosx_lector,
                :m_rx_rayosx_fech_lectura,
                :m_rx_rayosx_calidad,
                :m_rx_rayosx_causas,
                :m_rx_rayosx_coment_tec,
                :m_rx_rayosx_zona_a_sup_der,
                :m_rx_rayosx_zona_a_sup_izq,
                :m_rx_rayosx_zona_a_med_der,
                :m_rx_rayosx_zona_a_med_izq,
                :m_rx_rayosx_zona_a_inf_der,
                :m_rx_rayosx_zona_a_inf_izq,
                :m_rx_rayosx_profusion,
                :m_rx_rayosx_forma_tama_pri,
                :m_rx_rayosx_forma_tama_sec,
                :m_rx_rayosx_opacidad,
                :m_rx_rayosx_anormal_pleural,
                :m_rx_rayosx_sitio_pared,
                :m_rx_rayosx_sitio_pared_calci,
                :m_rx_rayosx_sitio_frente,
                :m_rx_rayosx_sitio_frente_calci,
                :m_rx_rayosx_sitio_diagra,
                :m_rx_rayosx_sitio_diagra_calci,
                :m_rx_rayosx_sitio_otros,
                :m_rx_rayosx_sitio_otros_calci,
                :m_rx_rayosx_sitio_oblite_calci,
                :m_rx_rayosx_exten_0D,
                :m_rx_rayosx_exten_0D_123,
                :m_rx_rayosx_exten_0I,
                :m_rx_rayosx_exten_0I_123,
                :m_rx_rayosx_ancho_D_abc,
                :m_rx_rayosx_ancho_I_abc,
                :m_rx_rayosx_pared_perfil,
                :m_rx_rayosx_pared_frente,
                :m_rx_rayosx_calci_perfil,
                :m_rx_rayosx_calci_frente,
                :m_rx_rayosx_engro_exten_0D,
                :m_rx_rayosx_engro_exten_0D_123,
                :m_rx_rayosx_engro_exten_0I,
                :m_rx_rayosx_engro_exten_0I_123,
                :m_rx_rayosx_engro_ancho_D_abc,
                :m_rx_rayosx_engro_ancho_I_abc,
                :m_rx_rayosx_simbolo,
                :m_rx_rayosx_aa,
                :m_rx_rayosx_at,
                :m_rx_rayosx_ax,
                :m_rx_rayosx_bu,
                :m_rx_rayosx_ca,
                :m_rx_rayosx_cg,
                :m_rx_rayosx_cn,
                :m_rx_rayosx_co,
                :m_rx_rayosx_cp,
                :m_rx_rayosx_cv,
                :m_rx_rayosx_di,
                :m_rx_rayosx_ef,
                :m_rx_rayosx_em,
                :m_rx_rayosx_es,
                :m_rx_rayosx_od,
                :m_rx_rayosx_fr,
                :m_rx_rayosx_hi,
                :m_rx_rayosx_ho,
                :m_rx_rayosx_ids,
                :m_rx_rayosx_ih,
                :m_rx_rayosx_kl,
                :m_rx_rayosx_me,
                :m_rx_rayosx_pa,
                :m_rx_rayosx_pb,
                :m_rx_rayosx_pi,
                :m_rx_rayosx_px,
                :m_rx_rayosx_ra,
                :m_rx_rayosx_rp,
                :m_rx_rayosx_tb,
                :m_rx_rayosx_coment,
                :m_rx_rayosx_obs,
                :m_rx_rayosx_concluciones,
                :m_rx_rayosx_vertice,
                :m_rx_rayosx_mediastinos,
                :m_rx_rayosx_camp_pulmo,
                :m_rx_rayosx_silueta_card,
                :m_rx_rayosx_hilos,
                :m_rx_rayosx_senos);";

        $verifica = $this->sql("SELECT 
		m_rayosx_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_rayosx 
		inner join sys_usuario on usu_id=m_rayosx_usu 
		where 
		m_rayosx_adm='$adm' and m_rayosx_examen='$exa';");
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

    public function update_rayosx_rayosx() {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_rayosx set
                    m_rayosx_usu=:usuario,
                    m_rayosx_fech_update=now()
                where
                m_rayosx_id=:id and m_rayosx_adm=:adm and m_rayosx_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_rx_rayosx_n_placa'] = $_POST['m_rx_rayosx_n_placa'];
        $params_2[':m_rx_rayosx_lector'] = $_POST['m_rx_rayosx_lector'];
        $params_2[':m_rx_rayosx_fech_lectura'] = $_POST['m_rx_rayosx_fech_lectura'];
        $params_2[':m_rx_rayosx_calidad'] = $_POST['m_rx_rayosx_calidad'];
        $params_2[':m_rx_rayosx_causas'] = $_POST['m_rx_rayosx_causas'];
        $params_2[':m_rx_rayosx_coment_tec'] = $_POST['m_rx_rayosx_coment_tec'];
        $params_2[':m_rx_rayosx_zona_a_sup_der'] = $_POST['m_rx_rayosx_zona_a_sup_der'];
        $params_2[':m_rx_rayosx_zona_a_sup_izq'] = $_POST['m_rx_rayosx_zona_a_sup_izq'];
        $params_2[':m_rx_rayosx_zona_a_med_der'] = $_POST['m_rx_rayosx_zona_a_med_der'];
        $params_2[':m_rx_rayosx_zona_a_med_izq'] = $_POST['m_rx_rayosx_zona_a_med_izq'];
        $params_2[':m_rx_rayosx_zona_a_inf_der'] = $_POST['m_rx_rayosx_zona_a_inf_der'];
        $params_2[':m_rx_rayosx_zona_a_inf_izq'] = $_POST['m_rx_rayosx_zona_a_inf_izq'];
        $params_2[':m_rx_rayosx_profusion'] = $_POST['m_rx_rayosx_profusion'];
        $params_2[':m_rx_rayosx_forma_tama_pri'] = $_POST['m_rx_rayosx_forma_tama_pri'];
        $params_2[':m_rx_rayosx_forma_tama_sec'] = $_POST['m_rx_rayosx_forma_tama_sec'];
        $params_2[':m_rx_rayosx_opacidad'] = $_POST['m_rx_rayosx_opacidad'];
        $params_2[':m_rx_rayosx_anormal_pleural'] = $_POST['m_rx_rayosx_anormal_pleural'];
        $params_2[':m_rx_rayosx_sitio_pared'] = $_POST['m_rx_rayosx_sitio_pared'];
        $params_2[':m_rx_rayosx_sitio_pared_calci'] = $_POST['m_rx_rayosx_sitio_pared_calci'];
        $params_2[':m_rx_rayosx_sitio_frente'] = $_POST['m_rx_rayosx_sitio_frente'];
        $params_2[':m_rx_rayosx_sitio_frente_calci'] = $_POST['m_rx_rayosx_sitio_frente_calci'];
        $params_2[':m_rx_rayosx_sitio_diagra'] = $_POST['m_rx_rayosx_sitio_diagra'];
        $params_2[':m_rx_rayosx_sitio_diagra_calci'] = $_POST['m_rx_rayosx_sitio_diagra_calci'];
        $params_2[':m_rx_rayosx_sitio_otros'] = $_POST['m_rx_rayosx_sitio_otros'];
        $params_2[':m_rx_rayosx_sitio_otros_calci'] = $_POST['m_rx_rayosx_sitio_otros_calci'];
        $params_2[':m_rx_rayosx_sitio_oblite_calci'] = $_POST['m_rx_rayosx_sitio_oblite_calci'];
        $params_2[':m_rx_rayosx_exten_0D'] = $_POST['m_rx_rayosx_exten_0D'];
        $params_2[':m_rx_rayosx_exten_0D_123'] = $_POST['m_rx_rayosx_exten_0D_123'];
        $params_2[':m_rx_rayosx_exten_0I'] = $_POST['m_rx_rayosx_exten_0I'];
        $params_2[':m_rx_rayosx_exten_0I_123'] = $_POST['m_rx_rayosx_exten_0I_123'];
        $params_2[':m_rx_rayosx_ancho_D_abc'] = $_POST['m_rx_rayosx_ancho_D_abc'];
        $params_2[':m_rx_rayosx_ancho_I_abc'] = $_POST['m_rx_rayosx_ancho_I_abc'];
        $params_2[':m_rx_rayosx_pared_perfil'] = $_POST['m_rx_rayosx_pared_perfil'];
        $params_2[':m_rx_rayosx_pared_frente'] = $_POST['m_rx_rayosx_pared_frente'];
        $params_2[':m_rx_rayosx_calci_perfil'] = $_POST['m_rx_rayosx_calci_perfil'];
        $params_2[':m_rx_rayosx_calci_frente'] = $_POST['m_rx_rayosx_calci_frente'];
        $params_2[':m_rx_rayosx_engro_exten_0D'] = $_POST['m_rx_rayosx_engro_exten_0D'];
        $params_2[':m_rx_rayosx_engro_exten_0D_123'] = $_POST['m_rx_rayosx_engro_exten_0D_123'];
        $params_2[':m_rx_rayosx_engro_exten_0I'] = $_POST['m_rx_rayosx_engro_exten_0I'];
        $params_2[':m_rx_rayosx_engro_exten_0I_123'] = $_POST['m_rx_rayosx_engro_exten_0I_123'];
        $params_2[':m_rx_rayosx_engro_ancho_D_abc'] = $_POST['m_rx_rayosx_engro_ancho_D_abc'];
        $params_2[':m_rx_rayosx_engro_ancho_I_abc'] = $_POST['m_rx_rayosx_engro_ancho_I_abc'];
        $params_2[':m_rx_rayosx_simbolo'] = $_POST['m_rx_rayosx_simbolo'];
        $params_2[':m_rx_rayosx_aa'] = $_POST['m_rx_rayosx_aa'];
        $params_2[':m_rx_rayosx_at'] = $_POST['m_rx_rayosx_at'];
        $params_2[':m_rx_rayosx_ax'] = $_POST['m_rx_rayosx_ax'];
        $params_2[':m_rx_rayosx_bu'] = $_POST['m_rx_rayosx_bu'];
        $params_2[':m_rx_rayosx_ca'] = $_POST['m_rx_rayosx_ca'];
        $params_2[':m_rx_rayosx_cg'] = $_POST['m_rx_rayosx_cg'];
        $params_2[':m_rx_rayosx_cn'] = $_POST['m_rx_rayosx_cn'];
        $params_2[':m_rx_rayosx_co'] = $_POST['m_rx_rayosx_co'];
        $params_2[':m_rx_rayosx_cp'] = $_POST['m_rx_rayosx_cp'];
        $params_2[':m_rx_rayosx_cv'] = $_POST['m_rx_rayosx_cv'];
        $params_2[':m_rx_rayosx_di'] = $_POST['m_rx_rayosx_di'];
        $params_2[':m_rx_rayosx_ef'] = $_POST['m_rx_rayosx_ef'];
        $params_2[':m_rx_rayosx_em'] = $_POST['m_rx_rayosx_em'];
        $params_2[':m_rx_rayosx_es'] = $_POST['m_rx_rayosx_es'];
        $params_2[':m_rx_rayosx_od'] = $_POST['m_rx_rayosx_od'];
        $params_2[':m_rx_rayosx_fr'] = $_POST['m_rx_rayosx_fr'];
        $params_2[':m_rx_rayosx_hi'] = $_POST['m_rx_rayosx_hi'];
        $params_2[':m_rx_rayosx_ho'] = $_POST['m_rx_rayosx_ho'];
        $params_2[':m_rx_rayosx_ids'] = $_POST['m_rx_rayosx_ids'];
        $params_2[':m_rx_rayosx_ih'] = $_POST['m_rx_rayosx_ih'];
        $params_2[':m_rx_rayosx_kl'] = $_POST['m_rx_rayosx_kl'];
        $params_2[':m_rx_rayosx_me'] = $_POST['m_rx_rayosx_me'];
        $params_2[':m_rx_rayosx_pa'] = $_POST['m_rx_rayosx_pa'];
        $params_2[':m_rx_rayosx_pb'] = $_POST['m_rx_rayosx_pb'];
        $params_2[':m_rx_rayosx_pi'] = $_POST['m_rx_rayosx_pi'];
        $params_2[':m_rx_rayosx_px'] = $_POST['m_rx_rayosx_px'];
        $params_2[':m_rx_rayosx_ra'] = $_POST['m_rx_rayosx_ra'];
        $params_2[':m_rx_rayosx_rp'] = $_POST['m_rx_rayosx_rp'];
        $params_2[':m_rx_rayosx_tb'] = $_POST['m_rx_rayosx_tb'];
        $params_2[':m_rx_rayosx_coment'] = $_POST['m_rx_rayosx_coment'];
        $params_2[':m_rx_rayosx_obs'] = $_POST['m_rx_rayosx_obs'];
        $params_2[':m_rx_rayosx_concluciones'] = $_POST['m_rx_rayosx_concluciones'];
        $params_2[':m_rx_rayosx_vertice'] = $_POST['m_rx_rayosx_vertice'];
        $params_2[':m_rx_rayosx_mediastinos'] = $_POST['m_rx_rayosx_mediastinos'];
        $params_2[':m_rx_rayosx_camp_pulmo'] = $_POST['m_rx_rayosx_camp_pulmo'];
        $params_2[':m_rx_rayosx_silueta_card'] = $_POST['m_rx_rayosx_silueta_card'];
        $params_2[':m_rx_rayosx_hilos'] = $_POST['m_rx_rayosx_hilos'];
        $params_2[':m_rx_rayosx_senos'] = $_POST['m_rx_rayosx_senos'];

        $q_2 = 'Update mod_rayosx_rayosx set
                    m_rx_rayosx_n_placa=:m_rx_rayosx_n_placa,
                    m_rx_rayosx_lector=:m_rx_rayosx_lector,
                    m_rx_rayosx_fech_lectura=:m_rx_rayosx_fech_lectura,
                    m_rx_rayosx_calidad=:m_rx_rayosx_calidad,
                    m_rx_rayosx_causas=:m_rx_rayosx_causas,
                    m_rx_rayosx_coment_tec=:m_rx_rayosx_coment_tec,
                    m_rx_rayosx_zona_a_sup_der=:m_rx_rayosx_zona_a_sup_der,
                    m_rx_rayosx_zona_a_sup_izq=:m_rx_rayosx_zona_a_sup_izq,
                    m_rx_rayosx_zona_a_med_der=:m_rx_rayosx_zona_a_med_der,
                    m_rx_rayosx_zona_a_med_izq=:m_rx_rayosx_zona_a_med_izq,
                    m_rx_rayosx_zona_a_inf_der=:m_rx_rayosx_zona_a_inf_der,
                    m_rx_rayosx_zona_a_inf_izq=:m_rx_rayosx_zona_a_inf_izq,
                    m_rx_rayosx_profusion=:m_rx_rayosx_profusion,
                    m_rx_rayosx_forma_tama_pri=:m_rx_rayosx_forma_tama_pri,
                    m_rx_rayosx_forma_tama_sec=:m_rx_rayosx_forma_tama_sec,
                    m_rx_rayosx_opacidad=:m_rx_rayosx_opacidad,
                    m_rx_rayosx_anormal_pleural=:m_rx_rayosx_anormal_pleural,
                    m_rx_rayosx_sitio_pared=:m_rx_rayosx_sitio_pared,
                    m_rx_rayosx_sitio_pared_calci=:m_rx_rayosx_sitio_pared_calci,
                    m_rx_rayosx_sitio_frente=:m_rx_rayosx_sitio_frente,
                    m_rx_rayosx_sitio_frente_calci=:m_rx_rayosx_sitio_frente_calci,
                    m_rx_rayosx_sitio_diagra=:m_rx_rayosx_sitio_diagra,
                    m_rx_rayosx_sitio_diagra_calci=:m_rx_rayosx_sitio_diagra_calci,
                    m_rx_rayosx_sitio_otros=:m_rx_rayosx_sitio_otros,
                    m_rx_rayosx_sitio_otros_calci=:m_rx_rayosx_sitio_otros_calci,
                    m_rx_rayosx_sitio_oblite_calci=:m_rx_rayosx_sitio_oblite_calci,
                    m_rx_rayosx_exten_0D=:m_rx_rayosx_exten_0D,
                    m_rx_rayosx_exten_0D_123=:m_rx_rayosx_exten_0D_123,
                    m_rx_rayosx_exten_0I=:m_rx_rayosx_exten_0I,
                    m_rx_rayosx_exten_0I_123=:m_rx_rayosx_exten_0I_123,
                    m_rx_rayosx_ancho_D_abc=:m_rx_rayosx_ancho_D_abc,
                    m_rx_rayosx_ancho_I_abc=:m_rx_rayosx_ancho_I_abc,
                    m_rx_rayosx_pared_perfil=:m_rx_rayosx_pared_perfil,
                    m_rx_rayosx_pared_frente=:m_rx_rayosx_pared_frente,
                    m_rx_rayosx_calci_perfil=:m_rx_rayosx_calci_perfil,
                    m_rx_rayosx_calci_frente=:m_rx_rayosx_calci_frente,
                    m_rx_rayosx_engro_exten_0D=:m_rx_rayosx_engro_exten_0D,
                    m_rx_rayosx_engro_exten_0D_123=:m_rx_rayosx_engro_exten_0D_123,
                    m_rx_rayosx_engro_exten_0I=:m_rx_rayosx_engro_exten_0I,
                    m_rx_rayosx_engro_exten_0I_123=:m_rx_rayosx_engro_exten_0I_123,
                    m_rx_rayosx_engro_ancho_D_abc=:m_rx_rayosx_engro_ancho_D_abc,
                    m_rx_rayosx_engro_ancho_I_abc=:m_rx_rayosx_engro_ancho_I_abc,
                    m_rx_rayosx_simbolo=:m_rx_rayosx_simbolo,
                    m_rx_rayosx_aa=:m_rx_rayosx_aa,
                    m_rx_rayosx_at=:m_rx_rayosx_at,
                    m_rx_rayosx_ax=:m_rx_rayosx_ax,
                    m_rx_rayosx_bu=:m_rx_rayosx_bu,
                    m_rx_rayosx_ca=:m_rx_rayosx_ca,
                    m_rx_rayosx_cg=:m_rx_rayosx_cg,
                    m_rx_rayosx_cn=:m_rx_rayosx_cn,
                    m_rx_rayosx_co=:m_rx_rayosx_co,
                    m_rx_rayosx_cp=:m_rx_rayosx_cp,
                    m_rx_rayosx_cv=:m_rx_rayosx_cv,
                    m_rx_rayosx_di=:m_rx_rayosx_di,
                    m_rx_rayosx_ef=:m_rx_rayosx_ef,
                    m_rx_rayosx_em=:m_rx_rayosx_em,
                    m_rx_rayosx_es=:m_rx_rayosx_es,
                    m_rx_rayosx_od=:m_rx_rayosx_od,
                    m_rx_rayosx_fr=:m_rx_rayosx_fr,
                    m_rx_rayosx_hi=:m_rx_rayosx_hi,
                    m_rx_rayosx_ho=:m_rx_rayosx_ho,
                    m_rx_rayosx_ids=:m_rx_rayosx_ids,
                    m_rx_rayosx_ih=:m_rx_rayosx_ih,
                    m_rx_rayosx_kl=:m_rx_rayosx_kl,
                    m_rx_rayosx_me=:m_rx_rayosx_me,
                    m_rx_rayosx_pa=:m_rx_rayosx_pa,
                    m_rx_rayosx_pb=:m_rx_rayosx_pb,
                    m_rx_rayosx_pi=:m_rx_rayosx_pi,
                    m_rx_rayosx_px=:m_rx_rayosx_px,
                    m_rx_rayosx_ra=:m_rx_rayosx_ra,
                    m_rx_rayosx_rp=:m_rx_rayosx_rp,
                    m_rx_rayosx_tb=:m_rx_rayosx_tb,
                    m_rx_rayosx_coment=:m_rx_rayosx_coment,
                    m_rx_rayosx_obs=:m_rx_rayosx_obs,
                    m_rx_rayosx_concluciones=:m_rx_rayosx_concluciones,
                    m_rx_rayosx_vertice=:m_rx_rayosx_vertice,
                    m_rx_rayosx_mediastinos=:m_rx_rayosx_mediastinos,
                    m_rx_rayosx_camp_pulmo=:m_rx_rayosx_camp_pulmo,
                    m_rx_rayosx_silueta_card=:m_rx_rayosx_silueta_card,
                    m_rx_rayosx_hilos=:m_rx_rayosx_hilos,
                    m_rx_rayosx_senos=:m_rx_rayosx_senos
                where
                m_rx_rayosx_adm=:adm;';


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

    public function rx_torax_report($adm) {
        $sql = $this->sql("SELECT *
        FROM mod_rayosx_rayosx
        where m_rx_rayosx_adm=$adm;");
        return $sql;
    }

}

//$sesion = new model(); 
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
?>