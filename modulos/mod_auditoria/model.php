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
        $sql1 = ("SELECT
                    adm_id as adm,adm_foto
                    ,tfi_desc,emp_desc, pac_ndoc
                    ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
                    ,concat(adm_puesto,' - ',adm_area)as puesto,tfi_desc
                    ,concat(pac_appat,' ',pac_apmat,' ',pac_nombres)as nombre
                    ,concat(pac_nombres)as nom
                    ,concat(pac_appat,' ',pac_apmat)as ape
                    ,pac_sexo, adm_fech fecha
                    FROM admision
                    inner join paciente on adm_pac=pac_id
                    inner join pack on adm_ruta=pk_id
                    left join empresa on pk_emp=emp_id
                    left join tficha on adm_tficha=tfi_id
                    ");
        $empresa = $this->user->empresas;
        ($this->user->acceso == 1) ? '' : $sql1 .= "and emp_id IN ($empresa) ";
        if (!is_null($columna) && !is_null($query)) {
            if ($columna == "1") {
                $sql1 .= "where adm_id=$query";
            } else if ($columna == "2") {
                $sql1 .= "where pac_ndoc=$query";
            } else if ($columna == "3") {
                $sql1 .= "where concat(pac_appat,' ',pac_apmat,' ',pac_nombres) like '%$query%'";
            } else if ($columna == "4" && $this->user->acceso == 1) {
                $sql1 .= "where emp_desc like '%$query%' or emp_id like '%$query%'";
            } else if ($columna == "5") {
                $sql1 .= "where tfi_desc LIKE '%$query%'";
            }
        }
        $sql1 .= " group by adm_id order by adm_id DESC;";
        $sql = $this->sql($sql1);
        foreach ($sql->data as $i => $value) {
            $adm_id = $value->adm;
            $nro_examenes = $value->nro_examenes;
            $verifica = $this->sql("SELECT count(m_psicologia_adm)total FROM mod_psicologia where m_psicologia_adm=$adm_id;");
            $total = $verifica->data[0]->total;
            $value->st = ($nro_examenes >= $total) ? '1' : '0';
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_formatos()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm_id = isset($_POST['adm']) ? $_POST['adm'] : NULL;
        $q = "SELECT adm_id as adm, ex_desc,ar_id,ex_id,ar_desc,ex_formato
                FROM admision
                inner join pack on adm_ruta=pk_id
                inner join dpack on dpk_pkid=pk_id
                inner join examen on ex_id=dpk_exid
                inner join paciente on pac_id=adm_pac
                inner join area on ex_arid=ar_id
                where  adm_id=$adm_id and ar_id NOT IN(1,10) order by ex_arid;";
        $sql = $this->sql($q);
        $t = $sql->total;

        $verifica = ("SELECT  count(ex_id) total FROM admision
        inner join pack on adm_ruta=pk_id
        inner join dpack on dpk_pkid=pk_id
        inner join examen on ex_id=dpk_exid
        where ex_arid=10 and
        adm_id=$adm_id");
        $veri = $this->sql($verifica);

        // $sql->data[$t]->adm = $_POST['adm'];
        // $sql->data[$t]->ex_desc = "CERTIFICADO MÉDICO";
        // $sql->data[$t]->ar_id = "9";
        // $sql->data[$t]->ex_id = "19";
        // $sql->data[$t]->ar_desc = "MEDICINA";
        // $sql->data[$t]->ex_formato = "mod_medicina&sys_report=certificado312";

        if ($veri->data[0]->total > 0) {
            $sql->data[$t + 1]->adm = $_POST['adm'];
            $sql->data[$t + 1]->ex_desc = "LABORATORIO";
            $sql->data[$t + 1]->ar_id = "10";
            $sql->data[$t + 1]->ex_id = "0";
            $sql->data[$t + 1]->ar_desc = "LABORATORIO";
            $sql->data[$t + 1]->ex_formato = "mod_laboratorio&sys_report=formato_laboratorio";
        }

        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_auditor_id id,m_auditor_st st,m_auditor_estado estado
                , m_auditor_usuario usu, m_auditor_fech_reg fech 
            FROM mod_auditoria 
            where m_auditor_adm=$adm_id and m_auditor_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->estado = $verifica->data[0]->estado;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function load_auditoria()
    {
        $m_auditor_id = $_POST['m_auditor_id'];
        //        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_auditoria where m_auditor_id='$m_auditor_id';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_auditoria()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':m_auditor_examen'] = $_POST['ex_id'];
        $params_1[':m_auditor_estado'] = $_POST['m_auditor_estado'];
        $params_1[':m_auditor_usuario'] = $this->user->us_id;

        $q_1 = "INSERT INTO mod_auditoria VALUES
                (NULL,
                :adm,
                :m_auditor_examen,
                1,
                :m_auditor_estado,
                :m_auditor_usuario,
                now()
                );";

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $verifica = $this->sql("SELECT
		m_auditor_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_auditoria
		inner join sys_usuario on usu_id=m_auditor_usuario
		where 
		m_auditor_adm ='$adm' and m_auditor_examen ='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $this->commit();
                return $sql_1;
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        }
    }

    public function update_auditoria()
    {
        $this->begin();

        $params_1 = array();
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':m_auditor_examen'] = $_POST['ex_id'];
        $params_1[':m_auditor_estado'] = $_POST['m_auditor_estado'];
        $q_1 = 'Update mod_auditoria set
                m_auditor_estado=:m_auditor_estado
                where
                m_auditor_id=:id and m_auditor_adm=:adm and m_auditor_examen=:m_auditor_examen;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $sql_1 = $this->sql($q_1, $params_1);
        if ($sql_1->success && $sql_1->total == 1) {
            $this->commit();
            return $sql_1;
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }

    public function st_busca_mod_auditoria_detalle()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_ad_obs FROM mod_auditoria_detalle
                            where
                            m_ad_obs like '%$query%'
                            group by m_ad_obs");
        return $sql;
    }
    
    public function load_mod_auditoria_detalle()
    {
        $m_ad_id = $_POST['m_ad_id'];
        $m_ad_adm = $_POST['m_ad_adm'];
        $query = "SELECT
            *
            FROM mod_auditoria_detalle
            where
            m_ad_id=$m_ad_id and
            m_ad_adm=$m_ad_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    
    public function save_mod_auditoria_detalle()
    {
        $params = array();
        $params[':m_ad_adm'] = $_POST['m_ad_adm'];
        $params[':m_ad_examen'] = $_POST['m_ad_examen'];
        $params[':m_ad_obs'] = $_POST['m_ad_obs'];

        $q = 'INSERT INTO mod_auditoria_detalle VALUES 
                (NULL,
                :m_ad_adm,
                :m_ad_examen,
                :m_ad_obs,
                NULL,
                NULL,
                NULL)';
        return $this->sql($q, $params);
    }

    public function update_mod_auditoria_detalle()
    {
        $params = array();
        $params[':m_ad_id'] = $_POST['m_ad_id'];
        $params[':m_ad_adm'] = $_POST['m_ad_adm'];
        $params[':m_ad_examen'] = $_POST['m_ad_examen'];
        $params[':m_ad_obs'] = $_POST['m_ad_obs'];

        $this->begin();
        $q = 'Update mod_auditoria_detalle set
                m_ad_obs=:m_ad_obs
                where
                m_ad_id=:m_ad_id and m_ad_adm=:m_ad_adm and m_ad_examen=:m_ad_examen;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $pac_id = $_POST['m_ad_adm'];
            $this->commit();
            return array('success' => true, 'data' => $pac_id);
        } else {
            $this->rollback();
        }
    }

    public function list_mod_auditoria_detalle()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $m_ad_adm = $_POST['m_ad_adm'];
        $m_ad_examen = $_POST['m_ad_examen'];
        $q = "SELECT m_ad_id, m_ad_id, m_ad_obs
                FROM mod_auditoria_detalle
                where m_ad_adm=$m_ad_adm and m_ad_examen=$m_ad_examen;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }
}
