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
                    ex_arid IN (4)
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
            $verifica = $this->sql("SELECT count(m_espiro_adm)total FROM mod_espiro where m_espiro_adm=$adm_id;");
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
                where ex_arid IN (4) and adm_id=$adm_id order by ex_arid;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_espiro_id id,m_espiro_st st
                , m_espiro_usu usu, m_espiro_fech_reg fech 
            FROM mod_espiro 
            where m_espiro_adm=$adm_id and m_espiro_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    //LOAD SAVE UPDATE mod_espiro_metria

    public function carga_pdf_espiro_metria($adm)
    {
        $query = "SELECT * FROM mod_espiro_metria
                    where m_espiro_metria_adm='$adm';";
        return $this->sql($query);
    }

    public function load_espiro_metria()
    {
        $adm = $_POST['adm'];
        $query = "SELECT * FROM mod_espiro_metria where m_espiro_metria_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_espiro_metria()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_espiro VALUES
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
        $params_2[':m_espiro_metria_fuma'] = $_POST['m_espiro_metria_fuma'];
        $params_2[':m_espiro_metria_cap_vital'] = $_POST['m_espiro_metria_cap_vital'];
        $params_2[':m_espiro_metria_FVC'] = $_POST['m_espiro_metria_FVC'];
        $params_2[':m_espiro_metria_FEV1'] = $_POST['m_espiro_metria_FEV1'];
        $params_2[':m_espiro_metria_FEV1_FVC'] = $_POST['m_espiro_metria_FEV1_FVC'];
        $params_2[':m_espiro_metria_PEF'] = $_POST['m_espiro_metria_PEF'];
        $params_2[':m_espiro_metria_FEF2575'] = $_POST['m_espiro_metria_FEF2575'];
        $params_2[':m_espiro_metria_recomendacion'] = $_POST['m_espiro_metria_recomendacion'];
        $params_2[':m_espiro_metria_conclusion'] = $_POST['m_espiro_metria_conclusion'];
        $params_2[':m_espiro_metria_cie10'] = $_POST['m_espiro_metria_cie10'];
        $params_2[':m_espiro_metria_diag'] = $_POST['m_espiro_metria_diag'];

        $q_2 = "INSERT INTO mod_espiro_metria VALUES 
                (null,
                :adm,
                :m_espiro_metria_fuma,
                :m_espiro_metria_cap_vital,
                :m_espiro_metria_FVC,
                :m_espiro_metria_FEV1,
                :m_espiro_metria_FEV1_FVC,
                :m_espiro_metria_PEF,
                :m_espiro_metria_FEF2575,
                :m_espiro_metria_recomendacion,
                :m_espiro_metria_conclusion,
                :m_espiro_metria_cie10,
                :m_espiro_metria_diag,
                null
                );";

        $verifica = $this->sql("SELECT 
                m_espiro_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
                FROM mod_espiro 
                inner join sys_usuario on usu_id=m_espiro_usu 
                where 
                m_espiro_adm='$adm' and m_espiro_examen='$exa';");
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

    public function update_espiro_metria()
    {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'update mod_espiro set
                    m_espiro_usu=:usuario,
                    m_espiro_fech_update=now()
                where
                m_espiro_id=:id and m_espiro_adm=:adm and m_espiro_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_espiro_metria_fuma'] = $_POST['m_espiro_metria_fuma'];
        $params_2[':m_espiro_metria_cap_vital'] = $_POST['m_espiro_metria_cap_vital'];
        $params_2[':m_espiro_metria_FVC'] = $_POST['m_espiro_metria_FVC'];
        $params_2[':m_espiro_metria_FEV1'] = $_POST['m_espiro_metria_FEV1'];
        $params_2[':m_espiro_metria_FEV1_FVC'] = $_POST['m_espiro_metria_FEV1_FVC'];
        $params_2[':m_espiro_metria_PEF'] = $_POST['m_espiro_metria_PEF'];
        $params_2[':m_espiro_metria_FEF2575'] = $_POST['m_espiro_metria_FEF2575'];
        $params_2[':m_espiro_metria_recomendacion'] = $_POST['m_espiro_metria_recomendacion'];
        $params_2[':m_espiro_metria_conclusion'] = $_POST['m_espiro_metria_conclusion'];
        $params_2[':m_espiro_metria_cie10'] = $_POST['m_espiro_metria_cie10'];
        $params_2[':m_espiro_metria_diag'] = $_POST['m_espiro_metria_diag'];

        $q_2 = 'Update mod_espiro_metria set
                    m_espiro_metria_fuma=:m_espiro_metria_fuma,
                    m_espiro_metria_cap_vital=:m_espiro_metria_cap_vital,
                    m_espiro_metria_FVC=:m_espiro_metria_FVC,
                    m_espiro_metria_FEV1=:m_espiro_metria_FEV1,
                    m_espiro_metria_FEV1_FVC=:m_espiro_metria_FEV1_FVC,
                    m_espiro_metria_PEF=:m_espiro_metria_PEF,
                    m_espiro_metria_FEF2575=:m_espiro_metria_FEF2575,
                    m_espiro_metria_recomendacion=:m_espiro_metria_recomendacion,
                    m_espiro_metria_conclusion=:m_espiro_metria_conclusion,
                    m_espiro_metria_cie10=:m_espiro_metria_cie10,
                    m_espiro_metria_diag=:m_espiro_metria_diag
                where
                m_espiro_metria_adm=:adm;';

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

    public function list_cie10()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT cie4_id, cie4_cie3id
                , concat(cie4_id,' - ',cie4_desc) cie4_desc
                FROM cie4
                where
                concat(cie4_id, cie4_cie3id, cie4_desc) like'%$query%'
                order by cie4_cie3id;");
        return $sql;
    }
}
