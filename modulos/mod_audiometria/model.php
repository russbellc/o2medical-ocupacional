<?php

class model extends core
{

    public function list_paciente()
    {
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
                    ex_arid IN (3)
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
            $verifica = $this->sql("SELECT count(m_audio_adm)total FROM mod_audio where m_audio_adm=$adm_id;");
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
                where ex_arid IN (3) and adm_id=$adm_id order by ex_arid;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_audio_id id,m_audio_st st
                , m_audio_usu usu, m_audio_fech_reg fech 
            FROM mod_audio 
            where m_audio_adm=$adm_id and m_audio_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    //LOAD SAVE UPDATE LABORATORIO

    public function load_audio_pred()
    {
        $adm = $_POST['adm'];
        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_audio_pred where m_audio_pred_adm='$adm' and m_audio_pred_examen='$examen';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_audio_pred()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':m_audio_st'] = '1'; //ESTADO DEL MODULO PRINCIPAL

        $params[':adm'] = $adm;
        $params[':ex_id'] = $exa;

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_audio_pred_resultado'] = $_POST['m_audio_pred_resultado'];
        $params[':m_audio_pred_observaciones'] = $_POST['m_audio_pred_observaciones'];
        $params[':m_audio_pred_diagnostico'] = $_POST['m_audio_pred_diagnostico'];


        $q = "INSERT INTO mod_audio_pred VALUES 
                (null,
                :adm,
                :ex_id,
                :m_audio_pred_resultado,
                :m_audio_pred_observaciones,
                :m_audio_pred_diagnostico);
                
                INSERT INTO mod_audio VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_audio_st,
                :ex_id);";

        $verifica = $this->sql("SELECT 
		m_audio_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_audio 
		inner join sys_usuario on usu_id=m_audio_usu 
		where 
		m_audio_adm='$adm' and m_audio_examen='$exa';");
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

    public function update_audio_pred()
    {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_audio_pred_resultado'] = $_POST['m_audio_pred_resultado'];
        $params[':m_audio_pred_observaciones'] = $_POST['m_audio_pred_observaciones'];
        $params[':m_audio_pred_diagnostico'] = $_POST['m_audio_pred_diagnostico'];

        $this->begin();
        $q = 'Update mod_audio_pred set
                    m_audio_pred_resultado=:m_audio_pred_resultado,
                    m_audio_pred_observaciones=:m_audio_pred_observaciones,
                    m_audio_pred_diagnostico=:m_audio_pred_diagnostico
                where
                m_audio_pred_adm=:adm and m_audio_pred_examen=:ex_id;
                
                update mod_audio set
                    m_audio_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_audio_adm=:adm and m_audio_examen=:ex_id;';


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

    public function load_audio_audio()
    {
        $adm = $_POST['adm'];
        //        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_audio_audio where m_a_audio_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_audio_audio()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_audio VALUES
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
        $params_2[':m_a_audio_ocupacion'] = $_POST['m_a_audio_ocupacion'];
        $params_2[':m_a_audio_anios'] = $_POST['m_a_audio_anios'];
        $params_2[':m_a_audio_horas_expo'] = $_POST['m_a_audio_horas_expo'];
        $params_2[':m_a_audio_ruido_laboral'] = $_POST['m_a_audio_ruido_laboral'];
        $params_2[':m_a_audio_antece_familiar'] = $_POST['m_a_audio_antece_familiar'];
        $params_2[':m_a_audio_antece_familiar_coment'] = $_POST['m_a_audio_antece_familiar_coment'];
        $params_2[':m_a_audio_antece_01'] = $_POST['m_a_audio_antece_01'];
        $params_2[':m_a_audio_antece_02'] = $_POST['m_a_audio_antece_02'];
        $params_2[':m_a_audio_antece_03'] = $_POST['m_a_audio_antece_03'];
        $params_2[':m_a_audio_antece_04'] = $_POST['m_a_audio_antece_04'];
        $params_2[':m_a_audio_antece_05'] = $_POST['m_a_audio_antece_05'];
        $params_2[':m_a_audio_antece_06'] = $_POST['m_a_audio_antece_06'];
        $params_2[':m_a_audio_antece_07'] = $_POST['m_a_audio_antece_07'];
        $params_2[':m_a_audio_antece_08'] = $_POST['m_a_audio_antece_08'];
        $params_2[':m_a_audio_antece_09'] = $_POST['m_a_audio_antece_09'];
        $params_2[':m_a_audio_antece_10'] = $_POST['m_a_audio_antece_10'];
        $params_2[':m_a_audio_sintoma_01'] = $_POST['m_a_audio_sintoma_01'];
        $params_2[':m_a_audio_sintoma_02'] = $_POST['m_a_audio_sintoma_02'];
        $params_2[':m_a_audio_sintoma_03'] = $_POST['m_a_audio_sintoma_03'];
        $params_2[':m_a_audio_sintoma_04'] = $_POST['m_a_audio_sintoma_04'];
        $params_2[':m_a_audio_sintoma_05'] = $_POST['m_a_audio_sintoma_05'];
        $params_2[':m_a_audio_sintoma_06'] = $_POST['m_a_audio_sintoma_06'];
        $params_2[':m_a_audio_sintoma_07'] = $_POST['m_a_audio_sintoma_07'];
        $params_2[':m_a_audio_sintoma_07_desc'] = $_POST['m_a_audio_sintoma_07_desc'];
        $params_2[':m_a_audio_tapones'] = $_POST['m_a_audio_tapones'];
        $params_2[':m_a_audio_orejeras'] = $_POST['m_a_audio_orejeras'];
        $params_2[':m_a_audio_nariz'] = $_POST['m_a_audio_nariz'];
        $params_2[':m_a_audio_nariz_esp'] = $_POST['m_a_audio_nariz_esp'];
        $params_2[':m_a_audio_orofaringe'] = $_POST['m_a_audio_orofaringe'];
        $params_2[':m_a_audio_orofaringe_esp'] = $_POST['m_a_audio_orofaringe_esp'];
        $params_2[':m_a_audio_oido'] = $_POST['m_a_audio_oido'];
        $params_2[':m_a_audio_oido_esp'] = $_POST['m_a_audio_oido_esp'];
        $params_2[':m_a_audio_otros'] = $_POST['m_a_audio_otros'];
        $params_2[':m_a_audio_otros_esp'] = $_POST['m_a_audio_otros_esp'];
        $params_2[':m_a_audio_otos_triangulo_od'] = $_POST['m_a_audio_otos_triangulo_od'];
        $params_2[':m_a_audio_otos_perfora_od'] = $_POST['m_a_audio_otos_perfora_od'];
        $params_2[':m_a_audio_otos_abomba_od'] = $_POST['m_a_audio_otos_abomba_od'];
        $params_2[':m_a_audio_otos_serumen_od'] = $_POST['m_a_audio_otos_serumen_od'];
        $params_2[':m_a_audio_otos_triangulo_oi'] = $_POST['m_a_audio_otos_triangulo_oi'];
        $params_2[':m_a_audio_otos_perfora_oi'] = $_POST['m_a_audio_otos_perfora_oi'];
        $params_2[':m_a_audio_otos_abomba_oi'] = $_POST['m_a_audio_otos_abomba_oi'];
        $params_2[':m_a_audio_otos_serumen_oi'] = $_POST['m_a_audio_otos_serumen_oi'];


        $params_2[':m_a_audio_otos_permeable_od'] = $_POST['m_a_audio_otos_permeable_od'];
        $params_2[':m_a_audio_otos_retraccion_od'] = $_POST['m_a_audio_otos_retraccion_od'];
        $params_2[':m_a_audio_otos_permeable_oi'] = $_POST['m_a_audio_otos_permeable_oi'];
        $params_2[':m_a_audio_otos_retraccion_oi'] = $_POST['m_a_audio_otos_retraccion_oi'];


        $params_2[':m_a_audio_aereo_125_od'] = $_POST['m_a_audio_aereo_125_od'];
        $params_2[':m_a_audio_aereo_250_od'] = $_POST['m_a_audio_aereo_250_od'];
        $params_2[':m_a_audio_aereo_500_od'] = $_POST['m_a_audio_aereo_500_od'];
        $params_2[':m_a_audio_aereo_1000_od'] = $_POST['m_a_audio_aereo_1000_od'];
        $params_2[':m_a_audio_aereo_2000_od'] = $_POST['m_a_audio_aereo_2000_od'];
        $params_2[':m_a_audio_aereo_3000_od'] = $_POST['m_a_audio_aereo_3000_od'];
        $params_2[':m_a_audio_aereo_4000_od'] = $_POST['m_a_audio_aereo_4000_od'];
        $params_2[':m_a_audio_aereo_6000_od'] = $_POST['m_a_audio_aereo_6000_od'];
        $params_2[':m_a_audio_aereo_8000_od'] = $_POST['m_a_audio_aereo_8000_od'];
        $params_2[':m_a_audio_aereo_125_oi'] = $_POST['m_a_audio_aereo_125_oi'];
        $params_2[':m_a_audio_aereo_250_oi'] = $_POST['m_a_audio_aereo_250_oi'];
        $params_2[':m_a_audio_aereo_500_oi'] = $_POST['m_a_audio_aereo_500_oi'];
        $params_2[':m_a_audio_aereo_1000_oi'] = $_POST['m_a_audio_aereo_1000_oi'];
        $params_2[':m_a_audio_aereo_2000_oi'] = $_POST['m_a_audio_aereo_2000_oi'];
        $params_2[':m_a_audio_aereo_3000_oi'] = $_POST['m_a_audio_aereo_3000_oi'];
        $params_2[':m_a_audio_aereo_4000_oi'] = $_POST['m_a_audio_aereo_4000_oi'];
        $params_2[':m_a_audio_aereo_6000_oi'] = $_POST['m_a_audio_aereo_6000_oi'];
        $params_2[':m_a_audio_aereo_8000_oi'] = $_POST['m_a_audio_aereo_8000_oi'];
        $params_2[':m_a_audio_oseo_125_od'] = $_POST['m_a_audio_oseo_125_od'];
        $params_2[':m_a_audio_oseo_250_od'] = $_POST['m_a_audio_oseo_250_od'];
        $params_2[':m_a_audio_oseo_500_od'] = $_POST['m_a_audio_oseo_500_od'];
        $params_2[':m_a_audio_oseo_1000_od'] = $_POST['m_a_audio_oseo_1000_od'];
        $params_2[':m_a_audio_oseo_2000_od'] = $_POST['m_a_audio_oseo_2000_od'];
        $params_2[':m_a_audio_oseo_3000_od'] = $_POST['m_a_audio_oseo_3000_od'];
        $params_2[':m_a_audio_oseo_4000_od'] = $_POST['m_a_audio_oseo_4000_od'];
        $params_2[':m_a_audio_oseo_6000_od'] = $_POST['m_a_audio_oseo_6000_od'];
        $params_2[':m_a_audio_oseo_8000_od'] = $_POST['m_a_audio_oseo_8000_od'];
        $params_2[':m_a_audio_oseo_125_oi'] = $_POST['m_a_audio_oseo_125_oi'];
        $params_2[':m_a_audio_oseo_250_oi'] = $_POST['m_a_audio_oseo_250_oi'];
        $params_2[':m_a_audio_oseo_500_oi'] = $_POST['m_a_audio_oseo_500_oi'];
        $params_2[':m_a_audio_oseo_1000_oi'] = $_POST['m_a_audio_oseo_1000_oi'];
        $params_2[':m_a_audio_oseo_2000_oi'] = $_POST['m_a_audio_oseo_2000_oi'];
        $params_2[':m_a_audio_oseo_3000_oi'] = $_POST['m_a_audio_oseo_3000_oi'];
        $params_2[':m_a_audio_oseo_4000_oi'] = $_POST['m_a_audio_oseo_4000_oi'];
        $params_2[':m_a_audio_oseo_6000_oi'] = $_POST['m_a_audio_oseo_6000_oi'];
        $params_2[':m_a_audio_oseo_8000_oi'] = $_POST['m_a_audio_oseo_8000_oi'];
        $params_2[':m_a_audio_diag_aereo_od'] = $_POST['m_a_audio_diag_aereo_od'];
        $params_2[':m_a_audio_diag_aereo_oi'] = $_POST['m_a_audio_diag_aereo_oi'];
        $params_2[':m_a_audio_diag_osteo_od'] = $_POST['m_a_audio_diag_osteo_od'];
        $params_2[':m_a_audio_diag_osteo_oi'] = $_POST['m_a_audio_diag_osteo_oi'];
        $params_2[':m_a_audio_kclokhoff'] = $_POST['m_a_audio_kclokhoff'];
        $params_2[':m_a_audio_comentarios'] = $_POST['m_a_audio_comentarios'];
        $params_2[':m_a_audio_medico'] = $_POST['m_a_audio_medico'];



        $q_2 = "INSERT INTO mod_audio_audio VALUES 
                (null,
                :adm,                
                :m_a_audio_ocupacion,
                :m_a_audio_anios,
                :m_a_audio_horas_expo,
                :m_a_audio_ruido_laboral,
                :m_a_audio_antece_familiar,
                :m_a_audio_antece_familiar_coment,
                :m_a_audio_antece_01,
                :m_a_audio_antece_02,
                :m_a_audio_antece_03,
                :m_a_audio_antece_04,
                :m_a_audio_antece_05,
                :m_a_audio_antece_06,
                :m_a_audio_antece_07,
                :m_a_audio_antece_08,
                :m_a_audio_antece_09,
                :m_a_audio_antece_10,
                :m_a_audio_sintoma_01,
                :m_a_audio_sintoma_02,
                :m_a_audio_sintoma_03,
                :m_a_audio_sintoma_04,
                :m_a_audio_sintoma_05,
                :m_a_audio_sintoma_06,
                :m_a_audio_sintoma_07,
                :m_a_audio_sintoma_07_desc,
                :m_a_audio_tapones,
                :m_a_audio_orejeras,
                :m_a_audio_nariz,
                :m_a_audio_nariz_esp,
                :m_a_audio_orofaringe,
                :m_a_audio_orofaringe_esp,
                :m_a_audio_oido,
                :m_a_audio_oido_esp,
                :m_a_audio_otros,
                :m_a_audio_otros_esp,
                :m_a_audio_otos_triangulo_od,
                :m_a_audio_otos_perfora_od,
                :m_a_audio_otos_abomba_od,
                :m_a_audio_otos_serumen_od,
                :m_a_audio_otos_permeable_od,                
                :m_a_audio_otos_retraccion_od,
                :m_a_audio_otos_triangulo_oi,
                :m_a_audio_otos_perfora_oi,
                :m_a_audio_otos_abomba_oi,
                :m_a_audio_otos_serumen_oi,                
                :m_a_audio_otos_permeable_oi,
                :m_a_audio_otos_retraccion_oi,

                :m_a_audio_aereo_125_od,
                :m_a_audio_aereo_250_od,
                :m_a_audio_aereo_500_od,
                :m_a_audio_aereo_1000_od,
                :m_a_audio_aereo_2000_od,
                :m_a_audio_aereo_3000_od,
                :m_a_audio_aereo_4000_od,
                :m_a_audio_aereo_6000_od,
                :m_a_audio_aereo_8000_od,

                :m_a_audio_aereo_125_oi,
                :m_a_audio_aereo_250_oi,
                :m_a_audio_aereo_500_oi,
                :m_a_audio_aereo_1000_oi,
                :m_a_audio_aereo_2000_oi,
                :m_a_audio_aereo_3000_oi,
                :m_a_audio_aereo_4000_oi,
                :m_a_audio_aereo_6000_oi,
                :m_a_audio_aereo_8000_oi,

                :m_a_audio_oseo_125_od,
                :m_a_audio_oseo_250_od,
                :m_a_audio_oseo_500_od,
                :m_a_audio_oseo_1000_od,
                :m_a_audio_oseo_2000_od,
                :m_a_audio_oseo_3000_od,
                :m_a_audio_oseo_4000_od,
                :m_a_audio_oseo_6000_od,
                :m_a_audio_oseo_8000_od,
                :m_a_audio_oseo_125_oi,
                :m_a_audio_oseo_250_oi,
                :m_a_audio_oseo_500_oi,
                :m_a_audio_oseo_1000_oi,
                :m_a_audio_oseo_2000_oi,
                :m_a_audio_oseo_3000_oi,
                :m_a_audio_oseo_4000_oi,
                :m_a_audio_oseo_6000_oi,
                :m_a_audio_oseo_8000_oi,
                :m_a_audio_diag_aereo_od,
                :m_a_audio_diag_aereo_oi,
                :m_a_audio_diag_osteo_od,
                :m_a_audio_diag_osteo_oi,
                :m_a_audio_kclokhoff,
                :m_a_audio_comentarios,
                :m_a_audio_medico
                );";

        $verifica = $this->sql("SELECT 
		m_audio_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_audio 
		inner join sys_usuario on usu_id=m_audio_usu 
		where 
		m_audio_adm='$adm' and m_audio_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    if (strlen($_POST['audiograma_aereo']) > 1) {
                        $upload_dir = "images/audio/";
                        $audio_aereo = $_POST['audiograma_aereo'];

                        unlink("images/audio/audiograma_aereo" . $adm . ".png");
                        $audio_aereo = str_replace('data:image/png;base64,', '', $audio_aereo);
                        $audio_aereo = str_replace(' ', '+', $audio_aereo);
                        $data = base64_decode($audio_aereo);
                        $file = $upload_dir . "audiograma_aereo" . $adm . ".png";
                        $success = file_put_contents($file, $data);



                        $audio_oseo = $_POST['audiograma_oseo'];
                        unlink("images/audio/audiograma_oseo" . $adm . ".png");
                        $audio_oseo = str_replace('data:image/png;base64,', '', $audio_oseo);
                        $audio_oseo = str_replace(' ', '+', $audio_oseo);
                        $data2 = base64_decode($audio_oseo);
                        $file2 = $upload_dir . "audiograma_oseo" . $adm . ".png";
                        $success_oseo = file_put_contents($file2, $data2);

                        $this->commit();
                        return $sql_2;
                        // $this->rollback();
                        // return array('success' => false, 'error' => 'Problemas con el registro.');
                    } else {
                        // $this->rollback();
                        // return array('success' => false, 'error' => 'Problemas con el registro.');
                        $this->commit();
                        return $sql_2;
                    }
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

    public function update_audio_audio()
    {
        $adm = $_POST['adm'];
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_audio set
                    m_audio_usu=:usuario,
                    m_audio_fech_update=now()
                where
                m_audio_id=:id and m_audio_adm=:adm and m_audio_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_a_audio_ocupacion'] = $_POST['m_a_audio_ocupacion'];
        $params_2[':m_a_audio_anios'] = $_POST['m_a_audio_anios'];
        $params_2[':m_a_audio_horas_expo'] = $_POST['m_a_audio_horas_expo'];
        $params_2[':m_a_audio_ruido_laboral'] = $_POST['m_a_audio_ruido_laboral'];
        $params_2[':m_a_audio_antece_familiar'] = $_POST['m_a_audio_antece_familiar'];
        $params_2[':m_a_audio_antece_familiar_coment'] = $_POST['m_a_audio_antece_familiar_coment'];
        $params_2[':m_a_audio_antece_01'] = $_POST['m_a_audio_antece_01'];
        $params_2[':m_a_audio_antece_02'] = $_POST['m_a_audio_antece_02'];
        $params_2[':m_a_audio_antece_03'] = $_POST['m_a_audio_antece_03'];
        $params_2[':m_a_audio_antece_04'] = $_POST['m_a_audio_antece_04'];
        $params_2[':m_a_audio_antece_05'] = $_POST['m_a_audio_antece_05'];
        $params_2[':m_a_audio_antece_06'] = $_POST['m_a_audio_antece_06'];
        $params_2[':m_a_audio_antece_07'] = $_POST['m_a_audio_antece_07'];
        $params_2[':m_a_audio_antece_08'] = $_POST['m_a_audio_antece_08'];
        $params_2[':m_a_audio_antece_09'] = $_POST['m_a_audio_antece_09'];
        $params_2[':m_a_audio_antece_10'] = $_POST['m_a_audio_antece_10'];
        $params_2[':m_a_audio_sintoma_01'] = $_POST['m_a_audio_sintoma_01'];
        $params_2[':m_a_audio_sintoma_02'] = $_POST['m_a_audio_sintoma_02'];
        $params_2[':m_a_audio_sintoma_03'] = $_POST['m_a_audio_sintoma_03'];
        $params_2[':m_a_audio_sintoma_04'] = $_POST['m_a_audio_sintoma_04'];
        $params_2[':m_a_audio_sintoma_05'] = $_POST['m_a_audio_sintoma_05'];
        $params_2[':m_a_audio_sintoma_06'] = $_POST['m_a_audio_sintoma_06'];
        $params_2[':m_a_audio_sintoma_07'] = $_POST['m_a_audio_sintoma_07'];
        $params_2[':m_a_audio_sintoma_07_desc'] = $_POST['m_a_audio_sintoma_07_desc'];
        $params_2[':m_a_audio_tapones'] = $_POST['m_a_audio_tapones'];
        $params_2[':m_a_audio_orejeras'] = $_POST['m_a_audio_orejeras'];
        $params_2[':m_a_audio_nariz'] = $_POST['m_a_audio_nariz'];
        $params_2[':m_a_audio_nariz_esp'] = $_POST['m_a_audio_nariz_esp'];
        $params_2[':m_a_audio_orofaringe'] = $_POST['m_a_audio_orofaringe'];
        $params_2[':m_a_audio_orofaringe_esp'] = $_POST['m_a_audio_orofaringe_esp'];
        $params_2[':m_a_audio_oido'] = $_POST['m_a_audio_oido'];
        $params_2[':m_a_audio_oido_esp'] = $_POST['m_a_audio_oido_esp'];
        $params_2[':m_a_audio_otros'] = $_POST['m_a_audio_otros'];
        $params_2[':m_a_audio_otros_esp'] = $_POST['m_a_audio_otros_esp'];
        $params_2[':m_a_audio_otos_triangulo_od'] = $_POST['m_a_audio_otos_triangulo_od'];
        $params_2[':m_a_audio_otos_perfora_od'] = $_POST['m_a_audio_otos_perfora_od'];
        $params_2[':m_a_audio_otos_abomba_od'] = $_POST['m_a_audio_otos_abomba_od'];
        $params_2[':m_a_audio_otos_serumen_od'] = $_POST['m_a_audio_otos_serumen_od'];
        $params_2[':m_a_audio_otos_triangulo_oi'] = $_POST['m_a_audio_otos_triangulo_oi'];
        $params_2[':m_a_audio_otos_perfora_oi'] = $_POST['m_a_audio_otos_perfora_oi'];
        $params_2[':m_a_audio_otos_abomba_oi'] = $_POST['m_a_audio_otos_abomba_oi'];
        $params_2[':m_a_audio_otos_serumen_oi'] = $_POST['m_a_audio_otos_serumen_oi'];


        $params_2[':m_a_audio_otos_permeable_od'] = $_POST['m_a_audio_otos_permeable_od'];
        $params_2[':m_a_audio_otos_retraccion_od'] = $_POST['m_a_audio_otos_retraccion_od'];
        $params_2[':m_a_audio_otos_permeable_oi'] = $_POST['m_a_audio_otos_permeable_oi'];
        $params_2[':m_a_audio_otos_retraccion_oi'] = $_POST['m_a_audio_otos_retraccion_oi'];


        $params_2[':m_a_audio_aereo_125_od'] = $_POST['m_a_audio_aereo_125_od'];
        $params_2[':m_a_audio_aereo_250_od'] = $_POST['m_a_audio_aereo_250_od'];
        $params_2[':m_a_audio_aereo_500_od'] = $_POST['m_a_audio_aereo_500_od'];
        $params_2[':m_a_audio_aereo_1000_od'] = $_POST['m_a_audio_aereo_1000_od'];
        $params_2[':m_a_audio_aereo_2000_od'] = $_POST['m_a_audio_aereo_2000_od'];
        $params_2[':m_a_audio_aereo_3000_od'] = $_POST['m_a_audio_aereo_3000_od'];
        $params_2[':m_a_audio_aereo_4000_od'] = $_POST['m_a_audio_aereo_4000_od'];
        $params_2[':m_a_audio_aereo_6000_od'] = $_POST['m_a_audio_aereo_6000_od'];
        $params_2[':m_a_audio_aereo_8000_od'] = $_POST['m_a_audio_aereo_8000_od'];
        $params_2[':m_a_audio_aereo_125_oi'] = $_POST['m_a_audio_aereo_125_oi'];
        $params_2[':m_a_audio_aereo_250_oi'] = $_POST['m_a_audio_aereo_250_oi'];
        $params_2[':m_a_audio_aereo_500_oi'] = $_POST['m_a_audio_aereo_500_oi'];
        $params_2[':m_a_audio_aereo_1000_oi'] = $_POST['m_a_audio_aereo_1000_oi'];
        $params_2[':m_a_audio_aereo_2000_oi'] = $_POST['m_a_audio_aereo_2000_oi'];
        $params_2[':m_a_audio_aereo_3000_oi'] = $_POST['m_a_audio_aereo_3000_oi'];
        $params_2[':m_a_audio_aereo_4000_oi'] = $_POST['m_a_audio_aereo_4000_oi'];
        $params_2[':m_a_audio_aereo_6000_oi'] = $_POST['m_a_audio_aereo_6000_oi'];
        $params_2[':m_a_audio_aereo_8000_oi'] = $_POST['m_a_audio_aereo_8000_oi'];
        $params_2[':m_a_audio_oseo_125_od'] = $_POST['m_a_audio_oseo_125_od'];
        $params_2[':m_a_audio_oseo_250_od'] = $_POST['m_a_audio_oseo_250_od'];
        $params_2[':m_a_audio_oseo_500_od'] = $_POST['m_a_audio_oseo_500_od'];
        $params_2[':m_a_audio_oseo_1000_od'] = $_POST['m_a_audio_oseo_1000_od'];
        $params_2[':m_a_audio_oseo_2000_od'] = $_POST['m_a_audio_oseo_2000_od'];
        $params_2[':m_a_audio_oseo_3000_od'] = $_POST['m_a_audio_oseo_3000_od'];
        $params_2[':m_a_audio_oseo_4000_od'] = $_POST['m_a_audio_oseo_4000_od'];
        $params_2[':m_a_audio_oseo_6000_od'] = $_POST['m_a_audio_oseo_6000_od'];
        $params_2[':m_a_audio_oseo_8000_od'] = $_POST['m_a_audio_oseo_8000_od'];
        $params_2[':m_a_audio_oseo_125_oi'] = $_POST['m_a_audio_oseo_125_oi'];
        $params_2[':m_a_audio_oseo_250_oi'] = $_POST['m_a_audio_oseo_250_oi'];
        $params_2[':m_a_audio_oseo_500_oi'] = $_POST['m_a_audio_oseo_500_oi'];
        $params_2[':m_a_audio_oseo_1000_oi'] = $_POST['m_a_audio_oseo_1000_oi'];
        $params_2[':m_a_audio_oseo_2000_oi'] = $_POST['m_a_audio_oseo_2000_oi'];
        $params_2[':m_a_audio_oseo_3000_oi'] = $_POST['m_a_audio_oseo_3000_oi'];
        $params_2[':m_a_audio_oseo_4000_oi'] = $_POST['m_a_audio_oseo_4000_oi'];
        $params_2[':m_a_audio_oseo_6000_oi'] = $_POST['m_a_audio_oseo_6000_oi'];
        $params_2[':m_a_audio_oseo_8000_oi'] = $_POST['m_a_audio_oseo_8000_oi'];
        $params_2[':m_a_audio_diag_aereo_od'] = $_POST['m_a_audio_diag_aereo_od'];
        $params_2[':m_a_audio_diag_aereo_oi'] = $_POST['m_a_audio_diag_aereo_oi'];
        $params_2[':m_a_audio_diag_osteo_od'] = $_POST['m_a_audio_diag_osteo_od'];
        $params_2[':m_a_audio_diag_osteo_oi'] = $_POST['m_a_audio_diag_osteo_oi'];
        $params_2[':m_a_audio_kclokhoff'] = $_POST['m_a_audio_kclokhoff'];
        $params_2[':m_a_audio_comentarios'] = $_POST['m_a_audio_comentarios'];
        $params_2[':m_a_audio_medico'] = $_POST['m_a_audio_medico'];

        $q_2 = 'Update mod_audio_audio set
                    m_a_audio_ocupacion=:m_a_audio_ocupacion,
                    m_a_audio_anios=:m_a_audio_anios,
                    m_a_audio_horas_expo=:m_a_audio_horas_expo,
                    m_a_audio_ruido_laboral=:m_a_audio_ruido_laboral,
                    m_a_audio_antece_familiar=:m_a_audio_antece_familiar,
                    m_a_audio_antece_familiar_coment=:m_a_audio_antece_familiar_coment,
                    m_a_audio_antece_01=:m_a_audio_antece_01,
                    m_a_audio_antece_02=:m_a_audio_antece_02,
                    m_a_audio_antece_03=:m_a_audio_antece_03,
                    m_a_audio_antece_04=:m_a_audio_antece_04,
                    m_a_audio_antece_05=:m_a_audio_antece_05,
                    m_a_audio_antece_06=:m_a_audio_antece_06,
                    m_a_audio_antece_07=:m_a_audio_antece_07,
                    m_a_audio_antece_08=:m_a_audio_antece_08,
                    m_a_audio_antece_09=:m_a_audio_antece_09,
                    m_a_audio_antece_10=:m_a_audio_antece_10,
                    m_a_audio_sintoma_01=:m_a_audio_sintoma_01,
                    m_a_audio_sintoma_02=:m_a_audio_sintoma_02,
                    m_a_audio_sintoma_03=:m_a_audio_sintoma_03,
                    m_a_audio_sintoma_04=:m_a_audio_sintoma_04,
                    m_a_audio_sintoma_05=:m_a_audio_sintoma_05,
                    m_a_audio_sintoma_06=:m_a_audio_sintoma_06,
                    m_a_audio_sintoma_07=:m_a_audio_sintoma_07,
                    m_a_audio_sintoma_07_desc=:m_a_audio_sintoma_07_desc,
                    m_a_audio_tapones=:m_a_audio_tapones,
                    m_a_audio_orejeras=:m_a_audio_orejeras,
                    m_a_audio_nariz=:m_a_audio_nariz,
                    m_a_audio_nariz_esp=:m_a_audio_nariz_esp,
                    m_a_audio_orofaringe=:m_a_audio_orofaringe,
                    m_a_audio_orofaringe_esp=:m_a_audio_orofaringe_esp,
                    m_a_audio_oido=:m_a_audio_oido,
                    m_a_audio_oido_esp=:m_a_audio_oido_esp,
                    m_a_audio_otros=:m_a_audio_otros,
                    m_a_audio_otros_esp=:m_a_audio_otros_esp,
                    m_a_audio_otos_triangulo_od=:m_a_audio_otos_triangulo_od,
                    m_a_audio_otos_perfora_od=:m_a_audio_otos_perfora_od,
                    m_a_audio_otos_abomba_od=:m_a_audio_otos_abomba_od,
                    m_a_audio_otos_serumen_od=:m_a_audio_otos_serumen_od,
                    m_a_audio_otos_permeable_od=:m_a_audio_otos_permeable_od,
                    m_a_audio_otos_retraccion_od=:m_a_audio_otos_retraccion_od,
                    m_a_audio_otos_triangulo_oi=:m_a_audio_otos_triangulo_oi,
                    m_a_audio_otos_perfora_oi=:m_a_audio_otos_perfora_oi,
                    m_a_audio_otos_abomba_oi=:m_a_audio_otos_abomba_oi,
                    m_a_audio_otos_serumen_oi=:m_a_audio_otos_serumen_oi,
                    m_a_audio_otos_permeable_oi=:m_a_audio_otos_permeable_oi,
                    m_a_audio_otos_retraccion_oi=:m_a_audio_otos_retraccion_oi,
                    m_a_audio_aereo_125_od=:m_a_audio_aereo_125_od,
                    m_a_audio_aereo_250_od=:m_a_audio_aereo_250_od,
                    m_a_audio_aereo_500_od=:m_a_audio_aereo_500_od,
                    m_a_audio_aereo_1000_od=:m_a_audio_aereo_1000_od,
                    m_a_audio_aereo_2000_od=:m_a_audio_aereo_2000_od,
                    m_a_audio_aereo_3000_od=:m_a_audio_aereo_3000_od,
                    m_a_audio_aereo_4000_od=:m_a_audio_aereo_4000_od,
                    m_a_audio_aereo_6000_od=:m_a_audio_aereo_6000_od,
                    m_a_audio_aereo_8000_od=:m_a_audio_aereo_8000_od,
                    m_a_audio_aereo_125_oi=:m_a_audio_aereo_125_oi,
                    m_a_audio_aereo_250_oi=:m_a_audio_aereo_250_oi,
                    m_a_audio_aereo_500_oi=:m_a_audio_aereo_500_oi,
                    m_a_audio_aereo_1000_oi=:m_a_audio_aereo_1000_oi,
                    m_a_audio_aereo_2000_oi=:m_a_audio_aereo_2000_oi,
                    m_a_audio_aereo_3000_oi=:m_a_audio_aereo_3000_oi,
                    m_a_audio_aereo_4000_oi=:m_a_audio_aereo_4000_oi,
                    m_a_audio_aereo_6000_oi=:m_a_audio_aereo_6000_oi,
                    m_a_audio_aereo_8000_oi=:m_a_audio_aereo_8000_oi,
                    m_a_audio_oseo_125_od=:m_a_audio_oseo_125_od,
                    m_a_audio_oseo_250_od=:m_a_audio_oseo_250_od,
                    m_a_audio_oseo_500_od=:m_a_audio_oseo_500_od,
                    m_a_audio_oseo_1000_od=:m_a_audio_oseo_1000_od,
                    m_a_audio_oseo_2000_od=:m_a_audio_oseo_2000_od,
                    m_a_audio_oseo_3000_od=:m_a_audio_oseo_3000_od,
                    m_a_audio_oseo_4000_od=:m_a_audio_oseo_4000_od,
                    m_a_audio_oseo_6000_od=:m_a_audio_oseo_6000_od,
                    m_a_audio_oseo_8000_od=:m_a_audio_oseo_8000_od,
                    m_a_audio_oseo_125_oi=:m_a_audio_oseo_125_oi,
                    m_a_audio_oseo_250_oi=:m_a_audio_oseo_250_oi,
                    m_a_audio_oseo_500_oi=:m_a_audio_oseo_500_oi,
                    m_a_audio_oseo_1000_oi=:m_a_audio_oseo_1000_oi,
                    m_a_audio_oseo_2000_oi=:m_a_audio_oseo_2000_oi,
                    m_a_audio_oseo_3000_oi=:m_a_audio_oseo_3000_oi,
                    m_a_audio_oseo_4000_oi=:m_a_audio_oseo_4000_oi,
                    m_a_audio_oseo_6000_oi=:m_a_audio_oseo_6000_oi,
                    m_a_audio_oseo_8000_oi=:m_a_audio_oseo_8000_oi,
                    m_a_audio_diag_aereo_od=:m_a_audio_diag_aereo_od,
                    m_a_audio_diag_aereo_oi=:m_a_audio_diag_aereo_oi,
                    m_a_audio_diag_osteo_od=:m_a_audio_diag_osteo_od,
                    m_a_audio_diag_osteo_oi=:m_a_audio_diag_osteo_oi,
                    m_a_audio_kclokhoff=:m_a_audio_kclokhoff,
                    m_a_audio_comentarios=:m_a_audio_comentarios,
                    m_a_audio_medico=:m_a_audio_medico
                where
                m_a_audio_adm=:adm;';

        $sql_2 = $this->sql($q_2, $params_2);
        if ($sql_2->success) {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success && $sql_1->total == 1) {
                if (strlen($_POST['audiograma_aereo']) > 1) {
                    $upload_dir = "images/audio/";
                    $audio_aereo = $_POST['audiograma_aereo'];

                    unlink("images/audio/audiograma_aereo" . $adm . ".png");
                    $audio_aereo = str_replace('data:image/png;base64,', '', $audio_aereo);
                    $audio_aereo = str_replace(' ', '+', $audio_aereo);
                    $data = base64_decode($audio_aereo);
                    $file = $upload_dir . "audiograma_aereo" . $adm . ".png";
                    $success = file_put_contents($file, $data);



                    $audio_oseo = $_POST['audiograma_oseo'];
                    unlink("images/audio/audiograma_oseo" . $adm . ".png");
                    $audio_oseo = str_replace('data:image/png;base64,', '', $audio_oseo);
                    $audio_oseo = str_replace(' ', '+', $audio_oseo);
                    $data2 = base64_decode($audio_oseo);
                    $file2 = $upload_dir . "audiograma_oseo" . $adm . ".png";
                    $success_oseo = file_put_contents($file2, $data2);

                    $this->commit();
                    return $sql_1;
                    // $this->rollback();
                    // return array('success' => false, 'error' => 'Problemas con el registro.');
                } else {
                    // $this->rollback();
                    // return array('success' => false, 'error' => 'Problemas con el registro.');

                    $this->commit();
                    return $sql_1;
                }
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }

    //AUTO GUARDADO
    public function st_m_a_audio_diag_aereo_od()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_a_audio_diag_aereo_od FROM mod_audio_audio
                            where
                            m_a_audio_diag_aereo_od like '%$query%'
                            group by m_a_audio_diag_aereo_od");
        return $sql;
    }

    public function st_m_a_audio_diag_aereo_oi()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_a_audio_diag_aereo_oi FROM mod_audio_audio
                            where
                            m_a_audio_diag_aereo_oi like '%$query%'
                            group by m_a_audio_diag_aereo_oi");
        return $sql;
    }

    public function st_m_a_audio_diag_osteo_od()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_a_audio_diag_osteo_od FROM mod_audio_audio
                            where
                            m_a_audio_diag_osteo_od like '%$query%'
                            group by m_a_audio_diag_osteo_od");
        return $sql;
    }

    public function st_m_a_audio_diag_osteo_oi()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_a_audio_diag_osteo_oi FROM mod_audio_audio
                            where
                            m_a_audio_diag_osteo_oi like '%$query%'
                            group by m_a_audio_diag_osteo_oi");
        return $sql;
    }

    public function st_m_a_audio_kclokhoff()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_a_audio_kclokhoff FROM mod_audio_audio
                            where
                            m_a_audio_kclokhoff like '%$query%'
                            group by m_a_audio_kclokhoff");
        return $sql;
    }

    //LOAD SAVE UPDATE PDF

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

    public function mod_audio_audio_report($adm)
    {
        $sql = $this->sql("SELECT mod_audio_audio.*
        , medico_cmp, medico_firma
        FROM mod_audio_audio
            left join medico on medico_id=m_a_audio_medico
        where m_a_audio_adm=$adm;");
        return $sql;
    }

    public function load_audio_medico()
    {
        $adm = $_POST['adm'];
        $st = $_POST['st'];
        $usuario = $this->user->us_id;
        $medico = "";
        if ($st < '1') {
            $medico = ",(SELECT medico_id FROM medico where medico_usu='$usuario') m_a_audio_medico";
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

    public function load_medico()
    {
        $sede = $this->user->con_sedid;
        return $this->sql("SELECT medico_id, concat(medico_apepat,' ',medico_apemat,', ',medico_nombre)as nombre
        FROM medico
        where medico_sede=$sede and medico_st=1 and medico_tipo = 'OTORRINOLARINGOLOGIA';");
    }
}

//$sesion = new model(); 
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
